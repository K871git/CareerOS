<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Database\Seeder;

class PythonAdvancedQuestionsSeeder extends Seeder
{
    public function run(): void
    {
        $track = LearningTrack::firstOrCreate(
            ['slug' => 'backend-engineering'],
            ['title' => 'Backend Engineering', 'display_order' => 3]
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

        $topic = Topic::where('slug', 'python-advanced')->firstOrFail();

        Question::where('topic_id', $topic->id)->delete();

        foreach ($this->questions() as $qData) {
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
                'is_correct'  => $opt['correct'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ], $qData['options']));
        }

        $count = Question::where('topic_id', $topic->id)->count();
        $this->command->info("Python Advanced: {$count} questions seeded.");
    }

    private function questions(): array
    {
        return [
            // --- Metaclasses ---
            [
                'question'    => 'What is a metaclass in Python?',
                'explanation' => 'A metaclass is the "class of a class". It controls how classes are created. The default metaclass is "type". Metaclasses let you intercept class creation and modify class attributes or methods.',
                'options'     => [
                    ['text' => 'A class that cannot be instantiated',                          'correct' => false],
                    ['text' => 'The class that defines how a class is created',                'correct' => true],
                    ['text' => 'A subclass that inherits from multiple parents',               'correct' => false],
                    ['text' => 'A decorator applied to class methods',                         'correct' => false],
                ],
            ],
            [
                'question'    => 'What does type("MyClass", (object,), {"x": 1}) do?',
                'explanation' => 'Calling type() with three arguments dynamically creates a new class named "MyClass" that inherits from object and has a class attribute x = 1.',
                'options'     => [
                    ['text' => 'Checks the type of an existing class',                        'correct' => false],
                    ['text' => 'Dynamically creates a new class at runtime',                  'correct' => true],
                    ['text' => 'Creates an instance of object with x=1',                     'correct' => false],
                    ['text' => 'Raises a TypeError for incorrect argument count',             'correct' => false],
                ],
            ],
            // --- Descriptors ---
            [
                'question'    => 'What makes an object a descriptor in Python?',
                'explanation' => 'A descriptor is any object that defines __get__, __set__, or __delete__. Descriptors power properties, methods, classmethod, staticmethod, and slots.',
                'options'     => [
                    ['text' => 'It inherits from the Descriptor base class',                   'correct' => false],
                    ['text' => 'It defines __get__, __set__, or __delete__',                   'correct' => true],
                    ['text' => 'It is decorated with @descriptor',                            'correct' => false],
                    ['text' => 'It overrides __init__ with a special signature',              'correct' => false],
                ],
            ],
            // --- Abstract Base Classes ---
            [
                'question'    => 'What is the purpose of abc.ABC and @abstractmethod?',
                'explanation' => 'ABC (Abstract Base Class) prevents instantiation of a class unless all @abstractmethod methods are implemented by a subclass, enforcing a required interface.',
                'options'     => [
                    ['text' => 'To make methods private to the class hierarchy',                   'correct' => false],
                    ['text' => 'To enforce that subclasses implement required methods',             'correct' => true],
                    ['text' => 'To enable multiple inheritance without conflicts',                  'correct' => false],
                    ['text' => 'To cache the result of expensive methods',                         'correct' => false],
                ],
            ],
            // --- MRO ---
            [
                'question'    => 'What is the Method Resolution Order (MRO) in Python?',
                'explanation' => 'MRO defines the order in which base classes are searched when looking for a method or attribute. Python uses the C3 linearisation algorithm. You can inspect it with ClassName.__mro__ or ClassName.mro().',
                'options'     => [
                    ['text' => 'The order in which __init__ methods are defined',                  'correct' => false],
                    ['text' => 'The order Python searches base classes for attributes and methods', 'correct' => true],
                    ['text' => 'The order in which modules are imported',                          'correct' => false],
                    ['text' => 'The priority of method decorators in a class',                    'correct' => false],
                ],
            ],
            [
                'question'    => 'Given: class A: pass; class B(A): pass; class C(A): pass; class D(B, C): pass — what is D.__mro__?',
                'explanation' => 'Python C3 linearisation gives: D → B → C → A → object. This is the "diamond problem" solved correctly.',
                'options'     => [
                    ['text' => '(D, A, B, C, object)',  'correct' => false],
                    ['text' => '(D, B, C, A, object)',  'correct' => true],
                    ['text' => '(D, B, A, C, object)',  'correct' => false],
                    ['text' => '(D, C, B, A, object)',  'correct' => false],
                ],
            ],
            // --- __slots__ ---
            [
                'question'    => 'What does defining __slots__ in a class do?',
                'explanation' => '__slots__ replaces the per-instance __dict__ with a fixed set of attributes, reducing memory usage and slightly improving attribute access speed.',
                'options'     => [
                    ['text' => 'Prevents the class from being subclassed',                    'correct' => false],
                    ['text' => 'Restricts instance attributes to a predefined set, saving memory', 'correct' => true],
                    ['text' => 'Makes all attributes read-only',                              'correct' => false],
                    ['text' => 'Enables the class to be pickled',                            'correct' => false],
                ],
            ],
            // --- GIL ---
            [
                'question'    => 'What is the Global Interpreter Lock (GIL) in CPython?',
                'explanation' => 'The GIL is a mutex that allows only one thread to execute Python bytecode at a time in CPython. It prevents true parallelism for CPU-bound threads but does not affect I/O-bound concurrency.',
                'options'     => [
                    ['text' => 'A lock that prevents two processes from accessing the same file',       'correct' => false],
                    ['text' => 'A mutex allowing only one thread to execute Python bytecode at a time', 'correct' => true],
                    ['text' => 'A mechanism for garbage collection of global variables',                'correct' => false],
                    ['text' => 'A compiler optimisation that locks constant values',                   'correct' => false],
                ],
            ],
            [
                'question'    => 'When is Python\'s threading module effective despite the GIL?',
                'explanation' => 'The GIL is released during I/O operations (file, network, sleep). Threading is effective for I/O-bound tasks. For CPU-bound work, use multiprocessing to bypass the GIL.',
                'options'     => [
                    ['text' => 'CPU-bound tasks that need true parallelism',          'correct' => false],
                    ['text' => 'I/O-bound tasks (network, file, database)',           'correct' => true],
                    ['text' => 'Memory-intensive numerical computations',             'correct' => false],
                    ['text' => 'Threaded tasks never benefit — always use multiprocessing', 'correct' => false],
                ],
            ],
            // --- asyncio ---
            [
                'question'    => 'What does the "async def" keyword create?',
                'explanation' => '"async def" defines a coroutine function. Calling it returns a coroutine object; you must await it (or run it with asyncio.run()) to execute the body.',
                'options'     => [
                    ['text' => 'A thread that runs in the background automatically', 'correct' => false],
                    ['text' => 'A coroutine function that must be awaited to run',   'correct' => true],
                    ['text' => 'A generator function similar to yield',              'correct' => false],
                    ['text' => 'A function that runs in a separate process',         'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of "await" in an async function?',
                'explanation' => '"await" suspends the current coroutine until the awaited coroutine completes, yielding control back to the event loop so other tasks can run.',
                'options'     => [
                    ['text' => 'It blocks the entire program until the operation finishes',           'correct' => false],
                    ['text' => 'It suspends the coroutine and yields control to the event loop',      'correct' => true],
                    ['text' => 'It starts a new thread to run the awaited coroutine',                 'correct' => false],
                    ['text' => 'It ignores exceptions thrown by the awaited coroutine',               'correct' => false],
                ],
            ],
            // --- functools ---
            [
                'question'    => 'What does @functools.lru_cache do?',
                'explanation' => 'lru_cache memoises the function — it caches return values for seen arguments using a Least Recently Used strategy, avoiding redundant recomputation.',
                'options'     => [
                    ['text' => 'It runs the function in a separate thread',                       'correct' => false],
                    ['text' => 'It caches function results to avoid redundant calls (memoisation)', 'correct' => true],
                    ['text' => 'It limits how many times a function can be called',               'correct' => false],
                    ['text' => 'It logs each function call to a file',                           'correct' => false],
                ],
            ],
            // --- Type hints ---
            [
                'question'    => 'What does the following type hint mean: def greet(name: str) -> str?',
                'explanation' => 'The hint declares that name should be a str and the return value will be a str. Type hints are informational; Python does not enforce them at runtime.',
                'options'     => [
                    ['text' => 'Python will enforce str type at runtime and raise TypeError otherwise', 'correct' => false],
                    ['text' => 'It documents the expected input and return types for tools and developers', 'correct' => true],
                    ['text' => 'The function can only accept str — other types raise SyntaxError',          'correct' => false],
                    ['text' => 'It converts the argument to str automatically',                              'correct' => false],
                ],
            ],
            [
                'question'    => 'What does Optional[str] mean in type hints?',
                'explanation' => 'Optional[str] is equivalent to Union[str, None]. It indicates the value can be a str or None.',
                'options'     => [
                    ['text' => 'The parameter is not required when calling the function',   'correct' => false],
                    ['text' => 'The value can be a str or None',                            'correct' => true],
                    ['text' => 'The value can be any type',                                 'correct' => false],
                    ['text' => 'The parameter has a default value of ""',                  'correct' => false],
                ],
            ],
            // --- dataclasses ---
            [
                'question'    => 'What does the @dataclass decorator do?',
                'explanation' => '@dataclass auto-generates __init__, __repr__, and __eq__ based on the class\'s annotated fields, eliminating boilerplate.',
                'options'     => [
                    ['text' => 'It converts the class into a dict automatically',                          'correct' => false],
                    ['text' => 'It auto-generates __init__, __repr__, and __eq__ from annotated fields',   'correct' => true],
                    ['text' => 'It makes all attributes immutable',                                        'correct' => false],
                    ['text' => 'It replaces class attributes with database columns',                       'correct' => false],
                ],
            ],
            // --- __new__ vs __init__ ---
            [
                'question'    => 'What is the difference between __new__ and __init__ in Python?',
                'explanation' => '__new__ creates the instance (allocates memory and returns the object). __init__ initialises the already-created instance. __new__ is rarely overridden except for singletons or immutable types.',
                'options'     => [
                    ['text' => '__new__ is called after __init__',                                   'correct' => false],
                    ['text' => '__new__ creates the instance; __init__ initialises it',              'correct' => true],
                    ['text' => 'They are aliases — both do the same thing',                          'correct' => false],
                    ['text' => '__new__ is only available in metaclasses',                           'correct' => false],
                ],
            ],
            // --- Memory management ---
            [
                'question'    => 'How does Python\'s garbage collector handle circular references?',
                'explanation' => 'CPython uses reference counting as its primary GC. For circular references (where ref counts never reach 0), it runs a cyclic garbage collector that detects and collects unreachable cycles.',
                'options'     => [
                    ['text' => 'Python cannot collect circular references — they are memory leaks',       'correct' => false],
                    ['text' => 'A cyclic garbage collector detects and collects circular references',     'correct' => true],
                    ['text' => 'Circular references are prevented at compile time',                      'correct' => false],
                    ['text' => 'Reference counting alone handles circular references',                   'correct' => false],
                ],
            ],
            // --- Protocol (structural subtyping) ---
            [
                'question'    => 'What does typing.Protocol enable in Python?',
                'explanation' => 'Protocol enables structural (duck-type) subtyping. A class satisfies a Protocol if it has the required methods/attributes — without needing to explicitly inherit from or register with the Protocol.',
                'options'     => [
                    ['text' => 'Runtime enforcement of method signatures',                          'correct' => false],
                    ['text' => 'Structural subtyping — compatibility is based on the presence of methods, not inheritance', 'correct' => true],
                    ['text' => 'A replacement for ABC that prevents subclassing',                  'correct' => false],
                    ['text' => 'Automatic serialisation of objects to JSON',                       'correct' => false],
                ],
            ],
            // --- __enter__ / __exit__ ---
            [
                'question'    => 'What must a class implement to support the "with" statement?',
                'explanation' => 'A context manager must implement __enter__ (called on entry, its return value binds to "as") and __exit__ (called on exit, receives exception info).',
                'options'     => [
                    ['text' => '__open__ and __close__',   'correct' => false],
                    ['text' => '__enter__ and __exit__',   'correct' => true],
                    ['text' => '__start__ and __stop__',   'correct' => false],
                    ['text' => '__begin__ and __end__',    'correct' => false],
                ],
            ],
            // --- Coroutine chaining ---
            [
                'question'    => 'What does asyncio.gather() do?',
                'explanation' => 'asyncio.gather() runs multiple awaitables concurrently and returns a future that resolves when all of them complete, collecting their results in order.',
                'options'     => [
                    ['text' => 'Runs awaitables sequentially one after another',                    'correct' => false],
                    ['text' => 'Runs multiple awaitables concurrently and collects all results',    'correct' => true],
                    ['text' => 'Cancels all running tasks when one fails',                         'correct' => false],
                    ['text' => 'Creates a new event loop for each awaitable',                      'correct' => false],
                ],
            ],
            // --- namedtuple ---
            [
                'question'    => 'What is the advantage of using collections.namedtuple over a regular tuple?',
                'explanation' => 'namedtuple gives each field a name, so elements can be accessed by name (point.x) instead of only by index (point[0]), improving readability with no extra memory overhead.',
                'options'     => [
                    ['text' => 'namedtuples are mutable unlike regular tuples',                    'correct' => false],
                    ['text' => 'Fields can be accessed by name instead of only by index',          'correct' => true],
                    ['text' => 'namedtuples support more elements than regular tuples',             'correct' => false],
                    ['text' => 'namedtuples are significantly faster than regular tuples',         'correct' => false],
                ],
            ],
            // --- Singleton pattern ---
            [
                'question'    => 'How can you implement a Singleton in Python using __new__?',
                'explanation' => 'Override __new__ to check if an instance already exists (stored as a class attribute). If it does, return it; otherwise create and store the new instance.',
                'options'     => [
                    ['text' => 'Override __init__ and raise an error on the second call',              'correct' => false],
                    ['text' => 'Override __new__ to return a cached instance if one already exists',   'correct' => true],
                    ['text' => 'Decorate the class with @singleton from the standard library',         'correct' => false],
                    ['text' => 'Use a global module-level variable and call it a class',              'correct' => false],
                ],
            ],
            // --- Weak references ---
            [
                'question'    => 'What is the purpose of weakref.ref() in Python?',
                'explanation' => 'A weak reference allows you to refer to an object without increasing its reference count. If no strong references remain, the object is garbage collected and the weak ref returns None.',
                'options'     => [
                    ['text' => 'It creates an immutable reference to an object',                       'correct' => false],
                    ['text' => 'It references an object without preventing its garbage collection',    'correct' => true],
                    ['text' => 'It creates a copy of the object to protect against mutation',         'correct' => false],
                    ['text' => 'It is a thread-safe reference for shared state',                      'correct' => false],
                ],
            ],
            // --- exec / eval ---
            [
                'question'    => 'What is the security risk of using eval() with untrusted input?',
                'explanation' => 'eval() executes arbitrary Python expressions. Passing untrusted user input to eval() allows code injection — the injected code runs with the same privileges as the application.',
                'options'     => [
                    ['text' => 'eval() is very slow and can cause performance issues',                'correct' => false],
                    ['text' => 'It executes arbitrary code, enabling code injection attacks',         'correct' => true],
                    ['text' => 'eval() only evaluates integer expressions — strings cause TypeError', 'correct' => false],
                    ['text' => 'It leaks memory on every call',                                      'correct' => false],
                ],
            ],
            // --- copy ---
            [
                'question'    => 'What is the difference between copy.copy() and copy.deepcopy()?',
                'explanation' => 'copy.copy() creates a shallow copy — nested objects are shared. copy.deepcopy() recursively copies all nested objects so the copy is completely independent.',
                'options'     => [
                    ['text' => 'copy.copy() is for immutable objects; deepcopy for mutable',        'correct' => false],
                    ['text' => 'copy() is shallow (nested objects shared); deepcopy() is fully independent', 'correct' => true],
                    ['text' => 'deepcopy() only works with dicts; copy() works with all types',     'correct' => false],
                    ['text' => 'They are identical except deepcopy() is faster',                    'correct' => false],
                ],
            ],
            // --- Monkey patching ---
            [
                'question'    => 'What is monkey patching in Python?',
                'explanation' => 'Monkey patching is dynamically replacing or adding attributes/methods of a module or class at runtime. It is often used in tests to mock dependencies.',
                'options'     => [
                    ['text' => 'A design pattern for chaining method calls',                            'correct' => false],
                    ['text' => 'Dynamically replacing attributes or methods of a module or class at runtime', 'correct' => true],
                    ['text' => 'A technique for optimising Python loops',                               'correct' => false],
                    ['text' => 'Applying multiple decorators to a single function',                    'correct' => false],
                ],
            ],
        ];
    }
}
