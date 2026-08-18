<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Seeder;

class TheoryLanguagesSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            // ── Level 1 — Easy ───────────────────────────────────────────────
            [
                'level' => 1, 'difficulty' => 'Easy',
                'question' => 'What does `typeof null` return in JavaScript?',
                'options' => [
                    ['text' => '"null"',      'correct' => false],
                    ['text' => '"object"',    'correct' => true],
                    ['text' => '"undefined"', 'correct' => false],
                    ['text' => '"boolean"',   'correct' => false],
                ],
                'explanation' => 'A historical bug in JavaScript means typeof null returns "object" even though null is a primitive.',
            ],
            [
                'level' => 1, 'difficulty' => 'Easy',
                'question' => 'Which method removes the last element from a JavaScript array?',
                'options' => [
                    ['text' => 'shift()',  'correct' => false],
                    ['text' => 'splice()', 'correct' => false],
                    ['text' => 'pop()',    'correct' => true],
                    ['text' => 'slice()',  'correct' => false],
                ],
                'explanation' => 'pop() removes and returns the last element of the array, mutating the original.',
            ],
            [
                'level' => 1, 'difficulty' => 'Easy',
                'question' => 'What is the result of `1 + "2"` in JavaScript?',
                'options' => [
                    ['text' => '3',    'correct' => false],
                    ['text' => '"12"', 'correct' => true],
                    ['text' => 'NaN',  'correct' => false],
                    ['text' => '"3"',  'correct' => false],
                ],
                'explanation' => 'When + is used with a number and a string, JavaScript coerces the number to a string and concatenates.',
            ],
            [
                'level' => 1, 'difficulty' => 'Easy',
                'question' => 'Which keyword declares a block-scoped variable that can be reassigned?',
                'options' => [
                    ['text' => 'var',   'correct' => false],
                    ['text' => 'const', 'correct' => false],
                    ['text' => 'let',   'correct' => true],
                    ['text' => 'static','correct' => false],
                ],
                'explanation' => 'let is block-scoped and allows reassignment, unlike const (no reassign) and var (function-scoped).',
            ],
            [
                'level' => 1, 'difficulty' => 'Easy',
                'question' => 'What does the `===` operator check in JavaScript?',
                'options' => [
                    ['text' => 'Value only',           'correct' => false],
                    ['text' => 'Type only',            'correct' => false],
                    ['text' => 'Value and type',       'correct' => true],
                    ['text' => 'Reference equality',   'correct' => false],
                ],
                'explanation' => 'Strict equality (===) checks both value and type with no type coercion, unlike == which coerces types.',
            ],
            [
                'level' => 1, 'difficulty' => 'Easy',
                'question' => 'What is a function that calls itself called?',
                'options' => [
                    ['text' => 'Callback function',   'correct' => false],
                    ['text' => 'Arrow function',      'correct' => false],
                    ['text' => 'Recursive function',  'correct' => true],
                    ['text' => 'Anonymous function',  'correct' => false],
                ],
                'explanation' => 'A recursive function is one that calls itself, typically with a base case to prevent infinite recursion.',
            ],
            [
                'level' => 1, 'difficulty' => 'Easy',
                'question' => 'Which of the following is NOT a primitive data type in JavaScript?',
                'options' => [
                    ['text' => 'string',  'correct' => false],
                    ['text' => 'number',  'correct' => false],
                    ['text' => 'Array',   'correct' => true],
                    ['text' => 'boolean', 'correct' => false],
                ],
                'explanation' => 'Arrays are objects in JavaScript. Primitives are: string, number, boolean, null, undefined, symbol, bigint.',
            ],
            [
                'level' => 1, 'difficulty' => 'Easy',
                'question' => 'What does the `return` statement do in a function?',
                'options' => [
                    ['text' => 'Exits the program',                         'correct' => false],
                    ['text' => 'Outputs to the console',                    'correct' => false],
                    ['text' => 'Exits the function and optionally returns a value', 'correct' => true],
                    ['text' => 'Declares a variable',                       'correct' => false],
                ],
                'explanation' => 'return ends function execution and specifies the value to be returned to the caller.',
            ],
            [
                'level' => 1, 'difficulty' => 'Easy',
                'question' => 'Which method converts a JavaScript string to all lowercase?',
                'options' => [
                    ['text' => 'toLower()',    'correct' => false],
                    ['text' => 'toLowerCase()', 'correct' => true],
                    ['text' => 'lower()',      'correct' => false],
                    ['text' => 'lowerCase()',  'correct' => false],
                ],
                'explanation' => 'String.prototype.toLowerCase() returns a new string with all characters in lowercase.',
            ],
            [
                'level' => 1, 'difficulty' => 'Easy',
                'question' => 'Which loop is guaranteed to execute its body at least once?',
                'options' => [
                    ['text' => 'for loop',       'correct' => false],
                    ['text' => 'while loop',     'correct' => false],
                    ['text' => 'do...while loop', 'correct' => true],
                    ['text' => 'for...of loop',  'correct' => false],
                ],
                'explanation' => 'A do...while loop executes its body first, then checks the condition, so it always runs at least once.',
            ],

            // ── Level 2 — Medium ─────────────────────────────────────────────
            [
                'level' => 2, 'difficulty' => 'Medium',
                'question' => 'What is a closure in JavaScript?',
                'options' => [
                    ['text' => 'A function that runs immediately upon definition',                     'correct' => false],
                    ['text' => 'A function that retains access to its outer scope after the outer function returns', 'correct' => true],
                    ['text' => 'A private method defined inside a class',                               'correct' => false],
                    ['text' => 'A function that takes another function as an argument',                 'correct' => false],
                ],
                'explanation' => 'A closure is the combination of a function and the lexical environment within which it was declared. It retains access to outer variables.',
            ],
            [
                'level' => 2, 'difficulty' => 'Medium',
                'question' => 'What does `this` refer to inside an arrow function?',
                'options' => [
                    ['text' => 'The arrow function itself',                                    'correct' => false],
                    ['text' => 'The global window object always',                              'correct' => false],
                    ['text' => 'The enclosing lexical `this` from the surrounding context',   'correct' => true],
                    ['text' => 'undefined in strict mode only',                               'correct' => false],
                ],
                'explanation' => 'Arrow functions do not have their own `this`. They inherit `this` from the surrounding lexical scope at definition time.',
            ],
            [
                'level' => 2, 'difficulty' => 'Medium',
                'question' => 'What is the Event Loop responsible for in JavaScript?',
                'options' => [
                    ['text' => 'Iterating over DOM events with a for loop',               'correct' => false],
                    ['text' => 'Moving async callbacks from the queue to the call stack when the stack is empty', 'correct' => true],
                    ['text' => 'Handling CSS animations and repaints',                    'correct' => false],
                    ['text' => 'Garbage collecting unused variables',                     'correct' => false],
                ],
                'explanation' => 'The Event Loop continuously checks if the call stack is empty, then picks the next task from the callback queue and pushes it to the stack.',
            ],
            [
                'level' => 2, 'difficulty' => 'Medium',
                'question' => 'What does `Promise.all([p1, p2, p3])` do when one promise rejects?',
                'options' => [
                    ['text' => 'It ignores the rejection and resolves with the others',    'correct' => false],
                    ['text' => 'It waits for all to settle before rejecting',              'correct' => false],
                    ['text' => 'It immediately rejects with the first rejection reason',   'correct' => true],
                    ['text' => 'It retries the rejected promise automatically',            'correct' => false],
                ],
                'explanation' => 'Promise.all fails fast — if any promise rejects, the returned promise immediately rejects with that reason.',
            ],
            [
                'level' => 2, 'difficulty' => 'Medium',
                'question' => 'What is hoisting in JavaScript?',
                'options' => [
                    ['text' => 'Uploading code to a CDN before execution',                                    'correct' => false],
                    ['text' => 'The engine moving function and variable declarations to the top of their scope during compilation', 'correct' => true],
                    ['text' => 'Calling a function before its closing brace',                                 'correct' => false],
                    ['text' => 'Promoting async operations to run on the main thread',                        'correct' => false],
                ],
                'explanation' => 'During the creation phase, JS hoists var declarations (initialised as undefined) and function declarations (fully available) to the top of their scope.',
            ],
            [
                'level' => 2, 'difficulty' => 'Medium',
                'question' => 'What does `Array.prototype.reduce()` always return?',
                'options' => [
                    ['text' => 'An array of the same length',  'correct' => false],
                    ['text' => 'A boolean',                    'correct' => false],
                    ['text' => 'A single accumulated value',   'correct' => true],
                    ['text' => 'A filtered array',             'correct' => false],
                ],
                'explanation' => 'reduce() applies a function against an accumulator and returns a single output value (which can be any type).',
            ],
            [
                'level' => 2, 'difficulty' => 'Medium',
                'question' => 'What is a higher-order function?',
                'options' => [
                    ['text' => 'A function with more than 3 parameters',                    'correct' => false],
                    ['text' => 'A function that takes another function as an argument or returns one', 'correct' => true],
                    ['text' => 'A function declared at the top of the module scope',        'correct' => false],
                    ['text' => 'A function with elevated execution priority',               'correct' => false],
                ],
                'explanation' => 'Higher-order functions treat functions as first-class citizens. Examples: map, filter, reduce, setTimeout.',
            ],
            [
                'level' => 2, 'difficulty' => 'Medium',
                'question' => 'What is prototypal inheritance?',
                'options' => [
                    ['text' => 'Inheriting from a class blueprint using the `extends` keyword only',  'correct' => false],
                    ['text' => 'Objects inheriting directly from other objects via the prototype chain', 'correct' => true],
                    ['text' => 'A design pattern exclusive to TypeScript',                             'correct' => false],
                    ['text' => 'Copying all properties from one object to another',                    'correct' => false],
                ],
                'explanation' => 'In JS, every object has a [[Prototype]] link to another object. Property lookup walks up this chain until null is reached.',
            ],
            [
                'level' => 2, 'difficulty' => 'Medium',
                'question' => 'What is the key difference between `null` and `undefined` in JavaScript?',
                'options' => [
                    ['text' => 'They are identical; both mean "no value"',                                        'correct' => false],
                    ['text' => '`null` is explicitly assigned absence of value; `undefined` means declared but not yet assigned', 'correct' => true],
                    ['text' => '`undefined` is assigned explicitly; `null` is the JS default',                    'correct' => false],
                    ['text' => 'Only `undefined` is falsy',                                                       'correct' => false],
                ],
                'explanation' => 'undefined is the default uninitialized state. null is an intentional assignment meaning "no object". Both are falsy.',
            ],
            [
                'level' => 2, 'difficulty' => 'Medium',
                'question' => 'What does `"use strict"` enable in JavaScript?',
                'options' => [
                    ['text' => 'Strong static typing for variables',                                         'correct' => false],
                    ['text' => 'Strict mode — prevents silent errors and disallows unsafe features',         'correct' => true],
                    ['text' => 'Disables all global variables',                                              'correct' => false],
                    ['text' => 'Forces synchronous execution of all code',                                   'correct' => false],
                ],
                'explanation' => 'Strict mode throws errors for things like using undeclared variables, duplicate parameters, and writing to read-only properties.',
            ],

            // ── Level 3 — Hard ───────────────────────────────────────────────
            [
                'level' => 3, 'difficulty' => 'Hard',
                'question' => 'What is the Temporal Dead Zone (TDZ) in JavaScript?',
                'options' => [
                    ['text' => 'The time between page load and first script execution',                                  'correct' => false],
                    ['text' => 'The period between entering a scope and the `let`/`const` declaration where accessing the binding throws a ReferenceError', 'correct' => true],
                    ['text' => 'A memory zone reserved for garbage collection',                                          'correct' => false],
                    ['text' => 'The gap between two consecutive event loop ticks',                                       'correct' => false],
                ],
                'explanation' => 'let and const are hoisted but NOT initialised. Any access before the declaration line is in the TDZ and throws ReferenceError.',
            ],
            [
                'level' => 3, 'difficulty' => 'Hard',
                'question' => 'What does `Function.prototype.bind()` return?',
                'options' => [
                    ['text' => 'The immediate result of calling the function',               'correct' => false],
                    ['text' => 'A new function with `this` permanently bound to the given context', 'correct' => true],
                    ['text' => 'A Promise that resolves when the function completes',        'correct' => false],
                    ['text' => 'undefined',                                                  'correct' => false],
                ],
                'explanation' => 'bind() returns a new function whose `this` is permanently set. Unlike call/apply, it does not invoke the function immediately.',
            ],
            [
                'level' => 3, 'difficulty' => 'Hard',
                'question' => 'What are WeakMap keys limited to in JavaScript?',
                'options' => [
                    ['text' => 'Strings and Symbols only',       'correct' => false],
                    ['text' => 'Objects and non-registered Symbols only', 'correct' => true],
                    ['text' => 'Any primitive value',            'correct' => false],
                    ['text' => 'Numbers and strings only',       'correct' => false],
                ],
                'explanation' => 'WeakMap keys must be objects or non-registered Symbols. This allows the GC to collect the key object when no other references exist, automatically removing the entry.',
            ],
            [
                'level' => 3, 'difficulty' => 'Hard',
                'question' => 'What is currying in functional programming?',
                'options' => [
                    ['text' => 'Adding try/catch error handling to a function',                                        'correct' => false],
                    ['text' => 'Transforming a multi-argument function into a chain of single-argument functions',     'correct' => true],
                    ['text' => 'Converting a callback-based API into a promise-based one',                             'correct' => false],
                    ['text' => 'Caching the results of a function for identical inputs',                               'correct' => false],
                ],
                'explanation' => 'Currying converts f(a, b, c) into f(a)(b)(c). Each call takes one argument and returns a new function until all args are supplied.',
            ],
            [
                'level' => 3, 'difficulty' => 'Hard',
                'question' => 'What is memoization?',
                'options' => [
                    ['text' => 'Storing program state to persistent disk storage',                              'correct' => false],
                    ['text' => 'An optimisation that caches the return value of a function for a given input so repeated calls skip re-computation', 'correct' => true],
                    ['text' => 'Pre-loading assets before the page renders',                                    'correct' => false],
                    ['text' => 'Compressing function arguments before passing them',                            'correct' => false],
                ],
                'explanation' => 'Memoization stores results in a cache (typically a Map or plain object). Subsequent calls with the same arguments return the cached result instantly.',
            ],
            [
                'level' => 3, 'difficulty' => 'Hard',
                'question' => 'What is the output of `0.1 + 0.2 === 0.3` in JavaScript?',
                'options' => [
                    ['text' => 'true',      'correct' => false],
                    ['text' => 'false',     'correct' => true],
                    ['text' => 'NaN',       'correct' => false],
                    ['text' => 'undefined', 'correct' => false],
                ],
                'explanation' => 'Floating-point arithmetic (IEEE 754) means 0.1 + 0.2 = 0.30000000000000004, not exactly 0.3. Always use epsilon comparison for floats.',
            ],
            [
                'level' => 3, 'difficulty' => 'Hard',
                'question' => 'What is a generator function in JavaScript?',
                'options' => [
                    ['text' => 'A function that always returns an array of values',           'correct' => false],
                    ['text' => 'A function that can pause execution with `yield` and resume, producing multiple values over time', 'correct' => true],
                    ['text' => 'A constructor function for generating class instances',       'correct' => false],
                    ['text' => 'A factory function that returns different object shapes',     'correct' => false],
                ],
                'explanation' => 'Defined with function*, generators use yield to pause/resume. Each call to .next() runs until the next yield, making them lazily evaluated sequences.',
            ],
            [
                'level' => 3, 'difficulty' => 'Hard',
                'question' => 'What does `Object.freeze()` do?',
                'options' => [
                    ['text' => 'Makes an object shallowly immutable — no properties can be added, changed, or deleted', 'correct' => true],
                    ['text' => 'Deeply freezes all nested objects recursively',                                          'correct' => false],
                    ['text' => 'Converts the object to an immutable JSON string',                                        'correct' => false],
                    ['text' => 'Prevents the object from being garbage collected',                                       'correct' => false],
                ],
                'explanation' => 'Object.freeze() makes shallow immutability — direct properties cannot be modified. Nested objects are still mutable unless also frozen.',
            ],
            [
                'level' => 3, 'difficulty' => 'Hard',
                'question' => 'What does `Symbol.iterator` define on an object?',
                'options' => [
                    ['text' => 'A unique identifier key for the object',                           'correct' => false],
                    ['text' => 'The default iteration protocol, making the object usable in for...of loops', 'correct' => true],
                    ['text' => 'A way to compare two Symbol values',                               'correct' => false],
                    ['text' => 'A metadata decorator for class methods',                            'correct' => false],
                ],
                'explanation' => 'An object with [Symbol.iterator]() is iterable. The method must return an iterator with a .next() method returning {value, done}.',
            ],
            [
                'level' => 3, 'difficulty' => 'Hard',
                'question' => 'What is the difference between `call()` and `apply()` in JavaScript?',
                'options' => [
                    ['text' => 'call() is synchronous; apply() is asynchronous',                                        'correct' => false],
                    ['text' => 'call() accepts arguments individually; apply() accepts them as an array',               'correct' => true],
                    ['text' => 'call() permanently binds `this`; apply() does not',                                     'correct' => false],
                    ['text' => 'There is no difference — they are aliases',                                              'correct' => false],
                ],
                'explanation' => 'Both invoke a function immediately with a given `this`. The only difference: call(ctx, a, b) vs apply(ctx, [a, b]). bind() returns a new function instead.',
            ],
        ];

        foreach ($questions as $q) {
            $question = Question::create([
                'topic_id'     => null,
                'type'         => 'MCQ',
                'difficulty'   => $q['difficulty'],
                'question'     => $q['question'],
                'explanation'  => $q['explanation'],
                'theory_area'  => 'languages',
                'theory_level' => $q['level'],
            ]);

            foreach ($q['options'] as $option) {
                $question->options()->create([
                    'option_text' => $option['text'],
                    'is_correct'  => $option['correct'],
                ]);
            }
        }
    }
}
