<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssessmentAttemptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $total      = $this->total_questions;
        $score      = $this->score;
        $percentage = $total > 0 ? round(($score / $total) * 100, 1) : 0;

        return [
            'id'              => $this->id,
            'topic_id'        => $this->topic_id,
            'subject_id'      => $this->topic?->subject_id,
            'score'           => $score,
            'total_questions' => $total,
            'percentage'      => $percentage,
            'submitted_at'    => $this->submitted_at,
            'answers'         => $this->answers->map(fn ($answer) => [
                'question_id'        => $answer->question_id,
                'question'           => $answer->question->question,
                'difficulty'         => $answer->question->difficulty,
                'selected_option_id' => $answer->selected_option_id,
                'selected_option'    => $answer->selectedOption?->option_text,
                'is_correct'         => $answer->is_correct,
                'correct_option'     => $answer->question->options
                    ->firstWhere('is_correct', true)?->option_text,
                'explanation'        => $answer->question->explanation,
                'options'            => $answer->question->options
                    ->sortBy('option_text')   // alphabetical → correct answer not always first
                    ->values()
                    ->map(fn ($opt) => [
                        'id'          => $opt->id,
                        'option_text' => $opt->option_text,
                        'is_correct'  => (bool) $opt->is_correct,
                    ]),
            ]),
        ];
    }
}
