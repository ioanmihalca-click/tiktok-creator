<?php

namespace App\Livewire;

use Exception;
use App\Models\User;
use Livewire\Component;
use App\Models\VideoProject;
use App\Jobs\GenerateTikTokJob;
use App\Services\CreditService;
use App\Jobs\CheckTikTokStatusJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\AI\CategoryService;
use Illuminate\Support\Facades\Auth;
use App\Services\AI\NarrationService;
use App\Models\UserCredit;


class CreateTikTok extends Component
{
    public ?string $title = null;
    public string $categorySlug = '';
    public ?array $script = null;
    public ?string $imageUrl = null;
    public ?string $audioUrl = null;
    public ?string $videoUrl = null;
    public ?string $render_id = null;
    public bool $isProcessing = false;
    public string $currentStep = '';
    public array $completedSteps = [];
    public ?int $projectId = null;
    public bool $jobStarted = false;
    public bool $showInitialProcessingModal = false;
    public bool $initialProcessingComplete = false;

    private CategoryService $categoryService;
    private CreditService $creditService;
    private NarrationService $narrationService;

    public bool $hasCredits = false;
    public string $creditType = '';

    public $availableVoices = [];
    public $selectedVoiceId = null;


    public function boot(CategoryService $categoryService, CreditService $creditService, NarrationService $narrationService)
    {
        $this->categoryService = $categoryService;
        $this->creditService = $creditService;
        $this->narrationService = $narrationService;
    }

    public function mount()
    {
        try {

            if (Auth::check()) {
                $user = User::find(Auth::id());

                // Folosește firstOrCreate() pentru a crea UserCredit dacă nu există
                $user->userCredit()->firstOrCreate(
                    [
                        'user_id' => $user->id
                    ],
                    [
                        'free_credits' => 3 // Valori implicite
                    ]
                );

                $this->creditType = $this->creditService->checkCreditType($user);
                $this->hasCredits = (bool) $this->creditType;

                // Încarcă vocile disponibile
                $this->availableVoices = $this->creditService->getAvailableVoices($user, $this->narrationService);

                // Setează vocea implicită
                if (!empty($this->availableVoices['free'])) {
                    $this->selectedVoiceId = $this->availableVoices['free'][0]['voice_id'];
                }


                $lastProject = $user->videoProjects()
                    ->whereNotNull('render_id')
                    ->latest()
                    ->first();

                if ($lastProject) {
                    $this->render_id = $lastProject->render_id;
                    $this->videoUrl = $lastProject->video_url;
                    $this->projectId = $lastProject->id;

                    // Important: Verificăm statusul real, nu ne bazăm doar pe câmpul din BD
                    $this->isProcessing = in_array($lastProject->status, ['processing', 'rendering']);

                    if ($this->isProcessing) {
                        $this->currentStep = 'Procesare video în curs...';
                    }
                }
            }
        } catch (Exception $e) {
            Log::error('Error in CreateTikTok mount:', ['error' => $e->getMessage()]);
        }
    }

    public function selectVoice($voiceId)
    {
        // Verifică dacă vocea este o voce premium
        $isPremiumVoice = false;

        if (!empty($this->availableVoices['premium'])) {
            foreach ($this->availableVoices['premium'] as $voice) {
                if ($voice['voice_id'] === $voiceId) {
                    $isPremiumVoice = true;
                    break;
                }
            }
        }

        // Dacă este o voce premium și utilizatorul nu are credite premium, nu face nimic
        if ($isPremiumVoice && $this->creditType !== 'paid') {
            return;
        }

        $this->selectedVoiceId = $voiceId;
    }

    public function generate()
    {
        // Verificăm doar disponibilitatea creditelor, fără a le deduce
        $user = User::find(Auth::id());
        $this->creditType = $this->creditService->checkCreditType($user);
        $this->hasCredits = (bool) $this->creditType;

        if (!$this->hasCredits) {
            session()->flash('error', 'Nu ai credite disponibile. Te rugăm să achiziționezi un pachet de credite.');
            return redirect()->route('credits.index');
        }

        $this->reset(['videoUrl', 'script', 'imageUrl', 'audioUrl', 'completedSteps', 'projectId', 'jobStarted']);
        $this->isProcessing = true;
        $this->showInitialProcessingModal = true;
        $this->initialProcessingComplete = false;
        $this->dispatch('processingStarted');

        $this->validate([
            'categorySlug' => 'required'
        ]);

        try {
            $this->currentStep = 'Inițializare generare TikTok...';

            // Începem tranzacția
            DB::beginTransaction();

            // ELIMINAT: $deductResult = $user->deductCredit(); 
            // Nu mai deducem creditul aici, se va face în job

            // Marcăm faptul că jobul este în curs de pornire
            $this->jobStarted = true;

            // Salvăm un proiect inițial pentru a avea un ID
            $initialProject = $user->videoProjects()->create([
                'title' => $this->title ?? $this->categoryService->getCategoryFullPath($this->categorySlug) . " TikTok",
                'status' => 'processing',
            ]);

            $this->projectId = $initialProject->id;

            // Dispatch job-ul pentru generarea TikTok
            GenerateTikTokJob::dispatch($user, $this->categorySlug, $this->title, $initialProject->id, $this->selectedVoiceId);

            session()->flash('message', 'Procesul de generare a început. Acest lucru poate dura câteva minute.');
            DB::commit(); //Comitem tranzacția
        } catch (Exception $e) {
            DB::rollBack(); //Rollback în caz de eroare
            $this->showInitialProcessingModal = false;
            $this->isProcessing = false;
            $this->currentStep = 'Eroare: ' . $e->getMessage();
            Log::error('TikTok generation job dispatch failed', [
                'error' => $e->getMessage(),
                'category' => $this->categorySlug
            ]);
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function finishInitialProcessing()
    {
        $this->showInitialProcessingModal = false;
        $this->initialProcessingComplete = true;
        $this->currentStep = 'Procesare video în curs...';

        // Putem să verificăm statusul proiectului imediat
        if ($this->projectId) {
            $this->checkStatus();
        }
    }


    public function checkStatus()
    {
        // Dacă nu avem un ID de proiect sau un render_id, nu putem verifica statusul
        if (!$this->projectId && !$this->render_id) {
            return;
        }

        try {
            $user = User::find(Auth::id());
            $project = $user->videoProjects()
                ->where(function ($query) {
                    if ($this->projectId) {
                        $query->where('id', $this->projectId);
                    } elseif ($this->render_id) {
                        $query->where('render_id', $this->render_id);
                    }
                })
                ->first();

            if (!$project) {
                $this->isProcessing = false;
                return;
            }

            $this->projectId = $project->id;
            $this->render_id = $project->render_id;

            // Să verificăm dacă avem deja un URL de video
            if ($project->status === 'completed' && $project->video_url) {
                // Doar dacă există un video_url valid, considerăm că procesul s-a finalizat
                $this->videoUrl = $project->video_url;
                $this->script = $project->script; // Adăugat aici - încărcăm scriptul
                $this->isProcessing = false;
                $this->dispatch('videoReady');
                session()->flash('message', 'Videoclipul este gata!');
            } elseif ($project->status === 'failed') {
                // Dacă s-a marcat ca eșuat, actualizăm interfața
                $this->isProcessing = false;
                session()->flash('error', 'Generarea video a eșuat.');
            } else {
                // Dacă proiectul este încă în procesare, ne asigurăm că interfața arată acest lucru
                $this->isProcessing = true;
                $this->currentStep = 'Procesare video în curs...';

                // Dacă proiectul are un render_id dar este încă în procesare, verificăm statusul
                if ($project->render_id) {
                    // Dispatchăm jobul de verificare a statusului
                    CheckTikTokStatusJob::dispatch($project->id);
                }
            }
        } catch (Exception $e) {
            // Nu schimbăm starea de procesare în caz de eroare pentru a evita afișarea falsă a finalizării
            Log::error('Status check failed', [
                'error' => $e->getMessage(),
                'project_id' => $this->projectId,
                'render_id' => $this->render_id
            ]);
            session()->flash('error', 'Error checking status: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Actualizăm starea creditelor la fiecare randare
        if (Auth::check()) {
            $user = User::find(Auth::id());
            //Asiguram existenta UserCredit.
            $user->userCredit()->firstOrCreate(
                [
                    'user_id' => $user->id
                ],
                [
                    'free_credits' => 3 // Valori implicite
                ]
            );
            $this->creditType = $this->creditService->checkCreditType($user);
            $this->hasCredits = (bool) $this->creditType;
        }

        $categories = $this->categoryService->getCategories();

        // Verificăm statusul dacă suntem în procesare și avem un project ID
        if ($this->isProcessing && $this->projectId) {
            $this->checkStatus();
        }

        return view('livewire.create-tik-tok', [
            'categories' => $categories,
            'hasCredits' => $this->hasCredits,
            'creditType' => $this->creditType,
            'userCredit' => Auth::check() ? Auth::user()->userCredit : null // Folosim Auth::user()
        ]);
    }

    public function updatedCategorySlug($value)
    {
        $this->reset(['script', 'imageUrl', 'audioUrl', 'videoUrl', 'isProcessing', 'projectId', 'jobStarted']);
    }

    public function setCategory($slug)
    {
        $this->reset(['script', 'imageUrl', 'audioUrl', 'videoUrl', 'isProcessing', 'projectId', 'jobStarted']);
        $this->categorySlug = $slug;
    }
}
