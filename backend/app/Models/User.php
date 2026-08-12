<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(UserSkill::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }

    public function assessmentAttempts(): HasMany
    {
        return $this->hasMany(AssessmentAttempt::class);
    }
}


/**
 * 
 * Near a tree by a river
There's a hole in the ground
Where an old man of Aran
Goes around and around
And his mind is a beacon
In the veil of the night
For a strange kind of fashion
There's a wrong and a right

Near a tree by a river
There's a hole in the ground
Where an old man of Aran
Goes around and around
And his mind is a beacon
In the veil of the night
For a strange kind of fashion
There's a wrong and a right
And he'll never fight over you

Near a tree by a river
There's a hole in the ground
Where an old man of Aran
Goes around and around
And his mind is a beacon
In the veil of the night
For a strange kind of fashion
There's a wrong and a right

Near a tree by a river
There's a hole in the ground
Where an old man of Aran
Goes around and around
And his mind is a beacon
In the veil of the night
For a strange kind of fashion
There's a wrong and a right

Near a tree by a river
There's a hole in the ground
Where an old man of Aran
Goes around and around
And his mind is a beacon
In the veil of the night
For a strange kind of fashion
There's a wrong and a right
And he'll never fight over you
 */