<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TopicSeeder extends Seeder
{
    public function run(): void
    {
        $subjectId = fn (string $slug) => DB::table('subjects')->where('slug', $slug)->value('id');

        $topics = [
            // PHP Fundamentals
            ['subject_id' => $subjectId('php-fundamentals'), 'title' => 'Variables & Data Types',    'slug' => 'php-variables-data-types',    'description' => 'Scalars, arrays, null, type juggling.',                    'display_order' => 1],
            ['subject_id' => $subjectId('php-fundamentals'), 'title' => 'Functions & Closures',      'slug' => 'php-functions-closures',      'description' => 'Named functions, arrow functions, closures, first-class callables.', 'display_order' => 2],
            ['subject_id' => $subjectId('php-fundamentals'), 'title' => 'OOP in PHP',                'slug' => 'php-oop',                     'description' => 'Classes, interfaces, traits, abstract classes, visibility.', 'display_order' => 3],
            ['subject_id' => $subjectId('php-fundamentals'), 'title' => 'Error Handling',            'slug' => 'php-error-handling',          'description' => 'Exceptions, try/catch/finally, custom exceptions.',         'display_order' => 4],

            // Laravel Framework
            ['subject_id' => $subjectId('laravel-framework'), 'title' => 'Routing & Middleware',     'slug' => 'laravel-routing-middleware',  'description' => 'Route definitions, route model binding, middleware pipeline.', 'display_order' => 1],
            ['subject_id' => $subjectId('laravel-framework'), 'title' => 'Eloquent ORM',             'slug' => 'laravel-eloquent',            'description' => 'Models, relationships, query builder, mass assignment.',    'display_order' => 2],
            ['subject_id' => $subjectId('laravel-framework'), 'title' => 'Migrations & Seeders',     'slug' => 'laravel-migrations-seeders',  'description' => 'Schema builder, migration workflow, database seeding.',     'display_order' => 3],
            ['subject_id' => $subjectId('laravel-framework'), 'title' => 'Form Requests & Validation','slug' => 'laravel-validation',         'description' => 'FormRequest classes, custom rules, validation messages.',   'display_order' => 4],

            // React & TypeScript
            ['subject_id' => $subjectId('react-and-typescript'), 'title' => 'React Hooks',           'slug' => 'react-hooks',                 'description' => 'useState, useEffect, useRef, custom hooks.',               'display_order' => 1],
            ['subject_id' => $subjectId('react-and-typescript'), 'title' => 'TypeScript with React', 'slug' => 'typescript-with-react',       'description' => 'Props typing, generics, utility types, strict mode.',       'display_order' => 2],

            // Databases & SQL
            ['subject_id' => $subjectId('databases-and-sql'), 'title' => 'SQL Queries',              'slug' => 'sql-queries',                 'description' => 'SELECT, JOIN, GROUP BY, subqueries, window functions.',     'display_order' => 1],
            ['subject_id' => $subjectId('databases-and-sql'), 'title' => 'Schema Design',            'slug' => 'database-schema-design',      'description' => 'Normalization, indexes, foreign keys, constraints.',        'display_order' => 2],

            // REST API Design
            ['subject_id' => $subjectId('rest-api-design'), 'title' => 'REST Principles',            'slug' => 'rest-principles',             'description' => 'Resources, HTTP verbs, status codes, statelessness.',      'display_order' => 1],
            ['subject_id' => $subjectId('rest-api-design'), 'title' => 'API Authentication',         'slug' => 'api-authentication',          'description' => 'Token auth, Sanctum, OAuth2 concepts.',                    'display_order' => 2],

            // System Design
            ['subject_id' => $subjectId('system-design'), 'title' => 'Scalability Basics',           'slug' => 'scalability-basics',          'description' => 'Vertical vs horizontal scaling, load balancers.',          'display_order' => 1],
            ['subject_id' => $subjectId('system-design'), 'title' => 'Caching Strategies',           'slug' => 'caching-strategies',          'description' => 'Cache-aside, write-through, Redis use cases.',             'display_order' => 2],

            // JavaScript Core
            ['subject_id' => $subjectId('javascript-core'), 'title' => 'Closures & Scope',           'slug' => 'js-closures-scope',           'description' => 'Lexical scope, closure patterns, IIFE.',                  'display_order' => 1],
            ['subject_id' => $subjectId('javascript-core'), 'title' => 'Async JavaScript',           'slug' => 'js-async',                    'description' => 'Promises, async/await, event loop, microtasks.',           'display_order' => 2],

            // React Fundamentals
            ['subject_id' => $subjectId('react-fundamentals'), 'title' => 'Components & Props',      'slug' => 'react-components-props',      'description' => 'Functional components, prop drilling, composition.',       'display_order' => 1],
            ['subject_id' => $subjectId('react-fundamentals'), 'title' => 'State Management',        'slug' => 'react-state-management',      'description' => 'useState, Context API, lifting state up.',                 'display_order' => 2],
        ];

        foreach ($topics as $topic) {
            DB::table('topics')->updateOrInsert(
                ['slug' => $topic['slug']],
                array_merge($topic, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]),
            );
        }
    }
}
