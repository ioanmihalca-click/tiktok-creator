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

            // IMPORTANT:  Eliminăm start_time și modificăm duration
            $totalDuration = 0;
            foreach ($script['scenes'] as &$scene) { // Iterăm prin referință (&)
                unset($scene['start_time']);      // Eliminăm start_time
                // Estimăm durata bazată pe lungimea narației (ajustabil)
                $words = str_word_count($scene['narration']);
                $wordsPerSecond = 2.3; // Estimare: 2.3 cuvinte pe secundă (poți ajusta)
                $scene['duration'] = max(1, round($words / $wordsPerSecond)); // Minim 1 secundă
                $totalDuration += $scene['duration'];

                //Pastram doar text, duration, narration, si animation
                $scene = [
                    'text' => $scene['text'] ?? '',
                    'duration' => $scene['duration'],
                    'narration' => $scene['narration'] ?? '',
                    'animation' => $scene['animation'] ?? 'fade-in', // Valoare implicită
                ];
            }
            unset($scene); // Eliminăm referința (bună practică)

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
                "duration": durata în secunde,
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