<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'type'        => $this->type,
            'difficulty'  => $this->difficulty,
            'question'    => $this->question,
            'options'     => $this->options->map(fn ($option) => [
                'id'          => $option->id,
                'option_text' => $option->option_text,
            ]),
        ];
    }
}
