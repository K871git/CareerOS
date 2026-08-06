<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Skill extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
    ];

    public function userSkills(): HasMany
    {
        return $this->hasMany(UserSkill::class);
    }
}
