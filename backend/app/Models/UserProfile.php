<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'current_role',
        'experience_level',
        'target_role',
        'career_goal',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
