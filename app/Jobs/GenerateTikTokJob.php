<?php

namespace App\Jobs;

use App\Models\VideoProject;
use App\Models\User;
use App\Services\AI\CategoryService;
use App\Services\AI\ScriptGenerationService;
use App\Services\AI\ImageGenerationService;
use App\Services\AI\NarrationService;
use App\Services\AI\VideoGenerationService;
use App\Services\CreditService;
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

    public $tries = 2;
    public $timeout = 600;

    protected $user;
    protected $categorySlug;
    protected $title;
    protected $existingProjectId;
    protected $voiceId;

    public function __construct(User $user, string $categorySlug, ?string $title = null, ?int $existingProjectId = null, ?string $voiceId = null)
    {
        $this->user = $user;
        $this->categorySlug = $categorySlug;
        $this->title = $title;
        $this->existingProjectId = $existingProjectId;
        $this->voiceId = $voiceId;
    }

    public function handle(
        CategoryService $categoryService,
        ScriptGenerationService $scriptService,
        ImageGenerationService $imageService,
        NarrationService $narrationService,
        VideoGenerationService $videoService,
        CreditService $creditService
    ) {
        Log::info('Starting GenerateTikTokJob', [
            'user_id' => $this->user->id,
            'category_slug' => $this->categorySlug,
            'existing_project_id' => $this->existingProjectId
        ]);

        try {
            $creditType = $creditService->checkCreditType($this->user);
            if (!$creditType) {
                throw new Exception("User has no available credits");
            }

            $environmentType = $creditService->getEnvironmentType($this->user);
            $hasWatermark = $creditService->shouldHaveWatermark($this->user);
            DB::beginTransaction();

            $categoryName = $categoryService->getCategoryFullPath($this->categorySlug);
            if (!$categoryName) {
                throw new Exception("Category not found: " . $this->categorySlug);
            }

            $category = $categoryService->getCategoryBySlug($this->categorySlug);
            $categoryId = $category ? $category->id : null;

            $script = $scriptService->generate($categoryName, $this->user->id);
            if (!$script) {
                throw new Exception("Script generation failed");
            }


            if ($this->existingProjectId) {
                $project = VideoProject::find($this->existingProjectId);

                if (!$project || $project->user_id !== $this->user->id) {
                    throw new Exception("Project not found or does not belong to the user");
                }
                // Stergem imaginile existente inainte de a crea altele noi (daca exista)
                foreach ($project->images as $image) {
                    $image->delete();
                }

                $project->update([
                    'script' => $script,
                    'audio_url' => null,
                    'audio_cloudinary_id' => null,
                    'audio_duration' => null,
                    'category_id' => $categoryId
                ]);
            } else {
                $project = $this->user->videoProjects()->create([
                    'title' => $this->title ?? $categoryName . " TikTok",
                    'script' => $script,
                    'status' => 'processing',
                    'audio_url' => null,
                    'audio_cloudinary_id' => null,
                    'audio_duration' => null,
                    'category_id' => $categoryId,
                    'environment_type' => $environmentType,
                    'has_watermark' => $hasWatermark
                ]);
            }

            foreach ($script['scenes'] as $index => $scene) {
                Log::info("Processing scene {$index}", ['scene' => $scene]);

                if (empty($scene['image_prompt'])) {
                    throw new Exception("Image prompt for scene {$index} is missing");
                }

                $imageResult = $imageService->generateImage($scene['image_prompt']);

                Log::info("Image generation result for scene {$index}", ['result' => $imageResult]);

                if (!$imageResult['success']) {
                    throw new Exception("Image generation failed for scene {$index}: " . ($imageResult['error'] ?? 'Unknown error'));
                }

                // Corecție calcul startTime:
                $startTime = 0;
                for ($i = 0; $i < $index; $i++) {
                    $startTime += $script['scenes'][$i]['duration'];
                }
                // SAU, varianta mai concisă:
                //$startTime = array_sum(array_column(array_slice($script['scenes'], 0, $index), 'duration'));


                $project->images()->create([
                    'url' => $imageResult['image_url'],
                    'cloudinary_id' => $imageResult['cloudinary_public_id'],
                    'start' => $startTime,
                    'duration' => $scene['duration'],
                    'order' => $index,
                ]);
            }

            $fullNarration = '';
            foreach ($script['scenes'] as $scene) {
                $fullNarration .= $scene['narration'] . " ";
            }

            $narrationResult = $narrationService->generate($fullNarration, $this->voiceId);
            if ($narrationResult['status'] !== 'success') {
                throw new Exception("Narration generation failed");
            }

            $audioUrl = $narrationResult['audio_url'];
            $audioCloudinaryId = $narrationResult['cloudinary_public_id'];
            $audioDuration = $narrationResult['audio_duration']; // Durata EXACTĂ (de la getID3)

            // Adaugă aceste log-uri pentru debugging
            Log::info('Narration result details', [
                'status' => $narrationResult['status'],
                'audio_url' => $narrationResult['audio_url'],
                'audio_duration' => $narrationResult['audio_duration'],
                'audio_duration_type' => gettype($narrationResult['audio_duration'])
            ]);

            // Verifică dacă durata este salvată corect
            $project->update([
                'audio_url' => $audioUrl,
                'audio_cloudinary_id' => $audioCloudinaryId,
                'audio_duration' => $audioDuration,
            ]);

            // Adaugă un log după update pentru a verifica dacă durata a fost salvată
            $updatedProject = VideoProject::find($project->id);
            Log::info('Project after audio update', [
                'project_id' => $updatedProject->id,
                'audio_duration' => $updatedProject->audio_duration,
                'audio_duration_type' => gettype($updatedProject->audio_duration)
            ]);


            DB::commit(); // Commit *înainte* de a genera video

            $videoResult = $videoService->generate($project);
            if (!$videoResult['success']) {
                throw new Exception("Video generation failed: " . ($videoResult['error'] ?? 'Unknown error'));
            }

            $project->update([
                'status' => 'rendering',
                'render_id' => $videoResult['render_id']
            ]);


            if ($creditType === 'free') {
                $this->user->userCredit->increment('used_free_credits');
                $transactionDescription = 'Used 1 free credit for video generation';
            } else {
                $this->user->userCredit->increment('used_credits');
                $transactionDescription = 'Used 1 paid credit for video generation';
            }

            $this->user->creditTransactions()->create([
                'transaction_type' => 'usage',
                'amount' => -1,
                'description' => $transactionDescription
            ]);


            CheckTikTokStatusJob::dispatch($project->id); // FARA DELAY

            Log::info('GenerateTikTokJob completed successfully', [
                'project_id' => $project->id,
                'render_id' => $videoResult['render_id'],
                // 'credit_type' => $creditType, // Comentat temporar
                // 'environment_type' => $environmentType, // Comentat temporar
                // 'has_watermark' => $hasWatermark // Comentat temporar
            ]);
        } catch (Exception $e) {
            DB::rollBack();

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
