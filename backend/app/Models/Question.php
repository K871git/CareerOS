<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = [
        'topic_id',
        'type',
        'difficulty',
        'question',
        'explanation',
        'theory_area',
        'theory_level',
    ];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class);
    }

    public function assessmentAnswers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class);
    }
}
