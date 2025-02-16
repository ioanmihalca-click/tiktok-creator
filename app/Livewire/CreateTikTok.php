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
    //public ?string $topic = null;
    public string $categorySlug = 'romantice';
    public array $categories = [];
    public string $status = 'draft';
    public ?array $script = null;
    public ?string $imageUrl = null;
    public ?string $audioUrl = null;
    public ?string $videoUrl = null;
    public ?string $render_id = null;
    public bool $isProcessing = false;
    //public bool $isGeneratingTopic = false;

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
    
   
    public function getCategoryFullName(CategoryService $categoryService): ?string
    {
        return $categoryService->getCategoryFullPath($this->categorySlug);
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

    // public function generateTopic(
    //     CategoryService $categoryService,
    //     TopicGenerationService $topicService
    // ) {
    //     try {
    //         $this->isGeneratingTopic = true;

    //         // Use a local variable, no need to call the service method twice
    //         $categoryName = $categoryService->getCategoryBySlug($this->categorySlug);
    //         if (!$categoryName) {
    //             throw new Exception("Category not found");
    //         }

    //         $this->topic = $topicService->generateTopic($categoryName);

    //         session()->flash('message', 'Topic generated successfully!');
    //     } catch (Exception $e) {
    //         Log::error('Topic generation failed', [
    //             'error' => $e->getMessage(),
    //             'category' => $this->categorySlug
    //         ]);
    //         session()->flash('error', 'Failed to generate topic: ' . $e->getMessage());
    //     } finally {
    //         $this->isGeneratingTopic = false;
    //     }
    // }

    public function generate(
        CategoryService $categoryService,
        ScriptGenerationService $scriptService,
        ImageGenerationService $imageService,
        NarrationService $narrationService,
        VideoGenerationService $videoService
    ) {

        // La început, resetăm starea video-ului
        $this->videoUrl = null;
        $this->isProcessing = false;

        // Setăm un timp mai mare de execuție pentru această operațiune
        ini_set('max_execution_time', '300'); // 5 minute
        set_time_limit(300); // 5 minute

        $this->isProcessing = false; // Reset initial state

        // Modificăm validarea să ceară doar topic și category
        $this->validate([
            //'topic' => 'required|min:3',
            'categorySlug' => 'required'
        ]);

        try {
            DB::beginTransaction();

            // Obținem numele categoriei
            $categoryName = $categoryService->getCategoryBySlug($this->categorySlug);
            if (!$categoryName) {
                throw new Exception("Category not found: " . $this->categorySlug);
            }

            // 1. Generăm scriptul
            $this->script = $scriptService->generate($categoryName);

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
                'title' => $this->title ?? $categoryName . " TikTok", // Titlu implicit
                'script' => $this->script,
                'status' => 'processing',
                'image_url' => $this->imageUrl,
                'image_cloudinary_id' => $imageCloudinaryId ?? null,
                'audio_url' => $this->audioUrl,
                'audio_cloudinary_id' => $audioCloudinaryId ?? null
            ]);

            // 5. Generăm videoclipul final
            $videoResult = $videoService->generate($project);

            if ($videoResult['success']) {
                $project->update([
                    'status' => 'rendering',
                    'render_id' => $videoResult['render_id']
                ]);

                $this->render_id = $videoResult['render_id'];
                $this->isProcessing = true;
                $this->videoUrl = null; // Resetăm explicit URL-ul video-ului vechi

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
                //'topic' => $this->topic,
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

                // Forțăm un refresh al componentei
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
    public function render(CategoryService $categoryService)
    {
        return view('livewire.create-tik-tok', [
            'categories' => $categoryService->getCategories()
        ]);
    }
}
