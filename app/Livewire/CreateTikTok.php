<?php

namespace App\Livewire;

use Exception;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\AI\NarrationService;
use App\Services\AI\ImageGenerationService;
use App\Services\AI\VideoGenerationService;
use App\Services\AI\ScriptGenerationService;

class CreateTikTok extends Component
{
    public $title;
    public $topic;
    public $style = 'amuzant';
    public $status = 'draft';

    public $script = null;
    public $imageUrl = null;
    public $audioUrl = null;
    public $videoUrl = null;

    public $render_id = null;

    public $isProcessing = false;

    public function mount()
    {
        // Inițializăm cu ultimul proiect generat (dacă există)
        $lastProject = Auth::user()->videoProjects()
            ->whereNotNull('render_id')
            ->latest()
            ->first();

        if ($lastProject) {
            $this->render_id = $lastProject->render_id;
            $this->videoUrl = $lastProject->video_url;
            $this->isProcessing = $lastProject->status === 'rendering';
        }
    }

    public function generate()
    {
        // La începutul metodei
        ini_set('max_execution_time', '120');
        set_time_limit(120);
    
        $this->validate([
            'topic' => 'required|min:3',
            'style' => 'required'
        ]);

    try {
        // 1. Generăm scriptul
        $scriptService = new ScriptGenerationService();
        $this->script = $scriptService->generate($this->topic, $this->style);
        
        // 2. Generăm imaginea
        if (isset($this->script['background_prompt'])) {
            $imageService = new ImageGenerationService();
            $imageResult = $imageService->generateImage($this->script['background_prompt']);
            
            if ($imageResult['success']) {
                $this->imageUrl = $imageResult['image_url']; // Acesta e URL-ul Cloudinary
                $imageCloudinaryId = $imageResult['cloudinary_public_id'];
            } else {
                throw new Exception("Image generation failed: " . $imageResult['error']);
            }
        }

        // 3. Generăm nararea
        $narrationService = new NarrationService();
        $fullNarration = '';
        foreach ($this->script['scenes'] as $scene) {
            $fullNarration .= $scene['narration'] . " ";
        }
        
        $narrationResult = $narrationService->generate($fullNarration);
        if ($narrationResult['status'] === 'success') {
            $this->audioUrl = $narrationResult['audio_url'];  // Acesta e URL-ul Cloudinary
            $audioCloudinaryId = $narrationResult['cloudinary_public_id'];
        } else {
            throw new Exception("Narration generation failed");
        }

        // 4. Salvăm proiectul cu URL-urile Cloudinary
        $project = Auth::user()->videoProjects()->create([
            'title' => $this->title ?? $this->topic,
            'script' => $this->script,
            'status' => 'processing',
            'image_url' => $this->imageUrl,  // URL Cloudinary
            'image_cloudinary_id' => $imageCloudinaryId ?? null,
            'audio_url' => $this->audioUrl,  // URL Cloudinary
            'audio_cloudinary_id' => $audioCloudinaryId ?? null
        ]);

        // 5. Generăm videoclipul final
        $videoService = new VideoGenerationService();
        $videoResult = $videoService->generate($project);
        
        if ($videoResult['success']) {
            $project->update([
                'status' => 'rendering',
                'render_id' => $videoResult['render_id']
            ]);
            
            $this->render_id = $videoResult['render_id'];
            $this->isProcessing = true;
            
            session()->flash('message', 'TikTok project created and rendering started!');
        } else {
            throw new Exception("Video generation failed: " . $videoResult['error']);
        }
        
    } catch (\Exception $e) {
        Log::error('TikTok generation failed', [
            'error' => $e->getMessage(),
            'topic' => $this->topic
        ]);
        session()->flash('error', 'Error: ' . $e->getMessage());
    }
}

    public function render()
    {
        return view('livewire.create-tik-tok');
    }

    public function checkStatus()
    {
        try {
            $project = Auth::user()->videoProjects()
                ->where('render_id', $this->render_id)
                ->first();

            if (!$project) {
                return;
            }

            $videoService = new VideoGenerationService();
            $status = $videoService->checkStatus($project->render_id);

            if ($status['success'] && $status['status'] === 'done') {
                $project->update([
                    'status' => 'completed',
                    'video_url' => $status['url']
                ]);

                // Cleanup resources după ce avem confirmarea că video-ul e gata
                $videoService->cleanupResources($project);

                $this->videoUrl = $status['url'];
                $this->isProcessing = false;
                session()->flash('message', 'Video is ready!');
            } elseif (!$status['success'] || $status['status'] === 'failed') {
                $project->update(['status' => 'failed']);
                $this->isProcessing = false;
                session()->flash('error', 'Video generation failed: ' . ($status['error'] ?? 'Unknown error'));
            } else {
                $this->isProcessing = true;
                session()->flash('message', 'Video is still processing... Status: ' . ($status['status'] ?? 'unknown'));
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error checking status: ' . $e->getMessage());
        }
    }
}
