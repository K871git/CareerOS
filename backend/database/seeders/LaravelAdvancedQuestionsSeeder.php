<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;

class LaravelAdvancedQuestionsSeeder extends Seeder
{
    public function run(): void
    {
        $track = LearningTrack::firstOrCreate(
            ['slug' => 'backend-engineering'],
            ['title' => 'Backend Engineering', 'description' => 'Backend engineering track.', 'display_order' => 3]
        );

        $subject = Subject::firstOrCreate(
            ['slug' => 'laravel'],
            ['learning_track_id' => $track->id, 'title' => 'Laravel', 'description' => 'Laravel framework practice questions.', 'display_order' => 3]
        );

        $topic = Topic::firstOrCreate(
            ['slug' => 'laravel-advanced'],
            ['subject_id' => $subject->id, 'title' => 'Laravel Advanced', 'description' => 'Advanced Laravel: design patterns, Horizon, Telescope, testing, Policies, Contracts, and performance.', 'display_order' => 3]
        );

        Question::where('topic_id', $topic->id)->delete();

        foreach ($this->questions() as $qData) {
            $q = Question::create([
                'topic_id'    => $topic->id,
                'type'        => 'MCQ',
                'difficulty'  => 'Hard',
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
        $this->command->info("Laravel Advanced: {$count} questions total.");
    }

    private function questions(): array
    {
        return [
            // DESIGN PATTERNS
            [
                'question' => 'What is the Repository pattern and why would you use it in a Laravel application?',
                'explanation' => 'The Repository pattern abstracts data access behind an interface. Controllers and services depend on the interface (not Eloquent directly), making it easy to swap implementations, mock in tests, and enforce the Single Responsibility Principle.',
                'options' => [
                    ['text' => 'Abstracts data access behind an interface, decoupling business logic from Eloquent and improving testability', 'correct' => true],
                    ['text' => 'A Laravel package that provides a local Git repository for your codebase', 'correct' => false],
                    ['text' => 'A caching layer that stores frequently used Eloquent queries', 'correct' => false],
                    ['text' => 'A pattern for grouping related controllers into namespaced folders', 'correct' => false],
                ],
            ],
            [
                'question' => 'What are Laravel Contracts?',
                'explanation' => 'Contracts are PHP interfaces that define the core services Laravel provides (Cache, Queue, Mail, etc.). Programming against Contracts decouples your code from specific implementations and makes swapping implementations trivial.',
                'options' => [
                    ['text' => 'PHP interfaces defining core framework services, allowing loose coupling from specific implementations', 'correct' => true],
                    ['text' => 'Database constraints like foreign keys and unique indexes defined in migrations', 'correct' => false],
                    ['text' => 'Service level agreements for Laravel Forge deployment uptime', 'correct' => false],
                    ['text' => 'Configuration files that define which packages are bound to the container', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is a custom Facade in Laravel?',
                'explanation' => 'A Facade provides a static, expressive interface to classes registered in the Service Container. A custom Facade extends `Illuminate\\Support\\Facades\\Facade` and implements `getFacadeAccessor()` returning the container binding key.',
                'options' => [
                    ['text' => 'A static proxy to a class registered in the Service Container, extending the Facade base class', 'correct' => true],
                    ['text' => 'A design pattern that hides all implementation details behind a simple HTML interface', 'correct' => false],
                    ['text' => 'A middleware class that decorates responses with CORS headers', 'correct' => false],
                    ['text' => 'An abstract controller providing shared logic for related controllers', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is the Pipeline pattern in Laravel and where is it used?',
                'explanation' => 'The Pipeline pattern passes an object through a series of "pipes" (handlers) in sequence. Laravel\'s HTTP middleware stack is a pipeline — each middleware either passes the request to the next pipe or returns early. Available as `app(Pipeline::class)`.',
                'options' => [
                    ['text' => 'Passes an object through a series of handlers in sequence — used by the middleware stack and available via Pipeline::class', 'correct' => true],
                    ['text' => 'A pattern for streaming large files to the browser without buffering', 'correct' => false],
                    ['text' => 'A CI/CD pipeline configuration integrated into Laravel', 'correct' => false],
                    ['text' => 'A pattern for queueing jobs in a sequential first-in-first-out order', 'correct' => false],
                ],
            ],
            // AUTHORIZATION
            [
                'question' => 'What is the difference between Gates and Policies in Laravel authorization?',
                'explanation' => 'Gates are closure-based authorization checks for simple cases not tied to a model. Policies are classes that organize authorization logic around a specific Eloquent model. Use Policies when you have many model-specific rules.',
                'options' => [
                    ['text' => 'Gates are closures for simple checks; Policies are classes organizing model-specific authorization rules', 'correct' => true],
                    ['text' => 'Gates handle authentication; Policies handle authorization', 'correct' => false],
                    ['text' => 'Policies are for API routes; Gates are for web routes', 'correct' => false],
                    ['text' => 'Both are identical; Policy is just the OOP version of Gate', 'correct' => false],
                ],
            ],
            [
                'question' => 'How do you generate a Policy class in Laravel?',
                'explanation' => '`php artisan make:policy PostPolicy --model=Post` generates a Policy class pre-filled with methods for each model action (viewAny, view, create, update, delete, restore, forceDelete).',
                'options' => [
                    ['text' => 'php artisan make:policy PostPolicy --model=Post', 'correct' => true],
                    ['text' => 'php artisan make:gate PostPolicy --model=Post', 'correct' => false],
                    ['text' => 'php artisan policy:create PostPolicy Post', 'correct' => false],
                    ['text' => 'php artisan authorization:policy Post', 'correct' => false],
                ],
            ],
            // TESTING
            [
                'question' => 'What is the difference between Feature tests and Unit tests in Laravel?',
                'explanation' => 'Feature tests test the full application stack — they boot the framework, hit real routes, use the database, and assert HTTP responses. Unit tests test isolated PHP classes in pure isolation without booting Laravel.',
                'options' => [
                    ['text' => 'Feature tests hit the full HTTP/framework stack; Unit tests test isolated classes without booting Laravel', 'correct' => true],
                    ['text' => 'Unit tests require a database; Feature tests use mocks only', 'correct' => false],
                    ['text' => 'Feature tests are for frontend behavior; Unit tests are for backend', 'correct' => false],
                    ['text' => 'Both are identical; the difference is only in file location', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does the RefreshDatabase trait do in Laravel tests?',
                'explanation' => 'The `RefreshDatabase` trait runs migrations before tests and wraps each test in a database transaction that rolls back after the test, leaving a clean state for the next test. It avoids the overhead of re-migrating for every test.',
                'options' => [
                    ['text' => 'Migrates the database fresh before tests and wraps each test in a transaction that rolls back after', 'correct' => true],
                    ['text' => 'Clears the database cache before each test method', 'correct' => false],
                    ['text' => 'Uses a SQLite in-memory database for all tests regardless of config', 'correct' => false],
                    ['text' => 'Reloads all seeders before each test to ensure consistent data', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does withoutExceptionHandling() do in a Laravel feature test?',
                'explanation' => '`withoutExceptionHandling()` disables Laravel\'s exception handler for the test. Instead of converting exceptions to HTTP error responses, they bubble up as raw PHP exceptions — making test failures easier to debug.',
                'options' => [
                    ['text' => 'Disables the exception handler so exceptions bubble up as raw errors instead of HTTP responses', 'correct' => true],
                    ['text' => 'Prevents the test from catching expected exceptions', 'correct' => false],
                    ['text' => 'Marks the test as expected to throw an exception', 'correct' => false],
                    ['text' => 'Disables try/catch blocks in all controllers during the test', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is Event::fake() used for in Laravel tests?',
                'explanation' => '`Event::fake()` prevents real event listeners from running during a test. After your code executes, you can assert that specific events were dispatched using `Event::assertDispatched(OrderShipped::class)`.',
                'options' => [
                    ['text' => 'Prevents real event listeners from firing and allows asserting events were dispatched', 'correct' => true],
                    ['text' => 'Creates fake event objects to pass into listener constructors', 'correct' => false],
                    ['text' => 'Automatically fires all events registered in EventServiceProvider', 'correct' => false],
                    ['text' => 'Replaces all event listeners with no-op stubs', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is Queue::fake() used for in Laravel tests?',
                'explanation' => '`Queue::fake()` prevents jobs from actually being dispatched to a queue worker. You can then assert that a job was (or was not) pushed to the queue using `Queue::assertPushed(SendWelcomeEmail::class)`.',
                'options' => [
                    ['text' => 'Prevents jobs from running on a real queue and allows asserting they were dispatched', 'correct' => true],
                    ['text' => 'Creates a fake queue connection using an array driver', 'correct' => false],
                    ['text' => 'Runs all queued jobs synchronously during tests', 'correct' => false],
                    ['text' => 'Clears all pending jobs from the test queue', 'correct' => false],
                ],
            ],
            // PERFORMANCE
            [
                'question' => 'What does php artisan route:cache do and when should you use it?',
                'explanation' => '`route:cache` compiles all routes into a single cached file for faster lookup. Use it in production to improve performance. Do NOT use it during development because changes to route files will not be reflected until you clear the cache.',
                'options' => [
                    ['text' => 'Compiles routes into a cached file for faster lookup — use in production, not in development', 'correct' => true],
                    ['text' => 'Caches route-specific HTTP responses for faster repeated requests', 'correct' => false],
                    ['text' => 'Stores all route parameters in a shared memory cache', 'correct' => false],
                    ['text' => 'Validates and pre-compiles middleware for each route', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does php artisan config:cache do and what is its key limitation?',
                'explanation' => '`config:cache` merges all config files into a single cached file for faster reads. The key limitation: `env()` calls in config files work, but calling `env()` OUTSIDE of config files will always return null after caching.',
                'options' => [
                    ['text' => 'Caches all config files for faster reads; after caching, env() outside config files returns null', 'correct' => true],
                    ['text' => 'Encrypts all configuration values before caching them to disk', 'correct' => false],
                    ['text' => 'Caches the database configuration only, ignoring other config files', 'correct' => false],
                    ['text' => 'Forces all env() calls to return cached values from the previous boot', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is php artisan optimize and what does it combine?',
                'explanation' => '`php artisan optimize` runs `config:cache`, `route:cache`, and `view:cache` together in one command. It is the standard production deployment step for caching all framework-generated assets.',
                'options' => [
                    ['text' => 'Runs config:cache, route:cache, and view:cache together — the standard production caching command', 'correct' => true],
                    ['text' => 'Minifies PHP files and removes comments for faster parsing', 'correct' => false],
                    ['text' => 'Runs database query optimization and adds missing indexes', 'correct' => false],
                    ['text' => 'Compiles all Blade views to bytecode for faster rendering', 'correct' => false],
                ],
            ],
            // HORIZON & TELESCOPE
            [
                'question' => 'What is Laravel Horizon?',
                'explanation' => 'Laravel Horizon is a dashboard and queue manager for Redis-backed queues. It provides real-time monitoring of queue throughput, job metrics, failed jobs, and queue worker configuration via a config file.',
                'options' => [
                    ['text' => 'A dashboard and queue manager for Redis queues with real-time metrics, job monitoring, and config-driven workers', 'correct' => true],
                    ['text' => 'A cloud hosting platform for deploying Laravel applications', 'correct' => false],
                    ['text' => 'A Laravel package for managing multi-tenant applications', 'correct' => false],
                    ['text' => 'A real-time broadcasting server for WebSocket connections', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is Laravel Telescope?',
                'explanation' => 'Telescope is an elegant debugging assistant for Laravel. It records requests, exceptions, log entries, database queries, queued jobs, mail, events, and more — providing a web UI to inspect them. Typically used in local/staging environments.',
                'options' => [
                    ['text' => 'A debugging tool that records and displays requests, exceptions, queries, jobs, and mail in a web UI', 'correct' => true],
                    ['text' => 'A testing framework for writing end-to-end browser tests', 'correct' => false],
                    ['text' => 'A distributed tracing tool for microservices architectures', 'correct' => false],
                    ['text' => 'A package for managing application feature flags', 'correct' => false],
                ],
            ],
            // GLOBAL SCOPES
            [
                'question' => 'What are global scopes in Eloquent?',
                'explanation' => 'Global scopes automatically add constraints to every query for a model. Laravel\'s SoftDeletes trait uses a global scope to exclude soft-deleted records. You can define your own by implementing the `Scope` interface.',
                'options' => [
                    ['text' => 'Constraints automatically applied to all queries for a model (e.g., SoftDeletes excludes deleted records)', 'correct' => true],
                    ['text' => 'Scopes that are shared and reusable across multiple model classes', 'correct' => false],
                    ['text' => 'Security rules that restrict which users can query a model', 'correct' => false],
                    ['text' => 'PHP global variables that store the current query builder state', 'correct' => false],
                ],
            ],
            // ELOQUENT ADVANCED
            [
                'question' => 'What does the $with property on an Eloquent model do?',
                'explanation' => 'The `$with` array defines relationships that are always eager-loaded whenever the model is queried. This ensures those relationships are always available without having to remember to call `with()` each time.',
                'options' => [
                    ['text' => 'Defines relationships that are always eager-loaded on every query for this model', 'correct' => true],
                    ['text' => 'Defines which attributes are included in JSON output', 'correct' => false],
                    ['text' => 'Defines which columns are included in SELECT queries by default', 'correct' => false],
                    ['text' => 'Defines which related models can be attached via pivot tables', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is the difference between the creating and created Eloquent model events?',
                'explanation' => '`creating` fires BEFORE the model is saved to the database (you can still modify attributes or cancel the save). `created` fires AFTER the model is successfully persisted — the model has an ID at this point.',
                'options' => [
                    ['text' => 'creating fires before the record is saved (no ID yet); created fires after (ID exists)', 'correct' => true],
                    ['text' => 'creating fires for bulk inserts; created fires for single record inserts', 'correct' => false],
                    ['text' => 'Both fire at the same time but in different listener queues', 'correct' => false],
                    ['text' => 'created fires before save; creating fires after save', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does cursor() return in Eloquent and why is it memory-efficient?',
                'explanation' => '`cursor()` returns a `LazyCollection` that uses PHP generators under the hood — records are fetched from the database one at a time. Unlike `get()` which loads all records into memory, `cursor()` keeps only the current record in memory at any point.',
                'options' => [
                    ['text' => 'A LazyCollection using generators — fetches one record at a time, keeping only the current record in memory', 'correct' => true],
                    ['text' => 'A database cursor object that lets you scroll through results with seek()', 'correct' => false],
                    ['text' => 'The same as chunk() but returns all results at once in a single array', 'correct' => false],
                    ['text' => 'A pagination cursor for cursor-based pagination without offsets', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is eager loading with constraints in Eloquent?',
                'explanation' => 'You can constrain what gets eager-loaded by passing a closure to `with()`. For example, `Post::with([\'comments\' => fn($q) => $q->where(\'approved\', true)])->get()` only loads approved comments.',
                'options' => [
                    ['text' => "Passing a closure to with() to filter what gets loaded: Post::with(['comments' => fn(\$q) => \$q->where('approved', true)])", 'correct' => true],
                    ['text' => 'Using withCount() instead of with() for better performance', 'correct' => false],
                    ['text' => 'Applying global scopes to relationships automatically', 'correct' => false],
                    ['text' => 'Restricting eager loading to specific user roles', 'correct' => false],
                ],
            ],
            // ARTISAN COMMANDS
            [
                'question' => 'How do you create a custom Artisan console command?',
                'explanation' => '`php artisan make:command SendDailyReport` generates a Command class with a `$signature` (the command name and arguments) and a `handle()` method containing the command logic.',
                'options' => [
                    ['text' => 'php artisan make:command SendDailyReport', 'correct' => true],
                    ['text' => 'php artisan create:command SendDailyReport', 'correct' => false],
                    ['text' => 'php artisan console:make SendDailyReport', 'correct' => false],
                    ['text' => 'php artisan new:command SendDailyReport', 'correct' => false],
                ],
            ],
            // RATE LIMITING
            [
                'question' => 'How do you define a named rate limiter in Laravel?',
                'explanation' => '`RateLimiter::for()` is defined in a Service Provider and lets you name a rate limiter with custom logic (e.g., per user). You then apply it with the `throttle:name` middleware on a route.',
                'options' => [
                    ['text' => "RateLimiter::for('api', fn(Request \$r) => Limit::perMinute(60)->by(\$r->user()?->id))", 'correct' => true],
                    ['text' => "Route::rateLimit(60, 'per_minute', fn() => ...)", 'correct' => false],
                    ['text' => "Middleware::throttle(60)->name('api')", 'correct' => false],
                    ['text' => "config(['ratelimiter.api' => 60])", 'correct' => false],
                ],
            ],
            // DEFERRED PROVIDERS
            [
                'question' => 'What is a deferred Service Provider in Laravel?',
                'explanation' => 'A deferred provider delays registration until one of its provided services is actually requested. It implements the `DeferrableProvider` interface and returns its bindings from `provides()`. This speeds up bootstrapping by not loading providers that are not needed for every request.',
                'options' => [
                    ['text' => 'A provider that delays loading until one of its services is requested, reducing bootstrap time', 'correct' => true],
                    ['text' => 'A provider that loads after the HTTP response is sent to the client', 'correct' => false],
                    ['text' => 'A provider that is only registered during queue worker processes', 'correct' => false],
                    ['text' => 'A provider loaded asynchronously using PHP fibers', 'correct' => false],
                ],
            ],
            // MISC ADVANCED
            [
                'question' => 'What does dispatchAfterResponse() do when dispatching a job?',
                'explanation' => '`dispatch(new Job)->afterResponse()` (or `dispatchAfterResponse(new Job)`) queues the job to run AFTER the HTTP response has been sent to the client. The user gets an instant response while the job runs immediately after in the same PHP process.',
                'options' => [
                    ['text' => 'Dispatches the job after the HTTP response is sent to the client — same process, no queue worker needed', 'correct' => true],
                    ['text' => 'Dispatches the job to a delayed queue after a response timeout', 'correct' => false],
                    ['text' => 'Sends the job to a remote server after the local server responds', 'correct' => false],
                    ['text' => 'Dispatches the job only after a previous response has been cached', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is the tap() helper in Laravel?',
                'explanation' => '`tap($value, fn($v) => ...)` passes `$value` to the callback, then returns `$value` unchanged. It allows you to perform a side effect (like saving a model) inside a method chain without breaking the chain.',
                'options' => [
                    ['text' => 'Passes a value to a callback for a side effect, then returns the original value unchanged', 'correct' => true],
                    ['text' => 'Taps into a database connection to run a raw query', 'correct' => false],
                    ['text' => 'Listens to all model events and logs them', 'correct' => false],
                    ['text' => 'Is an alias for dump() that does not stop execution', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is method spoofing in Laravel HTML forms?',
                'explanation' => 'HTML forms only support GET and POST. To use PUT, PATCH, or DELETE, Laravel reads a hidden `_method` field. The `@method(\'PUT\')` Blade directive generates this field, and Laravel\'s middleware converts the request method accordingly.',
                'options' => [
                    ['text' => 'Using @method("PUT") to add a hidden _method field since HTML forms only support GET/POST', 'correct' => true],
                    ['text' => 'Overriding a controller method by calling it from a different route', 'correct' => false],
                    ['text' => 'Spoofing the HTTP host header to bypass CORS restrictions', 'correct' => false],
                    ['text' => 'Changing the HTTP method of an API request from the client side', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is a macro in Laravel and how do you define one?',
                'explanation' => 'A macro adds a custom method to an existing class at runtime without modifying it, using the `macro()` method on macroable classes (Builder, Collection, Response, etc.). Defined in a Service Provider boot method.',
                'options' => [
                    ['text' => 'A custom method added to a macroable class at runtime (e.g., Collection::macro(), Builder::macro()) — defined in a provider', 'correct' => true],
                    ['text' => 'A PHP preprocessor directive that generates code before compilation', 'correct' => false],
                    ['text' => 'A Blade directive registered with Blade::directive()', 'correct' => false],
                    ['text' => 'A named query stored in the database and called via Eloquent', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is the purpose of database indexes in migrations and how do you add one?',
                'explanation' => 'Indexes dramatically speed up SELECT queries on indexed columns by avoiding full table scans. In migrations, use `$table->index(\'email\')` for a standard index or `$table->unique(\'email\')` for a unique index.',
                'options' => [
                    ['text' => 'Speed up queries on columns using $table->index("email") or $table->unique("email") in migrations', 'correct' => true],
                    ['text' => 'Lock rows during updates to prevent concurrent modification', 'correct' => false],
                    ['text' => 'Compress column data to reduce storage space', 'correct' => false],
                    ['text' => 'Define default sort order for Eloquent queries on that table', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is the difference between Mail::fake() and its assertion methods in tests?',
                'explanation' => '`Mail::fake()` prevents real emails from being sent during a test. After execution, you use `Mail::assertSent(WelcomeEmail::class)` to verify the mail was queued, `Mail::assertQueued()` for queued mail, or `Mail::assertNothingSent()` to confirm no mail was sent.',
                'options' => [
                    ['text' => 'Mail::fake() intercepts all mail; use assertSent(), assertQueued(), or assertNothingSent() to verify', 'correct' => true],
                    ['text' => 'Mail::fake() sends to a fake SMTP server that records all messages', 'correct' => false],
                    ['text' => 'Mail::fake() is identical to Mail::pretend() which logs but does not send', 'correct' => false],
                    ['text' => 'Mail::fake() redirects all mail to the address set in config(\'mail.fake_to\')', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is the withExists() method in Eloquent?',
                'explanation' => '`withExists(\'comments\')` adds a boolean `comments_exists` attribute to each model result — true if at least one related comment exists. It is more efficient than withCount() when you only need to know IF a relation exists, not how many.',
                'options' => [
                    ['text' => 'Adds a boolean {relation}_exists attribute indicating if the relationship exists without loading it', 'correct' => true],
                    ['text' => 'Checks if the model exists in the database before running the query', 'correct' => false],
                    ['text' => 'Throws an exception if the specified relationship does not exist on the model', 'correct' => false],
                    ['text' => 'Filters the query to only return models where the relationship exists', 'correct' => false],
                ],
            ],
        ];
    }
}
