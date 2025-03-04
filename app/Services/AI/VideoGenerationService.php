<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Exception;

class VideoGenerationService
{
    protected $apiKey;
    protected $sandboxApiKey;
    protected $productionApiKey;
    protected $baseUrl;
    protected $sandboxBaseUrl = 'https://api.shotstack.io/stage'; //development
    protected $productionBaseUrl = 'https://api.shotstack.io/v1'; //production

    public function __construct()
    {
        $this->sandboxApiKey = config('services.shotstack.key');
        $this->productionApiKey = config('services.shotstack.production_key');
        $this->apiKey = $this->sandboxApiKey;
        $this->baseUrl = $this->sandboxBaseUrl;
    }

    public function setEnvironment(string $environment)
    {
        $this->apiKey = ($environment === 'production') ? $this->productionApiKey : $this->sandboxApiKey;
        $this->baseUrl = ($environment === 'production') ? $this->productionBaseUrl : $this->sandboxBaseUrl;
    }

    public function generate($videoProject)
    {
        $this->setEnvironment($videoProject->environment_type);

        try {
            Log::info('Starting video generation', ['project_id' => $videoProject->id]);

            $script = is_string($videoProject->script) ? json_decode($videoProject->script, true) : $videoProject->script;
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON format in videoProject->script: ' . json_last_error_msg());
            }

            // Folosim durata audio REALĂ, dacă există
            $videoDuration = $videoProject->audio_duration ?? (float) ($script['total_duration'] ?? 15) + 2;

            $tracks = [];

            if ($videoProject->has_watermark) {
                $tracks[] = ['clips' => $this->generateLogoClip($videoDuration)];
            }

            // MODIFICARE MAJORĂ AICI: Creăm clipurile pentru imagini și text *împreună*
            $tracks[] = [
                'clips' => array_merge(
                    $this->generateImageClips($videoProject), // Folosim metoda corectată
                    $this->generateTextClips($script),
                    $this->generateAudioClip($videoProject, $videoDuration)
                )
            ];

            $timeline = [
                'background' => '#000000',
                'tracks' => $tracks
            ];

            $output = [
                'format'      => 'mp4',
                'resolution'  => 'hd',
                'aspectRatio' => '9:16'
            ];

            $response = Http::timeout(120)->withHeaders([
                'x-api-key'    => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/render', [
                'timeline' => $timeline,
                'output'   => $output
            ]);

            if (!$response->successful()) {
                throw new Exception('Shotstack API error: ' . $response->body());
            }

            $renderId = $response->json()['response']['id'];

            Log::info('Video render started', [
                'project_id' => $videoProject->id,
                'render_id'  => $renderId,
                'timeline'   => $timeline,
                'output'     => $output
            ]);

            return [
                'success'   => true,
                'render_id' => $renderId
            ];
        } catch (Exception $e) {
            Log::error('Video generation failed', [
                'error'      => $e->getMessage(),
                'project_id' => $videoProject->id,
                'timeline'   => $timeline ?? null,
                'output'     => $output ?? null
            ]);

            return [
                'success' => false,
                'error'   => $e->getMessage()
            ];
        }
    }

    private function generateImageClips($videoProject)
    {
        $imageClips = [];

        // Folosim relația $videoProject->images:
        foreach ($videoProject->images as $image) { // $image este acum un obiect VideoImage
            $imageClips[] = [
                'asset' => [
                    'type' => 'image',
                    // Folosim corect câmpul cloudinary_id sau construim URL-ul complet dacă este necesar
                    'src' => $image->cloudinary_id ? "https://res.cloudinary.com/yourcloudname/image/upload/{$image->cloudinary_id}" : null,
                ],
                'start' => $image->start,
                'length' => $image->duration,
                'fit' => 'cover',
                'effect' => 'zoomIn'
            ];
        }

        return $imageClips;
    }


    private function generateAudioClip($videoProject, $videoDuration)
    {
        return empty($videoProject->audio_url) ? [] : [
            [
                'asset' => ['type' => 'audio', 'src' => $videoProject->audio_url],
                'start' => 0,
                'length' => $videoDuration,
            ]
        ];
    }

    private function generateTextClips($script)
    {
        $clips = [];
        $currentTime = 0;

        if (!isset($script['scenes']) || !is_array($script['scenes'])) {
            Log::warning('Invalid script format: Missing or invalid "scenes" array.', ['script' => $script]);
            return [];
        }

        foreach ($script['scenes'] as $scene) {
            if (!isset($scene['text'], $scene['duration'])) {
                Log::warning('Invalid scene format: Missing required fields.', ['scene' => $scene]);
                continue;
            }

            $words = explode(" ", $scene['text']);
            $lines = [];
            $currentLine = "";
            $maxLineWidth = 25;

            foreach ($words as $word) {
                if (strlen($currentLine) + strlen($word) + 1 <= $maxLineWidth) {
                    $currentLine .= ($currentLine === "" ? "" : " ") . $word;
                } else {
                    $lines[] = $currentLine;
                    $currentLine = $word;
                }
            }
            $lines[] = $currentLine;

            $html = '<div style="width: 100%; text-align: center; position: absolute; bottom: 20px;">';
            foreach ($lines as $line) {
                $html .= '<p style="margin: 5px 0; padding: 10px; font-size: 40px; font-family: Roboto, sans-serif; color: white; background-color: rgba(0, 0, 0, 0.7); border-radius: 15px; display: inline-block; text-transform: uppercase;">' .
                    htmlspecialchars($line) .
                    '</p>';
            }
            $html .= '</div>';

            $htmlAsset = [
                'type'      => 'html',
                'html'      => $html,
                'width'     => 900,
                'height'    => 500,
                'background' => 'transparent'
            ];

            $clips[] = [
                'asset'     => $htmlAsset,
                'start'     => $currentTime,
                'length'    => $scene['duration'],
                'transition' => ['in' => 'fade', 'out' => 'fade'],
            ];

            $currentTime += $scene['duration'];
        }

        return $clips;
    }

    private function generateLogoClip($videoDuration)
    {
        return [
            [
                'asset' => [
                    'type'  => 'image',
                    'src'   => 'https://res.cloudinary.com/dpxess5iw/image/upload/v1739911905/logo-transparent_xiqqe0.png',
                ],
                'start'  => 0,
                'length' => $videoDuration,
                'fit'    => 'contain',
                'position' => 'top',
                'opacity' => 0.5,
                'scale'  => 0.7
            ]
        ];
    }

    public function checkStatus($renderId)
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey
            ])->get($this->baseUrl . '/render/' . $renderId);

            if (!$response->successful()) {
                throw new Exception('Shotstack API error: ' . $response->body());
            }

            $data = $response->json()['response'];

            if ($data['status'] === 'done') {
                return [
                    'success' => true,
                    'status' => 'done',
                    'url' => $data['url']
                ];
            } elseif ($data['status'] === 'failed') {
                return [
                    'success' => false,
                    'status' => 'failed',
                    'error' => $data['error'] ?? 'Unknown error'
                ];
            }

            return [
                'success' => true,
                'status' => $data['status']
            ];
        } catch (Exception $e) {
            Log::error('Failed to check render status', [
                'error' => $e->getMessage(),
                'render_id' => $renderId
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function cleanupResources($project)
    {
        try {
            // Stergem imaginile asociate, folosind relația:
            foreach ($project->images as $image) {
                if ($image->cloudinary_id) {
                    Log::info('Attempting to delete image from Cloudinary', [
                        'image_cloudinary_id' => $image->cloudinary_id
                    ]);
                    Cloudinary::destroy($image->cloudinary_id);
                    Log::info('Cleaned up image from Cloudinary', [
                        'project_id' => $project->id,
                        'cloudinary_id' => $image->cloudinary_id
                    ]);
                }
                $image->delete(); // Ștergem înregistrarea din baza de date
            }

            // Cleanup audio - specifică tipul "video" pentru resursele audio
            if ($project->audio_cloudinary_id) {
                Log::info('Attempting to delete audio from Cloudinary', [
                    'audio_cloudinary_id' => $project->audio_cloudinary_id,
                    'resource_type' => 'video'
                ]);
                Cloudinary::destroy($project->audio_cloudinary_id, ['resource_type' => 'video']);
                Log::info('Cleaned up audio from Cloudinary', [
                    'project_id' => $project->id,
                    'cloudinary_id' => $project->audio_cloudinary_id
                ]);
            }

            // Update project to clear Cloudinary IDs

            $project->update([

                'image_cloudinary_id' => null,
                'audio_cloudinary_id' => null
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Failed to cleanup Cloudinary resources', [
                'error' => $e->getMessage(),
                'project_id' => $project->id
            ]);
            return false;
        }
    }
}
