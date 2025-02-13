<?php

namespace App\Services\AI;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use HalilCosdu\Replicate\Facades\Replicate;
use Illuminate\Support\Facades\Http;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ImageGenerationService
{
    protected string $modelOwner = 'black-forest-labs';
    protected string $modelName = 'flux-schnell';

    private function enhancePrompt(string $prompt): string 
    {
        // Îmbunătățim promptul cu detalii pentru calitate și stil
        $enhancedPrompt = trim($prompt) . ", professional photography, high quality, 4k, sharp focus, detailed, natural lighting, vertical composition for TikTok, vibrant colors, ultra detailed, photorealistic";
        
        // Adăugăm indicații negative pentru a evita artefacte nedorite
        $negativePrompt = "low quality, blurry, watermark, text, bad anatomy, cut off, ugly, deformed";
        
        return "$enhancedPrompt --no $negativePrompt";
    }

    public function generateImage(string $prompt)
    {
        try {
            $enhancedPrompt = $this->enhancePrompt($prompt);
            Log::info('Starting image generation', ['original_prompt' => $prompt, 'enhanced_prompt' => $enhancedPrompt]);
            
            // Generăm imaginea cu Replicate
            $prediction = Replicate::createModelPrediction(
                $this->modelOwner,
                $this->modelName,
                'latest',
                [
                    'input' => [
                        'prompt' => $enhancedPrompt,
                        'go_fast' => false, // Dezactivăm go_fast pentru calitate mai bună
                        'megapixels' => "2", // Creștem rezoluția
                        'num_outputs' => 1,
                        'aspect_ratio' => "9:16",
                        'output_format' => "webp",
                        'output_quality' => 95, // Creștem calitatea output-ului
                        'num_inference_steps' => 20 // Creștem numărul de pași pentru rezultate mai bune
                    ]
                ]
            );

            Log::info('Initial prediction', ['prediction' => $prediction]);

            $predictionId = $prediction['id'];
            
            $maxAttempts = 30;
            $attempts = 0;
            
            do {
                sleep(2);
                $attempts++;
                
                $result = Replicate::getPrediction($predictionId);
                Log::info('Polling attempt ' . $attempts, ['result' => $result]);
                
                if (isset($result['status']) && $result['status'] === 'succeeded' && isset($result['output'][0])) {
                    $imageUrl = $result['output'][0];

                    // Descărcăm imaginea și o încărcăm pe Cloudinary
                    $imageContent = Http::get($imageUrl)->body();
                    $tempFile = tempnam(sys_get_temp_dir(), 'bg_');
                    file_put_contents($tempFile, $imageContent);

                    $uploadResult = Cloudinary::upload($tempFile, [
                        'folder' => 'tiktok/backgrounds',
                        'public_id' => 'bg_' . time(),
                        'resource_type' => 'image'
                    ]);

                    unlink($tempFile); // Ștergem fișierul temporar

                    Log::info('Image uploaded to Cloudinary', [
                        'cloudinary_url' => $uploadResult->getSecurePath()
                    ]);

                    return [
                        'success' => true,
                        'image_url' => $uploadResult->getSecurePath(),
                        'cloudinary_public_id' => $uploadResult->getPublicId()
                    ];
                }
                
                if (isset($result['status']) && $result['status'] === 'failed') {
                    throw new \Exception('Image generation failed: ' . ($result['error'] ?? 'Unknown error'));
                }
                
            } while ($attempts < $maxAttempts);
            
            throw new \Exception('Image generation timed out after ' . $maxAttempts . ' attempts');

        } catch (\Exception $e) {
            Log::error('Image Generation Error', [
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