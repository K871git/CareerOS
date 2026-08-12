<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Database\Seeder;

class PythonIntermediateQuestionsSeeder extends Seeder
{
    public function run(): void
    {
        $track = LearningTrack::firstOrCreate(
            ['slug' => 'full-stack-web-development'],
            ['title' => 'Full Stack Web Development', 'display_order' => 1]
        );

        Subject::firstOrCreate(
            ['slug' => 'python'],
            [
                'learning_track_id' => $track->id,
                'title'             => 'Python',
                'display_order'     => 5,
                'description'       => 'Python practice questions — Junior, Intermediate, Advanced',
            ]
        );

        $topic = Topic::where('slug', 'python-intermediate')->firstOrFail();

        $count = 0;
        foreach ($this->questions() as $qData) {
            $exists = Question::where('topic_id', $topic->id)
                ->where('question', $qData['question'])
                ->exists();

            if ($exists) {
                continue;
            }

            $question = Question::create([
                'topic_id'    => $topic->id,
                'question'    => $qData['question'],
                'type'        => 'MCQ',
                'difficulty'  => 'Medium',
                'explanation' => $qData['explanation'],
            ]);

            foreach ($qData['options'] as $opt) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $opt['text'],
                    'is_correct'  => $opt['correct'],
                ]);
            }

            $count++;
        }

        $this->command->info("Python Intermediate: {$count} questions seeded.");
    }

    private function questions(): array
    {
        return [
            // --- OOP ---
            [
                'question'    => 'What is the purpose of __init__ in a Python class?',
                'explanation' => '__init__ is the initialiser (constructor). It is called automatically when a new instance is created to set up its initial state.',
                'options'     => [
                    ['text' => 'To define class-level methods',                    'correct' => false],
                    ['text' => 'To initialise instance attributes when an object is created', 'correct' => true],
                    ['text' => 'To destroy an object',                             'correct' => false],
                    ['text' => 'To define the string representation of a class',   'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between a class variable and an instance variable?',
                'explanation' => 'Class variables are shared by all instances. Instance variables (defined via self) are unique to each object.',
                'options'     => [
                    ['text' => 'There is no difference',                                              'correct' => false],
                    ['text' => 'Class variables are shared across all instances; instance variables are per-object', 'correct' => true],
                    ['text' => 'Instance variables are shared; class variables are per-object',        'correct' => false],
                    ['text' => 'Class variables can only be integers',                                 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does super() do in Python?',
                'explanation' => 'super() returns a proxy to the parent class, allowing you to call its methods — commonly used to call the parent __init__ in a subclass.',
                'options'     => [
                    ['text' => 'Creates a new instance of the parent class',                    'correct' => false],
                    ['text' => 'Returns a proxy to the parent class so you can call its methods', 'correct' => true],
                    ['text' => 'Deletes the current instance',                                   'correct' => false],
                    ['text' => 'Checks if a class is a subclass',                               'correct' => false],
                ],
            ],
            [
                'question'    => 'What does __str__ define in a Python class?',
                'explanation' => '__str__ defines the human-readable string representation returned by str() and print(). __repr__ is for developers.',
                'options'     => [
                    ['text' => 'The string used in debugging and repr()',        'correct' => false],
                    ['text' => 'The human-readable string for str() and print()', 'correct' => true],
                    ['text' => 'The length of the object',                       'correct' => false],
                    ['text' => 'How two objects are compared with ==',           'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the @property decorator used for?',
                'explanation' => '@property lets you define a method that is accessed like an attribute, enabling computed properties without breaking the calling interface.',
                'options'     => [
                    ['text' => 'To make a method private',                                                'correct' => false],
                    ['text' => 'To convert a method into a read-only attribute-style accessor',           'correct' => true],
                    ['text' => 'To cache the result of a method permanently',                            'correct' => false],
                    ['text' => 'To prevent subclasses from overriding a method',                         'correct' => false],
                ],
            ],
            // --- Decorators ---
            [
                'question'    => 'What is a decorator in Python?',
                'explanation' => 'A decorator is a callable that wraps another function or class, adding behaviour before or after the original runs, using @syntax.',
                'options'     => [
                    ['text' => 'A built-in function for formatting strings',                         'correct' => false],
                    ['text' => 'A callable that wraps another callable to extend its behaviour',     'correct' => true],
                    ['text' => 'A keyword for creating class methods',                              'correct' => false],
                    ['text' => 'A module for applying CSS-like styles',                             'correct' => false],
                ],
            ],
            [
                'question'    => 'What does @staticmethod indicate in a class?',
                'explanation' => '@staticmethod defines a method that does not receive the instance (self) or class (cls). It is essentially a regular function namespaced inside the class.',
                'options'     => [
                    ['text' => 'The method receives the class as the first argument',       'correct' => false],
                    ['text' => 'The method is private and cannot be called outside the class', 'correct' => false],
                    ['text' => 'The method does not receive self or cls — it is a plain function', 'correct' => true],
                    ['text' => 'The method is cached after its first call',                 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does @classmethod indicate and what is its first parameter?',
                'explanation' => '@classmethod receives the class itself as its first argument (conventionally named cls), not an instance. Used for factory methods and alternative constructors.',
                'options'     => [
                    ['text' => 'It receives self — the current instance',           'correct' => false],
                    ['text' => 'It receives cls — the class itself',                 'correct' => true],
                    ['text' => 'It receives no arguments',                          'correct' => false],
                    ['text' => 'It receives the parent class automatically',        'correct' => false],
                ],
            ],
            // --- Generators ---
            [
                'question'    => 'What does the "yield" keyword do in a function?',
                'explanation' => 'yield turns a function into a generator. Each call to next() resumes from where yield paused and produces the next value.',
                'options'     => [
                    ['text' => 'Returns a value and ends the function',                    'correct' => false],
                    ['text' => 'Pauses execution and produces a value lazily',             'correct' => true],
                    ['text' => 'Raises a StopIteration exception immediately',            'correct' => false],
                    ['text' => 'Stores all values in memory before returning',            'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the main advantage of a generator over a list?',
                'explanation' => 'Generators produce values lazily (one at a time) instead of building the entire sequence in memory, which is ideal for large or infinite sequences.',
                'options'     => [
                    ['text' => 'Generators are always faster for small datasets',          'correct' => false],
                    ['text' => 'Generators produce values lazily, saving memory',          'correct' => true],
                    ['text' => 'Generators support indexing like lists',                   'correct' => false],
                    ['text' => 'Generators can be iterated multiple times automatically', 'correct' => false],
                ],
            ],
            // --- Closures ---
            [
                'question'    => 'What is a closure in Python?',
                'explanation' => 'A closure is an inner function that retains access to variables from its enclosing scope even after that outer function has finished executing.',
                'options'     => [
                    ['text' => 'A function that can only be called once',                               'correct' => false],
                    ['text' => 'An inner function that captures variables from its enclosing scope',    'correct' => true],
                    ['text' => 'A class with no public methods',                                        'correct' => false],
                    ['text' => 'A function decorated with @staticmethod',                               'correct' => false],
                ],
            ],
            // --- Lambda ---
            [
                'question'    => 'What is a lambda function?',
                'explanation' => 'A lambda is an anonymous, single-expression function. lambda x: x * 2 is equivalent to a small def with one return statement.',
                'options'     => [
                    ['text' => 'A function that can accept unlimited arguments',             'correct' => false],
                    ['text' => 'An anonymous function defined with a single expression',    'correct' => true],
                    ['text' => 'A built-in higher-order function like map or filter',       'correct' => false],
                    ['text' => 'A function that runs asynchronously',                       'correct' => false],
                ],
            ],
            [
                'question'    => 'What does sorted(["banana", "apple", "cherry"], key=lambda x: x[0]) return?',
                'explanation' => 'Sorting by the first character: "apple"[0]="a", "banana"[0]="b", "cherry"[0]="c" → ["apple", "banana", "cherry"].',
                'options'     => [
                    ['text' => '["apple", "banana", "cherry"]',   'correct' => true],
                    ['text' => '["cherry", "banana", "apple"]',   'correct' => false],
                    ['text' => '["banana", "apple", "cherry"]',   'correct' => false],
                    ['text' => '["apple", "cherry", "banana"]',   'correct' => false],
                ],
            ],
            // --- Comprehensions ---
            [
                'question'    => 'What does {k: v for k, v in d.items() if v > 0} create?',
                'explanation' => 'This is a dict comprehension that builds a new dictionary containing only key-value pairs where the value is greater than 0.',
                'options'     => [
                    ['text' => 'A list of keys where value > 0',                           'correct' => false],
                    ['text' => 'A new dict with only entries where value > 0',             'correct' => true],
                    ['text' => 'A set of values greater than 0',                           'correct' => false],
                    ['text' => 'A generator of (k, v) tuples where value > 0',            'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between a list comprehension and a generator expression?',
                'explanation' => 'List comprehensions use [] and produce the full list in memory. Generator expressions use () and produce values lazily one at a time.',
                'options'     => [
                    ['text' => 'There is no difference — both produce a list',                     'correct' => false],
                    ['text' => 'List comprehensions are eager (full list); generators are lazy',    'correct' => true],
                    ['text' => 'Generators produce a list; list comprehensions produce a generator', 'correct' => false],
                    ['text' => 'Generator expressions use [] syntax',                              'correct' => false],
                ],
            ],
            // --- Context Managers ---
            [
                'question'    => 'What is the purpose of the "with" statement?',
                'explanation' => '"with" is used for context management — it guarantees that __enter__ runs before the block and __exit__ runs after, even if an exception occurs. Commonly used with file I/O.',
                'options'     => [
                    ['text' => 'It defines a conditional block like if/else',                         'correct' => false],
                    ['text' => 'It runs setup and teardown code automatically (context manager)',      'correct' => true],
                    ['text' => 'It creates a local scope for variable isolation',                     'correct' => false],
                    ['text' => 'It imports a module for temporary use',                              'correct' => false],
                ],
            ],
            // --- Exception Handling ---
            [
                'question'    => 'What does the "finally" block in a try/except statement guarantee?',
                'explanation' => 'The finally block always executes — whether an exception was raised or not. It is used for cleanup code like closing files or releasing locks.',
                'options'     => [
                    ['text' => 'It runs only when no exception occurs',       'correct' => false],
                    ['text' => 'It runs only when an exception occurs',       'correct' => false],
                    ['text' => 'It always runs regardless of exceptions',     'correct' => true],
                    ['text' => 'It suppresses all exceptions',                'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you raise a custom exception in Python?',
                'explanation' => 'Custom exceptions are defined by subclassing Exception (or any built-in exception). You raise them with the raise keyword.',
                'options'     => [
                    ['text' => 'throw MyException()',                              'correct' => false],
                    ['text' => 'raise MyException()',                              'correct' => true],
                    ['text' => 'error MyException()',                              'correct' => false],
                    ['text' => 'except MyException()',                             'correct' => false],
                ],
            ],
            // --- File I/O ---
            [
                'question'    => 'What is the idiomatic way to open and read a file in Python?',
                'explanation' => 'Using "with open(filename) as f:" ensures the file is closed automatically when the block exits, even on error.',
                'options'     => [
                    ['text' => 'f = open(filename); f.read(); f.close()',       'correct' => false],
                    ['text' => 'with open(filename) as f: f.read()',            'correct' => true],
                    ['text' => 'file.open(filename).read()',                    'correct' => false],
                    ['text' => 'import file; file.read(filename)',              'correct' => false],
                ],
            ],
            // --- Collections module ---
            [
                'question'    => 'What does collections.Counter do?',
                'explanation' => 'Counter is a dict subclass that counts the occurrences of elements in an iterable. Counter("aabbc") → Counter({"a": 2, "b": 2, "c": 1}).',
                'options'     => [
                    ['text' => 'Increments a global integer counter',                          'correct' => false],
                    ['text' => 'Counts element occurrences in an iterable',                    'correct' => true],
                    ['text' => 'Creates a thread-safe counter for concurrency',                'correct' => false],
                    ['text' => 'Returns the number of objects in memory',                      'correct' => false],
                ],
            ],
            [
                'question'    => 'What does collections.defaultdict do differently from a regular dict?',
                'explanation' => 'defaultdict automatically creates a default value for missing keys using a factory function, instead of raising a KeyError.',
                'options'     => [
                    ['text' => 'It only allows string keys',                                             'correct' => false],
                    ['text' => 'It auto-creates a default value for missing keys instead of raising KeyError', 'correct' => true],
                    ['text' => 'It maintains insertion order better than a regular dict',                 'correct' => false],
                    ['text' => 'It stores key-value pairs in sorted order',                              'correct' => false],
                ],
            ],
            // --- Mutable default argument pitfall ---
            [
                'question'    => 'What is wrong with: def append_item(item, lst=[])? lst.append(item); return lst',
                'explanation' => 'Mutable default arguments (like []) are created once and shared across all calls. Use None as default and create the list inside the function instead.',
                'options'     => [
                    ['text' => 'Lists cannot be used as default arguments',                     'correct' => false],
                    ['text' => 'The default list is shared across all calls — mutations persist', 'correct' => true],
                    ['text' => 'append() is not allowed inside functions',                      'correct' => false],
                    ['text' => 'The return type is incorrect',                                  'correct' => false],
                ],
            ],
            // --- Iterators ---
            [
                'question'    => 'What two methods must an object implement to be an iterator?',
                'explanation' => 'An iterator must implement __iter__ (returns self) and __next__ (returns the next value or raises StopIteration when exhausted).',
                'options'     => [
                    ['text' => '__start__ and __stop__',  'correct' => false],
                    ['text' => '__iter__ and __next__',   'correct' => true],
                    ['text' => '__begin__ and __end__',   'correct' => false],
                    ['text' => '__get__ and __set__',     'correct' => false],
                ],
            ],
            // --- Sorting ---
            [
                'question'    => 'What is the difference between list.sort() and sorted()?',
                'explanation' => 'list.sort() modifies the list in-place and returns None. sorted() returns a new sorted list and works on any iterable.',
                'options'     => [
                    ['text' => 'sorted() only works on lists; list.sort() works on any iterable', 'correct' => false],
                    ['text' => 'list.sort() sorts in-place; sorted() returns a new list',          'correct' => true],
                    ['text' => 'They are identical except for the name',                           'correct' => false],
                    ['text' => 'list.sort() returns the sorted list; sorted() returns None',       'correct' => false],
                ],
            ],
            // --- Modules ---
            [
                'question'    => 'What does if __name__ == "__main__": guard?',
                'explanation' => 'This guard ensures the block runs only when the file is executed directly, not when it is imported as a module by another file.',
                'options'     => [
                    ['text' => 'It defines the entry point for all Python programs',              'correct' => false],
                    ['text' => 'It prevents the code from running when the file is imported',     'correct' => true],
                    ['text' => 'It checks if the module is named correctly',                     'correct' => false],
                    ['text' => 'It imports the main module from the standard library',           'correct' => false],
                ],
            ],
            // --- String formatting ---
            [
                'question'    => 'What is the output of: f"{3.14159:.2f}"?',
                'explanation' => ':.2f is a format spec for fixed-point with 2 decimal places. f"{3.14159:.2f}" produces "3.14".',
                'options'     => [
                    ['text' => '"3.14159"', 'correct' => false],
                    ['text' => '"3.14"',    'correct' => true],
                    ['text' => '"3.1"',     'correct' => false],
                    ['text' => '"3"',       'correct' => false],
                ],
            ],
            // --- map/filter ---
            [
                'question'    => 'What does list(map(lambda x: x * 2, [1, 2, 3])) return?',
                'explanation' => 'map() applies the function to every element. lambda x: x * 2 doubles each, producing a map object; list() converts it to [2, 4, 6].',
                'options'     => [
                    ['text' => '[1, 2, 3, 1, 2, 3]', 'correct' => false],
                    ['text' => '[2, 4, 6]',            'correct' => true],
                    ['text' => '[1, 4, 9]',            'correct' => false],
                    ['text' => '6',                    'correct' => false],
                ],
            ],
            [
                'question'    => 'What does list(filter(lambda x: x % 2 == 0, [1, 2, 3, 4, 5])) return?',
                'explanation' => 'filter() keeps only elements where the function returns True. Even numbers in [1–5] are 2 and 4.',
                'options'     => [
                    ['text' => '[1, 3, 5]', 'correct' => false],
                    ['text' => '[2, 4]',    'correct' => true],
                    ['text' => '[2, 4, 6]', 'correct' => false],
                    ['text' => '[0, 2, 4]', 'correct' => false],
                ],
            ],
            // --- String methods ---
            [
                'question'    => 'What does "  hello  ".strip() return?',
                'explanation' => 'strip() removes leading and trailing whitespace. "  hello  ".strip() returns "hello".',
                'options'     => [
                    ['text' => '"  hello  "', 'correct' => false],
                    ['text' => '"hello"',     'correct' => true],
                    ['text' => '"hello  "',   'correct' => false],
                    ['text' => '"  hello"',   'correct' => false],
                ],
            ],
            // --- Walrus operator ---
            [
                'question'    => 'What does the walrus operator (:=) do in Python 3.8+?',
                'explanation' => ':= (walrus) assigns a value to a variable as part of an expression. It is useful in while loops or comprehensions to avoid calling the same function twice.',
                'options'     => [
                    ['text' => 'Compares two values with strict equality',                            'correct' => false],
                    ['text' => 'Assigns a value AND returns it as part of an expression',             'correct' => true],
                    ['text' => 'Checks if a variable is a walrus (None-safe comparison)',             'correct' => false],
                    ['text' => 'Creates a shallow copy of an object',                                'correct' => false],
                ],
            ],
        ];
    }
}
