<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;

class JsIntermediateQuestionsSeeder extends Seeder
{
    public function run(): void
    {
        $track = LearningTrack::firstOrCreate(
            ['slug' => 'frontend-engineering'],
            ['title' => 'Frontend Engineering', 'description' => 'Frontend engineering track.', 'display_order' => 2]
        );

        $subject = Subject::firstOrCreate(
            ['slug' => 'javascript'],
            ['learning_track_id' => $track->id, 'title' => 'JavaScript', 'description' => 'JavaScript practice questions.', 'display_order' => 2]
        );

        $topic = Topic::firstOrCreate(
            ['slug' => 'js-intermediate'],
            ['subject_id' => $subject->id, 'title' => 'JavaScript Intermediate', 'description' => 'Intermediate JavaScript: closures, prototypes, async/await, the event loop, Promises, and this binding.', 'display_order' => 2]
        );

        Question::where('topic_id', $topic->id)->delete();

        foreach ($this->questions() as $qData) {
            $q = Question::create([
                'topic_id'    => $topic->id,
                'type'        => 'MCQ',
                'difficulty'  => 'Medium',
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
        $this->command->info("JS Intermediate: {$count} questions total.");
    }

    private function questions(): array
    {
        return [
            // CLOSURES
            [
                'question' => 'What is a closure in JavaScript?',
                'explanation' => 'A closure is a function that retains access to its outer (enclosing) scope even after the outer function has returned. The inner function "closes over" the variables of the outer function.',
                'options' => [
                    ['text' => 'A function that has access to its own scope, the outer function\'s scope, and the global scope even after the outer function returns', 'correct' => true],
                    ['text' => 'A function that is immediately invoked when defined', 'correct' => false],
                    ['text' => 'A function that cannot access variables outside its own scope', 'correct' => false],
                    ['text' => 'A method that closes the browser window programmatically', 'correct' => false],
                ],
            ],
            [
                'question' => "What will the following code output?\n\nfunction makeCounter() {\n  let count = 0;\n  return function() { return ++count; };\n}\nconst counter = makeCounter();\nconsole.log(counter());\nconsole.log(counter());",
                'explanation' => 'Each call to counter() increments the same `count` variable in the closure. The first call returns 1, the second returns 2.',
                'options' => [
                    ['text' => '1 then 2', 'correct' => true],
                    ['text' => '0 then 1', 'correct' => false],
                    ['text' => '1 then 1', 'correct' => false],
                    ['text' => 'undefined then undefined', 'correct' => false],
                ],
            ],
            [
                'question' => "What does the following code log?\n\nfor (var i = 0; i < 3; i++) {\n  setTimeout(() => console.log(i), 0);\n}",
                'explanation' => 'Because `var` is function-scoped, all three setTimeout callbacks share the same `i` variable. By the time the callbacks run, the loop has finished and i === 3, so it logs 3 three times.',
                'options' => [
                    ['text' => '3, 3, 3', 'correct' => true],
                    ['text' => '0, 1, 2', 'correct' => false],
                    ['text' => '0, 0, 0', 'correct' => false],
                    ['text' => 'undefined, undefined, undefined', 'correct' => false],
                ],
            ],
            [
                'question' => 'Which of the following correctly uses a closure to create a private variable?',
                'explanation' => 'An IIFE creates a new scope, and the returned object exposes methods that close over the private `_count` variable. This is the classic revealing-module pattern.',
                'options' => [
                    ['text' => 'const counter = (function() { let _count = 0; return { increment() { _count++; }, get() { return _count; } }; })()', 'correct' => true],
                    ['text' => 'const counter = { _count: 0, increment() { this._count++; } }', 'correct' => false],
                    ['text' => 'let _count = 0; const counter = { increment() { _count++; } }', 'correct' => false],
                    ['text' => 'class Counter { #count = 0; }', 'correct' => false],
                ],
            ],
            // SCOPE
            [
                'question' => 'What is lexical scope in JavaScript?',
                'explanation' => 'Lexical scope means the scope of a variable is determined by where it is written in the source code, not where it is called from at runtime.',
                'options' => [
                    ['text' => 'Scope determined by where a function is defined in the source code', 'correct' => true],
                    ['text' => 'Scope determined by where a function is called at runtime', 'correct' => false],
                    ['text' => 'Scope limited to a single code block between curly braces', 'correct' => false],
                    ['text' => 'Scope that is shared across all modules in an application', 'correct' => false],
                ],
            ],
            [
                'question' => "What does the following output?\n\nlet x = 'global';\nfunction outer() {\n  let x = 'outer';\n  function inner() { console.log(x); }\n  inner();\n}\nouter();",
                'explanation' => 'Due to lexical scoping, `inner` looks up `x` in its enclosing scope, which is `outer`. It finds `x = \'outer\'` before reaching global scope.',
                'options' => [
                    ['text' => "'outer'", 'correct' => true],
                    ['text' => "'global'", 'correct' => false],
                    ['text' => 'undefined', 'correct' => false],
                    ['text' => 'ReferenceError', 'correct' => false],
                ],
            ],
            // PROTOTYPE CHAIN
            [
                'question' => 'What is the prototype chain in JavaScript?',
                'explanation' => 'When a property is not found on an object, JavaScript looks at that object\'s [[Prototype]], then that prototype\'s [[Prototype]], and so on until it reaches null.',
                'options' => [
                    ['text' => 'A mechanism where objects inherit properties and methods through a chain of prototype objects', 'correct' => true],
                    ['text' => 'A list of all functions defined in a module', 'correct' => false],
                    ['text' => 'A security chain that controls access to object properties', 'correct' => false],
                    ['text' => 'An ordered sequence of constructor calls during class instantiation', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does Object.create(proto) do?',
                'explanation' => 'Object.create(proto) creates a new empty object whose [[Prototype]] is set to `proto`. This is the fundamental way to set up prototypal inheritance without using constructor functions or classes.',
                'options' => [
                    ['text' => 'Creates a new object with its [[Prototype]] set to proto', 'correct' => true],
                    ['text' => 'Creates a deep clone of the proto object', 'correct' => false],
                    ['text' => 'Copies all enumerable properties from proto to a new object', 'correct' => false],
                    ['text' => 'Creates a new class that extends proto', 'correct' => false],
                ],
            ],
            [
                'question' => 'How do you add a method to all instances of a constructor function?',
                'explanation' => 'Adding a method to `Constructor.prototype` makes it available to all instances via the prototype chain without copying it onto each object.',
                'options' => [
                    ['text' => 'Constructor.prototype.methodName = function() {}', 'correct' => true],
                    ['text' => 'Constructor.methodName = function() {}', 'correct' => false],
                    ['text' => 'Object.addMethod(Constructor, "methodName", function() {})', 'correct' => false],
                    ['text' => 'Constructor["methodName"] = function() {}', 'correct' => false],
                ],
            ],
            [
                'question' => "What will `console.log([] instanceof Array)` output?",
                'explanation' => '`instanceof` checks the prototype chain. `[]` is an Array instance, so `[] instanceof Array` is `true`.',
                'options' => [
                    ['text' => 'true', 'correct' => true],
                    ['text' => 'false', 'correct' => false],
                    ['text' => 'TypeError', 'correct' => false],
                    ['text' => 'undefined', 'correct' => false],
                ],
            ],
            // THIS BINDING
            [
                'question' => 'What does `this` refer to inside a regular function in non-strict mode when called as a standalone function?',
                'explanation' => 'In non-strict mode, when a regular function is called without an explicit receiver, `this` defaults to the global object (`window` in browsers, `global` in Node.js).',
                'options' => [
                    ['text' => 'The global object (window in browsers)', 'correct' => true],
                    ['text' => 'undefined', 'correct' => false],
                    ['text' => 'The function itself', 'correct' => false],
                    ['text' => 'null', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does Function.prototype.bind() return?',
                'explanation' => '`bind()` creates and returns a new function with `this` permanently set to the provided value. Unlike `call()` and `apply()`, it does not invoke the function immediately.',
                'options' => [
                    ['text' => 'A new function with `this` permanently set to the provided value', 'correct' => true],
                    ['text' => 'The result of calling the function with the provided `this`', 'correct' => false],
                    ['text' => 'A copy of the function with no `this` context', 'correct' => false],
                    ['text' => 'An array of the function\'s arguments', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is the key difference between call() and apply() in terms of arguments?',
                'explanation' => '`call()` accepts arguments individually (comma-separated), while `apply()` accepts arguments as an array. Both invoke the function immediately with the specified `this`.',
                'options' => [
                    ['text' => 'call() takes arguments individually; apply() takes arguments as an array', 'correct' => true],
                    ['text' => 'call() takes arguments as an array; apply() takes arguments individually', 'correct' => false],
                    ['text' => 'call() binds this permanently; apply() binds it temporarily', 'correct' => false],
                    ['text' => 'There is no difference between call() and apply()', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does `this` refer to inside an arrow function?',
                'explanation' => 'Arrow functions do not have their own `this`. They inherit `this` from the enclosing lexical scope at the time they are defined, not when they are called.',
                'options' => [
                    ['text' => 'The `this` value of the enclosing lexical scope where the arrow function was defined', 'correct' => true],
                    ['text' => 'The global object', 'correct' => false],
                    ['text' => 'The object the arrow function is called on', 'correct' => false],
                    ['text' => 'undefined in all cases', 'correct' => false],
                ],
            ],
            [
                'question' => "What will the following output?\n\nconst obj = {\n  name: 'Alice',\n  greet: function() {\n    const inner = () => console.log(this.name);\n    inner();\n  }\n};\nobj.greet();",
                'explanation' => 'The arrow function `inner` captures `this` from `greet`\'s execution context, where `this` is `obj`. So `this.name` is \'Alice\'.',
                'options' => [
                    ['text' => "'Alice'", 'correct' => true],
                    ['text' => 'undefined', 'correct' => false],
                    ['text' => 'TypeError', 'correct' => false],
                    ['text' => 'null', 'correct' => false],
                ],
            ],
            // EVENT LOOP
            [
                'question' => 'What is the JavaScript event loop responsible for?',
                'explanation' => 'The event loop continuously checks the call stack and the task/callback queues. When the call stack is empty, it moves the next callback from the queue to the stack for execution.',
                'options' => [
                    ['text' => 'Monitoring the call stack and moving callbacks from the task queue to the stack when the stack is empty', 'correct' => true],
                    ['text' => 'Managing memory allocation and garbage collection', 'correct' => false],
                    ['text' => 'Parsing and compiling JavaScript source code', 'correct' => false],
                    ['text' => 'Handling network requests directly on the main thread', 'correct' => false],
                ],
            ],
            [
                'question' => "In what order will the following log?\n\nconsole.log('A');\nsetTimeout(() => console.log('B'), 0);\nPromise.resolve().then(() => console.log('C'));\nconsole.log('D');",
                'explanation' => 'Synchronous code runs first (A, D). Then microtasks (Promises) run before macrotasks (setTimeout). Order: A → D → C → B.',
                'options' => [
                    ['text' => 'A, D, C, B', 'correct' => true],
                    ['text' => 'A, B, C, D', 'correct' => false],
                    ['text' => 'A, C, D, B', 'correct' => false],
                    ['text' => 'A, D, B, C', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is the difference between the microtask queue and the macrotask queue?',
                'explanation' => 'Microtasks (Promises, queueMicrotask) have higher priority. After each macrotask, ALL pending microtasks drain before the next macrotask runs. setTimeout/setInterval go into the macrotask queue.',
                'options' => [
                    ['text' => 'Microtasks (Promises) are processed before macrotasks (setTimeout) — all microtasks drain after each macrotask', 'correct' => true],
                    ['text' => 'Macrotasks are processed before microtasks', 'correct' => false],
                    ['text' => 'Both queues have the same priority and process in order of registration', 'correct' => false],
                    ['text' => 'Microtasks are only processed at the very end of the event loop cycle', 'correct' => false],
                ],
            ],
            // PROMISES
            [
                'question' => 'What are the three states a Promise can be in?',
                'explanation' => 'A Promise is always in one of three states: pending (initial, operation not complete), fulfilled (completed successfully), or rejected (failed).',
                'options' => [
                    ['text' => 'pending, fulfilled, rejected', 'correct' => true],
                    ['text' => 'waiting, resolved, failed', 'correct' => false],
                    ['text' => 'open, closed, error', 'correct' => false],
                    ['text' => 'loading, success, error', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is the difference between Promise.all() and Promise.race()?',
                'explanation' => 'Promise.all() waits for ALL promises to resolve (rejects immediately if any reject). Promise.race() settles as soon as the FIRST promise settles, whether fulfilled or rejected.',
                'options' => [
                    ['text' => 'Promise.all() resolves when all promises resolve; Promise.race() resolves/rejects as soon as the first settles', 'correct' => true],
                    ['text' => 'Promise.all() rejects if any promise rejects; Promise.race() waits for all to complete', 'correct' => false],
                    ['text' => 'Promise.race() is always faster than Promise.all()', 'correct' => false],
                    ['text' => 'Promise.all() and Promise.race() behave identically', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does .catch(fn) on a Promise do?',
                'explanation' => '`.catch(fn)` is shorthand for `.then(undefined, fn)`. It handles a rejected Promise and returns a new Promise, allowing the chain to continue.',
                'options' => [
                    ['text' => 'Handles a rejected Promise and returns a new Promise', 'correct' => true],
                    ['text' => 'Stops the Promise chain from continuing', 'correct' => false],
                    ['text' => 'Converts a rejected Promise to a resolved one unconditionally', 'correct' => false],
                    ['text' => 'Only runs if the previous .then() threw synchronously', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does Promise.allSettled() do differently from Promise.all()?',
                'explanation' => 'Promise.allSettled() waits for all promises to settle (fulfill OR reject) and returns an array with status and value/reason for each. It never short-circuits on rejection.',
                'options' => [
                    ['text' => 'Waits for all promises to settle regardless of rejection and returns all results', 'correct' => true],
                    ['text' => 'Resolves with the first promise that settles', 'correct' => false],
                    ['text' => 'Behaves identically to Promise.all()', 'correct' => false],
                    ['text' => 'Rejects immediately if any promise rejects', 'correct' => false],
                ],
            ],
            // ASYNC / AWAIT
            [
                'question' => 'What does an async function always return?',
                'explanation' => 'An async function always returns a Promise. Non-Promise return values are automatically wrapped in a resolved Promise. If it throws, it returns a rejected Promise.',
                'options' => [
                    ['text' => 'A Promise', 'correct' => true],
                    ['text' => 'The awaited value directly', 'correct' => false],
                    ['text' => 'undefined', 'correct' => false],
                    ['text' => 'A generator object', 'correct' => false],
                ],
            ],
            [
                'question' => 'What happens if you use `await` without a try/catch block and the awaited Promise rejects?',
                'explanation' => 'An unhandled Promise rejection occurs and the async function itself rejects, potentially crashing the process in Node.js if unhandled.',
                'options' => [
                    ['text' => 'An unhandled Promise rejection occurs and the async function rejects', 'correct' => true],
                    ['text' => 'The rejected value is silently returned as undefined', 'correct' => false],
                    ['text' => 'JavaScript automatically retries the operation', 'correct' => false],
                    ['text' => 'Code execution stops permanently and cannot continue', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is the most efficient way to run two independent async operations in parallel using async/await?',
                'explanation' => 'Using `await Promise.all([fetchA(), fetchB()])` starts both concurrently. Awaiting sequentially (`await fetchA(); await fetchB()`) runs them one after the other, doubling wait time.',
                'options' => [
                    ['text' => 'const [a, b] = await Promise.all([fetchA(), fetchB()])', 'correct' => true],
                    ['text' => 'const a = await fetchA(); const b = await fetchB()', 'correct' => false],
                    ['text' => 'await fetchA() + await fetchB()', 'correct' => false],
                    ['text' => 'async () => fetchA() && fetchB()', 'correct' => false],
                ],
            ],
            // IIFE
            [
                'question' => 'What is an IIFE (Immediately Invoked Function Expression)?',
                'explanation' => 'An IIFE is a function expression that is defined and immediately called at the point of definition. It creates its own scope, commonly used to avoid polluting the global scope.',
                'options' => [
                    ['text' => 'A function expression that is invoked immediately after being defined', 'correct' => true],
                    ['text' => 'A function that is called automatically when an event fires', 'correct' => false],
                    ['text' => 'A built-in JavaScript function for async operations', 'correct' => false],
                    ['text' => 'A function that can only be called once and is then garbage collected', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is the correct syntax for an IIFE?',
                'explanation' => 'The function must be wrapped in parentheses to be treated as an expression (not a declaration), then immediately invoked with `()` at the end.',
                'options' => [
                    ['text' => '(function() { /* code */ })()', 'correct' => true],
                    ['text' => 'function() { /* code */ }()', 'correct' => false],
                    ['text' => 'invoke(function() { /* code */ })', 'correct' => false],
                    ['text' => 'new function() { /* code */ }', 'correct' => false],
                ],
            ],
            // CURRYING / HOF
            [
                'question' => 'What is currying in JavaScript?',
                'explanation' => 'Currying transforms a function that takes multiple arguments into a sequence of functions each taking a single argument. `f(a, b, c)` becomes `f(a)(b)(c)`.',
                'options' => [
                    ['text' => 'Transforming a multi-argument function into a sequence of single-argument functions', 'correct' => true],
                    ['text' => 'Wrapping a function to handle errors automatically', 'correct' => false],
                    ['text' => 'Binding a function permanently to a specific this context', 'correct' => false],
                    ['text' => 'Memoizing function results for performance', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does a higher-order function do?',
                'explanation' => 'A higher-order function either takes one or more functions as arguments (like map, filter, reduce) or returns a function as its result (like bind, a currying factory, or a decorator).',
                'options' => [
                    ['text' => 'Takes a function as an argument or returns a function as a result', 'correct' => true],
                    ['text' => 'Runs at a higher priority than regular functions', 'correct' => false],
                    ['text' => 'Can only be defined at the top level of a module', 'correct' => false],
                    ['text' => 'Is a function defined inside a class method', 'correct' => false],
                ],
            ],
            // MAP / SET
            [
                'question' => 'What is a key difference between a JavaScript Map and a plain Object?',
                'explanation' => 'Map keys can be of any type (objects, functions, primitives), while Object keys are always strings or Symbols. Maps also maintain insertion order and have a built-in `size` property.',
                'options' => [
                    ['text' => 'Map keys can be any type; Object keys are always strings or Symbols', 'correct' => true],
                    ['text' => 'Map is faster than Object for all operations', 'correct' => false],
                    ['text' => 'Object supports more methods than Map', 'correct' => false],
                    ['text' => 'Map can only hold primitive values', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is the primary use case for a JavaScript Set?',
                'explanation' => 'A Set stores unique values of any type. Adding a duplicate has no effect. It is ideal for deduplication and membership checks.',
                'options' => [
                    ['text' => 'Storing a collection of unique values with no duplicates', 'correct' => true],
                    ['text' => 'Storing key-value pairs where keys are always strings', 'correct' => false],
                    ['text' => 'Providing a sorted collection of values', 'correct' => false],
                    ['text' => 'Creating immutable collections', 'correct' => false],
                ],
            ],
            [
                'question' => 'How do you remove duplicates from an array using a Set?',
                'explanation' => 'Converting an array to a Set removes duplicates, then spreading back gives a deduplicated array. `[...new Set(arr)]` is the idiomatic one-liner.',
                'options' => [
                    ['text' => '[...new Set(array)]', 'correct' => true],
                    ['text' => 'new Set(array).toArray()', 'correct' => false],
                    ['text' => 'Array.from(array).unique()', 'correct' => false],
                    ['text' => 'array.dedupe() is the correct built-in method', 'correct' => false],
                ],
            ],
            // OPTIONAL CHAINING / NULLISH COALESCING
            [
                'question' => 'What does optional chaining (?.) do when the left side is null or undefined?',
                'explanation' => 'The optional chaining operator `?.` short-circuits and returns `undefined` instead of throwing a TypeError.',
                'options' => [
                    ['text' => 'Returns undefined instead of throwing a TypeError', 'correct' => true],
                    ['text' => 'Returns null', 'correct' => false],
                    ['text' => 'Returns false', 'correct' => false],
                    ['text' => 'Throws a custom OptionalChainError', 'correct' => false],
                ],
            ],
            [
                'question' => "What is the output of: const user = null; console.log(user?.profile?.name ?? 'Guest');",
                'explanation' => '`user?.profile` short-circuits to `undefined` since `user` is null. Then `undefined ?? \'Guest\'` evaluates to \'Guest\' because `??` returns the right side when the left is null or undefined.',
                'options' => [
                    ['text' => "'Guest'", 'correct' => true],
                    ['text' => 'null', 'correct' => false],
                    ['text' => 'undefined', 'correct' => false],
                    ['text' => 'TypeError', 'correct' => false],
                ],
            ],
            // DESTRUCTURING / REST
            [
                'question' => "What does the following code do?\n\nconst { a, ...rest } = { a: 1, b: 2, c: 3 };",
                'explanation' => 'Object rest destructuring extracts `a` into its own variable and collects remaining properties (`b` and `c`) into a new object called `rest`.',
                'options' => [
                    ['text' => 'Extracts `a` and collects remaining properties into rest as { b: 2, c: 3 }', 'correct' => true],
                    ['text' => 'Copies all properties into `a` and makes `rest` an empty object', 'correct' => false],
                    ['text' => 'Creates a spread of the object into an array', 'correct' => false],
                    ['text' => 'Throws a SyntaxError because rest must be the first parameter', 'correct' => false],
                ],
            ],
            // WEAKMAP
            [
                'question' => 'What is the key difference between WeakMap and Map in JavaScript?',
                'explanation' => 'WeakMap keys must be objects and are held weakly — if there is no other reference to the key object, it can be garbage collected. WeakMap is not iterable and has no `size` property.',
                'options' => [
                    ['text' => 'WeakMap keys are held weakly (allowing GC) and must be objects; Map holds strong references', 'correct' => true],
                    ['text' => 'WeakMap stores primitive values; Map stores objects', 'correct' => false],
                    ['text' => 'WeakMap is faster than Map for all operations', 'correct' => false],
                    ['text' => 'WeakMap has a size property; Map does not', 'correct' => false],
                ],
            ],
            // ES6 MODULES
            [
                'question' => 'What is the difference between a named export and a default export in ES modules?',
                'explanation' => 'Named exports are imported with curly braces and their exact name. A module can have many. Default exports are imported without braces and can be given any name. A module can have only one default export.',
                'options' => [
                    ['text' => 'Named exports are imported with curly braces and their exact name; default exports are imported without braces and can be named freely', 'correct' => true],
                    ['text' => 'Named exports cannot be tree-shaken; default exports always are', 'correct' => false],
                    ['text' => 'A module can have many default exports but only one named export', 'correct' => false],
                    ['text' => 'There is no functional difference; it is only a syntax preference', 'correct' => false],
                ],
            ],
            // ARRAY METHODS
            [
                'question' => 'What does Array.prototype.flatMap() do?',
                'explanation' => '`flatMap()` maps each element with a function, then flattens the result one level deep. It is equivalent to `.map(...).flat(1)`.',
                'options' => [
                    ['text' => 'Maps each element with a function, then flattens the result one level deep', 'correct' => true],
                    ['text' => 'Flattens a nested array to any depth and then maps it', 'correct' => false],
                    ['text' => 'Is the same as map() but for flat (non-nested) arrays only', 'correct' => false],
                    ['text' => 'Creates a flat copy of the array without any transformation', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is the difference between Array.prototype.every() and Array.prototype.some()?',
                'explanation' => '`every()` returns true only if ALL elements satisfy the predicate (short-circuits on first failure). `some()` returns true if AT LEAST ONE element satisfies it (short-circuits on first match).',
                'options' => [
                    ['text' => 'every() returns true if all elements pass; some() returns true if at least one passes', 'correct' => true],
                    ['text' => 'every() returns true if at least one passes; some() requires all to pass', 'correct' => false],
                    ['text' => 'Both return a new filtered array', 'correct' => false],
                    ['text' => 'every() and some() are identical in behavior', 'correct' => false],
                ],
            ],
            // ERROR HANDLING
            [
                'question' => 'What does the `finally` block in a try/catch/finally statement do?',
                'explanation' => 'The `finally` block always executes after try and catch, regardless of whether an error was thrown or caught. It is used for cleanup operations.',
                'options' => [
                    ['text' => 'Executes after try/catch regardless of whether an error occurred', 'correct' => true],
                    ['text' => 'Only executes if no error was thrown', 'correct' => false],
                    ['text' => 'Only executes if an error was caught', 'correct' => false],
                    ['text' => 'Executes before the try block as a setup step', 'correct' => false],
                ],
            ],
        ];
    }
}
