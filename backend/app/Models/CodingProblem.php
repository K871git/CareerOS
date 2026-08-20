<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CodingProblem extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'constraints',
        'difficulty', 'language', 'starter_code', 'solution_code',
        'topic_id', 'is_active', 'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order'     => 'integer',
    ];

    public function testCases(): HasMany
    {
        return $this->hasMany(ProblemTestCase::class, 'problem_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(ProblemSubmission::class, 'problem_id');
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
