<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LevelCompletion extends Model
{
    protected $fillable = ['user_id', 'subject_id', 'level', 'score', 'passed'];

    protected $casts = [
        'passed' => 'boolean',
        'score'  => 'integer',
        'level'  => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
