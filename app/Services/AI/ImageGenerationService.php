<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ImageGenerationService
{
    protected string $togetherApiKey;
    protected string $togetherApiUrl = 'https://api.together.xyz/v1/images/generations';

    public function __construct()
    {
        $this->togetherApiKey = config('services.together.api_key');
    }

    public function generateImage(string $prompt)
    {
        try {
            // Verifică dacă acest utilizator a generat deja imagini pentru acest prompt
            $userId = Auth::id();
            $promptHash = md5($prompt);
            $userPromptsKey = "user_{$userId}_image_prompts";
            $userPrompts = Cache::get($userPromptsKey, []);
            
            // Dacă utilizatorul curent a mai generat imagini cu acest prompt, variăm puțin prompt-ul
            $forceNewForUser = in_array($promptHash, $userPrompts);
            
            // Adăugăm prompt-ul la lista utilizatorului pentru viitoare verificări
            if (!in_array($promptHash, $userPrompts)) {
                $userPrompts[] = $promptHash;
                Cache::put($userPromptsKey, $userPrompts, now()->addDays(30));
            }
            
            // Modificăm prompt-ul pentru a forța variație dacă e același utilizator
            $modifiedPrompt = $prompt;
            if ($forceNewForUser) {
                // Adăugăm un qualifier aleatoriu pentru a obține o imagine diferită
                $styles = ['cinematic', 'vibrant', 'dramatic', 'moody', 'bright', 'detailed', 'artistic'];
                $randomStyle = $styles[array_rand($styles)];
                $modifiedPrompt = $prompt . ", {$randomStyle} style";
            }

            Log::info('Starting image generation with Together AI Flux Schnell', [
                'prompt' => $prompt,
                'modifiedPrompt' => $modifiedPrompt,
                'forceNewForUser' => $forceNewForUser
            ]);

            // Facem request către Together AI API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->togetherApiKey,
                'Content-Type' => 'application/json',
            ])->post($this->togetherApiUrl, [
                'model' => 'black-forest-labs/FLUX.1-schnell-Free',
                'prompt' => $modifiedPrompt,
                'width' => 1008,  // Max allowed height while maintaining 9:16 ratio
                'height' => 1792,  // Maximum allowed height
                'steps' => 4,
                'n' => 1,
                'response_format' => 'url', // folosim url în loc de b64_json pentru simplitate
                'go_fast' => true,
                'output_format' => 'jpeg',
                'output_quality' => 80
            ]);

            if (!$response->successful()) {
                throw new \Exception('Together API Error: ' . $response->body());
            }

            $result = $response->json();

            if (!isset($result['data'][0]['url'])) {
                throw new \Exception('No image URL in Together AI response');
            }

            $imageUrl = $result['data'][0]['url'];

            // Descărcăm imaginea și o încărcăm pe Cloudinary
            $imageContent = Http::timeout(60)->get($imageUrl)->body();
            $tempFile = tempnam(sys_get_temp_dir(), 'bg_');
            file_put_contents($tempFile, $imageContent);

            try {
                $uploadResult = Cloudinary::upload($tempFile, [
                    'folder' => 'tiktok/backgrounds',
                    'public_id' => 'bg_' . time(),
                    'resource_type' => 'image'
                ]);

                Log::info('Image uploaded to Cloudinary', [
                    'cloudinary_url' => $uploadResult->getSecurePath()
                ]);

                return [
                    'success' => true,
                    'image_url' => $uploadResult->getSecurePath(),
                    'cloudinary_public_id' => $uploadResult->getPublicId()
                ];
            } finally {
                if (file_exists($tempFile)) {
                    unlink($tempFile);
                }
            }
        } catch (\Exception $e) {
            Log::error('Together AI Image Generation Error', [
                'error' => $e->getMessage(),
                'prompt' => $prompt,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}