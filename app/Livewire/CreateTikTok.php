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
use Livewire\Attributes\Locked;

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

    #[Locked]
    public string $processStep = 'idle'; // New property
    
    #[Locked]
    public array $processSteps = [
        'idle' => 'Waiting to start',
        'script' => 'Creating script',
        'image' => 'Generating visuals',
        'audio' => 'Creating narration',
        'video' => 'Preparing final video'
    ];

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
        $this->isProcessing = true;
        $this->processStep = 'script';

        ini_set('max_execution_time', '300');
        set_time_limit(300);

        $this->validate([
            'categorySlug' => 'required'
        ]);

        try {
            DB::beginTransaction();

            $categoryName = $this->categoryService->getCategoryFullPath($this->categorySlug);
            if (!$categoryName) {
                throw new Exception("Category not found: " . $this->categorySlug);
            }

            // Generate script
            $this->script = $this->scriptService->generate($categoryName);
            if (!$this->script) {
                throw new Exception("Script generation failed");
            }
            
            // Generate image
            $this->processStep = 'image';
            if (isset($this->script['background_prompt'])) {
                $imageResult = $this->imageService->generateImage($this->script['background_prompt']);
                if (!$imageResult['success']) {
                    throw new Exception("Image generation failed: " . ($imageResult['error'] ?? 'Unknown error'));
                }
                $this->imageUrl = $imageResult['image_url'];
                $imageCloudinaryId = $imageResult['cloudinary_public_id'];
            }

            // Generate audio
            $this->processStep = 'audio';
            $fullNarration = '';
            foreach ($this->script['scenes'] as $scene) {
                $fullNarration .= $scene['narration'] . " ";
            }

            $narrationResult = $this->narrationService->generate($fullNarration);
            if ($narrationResult['status'] !== 'success') {
                throw new Exception("Narration generation failed");
            }
            
            $this->audioUrl = $narrationResult['audio_url'];
            $audioCloudinaryId = $narrationResult['cloudinary_public_id'];
            $audioDuration = $narrationResult['audio_duration'];

            // Create project and generate video
            $this->processStep = 'video';
            $project = Auth::user()->videoProjects()->create([
                'title' => $this->title ?? $categoryName . " TikTok",
                'script' => $this->script,
                'status' => 'processing',
                'image_url' => $this->imageUrl,
                'image_cloudinary_id' => $imageCloudinaryId ?? null,
                'audio_url' => $this->audioUrl,
                'audio_cloudinary_id' => $audioCloudinaryId ?? null,
                'audio_duration' => $audioDuration ?? null
            ]);

            $videoResult = $this->videoService->generate($project);
            if (!$videoResult['success']) {
                throw new Exception("Video generation failed: " . ($videoResult['error'] ?? 'Unknown error'));
            }

            $project->update([
                'status' => 'rendering',
                'render_id' => $videoResult['render_id']
            ]);

            $this->render_id = $videoResult['render_id'];
            DB::commit();
            
            session()->flash('message', 'Video creation started successfully!');
            
        } catch (Exception $e) {
            $this->isProcessing = false;
            $this->processStep = 'idle';
            DB::rollBack();
            Log::error('TikTok generation failed', [
                'error' => $e->getMessage(),
                'step' => $this->processStep,
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
        return view('livewire.create-tik-tok', [
            'categories' => $this->getAvailableCategories()
        ]);
    }

    public function updatedCategorySlug($value)
    {
        $this->reset(['script', 'imageUrl', 'audioUrl', 'videoUrl', 'isProcessing']);
    }

    public function setCategory($slug)
    {
        $this->reset(['script', 'imageUrl', 'audioUrl', 'videoUrl', 'isProcessing']);
        $this->categorySlug = $slug;
    }
}