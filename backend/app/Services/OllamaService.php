<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaService
{
    private string $url;
    private string $model;

    public function __construct()
    {
        $this->url   = rtrim(config('services.ollama.url'), '/');
        $this->model = config('services.ollama.model');
    }

    // ── Shared prompt builder (used by both explain() and stream endpoint) ──
    public function buildPrompt(
        string  $question,
        string  $correctAnswer,
        string  $topic,
        string  $subject,
        ?string $wrongAnswer = null
    ): string {
        $sections   = $wrongAnswer ? 4 : 3;
        $wrongLine  = $wrongAnswer ? "\nWrong Answer Selected: {$wrongAnswer}" : '';
        $wrongBlock = $wrongAnswer
            ? "\n\n4. Why wrong: 2 sentences. First: the exact technical reason `{$wrongAnswer}` is incorrect in {$subject}. Second: what it actually does instead, or why it causes an error."
            : '';

        return <<<PROMPT
You are CareerOS AI. Explain this {$subject} question clearly, in depth, and with working code.

Subject: {$subject}
Topic: {$topic}
Question: {$question}
Correct Answer: {$correctAnswer}{$wrongLine}

Write {$sections} numbered sections. Each section starts on its own line with the number and a period.

1. Why correct: 2-3 sentences. First: the precise technical reason `{$correctAnswer}` is correct in {$subject}. Second: how it works internally or a key property. Third: when or why you would use it over alternatives.

2. Concept: **concept name** — 2-3 sentences. Define the concept. Explain how it works in {$subject}. State a practical use case or why it matters.

3. Real World: One sentence describing what the code example shows. Then a working {$subject} code example in a code block. Then 1-2 sentences explaining the output or key behavior.
```
working {$subject} code (4-6 lines, no pseudocode)
```{$wrongBlock}

Format rules:
- Sections 1, 2{$sections}: use `backticks` around every keyword, function, operator, value in text
- **bold** only for the concept name in section 2
- Triple backtick fence only for the code block in section 3
- {$subject} ONLY — never mention other languages or frameworks
- No filler: no "In summary", "As an AI", "Great question" — state facts directly
- Start immediately with "1."
PROMPT;
    }

    // ── Generation options applied to every request ──
    private function generationOptions(): array
    {
        return [
            'num_predict' => 320,  // more room for 2-3 sentences per section + code block
            'num_ctx'     => 2048,
            'temperature' => 0.2,  // low = factual, less hallucination
        ];
    }

    // ── One-shot (used when response is already cached) ──
    public function explain(
        string  $question,
        string  $correctAnswer,
        string  $topic,
        string  $subject,
        ?string $wrongAnswer = null
    ): string {
        $prompt = $this->buildPrompt($question, $correctAnswer, $topic, $subject, $wrongAnswer);

        try {
            $response = Http::timeout(90)
                ->post("{$this->url}/api/generate", [
                    'model'      => $this->model,
                    'prompt'     => $prompt,
                    'stream'     => false,
                    'keep_alive' => '10m',
                    'options'    => $this->generationOptions(),
                ]);

            if ($response->failed()) {
                Log::warning('Ollama request failed', ['status' => $response->status()]);
                return '';
            }

            return trim($response->json('response', ''));

        } catch (\Throwable $e) {
            Log::warning('Ollama unavailable', ['error' => $e->getMessage()]);
            return '';
        }
    }

    // ── Streaming generator — yields ['token' => string, 'done' => bool] ──
    public function streamGenerate(string $prompt): \Generator
    {
        $response = Http::withOptions(['stream' => true])
            ->timeout(120)
            ->post("{$this->url}/api/generate", [
                'model'      => $this->model,
                'prompt'     => $prompt,
                'stream'     => true,
                'keep_alive' => '10m',
                'options'    => $this->generationOptions(),
            ]);

        $body   = $response->getBody();
        $buffer = '';

        while (! $body->eof()) {
            $buffer .= $body->read(64);

            // Process every complete newline-delimited JSON object
            while (($pos = strpos($buffer, "\n")) !== false) {
                $line   = trim(substr($buffer, 0, $pos));
                $buffer = substr($buffer, $pos + 1);

                if ($line === '') continue;

                $data = json_decode($line, true);
                if (! is_array($data)) continue;

                yield [
                    'token' => $data['response'] ?? '',
                    'done'  => $data['done']     ?? false,
                ];

                if ($data['done'] ?? false) return;
            }
        }
    }
}
