<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Topic;
use Illuminate\Database\Seeder;

class PhpIntermediateQuestionsSeeder extends Seeder
{
    public function run(): void
    {
        $topic = Topic::where('slug', 'php-intermediate')->firstOrFail();

        Question::where('topic_id', $topic->id)->delete();

        foreach ($this->questions() as $qData) {
            $q = Question::create([
                'topic_id'    => $topic->id,
                'type'        => 'MCQ',
                'difficulty'  => 'Medium',
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
        $this->command->info("PHP Intermediate: {$count} questions total.");
    }

    private function questions(): array
    {
        return [
            // ── OOP — Classes & Objects ──────────────────────────────────
            [
                'question'    => 'What is the purpose of the __construct() method in a PHP class?',
                'explanation' => '__construct() is a magic method called automatically when a new instance of a class is created with "new". It is used to initialize object properties and dependencies.',
                'options'     => [
                    ['text' => 'It runs automatically when an object is instantiated', 'correct' => true],
                    ['text' => 'It defines the class structure', 'correct' => false],
                    ['text' => 'It is called when the object is destroyed', 'correct' => false],
                    ['text' => 'It is required in every class', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the "new" keyword do in PHP?',
                'explanation' => 'The "new" keyword creates a new instance of a class, calling its __construct() method if defined. The result is an object that holds the class\'s properties and methods.',
                'options'     => [
                    ['text' => 'Creates a new object instance of a class', 'correct' => true],
                    ['text' => 'Declares a new class', 'correct' => false],
                    ['text' => 'Creates a new array', 'correct' => false],
                    ['text' => 'Allocates memory without instantiation', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you access a property of an object in PHP?',
                'explanation' => 'The -> (object operator) accesses properties and methods of an object. $obj->propertyName reads the property; $obj->methodName() calls the method.',
                'options'     => [
                    ['text' => '$obj->property', 'correct' => true],
                    ['text' => '$obj.property', 'correct' => false],
                    ['text' => '$obj::property', 'correct' => false],
                    ['text' => '$obj[property]', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the "final" keyword do when applied to a class?',
                'explanation' => 'A final class cannot be extended — no other class can inherit from it. A final method cannot be overridden in subclasses. This is useful to prevent unintended inheritance.',
                'options'     => [
                    ['text' => 'Prevents the class from being extended', 'correct' => true],
                    ['text' => 'Makes all class properties read-only', 'correct' => false],
                    ['text' => 'Prevents the class from being instantiated', 'correct' => false],
                    ['text' => 'Marks the class as deprecated', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the $this keyword in PHP?',
                'explanation' => '$this is a reference to the current object instance. Inside a non-static method, $this refers to the object the method is called on, allowing access to its properties and methods.',
                'options'     => [
                    ['text' => 'A reference to the current object instance', 'correct' => true],
                    ['text' => 'A reference to the parent class', 'correct' => false],
                    ['text' => 'A reference to the current class name', 'correct' => false],
                    ['text' => 'A copy of the current object', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the self:: keyword used for in PHP?',
                'explanation' => 'self:: refers to the class in which it is defined (at compile time). It is used to access static properties, static methods, and constants of the current class without instantiating it.',
                'options'     => [
                    ['text' => 'Refers to the class in which the code is written (compile-time)', 'correct' => true],
                    ['text' => 'Refers to the calling object like $this', 'correct' => false],
                    ['text' => 'Refers to the parent class', 'correct' => false],
                    ['text' => 'Refers to the currently called class (runtime)', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you call a static method of a class in PHP?',
                'explanation' => 'Static methods are called using :: (scope resolution operator) on the class name: ClassName::methodName(). They can also be called on an instance, but that is discouraged.',
                'options'     => [
                    ['text' => 'ClassName::methodName()', 'correct' => true],
                    ['text' => 'ClassName->methodName()', 'correct' => false],
                    ['text' => 'ClassName.methodName()', 'correct' => false],
                    ['text' => 'static::ClassName.methodName()', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a class constant in PHP?',
                'explanation' => 'A class constant is declared with the "const" keyword inside a class. It belongs to the class, not to any instance, and is accessed via ClassName::CONSTANT or self::CONSTANT. It cannot be changed once defined.',
                'options'     => [
                    ['text' => 'A value defined with const inside a class, shared by all instances', 'correct' => true],
                    ['text' => 'A static property that cannot be changed', 'correct' => false],
                    ['text' => 'A variable initialized in __construct that never changes', 'correct' => false],
                    ['text' => 'A global constant visible only inside the class', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is __destruct() used for in PHP?',
                'explanation' => '__destruct() is called automatically when an object is destroyed (no more references or script ends). It is used for cleanup — closing database connections, releasing file handles, etc.',
                'options'     => [
                    ['text' => 'Clean-up code run when the object is garbage-collected', 'correct' => true],
                    ['text' => 'Remove the class definition from memory', 'correct' => false],
                    ['text' => 'Throw an error when the object fails to construct', 'correct' => false],
                    ['text' => 'Log when an object is created', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is object cloning in PHP and how is it done?',
                'explanation' => 'Object cloning creates a shallow copy of an object using the "clone" keyword: $copy = clone $obj. Properties are copied, but if properties are objects, only the reference is copied (not a deep copy). Define __clone() to customize clone behavior.',
                'options'     => [
                    ['text' => 'Creating a shallow copy of an object with the clone keyword', 'correct' => true],
                    ['text' => 'Extending a class to inherit its properties', 'correct' => false],
                    ['text' => 'Serializing an object to copy it', 'correct' => false],
                    ['text' => 'Assigning an object variable to another variable', 'correct' => false],
                ],
            ],

            // ── Inheritance ─────────────────────────────────────────────
            [
                'question'    => 'How do you call the parent class constructor in a child class?',
                'explanation' => 'Use parent::__construct() inside the child class constructor to call the parent\'s constructor. This is necessary when the parent constructor initializes state the child also needs.',
                'options'     => [
                    ['text' => 'parent::__construct()', 'correct' => true],
                    ['text' => 'super()', 'correct' => false],
                    ['text' => 'this->parent()', 'correct' => false],
                    ['text' => 'extends::__construct()', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Can a PHP class extend multiple classes?',
                'explanation' => 'PHP does not support multiple inheritance. A class can only extend one parent class. However, a class can implement multiple interfaces and use multiple traits to achieve similar code-reuse patterns.',
                'options'     => [
                    ['text' => 'No — PHP supports only single inheritance', 'correct' => true],
                    ['text' => 'Yes — use "extends ClassA, ClassB"', 'correct' => false],
                    ['text' => 'Yes — but only for abstract classes', 'correct' => false],
                    ['text' => 'Yes — if both parents are interfaces', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is method overriding in PHP?',
                'explanation' => 'Method overriding is when a child class defines a method with the same name as a method in its parent class. The child\'s version replaces the parent\'s for instances of the child class. Call parent::methodName() to retain parent behavior.',
                'options'     => [
                    ['text' => 'A child class redefines a method from its parent class', 'correct' => true],
                    ['text' => 'A class defines multiple methods with the same name but different parameters', 'correct' => false],
                    ['text' => 'Changing a method\'s return type in a subclass', 'correct' => false],
                    ['text' => 'Preventing a parent method from being called', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is instanceof used for in PHP?',
                'explanation' => 'instanceof checks whether an object is an instance of a specific class or implements a specific interface. It returns true or false. Useful for type-checking before calling class-specific methods.',
                'options'     => [
                    ['text' => 'Checks if an object is an instance of a specific class or interface', 'correct' => true],
                    ['text' => 'Creates a new instance of a class', 'correct' => false],
                    ['text' => 'Checks if a class has been instantiated at least once', 'correct' => false],
                    ['text' => 'Returns the class name of an object', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does get_class($obj) return?',
                'explanation' => 'get_class() returns the class name of the given object as a string. get_parent_class() returns the parent class name. These are useful for debugging and reflection.',
                'options'     => [
                    ['text' => 'The class name of the object as a string', 'correct' => true],
                    ['text' => 'The object\'s type as an integer', 'correct' => false],
                    ['text' => 'The parent class name', 'correct' => false],
                    ['text' => 'The object\'s memory address', 'correct' => false],
                ],
            ],

            // ── Interfaces ──────────────────────────────────────────────
            [
                'question'    => 'What is the difference between an interface and an abstract class in PHP?',
                'explanation' => 'An interface can only define method signatures (no implementation in PHP < 8.0) and constants. A class can implement multiple interfaces. An abstract class can have concrete methods, properties, and constructors. A class can only extend one abstract class.',
                'options'     => [
                    ['text' => 'A class can implement multiple interfaces but extend only one abstract class', 'correct' => true],
                    ['text' => 'Abstract classes can only have abstract methods; interfaces can have concrete methods', 'correct' => false],
                    ['text' => 'Interfaces support constructors; abstract classes do not', 'correct' => false],
                    ['text' => 'They are identical; both prevent instantiation', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What keyword does a class use to implement an interface?',
                'explanation' => 'A class uses the "implements" keyword to declare it implements an interface. It must then define all the methods declared in that interface, or be declared abstract.',
                'options'     => [
                    ['text' => 'implements', 'correct' => true],
                    ['text' => 'extends', 'correct' => false],
                    ['text' => 'uses', 'correct' => false],
                    ['text' => 'inherits', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Can an interface have properties in PHP?',
                'explanation' => 'PHP interfaces cannot have properties (prior to PHP 8.4). They can only have method signatures and constants. Classes implementing the interface are responsible for defining any properties.',
                'options'     => [
                    ['text' => 'No — interfaces can only have method signatures and constants', 'correct' => true],
                    ['text' => 'Yes — but only static properties', 'correct' => false],
                    ['text' => 'Yes — any property type', 'correct' => false],
                    ['text' => 'Yes — but only public properties', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Can an interface extend another interface in PHP?',
                'explanation' => 'Yes, an interface can extend one or more other interfaces using the extends keyword. A class implementing the child interface must implement all methods from both the child and parent interfaces.',
                'options'     => [
                    ['text' => 'Yes — an interface can extend one or more interfaces', 'correct' => true],
                    ['text' => 'No — interfaces cannot extend other interfaces', 'correct' => false],
                    ['text' => 'Yes — but only one parent interface', 'correct' => false],
                    ['text' => 'Yes — using the implements keyword', 'correct' => false],
                ],
            ],

            // ── Traits ──────────────────────────────────────────────────
            [
                'question'    => 'How do you use a trait inside a class?',
                'explanation' => 'Traits are included in a class using the "use" keyword inside the class body. The trait\'s methods and properties become part of the class.',
                'options'     => [
                    ['text' => 'class MyClass { use MyTrait; }', 'correct' => true],
                    ['text' => 'class MyClass extends MyTrait { }', 'correct' => false],
                    ['text' => 'class MyClass implements MyTrait { }', 'correct' => false],
                    ['text' => 'include MyTrait; class MyClass { }', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What happens when two traits used in the same class have a method with the same name?',
                'explanation' => 'A conflict occurs. PHP requires explicit resolution using the "insteadof" operator to choose which trait\'s method to use, and optionally "as" to create an alias for the other.',
                'options'     => [
                    ['text' => 'A conflict that must be resolved using insteadof or as', 'correct' => true],
                    ['text' => 'The last trait wins automatically', 'correct' => false],
                    ['text' => 'Both methods are available with trait-prefixed names', 'correct' => false],
                    ['text' => 'A fatal error occurs immediately', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Can a trait have a constructor in PHP?',
                'explanation' => 'Yes, traits can have a __construct method. However, if the using class also has a __construct, a conflict resolution is required. It is generally considered bad practice for traits to have constructors.',
                'options'     => [
                    ['text' => 'Yes, but it may conflict with the class constructor', 'correct' => true],
                    ['text' => 'No — traits cannot define constructors', 'correct' => false],
                    ['text' => 'Yes — and it always overrides the class constructor', 'correct' => false],
                    ['text' => 'Yes — and it is always called before the class constructor', 'correct' => false],
                ],
            ],

            // ── Error Handling & Exceptions ─────────────────────────────
            [
                'question'    => 'What is the correct syntax for a try-catch-finally block in PHP?',
                'explanation' => 'try contains code that might throw an exception; catch handles specific exception types; finally always runs regardless of whether an exception was thrown or caught.',
                'options'     => [
                    ['text' => 'try { } catch (ExceptionType $e) { } finally { }', 'correct' => true],
                    ['text' => 'try { } except (ExceptionType $e) { }', 'correct' => false],
                    ['text' => 'try { } handle (ExceptionType $e) { } always { }', 'correct' => false],
                    ['text' => 'catch { } try { } finally { }', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you throw an exception in PHP?',
                'explanation' => 'The throw keyword is used to throw an exception. throw new RuntimeException("message") creates and throws a RuntimeException. The exception must extend the built-in Exception or Error class.',
                'options'     => [
                    ['text' => 'throw new ExceptionType("message")', 'correct' => true],
                    ['text' => 'raise new ExceptionType("message")', 'correct' => false],
                    ['text' => 'error new ExceptionType("message")', 'correct' => false],
                    ['text' => 'exception ExceptionType("message")', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between Exception and Error in PHP?',
                'explanation' => 'Exception is the base class for user-space exceptions (thrown with throw). Error is the base class for internal PHP errors (e.g., TypeError, ParseError, ArithmeticError). Both implement the Throwable interface. Catching \\Throwable catches both.',
                'options'     => [
                    ['text' => 'Exception is for user-thrown errors; Error is for internal PHP errors', 'correct' => true],
                    ['text' => 'They are identical — both catch any error', 'correct' => false],
                    ['text' => 'Error is for user-thrown errors; Exception is for PHP internals', 'correct' => false],
                    ['text' => 'Exception is PHP 7+; Error is the legacy class', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What happens if an exception is thrown but not caught?',
                'explanation' => 'If an exception is not caught, PHP calls the uncaught exception handler. By default this outputs a fatal error and terminates the script. You can register a custom handler with set_exception_handler().',
                'options'     => [
                    ['text' => 'A fatal error is triggered and the script stops', 'correct' => true],
                    ['text' => 'The exception is silently ignored', 'correct' => false],
                    ['text' => 'PHP retries the operation 3 times', 'correct' => false],
                    ['text' => 'The exception is logged to error_log automatically', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you create a custom exception class in PHP?',
                'explanation' => 'Custom exceptions are created by extending the built-in Exception class (or any of its subclasses). The child class inherits all Exception methods like getMessage(), getCode(), and getTrace().',
                'options'     => [
                    ['text' => 'class MyException extends Exception { }', 'correct' => true],
                    ['text' => 'class MyException implements Exception { }', 'correct' => false],
                    ['text' => 'class MyException uses Exception { }', 'correct' => false],
                    ['text' => 'exception MyException extends Exception { }', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does $e->getMessage() return in a catch block?',
                'explanation' => 'getMessage() is a method of the Exception class that returns the exception message string that was passed to the exception constructor. It is the most commonly used method when logging or displaying errors.',
                'options'     => [
                    ['text' => 'The message string passed when the exception was created', 'correct' => true],
                    ['text' => 'The full stack trace as a string', 'correct' => false],
                    ['text' => 'The exception class name', 'correct' => false],
                    ['text' => 'The line number where the exception was thrown', 'correct' => false],
                ],
            ],

            // ── PDO & Database ──────────────────────────────────────────
            [
                'question'    => 'What does PDO stand for?',
                'explanation' => 'PDO stands for PHP Data Objects. It provides a uniform interface to interact with multiple database systems (MySQL, PostgreSQL, SQLite, etc.) using the same methods.',
                'options'     => [
                    ['text' => 'PHP Data Objects', 'correct' => true],
                    ['text' => 'PHP Database Operations', 'correct' => false],
                    ['text' => 'PHP Data Operator', 'correct' => false],
                    ['text' => 'PHP Direct Output', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you create a PDO connection to a MySQL database?',
                'explanation' => 'PDO connections are created by instantiating the PDO class with a DSN string, username, and password. The DSN format for MySQL is: "mysql:host=hostname;dbname=database;charset=utf8mb4".',
                'options'     => [
                    ['text' => 'new PDO("mysql:host=localhost;dbname=db", $user, $pass)', 'correct' => true],
                    ['text' => 'PDO::connect("mysql://localhost/db", $user, $pass)', 'correct' => false],
                    ['text' => 'pdo_connect("mysql", "localhost", "db", $user, $pass)', 'correct' => false],
                    ['text' => 'DB::connect("mysql:localhost", $user, $pass)', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between PDO::query() and PDO::exec()?',
                'explanation' => 'PDO::query() executes a SQL query and returns a PDOStatement object for fetching results — use for SELECT. PDO::exec() executes a SQL statement and returns the number of rows affected — use for INSERT, UPDATE, DELETE.',
                'options'     => [
                    ['text' => 'query() returns a result set; exec() returns affected row count', 'correct' => true],
                    ['text' => 'They are identical', 'correct' => false],
                    ['text' => 'exec() returns a result set; query() returns affected row count', 'correct' => false],
                    ['text' => 'query() is for prepared statements; exec() is for raw SQL', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does PDOStatement::fetchAll(PDO::FETCH_ASSOC) return?',
                'explanation' => 'fetchAll(PDO::FETCH_ASSOC) returns all rows as an array of associative arrays where the column names are the keys. PDO::FETCH_OBJ returns objects; PDO::FETCH_NUM returns numeric-indexed arrays.',
                'options'     => [
                    ['text' => 'All rows as an array of associative arrays keyed by column name', 'correct' => true],
                    ['text' => 'All rows as a single flat array', 'correct' => false],
                    ['text' => 'All rows as an array of objects', 'correct' => false],
                    ['text' => 'The first row only as an associative array', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you get the last inserted row\'s ID using PDO?',
                'explanation' => 'PDO::lastInsertId() returns the ID generated by the last INSERT query. It uses the auto_increment value from the database. This method is called on the PDO connection object, not the statement.',
                'options'     => [
                    ['text' => '$pdo->lastInsertId()', 'correct' => true],
                    ['text' => '$stmt->getLastId()', 'correct' => false],
                    ['text' => 'PDO::getInsertId($pdo)', 'correct' => false],
                    ['text' => '$pdo->insertId()', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are PDO transactions used for?',
                'explanation' => 'PDO transactions group multiple SQL statements into an atomic unit. beginTransaction() starts a transaction; commit() makes changes permanent; rollBack() undoes all changes. This ensures data integrity when multiple related queries must all succeed.',
                'options'     => [
                    ['text' => 'Grouping SQL operations so all succeed or all are rolled back', 'correct' => true],
                    ['text' => 'Caching query results for performance', 'correct' => false],
                    ['text' => 'Connecting to multiple databases simultaneously', 'correct' => false],
                    ['text' => 'Running queries asynchronously', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does PDO::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION) do?',
                'explanation' => 'This sets PDO to throw PDOException exceptions on errors instead of silently failing (ERRMODE_SILENT) or issuing warnings (ERRMODE_WARNING). ERRMODE_EXCEPTION is the recommended setting for production code.',
                'options'     => [
                    ['text' => 'Makes PDO throw exceptions on database errors', 'correct' => true],
                    ['text' => 'Enables query caching', 'correct' => false],
                    ['text' => 'Sets the character encoding of the connection', 'correct' => false],
                    ['text' => 'Enables persistent connections', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does bindParam() do compared to bindValue() in PDO?',
                'explanation' => 'bindParam() binds a parameter to a variable by reference — the variable is read when execute() is called. bindValue() binds a value immediately. For most cases bindValue() is simpler; bindParam() is needed when re-using a statement with different variable values.',
                'options'     => [
                    ['text' => 'bindParam() binds by reference; bindValue() binds the value immediately', 'correct' => true],
                    ['text' => 'bindParam() is for named placeholders; bindValue() is for positional', 'correct' => false],
                    ['text' => 'They are identical', 'correct' => false],
                    ['text' => 'bindParam() is for integers; bindValue() is for strings', 'correct' => false],
                ],
            ],

            // ── Sessions & Cookies ──────────────────────────────────────
            [
                'question'    => 'Where does PHP store session data by default?',
                'explanation' => 'By default, PHP stores session data in temporary files on the server in the directory specified by session.save_path (often /tmp or a system temp directory). Custom session handlers can store data in databases or Redis.',
                'options'     => [
                    ['text' => 'In files on the server (in the session.save_path directory)', 'correct' => true],
                    ['text' => 'In a cookie on the client browser', 'correct' => false],
                    ['text' => 'In the PHP memory pool', 'correct' => false],
                    ['text' => 'In the $_SESSION superglobal permanently', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the session ID and how is it passed to the browser?',
                'explanation' => 'The session ID is a unique identifier for a session, generated by PHP. By default it is passed via a cookie named PHPSESSID. If cookies are disabled, it can be passed as a URL query parameter (session.use_trans_sid).',
                'options'     => [
                    ['text' => 'A unique ID stored in a cookie (PHPSESSID) or URL parameter', 'correct' => true],
                    ['text' => 'The user\'s IP address', 'correct' => false],
                    ['text' => 'An MD5 hash of the username', 'correct' => false],
                    ['text' => 'A token stored in the session file', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you destroy a session completely in PHP?',
                'explanation' => 'To fully destroy a session: (1) call session_start() to open it, (2) unset all session variables with $_SESSION = [], (3) delete the session cookie, (4) call session_destroy(). Skipping any step may leave data behind.',
                'options'     => [
                    ['text' => 'session_unset() to clear data, then session_destroy() to delete the session', 'correct' => true],
                    ['text' => 'unset($_SESSION) and session_close()', 'correct' => false],
                    ['text' => 'session_kill() clears everything in one step', 'correct' => false],
                    ['text' => 'Simply calling unset($_SESSION) is sufficient', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you set a cookie that expires in 1 hour?',
                'explanation' => 'setcookie() sets a cookie. The expiry is a Unix timestamp. time() + 3600 means current time plus 3600 seconds (1 hour). The cookie is sent to the browser as an HTTP header.',
                'options'     => [
                    ['text' => 'setcookie("name", "value", time() + 3600)', 'correct' => true],
                    ['text' => 'setcookie("name", "value", 3600)', 'correct' => false],
                    ['text' => 'cookie("name", "value", expire: "+1 hour")', 'correct' => false],
                    ['text' => 'setCookie(["name", "value", 3600])', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you delete a cookie in PHP?',
                'explanation' => 'To delete a cookie, call setcookie() with the same name but set the expiry to a past time (e.g., time() - 3600). This instructs the browser to remove the cookie.',
                'options'     => [
                    ['text' => 'setcookie("name", "", time() - 3600)', 'correct' => true],
                    ['text' => 'unset($_COOKIE["name"])', 'correct' => false],
                    ['text' => 'deletecookie("name")', 'correct' => false],
                    ['text' => 'cookie_delete("name")', 'correct' => false],
                ],
            ],

            // ── Namespaces ──────────────────────────────────────────────
            [
                'question'    => 'How do you declare a namespace in PHP?',
                'explanation' => 'The namespace keyword declares the namespace. It must be the first statement in the file (except for declare statements). Example: namespace App\\Http\\Controllers;',
                'options'     => [
                    ['text' => 'namespace MyApp\\Controllers; at the top of the file', 'correct' => true],
                    ['text' => 'using namespace MyApp\\Controllers;', 'correct' => false],
                    ['text' => 'import namespace MyApp\\Controllers;', 'correct' => false],
                    ['text' => '#namespace MyApp\\Controllers;', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the "use" keyword do in the context of namespaces?',
                'explanation' => 'The "use" keyword imports a class, function, or constant from another namespace into the current scope. This allows you to reference it by its short name instead of the fully qualified name.',
                'options'     => [
                    ['text' => 'Imports a class from another namespace into the current scope', 'correct' => true],
                    ['text' => 'Creates a new namespace alias', 'correct' => false],
                    ['text' => 'Includes a PHP file', 'correct' => false],
                    ['text' => 'It is only used for traits', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the fully qualified class name (FQCN)?',
                'explanation' => 'The FQCN is the complete name of a class including its namespace, starting with a backslash. For example: \\App\\Http\\Controllers\\UserController. When used from global scope or after a "use" statement, the short name is sufficient.',
                'options'     => [
                    ['text' => 'The complete class name including its full namespace path', 'correct' => true],
                    ['text' => 'The class name defined inside an interface', 'correct' => false],
                    ['text' => 'The class name as returned by get_class()', 'correct' => false],
                    ['text' => 'An alias for a class created with "use ... as"', 'correct' => false],
                ],
            ],

            // ── Closures & Anonymous Functions ──────────────────────────
            [
                'question'    => 'What is an arrow function in PHP 7.4+?',
                'explanation' => 'Arrow functions (fn() =>) are a shorthand for closures. They automatically capture outer scope variables by value (no "use" keyword needed). They must have a single expression as the body (which is the implicit return value).',
                'options'     => [
                    ['text' => 'A short closure that auto-captures outer variables: fn($x) => $x * 2', 'correct' => true],
                    ['text' => 'A function defined using =>  without return', 'correct' => false],
                    ['text' => 'An arrow function from JavaScript ported to PHP', 'correct' => false],
                    ['text' => 'A method that returns $this for chaining', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the "use" keyword do inside a closure?',
                'explanation' => 'Inside a closure, "use" captures variables from the enclosing scope. Without "use", the closure cannot access outer variables. By default, variables are captured by value (a copy). Add & to capture by reference: use (&$var).',
                'options'     => [
                    ['text' => 'Captures variables from the outer scope into the closure', 'correct' => true],
                    ['text' => 'Imports a namespace into the closure', 'correct' => false],
                    ['text' => 'Imports a trait into the closure', 'correct' => false],
                    ['text' => 'Makes the closure static', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does array_filter() do when no callback is provided?',
                'explanation' => 'Without a callback, array_filter() removes all falsy values from the array (false, 0, 0.0, "", "0", null, []). This is a quick way to clean up an array. Note: the original keys are preserved.',
                'options'     => [
                    ['text' => 'Removes all falsy values from the array', 'correct' => true],
                    ['text' => 'Returns false if any element fails a check', 'correct' => false],
                    ['text' => 'Filters the array leaving only unique values', 'correct' => false],
                    ['text' => 'Throws an error — callback is required', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does array_reduce() do?',
                'explanation' => 'array_reduce() iterates over an array and applies a callback function that accumulates a single return value. Example: array_reduce([1,2,3], fn($carry, $item) => $carry + $item, 0) returns 6.',
                'options'     => [
                    ['text' => 'Reduces an array to a single value using a callback', 'correct' => true],
                    ['text' => 'Removes elements from an array based on a condition', 'correct' => false],
                    ['text' => 'Reduces the array to its unique values', 'correct' => false],
                    ['text' => 'Returns the minimum value in the array', 'correct' => false],
                ],
            ],

            // ── Type Declarations ────────────────────────────────────────
            [
                'question'    => 'What does "strict_types=1" do at the top of a PHP file?',
                'explanation' => 'declare(strict_types=1) enables strict type checking for scalar type declarations in that file. Without it, PHP coerces types (e.g., passing "5" where int is expected works). With strict mode, it throws a TypeError instead.',
                'options'     => [
                    ['text' => 'Enables strict type checking — no automatic type coercion for scalars', 'correct' => true],
                    ['text' => 'Disables all type declarations in the file', 'correct' => false],
                    ['text' => 'Enables strict comparison (===) everywhere', 'correct' => false],
                    ['text' => 'Prevents variable redeclaration', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a nullable type in PHP?',
                'explanation' => 'A nullable type prefixed with ? allows a parameter or return type to be either the specified type or null. For example: function greet(?string $name) accepts a string or null. This was introduced in PHP 7.1.',
                'options'     => [
                    ['text' => 'A type that can be the specified type or null, declared with ?', 'correct' => true],
                    ['text' => 'A type that can be any value (like mixed)', 'correct' => false],
                    ['text' => 'A type that can be null only', 'correct' => false],
                    ['text' => 'A type used only for return type declarations', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a union type in PHP 8.0?',
                'explanation' => 'Union types allow a parameter or return to accept multiple types. Syntax: int|string means the value can be either an integer or a string. PHP 8.0 introduced this feature.',
                'options'     => [
                    ['text' => 'A type that allows multiple types separated by |', 'correct' => true],
                    ['text' => 'A type that combines two classes into one', 'correct' => false],
                    ['text' => 'A type that represents a PHP union data structure', 'correct' => false],
                    ['text' => 'An alias for the mixed type', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the "mixed" return type mean in PHP?',
                'explanation' => '"mixed" is a built-in type that means the function can return any type (string, int, null, array, object, etc.). It was introduced in PHP 8.0 and makes a function\'s flexible return types explicit in the signature.',
                'options'     => [
                    ['text' => 'The function can return any type', 'correct' => true],
                    ['text' => 'The function mixes two return types', 'correct' => false],
                    ['text' => 'The return type is undefined', 'correct' => false],
                    ['text' => 'The function returns only scalar types', 'correct' => false],
                ],
            ],

            // ── Regular Expressions ─────────────────────────────────────
            [
                'question'    => 'Which PHP function checks if a string matches a regex pattern?',
                'explanation' => 'preg_match() returns 1 if the pattern matches, 0 if it doesn\'t, and false on error. The second argument is the subject string; an optional third argument captures matched groups.',
                'options'     => [
                    ['text' => 'preg_match($pattern, $string)', 'correct' => true],
                    ['text' => 'regex_match($pattern, $string)', 'correct' => false],
                    ['text' => 'str_match($pattern, $string)', 'correct' => false],
                    ['text' => 'match($string, $pattern)', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does preg_replace() do?',
                'explanation' => 'preg_replace($pattern, $replacement, $subject) replaces all occurrences of the pattern in $subject with $replacement. It returns the modified string. Use preg_replace_callback() for dynamic replacements.',
                'options'     => [
                    ['text' => 'Replaces pattern matches in a string with a replacement', 'correct' => true],
                    ['text' => 'Removes a regex pattern from a string', 'correct' => false],
                    ['text' => 'Returns all matches of a pattern in an array', 'correct' => false],
                    ['text' => 'Escapes special regex characters in a string', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does preg_split() do?',
                'explanation' => 'preg_split() splits a string by a regex pattern and returns an array of substrings. It is like explode() but accepts a regex delimiter for more powerful splitting.',
                'options'     => [
                    ['text' => 'Splits a string by a regular expression pattern', 'correct' => true],
                    ['text' => 'Splits a regex pattern into parts', 'correct' => false],
                    ['text' => 'Returns all captured groups from a regex match', 'correct' => false],
                    ['text' => 'Splits a string into individual characters', 'correct' => false],
                ],
            ],

            // ── File Handling ────────────────────────────────────────────
            [
                'question'    => 'How do you read the entire content of a file in PHP?',
                'explanation' => 'file_get_contents() reads a file and returns its contents as a string. It is simpler than fopen()/fread()/fclose() for reading entire files and also works with URLs.',
                'options'     => [
                    ['text' => 'file_get_contents($filename)', 'correct' => true],
                    ['text' => 'read_file($filename)', 'correct' => false],
                    ['text' => 'fread($filename)', 'correct' => false],
                    ['text' => 'get_file($filename)', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which function writes data to a file (creating it if it doesn\'t exist)?',
                'explanation' => 'file_put_contents() writes a string to a file. If the file doesn\'t exist, it creates it. If it does exist, it overwrites it by default. Use FILE_APPEND flag to append instead.',
                'options'     => [
                    ['text' => 'file_put_contents($filename, $data)', 'correct' => true],
                    ['text' => 'file_write($filename, $data)', 'correct' => false],
                    ['text' => 'write_file($filename, $data)', 'correct' => false],
                    ['text' => 'put_contents($filename, $data)', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does file_exists() return if the path is a directory?',
                'explanation' => 'file_exists() returns true for both files and directories. To check specifically for a file, use is_file(). To check for a directory, use is_dir().',
                'options'     => [
                    ['text' => 'true — it works for both files and directories', 'correct' => true],
                    ['text' => 'false — it only works for files', 'correct' => false],
                    ['text' => '"directory"', 'correct' => false],
                    ['text' => 'Throws an error', 'correct' => false],
                ],
            ],

            // ── SPL & Utility ────────────────────────────────────────────
            [
                'question'    => 'What does PHP\'s serialize() function do?',
                'explanation' => 'serialize() converts a PHP value (array, object, etc.) into a storable string representation. unserialize() reverses this. Used for storing PHP objects in files, databases, or sessions.',
                'options'     => [
                    ['text' => 'Converts a PHP value to a storable string representation', 'correct' => true],
                    ['text' => 'Converts a PHP value to JSON', 'correct' => false],
                    ['text' => 'Sends data as an HTTP body', 'correct' => false],
                    ['text' => 'Compresses data using gzip', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a magic method in PHP?',
                'explanation' => 'Magic methods are predefined methods that PHP calls automatically in certain situations. They start with a double underscore (__). Examples: __construct, __destruct, __get, __set, __call, __toString, __invoke.',
                'options'     => [
                    ['text' => 'Methods with double-underscore prefix called automatically by PHP', 'correct' => true],
                    ['text' => 'Methods that return magic constants', 'correct' => false],
                    ['text' => 'Methods available only in abstract classes', 'correct' => false],
                    ['text' => 'Deprecated PHP 4-era methods', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the __toString() magic method do?',
                'explanation' => '__toString() is called when an object is used in a string context (e.g., echo $obj or "Hello " . $obj). It must return a string. Without it, using an object as a string causes a fatal error.',
                'options'     => [
                    ['text' => 'Called when an object is used in a string context; must return a string', 'correct' => true],
                    ['text' => 'Converts all properties to strings automatically', 'correct' => false],
                    ['text' => 'Called after unserialize()', 'correct' => false],
                    ['text' => 'Converts the object to a JSON string', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does __get() magic method do in PHP?',
                'explanation' => '__get($name) is called when reading an inaccessible or undefined property. It allows implementing dynamic properties. Similarly, __set($name, $value) handles writes to inaccessible properties.',
                'options'     => [
                    ['text' => 'Called when reading an inaccessible or undefined property', 'correct' => true],
                    ['text' => 'Called when calling an undefined method', 'correct' => false],
                    ['text' => 'Returns a property value type-safely', 'correct' => false],
                    ['text' => 'Automatically creates getter methods', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does __invoke() do in PHP?',
                'explanation' => '__invoke() is called when an object is used as a function (callable). For example: $obj = new MyClass(); $obj(); calls __invoke(). Useful for callable objects like middleware or handlers.',
                'options'     => [
                    ['text' => 'Called when the object is used as a function', 'correct' => true],
                    ['text' => 'Called when the object is instantiated', 'correct' => false],
                    ['text' => 'Called when the object is serialized', 'correct' => false],
                    ['text' => 'Invokes all methods on the object', 'correct' => false],
                ],
            ],

            // ── Array Sorting & Manipulation ─────────────────────────────
            [
                'question'    => 'What is the difference between usort() and uasort()?',
                'explanation' => 'Both use a user-defined comparison function. usort() re-indexes the array after sorting (numeric keys reset). uasort() preserves the original key-value associations after sorting.',
                'options'     => [
                    ['text' => 'usort() reindexes; uasort() preserves key-value associations', 'correct' => true],
                    ['text' => 'uasort() is for ascending; usort() is for any order', 'correct' => false],
                    ['text' => 'usort() is for associative arrays; uasort() for indexed', 'correct' => false],
                    ['text' => 'They are identical', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does ksort() do to an array?',
                'explanation' => 'ksort() sorts an array by its keys in ascending order, maintaining the key-value associations. krsort() sorts by keys in reverse order. This is useful for sorting associative arrays alphabetically by key.',
                'options'     => [
                    ['text' => 'Sorts an array by keys in ascending order', 'correct' => true],
                    ['text' => 'Sorts an array by values and keeps keys', 'correct' => false],
                    ['text' => 'Sorts and removes duplicate keys', 'correct' => false],
                    ['text' => 'Returns an array of just the keys', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does array_combine(["a","b"], [1,2]) return?',
                'explanation' => 'array_combine() creates an array by using one array for keys and another for values. array_combine(["a","b"], [1,2]) returns ["a" => 1, "b" => 2]. Both arrays must have the same number of elements.',
                'options'     => [
                    ['text' => '["a" => 1, "b" => 2]', 'correct' => true],
                    ['text' => '[["a", 1], ["b", 2]]', 'correct' => false],
                    ['text' => '["a", "b", 1, 2]', 'correct' => false],
                    ['text' => '["a1", "b2"]', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does array_column($records, "name") do?',
                'explanation' => 'array_column() extracts a single column from a multi-dimensional array. array_column($records, "name") returns an indexed array of all "name" values from each record.',
                'options'     => [
                    ['text' => 'Returns all values for the "name" key from a multi-dimensional array', 'correct' => true],
                    ['text' => 'Returns the column names of the array', 'correct' => false],
                    ['text' => 'Transforms the array into a matrix by column', 'correct' => false],
                    ['text' => 'Returns the count of rows in the "name" column', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does array_map(null, [1,2], [3,4]) do?',
                'explanation' => 'When null is passed as the callback to array_map() with multiple arrays, it zips the arrays together. array_map(null, [1,2], [3,4]) returns [[1,3],[2,4]] — pairs of elements from each array.',
                'options'     => [
                    ['text' => 'Zips the arrays: returns [[1,3],[2,4]]', 'correct' => true],
                    ['text' => 'Merges the arrays: [1,2,3,4]', 'correct' => false],
                    ['text' => 'Returns the cartesian product', 'correct' => false],
                    ['text' => 'Throws an error when callback is null', 'correct' => false],
                ],
            ],

            // ── DateTime ─────────────────────────────────────────────────
            [
                'question'    => 'How do you create a DateTime object for tomorrow in PHP?',
                'explanation' => 'new DateTime() creates a DateTime object for the current time. modify() changes it. new DateTime("+1 day") or (new DateTime())->modify("+1 day") creates a DateTime for tomorrow.',
                'options'     => [
                    ['text' => 'new DateTime("+1 day") or (new DateTime())->modify("+1 day")', 'correct' => true],
                    ['text' => 'new DateTime(time() + 86400)', 'correct' => false],
                    ['text' => 'DateTime::tomorrow()', 'correct' => false],
                    ['text' => 'date_create("tomorrow")', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between strtotime() and DateTime?',
                'explanation' => 'strtotime() converts a date string to a Unix timestamp (integer). DateTime is an OOP class offering a richer API for manipulation, comparison, and formatting. DateTime is preferred for complex date math; strtotime() is quick for simple conversions.',
                'options'     => [
                    ['text' => 'strtotime() returns a timestamp; DateTime is an OOP class with more features', 'correct' => true],
                    ['text' => 'DateTime is procedural; strtotime() is object-oriented', 'correct' => false],
                    ['text' => 'They return the same results in different formats', 'correct' => false],
                    ['text' => 'strtotime() is for timezones; DateTime is timezone-agnostic', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you format a DateTime object as "YYYY-MM-DD"?',
                'explanation' => 'DateTime::format() formats the date using format characters. "Y-m-d" produces year-month-day (e.g., 2025-03-15). "Y" = 4-digit year, "m" = 2-digit month, "d" = 2-digit day.',
                'options'     => [
                    ['text' => '$dt->format("Y-m-d")', 'correct' => true],
                    ['text' => '$dt->format("YYYY-MM-DD")', 'correct' => false],
                    ['text' => 'date_format($dt, "iso")', 'correct' => false],
                    ['text' => '$dt->toDate("Y-m-d")', 'correct' => false],
                ],
            ],

            // ── Output Buffering ─────────────────────────────────────────
            [
                'question'    => 'What does ob_start() do in PHP?',
                'explanation' => 'ob_start() starts output buffering — all output (echo, print, HTML) is captured in a buffer rather than sent to the browser. ob_get_clean() retrieves and clears the buffer. This is used for template engines, header manipulation, and testing.',
                'options'     => [
                    ['text' => 'Starts output buffering — captures output instead of sending it', 'correct' => true],
                    ['text' => 'Starts a new output stream', 'correct' => false],
                    ['text' => 'Buffers HTTP headers', 'correct' => false],
                    ['text' => 'Opens a file for buffered writing', 'correct' => false],
                ],
            ],

            // ── Miscellaneous Intermediate ───────────────────────────────
            [
                'question'    => 'What is method chaining in PHP?',
                'explanation' => 'Method chaining is returning $this from each method, allowing multiple methods to be called in sequence on the same object: $obj->setName("Bob")->setAge(30)->save(). This is common in query builders and fluent APIs.',
                'options'     => [
                    ['text' => 'Calling multiple methods on one object by returning $this each time', 'correct' => true],
                    ['text' => 'Inheriting methods from parent classes', 'correct' => false],
                    ['text' => 'Calling a method from inside another method', 'correct' => false],
                    ['text' => 'Using arrays of method names to call them dynamically', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is dependency injection (DI)?',
                'explanation' => 'Dependency injection is a design technique where a class receives its dependencies from external code rather than creating them internally. This makes classes more testable and loosely coupled. The most common form is constructor injection.',
                'options'     => [
                    ['text' => 'Passing dependencies into a class rather than creating them inside it', 'correct' => true],
                    ['text' => 'Automatically loading required PHP files', 'correct' => false],
                    ['text' => 'Injecting SQL into a database query', 'correct' => false],
                    ['text' => 'A technique for mocking objects in tests', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does PHP\'s Autoloading do?',
                'explanation' => 'Autoloading automatically requires the file containing a class when it is first used, without manual require/include statements. spl_autoload_register() registers an autoloader function. Composer generates an efficient autoloader based on PSR-4.',
                'options'     => [
                    ['text' => 'Automatically loads class files when they are first used', 'correct' => true],
                    ['text' => 'Automatically creates class instances', 'correct' => false],
                    ['text' => 'Pre-loads all PHP files at application start', 'correct' => false],
                    ['text' => 'A Composer feature that downloads packages automatically', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does PHP\'s match expression return compared to switch?',
                'explanation' => 'match returns a value and uses strict comparison (===) with no fall-through. switch uses loose comparison (==), falls through, and does not return a value directly. match throws UnhandledMatchError if no arm matches.',
                'options'     => [
                    ['text' => 'A value; uses strict comparison; no fall-through', 'correct' => true],
                    ['text' => 'A boolean; uses loose comparison', 'correct' => false],
                    ['text' => 'The same as switch — no difference', 'correct' => false],
                    ['text' => 'An array of all matching cases', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the nullsafe operator ?-> in PHP 8?',
                'explanation' => 'The nullsafe operator ?-> is a shorthand for null checks in method chains. $user?->getAddress()?->getCity() returns null if $user or getAddress() is null, instead of throwing an error.',
                'options'     => [
                    ['text' => 'Calls a method only if the object is not null, returning null otherwise', 'correct' => true],
                    ['text' => 'Assigns a value only if the variable is null', 'correct' => false],
                    ['text' => 'Converts null to false before the method call', 'correct' => false],
                    ['text' => 'Same as the null coalescing operator ??', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are named arguments in PHP 8?',
                'explanation' => 'Named arguments allow passing arguments to a function by their parameter name instead of position. Example: htmlspecialchars(string: $str, encoding: "UTF-8"). This improves readability and allows skipping optional parameters.',
                'options'     => [
                    ['text' => 'Passing arguments by parameter name instead of position', 'correct' => true],
                    ['text' => 'Defining function parameters with descriptive names only', 'correct' => false],
                    ['text' => 'PHP 8 renamed all function parameters to camelCase', 'correct' => false],
                    ['text' => 'A way to pass arguments as key-value arrays', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the spread operator ... do when used in a function call?',
                'explanation' => 'The spread operator ... unpacks an array or Traversable into individual arguments when used in a function call. ...$args passes all elements of $args as separate arguments. This is the inverse of the ... in a variadic function definition.',
                'options'     => [
                    ['text' => 'Unpacks an array into individual arguments for a function call', 'correct' => true],
                    ['text' => 'Collects all extra arguments into an array', 'correct' => false],
                    ['text' => 'Copies an object\'s properties', 'correct' => false],
                    ['text' => 'Merges multiple arrays', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is PHP\'s Fibers feature (PHP 8.1)?',
                'explanation' => 'Fibers are lightweight coroutines — they allow a function to be paused (using Fiber::suspend()) and resumed later. Unlike regular generators, Fibers can be suspended from anywhere in the call stack. They are the foundation for async PHP.',
                'options'     => [
                    ['text' => 'Lightweight coroutines that can pause and resume execution', 'correct' => true],
                    ['text' => 'A way to run PHP code in parallel threads', 'correct' => false],
                    ['text' => 'A high-performance file reading API', 'correct' => false],
                    ['text' => 'An alternative to the foreach loop', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are readonly properties in PHP 8.1?',
                'explanation' => 'Readonly properties can be initialized only once (in the constructor) and then cannot be modified. Attempting to write to them after initialization throws an Error. They are perfect for value objects and DTOs.',
                'options'     => [
                    ['text' => 'Properties that can be set once (in constructor) and never modified', 'correct' => true],
                    ['text' => 'Properties accessible only from outside the class', 'correct' => false],
                    ['text' => 'Properties automatically generated from constructor parameters', 'correct' => false],
                    ['text' => 'Static properties that cannot be reassigned', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is PHP constructor property promotion (PHP 8.0)?',
                'explanation' => 'Constructor property promotion allows defining and initializing class properties directly in the constructor signature. Instead of declaring a property and then assigning it in the constructor, you prefix the parameter with a visibility modifier.',
                'options'     => [
                    ['text' => 'Declaring class properties directly in the constructor parameters', 'correct' => true],
                    ['text' => 'Automatically calling parent::__construct()', 'correct' => false],
                    ['text' => 'Promoting properties from private to public automatically', 'correct' => false],
                    ['text' => 'Using traits to inject constructor logic', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an enum in PHP 8.1?',
                'explanation' => 'Enums are a first-class way to define a type with a fixed set of named cases. Pure enums have no value; Backed enums (int or string) associate each case with a scalar value. Enums can implement interfaces and have methods.',
                'options'     => [
                    ['text' => 'A type with a fixed set of named constants called cases', 'correct' => true],
                    ['text' => 'An enumerable iterator for collections', 'correct' => false],
                    ['text' => 'A class with only static methods', 'correct' => false],
                    ['text' => 'A set of constants defined outside a class', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between require, include and Composer autoloading?',
                'explanation' => 'require/include manually load single files. Composer autoloading uses PSR-4 to automatically load any class file by mapping its namespace to a directory, eliminating the need for manual require statements in modern PHP projects.',
                'options'     => [
                    ['text' => 'Composer autoloads classes on demand; require/include manually load files', 'correct' => true],
                    ['text' => 'Composer autoloading is just a wrapper around require', 'correct' => false],
                    ['text' => 'require/include work with namespaces; Composer does not', 'correct' => false],
                    ['text' => 'They are all equivalent for loading classes', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of PHP\'s interface Countable?',
                'explanation' => 'The Countable interface requires implementing a count() method. When an object implements Countable, PHP\'s built-in count() function will call that method, allowing custom objects to work with count() naturally.',
                'options'     => [
                    ['text' => 'Allows objects to be counted with PHP\'s count() function', 'correct' => true],
                    ['text' => 'Forces a class to have a static COUNT property', 'correct' => false],
                    ['text' => 'Makes an object iterable with foreach', 'correct' => false],
                    ['text' => 'Implements counting for arrays inside objects', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the Traversable interface enable in PHP?',
                'explanation' => 'Traversable is the base interface for objects that can be iterated with foreach. It cannot be implemented directly — instead, implement Iterator or IteratorAggregate, which extend Traversable.',
                'options'     => [
                    ['text' => 'Makes objects iterable with foreach by implementing Iterator or IteratorAggregate', 'correct' => true],
                    ['text' => 'Makes an object sortable with usort()', 'correct' => false],
                    ['text' => 'Allows an object to be traversed backwards', 'correct' => false],
                    ['text' => 'Implements tree traversal algorithms', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is PHP\'s ArrayAccess interface used for?',
                'explanation' => 'ArrayAccess allows objects to be used with array syntax ([]) for reading ($obj[$key]), writing ($obj[$key] = $val), checking (isset($obj[$key])), and deleting (unset($obj[$key])). Laravel\'s Collection uses this.',
                'options'     => [
                    ['text' => 'Allows objects to be accessed like arrays using [] syntax', 'correct' => true],
                    ['text' => 'Provides access to PHP\'s internal array functions for objects', 'correct' => false],
                    ['text' => 'Converts objects to arrays automatically', 'correct' => false],
                    ['text' => 'Restricts array access to specific keys', 'correct' => false],
                ],
            ],
        ];
    }
}
