<?php

namespace App\Livewire;

use Exception;
use Livewire\Component;
use App\Jobs\GenerateTikTokJob;
use App\Jobs\CheckTikTokStatusJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\AI\CategoryService;
use Illuminate\Support\Facades\Auth;
use App\Models\VideoProject;

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

    public function boot(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function mount()
    {
        try {
            if (Auth::check()) {
                $lastProject = Auth::user()->videoProjects()
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

    public function generate()
    {
        $this->reset(['videoUrl', 'script', 'imageUrl', 'audioUrl', 'completedSteps', 'projectId', 'jobStarted']);
        $this->isProcessing = true;
        $this->showInitialProcessingModal = true;  // Afișăm modalul inițial
        $this->initialProcessingComplete = false;  // Resetăm flag-ul de finalizare
        $this->dispatch('processingStarted');

        $this->validate([
            'categorySlug' => 'required'
        ]);

        try {
            $this->currentStep = 'Inițializare generare TikTok...';

            // Marcăm faptul că jobul este în curs de pornire
            $this->jobStarted = true;

            // Salvăm un proiect inițial pentru a avea un ID
            $initialProject = Auth::user()->videoProjects()->create([
                'title' => $this->title ?? $this->categoryService->getCategoryFullPath($this->categorySlug) . " TikTok",
                'status' => 'processing',
            ]);

            $this->projectId = $initialProject->id;

            // Dispatch job-ul pentru generarea TikTok
            GenerateTikTokJob::dispatch(Auth::user(), $this->categorySlug, $this->title, $initialProject->id);

            // Nu mai schimbăm currentStep aici - rămâne în starea inițială
            // Pentru a permite vizualizarea modalului pentru o perioadă mai lungă de timp

            session()->flash('message', 'Procesul de generare a început. Acest lucru poate dura câteva minute.');
        } catch (Exception $e) {
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
            $project = Auth::user()->videoProjects()
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
        $categories = $this->categoryService->getCategories();

        // Verificăm statusul dacă suntem în procesare și avem un project ID
        if ($this->isProcessing && $this->projectId) {
            $this->checkStatus();
        }

        return view('livewire.create-tik-tok', [
            'categories' => $categories
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
