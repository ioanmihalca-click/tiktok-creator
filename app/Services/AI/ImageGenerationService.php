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

    public function generateImage(string $prompt)
    {
        try {
            Log::info('Starting image generation', ['prompt' => $prompt]);
            
            // Generăm imaginea cu Replicate
            $prediction = Replicate::createModelPrediction(
                $this->modelOwner,
                $this->modelName,
                'latest',
                [
                    'input' => [
                        'prompt' => $prompt,
                        'go_fast' => true,
                        'megapixels' => "1",
                        'num_outputs' => 1,
                        'aspect_ratio' => "9:16",
                        'output_format' => "webp",
                        'output_quality' => 80,
                        'num_inference_steps' => 4
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