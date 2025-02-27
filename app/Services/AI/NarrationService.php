<?php

namespace App\Services\AI;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use getID3; 

class NarrationService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.elevenlabs.io/v1';
    protected $defaultVoiceId = 'S98OhkhaxeAKHEbhoLi7';

    public function __construct()
    {
        $this->apiKey = config('services.elevenlabs.key');
    }

    public function generate(string $text, string $voiceId = null): array
    {
        try {
            // Verifică dacă acest utilizator a generat deja narări pentru acest text
            $userId = Auth::id();
            $textHash = md5($text);
            $userNarrationsKey = "user_{$userId}_narrations";
            $userNarrations = Cache::get($userNarrationsKey, []);
            
            // Dacă utilizatorul curent a mai generat narare cu acest text, variăm parametrii vocii
            $forceNewForUser = in_array($textHash, $userNarrations);
            
            // Adăugăm textul la lista utilizatorului pentru viitoare verificări
            if (!in_array($textHash, $userNarrations)) {
                $userNarrations[] = $textHash;
                Cache::put($userNarrationsKey, $userNarrations, now()->addDays(30));
            }

            Log::info('Starting narration generation', [
                'text_hash' => $textHash,
                'forceNewForUser' => $forceNewForUser
            ]);

            $voiceId = $voiceId ?? $this->defaultVoiceId;

            // Pentru utilizatorii care refolosesc același text, putem varia stilul vocii
            $stability = 0.5;
            $similarityBoost = 0.75;
            $style = 1.0;
            
            if ($forceNewForUser) {
                // Dacă e același utilizator, variază parametrii vocii pentru a obține o narare diferită
                $stability = mt_rand(40, 60) / 100; // între 0.4 și 0.6
                $similarityBoost = mt_rand(65, 85) / 100; // între 0.65 și 0.85
                $style = mt_rand(90, 110) / 100; // între 0.9 și 1.1
            }

            // Setăm timeout mai mare pentru ElevenLabs
            $response = Http::timeout(60)->withHeaders([
                'xi-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/text-to-speech/{$voiceId}", [
                'text' => $text,
                'model_id' => 'eleven_multilingual_v2',
                'voice_settings' => [
                    'stability' => $stability,
                    'similarity_boost' => $similarityBoost,
                    'style' => $style,
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

    public function getAvailableVoices(): array
    {
        return cache()->remember('elevenlabs_voices', 3600, function () {
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
    }
}