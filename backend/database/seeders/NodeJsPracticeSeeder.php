<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;

class NodeJsPracticeSeeder extends Seeder
{
    public function run(): void
    {
        $track = LearningTrack::firstOrCreate(
            ['slug' => 'backend-engineering'],
            [
                'title'         => 'Backend Engineering',
                'description'   => 'Backend engineering — server-side programming, APIs, databases, and infrastructure.',
                'display_order' => 3,
            ]
        );

        $subject = Subject::firstOrCreate(
            ['slug' => 'nodejs'],
            [
                'learning_track_id' => $track->id,
                'title'             => 'Node.js',
                'description'       => 'Node.js is a JavaScript runtime built on Chrome\'s V8 engine. Master the event loop, async patterns, streams, modules, and server-side JavaScript development.',
                'display_order'     => 6,
            ]
        );

        $levels = [
            [
                'title'         => 'Node.js Basics — Junior',
                'slug'          => 'nodejs-junior',
                'description'   => 'Core Node.js concepts — event loop, modules, async callbacks, and built-in modules. For junior-level interview preparation.',
                'display_order' => 1,
                'difficulty'    => 'Easy',
                'questions'     => $this->juniorQuestions(),
            ],
            [
                'title'         => 'Node.js Intermediate',
                'slug'          => 'nodejs-intermediate',
                'description'   => 'Streams, error handling, npm, environment config, and building HTTP servers. For mid-level backend roles.',
                'display_order' => 2,
                'difficulty'    => 'Medium',
                'questions'     => $this->intermediateQuestions(),
            ],
            [
                'title'         => 'Node.js Advanced',
                'slug'          => 'nodejs-advanced',
                'description'   => 'Worker threads, clustering, performance, security, and production patterns. For senior backend developer interviews.',
                'display_order' => 3,
                'difficulty'    => 'Hard',
                'questions'     => $this->advancedQuestions(),
            ],
        ];

        foreach ($levels as $levelData) {
            $topic = Topic::firstOrCreate(
                ['slug' => $levelData['slug']],
                [
                    'subject_id'    => $subject->id,
                    'title'         => $levelData['title'],
                    'description'   => $levelData['description'],
                    'display_order' => $levelData['display_order'],
                ]
            );

            foreach ($levelData['questions'] as $qData) {
                $exists = Question::where('topic_id', $topic->id)
                    ->where('question', $qData['question'])
                    ->exists();
                if ($exists) {
                    continue;
                }

                $question = Question::create([
                    'topic_id'    => $topic->id,
                    'type'        => 'MCQ',
                    'difficulty'  => $levelData['difficulty'],
                    'question'    => $qData['question'],
                    'explanation' => $qData['explanation'],
                ]);

                foreach ($qData['options'] as $opt) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $opt['text'],
                        'is_correct'  => $opt['correct'],
                    ]);
                }
            }
        }

        $this->command->info('Node.js Practice seeded: 1 subject, 3 topics, ~100 questions.');
    }

    private function juniorQuestions(): array
    {
        return [
            // ── Original 10 ──────────────────────────────────────────────────
            [
                'question'    => 'What is Node.js?',
                'explanation' => 'Node.js is an open-source, cross-platform JavaScript runtime environment that executes JavaScript code outside a browser. It is built on Chrome\'s V8 JavaScript engine. It uses an event-driven, non-blocking I/O model which makes it lightweight and efficient — ideal for data-intensive real-time applications like APIs, chat servers, and streaming services.',
                'options'     => [
                    ['text' => 'A JavaScript runtime built on Chrome\'s V8 engine for server-side JavaScript execution', 'correct' => true],
                    ['text' => 'A JavaScript framework similar to React or Angular for building web UIs', 'correct' => false],
                    ['text' => 'A browser extension that runs JavaScript in isolated sandboxes', 'correct' => false],
                    ['text' => 'A package manager for installing JavaScript libraries', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the Node.js event loop?',
                'explanation' => 'The event loop is the mechanism that allows Node.js to perform non-blocking I/O despite JavaScript being single-threaded. It continuously checks a queue of callbacks (from timers, I/O events, etc.) and executes them one at a time. While waiting for I/O (file reads, network requests), the loop keeps processing other tasks — this is what makes Node.js fast for I/O-heavy work.',
                'options'     => [
                    ['text' => 'A loop that processes async callbacks one at a time, enabling non-blocking I/O', 'correct' => true],
                    ['text' => 'A for-loop that iterates over all HTTP requests simultaneously', 'correct' => false],
                    ['text' => 'An infinite loop that keeps the Node.js process alive', 'correct' => false],
                    ['text' => 'A built-in scheduler that runs multiple JavaScript threads in parallel', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `npm` in the context of Node.js?',
                'explanation' => 'npm (Node Package Manager) is the default package manager for Node.js. It ships with Node.js and provides access to the npm registry — the world\'s largest software registry with over 2 million packages. Commands: `npm install`, `npm init`, `npm run`, `npm publish`. `package.json` is the manifest file that lists dependencies and project scripts.',
                'options'     => [
                    ['text' => 'Node Package Manager — used to install, share, and manage JavaScript packages', 'correct' => true],
                    ['text' => 'A Node.js module for monitoring production application performance', 'correct' => false],
                    ['text' => 'A built-in Node.js command for running network performance tests', 'correct' => false],
                    ['text' => 'The compiler that turns Node.js JavaScript into native machine code', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `require()` do in Node.js?',
                'explanation' => '`require()` is Node.js\'s CommonJS module system function used to import modules. It can load built-in modules (`require("fs")`), npm packages (`require("express")`), or local files (`require("./utils")`). When first called, the module is executed and its `module.exports` value is returned and cached — subsequent `require()` calls return the cached version without re-executing.',
                'options'     => [
                    ['text' => 'Imports a module by loading and caching it, returning its module.exports value', 'correct' => true],
                    ['text' => 'Makes an HTTP GET request to an external server', 'correct' => false],
                    ['text' => 'Requires the user to provide input before the script continues', 'correct' => false],
                    ['text' => 'Validates that all npm dependencies are installed before the app starts', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `module.exports` and `exports` in Node.js?',
                'explanation' => '`module.exports` is the actual object that `require()` returns. `exports` is initially a reference to the same object — a shorthand. You can add properties to `exports` safely (`exports.fn = fn`). But if you reassign `exports = something`, you break the reference — `module.exports` is unchanged. To export a single value (a class/function), always use `module.exports = MyClass`.',
                'options'     => [
                    ['text' => 'module.exports is what require() returns; exports is a shorthand reference to it', 'correct' => true],
                    ['text' => 'exports is the new syntax; module.exports is deprecated in modern Node.js', 'correct' => false],
                    ['text' => 'They are identical — completely interchangeable in all cases', 'correct' => false],
                    ['text' => 'module.exports is for classes; exports is for functions and variables', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `fs` module in Node.js?',
                'explanation' => 'The `fs` (File System) module provides APIs to interact with the file system. It has synchronous versions (`fs.readFileSync`) and asynchronous callback versions (`fs.readFile`), plus a Promise-based API (`fs.promises.readFile`). Common operations: reading files, writing files, creating directories, deleting files, watching for changes. It is a core module — no installation needed.',
                'options'     => [
                    ['text' => 'A built-in module for reading, writing, and manipulating files on the filesystem', 'correct' => true],
                    ['text' => 'A module for managing file system permissions in Linux', 'correct' => false],
                    ['text' => 'A third-party npm package for working with cloud file storage', 'correct' => false],
                    ['text' => 'A framework for building file-serving HTTP servers', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a callback function in Node.js?',
                'explanation' => 'A callback is a function passed as an argument to another function, to be executed after an async operation completes. Node.js uses the "error-first callback" convention: `(err, result) => {}`. If `err` is non-null, the operation failed. This pattern was the original way to handle async in Node.js before Promises and async/await were introduced.',
                'options'     => [
                    ['text' => 'A function passed to an async operation that is called when the operation completes', 'correct' => true],
                    ['text' => 'A function that calls back to the parent process when a child process exits', 'correct' => false],
                    ['text' => 'A Node.js built-in for scheduling repeated function calls', 'correct' => false],
                    ['text' => 'An event listener that fires when an HTTP response is received', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `process.env` in Node.js?',
                'explanation' => '`process.env` is an object that holds the current environment variables of the Node.js process — the same variables you\'d see with `printenv` on Linux. It is used to configure an application differently across environments (development, staging, production) without hardcoding values. Common pattern: `const PORT = process.env.PORT || 3000`. Libraries like `dotenv` load a `.env` file into `process.env` at startup.',
                'options'     => [
                    ['text' => 'An object containing the current environment variables for the Node.js process', 'correct' => true],
                    ['text' => 'The environment configuration file (.env) parsed into a JavaScript object', 'correct' => false],
                    ['text' => 'A set of built-in Node.js settings for configuring the V8 engine', 'correct' => false],
                    ['text' => 'A global object for managing server environment state at runtime', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `http` module in Node.js used for?',
                'explanation' => 'The `http` module is a core module for creating HTTP servers and making HTTP requests. `http.createServer((req, res) => { res.end("Hello") })` creates a server. The `req` object contains the request details (URL, method, headers, body stream) and `res` is the response object. This is the lowest-level way to build a web server; frameworks like Express wrap this module.',
                'options'     => [
                    ['text' => 'Creating raw HTTP servers and making HTTP requests at the lowest level', 'correct' => true],
                    ['text' => 'Managing cookies and sessions for web applications', 'correct' => false],
                    ['text' => 'A replacement for fetch() to make API calls to external services', 'correct' => false],
                    ['text' => 'Configuring HTTPS certificates and SSL/TLS settings', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does "non-blocking I/O" mean in the context of Node.js?',
                'explanation' => 'Non-blocking I/O means that Node.js initiates an I/O operation (reading a file, making a database query) and immediately moves on to the next task — without waiting for the I/O to complete. When the I/O finishes, the event loop picks up the callback and executes it. This contrasts with blocking I/O where the thread waits idle until the operation completes, wasting resources.',
                'options'     => [
                    ['text' => 'I/O operations are initiated and the program continues — the result arrives via a callback', 'correct' => true],
                    ['text' => 'Node.js blocks all I/O when the CPU is busy with JavaScript execution', 'correct' => false],
                    ['text' => 'I/O is handled by multiple threads in parallel without any callback', 'correct' => false],
                    ['text' => 'Incoming network connections are buffered to avoid blocking the main loop', 'correct' => false],
                ],
            ],

            // ── 23 New Junior Questions ───────────────────────────────────────
            [
                'question'    => 'What is the key difference between Node.js and browser JavaScript?',
                'explanation' => 'Both run the same JavaScript language, but they target different environments. Browser JavaScript has access to the DOM, window, localStorage, fetch, and browser APIs. Node.js has no DOM but provides access to the file system (fs), OS, network sockets, child processes, and other server-side capabilities. Node.js also exposes the global object as `global` (not `window`) and adds CommonJS module support via `require()`.',
                'options'     => [
                    ['text' => 'Node.js has no DOM but provides fs, os, and server APIs; browsers have the DOM but no file access', 'correct' => true],
                    ['text' => 'Node.js uses a different version of JavaScript than browsers do', 'correct' => false],
                    ['text' => 'Browser JavaScript supports async/await but Node.js does not', 'correct' => false],
                    ['text' => 'Node.js compiles JavaScript to machine code; browsers interpret it line by line', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the Node.js REPL?',
                'explanation' => 'REPL stands for Read-Eval-Print Loop. It is an interactive shell that ships with Node.js — start it by typing `node` in your terminal without any arguments. You can type JavaScript expressions and immediately see results. It is useful for quickly testing code snippets, exploring Node.js APIs, and debugging. Type `.exit` or press Ctrl+C twice to quit.',
                'options'     => [
                    ['text' => 'An interactive shell for running JavaScript expressions immediately — started by typing `node`', 'correct' => true],
                    ['text' => 'A remote execution environment for running Node.js code on a cloud server', 'correct' => false],
                    ['text' => 'A Node.js debugging tool that replays recorded HTTP requests', 'correct' => false],
                    ['text' => 'A template engine for generating HTML in Node.js applications', 'correct' => false],
                ],
            ],
            [
                'question'    => 'In Node.js, what is the `global` object?',
                'explanation' => '`global` is Node.js\'s global object — the equivalent of `window` in the browser. Variables or functions declared on `global` are accessible throughout the entire Node.js process without importing. Examples of built-in globals: `console`, `process`, `setTimeout`, `setInterval`, `Buffer`, and `__dirname`. Unlike the browser\'s `window`, properties of `global` are not added to the object automatically with `var` declarations at the top level of a module.',
                'options'     => [
                    ['text' => 'The global namespace object in Node.js — equivalent to window in the browser', 'correct' => true],
                    ['text' => 'A module for storing and sharing global state across Node.js worker threads', 'correct' => false],
                    ['text' => 'An environment variable that sets the Node.js global timeout limit', 'correct' => false],
                    ['text' => 'A reserved keyword that declares variables available only within the current module', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `setTimeout` and `setInterval` in Node.js?',
                'explanation' => '`setTimeout(fn, delay)` schedules `fn` to run once after `delay` milliseconds. `setInterval(fn, interval)` schedules `fn` to run repeatedly every `interval` milliseconds until cleared. Both return a timer ID. Use `clearTimeout(id)` to cancel a `setTimeout` and `clearInterval(id)` to stop a `setInterval`. They behave the same as in browsers but are implemented by Node.js via libuv timers.',
                'options'     => [
                    ['text' => 'setTimeout runs a function once after a delay; setInterval runs it repeatedly on a fixed interval', 'correct' => true],
                    ['text' => 'setInterval runs a function once; setTimeout runs it on every event loop tick', 'correct' => false],
                    ['text' => 'Both schedule functions to run after the same delay — they are functionally identical', 'correct' => false],
                    ['text' => 'setTimeout is for synchronous code; setInterval is for async code', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What do `clearTimeout` and `clearInterval` do in Node.js?',
                'explanation' => '`clearTimeout(timerId)` cancels a pending `setTimeout` before it fires. `clearInterval(timerId)` stops a `setInterval` from continuing to fire. Both accept the timer ID returned when the timer was created. This is important for resource management — a `setInterval` that is not cleared will prevent the Node.js process from exiting naturally and can cause memory leaks.',
                'options'     => [
                    ['text' => 'They cancel pending timers — clearTimeout for one-shot timers, clearInterval for repeating ones', 'correct' => true],
                    ['text' => 'They reset a timer back to zero without stopping it', 'correct' => false],
                    ['text' => 'They pause a timer until it is resumed with a corresponding start call', 'correct' => false],
                    ['text' => 'They remove all timers from the event loop queue immediately', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `Buffer` class in Node.js?',
                'explanation' => '`Buffer` is a global class in Node.js for working with raw binary data — it represents a fixed-size chunk of memory outside the V8 heap. Buffers are used when handling streams, file I/O, network packets, and binary protocols. Create with `Buffer.from("hello", "utf8")`, `Buffer.alloc(10)`, or `Buffer.allocUnsafe(10)`. `Buffer.alloc` zero-fills; `allocUnsafe` is faster but may contain old memory.',
                'options'     => [
                    ['text' => 'A global class for storing and manipulating raw binary data outside the V8 heap', 'correct' => true],
                    ['text' => 'A class for buffering HTTP responses before sending them to the client', 'correct' => false],
                    ['text' => 'A queue that buffers incoming event loop callbacks when the loop is busy', 'correct' => false],
                    ['text' => 'A built-in cache layer for storing frequently used module exports', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `url` module\'s `url.parse()` method do in Node.js?',
                'explanation' => '`url.parse(urlString)` parses a URL string into its components: `protocol`, `host`, `hostname`, `port`, `pathname`, `query`, `hash`, etc. For example, parsing `http://example.com:8080/path?q=1` gives you each part as a property. Note: `url.parse` is legacy — the modern approach is the WHATWG `URL` class (`new URL(urlString)`), which is globally available in Node.js 10+.',
                'options'     => [
                    ['text' => 'Breaks a URL string into its components (protocol, host, pathname, query, etc.)', 'correct' => true],
                    ['text' => 'Makes an HTTP GET request to the given URL and returns the response body', 'correct' => false],
                    ['text' => 'Validates that a URL is correctly formatted before sending a network request', 'correct' => false],
                    ['text' => 'Encodes special characters in a URL using percent-encoding', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `querystring` module used for in Node.js?',
                'explanation' => 'The `querystring` module parses and stringifies URL query strings. `querystring.parse("name=John&age=30")` returns `{ name: "John", age: "30" }`. `querystring.stringify({ name: "John", age: 30 })` returns `"name=John&age=30"`. This module is considered legacy — the modern replacement is `URLSearchParams`, available globally via the WHATWG URL API.',
                'options'     => [
                    ['text' => 'Parses URL query strings into objects and serialises objects back into query strings', 'correct' => true],
                    ['text' => 'Validates and sanitises query parameters to prevent SQL injection', 'correct' => false],
                    ['text' => 'Generates signed query strings for secure API requests', 'correct' => false],
                    ['text' => 'A middleware for automatically parsing query parameters in Express routes', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `os` module provide in Node.js?',
                'explanation' => 'The `os` module exposes operating system information. Key methods: `os.platform()` returns the OS platform (`linux`, `win32`, `darwin`); `os.cpus()` returns an array of CPU core info objects; `os.freemem()` returns free system memory in bytes; `os.totalmem()` returns total memory; `os.homedir()` returns the current user\'s home directory; `os.hostname()` returns the machine\'s hostname.',
                'options'     => [
                    ['text' => 'Provides OS information — platform, CPU count, free memory, hostname, etc.', 'correct' => true],
                    ['text' => 'A module for interacting with the operating system\'s process manager', 'correct' => false],
                    ['text' => 'A tool for cross-compiling Node.js applications for different operating systems', 'correct' => false],
                    ['text' => 'A module for setting operating system environment variables from Node.js', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you create an MD5 or SHA-256 hash using the Node.js `crypto` module?',
                'explanation' => 'Use `crypto.createHash(algorithm)` to create a hash object, call `.update(data)` to feed data into it, and call `.digest("hex")` to get the hex string result. Example: `crypto.createHash("sha256").update("hello").digest("hex")`. The `crypto` module is a core module — no installation needed. It wraps OpenSSL and supports SHA-256, SHA-512, MD5, and many more algorithms.',
                'options'     => [
                    ['text' => 'crypto.createHash("sha256").update(data).digest("hex") returns the hex hash string', 'correct' => true],
                    ['text' => 'crypto.hash("sha256", data) returns the hash as a Buffer directly', 'correct' => false],
                    ['text' => 'Use crypto.md5(data) for MD5 and crypto.sha256(data) for SHA-256', 'correct' => false],
                    ['text' => 'The crypto module only supports HMAC — use a third-party package for plain hashing', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What do `console.error`, `console.table`, and `console.time` do in Node.js?',
                'explanation' => '`console.error()` writes to stderr (not stdout) — useful for logging errors separately. `console.table(data)` prints an array of objects as a formatted ASCII table — great for debugging structured data. `console.time("label")` starts a timer, and `console.timeEnd("label")` stops it and logs the elapsed time in milliseconds — useful for measuring code performance.',
                'options'     => [
                    ['text' => 'error writes to stderr; table prints data as a table; time/timeEnd measures elapsed ms', 'correct' => true],
                    ['text' => 'All three write to stdout — they only differ in formatting style', 'correct' => false],
                    ['text' => 'console.error throws an exception; console.table logs to a file; console.time pauses execution', 'correct' => false],
                    ['text' => 'They are browser-only APIs — Node.js console only supports console.log', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `process.argv` in Node.js?',
                'explanation' => '`process.argv` is an array containing the command-line arguments passed when the Node.js process was started. `process.argv[0]` is always the path to the `node` executable, `process.argv[1]` is the path to the script being run, and `process.argv[2]` onward are the user-supplied arguments. Example: running `node app.js --port 3000` gives `process.argv` as `["node", "app.js", "--port", "3000"]`.',
                'options'     => [
                    ['text' => 'An array of command-line arguments — index 0 is node path, 1 is script path, 2+ are user args', 'correct' => true],
                    ['text' => 'An object mapping argument names to their values, automatically parsed from the command line', 'correct' => false],
                    ['text' => 'The arguments passed to the currently executing JavaScript function', 'correct' => false],
                    ['text' => 'A list of environment variables set when the Node.js process started', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `process.exit()` do in Node.js?',
                'explanation' => '`process.exit(code)` terminates the Node.js process immediately with the given exit code. Exit code `0` means success; any non-zero code indicates an error (convention: `1` for general errors). It stops the event loop immediately — no more callbacks, timers, or I/O will run. Avoid it in library code. In applications, prefer graceful shutdown (drain connections, close DB) before calling `process.exit(0)`.',
                'options'     => [
                    ['text' => 'Immediately terminates the process with an exit code — 0 for success, non-zero for error', 'correct' => true],
                    ['text' => 'Pauses the process until a SIGCONT signal is received', 'correct' => false],
                    ['text' => 'Restarts the Node.js process with the same arguments', 'correct' => false],
                    ['text' => 'Gracefully closes all open HTTP connections before the process stops', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `process.stdout.write` and how does it differ from `console.log`?',
                'explanation' => '`process.stdout.write(data)` writes raw data to standard output without adding a newline at the end. `console.log()` internally calls `process.stdout.write` and appends a `\\n`. This makes `process.stdout.write` useful for building CLIs or progress indicators where you want to overwrite the current line using `\\r` (carriage return) or control the exact output format without automatic line breaks.',
                'options'     => [
                    ['text' => 'process.stdout.write writes raw data to stdout without a newline; console.log adds a newline', 'correct' => true],
                    ['text' => 'process.stdout.write is async; console.log is synchronous', 'correct' => false],
                    ['text' => 'They are identical — console.log is just an alias for process.stdout.write', 'correct' => false],
                    ['text' => 'process.stdout.write writes to a file; console.log writes to the terminal', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between synchronous and asynchronous file reading in Node.js?',
                'explanation' => '`fs.readFileSync(path)` blocks the entire event loop until the file is fully read — no other code executes during this time. `fs.readFile(path, callback)` is non-blocking — it initiates the read and immediately returns, allowing other code to run; the callback fires when reading is done. For server applications, always prefer async reads to avoid blocking other requests. Sync reads are acceptable in startup scripts.',
                'options'     => [
                    ['text' => 'readFileSync blocks the event loop until done; readFile is non-blocking with a callback', 'correct' => true],
                    ['text' => 'readFileSync is faster; readFile is safer but reads files in chunks', 'correct' => false],
                    ['text' => 'Both are identical in behavior — the difference is only naming convention', 'correct' => false],
                    ['text' => 'readFile is deprecated; readFileSync is the modern recommended approach', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `fs.existsSync(path)` do in Node.js?',
                'explanation' => '`fs.existsSync(path)` synchronously checks whether a file or directory exists at the given path and returns a boolean (`true` or `false`). It is a simple check — it does not tell you if it is a file or a directory. It is synchronous, so it blocks the event loop. Use only in scripts or startup code. For production servers, prefer async alternatives like `fs.access()` or `fs.stat()` with a callback.',
                'options'     => [
                    ['text' => 'Synchronously returns true if a file or directory exists at the given path, false otherwise', 'correct' => true],
                    ['text' => 'Asynchronously checks if a file exists and resolves to a boolean Promise', 'correct' => false],
                    ['text' => 'Returns file metadata (size, permissions) if the file exists, or null if not', 'correct' => false],
                    ['text' => 'Creates the file if it does not exist and returns the file handle', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `fs.mkdirSync(path)` do and what option allows creating nested directories?',
                'explanation' => '`fs.mkdirSync(path)` creates a directory synchronously. By default it throws an error if any parent directory in the path does not exist. Passing `{ recursive: true }` makes it behave like `mkdir -p` — it creates all missing parent directories automatically and does not throw if the directory already exists. The async version is `fs.mkdir(path, { recursive: true }, callback)`.',
                'options'     => [
                    ['text' => 'Creates a directory synchronously; { recursive: true } creates all missing parent directories', 'correct' => true],
                    ['text' => 'Creates a directory and all files inside it from a template structure', 'correct' => false],
                    ['text' => 'Moves an existing directory to a new path synchronously', 'correct' => false],
                    ['text' => 'Creates a temporary directory and returns its path', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `fs.readdirSync(path)` return in Node.js?',
                'explanation' => '`fs.readdirSync(path)` synchronously reads the contents of a directory and returns an array of file and directory names (strings) within it. It does not recurse into subdirectories — it only lists the immediate children. Pass `{ withFileTypes: true }` to get `Dirent` objects, which have methods like `.isFile()` and `.isDirectory()` instead of just name strings.',
                'options'     => [
                    ['text' => 'Returns an array of filenames (strings) of the immediate children of the directory', 'correct' => true],
                    ['text' => 'Recursively lists all files in a directory and all its subdirectories', 'correct' => false],
                    ['text' => 'Returns the number of files and directories inside the specified path', 'correct' => false],
                    ['text' => 'Reads a directory and returns a stream of file names one at a time', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `path.join()` and `path.resolve()` in Node.js?',
                'explanation' => '`path.join()` simply concatenates path segments using the OS separator without caring about the current directory. `path.resolve()` builds an absolute path — it resolves segments from right to left and treats an absolute segment as the new root. Example: `path.resolve("/foo", "bar")` returns `/foo/bar`, while `path.resolve("foo", "bar")` returns the current working directory + `/foo/bar`. `path.join` never produces an absolute path unless you start with one.',
                'options'     => [
                    ['text' => 'join concatenates segments with OS separator; resolve builds an absolute path from the cwd', 'correct' => true],
                    ['text' => 'They are identical — both produce the same result for all inputs', 'correct' => false],
                    ['text' => 'join works on Windows; resolve works on Linux and macOS only', 'correct' => false],
                    ['text' => 'resolve checks whether the path exists on disk; join does not', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `npm install --save-dev` and `npm install --save`?',
                'explanation' => '`npm install --save` (or just `npm install`) adds the package to `dependencies` in `package.json` — these are packages required at runtime in production. `npm install --save-dev` adds the package to `devDependencies` — packages only needed during development (testing frameworks, build tools, linters). When deploying, you run `npm install --production` to skip devDependencies and keep the production build lean.',
                'options'     => [
                    ['text' => '--save adds to dependencies (runtime); --save-dev adds to devDependencies (development only)', 'correct' => true],
                    ['text' => '--save-dev saves the package globally; --save saves it locally in the project', 'correct' => false],
                    ['text' => 'Both are identical — they both add packages to dependencies in package.json', 'correct' => false],
                    ['text' => '--save-dev is for frontend packages; --save is for backend packages', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `npx` command used for in Node.js?',
                'explanation' => '`npx` (bundled with npm 5.2+) lets you execute an npm package binary without globally installing it. Example: `npx create-react-app my-app` downloads and runs the `create-react-app` package temporarily. It also runs locally-installed binaries from `node_modules/.bin` — so `npx jest` runs the jest binary installed in the project instead of a globally installed version. This avoids global install conflicts.',
                'options'     => [
                    ['text' => 'Runs an npm package binary without globally installing it — great for one-off CLI tools', 'correct' => true],
                    ['text' => 'A Node.js command for executing scripts defined in package.json', 'correct' => false],
                    ['text' => 'A tool for cross-platform script execution in Node.js projects', 'correct' => false],
                    ['text' => 'An alias for npm install -g that installs packages in the global scope', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Why should `node_modules` be added to `.gitignore`?',
                'explanation' => 'The `node_modules` folder can contain thousands of files and hundreds of megabytes of code. It is fully reproducible by running `npm install` using `package.json` and `package-lock.json`. Committing it to git makes the repository huge, slows down cloning, and causes merge conflicts. The `.gitignore` file tells git to ignore `node_modules/` so only source code is tracked. Always commit `package-lock.json` to ensure reproducible installs.',
                'options'     => [
                    ['text' => 'node_modules is large and reproducible via package.json — committing it wastes repo space', 'correct' => true],
                    ['text' => 'node_modules contains binary files that git cannot diff or merge', 'correct' => false],
                    ['text' => 'npm automatically deletes node_modules after each install so it cannot be committed', 'correct' => false],
                    ['text' => 'It is a security requirement — node_modules must never be shared outside the team', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of the `node_modules` folder in a Node.js project?',
                'explanation' => '`node_modules` is the directory where npm installs all packages listed in `package.json` (and their transitive dependencies). When you run `require("express")`, Node.js looks for the package in the `node_modules` folder. Each package may have its own nested `node_modules` for its dependencies (though npm 3+ flattens the tree as much as possible). The folder is created or updated by `npm install`.',
                'options'     => [
                    ['text' => 'Stores all installed npm packages and their dependencies for the project', 'correct' => true],
                    ['text' => 'A folder where Node.js stores compiled native C++ addons for the project', 'correct' => false],
                    ['text' => 'A cache directory used by the Node.js runtime to speed up module loading', 'correct' => false],
                    ['text' => 'A folder containing user-written modules that are shared across multiple projects', 'correct' => false],
                ],
            ],
        ];
    }

    private function intermediateQuestions(): array
    {
        return [
            // ── Original 10 ──────────────────────────────────────────────────
            [
                'question'    => 'What is the difference between `setImmediate()` and `process.nextTick()` in Node.js?',
                'explanation' => '`process.nextTick()` fires before the event loop moves to the next phase — it queues callbacks in the "next tick queue" which runs after the current operation, before any I/O. `setImmediate()` fires in the "check" phase of the event loop — after I/O callbacks. `nextTick` can starve I/O if used excessively; `setImmediate` is the safer choice for recursive scheduling.',
                'options'     => [
                    ['text' => 'nextTick fires before the next event loop iteration; setImmediate fires in the check phase after I/O', 'correct' => true],
                    ['text' => 'setImmediate fires immediately (synchronously); nextTick waits for the next tick', 'correct' => false],
                    ['text' => 'They are identical — both schedule a callback for the next iteration of the event loop', 'correct' => false],
                    ['text' => 'nextTick is for timers; setImmediate is for I/O callbacks', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are Node.js Streams?',
                'explanation' => 'Streams are objects that let you read or write data in chunks — instead of buffering everything in memory. Types: Readable (data source), Writable (data destination), Duplex (both), and Transform (read/modify/write). Example: piping a file read stream directly to an HTTP response (`fs.createReadStream("file").pipe(res)`) handles large files without loading them fully into memory.',
                'options'     => [
                    ['text' => 'Objects for reading/writing data in chunks without loading everything into memory', 'correct' => true],
                    ['text' => 'WebSocket connections that stream live data from a server to the client', 'correct' => false],
                    ['text' => 'Node.js worker threads that process data in parallel streams', 'correct' => false],
                    ['text' => 'Async iterators that yield database rows one at a time', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of `package.json` in a Node.js project?',
                'explanation' => '`package.json` is the manifest file for a Node.js project. It records: the project name, version, description, and main entry point; `dependencies` (runtime packages); `devDependencies` (build-time packages like Jest); `scripts` (shortcuts for CLI commands like `npm start`, `npm test`); and engine requirements. It is checked into source control; `package-lock.json` locks exact dependency versions.',
                'options'     => [
                    ['text' => 'The project manifest — records metadata, dependencies, and npm scripts', 'correct' => true],
                    ['text' => 'A JSON file that maps file paths to their compiled JavaScript output', 'correct' => false],
                    ['text' => 'The npm config file that stores global npm settings', 'correct' => false],
                    ['text' => 'A lockfile that pins the exact installed version of each dependency', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you handle unhandled promise rejections in Node.js?',
                'explanation' => 'Unhandled promise rejections are dangerous — they crash the process in Node.js 15+ (older versions emitted a warning). Best practice: always attach `.catch()` to promise chains, or use `try/catch` with `async/await`. For global handling, listen to `process.on("unhandledRejection", (reason, promise) => {})`. In production, use this to log the error and gracefully shut down.',
                'options'     => [
                    ['text' => 'Attach .catch() or try/catch; use process.on("unhandledRejection") for global handling', 'correct' => true],
                    ['text' => 'Node.js automatically retries rejected promises before throwing', 'correct' => false],
                    ['text' => 'Use process.env.REJECT_MODE = "silent" to suppress promise errors', 'correct' => false],
                    ['text' => 'Unhandled rejections are safe — they are caught by the event loop', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between CommonJS (`require`) and ES Modules (`import`) in Node.js?',
                'explanation' => 'CommonJS (CJS) is Node.js\'s original module system using `require()` and `module.exports`. ES Modules (ESM) use `import`/`export` syntax — the standard in browsers and modern Node.js. Key differences: CJS is synchronous and loads at runtime; ESM is static and analyzed at parse time (enabling tree-shaking). To use ESM in Node.js, use `.mjs` files or set `"type": "module"` in package.json.',
                'options'     => [
                    ['text' => 'CJS uses require/module.exports (synchronous); ESM uses import/export (static, tree-shakeable)', 'correct' => true],
                    ['text' => 'ESM is the legacy syntax; CommonJS is the modern standard in Node.js 18+', 'correct' => false],
                    ['text' => 'They are interchangeable — both work identically in all Node.js versions', 'correct' => false],
                    ['text' => 'CommonJS is for third-party packages; ESM is for application code', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is middleware in the context of a Node.js HTTP server?',
                'explanation' => 'Middleware is a function that sits between the request and the response in the request-processing pipeline. It receives `(req, res, next)` and can read/modify the request or response, terminate the request cycle, or call `next()` to pass control to the next middleware. Common uses: authentication, logging, parsing request body, compression, CORS headers. Express is built entirely around this pattern.',
                'options'     => [
                    ['text' => 'Functions in the request pipeline with (req, res, next) — each can process or pass to the next', 'correct' => true],
                    ['text' => 'Database connection middleware that sits between Node.js and SQL servers', 'correct' => false],
                    ['text' => 'A Node.js process that runs between the web server and the application', 'correct' => false],
                    ['text' => 'Caching layer between the Node.js server and its clients', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `EventEmitter` class in Node.js?',
                'explanation' => 'EventEmitter is Node.js\'s built-in class for implementing the observer/pub-sub pattern. Objects extend EventEmitter to emit named events (`this.emit("data", payload)`) and allow listeners to subscribe (`emitter.on("data", callback)`). It is the foundation of Node.js\'s core — streams, HTTP servers, and process itself are EventEmitters. `once()` attaches a one-time listener.',
                'options'     => [
                    ['text' => 'A class for implementing pub-sub — emit named events and register listeners with .on()', 'correct' => true],
                    ['text' => 'A class that wraps browser DOM events for use in Node.js server code', 'correct' => false],
                    ['text' => 'A built-in Node.js scheduler for emitting events at timed intervals', 'correct' => false],
                    ['text' => 'A class for sending WebSocket events from the Node.js server to the client', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `path` module in Node.js used for?',
                'explanation' => 'The `path` module provides utilities for working with file and directory paths in a cross-platform way. Key methods: `path.join()` combines segments with the correct separator (`/` on Linux, `\\` on Windows); `path.resolve()` resolves an absolute path; `path.dirname()` returns the directory; `path.basename()` returns the filename; `path.extname()` returns the file extension.',
                'options'     => [
                    ['text' => 'Cross-platform utilities for joining, resolving, and parsing file system paths', 'correct' => true],
                    ['text' => 'A module for configuring URL paths in an Express router', 'correct' => false],
                    ['text' => 'A built-in tool for searching the filesystem by file path pattern', 'correct' => false],
                    ['text' => 'A module for registering Node.js process paths in system environment variables', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How does Node.js handle CPU-intensive tasks without blocking the event loop?',
                'explanation' => 'Since Node.js is single-threaded, CPU-intensive work (image processing, cryptography, heavy computation) blocks the event loop and freezes all other requests. Solutions: Worker Threads (`worker_threads` module) run JavaScript in separate threads with shared memory. Child Processes (`child_process`) spawn separate Node.js processes. The `cluster` module forks the process for each CPU core to parallelize across cores.',
                'options'     => [
                    ['text' => 'Worker Threads or Child Processes offload CPU work to separate threads/processes', 'correct' => true],
                    ['text' => 'Node.js automatically detects CPU-intensive code and moves it to a background thread', 'correct' => false],
                    ['text' => 'Using async/await wraps CPU work in microtasks so it doesn\'t block I/O', 'correct' => false],
                    ['text' => 'The libuv thread pool handles CPU work alongside I/O operations automatically', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `__dirname` and `__filename` in Node.js?',
                'explanation' => '`__dirname` is a global variable containing the absolute path of the directory containing the current module file. `__filename` contains the absolute path of the current module file itself. These are available in CommonJS modules and are useful for building file paths relative to the current file (e.g., `path.join(__dirname, "views")`). In ESM modules, use `import.meta.url` with `fileURLToPath` instead.',
                'options'     => [
                    ['text' => '__dirname is the current module\'s directory path; __filename is the current file\'s full path', 'correct' => true],
                    ['text' => '__dirname is the root directory of the Node.js installation', 'correct' => false],
                    ['text' => 'Both are environment variables set by npm when running project scripts', 'correct' => false],
                    ['text' => '__filename contains the currently executing function name at runtime', 'correct' => false],
                ],
            ],

            // ── 23 New Intermediate Questions ────────────────────────────────
            [
                'question'    => 'What is the difference between `child_process.exec`, `spawn`, and `fork` in Node.js?',
                'explanation' => '`exec(command, callback)` runs a shell command, buffers stdout/stderr, and returns them to a callback — convenient but not suited to large output. `spawn(command, args)` launches a process and returns its I/O as streams — better for large or streaming output. `fork(modulePath)` is a special form of `spawn` for Node.js scripts; it creates an IPC channel between parent and child for passing messages with `.send()` and `on("message")`.',
                'options'     => [
                    ['text' => 'exec buffers output in a callback; spawn streams output; fork is spawn with IPC for Node.js scripts', 'correct' => true],
                    ['text' => 'exec is async; spawn is sync; fork runs in a separate thread instead of a process', 'correct' => false],
                    ['text' => 'All three are identical — they only differ in the name of the callback parameter', 'correct' => false],
                    ['text' => 'exec is for system commands; spawn is for npm scripts; fork is for worker threads', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `readline` module used for in Node.js?',
                'explanation' => 'The `readline` module provides an interface for reading input line by line from a readable stream (commonly `process.stdin`). It is used to build interactive CLI tools. Create an interface with `readline.createInterface({ input: process.stdin, output: process.stdout })`, then call `.question("prompt", callback)` to prompt the user or listen to the `"line"` event to process each line as it is entered.',
                'options'     => [
                    ['text' => 'Reads input line by line from a stream — commonly used to build interactive CLI tools', 'correct' => true],
                    ['text' => 'Reads a text file and returns all lines as an array synchronously', 'correct' => false],
                    ['text' => 'A module for rendering terminal UI components like progress bars', 'correct' => false],
                    ['text' => 'A wrapper around the fs module that reads files line by line into a buffer', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `zlib` module used for in Node.js?',
                'explanation' => 'The `zlib` module provides compression and decompression using gzip, deflate, and brotli algorithms. It is commonly used to compress HTTP responses (`res.pipe(zlib.createGzip()).pipe(originalRes)`) and to compress or decompress files. It works with streams, making it memory-efficient for large payloads. HTTP servers use `Accept-Encoding` and `Content-Encoding` headers to negotiate compression with clients.',
                'options'     => [
                    ['text' => 'Provides gzip/deflate/brotli compression and decompression — often used for HTTP response compression', 'correct' => true],
                    ['text' => 'A module for creating ZIP archives from multiple files and directories', 'correct' => false],
                    ['text' => 'A library for lossless image compression in Node.js servers', 'correct' => false],
                    ['text' => 'A built-in database compression driver for reducing MySQL storage usage', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `net` module provide in Node.js?',
                'explanation' => 'The `net` module provides an API for creating raw TCP servers and clients. `net.createServer(socket => { ... })` creates a TCP server where you interact with each connection as a duplex stream. `net.createConnection({ host, port })` creates a TCP client. The `http` module is built on top of `net`. It is useful for building custom protocols, proxies, and low-level networking tools.',
                'options'     => [
                    ['text' => 'Creates raw TCP servers and clients — the foundation that the http module is built on', 'correct' => true],
                    ['text' => 'A module for managing Node.js network interfaces and IP address configuration', 'correct' => false],
                    ['text' => 'Provides utilities for parsing network packets and inspecting IP headers', 'correct' => false],
                    ['text' => 'A high-level HTTP client for making REST API calls from Node.js', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `dgram` module used for in Node.js?',
                'explanation' => 'The `dgram` module provides an implementation of UDP (User Datagram Protocol) sockets. Unlike TCP, UDP is connectionless — packets are sent without establishing a connection first. Use `dgram.createSocket("udp4")` to create a UDP socket, then call `.send()` to send datagrams and listen to the `"message"` event to receive them. UDP is used where low latency matters more than reliability (e.g., DNS, video streaming).',
                'options'     => [
                    ['text' => 'Implements UDP sockets for connectionless, low-latency datagram communication', 'correct' => true],
                    ['text' => 'A module for creating diagram (flowchart) data visualisations from Node.js data', 'correct' => false],
                    ['text' => 'A distributed graph module for building peer-to-peer Node.js networks', 'correct' => false],
                    ['text' => 'A diagnostic module for monitoring event loop lag and I/O throughput', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between the `https` module and the `http` module in Node.js?',
                'explanation' => 'The `https` module is the TLS/SSL-encrypted version of `http`. Both have identical APIs — you create servers with `createServer()` and make requests similarly. The key difference: `https.createServer()` requires a `key` and `cert` option (your SSL certificate). In production, TLS is often terminated at a reverse proxy (nginx, AWS ALB) and Node.js receives plain `http` traffic internally, avoiding the overhead of TLS in the application layer.',
                'options'     => [
                    ['text' => 'https wraps http with TLS encryption — identical API but requires an SSL key and certificate', 'correct' => true],
                    ['text' => 'https is a third-party package; http is the only built-in module for networking', 'correct' => false],
                    ['text' => 'https automatically redirects http traffic — no certificate configuration needed', 'correct' => false],
                    ['text' => 'http is for internal APIs; https is required for any public-facing endpoint', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the WHATWG `URL` class in Node.js (url.URL)?',
                'explanation' => 'The WHATWG `URL` class (available globally in Node.js 10+ or via `require("url").URL`) implements the URL Standard used in browsers. `new URL("https://example.com/path?q=1")` gives you an object with properties like `.hostname`, `.pathname`, `.searchParams` (a `URLSearchParams` object for reading and modifying query params). It is the modern replacement for the legacy `url.parse()` function.',
                'options'     => [
                    ['text' => 'The modern browser-compatible URL API — parses URLs into structured objects with URLSearchParams support', 'correct' => true],
                    ['text' => 'A utility class for building and validating URL regex patterns in Node.js', 'correct' => false],
                    ['text' => 'A Node.js-specific URL class that only works with file:// protocol URLs', 'correct' => false],
                    ['text' => 'A class for encoding and decoding URLs using the encodeURIComponent algorithm', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `tls` module in Node.js used for?',
                'explanation' => 'The `tls` module implements TLS (Transport Layer Security) and SSL protocols. It wraps `net` sockets to provide encrypted communication. `tls.createServer({ key, cert }, callback)` creates a TLS server; `tls.connect({ host, port, ca })` creates a TLS client. The `https` module uses `tls` internally. You interact with `tls` directly when building custom encrypted TCP protocols or mutual TLS (mTLS) authentication.',
                'options'     => [
                    ['text' => 'Implements TLS/SSL encryption for TCP streams — used directly for custom encrypted protocols', 'correct' => true],
                    ['text' => 'A module for managing TLS certificates stored in the OS certificate store', 'correct' => false],
                    ['text' => 'A third-party library that adds HTTPS support to the built-in http module', 'correct' => false],
                    ['text' => 'A module for testing SSL handshakes and verifying certificate chains', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `vm` module in Node.js?',
                'explanation' => 'The `vm` module lets you compile and run JavaScript code within V8 virtual machine contexts. `vm.runInNewContext(code, sandbox)` runs code in an isolated context where `sandbox` is the global object. It is not a security sandbox — code can still escape with enough creativity. It is used for dynamic code execution, REPL implementations, and template engines that evaluate expressions. For real sandboxing, use a separate process or a container.',
                'options'     => [
                    ['text' => 'Compiles and runs JavaScript code in a separate V8 context — used for dynamic code evaluation', 'correct' => true],
                    ['text' => 'A module for managing virtual machines and Docker containers from Node.js', 'correct' => false],
                    ['text' => 'A memory management module that controls how V8 allocates heap memory', 'correct' => false],
                    ['text' => 'A testing utility that creates virtual module mocks without modifying the file system', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `console.assert` do in Node.js?',
                'explanation' => '`console.assert(condition, message)` writes an error message to stderr if `condition` is falsy. If the condition is truthy, nothing happens. In Node.js (unlike browsers), `console.assert` does not throw an error on failure — it only logs. It is useful for lightweight sanity checks during development. For production assertions that throw, use the built-in `assert` module (`const assert = require("assert")`).',
                'options'     => [
                    ['text' => 'Logs an error message to stderr if the condition is falsy — does not throw in Node.js', 'correct' => true],
                    ['text' => 'Throws an AssertionError and stops execution if the condition is false', 'correct' => false],
                    ['text' => 'Runs a unit test assertion and reports the result in a test suite output', 'correct' => false],
                    ['text' => 'Asserts that a value is defined and throws TypeError if it is null or undefined', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `util.promisify` do in Node.js?',
                'explanation' => '`util.promisify(fn)` takes a function that follows the Node.js error-first callback convention `(err, result) => {}` and returns a new function that returns a Promise instead. Example: `const readFile = util.promisify(fs.readFile)` — you can then use `await readFile("file.txt")`. This bridges old callback-based APIs with modern async/await code without rewriting the underlying implementation.',
                'options'     => [
                    ['text' => 'Converts a Node.js error-first callback function into one that returns a Promise', 'correct' => true],
                    ['text' => 'Wraps any function to make it run asynchronously in the libuv thread pool', 'correct' => false],
                    ['text' => 'Converts a Promise-based function back to a callback-based function', 'correct' => false],
                    ['text' => 'Provides a polyfill for Promise in older versions of Node.js', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `util.inspect` do in Node.js?',
                'explanation' => '`util.inspect(object, options)` returns a string representation of any JavaScript value — including deeply nested objects, circular references, and non-enumerable properties. Options include `depth` (how deep to recurse), `colors` (ANSI color codes), and `showHidden` (include non-enumerable properties). `console.log` uses `util.inspect` internally. Useful for debugging complex data structures and building custom logging.',
                'options'     => [
                    ['text' => 'Returns a string representation of any value, including deep objects and circular references', 'correct' => true],
                    ['text' => 'Inspects and validates the type signature of a function\'s arguments', 'correct' => false],
                    ['text' => 'A decorator that adds runtime type checking to class properties', 'correct' => false],
                    ['text' => 'Checks whether a given value is a Node.js built-in object type', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `util.format` do in Node.js?',
                'explanation' => '`util.format(format, ...args)` works like `printf` in C — it formats a string using placeholders. Supported placeholders: `%s` for strings, `%d` for numbers, `%i` for integers, `%f` for floats, `%o` and `%O` for objects (using `util.inspect`), `%j` for JSON, and `%%` for a literal `%`. `console.log` uses `util.format` internally when given multiple arguments. Useful for building log messages programmatically.',
                'options'     => [
                    ['text' => 'Formats a string with printf-style placeholders (%s, %d, %o) — used internally by console.log', 'correct' => true],
                    ['text' => 'Formats a JavaScript object as a prettified JSON string with indentation', 'correct' => false],
                    ['text' => 'A template literal tag that automatically escapes HTML special characters', 'correct' => false],
                    ['text' => 'Converts a date or number to a locale-specific string format', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `timers/promises` module in Node.js?',
                'explanation' => 'The `timers/promises` module (available in Node.js 15+) provides Promise-based versions of timer functions. `timers/promises.setTimeout(delay)` returns a Promise that resolves after `delay` ms — you can `await` it instead of nesting callbacks. `timers/promises.setImmediate()` and `timers/promises.setInterval()` are also available. This makes timer-based async code cleaner and avoids callback nesting.',
                'options'     => [
                    ['text' => 'Provides Promise-based timer functions (setTimeout, setImmediate) that can be awaited', 'correct' => true],
                    ['text' => 'A module that tracks how long each timer runs and reports timing statistics', 'correct' => false],
                    ['text' => 'A replacement for the event loop that processes timers using Promises instead', 'correct' => false],
                    ['text' => 'A module for scheduling tasks at specific clock times using cron expressions', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `async_hooks` module used for in Node.js?',
                'explanation' => 'The `async_hooks` module provides an API for tracking the lifetime of async resources in Node.js. It allows you to hook into the creation, destruction, and lifecycle events of async resources (Promises, timers, I/O operations). The primary use case is `AsyncLocalStorage` — built on `async_hooks` — which stores per-request context (like a request ID or user session) that flows automatically across all async operations in a request chain.',
                'options'     => [
                    ['text' => 'Tracks async resource lifetimes — the foundation for AsyncLocalStorage and request-scoped context', 'correct' => true],
                    ['text' => 'A module for running multiple async functions concurrently with a concurrency limit', 'correct' => false],
                    ['text' => 'Provides hooks that fire before and after each event loop iteration', 'correct' => false],
                    ['text' => 'A debugging module that logs the call stack for all unhandled promise rejections', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `diagnostics_channel` in Node.js?',
                'explanation' => '`diagnostics_channel` (Node.js 15+) provides a pub-sub API for publishing and subscribing to named diagnostic events. Libraries like `undici`, `mongodb`, and `pg` use it to publish instrumentation events (e.g., when a query starts or ends). APM tools and tracing libraries subscribe to these channels to collect metrics without monkey-patching. It is designed to be zero-cost when no subscriber is attached.',
                'options'     => [
                    ['text' => 'A pub-sub API for libraries to publish instrumentation events that APM tools can subscribe to', 'correct' => true],
                    ['text' => 'A module for sending diagnostic data and crash reports to a remote monitoring service', 'correct' => false],
                    ['text' => 'A communication channel between Node.js worker threads for diagnostic messages', 'correct' => false],
                    ['text' => 'A built-in log aggregator that channels all console output to a single stream', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `perf_hooks` (PerformanceObserver) used for in Node.js?',
                'explanation' => 'The `perf_hooks` module provides the Performance Measurement API — similar to `performance` in browsers. `performance.now()` gives a high-resolution timestamp. `PerformanceObserver` subscribes to performance entries (marks, measures, GC events, HTTP timings). Use `performance.mark("start")` and `performance.measure("label", "start")` to measure code durations. Useful for profiling critical paths without adding external dependencies.',
                'options'     => [
                    ['text' => 'Provides high-resolution timing and PerformanceObserver for measuring code durations and GC events', 'correct' => true],
                    ['text' => 'A module for monitoring Node.js process CPU and memory usage in real time', 'correct' => false],
                    ['text' => 'A built-in HTTP benchmarking tool for measuring request throughput', 'correct' => false],
                    ['text' => 'A profiler that automatically identifies and logs the slowest database queries', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are Node.js trace events?',
                'explanation' => 'Trace events provide a mechanism for centralizing tracing information generated by V8, Node.js core, and user code. Enable them with `--trace-event-categories` or the `trace_events` module. Trace data is written to a JSON file (compatible with Chrome\'s `chrome://tracing` viewer) and contains V8 runtime events, GC events, async I/O events, and custom user events. They are useful for low-level performance investigation.',
                'options'     => [
                    ['text' => 'A V8/Node.js instrumentation mechanism that records runtime events in a Chrome-compatible trace format', 'correct' => true],
                    ['text' => 'A logging framework that adds timestamps and stack traces to console output', 'correct' => false],
                    ['text' => 'A distributed tracing protocol similar to OpenTelemetry built into Node.js', 'correct' => false],
                    ['text' => 'A module for tracing HTTP requests end-to-end across microservices', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `node --prof` flag do?',
                'explanation' => 'Running `node --prof app.js` enables V8\'s built-in sampling profiler. It records CPU samples at a fixed interval and writes an `isolate-*.log` file when the process exits. Process the log with `node --prof-process isolate-*.log > output.txt` to get a human-readable profile showing which functions consumed the most CPU time. It is the built-in alternative to tools like Clinic.js flame graphs.',
                'options'     => [
                    ['text' => 'Enables V8\'s sampling CPU profiler and writes a log file that can be processed for flame graphs', 'correct' => true],
                    ['text' => 'Enables production mode with all optimisations but disables debugging features', 'correct' => false],
                    ['text' => 'Runs the Node.js process with professional-grade security hardening enabled', 'correct' => false],
                    ['text' => 'Profiles memory usage and generates a heap snapshot at regular intervals', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `node --inspect` enable in Node.js?',
                'explanation' => '`node --inspect app.js` starts the Node.js process with the V8 Inspector Protocol enabled on port 9229. You can then open Chrome DevTools (navigate to `chrome://inspect`) or VS Code to attach a debugger. This allows setting breakpoints, stepping through code, inspecting variables, taking heap snapshots, and profiling CPU — all from a GUI. `--inspect-brk` pauses at the first line so you can debug startup code.',
                'options'     => [
                    ['text' => 'Starts the process with the V8 Inspector on port 9229 so Chrome DevTools or VS Code can attach', 'correct' => true],
                    ['text' => 'Runs the application in inspection mode where all console.log output is suppressed', 'correct' => false],
                    ['text' => 'Enables source map support so TypeScript stack traces show original line numbers', 'correct' => false],
                    ['text' => 'Opens an interactive inspection shell for examining live module exports', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `--require` flag do when starting a Node.js process?',
                'explanation' => 'The `--require` (or `-r`) CLI flag tells Node.js to `require()` a specific module before the main script runs. Example: `node -r dotenv/config app.js` loads `dotenv` and populates `process.env` before `app.js` starts — without modifying `app.js`. It is also used to preload test setup files, TypeScript compilers (`-r ts-node/register`), and instrumentation agents (OpenTelemetry, DataDog).',
                'options'     => [
                    ['text' => 'Preloads a module before the main script runs — useful for dotenv, TypeScript, or APM setup', 'correct' => true],
                    ['text' => 'Requires the user to confirm before the Node.js process is allowed to start', 'correct' => false],
                    ['text' => 'Forces all subsequent require() calls to load fresh copies without using the module cache', 'correct' => false],
                    ['text' => 'Marks a package as required — the process exits if the package is not installed', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `--loader` flag do in Node.js ESM?',
                'explanation' => 'The `--loader` (or `--experimental-loader`) flag specifies a custom ES Module loader hook. Loader hooks allow you to intercept module resolution and loading — for example, to support TypeScript (`--loader ts-node/esm`), custom file extensions, or transforming code at import time. Loaders run in a separate worker thread to keep the main thread clean. This is the ESM equivalent of the CJS `--require` flag.',
                'options'     => [
                    ['text' => 'Specifies a custom ESM loader hook for transforming or intercepting module imports', 'correct' => true],
                    ['text' => 'Loads a shared C++ addon (.node file) before the Node.js main script starts', 'correct' => false],
                    ['text' => 'Configures the module resolver to look in a custom directory instead of node_modules', 'correct' => false],
                    ['text' => 'Enables lazy module loading to reduce startup time by deferring require() calls', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `corepack` in the Node.js ecosystem?',
                'explanation' => '`corepack` is a tool bundled with Node.js 16.9+ that manages package manager versions (npm, yarn, pnpm) on a per-project basis. It reads the `"packageManager"` field in `package.json` (e.g., `"packageManager": "pnpm@8.0.0"`) and transparently uses the correct version for the project — without requiring global installs. Enable it with `corepack enable`. It helps teams ensure everyone uses the same package manager version.',
                'options'     => [
                    ['text' => 'A Node.js-bundled tool that manages package manager versions (npm/yarn/pnpm) per project', 'correct' => true],
                    ['text' => 'A module bundler that packages Node.js applications into a single executable file', 'correct' => false],
                    ['text' => 'A core module package registry that caches packages locally for offline use', 'correct' => false],
                    ['text' => 'A security tool that audits core Node.js packages for known vulnerabilities', 'correct' => false],
                ],
            ],
        ];
    }

    private function advancedQuestions(): array
    {
        return [
            // ── Original 10 ──────────────────────────────────────────────────
            [
                'question'    => 'What is libuv and what role does it play in Node.js?',
                'explanation' => 'libuv is a C library that provides Node.js with its event loop, asynchronous I/O, and thread pool. It abstracts OS-level async APIs (epoll on Linux, kqueue on macOS, IOCP on Windows) to provide a unified async interface. Its internal thread pool (default size 4) handles I/O operations that don\'t have async OS support — like DNS lookups and file I/O. All I/O callbacks flow back through libuv to the V8 JavaScript engine.',
                'options'     => [
                    ['text' => 'A C library providing the event loop, async I/O abstraction, and internal thread pool', 'correct' => true],
                    ['text' => 'The JavaScript engine inside Node.js that compiles and executes code', 'correct' => false],
                    ['text' => 'An npm package for building high-performance UV-mapped images in Node.js', 'correct' => false],
                    ['text' => 'A process manager for keeping Node.js applications alive in production', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the Node.js cluster module and when should you use it?',
                'explanation' => 'The `cluster` module allows you to create child processes (workers) that share the same server port. The master process forks one worker per CPU core using `cluster.fork()`. Each worker runs in its own event loop and OS thread, fully utilizing multi-core CPUs. Since Node.js is single-threaded, a single process can only use one core — clustering is the standard way to scale Node.js across all CPU cores.',
                'options'     => [
                    ['text' => 'Forks worker processes per CPU core to utilize all cores for multi-core scaling', 'correct' => true],
                    ['text' => 'Groups multiple Node.js servers into a cluster for distributed load balancing', 'correct' => false],
                    ['text' => 'A module for clustering database connections to reduce connection overhead', 'correct' => false],
                    ['text' => 'A container orchestration tool for managing Node.js app instances in Docker', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are Worker Threads in Node.js and how do they differ from cluster?',
                'explanation' => 'Worker Threads (`worker_threads` module, Node.js 10.5+) run JavaScript in parallel threads sharing the same process and memory (via SharedArrayBuffer and Atomics). They are ideal for CPU-intensive tasks (encryption, image processing). Cluster forks separate processes (no shared memory, separate V8 heaps) and is designed for scaling network servers across CPU cores. Use Worker Threads for computation, cluster for network scaling.',
                'options'     => [
                    ['text' => 'Worker Threads are in-process threads with shared memory; cluster forks separate processes', 'correct' => true],
                    ['text' => 'Worker Threads are deprecated; cluster is the modern replacement', 'correct' => false],
                    ['text' => 'Both are identical — different APIs for the same OS-level threading mechanism', 'correct' => false],
                    ['text' => 'Worker Threads handle I/O; cluster handles CPU-intensive computation', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is backpressure in Node.js streams and how do you handle it?',
                'explanation' => 'Backpressure occurs when a writable stream\'s buffer is full and cannot accept more data — the readable source is producing data faster than the writable destination can consume it. If ignored, it leads to memory bloat. Handle it by: checking `writable.write()`\'s return value (returns false when buffer is full), pausing the readable stream, and listening for the `"drain"` event before resuming. Using `.pipe()` handles backpressure automatically.',
                'options'     => [
                    ['text' => 'When a writable buffer is full — pause the readable stream, resume on the drain event', 'correct' => true],
                    ['text' => 'When too many HTTP requests overwhelm the server — handled by rate limiting middleware', 'correct' => false],
                    ['text' => 'When database queries are slower than the API response rate', 'correct' => false],
                    ['text' => 'When the event loop queue exceeds 1000 callbacks — Node.js applies backpressure automatically', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a memory leak in Node.js and what are common causes?',
                'explanation' => 'A memory leak occurs when memory is allocated but never released, causing heap usage to grow until the process crashes. Common Node.js causes: global variables that grow unboundedly (arrays/maps holding stale references); event listeners never removed (call `emitter.off()` or `removeAllListeners()`); closures capturing large objects; timers (`setInterval`) not cleared; caches without eviction policies. Use `--inspect` + Chrome DevTools heap snapshots or `clinic.js` to diagnose.',
                'options'     => [
                    ['text' => 'Memory allocated but not freed — caused by growing globals, unremoved listeners, or uncleaned timers', 'correct' => true],
                    ['text' => 'When Node.js runs out of file descriptors due to too many open connections', 'correct' => false],
                    ['text' => 'When the event loop queue accumulates more callbacks than libuv can process', 'correct' => false],
                    ['text' => 'Garbage collection pauses that temporarily freeze the Node.js process', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of `--max-old-space-size` in Node.js?',
                'explanation' => '`--max-old-space-size=<MB>` is a V8 flag that sets the maximum size of the old generation heap (where long-lived objects are stored). Node.js defaults to ~1.5 GB on 64-bit systems. For memory-intensive applications (data processing, SSR), this limit can be increased: `node --max-old-space-size=4096 server.js`. It should be treated as a signal that you may have a memory leak, not a permanent fix.',
                'options'     => [
                    ['text' => 'A V8 flag to set the maximum old-generation heap size for memory-intensive Node.js apps', 'correct' => true],
                    ['text' => 'A flag that limits how much disk space Node.js can use for temporary files', 'correct' => false],
                    ['text' => 'A setting that configures the maximum number of old npm packages to cache', 'correct' => false],
                    ['text' => 'A garbage collection parameter that controls how often the old heap is swept', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `AsyncLocalStorage` class used for in Node.js?',
                'explanation' => '`AsyncLocalStorage` (from the `async_hooks` module) provides a way to store data that persists across async operations within the same execution context — similar to thread-local storage. Common use case: storing a request-scoped context (request ID, user session, logger instance) that is automatically available to all async calls triggered within a single HTTP request — without threading it through every function signature.',
                'options'     => [
                    ['text' => 'Stores context data (e.g. request ID) that persists across all async calls in one execution chain', 'correct' => true],
                    ['text' => 'A local key-value store that persists between Node.js process restarts', 'correct' => false],
                    ['text' => 'An async wrapper around localStorage for Node.js server environments', 'correct' => false],
                    ['text' => 'A mechanism for sharing data between Worker Threads via shared memory', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How does Node.js handle HTTPS and SSL/TLS?',
                'explanation' => 'Node.js has a built-in `https` module for creating HTTPS servers. It wraps the `http` module and adds TLS support via the `tls` module. You provide the SSL certificate and private key: `https.createServer({ key, cert }, app)`. In production, it is common to terminate TLS at a reverse proxy (nginx, AWS ALB) and connect to Node.js via plain HTTP internally, reducing TLS overhead on the application server.',
                'options'     => [
                    ['text' => 'Built-in https module wraps http with TLS; in prod, TLS is often terminated at a reverse proxy', 'correct' => true],
                    ['text' => 'HTTPS is not supported natively — a third-party library (greenlock) is required', 'correct' => false],
                    ['text' => 'Node.js automatically enables HTTPS when the PORT is set to 443', 'correct' => false],
                    ['text' => 'SSL is handled exclusively by the operating system — Node.js has no TLS-specific code', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is graceful shutdown in Node.js and how is it implemented?',
                'explanation' => 'Graceful shutdown means stopping a server without abruptly cutting active connections. When a `SIGTERM` signal is received (from a process manager or container orchestrator), the server stops accepting new connections (`server.close()`), waits for in-flight requests to finish, closes database connections, clears intervals/timeouts, then exits. Without it, active requests are killed mid-flight, causing data corruption or 500 errors.',
                'options'     => [
                    ['text' => 'On SIGTERM, stop accepting new requests, drain in-flight requests, close connections, then exit', 'correct' => true],
                    ['text' => 'Calling process.exit(0) as soon as a SIGTERM is received', 'correct' => false],
                    ['text' => 'Saving the server state to disk so it can resume after restart', 'correct' => false],
                    ['text' => 'Using try/catch around the main server loop to prevent unexpected crashes', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are common Node.js security best practices?',
                'explanation' => 'Key Node.js security practices: validate and sanitize all user input to prevent injection; use `helmet` to set security HTTP headers; rate-limit APIs to prevent DoS; use environment variables for secrets (never hardcode); keep `node_modules` audited (`npm audit`); avoid `eval()` and `new Function()` with user data; set `--max-old-space-size` and use a process manager; run Node.js as a non-root user in production containers.',
                'options'     => [
                    ['text' => 'Validate input, use helmet, rate-limit, store secrets in env vars, audit dependencies', 'correct' => true],
                    ['text' => 'Use HTTPS only — all other security concerns are handled by the operating system', 'correct' => false],
                    ['text' => 'Disable CORS entirely and rely on server-side session validation', 'correct' => false],
                    ['text' => 'Use synchronous I/O to prevent timing attacks via the event loop', 'correct' => false],
                ],
            ],

            // ── 23 New Advanced Questions ─────────────────────────────────────
            [
                'question'    => 'What is N-API (Node-API) and why is it used for native addons?',
                'explanation' => 'N-API (now called Node-API) is a C API for building native addons that is ABI-stable across Node.js versions. Before N-API, addons used the V8 C++ API directly — any Node.js update that changed the V8 ABI broke all addons, requiring recompilation. N-API addons compiled against one Node.js version continue to work on newer versions without recompilation. It simplifies and stabilises native addon development.',
                'options'     => [
                    ['text' => 'An ABI-stable C API for native addons that works across Node.js versions without recompilation', 'correct' => true],
                    ['text' => 'A Node.js package for building native iOS and Android apps using JavaScript', 'correct' => false],
                    ['text' => 'The public REST API provided by the Node.js Foundation for version management', 'correct' => false],
                    ['text' => 'A tool that automatically generates C++ bindings from TypeScript interface definitions', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `node-gyp` and when is it used?',
                'explanation' => '`node-gyp` is a cross-platform build tool for compiling native Node.js addons written in C or C++. It wraps GYP (Generate Your Projects) to produce platform-specific build files (Makefile on Linux, .vcxproj on Windows). It is invoked automatically by `npm install` when a package includes a `binding.gyp` file. It requires Python and a C++ compiler (gcc/clang/MSVC) to be installed on the machine.',
                'options'     => [
                    ['text' => 'A build tool for compiling C/C++ native Node.js addons — invoked by npm when binding.gyp is present', 'correct' => true],
                    ['text' => 'A Node.js debugging tool for tracing garbage collection cycles', 'correct' => false],
                    ['text' => 'A CLI for scaffolding new Node.js project structures using templates', 'correct' => false],
                    ['text' => 'A package for running Python scripts from a Node.js application', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do `SharedArrayBuffer` and `Atomics` work in Node.js Worker Threads?',
                'explanation' => '`SharedArrayBuffer` is a fixed-length raw binary buffer that can be shared between the main thread and Worker Threads without copying — it maps to the same physical memory. `Atomics` provides atomic operations on shared memory (e.g., `Atomics.add`, `Atomics.compareExchange`, `Atomics.wait`, `Atomics.notify`) to prevent race conditions. This is the closest Node.js comes to true shared-memory multithreading.',
                'options'     => [
                    ['text' => 'SharedArrayBuffer maps the same memory across threads; Atomics provides race-free operations on it', 'correct' => true],
                    ['text' => 'SharedArrayBuffer is a thread-safe queue for passing messages between workers', 'correct' => false],
                    ['text' => 'Atomics encrypts data before writing to a SharedArrayBuffer for security', 'correct' => false],
                    ['text' => 'SharedArrayBuffer copies data efficiently between threads using zero-copy serialisation', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are `MessageChannel` and `MessagePort` in Node.js?',
                'explanation' => '`MessageChannel` creates a pair of connected `MessagePort` objects. Each port has a `.postMessage(data)` method and an `"message"` event. Passing a `MessagePort` in a `worker.postMessage()` call (via the `transferList` argument) transfers ownership of that port to the worker, enabling direct bidirectional communication between threads without going through the parent. This is more efficient than routing all messages through the main thread.',
                'options'     => [
                    ['text' => 'MessageChannel creates two linked MessagePorts for direct bidirectional thread-to-thread messaging', 'correct' => true],
                    ['text' => 'MessageChannel is a TCP channel abstraction; MessagePort is a network port number wrapper', 'correct' => false],
                    ['text' => 'They implement the WebSocket protocol within a single Node.js process', 'correct' => false],
                    ['text' => 'MessagePort is a stream that bridges a Worker Thread to a Unix socket', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `BroadcastChannel` in Node.js?',
                'explanation' => '`BroadcastChannel` (available in Node.js 15.4+, mirroring the browser API) allows messages to be broadcast to all instances listening on the same channel name — across Worker Threads and even multiple `vm.createContext()` contexts. Unlike `MessagePort`, there is no need to explicitly pass a port reference. Any thread that creates a `BroadcastChannel("my-channel")` will receive messages posted to that channel name.',
                'options'     => [
                    ['text' => 'Broadcasts messages to all Worker Threads subscribed to the same named channel — no port wiring needed', 'correct' => true],
                    ['text' => 'A module for broadcasting UDP packets to all devices on the local network', 'correct' => false],
                    ['text' => 'A pub-sub system for distributing messages across multiple Node.js server instances', 'correct' => false],
                    ['text' => 'A wrapper around WebSocket that multicasts to all connected browser clients', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `Readable.from()` do in Node.js streams?',
                'explanation' => '`Readable.from(iterable)` creates a `Readable` stream from any synchronous or asynchronous iterable — including arrays, generators, async generators, and async iterables. It is the simplest way to create a readable stream from an in-memory data source or a custom async generator that yields data. Example: `Readable.from(["chunk1", "chunk2"])` creates a stream that emits those two strings.',
                'options'     => [
                    ['text' => 'Creates a Readable stream from any (async) iterable — arrays, generators, or async generators', 'correct' => true],
                    ['text' => 'Reads data from a file path and returns it as a Readable stream', 'correct' => false],
                    ['text' => 'Converts a Promise that resolves to a Buffer into a Readable stream', 'correct' => false],
                    ['text' => 'Clones an existing Readable stream so it can be consumed multiple times', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `stream.pipeline()` and `.pipe()` in Node.js?',
                'explanation' => '`.pipe()` connects streams but does not handle errors — if any stream in the chain errors or is destroyed, the others are not automatically cleaned up, causing resource leaks. `stream.pipeline(source, ...transforms, destination, callback)` connects streams AND ensures all streams are properly destroyed and the callback is called with any error. It is the recommended way to pipe streams in production code.',
                'options'     => [
                    ['text' => 'pipeline handles errors and cleans up all streams on failure; .pipe() does not destroy on error', 'correct' => true],
                    ['text' => '.pipe() supports backpressure; pipeline does not and can overflow the writable buffer', 'correct' => false],
                    ['text' => 'They are identical — pipeline is just a Promise-based wrapper around .pipe()', 'correct' => false],
                    ['text' => '.pipe() is for object mode streams; pipeline is for binary streams only', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `stream.finished()` do in Node.js?',
                'explanation' => '`stream.finished(stream, callback)` calls the callback when a stream is no longer readable, writable, or has experienced an error or premature close. It correctly detects all the ways a stream can end or fail and always calls the callback once — making it the reliable way to know when a stream is truly done. `stream.finished` is used internally by `stream.pipeline`. A Promise-based version is available as `stream.promises.finished(stream)`.',
                'options'     => [
                    ['text' => 'Calls a callback when a stream ends, errors, or closes — the reliable way to detect stream completion', 'correct' => true],
                    ['text' => 'Terminates a stream immediately by destroying it and flushing its internal buffer', 'correct' => false],
                    ['text' => 'Returns a boolean indicating whether a readable stream has been fully consumed', 'correct' => false],
                    ['text' => 'Marks a writable stream as complete so no further writes are accepted', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you implement a Transform stream in Node.js?',
                'explanation' => 'A `Transform` stream is a `Duplex` that reads data, transforms it, and outputs it. Implement by extending `stream.Transform` and overriding the `_transform(chunk, encoding, callback)` method — call `this.push(transformedData)` to output data and `callback()` when done. Optionally override `_flush(callback)` to emit remaining data after the source ends. Examples: gzip compression, JSON parsing, CSV parsing.',
                'options'     => [
                    ['text' => 'Extend stream.Transform, override _transform(chunk, encoding, callback) and push transformed output', 'correct' => true],
                    ['text' => 'Implement a Readable stream that reads and a Writable stream that writes, then connect them with pipe()', 'correct' => false],
                    ['text' => 'Use stream.PassThrough and attach a "data" listener that modifies chunks in place', 'correct' => false],
                    ['text' => 'Call stream.createTransform(fn) with a mapping function — similar to Array.map for streams', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a Duplex stream in Node.js?',
                'explanation' => 'A `Duplex` stream is both `Readable` and `Writable` — data can flow in both directions independently. TCP sockets (`net.Socket`) are the canonical example: you can write data to the server and simultaneously read data coming from the server on the same socket. The readable and writable sides of a Duplex stream are independent — they do not share an internal buffer. Transform streams are a special case of Duplex where output depends on input.',
                'options'     => [
                    ['text' => 'A stream that is both readable and writable with independent buffers — e.g., a TCP socket', 'correct' => true],
                    ['text' => 'A stream that duplicates every written chunk to two separate output streams simultaneously', 'correct' => false],
                    ['text' => 'A bidirectional stream where output equals input — a no-op pass-through', 'correct' => false],
                    ['text' => 'A stream type exclusive to WebSocket connections for full-duplex HTTP communication', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `highWaterMark` in Node.js streams and how does it relate to backpressure?',
                'explanation' => '`highWaterMark` is the buffer size threshold for a stream — the maximum number of bytes (or objects in objectMode) that should be held in the internal buffer before backpressure kicks in. When a Readable\'s buffer exceeds `highWaterMark`, `read()` returns null and the stream pauses. When a Writable\'s buffer exceeds `highWaterMark`, `.write()` returns false, signalling the producer to pause. Default is 16 KB for byte streams and 16 objects for object mode.',
                'options'     => [
                    ['text' => 'The buffer size threshold — when exceeded, streams signal backpressure to pause the producer', 'correct' => true],
                    ['text' => 'The maximum number of concurrent streams allowed per Node.js process', 'correct' => false],
                    ['text' => 'The maximum file size that can be processed by a stream without chunking', 'correct' => false],
                    ['text' => 'A flag that enables high-performance mode, bypassing backpressure checks for speed', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is stream `objectMode` in Node.js?',
                'explanation' => 'By default, Node.js streams work with `Buffer` or strings (binary data). When `objectMode: true` is set, a stream can pass arbitrary JavaScript objects (not just Buffers) through. In object mode, `highWaterMark` counts objects instead of bytes. Object mode streams are used in data processing pipelines where each chunk is a parsed record (e.g., a JSON object from a CSV line) rather than raw bytes.',
                'options'     => [
                    ['text' => 'Allows streams to pass arbitrary JavaScript objects instead of Buffers or strings', 'correct' => true],
                    ['text' => 'Enables object-oriented stream chaining syntax using method calls instead of pipe()', 'correct' => false],
                    ['text' => 'A mode where the stream emits JavaScript objects describing the stream\'s internal state', 'correct' => false],
                    ['text' => 'A security mode that serialises all stream data as JSON to prevent binary injection', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you read a large file line by line in Node.js efficiently?',
                'explanation' => 'Use `readline.createInterface({ input: fs.createReadStream("file.txt") })` and listen to the `"line"` event — each event fires with one line as a string. This is memory-efficient because it reads the file as a stream (not loading it all at once) and processes one line at a time. For async iteration, you can use `for await (const line of rl)` since readline interfaces are async iterables in Node.js 11.4+.',
                'options'     => [
                    ['text' => 'readline.createInterface with a file read stream — listen to the "line" event or use for-await-of', 'correct' => true],
                    ['text' => 'fs.readFileSync().split("\\n") — the sync approach is faster for large files', 'correct' => false],
                    ['text' => 'stream.pipeline with a custom Transform that splits chunks on newlines into an array', 'correct' => false],
                    ['text' => 'Use the built-in lines() method on a ReadStream — available in Node.js 18+', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `dns.lookup` and `dns.resolve` in Node.js?',
                'explanation' => '`dns.lookup(hostname, callback)` uses the OS\'s DNS resolver (getaddrinfo) — it respects `/etc/hosts`, local DNS cache, and nsswitch.conf. It goes through libuv\'s thread pool. `dns.resolve(hostname, type, callback)` sends a direct DNS query over the network using c-ares, bypassing OS settings. `resolve` is more predictable for pure DNS queries; `lookup` matches what a browser or curl would do.',
                'options'     => [
                    ['text' => 'lookup uses the OS resolver (respects /etc/hosts); resolve sends direct DNS queries via c-ares', 'correct' => true],
                    ['text' => 'lookup is async; resolve is synchronous and blocks the event loop', 'correct' => false],
                    ['text' => 'resolve returns all DNS record types; lookup only returns A records', 'correct' => false],
                    ['text' => 'They are identical — dns.resolve is an alias for dns.lookup in modern Node.js', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you create a raw TCP server in Node.js using the `net` module?',
                'explanation' => '`net.createServer(socket => { ... }).listen(port)` creates a TCP server. The callback receives a `net.Socket` (a Duplex stream) for each connection. You read data with the `"data"` event and write with `socket.write()`. This is lower level than HTTP — you define your own protocol on top of TCP. The `http` module builds on `net` under the hood.',
                'options'     => [
                    ['text' => 'net.createServer(socket => { ... }).listen(port) — socket is a Duplex stream for each TCP connection', 'correct' => true],
                    ['text' => 'net.createTCPServer(port, handler) — the handler receives request and response objects', 'correct' => false],
                    ['text' => 'Extend net.Server and override the onConnection(socket) method to handle connections', 'correct' => false],
                    ['text' => 'Use net.bind(port) to create a raw socket, then net.accept() to accept connections in a loop', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How does Socket.IO work under the hood in Node.js?',
                'explanation' => 'Socket.IO is a library that provides real-time, bidirectional communication. It first attempts to establish a WebSocket connection (via the `ws` library). If WebSocket is unavailable, it falls back to long-polling over HTTP. On top of the transport layer, Socket.IO adds a protocol for: rooms (broadcasting to groups), namespaces (logical separation), auto-reconnection, and event acknowledgements. It is not a direct WebSocket implementation — it is an abstraction layer.',
                'options'     => [
                    ['text' => 'Uses WebSocket first, falls back to HTTP long-polling; adds rooms, namespaces, and auto-reconnect on top', 'correct' => true],
                    ['text' => 'Socket.IO is a pure WebSocket library — it does not support HTTP fallback', 'correct' => false],
                    ['text' => 'It uses UDP under the hood for low-latency real-time message delivery', 'correct' => false],
                    ['text' => 'Socket.IO runs on the client only — the server uses the raw ws WebSocket module', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `http2` module in Node.js and what advantages does it offer?',
                'explanation' => 'The `http2` module implements HTTP/2, which offers: multiplexing (multiple requests over a single TCP connection eliminating head-of-line blocking); header compression (HPACK); server push (proactively sending resources before the client requests them); and binary framing (more efficient than HTTP/1.1\'s text-based format). Use `http2.createSecureServer({ key, cert }, handler)` to create an HTTP/2 server. Most frameworks support HTTP/2 via this module.',
                'options'     => [
                    ['text' => 'Implements HTTP/2 with multiplexing, header compression, server push, and binary framing', 'correct' => true],
                    ['text' => 'An upgraded http module with better error handling and async/await support built in', 'correct' => false],
                    ['text' => 'A module for HTTP version 2 of the npm registry protocol', 'correct' => false],
                    ['text' => 'HTTP/2 is not supported natively — it requires the h2 npm package', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is QUIC protocol support in Node.js?',
                'explanation' => 'QUIC is a transport protocol developed by Google (now an IETF standard) that runs over UDP and forms the basis of HTTP/3. It provides features of TCP (reliability, ordering, flow control) plus TLS 1.3 encryption built-in, 0-RTT connection establishment, and eliminates head-of-line blocking at the transport level. Node.js has been adding QUIC support experimentally. HTTP/3 clients and servers built on QUIC promise lower latency than HTTP/2 over TCP.',
                'options'     => [
                    ['text' => 'A UDP-based transport protocol underpinning HTTP/3 — offers 0-RTT, built-in TLS, and no HOL blocking', 'correct' => true],
                    ['text' => 'A Node.js queue system for managing concurrent HTTP requests without blocking', 'correct' => false],
                    ['text' => 'A quick-connect mode for Node.js that reuses existing TCP connections for new requests', 'correct' => false],
                    ['text' => 'A caching protocol that stores HTTP responses in memory for sub-millisecond repeat access', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are Single Executable Applications (SEA) in Node.js?',
                'explanation' => 'Single Executable Applications (SEA), introduced experimentally in Node.js 20, allow you to package a Node.js application into a single self-contained executable binary — embedding the Node.js runtime and your application code together. This removes the need for Node.js to be installed on the target machine. You create a SEA by injecting a "blob" (your bundled JS) into the Node.js binary using `postject`. The result is a single `.exe` or ELF binary.',
                'options'     => [
                    ['text' => 'Package a Node.js app and runtime into a single self-contained executable binary — no Node.js install needed', 'correct' => true],
                    ['text' => 'A mode where the Node.js server handles one request at a time for predictable performance', 'correct' => false],
                    ['text' => 'An npm feature that installs a package as a global executable with a single command', 'correct' => false],
                    ['text' => 'A Docker image format that packages a Node.js app as a minimal single-layer container', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the Node.js permission model and how is `--allow-fs-read` used?',
                'explanation' => 'The Node.js permission model (experimental, Node.js 20+) restricts what resources a Node.js process can access — similar to Deno\'s security model. Without `--experimental-permission`, Node.js has no restrictions. With it, you must explicitly grant permissions. `--allow-fs-read=<path>` allows reading only the specified path (or `*` for all). Other flags: `--allow-fs-write`, `--allow-child-process`, `--allow-worker`, `--allow-net`. Denied operations throw `ERR_ACCESS_DENIED`.',
                'options'     => [
                    ['text' => 'An experimental security model that restricts file/network/process access; --allow-fs-read grants specific read paths', 'correct' => true],
                    ['text' => 'A Linux file permission flag passed to Node.js to run as a read-only user account', 'correct' => false],
                    ['text' => 'A flag that makes fs.readFileSync safe for concurrent use by enabling file locks', 'correct' => false],
                    ['text' => 'A performance optimisation that pre-caches allowed file reads at process startup', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How does CJS/ESM interoperability work in Node.js?',
                'explanation' => 'CommonJS can `require()` an ESM file with a dynamic `import()` call (since `require()` is synchronous and ESM loading is asynchronous, static `require()` of `.mjs` is not allowed). ESM can `import` CJS modules — but only the `module.exports` value as the default export (named exports from CJS are not statically analysable). Dual-package hazard: a package that ships both CJS and ESM versions may load twice, causing bugs with singleton state.',
                'options'     => [
                    ['text' => 'ESM can import CJS (default export only); CJS cannot statically require ESM — must use dynamic import()', 'correct' => true],
                    ['text' => 'CJS and ESM cannot be mixed in the same project — choose one module system', 'correct' => false],
                    ['text' => 'Both systems are fully interoperable — require() and import are synonyms in Node.js 18+', 'correct' => false],
                    ['text' => 'ESM modules cannot import CJS packages — use a bundler to convert CJS to ESM first', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is top-level `await` in Node.js ESM modules?',
                'explanation' => 'Top-level `await` allows you to use the `await` keyword directly at the top level of an ES Module file — without wrapping it in an `async` function. This is only supported in ESM (`.mjs` files or `"type": "module"` packages). It is useful for module initialization that requires async work: loading config, connecting to a database, or fetching remote data at startup. Modules that import a top-level awaiting module will wait for it to resolve.',
                'options'     => [
                    ['text' => 'Allows using await at the module\'s top level in ESM — the importing module waits for it to resolve', 'correct' => true],
                    ['text' => 'A Node.js flag that makes all async functions execute synchronously at the top level', 'correct' => false],
                    ['text' => 'An experimental feature that allows await inside CommonJS require() calls', 'correct' => false],
                    ['text' => 'A V8 optimisation that automatically hoists await calls to the top of async functions', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `import.meta.resolve()` do in Node.js ESM?',
                'explanation' => '`import.meta.resolve(specifier)` resolves a module specifier (e.g., a package name or relative path) to its fully-resolved absolute URL — using the same resolution algorithm as `import`. It returns a string URL (e.g., `file:///path/to/module.js`). This is the ESM equivalent of `require.resolve()` in CommonJS. It is useful for custom loaders, finding the path to a dependency\'s files, or building tools that manipulate module paths.',
                'options'     => [
                    ['text' => 'Resolves a module specifier to its absolute file URL — the ESM equivalent of require.resolve()', 'correct' => true],
                    ['text' => 'Returns the metadata object associated with the current module\'s import statement', 'correct' => false],
                    ['text' => 'Dynamically imports a module and returns a Promise resolving to the module namespace', 'correct' => false],
                    ['text' => 'Validates that a specifier points to an existing module without actually loading it', 'correct' => false],
                ],
            ],
        ];
    }
}
