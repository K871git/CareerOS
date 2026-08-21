<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;

class JsJuniorQuestionsSeeder extends Seeder
{
    public function run(): void
    {
        $track = LearningTrack::firstOrCreate(
            ['slug' => 'frontend-engineering'],
            [
                'title'         => 'Frontend Engineering',
                'description'   => 'Frontend engineering — JavaScript, React, and modern web technologies.',
                'display_order' => 2,
            ]
        );

        $subject = Subject::firstOrCreate(
            ['slug' => 'javascript'],
            [
                'learning_track_id' => $track->id,
                'title'             => 'JavaScript',
                'description'       => 'The language of the web. Master JavaScript from core fundamentals to advanced patterns used in modern front-end and full-stack development.',
                'display_order'     => 2,
            ]
        );

        // Ensure all 3 level topics exist (safe for later seeders)
        Topic::firstOrCreate(
            ['slug' => 'js-basics-junior'],
            [
                'subject_id'    => $subject->id,
                'title'         => 'JavaScript Basics — Junior',
                'description'   => 'Core JavaScript: variables, data types, functions, arrays, objects, and ES6 fundamentals. Perfect for junior-level interview preparation.',
                'display_order' => 1,
            ]
        );
        Topic::firstOrCreate(
            ['slug' => 'js-intermediate'],
            [
                'subject_id'    => $subject->id,
                'title'         => 'JavaScript Intermediate',
                'description'   => 'Closures, prototypes, async/await, the event loop, and advanced array methods. For developers targeting mid-level roles.',
                'display_order' => 2,
            ]
        );
        Topic::firstOrCreate(
            ['slug' => 'js-advanced'],
            [
                'subject_id'    => $subject->id,
                'title'         => 'JavaScript Advanced',
                'description'   => 'Design patterns, performance optimisation, memory management, module systems, and TypeScript concepts. Essential for senior developer interviews.',
                'display_order' => 3,
            ]
        );

        $topic = Topic::where('slug', 'js-basics-junior')->firstOrFail();

        Question::where('topic_id', $topic->id)->delete();

        foreach ($this->questions() as $qData) {
            $q = Question::create([
                'topic_id'    => $topic->id,
                'type'        => 'MCQ',
                'difficulty'  => 'Easy',
                'question'    => $qData['question'],
                'explanation' => $qData['explanation'],
            ]);

            QuestionOption::insert(array_map(fn ($opt) => [
                'question_id' => $q->id,
                'option_text' => $opt['text'],
                'is_correct'  => $opt['correct'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ], $qData['options']));
        }

        $count = Question::where('topic_id', $topic->id)->count();
        $this->command->info("JS Junior: {$count} questions total.");
    }

    private function questions(): array
    {
        return [
            // ── Variables & Declarations ────────────────────────────────
            [
                'question'    => 'What is the difference between let and var in JavaScript?',
                'explanation' => 'var is function-scoped and is hoisted to the top of its function. let is block-scoped (confined to the nearest { }) and is not accessible before its declaration (temporal dead zone). let is preferred in modern JavaScript.',
                'options'     => [
                    ['text' => 'let is block-scoped; var is function-scoped', 'correct' => true],
                    ['text' => 'var is block-scoped; let is function-scoped', 'correct' => false],
                    ['text' => 'They are identical — just different keywords', 'correct' => false],
                    ['text' => 'let only works inside classes; var works everywhere', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which keyword declares a variable whose value cannot be reassigned?',
                'explanation' => 'const declares a binding that cannot be reassigned after initialisation. Note: for objects and arrays, the reference is constant but the contents can still be mutated. const is block-scoped like let.',
                'options'     => [
                    ['text' => 'const', 'correct' => true],
                    ['text' => 'final', 'correct' => false],
                    ['text' => 'static', 'correct' => false],
                    ['text' => 'readonly', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What will the following code output? console.log(typeof undefined);',
                'explanation' => 'The typeof operator returns a string describing the type. typeof undefined returns the string "undefined". This is one of the few safe uses of typeof on a potentially undeclared variable.',
                'options'     => [
                    ['text' => '"undefined"', 'correct' => true],
                    ['text' => '"null"', 'correct' => false],
                    ['text' => '"object"', 'correct' => false],
                    ['text' => 'ReferenceError', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does variable hoisting mean in JavaScript?',
                'explanation' => 'Hoisting is JavaScript\'s behaviour of moving variable and function declarations to the top of their scope before code execution. var declarations are hoisted and initialised to undefined; function declarations are fully hoisted. let and const are hoisted but not initialised (temporal dead zone).',
                'options'     => [
                    ['text' => 'Variable declarations are moved to the top of their scope before execution', 'correct' => true],
                    ['text' => 'Variables are automatically converted to the correct type', 'correct' => false],
                    ['text' => 'Variables are stored in the global scope by default', 'correct' => false],
                    ['text' => 'Variables defined with var cannot be used before declaration', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the output of: console.log(typeof null);',
                'explanation' => 'typeof null returns "object" — this is a well-known JavaScript bug from the language\'s first version. null is a primitive value, not an object, but typeof incorrectly reports it as "object". Use === null to check for null.',
                'options'     => [
                    ['text' => '"object"', 'correct' => true],
                    ['text' => '"null"', 'correct' => false],
                    ['text' => '"undefined"', 'correct' => false],
                    ['text' => '"primitive"', 'correct' => false],
                ],
            ],
            // ── Data Types & Type Coercion ──────────────────────────────
            [
                'question'    => 'How many primitive data types does JavaScript have?',
                'explanation' => 'JavaScript has 7 primitive types: string, number, bigint, boolean, undefined, symbol, and null. Everything else (arrays, functions, objects) is of type "object". Primitives are immutable and compared by value.',
                'options'     => [
                    ['text' => '7', 'correct' => true],
                    ['text' => '5', 'correct' => false],
                    ['text' => '6', 'correct' => false],
                    ['text' => '8', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the result of: "5" + 3 in JavaScript?',
                'explanation' => 'When the + operator is used with a string and a number, JavaScript coerces the number to a string and performs string concatenation. "5" + 3 results in "53", not 8. Use parseInt() or Number() to convert first if arithmetic is intended.',
                'options'     => [
                    ['text' => '"53"', 'correct' => true],
                    ['text' => '8', 'correct' => false],
                    ['text' => 'NaN', 'correct' => false],
                    ['text' => 'TypeError', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the result of: "5" - 3 in JavaScript?',
                'explanation' => 'Unlike +, the - operator does not concatenate strings. JavaScript coerces "5" to the number 5 and performs arithmetic subtraction. "5" - 3 = 2. The - operator always attempts numeric conversion.',
                'options'     => [
                    ['text' => '2', 'correct' => true],
                    ['text' => '"53"', 'correct' => false],
                    ['text' => 'NaN', 'correct' => false],
                    ['text' => '"5-3"', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does NaN stand for in JavaScript?',
                'explanation' => 'NaN stands for "Not a Number". It is a special numeric value returned when a mathematical operation cannot produce a meaningful numeric result, such as parseInt("abc") or 0/0. Despite its name, typeof NaN === "number".',
                'options'     => [
                    ['text' => 'Not a Number', 'correct' => true],
                    ['text' => 'Null and Nil', 'correct' => false],
                    ['text' => 'Negative and Null', 'correct' => false],
                    ['text' => 'No Assigned Name', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the result of: Boolean("") in JavaScript?',
                'explanation' => 'An empty string "" is a falsy value in JavaScript. Boolean("") returns false. The falsy values are: false, 0, -0, 0n, "", null, undefined, and NaN. Everything else (including "0" and []) is truthy.',
                'options'     => [
                    ['text' => 'false', 'correct' => true],
                    ['text' => 'true', 'correct' => false],
                    ['text' => 'undefined', 'correct' => false],
                    ['text' => 'null', 'correct' => false],
                ],
            ],
            // ── Operators & Equality ────────────────────────────────────
            [
                'question'    => 'What is the difference between == and === in JavaScript?',
                'explanation' => '== is the loose equality operator — it performs type coercion before comparison (e.g., 0 == false is true). === is the strict equality operator — it checks both value and type without coercion (0 === false is false). Always prefer === to avoid unexpected bugs.',
                'options'     => [
                    ['text' => '== coerces types; === checks type and value without coercion', 'correct' => true],
                    ['text' => '=== coerces types; == checks type and value', 'correct' => false],
                    ['text' => 'They are identical in modern JavaScript', 'correct' => false],
                    ['text' => '== is for numbers only; === is for strings only', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the typeof operator return for an array?',
                'explanation' => 'Arrays are objects in JavaScript, so typeof [] returns "object". To check if a value is an array, use Array.isArray(value) which returns true only for arrays, not for other objects.',
                'options'     => [
                    ['text' => '"object"', 'correct' => true],
                    ['text' => '"array"', 'correct' => false],
                    ['text' => '"list"', 'correct' => false],
                    ['text' => '"collection"', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the || operator return in JavaScript?',
                'explanation' => '|| (logical OR) returns the first truthy operand, or the last operand if all are falsy. For example: null || "default" returns "default". This is commonly used to set default values, though the nullish coalescing operator ?? is preferred for null/undefined specifically.',
                'options'     => [
                    ['text' => 'The first truthy value, or the last value if all are falsy', 'correct' => true],
                    ['text' => 'Always true or false', 'correct' => false],
                    ['text' => 'The last truthy value', 'correct' => false],
                    ['text' => 'The first falsy value', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the nullish coalescing operator (??) used for?',
                'explanation' => 'The ?? operator returns the right-hand operand only if the left is null or undefined. Unlike ||, it does NOT treat 0, "", or false as nullish. Example: 0 ?? "default" returns 0, but 0 || "default" returns "default".',
                'options'     => [
                    ['text' => 'Returns the right operand only when the left is null or undefined', 'correct' => true],
                    ['text' => 'Returns the right operand for any falsy left value', 'correct' => false],
                    ['text' => 'Checks if both operands are null', 'correct' => false],
                    ['text' => 'It is an alias for the || operator', 'correct' => false],
                ],
            ],
            // ── Functions ───────────────────────────────────────────────
            [
                'question'    => 'What is the difference between a function declaration and a function expression?',
                'explanation' => 'A function declaration (function foo(){}) is hoisted entirely — it can be called before its definition in the code. A function expression (const foo = function(){}) is only hoisted as a variable (undefined), so calling it before the assignment throws a TypeError.',
                'options'     => [
                    ['text' => 'Declarations are hoisted fully; expressions are not callable before their assignment', 'correct' => true],
                    ['text' => 'Expressions are hoisted; declarations are not', 'correct' => false],
                    ['text' => 'They are completely identical in behaviour', 'correct' => false],
                    ['text' => 'Declarations cannot be passed as arguments; expressions can', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an arrow function in JavaScript?',
                'explanation' => 'Arrow functions (=>) are a compact function syntax introduced in ES6. They do not have their own this, arguments, super, or new.target bindings — they inherit these from the surrounding lexical scope. They cannot be used as constructors.',
                'options'     => [
                    ['text' => 'A concise function syntax that lexically binds "this" from its surrounding scope', 'correct' => true],
                    ['text' => 'A function that only accepts one argument', 'correct' => false],
                    ['text' => 'A function that automatically returns a value without braces', 'correct' => false],
                    ['text' => 'A function that cannot be assigned to a variable', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does a function return if no return statement is specified?',
                'explanation' => 'In JavaScript, if a function has no return statement, or a return with no value, it returns undefined. This is true for regular functions and arrow functions with a block body. Arrow functions with an expression body (no {}) implicitly return the expression.',
                'options'     => [
                    ['text' => 'undefined', 'correct' => true],
                    ['text' => 'null', 'correct' => false],
                    ['text' => '0', 'correct' => false],
                    ['text' => 'false', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a higher-order function in JavaScript?',
                'explanation' => 'A higher-order function is a function that accepts another function as an argument, returns a function, or both. Common examples in JavaScript are Array.map(), Array.filter(), Array.reduce(), and setTimeout(). This enables functional programming patterns.',
                'options'     => [
                    ['text' => 'A function that takes another function as argument or returns one', 'correct' => true],
                    ['text' => 'A function defined at the top of the file', 'correct' => false],
                    ['text' => 'A function with more than three parameters', 'correct' => false],
                    ['text' => 'A function that calls itself recursively', 'correct' => false],
                ],
            ],
            // ── Arrays ──────────────────────────────────────────────────
            [
                'question'    => 'Which array method adds one or more elements to the END of an array and returns the new length?',
                'explanation' => 'Array.push() appends elements to the end of an array and returns the new length. Use unshift() to add to the beginning, pop() to remove from the end, and shift() to remove from the beginning.',
                'options'     => [
                    ['text' => 'push()', 'correct' => true],
                    ['text' => 'append()', 'correct' => false],
                    ['text' => 'add()', 'correct' => false],
                    ['text' => 'insert()', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does Array.map() do?',
                'explanation' => 'Array.map() creates a new array by applying a callback function to each element of the original array. It does not modify the original array. The new array has the same length, with each element transformed by the callback.',
                'options'     => [
                    ['text' => 'Creates a new array by transforming each element with a callback', 'correct' => true],
                    ['text' => 'Filters elements from an array based on a condition', 'correct' => false],
                    ['text' => 'Reduces an array to a single accumulated value', 'correct' => false],
                    ['text' => 'Checks if at least one element passes a test', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does Array.filter() return?',
                'explanation' => 'Array.filter() returns a new array containing only elements for which the callback returns a truthy value. If no elements match, it returns an empty array. The original array is not modified.',
                'options'     => [
                    ['text' => 'A new array with elements that pass the callback test', 'correct' => true],
                    ['text' => 'The first element that passes the test', 'correct' => false],
                    ['text' => 'true or false', 'correct' => false],
                    ['text' => 'The modified original array', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you check if a variable is an array in JavaScript?',
                'explanation' => 'Array.isArray(value) is the correct and reliable way to check if a value is an array. typeof [] returns "object" (misleading), and instanceof Array can fail across different iframes/windows due to different Array constructors.',
                'options'     => [
                    ['text' => 'Array.isArray(value)', 'correct' => true],
                    ['text' => 'typeof value === "array"', 'correct' => false],
                    ['text' => 'value.isArray()', 'correct' => false],
                    ['text' => 'value instanceof Object', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the spread operator (...) do with an array?',
                'explanation' => 'The spread operator (...) expands an iterable (like an array) into individual elements. It is used to copy arrays ([...arr]), merge arrays ([...a, ...b]), or pass array elements as function arguments (Math.max(...nums)).',
                'options'     => [
                    ['text' => 'Expands an array into individual elements', 'correct' => true],
                    ['text' => 'Merges all arrays into a nested structure', 'correct' => false],
                    ['text' => 'Converts an array to a string', 'correct' => false],
                    ['text' => 'Removes duplicate elements from an array', 'correct' => false],
                ],
            ],
            // ── Objects ─────────────────────────────────────────────────
            [
                'question'    => 'How do you access a property of an object in JavaScript?',
                'explanation' => 'Object properties can be accessed with dot notation (obj.property) for valid identifier names, or bracket notation (obj["property"]) for dynamic keys, keys with spaces, or reserved words. Both produce the same result for simple string keys.',
                'options'     => [
                    ['text' => 'Both dot notation (obj.prop) and bracket notation (obj["prop"])', 'correct' => true],
                    ['text' => 'Only dot notation (obj.prop)', 'correct' => false],
                    ['text' => 'Only bracket notation (obj["prop"])', 'correct' => false],
                    ['text' => 'Using the get() method: obj.get("prop")', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is destructuring assignment in JavaScript?',
                'explanation' => 'Destructuring allows unpacking values from arrays or properties from objects into distinct variables in a single expression. Example: const { name, age } = person; or const [first, second] = array; It makes code cleaner when extracting multiple values.',
                'options'     => [
                    ['text' => 'A syntax to unpack values from arrays or objects into variables', 'correct' => true],
                    ['text' => 'A way to delete properties from an object', 'correct' => false],
                    ['text' => 'A method to convert objects to JSON', 'correct' => false],
                    ['text' => 'A way to merge two objects into one', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does Object.keys() return?',
                'explanation' => 'Object.keys(obj) returns an array of the object\'s own enumerable string-keyed property names. It does not include inherited properties from the prototype chain. Object.values() returns the values, and Object.entries() returns [key, value] pairs.',
                'options'     => [
                    ['text' => 'An array of the object\'s own enumerable property names', 'correct' => true],
                    ['text' => 'An array of the object\'s values', 'correct' => false],
                    ['text' => 'The number of properties in the object', 'correct' => false],
                    ['text' => 'A boolean indicating if the object has any keys', 'correct' => false],
                ],
            ],
            // ── ES6+ Features ───────────────────────────────────────────
            [
                'question'    => 'What are template literals in JavaScript?',
                'explanation' => 'Template literals use backticks (`) and allow embedding expressions with ${expression} syntax. They also support multi-line strings without escape characters. Example: `Hello, ${name}!` is cleaner than "Hello, " + name + "!".',
                'options'     => [
                    ['text' => 'String literals using backticks that support embedded expressions and multi-line text', 'correct' => true],
                    ['text' => 'A special type of string that is automatically translated', 'correct' => false],
                    ['text' => 'An alternative syntax for regular expressions', 'correct' => false],
                    ['text' => 'A way to define reusable string constants', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of the "use strict" directive in JavaScript?',
                'explanation' => '"use strict" enables strict mode, which applies stricter parsing and error handling. In strict mode: undeclared variables cause errors, deleting variables/functions is not allowed, duplicate parameter names are forbidden, and this is undefined in non-method functions. It prevents many silent errors.',
                'options'     => [
                    ['text' => 'Enables strict mode, which turns silent errors into thrown errors', 'correct' => true],
                    ['text' => 'Forces the engine to use the latest JavaScript version', 'correct' => false],
                    ['text' => 'Prevents the file from being imported into other modules', 'correct' => false],
                    ['text' => 'Disables type coercion throughout the file', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the Array.find() method return?',
                'explanation' => 'Array.find() returns the FIRST element that satisfies the testing callback. If no element satisfies the test, it returns undefined. Use findIndex() to get the index instead, or filter() to get all matching elements.',
                'options'     => [
                    ['text' => 'The first element that passes the callback test, or undefined', 'correct' => true],
                    ['text' => 'All elements that pass the test in a new array', 'correct' => false],
                    ['text' => 'The index of the first matching element', 'correct' => false],
                    ['text' => 'true or false', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which method converts a JavaScript object to a JSON string?',
                'explanation' => 'JSON.stringify(value) converts a JavaScript value (object, array, etc.) to a JSON string. JSON.parse(jsonString) does the reverse — it parses a JSON string back into a JavaScript value. Properties with undefined values or function values are omitted from the output.',
                'options'     => [
                    ['text' => 'JSON.stringify()', 'correct' => true],
                    ['text' => 'JSON.parse()', 'correct' => false],
                    ['text' => 'Object.toJSON()', 'correct' => false],
                    ['text' => 'toString()', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the ternary operator (?:) do in JavaScript?',
                'explanation' => 'The ternary operator is a concise inline conditional: condition ? valueIfTrue : valueIfFalse. It is the only JavaScript operator that takes three operands. Example: const label = age >= 18 ? "adult" : "minor";',
                'options'     => [
                    ['text' => 'Evaluates a condition and returns one of two values', 'correct' => true],
                    ['text' => 'Checks if a variable is null or undefined', 'correct' => false],
                    ['text' => 'Performs three mathematical operations in one statement', 'correct' => false],
                    ['text' => 'Declares a variable with a default value', 'correct' => false],
                ],
            ],
            // ── Control Flow & Errors ───────────────────────────────────
            [
                'question'    => 'What is the difference between for...in and for...of loops?',
                'explanation' => 'for...in iterates over the enumerable KEYS (property names) of an object, including inherited ones from the prototype chain. for...of iterates over the VALUES of any iterable (arrays, strings, Maps, Sets). Use for...of for arrays to avoid unexpected behaviour.',
                'options'     => [
                    ['text' => 'for...in iterates keys; for...of iterates values of iterables', 'correct' => true],
                    ['text' => 'for...of iterates keys; for...in iterates values', 'correct' => false],
                    ['text' => 'They are identical — just different syntax', 'correct' => false],
                    ['text' => 'for...in is for arrays only; for...of is for objects only', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the try...catch block do in JavaScript?',
                'explanation' => 'A try...catch block allows you to handle runtime errors gracefully. Code in the try block runs normally. If it throws an error, execution jumps to the catch block where the error can be handled. An optional finally block runs regardless of whether an error occurred.',
                'options'     => [
                    ['text' => 'Executes code and catches errors so they can be handled gracefully', 'correct' => true],
                    ['text' => 'Prevents the browser from logging errors to the console', 'correct' => false],
                    ['text' => 'Stops all errors from occurring in the try block', 'correct' => false],
                    ['text' => 'Retries failed code up to three times automatically', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the output of: console.log(1 == "1");',
                'explanation' => '1 == "1" uses loose equality. JavaScript coerces the string "1" to the number 1, then compares 1 === 1, which is true. This is why strict equality (===) is recommended — it avoids unexpected coercion.',
                'options'     => [
                    ['text' => 'true', 'correct' => true],
                    ['text' => 'false', 'correct' => false],
                    ['text' => 'TypeError', 'correct' => false],
                    ['text' => 'undefined', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the Array.includes() method do?',
                'explanation' => 'Array.includes(value) returns true if the array contains the specified value, using the SameValueZero comparison (similar to ===, but handles NaN correctly). It returns false otherwise. More readable than indexOf() for simple existence checks.',
                'options'     => [
                    ['text' => 'Returns true if the array contains the specified value', 'correct' => true],
                    ['text' => 'Returns the index of the value in the array', 'correct' => false],
                    ['text' => 'Adds the value to the array if not already present', 'correct' => false],
                    ['text' => 'Returns the number of times the value appears', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a callback function in JavaScript?',
                'explanation' => 'A callback function is a function passed as an argument to another function, to be invoked after some operation completes. Callbacks are fundamental to JavaScript\'s asynchronous model (e.g., setTimeout, event listeners, array methods like forEach).',
                'options'     => [
                    ['text' => 'A function passed as an argument to be called later', 'correct' => true],
                    ['text' => 'A function that calls itself recursively', 'correct' => false],
                    ['text' => 'A function that returns another function', 'correct' => false],
                    ['text' => 'A function that only runs once', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the Array.reduce() method do?',
                'explanation' => 'Array.reduce(callback, initialValue) processes each element of an array with the callback, accumulating a single result. The callback receives (accumulator, currentValue, index, array). It is used to sum numbers, flatten arrays, build objects, and more.',
                'options'     => [
                    ['text' => 'Reduces the array to a single accumulated value using a callback', 'correct' => true],
                    ['text' => 'Removes the last element from the array', 'correct' => false],
                    ['text' => 'Filters elements and returns a shorter array', 'correct' => false],
                    ['text' => 'Sorts the array in ascending order', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the output of: console.log([] == false);',
                'explanation' => 'This is type coercion in action. [] coerces to "" (empty string), then "" coerces to 0. false coerces to 0. So 0 == 0 is true. This demonstrates why === is critical — [] === false would correctly return false.',
                'options'     => [
                    ['text' => 'true', 'correct' => true],
                    ['text' => 'false', 'correct' => false],
                    ['text' => 'TypeError', 'correct' => false],
                    ['text' => 'undefined', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you remove the last element from an array in JavaScript?',
                'explanation' => 'Array.pop() removes the LAST element from an array and returns it. It mutates the original array. Use shift() to remove the first element, splice() to remove from a specific index, and filter() for non-mutating removal.',
                'options'     => [
                    ['text' => 'pop()', 'correct' => true],
                    ['text' => 'shift()', 'correct' => false],
                    ['text' => 'remove()', 'correct' => false],
                    ['text' => 'delete()', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is short-circuit evaluation in JavaScript?',
                'explanation' => 'Short-circuit evaluation means logical operators stop evaluating as soon as the result is determined. With &&, if the left side is falsy the right side is never evaluated. With ||, if the left side is truthy the right side is skipped. This is often used for conditional execution: condition && doSomething().',
                'options'     => [
                    ['text' => 'Logical operators stop evaluating once the result is known', 'correct' => true],
                    ['text' => 'The engine skips expressions that contain errors', 'correct' => false],
                    ['text' => 'The || operator always evaluates both sides', 'correct' => false],
                    ['text' => 'A technique to reduce function execution time', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which method joins all elements of an array into a string?',
                'explanation' => 'Array.join(separator) concatenates all array elements into a string, separated by the given separator. If no separator is provided, elements are joined with a comma by default. join() does not modify the original array.',
                'options'     => [
                    ['text' => 'join()', 'correct' => true],
                    ['text' => 'concat()', 'correct' => false],
                    ['text' => 'toString()', 'correct' => false],
                    ['text' => 'merge()', 'correct' => false],
                ],
            ],
        ];
    }
}
