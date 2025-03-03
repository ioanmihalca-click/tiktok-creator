<?php

namespace App\Services\AI;

use Anthropic\Laravel\Facades\Anthropic;
use App\Models\VideoProject;
use Exception;
use Illuminate\Support\Facades\Log;

class ScriptGenerationService
{
    public function generate(string $fullCategoryPath, int $userId = null)
    {
        try {
            Log::info('Starting script generation', ['category' => $fullCategoryPath, 'user_id' => $userId]);

            // Obținem scripturile anterioare ale utilizatorului pentru aceeași categorie
            $previousPrompts = $this->getPreviousPromptsForCategory($fullCategoryPath, $userId);

            $systemPrompt = $this->getSystemPrompt();

            // Adăugăm instrucțiuni pentru evitarea repetării conținutului
            if (!empty($previousPrompts)) {
                $systemPrompt .= "\n\n**IMPORTANT: Evită să generezi conținut similar cu următoarele scripturi anterioare ale utilizatorului:**\n";
                foreach ($previousPrompts as $index => $prompt) {
                    $systemPrompt .= "\n" . ($index + 1) . ". " . $prompt;
                }
            }

            $result = Anthropic::messages()->create([
                'model' => 'claude-3-5-sonnet-20241022',
                'max_tokens' => 1024,
                'system' => $systemPrompt,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => "Creează un script TikTok în limba română pentru categoria: '{$fullCategoryPath}'.
                                    Durata totală: între 15-30 secunde (maxim 45 secunde doar dacă subiectul necesită).
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

            // Rest of your existing code...
            $totalDuration = 0;
            foreach ($script['scenes'] as &$scene) {
                unset($scene['start_time']);
                $words = str_word_count($scene['narration']);
                $wordsPerSecond = 2.3;
                $scene['duration'] = max(1, round($words / $wordsPerSecond));
                $totalDuration += $scene['duration'];

                $scene = [
                    'text' => $scene['text'] ?? '',
                    'duration' => $scene['duration'],
                    'narration' => $scene['narration'] ?? '',

                ];
            }
            unset($scene);

            $script['total_duration'] = $totalDuration;

            return $script;
        } catch (Exception $e) {
            Log::error('Script generation failed', [
                'error' => $e->getMessage(),
                'category' => $fullCategoryPath
            ]);
            throw new Exception("Generarea scriptului a eșuat: " . $e->getMessage());
        }
    }

    /**
     * Obține prompt-urile anterioare folosite pentru aceeași categorie de către același utilizator
     * 
     * @param string $categoryPath Calea completă a categoriei
     * @param int|null $userId ID-ul utilizatorului
     * @return array Array cu prompt-urile anterioare
     */
    private function getPreviousPromptsForCategory(string $categoryPath, ?int $userId): array
    {
        if (!$userId) {
            return [];
        }

        // Obținem ultimele 3 proiecte pentru această categorie și acest utilizator
        $previousProjects = VideoProject::where('user_id', $userId)
            ->where('status', 'completed')
            ->whereNotNull('script')
            ->latest()
            ->take(3)
            ->get();

        $prompts = [];

        foreach ($previousProjects as $project) {
            if (is_string($project->script)) {
                $script = json_decode($project->script, true);
            } else {
                $script = $project->script;
            }

            // Extragem prompt-ul de fundal sau prompt-urile pentru scene
            if (isset($script['background_prompt'])) {
                $prompts[] = $script['background_prompt'];
            }

            // Opțional: adaugă și textele scenelor pentru mai multă diversitate
            if (isset($script['scenes']) && is_array($script['scenes'])) {
                $sceneTexts = [];
                foreach ($script['scenes'] as $scene) {
                    if (isset($scene['text'])) {
                        $sceneTexts[] = $scene['text'];
                    }
                }
                if (!empty($sceneTexts)) {
                    $prompts[] = "Scene anterioare: " . implode(" | ", array_slice($sceneTexts, 0, 3));
                }
            }
        }

        return array_slice($prompts, 0, 5); // Limităm la 5 prompt-uri pentru a nu încărca sistemul
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
                        
    **IMPORTANT: Evită să menționezi anii specifici (precum 2024) în script. Folosește formulări atemporale sau referințe generale precum "în prezent", "în zilele noastre", "în era digitală", "recent", etc. Conținutul trebuie să rămână relevant pentru o perioadă mai lungă de timp.** 

    **ASIGURĂ-TE CĂ GENEREZI CONȚINUT UNIC și VARIAT, evitând repetarea ideilor sau abordărilor similare din scripturile anterioare ale utilizatorului.**

    Răspunsul tău trebuie să fie întotdeauna în format JSON cu următoarea structură:
    {
        "scenes": [
            {
                "text": "textul care apare pe ecran",
                "duration": durata în secunde,
                "narration": "textul pentru narare",
                
            }
        ],
        "background_prompt": "prompt în engleză pentru generarea imaginii de fundal",
        "style_notes": "note despre stil și aspect",
        "hashtags": ["#tag1", "#tag2"],
      
    }
    EOT;
    }
}
