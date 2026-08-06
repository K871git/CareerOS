<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'lesson_id'    => $this->lesson_id,
            'lesson_title' => $this->lesson->title,
            'status'       => $this->status,
            'completed_at' => $this->completed_at,
        ];
    }
}
