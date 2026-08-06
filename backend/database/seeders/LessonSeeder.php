<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        $topicId = fn (string $slug) => DB::table('topics')->where('slug', $slug)->value('id');

        $lessons = [
            // PHP Variables & Data Types
            [
                'topic_id'          => $topicId('php-variables-data-types'),
                'title'             => 'Scalar Types in PHP',
                'content'           => "PHP has four scalar types: int, float, string, and bool.\n\n**int**: Whole numbers. Example: `\$age = 25;`\n**float**: Decimal numbers. Example: `\$price = 9.99;`\n**string**: Text. Example: `\$name = 'Alice';`\n**bool**: True or false. Example: `\$active = true;`\n\nPHP is dynamically typed — you don't declare types explicitly (though you can with strict_types). Type juggling happens automatically.\n\n```php\n\$a = '5' + 3; // int 8 — string coerced to int\n```\n\nUse `gettype()` or `var_dump()` to inspect types at runtime.",
                'estimated_minutes' => 10,
                'display_order'     => 1,
            ],
            [
                'topic_id'          => $topicId('php-variables-data-types'),
                'title'             => 'Arrays in PHP',
                'content'           => "PHP arrays are ordered maps — they can act as lists, dictionaries, or both.\n\n**Indexed array**:\n```php\n\$fruits = ['apple', 'banana', 'cherry'];\necho \$fruits[0]; // 'apple'\n```\n\n**Associative array**:\n```php\n\$user = ['name' => 'Alice', 'age' => 25];\necho \$user['name']; // 'Alice'\n```\n\n**Nested array**:\n```php\n\$matrix = [[1, 2], [3, 4]];\n```\n\nCommon functions: `array_push()`, `array_pop()`, `array_map()`, `array_filter()`, `array_keys()`, `count()`.",
                'estimated_minutes' => 12,
                'display_order'     => 2,
            ],

            // PHP Functions & Closures
            [
                'topic_id'          => $topicId('php-functions-closures'),
                'title'             => 'Named Functions',
                'content'           => "A named function is defined with the `function` keyword and can be called anywhere in the same scope.\n\n```php\nfunction greet(string \$name): string\n{\n    return \"Hello, \$name!\";\n}\n\necho greet('Alice'); // Hello, Alice!\n```\n\n**Type declarations** (PHP 8+): parameter types and return types make code self-documenting and catch bugs early.\n\n**Default parameters**:\n```php\nfunction connect(string \$host = 'localhost', int \$port = 3306): void { ... }\n```\n\nFunctions are first-class in PHP 8.1+ via the syntax `strlen(...)` (first-class callable syntax).",
                'estimated_minutes' => 10,
                'display_order'     => 1,
            ],
            [
                'topic_id'          => $topicId('php-functions-closures'),
                'title'             => 'Closures & Arrow Functions',
                'content'           => "A **closure** is an anonymous function that can capture variables from its surrounding scope using `use`.\n\n```php\n\$multiplier = 3;\n\$multiply = function (int \$n) use (\$multiplier): int {\n    return \$n * \$multiplier;\n};\n\necho \$multiply(5); // 15\n```\n\n**Arrow functions** (PHP 7.4+) capture the outer scope implicitly:\n```php\n\$multiplier = 3;\n\$multiply = fn (int \$n) => \$n * \$multiplier;\n```\n\nClosures are heavily used with `array_map`, `array_filter`, and Laravel's collection methods.",
                'estimated_minutes' => 12,
                'display_order'     => 2,
            ],

            // OOP in PHP
            [
                'topic_id'          => $topicId('php-oop'),
                'title'             => 'Classes, Properties & Methods',
                'content'           => "A **class** is a blueprint for objects.\n\n```php\nclass User\n{\n    public function __construct(\n        private string \$name,\n        private string \$email,\n    ) {}\n\n    public function getName(): string\n    {\n        return \$this->name;\n    }\n}\n\n\$user = new User('Alice', 'alice@example.com');\necho \$user->getName(); // Alice\n```\n\n**Constructor property promotion** (PHP 8.0+): declare and assign properties directly in the constructor signature — no need for separate property declarations.",
                'estimated_minutes' => 15,
                'display_order'     => 1,
            ],
            [
                'topic_id'          => $topicId('php-oop'),
                'title'             => 'Interfaces & Traits',
                'content'           => "**Interfaces** define a contract — the implementing class must provide all listed methods.\n\n```php\ninterface Notifiable\n{\n    public function notify(string \$message): void;\n}\n\nclass User implements Notifiable\n{\n    public function notify(string \$message): void\n    {\n        // send email...\n    }\n}\n```\n\n**Traits** are reusable method bundles you mix into classes:\n```php\ntrait HasTimestamps\n{\n    public function createdAt(): string { return \$this->created_at; }\n}\n\nclass Post { use HasTimestamps; }\n```\n\nUse interfaces for type contracts; use traits for shared behaviour that doesn't fit inheritance.",
                'estimated_minutes' => 15,
                'display_order'     => 2,
            ],

            // Eloquent ORM
            [
                'topic_id'          => $topicId('laravel-eloquent'),
                'title'             => 'Eloquent Relationships',
                'content'           => "Eloquent relationships map database foreign keys to PHP methods.\n\n**hasMany / belongsTo** (one-to-many):\n```php\n// User has many Posts\npublic function posts(): HasMany\n{\n    return \$this->hasMany(Post::class);\n}\n\n// Post belongs to User\npublic function user(): BelongsTo\n{\n    return \$this->belongsTo(User::class);\n}\n```\n\n**Usage**:\n```php\n\$posts = User::find(1)->posts; // Collection of Post models\n\$author = Post::find(1)->user; // User model\n```\n\nAlways eager-load related data to avoid N+1 queries:\n```php\n\$users = User::with('posts')->get();\n```",
                'estimated_minutes' => 20,
                'display_order'     => 1,
            ],
            [
                'topic_id'          => $topicId('laravel-eloquent'),
                'title'             => 'Query Builder vs Eloquent',
                'content'           => "**Eloquent** is the ActiveRecord ORM — you work with model objects.\n```php\n\$user = User::where('email', 'alice@example.com')->first();\n```\n\n**Query Builder** works at the DB level — returns arrays/stdClass, not models.\n```php\n\$row = DB::table('users')->where('email', 'alice@example.com')->first();\n```\n\n**When to use which**:\n- Use Eloquent when you need model events, relationships, casting, or accessors.\n- Use Query Builder for bulk operations, complex joins, or when model overhead matters.\n\n**Tip**: Eloquent calls the query builder internally — you can always chain `->toSql()` to see the generated SQL.",
                'estimated_minutes' => 15,
                'display_order'     => 2,
            ],

            // REST Principles
            [
                'topic_id'          => $topicId('rest-principles'),
                'title'             => 'HTTP Methods & Status Codes',
                'content'           => "REST maps CRUD operations to HTTP methods:\n\n| Method | Action | Typical Status |\n|--------|--------|----------------|\n| GET    | Read   | 200 OK         |\n| POST   | Create | 201 Created    |\n| PUT    | Full update | 200 OK   |\n| PATCH  | Partial update | 200 OK |\n| DELETE | Delete | 204 No Content |\n\n**Common error codes**:\n- 400 Bad Request — invalid input\n- 401 Unauthorized — not authenticated\n- 403 Forbidden — authenticated but not allowed\n- 404 Not Found — resource does not exist\n- 422 Unprocessable Entity — validation failed\n- 500 Internal Server Error — unexpected server failure\n\nAlways return a consistent response envelope: `{ success, message, data }`.",
                'estimated_minutes' => 12,
                'display_order'     => 1,
            ],

            // JavaScript Async
            [
                'topic_id'          => $topicId('js-async'),
                'title'             => 'Promises & async/await',
                'content'           => "A **Promise** represents a value that will be available in the future.\n\n```js\nfetch('/api/user')\n  .then(res => res.json())\n  .then(data => console.log(data))\n  .catch(err => console.error(err));\n```\n\n**async/await** is syntactic sugar over Promises — it makes async code read like synchronous code:\n\n```js\nasync function loadUser() {\n  try {\n    const res = await fetch('/api/user');\n    const data = await res.json();\n    console.log(data);\n  } catch (err) {\n    console.error(err);\n  }\n}\n```\n\n**Key rule**: `await` can only be used inside an `async` function. Unhandled promise rejections will crash Node and log warnings in browsers.",
                'estimated_minutes' => 15,
                'display_order'     => 1,
            ],

            // React Hooks
            [
                'topic_id'          => $topicId('react-hooks'),
                'title'             => 'useState & useEffect',
                'content'           => '`useState` manages local component state. `useEffect` runs side effects (data fetching, subscriptions).' . "\n\n" . '```tsx' . "\n" . 'import { useState, useEffect } from \'react\';' . "\n\n" . 'function UserProfile({ userId }: { userId: number }) {' . "\n" . '  const [user, setUser] = useState(null);' . "\n\n" . '  useEffect(() => {' . "\n" . '    fetch(`/api/users/${userId}`)' . "\n" . '      .then(r => r.json())' . "\n" . '      .then(data => setUser(data));' . "\n" . '  }, [userId]);' . "\n\n" . '  if (!user) return <p>Loading...</p>;' . "\n" . '  return <h1>{user.name}</h1>;' . "\n" . '}' . "\n" . '```' . "\n\n" . "**Dependency array rules**:\n- `[]` — run once after mount\n- `[value]` — run after mount and whenever `value` changes\n- no array — run after every render (almost never what you want)",
                'estimated_minutes' => 18,
                'display_order'     => 1,
            ],
        ];

        foreach ($lessons as $lesson) {
            DB::table('lessons')->updateOrInsert(
                ['topic_id' => $lesson['topic_id'], 'title' => $lesson['title']],
                array_merge($lesson, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]),
            );
        }
    }
}
