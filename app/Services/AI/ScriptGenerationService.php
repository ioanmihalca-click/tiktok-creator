<?php

namespace App\Services\AI;

use Anthropic\Laravel\Facades\Anthropic;
use Exception;
use Illuminate\Support\Facades\Log;

class ScriptGenerationService
{
    public function generate(string $fullCategoryPath) // Modificat: primește calea completă a categoriei
    {
        try {
            Log::info('Starting script generation', ['category' => $fullCategoryPath]); // Modificat: loghează doar categoria

            $result = Anthropic::messages()->create([
                'model' => 'claude-3-5-sonnet-20241022',
                'max_tokens' => 1024,
                'system' => $this->getSystemPrompt(),
                'messages' => [
                    [
                        'role' => 'user',
                        // Modificat: prompt-ul folosește doar calea completă a categoriei
                        'content' => "Creează un script TikTok în limba română pentru categoria: '{$fullCategoryPath}'.
                                    Durata totală: între 30 și 60 de secunde.
                                    Asigură-te că textul este captivant și natural în limba română."
                    ]
                ],
                'temperature' => 0.7,
            ]);

            $content = $result->content[0]->text;
            Log::info('Script generated successfully', ['content' => $content]);

            $script = json_decode($content, true);

            if (!isset($script['scenes']) || !is_array($script['scenes'])) {
                Log::error('Invalid script format: Missing or invalid "scenes" array.', ['content' => $content]);
                throw new Exception('Format de script invalid: lipsește array-ul "scenes".');
            }

            // Calculează durata totală (acest cod rămâne neschimbat):
            $totalDuration = 0;
            if (isset($script['scenes']) && is_array($script['scenes'])) {
                foreach ($script['scenes'] as $scene) {
                    if (isset($scene['duration']) && is_numeric($scene['duration'])) {
                        $totalDuration += $scene['duration'];
                    }
                }
            }
            $script['total_duration'] = $totalDuration;

            return $script;

        } catch (Exception $e) {
            Log::error('Script generation failed', [
                'error' => $e->getMessage(),
                'category' => $fullCategoryPath // Modificat: loghează doar categoria
            ]);
            throw new Exception("Generarea scriptului a eșuat: " . $e->getMessage());
        }
    }
  //Restul codului este neschimbat
   private function getSystemPrompt(): string
    {
        return <<<EOT
    Ești un creator de conținut expert în realizarea de scripturi virale pentru TikTok în limba română, specializat pe nișa ta. Scopul tău este să generezi scripturi captivante, amuzante (dacă e cazul) și relevante pentru publicul din România, care să încurajeze interacțiunea (like-uri, comentarii, distribuiri).
    
    **FOARTE IMPORTANT: Scriptul trebuie să încurajeze activ interacțiunea prin:
                        - Întrebări care să stimuleze comentariile
                        - Call-to-action pentru like-uri și share
                        - Hook puternic în primele 3 secunde
                        - Suspans care să țină utilizatorul până la final
                        - Durata totală a videoclipului trebuie să fie între 30 și 60 de secunde.**
    

    Răspunsul tău trebuie să fie întotdeauna în format JSON cu următoarea structură:
    {
        "scenes": [
            {
                "text": "textul care apare pe ecran",
                "start_time": număr în secunde,
                "duration": durata în secunde,
                "position": "center/top/bottom",
                "narration": "textul pentru narare",
                "animation": "fade-in/slide-up/bounce"
            }
        ],
        "background_prompt": "prompt în engleză pentru generarea imaginii de fundal",
        "style_notes": "note despre stil și aspect",
        "hashtags": ["#tag1", "#tag2"],
        "music_suggestion": "tip de muzică recomandată"
    }
    EOT;
    }
}