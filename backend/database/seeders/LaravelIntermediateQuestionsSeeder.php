<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;

class LaravelIntermediateQuestionsSeeder extends Seeder
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
            ['slug' => 'laravel-intermediate'],
            ['subject_id' => $subject->id, 'title' => 'Laravel Intermediate', 'description' => 'Intermediate Laravel: Eloquent relationships, Service Container, validation, API Resources, queues, and more.', 'display_order' => 2]
        );

        foreach ($this->questions() as $qData) {
            $exists = Question::where('topic_id', $topic->id)
                ->where('question', $qData['question'])
                ->exists();
            if ($exists) continue;

            $q = Question::create([
                'topic_id'    => $topic->id,
                'type'        => 'MCQ',
                'difficulty'  => 'Medium',
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
        $this->command->info("Laravel Intermediate: {$count} questions total.");
    }

    private function questions(): array
    {
        return [
            // N+1 / EAGER LOADING
            [
                'question' => 'What is the N+1 query problem in Eloquent?',
                'explanation' => 'The N+1 problem occurs when you fetch N models and then loop through them accessing a relationship, causing 1 query for the parent collection plus N additional queries (one per model). Fix it with eager loading using with().',
                'options' => [
                    ['text' => 'Fetching N models then accessing a relationship in a loop, causing 1 + N queries instead of 2', 'correct' => true],
                    ['text' => 'A performance bug where a query returns N more rows than expected', 'correct' => false],
                    ['text' => 'An error that occurs when a model has more than N relationships defined', 'correct' => false],
                    ['text' => 'A situation where N migrations try to run simultaneously', 'correct' => false],
                ],
            ],
            [
                'question' => 'How do you solve the N+1 query problem in Eloquent?',
                'explanation' => 'Use eager loading with `with()` to load the relationship upfront in a single additional query. `Post::with(\'comments\')->get()` runs 2 queries total instead of N+1.',
                'options' => [
                    ['text' => "Post::with('comments')->get()", 'correct' => true],
                    ['text' => "Post::join('comments', ...)->get()", 'correct' => false],
                    ['text' => "Post::all()->load('comments')", 'correct' => false],
                    ['text' => "Post::eager('comments')->get()", 'correct' => false],
                ],
            ],
            [
                'question' => 'What is the difference between with() and load() in Eloquent?',
                'explanation' => '`with()` is eager loading: it loads relationships before the query runs (in the initial query call). `load()` is lazy eager loading: it loads relationships on already-fetched model instances, useful when you decide you need a relationship after the fact.',
                'options' => [
                    ['text' => 'with() loads relationships before the query; load() loads relationships on already-fetched models', 'correct' => true],
                    ['text' => 'with() is for one-to-many; load() is for many-to-many relationships', 'correct' => false],
                    ['text' => 'load() is faster than with() because it uses database joins', 'correct' => false],
                    ['text' => 'Both do the same thing; load() is just older syntax', 'correct' => false],
                ],
            ],
            // ELOQUENT RELATIONSHIPS
            [
                'question' => 'How do you define a hasMany relationship in Eloquent?',
                'explanation' => 'A `hasMany` relationship is defined in the parent model. A User hasMany Posts means one user can have many posts. Eloquent looks for a `user_id` foreign key on the `posts` table by default.',
                'options' => [
                    ['text' => 'public function posts() { return $this->hasMany(Post::class); }', 'correct' => true],
                    ['text' => 'public function posts() { return $this->manyPosts(Post::class); }', 'correct' => false],
                    ['text' => 'public function posts() { return $this->belongsToMany(Post::class); }', 'correct' => false],
                    ['text' => 'public function posts() { return $this->has(Post::class, many: true); }', 'correct' => false],
                ],
            ],
            [
                'question' => 'How do you define a belongsTo relationship in Eloquent?',
                'explanation' => '`belongsTo` is defined in the child model. A Post belongsTo a User means the posts table has a `user_id` foreign key. Eloquent uses this foreign key to find the related User.',
                'options' => [
                    ['text' => 'public function user() { return $this->belongsTo(User::class); }', 'correct' => true],
                    ['text' => 'public function user() { return $this->hasOne(User::class); }', 'correct' => false],
                    ['text' => 'public function user() { return $this->ownedBy(User::class); }', 'correct' => false],
                    ['text' => 'public function user() { return $this->parentOf(User::class); }', 'correct' => false],
                ],
            ],
            [
                'question' => 'How do you define a belongsToMany relationship in Eloquent?',
                'explanation' => '`belongsToMany` is for many-to-many relationships. It requires a pivot (junction) table. A Post can have many Tags, and a Tag can belong to many Posts, joined through a `post_tag` pivot table.',
                'options' => [
                    ['text' => 'public function tags() { return $this->belongsToMany(Tag::class); }', 'correct' => true],
                    ['text' => 'public function tags() { return $this->hasMany(Tag::class)->through(PostTag::class); }', 'correct' => false],
                    ['text' => 'public function tags() { return $this->manyToMany(Tag::class); }', 'correct' => false],
                    ['text' => 'public function tags() { return $this->pivot(Tag::class); }', 'correct' => false],
                ],
            ],
            [
                'question' => 'How do you attach a record in a belongsToMany relationship?',
                'explanation' => '`attach()` inserts a row into the pivot table linking the two models. `detach()` removes it. `sync()` sets the pivot table to match exactly the given array of IDs.',
                'options' => [
                    ['text' => '$post->tags()->attach($tagId)', 'correct' => true],
                    ['text' => '$post->tags()->add($tagId)', 'correct' => false],
                    ['text' => '$post->tags()->connect($tagId)', 'correct' => false],
                    ['text' => '$post->tags()->link($tagId)', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does sync() do on a belongsToMany relationship?',
                'explanation' => '`sync([1, 2, 3])` updates the pivot table so it contains only the given IDs — adding rows for new IDs and removing rows for IDs not in the array. It is the "set exactly this list" operation.',
                'options' => [
                    ['text' => 'Updates the pivot table to contain exactly the given array of IDs, adding and removing as needed', 'correct' => true],
                    ['text' => 'Adds all given IDs to the pivot table without removing existing ones', 'correct' => false],
                    ['text' => 'Synchronizes the related model table with the current model\'s data', 'correct' => false],
                    ['text' => 'Refreshes the relationship cache in memory', 'correct' => false],
                ],
            ],
            // ROUTE MODEL BINDING
            [
                'question' => 'What is implicit route model binding in Laravel?',
                'explanation' => 'Laravel automatically resolves an Eloquent model from a route parameter by matching the parameter name to a type-hinted model. `Route::get(\'/posts/{post}\', fn(Post $post) => ...)` automatically fetches the Post with the given ID.',
                'options' => [
                    ['text' => 'Automatically resolving an Eloquent model from a route parameter by matching the parameter name to a type-hint', 'correct' => true],
                    ['text' => 'Binding a middleware to a specific route model', 'correct' => false],
                    ['text' => 'Injecting the database connection into a route closure', 'correct' => false],
                    ['text' => 'Caching route parameters for reuse in other routes', 'correct' => false],
                ],
            ],
            // FORM REQUESTS & VALIDATION
            [
                'question' => 'What is a Form Request class in Laravel?',
                'explanation' => 'A Form Request is a dedicated class (extending `FormRequest`) that encapsulates validation rules and authorization logic for a specific HTTP request. It keeps controllers thin by moving validation out.',
                'options' => [
                    ['text' => 'A dedicated class containing validation rules and authorization logic for a specific request', 'correct' => true],
                    ['text' => 'An HTML form rendered using Blade components', 'correct' => false],
                    ['text' => 'A class that handles CSRF token generation for forms', 'correct' => false],
                    ['text' => 'A service class that sends HTTP requests to external APIs', 'correct' => false],
                ],
            ],
            [
                'question' => 'What command generates a Form Request class?',
                'explanation' => '`php artisan make:request StorePostRequest` creates a Form Request class in `app/Http/Requests/`. It includes `authorize()` and `rules()` methods to fill in.',
                'options' => [
                    ['text' => 'php artisan make:request StorePostRequest', 'correct' => true],
                    ['text' => 'php artisan make:form StorePostRequest', 'correct' => false],
                    ['text' => 'php artisan make:validation StorePostRequest', 'correct' => false],
                    ['text' => 'php artisan create:request StorePostRequest', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is the difference between $request->validate() and a Form Request?',
                'explanation' => '`$request->validate()` is inline validation in the controller — quick but mixes concerns. A Form Request moves the validation and authorization into a reusable class, keeping the controller thin and the logic testable independently.',
                'options' => [
                    ['text' => 'validate() is inline in the controller; Form Requests move validation into a dedicated, reusable class', 'correct' => true],
                    ['text' => 'validate() is for GET requests; Form Requests are for POST requests', 'correct' => false],
                    ['text' => 'Form Requests only handle authorization, not validation rules', 'correct' => false],
                    ['text' => 'Both are identical — Form Request is just a wrapper around validate()', 'correct' => false],
                ],
            ],
            // ELOQUENT SCOPES
            [
                'question' => 'What is a local query scope in Eloquent?',
                'explanation' => 'A local scope is a method prefixed with `scope` on a model that adds a reusable query constraint. `scopeActive($query)` is called as `Post::active()->get()`. It promotes DRY query logic.',
                'options' => [
                    ['text' => 'A method prefixed with scope on a model that adds a reusable, chainable query constraint', 'correct' => true],
                    ['text' => 'A constraint applied automatically to all queries for a model', 'correct' => false],
                    ['text' => 'A method that restricts which users can access a model', 'correct' => false],
                    ['text' => 'A cache scope that stores query results for a configurable duration', 'correct' => false],
                ],
            ],
            // API RESOURCES
            [
                'question' => 'What is an API Resource in Laravel?',
                'explanation' => 'An API Resource is a transformation class that controls how an Eloquent model (or collection) is serialized to JSON. It decouples your internal data structure from your API response shape.',
                'options' => [
                    ['text' => 'A transformation class that controls how a model is serialized to JSON for API responses', 'correct' => true],
                    ['text' => 'A route group that restricts access to API routes', 'correct' => false],
                    ['text' => 'A controller that handles all CRUD operations for a model', 'correct' => false],
                    ['text' => 'A middleware that formats all responses as JSON automatically', 'correct' => false],
                ],
            ],
            [
                'question' => 'What command generates an API Resource class?',
                'explanation' => '`php artisan make:resource PostResource` creates a resource class in `app/Http/Resources/`. Use `--collection` to generate a collection resource for wrapping arrays of models.',
                'options' => [
                    ['text' => 'php artisan make:resource PostResource', 'correct' => true],
                    ['text' => 'php artisan make:transformer PostResource', 'correct' => false],
                    ['text' => 'php artisan make:api PostResource', 'correct' => false],
                    ['text' => 'php artisan resource:make PostResource', 'correct' => false],
                ],
            ],
            // SERVICE CONTAINER
            [
                'question' => 'What is the Service Container in Laravel?',
                'explanation' => 'The Service Container is Laravel\'s IoC (Inversion of Control) container. It manages class dependencies and performs dependency injection — automatically resolving and injecting dependencies into controllers, commands, and jobs.',
                'options' => [
                    ['text' => "Laravel's IoC container that manages class dependencies and performs automatic dependency injection", 'correct' => true],
                    ['text' => "A database that stores application service configurations", 'correct' => false],
                    ['text' => "A middleware stack that wraps every HTTP request", 'correct' => false],
                    ['text' => "A caching layer for expensive service instantiations", 'correct' => false],
                ],
            ],
            [
                'question' => 'What is the difference between bind() and singleton() in the Service Container?',
                'explanation' => '`bind()` creates a fresh instance each time the binding is resolved. `singleton()` creates the instance once and returns the SAME instance on every subsequent resolution. Use singleton for shared state.',
                'options' => [
                    ['text' => 'bind() returns a new instance each resolution; singleton() returns the same instance every time', 'correct' => true],
                    ['text' => 'singleton() is only for third-party service bindings; bind() is for application services', 'correct' => false],
                    ['text' => 'bind() resolves immediately at boot; singleton() resolves lazily', 'correct' => false],
                    ['text' => 'There is no practical difference between them', 'correct' => false],
                ],
            ],
            // SANCTUM
            [
                'question' => 'What is Laravel Sanctum used for?',
                'explanation' => 'Laravel Sanctum provides a lightweight authentication system for SPAs (using cookie-based sessions), mobile applications (using token authentication), and simple token-based APIs.',
                'options' => [
                    ['text' => 'Lightweight authentication for SPAs (cookie sessions), mobile apps, and simple API tokens', 'correct' => true],
                    ['text' => 'Full OAuth2 server implementation for third-party authorization', 'correct' => false],
                    ['text' => 'A package for encrypting database records', 'correct' => false],
                    ['text' => 'A two-factor authentication (2FA) package', 'correct' => false],
                ],
            ],
            // ELOQUENT FEATURES
            [
                'question' => 'What are Eloquent accessors?',
                'explanation' => 'Accessors transform an attribute\'s value when you READ it from a model. In Laravel 9+, define them using `Attribute::get()` inside a method named after the attribute. They are called automatically when you access the property.',
                'options' => [
                    ['text' => 'Methods that transform an attribute\'s value when reading it from the model', 'correct' => true],
                    ['text' => 'Methods that validate attribute values before saving to the database', 'correct' => false],
                    ['text' => 'Methods that control which attributes are visible in JSON output', 'correct' => false],
                    ['text' => 'Methods that auto-increment attribute values on each access', 'correct' => false],
                ],
            ],
            [
                'question' => 'What are Eloquent mutators?',
                'explanation' => 'Mutators transform a value when you SET it on a model. They run automatically when you assign a value to a model property, before it is persisted. Used for hashing passwords, formatting dates, etc.',
                'options' => [
                    ['text' => 'Methods that transform a value when setting it on the model (before save)', 'correct' => true],
                    ['text' => 'Methods that transform a value when reading it from the model', 'correct' => false],
                    ['text' => 'Methods that fire events when a model attribute changes', 'correct' => false],
                    ['text' => 'Methods that mutate database records during migration', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is soft deleting in Laravel?',
                'explanation' => 'Soft deleting marks a record as deleted by setting a `deleted_at` timestamp instead of physically removing the row. The record remains in the database and can be restored. Add the `SoftDeletes` trait to your model.',
                'options' => [
                    ['text' => 'Setting a deleted_at timestamp on a record instead of physically removing it from the database', 'correct' => true],
                    ['text' => 'Moving deleted records to a separate archive table', 'correct' => false],
                    ['text' => 'Deleting a record only after a set delay period', 'correct' => false],
                    ['text' => 'Making a record read-only before permanently deleting it', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does withTrashed() do in an Eloquent query?',
                'explanation' => '`withTrashed()` includes soft-deleted records (those with a non-null `deleted_at`) in the query results. Without it, soft-deleted records are automatically excluded.',
                'options' => [
                    ['text' => 'Includes soft-deleted records in query results', 'correct' => true],
                    ['text' => 'Returns only soft-deleted records', 'correct' => false],
                    ['text' => 'Restores all soft-deleted records before fetching', 'correct' => false],
                    ['text' => 'Permanently deletes records marked as trashed', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does pluck(\'column\') do in Eloquent?',
                'explanation' => '`pluck(\'title\')` returns a Collection containing only the values from the specified column — no full model instances. Useful when you only need a list of IDs or titles.',
                'options' => [
                    ['text' => 'Returns a Collection of values from a single column without loading full model instances', 'correct' => true],
                    ['text' => 'Picks and returns the first record from a query', 'correct' => false],
                    ['text' => 'Removes a column value from all records matching the query', 'correct' => false],
                    ['text' => 'Returns a Collection sorted by the specified column', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does chunk(100, callback) do in Eloquent?',
                'explanation' => '`chunk()` retrieves records in batches (e.g., 100 at a time) and passes each batch to the callback. This prevents loading thousands of records into memory at once.',
                'options' => [
                    ['text' => 'Processes records in batches to reduce memory usage when iterating large datasets', 'correct' => true],
                    ['text' => 'Splits a query into parallel subqueries for faster execution', 'correct' => false],
                    ['text' => 'Caches the query result in chunks for pagination', 'correct' => false],
                    ['text' => 'Limits the query result to exactly 100 records', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does withCount(\'comments\') do in Eloquent?',
                'explanation' => '`withCount(\'comments\')` adds a `comments_count` column to each result containing the number of related comments, without loading the actual comment records. It runs as a subquery.',
                'options' => [
                    ['text' => 'Adds a {relation}_count column with the count of related records without loading them', 'correct' => true],
                    ['text' => 'Counts all comments in the database and returns the total', 'correct' => false],
                    ['text' => 'Loads comments and counts them in PHP memory', 'correct' => false],
                    ['text' => 'Returns only records that have at least one related comment', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does updateOrCreate(array $attributes, array $values = []) do?',
                'explanation' => '`updateOrCreate` searches for a record matching `$attributes`. If found, it updates it with `$values`. If not found, it creates a new record combining both arrays.',
                'options' => [
                    ['text' => 'Finds a matching record and updates it, or creates it if not found', 'correct' => true],
                    ['text' => 'Updates all records and creates missing ones in a single query', 'correct' => false],
                    ['text' => 'Creates a record only if it does not already exist, never updates', 'correct' => false],
                    ['text' => 'Upserts the entire table by replacing all rows', 'correct' => false],
                ],
            ],
            // DATABASE TRANSACTIONS
            [
                'question' => 'What is a database transaction and when should you use one?',
                'explanation' => 'A transaction groups multiple queries into an atomic unit: either ALL succeed (commit) or ALL fail (rollback). Use when multiple related database writes must all succeed together — e.g., creating an order and decrementing inventory.',
                'options' => [
                    ['text' => 'A group of queries that must all succeed or all rollback — used when multiple writes must be atomic', 'correct' => true],
                    ['text' => 'A log of all queries executed during a request for debugging', 'correct' => false],
                    ['text' => 'A mechanism for queueing database writes for later execution', 'correct' => false],
                    ['text' => 'A read-only snapshot of the database at a point in time', 'correct' => false],
                ],
            ],
            [
                'question' => 'How do you run a database transaction in Laravel?',
                'explanation' => '`DB::transaction(fn)` automatically commits if no exception is thrown, or rolls back if an exception occurs. It also handles retries for deadlocks.',
                'options' => [
                    ['text' => 'DB::transaction(function() { /* queries */ })', 'correct' => true],
                    ['text' => 'DB::beginTransaction(); /* queries */; DB::commit()', 'correct' => false],
                    ['text' => 'Transaction::run(function() { /* queries */ })', 'correct' => false],
                    ['text' => 'DB::atomic(function() { /* queries */ })', 'correct' => false],
                ],
            ],
            // PAGINATION
            [
                'question' => 'What is the difference between paginate() and simplePaginate() in Eloquent?',
                'explanation' => '`paginate()` runs a COUNT query to know the total number of pages (gives you "Page 3 of 10"). `simplePaginate()` skips the COUNT query and only knows if there is a next page — faster for large tables but no total page count.',
                'options' => [
                    ['text' => 'paginate() counts total records (shows total pages); simplePaginate() does not count (faster, next/prev only)', 'correct' => true],
                    ['text' => 'simplePaginate() is for API responses; paginate() is for web views', 'correct' => false],
                    ['text' => 'paginate() uses cursor-based pagination; simplePaginate() uses offset-based', 'correct' => false],
                    ['text' => 'Both are identical; simplePaginate() is just a shorter alias', 'correct' => false],
                ],
            ],
            // RESOURCE CONTROLLERS
            [
                'question' => 'What does php artisan make:controller PostController --resource generate?',
                'explanation' => 'A resource controller contains 7 methods: `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`. These map to standard RESTful CRUD operations and can be registered with `Route::resource()`.',
                'options' => [
                    ['text' => 'A controller with 7 RESTful methods: index, create, store, show, edit, update, destroy', 'correct' => true],
                    ['text' => 'A controller with 4 methods for CRUD: create, read, update, delete', 'correct' => false],
                    ['text' => 'A controller with only index() and show() for read-only resources', 'correct' => false],
                    ['text' => 'A controller that automatically generates API endpoints for a model', 'correct' => false],
                ],
            ],
            // QUERY BUILDER
            [
                'question' => 'What does the query builder when() method do in Laravel?',
                'explanation' => '`when($condition, fn($q) => ...)` conditionally adds a query clause only if the condition is truthy. If the condition is false, the closure is skipped. It avoids messy if/else blocks in queries.',
                'options' => [
                    ['text' => 'Conditionally applies a query clause only when the first argument is truthy', 'correct' => true],
                    ['text' => 'Waits for a condition to be met before executing the query', 'correct' => false],
                    ['text' => 'Adds a WHERE clause that checks if a value is set', 'correct' => false],
                    ['text' => 'Runs the query only during specific time windows', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does findOrFail($id) do differently from find($id)?',
                'explanation' => '`find($id)` returns the model or `null` if not found. `findOrFail($id)` throws a `ModelNotFoundException` (which Laravel converts to a 404 response) if no record is found.',
                'options' => [
                    ['text' => 'findOrFail() throws ModelNotFoundException (404) if not found; find() returns null', 'correct' => true],
                    ['text' => 'findOrFail() creates the record if it does not exist; find() returns null', 'correct' => false],
                    ['text' => 'find() throws an exception; findOrFail() returns null', 'correct' => false],
                    ['text' => 'Both return the same result; findOrFail() just logs the failed attempt', 'correct' => false],
                ],
            ],
            // OBSERVERS
            [
                'question' => 'What are Model Observers in Laravel?',
                'explanation' => 'Observers group all Eloquent event listeners for a model (creating, created, updating, updated, saving, saved, deleting, deleted, restored) into a single class, keeping event handling organized.',
                'options' => [
                    ['text' => 'Classes that group Eloquent event listeners (creating, created, deleted, etc.) for a model', 'correct' => true],
                    ['text' => 'Middleware that observes all HTTP requests for a given resource', 'correct' => false],
                    ['text' => 'Classes that watch for database schema changes and apply migrations', 'correct' => false],
                    ['text' => 'Listeners attached to the Eloquent global scope', 'correct' => false],
                ],
            ],
            // EVENTS
            [
                'question' => 'What is an Event in Laravel?',
                'explanation' => 'An Event is a simple class that represents something that happened in your application (e.g., `OrderShipped`, `UserRegistered`). Events decouple components — when an event fires, any number of listeners can respond independently.',
                'options' => [
                    ['text' => 'A class representing something that happened in the application, used to decouple components', 'correct' => true],
                    ['text' => 'A scheduled task that runs at a set interval', 'correct' => false],
                    ['text' => 'A JavaScript event handler registered in a Blade template', 'correct' => false],
                    ['text' => 'An HTTP webhook that notifies external services of changes', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is a Queue in Laravel and when would you use it?',
                'explanation' => 'A Queue defers time-consuming tasks (sending emails, resizing images, processing payments) to run in the background, returning an instant response to the user. Workers process queued jobs asynchronously.',
                'options' => [
                    ['text' => 'A system for deferring slow tasks (emails, image processing) to background workers', 'correct' => true],
                    ['text' => 'An ordered list of HTTP requests waiting to be processed', 'correct' => false],
                    ['text' => 'A first-in-first-out data structure for managing database connections', 'correct' => false],
                    ['text' => 'A caching mechanism for slow database queries', 'correct' => false],
                ],
            ],
            // MODEL HIDDEN
            [
                'question' => 'What is the purpose of the $hidden property in an Eloquent model?',
                'explanation' => '$hidden is an array of attribute names that are excluded when the model is serialized to JSON or an array. Commonly used to hide `password` and `remember_token` from API responses.',
                'options' => [
                    ['text' => 'Excludes specified attributes from JSON and array serialization', 'correct' => true],
                    ['text' => 'Encrypts specified attributes before storing in the database', 'correct' => false],
                    ['text' => 'Makes specified columns invisible to database queries', 'correct' => false],
                    ['text' => 'Prevents specified attributes from being mass-assigned', 'correct' => false],
                ],
            ],
            // HAS / WHERE HAS
            [
                'question' => 'What is the difference between has() and whereHas() in Eloquent?',
                'explanation' => '`has(\'comments\')` filters posts that have at least one comment — no conditions on the comments. `whereHas(\'comments\', fn($q) => $q->where(\'approved\', true))` adds conditions ON the related records.',
                'options' => [
                    ['text' => 'has() filters records that have related records; whereHas() adds conditions on those related records', 'correct' => true],
                    ['text' => 'has() is faster; whereHas() runs a PHP-level filter after the query', 'correct' => false],
                    ['text' => 'has() and whereHas() are identical in behavior', 'correct' => false],
                    ['text' => 'whereHas() is the eager loading version of has()', 'correct' => false],
                ],
            ],
        ];
    }
}
