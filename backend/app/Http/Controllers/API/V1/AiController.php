<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Services\OllamaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AiController extends Controller
{
    // ── One-shot (legacy — still used when cache already has the response) ──
    public function explain(Request $request): JsonResponse
    {
        if (! config('services.ollama.enabled')) {
            return response()->json(['available' => false, 'explanation' => null]);
        }

        $request->validate([
            'question_id'     => ['required', 'integer', 'exists:questions,id'],
            'wrong_option_id' => ['nullable', 'integer', 'exists:question_options,id'],
        ]);

        $question = Question::with(['options', 'topic.subject'])->findOrFail($request->question_id);
        $correct  = $question->options->firstWhere('is_correct', true);

        if (! $correct) {
            return response()->json(['available' => false, 'explanation' => null]);
        }

        $topic   = $question->topic->name         ?? 'General';
        $subject = $question->topic->subject->name ?? 'Engineering';

        $wrongOption = $request->wrong_option_id
            ? $question->options->firstWhere('id', $request->wrong_option_id)
            : null;
        $wrongAnswer = $wrongOption?->option_text;

        $cacheKey    = "ai.explain.{$question->id}" . ($wrongOption ? ".w{$wrongOption->id}" : '');
        $explanation = Cache::remember($cacheKey, now()->addDays(30), function () use ($question, $correct, $topic, $subject, $wrongAnswer) {
            return app(OllamaService::class)->explain(
                $question->question,
                $correct->option_text,
                $topic,
                $subject,
                $wrongAnswer
            );
        });

        if (empty($explanation)) {
            return response()->json(['available' => false, 'explanation' => null]);
        }

        return response()->json(['available' => true, 'explanation' => $explanation]);
    }

    // ── SSE streaming endpoint ──
    public function explainStream(Request $request): StreamedResponse
    {
        $unavailable = fn() => response()->stream(
            function () {
                echo "data: " . json_encode(['unavailable' => true]) . "\n\n";
                ob_flush(); flush();
            },
            200,
            $this->sseHeaders()
        );

        if (! config('services.ollama.enabled')) {
            return $unavailable();
        }

        $request->validate([
            'question_id'     => ['required', 'integer', 'exists:questions,id'],
            'wrong_option_id' => ['nullable', 'integer', 'exists:question_options,id'],
        ]);

        $question = Question::with(['options', 'topic.subject'])->findOrFail($request->question_id);
        $correct  = $question->options->firstWhere('is_correct', true);

        if (! $correct) return $unavailable();

        $topic   = $question->topic->name         ?? 'General';
        $subject = $question->topic->subject->name ?? 'Engineering';

        $wrongOption = $request->wrong_option_id
            ? $question->options->firstWhere('id', $request->wrong_option_id)
            : null;
        $wrongAnswer = $wrongOption?->option_text;

        $cacheKey = "ai.explain.{$question->id}" . ($wrongOption ? ".w{$wrongOption->id}" : '');

        $ollama = app(OllamaService::class);
        $prompt = $ollama->buildPrompt(
            $question->question,
            $correct->option_text,
            $topic,
            $subject,
            $wrongAnswer
        );

        return response()->stream(function () use ($ollama, $prompt, $cacheKey) {
            // Disable PHP output buffering so bytes reach the client immediately
            while (ob_get_level() > 0) ob_end_clean();
            set_time_limit(0);

            // ── Cache hit: return full text instantly, no need to stream ──
            if (Cache::has($cacheKey)) {
                $cached = Cache::get($cacheKey);
                echo "data: " . json_encode(['done' => true, 'text' => $cached]) . "\n\n";
                flush();
                return;
            }

            // ── Cache miss: stream token by token from Ollama ──
            $fullText = '';
            try {
                foreach ($ollama->streamGenerate($prompt) as $chunk) {
                    $token = $chunk['token'];
                    $fullText .= $token;

                    if ($token !== '') {
                        echo "data: " . json_encode(['token' => $token]) . "\n\n";
                        flush();
                    }

                    if ($chunk['done']) {
                        $trimmed = trim($fullText);
                        if ($trimmed) {
                            Cache::put($cacheKey, $trimmed, now()->addDays(30));
                        }
                        echo "data: " . json_encode(['done' => true]) . "\n\n";
                        flush();
                        break;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Ollama stream error', ['error' => $e->getMessage()]);
                echo "data: " . json_encode(['unavailable' => true]) . "\n\n";
                flush();
            }
        }, 200, $this->sseHeaders());
    }

    private function sseHeaders(): array
    {
        return [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache, must-revalidate',
            'X-Accel-Buffering' => 'no',  // disable nginx buffering
            'Connection'        => 'keep-alive',
        ];
    }
}
