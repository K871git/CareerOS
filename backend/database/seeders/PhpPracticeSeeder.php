<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PhpPracticeSeeder extends Seeder
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

        $levels = [
            [
                'title'         => 'PHP Basics — Junior',
                'slug'          => 'php-basics-junior',
                'description'   => 'Core PHP fundamentals: variables, control flow, arrays, functions, and string manipulation. Perfect for interview preparation at the junior level.',
                'display_order' => 1,
                'difficulty'    => 'Easy',
                'questions'     => $this->juniorQuestions(),
            ],
            [
                'title'         => 'PHP Intermediate',
                'slug'          => 'php-intermediate',
                'description'   => 'Object-oriented PHP, error handling, sessions, database interaction with PDO, and namespaces. For developers targeting mid-level roles.',
                'display_order' => 2,
                'difficulty'    => 'Medium',
                'questions'     => $this->intermediateQuestions(),
            ],
            [
                'title'         => 'PHP Advanced',
                'slug'          => 'php-advanced',
                'description'   => 'Design patterns, performance, security, testing, and modern PHP ecosystem. Essential for senior and experienced developer interviews.',
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

            Question::where('topic_id', $topic->id)->delete();

            foreach ($levelData['questions'] as $qData) {
                $question = Question::create([
                    'topic_id'    => $topic->id,
                    'type'        => 'MCQ',
                    'difficulty'  => $levelData['difficulty'],
                    'question'    => $qData['question'],
                    'explanation' => $qData['explanation'],
                ]);

                QuestionOption::insert(array_map(fn ($opt) => [
                    'question_id' => $question->id,
                    'option_text' => $opt['text'],
                    'is_correct'  => $opt['correct'],
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ], $qData['options']));
            }
        }

        $this->command->info('PHP Practice seeded: 1 track, 1 subject, 3 topics, 30 questions.');
    }

    private function juniorQuestions(): array
    {
        return [
            [
                'question'    => 'Which of the following is the correct way to declare a variable in PHP?',
                'explanation' => 'PHP variables always start with a dollar sign ($) followed by the variable name. Variable names are case-sensitive and must start with a letter or underscore.',
                'options'     => [
                    ['text' => '$variableName = "value";', 'correct' => true],
                    ['text' => 'var variableName = "value";', 'correct' => false],
                    ['text' => 'let variableName = "value";', 'correct' => false],
                    ['text' => 'variableName = "value";', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What will the following code output? echo 10 + "5 apples";',
                'explanation' => 'PHP performs type juggling. When a string starting with a number is used in arithmetic, PHP converts it to the numeric portion. "5 apples" becomes 5, so 10 + 5 = 15.',
                'options'     => [
                    ['text' => '15', 'correct' => true],
                    ['text' => '105 apples', 'correct' => false],
                    ['text' => 'Error', 'correct' => false],
                    ['text' => '10', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which function is used to find the length of a string in PHP?',
                'explanation' => 'strlen() returns the number of bytes in a string. For multibyte characters (like UTF-8), mb_strlen() should be used instead.',
                'options'     => [
                    ['text' => 'strlen()', 'correct' => true],
                    ['text' => 'length()', 'correct' => false],
                    ['text' => 'count()', 'correct' => false],
                    ['text' => 'size()', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between == and === in PHP?',
                'explanation' => '== compares values after type coercion (loose comparison), while === compares both value AND type (strict comparison). For example, 0 == false is true, but 0 === false is false.',
                'options'     => [
                    ['text' => '== compares values only; === compares value and type', 'correct' => true],
                    ['text' => 'They are identical — both compare value and type', 'correct' => false],
                    ['text' => '=== compares values only; == compares value and type', 'correct' => false],
                    ['text' => '== is used for strings; === is used for numbers', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which PHP function is used to include and execute a file, and produces a fatal error if the file is not found?',
                'explanation' => 'require() includes and evaluates a file, producing a fatal error (E_COMPILE_ERROR) and halting execution if the file is missing. include() only produces a warning and continues execution.',
                'options'     => [
                    ['text' => 'require()', 'correct' => true],
                    ['text' => 'include()', 'correct' => false],
                    ['text' => 'import()', 'correct' => false],
                    ['text' => 'load()', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you create an associative array in PHP?',
                'explanation' => 'Associative arrays use string keys instead of numeric indices. They are created using key => value pairs inside array() or the short [] syntax.',
                'options'     => [
                    ['text' => '$arr = ["name" => "Alice", "age" => 25];', 'correct' => true],
                    ['text' => '$arr = {"name": "Alice", "age": 25};', 'correct' => false],
                    ['text' => '$arr = array("Alice", 25);', 'correct' => false],
                    ['text' => '$arr = ["Alice", 25];', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the foreach loop iterate over in PHP?',
                'explanation' => 'foreach is designed specifically to iterate over arrays and objects. It provides a convenient way to loop through every element without needing an index counter.',
                'options'     => [
                    ['text' => 'Arrays and objects', 'correct' => true],
                    ['text' => 'Only numeric arrays', 'correct' => false],
                    ['text' => 'Only integers', 'correct' => false],
                    ['text' => 'Strings only', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which superglobal is used to collect form data sent via the POST method?',
                'explanation' => '$_POST is a superglobal array that contains data submitted via HTTP POST. $_GET captures URL query string data, and $_REQUEST contains both GET and POST data.',
                'options'     => [
                    ['text' => '$_POST', 'correct' => true],
                    ['text' => '$_GET', 'correct' => false],
                    ['text' => '$_FORM', 'correct' => false],
                    ['text' => '$_INPUT', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the output of: echo count([1, 2, 3, [4, 5]]);',
                'explanation' => 'count() on a non-recursive call counts only the top-level elements. The array has 4 elements: 1, 2, 3, and [4, 5] (the sub-array counts as one element). Use count($arr, COUNT_RECURSIVE) for deep counting.',
                'options'     => [
                    ['text' => '4', 'correct' => true],
                    ['text' => '5', 'correct' => false],
                    ['text' => '3', 'correct' => false],
                    ['text' => 'Error', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which of the following correctly defines a function with a default parameter in PHP?',
                'explanation' => 'Default parameter values allow a function to be called without providing that argument. The default must be a constant expression (literal, constant, or array), not a variable.',
                'options'     => [
                    ['text' => 'function greet($name = "Guest") { return "Hello, $name"; }', 'correct' => true],
                    ['text' => 'function greet($name := "Guest") { return "Hello, $name"; }', 'correct' => false],
                    ['text' => 'function greet($name | "Guest") { return "Hello, $name"; }', 'correct' => false],
                    ['text' => 'function greet($name) default "Guest" { return "Hello, $name"; }', 'correct' => false],
                ],
            ],
        ];
    }

    private function intermediateQuestions(): array
    {
        return [
            [
                'question'    => 'In PHP OOP, what does the "extends" keyword do?',
                'explanation' => '"extends" creates a child class that inherits all public and protected properties and methods from the parent class. PHP supports single inheritance only — a class can only extend one parent.',
                'options'     => [
                    ['text' => 'Creates a child class that inherits from a parent class', 'correct' => true],
                    ['text' => 'Imports an external PHP file', 'correct' => false],
                    ['text' => 'Creates an interface implementation', 'correct' => false],
                    ['text' => 'Copies all methods from another class as static methods', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of the "abstract" keyword in PHP?',
                'explanation' => 'Abstract classes cannot be instantiated directly. They may contain abstract methods (declared without a body) that must be implemented by any concrete child class. They serve as blueprints for related classes.',
                'options'     => [
                    ['text' => 'Defines a class or method that must be implemented by subclasses', 'correct' => true],
                    ['text' => 'Marks a class as final so it cannot be extended', 'correct' => false],
                    ['text' => 'Makes all properties private', 'correct' => false],
                    ['text' => 'Prevents a method from being overridden', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a PHP Trait?',
                'explanation' => 'Traits are a mechanism for code reuse in single-inheritance languages like PHP. A trait is similar to a class but is intended to group functionality and can be used (mixed in) by multiple classes using the "use" keyword.',
                'options'     => [
                    ['text' => 'A mechanism for code reuse that can be mixed into multiple classes', 'correct' => true],
                    ['text' => 'A type of interface that requires method implementation', 'correct' => false],
                    ['text' => 'A special type of abstract class', 'correct' => false],
                    ['text' => 'A PHP function that returns typed values', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you catch multiple exception types in a single catch block in PHP 8?',
                'explanation' => 'Since PHP 8, you can catch multiple exception types using the pipe | operator: catch (TypeError | ValueError $e). This avoids duplicating the same error handling code for different exceptions.',
                'options'     => [
                    ['text' => 'catch (TypeError | ValueError $e)', 'correct' => true],
                    ['text' => 'catch (TypeError, ValueError $e)', 'correct' => false],
                    ['text' => 'catch [TypeError, ValueError] as $e', 'correct' => false],
                    ['text' => 'catch (TypeError && ValueError $e)', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What PHP function is used to start a session?',
                'explanation' => 'session_start() creates a new session or resumes an existing one based on a session ID in a GET variable, POST variable, or a cookie. It must be called before any output is sent to the browser.',
                'options'     => [
                    ['text' => 'session_start()', 'correct' => true],
                    ['text' => 'start_session()', 'correct' => false],
                    ['text' => 'init_session()', 'correct' => false],
                    ['text' => '$_SESSION = new Session()', 'correct' => false],
                ],
            ],
            [
                'question'    => 'In PDO, what does the prepare() method do?',
                'explanation' => 'PDO::prepare() prepares an SQL statement for execution and returns a PDOStatement object. It supports named (:name) and positional (?) placeholders, protecting against SQL injection by separating SQL code from data.',
                'options'     => [
                    ['text' => 'Creates a prepared statement with placeholders to prevent SQL injection', 'correct' => true],
                    ['text' => 'Executes the SQL query immediately', 'correct' => false],
                    ['text' => 'Validates the SQL syntax without connecting to the database', 'correct' => false],
                    ['text' => 'Escapes special characters in the SQL string', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the visibility difference between "protected" and "private" in PHP?',
                'explanation' => 'private members are only accessible within the class that declares them. protected members are accessible in the declaring class AND any subclasses (child classes). Neither is accessible from outside the class hierarchy.',
                'options'     => [
                    ['text' => 'protected is accessible in child classes; private is only in the declaring class', 'correct' => true],
                    ['text' => 'private is accessible in child classes; protected is only in the declaring class', 'correct' => false],
                    ['text' => 'Both are identical — accessible only within the class', 'correct' => false],
                    ['text' => 'protected is public to all; private is accessible only to the class', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does PHP\'s array_map() function do?',
                'explanation' => 'array_map() applies a callback function to every element of an array and returns a new array containing the results. The original array is not modified. It is equivalent to a "map" operation in functional programming.',
                'options'     => [
                    ['text' => 'Applies a callback to each element and returns a new array of results', 'correct' => true],
                    ['text' => 'Filters elements from an array based on a condition', 'correct' => false],
                    ['text' => 'Reduces an array to a single value', 'correct' => false],
                    ['text' => 'Merges two arrays together', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a PHP namespace used for?',
                'explanation' => 'Namespaces encapsulate classes, functions, and constants to avoid name conflicts between code from different libraries or parts of an application. They work similarly to directories on a filesystem — the same filename can exist in different directories.',
                'options'     => [
                    ['text' => 'To organize code and avoid class/function name collisions', 'correct' => true],
                    ['text' => 'To define the visibility of a class', 'correct' => false],
                    ['text' => 'To declare the PHP version being used', 'correct' => false],
                    ['text' => 'To import external libraries at runtime', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a closure in PHP?',
                'explanation' => 'A closure is an anonymous function that can be assigned to a variable or passed as an argument. It can capture variables from the enclosing scope using the "use" keyword: function() use ($var) {}. Closures are instances of the Closure class.',
                'options'     => [
                    ['text' => 'An anonymous function that can capture variables from its outer scope', 'correct' => true],
                    ['text' => 'A function that automatically closes database connections', 'correct' => false],
                    ['text' => 'A method that prevents a class from being extended', 'correct' => false],
                    ['text' => 'A destructor method called when an object is destroyed', 'correct' => false],
                ],
            ],
        ];
    }

    private function advancedQuestions(): array
    {
        return [
            [
                'question'    => 'Which design pattern ensures a class has only one instance and provides a global access point to it?',
                'explanation' => 'The Singleton pattern restricts instantiation to one object. It uses a static method (commonly getInstance()) that either creates the instance on first call or returns the existing one. Overused Singletons are considered an anti-pattern because they introduce hidden global state.',
                'options'     => [
                    ['text' => 'Singleton', 'correct' => true],
                    ['text' => 'Factory', 'correct' => false],
                    ['text' => 'Observer', 'correct' => false],
                    ['text' => 'Prototype', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does PSR-4 define in the PHP ecosystem?',
                'explanation' => 'PSR-4 is an autoloading standard that maps fully-qualified class names to file paths. It allows Composer\'s autoloader to find class files without manual require statements. The namespace prefix maps to a base directory, and sub-namespaces map to subdirectories.',
                'options'     => [
                    ['text' => 'An autoloading standard that maps class names to file paths', 'correct' => true],
                    ['text' => 'A coding style standard for PHP files', 'correct' => false],
                    ['text' => 'A standard for writing HTTP message interfaces', 'correct' => false],
                    ['text' => 'A standard for database abstraction layers', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is late static binding in PHP and which keyword enables it?',
                'explanation' => 'Late static binding (LSB) allows a static method to reference the class it was called on rather than the class it was defined in. The "static::" keyword enables LSB, while "self::" always refers to the defining class. This is essential for correct inheritance of static methods.',
                'options'     => [
                    ['text' => 'Referencing the called class in static context using static::', 'correct' => true],
                    ['text' => 'Delaying object creation until first use via self::', 'correct' => false],
                    ['text' => 'A compiler optimization that defers method binding using parent::', 'correct' => false],
                    ['text' => 'Automatically selecting the correct method override at compile time', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the primary purpose of PHP Generators?',
                'explanation' => 'Generators allow writing iterators using a simple function with the yield keyword. They pause execution and return a value on each yield, resuming when the next value is requested. This enables iterating over large datasets without loading everything into memory at once.',
                'options'     => [
                    ['text' => 'To iterate over large datasets without holding all data in memory', 'correct' => true],
                    ['text' => 'To auto-generate PHP boilerplate code', 'correct' => false],
                    ['text' => 'To create random data for testing purposes', 'correct' => false],
                    ['text' => 'To compile PHP to native machine code', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How does PHP prevent SQL injection when using PDO?',
                'explanation' => 'PDO prepared statements separate SQL logic from user data. Placeholders (:name or ?) are used in the query, and values are bound using bindParam() or passed as an array to execute(). The database driver handles escaping, making injection impossible regardless of the input.',
                'options'     => [
                    ['text' => 'Using prepared statements with parameterized placeholders', 'correct' => true],
                    ['text' => 'Using htmlspecialchars() on all user input', 'correct' => false],
                    ['text' => 'Using addslashes() before inserting into queries', 'correct' => false],
                    ['text' => 'Enabling PDO::ATTR_SAFE_MODE in the connection', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the Repository pattern used for in Laravel/PHP applications?',
                'explanation' => 'The Repository pattern abstracts the data layer by providing a clean API for data access. Controllers and services interact with repositories rather than directly with Eloquent or SQL. This makes it easier to swap data sources (e.g., database to API) and enables unit testing with mocked repositories.',
                'options'     => [
                    ['text' => 'To abstract data access logic and decouple it from business logic', 'correct' => true],
                    ['text' => 'To store application configuration in a central location', 'correct' => false],
                    ['text' => 'To manage Git repositories from within PHP', 'correct' => false],
                    ['text' => 'To cache database queries automatically', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does PHP\'s Composer tool do?',
                'explanation' => 'Composer is PHP\'s dependency manager. It reads composer.json to resolve and install packages from Packagist. It also generates an autoloader (vendor/autoload.php) using PSR-4 mappings, so classes from all installed packages are available without manual require statements.',
                'options'     => [
                    ['text' => 'Manages PHP package dependencies and generates an autoloader', 'correct' => true],
                    ['text' => 'Compiles PHP code to bytecode for faster execution', 'correct' => false],
                    ['text' => 'Builds Docker containers for PHP applications', 'correct' => false],
                    ['text' => 'Manages PHP version switching on the server', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between unit tests and integration tests in PHP?',
                'explanation' => 'Unit tests test a single class or function in isolation, with all dependencies mocked. Integration tests test how multiple components work together (e.g., a controller hitting a real database). PHPUnit supports both; Laravel adds HTTP-level integration testing via TestCase.',
                'options'     => [
                    ['text' => 'Unit tests isolate a single unit with mocks; integration tests test component interaction', 'correct' => true],
                    ['text' => 'Unit tests use a browser; integration tests use the command line', 'correct' => false],
                    ['text' => 'They are the same — both test classes in isolation', 'correct' => false],
                    ['text' => 'Integration tests only test third-party API connections', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is XSS and how does PHP protect against it?',
                'explanation' => 'Cross-Site Scripting (XSS) occurs when malicious scripts are injected into web pages viewed by other users. PHP protects against it using htmlspecialchars() or htmlentities() to escape output, converting < > " & into their HTML entities so the browser renders them as text, not code.',
                'options'     => [
                    ['text' => 'Injecting malicious scripts into pages; prevented by escaping output with htmlspecialchars()', 'correct' => true],
                    ['text' => 'Database injection; prevented using prepared statements', 'correct' => false],
                    ['text' => 'Cross-server file access; prevented using open_basedir in php.ini', 'correct' => false],
                    ['text' => 'Brute-force login attacks; prevented using rate limiting', 'correct' => false],
                ],
            ],
            [
                'question'    => 'In PHP 8, what is the "match" expression and how does it differ from switch?',
                'explanation' => 'The match expression (PHP 8.0+) is like switch but uses strict comparison (===), returns a value, does not fall through between arms, and throws an UnhandledMatchError if no arm matches. switch uses loose comparison (==), falls through unless "break" is used, and cannot return a value directly.',
                'options'     => [
                    ['text' => 'match uses strict comparison, returns a value, and has no fall-through', 'correct' => true],
                    ['text' => 'match is identical to switch but with cleaner syntax only', 'correct' => false],
                    ['text' => 'match only works with integers; switch works with any type', 'correct' => false],
                    ['text' => 'match requires explicit break statements like switch does', 'correct' => false],
                ],
            ],
        ];
    }
}
