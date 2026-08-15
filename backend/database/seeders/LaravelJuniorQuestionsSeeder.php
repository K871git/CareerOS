<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;

class LaravelJuniorQuestionsSeeder extends Seeder
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

        // Create all 3 level topics upfront so later seeders can use firstOrCreate safely
        Topic::firstOrCreate(
            ['slug' => 'laravel-junior'],
            ['subject_id' => $subject->id, 'title' => 'Laravel Basics — Junior', 'description' => 'Junior-level Laravel questions: routing, controllers, Blade, Eloquent basics, migrations, and Artisan.', 'display_order' => 1]
        );
        Topic::firstOrCreate(
            ['slug' => 'laravel-intermediate'],
            ['subject_id' => $subject->id, 'title' => 'Laravel Intermediate', 'description' => 'Intermediate Laravel: Eloquent relationships, Service Container, validation, API Resources, queues, and more.', 'display_order' => 2]
        );
        Topic::firstOrCreate(
            ['slug' => 'laravel-advanced'],
            ['subject_id' => $subject->id, 'title' => 'Laravel Advanced', 'description' => 'Advanced Laravel: design patterns, Horizon, Telescope, testing, Policies, Contracts, and performance.', 'display_order' => 3]
        );

        $topic = Topic::where('slug', 'laravel-junior')->firstOrFail();

        foreach ($this->questions() as $qData) {
            $exists = Question::where('topic_id', $topic->id)
                ->where('question', $qData['question'])
                ->exists();
            if ($exists) continue;

            $q = Question::create([
                'topic_id'    => $topic->id,
                'type'        => 'MCQ',
                'difficulty'  => 'Easy',
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
        $this->command->info("Laravel Junior: {$count} questions total.");
    }

    private function questions(): array
    {
        return [
            // FRAMEWORK BASICS
            [
                'question' => 'What type of framework is Laravel?',
                'explanation' => 'Laravel is a PHP web application framework that follows the MVC (Model-View-Controller) architectural pattern. It provides tools for routing, authentication, sessions, caching, and more.',
                'options' => [
                    ['text' => 'A PHP framework following the MVC (Model-View-Controller) pattern', 'correct' => true],
                    ['text' => 'A JavaScript framework for building single-page applications', 'correct' => false],
                    ['text' => 'A Python microframework for REST APIs', 'correct' => false],
                    ['text' => 'A CSS utility framework for styling web pages', 'correct' => false],
                ],
            ],
            [
                'question' => 'What command creates a new Laravel project using Composer?',
                'explanation' => '`composer create-project laravel/laravel project-name` downloads and sets up a fresh Laravel application. Alternatively, `laravel new project-name` works if the Laravel installer is globally installed.',
                'options' => [
                    ['text' => 'composer create-project laravel/laravel project-name', 'correct' => true],
                    ['text' => 'laravel create project-name', 'correct' => false],
                    ['text' => 'npm install laravel project-name', 'correct' => false],
                    ['text' => 'php install laravel project-name', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is Artisan in Laravel?',
                'explanation' => 'Artisan is Laravel\'s built-in command-line interface. It provides helpful commands for development tasks such as generating code, running migrations, managing queues, and clearing caches.',
                'options' => [
                    ['text' => 'Laravel\'s built-in command-line interface (CLI) for development tasks', 'correct' => true],
                    ['text' => 'A package manager for Laravel projects', 'correct' => false],
                    ['text' => 'Laravel\'s database query builder class', 'correct' => false],
                    ['text' => 'A third-party deployment tool for Laravel applications', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does php artisan serve do?',
                'explanation' => '`php artisan serve` starts a local PHP development server, making the application accessible at http://127.0.0.1:8000 by default. It is a quick way to test without configuring Nginx or Apache.',
                'options' => [
                    ['text' => 'Starts a local PHP development server at http://127.0.0.1:8000', 'correct' => true],
                    ['text' => 'Deploys the application to a production server', 'correct' => false],
                    ['text' => 'Runs all application tests', 'correct' => false],
                    ['text' => 'Starts the Laravel queue worker', 'correct' => false],
                ],
            ],
            // ROUTING
            [
                'question' => 'How do you define a basic GET route in Laravel?',
                'explanation' => 'Routes are defined in route files using the `Route` facade. `Route::get(\'/path\', fn)` registers a GET route that calls a closure or controller method.',
                'options' => [
                    ['text' => "Route::get('/path', [Controller::class, 'method'])", 'correct' => true],
                    ['text' => "get('/path', [Controller::class, 'method'])", 'correct' => false],
                    ['text' => "Route.get('/path', Controller::method)", 'correct' => false],
                    ['text' => "@Route('/path', method='get')", 'correct' => false],
                ],
            ],
            [
                'question' => 'Which file contains web (browser) routes by default in Laravel?',
                'explanation' => '`routes/web.php` contains routes that use the `web` middleware group (sessions, CSRF protection, etc.). These are for browser-based requests.',
                'options' => [
                    ['text' => 'routes/web.php', 'correct' => true],
                    ['text' => 'routes/api.php', 'correct' => false],
                    ['text' => 'app/Http/routes.php', 'correct' => false],
                    ['text' => 'config/routes.php', 'correct' => false],
                ],
            ],
            [
                'question' => 'Which file contains API routes by default in Laravel?',
                'explanation' => '`routes/api.php` registers routes under the `api` middleware group. Routes here are prefixed with `/api` and are stateless (no sessions).',
                'options' => [
                    ['text' => 'routes/api.php', 'correct' => true],
                    ['text' => 'routes/web.php', 'correct' => false],
                    ['text' => 'routes/rest.php', 'correct' => false],
                    ['text' => 'app/Routes/api.php', 'correct' => false],
                ],
            ],
            [
                'question' => 'How do you give a route a name in Laravel?',
                'explanation' => 'Chaining `->name(\'route.name\')` onto a route definition gives it a named reference. Named routes allow you to generate URLs with `route(\'name\')` without hard-coding paths.',
                'options' => [
                    ['text' => "Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show')", 'correct' => true],
                    ['text' => "Route::get('/profile', [ProfileController::class, 'show'], name: 'profile.show')", 'correct' => false],
                    ['text' => "Route::name('profile.show')->get('/profile', [ProfileController::class, 'show'])", 'correct' => false],
                    ['text' => "Route::get('/profile', fn() => ...)->setName('profile.show')", 'correct' => false],
                ],
            ],
            [
                'question' => 'How do you generate a URL to a named route in Laravel?',
                'explanation' => 'The `route()` helper generates a fully qualified URL to a named route. `route(\'profile.show\')` is equivalent to the URL registered for that name.',
                'options' => [
                    ['text' => "route('profile.show')", 'correct' => true],
                    ['text' => "url('profile.show')", 'correct' => false],
                    ['text' => "link_to_route('profile.show')", 'correct' => false],
                    ['text' => "Route::url('profile.show')", 'correct' => false],
                ],
            ],
            [
                'question' => 'What is a route group in Laravel?',
                'explanation' => 'Route groups allow you to share route attributes (middleware, prefix, namespace) across multiple routes without repeating them on each individual route.',
                'options' => [
                    ['text' => 'A way to group routes that share common attributes like middleware or URL prefix', 'correct' => true],
                    ['text' => 'A collection of routes stored in a separate database table', 'correct' => false],
                    ['text' => 'A special route that handles multiple HTTP methods at once', 'correct' => false],
                    ['text' => 'A grouping of routes that are loaded conditionally based on environment', 'correct' => false],
                ],
            ],
            // CONTROLLERS
            [
                'question' => 'What command creates a new controller in Laravel?',
                'explanation' => '`php artisan make:controller ControllerName` generates a new controller class in `app/Http/Controllers`. Add `--resource` for a full CRUD controller.',
                'options' => [
                    ['text' => 'php artisan make:controller PostController', 'correct' => true],
                    ['text' => 'php artisan create:controller PostController', 'correct' => false],
                    ['text' => 'php artisan generate:controller PostController', 'correct' => false],
                    ['text' => 'php artisan new:controller PostController', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is the main responsibility of a controller in Laravel?',
                'explanation' => 'Controllers handle incoming HTTP requests and return responses. Business logic should live in services or models — controllers should stay thin, only coordinating the request/response cycle.',
                'options' => [
                    ['text' => 'Handle HTTP requests and return responses — not contain business logic', 'correct' => true],
                    ['text' => 'Define database schema and manage migrations', 'correct' => false],
                    ['text' => 'Store all business logic and calculation code', 'correct' => false],
                    ['text' => 'Manage frontend routing and template rendering logic', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does php artisan route:list do?',
                'explanation' => '`php artisan route:list` prints a table of all registered routes including their method, URI, name, action, and middleware. Useful for debugging and understanding your application\'s routing.',
                'options' => [
                    ['text' => 'Displays all registered routes with their methods, URIs, names, and middleware', 'correct' => true],
                    ['text' => 'Generates a list of all controllers in the application', 'correct' => false],
                    ['text' => 'Creates a static sitemap of all application routes', 'correct' => false],
                    ['text' => 'Checks all routes for broken links', 'correct' => false],
                ],
            ],
            // BLADE
            [
                'question' => 'What is Blade in Laravel?',
                'explanation' => 'Blade is Laravel\'s built-in templating engine. It provides directives like @if, @foreach, @extends, @section, and @yield for writing clean, readable templates. Blade files are compiled to plain PHP and cached.',
                'options' => [
                    ['text' => "Laravel's built-in templating engine with directives like @if, @foreach, @extends", 'correct' => true],
                    ['text' => "Laravel's CSS pre-processor for styling views", 'correct' => false],
                    ['text' => "A JavaScript framework integrated with Laravel", 'correct' => false],
                    ['text' => "Laravel's database query visualization tool", 'correct' => false],
                ],
            ],
            [
                'question' => 'What does @extends(\'layouts.app\') do in a Blade view?',
                'explanation' => '@extends tells the child view to inherit from a parent layout. The child can then override sections defined in the layout using @section/@endsection.',
                'options' => [
                    ['text' => 'Tells the child view to inherit from the layouts/app.blade.php layout file', 'correct' => true],
                    ['text' => 'Imports a PHP class into the Blade view', 'correct' => false],
                    ['text' => 'Includes another view file inline', 'correct' => false],
                    ['text' => 'Runs the parent controller method before rendering', 'correct' => false],
                ],
            ],
            [
                'question' => "What does @yield('content') do in a Blade layout?",
                'explanation' => "@yield marks a placeholder section in a layout. Child views that extend the layout use @section('content') and @endsection to fill in that placeholder.",
                'options' => [
                    ['text' => "Marks a placeholder that child views fill with @section('content')", 'correct' => true],
                    ['text' => 'Outputs the return value of the controller method', 'correct' => false],
                    ['text' => 'Yields execution back to the PHP runtime', 'correct' => false],
                    ['text' => 'Includes a partial view file', 'correct' => false],
                ],
            ],
            [
                'question' => "What does @section('content') ... @endsection do in Blade?",
                'explanation' => "@section defines the content that will be injected into the parent layout's @yield('content') placeholder. Everything between @section and @endsection is the content for that slot.",
                'options' => [
                    ['text' => "Defines content that will be injected into the parent layout's matching @yield placeholder", 'correct' => true],
                    ['text' => 'Creates a new reusable Blade component', 'correct' => false],
                    ['text' => 'Conditionally renders content if a section is defined', 'correct' => false],
                    ['text' => 'Declares a PHP variable section available throughout the view', 'correct' => false],
                ],
            ],
            [
                'question' => 'How do you pass data from a controller to a Blade view?',
                'explanation' => "Pass data as the second argument to view() as an associative array. The keys become variable names in the Blade template. You can also use compact() or ->with().",
                'options' => [
                    ['text' => "return view('posts.index', ['posts' => \$posts])", 'correct' => true],
                    ['text' => "return view('posts.index')::with('posts', \$posts)", 'correct' => false],
                    ['text' => "View::pass('posts.index', \$posts)", 'correct' => false],
                    ['text' => "return view('posts.index', \$posts)", 'correct' => false],
                ],
            ],
            [
                'question' => 'What does the @csrf Blade directive do?',
                'explanation' => '@csrf generates a hidden HTML input field containing a CSRF token. Laravel validates this token on POST/PUT/PATCH/DELETE requests to protect against Cross-Site Request Forgery attacks.',
                'options' => [
                    ['text' => 'Generates a hidden input with a CSRF token required for form submission security', 'correct' => true],
                    ['text' => 'Disables CSRF protection for the current form', 'correct' => false],
                    ['text' => 'Encrypts the entire form data before sending', 'correct' => false],
                    ['text' => 'Creates a Content Security Policy header for the page', 'correct' => false],
                ],
            ],
            [
                'question' => 'How do you output a variable in Blade without escaping HTML?',
                'explanation' => '{!! $variable !!} outputs raw unescaped content. The standard {{ $variable }} escapes HTML entities to prevent XSS. Only use {!! !!} when you trust the content.',
                'options' => [
                    ['text' => '{!! $variable !!}', 'correct' => true],
                    ['text' => '{{ $variable }}', 'correct' => false],
                    ['text' => '{= $variable =}', 'correct' => false],
                    ['text' => '<?= $variable ?>', 'correct' => false],
                ],
            ],
            // ELOQUENT BASICS
            [
                'question' => 'What is Eloquent ORM in Laravel?',
                'explanation' => 'Eloquent is Laravel\'s ActiveRecord ORM. Each database table has a corresponding Model class. Models allow you to interact with the database using expressive PHP syntax instead of raw SQL.',
                'options' => [
                    ['text' => "Laravel's ActiveRecord ORM where each model class maps to a database table", 'correct' => true],
                    ['text' => "A database migration manager that tracks schema changes", 'correct' => false],
                    ['text' => "A query profiling tool that analyzes SQL performance", 'correct' => false],
                    ['text' => "A data validation library for incoming request data", 'correct' => false],
                ],
            ],
            [
                'question' => 'What command creates a new Eloquent model?',
                'explanation' => '`php artisan make:model Post` creates a new model class in `app/Models/Post.php`. Adding `-m` also generates a migration file for it.',
                'options' => [
                    ['text' => 'php artisan make:model Post', 'correct' => true],
                    ['text' => 'php artisan create:model Post', 'correct' => false],
                    ['text' => 'php artisan model:make Post', 'correct' => false],
                    ['text' => 'php artisan generate:model Post', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does php artisan make:model Post -m do?',
                'explanation' => 'The `-m` flag creates both the `Post` model AND a corresponding database migration file. This is a convenient shortcut instead of running two separate artisan commands.',
                'options' => [
                    ['text' => 'Creates the Post model AND generates a migration file for it', 'correct' => true],
                    ['text' => 'Creates the Post model with a factory method attached', 'correct' => false],
                    ['text' => 'Creates the Post model and runs the migration immediately', 'correct' => false],
                    ['text' => 'Creates the Post model with a middleware class', 'correct' => false],
                ],
            ],
            [
                'question' => 'How do you retrieve all records from an Eloquent model?',
                'explanation' => '`Model::all()` executes a SELECT * query and returns all rows as an Eloquent Collection. For large tables, use Query Builder with chunking to avoid memory issues.',
                'options' => [
                    ['text' => 'Post::all()', 'correct' => true],
                    ['text' => 'Post::get()', 'correct' => false],
                    ['text' => 'Post::select()', 'correct' => false],
                    ['text' => 'Post::fetch()', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is the difference between first() and get() in Eloquent?',
                'explanation' => '`first()` returns a single Model instance (or null if not found). `get()` executes the query and returns an Eloquent Collection, even if it contains only one record.',
                'options' => [
                    ['text' => 'first() returns one Model instance or null; get() returns an Eloquent Collection', 'correct' => true],
                    ['text' => 'first() is faster than get() only for indexed columns', 'correct' => false],
                    ['text' => 'get() returns the first record; first() returns all records', 'correct' => false],
                    ['text' => 'Both return a Collection but first() limits it to one item', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does findOrFail($id) do in Eloquent?',
                'explanation' => '`findOrFail($id)` finds a record by primary key. If no record is found, it throws a `ModelNotFoundException` which Laravel automatically converts to a 404 response.',
                'options' => [
                    ['text' => 'Finds by primary key and throws ModelNotFoundException (404) if not found', 'correct' => true],
                    ['text' => 'Finds a record or creates it if it does not exist', 'correct' => false],
                    ['text' => 'Finds a record and fails silently, returning null', 'correct' => false],
                    ['text' => 'Finds all records matching the given ID array', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is the $fillable property in an Eloquent model?',
                'explanation' => '$fillable is an array of column names that can be set via mass assignment (e.g., Model::create($request->all())). It is a whitelist that protects against mass assignment vulnerabilities.',
                'options' => [
                    ['text' => 'A whitelist of columns that can be mass-assigned via create() or fill()', 'correct' => true],
                    ['text' => 'A list of columns that are automatically filled with default values', 'correct' => false],
                    ['text' => 'A list of columns that are excluded from JSON output', 'correct' => false],
                    ['text' => 'A blacklist of columns that cannot be updated', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is mass assignment in Eloquent?',
                'explanation' => 'Mass assignment sets multiple model attributes at once from an array, typically from form input. Laravel protects against malicious mass assignment with $fillable (whitelist) or $guarded (blacklist).',
                'options' => [
                    ['text' => 'Setting multiple model attributes at once from an array, protected by $fillable or $guarded', 'correct' => true],
                    ['text' => 'Updating many records with a single SQL UPDATE query', 'correct' => false],
                    ['text' => 'Inserting multiple rows in one database transaction', 'correct' => false],
                    ['text' => 'Bulk loading models into memory for performance', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does firstOrCreate(array $attributes, array $values = []) do?',
                'explanation' => '`firstOrCreate` searches for a record matching `$attributes`. If found, it returns it. If not found, it creates a new record with the merged `$attributes` and `$values` arrays.',
                'options' => [
                    ['text' => 'Returns the matching record or creates it if not found', 'correct' => true],
                    ['text' => 'Returns the first record in the table or an empty model', 'correct' => false],
                    ['text' => 'Creates a new record only if the table is empty', 'correct' => false],
                    ['text' => 'Finds or throws an exception if the record does not exist', 'correct' => false],
                ],
            ],
            // MIGRATIONS
            [
                'question' => 'What is a migration in Laravel?',
                'explanation' => 'A migration is a version-controlled class that defines database schema changes. Migrations allow your team to share and evolve the database schema together, just like version control for code.',
                'options' => [
                    ['text' => 'A version-controlled class that defines and tracks database schema changes', 'correct' => true],
                    ['text' => 'A tool for moving data between databases', 'correct' => false],
                    ['text' => 'A script that backs up the database before deployments', 'correct' => false],
                    ['text' => 'A configuration file that maps model names to table names', 'correct' => false],
                ],
            ],
            [
                'question' => 'What command runs all pending database migrations?',
                'explanation' => '`php artisan migrate` runs all migration files that have not yet been executed, in order. Laravel tracks which migrations have run in the `migrations` table.',
                'options' => [
                    ['text' => 'php artisan migrate', 'correct' => true],
                    ['text' => 'php artisan db:migrate', 'correct' => false],
                    ['text' => 'php artisan migration:run', 'correct' => false],
                    ['text' => 'php artisan schema:update', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does the up() method in a migration file do?',
                'explanation' => 'The `up()` method defines the schema changes to apply when the migration runs — creating tables, adding columns, adding indexes, etc.',
                'options' => [
                    ['text' => 'Defines the schema changes to apply when running the migration', 'correct' => true],
                    ['text' => 'Defines how to reverse/rollback the migration', 'correct' => false],
                    ['text' => 'Validates the schema before applying changes', 'correct' => false],
                    ['text' => 'Seeds the table with initial data', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does the down() method in a migration file do?',
                'explanation' => 'The `down()` method reverses what `up()` did. When you run `php artisan migrate:rollback`, Laravel calls `down()` to undo the migration.',
                'options' => [
                    ['text' => 'Defines how to reverse the migration when rolling back', 'correct' => true],
                    ['text' => 'Defines the schema changes to apply when running the migration', 'correct' => false],
                    ['text' => 'Drops the entire database and starts fresh', 'correct' => false],
                    ['text' => 'Moves data down from production to staging', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does php artisan migrate:rollback do?',
                'explanation' => '`migrate:rollback` reverses the last batch of migrations by calling their `down()` methods. This undoes the most recent set of migrations, not just the very last one.',
                'options' => [
                    ['text' => 'Reverses the last batch of migrations by calling their down() methods', 'correct' => true],
                    ['text' => 'Deletes all migration files from the filesystem', 'correct' => false],
                    ['text' => 'Drops all tables and re-runs all migrations from scratch', 'correct' => false],
                    ['text' => 'Reverts the database to a saved snapshot', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does php artisan migrate:fresh do?',
                'explanation' => '`migrate:fresh` drops ALL tables and re-runs every migration from the beginning. It is useful in development for a clean slate but should never be used in production.',
                'options' => [
                    ['text' => 'Drops all tables and re-runs all migrations from scratch', 'correct' => true],
                    ['text' => 'Refreshes the schema cache without modifying tables', 'correct' => false],
                    ['text' => 'Runs only the latest migration file', 'correct' => false],
                    ['text' => 'Rolls back one migration and then re-runs it', 'correct' => false],
                ],
            ],
            // MIDDLEWARE
            [
                'question' => 'What is middleware in Laravel?',
                'explanation' => 'Middleware is code that intercepts HTTP requests before they reach a controller (or responses before they leave). Common uses: authentication checks, logging, CORS headers, and request throttling.',
                'options' => [
                    ['text' => 'Code that filters HTTP requests before they reach a controller or modifies responses before they leave', 'correct' => true],
                    ['text' => 'A database layer between the ORM and the raw SQL driver', 'correct' => false],
                    ['text' => 'A JavaScript layer between the frontend and the backend API', 'correct' => false],
                    ['text' => 'A caching mechanism for slow database queries', 'correct' => false],
                ],
            ],
            [
                'question' => 'What command creates a new middleware class in Laravel?',
                'explanation' => '`php artisan make:middleware CheckAge` generates a new middleware class with a `handle()` method where you write your logic.',
                'options' => [
                    ['text' => 'php artisan make:middleware CheckAge', 'correct' => true],
                    ['text' => 'php artisan create:middleware CheckAge', 'correct' => false],
                    ['text' => 'php artisan middleware:generate CheckAge', 'correct' => false],
                    ['text' => 'php artisan new:middleware CheckAge', 'correct' => false],
                ],
            ],
            // CONFIGURATION & HELPERS
            [
                'question' => 'What is the .env file used for in Laravel?',
                'explanation' => 'The `.env` file stores environment-specific configuration values (database credentials, API keys, APP_ENV). It is not committed to version control so each environment can have its own values.',
                'options' => [
                    ['text' => 'Stores environment-specific configuration like database credentials and API keys — not committed to version control', 'correct' => true],
                    ['text' => 'Defines PHP version requirements for the application', 'correct' => false],
                    ['text' => 'Contains the application\'s Composer autoload configuration', 'correct' => false],
                    ['text' => 'Stores compiled Blade template cache files', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does the env() helper do in Laravel?',
                'explanation' => '`env(\'KEY\', $default)` reads a value from the `.env` file. The optional second argument is a fallback value returned if the key is not set.',
                'options' => [
                    ['text' => 'Reads a value from the .env file, with an optional default fallback', 'correct' => true],
                    ['text' => 'Sets a new environment variable at runtime', 'correct' => false],
                    ['text' => 'Checks if the application is running in a specific environment', 'correct' => false],
                    ['text' => 'Lists all currently defined environment variables', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does the config() helper do in Laravel?',
                'explanation' => '`config(\'app.name\')` retrieves a value from configuration files in the `config/` directory using dot notation. You can also use it to set a value at runtime: `config([\'key\' => \'value\'])`.',
                'options' => [
                    ['text' => 'Retrieves configuration values from config files using dot notation', 'correct' => true],
                    ['text' => 'Checks if a configuration file exists', 'correct' => false],
                    ['text' => 'Reloads all configuration files from disk', 'correct' => false],
                    ['text' => 'Returns the database connection configuration array', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does the dd() function do in Laravel?',
                'explanation' => '`dd()` stands for "Dump and Die". It dumps the given variables to the screen in a readable format and immediately stops execution. Useful for quick debugging.',
                'options' => [
                    ['text' => 'Dumps the given variable contents and stops execution ("Dump and Die")', 'correct' => true],
                    ['text' => 'Deletes a record from the database and redirects', 'correct' => false],
                    ['text' => 'Drops the database and re-creates it', 'correct' => false],
                    ['text' => 'Defines a dependency injection binding in the container', 'correct' => false],
                ],
            ],
            // SEEDERS & FACTORIES
            [
                'question' => 'What is a seeder in Laravel?',
                'explanation' => 'A seeder is a class that inserts data into the database, typically used for test/sample data or initial required data (like admin users or categories).',
                'options' => [
                    ['text' => 'A class that inserts sample or required data into the database', 'correct' => true],
                    ['text' => 'A class that exports data from the database to a CSV file', 'correct' => false],
                    ['text' => 'A configuration class that defines database connection settings', 'correct' => false],
                    ['text' => 'A class that validates data before inserting it', 'correct' => false],
                ],
            ],
            [
                'question' => 'What command runs all database seeders?',
                'explanation' => '`php artisan db:seed` runs the `DatabaseSeeder` class which typically calls all other seeders. You can also run a specific seeder with `--class=ClassName`.',
                'options' => [
                    ['text' => 'php artisan db:seed', 'correct' => true],
                    ['text' => 'php artisan seed:run', 'correct' => false],
                    ['text' => 'php artisan migrate --seed', 'correct' => false],
                    ['text' => 'php artisan database:seed', 'correct' => false],
                ],
            ],
            // RESPONSE / REDIRECT
            [
                'question' => 'How do you redirect to a named route in a Laravel controller?',
                'explanation' => '`redirect()->route(\'name\')` generates a redirect response to the URL of the named route. It is equivalent to `return redirect(route(\'name\'))`.',
                'options' => [
                    ['text' => "return redirect()->route('profile.show')", 'correct' => true],
                    ['text' => "return Redirect::to('profile.show')", 'correct' => false],
                    ['text' => "return response()->redirect('profile.show')", 'correct' => false],
                    ['text' => "return Route::redirect('profile.show')", 'correct' => false],
                ],
            ],
            [
                'question' => 'What does php artisan cache:clear do?',
                'explanation' => '`php artisan cache:clear` clears the application cache store (not config, route, or view caches). Use `php artisan optimize:clear` to clear all caches at once.',
                'options' => [
                    ['text' => 'Clears the application cache store', 'correct' => true],
                    ['text' => 'Clears the database query cache', 'correct' => false],
                    ['text' => 'Clears all compiled Blade templates', 'correct' => false],
                    ['text' => 'Clears config, route, and view caches simultaneously', 'correct' => false],
                ],
            ],
            // SERVICE PROVIDER
            [
                'question' => 'What is a Service Provider in Laravel?',
                'explanation' => 'Service Providers are the central place to bootstrap and register application services, bindings, event listeners, middleware, and routes. Every Laravel application (and package) uses service providers.',
                'options' => [
                    ['text' => 'The central place to bootstrap and register application services and bindings', 'correct' => true],
                    ['text' => 'A third-party API integration layer', 'correct' => false],
                    ['text' => 'A class that provides template rendering services', 'correct' => false],
                    ['text' => 'A queue driver that manages background jobs', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does php artisan make:migration create_posts_table do?',
                'explanation' => 'This creates a new migration file in `database/migrations/` with a timestamped filename. The generated file includes stubbed `up()` and `down()` methods with a `Schema::create(\'posts_table\', ...)` call.',
                'options' => [
                    ['text' => 'Creates a new migration file with a Schema::create stub for a posts table', 'correct' => true],
                    ['text' => 'Immediately creates the posts table in the database', 'correct' => false],
                    ['text' => 'Creates a model AND migration for the Post class', 'correct' => false],
                    ['text' => 'Generates a seeder class for the posts table', 'correct' => false],
                ],
            ],
        ];
    }
}
