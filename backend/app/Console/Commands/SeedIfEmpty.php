<?php

namespace App\Console\Commands;

use App\Models\LearningTrack;
use Illuminate\Console\Command;

class SeedIfEmpty extends Command
{
    protected $signature   = 'db:seed-if-empty';
    protected $description = 'Run seeders only when the database has no data (safe for every deploy)';

    public function handle(): void
    {
        if (LearningTrack::count() === 0) {
            $this->info('Database is empty — running seeders...');
            $this->call('db:seed', ['--force' => true]);
            $this->info('Done.');
        } else {
            $this->info('Data already exists — skipping seed.');
        }
    }
}
