<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PointEvent;
use App\Models\UserPoint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PointController extends Controller
{
    public function balance(Request $request): JsonResponse
    {
        $user   = $request->user();
        $points = UserPoint::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'lifetime_earned' => 0, 'lifetime_spent' => 0]
        );

        return response()->json([
            'success' => true,
            'data'    => [
                'balance'         => $points->balance,
                'lifetime_earned' => $points->lifetime_earned,
                'lifetime_spent'  => $points->lifetime_spent,
            ],
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $events = PointEvent::where('user_id', $request->user()->id)
            ->latest()
            ->limit(50)
            ->get(['id', 'type', 'amount', 'description', 'meta', 'created_at']);

        return response()->json([
            'success' => true,
            'data'    => $events,
        ]);
    }
}
