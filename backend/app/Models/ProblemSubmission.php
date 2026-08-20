<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProblemSubmission extends Model
{
    protected $fillable = [
        'user_id', 'problem_id', 'language', 'code',
        'status', 'test_cases_passed', 'test_cases_total', 'execution_time_ms',
    ];

    protected $casts = [
        'test_cases_passed' => 'integer',
        'test_cases_total'  => 'integer',
        'execution_time_ms' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function problem(): BelongsTo
    {
        return $this->belongsTo(CodingProblem::class, 'problem_id');
    }
}
