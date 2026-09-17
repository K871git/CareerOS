<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\UserConsent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConsentController extends Controller
{
    public const CURRENT_VERSION = '1.0';

    /**
     * Record the authenticated user's consent acceptance.
     * Called after login/register if consent was not yet stored for this version.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        UserConsent::create([
            'user_id'          => $user->id,
            'consent_version'  => self::CURRENT_VERSION,
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
            'consented_at'     => now(),
        ]);

        $user->update([
            'consent_version'      => self::CURRENT_VERSION,
            'consent_accepted_at'  => now(),
        ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'consent_version' => self::CURRENT_VERSION,
                'message'         => 'Consent recorded. Thank you.',
            ],
        ]);
    }

    /**
     * Withdraw the authenticated user's consent.
     * Per DPDP Act 2023 §6(4): withdrawal must be as easy as giving consent.
     */
    public function withdraw(Request $request): JsonResponse
    {
        $user = $request->user();

        UserConsent::where('user_id', $user->id)
            ->whereNull('withdrawn_at')
            ->update(['withdrawn_at' => now()]);

        $user->update([
            'consent_version'      => null,
            'consent_accepted_at'  => null,
        ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'message' => 'Consent withdrawn. Your account has been marked accordingly.',
            ],
        ]);
    }
}
