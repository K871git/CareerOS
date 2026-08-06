<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionOptionSeeder extends Seeder
{
    public function run(): void
    {
        $getQuestionId = fn (string $question) => DB::table('questions')->where('question', $question)->value('id');

        $options = [
            // Q1: gettype(42)
            'What is the output of: echo gettype(42);' => [
                ['option_text' => 'int',     'is_correct' => false],
                ['option_text' => 'integer', 'is_correct' => true],
                ['option_text' => 'number',  'is_correct' => false],
                ['option_text' => 'float',   'is_correct' => false],
            ],

            // Q2: isset()
            'Which PHP function checks whether a variable is set and is not null?' => [
                ['option_text' => 'empty()',   'is_correct' => false],
                ['option_text' => 'is_null()', 'is_correct' => false],
                ['option_text' => 'isset()',   'is_correct' => true],
                ['option_text' => 'defined()', 'is_correct' => false],
            ],

            // Q3: closure use keyword
            'What keyword is used inside a closure to import a variable from the outer scope?' => [
                ['option_text' => 'import', 'is_correct' => false],
                ['option_text' => 'use',    'is_correct' => true],
                ['option_text' => 'global', 'is_correct' => false],
                ['option_text' => 'bind',   'is_correct' => false],
            ],

            // Q4: arrow function syntax
            'Which syntax creates an arrow function in PHP 7.4+?' => [
                ['option_text' => 'function() =>',    'is_correct' => false],
                ['option_text' => 'fn() =>',          'is_correct' => true],
                ['option_text' => 'lambda() =>',      'is_correct' => false],
                ['option_text' => 'arrow function()', 'is_correct' => false],
            ],

            // Q5: constructor property promotion
            'What is constructor property promotion in PHP 8?' => [
                ['option_text' => 'Automatically calling the parent constructor',                   'is_correct' => false],
                ['option_text' => 'Declaring and assigning properties directly in the constructor', 'is_correct' => true],
                ['option_text' => 'Creating a constructor with default values',                     'is_correct' => false],
                ['option_text' => 'Making constructors private',                                    'is_correct' => false],
            ],

            // Q6: with() eager loading
            'Which Eloquent method prevents N+1 query problems by loading relationships in advance?' => [
                ['option_text' => 'load()',    'is_correct' => false],
                ['option_text' => 'with()',    'is_correct' => true],
                ['option_text' => 'preload()', 'is_correct' => false],
                ['option_text' => 'include()', 'is_correct' => false],
            ],

            // Q7: upsert()
            'What does upsert() do in Laravel Eloquent?' => [
                ['option_text' => 'Deletes and re-inserts records',                             'is_correct' => false],
                ['option_text' => 'Inserts new records and updates existing ones in one query', 'is_correct' => true],
                ['option_text' => 'Updates all records unconditionally',                        'is_correct' => false],
                ['option_text' => 'Creates a record only if it does not exist',                'is_correct' => false],
            ],

            // Q8: 201 status code
            'Which HTTP status code should a REST API return when a resource is created successfully?' => [
                ['option_text' => '200 OK',         'is_correct' => false],
                ['option_text' => '201 Created',    'is_correct' => true],
                ['option_text' => '202 Accepted',   'is_correct' => false],
                ['option_text' => '204 No Content', 'is_correct' => false],
            ],

            // Q9: await keyword
            'What does the await keyword do in an async function?' => [
                ['option_text' => 'Blocks the entire JavaScript thread',                          'is_correct' => false],
                ['option_text' => 'Pauses the async function until the Promise resolves',         'is_correct' => true],
                ['option_text' => 'Converts a callback to a Promise',                             'is_correct' => false],
                ['option_text' => 'Runs a function synchronously',                                'is_correct' => false],
            ],

            // Q10: useEffect dependency array
            'What is the purpose of the dependency array in useEffect?' => [
                ['option_text' => 'It lists the props the component accepts',                            'is_correct' => false],
                ['option_text' => 'It controls when the effect re-runs based on which values changed',   'is_correct' => true],
                ['option_text' => 'It prevents the component from re-rendering',                         'is_correct' => false],
                ['option_text' => 'It defines which state variables are available in the effect',        'is_correct' => false],
            ],
        ];

        foreach ($options as $questionText => $questionOptions) {
            $questionId = $getQuestionId($questionText);

            if (! $questionId) {
                continue;
            }

            $alreadySeeded = DB::table('question_options')
                ->where('question_id', $questionId)
                ->exists();

            if ($alreadySeeded) {
                continue;
            }

            foreach ($questionOptions as $option) {
                DB::table('question_options')->insert([
                    'question_id' => $questionId,
                    'option_text' => $option['option_text'],
                    'is_correct'  => $option['is_correct'],
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }
    }
}
