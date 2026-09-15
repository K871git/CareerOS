<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Seed the database on first boot and after fresh deployments
Schedule::command('db:seed-if-empty')->everyFifteenMinutes()->runInBackground();
