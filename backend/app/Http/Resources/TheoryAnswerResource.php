<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TheoryAnswerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isReviewed = $this->status === 'reviewed';

        return [
            'id'          => $this->id,
            'question_id' => $this->question_id,
            'question'    => $this->question->question,
            'answer'      => $this->answer,
            'status'      => $this->status,
            'score'       => $isReviewed ? $this->score    : null,
            'feedback'    => $isReviewed ? $this->feedback : null,
            'explanation' => $isReviewed ? $this->question->explanation : null,
            'submitted_at' => $this->created_at,
        ];
    }
}
