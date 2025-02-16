<?php

namespace App\Services\AI;

use OpenAI\Laravel\Facades\OpenAI;
use Exception;
use Illuminate\Support\Facades\Log;

class ScriptGenerationService
{
    public function generate(string $topic, string $style = 'amuzant')
    {
        try {
            Log::info('Starting script generation', ['topic' => $topic, 'style' => $style]);

            $result = OpenAI::chat()->create([
                'model' => 'gpt-4o-2024-08-06', 
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getSystemPrompt()
                    ],
                    [
                        'role' => 'user',
                        'content' => $this->getUserPrompt($topic, $style)
                    ]
                ],
                'response_format' => ['type' => 'json_object'],
                'temperature' => 0.7, // Adăugat temperature
            ]);

            $content = $result->choices[0]->message->content;
            Log::info('Script generated successfully', ['content' => $content]);

            $script = json_decode($content, true);

            if (!isset($script['scenes']) || !is_array($script['scenes'])) {
                Log::error('Invalid script format: Missing or invalid "scenes" array.', ['content' => $content]);
                throw new Exception('Format de script invalid: lipsește array-ul "scenes".');
            }

            // Calculează durata totală:
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
                'topic' => $topic,
                'style' => $style
            ]);
            throw new Exception("Generarea scriptului a eșuat: " . $e->getMessage());
        }
    }

    private function getSystemPrompt(): string
    {
        return <<<EOT
    Ești un creator de conținut expert în realizarea de scripturi virale pentru TikTok în limba română, specializat pe nișa ta. Scopul tău este să generezi scripturi captivante, amuzante (dacă e cazul) și relevante pentru publicul din România, care să încurajeze interacțiunea (like-uri, comentarii, distribuiri).
    
    **FOARTE IMPORTANT: Durata totală a videoclipului trebuie să fie între 30 și 60 de secunde.**
    

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
    private function getUserPrompt(string $topic, string $style): string
    {
        $stylePrompts = [
            'amuzant' => 'folosește umor și un ton relaxat',
            'educational' => 'explică într-un mod simplu și clar',
            'motivational' => 'inspiră și motivează audiența',
            'storytelling' => 'prezintă informația într-un mod profesional și structurat'
        ];

        $styleInfo = $stylePrompts[$style] ?? $stylePrompts['amuzant'];

        return "Creează un script TikTok în limba română despre '{$topic}'. 
                Stil: {$styleInfo}. 
                Durata totală: între 30 și 60 de secunde.
                Asigură-te că textul este captivant și natural în limba română.";
    }
}
