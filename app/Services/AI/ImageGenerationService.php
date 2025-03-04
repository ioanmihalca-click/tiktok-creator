<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;
use HalilCosdu\Replicate\Facades\Replicate;

class ImageGenerationService
{
    protected string $modelOwner = 'black-forest-labs';
    protected string $modelName = 'flux-schnell';

    public function generateImage(string $prompt)
    {
        try {
            Log::info('Starting image generation with Replicate', ['prompt' => $prompt]);

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

            Log::info('Replicate prediction created', ['prediction_id' => $prediction['id']]);

            // Returnăm *doar* ID-ul predicției.  NU mai returnăm URL sau Cloudinary ID.
            return [
                'success' => true,
                'prediction_id' => $prediction['id'], // Returnăm DOAR prediction_id
            ];
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
