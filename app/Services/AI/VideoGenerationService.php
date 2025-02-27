<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Exception;

class VideoGenerationService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.shotstack.io/stage'; // pentru development

    public function __construct()
    {
        $this->apiKey = config('services.shotstack.key');
    }

    public function generate($videoProject)
    {
        try {
            Log::info('Starting video generation', ['project_id' => $videoProject->id]);

            if (is_string($videoProject->script)) {
                $script = json_decode($videoProject->script, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception('Invalid JSON format in videoProject->script: ' . json_last_error_msg());
                }
            } else {
                $script = $videoProject->script;
            }

            // Folosim durata audio REALĂ, dacă există; altfel, fallback (cu marjă, dacă nu folosim getID3):
            $videoDuration = $videoProject->audio_duration ?? (float) ($script['total_duration'] ?? 15) + 2;


            $timeline = [
                'background' => '#000000', // Opțional, culoarea de fundal a videoclipului
                'tracks'     => [
                    [  // Track 1: Logo-ul (watermark-ul) - va fi afișat DEASUPRA
                        'clips' => $this->generateLogoClip($videoDuration)
                    ],
                    [  // Track 2: Imaginea, textul și audio-ul - vor fi afișate SUB logo
                        'clips' => array_merge(
                            $this->generateImageClip($videoProject, $videoDuration),
                            $this->generateTextClips($script),
                            $this->generateAudioClip($videoProject, $videoDuration)
                        )
                    ]
                ]
            ];


            $output = [
                'format'      => 'mp4',
                'resolution'  => 'hd',
                'aspectRatio' => '9:16'
            ];

            $response = Http::timeout(120)->withHeaders([ // Timeout mai mare
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
                'timeline'   => $timeline, // Loghează timeline-ul
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
    private function generateImageClip($videoProject, $videoDuration)
    {
        return [
            [
                'asset'  => [
                    'type' => 'image',
                    'src'  => $videoProject->image_url
                ],
                'start'  => 0,
                'length' => $videoDuration, // Durata exactă a videoclipului
                'fit'    => 'cover',        // Acoperă întregul cadru (crop dacă e necesar)
                'effect' => 'zoomIn'      // Efect de zoom (opțional)
            ]
        ];
    }
    private function generateAudioClip($videoProject, $videoDuration){

        if(!$videoProject->audio_url){
            return []; // Returneaza un array gol daca nu exista audio
        }

        return [
            [
                'asset' => [
                    'type' => 'audio',
                    'src' => $videoProject->audio_url,
                ],
                'start' => 0,
                'length' => $videoDuration, // Durata exactă a videoclipului
                // NU mai punem effect aici
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

    /**
     * Generează clipul pentru logo (watermark).
     *
     * @param float $videoDuration Durata totală a videoclipului.
     * @return array
     */
    private function generateLogoClip($videoDuration)
    {
        return [
            [
                'asset' => [
                    'type'  => 'image',
                    'src'   => 'https://res.cloudinary.com/dpxess5iw/image/upload/v1739911905/logo-transparent_xiqqe0.png', // URL-ul logo-ului
                ],
                'start'  => 0,               // Începe de la începutul videoclipului
                'length' => $videoDuration,  // Se afișează pe toată durata
                'fit'    => 'contain',       // Logo-ul este afișat complet, fără crop
                'position' => 'top',         // Centrat sus
                'opacity' => 0.5,            // Opacitate 50%
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
        // Cleanup imagine
        if ($project->image_cloudinary_id) {
            Log::info('Attempting to delete image from Cloudinary', [
                'image_cloudinary_id' => $project->image_cloudinary_id
            ]);
            Cloudinary::destroy($project->image_cloudinary_id);
            Log::info('Cleaned up image from Cloudinary', [
                'project_id' => $project->id,
                'cloudinary_id' => $project->image_cloudinary_id
            ]);
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