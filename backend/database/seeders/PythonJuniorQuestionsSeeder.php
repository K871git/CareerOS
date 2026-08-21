<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Database\Seeder;

class PythonJuniorQuestionsSeeder extends Seeder
{
    public function run(): void
    {
        $track = LearningTrack::firstOrCreate(
            ['slug' => 'backend-engineering'],
            ['title' => 'Backend Engineering', 'display_order' => 3]
        );

        $subject = Subject::firstOrCreate(
            ['slug' => 'python'],
            [
                'learning_track_id' => $track->id,
                'title'             => 'Python',
                'display_order'     => 5,
                'description'       => 'Python practice questions — Junior, Intermediate, Advanced',
            ]
        );

        // Create ALL 3 topics upfront so subsequent seeders can reference them safely
        Topic::firstOrCreate(
            ['slug' => 'python-junior'],
            ['subject_id' => $subject->id, 'title' => 'Python Basics — Junior', 'display_order' => 1]
        );
        Topic::firstOrCreate(
            ['slug' => 'python-intermediate'],
            ['subject_id' => $subject->id, 'title' => 'Python Intermediate', 'display_order' => 2]
        );
        Topic::firstOrCreate(
            ['slug' => 'python-advanced'],
            ['subject_id' => $subject->id, 'title' => 'Python Advanced', 'display_order' => 3]
        );

        $topic = Topic::where('slug', 'python-junior')->firstOrFail();

        Question::where('topic_id', $topic->id)->delete();

        foreach ($this->questions() as $qData) {
            $question = Question::create([
                'topic_id'    => $topic->id,
                'question'    => $qData['question'],
                'type'        => 'MCQ',
                'difficulty'  => 'Easy',
                'explanation' => $qData['explanation'],
            ]);

            QuestionOption::insert(array_map(fn ($opt) => [
                'question_id' => $question->id,
                'option_text' => $opt['text'],
                'is_correct'  => $opt['correct'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ], $qData['options']));
        }

        $count = Question::where('topic_id', $topic->id)->count();
        $this->command->info("Python Junior: {$count} questions seeded.");
    }

    private function questions(): array
    {
        return [
            // --- Variables & Data Types ---
            [
                'question'    => 'What is the output of: type(3.14)?',
                'explanation' => 'Python represents decimal numbers as floats. type() returns the class of the object.',
                'options'     => [
                    ['text' => "<class 'int'>",   'correct' => false],
                    ['text' => "<class 'float'>", 'correct' => true],
                    ['text' => "<class 'str'>",   'correct' => false],
                    ['text' => "<class 'number'>", 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which of the following is a valid Python variable name?',
                'explanation' => 'Python variable names must start with a letter or underscore, not a digit. Hyphens are not allowed.',
                'options'     => [
                    ['text' => '2name',   'correct' => false],
                    ['text' => 'my-var',  'correct' => false],
                    ['text' => '_count',  'correct' => true],
                    ['text' => 'class',   'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the value of: bool(0)?',
                'explanation' => 'In Python, 0, empty strings, empty lists, None, and empty dicts are all falsy. bool(0) is False.',
                'options'     => [
                    ['text' => 'True',  'correct' => false],
                    ['text' => 'False', 'correct' => true],
                    ['text' => '0',     'correct' => false],
                    ['text' => 'None',  'correct' => false],
                ],
            ],
            [
                'question'    => 'What does None represent in Python?',
                'explanation' => 'None is Python\'s null value. It is an object of NoneType and represents the absence of a value.',
                'options'     => [
                    ['text' => 'The integer 0',          'correct' => false],
                    ['text' => 'An empty string',         'correct' => false],
                    ['text' => 'The absence of a value', 'correct' => true],
                    ['text' => 'False',                   'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the result of: 10 // 3?',
                'explanation' => '// is the floor division operator. 10 // 3 = 3 (the integer part of 3.33...).',
                'options'     => [
                    ['text' => '3.33', 'correct' => false],
                    ['text' => '3',    'correct' => true],
                    ['text' => '1',    'correct' => false],
                    ['text' => '4',    'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the result of: 10 % 3?',
                'explanation' => '% is the modulo operator. 10 % 3 = 1 because 10 = 3 * 3 + 1.',
                'options'     => [
                    ['text' => '3', 'correct' => false],
                    ['text' => '0', 'correct' => false],
                    ['text' => '1', 'correct' => true],
                    ['text' => '2', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the result of: 2 ** 3?',
                'explanation' => '** is the exponentiation operator. 2 ** 3 = 8 (2 to the power of 3).',
                'options'     => [
                    ['text' => '6',  'correct' => false],
                    ['text' => '8',  'correct' => true],
                    ['text' => '9',  'correct' => false],
                    ['text' => '23', 'correct' => false],
                ],
            ],
            // --- Strings ---
            [
                'question'    => 'Which method converts a string to uppercase?',
                'explanation' => 'str.upper() returns a copy of the string with all characters converted to uppercase.',
                'options'     => [
                    ['text' => 'str.upper()',      'correct' => true],
                    ['text' => 'str.toUpper()',    'correct' => false],
                    ['text' => 'str.capitalize()', 'correct' => false],
                    ['text' => 'str.uppercase()',  'correct' => false],
                ],
            ],
            [
                'question'    => 'What does "hello"[1] return?',
                'explanation' => 'Python strings are zero-indexed. Index 1 is the second character, which is "e".',
                'options'     => [
                    ['text' => '"h"', 'correct' => false],
                    ['text' => '"e"', 'correct' => true],
                    ['text' => '"l"', 'correct' => false],
                    ['text' => '"o"', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does "hello"[-1] return?',
                'explanation' => 'Negative indices count from the end. -1 is the last character, which is "o".',
                'options'     => [
                    ['text' => '"h"', 'correct' => false],
                    ['text' => '"e"', 'correct' => false],
                    ['text' => '"l"', 'correct' => false],
                    ['text' => '"o"', 'correct' => true],
                ],
            ],
            [
                'question'    => 'What is the output of: len("Python")?',
                'explanation' => 'len() returns the number of characters in a string. "Python" has 6 characters.',
                'options'     => [
                    ['text' => '5', 'correct' => false],
                    ['text' => '6', 'correct' => true],
                    ['text' => '7', 'correct' => false],
                    ['text' => '4', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does "hello world".split() return?',
                'explanation' => 'split() with no argument splits on whitespace, returning a list of words: [\'hello\', \'world\'].',
                'options'     => [
                    ['text' => '("hello", "world")',   'correct' => false],
                    ['text' => '["hello", "world"]',   'correct' => true],
                    ['text' => '"hello", "world"',      'correct' => false],
                    ['text' => '{"hello", "world"}',   'correct' => false],
                ],
            ],
            [
                'question'    => 'Which f-string correctly embeds a variable name in a string?',
                'explanation' => 'F-strings (formatted string literals) use curly braces to embed expressions: f"Hello, {name}".',
                'options'     => [
                    ['text' => '"Hello, " + name',    'correct' => false],
                    ['text' => 'f"Hello, {name}"',    'correct' => true],
                    ['text' => '"Hello, %s" % name',  'correct' => false],
                    ['text' => '"Hello, $(name)"',    'correct' => false],
                ],
            ],
            [
                'question'    => 'What does "abc" * 3 produce?',
                'explanation' => 'String repetition multiplies the string by the given number: "abcabcabc".',
                'options'     => [
                    ['text' => '"abc3"',      'correct' => false],
                    ['text' => '"abcabcabc"', 'correct' => true],
                    ['text' => '["abc"] * 3', 'correct' => false],
                    ['text' => 'Error',       'correct' => false],
                ],
            ],
            [
                'question'    => 'What does "hello world".replace("world", "Python") return?',
                'explanation' => 'str.replace(old, new) returns a copy with all occurrences of old replaced by new.',
                'options'     => [
                    ['text' => '"hello Python"', 'correct' => true],
                    ['text' => '"Python world"', 'correct' => false],
                    ['text' => '"hello world"',  'correct' => false],
                    ['text' => 'None',            'correct' => false],
                ],
            ],
            // --- Lists ---
            [
                'question'    => 'How do you add an element to the end of a list?',
                'explanation' => 'list.append(item) adds a single element to the end of the list in-place.',
                'options'     => [
                    ['text' => 'list.add(item)',    'correct' => false],
                    ['text' => 'list.push(item)',   'correct' => false],
                    ['text' => 'list.append(item)', 'correct' => true],
                    ['text' => 'list.insert(item)', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does list.pop() do by default?',
                'explanation' => 'pop() with no argument removes and returns the last element of the list.',
                'options'     => [
                    ['text' => 'Removes and returns the first element', 'correct' => false],
                    ['text' => 'Removes and returns the last element',  'correct' => true],
                    ['text' => 'Removes all elements',                  'correct' => false],
                    ['text' => 'Returns the last element without removing it', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the output of: [1, 2, 3][1:3]?',
                'explanation' => 'Slicing [1:3] returns elements at index 1 and 2 (stop is exclusive): [2, 3].',
                'options'     => [
                    ['text' => '[1, 2]',    'correct' => false],
                    ['text' => '[2, 3]',    'correct' => true],
                    ['text' => '[1, 2, 3]', 'correct' => false],
                    ['text' => '[3]',       'correct' => false],
                ],
            ],
            [
                'question'    => 'Which of the following creates an empty list?',
                'explanation' => 'Both [] and list() create an empty list. [] is the most common idiom.',
                'options'     => [
                    ['text' => '{}',     'correct' => false],
                    ['text' => '()',     'correct' => false],
                    ['text' => '[]',     'correct' => true],
                    ['text' => 'set()', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does list.sort() do?',
                'explanation' => 'list.sort() sorts the list in-place in ascending order and returns None.',
                'options'     => [
                    ['text' => 'Returns a new sorted list without modifying the original', 'correct' => false],
                    ['text' => 'Sorts the list in-place and returns None',                 'correct' => true],
                    ['text' => 'Sorts and returns the sorted list',                         'correct' => false],
                    ['text' => 'Raises TypeError for mixed types only',                    'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a list comprehension for squaring numbers 1–5?',
                'explanation' => '[x**2 for x in range(1, 6)] creates [1, 4, 9, 16, 25] in a single readable expression.',
                'options'     => [
                    ['text' => '[x**2 for x in range(1, 6)]', 'correct' => true],
                    ['text' => 'list(x**2, range(1, 6))',      'correct' => false],
                    ['text' => '[x^2 for x in range(5)]',      'correct' => false],
                    ['text' => 'map(x**2, range(1, 6))',        'correct' => false],
                ],
            ],
            // --- Tuples ---
            [
                'question'    => 'What is the key difference between a list and a tuple in Python?',
                'explanation' => 'Tuples are immutable — once created their elements cannot be changed. Lists are mutable.',
                'options'     => [
                    ['text' => 'Tuples can hold more elements than lists',            'correct' => false],
                    ['text' => 'Tuples are immutable; lists are mutable',             'correct' => true],
                    ['text' => 'Lists are faster than tuples',                         'correct' => false],
                    ['text' => 'Tuples are defined with square brackets',             'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you create a tuple with a single element?',
                'explanation' => 'A trailing comma is required: (1,). Without the comma (1) is just parentheses around an integer.',
                'options'     => [
                    ['text' => '(1)',   'correct' => false],
                    ['text' => '(1,)',  'correct' => true],
                    ['text' => '[1,]',  'correct' => false],
                    ['text' => '{1,}',  'correct' => false],
                ],
            ],
            // --- Dictionaries ---
            [
                'question'    => 'How do you access the value for key "name" in a dict d?',
                'explanation' => 'd["name"] accesses the value. d.name would raise an AttributeError; d.get("name") also works but returns None if missing.',
                'options'     => [
                    ['text' => 'd.name',       'correct' => false],
                    ['text' => 'd["name"]',    'correct' => true],
                    ['text' => 'd->name',      'correct' => false],
                    ['text' => 'd.get_key("name")', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does dict.keys() return?',
                'explanation' => 'dict.keys() returns a view object of all the keys in the dictionary.',
                'options'     => [
                    ['text' => 'A list of all values',      'correct' => false],
                    ['text' => 'A view object of all keys', 'correct' => true],
                    ['text' => 'A tuple of all keys',       'correct' => false],
                    ['text' => 'A set of key-value pairs',  'correct' => false],
                ],
            ],
            [
                'question'    => 'What does dict.get("key", "default") return if "key" does not exist?',
                'explanation' => 'dict.get(key, default) returns the default value if the key is not found, instead of raising a KeyError.',
                'options'     => [
                    ['text' => 'Raises KeyError',     'correct' => false],
                    ['text' => 'Returns None',         'correct' => false],
                    ['text' => 'Returns "default"',   'correct' => true],
                    ['text' => 'Returns an empty dict', 'correct' => false],
                ],
            ],
            // --- Sets ---
            [
                'question'    => 'What property makes a set unique compared to a list?',
                'explanation' => 'Sets automatically remove duplicate values. Each element in a set is guaranteed to be unique.',
                'options'     => [
                    ['text' => 'Sets are ordered',                   'correct' => false],
                    ['text' => 'Sets allow duplicate values',         'correct' => false],
                    ['text' => 'Sets only store unique elements',     'correct' => true],
                    ['text' => 'Sets can be indexed like lists',      'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you create an empty set in Python?',
                'explanation' => '{} creates an empty dict, NOT a set. set() is the correct way to create an empty set.',
                'options'     => [
                    ['text' => '{}',     'correct' => false],
                    ['text' => 'set()',  'correct' => true],
                    ['text' => '[]',     'correct' => false],
                    ['text' => 'Set()',  'correct' => false],
                ],
            ],
            // --- Control Flow ---
            [
                'question'    => 'What is the correct syntax for an if-elif-else block in Python?',
                'explanation' => 'Python uses elif (not else if) for additional conditions and uses colons and indentation instead of braces.',
                'options'     => [
                    ['text' => 'if x: elif y: else:', 'correct' => true],
                    ['text' => 'if (x) { elif (y) { else {', 'correct' => false],
                    ['text' => 'if x then elif y then else', 'correct' => false],
                    ['text' => 'if x; elseif y; else;', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the "in" operator do when used with a list?',
                'explanation' => '"in" checks membership. `3 in [1, 2, 3]` returns True if 3 is an element of the list.',
                'options'     => [
                    ['text' => 'Iterates over the list',                    'correct' => false],
                    ['text' => 'Checks if a value exists in the list',      'correct' => true],
                    ['text' => 'Returns the index of the value',            'correct' => false],
                    ['text' => 'Counts occurrences of the value',           'correct' => false],
                ],
            ],
            // --- Loops ---
            [
                'question'    => 'What does range(5) produce?',
                'explanation' => 'range(5) generates integers from 0 up to (but not including) 5: 0, 1, 2, 3, 4.',
                'options'     => [
                    ['text' => '[1, 2, 3, 4, 5]', 'correct' => false],
                    ['text' => '[0, 1, 2, 3, 4]', 'correct' => true],
                    ['text' => '[0, 1, 2, 3, 4, 5]', 'correct' => false],
                    ['text' => '(0, 1, 2, 3, 4)',    'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the "break" statement do inside a loop?',
                'explanation' => 'break exits the nearest enclosing loop immediately, skipping any remaining iterations.',
                'options'     => [
                    ['text' => 'Skips the current iteration and continues the loop', 'correct' => false],
                    ['text' => 'Exits the entire program',                            'correct' => false],
                    ['text' => 'Exits the current loop immediately',                  'correct' => true],
                    ['text' => 'Pauses execution for one iteration',                 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the "continue" statement do inside a loop?',
                'explanation' => 'continue skips the rest of the current iteration and moves to the next one.',
                'options'     => [
                    ['text' => 'Exits the loop immediately',                          'correct' => false],
                    ['text' => 'Skips the current iteration and continues the loop',  'correct' => true],
                    ['text' => 'Restarts the loop from the beginning',                'correct' => false],
                    ['text' => 'Pauses the loop',                                     'correct' => false],
                ],
            ],
            [
                'question'    => 'What does enumerate(["a", "b", "c"]) produce when iterated?',
                'explanation' => 'enumerate() yields (index, value) pairs: (0, "a"), (1, "b"), (2, "c").',
                'options'     => [
                    ['text' => '["a", "b", "c"]',                      'correct' => false],
                    ['text' => '(0, "a"), (1, "b"), (2, "c")',         'correct' => true],
                    ['text' => '(1, "a"), (2, "b"), (3, "c")',         'correct' => false],
                    ['text' => '{"a": 0, "b": 1, "c": 2}',            'correct' => false],
                ],
            ],
            // --- Functions ---
            [
                'question'    => 'What keyword is used to define a function in Python?',
                'explanation' => 'The def keyword is used to define functions in Python.',
                'options'     => [
                    ['text' => 'function', 'correct' => false],
                    ['text' => 'fn',       'correct' => false],
                    ['text' => 'def',      'correct' => true],
                    ['text' => 'func',     'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the output of a function that has no return statement?',
                'explanation' => 'A function without an explicit return statement implicitly returns None.',
                'options'     => [
                    ['text' => '0',    'correct' => false],
                    ['text' => 'None', 'correct' => true],
                    ['text' => 'False', 'correct' => false],
                    ['text' => 'Error', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does *args allow in a function definition?',
                'explanation' => '*args allows a function to accept any number of positional arguments, collected into a tuple.',
                'options'     => [
                    ['text' => 'Accepts only keyword arguments',              'correct' => false],
                    ['text' => 'Accepts any number of positional arguments',  'correct' => true],
                    ['text' => 'Accepts a single optional argument',          'correct' => false],
                    ['text' => 'Accepts a dictionary of arguments',           'correct' => false],
                ],
            ],
            [
                'question'    => 'What does **kwargs allow in a function definition?',
                'explanation' => '**kwargs collects any number of keyword arguments into a dictionary inside the function.',
                'options'     => [
                    ['text' => 'Accepts any number of positional arguments', 'correct' => false],
                    ['text' => 'Accepts any number of keyword arguments',    'correct' => true],
                    ['text' => 'Unpacks a list into function arguments',     'correct' => false],
                    ['text' => 'Merges two dictionaries',                    'correct' => false],
                ],
            ],
            // --- Built-ins ---
            [
                'question'    => 'What does the built-in zip() function do?',
                'explanation' => 'zip() pairs elements from multiple iterables together, producing tuples of corresponding elements.',
                'options'     => [
                    ['text' => 'Compresses files',                                          'correct' => false],
                    ['text' => 'Merges two dictionaries',                                   'correct' => false],
                    ['text' => 'Pairs elements from multiple iterables into tuples',         'correct' => true],
                    ['text' => 'Flattens nested lists',                                     'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the sorted() function return?',
                'explanation' => 'sorted() returns a new sorted list from any iterable, without modifying the original.',
                'options'     => [
                    ['text' => 'Sorts the original iterable in-place and returns None', 'correct' => false],
                    ['text' => 'Returns a new sorted list',                              'correct' => true],
                    ['text' => 'Returns a sorted tuple',                                 'correct' => false],
                    ['text' => 'Raises TypeError for non-numeric iterables',            'correct' => false],
                ],
            ],
            [
                'question'    => 'What does int("42") return?',
                'explanation' => 'int() converts a string to an integer. int("42") returns the integer 42.',
                'options'     => [
                    ['text' => '"42"', 'correct' => false],
                    ['text' => '42.0', 'correct' => false],
                    ['text' => '42',   'correct' => true],
                    ['text' => 'Error', 'correct' => false],
                ],
            ],
            // --- Exception basics ---
            [
                'question'    => 'Which block is used to catch exceptions in Python?',
                'explanation' => 'try/except is used for exception handling. Code that might fail goes in try; the handler goes in except.',
                'options'     => [
                    ['text' => 'try/catch',   'correct' => false],
                    ['text' => 'try/except',  'correct' => true],
                    ['text' => 'try/handle',  'correct' => false],
                    ['text' => 'catch/throw', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What exception is raised when you try to divide by zero in Python?',
                'explanation' => 'Python raises ZeroDivisionError when you attempt to divide any number by zero.',
                'options'     => [
                    ['text' => 'ArithmeticError',  'correct' => false],
                    ['text' => 'ValueError',        'correct' => false],
                    ['text' => 'ZeroDivisionError', 'correct' => true],
                    ['text' => 'MathError',         'correct' => false],
                ],
            ],
            // --- Import ---
            [
                'question'    => 'Which statement correctly imports only the sqrt function from the math module?',
                'explanation' => '"from math import sqrt" imports only sqrt, so you can call it directly as sqrt(9) instead of math.sqrt(9).',
                'options'     => [
                    ['text' => 'import math.sqrt',         'correct' => false],
                    ['text' => 'from math import sqrt',    'correct' => true],
                    ['text' => 'include math (sqrt)',       'correct' => false],
                    ['text' => 'require math.sqrt',        'correct' => false],
                ],
            ],
            // --- Miscellaneous ---
            [
                'question'    => 'What is the output of: print(type([]))?',
                'explanation' => 'type([]) returns the class of an empty list, which is <class \'list\'>.',
                'options'     => [
                    ['text' => "<class 'tuple'>",  'correct' => false],
                    ['text' => "<class 'dict'>",   'correct' => false],
                    ['text' => "<class 'list'>",   'correct' => true],
                    ['text' => "<class 'array'>",  'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the result of: "hello" == "Hello"?',
                'explanation' => 'String comparison in Python is case-sensitive. "hello" and "Hello" are different strings.',
                'options'     => [
                    ['text' => 'True',  'correct' => false],
                    ['text' => 'False', 'correct' => true],
                    ['text' => 'None',  'correct' => false],
                    ['text' => 'Error', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the "pass" statement do in Python?',
                'explanation' => 'pass is a no-op placeholder. It is used where a statement is syntactically required but no action is needed.',
                'options'     => [
                    ['text' => 'Exits the current block',             'correct' => false],
                    ['text' => 'Skips to the next iteration',         'correct' => false],
                    ['text' => 'Does nothing — acts as a placeholder', 'correct' => true],
                    ['text' => 'Passes a value to the caller',        'correct' => false],
                ],
            ],
            [
                'question'    => 'Which comparison operator checks that two values are equal in Python?',
                'explanation' => '== checks equality (are the values the same?). = is assignment. is checks object identity.',
                'options'     => [
                    ['text' => '=',   'correct' => false],
                    ['text' => '==',  'correct' => true],
                    ['text' => '!=',  'correct' => false],
                    ['text' => 'is',  'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the "and" operator return in Python?',
                'explanation' => '"and" returns the first falsy operand, or the last operand if all are truthy. It is short-circuit evaluated.',
                'options'     => [
                    ['text' => 'Always True or False',                                          'correct' => false],
                    ['text' => 'The first falsy value, or the last value if all are truthy',    'correct' => true],
                    ['text' => 'Always a boolean',                                              'correct' => false],
                    ['text' => 'The left operand if it is truthy, else None',                  'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the output of: [1, 2] + [3, 4]?',
                'explanation' => 'The + operator concatenates two lists, returning a new list with all elements: [1, 2, 3, 4].',
                'options'     => [
                    ['text' => '[4, 6]',       'correct' => false],
                    ['text' => '[1, 2, 3, 4]', 'correct' => true],
                    ['text' => '[[1, 2], [3, 4]]', 'correct' => false],
                    ['text' => 'Error',            'correct' => false],
                ],
            ],
        ];
    }
}
