<?php

namespace App\Livewire;

use Exception;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\AI\CategoryService;
use Illuminate\Support\Facades\Auth;
use App\Services\AI\NarrationService;
use App\Services\AI\ImageGenerationService;
use App\Services\AI\VideoGenerationService;
use App\Services\AI\ScriptGenerationService;
use Livewire\Attributes\Computed;

class CreateTikTok extends Component
{
    public ?string $title = null;
    public string $categorySlug = 'romantice';
    public array $categories = [];
    public string $status = 'draft';
    public ?array $script = null;
    public ?string $imageUrl = null;
    public ?string $audioUrl = null;
    public ?string $videoUrl = null;
    public ?string $render_id = null;
    public bool $isProcessing = false;

    // Constructor Injection
    private CategoryService $categoryService;
    private ScriptGenerationService $scriptService;
    private ImageGenerationService $imageService;
    private NarrationService $narrationService;
    private VideoGenerationService $videoService;


    public function boot(
        CategoryService $categoryService,
        ScriptGenerationService $scriptService,
        ImageGenerationService $imageService,
        NarrationService $narrationService,
        VideoGenerationService $videoService
    ) {
        $this->categoryService = $categoryService;
        $this->scriptService = $scriptService;
        $this->imageService = $imageService;
        $this->narrationService = $narrationService;
        $this->videoService = $videoService;
    }


    public function mount()
    {
        $this->categories = $this->categoryService->getCategories();

        try {
            if (Auth::check()) {
                $lastProject = Auth::user()->videoProjects()
                    ->whereNotNull('render_id')
                    ->latest()
                    ->first();

                if ($lastProject) {
                    $this->render_id = $lastProject->render_id;
                    $this->videoUrl = $lastProject->video_url;
                    $this->isProcessing = $lastProject->status === 'rendering';
                }
            }
        } catch (Exception $e) {
            Log::error('Error in CreateTikTok mount:', ['error' => $e->getMessage()]);
        }
    }


    #[Computed]
    public function getAvailableCategories(): array
    {
        return $this->categories;
    }


    public function generate()
    {
        $this->videoUrl = null;
        $this->isProcessing = false;

        ini_set('max_execution_time', '300');
        set_time_limit(300);

        $this->isProcessing = false;

        $this->validate([
            'categorySlug' => 'required'
        ]);

        try {
            DB::beginTransaction();

            $categoryName = $this->categoryService->getCategoryFullPath($this->categorySlug);  // Use full path
            if (!$categoryName) {
                throw new Exception("Category not found: " . $this->categorySlug);
            }

            $this->script = $this->scriptService->generate($categoryName); // Use full path

            if (isset($this->script['background_prompt'])) {
                $imageResult = $this->imageService->generateImage($this->script['background_prompt']);

                if ($imageResult['success']) {
                    $this->imageUrl = $imageResult['image_url'];
                    $imageCloudinaryId = $imageResult['cloudinary_public_id'];
                } else {
                    throw new Exception("Image generation failed: " . $imageResult['error']);
                }
            }


            $fullNarration = '';
            foreach ($this->script['scenes'] as $scene) {
                $fullNarration .= $scene['narration'] . " ";
            }

            $narrationResult = $this->narrationService->generate($fullNarration);
            if ($narrationResult['status'] === 'success') {
                $this->audioUrl = $narrationResult['audio_url'];
                $audioCloudinaryId = $narrationResult['cloudinary_public_id'];
                $audioDuration = $narrationResult['audio_duration']; // Obținem durata
            } else {
                throw new Exception("Narration generation failed");
            }


            $project = Auth::user()->videoProjects()->create([
                'title' => $this->title ?? $categoryName . " TikTok",
                'script' => $this->script,
                'status' => 'processing',
                'image_url' => $this->imageUrl,
                'image_cloudinary_id' => $imageCloudinaryId ?? null,
                'audio_url' => $this->audioUrl,
                'audio_cloudinary_id' => $audioCloudinaryId ?? null,
                'audio_duration' => $audioDuration ?? null // Foarte important!
            ]);



            $videoResult = $this->videoService->generate($project);

            if ($videoResult['success']) {
                $project->update([
                    'status' => 'rendering',
                    'render_id' => $videoResult['render_id']
                ]);

                $this->render_id = $videoResult['render_id'];
                $this->isProcessing = true;
                $this->videoUrl = null;

                session()->flash('message', 'Proiectul TikTok a fost creat și randarea a început!');
            } else {
                throw new Exception("Video generation failed: " . $videoResult['error']);
            }

            DB::commit();
        } catch (Exception $e) {
            $this->isProcessing = false;
            DB::rollBack();
            Log::error('TikTok generation failed', [
                'error' => $e->getMessage(),
                'category' => $this->categorySlug
            ]);
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function checkStatus()
    {
        try {
            $project = Auth::user()->videoProjects()
                ->where('render_id', $this->render_id)
                ->first();

            if (!$project) {
                $this->isProcessing = false;
                return;
            }

            $status = $this->videoService->checkStatus($project->render_id);

            if ($status['success'] && $status['status'] === 'done') {
                $project->update([
                    'status' => 'completed',
                    'video_url' => $status['url']
                ]);

                $this->videoUrl = $status['url'];
                $this->isProcessing = false;

                $this->dispatch('videoReady');

                session()->flash('message', 'Videoclipul este gata!');
            } elseif (!$status['success'] || $status['status'] === 'failed') {
                $project->update(['status' => 'failed']);
                $this->isProcessing = false;
                session()->flash('error', 'Video generation failed: ' . ($status['error'] ?? 'Unknown error'));
            }
        } catch (Exception $e) {
            $this->isProcessing = false;
            Log::error('Status check failed', [
                'error' => $e->getMessage(),
                'render_id' => $this->render_id
            ]);
            session()->flash('error', 'Error checking status: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.create-tik-tok'); // No need to pass categories again
    }
}