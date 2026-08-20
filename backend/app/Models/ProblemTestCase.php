<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProblemTestCase extends Model
{
    protected $fillable = [
        'problem_id', 'input', 'expected_output',
        'is_hidden', 'order', 'label',
    ];

    protected $casts = [
        'is_hidden' => 'boolean',
        'order'     => 'integer',
    ];

    public function problem(): BelongsTo
    {
        return $this->belongsTo(CodingProblem::class, 'problem_id');
    }
}
