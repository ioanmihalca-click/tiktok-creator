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

            if (!isset($script['total_duration']) || !is_numeric($script['total_duration'])) {
                Log::warning('Missing or invalid total_duration in script. Using fallback.', ['script' => $script]);
                $videoDuration = 15;
            } else {
                $videoDuration = (float) $script['total_duration'];
            }

            $timeline = [
                'soundtrack' => [
                    'src'    => $videoProject->audio_url,
                   // 'effect' => 'fadeInFadeOut'
                ],
                'background' => '#000000',
                'tracks'     => [
                    // 1. Track-ul cu HTML (text) *ÎNAINTE* de track-ul cu imaginea:
                    [
                        'clips' => $this->generateTextClips($script) //  Aici se generează clipurile HTML.
                    ],
                    // 2. Track-ul cu imaginea:
                    [
                        'clips' => [
                            [
                                'asset'  => [
                                    'type' => 'image',
                                    'src'  => $videoProject->image_url
                                ],
                                'start'  => 0,
                                'length' => $videoDuration, // Folosește durata calculată.
                                'fit'    => 'cover'
                            ]
                        ]
                    ],
                ]
            ];

            $output = [
                'format'      => 'mp4',
                'resolution'  => 'hd',
                'aspectRatio' => '9:16'
            ];

            // Setăm timeout mare pentru Shotstack
            $response = Http::timeout(60)->withHeaders([
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
                'timeline'   => $timeline, // Pentru debugging, e util să loghezi timeline-ul complet.
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
                'timeline'   => $timeline ?? null, // Loghează timeline-ul chiar și în caz de eroare.
                'output'     => $output ?? null
            ]);

            return [
                'success' => false,
                'error'   => $e->getMessage()
            ];
        }
    }

    private function generateTextClips($script)
    {
        $clips = [];

        if (!isset($script['scenes']) || !is_array($script['scenes'])) {
            Log::warning('Invalid script format: Missing or invalid "scenes" array.', ['script' => $script]);
            return [];
        }

        foreach ($script['scenes'] as $scene) {
            if (!isset($scene['text'], $scene['start_time'], $scene['duration'], $scene['position'])) {
                Log::warning('Invalid scene format: Missing required fields.', ['scene' => $scene]);
                continue;
            }

            // 1. Word Wrapping.
            $words = explode(" ", $scene['text']);
            $lines = [];
            $currentLine = "";
            $maxLineWidth = 25; // Reducem și mai mult, pentru a forța mai multe rânduri.

            foreach ($words as $word) {
                if (strlen($currentLine) + strlen($word) + 1 <= $maxLineWidth) {
                    $currentLine .= ($currentLine === "" ? "" : " ") . $word;
                } else {
                    $lines[] = $currentLine;
                    $currentLine = $word;
                }
            }
            $lines[] = $currentLine;

            // 2. Construiește HTML-ul (cu Roboto, uppercase, și dimensiune mai mare).
            $html = '<div style="width: 100%; text-align: center; position: absolute; bottom: 20px;">';

            foreach ($lines as $line) {
                $html .= '<p style="margin: 5px 0; padding: 10px; font-size: 40px; font-family: Roboto, sans-serif; color: white; background-color: rgba(0, 0, 0, 0.7); border-radius: 15px; display: inline-block; text-transform: uppercase;">' .
                    htmlspecialchars($line) .
                    '</p>';
            }
            $html .= '</div>';


            // 3. Creează asset-ul HTML.
            $htmlAsset = [
                'type'      => 'html',
                'html'      => $html,
                'width'     => 900,  //  Mărește lățimea.
                'height'    => 500,   // Mărește înălțimea. Ajustează sau calculează dinamic.
                'background' => 'transparent',
                //Elimina mentiunea position
            ];


            // 4. Creează clipul.
            $clips[] = [
                'asset'     => $htmlAsset,
                'start'     => (float) $scene['start_time'],
                'length'    => (float) $scene['duration'],
                'transition' => ['in' => 'fade', 'out' => 'fade'],
            ];
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
