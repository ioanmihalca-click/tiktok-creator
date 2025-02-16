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


            // UN SINGUR TRACK (pentru imagine, text suprapus și audio)
            $timeline = [
               // 'soundtrack' => [
                //    'src'    => $videoProject->audio_url,
                 //   'effect' => 'fadeInFadeOut' // Opțional
          //      ],
                'background' => '#000000', // Opțional
                'tracks'     => [
                    [  // Un singur track
                        'clips' => array_merge(
                            $this->generateImageClip($videoProject, $videoDuration), // Imaginea PRIMA
                            $this->generateTextClips($script),       // Textul suprapus
                            $this->generateAudioClip($videoProject, $videoDuration)  //Audio-ul
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
                'length' => $videoDuration, // Folosește durata EXACTĂ!
                'fit'    => 'cover',
                'effect' => 'zoomIn' // Exemplu de efect PERMIS pe clip.  Elimină dacă nu vrei.
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
                'length' => $videoDuration, // Folosește durata EXACTĂ!
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
                'start'     => $currentTime,       // Timpul de start este timpul CURENT
                'length'    => $scene['duration'], // Durata scenei
                'transition' => ['in' => 'fade', 'out' => 'fade'], // Opțional: tranziții, NU effect
            ];

            $currentTime += $scene['duration'];  // Incrementăm timpul curent cu durata scenei
        }

        return $clips;
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
                Cloudinary::destroy($project->image_cloudinary_id);
                Log::info('Cleaned up image from Cloudinary', [
                    'project_id' => $project->id,
                    'cloudinary_id' => $project->image_cloudinary_id
                ]);
            }

            // Cleanup audio
            if ($project->audio_cloudinary_id) {
                Cloudinary::destroy($project->audio_cloudinary_id);
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