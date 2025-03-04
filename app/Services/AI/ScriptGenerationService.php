<?php

namespace App\Services\AI;

use Anthropic\Laravel\Facades\Anthropic;
use App\Models\VideoProject;
use Exception;
use Illuminate\Support\Facades\Log;

class ScriptGenerationService
{
    public function generate(string $fullCategoryPath, ?int $userId = null)
    {
        try {
            Log::info('Starting script generation', ['category' => $fullCategoryPath, 'user_id' => $userId]);

            $previousPrompts = $this->getPreviousPromptsForCategory($fullCategoryPath, $userId);
            $systemPrompt = $this->getSystemPrompt();

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
                                    Asigură-te că textul este captivant și natural în limba română. Imparte scriptul in 3 scene."
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

            // MODIFICARE AICI: Verificăm dacă avem exact 3 scene
            if (count($script['scenes']) !== 3) {
                Log::error('Invalid script format: Scriptul trebuie sa contina exact 3 scene.', ['content' => $content]);
                throw new Exception('Format de script invalid: Scriptul trebuie sa contina exact 3 scene.');
            }


            $totalDuration = 0;
            foreach ($script['scenes'] as &$scene) {

                $words = str_word_count($scene['narration']);
                $wordsPerSecond = 2.3;
                $scene['duration'] = max(1, round($words / $wordsPerSecond));
                $totalDuration += $scene['duration']; // Corect

                $scene = [
                    'text' => $scene['text'] ?? '',
                    'duration' => $scene['duration'],
                    'narration' => $scene['narration'] ?? '',
                    'image_prompt' => $scene['image_prompt'] ?? ''
                ];
            }
            unset($scene);

            $script['total_duration'] = $totalDuration; // Corect

            return $script;

            $script['total_duration'] = $totalDuration;

            // ELIMINĂM 'background_prompt', 'style_notes' - nu mai sunt necesare în formatul nou
            // unset($script['background_prompt']);
            // unset($script['style_notes']);

            return $script;
        } catch (Exception $e) {
            Log::error('Script generation failed', [
                'error' => $e->getMessage(),
                'category' => $fullCategoryPath
            ]);
            throw new Exception("Generarea scriptului a eșuat: " . $e->getMessage());
        }
    }

    private function getPreviousPromptsForCategory(string $categoryPath, ?int $userId): array
    {
        if (!$userId) {
            return [];
        }

        $previousProjects = VideoProject::where('user_id', $userId)
            ->where('status', 'completed')
            ->whereNotNull('script')
            ->latest()
            ->take(3)
            ->get();

        $prompts = [];

        foreach ($previousProjects as $project) {
            $script = is_string($project->script) ? json_decode($project->script, true) : $project->script;

            // MODIFICARE: Extragem prompt-urile pentru *fiecare* scenă
            if (isset($script['scenes']) && is_array($script['scenes'])) {
                foreach ($script['scenes'] as $scene) {
                    if (isset($scene['image_prompt'])) {
                        $prompts[] = $scene['image_prompt'];
                    }
                }
            }
        }

        return array_slice($prompts, 0, 5);
    }
    private function getSystemPrompt(): string
    {
        return <<<EOT
Ești un creator de conținut expert în realizarea de scripturi virale pentru TikTok în limba română, specializat pe nișa ta. Scopul tău este să generezi scripturi captivante, relevante pentru publicul din România, care să încurajeze interacțiunea (like-uri, comentarii, distribuiri). Incearca sa nu folosesti prescurtari (de ex: km, cm), ci sa folosesti forma lor intreaga(de ex:kilometru respectiv centimetru)

**FOARTE IMPORTANT: Scriptul trebuie să încurajeze activ interacțiunea prin:
                    - Întrebări care să stimuleze comentariile
                    - Call-to-action pentru like-uri și share
                    - Hook puternic în primele 3 secunde
                    - Suspans care să țină utilizatorul până la final
                    - Durata totală: 15-30 secunde (maxim 45 secunde pentru topics complexe)**
                    
**IMPORTANT: Evită să menționezi anii specifici (precum 2024) în script. Folosește formulări atemporale sau referințe generale precum "în prezent", "în zilele noastre", "în era digitală", "recent", etc. Conținutul trebuie să rămână relevant pentru o perioadă mai lungă de timp.** 

**IMPORTANT PENTRU HASHTAGS: Folosește doar caractere latine (a-z, A-Z), cifre și underscore în hashtaguri. NU folosi diacritice sau caractere speciale. De exemplu, folosește #InteligentaArtificiala în loc de #InteligenţăArtificială sau caractere din alte limbi.**

**ASIGURĂ-TE CĂ GENEREZI CONȚINUT UNIC și VARIAT, evitând repetarea ideilor sau abordărilor similare din scripturile anterioare ale utilizatorului.**

**IMPARTE SCRIPTUL IN 3 SCENE DISTINCTE. Fiecare scena trebuie sa contina un prompt de imagine in limba engleza, textul pentru ecran, durata si naratiunea.**

Răspunsul tău trebuie să fie întotdeauna în format JSON cu următoarea structură:
{
    "scenes": [
        {
            "text": "textul care apare pe ecran",
            "duration": durata în secunde,
            "narration": "textul pentru narare",
            "image_prompt": "prompt în engleză pentru generarea imaginii"
        },
        {
            "text": "textul care apare pe ecran pentru scena 2",
            "duration": durata în secunde,
            "narration": "textul pentru narare pentru scena 2",
            "image_prompt": "prompt în engleză pentru generarea imaginii pentru scena 2"
        },
        {
            "text": "textul care apare pe ecran pentru scena 3",
            "duration": durata în secunde,
            "narration": "textul pentru narare pentru scena 3",
            "image_prompt": "prompt în engleză pentru generarea imaginii pentru scena 3"
        }
    ],
    "hashtags": ["#tag1", "#tag2"]
}
EOT;
    }
}
