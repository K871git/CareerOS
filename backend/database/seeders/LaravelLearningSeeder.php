<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LaravelLearningSeeder extends Seeder
{
    public function run(): void
    {
        $track = LearningTrack::firstOrCreate(
            ['slug' => 'backend-engineering'],
            ['title' => 'Backend Engineering', 'description' => 'Backend engineering track.', 'display_order' => 3]
        );

        $subject = Subject::firstOrCreate(
            ['slug' => 'laravel'],
            ['learning_track_id' => $track->id, 'title' => 'Laravel', 'description' => 'Laravel framework — from routing to production-grade architecture.', 'display_order' => 3]
        );

        // Levels 1-3 reuse existing practice topics
        Topic::where('slug', 'laravel-junior')->update(['level' => 1]);
        Topic::where('slug', 'laravel-intermediate')->update(['level' => 2]);
        Topic::where('slug', 'laravel-advanced')->update(['level' => 3]);

        // Levels 4-5 are new topics
        $topic4 = Topic::firstOrCreate(
            ['slug' => 'laravel-level-4-patterns'],
            ['subject_id' => $subject->id, 'title' => 'Laravel Architecture & Advanced Patterns', 'description' => 'Repository pattern, SOLID in Laravel, advanced Eloquent, and testing.', 'display_order' => 4, 'level' => 4]
        );
        Topic::where('slug', 'laravel-level-4-patterns')->update(['level' => 4]);

        $topic5 = Topic::firstOrCreate(
            ['slug' => 'laravel-level-5-expert'],
            ['subject_id' => $subject->id, 'title' => 'Expert Laravel & Production Systems', 'description' => 'Production monitoring, Octane, caching, multi-tenancy, and scale.', 'display_order' => 5, 'level' => 5]
        );
        Topic::where('slug', 'laravel-level-5-expert')->update(['level' => 5]);

        $this->seedLessons($subject);
        $this->seedLevel4Questions($topic4);
        $this->seedLevel5Questions($topic5);

        $this->command->info('Lessons seeded for all 5 Laravel levels.');
        $this->command->info('Laravel Learning seeder complete — all 5 levels populated.');
    }

    private function seedLessons(Subject $subject): void
    {
        $topics = [
            'laravel-junior'          => Topic::where('slug', 'laravel-junior')->first(),
            'laravel-intermediate'    => Topic::where('slug', 'laravel-intermediate')->first(),
            'laravel-advanced'        => Topic::where('slug', 'laravel-advanced')->first(),
            'laravel-level-4-patterns'=> Topic::where('slug', 'laravel-level-4-patterns')->first(),
            'laravel-level-5-expert'  => Topic::where('slug', 'laravel-level-5-expert')->first(),
        ];

        $lessons = [
            // Level 1
            [
                'topic_id'      => $topics['laravel-junior']->id,
                'title'         => 'Routing, Controllers & Blade Templates',
                'content'       => <<<'MD'
## Routing, Controllers & Blade Templates

Laravel's routing engine is one of its most expressive features. Every HTTP request your application receives is matched against routes defined in `routes/web.php` (browser) or `routes/api.php` (API).

### Route Basics

```php
// GET route — returns a view
Route::get('/dashboard', function () {
    return view('dashboard');
});

// Named routes allow you to generate URLs without hardcoding paths
Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
```

**Route parameters** capture dynamic segments from the URL:

```php
Route::get('/users/{id}', [UserController::class, 'show']);
// {id?} makes the parameter optional
```

### Controllers

Controllers group related request-handling logic into a single class. They live in `app/Http/Controllers/`.

```php
class UserController extends Controller
{
    public function index(): View
    {
        return view('users.index', ['users' => User::all()]);
    }

    public function show(User $user): View  // Route–model binding: Laravel resolves the User automatically
    {
        return view('users.show', compact('user'));
    }
}
```

**Route–model binding** is a powerful feature: when a route parameter matches a model's primary key, Laravel fetches the record automatically and throws a 404 if not found.

### Blade Templates

Blade is Laravel's templating engine. Files use the `.blade.php` extension and are compiled to plain PHP, so there is no performance overhead.

```blade
{{-- Escaped output (safe against XSS) --}}
{{ $user->name }}

{{-- Unescaped output (use only for trusted HTML) --}}
{!! $html !!}

{{-- Directives --}}
@if ($user->isAdmin())
    <span>Admin</span>
@endif

@foreach ($posts as $post)
    <li>{{ $post->title }}</li>
@endforeach
```

**Template inheritance** with `@extends` and `@section`:

```blade
{{-- resources/views/layouts/app.blade.php --}}
<html>
<body>
    @yield('content')
</body>
</html>

{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
    <h1>Dashboard</h1>
@endsection
```

### The Request Lifecycle (Simplified)

1. HTTP request arrives → `public/index.php`
2. Bootstraps the application (Service Providers registered)
3. Request passed through global middleware
4. Router matches the route → dispatches to controller
5. Controller returns a Response
6. Response sent back to the client
MD,
                'display_order' => 1,

            ],
            [
                'topic_id'      => $topics['laravel-junior']->id,
                'title'         => 'Eloquent ORM: Models, Migrations & Queries',
                'content'       => <<<'MD'
## Eloquent ORM: Models, Migrations & Queries

Eloquent is Laravel's built-in Active Record ORM. Each database table has a corresponding Model class that you use to query and interact with that table.

### Models

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    // Columns that can be mass-assigned
    protected $fillable = ['title', 'body', 'user_id'];

    // Cast JSON column to array automatically
    protected $casts = [
        'published_at' => 'datetime',
        'metadata'     => 'array',
    ];
}
```

By default, Eloquent assumes the table name is the snake_case plural of the model name (`Post` → `posts`). Override with `protected $table = 'custom_name';`.

### Migrations

Migrations are version-controlled database schema changes.

```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('title');
    $table->longText('body');
    $table->timestamp('published_at')->nullable();
    $table->timestamps(); // created_at + updated_at
});
```

Run migrations with `php artisan migrate`. Roll back with `php artisan migrate:rollback`.

### Common Query Methods

```php
// All records
Post::all();

// Find by primary key (throws ModelNotFoundException if not found)
Post::findOrFail(1);

// WHERE clause
Post::where('user_id', auth()->id())->get();

// First matching record
Post::where('title', 'Hello')->first();

// Create a record
Post::create(['title' => 'Hello', 'body' => '...', 'user_id' => 1]);

// Update
$post->update(['title' => 'Updated']);

// Delete
$post->delete();
```

### Mass Assignment Protection

`$fillable` (allowlist) and `$guarded` (denylist) protect against mass-assignment vulnerabilities where a user could inject extra fields into a request.

```php
// Only these fields can be filled via create() / update()
protected $fillable = ['title', 'body'];
```

### Factories & Seeding

```php
// database/factories/PostFactory.php
public function definition(): array
{
    return [
        'title'   => fake()->sentence(),
        'body'    => fake()->paragraphs(3, true),
        'user_id' => User::factory(),
    ];
}

// In a seeder
Post::factory()->count(50)->create();
```
MD,
                'display_order' => 2,

            ],
            [
                'topic_id'      => $topics['laravel-junior']->id,
                'title'         => 'Middleware, Request Lifecycle & Artisan CLI',
                'content'       => <<<'MD'
## Middleware, Request Lifecycle & Artisan CLI

### Middleware

Middleware filters HTTP requests entering your application. Common uses: authentication, logging, CORS headers, rate limiting.

```php
// Generate a middleware
php artisan make:middleware EnsureSubscribed

// app/Http/Middleware/EnsureSubscribed.php
public function handle(Request $request, Closure $next): Response
{
    if (! $request->user()->isSubscribed()) {
        return redirect('/subscribe');
    }
    return $next($request); // pass to the next middleware / controller
}
```

**Register** in `bootstrap/app.php` (Laravel 11+) or `Kernel.php` (Laravel 10), then apply to routes:

```php
Route::get('/premium', [PremiumController::class, 'index'])->middleware('subscribed');
```

**Global middleware** runs on every request. **Route middleware** runs only on specific routes.

### The Full Request Lifecycle

1. Entry point: `public/index.php` loads Composer autoloader + bootstraps the app
2. `Application::make()` creates the IoC container
3. HTTP kernel runs all global middleware
4. Router dispatches request to the correct controller action
5. Controller action runs (possibly through route middleware)
6. Response is returned, transformed, and sent

### Common Artisan Commands

Artisan is Laravel's CLI. It's the daily driver for development tasks.

| Command | Purpose |
|---------|---------|
| `php artisan serve` | Start development server |
| `php artisan make:model Post -mfc` | Model + migration + factory + controller |
| `php artisan migrate` | Run pending migrations |
| `php artisan migrate:fresh --seed` | Drop all tables, re-migrate, seed |
| `php artisan tinker` | Interactive REPL for your app |
| `php artisan route:list` | List all registered routes |
| `php artisan config:cache` | Cache configuration for production |
| `php artisan queue:work` | Process queued jobs |

### Custom Artisan Commands

```php
// app/Console/Commands/SendDailyReport.php
class SendDailyReport extends Command
{
    protected $signature   = 'report:daily {--date= : The date to report on}';
    protected $description = 'Send the daily summary report';

    public function handle(): void
    {
        $date = $this->option('date') ?? today()->toDateString();
        $this->info("Sending report for {$date}...");
        // ... logic
    }
}
```
MD,
                'display_order' => 3,

            ],

            // Level 2
            [
                'topic_id'      => $topics['laravel-intermediate']->id,
                'title'         => 'Eloquent Relationships & Query Optimization',
                'content'       => <<<'MD'
## Eloquent Relationships & Query Optimization

### Relationship Types

```php
// One-to-Many: A User has many Posts
class User extends Model
{
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}

// Inverse: A Post belongs to a User
class Post extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

// Many-to-Many: Posts have many Tags (via pivot table post_tag)
class Post extends Model
{
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }
}

// Has-One-Through / Has-Many-Through
class Country extends Model
{
    public function posts(): HasManyThrough
    {
        return $this->hasManyThrough(Post::class, User::class);
    }
}

// Polymorphic: Comments can belong to Post or Video
class Comment extends Model
{
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }
}
```

### The N+1 Problem & Eager Loading

N+1 is the most common Eloquent performance bug. It happens when you load a collection and then access a relationship inside a loop, firing one additional query per item.

```php
// BAD — N+1: 1 query for posts, then 1 per post to get user
$posts = Post::all();
foreach ($posts as $post) {
    echo $post->user->name; // query fires here each time
}

// GOOD — eager loading: 2 queries total (posts + all users)
$posts = Post::with('user')->get();

// Nested eager loading
$posts = Post::with('user', 'tags', 'comments.user')->get();
```

Use `withCount()` to include a count without loading all related records:

```php
Post::withCount('comments')->get();
// Access via $post->comments_count
```

### Query Scopes

```php
class Post extends Model
{
    // Local scope
    public function scopePublished(Builder $query): void
    {
        $query->whereNotNull('published_at');
    }
}

// Usage
Post::published()->latest()->get();
```

### Chunking Large Datasets

Avoid loading millions of rows into memory:

```php
// Process 500 records at a time
Post::chunk(500, function ($posts) {
    foreach ($posts as $post) {
        // process
    }
});

// Or use lazy collections (cursor-based)
foreach (Post::lazy() as $post) {
    // one record at a time — very low memory
}
```
MD,
                'display_order' => 1,

            ],
            [
                'topic_id'      => $topics['laravel-intermediate']->id,
                'title'         => 'Service Container, Service Providers & Facades',
                'content'       => <<<'MD'
## Service Container, Service Providers & Facades

### The Service Container

The Service Container (IoC container) is Laravel's dependency injection engine. It knows how to build objects and automatically injects their dependencies.

```php
// Automatic injection — Laravel resolves PostRepository automatically
class PostController extends Controller
{
    public function __construct(
        private readonly PostRepository $posts
    ) {}
}

// Manual binding in a ServiceProvider
$this->app->bind(PostRepositoryInterface::class, PostRepository::class);

// Singleton — same instance reused for the entire request
$this->app->singleton(Cache::class, function ($app) {
    return new Cache($app['config']['cache']);
});
```

**`bind()` vs `singleton()`**: `bind()` creates a new instance each time it's resolved. `singleton()` creates once, then reuses the same instance.

### Service Providers

Service Providers are the central place to bootstrap application services. Every Laravel application feature (routing, database, queues) is bootstrapped via a service provider.

```php
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind interfaces to implementations
        $this->app->bind(
            PaymentGatewayInterface::class,
            StripeGateway::class
        );
    }

    public function boot(): void
    {
        // Run AFTER all providers are registered
        // Safe to use other services here
        View::share('appName', config('app.name'));
    }
}
```

**`register()`** — only bind things. Do NOT use other services yet (they may not be registered).
**`boot()`** — all services are available. Safe to add observers, view composers, routes.

### Facades

Facades provide a static-like interface to classes resolved from the container. They are not actual static calls — they proxy to the underlying instance.

```php
// Looks static but isn't — Cache::get() proxies to the cache driver
Cache::put('key', 'value', now()->addMinutes(10));
Cache::get('key');

// DB Facade
DB::table('users')->where('active', true)->count();

// Auth Facade
Auth::user();
Auth::id();
```

Facades make code readable but can hide dependencies, making testing harder. In complex services, prefer explicit constructor injection over Facade usage.

### Contracts (Interfaces)

Contracts are interfaces that define the core services Laravel provides. Programming against a Contract (not a concrete class) makes code easier to swap and test.

```php
use Illuminate\Contracts\Cache\Repository as CacheContract;

class UserService
{
    public function __construct(private CacheContract $cache) {}
}
```
MD,
                'display_order' => 2,

            ],
            [
                'topic_id'      => $topics['laravel-intermediate']->id,
                'title'         => 'API Resources, Validation & Authentication with Sanctum',
                'content'       => <<<'MD'
## API Resources, Validation & Authentication with Sanctum

### Form Request Validation

Keep controllers thin by moving validation into dedicated Form Request classes.

```php
php artisan make:request StorePostRequest

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check(); // only authenticated users
    }

    public function rules(): array
    {
        return [
            'title'    => ['required', 'string', 'max:255'],
            'body'     => ['required', 'string', 'min:10'],
            'tags'     => ['array'],
            'tags.*'   => ['exists:tags,id'],
        ];
    }
}

// Controller — validation runs automatically before the method body
public function store(StorePostRequest $request): JsonResponse
{
    $post = Post::create($request->validated());
    return response()->json($post, 201);
}
```

### API Resources

Transform Eloquent models into JSON responses with full control over the output shape.

```php
class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'author'     => new UserResource($this->whenLoaded('user')),
            'tags'       => TagResource::collection($this->whenLoaded('tags')),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}

// Return a single resource
return new PostResource($post);

// Return a collection
return PostResource::collection(Post::paginate(15));
```

`whenLoaded()` prevents including a relationship in the response if it wasn't eager-loaded, avoiding N+1 in the transformation layer.

### Laravel Sanctum

Sanctum provides a simple token-based API authentication system, and SPA cookie-based auth.

**Token authentication (for mobile / third-party API clients):**

```php
// Issue a token on login
$token = $user->createToken('api-token')->plainTextToken;

// Return it once — store securely on the client
return response()->json(['token' => $token]);

// Protect routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $r) => $r->user());
});

// Revoke token on logout
$request->user()->currentAccessToken()->delete();
```

**Scoped tokens** restrict what a token can do:

```php
$token = $user->createToken('read-only', ['posts:read'])->plainTextToken;

// In a controller:
if ($request->user()->tokenCan('posts:read')) { ... }
```
MD,
                'display_order' => 3,

            ],

            // Level 3
            [
                'topic_id'      => $topics['laravel-advanced']->id,
                'title'         => 'Queues, Jobs & Laravel Horizon',
                'content'       => <<<'MD'
## Queues, Jobs & Laravel Horizon

### Why Queues?

Heavy tasks (sending emails, generating reports, calling external APIs) block the HTTP response if done synchronously. Queues push these tasks to a background worker, so the user gets an instant response.

### Creating & Dispatching Jobs

```php
php artisan make:job SendWelcomeEmail

class SendWelcomeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly User $user) {}

    public function handle(Mailer $mailer): void
    {
        $mailer->to($this->user->email)->send(new WelcomeMail($this->user));
    }

    public function failed(Throwable $e): void
    {
        // Called if all retries are exhausted
        Log::error("Welcome email failed for user {$this->user->id}");
    }
}

// Dispatch
SendWelcomeEmail::dispatch($user);

// Delay by 5 minutes
SendWelcomeEmail::dispatch($user)->delay(now()->addMinutes(5));

// Dispatch on a specific queue
SendWelcomeEmail::dispatch($user)->onQueue('emails');
```

### Job Configuration

```php
class SendWelcomeEmail implements ShouldQueue
{
    public int $tries    = 3;       // retry up to 3 times
    public int $timeout  = 60;      // kill if running > 60s
    public int $backoff  = 30;      // wait 30s between retries
    public bool $deleteWhenMissingModels = true; // don't fail if User was deleted
}
```

### Queue Drivers

| Driver | Use case |
|--------|----------|
| `sync` | Development (runs immediately, no worker needed) |
| `database` | Small apps, no Redis available |
| `redis` | Production — fast, supports priorities |
| `sqs` | AWS environments |

### Laravel Horizon

Horizon is a dashboard + supervisor for Redis queues.

```bash
composer require laravel/horizon
php artisan horizon:install
php artisan horizon  # starts the supervisor
```

Horizon gives you:
- Real-time job throughput metrics
- Failed job management with retry UI
- Queue worker auto-scaling based on load
- Configurable worker pools per queue

```php
// config/horizon.php — supervisor configuration
'production' => [
    'supervisor-1' => [
        'connection' => 'redis',
        'queue'      => ['emails', 'default'],
        'balance'    => 'auto',
        'processes'  => 10,
        'tries'      => 3,
    ],
],
```
MD,
                'display_order' => 1,

            ],
            [
                'topic_id'      => $topics['laravel-advanced']->id,
                'title'         => 'Events, Listeners & Broadcasting',
                'content'       => <<<'MD'
## Events, Listeners & Broadcasting

### Events & Listeners

The Event system decouples different parts of your application. An action fires an Event; zero or more Listeners respond to it independently.

```php
// Create an event + listener
php artisan make:event UserRegistered
php artisan make:listener SendWelcomeEmail --event=UserRegistered
php artisan make:listener NotifyAdminOfNewUser --event=UserRegistered

// app/Events/UserRegistered.php
class UserRegistered
{
    public function __construct(public readonly User $user) {}
}

// app/Listeners/SendWelcomeEmail.php
class SendWelcomeEmail implements ShouldQueue  // queued listener
{
    public function handle(UserRegistered $event): void
    {
        Mail::to($event->user)->send(new WelcomeMail($event->user));
    }
}
```

Register in `EventServiceProvider::$listen` (Laravel 10) or use `#[AsEventListener]` attribute (Laravel 11+).

**Fire the event:**
```php
event(new UserRegistered($user));
// or
UserRegistered::dispatch($user);
```

### Event Subscribers

Bundle multiple event handlers into one class:

```php
class UserEventSubscriber
{
    public function handleLogin(Login $event): void { ... }
    public function handleLogout(Logout $event): void { ... }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(Login::class,  [self::class, 'handleLogin']);
        $events->listen(Logout::class, [self::class, 'handleLogout']);
    }
}
```

### Broadcasting (WebSockets)

Broadcasting lets you push server-side events to connected browser clients in real time.

```php
class OrderShipped implements ShouldBroadcast
{
    public function __construct(public readonly Order $order) {}

    public function broadcastOn(): Channel
    {
        return new PrivateChannel("orders.{$this->order->user_id}");
    }

    public function broadcastAs(): string
    {
        return 'order.shipped'; // event name on the JS side
    }
}
```

On the frontend with Laravel Echo:
```js
Echo.private(`orders.${userId}`)
    .listen('.order.shipped', (e) => {
        console.log('Order shipped:', e.order);
    });
```

Supported drivers: **Pusher**, **Ably**, **Reverb** (Laravel's own WebSocket server), **Redis + Socket.io**.
MD,
                'display_order' => 2,

            ],
            [
                'topic_id'      => $topics['laravel-advanced']->id,
                'title'         => 'Authorization: Gates, Policies & Roles',
                'content'       => <<<'MD'
## Authorization: Gates, Policies & Roles

### Gates

Gates are simple closures for authorizing actions that don't relate to a specific model.

```php
// app/Providers/AppServiceProvider.php
Gate::define('view-dashboard', function (User $user) {
    return $user->isAdmin();
});

// Authorize in a controller
Gate::authorize('view-dashboard'); // throws 403 if denied

// Check without throwing
if (Gate::allows('view-dashboard')) { ... }
if (Gate::denies('view-dashboard')) { ... }
```

### Policies

Policies group authorization logic for a specific Eloquent model.

```php
php artisan make:policy PostPolicy --model=Post

class PostPolicy
{
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id || $user->isAdmin();
    }

    // Runs before every other policy method — returning true grants everything
    public function before(User $user): ?bool
    {
        if ($user->isSuperAdmin()) return true;
        return null; // fall through to specific methods
    }
}
```

**Using policies:**
```php
// Controller
$this->authorize('update', $post); // throws AuthorizationException if denied

// Blade
@can('update', $post)
    <a href="{{ route('posts.edit', $post) }}">Edit</a>
@endcan
```

### Role-Based Access Control (RBAC)

Laravel doesn't ship with roles out of the box, but the pattern is straightforward:

```php
// Simple column-based roles
class User extends Authenticatable
{
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }
}

// Gate based on role
Gate::define('manage-users', fn (User $u) => $u->hasRole('admin'));
```

For complex permission systems (multiple roles per user, per-role permissions), use **Spatie Laravel Permission** package which adds `roles` and `permissions` tables with a clean API:

```php
$user->assignRole('editor');
$user->can('edit articles'); // checks permission
```
MD,
                'display_order' => 3,

            ],

            // Level 4
            [
                'topic_id'      => $topics['laravel-level-4-patterns']->id,
                'title'         => 'Repository Pattern, SOLID & Design Patterns in Laravel',
                'content'       => <<<'MD'
## Repository Pattern, SOLID & Design Patterns in Laravel

### Why Patterns?

Controllers and models can become dumping grounds. Applying design patterns separates concerns, makes code testable, and lets you swap implementations without touching business logic.

### Repository Pattern

Abstracts data access behind an interface, decoupling business logic from Eloquent.

```php
// 1. Define the contract
interface PostRepositoryInterface
{
    public function findPublished(int $limit): Collection;
    public function findBySlug(string $slug): ?Post;
    public function create(array $data): Post;
}

// 2. Eloquent implementation
class EloquentPostRepository implements PostRepositoryInterface
{
    public function findPublished(int $limit): Collection
    {
        return Post::published()->with('user', 'tags')->latest()->limit($limit)->get();
    }

    public function findBySlug(string $slug): ?Post
    {
        return Post::where('slug', $slug)->with('user')->first();
    }

    public function create(array $data): Post
    {
        return Post::create($data);
    }
}

// 3. Bind in AppServiceProvider
$this->app->bind(PostRepositoryInterface::class, EloquentPostRepository::class);

// 4. Inject in controller — depends on the interface, not Eloquent
class PostController extends Controller
{
    public function __construct(private PostRepositoryInterface $posts) {}
}
```

If you later switch to a Redis-backed or API-backed repository, only the binding changes. The controller stays untouched.

### Service Layer

Extract business logic into dedicated Service classes to keep repositories focused on data and controllers thin:

```php
class PostService
{
    public function __construct(
        private PostRepositoryInterface $posts,
        private TagService $tags,
    ) {}

    public function publish(Post $post, array $tagIds): Post
    {
        $post->update(['published_at' => now()]);
        $this->tags->sync($post, $tagIds);
        event(new PostPublished($post));
        return $post;
    }
}
```

### SOLID in Laravel Context

| Principle | Laravel Application |
|-----------|-------------------|
| **S**ingle Responsibility | One class per concern — PostService handles publishing, not emailing |
| **O**pen/Closed | Extend via new implementations, not by modifying existing ones |
| **L**iskov Substitution | Any implementation of PostRepositoryInterface can replace another |
| **I**nterface Segregation | Separate `ReadableRepository` from `WritableRepository` if clients need only one |
| **D**ependency Inversion | Depend on `PostRepositoryInterface`, not `EloquentPostRepository` |

### Data Transfer Objects (DTOs)

Pass typed data between layers instead of raw arrays:

```php
readonly class CreatePostData
{
    public function __construct(
        public string $title,
        public string $body,
        public int    $authorId,
        public array  $tagIds = [],
    ) {}

    public static function fromRequest(StorePostRequest $request): self
    {
        return new self(
            title:    $request->input('title'),
            body:     $request->input('body'),
            authorId: auth()->id(),
            tagIds:   $request->input('tags', []),
        );
    }
}
```
MD,
                'display_order' => 1,

            ],
            [
                'topic_id'      => $topics['laravel-level-4-patterns']->id,
                'title'         => 'Advanced Eloquent: Observers, Scopes & Performance Tuning',
                'content'       => <<<'MD'
## Advanced Eloquent: Observers, Scopes & Performance Tuning

### Model Observers

Observers listen to Eloquent model lifecycle events: `creating`, `created`, `updating`, `updated`, `saving`, `saved`, `deleting`, `deleted`, `restored`, `forceDeleted`.

```php
php artisan make:observer PostObserver --model=Post

class PostObserver
{
    public function creating(Post $post): void
    {
        $post->slug = Str::slug($post->title);
    }

    public function deleting(Post $post): void
    {
        $post->tags()->detach(); // clean up pivot before delete
    }
}

// Register in AppServiceProvider::boot()
Post::observe(PostObserver::class);
```

### Global vs Local Scopes

**Global scope** — automatically applied to every query on the model (e.g., soft deletes, tenant isolation):

```php
class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where('tenant_id', auth()->user()->tenant_id);
    }
}

class Post extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());
    }
}
```

**Local scope** — opt-in, chainable query helpers:

```php
public function scopeFeatured(Builder $q): void
{
    $q->where('featured', true)->whereNotNull('published_at');
}

Post::featured()->latest()->get();
```

### select() — Avoid SELECT *

Loading only the columns you need dramatically reduces memory usage:

```php
// Instead of fetching 20 columns:
Post::select('id', 'title', 'slug', 'published_at')->published()->get();
```

### Preventing Lazy Loading

In production, lazy loading silently causes N+1. Enable strict mode to make it throw:

```php
// AppServiceProvider::boot()
Model::preventLazyLoading(! app()->isProduction());
```

### Raw Expressions & DB::select()

For complex queries, drop to raw SQL when Eloquent becomes unwieldy:

```php
$results = DB::select('
    SELECT users.id, COUNT(posts.id) as post_count
    FROM users
    LEFT JOIN posts ON posts.user_id = users.id
    GROUP BY users.id
    HAVING post_count > ?
', [10]);
```

### Indexes & Query Optimization

- Add indexes in migrations for columns used in WHERE / ORDER BY / JOIN
- Use `EXPLAIN` to inspect query plans
- `remember()` caches a query result: `Post::published()->remember(60)->get()`
- Use database-level pagination (`paginate()`) instead of `all()` + PHP slice
MD,
                'display_order' => 2,

            ],
            [
                'topic_id'      => $topics['laravel-level-4-patterns']->id,
                'title'         => 'Testing Laravel: Feature Tests, Unit Tests & Mocking',
                'content'       => <<<'MD'
## Testing Laravel: Feature Tests, Unit Tests & Mocking

### Test Types

| Type | Scope | Speed | What it tests |
|------|-------|-------|---------------|
| Unit | Single class | Fast | Pure logic, no framework |
| Feature | HTTP request → DB | Medium | End-to-end controller flow |
| Browser | Real browser | Slow | UI interaction (Dusk) |

### Feature Tests

Feature tests make real HTTP requests against your application, including middleware, routing, and database.

```php
class PostTest extends TestCase
{
    use RefreshDatabase; // wraps each test in a transaction and rolls back

    public function test_authenticated_user_can_create_post(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/posts', [
            'title' => 'Test Post',
            'body'  => 'Some body content here.',
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('title', 'Test Post');

        $this->assertDatabaseHas('posts', ['title' => 'Test Post', 'user_id' => $user->id]);
    }

    public function test_guest_cannot_create_post(): void
    {
        $this->postJson('/api/posts', ['title' => 'Fail'])
             ->assertStatus(401);
    }
}
```

### Unit Tests

```php
class SlugGeneratorTest extends TestCase
{
    public function test_generates_slug_from_title(): void
    {
        $generator = new SlugGenerator();
        $this->assertSame('hello-world', $generator->generate('Hello World'));
    }
}
```

### Mocking

Use `$this->mock()` to replace real implementations with controlled fakes, isolating the code under test.

```php
public function test_sends_welcome_email_on_registration(): void
{
    Mail::fake(); // intercept all mail

    $this->postJson('/api/register', [
        'name'     => 'Alice',
        'email'    => 'alice@example.com',
        'password' => 'password123',
    ]);

    Mail::assertSent(WelcomeMail::class, fn ($m) => $m->hasTo('alice@example.com'));
}

// Mock a service
public function test_publishes_post_via_service(): void
{
    $mock = $this->mock(PostService::class);
    $mock->shouldReceive('publish')->once()->andReturn(Post::factory()->make());

    $this->actingAs(User::factory()->create())
         ->postJson('/api/posts/1/publish')
         ->assertOk();
}
```

### Other Test Fakes

```php
Queue::fake();  // assert jobs were dispatched
Event::fake();  // assert events were fired
Storage::fake('s3'); // test file uploads without real S3
Http::fake([   // mock external HTTP calls
    'api.stripe.com/*' => Http::response(['id' => 'ch_123'], 200),
]);
```

### `RefreshDatabase` vs `DatabaseTransactions`

- `RefreshDatabase` — runs migrations fresh + wraps in transaction. Use when you need a clean schema.
- `DatabaseTransactions` — only wraps in transaction (no re-migration). Faster but relies on existing schema.
MD,
                'display_order' => 3,

            ],

            // Level 5
            [
                'topic_id'      => $topics['laravel-level-5-expert']->id,
                'title'         => 'Laravel Octane, Caching & Performance at Scale',
                'content'       => <<<'MD'
## Laravel Octane, Caching & Performance at Scale

### Laravel Octane

Octane supercharges performance by booting your application once and keeping it in memory across requests, eliminating the bootstrap overhead of each request.

```bash
composer require laravel/octane
php artisan octane:install  # choose: swoole or roadrunner or frankenphp
php artisan octane:start --workers=8 --task-workers=4
```

**Key difference from FPM:** With PHP-FPM each request bootstraps from scratch. With Octane the app is booted once. This means:
- Static properties persist between requests (can cause memory leaks / state leakage)
- Singletons are shared — be careful with stateful services
- Always test for memory leaks: `php artisan octane:start --watch`

### Cache Strategies

```php
// Cache-aside pattern
$users = Cache::remember('active-users', now()->addMinutes(30), function () {
    return User::active()->get();
});

// Forever cache (invalidate manually)
Cache::rememberForever('settings', fn () => Setting::all()->keyBy('key'));

// Tagged cache — invalidate a group at once
Cache::tags(['posts', 'user-1'])->put('user-1-posts', $posts, 3600);
Cache::tags(['posts'])->flush(); // clears all post caches
```

Cache drivers by use case:
| Driver | Best for |
|--------|---------|
| `file` | Development |
| `redis` | Production — fast, supports tags, pub/sub |
| `memcached` | High-throughput, key-value only |
| `dynamodb` | AWS serverless environments |

### Query Result Caching

```php
// With Eloquent + a caching package (e.g., Watson/Rememberable)
Post::with('user')->published()->remember(60)->get();

// Manual approach
$cacheKey = "posts.{$category}.page.{$page}";
return Cache::remember($cacheKey, 600, fn () =>
    Post::where('category', $category)->paginate(15)
);
```

### HTTP Response Caching

```php
// Etag + Last-Modified headers
return response()->json($data)
    ->setEtag(md5(serialize($data)))
    ->setLastModified($post->updated_at)
    ->isNotModified($request) ? response('', 304) : null;
```

### Rate Limiting

```php
// RouteServiceProvider or routes/api.php
Route::middleware('throttle:api')->group(function () { ... });

// Custom limiter in AppServiceProvider
RateLimiter::for('uploads', function (Request $request) {
    return Limit::perMinute(5)->by($request->user()->id);
});
```

### Profiling with Telescope & Pulse

**Telescope** (development): inspects requests, queries, jobs, exceptions, mail, and cache in real time.

**Pulse** (production-safe): aggregates metrics over time — slow queries, user activity, queue health — with minimal overhead. Displayed at `/pulse`.

```bash
composer require laravel/telescope --dev
composer require laravel/pulse
php artisan pulse:work  # starts the aggregation worker
```
MD,
                'display_order' => 1,

            ],
            [
                'topic_id'      => $topics['laravel-level-5-expert']->id,
                'title'         => 'Multi-Tenancy, Microservices & Advanced Architecture',
                'content'       => <<<'MD'
## Multi-Tenancy, Microservices & Advanced Architecture

### Multi-Tenancy Approaches

Multi-tenancy means serving multiple customers (tenants) from a single application instance.

**Single database (row-level isolation):**
```php
// Global scope isolates all queries by tenant automatically
class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where('tenant_id', Tenant::current()->id);
    }
}
```
- Pros: simple deployment, shared infrastructure cost
- Cons: one slow query can affect all tenants, data isolation risk

**Separate databases per tenant:**
```php
// Dynamically switch connection at request start
config(['database.connections.tenant.database' => $tenant->db_name]);
DB::purge('tenant');
DB::reconnect('tenant');
```
- Pros: full data isolation, per-tenant backups
- Cons: migration complexity (`artisan tenants:migrate`)

**Spatie Laravel Multitenancy** and **tenancy/tenancy** are popular packages for both approaches.

### Service-Oriented Architecture in Laravel

Large monoliths can be split into services that communicate via:

1. **Synchronous HTTP** — `Http::post('order-service/api/orders', $data)`
2. **Asynchronous events** — publish to Redis/SQS queue, other services consume
3. **gRPC** — strongly-typed, binary protocol for internal services

```php
// HTTP client with retry + timeout for internal service calls
Http::baseUrl('https://payments.internal')
    ->timeout(5)
    ->retry(3, 100)
    ->withToken($this->serviceToken)
    ->post('/charge', ['amount' => 5000, 'currency' => 'USD']);
```

### Laravel Pipeline

The Pipeline pattern chains a payload through a series of handlers (stages), each transforming or validating it:

```php
$result = Pipeline::send($order)
    ->through([
        ValidateStock::class,
        ApplyDiscount::class,
        ChargeCreditCard::class,
        SendConfirmationEmail::class,
    ])
    ->thenReturn();
```

Each stage:
```php
class ValidateStock
{
    public function handle(Order $order, Closure $next): mixed
    {
        if (! $order->hasStock()) {
            throw new OutOfStockException();
        }
        return $next($order);
    }
}
```

This is how Laravel's own middleware stack works.

### Macro System

Extend core Laravel classes without modifying them:

```php
// AppServiceProvider::boot()
Collection::macro('toAssoc', function () {
    return $this->mapWithKeys(fn ($item) => [$item['key'] => $item['value']]);
});

// Usage anywhere
collect($items)->toAssoc();
```

Macros work on: `Collection`, `Builder`, `Request`, `Response`, `Str`, `Arr`, and more.
MD,
                'display_order' => 2,

            ],
            [
                'topic_id'      => $topics['laravel-level-5-expert']->id,
                'title'         => 'Production Laravel: Deployment, CI/CD & Monitoring',
                'content'       => <<<'MD'
## Production Laravel: Deployment, CI/CD & Monitoring

### Production Optimization Commands

Run these as part of every deployment:

```bash
php artisan config:cache    # compile all config files into one cached file
php artisan route:cache     # compile route registration into a single file
php artisan view:cache      # pre-compile all Blade templates
php artisan event:cache     # cache event-listener mapping (Laravel 11+)
php artisan optimize        # runs config:cache + route:cache + view:cache together

# Clear all caches (never run optimize in development — it ignores .env changes)
php artisan optimize:clear
```

### Zero-Downtime Deployment

Deploy without dropping requests using Envoyer, Deployer, or custom scripts:

1. Checkout new release into a new directory (`releases/2024-01-15-120000/`)
2. Run `composer install --no-dev --optimize-autoloader`
3. Copy `.env` + shared directories (storage, uploads)
4. Run `php artisan migrate --force`
5. Warm caches (`optimize`, `queue:restart`)
6. Atomically swap the `current` symlink to the new release
7. Reload PHP-FPM / Octane workers

The symlink swap is atomic — no request sees a partially-deployed state.

### Supervisor Configuration

Supervisor keeps queue workers alive in production:

```ini
[program:laravel-worker]
command=php /var/www/artisan queue:work redis --queue=high,default --tries=3 --timeout=90
directory=/var/www
autostart=true
autorestart=true
stopasgroup=true
numprocs=4
redirect_stderr=true
stdout_logfile=/var/log/laravel-worker.log
```

### Scheduled Tasks & Cron

```php
// app/Console/Kernel.php or routes/console.php (Laravel 11)
Schedule::command('reports:daily')->dailyAt('06:00')->withoutOverlapping();
Schedule::job(ProcessPendingPayments::class)->everyFiveMinutes();
Schedule::call(fn () => Cache::flush())->weekly()->mondays()->at('00:00');
```

`withoutOverlapping()` ensures only one instance runs even if the previous one hasn't finished.

Single server cron entry:
```bash
* * * * * cd /var/www && php artisan schedule:run >> /dev/null 2>&1
```

### Health Checks & Monitoring

```php
// Laravel 11 built-in health check endpoint
Route::get('/up', function () {
    return response()->json(['status' => 'ok']);
});
```

Monitor:
- **Telescope** (dev) — query inspector, exception tracker, job monitor
- **Pulse** (prod) — slow queries, user activity, queue depth
- **Sentry / Bugsnag** — exception tracking with stack traces
- **Prometheus + Grafana** — infrastructure and app metrics
- Queue depth, failed job count, and job processing time are key SLIs for queue-heavy apps
MD,
                'display_order' => 3,

            ],
        ];

        foreach ($lessons as $lesson) {
            DB::table('lessons')->updateOrInsert(
                ['topic_id' => $lesson['topic_id'], 'title' => $lesson['title']],
                array_merge($lesson, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        $this->command->info('Laravel Level 4: exam questions seeding...');
    }

    private function seedLevel4Questions(Topic $topic): void
    {
        $questions = [
            [
                'question' => 'What is the primary purpose of the Repository Pattern in Laravel?',
                'options'  => [
                    ['text' => 'To abstract data access behind an interface, decoupling business logic from Eloquent', 'is_correct' => true],
                    ['text' => 'To cache database query results automatically', 'is_correct' => false],
                    ['text' => 'To replace Eloquent with raw SQL queries', 'is_correct' => false],
                    ['text' => 'To handle HTTP request validation', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What is the difference between `bind()` and `singleton()` in the Laravel Service Container?',
                'options'  => [
                    ['text' => '`bind()` creates a new instance each resolution; `singleton()` creates once and reuses it', 'is_correct' => true],
                    ['text' => '`singleton()` creates a new instance each resolution; `bind()` reuses the same one', 'is_correct' => false],
                    ['text' => 'They are identical — both create a new instance each time', 'is_correct' => false],
                    ['text' => '`bind()` is for interfaces only; `singleton()` is for concrete classes only', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'In a Service Provider, what is the key difference between `register()` and `boot()`?',
                'options'  => [
                    ['text' => '`register()` is for bindings only — other services are not yet available; `boot()` runs after all providers are registered', 'is_correct' => true],
                    ['text' => '`register()` runs after `boot()` completes', 'is_correct' => false],
                    ['text' => '`boot()` is only for route definitions', 'is_correct' => false],
                    ['text' => 'Both methods run at the same time during request startup', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What does an Eloquent Observer do?',
                'options'  => [
                    ['text' => 'It listens to model lifecycle events (creating, created, updating, deleting, etc.) and runs logic automatically', 'is_correct' => true],
                    ['text' => 'It monitors database query performance and logs slow queries', 'is_correct' => false],
                    ['text' => 'It enforces validation rules before saving a model', 'is_correct' => false],
                    ['text' => 'It caches model data in Redis on every save', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What is a Global Scope in Eloquent and when would you use one?',
                'options'  => [
                    ['text' => 'A constraint automatically applied to every query on a model — used for soft deletes, tenant isolation, etc.', 'is_correct' => true],
                    ['text' => 'A scope that can be shared across multiple models', 'is_correct' => false],
                    ['text' => 'A database-level index that speeds up global queries', 'is_correct' => false],
                    ['text' => 'A method that runs before any Artisan command', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What is the N+1 query problem and how does eager loading solve it?',
                'options'  => [
                    ['text' => 'N+1 fires one extra query per item in a loop; eager loading (`with()`) fetches all related records in 2 queries total', 'is_correct' => true],
                    ['text' => 'N+1 means 1 query runs N times due to caching errors; `with()` disables caching', 'is_correct' => false],
                    ['text' => 'N+1 is a pagination issue; eager loading sets the page size to 1', 'is_correct' => false],
                    ['text' => 'N+1 is caused by missing indexes; eager loading adds indexes automatically', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What is `RefreshDatabase` trait used for in Laravel tests?',
                'options'  => [
                    ['text' => 'It wraps each test in a transaction and rolls back after, ensuring a clean database state', 'is_correct' => true],
                    ['text' => 'It drops and re-creates the entire database before every test', 'is_correct' => false],
                    ['text' => 'It seeds the database with factory data before every test', 'is_correct' => false],
                    ['text' => 'It disables database writes during tests', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What does `$this->mock(PaymentService::class)` do in a Laravel Feature Test?',
                'options'  => [
                    ['text' => 'It replaces the real PaymentService in the container with a mock, preventing real payments in tests', 'is_correct' => true],
                    ['text' => 'It creates a new PaymentService with a test API key', 'is_correct' => false],
                    ['text' => 'It queues the PaymentService to run after the test completes', 'is_correct' => false],
                    ['text' => 'It records all calls to PaymentService and logs them', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What is a Laravel Contract (interface) and why use it instead of a Facade?',
                'options'  => [
                    ['text' => 'A Contract is an interface that defines a service\'s API — using it enables type hinting, better IDE support, and easier swapping of implementations', 'is_correct' => true],
                    ['text' => 'A Contract is a route group with shared middleware', 'is_correct' => false],
                    ['text' => 'A Contract is a validation rule set that can be reused across requests', 'is_correct' => false],
                    ['text' => 'Contracts are only for Eloquent models', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What is a Data Transfer Object (DTO) and what problem does it solve in Laravel?',
                'options'  => [
                    ['text' => 'A typed object that carries data between layers (e.g., request → service), replacing raw arrays with a defined shape', 'is_correct' => true],
                    ['text' => 'A Laravel model that syncs data with an external API', 'is_correct' => false],
                    ['text' => 'A database seeder that transfers data between tables', 'is_correct' => false],
                    ['text' => 'An Eloquent resource that transforms models to JSON', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What does `Queue::fake()` do when used in a test?',
                'options'  => [
                    ['text' => 'It intercepts all job dispatches so they are not actually queued, allowing you to assert which jobs were dispatched', 'is_correct' => true],
                    ['text' => 'It runs all queued jobs synchronously during the test', 'is_correct' => false],
                    ['text' => 'It prevents any jobs from being created for the test duration', 'is_correct' => false],
                    ['text' => 'It connects to a test Redis instance for realistic queue behavior', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What does the `withoutOverlapping()` modifier do on a scheduled task?',
                'options'  => [
                    ['text' => 'Ensures only one instance of the task runs at a time, even if the previous run is still in progress', 'is_correct' => true],
                    ['text' => 'Runs the task multiple times in parallel to improve throughput', 'is_correct' => false],
                    ['text' => 'Prevents the task from running if another scheduled task is running', 'is_correct' => false],
                    ['text' => 'Disables task scheduling during peak hours', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What does `Model::preventLazyLoading()` do when called in `AppServiceProvider::boot()`?',
                'options'  => [
                    ['text' => 'Throws an exception whenever a relationship is accessed without eager loading — preventing silent N+1 queries', 'is_correct' => true],
                    ['text' => 'Disables all relationship loading in the application', 'is_correct' => false],
                    ['text' => 'Caches all lazy-loaded relationships in Redis automatically', 'is_correct' => false],
                    ['text' => 'Converts all lazy loads to chunked queries', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'In the Repository Pattern, why should the controller depend on an interface (`PostRepositoryInterface`) rather than the concrete class (`EloquentPostRepository`)?',
                'options'  => [
                    ['text' => 'So the implementation can be swapped (e.g., to a cached or API-backed repository) without changing the controller', 'is_correct' => true],
                    ['text' => 'Because PHP does not allow controllers to depend on concrete classes', 'is_correct' => false],
                    ['text' => 'To prevent Eloquent from executing queries directly in the controller', 'is_correct' => false],
                    ['text' => 'Interfaces make the code run faster than concrete classes', 'is_correct' => false],
                ],
            ],
        ];

        $this->insertQuestions($topic, $questions);
        $this->command->info("Laravel Level 4: {$this->countQuestions($questions)} questions total.");
    }

    private function seedLevel5Questions(Topic $topic): void
    {
        $questions = [
            [
                'question' => 'What is Laravel Octane and what key difference does it have from traditional PHP-FPM?',
                'options'  => [
                    ['text' => 'Octane boots the app once and keeps it in memory across requests; PHP-FPM bootstraps fresh on every request', 'is_correct' => true],
                    ['text' => 'Octane is a caching layer that stores Eloquent results in Redis automatically', 'is_correct' => false],
                    ['text' => 'Octane is Laravel\'s queue driver for high-throughput job processing', 'is_correct' => false],
                    ['text' => 'Octane replaces Blade with a faster compiled template engine', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What is the critical risk when using Laravel Octane with stateful singletons?',
                'options'  => [
                    ['text' => 'Singleton state persists between requests — a singleton mutated in request A can leak stale data into request B', 'is_correct' => true],
                    ['text' => 'Singletons are not supported in Octane and will cause boot errors', 'is_correct' => false],
                    ['text' => 'Octane recreates all singletons on every request, removing their performance benefit', 'is_correct' => false],
                    ['text' => 'Singletons in Octane are shared across different worker processes', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What is the difference between `Cache::remember()` and `Cache::rememberForever()`?',
                'options'  => [
                    ['text' => '`remember()` stores the value with a TTL; `rememberForever()` stores indefinitely until explicitly deleted', 'is_correct' => true],
                    ['text' => '`rememberForever()` stores across all cache drivers simultaneously; `remember()` is driver-specific', 'is_correct' => false],
                    ['text' => '`remember()` loads from cache on every call; `rememberForever()` loads only on the first call', 'is_correct' => false],
                    ['text' => 'They are identical — both expire after the default cache TTL', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What is Laravel Pulse used for and how does it differ from Telescope?',
                'options'  => [
                    ['text' => 'Pulse aggregates production performance metrics over time with low overhead; Telescope is a development-only real-time inspector', 'is_correct' => true],
                    ['text' => 'Telescope is for production monitoring; Pulse is for development debugging', 'is_correct' => false],
                    ['text' => 'Pulse is a queue monitoring tool only; Telescope covers all aspects', 'is_correct' => false],
                    ['text' => 'They are interchangeable — use either in any environment', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What is the Laravel Pipeline pattern and how does Laravel itself use it?',
                'options'  => [
                    ['text' => 'A chain of handlers that each transform a payload and pass it along; Laravel\'s HTTP middleware stack is built on this pattern', 'is_correct' => true],
                    ['text' => 'A build tool that compiles assets in a sequential pipeline', 'is_correct' => false],
                    ['text' => 'A queue driver that processes jobs in strict sequential order', 'is_correct' => false],
                    ['text' => 'A pattern for chaining Eloquent query scopes', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What are the two main approaches to multi-tenancy in a Laravel application?',
                'options'  => [
                    ['text' => 'Single database with `tenant_id` column (row-level isolation) vs separate database per tenant', 'is_correct' => true],
                    ['text' => 'One Laravel app per tenant vs one Kubernetes pod per tenant', 'is_correct' => false],
                    ['text' => 'Redis-based sessions vs database-based sessions per tenant', 'is_correct' => false],
                    ['text' => 'Single Eloquent model vs per-tenant Eloquent model classes', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What does `php artisan optimize` do and why should you NOT run it in development?',
                'options'  => [
                    ['text' => 'It caches config, routes, and views for performance; in dev it hides .env changes because cached config overrides it', 'is_correct' => true],
                    ['text' => 'It minifies PHP files — in dev this removes comments needed for debugging', 'is_correct' => false],
                    ['text' => 'It locks composer.lock — in dev this prevents package updates', 'is_correct' => false],
                    ['text' => 'It compresses the database — in dev this corrupts test data', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What is a Lazy Collection in Laravel and when should you use it over `get()`?',
                'options'  => [
                    ['text' => 'A generator-backed collection that loads one record at a time — use when processing millions of rows to avoid loading them all into memory', 'is_correct' => true],
                    ['text' => 'A collection that defers filtering to the database — always faster than `get()`', 'is_correct' => false],
                    ['text' => 'A collection that caches results in Redis automatically', 'is_correct' => false],
                    ['text' => 'A collection that lazy-loads relationships — identical to `with()` but with lower overhead', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What is the difference between `queue:work` and `queue:listen` in Laravel?',
                'options'  => [
                    ['text' => '`queue:work` loads the app once and processes until stopped (production); `queue:listen` restarts on every job (useful in dev, slower)', 'is_correct' => true],
                    ['text' => '`queue:listen` handles high-priority queues; `queue:work` handles low-priority queues', 'is_correct' => false],
                    ['text' => '`queue:work` runs all queued jobs synchronously; `queue:listen` runs them asynchronously', 'is_correct' => false],
                    ['text' => 'They are identical — `queue:listen` is just an alias for `queue:work`', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What is a zero-downtime deployment strategy for a Laravel application?',
                'options'  => [
                    ['text' => 'Deploy to a new release directory, run migrations, atomically swap a symlink — so in-flight requests are never interrupted', 'is_correct' => true],
                    ['text' => 'Put the app in maintenance mode, deploy, then disable maintenance mode', 'is_correct' => false],
                    ['text' => 'Run `php artisan migrate:fresh` on production — it only takes a few seconds', 'is_correct' => false],
                    ['text' => 'Restart PHP-FPM with `--graceful` flag so it finishes current requests first', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What does a Collection Macro allow you to do in Laravel?',
                'options'  => [
                    ['text' => 'Add custom methods to the Collection class without modifying the framework source', 'is_correct' => true],
                    ['text' => 'Define a reusable Eloquent query that can be applied to any collection', 'is_correct' => false],
                    ['text' => 'Cache a collection result and replay it on subsequent calls', 'is_correct' => false],
                    ['text' => 'Persist a collection to a database table with a single call', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'In tagged cache (`Cache::tags()`), what does `Cache::tags([\'posts\'])->flush()` do?',
                'options'  => [
                    ['text' => 'Deletes all cache entries tagged with \'posts\', leaving other cache entries untouched', 'is_correct' => true],
                    ['text' => 'Flushes the entire cache store regardless of tags', 'is_correct' => false],
                    ['text' => 'Resets the TTL of all \'posts\' tagged entries to the default', 'is_correct' => false],
                    ['text' => 'Converts all \'posts\' tagged entries from memory to disk cache', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What is the purpose of Supervisor in a Laravel production environment?',
                'options'  => [
                    ['text' => 'To keep queue workers running continuously — restarting them automatically if they crash', 'is_correct' => true],
                    ['text' => 'To manage PHP-FPM worker pools and scale them based on CPU load', 'is_correct' => false],
                    ['text' => 'To monitor slow database queries and log them to Telescope', 'is_correct' => false],
                    ['text' => 'To supervise scheduled task execution and prevent overlapping runs', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'When using Laravel\'s HTTP client to call an internal service, what does `.retry(3, 100)` do?',
                'options'  => [
                    ['text' => 'Retries the request up to 3 times with 100ms delay between attempts if it fails', 'is_correct' => true],
                    ['text' => 'Sets a timeout of 3 seconds and a connection timeout of 100ms', 'is_correct' => false],
                    ['text' => 'Queues 3 parallel requests and returns the first successful response', 'is_correct' => false],
                    ['text' => 'Caches the response for 3 minutes and refreshes every 100 seconds', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What is Laravel Sanctum\'s token scoping feature used for?',
                'options'  => [
                    ['text' => 'To limit what actions a specific API token can perform — e.g., a read-only token that cannot write', 'is_correct' => true],
                    ['text' => 'To restrict which IP addresses can use a token', 'is_correct' => false],
                    ['text' => 'To assign tokens to specific database tables for row-level security', 'is_correct' => false],
                    ['text' => 'To set the token\'s expiry time by scope name', 'is_correct' => false],
                ],
            ],
        ];

        $this->insertQuestions($topic, $questions);
        $this->command->info("Laravel Level 5: {$this->countQuestions($questions)} questions total.");
    }

    private function insertQuestions(Topic $topic, array $questions): void
    {
        foreach ($questions as $qData) {
            $exists = Question::where('topic_id', $topic->id)
                ->where('question', $qData['question'])
                ->exists();

            if ($exists) {
                continue;
            }

            $question = Question::create([
                'topic_id'   => $topic->id,
                'question'   => $qData['question'],
                'type'       => 'MCQ',
                'difficulty' => 'Medium',
            ]);

            foreach ($qData['options'] as $opt) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $opt['text'],
                    'is_correct'  => $opt['is_correct'],
                ]);
            }
        }
    }

    private function countQuestions(array $questions): int
    {
        return count($questions);
    }
}
