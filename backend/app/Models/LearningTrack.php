<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningTrack extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'display_order',
    ];

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }
}
