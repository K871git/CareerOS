<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $getTopicId = fn (string $slug) => DB::table('topics')->where('slug', $slug)->value('id');

        $questions = [
            // PHP Variables & Data Types
            ['topic_slug' => 'php-variables-data-types', 'type' => 'MCQ', 'difficulty' => 'Easy',   'question' => 'What is the output of: echo gettype(42);',                                                    'explanation' => 'gettype() returns a string representing the type. 42 is an integer, so it returns "integer".'],
            ['topic_slug' => 'php-variables-data-types', 'type' => 'MCQ', 'difficulty' => 'Easy',   'question' => 'Which PHP function checks whether a variable is set and is not null?',                        'explanation' => 'isset() returns true if a variable exists and its value is not null. empty() checks falsy values. is_null() only checks for null.'],

            // PHP Functions & Closures
            ['topic_slug' => 'php-functions-closures',  'type' => 'MCQ', 'difficulty' => 'Medium', 'question' => 'What keyword is used inside a closure to import a variable from the outer scope?',            'explanation' => 'The "use" keyword captures outer scope variables into a closure. Arrow functions capture them implicitly.'],
            ['topic_slug' => 'php-functions-closures',  'type' => 'MCQ', 'difficulty' => 'Medium', 'question' => 'Which syntax creates an arrow function in PHP 7.4+?',                                         'explanation' => 'Arrow functions use the fn keyword with => and automatically capture outer scope variables.'],

            // OOP in PHP
            ['topic_slug' => 'php-oop',                 'type' => 'MCQ', 'difficulty' => 'Medium', 'question' => 'What is constructor property promotion in PHP 8?',                                            'explanation' => 'Constructor property promotion lets you declare and assign class properties directly in the constructor signature, removing the need for separate property declarations.'],

            // Eloquent ORM
            ['topic_slug' => 'laravel-eloquent',        'type' => 'MCQ', 'difficulty' => 'Medium', 'question' => 'Which Eloquent method prevents N+1 query problems by loading relationships in advance?',      'explanation' => 'with() eager-loads the specified relationships in a single additional query instead of one query per model, preventing N+1 problems.'],
            ['topic_slug' => 'laravel-eloquent',        'type' => 'MCQ', 'difficulty' => 'Medium', 'question' => 'What does upsert() do in Laravel Eloquent?',                                                  'explanation' => 'upsert() inserts rows that do not exist and updates rows that do, based on a unique key — all in a single query.'],

            // REST Principles
            ['topic_slug' => 'rest-principles',         'type' => 'MCQ', 'difficulty' => 'Easy',   'question' => 'Which HTTP status code should a REST API return when a resource is created successfully?',    'explanation' => '201 Created is the correct status for a successful POST that results in a new resource. 200 OK is for successful reads or updates.'],

            // JavaScript Async
            ['topic_slug' => 'js-async',                'type' => 'MCQ', 'difficulty' => 'Medium', 'question' => 'What does the await keyword do in an async function?',                                        'explanation' => 'await pauses the execution of the async function until the Promise resolves, then returns its resolved value. It does not block the event loop.'],

            // React Hooks
            ['topic_slug' => 'react-hooks',             'type' => 'MCQ', 'difficulty' => 'Medium', 'question' => 'What is the purpose of the dependency array in useEffect?',                                   'explanation' => 'The dependency array controls when useEffect re-runs. An empty array runs it once after mount. Specific values run it when those values change.'],
        ];

        foreach ($questions as $item) {
            $topicId = $getTopicId($item['topic_slug']);

            $exists = DB::table('questions')
                ->where('topic_id', $topicId)
                ->where('question', $item['question'])
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('questions')->insert([
                'topic_id'    => $topicId,
                'type'        => $item['type'],
                'difficulty'  => $item['difficulty'],
                'question'    => $item['question'],
                'explanation' => $item['explanation'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }
}
