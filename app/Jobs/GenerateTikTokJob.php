<?php

namespace App\Jobs;

use App\Models\VideoProject;
use App\Models\User;
use App\Services\AI\CategoryService;
use App\Services\AI\ScriptGenerationService;
use App\Services\AI\ImageGenerationService;
use App\Services\AI\NarrationService;
use App\Services\AI\VideoGenerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class GenerateTikTokJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Numărul maxim de încercări pentru acest job.
     *
     * @var int
     */
    public $tries = 2;

    /**
     * Timeout-ul în secunde.
     *
     * @var int
     */
    public $timeout = 600; // 10 minute

    protected $user;
    protected $categorySlug;
    protected $title;
    protected $existingProjectId;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param string $categorySlug
     * @param string|null $title
     * @param int|null $existingProjectId ID-ul unui proiect existent
     * @return void
     */
    public function __construct(User $user, string $categorySlug, ?string $title = null, ?int $existingProjectId = null)
    {
        $this->user = $user;
        $this->categorySlug = $categorySlug;
        $this->title = $title;
        $this->existingProjectId = $existingProjectId;
    }

    /**
     * Execute the job.
     *
     * @param CategoryService $categoryService
     * @param ScriptGenerationService $scriptService
     * @param ImageGenerationService $imageService
     * @param NarrationService $narrationService
     * @param VideoGenerationService $videoService
     * @return void
     */
    public function handle(
        CategoryService $categoryService,
        ScriptGenerationService $scriptService,
        ImageGenerationService $imageService,
        NarrationService $narrationService,
        VideoGenerationService $videoService
    ) {
        Log::info('Starting GenerateTikTokJob', [
            'user_id' => $this->user->id,
            'category_slug' => $this->categorySlug,
            'existing_project_id' => $this->existingProjectId
        ]);

        try {
            DB::beginTransaction();

            // Obținem calea completă a categoriei
            $categoryName = $categoryService->getCategoryFullPath($this->categorySlug);
            if (!$categoryName) {
                throw new Exception("Category not found: " . $this->categorySlug);
            }

            // Generăm scriptul
            $script = $scriptService->generate($categoryName);
            if (!$script) {
                throw new Exception("Script generation failed");
            }

            // Generăm imaginea
            $imageUrl = null;
            $imageCloudinaryId = null;
            if (isset($script['background_prompt'])) {
                $imageResult = $imageService->generateImage($script['background_prompt']);
                if (!$imageResult['success']) {
                    throw new Exception("Image generation failed: " . ($imageResult['error'] ?? 'Unknown error'));
                }
                $imageUrl = $imageResult['image_url'];
                $imageCloudinaryId = $imageResult['cloudinary_public_id'];
            }

            // Generăm narațiunea audio
            $fullNarration = '';
            foreach ($script['scenes'] as $scene) {
                $fullNarration .= $scene['narration'] . " ";
            }

            $narrationResult = $narrationService->generate($fullNarration);
            if ($narrationResult['status'] !== 'success') {
                throw new Exception("Narration generation failed");
            }

            $audioUrl = $narrationResult['audio_url'];
            $audioCloudinaryId = $narrationResult['cloudinary_public_id'];
            $audioDuration = $narrationResult['audio_duration'];

            // Verificăm dacă trebuie să utilizăm un proiect existent sau să creăm unul nou
            if ($this->existingProjectId) {
                $project = VideoProject::find($this->existingProjectId);
                
                if (!$project || $project->user_id !== $this->user->id) {
                    throw new Exception("Project not found or does not belong to the user");
                }
                
                // Actualizăm proiectul existent
                $project->update([
                    'script' => $script,
                    'image_url' => $imageUrl,
                    'image_cloudinary_id' => $imageCloudinaryId ?? null,
                    'audio_url' => $audioUrl,
                    'audio_cloudinary_id' => $audioCloudinaryId ?? null,
                    'audio_duration' => $audioDuration ?? null
                ]);
            } else {
                // Creăm un proiect nou
                $project = $this->user->videoProjects()->create([
                    'title' => $this->title ?? $categoryName . " TikTok",
                    'script' => $script,
                    'status' => 'processing',
                    'image_url' => $imageUrl,
                    'image_cloudinary_id' => $imageCloudinaryId ?? null,
                    'audio_url' => $audioUrl,
                    'audio_cloudinary_id' => $audioCloudinaryId ?? null,
                    'audio_duration' => $audioDuration ?? null
                ]);
            }

            // Generăm videoclipul
            $videoResult = $videoService->generate($project);
            if (!$videoResult['success']) {
                throw new Exception("Video generation failed: " . ($videoResult['error'] ?? 'Unknown error'));
            }

            $project->update([
                'status' => 'rendering',
                'render_id' => $videoResult['render_id']
            ]);

            DB::commit();
            
            // Dispatchăm imediat un job pentru a verifica statusul
            CheckTikTokStatusJob::dispatch($project->id)->delay(now()->addSeconds(10));
            
            Log::info('GenerateTikTokJob completed successfully', [
                'project_id' => $project->id,
                'render_id' => $videoResult['render_id']
            ]);
            
        } catch (Exception $e) {
            DB::rollBack();
            
            // Dacă avem un proiect existent, actualizăm statusul la failed
            if ($this->existingProjectId) {
                try {
                    VideoProject::where('id', $this->existingProjectId)
                        ->where('user_id', $this->user->id)
                        ->update(['status' => 'failed']);
                } catch (Exception $innerException) {
                    Log::error('Failed to update project status', [
                        'error' => $innerException->getMessage(),
                        'project_id' => $this->existingProjectId
                    ]);
                }
            }
            
            Log::error('GenerateTikTokJob failed', [
                'error' => $e->getMessage(),
                'category' => $this->categorySlug,
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e; // Re-aruncăm excepția pentru a marca job-ul ca eșuat
        }
    }
}