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

            $videoProject->load('images');

            $script = is_string($videoProject->script) ? json_decode($videoProject->script, true) : $videoProject->script;
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON format in videoProject->script: ' . json_last_error_msg());
            }

            $videoDuration = $videoProject->audio_duration;

            // Adaugă un log detaliat pentru a vedea valoarea exactă
            Log::info('Video duration info', [
                'project_id' => $videoProject->id,
                'audio_duration' => $videoDuration,
                'audio_duration_type' => gettype($videoDuration),
                'is_null' => $videoDuration === null ? 'yes' : 'no',
                'audio_url' => $videoProject->audio_url,
                'audio_cloudinary_id' => $videoProject->audio_cloudinary_id
            ]);

            // Verifică dacă cumva durata este un string și nu poate fi convertită
            if (is_string($videoDuration)) {
                $videoDuration = floatval($videoDuration);
                Log::info('Converted string duration to float', ['new_duration' => $videoDuration]);
            }

            // Verifică și repară dacă durata este totuși null sau zero
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

            // Adaugă un log înainte de a construi primul track
            Log::info('Building tracks with duration', ['video_duration' => $videoDuration]);

            // 1. Adaugă track-ul pentru LOGO (dacă există)
            if ($videoProject->has_watermark) {
                $logoClips = $this->generateLogoClip($videoDuration);
                Log::info('Generated logo clip', ['clips' => $logoClips]);
                $tracks[] = ['clips' => $logoClips];
            }

            // 2. Adaugă track-ul pentru TEXT
            $tracks[] = ['clips' => $this->generateTextClips($script)];

            // 3. Adaugă track-ul pentru IMAGINI
            $tracks[] = ['clips' => $this->generateImageClips($videoProject)];

            // 4. Adaugă track-ul pentru AUDIO
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

            Log::info('Shotstack timeline', ['timeline' => $timeline]);

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
                'render_id'  => $renderId
            ]);

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

        Log::info('Generating image clips', [
            'project_id' => $videoProject->id,
            'image_count' => $videoProject->images->count() // Numărul de imagini
        ]);

        foreach ($videoProject->images as $image) {
            Log::info('Processing image for clip', [
                'image_id' => $image->id,
                'url' => $image->url,
                'start' => $image->start,
                'duration' => $image->duration
            ]);

            $imageClips[] = [
                'asset' => [
                    'type' => 'image',
                    'src' => $image->url,
                ],
                'start' => $image->start,
                'length' => $image->duration,
                'fit' => 'cover',
                'effect' => 'zoomIn'
            ];
        }
        Log::info('Image clips generated', [
            'clip_count' => count($imageClips) // Numărul de clipuri generate
        ]);

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

        foreach ($script['scenes'] as $index => $scene) { // Adăugăm indexul
            Log::info('Processing text scene', ['index' => $index, 'scene' => $scene]);

            if (!isset($scene['text'], $scene['duration'])) {
                Log::warning('Invalid scene format: Missing required fields.', ['scene' => $scene]);
                continue;
            }

            // Simplificăm HTML-ul și folosim position: absolute *corect*:
            $html = '<div style="position: absolute; width: 70%; left: 10%; top: 70%;  color: yellow; font-size: 30px; font-family: Arial, sans-serif;  text-align: center; background-color: rgba(0,0,0,0.5);">';
            $html .= htmlspecialchars($scene['text']); // O singură linie, fără împărțire, fără <p>
            $html .= '</div>';

            Log::info('Text HTML', ['html' => $html]); // Verificăm HTML-ul generat

            $htmlAsset = [
                'type'      => 'html',
                'html'      => $html,
                'width'     => 854, // Setăm lățimea la 854 (pentru 9:16, la rezoluția HD)
                'height'    => 480, // Setăm înălțimea la 480
                'background' => 'transparent'
            ];

            $clips[] = [
                'asset'     => $htmlAsset,
                'start'     => $currentTime,
                'length'    => $scene['duration'],
                'transition' => ['in' => 'fade', 'out' => 'fade'],
            ];

            Log::info('Text clip generated', ['clip' => $clips[$index] ?? null]); // Loghează clipul generat
            $currentTime += $scene['duration'];
        }

        Log::info('All text clips generated', ['clips' => $clips]);
        return $clips;
    }

    private function generateLogoClip($videoDuration)
    {
        // Convertește și validează durata
        $duration = is_numeric($videoDuration) ? (float)$videoDuration : 0;
        $duration = max(0.1, $duration); // Asigură-te că durata nu este zero

        Log::info('Generating logo clip with duration', ['duration' => $duration]);

        return [
            [
                'asset' => [
                    'type'  => 'image',
                    'src'   => 'https://res.cloudinary.com/dpxess5iw/image/upload/v1741154703/logo-clips_umdxz3.png',
                ],
                'start'  => 0,
                'length' => $duration,
                'fit'    => 'contain',
                "offset" => [
                    "x" => 0,
                    "y" => 0.25
                ],
                "position" => "center",
                'opacity' => 0.5,
                'scale'  => 0.15
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
