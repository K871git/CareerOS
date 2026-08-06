<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CareerAssessmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'skill_id'   => $this->skill_id,
            'skill_name' => $this->skill->name,
            'level'      => $this->level,
            'score'      => $this->score,
        ];
    }
}
