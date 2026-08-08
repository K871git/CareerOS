<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'title'              => $this->title,
            'slug'               => $this->slug,
            'description'        => $this->description,
            'display_order'      => $this->display_order,
            'mcq_question_count' => $this->mcq_question_count ?? 0,
        ];
    }
}
