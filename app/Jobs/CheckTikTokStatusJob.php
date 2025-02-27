<?php

namespace App\Jobs;

use App\Models\VideoProject;
use App\Services\AI\VideoGenerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;

class CheckTikTokStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Numărul maxim de încercări pentru acest job.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * Timeout-ul în secunde.
     *
     * @var int
     */
    public $timeout = 30;

    /**
     * ID-ul proiectului video.
     *
     * @var int
     */
    protected $projectId;

    /**
     * Create a new job instance.
     *
     * @param int $projectId
     * @return void
     */
    public function __construct(int $projectId)
    {
        $this->projectId = $projectId;
    }

    /**
     * Execute the job.
     *
     * @param VideoGenerationService $videoService
     * @return void
     */
    public function handle(VideoGenerationService $videoService)
    {
        Log::info('Starting CheckTikTokStatusJob', ['project_id' => $this->projectId]);

        try {
            $project = VideoProject::findOrFail($this->projectId);

            if ($project->status !== 'rendering' || !$project->render_id) {
                Log::info('Video project is not in rendering state or has no render_id', [
                    'project_id' => $this->projectId,
                    'status' => $project->status,
                    'render_id' => $project->render_id
                ]);
                return;
            }

            $status = $videoService->checkStatus($project->render_id);

            if ($status['success'] && $status['status'] === 'done') {
                $project->update([
                    'status' => 'completed',
                    'video_url' => $status['url']
                ]);

                // Curățăm resursele Cloudinary după generarea cu succes a videoclipului
                $cleanupResult = $videoService->cleanupResources($project);
                
                if ($cleanupResult) {
                    Log::info('Successfully cleaned up Cloudinary resources', ['project_id' => $project->id]);
                } else {
                    Log::warning('Failed to clean up some Cloudinary resources', ['project_id' => $project->id]);
                }

                Log::info('Video is ready', [
                    'project_id' => $project->id,
                    'video_url' => $status['url']
                ]);
            } elseif (!$status['success'] || $status['status'] === 'failed') {
                $project->update(['status' => 'failed']);
                Log::error('Video generation failed', [
                    'project_id' => $project->id,
                    'error' => $status['error'] ?? 'Unknown error'
                ]);
            } else {
                // Dacă videoul încă se procesează, reprogramăm job-ul pentru verificare
                Log::info('Video still processing, status: ' . $status['status'], [
                    'project_id' => $project->id
                ]);
                
                // Reprogramăm job-ul pentru verificare peste 30 secunde
                CheckTikTokStatusJob::dispatch($this->projectId)
                    ->delay(now()->addSeconds(30));
            }
        } catch (Exception $e) {
            Log::error('CheckTikTokStatusJob failed', [
                'project_id' => $this->projectId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }
}