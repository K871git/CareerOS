<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Send password reset links to the React frontend, not the Laravel backend
        ResetPassword::createUrlUsing(function ($notifiable, string $token): string {
            $frontendUrl = rtrim(config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:5173')), '/');
            return "{$frontendUrl}/reset-password?token={$token}&email=" . urlencode($notifiable->email);
        });
    }
}
