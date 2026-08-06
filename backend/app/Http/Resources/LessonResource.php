<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'title'             => $this->title,
            'content'           => $this->content,
            'estimated_minutes' => $this->estimated_minutes,
            'display_order'     => $this->display_order,
        ];
    }
}
