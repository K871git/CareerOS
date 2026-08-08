<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;

class JsAdvancedQuestionsSeeder extends Seeder
{
    public function run(): void
    {
        $track = LearningTrack::firstOrCreate(
            ['slug' => 'full-stack-web-development'],
            ['title' => 'Full Stack Web Development', 'description' => 'Full stack web development track.', 'display_order' => 1]
        );

        $subject = Subject::firstOrCreate(
            ['slug' => 'javascript'],
            ['learning_track_id' => $track->id, 'title' => 'JavaScript', 'description' => 'JavaScript practice questions.', 'display_order' => 2]
        );

        $topic = Topic::firstOrCreate(
            ['slug' => 'js-advanced'],
            ['subject_id' => $subject->id, 'title' => 'JavaScript Advanced', 'description' => 'Advanced JavaScript: design patterns, performance, memory, module systems, WeakMap/WeakRef, and generators.', 'display_order' => 3]
        );

        foreach ($this->questions() as $qData) {
            $exists = Question::where('topic_id', $topic->id)
                ->where('question', $qData['question'])
                ->exists();
            if ($exists) continue;

            $q = Question::create([
                'topic_id'    => $topic->id,
                'type'        => 'MCQ',
                'difficulty'  => 'Hard',
                'question'    => $qData['question'],
                'explanation' => $qData['explanation'],
            ]);

            foreach ($qData['options'] as $opt) {
                QuestionOption::create([
                    'question_id' => $q->id,
                    'option_text' => $opt['text'],
                    'is_correct'  => $opt['correct'],
                ]);
            }
        }

        $count = Question::where('topic_id', $topic->id)->count();
        $this->command->info("JS Advanced: {$count} questions total.");
    }

    private function questions(): array
    {
        return [
            // DESIGN PATTERNS
            [
                'question' => 'What problem does the Singleton design pattern solve?',
                'explanation' => 'The Singleton pattern ensures a class has exactly one instance and provides a global access point to it. Common uses include shared configuration objects, database connection pools, or a logging service.',
                'options' => [
                    ['text' => 'Ensuring a class has only one instance with a global access point', 'correct' => true],
                    ['text' => 'Creating multiple similar objects from a template', 'correct' => false],
                    ['text' => 'Separating object construction from its representation', 'correct' => false],
                    ['text' => 'Providing a simplified interface to a complex subsystem', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is the Observer pattern and where is it commonly used in JavaScript?',
                'explanation' => 'The Observer pattern defines a one-to-many dependency: when the subject changes state, all its observers are notified. It is the foundation of DOM addEventListener, RxJS Observables, and Node.js EventEmitter.',
                'options' => [
                    ['text' => 'A subject notifies multiple observers of state changes — used in DOM events, RxJS, EventEmitter', 'correct' => true],
                    ['text' => 'An object that proxies method calls to another object', 'correct' => false],
                    ['text' => 'A pattern where one function decorates another with extra behavior', 'correct' => false],
                    ['text' => 'A structural pattern for composing objects into tree hierarchies', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does the Factory pattern do in JavaScript?',
                'explanation' => 'A Factory creates and returns objects without exposing creation logic or requiring `new`. It abstracts instantiation and is useful when the exact type of object to create may vary.',
                'options' => [
                    ['text' => 'Creates objects without specifying the exact class, abstracting instantiation logic', 'correct' => true],
                    ['text' => 'Ensures all created objects share the same prototype', 'correct' => false],
                    ['text' => 'Restricts object creation to a single instance', 'correct' => false],
                    ['text' => 'Copies an existing object to create new instances', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is the Module pattern and what problem does it solve?',
                'explanation' => 'The Module pattern (IIFE or ES modules) encapsulates code with public and private members. It solves global namespace pollution and provides data privacy via closures.',
                'options' => [
                    ['text' => 'Encapsulates code with public/private members, solving global namespace pollution', 'correct' => true],
                    ['text' => 'Allows one module to extend another class using inheritance', 'correct' => false],
                    ['text' => 'Provides a way to lazy-load code only when needed', 'correct' => false],
                    ['text' => 'Converts CommonJS modules to ES module format automatically', 'correct' => false],
                ],
            ],
            [
                'question' => 'What problem does the Decorator pattern solve?',
                'explanation' => 'The Decorator pattern adds behavior to individual objects dynamically without affecting other objects and without using inheritance. In JavaScript it is achieved by wrapping functions or classes.',
                'options' => [
                    ['text' => 'Adding behavior to an object dynamically without changing its class or using inheritance', 'correct' => true],
                    ['text' => 'Restricting direct access to an object\'s methods', 'correct' => false],
                    ['text' => 'Converting one interface into another interface', 'correct' => false],
                    ['text' => 'Composing multiple classes into a single object', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is the purpose of the Mediator pattern?',
                'explanation' => 'The Mediator defines an object that coordinates communication between components, reducing direct dependencies. Components talk through the mediator, not directly with each other.',
                'options' => [
                    ['text' => 'An object that coordinates communication between components, reducing direct dependencies', 'correct' => true],
                    ['text' => 'A wrapper that translates one interface to another', 'correct' => false],
                    ['text' => 'A pattern for creating families of related objects', 'correct' => false],
                    ['text' => 'A pattern for saving and restoring object state', 'correct' => false],
                ],
            ],
            // GENERATORS
            [
                'question' => 'What does the `yield` keyword do inside a generator function?',
                'explanation' => '`yield` pauses the generator\'s execution and returns a value to the caller. Execution resumes from where it paused on the next `.next()` call.',
                'options' => [
                    ['text' => 'Pauses execution and returns a value; resumes on the next .next() call', 'correct' => true],
                    ['text' => 'Terminates the generator permanently and returns a final value', 'correct' => false],
                    ['text' => 'Throws an error from within the generator', 'correct' => false],
                    ['text' => 'Is an alias for return inside a generator', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does calling a generator function return?',
                'explanation' => 'Calling a generator function does NOT execute its body. It returns a Generator (iterator) object. The body executes lazily when you call `.next()` on it.',
                'options' => [
                    ['text' => 'A Generator (iterator) object — the body does not execute until .next() is called', 'correct' => true],
                    ['text' => 'The first yielded value directly', 'correct' => false],
                    ['text' => 'A Promise that resolves to the first yielded value', 'correct' => false],
                    ['text' => 'undefined, because generators are statements not expressions', 'correct' => false],
                ],
            ],
            [
                'question' => 'Why are generators suitable for representing infinite sequences?',
                'explanation' => 'Generators produce values lazily on demand via `yield`. The sequence only progresses when `.next()` is called, so there is no memory issue from storing all values.',
                'options' => [
                    ['text' => 'Values are produced lazily on demand without storing all values in memory', 'correct' => true],
                    ['text' => 'They run asynchronously in the background without blocking the main thread', 'correct' => false],
                    ['text' => 'They keep a Promise pending indefinitely', 'correct' => false],
                    ['text' => 'They use a special memory pool that grows dynamically', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is the Symbol.iterator protocol?',
                'explanation' => 'An object is iterable if it has a `[Symbol.iterator]()` method that returns an iterator (an object with `.next()` returning `{ value, done }`). Arrays, Maps, Sets, and Strings are built-in iterables.',
                'options' => [
                    ['text' => 'An interface where an object exposes [Symbol.iterator]() returning an iterator with a .next() method', 'correct' => true],
                    ['text' => 'A way to define custom sorting logic for objects', 'correct' => false],
                    ['text' => 'A method that converts an object to a string representation', 'correct' => false],
                    ['text' => 'A Symbol used to mark private object properties', 'correct' => false],
                ],
            ],
            // PROXY / REFLECT
            [
                'question' => 'What does JavaScript\'s Proxy object allow you to do?',
                'explanation' => 'A Proxy wraps an object and intercepts fundamental operations (get, set, has, apply, etc.) via "trap" functions in a handler. Used for validation, logging, and reactive data systems.',
                'options' => [
                    ['text' => 'Intercept and redefine fundamental operations on an object using trap functions', 'correct' => true],
                    ['text' => 'Create a deep frozen (immutable) copy of an object', 'correct' => false],
                    ['text' => 'Serialize an object to a JSON-compatible format automatically', 'correct' => false],
                    ['text' => 'Create a lazy-loaded version of an object to improve startup performance', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is the Reflect API and how does it relate to Proxy?',
                'explanation' => 'The `Reflect` API provides static methods that mirror Proxy traps (Reflect.get, Reflect.set, etc.). Inside a Proxy trap you typically call the corresponding Reflect method to forward the operation to the original target with correct default behavior.',
                'options' => [
                    ['text' => 'A built-in API providing methods corresponding to Proxy traps for forwarding operations to targets', 'correct' => true],
                    ['text' => 'A class for reflecting on type metadata of TypeScript objects at runtime', 'correct' => false],
                    ['text' => 'A debugging tool that reflects the call stack', 'correct' => false],
                    ['text' => 'An async utility for mirroring data between two objects', 'correct' => false],
                ],
            ],
            // MEMORY / PERFORMANCE
            [
                'question' => 'What is a common cause of memory leaks in JavaScript applications?',
                'explanation' => 'Common causes: closures retaining references to large data, detached DOM nodes still referenced in JS, forgotten event listeners, and setInterval callbacks never cleared.',
                'options' => [
                    ['text' => 'Closures retaining references to large objects, detached DOM nodes, or uncleaned event listeners/timers', 'correct' => true],
                    ['text' => 'Using const instead of let for object declarations', 'correct' => false],
                    ['text' => 'Synchronous code blocking the event loop', 'correct' => false],
                    ['text' => 'Using arrow functions instead of regular functions', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is memoization and when should you use it?',
                'explanation' => 'Memoization caches a function\'s return value by its input. On repeated calls with the same arguments, the cached result is returned. Use it for pure functions with expensive computations called repeatedly with the same inputs.',
                'options' => [
                    ['text' => 'Caching function results by input so repeated calls return instantly without recalculation', 'correct' => true],
                    ['text' => 'Storing function definitions in memory for faster lookup', 'correct' => false],
                    ['text' => 'Persisting data to localStorage automatically after each function call', 'correct' => false],
                    ['text' => 'Compiling functions to bytecode for faster execution', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is the difference between debounce and throttle?',
                'explanation' => 'Debounce delays execution until after a quiet period (fires only after the user stops triggering for Xms). Throttle limits execution to at most once per time window. Debounce is "wait until done"; throttle is "rate limit".',
                'options' => [
                    ['text' => 'Debounce fires after a quiet period; throttle fires at most once per time window', 'correct' => true],
                    ['text' => 'Throttle fires after a quiet period; debounce fires at most once per time window', 'correct' => false],
                    ['text' => 'Both are identical but debounce only works with async functions', 'correct' => false],
                    ['text' => 'Debounce cancels a function call; throttle queues function calls', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is WeakRef in JavaScript and when would you use it?',
                'explanation' => 'A `WeakRef` holds a weak reference to an object that does not prevent garbage collection. Always check `.deref()` before use — it may return `undefined` if the object was GC\'d. Used for caches that should not prevent GC.',
                'options' => [
                    ['text' => 'A weak reference that does not prevent GC; deref() returns undefined if the object was collected', 'correct' => true],
                    ['text' => 'A reference to a read-only variable that cannot be reassigned', 'correct' => false],
                    ['text' => 'A Proxy that automatically releases memory after a timeout', 'correct' => false],
                    ['text' => 'A Symbol for marking deprecated object properties', 'correct' => false],
                ],
            ],
            // MODULE SYSTEMS
            [
                'question' => 'What is the key difference between ES Modules (ESM) and CommonJS (CJS)?',
                'explanation' => 'ESM uses `import`/`export`, is statically analyzed (enabling tree-shaking), and loads asynchronously. CJS uses `require`/`module.exports`, resolves dynamically at runtime, and is synchronous.',
                'options' => [
                    ['text' => 'ESM uses static import/export (tree-shakeable, async); CJS uses dynamic require (synchronous, runtime resolution)', 'correct' => true],
                    ['text' => 'CJS is faster than ESM because it avoids Promise overhead', 'correct' => false],
                    ['text' => 'ESM can only be used in browsers; CJS is for Node.js only', 'correct' => false],
                    ['text' => 'Both are identical — CJS is just older syntax for the same system', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is dynamic import() and when would you use it?',
                'explanation' => 'Dynamic `import()` loads a module lazily at runtime and returns a Promise. Use it for code splitting (load a module only when needed), conditional loading, or loading based on user interaction.',
                'options' => [
                    ['text' => 'Lazily loads a module at runtime and returns a Promise — used for code splitting and conditional loading', 'correct' => true],
                    ['text' => 'Imports all exports from a module into the current scope simultaneously', 'correct' => false],
                    ['text' => 'Reloads a module that was previously imported', 'correct' => false],
                    ['text' => 'Imports a CJS module into an ESM file', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is tree shaking in the context of JavaScript bundlers?',
                'explanation' => 'Tree shaking is dead code elimination via static analysis of ES module imports/exports. Bundlers (Webpack, Rollup, Vite) detect unused exports and exclude them from the final bundle.',
                'options' => [
                    ['text' => 'Removing unused exports from the final bundle using static analysis of ES module imports', 'correct' => true],
                    ['text' => 'Recursively removing node_modules not listed in package.json', 'correct' => false],
                    ['text' => 'Splitting one large bundle into multiple smaller chunks', 'correct' => false],
                    ['text' => 'Minifying code by removing whitespace and shortening variable names', 'correct' => false],
                ],
            ],
            // ADVANCED ASYNC
            [
                'question' => 'What does Promise.any() do?',
                'explanation' => 'Promise.any() resolves with the value of the FIRST promise that fulfills. It only rejects (with AggregateError) if ALL promises reject. Unlike Promise.race(), it ignores rejections unless all fail.',
                'options' => [
                    ['text' => 'Resolves with the first Promise that fulfills; only rejects if ALL promises reject', 'correct' => true],
                    ['text' => 'Resolves or rejects with the first Promise that settles regardless of status', 'correct' => false],
                    ['text' => 'Resolves only when all promises fulfill, like Promise.all()', 'correct' => false],
                    ['text' => 'Returns all fulfilled values while ignoring any rejected promises', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is an async generator function and what does it produce?',
                'explanation' => 'An async generator (declared with `async function*`) combines generators and async/await. It produces an async iterator consumed with `for await...of`. Each `yield` produces a value asynchronously.',
                'options' => [
                    ['text' => 'A function that yields Promises, producing an async iterator consumed with for-await-of', 'correct' => true],
                    ['text' => 'A function that runs multiple generators concurrently', 'correct' => false],
                    ['text' => 'An async function that returns a synchronous iterator', 'correct' => false],
                    ['text' => 'A generator that converts callbacks to Promises automatically', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does AbortController allow you to do?',
                'explanation' => '`AbortController` provides an `AbortSignal` passed to APIs like `fetch`. Calling `controller.abort()` cancels the in-flight request, preventing state updates on unmounted components or race conditions.',
                'options' => [
                    ['text' => 'Cancel in-progress fetch requests or other async operations that support AbortSignal', 'correct' => true],
                    ['text' => 'Stop all running Promises immediately regardless of their state', 'correct' => false],
                    ['text' => 'Limit the number of concurrent network requests', 'correct' => false],
                    ['text' => 'Retry a failed fetch request automatically with exponential backoff', 'correct' => false],
                ],
            ],
            // ADVANCED ERROR HANDLING
            [
                'question' => 'How do you create a custom Error class in JavaScript?',
                'explanation' => 'Extend the built-in `Error` class, call `super(message)` in the constructor to set the message, and set `this.name` to distinguish the error type in catch blocks.',
                'options' => [
                    ['text' => 'class CustomError extends Error { constructor(msg) { super(msg); this.name = "CustomError"; } }', 'correct' => true],
                    ['text' => 'const CustomError = new Error("Custom"); CustomError.type = "custom";', 'correct' => false],
                    ['text' => 'function CustomError(msg) { Error.call(this, msg); } CustomError.prototype = Error;', 'correct' => false],
                    ['text' => 'class CustomError { constructor(msg) { this.message = msg; this.stack = Error().stack; } }', 'correct' => false],
                ],
            ],
            // ADVANCED FUNCTIONAL
            [
                'question' => 'What is function composition in JavaScript?',
                'explanation' => 'Function composition combines functions so the output of one becomes the input of the next. `compose(f, g)(x)` is equivalent to `f(g(x))`. Libraries like Ramda provide compose/pipe utilities.',
                'options' => [
                    ['text' => 'Combining functions so the output of one is the input of the next: compose(f, g)(x) === f(g(x))', 'correct' => true],
                    ['text' => 'Calling multiple functions simultaneously in parallel', 'correct' => false],
                    ['text' => 'Merging two function bodies into one function', 'correct' => false],
                    ['text' => 'Nesting function declarations inside each other to share scope', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is a pure function?',
                'explanation' => 'A pure function always produces the same output for the same inputs and has no side effects. Pure functions are predictable, testable, and safe to memoize.',
                'options' => [
                    ['text' => 'A function that always returns the same output for the same inputs and has no side effects', 'correct' => true],
                    ['text' => 'A function defined with the `pure` keyword in strict mode', 'correct' => false],
                    ['text' => 'A function that does not use any variables from its outer scope', 'correct' => false],
                    ['text' => 'A function that only operates on primitive values, not objects', 'correct' => false],
                ],
            ],
            // JS INTERNALS
            [
                'question' => 'What is the Temporal Dead Zone (TDZ) in JavaScript?',
                'explanation' => 'The TDZ is the period between entering a block scope where a `let` or `const` variable is declared and the actual declaration line being executed. Accessing the variable during this period throws a ReferenceError.',
                'options' => [
                    ['text' => 'The period between block entry and let/const initialization where accessing the variable throws ReferenceError', 'correct' => true],
                    ['text' => 'A zone in memory where deleted objects are stored before garbage collection', 'correct' => false],
                    ['text' => 'The time between a setTimeout call and when its callback executes', 'correct' => false],
                    ['text' => 'A deprecated browser API that restricted DOM access during page load', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does Object.freeze() do, and what is its key limitation?',
                'explanation' => '`Object.freeze()` makes an object\'s own properties non-writable and non-configurable, and prevents adding/removing properties. However, it is SHALLOW — nested objects inside are still mutable.',
                'options' => [
                    ['text' => 'Prevents modification of an object\'s own properties, but does not deeply freeze nested objects', 'correct' => true],
                    ['text' => 'Recursively freezes an object and all nested objects', 'correct' => false],
                    ['text' => 'Prevents an object from being garbage collected', 'correct' => false],
                    ['text' => 'Makes all object properties private and inaccessible from outside', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is the purpose of Symbol in JavaScript?',
                'explanation' => 'Symbol creates a guaranteed-unique primitive value. Symbols are used as unique property keys that do not conflict with string keys, or as well-known hooks (Symbol.iterator, Symbol.toPrimitive) to customize built-in behaviors.',
                'options' => [
                    ['text' => 'Creates a unique, immutable primitive value for use as property keys or to customize built-in behaviors', 'correct' => true],
                    ['text' => 'Provides a way to define mathematical symbol constants', 'correct' => false],
                    ['text' => 'Creates private class properties that cannot be accessed from outside', 'correct' => false],
                    ['text' => 'Wraps a string value to give it unique identity based on its content', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does structuredClone() do and how does it differ from JSON.parse(JSON.stringify(...))?',
                'explanation' => '`structuredClone()` creates a deep clone using the Structured Clone Algorithm. Unlike JSON.parse/stringify, it handles Dates, RegExps, Maps, Sets, ArrayBuffers, and circular references correctly.',
                'options' => [
                    ['text' => 'Creates a deep clone handling Date, Map, Set, circular refs — unlike JSON.parse/stringify', 'correct' => true],
                    ['text' => 'Creates a shallow clone equivalent to Object.assign({}, obj)', 'correct' => false],
                    ['text' => 'Clones a DOM node and all its child elements', 'correct' => false],
                    ['text' => 'Creates an immutable snapshot of an object at a point in time', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is FinalizationRegistry in JavaScript?',
                'explanation' => '`FinalizationRegistry` lets you register a callback to run when a registered object is garbage collected. Typically paired with `WeakRef` for cache cleanup or releasing external resources.',
                'options' => [
                    ['text' => 'A registry that runs a callback when a registered object is garbage collected', 'correct' => true],
                    ['text' => 'A registry that tracks all finally blocks in the current execution context', 'correct' => false],
                    ['text' => 'A tool that finalizes and seals all registered objects to prevent modification', 'correct' => false],
                    ['text' => 'A global service that manages all Promise cleanup operations', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is tail call optimization (TCO)?',
                'explanation' => 'TCO reuses the current stack frame for a tail-position function call instead of adding a new one. This allows deep recursion without growing the call stack. Specified in ES6 strict mode, but support varies across engines.',
                'options' => [
                    ['text' => 'Reusing the current stack frame for a tail-position call, preventing stack overflow in deep recursion', 'correct' => true],
                    ['text' => 'Caching the results of tail-recursive functions automatically', 'correct' => false],
                    ['text' => 'Converting recursive functions to iterative ones at compile time', 'correct' => false],
                    ['text' => 'Running the last function call in a chain asynchronously', 'correct' => false],
                ],
            ],
            [
                'question' => 'At an advanced level, what is notable about == vs === with NaN and ±0?',
                'explanation' => 'Both `==` and `===` treat NaN as not equal to itself (NaN !== NaN), and both treat +0 and -0 as equal. Use `Object.is()` to correctly distinguish NaN from itself and +0 from -0.',
                'options' => [
                    ['text' => 'Both == and === return false for NaN===NaN. Use Object.is() for NaN and ±0 distinctions.', 'correct' => true],
                    ['text' => '== correctly identifies NaN while === does not', 'correct' => false],
                    ['text' => '=== throws an error when types differ; == silently converts types', 'correct' => false],
                    ['text' => 'Both are identical after JIT compilation in modern JavaScript engines', 'correct' => false],
                ],
            ],
        ];
    }
}
