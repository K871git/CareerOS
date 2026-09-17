<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\SendOtpRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Http\Resources\UserResource;
use App\Mail\OtpMail;
use App\Models\OtpToken;
use App\Models\User;
use App\Models\UserConsent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name'                => $request->name,
            'email'               => $request->email,
            'mobile'              => $request->mobile,
            'password'            => Hash::make($request->password),
            'consent_version'     => \App\Http\Controllers\Api\V1\ConsentController::CURRENT_VERSION,
            'consent_accepted_at' => now(),
        ]);

        // Record consent event with IP and UA for DPDP Act 2023 compliance
        UserConsent::create([
            'user_id'         => $user->id,
            'consent_version' => \App\Http\Controllers\Api\V1\ConsentController::CURRENT_VERSION,
            'ip_address'      => $request->ip(),
            'user_agent'      => $request->userAgent(),
            'consented_at'    => now(),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful.',
            'data'    => [
                'user'  => new UserResource($user),
                'token' => $token,
            ],
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.',
                'errors'  => [],
            ], 401);
        }

        $user  = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data'    => [
                'user'  => new UserResource($user),
                'token' => $token,
            ],
        ]);
    }

    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        $email = $request->email;

        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'success' => true,
                'message' => 'If this email is registered, an OTP has been sent.',
            ]);
        }

        // Rate-limit per email: block if an unexpired OTP was sent in the last 2 minutes
        $recentExists = OtpToken::where('email', $email)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->where('created_at', '>', now()->subMinutes(2))
            ->exists();

        if ($recentExists) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait a moment before requesting another OTP.',
            ], 429);
        }

        OtpToken::where('email', $email)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->delete();

        $code = OtpToken::generateCode();

        OtpToken::create([
            'email'      => $email,
            'code'       => Hash::make($code),
            'expires_at' => now()->addMinutes(5),
        ]);

        Mail::to($email)->send(new OtpMail($code));

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to your email.',
            'data'    => [],
        ]);
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $otp = OtpToken::where('email', $request->email)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otp || !Hash::check($request->code, $otp->code)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP.',
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No account found with this email address.',
            ], 404);
        }

        $otp->markUsed();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data'    => [
                'user'  => new UserResource($user),
                'token' => $token,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
            'data'    => [],
        ]);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        Password::sendResetLink($request->only('email'));

        // Always return success to prevent email enumeration
        return response()->json([
            'success' => true,
            'message' => 'If this email is registered, you will receive a password reset link shortly.',
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token'                 => ['required', 'string'],
            'email'                 => ['required', 'email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password): void {
                $user->forceFill(['password' => Hash::make($password)])->save();
                $user->tokens()->delete(); // revoke all sessions after password change
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully. Please log in with your new password.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => match ($status) {
                Password::INVALID_TOKEN => 'This password reset link is invalid or has expired.',
                Password::INVALID_USER  => 'No account found with this email address.',
                default                 => 'Unable to reset password. Please try again.',
            },
        ], 422);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Authenticated user retrieved.',
            'data'    => new UserResource($request->user()),
        ]);
    }
}
