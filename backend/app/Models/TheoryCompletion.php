<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TheoryCompletion extends Model
{
    protected $fillable = [
        'user_id',
        'theory_area',
        'level',
        'score',
        'passed',
    ];

    protected $casts = [
        'passed' => 'boolean',
        'score'  => 'integer',
        'level'  => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
