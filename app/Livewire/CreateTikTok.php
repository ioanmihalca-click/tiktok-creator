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
use App\Services\AI\TopicGenerationService;
use App\Services\AI\VideoGenerationService;
use App\Services\AI\ScriptGenerationService;
use Livewire\Attributes\Computed;


class CreateTikTok extends Component
{
    public ?string $title = null;
    public ?string $topic = null;
    public string $categorySlug = 'romantice';
    public array $categories = [];
    public string $status = 'draft';
    public ?array $script = null;
    public ?string $imageUrl = null;
    public ?string $audioUrl = null;
    public ?string $videoUrl = null;
    public ?string $render_id = null;
    public bool $isProcessing = false;
    public bool $isGeneratingTopic = false;

    public function mount(CategoryService $categoryService)
    {
        $this->categories = $categoryService->getCategories();

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

    public function getCategories(): array
    {
        return $this->categories;
    }

    #[Computed]
    public function getAvailableCategories(): array
    {
        return $this->categories;
    }

    public function generateTopic(
        CategoryService $categoryService,
        TopicGenerationService $topicService
    ) {
        try {
            $this->isGeneratingTopic = true;

            // Use a local variable, no need to call the service method twice
            $categoryName = $categoryService->getCategoryBySlug($this->categorySlug);
            if (!$categoryName) {
                throw new Exception("Category not found");
            }

            $this->topic = $topicService->generateTopic($categoryName);

            session()->flash('message', 'Topic generated successfully!');
        } catch (Exception $e) {
            Log::error('Topic generation failed', [
                'error' => $e->getMessage(),
                'category' => $this->categorySlug
            ]);
            session()->flash('error', 'Failed to generate topic: ' . $e->getMessage());
        } finally {
            $this->isGeneratingTopic = false;
        }
    }

    public function generate(
        CategoryService $categoryService,
        ScriptGenerationService $scriptService,
        ImageGenerationService $imageService,
        NarrationService $narrationService,
        VideoGenerationService $videoService
    ) {
        $this->isProcessing = false; // Reset initial state

        // Ideally, this value should be in a configuration file (e.g., config/app.php)
        // ini_set('max_execution_time', config('app.max_execution_time', 120));
        ini_set('max_execution_time', '120'); // Fallback to 120 seconds
        set_time_limit(120);

        $this->validate([
            'topic' => 'required|min:3',
            'categorySlug' => 'required',
            'title' => 'required|min:3' // Added title validation
        ]);

        try {
            DB::beginTransaction(); // Start transaction

            // Obținem numele categoriei
            $categoryName = $categoryService->getCategoryBySlug($this->categorySlug);
            if (!$categoryName) {
                throw new Exception("Category not found: " . $this->categorySlug);
            }

            // 1. Generăm scriptul
            $this->script = $scriptService->generate($this->topic, $categoryName);

            // 2. Generăm imaginea
            if (isset($this->script['background_prompt'])) {
                $imageResult = $imageService->generateImage($this->script['background_prompt']);

                if ($imageResult['success']) {
                    $this->imageUrl = $imageResult['image_url'];
                    $imageCloudinaryId = $imageResult['cloudinary_public_id'];
                } else {
                    throw new Exception("Image generation failed: " . $imageResult['error']);
                }
            }

            // 3. Generăm nararea
            $fullNarration = '';
            foreach ($this->script['scenes'] as $scene) {
                $fullNarration .= $scene['narration'] . " ";
            }

            $narrationResult = $narrationService->generate($fullNarration);
            if ($narrationResult['status'] === 'success') {
                $this->audioUrl = $narrationResult['audio_url'];
                $audioCloudinaryId = $narrationResult['cloudinary_public_id'];
            } else {
                throw new Exception("Narration generation failed");
            }

            // 4. Salvăm proiectul
            $project = Auth::user()->videoProjects()->create([
                'title' => $this->title ?? $this->topic,  // Use $this->title
                'script' => $this->script,
                'status' => 'processing',
                'image_url' => $this->imageUrl,
                'image_cloudinary_id' => $imageCloudinaryId ?? null,
                'audio_url' => $this->audioUrl,
                'audio_cloudinary_id' => $audioCloudinaryId ?? null
            ]);

            // 5. Generăm videoclipul final
            // Ideally, this should be dispatched to a queue:
            // VideoGenerationJob::dispatch($project);
            $videoResult = $videoService->generate($project);

            if ($videoResult['success']) {
                $project->update([
                    'status' => 'rendering',
                    'render_id' => $videoResult['render_id']
                ]);

                $this->render_id = $videoResult['render_id'];
                $this->isProcessing = true;

                session()->flash('message', 'TikTok project created and rendering started!');
            } else {
                $this->isProcessing = false;
                throw new Exception("Video generation failed: " . $videoResult['error']);
            }

            DB::commit(); // Commit transaction

        } catch (Exception $e) {
            $this->isProcessing = false;
            DB::rollBack(); // Rollback transaction on error
            Log::error('TikTok generation failed', [
                'error' => $e->getMessage(),
                'topic' => $this->topic,
                'category' => $this->categorySlug
            ]);
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function checkStatus(VideoGenerationService $videoService)
    {
        try {
            $project = Auth::user()->videoProjects()
                ->where('render_id', $this->render_id)
                ->first();
    
            if (!$project) {
                $this->isProcessing = false;
                return;
            }
    
            $status = $videoService->checkStatus($project->render_id);
    
            if ($status['success'] && $status['status'] === 'done') {
                $project->update([
                    'status' => 'completed',
                    'video_url' => $status['url']
                ]);
    
                $this->videoUrl = $status['url'];
                $this->isProcessing = false;
                session()->flash('message', 'Video is ready!');
            } elseif (!$status['success'] || $status['status'] === 'failed') {
                $project->update(['status' => 'failed']);
                $this->isProcessing = false;
                session()->flash('error', 'Video generation failed: ' . ($status['error'] ?? 'Unknown error'));
            } elseif ($status['status'] !== 'rendering') {
                // For any other non-rendering status, stop processing
                $this->isProcessing = false;
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

    public function render(CategoryService $categoryService)
    {
        return view('livewire.create-tik-tok', [
            'categories' => $categoryService->getCategories()
        ]);
    }
}
