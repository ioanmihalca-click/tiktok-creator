<?php

namespace App\Services\AI;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use getID3;

class NarrationService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.elevenlabs.io/v1';
    protected $defaultVoiceId = 'S98OhkhaxeAKHEbhoLi7';

    private $freeVoiceIds = [
        'S98OhkhaxeAKHEbhoLi7' // Vocea implicită curentă
    ];

    private $premiumVoiceIds = [
        'premium_voice_id_1',
        'premium_voice_id_2',
        'premium_voice_id_3',
        'premium_voice_id_4',
    ];

    public function __construct()
    {
        $this->apiKey = config('services.elevenlabs.key');
    }

    public function generate(string $text, ?string $voiceId = null): array
    {
        // Folosește vocea implicită dacă nu este specificat un ID
        $voiceId = $voiceId ?? $this->defaultVoiceId;

        try {
            Log::info('Starting narration generation', ['text' => $text]);

            $voiceId = $voiceId ?? $this->defaultVoiceId;

            // Setăm timeout mai mare pentru ElevenLabs
            $response = Http::timeout(60)->withHeaders([
                'xi-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/text-to-speech/{$voiceId}", [
                'text' => $text,
                'model_id' => 'eleven_multilingual_v2',
                'voice_settings' => [
                    'stability' => 0.5,
                    'similarity_boost' => 0.75,
                    'style' => 1.0,
                    'use_speaker_boost' => true
                ]
            ]);

            if (!$response->successful()) {
                throw new Exception('ElevenLabs API error: ' . $response->body());
            }

            // Salvăm temporar audio-ul
            $tempFile = tempnam(sys_get_temp_dir(), 'narration_');
            file_put_contents($tempFile, $response->body());

            // OBȚINEM DURATA REALĂ:
            $getID3 = new getID3;
            $fileInfo = $getID3->analyze($tempFile);
            $audioDuration = $fileInfo['playtime_seconds']; // Durata în secunde


            // Setăm timeout mai mare pentru Cloudinary
            Config::set('cloudinary.upload_timeout', 60);

            // Încărcăm pe Cloudinary
            $uploadResult = Cloudinary::uploadFile($tempFile, [
                'folder' => 'tiktok/narrations',
                'public_id' => 'narration_' . time(),
                'resource_type' => 'video' // pentru fișiere audio
            ]);

            unlink($tempFile); // Ștergem fișierul temporar

            Log::info('Narration uploaded to Cloudinary', [
                'cloudinary_url' => $uploadResult->getSecurePath()
            ]);

            return [
                'status' => 'success',
                'audio_url' => $uploadResult->getSecurePath(),
                'cloudinary_public_id' => $uploadResult->getPublicId(),
                'audio_duration' => $audioDuration // Returnăm și durata!
            ];
        } catch (Exception $e) {
            Log::error('Narration generation failed', [
                'error' => $e->getMessage(),
                'text' => $text
            ]);
            throw new Exception("Generarea narării a eșuat: " . $e->getMessage());
        }
    }

    public function getAvailableVoices(bool $includePremium = false): array
    {
        $voices = cache()->remember('elevenlabs_voices', 3600, function () {
            try {
                $response = Http::withHeaders([
                    'xi-api-key' => $this->apiKey
                ])->get("{$this->baseUrl}/voices");

                if (!$response->successful()) {
                    throw new Exception('Could not fetch voices');
                }

                return $response->json()['voices'];
            } catch (Exception $e) {
                Log::error('Failed to fetch voices', ['error' => $e->getMessage()]);
                return [];
            }
        });

        $result = [
            'free' => [],
            'premium' => []
        ];

        foreach ($voices as $voice) {
            if (in_array($voice['voice_id'], $this->freeVoiceIds)) {
                $result['free'][] = $voice;
            } elseif (in_array($voice['voice_id'], $this->premiumVoiceIds)) {
                $result['premium'][] = $voice;
            }
        }

        return $includePremium ? $result : ['free' => $result['free']];
    }
}
