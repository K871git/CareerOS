<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhpLearningSeeder extends Seeder
{
    public function run(): void
    {
        $track = LearningTrack::firstOrCreate(
            ['slug' => 'full-stack-web-development'],
            [
                'title'         => 'Full Stack Web Development',
                'description'   => 'Master modern full stack web development from front-end to back-end.',
                'display_order' => 1,
            ]
        );

        $subject = Subject::firstOrCreate(
            ['slug' => 'php'],
            [
                'learning_track_id' => $track->id,
                'title'             => 'PHP',
                'description'       => 'PHP is the most widely used server-side scripting language for web development. Master it from basics to advanced patterns.',
                'display_order'     => 1,
            ]
        );

        // Assign correct levels to existing practice topics
        Topic::where('slug', 'php-basics-junior')->update(['level' => 1]);
        Topic::where('slug', 'php-intermediate')->update(['level' => 2]);
        Topic::where('slug', 'php-advanced')->update(['level' => 3]);

        $this->seedLessons($subject);

        $t1 = Topic::where('slug', 'php-basics-junior')->first();
        $t2 = Topic::where('slug', 'php-intermediate')->first();
        $t3 = Topic::where('slug', 'php-advanced')->first();

        $this->seedExamQuestions($t1, $this->level1Questions(), 'Easy', 'PHP Level 1');
        $this->seedExamQuestions($t2, $this->level2Questions(), 'Medium', 'PHP Level 2');
        $this->seedExamQuestions($t3, $this->level3Questions(), 'Hard', 'PHP Level 3');

        $this->command->info('PHP Learning seeder complete — 3 levels, lessons + exams.');
    }

    // ── LESSONS ──────────────────────────────────────────────────────────────

    private function seedLessons(Subject $subject): void
    {
        $t1 = Topic::where('slug', 'php-basics-junior')->first();
        $t2 = Topic::where('slug', 'php-intermediate')->first();
        $t3 = Topic::where('slug', 'php-advanced')->first();

        $lessons = [

            // ── LEVEL 1 ────────────────────────────────────────────────────
            [
                'topic_id'          => $t1->id,
                'title'             => 'Variables, Data Types & the PHP Type System',
                'estimated_minutes' => 15,
                'display_order'     => 1,
                'content'           => <<<'MARKDOWN'
## Variables in PHP

Variables in PHP are prefixed with a `$` sign and are loosely typed by default — the type is determined by the assigned value.

```php
$name   = 'Alice';   // string
$age    = 25;        // int
$price  = 9.99;      // float
$active = true;      // bool
$data   = null;      // NULL
```

Variable names are case-sensitive. `$Name` and `$name` are different variables.

## The 8 Primitive Types

| Type      | Example                        | Notes                            |
|-----------|--------------------------------|----------------------------------|
| int       | `42`, `-7`                     | Platform-dependent (64-bit)      |
| float     | `3.14`, `1.5e3`                | Double-precision IEEE 754        |
| string    | `"hello"`, `'world'`           | Byte sequence                    |
| bool      | `true`, `false`                | Case-insensitive                 |
| array     | `[1, 2, 3]`                    | Ordered map                      |
| object    | `new stdClass()`               | Instance of a class              |
| NULL      | `null`                         | Explicitly no value              |
| resource  | `fopen(...)` result            | External resource handle         |

Check a type with `gettype($var)` or the `is_*` family: `is_string()`, `is_int()`, `is_array()`, etc.

## Type Juggling and Casting

PHP automatically converts types based on context — this is called **type juggling**:

```php
$result = "5" + 3;        // 8 — "5" coerced to int
$result = "5 apples" + 2; // 7 — leading numeric string
$result = "" + 0;         // 0
```

**Explicit type casting**:
```php
$str = "42abc";
(int)$str;    // 42  — truncates at first non-numeric char
(float)$str;  // 42.0
(bool)"";     // false — empty string is falsy
(bool)"0";    // false — "0" is also falsy (PHP-specific!)
(array)$str;  // ['42abc']
```

**Falsy values in PHP**: `false`, `0`, `0.0`, `""`, `"0"`, `[]`, `null`.

Everything else is truthy — including `"false"`, `"0.0"`, `"00"`, and all non-empty arrays.

## Strict Types

By default PHP coerces types in function arguments. Enable strict typing to throw a `TypeError` instead:

```php
<?php
declare(strict_types=1); // must be the very first statement

function add(int $a, int $b): int {
    return $a + $b;
}

add(1, 2);     // 3 — fine
add(1, "2");   // TypeError in strict mode — "2" is not int
```

Always use `declare(strict_types=1)` in professional PHP code.

## String Interpolation

Double-quoted strings interpret variables; single-quoted strings do not:

```php
$name = 'World';
echo "Hello, $name!";       // Hello, World!
echo 'Hello, $name!';       // Hello, $name!  (literal)
echo "Value: {$obj->prop}"; // use curly braces for complex expressions

// Heredoc — like double-quoted, multiline
$text = <<<EOT
Hello, $name!
This spans multiple lines.
EOT;

// Nowdoc — like single-quoted, multiline, nothing is interpolated
$text = <<<'EOT'
Hello, $name!
EOT;
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t1->id,
                'title'             => 'Control Structures: Conditions, Loops & Match',
                'estimated_minutes' => 15,
                'display_order'     => 2,
                'content'           => <<<'MARKDOWN'
## Conditional Statements

**if / elseif / else**:
```php
$score = 85;

if ($score >= 90) {
    echo 'A';
} elseif ($score >= 80) {
    echo 'B';   // this runs
} else {
    echo 'C';
}
```

**Ternary operator** — short conditional:
```php
$label = $score >= 60 ? 'Pass' : 'Fail';
```

**Null coalescing operator (??)** — returns left side if set and not null, otherwise right:
```php
$username = $_GET['user'] ?? 'Guest';
// equivalent to: isset($_GET['user']) ? $_GET['user'] : 'Guest'

// Chainable
$city = $user->address?->city ?? 'Unknown';
```

**match expression** (PHP 8.0+) — strict comparison, returns a value, no fall-through:
```php
$status = 2;

$label = match($status) {
    1       => 'Pending',
    2, 3    => 'Active',      // multiple conditions per arm
    default => 'Inactive',
};
// $label = 'Active'
```

Unlike `switch`, `match` uses strict (`===`) comparison and throws `UnhandledMatchError` if no arm matches and there is no `default`.

## Loops

**for** — when the iteration count is known:
```php
for ($i = 0; $i < 5; $i++) {
    echo $i; // 0 1 2 3 4
}
```

**foreach** — iterate over arrays and objects:
```php
$fruits = ['apple', 'banana', 'cherry'];
foreach ($fruits as $fruit) {
    echo $fruit;
}

// With keys
$user = ['name' => 'Alice', 'age' => 25];
foreach ($user as $key => $value) {
    echo "$key: $value\n";
}
```

**while** — condition checked before each iteration:
```php
$n = 1;
while ($n <= 5) {
    echo $n++;
}
```

**do-while** — runs at least once, condition checked after:
```php
do {
    $input = readline('Enter a number: ');
} while (!is_numeric($input));
```

## Break & Continue

```php
foreach (range(1, 10) as $n) {
    if ($n % 2 === 0) continue; // skip even numbers
    if ($n > 7) break;          // stop after 7
    echo $n;                    // 1 3 5 7
}
```

`break 2` and `continue 2` work on nested loops — the number specifies how many loop levels to break/continue.
MARKDOWN,
            ],
            [
                'topic_id'          => $t1->id,
                'title'             => 'Functions, Arrays & String Manipulation',
                'estimated_minutes' => 18,
                'display_order'     => 3,
                'content'           => <<<'MARKDOWN'
## Functions

```php
function greet(string $name, string $greeting = 'Hello'): string {
    return "$greeting, $name!";
}

echo greet('Alice');         // Hello, Alice!
echo greet('Bob', 'Hi');    // Hi, Bob!
```

**Variadic functions** — accept any number of arguments:
```php
function sum(int ...$numbers): int {
    return array_sum($numbers);
}
sum(1, 2, 3, 4); // 10
```

**Pass by reference** — modify the original variable:
```php
function addSuffix(string &$str, string $suffix): void {
    $str .= $suffix;
}
$name = 'Alice';
addSuffix($name, ' Jr.');
echo $name; // Alice Jr.
```

**Arrow functions** (PHP 7.4+) — concise syntax, automatically captures outer scope:
```php
$multiplier = 3;
$triple = fn($n) => $n * $multiplier; // captures $multiplier automatically
$triple(5); // 15
```

## Essential Array Functions

```php
$numbers = [5, 2, 8, 1, 9, 3];

// Transform — returns new array
array_map(fn($n) => $n * 2, $numbers);          // [10, 4, 16, 2, 18, 6]

// Filter — returns filtered array (preserves original keys)
array_filter($numbers, fn($n) => $n > 4);       // [5, 8, 9] with original keys

// Reduce — fold into a single value
array_reduce($numbers, fn($carry, $n) => $carry + $n, 0); // 28

// Sorting (mutates original array)
sort($numbers);                                  // [1, 2, 3, 5, 8, 9] ascending
rsort($numbers);                                 // descending
usort($numbers, fn($a, $b) => $b - $a);         // custom comparator

// Searching
in_array(5, $numbers);       // true
array_search(8, $numbers);   // returns index (or false if not found)

// Slicing & Merging
array_slice($numbers, 1, 3); // 3 elements starting at index 1
array_merge([1, 2], [3, 4]); // [1, 2, 3, 4]
array_unique([1, 2, 2, 3]);  // [1, 2, 3]
array_flip(['a' => 1, 'b' => 2]); // [1 => 'a', 2 => 'b']
array_keys(['a' => 1, 'b' => 2]); // ['a', 'b']
array_values(['a' => 1, 'b' => 2]); // [1, 2]
```

**Spread operator**:
```php
$a = [1, 2, 3];
$b = [0, ...$a, 4]; // [0, 1, 2, 3, 4]
```

## Key String Functions

```php
$str = '  Hello, World!  ';

strlen($str);                          // byte length
trim($str);                            // 'Hello, World!'
strtolower($str);                      // '  hello, world!  '
strtoupper($str);                      // '  HELLO, WORLD!  '
str_replace('World', 'PHP', $str);    // replace substring
strpos($str, 'World');                 // position of first occurrence (false if absent)
substr($str, 2, 5);                    // 'Hello' — 5 chars starting at index 2
explode(', ', trim($str));             // ['Hello', 'World!']
implode(' - ', ['a', 'b', 'c']);       // 'a - b - c'
str_contains($str, 'World');           // true (PHP 8.0+)
str_starts_with(trim($str), 'Hello'); // true (PHP 8.0+)
str_ends_with(trim($str), '!');       // true (PHP 8.0+)
sprintf('User: %s, Age: %d', 'Alice', 25); // formatted string
```

## include vs require

```php
include 'header.php';    // warning on failure, script continues
require 'config.php';    // fatal error on failure, script stops

include_once 'util.php'; // only includes once even if called multiple times
require_once 'util.php';
```

Use `require_once` for essential dependencies the script cannot run without.
MARKDOWN,
            ],

            // ── LEVEL 2 ────────────────────────────────────────────────────
            [
                'topic_id'          => $t2->id,
                'title'             => 'OOP in PHP: Classes, Interfaces, Traits & Abstract Classes',
                'estimated_minutes' => 20,
                'display_order'     => 1,
                'content'           => <<<'MARKDOWN'
## Classes & Objects

```php
class BankAccount
{
    private float $balance;

    public function __construct(
        private readonly string $owner,  // constructor property promotion (PHP 8.0+)
        float $initialBalance = 0.0
    ) {
        $this->balance = $initialBalance;
    }

    public function deposit(float $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive.');
        }
        $this->balance += $amount;
    }

    public function getBalance(): float { return $this->balance; }

    public function __toString(): string
    {
        return "{$this->owner}: \${$this->balance}";
    }
}

$account = new BankAccount('Alice', 100.0);
$account->deposit(50.0);
echo $account->getBalance(); // 150
echo $account;               // Alice: $150
```

## Visibility & Static Members

```php
class Counter
{
    private static int $count = 0;

    public static function increment(): void { self::$count++; }
    public static function getCount(): int   { return self::$count; }
}

Counter::increment();
Counter::increment();
Counter::getCount(); // 2
```

`self::` refers to the class in which the code is **written** (compile-time). `static::` uses **late static binding** — it refers to the class actually **called** at runtime, which matters in inheritance.

## Interfaces

An interface defines a contract — what methods a class must implement, without any implementation:

```php
interface Serializable
{
    public function serialize(): string;
    public function unserialize(string $data): void;
}

interface Loggable
{
    public function getLogContext(): array;
}

class User implements Serializable, Loggable
{
    public function __construct(private string $name) {}

    public function serialize(): string { return json_encode(['name' => $this->name]); }

    public function unserialize(string $data): void
    {
        $this->name = json_decode($data, true)['name'];
    }

    public function getLogContext(): array { return ['user' => $this->name]; }
}
```

A class can implement **multiple interfaces** but can only **extend one class**.

## Abstract Classes

Abstract classes cannot be instantiated. They may contain abstract methods (no body) and concrete methods (with implementation):

```php
abstract class Shape
{
    abstract public function area(): float;

    public function describe(): string   // concrete — shared by all subclasses
    {
        return get_class($this) . ' with area ' . $this->area();
    }
}

class Circle extends Shape
{
    public function __construct(private float $radius) {}
    public function area(): float { return M_PI * $this->radius ** 2; }
}

$c = new Circle(5);
echo $c->describe(); // Circle with area 78.54
```

## Traits

Traits enable **horizontal code reuse** across unrelated class hierarchies — PHP's answer to multiple inheritance:

```php
trait Timestamps
{
    private ?DateTime $createdAt = null;

    public function touch(): void
    {
        $this->createdAt ??= new DateTime();
    }

    public function getCreatedAt(): ?DateTime { return $this->createdAt; }
}

trait SoftDelete
{
    private ?DateTime $deletedAt = null;

    public function delete(): void  { $this->deletedAt = new DateTime(); }
    public function isDeleted(): bool { return $this->deletedAt !== null; }
}

class Post
{
    use Timestamps, SoftDelete;
    public function __construct(public string $title) {}
}

$post = new Post('Hello World');
$post->touch();
$post->delete();
$post->isDeleted(); // true
```

If two traits define the same method, resolve the conflict with `insteadof` and `as` in the `use` block.

## final

Prevents a class from being extended or a method from being overridden:

```php
final class Singleton { /* cannot be subclassed */ }

class Base {
    final public function id(): int { return 1; } // cannot be overridden in child
}
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t2->id,
                'title'             => 'Error Handling, Exceptions & PDO',
                'estimated_minutes' => 18,
                'display_order'     => 2,
                'content'           => <<<'MARKDOWN'
## Exceptions

PHP uses exception-based error handling:

```php
try {
    $result = divide(10, 0);
} catch (\DivisionByZeroError $e) {
    echo "Math error: " . $e->getMessage();
} catch (\InvalidArgumentException $e) {
    echo "Bad input: " . $e->getMessage();
} finally {
    echo "This always runs — cleanup code here.";
}
```

**Custom exception classes**:
```php
class PaymentFailedException extends \RuntimeException
{
    public function __construct(
        string $message,
        private readonly string $transactionId,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getTransactionId(): string { return $this->transactionId; }
}

throw new PaymentFailedException('Card declined', 'TXN-123');
```

**PHP exception hierarchy**: `Throwable` has two branches:
- `Error` — engine-level: `TypeError`, `ParseError`, `DivisionByZeroError`, `ArithmeticError`
- `Exception` — application-level: `RuntimeException`, `LogicException`, `InvalidArgumentException`, etc.

Always catch the most specific type first.

## PDO — Database Access

PDO (PHP Data Objects) is the recommended way to interact with databases:

```php
$dsn = 'mysql:host=localhost;dbname=myapp;charset=utf8mb4';
$pdo = new PDO($dsn, 'user', 'password', [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // throw exceptions on error
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // return associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // native prepared statements
]);
```

**Prepared statements** — prevent SQL injection by separating query structure from data:
```php
// Named placeholders
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email AND active = :active');
$stmt->execute(['email' => $email, 'active' => 1]);
$user = $stmt->fetch();

// Positional placeholders
$stmt = $pdo->prepare('INSERT INTO posts (title, body, user_id) VALUES (?, ?, ?)');
$stmt->execute([$title, $body, $userId]);
$newId = $pdo->lastInsertId();
```

**Never build queries with string concatenation** — that opens SQL injection:
```php
// WRONG — SQL injection vulnerability
$query = "SELECT * FROM users WHERE name = '$name'";

// RIGHT — prepared statement, data is always treated as a value
$stmt = $pdo->prepare('SELECT * FROM users WHERE name = ?');
$stmt->execute([$name]);
```

## Transactions

```php
try {
    $pdo->beginTransaction();

    $pdo->prepare('UPDATE accounts SET balance = balance - ? WHERE id = ?')
        ->execute([100, $fromId]);

    $pdo->prepare('UPDATE accounts SET balance = balance + ? WHERE id = ?')
        ->execute([100, $toId]);

    $pdo->commit();
} catch (\Throwable $e) {
    $pdo->rollBack();
    throw $e; // re-throw so the caller knows it failed
}
```

Transactions ensure atomicity — either all operations succeed, or none are applied.
MARKDOWN,
            ],
            [
                'topic_id'          => $t2->id,
                'title'             => 'Sessions, Cookies & PHP Security Fundamentals',
                'estimated_minutes' => 15,
                'display_order'     => 3,
                'content'           => <<<'MARKDOWN'
## Sessions

Sessions store user data server-side across multiple HTTP requests:

```php
session_start(); // start or resume

$_SESSION['user_id']  = 42;
$_SESSION['username'] = 'alice';

$userId = $_SESSION['user_id'] ?? null;

unset($_SESSION['username']); // remove one key
session_destroy();            // destroy entire session
```

**Session regeneration** — prevent session fixation attacks — always regenerate the session ID after a successful login:
```php
session_start();
// ... verify credentials ...
session_regenerate_id(true); // true = delete the old session file
$_SESSION['user_id'] = $user->id;
```

## Cookies

Cookies are stored client-side (browser):

```php
setcookie(
    name:     'remember_token',
    value:    $token,
    expires:  time() + 30 * 24 * 3600, // 30 days
    path:     '/',
    domain:   '',
    secure:   true,  // HTTPS only
    httponly: true,  // not accessible via JavaScript (prevents XSS theft)
);

$token = $_COOKIE['remember_token'] ?? null;
setcookie('remember_token', '', time() - 3600); // delete: set past expiry
```

Always set `secure: true` and `httponly: true` on authentication cookies.

## Password Hashing

Never store plain-text passwords:

```php
// Hash — bcrypt by default, automatically generates salt
$hash = password_hash($plainPassword, PASSWORD_DEFAULT);

// Verify
if (password_verify($plainPassword, $hash)) {
    // login successful
}

// Rehash if cost factor changes
if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
    $hash = password_hash($plainPassword, PASSWORD_DEFAULT);
    // save new hash to DB
}
```

## Preventing XSS

Cross-Site Scripting injects malicious scripts into HTML output. Always escape output:

```php
// htmlspecialchars converts < > " ' & to HTML entities
echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');
```

Never echo raw user input into HTML. Content Security Policy (CSP) headers add another layer.

## CSRF Protection

Cross-Site Request Forgery tricks authenticated users into submitting malicious forms:

```php
// Generate token
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// In HTML form
echo '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';

// Verify on form submission
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    exit('CSRF token mismatch');
}
```

Use `hash_equals()` — not `===` — to prevent timing attacks.

## Input Validation

```php
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL); // false if invalid
$age   = filter_var($_POST['age'],   FILTER_VALIDATE_INT);
$url   = filter_var($_POST['url'],   FILTER_VALIDATE_URL);
```

Never use raw `$_GET`/`$_POST` directly in SQL queries or HTML output.
MARKDOWN,
            ],

            // ── LEVEL 3 ────────────────────────────────────────────────────
            [
                'topic_id'          => $t3->id,
                'title'             => 'Design Patterns in PHP',
                'estimated_minutes' => 22,
                'display_order'     => 1,
                'content'           => <<<'MARKDOWN'
## Why Design Patterns?

Design patterns are proven, named solutions to recurring design problems. They make architecture discussions precise and code easier to maintain and extend.

## Creational Patterns

### Singleton

Ensures exactly one instance exists:

```php
final class Database
{
    private static ?self $instance = null;
    private \PDO $pdo;

    private function __construct()
    {
        $this->pdo = new \PDO('mysql:host=localhost;dbname=app', 'user', 'pass');
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): \PDO { return $this->pdo; }
}
```

**Caution**: Singletons carry global state, making testing hard. Prefer dependency injection in testable code.

### Factory Method

Creates objects without specifying the exact class:

```php
interface Logger
{
    public function log(string $message): void;
}

class FileLogger implements Logger
{
    public function log(string $message): void
    {
        file_put_contents('app.log', $message . PHP_EOL, FILE_APPEND);
    }
}

class NullLogger implements Logger
{
    public function log(string $message): void { /* discard */ }
}

class LoggerFactory
{
    public static function create(string $driver): Logger
    {
        return match($driver) {
            'file'  => new FileLogger(),
            'null'  => new NullLogger(),
            default => throw new \InvalidArgumentException("Unknown driver: $driver"),
        };
    }
}

$logger = LoggerFactory::create('file');
```

## Structural Patterns

### Repository

Separates domain logic from data persistence:

```php
interface UserRepository
{
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function save(User $user): void;
    public function delete(int $id): void;
}

class PdoUserRepository implements UserRepository
{
    public function __construct(private \PDO $pdo) {}

    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? User::fromArray($row) : null;
    }
    // ... other methods
}
```

In tests, inject `InMemoryUserRepository` instead — no database required.

### Decorator

Adds behaviour dynamically by wrapping an object implementing the same interface:

```php
interface TextFormatter
{
    public function format(string $text): string;
}

class PlainText implements TextFormatter
{
    public function format(string $text): string { return $text; }
}

class BoldDecorator implements TextFormatter
{
    public function __construct(private TextFormatter $inner) {}
    public function format(string $text): string
    {
        return '<b>' . $this->inner->format($text) . '</b>';
    }
}

class ItalicDecorator implements TextFormatter
{
    public function __construct(private TextFormatter $inner) {}
    public function format(string $text): string
    {
        return '<i>' . $this->inner->format($text) . '</i>';
    }
}

$formatter = new ItalicDecorator(new BoldDecorator(new PlainText()));
echo $formatter->format('Hello'); // <i><b>Hello</b></i>
```

## Behavioural Patterns

### Observer

One subject notifies many listeners on state change:

```php
interface EventListener
{
    public function handle(array $event): void;
}

class EventEmitter
{
    private array $listeners = [];

    public function listen(string $event, EventListener $listener): void
    {
        $this->listeners[$event][] = $listener;
    }

    public function emit(string $event, array $data = []): void
    {
        foreach ($this->listeners[$event] ?? [] as $listener) {
            $listener->handle($data);
        }
    }
}

class SendWelcomeEmail implements EventListener
{
    public function handle(array $event): void
    {
        mail($event['email'], 'Welcome!', 'Thanks for signing up.');
    }
}

$emitter = new EventEmitter();
$emitter->listen('user.registered', new SendWelcomeEmail());
$emitter->emit('user.registered', ['email' => 'alice@example.com']);
```

### Strategy

Encapsulates interchangeable algorithms behind a common interface:

```php
interface SortStrategy
{
    public function sort(array $data): array;
}

class QuickSort implements SortStrategy
{
    public function sort(array $data): array { sort($data); return $data; }
}

class Sorter
{
    public function __construct(private SortStrategy $strategy) {}

    public function setStrategy(SortStrategy $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function sort(array $data): array
    {
        return $this->strategy->sort($data);
    }
}

$sorter = new Sorter(new QuickSort());
$sorted = $sorter->sort([5, 2, 8, 1]); // strategy can be swapped at runtime
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t3->id,
                'title'             => 'Modern PHP 8.x Features',
                'estimated_minutes' => 20,
                'display_order'     => 2,
                'content'           => <<<'MARKDOWN'
## Named Arguments (PHP 8.0)

Pass arguments by parameter name — order no longer matters:

```php
function createUser(string $name, int $age = 0, string $role = 'user'): array
{
    return compact('name', 'age', 'role');
}

createUser(name: 'Alice', role: 'admin');
// ['name' => 'Alice', 'age' => 0, 'role' => 'admin']

// Useful with built-in functions with many optional params
array_slice(array: $items, offset: 0, length: 10, preserve_keys: true);
```

## Enums (PHP 8.1)

Enums define a type with a fixed set of named values:

```php
// Pure enum — named constants, no scalar value
enum Status
{
    case Pending;
    case Active;
    case Inactive;
}

// Backed enum — each case has a scalar value
enum Color: string
{
    case Red   = 'red';
    case Green = 'green';
    case Blue  = 'blue';

    public function label(): string { return ucfirst($this->value); }
}

$color = Color::Red;
echo $color->value;   // 'red'
echo $color->label(); // 'Red'

Color::from('green');    // Color::Green (throws ValueError if not found)
Color::tryFrom('unknown'); // null (never throws)
```

## Readonly Properties & Classes (PHP 8.1 / 8.2)

```php
class Money
{
    public function __construct(
        public readonly float $amount,
        public readonly string $currency,
    ) {}
}

$price = new Money(9.99, 'USD');
// $price->amount = 5; // Error: cannot modify readonly property
```

PHP 8.2 adds `readonly class` — all constructor-promoted properties become readonly automatically.

## Fibers (PHP 8.1)

Fibers are cooperative coroutines — lightweight execution contexts that can pause and resume:

```php
$fiber = new Fiber(function (): void {
    $value = Fiber::suspend('first yield');
    echo "Resumed with: $value\n";
    Fiber::suspend('second yield');
});

$v1 = $fiber->start();           // 'first yield'
$v2 = $fiber->resume('hello');  // echoes "Resumed with: hello", returns 'second yield'
$fiber->resume();
```

Fibers are the primitive async libraries (ReactPHP, Amp) use to build event loops. They are NOT threads — only one Fiber runs at a time.

## Intersection Types (PHP 8.1)

Require a value to satisfy multiple type constraints simultaneously:

```php
function process(Stringable&Countable $collection): void
{
    echo $collection->count() . ' items: ' . $collection;
}
```

## Null Safe Operator (PHP 8.0)

Chain method calls safely on potentially-null values:

```php
// Before — verbose null checks
$city = null;
if ($user !== null && $user->getAddress() !== null) {
    $city = $user->getAddress()->getCity();
}

// After
$city = $user?->getAddress()?->getCity(); // null if any step returns null
```

## First Class Callable Syntax (PHP 8.1)

Create closures from any callable cleanly:

```php
$fn = strlen(...);    // Closure wrapping strlen
$fn('hello');         // 5

$arr = ['banana', 'apple', 'cherry'];
usort($arr, strcmp(...));

$upper = strtoupper(...);
array_map($upper, ['a', 'b']); // ['A', 'B']
```

## Attributes (PHP 8.0)

Native structured metadata for classes, methods, and properties:

```php
#[Attribute]
class Route
{
    public function __construct(
        public string $path,
        public string $method = 'GET',
    ) {}
}

class UserController
{
    #[Route('/users', 'GET')]
    public function index(): array { return []; }

    #[Route('/users', 'POST')]
    public function store(): array { return []; }
}

// Read at runtime via Reflection
$ref   = new \ReflectionMethod(UserController::class, 'index');
$attrs = $ref->getAttributes(Route::class);
$route = $attrs[0]->newInstance(); // Route { path: '/users', method: 'GET' }
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t3->id,
                'title'             => 'Testing, Composer & the PHP Ecosystem',
                'estimated_minutes' => 18,
                'display_order'     => 3,
                'content'           => <<<'MARKDOWN'
## PHPUnit — Unit Testing

```php
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    private Calculator $calc;

    protected function setUp(): void
    {
        $this->calc = new Calculator();
    }

    public function test_add_two_positive_integers(): void
    {
        $this->assertSame(5, $this->calc->add(2, 3));
    }

    public function test_divide_by_zero_throws(): void
    {
        $this->expectException(\DivisionByZeroError::class);
        $this->calc->divide(10, 0);
    }
}
```

Key assertions: `assertSame`, `assertEquals`, `assertTrue`, `assertNull`, `assertCount`, `assertInstanceOf`, `assertArrayHasKey`.

**`assertSame` vs `assertEquals`**: `assertSame` checks value AND type (`===`); `assertEquals` is loose (`==`). Always prefer `assertSame`.

## Mocking Dependencies

```php
class UserServiceTest extends TestCase
{
    public function test_create_user_sends_welcome_email(): void
    {
        $mailer = $this->createMock(Mailer::class);
        $repo   = $this->createMock(UserRepository::class);

        $mailer->expects($this->once())
               ->method('sendWelcome')
               ->with($this->isInstanceOf(User::class));

        $service = new UserService($repo, $mailer);
        $service->createUser('Alice', 'alice@example.com');
    }
}
```

Good design with dependency injection makes mocking straightforward — inject a mock instead of the real implementation.

## Composer — Dependency Manager

```bash
composer require vendor/package           # add dependency
composer require --dev phpunit/phpunit    # dev-only dependency
composer install                          # install from composer.lock
composer update                           # update to latest allowed versions
composer dump-autoload                    # rebuild the autoloader
```

**composer.json**:
```json
{
    "require": {
        "php": "^8.2",
        "guzzlehttp/guzzle": "^7.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^11.0"
    },
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}
```

## PSR-4 Autoloading

PSR-4 maps namespace prefixes to directory structures:

```
"App\\": "src/"

App\Models\User         → src/Models/User.php
App\Http\Controllers\   → src/Http/Controllers/*.php
```

Composer generates an autoloader (`vendor/autoload.php`) that loads classes on demand — no manual `require` needed.

## Key PSR Standards

| PSR   | Description                    |
|-------|-------------------------------|
| PSR-1 | Basic coding standard          |
| PSR-4 | Autoloading standard           |
| PSR-7 | HTTP message interfaces        |
| PSR-11| Container interface            |
| PSR-12| Extended coding style guide    |
| PSR-15| HTTP server middleware         |

## Popular PHP Packages

| Package                  | Purpose                        |
|--------------------------|-------------------------------|
| `guzzlehttp/guzzle`      | HTTP client                    |
| `symfony/console`        | CLI commands                   |
| `league/flysystem`       | File storage abstraction       |
| `nesbot/carbon`          | DateTime library               |
| `monolog/monolog`        | Logging (PSR-3 compliant)      |
| `vlucas/phpdotenv`       | `.env` file loading            |
| `ramsey/uuid`            | UUID generation                |

## Running Tests

```bash
./vendor/bin/phpunit                        # run all tests
./vendor/bin/phpunit tests/Unit             # specific directory
./vendor/bin/phpunit --coverage-html report # HTML coverage report
```

Aim for meaningful coverage — test behaviour, not just lines.
MARKDOWN,
            ],
        ];

        foreach ($lessons as $lesson) {
            DB::table('lessons')->updateOrInsert(
                ['topic_id' => $lesson['topic_id'], 'title' => $lesson['title']],
                array_merge($lesson, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('Lessons seeded for all 3 PHP levels.');
    }

    // ── EXAM QUESTION SEEDER ─────────────────────────────────────────────────

    private function seedExamQuestions(Topic $topic, array $questions, string $difficulty, string $label): void
    {
        Question::where('topic_id', $topic->id)->delete();

        foreach ($questions as $qData) {
            $q = Question::create([
                'topic_id'    => $topic->id,
                'type'        => 'MCQ',
                'difficulty'  => $difficulty,
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
        $this->command->info("{$label}: {$count} questions total.");
    }

    // ── LEVEL 1 EXAM QUESTIONS ───────────────────────────────────────────────

    private function level1Questions(): array
    {
        return [
            [
                'question'    => 'What is the difference between == and === in PHP?',
                'explanation' => '`==` performs loose comparison with type coercion — `0 == false` is `true`. `===` performs strict comparison checking both value AND type — `0 === false` is `false`. Always prefer `===` to avoid unexpected results from PHP\'s type juggling.',
                'options'     => [
                    ['text' => '== checks value only with type coercion; === checks both value and type strictly', 'correct' => true],
                    ['text' => '== is faster because it skips type checking; === is slower', 'correct' => false],
                    ['text' => '=== is for objects only; == works for all types', 'correct' => false],
                    ['text' => 'They are identical — PHP always does strict comparison internally', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the null coalescing operator ?? do in PHP?',
                'explanation' => 'The `??` operator returns its left operand if it is set and not null, otherwise returns the right operand. It is equivalent to `isset($a) ? $a : $b`. It can be chained: `$a ?? $b ?? $c`. It is much cleaner than a ternary with `isset()`.',
                'options'     => [
                    ['text' => 'Returns the left operand if it is set and not null, otherwise returns the right operand', 'correct' => true],
                    ['text' => 'Returns null if either operand is null', 'correct' => false],
                    ['text' => 'Checks if a variable equals null and throws an exception if so', 'correct' => false],
                    ['text' => 'Assigns null to a variable if it is undefined', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between single-quoted and double-quoted strings in PHP?',
                'explanation' => 'Double-quoted strings (`"..."`) interpolate variables and parse escape sequences like `\\n`, `\\t`. Single-quoted strings (`\'...\'`) treat everything literally — only `\\\\` (backslash) and `\\\'` (single quote) are escape sequences. Single-quoted strings are slightly faster because no parsing is needed, but the difference is negligible in practice.',
                'options'     => [
                    ['text' => 'Double-quoted strings interpolate variables and parse escape sequences; single-quoted are literal', 'correct' => true],
                    ['text' => 'Single-quoted strings are for multi-line text; double-quoted are for single lines', 'correct' => false],
                    ['text' => 'They are identical — PHP converts them to the same internal representation', 'correct' => false],
                    ['text' => 'Double-quoted strings require escaping all special characters', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does array_filter() return when no callback is provided?',
                'explanation' => 'When called without a callback, `array_filter()` removes all elements that evaluate to `false` — i.e., falsy values: `false`, `0`, `0.0`, `""`, `"0"`, `[]`, `null`. It preserves original array keys. Use `array_values()` afterwards if you need a re-indexed array.',
                'options'     => [
                    ['text' => 'Removes all falsy values (false, 0, "", null, [], "0") while preserving keys', 'correct' => true],
                    ['text' => 'Returns an empty array — a callback is mandatory', 'correct' => false],
                    ['text' => 'Removes duplicate values from the array', 'correct' => false],
                    ['text' => 'Removes null values only, keeping 0 and false', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between require and include in PHP?',
                'explanation' => '`require` produces a **fatal error** (E_COMPILE_ERROR) and stops execution if the file cannot be found. `include` produces only a **warning** (E_WARNING) and continues execution. Use `require` for files that are essential, `include` for optional ones. Both have `_once` variants that prevent double-inclusion.',
                'options'     => [
                    ['text' => 'require produces a fatal error on failure and stops; include produces only a warning and continues', 'correct' => true],
                    ['text' => 'include searches more directories; require only looks in the current directory', 'correct' => false],
                    ['text' => 'require executes the file in a new scope; include uses the current scope', 'correct' => false],
                    ['text' => 'They are identical — require is just older syntax', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How does the match expression differ from switch in PHP 8.0+?',
                'explanation' => 'The `match` expression uses strict comparison (`===` not `==`), returns a value, throws `UnhandledMatchError` if no arm matches and there is no `default`, does not fall through between arms (no `break` needed), and each arm can have multiple comma-separated conditions.',
                'options'     => [
                    ['text' => 'match uses strict comparison, returns a value, throws on no match, no fall-through', 'correct' => true],
                    ['text' => 'match is just shorthand — it compiles to the same bytecode as switch', 'correct' => false],
                    ['text' => 'match can only compare integers; switch works with any type', 'correct' => false],
                    ['text' => 'match requires a default arm; switch does not', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the spread operator (...) do in PHP arrays?',
                'explanation' => 'The spread operator `...` unpacks an array into individual elements. It can be used in function calls: `fn(...$args)`, in array literals: `[...$a, ...$b]` (merging), and in function parameter definitions for variadic functions: `function fn(int ...$nums)`. In arrays, it performs a shallow merge similar to `array_merge()`.',
                'options'     => [
                    ['text' => 'Unpacks an array into individual elements — used for merging arrays or spreading args into functions', 'correct' => true],
                    ['text' => 'Creates a deep copy of the array', 'correct' => false],
                    ['text' => 'Converts an associative array to an indexed array', 'correct' => false],
                    ['text' => 'Removes all falsy values from the array', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of declare(strict_types=1) in PHP?',
                'explanation' => '`declare(strict_types=1)` must be the very first statement in a file. When enabled, PHP throws a `TypeError` if a function argument does not match its declared type instead of silently coercing it. Without it, `add(int $a, int $b)` called with `add(1, "2")` would silently coerce "2" to 2.',
                'options'     => [
                    ['text' => 'Enables strict type checking — type mismatches in function calls throw TypeError instead of coercing', 'correct' => true],
                    ['text' => 'Disables all type annotations and runs in dynamic mode', 'correct' => false],
                    ['text' => 'Makes all variables immutable within the file', 'correct' => false],
                    ['text' => 'Enables === for all == comparisons throughout the file', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you pass a variable by reference in PHP?',
                'explanation' => 'By default PHP passes values by value — a copy is made and the original is not affected. To pass by reference, prefix the parameter with `&`: `function increment(int &$n) { $n++; }`. The function then modifies the original variable. PHP\'s built-in `sort()` uses this — it mutates the array passed to it.',
                'options'     => [
                    ['text' => 'Prefix the parameter with & — the function receives the original variable and can modify it', 'correct' => true],
                    ['text' => 'PHP always passes arrays by reference automatically', 'correct' => false],
                    ['text' => 'Pass-by-reference means the return value is stored in the argument variable', 'correct' => false],
                    ['text' => 'References are only possible with objects, not primitives', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are PHP\'s falsy values?',
                'explanation' => 'PHP treats these values as `false` in boolean context: `false`, `0`, `0.0`, `""` (empty string), `"0"` (the string zero), `[]` (empty array), `null`, and unset variables. Note: `"0"` is falsy — this surprises many developers. Unlike JavaScript, empty objects are NOT falsy in PHP.',
                'options'     => [
                    ['text' => 'false, 0, 0.0, "", "0", [], null — notably "0" is falsy unlike in JavaScript', 'correct' => true],
                    ['text' => 'false, null, and 0 only — all other values are truthy', 'correct' => false],
                    ['text' => 'false, null, 0, [], and all negative numbers', 'correct' => false],
                    ['text' => 'Only false and null — PHP does not coerce other types to bool', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between array_push() and $arr[] = $value in PHP?',
                'explanation' => '`$arr[] = $value` and `array_push($arr, $value)` are functionally equivalent for a single element, but `$arr[]` is faster because it avoids function call overhead. `array_push()` is useful when appending multiple values at once: `array_push($arr, $v1, $v2, $v3)`. PHP documentation recommends `$arr[]` for single additions.',
                'options'     => [
                    ['text' => 'They are functionally equivalent but $arr[] is faster (no function call overhead)', 'correct' => true],
                    ['text' => 'array_push() prepends to the array; $arr[] appends', 'correct' => false],
                    ['text' => 'array_push() returns the new count; $arr[] returns the element', 'correct' => false],
                    ['text' => '$arr[] only works with numeric arrays; array_push() works with any array', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does array_map() return compared to array_walk() in PHP?',
                'explanation' => '`array_map()` applies a callback to each element and **returns a new array** — the original is unchanged. `array_walk()` applies a callback **in place** (modifying the original array), takes the element by reference, and returns a boolean. Use `array_map()` when you want a transformed copy; use `array_walk()` when you want to mutate in place (though a `foreach` is often clearer).',
                'options'     => [
                    ['text' => 'array_map() returns a new transformed array; array_walk() modifies the original array in place', 'correct' => true],
                    ['text' => 'array_map() only works with numeric arrays; array_walk() works with associative too', 'correct' => false],
                    ['text' => 'They are identical — array_walk is just the older name for array_map', 'correct' => false],
                    ['text' => 'array_walk() returns a new array; array_map() modifies the original', 'correct' => false],
                ],
            ],
        ];
    }

    // ── LEVEL 2 EXAM QUESTIONS ───────────────────────────────────────────────

    private function level2Questions(): array
    {
        return [
            [
                'question'    => 'What is the difference between an interface and an abstract class in PHP?',
                'explanation' => 'An interface defines a contract with method signatures only — no implementations, no properties (only constants). A class can implement multiple interfaces. An abstract class can have both abstract methods (no body) and concrete methods (with implementation), and can hold properties. A class can only extend one abstract class. Use interfaces for contracts; use abstract classes for shared implementation.',
                'options'     => [
                    ['text' => 'Interfaces have signatures only, allow multiple implementation; abstract classes allow shared implementation but single inheritance', 'correct' => true],
                    ['text' => 'Abstract classes cannot be instantiated; interfaces can be instantiated directly', 'correct' => false],
                    ['text' => 'Interfaces only work with public methods; abstract classes work with any visibility', 'correct' => false],
                    ['text' => 'They are identical — interface is just an alias for abstract class', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a PHP Trait and what problem does it solve?',
                'explanation' => 'PHP only supports single inheritance — a class can only extend one parent. Traits solve the "multiple inheritance" problem by allowing horizontal code reuse. A trait is a group of methods that can be `use`d in any class. If two traits define the same method, you must resolve the conflict with `insteadof` and `as`. Traits cannot be instantiated and cannot declare constants.',
                'options'     => [
                    ['text' => 'A reusable group of methods that a class can use — solves code sharing across unrelated class hierarchies', 'correct' => true],
                    ['text' => 'A type declaration that combines two interfaces into one', 'correct' => false],
                    ['text' => 'A class that can be extended by multiple other classes simultaneously', 'correct' => false],
                    ['text' => 'A PHP equivalent of Java\'s generic types', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is late static binding in PHP and how does static:: differ from self::?',
                'explanation' => '`self::` refers to the class in which the code is WRITTEN (resolved at compile time). `static::` refers to the class actually CALLED at runtime — late static binding. In inheritance, a parent method using `self::` always creates a parent instance. With `static::`, it creates an instance of the actual child class called. Essential for correct factory/singleton patterns in inheritance.',
                'options'     => [
                    ['text' => 'self:: always refers to the defining class; static:: refers to the class called at runtime', 'correct' => true],
                    ['text' => 'static:: is for static methods only; self:: works for both static and instance methods', 'correct' => false],
                    ['text' => 'They are identical — PHP uses self:: internally for both', 'correct' => false],
                    ['text' => 'static:: prevents inheritance; self:: allows it', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do PDO prepared statements prevent SQL injection?',
                'explanation' => 'Prepared statements separate the SQL query structure from the data. The query template (with placeholders `?` or `:name`) is parsed and planned by the database first. Then user data is sent separately and bound to the placeholders — the database treats it as a pure value, never as SQL code. So quotes, semicolons, and SQL keywords in user input cannot alter the query structure.',
                'options'     => [
                    ['text' => 'They separate query structure from data — data is bound as values, never interpreted as SQL code', 'correct' => true],
                    ['text' => 'They encrypt the SQL query so user input cannot be read', 'correct' => false],
                    ['text' => 'They escape all special characters in user input automatically', 'correct' => false],
                    ['text' => 'They only protect against integer injection, not string injection', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the finally block do in PHP exception handling?',
                'explanation' => 'The `finally` block ALWAYS executes regardless of whether an exception was thrown or caught — even if a `return` statement is in the `try` or `catch` block. It is used for cleanup code that must run regardless: closing database connections, releasing locks, closing file handles.',
                'options'     => [
                    ['text' => 'Always executes regardless of exceptions — used for cleanup code like closing connections', 'correct' => true],
                    ['text' => 'Only executes if an exception was thrown and caught', 'correct' => false],
                    ['text' => 'Only executes if no exception was thrown (the success path)', 'correct' => false],
                    ['text' => 'Re-throws any exception that was caught in the catch block', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is constructor property promotion in PHP?',
                'explanation' => 'Constructor property promotion (PHP 8.0+) allows declaring and assigning class properties directly in the constructor signature by adding a visibility modifier (`public`, `protected`, `private`) or `readonly`. Instead of declaring the property, adding a parameter, and assigning `$this->name = $name`, you write just `public function __construct(private string $name) {}`. The property is automatically created and assigned.',
                'options'     => [
                    ['text' => 'Declaring class properties directly in the constructor signature with visibility modifiers — reduces boilerplate', 'correct' => true],
                    ['text' => 'Making the constructor automatically call the parent constructor', 'correct' => false],
                    ['text' => 'Promoting a protected constructor to public in a subclass', 'correct' => false],
                    ['text' => 'Allowing constructors to be called without the new keyword', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is session_regenerate_id() used for and when should you call it?',
                'explanation' => 'Session fixation is an attack where an attacker sets the session ID before the user logs in, then uses that known ID to hijack the session after login. `session_regenerate_id(true)` generates a new session ID and (with `true`) deletes the old session data. Always call it immediately after a successful login or privilege escalation.',
                'options'     => [
                    ['text' => 'Generates a new session ID after login to prevent session fixation attacks', 'correct' => true],
                    ['text' => 'Resets all session variables to their default values', 'correct' => false],
                    ['text' => 'Extends the session lifetime by regenerating the expiry timestamp', 'correct' => false],
                    ['text' => 'Synchronises the session ID between the server and the client cookie', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between password_hash() and md5() for storing passwords?',
                'explanation' => '`md5()` and `sha1()` are cryptographic hash functions designed to be FAST — that makes them dangerous for passwords. An attacker with a GPU can compute billions of MD5 hashes per second. `password_hash()` uses bcrypt (or Argon2) which is intentionally SLOW and automatically adds a random salt, preventing rainbow table attacks. Never use MD5/SHA for passwords.',
                'options'     => [
                    ['text' => 'password_hash() uses bcrypt/Argon2 (slow, salted) designed for passwords; md5 is fast and unsalted — never use md5 for passwords', 'correct' => true],
                    ['text' => 'They are equally secure — password_hash just adds a prefix to the md5 hash', 'correct' => false],
                    ['text' => 'md5() is more secure because it produces a longer hash', 'correct' => false],
                    ['text' => 'password_hash() encrypts the password; md5() only hashes it', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of hash_equals() when verifying CSRF tokens?',
                'explanation' => 'A normal `===` string comparison takes different amounts of time depending on how many characters match before finding a mismatch. This timing difference can be exploited in a timing attack to guess a secret byte-by-byte. `hash_equals()` always takes the same time regardless of how many characters match, preventing timing attacks. Always use it when comparing security-sensitive tokens.',
                'options'     => [
                    ['text' => 'Performs constant-time comparison to prevent timing attacks — timing differences in === could expose token bytes', 'correct' => true],
                    ['text' => 'Hashes the CSRF token before comparing to provide extra encryption', 'correct' => false],
                    ['text' => 'Checks that both strings have the same length before comparing', 'correct' => false],
                    ['text' => 'It is just an alias for === with added input validation', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an anonymous class in PHP and when would you use one?',
                'explanation' => 'Anonymous classes (PHP 7+) are classes defined inline without a name: `$obj = new class implements Logger { public function log(string $msg): void { echo $msg; } };`. They are useful when you need a one-off object implementing an interface without polluting the namespace with a named class. Often used in testing for quick mock objects and event listeners.',
                'options'     => [
                    ['text' => 'A class defined inline without a name — useful for one-off implementations, especially in tests', 'correct' => true],
                    ['text' => 'A class that cannot be extended or used as a type hint', 'correct' => false],
                    ['text' => 'A class whose constructor is private — can only be created via a factory', 'correct' => false],
                    ['text' => 'A class generated at runtime from an array or JSON configuration', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a PDO transaction and why is it important?',
                'explanation' => 'A transaction groups multiple database operations into an atomic unit. Either ALL operations succeed (commit) or NONE are applied (rollback). This is critical for operations that must be consistent — like transferring money between accounts. Without transactions, a crash between two related operations would leave the database in an inconsistent state.',
                'options'     => [
                    ['text' => 'Groups multiple operations atomically — all succeed or all are rolled back on failure', 'correct' => true],
                    ['text' => 'Caches database queries to reduce server load', 'correct' => false],
                    ['text' => 'Encrypts database communication for secure data transfer', 'correct' => false],
                    ['text' => 'Queues multiple queries and executes them in batch for performance', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the output of var_dump((bool) "0") in PHP and why?',
                'explanation' => '`var_dump((bool) "0")` outputs `bool(false)`. In PHP, the string `"0"` is the ONLY non-empty string that evaluates to `false`. All other non-empty strings (including `"false"`, `"0.0"`, `"00"`) evaluate to `true`. This is because PHP\'s string-to-bool conversion specifically checks for both `""` and `"0"`.',
                'options'     => [
                    ['text' => 'bool(false) — "0" is the only non-empty string that is falsy in PHP', 'correct' => true],
                    ['text' => 'bool(true) — non-empty strings are always truthy', 'correct' => false],
                    ['text' => 'int(0) — PHP converts "0" to integer 0, not boolean', 'correct' => false],
                    ['text' => 'NULL — "0" has no boolean equivalent in PHP', 'correct' => false],
                ],
            ],
        ];
    }

    // ── LEVEL 3 EXAM QUESTIONS ───────────────────────────────────────────────

    private function level3Questions(): array
    {
        return [
            [
                'question'    => 'What is the Repository pattern and what benefits does it provide in PHP?',
                'explanation' => 'The Repository pattern abstracts the data persistence layer behind an interface. Business logic works with the interface without knowing whether data comes from MySQL, MongoDB, an API, or an in-memory array. Benefits: (1) Business logic decoupled from storage — swap databases without changing business code. (2) Testing is easy — inject an in-memory repository. (3) Centralised query logic. Used heavily in Laravel with Eloquent or raw PDO.',
                'options'     => [
                    ['text' => 'Abstracts persistence behind an interface — decouples business logic from storage, enables easy testing with fake repos', 'correct' => true],
                    ['text' => 'Stores query results in memory to avoid repeated database calls', 'correct' => false],
                    ['text' => 'A single class handling all database operations for the entire application', 'correct' => false],
                    ['text' => 'Mirrors the database schema as PHP classes — one class per table', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are PHP Fibers and how do they differ from async/await?',
                'explanation' => 'Fibers (PHP 8.1) are cooperative coroutines — lightweight execution contexts that can pause and resume. Only one Fiber runs at a time (no parallelism). Unlike traditional PHP where a function runs to completion, a Fiber can `Fiber::suspend()` mid-execution. They are NOT async/await — they are a primitive that async libraries (ReactPHP, Amp) use internally to implement green threads.',
                'options'     => [
                    ['text' => 'Cooperative coroutines that can pause mid-execution — a primitive for async libraries, not async/await itself', 'correct' => true],
                    ['text' => 'PHP\'s equivalent of threads — run code in parallel on multiple CPU cores', 'correct' => false],
                    ['text' => 'PHP\'s async/await — identical in behaviour to JavaScript async/await', 'correct' => false],
                    ['text' => 'A sandboxed execution environment with limited memory', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are PHP Enums and how do backed enums differ from pure enums?',
                'explanation' => 'Enums (PHP 8.1) define a type with a fixed set of named cases. Pure enums have no scalar value — cases are just named constants. Backed enums have a string or int scalar value (`: string` or `: int`). Backed enums support `from()` (throws `ValueError` if not found) and `tryFrom()` (returns null). Enums can implement interfaces and have methods. They cannot be instantiated with `new`.',
                'options'     => [
                    ['text' => 'Pure enums are named constants only; backed enums add a scalar value and support from()/tryFrom()', 'correct' => true],
                    ['text' => 'Backed enums are enums that extend a parent class', 'correct' => false],
                    ['text' => 'Pure enums can only hold integer values; backed enums hold any type', 'correct' => false],
                    ['text' => 'They are identical — "backed" is just documentation style', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is covariance and contravariance in PHP type declarations?',
                'explanation' => 'Covariance means a child class can return a MORE specific type than the parent declared. Contravariance means a child class can accept a MORE general parameter type. PHP 7.4+ supports both. Covariance example: parent returns `Animal`, child can return `Dog`. Contravariance example: parent accepts `Dog`, child can accept `Animal`. These preserve the Liskov Substitution Principle.',
                'options'     => [
                    ['text' => 'Covariance: return types can be more specific in subclasses; contravariance: parameter types can be more general', 'correct' => true],
                    ['text' => 'Covariance: parameters can be more specific; contravariance: return types can be more general', 'correct' => false],
                    ['text' => 'Both mean the same thing — type widening is allowed in subclasses', 'correct' => false],
                    ['text' => 'They apply only to generics which PHP does not support', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the #[Attribute] syntax do in PHP?',
                'explanation' => 'PHP 8.0 introduced native Attributes — structured metadata for classes, methods, properties, functions, and parameters. An Attribute is a class annotated with `#[Attribute]`. It can be read at runtime via the Reflection API (`ReflectionClass::getAttributes()`). Frameworks use attributes for routing (`#[Route(\'/users\')]`), validation, and ORM mapping — replacing docblock annotations.',
                'options'     => [
                    ['text' => 'Adds structured metadata to classes/methods readable at runtime via Reflection — replaces docblock annotations', 'correct' => true],
                    ['text' => 'Marks a class as abstract without using the abstract keyword', 'correct' => false],
                    ['text' => 'A compiler directive that enables strict type checking for the annotated element', 'correct' => false],
                    ['text' => 'A way to disable specific PHP warnings for a particular class', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the Observer pattern and how is it implemented in PHP?',
                'explanation' => 'The Observer pattern defines a one-to-many dependency: when a subject changes state, all registered observers are notified. Implementation: define an `EventListener` interface with `handle()`, an `EventEmitter` with `listen()` and `emit()`, listeners register themselves, emitter notifies all. PHP\'s SPL provides `SplObserver` / `SplSubject`. Laravel\'s Event system is a production implementation.',
                'options'     => [
                    ['text' => 'One subject notifies multiple registered listeners on state change — decouples event producers from consumers', 'correct' => true],
                    ['text' => 'A class that watches for file system changes and triggers callbacks', 'correct' => false],
                    ['text' => 'A caching pattern that observes database queries and caches their results', 'correct' => false],
                    ['text' => 'A debugging tool that logs all method calls on an object', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is PSR-4 autoloading and how does Composer implement it?',
                'explanation' => 'PSR-4 maps fully qualified class names to file paths. A namespace prefix maps to a base directory: `"App\\\\" => "src/"` means `App\\Models\\User` maps to `src/Models/User.php`. Composer reads the `autoload.psr-4` section of `composer.json` and generates an autoloader in `vendor/autoload.php`. When PHP encounters an unknown class, the autoloader finds and loads the correct file.',
                'options'     => [
                    ['text' => 'Maps namespace prefixes to directories — App\\Models\\User → src/Models/User.php — Composer generates the autoloader', 'correct' => true],
                    ['text' => 'A PHP extension that compiles class files into bytecode for faster loading', 'correct' => false],
                    ['text' => 'Loads all classes at application start to avoid lazy loading overhead', 'correct' => false],
                    ['text' => 'Requires all classes to be in a single directory', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the Strategy pattern and when would you use it over inheritance?',
                'explanation' => 'The Strategy pattern encapsulates a family of algorithms behind a common interface and makes them interchangeable at runtime. Instead of subclassing (`QuickSortList extends SortableList`), you inject a strategy object (`new Sorter(new QuickSort())`). Use it when you need to switch algorithms at runtime, want to avoid deep inheritance hierarchies, and when algorithm variations are independent of the class using them.',
                'options'     => [
                    ['text' => 'Encapsulates interchangeable algorithms behind an interface — enables runtime algorithm switching without inheritance', 'correct' => true],
                    ['text' => 'Stores multiple versions of an object\'s state and allows reverting to a previous state', 'correct' => false],
                    ['text' => 'A pattern where one class acts as a coordinator and delegates all work to helper classes', 'correct' => false],
                    ['text' => 'Chains multiple processing steps together, passing output of one as input to the next', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between assertSame() and assertEquals() in PHPUnit?',
                'explanation' => '`assertSame($expected, $actual)` checks that both values are identical — same type AND value, equivalent to `===`. `assertEquals()` is loose — it uses type coercion, equivalent to `==`. `assertSame(0, false)` fails; `assertEquals(0, false)` passes. Always prefer `assertSame()` to write precise tests.',
                'options'     => [
                    ['text' => 'assertSame checks type AND value (like ===); assertEquals is loose comparison (like ==) — prefer assertSame', 'correct' => true],
                    ['text' => 'assertSame is for objects (checks instance identity); assertEquals is for value types', 'correct' => false],
                    ['text' => 'They are identical — PHPUnit uses === internally for both', 'correct' => false],
                    ['text' => 'assertSame is faster; assertEquals runs additional checks for complex objects', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is dependency injection and why does it improve testability in PHP?',
                'explanation' => 'Dependency Injection (DI) means a class receives its dependencies from the outside (via constructor, method, or property) rather than creating them internally. Instead of `$this->mailer = new Mailer()`, the constructor accepts `Mailer $mailer`. In tests, you pass a mock — no network calls. Without DI, code creates its own dependencies, making them impossible to swap for testing.',
                'options'     => [
                    ['text' => 'Dependencies are passed in from outside rather than created internally — lets tests inject mocks instead of real implementations', 'correct' => true],
                    ['text' => 'Automatically generating stub implementations of interfaces for testing', 'correct' => false],
                    ['text' => 'A technique where PHP automatically resolves type hints by scanning the class path', 'correct' => false],
                    ['text' => 'Using global variables to share dependencies between classes', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does readonly mean in PHP 8.1 and what are its constraints?',
                'explanation' => '`readonly` properties can only be initialised once — typically in the constructor — and cannot be modified afterwards. Attempting to reassign throws an `Error`. Constraints: readonly properties MUST have a declared type; they cannot have a default value in the declaration; they cannot be `unset()`; they are shallow — a readonly array\'s elements can still be modified, only the reference is locked.',
                'options'     => [
                    ['text' => 'Can only be initialised once in the constructor — reassignment throws Error. Must have a type. Shallow immutability only', 'correct' => true],
                    ['text' => 'Makes the property private and automatically generates a getter method', 'correct' => false],
                    ['text' => 'Prevents the property from being serialised or included in json_encode()', 'correct' => false],
                    ['text' => 'Deep immutability — recursively freezes all nested objects too', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the Decorator pattern and how does it differ from inheritance in PHP?',
                'explanation' => 'The Decorator pattern adds behaviour to an object by wrapping it in another object implementing the same interface, delegating to the wrapped object and adding extra behaviour before/after. Unlike inheritance (static, compile-time), decorators compose at runtime and can be stacked. Each decorator adds one responsibility. This follows the Open/Closed Principle — extend without modifying the original.',
                'options'     => [
                    ['text' => 'Wraps an object in another with the same interface to add behaviour — runtime composition vs compile-time inheritance', 'correct' => true],
                    ['text' => 'Uses PHP attributes (#[Attribute]) to add metadata that modifies class behaviour', 'correct' => false],
                    ['text' => 'Overrides specific methods of a parent class to add logging or caching', 'correct' => false],
                    ['text' => 'Creates a proxy object that intercepts all method calls via __call magic method', 'correct' => false],
                ],
            ],
        ];
    }
}
