<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserConsent extends Model
{
    protected $fillable = [
        'user_id',
        'consent_version',
        'ip_address',
        'user_agent',
        'consented_at',
        'withdrawn_at',
    ];

    protected function casts(): array
    {
        return [
            'consented_at'  => 'datetime',
            'withdrawn_at'  => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
