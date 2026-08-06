<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LearningTrackSeeder extends Seeder
{
    public function run(): void
    {
        $tracks = [
            [
                'title'         => 'Full Stack Web Development',
                'slug'          => 'full-stack-web-development',
                'description'   => 'Master both frontend and backend web development from fundamentals to production.',
                'display_order' => 1,
            ],
            [
                'title'         => 'Backend Engineering',
                'slug'          => 'backend-engineering',
                'description'   => 'Deep dive into server-side development, APIs, databases, and system design.',
                'display_order' => 2,
            ],
            [
                'title'         => 'Frontend Engineering',
                'slug'          => 'frontend-engineering',
                'description'   => 'Build modern, responsive user interfaces with React and TypeScript.',
                'display_order' => 3,
            ],
        ];

        foreach ($tracks as $track) {
            DB::table('learning_tracks')->updateOrInsert(
                ['slug' => $track['slug']],
                array_merge($track, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]),
            );
        }
    }
}
