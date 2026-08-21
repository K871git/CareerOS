<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Topic;
use Illuminate\Database\Seeder;

class PhpAdvancedQuestionsSeeder extends Seeder
{
    public function run(): void
    {
        $topic = Topic::where('slug', 'php-advanced')->firstOrFail();

        $questions = [

            // ─── Design Patterns ────────────────────────────────────────────
            [
                'question'    => 'Which design pattern ensures only one instance of a class is created?',
                'explanation' => 'The Singleton pattern restricts class instantiation to a single object and provides a global access point to that instance.',
                'options'     => [
                    ['text' => 'Singleton',  'is_correct' => true],
                    ['text' => 'Factory',    'is_correct' => false],
                    ['text' => 'Prototype',  'is_correct' => false],
                    ['text' => 'Builder',    'is_correct' => false],
                ],
            ],
            [
                'question'    => 'In the Factory Method pattern, what is the purpose of the factory method?',
                'explanation' => 'The factory method defers object creation to subclasses, letting subclasses decide which class to instantiate.',
                'options'     => [
                    ['text' => 'To defer object creation to subclasses',              'is_correct' => true],
                    ['text' => 'To create objects without any parameters',            'is_correct' => false],
                    ['text' => 'To restrict the number of objects created',           'is_correct' => false],
                    ['text' => 'To merge multiple objects into one',                  'is_correct' => false],
                ],
            ],
            [
                'question'    => 'Which pattern defines a family of algorithms, encapsulates each one, and makes them interchangeable?',
                'explanation' => 'The Strategy pattern lets the algorithm vary independently from the clients that use it.',
                'options'     => [
                    ['text' => 'Strategy',   'is_correct' => true],
                    ['text' => 'Observer',   'is_correct' => false],
                    ['text' => 'Command',    'is_correct' => false],
                    ['text' => 'Decorator',  'is_correct' => false],
                ],
            ],
            [
                'question'    => 'The Observer pattern is used to:',
                'explanation' => 'Observer defines a one-to-many dependency so when one object changes state, all dependents are notified automatically.',
                'options'     => [
                    ['text' => 'Notify dependent objects when state changes',         'is_correct' => true],
                    ['text' => 'Wrap objects to add behaviour',                       'is_correct' => false],
                    ['text' => 'Restrict access to an object',                        'is_correct' => false],
                    ['text' => 'Create objects step by step',                         'is_correct' => false],
                ],
            ],
            [
                'question'    => 'Which pattern attaches additional responsibilities to an object dynamically without subclassing?',
                'explanation' => 'The Decorator pattern wraps an object to extend its behaviour at runtime.',
                'options'     => [
                    ['text' => 'Decorator',  'is_correct' => true],
                    ['text' => 'Adapter',    'is_correct' => false],
                    ['text' => 'Proxy',      'is_correct' => false],
                    ['text' => 'Facade',     'is_correct' => false],
                ],
            ],
            [
                'question'    => 'The Repository pattern is primarily used to:',
                'explanation' => 'Repository abstracts the data layer, providing a collection-like interface for accessing domain objects.',
                'options'     => [
                    ['text' => 'Abstract data access logic from business logic',      'is_correct' => true],
                    ['text' => 'Cache database queries automatically',               'is_correct' => false],
                    ['text' => 'Encrypt database records',                           'is_correct' => false],
                    ['text' => 'Convert SQL results to HTML',                        'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What is the Dependency Injection (DI) container responsible for?',
                'explanation' => 'A DI container resolves and injects class dependencies automatically, inverting control of object creation.',
                'options'     => [
                    ['text' => 'Automatically resolving and injecting dependencies',  'is_correct' => true],
                    ['text' => 'Storing session data between requests',               'is_correct' => false],
                    ['text' => 'Compiling PHP to native code',                       'is_correct' => false],
                    ['text' => 'Managing database migrations',                       'is_correct' => false],
                ],
            ],
            [
                'question'    => 'Which SOLID principle states that a class should have only one reason to change?',
                'explanation' => 'The Single Responsibility Principle (SRP) means each class has a single, well-defined responsibility.',
                'options'     => [
                    ['text' => 'Single Responsibility Principle',  'is_correct' => true],
                    ['text' => 'Open/Closed Principle',            'is_correct' => false],
                    ['text' => 'Liskov Substitution Principle',    'is_correct' => false],
                    ['text' => 'Interface Segregation Principle',  'is_correct' => false],
                ],
            ],
            [
                'question'    => 'Which SOLID principle states software entities should be open for extension but closed for modification?',
                'explanation' => 'The Open/Closed Principle encourages extending behaviour via inheritance or composition rather than modifying existing code.',
                'options'     => [
                    ['text' => 'Open/Closed Principle',            'is_correct' => true],
                    ['text' => 'Single Responsibility Principle',  'is_correct' => false],
                    ['text' => 'Dependency Inversion Principle',   'is_correct' => false],
                    ['text' => 'Liskov Substitution Principle',    'is_correct' => false],
                ],
            ],
            [
                'question'    => 'The Liskov Substitution Principle requires that:',
                'explanation' => 'Objects of a subclass must be substitutable for objects of the superclass without altering program correctness.',
                'options'     => [
                    ['text' => 'Subclasses can replace their base class without breaking the program', 'is_correct' => true],
                    ['text' => 'Classes should never be extended',                                    'is_correct' => false],
                    ['text' => 'Interfaces must always be used instead of classes',                   'is_correct' => false],
                    ['text' => 'Each class must implement every interface method',                    'is_correct' => false],
                ],
            ],

            // ─── PSR Standards ──────────────────────────────────────────────
            [
                'question'    => 'PSR-4 defines:',
                'explanation' => 'PSR-4 is the autoloading standard that maps namespaces to directory structures, enabling Composer to autoload classes.',
                'options'     => [
                    ['text' => 'Autoloading standard for namespaces to directories',  'is_correct' => true],
                    ['text' => 'HTTP message interfaces',                             'is_correct' => false],
                    ['text' => 'Coding style guide',                                 'is_correct' => false],
                    ['text' => 'Logger interface',                                   'is_correct' => false],
                ],
            ],
            [
                'question'    => 'PSR-12 is the standard for:',
                'explanation' => 'PSR-12 extends PSR-2 and defines extended coding style guidelines for PHP code.',
                'options'     => [
                    ['text' => 'Extended coding style guide',   'is_correct' => true],
                    ['text' => 'HTTP client interfaces',        'is_correct' => false],
                    ['text' => 'Cache interface',               'is_correct' => false],
                    ['text' => 'Event dispatcher interface',    'is_correct' => false],
                ],
            ],
            [
                'question'    => 'PSR-7 defines:',
                'explanation' => 'PSR-7 defines common interfaces for HTTP messages (requests and responses) to enable interoperability between frameworks.',
                'options'     => [
                    ['text' => 'HTTP message interfaces',               'is_correct' => true],
                    ['text' => 'Autoloading standard',                  'is_correct' => false],
                    ['text' => 'Dependency injection container',        'is_correct' => false],
                    ['text' => 'SQL query builder interface',           'is_correct' => false],
                ],
            ],
            [
                'question'    => 'PSR-3 defines the:',
                'explanation' => 'PSR-3 specifies a common LoggerInterface, allowing libraries to log to any PSR-3-compliant logger (e.g., Monolog).',
                'options'     => [
                    ['text' => 'Logger interface',           'is_correct' => true],
                    ['text' => 'HTTP client interface',      'is_correct' => false],
                    ['text' => 'Cache interface',            'is_correct' => false],
                    ['text' => 'Autoloading standard',      'is_correct' => false],
                ],
            ],
            [
                'question'    => 'According to PSR-1, class names must be declared using:',
                'explanation' => 'PSR-1 requires class names to follow StudlyCaps (PascalCase) notation.',
                'options'     => [
                    ['text' => 'StudlyCaps',    'is_correct' => true],
                    ['text' => 'snake_case',    'is_correct' => false],
                    ['text' => 'camelCase',     'is_correct' => false],
                    ['text' => 'UPPER_CASE',    'is_correct' => false],
                ],
            ],

            // ─── PHP 8.x Advanced Features ──────────────────────────────────
            [
                'question'    => 'What does the `never` return type indicate in PHP 8.1?',
                'explanation' => '`never` means the function never returns normally — it either throws an exception or calls exit().',
                'options'     => [
                    ['text' => 'The function always throws or exits, never returning normally', 'is_correct' => true],
                    ['text' => 'The function returns null',                                     'is_correct' => false],
                    ['text' => 'The function is asynchronous',                                 'is_correct' => false],
                    ['text' => 'The function has no return type declared',                     'is_correct' => false],
                ],
            ],
            [
                'question'    => 'PHP 8.0 Fibers are best described as:',
                'explanation' => 'Fibers are interruptible functions that allow cooperative multitasking within a single thread in PHP.',
                'options'     => [
                    ['text' => 'Interruptible functions for cooperative multitasking',    'is_correct' => true],
                    ['text' => 'Multi-threaded parallel execution units',                 'is_correct' => false],
                    ['text' => 'A new way to declare closures',                          'is_correct' => false],
                    ['text' => 'Async HTTP request wrappers',                            'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of `readonly` properties in PHP 8.1?',
                'explanation' => 'A `readonly` property can only be initialized once; subsequent write attempts throw an Error.',
                'options'     => [
                    ['text' => 'They can be written only once after initialization',     'is_correct' => true],
                    ['text' => 'They are automatically public',                          'is_correct' => false],
                    ['text' => 'They cannot be accessed outside the class',              'is_correct' => false],
                    ['text' => 'They are serialised to JSON by default',                 'is_correct' => false],
                ],
            ],
            [
                'question'    => 'PHP 8.1 Enums can implement:',
                'explanation' => 'Enums in PHP 8.1 can implement interfaces, allowing them to satisfy type constraints.',
                'options'     => [
                    ['text' => 'Interfaces',           'is_correct' => true],
                    ['text' => 'Abstract classes',     'is_correct' => false],
                    ['text' => 'Traits only',          'is_correct' => false],
                    ['text' => 'No contracts at all',  'is_correct' => false],
                ],
            ],
            [
                'question'    => 'In PHP 8.0, what does the `match` expression do differently from `switch`?',
                'explanation' => '`match` uses strict comparison (===), returns a value, has no fall-through, and throws UnhandledMatchError for unmatched values.',
                'options'     => [
                    ['text' => 'Uses strict comparison and returns a value with no fall-through', 'is_correct' => true],
                    ['text' => 'Uses loose comparison like switch',                               'is_correct' => false],
                    ['text' => 'Only works with string values',                                   'is_correct' => false],
                    ['text' => 'Cannot have a default arm',                                       'is_correct' => false],
                ],
            ],
            [
                'question'    => 'Constructor property promotion in PHP 8.0 allows you to:',
                'explanation' => 'Constructor promotion lets you declare and initialize class properties directly in the constructor parameter list.',
                'options'     => [
                    ['text' => 'Declare and initialise properties in the constructor signature',   'is_correct' => true],
                    ['text' => 'Make all constructor parameters optional',                         'is_correct' => false],
                    ['text' => 'Automatically generate getter methods',                            'is_correct' => false],
                    ['text' => 'Prevent subclasses from overriding the constructor',               'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What does the nullsafe operator `?->` do in PHP 8.0?',
                'explanation' => 'The nullsafe operator short-circuits the chain and returns null if any operand is null, avoiding null pointer errors.',
                'options'     => [
                    ['text' => 'Short-circuits and returns null if the left side is null',  'is_correct' => true],
                    ['text' => 'Throws a NullPointerException if null',                    'is_correct' => false],
                    ['text' => 'Converts null to an empty string',                         'is_correct' => false],
                    ['text' => 'Checks if a variable is null and assigns a default',       'is_correct' => false],
                ],
            ],
            [
                'question'    => 'Named arguments in PHP 8.0 allow you to:',
                'explanation' => 'Named arguments let you pass values to function parameters by name, in any order, and skip optional parameters.',
                'options'     => [
                    ['text' => 'Pass arguments by parameter name in any order',             'is_correct' => true],
                    ['text' => 'Override the function signature at call time',              'is_correct' => false],
                    ['text' => 'Automatically convert types to match the function',        'is_correct' => false],
                    ['text' => 'Pass arrays as individual arguments',                      'is_correct' => false],
                ],
            ],

            // ─── Generators ─────────────────────────────────────────────────
            [
                'question'    => 'What keyword is used to create a generator function in PHP?',
                'explanation' => '`yield` pauses execution and returns a value; resuming continues from the yield point.',
                'options'     => [
                    ['text' => 'yield',    'is_correct' => true],
                    ['text' => 'return',   'is_correct' => false],
                    ['text' => 'emit',     'is_correct' => false],
                    ['text' => 'produce',  'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What interface do PHP generators implement?',
                'explanation' => 'Generator objects implement the Iterator interface, providing current(), key(), next(), rewind(), and valid() methods.',
                'options'     => [
                    ['text' => 'Iterator',       'is_correct' => true],
                    ['text' => 'Countable',      'is_correct' => false],
                    ['text' => 'Serializable',   'is_correct' => false],
                    ['text' => 'ArrayAccess',    'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What is the main memory advantage of generators over returning large arrays?',
                'explanation' => 'Generators produce values one at a time, so only a single value is held in memory at any point.',
                'options'     => [
                    ['text' => 'Only one value is held in memory at a time',              'is_correct' => true],
                    ['text' => 'Generators compress data automatically',                  'is_correct' => false],
                    ['text' => 'Generators skip duplicate values',                        'is_correct' => false],
                    ['text' => 'Generators cache results to disk',                        'is_correct' => false],
                ],
            ],
            [
                'question'    => '`yield from` in PHP is used to:',
                'explanation' => '`yield from` delegates to another generator or iterable, yielding each of its values in turn.',
                'options'     => [
                    ['text' => 'Delegate to another generator or iterable',              'is_correct' => true],
                    ['text' => 'Return a value from inside a generator',                 'is_correct' => false],
                    ['text' => 'Import values from an external file',                    'is_correct' => false],
                    ['text' => 'Send data to a socket',                                  'is_correct' => false],
                ],
            ],

            // ─── SPL Data Structures ────────────────────────────────────────
            [
                'question'    => 'Which SPL class provides a doubly linked list implementation?',
                'explanation' => 'SplDoublyLinkedList is the base for SplStack and SplQueue in SPL.',
                'options'     => [
                    ['text' => 'SplDoublyLinkedList',  'is_correct' => true],
                    ['text' => 'SplHeap',              'is_correct' => false],
                    ['text' => 'SplFixedArray',        'is_correct' => false],
                    ['text' => 'SplPriorityQueue',     'is_correct' => false],
                ],
            ],
            [
                'question'    => 'When should you use `SplFixedArray` over a standard PHP array?',
                'explanation' => 'SplFixedArray uses less memory than a regular array because it has a fixed size and only stores numeric indices.',
                'options'     => [
                    ['text' => 'When you need a fixed-size, memory-efficient numeric array',  'is_correct' => true],
                    ['text' => 'When you need associative keys',                              'is_correct' => false],
                    ['text' => 'When you need to sort automatically',                        'is_correct' => false],
                    ['text' => 'When you need to serialize data to JSON',                    'is_correct' => false],
                ],
            ],
            [
                'question'    => 'Which SPL class implements a max-heap?',
                'explanation' => 'SplMaxHeap extends SplHeap and keeps the maximum element at the top.',
                'options'     => [
                    ['text' => 'SplMaxHeap',         'is_correct' => true],
                    ['text' => 'SplPriorityQueue',   'is_correct' => false],
                    ['text' => 'SplStack',            'is_correct' => false],
                    ['text' => 'SplHeap',             'is_correct' => false],
                ],
            ],

            // ─── Reflection API ─────────────────────────────────────────────
            [
                'question'    => 'What is the PHP Reflection API primarily used for?',
                'explanation' => 'The Reflection API lets you introspect classes, methods, properties, and parameters at runtime.',
                'options'     => [
                    ['text' => 'Inspecting classes and methods at runtime',            'is_correct' => true],
                    ['text' => 'Caching method return values',                        'is_correct' => false],
                    ['text' => 'Encrypting class properties',                         'is_correct' => false],
                    ['text' => 'Converting objects to arrays automatically',          'is_correct' => false],
                ],
            ],
            [
                'question'    => 'Which class do you use to reflect on a PHP class?',
                'explanation' => 'ReflectionClass provides introspection of a class\'s structure including methods, properties, and constants.',
                'options'     => [
                    ['text' => 'ReflectionClass',    'is_correct' => true],
                    ['text' => 'Inspector',          'is_correct' => false],
                    ['text' => 'ClassAnalyzer',      'is_correct' => false],
                    ['text' => 'MetaClass',          'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What does `ReflectionMethod::invoke()` do?',
                'explanation' => 'ReflectionMethod::invoke() calls the reflected method on a given object, even if it is private.',
                'options'     => [
                    ['text' => 'Calls the reflected method on the given object',     'is_correct' => true],
                    ['text' => 'Returns the method\'s source code as a string',      'is_correct' => false],
                    ['text' => 'Disables the method permanently',                   'is_correct' => false],
                    ['text' => 'Returns the return type of the method',             'is_correct' => false],
                ],
            ],

            // ─── Late Static Binding ────────────────────────────────────────
            [
                'question'    => 'What does `static::` refer to in PHP (Late Static Binding)?',
                'explanation' => '`static::` refers to the class that was actually called at runtime, not the class where the method is defined.',
                'options'     => [
                    ['text' => 'The class that was called at runtime',               'is_correct' => true],
                    ['text' => 'The parent class',                                   'is_correct' => false],
                    ['text' => 'The current object\'s class, always',               'is_correct' => false],
                    ['text' => 'A static variable shared across all instances',      'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `self::` and `static::` in PHP?',
                'explanation' => '`self::` always refers to the class where the method is written; `static::` refers to the called class (late static binding).',
                'options'     => [
                    ['text' => '`self::` binds to the defining class; `static::` binds to the called class', 'is_correct' => true],
                    ['text' => 'They are identical in behaviour',                                             'is_correct' => false],
                    ['text' => '`static::` always refers to the parent class',                               'is_correct' => false],
                    ['text' => '`self::` is for static methods only',                                       'is_correct' => false],
                ],
            ],

            // ─── Security ───────────────────────────────────────────────────
            [
                'question'    => 'Which PHP function is recommended for hashing passwords?',
                'explanation' => '`password_hash()` with PASSWORD_BCRYPT or PASSWORD_DEFAULT uses a strong adaptive hashing algorithm with automatic salting.',
                'options'     => [
                    ['text' => 'password_hash()',  'is_correct' => true],
                    ['text' => 'md5()',             'is_correct' => false],
                    ['text' => 'sha256()',          'is_correct' => false],
                    ['text' => 'crypt()',           'is_correct' => false],
                ],
            ],
            [
                'question'    => 'How do you prevent SQL injection in PHP?',
                'explanation' => 'Prepared statements with bound parameters ensure user input is never interpreted as SQL.',
                'options'     => [
                    ['text' => 'Use PDO prepared statements with bound parameters',    'is_correct' => true],
                    ['text' => 'Escape strings with addslashes()',                    'is_correct' => false],
                    ['text' => 'Use htmlspecialchars() on all inputs',               'is_correct' => false],
                    ['text' => 'Validate inputs with is_numeric()',                  'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of CSRF tokens?',
                'explanation' => 'CSRF tokens ensure that form submissions originate from the legitimate site and not from a malicious third-party page.',
                'options'     => [
                    ['text' => 'Prevent forged cross-site requests',                  'is_correct' => true],
                    ['text' => 'Encrypt form data before submission',                 'is_correct' => false],
                    ['text' => 'Speed up form processing',                           'is_correct' => false],
                    ['text' => 'Store session data securely',                        'is_correct' => false],
                ],
            ],
            [
                'question'    => 'Which function prevents XSS when outputting user data in HTML?',
                'explanation' => '`htmlspecialchars()` converts special characters to HTML entities, neutralising XSS payloads.',
                'options'     => [
                    ['text' => 'htmlspecialchars()',    'is_correct' => true],
                    ['text' => 'strip_tags()',          'is_correct' => false],
                    ['text' => 'addslashes()',          'is_correct' => false],
                    ['text' => 'urlencode()',           'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What does Content Security Policy (CSP) protect against?',
                'explanation' => 'CSP is an HTTP header that restricts which resources a browser can load, mitigating XSS and data injection attacks.',
                'options'     => [
                    ['text' => 'Cross-Site Scripting (XSS) and data injection',       'is_correct' => true],
                    ['text' => 'SQL injection attacks',                               'is_correct' => false],
                    ['text' => 'Session fixation attacks',                           'is_correct' => false],
                    ['text' => 'Brute force login attacks',                          'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What is the best practice for session fixation prevention?',
                'explanation' => 'Regenerating the session ID after login prevents an attacker from fixing a known session ID before authentication.',
                'options'     => [
                    ['text' => 'Call session_regenerate_id(true) after login',       'is_correct' => true],
                    ['text' => 'Store session in a cookie without HttpOnly',         'is_correct' => false],
                    ['text' => 'Use a predictable session ID',                       'is_correct' => false],
                    ['text' => 'Disable sessions after login',                       'is_correct' => false],
                ],
            ],
            [
                'question'    => 'Which header should be set to prevent clickjacking?',
                'explanation' => 'X-Frame-Options: DENY or SAMEORIGIN prevents the page from being embedded in iframes on other origins.',
                'options'     => [
                    ['text' => 'X-Frame-Options',              'is_correct' => true],
                    ['text' => 'X-Content-Type-Options',       'is_correct' => false],
                    ['text' => 'Strict-Transport-Security',    'is_correct' => false],
                    ['text' => 'Content-Security-Policy',      'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What does `password_verify()` do?',
                'explanation' => '`password_verify()` compares a plaintext password against a stored hash in a timing-safe manner.',
                'options'     => [
                    ['text' => 'Checks a plaintext password against a stored hash',  'is_correct' => true],
                    ['text' => 'Re-hashes the password with a new salt',             'is_correct' => false],
                    ['text' => 'Encrypts the password for storage',                 'is_correct' => false],
                    ['text' => 'Validates password length and complexity',           'is_correct' => false],
                ],
            ],

            // ─── Performance & OPcache ──────────────────────────────────────
            [
                'question'    => 'What does PHP OPcache do?',
                'explanation' => 'OPcache stores precompiled script bytecode in memory, eliminating parsing and compilation on every request.',
                'options'     => [
                    ['text' => 'Caches compiled PHP bytecode in memory',             'is_correct' => true],
                    ['text' => 'Caches database query results',                      'is_correct' => false],
                    ['text' => 'Compresses HTTP responses',                         'is_correct' => false],
                    ['text' => 'Minifies PHP source files',                         'is_correct' => false],
                ],
            ],
            [
                'question'    => 'Which PHP function measures memory usage of the current script?',
                'explanation' => '`memory_get_usage()` returns the amount of memory allocated to PHP; `memory_get_peak_usage()` returns the peak.',
                'options'     => [
                    ['text' => 'memory_get_usage()',      'is_correct' => true],
                    ['text' => 'getMemory()',              'is_correct' => false],
                    ['text' => 'sys_getloadavg()',         'is_correct' => false],
                    ['text' => 'php_memory()',             'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of `xdebug` in PHP development?',
                'explanation' => 'Xdebug provides stack traces, profiling, code coverage, and step debugging capabilities.',
                'options'     => [
                    ['text' => 'Profiling, debugging, and code coverage',             'is_correct' => true],
                    ['text' => 'Encrypting PHP sessions',                            'is_correct' => false],
                    ['text' => 'Minifying PHP output',                              'is_correct' => false],
                    ['text' => 'Managing Composer dependencies',                    'is_correct' => false],
                ],
            ],
            [
                'question'    => 'Lazy loading of objects in PHP refers to:',
                'explanation' => 'Lazy loading defers object initialisation until the object is actually needed, reducing startup overhead.',
                'options'     => [
                    ['text' => 'Deferring object creation until it is actually needed', 'is_correct' => true],
                    ['text' => 'Loading objects from a cache file',                     'is_correct' => false],
                    ['text' => 'Loading multiple objects simultaneously',              'is_correct' => false],
                    ['text' => 'Skipping object validation',                          'is_correct' => false],
                ],
            ],

            // ─── PHPUnit Testing ────────────────────────────────────────────
            [
                'question'    => 'Which PHPUnit annotation marks a method as a test case?',
                'explanation' => 'Methods prefixed with `test` or annotated with @test are recognised by PHPUnit as test cases.',
                'options'     => [
                    ['text' => '@test',           'is_correct' => true],
                    ['text' => '@assert',         'is_correct' => false],
                    ['text' => '@check',          'is_correct' => false],
                    ['text' => '@verify',         'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What is a PHPUnit mock object?',
                'explanation' => 'A mock object replaces a real dependency with a test double that you can configure to return specific values and assert calls.',
                'options'     => [
                    ['text' => 'A test double that replaces a real dependency',       'is_correct' => true],
                    ['text' => 'A copy of the database for testing',                 'is_correct' => false],
                    ['text' => 'A simulated HTTP request',                          'is_correct' => false],
                    ['text' => 'A read-only version of a class',                    'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What does `setUp()` do in a PHPUnit test class?',
                'explanation' => '`setUp()` runs before each test method, allowing you to initialise shared state or dependencies.',
                'options'     => [
                    ['text' => 'Runs before each test method to initialise state',   'is_correct' => true],
                    ['text' => 'Runs once before all tests in the class',            'is_correct' => false],
                    ['text' => 'Cleans up after all tests are done',                'is_correct' => false],
                    ['text' => 'Sets the database schema for testing',              'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between a PHPUnit stub and a mock?',
                'explanation' => 'A stub returns pre-programmed values; a mock additionally verifies that specific methods were called as expected.',
                'options'     => [
                    ['text' => 'A stub provides pre-set return values; a mock also verifies method calls', 'is_correct' => true],
                    ['text' => 'They are identical in behaviour',                                          'is_correct' => false],
                    ['text' => 'A mock replaces entire classes; a stub only replaces methods',            'is_correct' => false],
                    ['text' => 'Stubs work with databases; mocks work with HTTP',                        'is_correct' => false],
                ],
            ],
            [
                'question'    => 'Which PHPUnit method asserts that two values are strictly equal?',
                'explanation' => '`assertSame()` checks both value and type equality (===), whereas `assertEquals()` uses loose comparison.',
                'options'     => [
                    ['text' => 'assertSame()',       'is_correct' => true],
                    ['text' => 'assertEquals()',     'is_correct' => false],
                    ['text' => 'assertIdentical()',  'is_correct' => false],
                    ['text' => 'assertStrict()',     'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What does TDD stand for and what is its core cycle?',
                'explanation' => 'Test-Driven Development: write a failing test (Red), make it pass (Green), then refactor (Refactor).',
                'options'     => [
                    ['text' => 'Test-Driven Development — Red, Green, Refactor',     'is_correct' => true],
                    ['text' => 'Test-Deploy Development — Build, Test, Deploy',      'is_correct' => false],
                    ['text' => 'Type-Driven Design — Define, Implement, Test',       'is_correct' => false],
                    ['text' => 'Total-Driven Development — Plan, Code, Review',      'is_correct' => false],
                ],
            ],

            // ─── Composer Internals ─────────────────────────────────────────
            [
                'question'    => 'What file does Composer generate to lock dependency versions?',
                'explanation' => '`composer.lock` records the exact installed versions so every environment gets identical packages.',
                'options'     => [
                    ['text' => 'composer.lock',      'is_correct' => true],
                    ['text' => 'composer.json',      'is_correct' => false],
                    ['text' => 'packages.json',      'is_correct' => false],
                    ['text' => 'vendor.lock',        'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What does `composer dump-autoload --optimize` do?',
                'explanation' => 'It generates an optimised class map that avoids filesystem lookups at runtime, improving performance.',
                'options'     => [
                    ['text' => 'Generates an optimised classmap for faster autoloading', 'is_correct' => true],
                    ['text' => 'Removes unused packages from vendor',                   'is_correct' => false],
                    ['text' => 'Updates all dependencies to latest versions',           'is_correct' => false],
                    ['text' => 'Minifies PHP files in the vendor directory',            'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `require` and `require-dev` in composer.json?',
                'explanation' => '`require` lists production dependencies; `require-dev` lists packages needed only for development and testing.',
                'options'     => [
                    ['text' => '`require` is for production; `require-dev` is for development/testing only', 'is_correct' => true],
                    ['text' => '`require-dev` is loaded first at startup',                                   'is_correct' => false],
                    ['text' => 'They are identical — the naming is just convention',                         'is_correct' => false],
                    ['text' => '`require` installs globally; `require-dev` installs locally',               'is_correct' => false],
                ],
            ],

            // ─── Advanced OOP ───────────────────────────────────────────────
            [
                'question'    => 'What is Covariance in PHP return types?',
                'explanation' => 'Covariance allows a child class method to return a more specific (narrower) type than declared in the parent.',
                'options'     => [
                    ['text' => 'A child method can return a more specific type than the parent',  'is_correct' => true],
                    ['text' => 'A child method must return the exact same type as the parent',   'is_correct' => false],
                    ['text' => 'A method can return any type regardless of the parent',          'is_correct' => false],
                    ['text' => 'A method return type is inferred automatically',                 'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What is Contravariance in PHP parameter types?',
                'explanation' => 'Contravariance allows a child class method to accept a broader (wider) type in its parameters than declared in the parent.',
                'options'     => [
                    ['text' => 'A child method can accept a wider type than the parent declares', 'is_correct' => true],
                    ['text' => 'A child method must accept a narrower type',                      'is_correct' => false],
                    ['text' => 'Parameters must match exactly in child classes',                 'is_correct' => false],
                    ['text' => 'Only return types can vary between parent and child',            'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `__clone()` magic method allow you to do?',
                'explanation' => '`__clone()` is invoked when an object is cloned with the `clone` keyword, allowing you to deep-copy nested objects.',
                'options'     => [
                    ['text' => 'Customise the behaviour of object cloning',          'is_correct' => true],
                    ['text' => 'Prevent an object from being cloned',               'is_correct' => false],
                    ['text' => 'Duplicate a database record automatically',         'is_correct' => false],
                    ['text' => 'Reset all properties to their default values',      'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `__debugInfo()` magic method control?',
                'explanation' => '`__debugInfo()` defines which properties are shown when `var_dump()` is called on the object.',
                'options'     => [
                    ['text' => 'Properties shown when var_dump() is called',         'is_correct' => true],
                    ['text' => 'Properties stored when serializing the object',      'is_correct' => false],
                    ['text' => 'Error messages thrown by the object',               'is_correct' => false],
                    ['text' => 'Properties accessible from outside the class',      'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of the `__invoke()` magic method?',
                'explanation' => '`__invoke()` is called when an object is used as a callable (invoked like a function).',
                'options'     => [
                    ['text' => 'Makes an object callable like a function',           'is_correct' => true],
                    ['text' => 'Runs when an object is created',                    'is_correct' => false],
                    ['text' => 'Converts an object to a string',                   'is_correct' => false],
                    ['text' => 'Handles undefined method calls',                   'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What is method chaining in PHP?',
                'explanation' => 'Method chaining returns `$this` from each method, allowing multiple method calls on the same object in one expression.',
                'options'     => [
                    ['text' => 'Returning $this from methods to chain calls fluently', 'is_correct' => true],
                    ['text' => 'Calling static methods one after another',            'is_correct' => false],
                    ['text' => 'Linking multiple objects together in memory',         'is_correct' => false],
                    ['text' => 'Overloading operators to concatenate objects',        'is_correct' => false],
                ],
            ],

            // ─── Advanced Array & Functional PHP ────────────────────────────
            [
                'question'    => 'What does `array_walk_recursive()` do?',
                'explanation' => '`array_walk_recursive()` applies a callback to every value of a multidimensional array, recursing into nested arrays.',
                'options'     => [
                    ['text' => 'Applies a callback to every value in a nested array recursively', 'is_correct' => true],
                    ['text' => 'Flattens a multi-dimensional array into one level',               'is_correct' => false],
                    ['text' => 'Sorts a multi-dimensional array by its keys',                     'is_correct' => false],
                    ['text' => 'Merges nested arrays into the parent array',                      'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What does `array_chunk()` do?',
                'explanation' => '`array_chunk()` splits an array into chunks of specified size, returning an array of arrays.',
                'options'     => [
                    ['text' => 'Splits an array into chunks of specified size',       'is_correct' => true],
                    ['text' => 'Removes duplicate values from an array',             'is_correct' => false],
                    ['text' => 'Groups array elements by a callback result',         'is_correct' => false],
                    ['text' => 'Flattens nested arrays to a single level',           'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What is the output of `array_diff([1,2,3], [2,3,4])`?',
                'explanation' => '`array_diff()` returns values in the first array that are not present in any subsequent array — here only 1.',
                'options'     => [
                    ['text' => '[1]',      'is_correct' => true],
                    ['text' => '[4]',      'is_correct' => false],
                    ['text' => '[1, 4]',   'is_correct' => false],
                    ['text' => '[]',       'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What does `array_intersect()` return?',
                'explanation' => '`array_intersect()` returns values that exist in all given arrays.',
                'options'     => [
                    ['text' => 'Values present in all given arrays',                  'is_correct' => true],
                    ['text' => 'Values unique to the first array',                   'is_correct' => false],
                    ['text' => 'The merged result of all arrays',                    'is_correct' => false],
                    ['text' => 'Keys that are common across arrays',                 'is_correct' => false],
                ],
            ],

            // ─── Advanced String & Regex ─────────────────────────────────────
            [
                'question'    => 'What does the `(?:...)` construct do in a PHP regex?',
                'explanation' => '`(?:...)` is a non-capturing group — it groups expressions without creating a back-reference capture group.',
                'options'     => [
                    ['text' => 'Groups without capturing (non-capturing group)',      'is_correct' => true],
                    ['text' => 'Makes the group optional',                           'is_correct' => false],
                    ['text' => 'Creates a named capture group',                      'is_correct' => false],
                    ['text' => 'Matches the group zero or more times',               'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What is a lookahead assertion in PHP regex?',
                'explanation' => 'A lookahead `(?=...)` matches a position only if the pattern ahead matches, without consuming characters.',
                'options'     => [
                    ['text' => 'Matches a position where the pattern ahead is found without consuming it', 'is_correct' => true],
                    ['text' => 'Looks behind for a previous match',                                       'is_correct' => false],
                    ['text' => 'Matches the last occurrence of a pattern',                                'is_correct' => false],
                    ['text' => 'Skips whitespace before matching',                                       'is_correct' => false],
                ],
            ],
            [
                'question'    => 'Which function replaces regex matches using a callback in PHP?',
                'explanation' => '`preg_replace_callback()` passes each match to a callback function and replaces it with the callback\'s return value.',
                'options'     => [
                    ['text' => 'preg_replace_callback()',   'is_correct' => true],
                    ['text' => 'preg_replace()',            'is_correct' => false],
                    ['text' => 'preg_match_all()',          'is_correct' => false],
                    ['text' => 'str_replace()',             'is_correct' => false],
                ],
            ],

            // ─── Concurrency, Queues & Advanced Architecture ─────────────────
            [
                'question'    => 'What is a Message Queue used for in PHP applications?',
                'explanation' => 'Message queues decouple producers from consumers, enabling background processing of time-consuming tasks.',
                'options'     => [
                    ['text' => 'Decoupling and background processing of tasks',       'is_correct' => true],
                    ['text' => 'Storing session messages between requests',           'is_correct' => false],
                    ['text' => 'Caching SQL queries between requests',               'is_correct' => false],
                    ['text' => 'Sending real-time updates to the browser',           'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What is the Command Bus pattern?',
                'explanation' => 'The Command Bus routes Command objects to their dedicated Handlers, separating the intent (command) from the execution (handler).',
                'options'     => [
                    ['text' => 'Routes command objects to their dedicated handlers',  'is_correct' => true],
                    ['text' => 'Executes database commands in sequence',              'is_correct' => false],
                    ['text' => 'Sends shell commands to the operating system',       'is_correct' => false],
                    ['text' => 'Manages CLI argument parsing',                       'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What does CQRS stand for?',
                'explanation' => 'Command Query Responsibility Segregation separates read (query) and write (command) models for scalability and clarity.',
                'options'     => [
                    ['text' => 'Command Query Responsibility Segregation',            'is_correct' => true],
                    ['text' => 'Concurrent Queue Routing Service',                   'is_correct' => false],
                    ['text' => 'Cached Query Result Storage',                        'is_correct' => false],
                    ['text' => 'Class-Query Response System',                       'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What is Event Sourcing?',
                'explanation' => 'Event Sourcing stores state as a sequence of domain events instead of the current state, allowing full audit and replay.',
                'options'     => [
                    ['text' => 'Storing state as a sequence of events for full audit trail', 'is_correct' => true],
                    ['text' => 'Logging PHP errors to an external service',                  'is_correct' => false],
                    ['text' => 'Subscribing to browser events from the server',              'is_correct' => false],
                    ['text' => 'Caching event handler return values',                       'is_correct' => false],
                ],
            ],

            // ─── PHP Type System ─────────────────────────────────────────────
            [
                'question'    => 'What is an intersection type in PHP 8.1?',
                'explanation' => 'An intersection type (T&U) requires the value to satisfy ALL listed types simultaneously.',
                'options'     => [
                    ['text' => 'A type that must satisfy all combined types simultaneously', 'is_correct' => true],
                    ['text' => 'A type that satisfies any one of the combined types',       'is_correct' => false],
                    ['text' => 'A union of two primitive types',                            'is_correct' => false],
                    ['text' => 'A nullable version of a type',                             'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `mixed` type declaration in PHP 8?',
                'explanation' => '`mixed` is an explicit declaration that a parameter or return value can be of any type, including null.',
                'options'     => [
                    ['text' => 'Explicitly declares any type is accepted, including null', 'is_correct' => true],
                    ['text' => 'A type that converts values automatically',               'is_correct' => false],
                    ['text' => 'A type only for class properties',                       'is_correct' => false],
                    ['text' => 'A type that prevents type errors',                       'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What happens when `declare(strict_types=1)` is set at the top of a file?',
                'explanation' => 'With strict_types=1, PHP enforces type declarations strictly for scalar types; passing the wrong type throws a TypeError.',
                'options'     => [
                    ['text' => 'Type coercion is disabled; wrong types throw TypeError', 'is_correct' => true],
                    ['text' => 'All variables become typed automatically',               'is_correct' => false],
                    ['text' => 'Only class type hints are enforced',                    'is_correct' => false],
                    ['text' => 'PHP compiles the file to native code',                  'is_correct' => false],
                ],
            ],

            // ─── Advanced Database & Laravel Patterns ────────────────────────
            [
                'question'    => 'What is the N+1 query problem?',
                'explanation' => 'N+1 occurs when a query fetches N records and then executes one additional query per record. Eager loading fixes it.',
                'options'     => [
                    ['text' => 'One query fetches N records then N additional queries run for each', 'is_correct' => true],
                    ['text' => 'A query that runs N times slower than expected',                    'is_correct' => false],
                    ['text' => 'A database constraint that limits result sets',                     'is_correct' => false],
                    ['text' => 'A caching issue where N records expire simultaneously',             'is_correct' => false],
                ],
            ],
            [
                'question'    => 'How does database query indexing improve performance?',
                'explanation' => 'Indexes allow the database engine to find rows without a full table scan, dramatically reducing query time on large datasets.',
                'options'     => [
                    ['text' => 'Allows the engine to find rows without a full table scan', 'is_correct' => true],
                    ['text' => 'Compresses table data to reduce storage',                   'is_correct' => false],
                    ['text' => 'Automatically caches query results',                        'is_correct' => false],
                    ['text' => 'Prevents duplicate rows in a table',                       'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What is a database transaction and why is it important?',
                'explanation' => 'A transaction groups multiple operations into an atomic unit — either all succeed (commit) or all fail (rollback), preserving data integrity.',
                'options'     => [
                    ['text' => 'An atomic group of operations that all succeed or all fail together', 'is_correct' => true],
                    ['text' => 'A way to export database records to CSV',                             'is_correct' => false],
                    ['text' => 'A read-only snapshot of the database',                               'is_correct' => false],
                    ['text' => 'A queue of pending SQL statements',                                   'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between optimistic and pessimistic locking?',
                'explanation' => 'Optimistic locking checks for conflicts at commit time; pessimistic locking acquires locks upfront to prevent concurrent modification.',
                'options'     => [
                    ['text' => 'Optimistic checks conflicts on commit; pessimistic locks upfront', 'is_correct' => true],
                    ['text' => 'Pessimistic is faster than optimistic locking',                   'is_correct' => false],
                    ['text' => 'They are two names for the same mechanism',                       'is_correct' => false],
                    ['text' => 'Optimistic locking is only for read operations',                 'is_correct' => false],
                ],
            ],

            // ─── Advanced HTTP & API ─────────────────────────────────────────
            [
                'question'    => 'What does idempotency mean for HTTP methods?',
                'explanation' => 'An idempotent method produces the same result regardless of how many times it is called (GET, PUT, DELETE are idempotent; POST is not).',
                'options'     => [
                    ['text' => 'Calling the same request multiple times produces the same result', 'is_correct' => true],
                    ['text' => 'The request can only be sent once',                               'is_correct' => false],
                    ['text' => 'The response is always cached',                                  'is_correct' => false],
                    ['text' => 'The method does not modify server state',                        'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What HTTP status code indicates a resource was created?',
                'explanation' => '201 Created is the correct response when a new resource has been successfully created.',
                'options'     => [
                    ['text' => '201',  'is_correct' => true],
                    ['text' => '200',  'is_correct' => false],
                    ['text' => '204',  'is_correct' => false],
                    ['text' => '202',  'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What is API rate limiting used for?',
                'explanation' => 'Rate limiting controls the number of requests a client can make in a given time window to prevent abuse and ensure fair usage.',
                'options'     => [
                    ['text' => 'Controlling request volume to prevent abuse',          'is_correct' => true],
                    ['text' => 'Sorting API responses by speed',                      'is_correct' => false],
                    ['text' => 'Encrypting API responses',                           'is_correct' => false],
                    ['text' => 'Caching repeated API responses',                     'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of an API Gateway?',
                'explanation' => 'An API Gateway acts as the single entry point for clients, routing requests to microservices and handling cross-cutting concerns like auth, rate limiting, and logging.',
                'options'     => [
                    ['text' => 'Single entry point routing to microservices with cross-cutting concerns', 'is_correct' => true],
                    ['text' => 'A PHP library for building REST APIs',                                   'is_correct' => false],
                    ['text' => 'A database proxy that caches queries',                                   'is_correct' => false],
                    ['text' => 'A tool for generating API documentation',                               'is_correct' => false],
                ],
            ],

            // ─── Advanced PHP Runtime & Internals ───────────────────────────
            [
                'question'    => 'What is PHP-FPM and when is it used?',
                'explanation' => 'PHP-FPM (FastCGI Process Manager) manages a pool of PHP worker processes, used with web servers like Nginx.',
                'options'     => [
                    ['text' => 'FastCGI Process Manager — manages PHP worker processes for web servers', 'is_correct' => true],
                    ['text' => 'A PHP debugging extension',                                              'is_correct' => false],
                    ['text' => 'A PHP package manager alternative to Composer',                         'is_correct' => false],
                    ['text' => 'A tool for running PHP in a container',                                 'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of `ob_start()` in PHP?',
                'explanation' => '`ob_start()` enables output buffering so that all output is captured to a buffer instead of sent to the browser.',
                'options'     => [
                    ['text' => 'Starts output buffering to capture output in a buffer', 'is_correct' => true],
                    ['text' => 'Opens a database connection buffer',                   'is_correct' => false],
                    ['text' => 'Starts a background job queue',                       'is_correct' => false],
                    ['text' => 'Enables gzip compression of output',                 'is_correct' => false],
                ],
            ],
            [
                'question'    => 'In PHP, what is the garbage collector responsible for?',
                'explanation' => 'PHP\'s garbage collector frees memory by detecting and collecting circular references that the reference counter cannot handle.',
                'options'     => [
                    ['text' => 'Collecting circular references that cannot be freed by reference counting', 'is_correct' => true],
                    ['text' => 'Deleting temporary files created by the script',                           'is_correct' => false],
                    ['text' => 'Cleaning unused database connections',                                     'is_correct' => false],
                    ['text' => 'Removing cached query results from memory',                               'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What does `spl_autoload_register()` do?',
                'explanation' => '`spl_autoload_register()` registers a function to be called automatically when an undefined class is used.',
                'options'     => [
                    ['text' => 'Registers an autoloader function for undefined classes', 'is_correct' => true],
                    ['text' => 'Loads all SPL classes at startup',                      'is_correct' => false],
                    ['text' => 'Registers a shutdown function',                         'is_correct' => false],
                    ['text' => 'Caches class definitions for faster loading',           'is_correct' => false],
                ],
            ],

            // ─── Advanced Closures & FP ──────────────────────────────────────
            [
                'question'    => 'What does `Closure::bind()` do?',
                'explanation' => '`Closure::bind()` creates a new closure bound to a specific object and class scope, giving access to private properties.',
                'options'     => [
                    ['text' => 'Creates a closure bound to a specific object and class scope', 'is_correct' => true],
                    ['text' => 'Calls the closure immediately with given arguments',          'is_correct' => false],
                    ['text' => 'Converts a closure to a named function',                      'is_correct' => false],
                    ['text' => 'Prevents the closure from capturing outer variables',         'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What is currying in functional PHP?',
                'explanation' => 'Currying transforms a function with multiple arguments into a sequence of single-argument functions.',
                'options'     => [
                    ['text' => 'Transforming a multi-argument function into a chain of single-argument functions', 'is_correct' => true],
                    ['text' => 'Caching the return value of a pure function',                                     'is_correct' => false],
                    ['text' => 'Composing two closures into one',                                                 'is_correct' => false],
                    ['text' => 'Binding a closure to a static context',                                          'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What is memoization?',
                'explanation' => 'Memoization caches the result of a pure function for given arguments so repeated calls skip recomputation.',
                'options'     => [
                    ['text' => 'Caching function results to avoid redundant computation',    'is_correct' => true],
                    ['text' => 'Storing function source code in memory',                    'is_correct' => false],
                    ['text' => 'Converting recursive functions to iterative ones',          'is_correct' => false],
                    ['text' => 'Logging function call arguments for debugging',            'is_correct' => false],
                ],
            ],

            // ─── Microservices & Architecture ────────────────────────────────
            [
                'question'    => 'What is the Strangler Fig pattern in software migration?',
                'explanation' => 'The Strangler Fig pattern gradually replaces legacy system parts with new functionality until the legacy system is fully replaced.',
                'options'     => [
                    ['text' => 'Incrementally replacing a legacy system with new components', 'is_correct' => true],
                    ['text' => 'Killing all legacy code at once and rewriting from scratch',  'is_correct' => false],
                    ['text' => 'Wrapping legacy code in an adapter to hide complexity',      'is_correct' => false],
                    ['text' => 'Importing legacy database records into a new schema',        'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What is a Circuit Breaker pattern?',
                'explanation' => 'A Circuit Breaker prevents cascading failures by stopping calls to a failing service and returning fallback responses.',
                'options'     => [
                    ['text' => 'Stops calls to a failing service to prevent cascading failures', 'is_correct' => true],
                    ['text' => 'Breaks infinite loops in recursive functions',                   'is_correct' => false],
                    ['text' => 'Disconnects idle database connections',                         'is_correct' => false],
                    ['text' => 'Terminates long-running HTTP requests',                         'is_correct' => false],
                ],
            ],

            // ─── PHP Attributes (Annotations) ────────────────────────────────
            [
                'question'    => 'What are PHP 8.0 Attributes used for?',
                'explanation' => 'Attributes provide structured metadata on classes, methods, properties, and parameters, readable at runtime via Reflection.',
                'options'     => [
                    ['text' => 'Adding structured metadata readable via Reflection API', 'is_correct' => true],
                    ['text' => 'Adding HTML attributes to PHP-generated output',        'is_correct' => false],
                    ['text' => 'Marking methods as deprecated',                        'is_correct' => false],
                    ['text' => 'Setting default property values',                      'is_correct' => false],
                ],
            ],
            [
                'question'    => 'How do you declare a PHP 8.0 Attribute class?',
                'explanation' => 'An Attribute class is annotated with `#[Attribute]` above its class declaration.',
                'options'     => [
                    ['text' => '#[Attribute] above the class declaration',   'is_correct' => true],
                    ['text' => 'implements AttributeInterface',              'is_correct' => false],
                    ['text' => '@Attribute docblock annotation',             'is_correct' => false],
                    ['text' => 'extends BaseAttribute',                      'is_correct' => false],
                ],
            ],

            // ─── Docker & Dev Environment ────────────────────────────────────
            [
                'question'    => 'Why should you NOT run PHP as root inside a Docker container?',
                'explanation' => 'Running as root in a container is a security risk — if the container is compromised, the attacker gains root-level access to the host.',
                'options'     => [
                    ['text' => 'A compromised container would give root-level host access',  'is_correct' => true],
                    ['text' => 'PHP does not support root execution',                       'is_correct' => false],
                    ['text' => 'OPcache does not work as root',                            'is_correct' => false],
                    ['text' => 'Composer refuses to run as root in Docker',               'is_correct' => false],
                ],
            ],

            // ─── REST API Design ─────────────────────────────────────────────
            [
                'question'    => 'In REST, what HTTP method is used to partially update a resource?',
                'explanation' => 'PATCH is used for partial updates; PUT replaces the entire resource.',
                'options'     => [
                    ['text' => 'PATCH',   'is_correct' => true],
                    ['text' => 'PUT',     'is_correct' => false],
                    ['text' => 'POST',    'is_correct' => false],
                    ['text' => 'UPDATE',  'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What is API versioning and why is it important?',
                'explanation' => 'API versioning (e.g., /v1/, /v2/) allows breaking changes to be introduced without disrupting existing clients.',
                'options'     => [
                    ['text' => 'Allows breaking changes without disrupting existing clients', 'is_correct' => true],
                    ['text' => 'Encrypts the API response for each version',                'is_correct' => false],
                    ['text' => 'Restricts API access to specific user roles',               'is_correct' => false],
                    ['text' => 'Logs API call history by version',                         'is_correct' => false],
                ],
            ],

            // ─── Advanced Error Handling ─────────────────────────────────────
            [
                'question'    => 'What is the difference between Exceptions and Errors in PHP 7+?',
                'explanation' => 'Both implement Throwable; Errors represent internal PHP failures (TypeError, ParseError); Exceptions represent application-level failures.',
                'options'     => [
                    ['text' => 'Both implement Throwable; Errors are engine-level; Exceptions are application-level', 'is_correct' => true],
                    ['text' => 'Errors are catchable; Exceptions are not',                                          'is_correct' => false],
                    ['text' => 'They are identical since PHP 7',                                                    'is_correct' => false],
                    ['text' => 'Exceptions extend Error in PHP 7+',                                                 'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of `set_exception_handler()` in PHP?',
                'explanation' => '`set_exception_handler()` registers a global handler that is called for uncaught exceptions, allowing graceful error responses.',
                'options'     => [
                    ['text' => 'Registers a global handler for uncaught exceptions',      'is_correct' => true],
                    ['text' => 'Converts exceptions to error codes automatically',        'is_correct' => false],
                    ['text' => 'Prevents exceptions from being thrown',                  'is_correct' => false],
                    ['text' => 'Logs exceptions to a database table',                    'is_correct' => false],
                ],
            ],

            // ─── Cache & Redis ───────────────────────────────────────────────
            [
                'question'    => 'What is cache stampede (thundering herd) and how is it mitigated?',
                'explanation' => 'Cache stampede happens when many requests hit the database simultaneously after a cache miss. Mitigations include locking, probabilistic early expiry, or staggered TTLs.',
                'options'     => [
                    ['text' => 'Many requests hit the DB simultaneously after cache miss; mitigate with locks or early recomputation', 'is_correct' => true],
                    ['text' => 'A cache that grows too large and consumes all memory',                                                 'is_correct' => false],
                    ['text' => 'Multiple cache servers writing conflicting values',                                                    'is_correct' => false],
                    ['text' => 'A PHP bug that empties the OPcache unexpectedly',                                                     'is_correct' => false],
                ],
            ],
            [
                'question'    => 'What does Redis `SETNX` do?',
                'explanation' => '`SETNX` (Set if Not eXists) sets a key only if it does not already exist — useful for distributed locks.',
                'options'     => [
                    ['text' => 'Sets a key only if it does not already exist',         'is_correct' => true],
                    ['text' => 'Sets a key with a next expiry time',                  'is_correct' => false],
                    ['text' => 'Deletes a key if it has no value',                   'is_correct' => false],
                    ['text' => 'Sets a key and returns the next available slot',      'is_correct' => false],
                ],
            ],

        ];

        Question::where('topic_id', $topic->id)->delete();

        foreach ($questions as $qData) {
            $question = Question::create([
                'topic_id'    => $topic->id,
                'question'    => $qData['question'],
                'type'        => 'MCQ',
                'difficulty'  => 'Hard',
                'explanation' => $qData['explanation'],
            ]);

            QuestionOption::insert(array_map(fn ($opt) => [
                'question_id' => $question->id,
                'option_text' => $opt['text'],
                'is_correct'  => $opt['is_correct'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ], $qData['options']));
        }

        $count = Question::where('topic_id', $topic->id)->count();
        $this->command->info("PHP Advanced: {$count} questions total.");
    }
}
