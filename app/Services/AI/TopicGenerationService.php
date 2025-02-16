<?php

namespace App\Services\AI;

use Anthropic\Laravel\Facades\Anthropic;
use Illuminate\Support\Facades\Log;

class TopicGenerationService
{
    public function generateTopic(string $categoryName): string
    {
        try {
            $result = Anthropic::messages()->create([
                'model' => 'claude-3-5-sonnet-20241022',
                'max_tokens' => 1024,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => "Generează un subiect interesant pentru un video TikTok din categoria: {$categoryName}. 
                                    Răspunde doar cu subiectul, fără alte explicații. 
                                    Subiectul trebuie să fie specific, interesant și sub forma unei propoziții scurte în limba română.
                                    Exemplu format răspuns: 'Cum să-ți găsești pacea interioară în 3 pași simpli'"
                    ]
                ],
            ]);

            return $result->content[0]->text;
        } catch (\Exception $e) {
            Log::error('Topic generation failed', [
                'error' => $e->getMessage(),
                'category' => $categoryName
            ]);
            throw new \Exception("Generarea subiectului a eșuat: " . $e->getMessage());
        }
    }
}