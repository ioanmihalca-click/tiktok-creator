<?php

namespace App\Services\AI;

use Anthropic\Laravel\Facades\Anthropic;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ScriptGenerationService
{
    public function generate(string $fullCategoryPath)
    {
        try {
            $userId = Auth::id();
            $cacheKey = "script_{$fullCategoryPath}";
            
            // Verifică dacă acest utilizator a generat deja conținut pentru această categorie
            $userCategoriesKey = "user_{$userId}_categories";
            $userCategories = Cache::get($userCategoriesKey, []);
            
            // Dacă utilizatorul curent a mai generat conținut în această categorie, forțăm conținut nou
            $forceNewForUser = in_array($fullCategoryPath, $userCategories);
            
            // Verifică cache-ul doar dacă nu este forțată generarea de conținut nou pentru utilizator
            if (!$forceNewForUser && Cache::has($cacheKey)) {
                $cachedScript = Cache::get($cacheKey);
                Log::info('Using cached script for category', ['category' => $fullCategoryPath]);
                return $cachedScript;
            }
            
            Log::info('Starting script generation', [
                'category' => $fullCategoryPath,
                'forceNewForUser' => $forceNewForUser
            ]);
            
            // Crește temperatura pentru mai multă variabilitate dacă același utilizator accesează din nou
            $temperature = $forceNewForUser ? 0.9 : 0.7;
            
            $result = Anthropic::messages()->create([
                'model' => 'claude-3-7-sonnet-20250219',
                'max_tokens' => 1024,
                'system' => $this->getSystemPrompt(),
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => "Creează un script TikTok în limba română pentru categoria: '{$fullCategoryPath}'.
                                    Durata totală: între 15-30 secunde (maxim 45 secunde doar dacă subiectul necesită).
                                    Asigură-te că textul este captivant și natural în limba română."
                                    . ($forceNewForUser ? " IMPORTANT: Generează conținut complet diferit de creațiile anterioare pe acest subiect, cu abordare și idei noi." : "")
                    ]
                ],
                'temperature' => $temperature,
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
            
            // Adaugă această categorie la lista utilizatorului
            if (!in_array($fullCategoryPath, $userCategories)) {
                $userCategories[] = $fullCategoryPath;
                Cache::put($userCategoriesKey, $userCategories, now()->addDays(30));
            }
            
            // Salvează în cache doar dacă nu e pentru același utilizator
            if (!$forceNewForUser) {
                Cache::put($cacheKey, $script, now()->addHours(6));
            }

            return $script;

        } catch (Exception $e) {
            Log::error('Script generation failed', [
                'error' => $e->getMessage(),
                'category' => $fullCategoryPath
            ]);
            throw new Exception("Generarea scriptului a eșuat: " . $e->getMessage());
        }
    }

    private function getSystemPrompt(): string
    {
        return <<<EOT
    Ești un creator de conținut expert în realizarea de scripturi virale pentru TikTok în limba română, specializat pe nișa ta. Scopul tău este să generezi scripturi captivante, amuzante (dacă e cazul) și relevante pentru publicul din România, care să încurajeze interacțiunea (like-uri, comentarii, distribuiri). Incearca sa nu folosesti prescurtari (de ex: km, cm), ci sa folosesti forma lor intreaga(de ex:kilometru respectiv centimetru)

    **FOARTE IMPORTANT: Scriptul trebuie să încurajeze activ interacțiunea prin:
                        - Întrebări care să stimuleze comentariile
                        - Call-to-action pentru like-uri și share
                        - Hook puternic în primele 3 secunde
                        - Suspans care să țină utilizatorul până la final
                        - Durata totală: 15-30 secunde (maxim 45 secunde pentru topics complexe)**

    Fiecare script pe care îl generezi trebuie să fie unic și original, chiar dacă subiectul este similar cu script-uri anterioare. Folosește abordări diferite, structuri variate și idei proaspete.

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