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
            $userId = Auth::id();
            $cacheKey = "image_" . md5($prompt);
            
            // Verifică dacă acest utilizator a generat deja imagini pentru acest prompt
            $userPromptsKey = "user_{$userId}_image_prompts";
            $userPrompts = Cache::get($userPromptsKey, []);
            
            // Dacă utilizatorul curent a mai generat imagini cu acest prompt, forțăm generare nouă
            $forceNewForUser = in_array(md5($prompt), $userPrompts);
            
            // Verifică cache-ul doar dacă nu este forțată generarea de imagini noi pentru utilizator
            if (!$forceNewForUser && Cache::has($cacheKey)) {
                $cachedImage = Cache::get($cacheKey);
                Log::info('Using cached image for prompt', ['prompt_hash' => md5($prompt)]);
                return $cachedImage;
            }
            
            Log::info('Starting image generation with Together AI Flux Schnell', [
                'prompt' => $prompt,
                'forceNewForUser' => $forceNewForUser
            ]);

            // Modificăm prompt-ul pentru a forța variație dacă e același utilizator
            $modifiedPrompt = $prompt;
            if ($forceNewForUser) {
                // Adăugăm un qualifier aleatoriu pentru a obține o imagine diferită
                $styles = ['cinematic', 'vibrant', 'dramatic', 'moody', 'bright', 'detailed'];
                $randomStyle = $styles[array_rand($styles)];
                $modifiedPrompt = $prompt . ", {$randomStyle} style";
            }

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
                'response_format' => 'url',
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

                $response = [
                    'success' => true,
                    'image_url' => $uploadResult->getSecurePath(),
                    'cloudinary_public_id' => $uploadResult->getPublicId()
                ];
                
                // Adaugă acest prompt la lista utilizatorului (hash pentru a economisi spațiu)
                if (!in_array(md5($prompt), $userPrompts)) {
                    $userPrompts[] = md5($prompt);
                    Cache::put($userPromptsKey, $userPrompts, now()->addDays(30));
                }
                
                // Salvează în cache doar dacă nu e pentru același utilizator
                if (!$forceNewForUser) {
                    Cache::put($cacheKey, $response, now()->addHours(6));
                }

                return $response;
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