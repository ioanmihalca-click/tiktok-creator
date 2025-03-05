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

            $videoProject->load('images');

            $script = is_string($videoProject->script) ? json_decode($videoProject->script, true) : $videoProject->script;
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON format in videoProject->script: ' . json_last_error_msg());
            }

            $videoDuration = $videoProject->audio_duration;

            // Distribuie durata audio în mod egal între scene dacă avem o durată audio
            if ($videoDuration && isset($script['scenes']) && count($script['scenes']) > 0) {
                // Distribuie durata audio în mod egal între scene
                $sceneCount = count($script['scenes']);
                $baseDuration = $videoDuration / $sceneCount;

                // Actualizează scriptul cu durate egale
                foreach ($script['scenes'] as &$scene) {
                    $scene['duration'] = $baseDuration;
                }
            }

            // Verifică dacă durata este validă
            if ($videoDuration === null || $videoDuration <= 0) {
                Log::warning('Invalid video duration, calculating from script', ['project_id' => $videoProject->id]);

                $videoDuration = 0;
                if (isset($script['scenes']) && is_array($script['scenes'])) {
                    foreach ($script['scenes'] as $scene) {
                        $videoDuration += $scene['duration'] ?? 0;
                    }
                }

                // Asigură-te că avem cel puțin o valoare validă
                $videoDuration = max(1, $videoDuration);

                Log::info('Calculated duration from scenes', ['duration' => $videoDuration]);
            }

            $tracks = [];

            // Adaugă track-ul pentru LOGO (dacă există)
            if ($videoProject->has_watermark) {
                $tracks[] = ['clips' => $this->generateLogoClip($videoDuration)];
            }

            // Adaugă track-ul pentru TEXT
            $tracks[] = ['clips' => $this->generateTextClips($script)];

            // Adaugă track-ul pentru IMAGINI
            $tracks[] = ['clips' => $this->generateImageClips($videoProject)];

            // Adaugă track-ul pentru AUDIO
            $tracks[] = ['clips' => $this->generateAudioClip($videoProject, $videoDuration)];


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

            return [
                'success'   => true,
                'render_id' => $renderId
            ];
        } catch (Exception $e) {
            Log::error('Video generation failed', [
                'error'      => $e->getMessage(),
                'project_id' => $videoProject->id,
                'timeline'   => $timeline ?? null,  // Loghează timeline-ul și în caz de eroare
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
        $imageCount = count($videoProject->images);

        foreach ($videoProject->images as $index => $image) {
            // Determine which transition to use based on image position
            $transition = null;

            if ($index < $imageCount - 1) { // Not the last image
                if ($index % 2 == 0) { // Even index (first, third, etc.)
                    $transition = [
                        'in' => 'fade',
                        'out' => 'shuffleTopRightFast'
                    ];
                } else { // Odd index (second, fourth, etc.)
                    $transition = [
                        'in' => 'shuffleRightBottomFast',
                        'out' => 'shuffleTopLeft'
                    ];
                }
            } else {
                // Last image only has in transition
                $transition = [
                    'in' => 'shuffleRightBottomFast',
                    'out' => 'fade'
                ];
            }

            $imageClips[] = [
                'asset' => [
                    'type' => 'image',
                    'src' => $image->url,
                ],
                'start' => $image->start,
                'length' => $image->duration,
                'fit' => 'cover',
                'effect' => 'zoomIn',
                'transition' => $transition
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
        $currentTime = 0; // Timpul curent (începe de la 0)

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
                'start'     => $currentTime,       // Timpul de start al scenei curente
                'length'    => $scene['duration'], // Durata scenei
                'transition' => ['in' => 'fade', 'out' => 'fade'], // Tranziții fade (opțional)
            ];

            $currentTime += $scene['duration'];  // Trecem la următoarea scenă
        }

        return $clips;
    }

    private function generateLogoClip($videoDuration)
    {
        // Convertește și validează durata
        $duration = is_numeric($videoDuration) ? (float)$videoDuration : 0;
        $duration = max(0.1, $duration);

        return [
            [
                'asset' => [
                    'type' => 'image',
                    'src' => 'https://res.cloudinary.com/dpxess5iw/image/upload/v1741154703/logo-clips_umdxz3.png',
                ],
                'start' => 0,
                'length' => $duration,
                'scale' => 0.15,
                'position' => 'center',
                'offset' => [
                    'x' => 0,
                    'y' => 0.4
                ],
                'opacity' => 0.5
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

            $project->update([
                'audio_cloudinary_id' => null // Doar audio_cloudinary_id trebuie setat la null aici
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
