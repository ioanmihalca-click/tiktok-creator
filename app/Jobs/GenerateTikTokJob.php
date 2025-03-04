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

            // MODIFICĂRI AICI: Iterăm prin scene și generăm imaginile
            $images = [];
            foreach ($script['scenes'] as $index => $scene) {
                Log::info("Processing scene {$index}", ['scene' => $scene]);

                if (empty($scene['image_prompt'])) {
                    throw new Exception("Image prompt for scene {$index} is missing");
                }

                $imageResult = $imageService->generateImage($scene['image_prompt']); // Apelăm noua metodă

                Log::info("Image generation result for scene {$index}", ['result' => $imageResult]);

                if (!$imageResult['success']) {
                    throw new Exception("Image generation failed for scene {$index}: " . ($imageResult['error'] ?? 'Unknown error'));
                }

                // STOCĂM prediction_id ÎN ARRAY-UL DE IMAGINI!
                $images[] = [
                    'prediction_id' => $imageResult['prediction_id'], // Foarte important!
                    'url' => null, // Inițializăm cu null
                    'cloudinary_id' => null, // Inițializăm cu null
                    'start' => $index === 0 ? 0 : $script['scenes'][$index - 1]['duration'],
                    'duration' => $scene['duration']
                ];
                if ($index > 0) {
                    $images[$index]['start'] +=  $images[$index - 1]['start'];
                }
            }
            // Stocăm prediction ID-ul *ultimei* imagini generate în VideoProject (opțional, dar util)
            //$project->update(['temp_prediction_id' => $imageResult['prediction_id']]);


            Log::info('Images generated', ['images' => $images]);


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
            $audioDuration = $narrationResult['audio_duration'];

            if ($this->existingProjectId) {
                $project = VideoProject::find($this->existingProjectId);

                if (!$project || $project->user_id !== $this->user->id) {
                    throw new Exception("Project not found or does not belong to the user");
                }

                $project->update([
                    'script' => $script,
                    'images' => $images, // STOCĂM ARRAY-UL CU IMAGINI
                    'image_url' => null,          // Setam la null vechiul camp
                    'image_cloudinary_id' => null, // Setam la null vechiul camp
                    'audio_url' => $audioUrl,
                    'audio_cloudinary_id' => $audioCloudinaryId ?? null,
                    'audio_duration' => $audioDuration ?? null,
                    'category_id' => $categoryId
                ]);
            } else {
                $project = $this->user->videoProjects()->create([
                    'title' => $this->title ?? $categoryName . " TikTok",
                    'script' => $script,
                    'status' => 'processing',
                    'images' => $images, // STOCĂM ARRAY-UL CU IMAGINI
                    'image_url' => null, //NU MAI AVEM NEVOIE
                    'image_cloudinary_id' => null, //NU MAI AVEM NEVOIE
                    'audio_url' => $audioUrl,
                    'audio_cloudinary_id' => $audioCloudinaryId ?? null,
                    'audio_duration' => $audioDuration ?? null,
                    'category_id' => $categoryId,
                    'environment_type' => $environmentType,
                    'has_watermark' => $hasWatermark
                ]);
            }

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

            DB::commit();

            CheckTikTokStatusJob::dispatch($project->id)->delay(now()->addSeconds(10));

            Log::info('GenerateTikTokJob completed successfully', [
                'project_id' => $project->id,
                'render_id' => $videoResult['render_id'],
                'credit_type' => $creditType,
                'environment_type' => $environmentType,
                'has_watermark' => $hasWatermark
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

            throw $e;
        }
    }
}
