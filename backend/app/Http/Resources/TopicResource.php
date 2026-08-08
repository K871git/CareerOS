<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'slug'          => $this->slug,
            'description'   => $this->description,
            'display_order' => $this->display_order,
            'is_locked'     => (bool) ($this->resource->is_locked ?? false),
            'is_completed'  => (bool) ($this->resource->is_completed ?? false),
            'best_score'    => (int) ($this->resource->best_score ?? 0),
        ];
    }
}
