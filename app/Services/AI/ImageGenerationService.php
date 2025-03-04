<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use HalilCosdu\Replicate\Facades\Replicate;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


class ImageGenerationService
{
    protected string $modelOwner = 'black-forest-labs';
    protected string $modelName = 'flux-schnell';

    public function generateImage(string $prompt)
    {
        try {
            Log::info('Starting image generation with Replicate', ['prompt' => $prompt]);

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

            $predictionId = $prediction['id'];
            Log::info('Replicate prediction created', ['prediction_id' => $predictionId]);

            $maxAttempts = 30;
            $attempts = 0;

            do {
                sleep(2); // Așteaptă 2 secunde între verificări
                $attempts++;

                $result = Replicate::getPrediction($predictionId);
                Log::info("Polling attempt {$attempts}", ['result' => $result]);

                if (isset($result['status']) && $result['status'] === 'succeeded' && isset($result['output'][0])) {
                    $imageUrl = $result['output'][0];

                    // Descarcă și încarcă pe Cloudinary *aici*
                    $imageContent = Http::timeout(120)->get($imageUrl)->body();
                    $tempFile = tempnam(sys_get_temp_dir(), 'bg_');
                    file_put_contents($tempFile, $imageContent);

                    try {
                        $uploadResult = Cloudinary::upload($tempFile, [
                            'folder' => 'tiktok/backgrounds',
                            'public_id' => 'bg_' . time(),
                            'resource_type' => 'image'
                        ]);

                        Log::info('Image uploaded to Cloudinary', ['cloudinary_url' => $uploadResult->getSecurePath()]);

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
                }

                if (isset($result['status']) && $result['status'] === 'failed') {
                    throw new \Exception('Image generation failed: ' . ($result['error'] ?? 'Unknown error'));
                }
            } while ($attempts < $maxAttempts);

            throw new \Exception('Image generation timed out after ' . $maxAttempts . ' attempts');
        } catch (\Exception $e) {
            Log::error('Replicate Image Generation Error', [
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
