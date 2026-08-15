<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $trackId = fn (string $slug) => DB::table('learning_tracks')->where('slug', $slug)->value('id');

        $subjects = [
            // Full Stack Web Development — core language foundations
            [
                'learning_track_id' => $trackId('full-stack-web-development'),
                'title'             => 'PHP Fundamentals',
                'slug'              => 'php-fundamentals',
                'description'       => 'Core PHP syntax, OOP, error handling, and modern PHP features.',
                'display_order'     => 1,
            ],
            [
                'learning_track_id' => $trackId('full-stack-web-development'),
                'title'             => 'Databases & SQL',
                'slug'              => 'databases-and-sql',
                'description'       => 'MySQL fundamentals, schema design, indexing, and query optimization.',
                'display_order'     => 2,
            ],

            // Backend Engineering
            [
                'learning_track_id' => $trackId('backend-engineering'),
                'title'             => 'Laravel Framework',
                'slug'              => 'laravel-framework',
                'description'       => 'Routing, controllers, models, migrations, Eloquent ORM, and APIs.',
                'display_order'     => 1,
            ],
            [
                'learning_track_id' => $trackId('backend-engineering'),
                'title'             => 'REST API Design',
                'slug'              => 'rest-api-design',
                'description'       => 'RESTful principles, versioning, authentication, and API best practices.',
                'display_order'     => 2,
            ],
            [
                'learning_track_id' => $trackId('backend-engineering'),
                'title'             => 'System Design',
                'slug'              => 'system-design',
                'description'       => 'Scalability, load balancing, caching, microservices, and distributed systems.',
                'display_order'     => 3,
            ],

            // Frontend Engineering
            [
                'learning_track_id' => $trackId('frontend-engineering'),
                'title'             => 'JavaScript Core',
                'slug'              => 'javascript-core',
                'description'       => 'ES6+, async/await, closures, prototypes, and the event loop.',
                'display_order'     => 1,
            ],
            [
                'learning_track_id' => $trackId('frontend-engineering'),
                'title'             => 'React Fundamentals',
                'slug'              => 'react-fundamentals',
                'description'       => 'JSX, components, props, state, lifecycle, and the virtual DOM.',
                'display_order'     => 2,
            ],
            [
                'learning_track_id' => $trackId('frontend-engineering'),
                'title'             => 'React & TypeScript',
                'slug'              => 'react-and-typescript',
                'description'       => 'Component architecture, hooks, state management, and TypeScript integration.',
                'display_order'     => 3,
            ],
        ];

        foreach ($subjects as $subject) {
            DB::table('subjects')->updateOrInsert(
                ['slug' => $subject['slug']],
                array_merge($subject, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]),
            );
        }
    }
}
