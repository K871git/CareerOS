<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = [
        'learning_track_id',
        'title',
        'slug',
        'description',
        'display_order',
    ];

    public function learningTrack(): BelongsTo
    {
        return $this->belongsTo(LearningTrack::class);
    }

    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class);
    }
}
