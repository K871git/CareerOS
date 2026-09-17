<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\UserPoint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HintController extends Controller
{
    const HINT_COST = 50;

    public function unlock(Request $request): JsonResponse
    {
        $request->validate([
            'question_id' => ['required', 'integer', 'exists:questions,id'],
        ]);

        $user       = $request->user();
        $questionId = (int) $request->question_id;

        $deducted = UserPoint::debit(
            $user->id,
            self::HINT_COST,
            'hint_spent',
            'AI hint for question #' . $questionId,
            ['question_id' => $questionId]
        );

        if (!$deducted) {
            $balance = UserPoint::balanceFor($user->id);
            return response()->json([
                'success' => false,
                'message' => "Not enough points. You have {$balance} pts — hints cost " . self::HINT_COST . ' pts.',
                'data'    => ['balance' => $balance, 'cost' => self::HINT_COST],
            ], 422);
        }

        $question   = Question::with('options')->findOrFail($questionId);
        $hint       = $this->generateHint($question);
        $newBalance = UserPoint::balanceFor($user->id);

        return response()->json([
            'success' => true,
            'data'    => [
                'hint'         => $hint,
                'points_spent' => self::HINT_COST,
                'new_balance'  => $newBalance,
            ],
        ]);
    }

    private function generateHint(Question $question): string
    {
        $letters       = range('A', 'Z');
        $correctOption = $question->options->firstWhere('is_correct', true);
        $correctText   = $correctOption?->option_text ?? '';

        $optionsList = $question->options->values()->map(
            fn ($o, $i) => $letters[$i] . ') ' . $o->option_text
        )->join("\n");

        // Tight, directive prompt — shorter input = faster generation
        $prompt = <<<PROMPT
You are a concise coding tutor giving a one-sentence MCQ hint.

Question: {$question->question}
Options:
{$optionsList}
Correct answer (DO NOT state this directly): {$correctText}

Write exactly ONE sentence that points the student toward the correct answer by mentioning the KEY concept or rule involved. Do not say which letter/option is correct. Do not start with "Hint:".
PROMPT;

        try {
            $response = Http::timeout(20)->post(
                rtrim(config('services.ollama.url', 'http://localhost:11434'), '/') . '/api/generate',
                [
                    'model'  => config('services.ollama.model', 'llama3.2:3b'),
                    'prompt' => $prompt,
                    'stream' => false,
                    'options' => [
                        'temperature'   => 0,      // greedy = fastest + most deterministic
                        'num_predict'   => 60,     // one sentence fits in 60 tokens
                        'num_ctx'       => 512,    // small context = faster inference
                        'top_k'         => 1,      // greedy decoding
                        'repeat_penalty'=> 1.0,
                        'stop'          => ["\n", ".", "!"],  // stop at first sentence end
                    ],
                ]
            );

            if ($response->successful()) {
                $raw  = trim($response->json('response') ?? '');
                $text = $this->cleanHint($raw, $correctText);
                if ($text !== '') return $text;
            }
        } catch (\Throwable $e) {
            \Log::warning('Ollama hint failed: ' . $e->getMessage());
        }

        // Fallback: meaningful topic-based hint rather than a generic string
        return $this->fallbackHint($question->question, $correctText);
    }

    private function cleanHint(string $raw, string $correctText): string
    {
        // Remove any "Hint:" prefix the model might add
        $cleaned = preg_replace('/^(hint\s*:?\s*)/i', '', $raw);

        // If the model blurted the answer verbatim, reject and fall through to fallback
        if ($correctText !== '' && stripos($cleaned, $correctText) !== false) {
            return '';
        }

        // Ensure it ends with a period
        $cleaned = rtrim($cleaned, " \t\n\r\0\x0B.,!;") . '.';

        return strlen($cleaned) >= 15 ? $cleaned : '';
    }

    private function fallbackHint(string $question, string $correctText): string
    {
        // Smarter fallback: extract a keyword from the correct answer
        $words = array_filter(explode(' ', $correctText), fn($w) => strlen($w) > 3);
        $keyword = !empty($words) ? array_values($words)[0] : 'the concept being tested';

        return "Focus on how \"{$keyword}\" works in this context and why the other options don't apply here.";
    }
}
