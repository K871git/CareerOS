<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class UserPoint extends Model
{
    protected $fillable = ['user_id', 'balance', 'lifetime_earned', 'lifetime_spent'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function ensureExists(int $userId): void
    {
        static::firstOrCreate(['user_id' => $userId], [
            'balance'          => 0,
            'lifetime_earned'  => 0,
            'lifetime_spent'   => 0,
        ]);
    }

    public static function credit(int $userId, int $amount, string $type, string $description, array $meta = []): void
    {
        DB::transaction(function () use ($userId, $amount, $type, $description, $meta) {
            static::ensureExists($userId);

            static::where('user_id', $userId)->increment('balance', $amount);
            static::where('user_id', $userId)->increment('lifetime_earned', $amount);

            PointEvent::create([
                'user_id'     => $userId,
                'type'        => $type,
                'amount'      => $amount,
                'description' => $description,
                'meta'        => $meta ?: null,
            ]);
        });
    }

    // Returns false if insufficient balance
    public static function debit(int $userId, int $amount, string $type, string $description, array $meta = []): bool
    {
        return DB::transaction(function () use ($userId, $amount, $type, $description, $meta) {
            static::ensureExists($userId);

            $row = static::where('user_id', $userId)->lockForUpdate()->first();

            if (!$row || $row->balance < $amount) {
                return false;
            }

            $row->decrement('balance', $amount);
            $row->increment('lifetime_spent', $amount);

            PointEvent::create([
                'user_id'     => $userId,
                'type'        => $type,
                'amount'      => -$amount,
                'description' => $description,
                'meta'        => $meta ?: null,
            ]);

            return true;
        });
    }

    public static function balanceFor(int $userId): int
    {
        static::ensureExists($userId);
        return (int) static::where('user_id', $userId)->value('balance');
    }
}
