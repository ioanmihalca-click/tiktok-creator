<?php

namespace App\Services\AI;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

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
                'cloudinary_public_id' => $uploadResult->getPublicId()
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