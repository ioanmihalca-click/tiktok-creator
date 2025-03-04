<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\VideoProject;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Http;


class ReplicateWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Replicate webhook received', ['payload' => $request->all()]);

        $data = $request->all();

        // Verificarea semnăturii (opțional, dar FOARTE RECOMANDAT)
        if (!$this->verifyWebhook($request)) {
            Log::error('Replicate webhook verification failed');
            return response('Unauthorized', 401); // Sau orice alt răspuns de eroare dorești
        }

        if ($data['status'] === 'succeeded' && isset($data['output'][0])) {
            $imageUrl = $data['output'][0];
            $predictionId = $data['id'];

            // Găsim VideoProject-ul folosind prediction ID-ul stocat temporar
            $project = VideoProject::where('temp_prediction_id', $predictionId)->first();

            if (!$project) {
                Log::error('VideoProject not found for prediction ID', ['prediction_id' => $predictionId]);
                return response('VideoProject not found', 404); // Sau orice alt răspuns de eroare
            }

            // Upload pe Cloudinary
            $imageContent = Http::timeout(120)->get($imageUrl)->body();
            $tempFile = tempnam(sys_get_temp_dir(), 'bg_');
            file_put_contents($tempFile, $imageContent);

            try {
                $uploadResult = Cloudinary::upload($tempFile, [
                    'folder' => 'tiktok/backgrounds',
                    'public_id' => 'bg_' . time(),
                    'resource_type' => 'image'
                ]);

                $cloudinaryUrl = $uploadResult->getSecurePath();
                $cloudinaryId = $uploadResult->getPublicId();

                Log::info('Image uploaded to Cloudinary', ['cloudinary_url' => $cloudinaryUrl]);
            } finally {
                if (file_exists($tempFile)) {
                    unlink($tempFile);
                }
            }

            // Actualizăm array-ul 'images' din VideoProject
            $images = $project->images;
            $imageUpdated = false;
            foreach ($images as &$image) {
                if (isset($image['prediction_id']) && $image['prediction_id'] == $predictionId) {
                    $image['url'] = $cloudinaryUrl; // Actualizăm cu URL-ul de la Cloudinary
                    $image['cloudinary_id'] = $cloudinaryId; // Actualizăm cu ID-ul de la Cloudinary
                    unset($image['prediction_id']); // Ștergem prediction_id, nu mai avem nevoie de el
                    $imageUpdated = true;
                    break;
                }
            }
            unset($image); // Foarte important!  Dezlegăm referința!

            if (!$imageUpdated) {
                Log::error('Could not find image to update in VideoProject', ['project_id' => $project->id, 'prediction_id' => $predictionId]);
                return response('Could not find image to update', 404); // Sau alt răspuns de eroare
            }
            // Stergem si temp_prediction_id
            $project->update(['images' => $images, 'temp_prediction_id' => null]);

            Log::info('Updated image URL in VideoProject', ['project_id' => $project->id, 'image_url' => $cloudinaryUrl]);
            return response('OK', 200); // Replicate se așteaptă la un răspuns 200 OK

        } elseif ($data['status'] === 'failed') {
            Log::error('Image generation failed (webhook)', ['error' => $data['error'] ?? 'Unknown error', 'prediction_id' => $data['id']]);

            $predictionId = $data['id'];
            $project = VideoProject::where('temp_prediction_id', $predictionId)->first();
            if ($project) {
                $images = $project->images;
                foreach ($images as &$image) {
                    if (isset($image['prediction_id']) && $image['prediction_id'] == $predictionId) {
                        $image['status'] = 'failed';
                        unset($image['prediction_id']);
                        break;
                    }
                }
                unset($image);
                $project->update(['images' => $images, 'temp_prediction_id' => null]);
            }

            return response('Image generation failed', 500); // Sau alt cod de eroare
        }

        // Dacă nu e nici 'succeeded', nici 'failed' (de ex, 'processing'),
        // Replicate se așteaptă tot la un 200 OK.
        return response('OK', 200);
    }

    /**
     * Verifică semnătura webhook-ului de la Replicate.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    private function verifyWebhook(Request $request)
    {
        $signatureHeader = $request->header('Replicate-Signature');

        if (!$signatureHeader) {
            return false;
        }

        $signatures = explode(',', $signatureHeader);
        $webhookSecret = config('services.replicate.webhook_secret'); // !!! Adaugă cheia secretă în .env și config/services.php

        foreach ($signatures as $signature) {
            list($timestamp, $signatureHash) = explode('=', trim($signature), 2);

            // Verifică dacă timestamp-ul este recent (previne replay attacks)
            if (abs(time() - $timestamp) > 300) { // 5 minute toleranță
                continue;
            }

            $signedPayload = $timestamp . '.' . $request->getContent();
            $expectedSignature = hash_hmac('sha256', $signedPayload, $webhookSecret);

            if (hash_equals($expectedSignature, $signatureHash)) {
                return true; // Semnătura validă
            }
        }

        return false; // Nicio semnătură validă
    }
}
