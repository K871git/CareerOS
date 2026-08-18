<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PythonLearningSeeder extends Seeder
{
    public function run(): void
    {
        $track = LearningTrack::firstOrCreate(
            ['slug' => 'backend-engineering'],
            [
                'title'         => 'Backend Engineering',
                'description'   => 'Backend engineering — Python, databases, APIs, and server-side architecture.',
                'display_order' => 3,
            ]
        );

        $subject = Subject::firstOrCreate(
            ['slug' => 'python'],
            [
                'learning_track_id' => $track->id,
                'title'             => 'Python',
                'description'       => 'Master Python from core fundamentals to advanced patterns used in modern backend development.',
                'display_order'     => 5,
            ]
        );

        // ── Step 1: Assign correct levels to existing practice topics ──────
        Topic::where('slug', 'python-junior')->update(['level' => 1]);
        Topic::where('slug', 'python-intermediate')->update(['level' => 2]);
        Topic::where('slug', 'python-advanced')->update(['level' => 3]);

        // ── Step 2: Create topics for levels 4 and 5 ──────────────────────
        $topic4 = Topic::firstOrCreate(
            ['slug' => 'python-level-4-stdlib'],
            [
                'subject_id'    => $subject->id,
                'title'         => 'Python Standard Library & Modern Python',
                'description'   => 'Type hints, dataclasses, standard library deep dive, data structures, and algorithms.',
                'display_order' => 4,
                'level'         => 4,
            ]
        );
        Topic::where('slug', 'python-level-4-stdlib')->update(['level' => 4]);

        $topic5 = Topic::firstOrCreate(
            ['slug' => 'python-level-5-expert'],
            [
                'subject_id'    => $subject->id,
                'title'         => 'Expert Python',
                'description'   => 'Async programming, testing, packaging, metaclasses, and the Python ecosystem.',
                'display_order' => 5,
                'level'         => 5,
            ]
        );
        Topic::where('slug', 'python-level-5-expert')->update(['level' => 5]);

        // ── Step 3: Seed lessons for all 5 levels ─────────────────────────
        $this->seedLessons($subject);

        // ── Step 4: Seed exam questions for levels 4 and 5 ────────────────
        $this->seedLevel4Questions($topic4);
        $this->seedLevel5Questions($topic5);

        $this->command->info('Python Learning seeder complete — all 5 levels populated.');
    }

    // ── LESSONS ─────────────────────────────────────────────────────────────

    private function seedLessons(Subject $subject): void
    {
        $t1 = Topic::where('slug', 'python-junior')->first();
        $t2 = Topic::where('slug', 'python-intermediate')->first();
        $t3 = Topic::where('slug', 'python-advanced')->first();
        $t4 = Topic::where('slug', 'python-level-4-stdlib')->first();
        $t5 = Topic::where('slug', 'python-level-5-expert')->first();

        $lessons = [

            // ── LEVEL 1 ────────────────────────────────────────────────────
            [
                'topic_id'          => $t1->id,
                'title'             => 'Variables, Data Types & Python\'s Type System',
                'estimated_minutes' => 15,
                'display_order'     => 1,
                'content'           => <<<'MARKDOWN'
## Variables in Python

Python variables are dynamically typed — you do not declare a type; the interpreter infers it from the value.

```python
name = "Alice"        # str
age  = 25             # int
price = 9.99          # float
active = True         # bool
nothing = None        # NoneType
```

Variables are just labels pointing to objects in memory. Reassigning changes what the label points to.

## Core Built-in Types

| Type    | Example                        | Notes                        |
|---------|--------------------------------|------------------------------|
| `int`   | `42`, `-7`, `1_000_000`        | arbitrary precision          |
| `float` | `3.14`, `1e10`, `float('inf')` | IEEE-754 double              |
| `bool`  | `True`, `False`                | subclass of int              |
| `str`   | `"hello"`, `'world'`, `"""..."""` | immutable, Unicode          |
| `bytes` | `b"data"`                      | raw binary                   |
| `None`  | `None`                         | singleton null value         |

## Strings

Strings are immutable sequences of Unicode characters.

```python
s = "Hello, World!"
print(s.upper())         # "HELLO, WORLD!"
print(s[0:5])            # "Hello"  — slicing
print(s.replace("o", "0"))  # "Hell0, W0rld!"
print(len(s))            # 13

# f-strings (Python 3.6+) — preferred for formatting
name = "Alice"
age = 25
print(f"{name} is {age} years old.")  # "Alice is 25 years old."
```

## Type Checking and Conversion

```python
type(42)         # <class 'int'>
isinstance(42, int)  # True

# Explicit conversion
int("42")        # 42
float("3.14")    # 3.14
str(100)         # "100"
bool(0)          # False
bool("hello")    # True
```

## Falsy Values

In Python, these values are considered falsy:
`False`, `0`, `0.0`, `0j`, `""`, `b""`, `[]`, `()`, `{}`, `set()`, `None`

Everything else is truthy.

```python
if []:      print("truthy")  # not printed — empty list is falsy
if [0]:     print("truthy")  # printed — list with one element is truthy
if "":      print("truthy")  # not printed — empty string is falsy
if "False": print("truthy")  # printed — non-empty string is truthy
```

## Multiple Assignment & Unpacking

```python
a, b = 1, 2          # tuple unpacking
a, b = b, a          # swap without temp variable
x, *rest = [1, 2, 3, 4]  # x=1, rest=[2, 3, 4]

# Constants — Python has no true constants; UPPER_CASE by convention
MAX_SIZE = 100
PI = 3.14159
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t1->id,
                'title'             => 'Control Flow: Conditionals, Loops & Comprehensions',
                'estimated_minutes' => 15,
                'display_order'     => 2,
                'content'           => <<<'MARKDOWN'
## Conditionals

```python
score = 85

if score >= 90:
    grade = 'A'
elif score >= 80:
    grade = 'B'
elif score >= 70:
    grade = 'C'
else:
    grade = 'F'

# Ternary expression
grade = 'pass' if score >= 60 else 'fail'
```

## Loops

**`for` loop** — iterates over any iterable:
```python
fruits = ["apple", "banana", "cherry"]
for fruit in fruits:
    print(fruit)

# range() generates a sequence of numbers
for i in range(5):      # 0, 1, 2, 3, 4
    print(i)

for i in range(2, 10, 2):  # 2, 4, 6, 8
    print(i)
```

**`while` loop** — runs while condition is true:
```python
count = 0
while count < 5:
    print(count)
    count += 1
```

**`break`, `continue`, `else`**:
```python
for i in range(10):
    if i == 3:
        continue    # skip 3
    if i == 7:
        break       # stop at 7
    print(i)

# for-else: the else runs if the loop was NOT broken
for n in [2, 4, 6]:
    if n % 2 != 0:
        break
else:
    print("All numbers are even")  # this runs
```

**`enumerate` and `zip`**:
```python
names = ["Alice", "Bob", "Carol"]
for i, name in enumerate(names, start=1):
    print(f"{i}. {name}")  # 1. Alice, 2. Bob, 3. Carol

scores = [90, 85, 92]
for name, score in zip(names, scores):
    print(f"{name}: {score}")
```

## List Comprehensions

A concise way to build lists:
```python
# [expression for item in iterable if condition]
squares = [x ** 2 for x in range(10)]          # [0, 1, 4, 9, 16, 25, 36, 49, 64, 81]
evens   = [x for x in range(20) if x % 2 == 0] # [0, 2, 4, ..., 18]
pairs   = [(x, y) for x in range(3) for y in range(3)]  # nested
```

**Dict and set comprehensions**:
```python
word_lengths = {word: len(word) for word in ["cat", "elephant", "dog"]}
# {'cat': 3, 'elephant': 8, 'dog': 3}

unique_lengths = {len(word) for word in ["cat", "elephant", "dog"]}
# {3, 8}
```

Generator expressions (lazy, memory-efficient):
```python
total = sum(x ** 2 for x in range(1000))  # no list created
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t1->id,
                'title'             => 'Functions, Arguments & Built-in Data Structures',
                'estimated_minutes' => 18,
                'display_order'     => 3,
                'content'           => <<<'MARKDOWN'
## Defining Functions

```python
def greet(name, greeting="Hello"):
    return f"{greeting}, {name}!"

greet("Alice")            # "Hello, Alice!"
greet("Bob", "Hi")        # "Hi, Bob!"
greet(greeting="Hey", name="Carol")  # keyword arguments
```

**`*args` and `**kwargs`**:
```python
def sum_all(*numbers):
    return sum(numbers)

sum_all(1, 2, 3, 4)   # 10

def print_info(**kwargs):
    for key, value in kwargs.items():
        print(f"{key}: {value}")

print_info(name="Alice", age=25)
```

## Lists

```python
numbers = [3, 1, 4, 1, 5, 9]

numbers.append(2)          # add to end
numbers.insert(0, 0)       # insert at index
numbers.remove(1)          # remove first occurrence of 1
popped = numbers.pop()     # remove and return last
numbers.sort()             # in-place sort
sorted_copy = sorted(numbers)  # returns new list
numbers.reverse()          # in-place reverse
numbers.index(4)           # index of first 4
numbers.count(1)           # count occurrences of 1
```

Slicing:
```python
lst = [0, 1, 2, 3, 4, 5]
lst[1:4]    # [1, 2, 3]
lst[:3]     # [0, 1, 2]
lst[3:]     # [3, 4, 5]
lst[::2]    # [0, 2, 4]   — step
lst[::-1]   # [5, 4, 3, 2, 1, 0]  — reversed
```

## Tuples & Sets

**Tuple** — immutable ordered sequence:
```python
point = (3, 4)
x, y = point            # unpacking

# Named tuples (more readable)
from collections import namedtuple
Point = namedtuple('Point', ['x', 'y'])
p = Point(3, 4)
p.x  # 3
```

**Set** — unordered, unique elements:
```python
s = {1, 2, 3, 2, 1}     # {1, 2, 3}
s.add(4)
s.discard(1)             # no error if not present
a = {1, 2, 3}
b = {2, 3, 4}
a | b   # union:        {1, 2, 3, 4}
a & b   # intersection: {2, 3}
a - b   # difference:   {1}
a ^ b   # symmetric difference: {1, 4}
```

## Dictionaries

```python
user = {"name": "Alice", "age": 25}
user["email"] = "alice@example.com"   # add/update
user.get("phone", "N/A")              # safe get with default
del user["age"]                       # delete key
"name" in user                        # True

# Iterating
for key in user:           print(key)
for val in user.values():  print(val)
for k, v in user.items():  print(k, v)

# Merging (Python 3.9+)
merged = {"a": 1} | {"b": 2}   # {'a': 1, 'b': 2}
```

Dictionary comprehension:
```python
squares = {x: x**2 for x in range(5)}  # {0: 0, 1: 1, 2: 4, 3: 9, 4: 16}
```
MARKDOWN,
            ],

            // ── LEVEL 2 ────────────────────────────────────────────────────
            [
                'topic_id'          => $t2->id,
                'title'             => 'Object-Oriented Python: Classes, Inheritance & Magic Methods',
                'estimated_minutes' => 18,
                'display_order'     => 1,
                'content'           => <<<'MARKDOWN'
## Defining Classes

```python
class Animal:
    # Class variable — shared by all instances
    kingdom = "Animalia"

    def __init__(self, name, sound):
        # Instance variables — unique per object
        self.name  = name
        self._sound = sound   # convention: "protected"

    def speak(self):
        return f"{self.name} says {self._sound}"

    def __repr__(self):
        return f"Animal(name={self.name!r})"

    @classmethod
    def from_dict(cls, data):
        return cls(data["name"], data["sound"])

    @staticmethod
    def is_valid_name(name):
        return isinstance(name, str) and len(name) > 0

dog = Animal("Rex", "woof")
dog.speak()           # "Rex says woof"
Animal.kingdom        # "Animalia"
Animal.is_valid_name("Rex")  # True
```

## Inheritance

```python
class Dog(Animal):
    def __init__(self, name, breed):
        super().__init__(name, "woof")
        self.breed = breed

    def speak(self):           # override
        return f"{super().speak()}!"

    def fetch(self, item):
        return f"{self.name} fetches the {item}"

rex = Dog("Rex", "Labrador")
rex.speak()           # "Rex says woof!"
isinstance(rex, Dog)    # True
isinstance(rex, Animal) # True
```

## Magic Methods (Dunder Methods)

Magic methods let your classes integrate with Python's built-in protocols:

```python
class Vector:
    def __init__(self, x, y):
        self.x, self.y = x, y

    def __repr__(self):          # repr() — useful for debugging
        return f"Vector({self.x}, {self.y})"

    def __str__(self):           # str() — user-facing string
        return f"({self.x}, {self.y})"

    def __add__(self, other):    # +
        return Vector(self.x + other.x, self.y + other.y)

    def __len__(self):           # len()
        return 2

    def __eq__(self, other):     # ==
        return self.x == other.x and self.y == other.y

    def __lt__(self, other):     # < (enables sorting)
        return (self.x**2 + self.y**2) < (other.x**2 + other.y**2)

    def __getitem__(self, index):  # v[0], v[1]
        return (self.x, self.y)[index]

v1 = Vector(1, 2)
v2 = Vector(3, 4)
v1 + v2     # Vector(4, 6)
len(v1)     # 2
v1[0]       # 1
```

## Properties

```python
class Temperature:
    def __init__(self, celsius):
        self._celsius = celsius

    @property
    def celsius(self):
        return self._celsius

    @celsius.setter
    def celsius(self, value):
        if value < -273.15:
            raise ValueError("Temperature below absolute zero!")
        self._celsius = value

    @property
    def fahrenheit(self):
        return self._celsius * 9/5 + 32

t = Temperature(100)
t.fahrenheit    # 212.0
t.celsius = 0
t.fahrenheit    # 32.0
```

## Abstract Classes

```python
from abc import ABC, abstractmethod

class Shape(ABC):
    @abstractmethod
    def area(self) -> float: ...

    @abstractmethod
    def perimeter(self) -> float: ...

class Circle(Shape):
    def __init__(self, radius):
        self.radius = radius

    def area(self):
        return 3.14159 * self.radius ** 2

    def perimeter(self):
        return 2 * 3.14159 * self.radius
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t2->id,
                'title'             => 'File I/O, Exceptions & Context Managers',
                'estimated_minutes' => 15,
                'display_order'     => 2,
                'content'           => <<<'MARKDOWN'
## Reading and Writing Files

```python
# Context manager ensures file is always closed
with open("data.txt", "r") as f:
    content = f.read()       # read entire file as string

with open("data.txt", "r") as f:
    lines = f.readlines()    # list of lines

with open("data.txt", "r") as f:
    for line in f:           # memory-efficient line-by-line
        print(line.strip())

# Writing
with open("output.txt", "w") as f:   # "w" overwrites, "a" appends
    f.write("Hello, World!\n")
    f.writelines(["line 1\n", "line 2\n"])
```

**Modes**: `"r"` (read), `"w"` (write/overwrite), `"a"` (append), `"rb"` / `"wb"` (binary).

## Working with JSON and CSV

```python
import json

# Write JSON
data = {"name": "Alice", "scores": [90, 85, 92]}
with open("data.json", "w") as f:
    json.dump(data, f, indent=2)

# Read JSON
with open("data.json") as f:
    loaded = json.load(f)

# String conversions
json.dumps(data)           # dict → JSON string
json.loads('{"key": 1}')  # JSON string → dict

# CSV
import csv
with open("users.csv", "w", newline="") as f:
    writer = csv.DictWriter(f, fieldnames=["name", "age"])
    writer.writeheader()
    writer.writerow({"name": "Alice", "age": 25})
```

## Exception Handling

```python
try:
    result = 10 / int(input("Enter divisor: "))
except ZeroDivisionError:
    print("Cannot divide by zero")
except ValueError as e:
    print(f"Invalid input: {e}")
except (TypeError, RuntimeError) as e:
    print(f"Error: {e}")
else:
    print(f"Result: {result}")    # runs if no exception
finally:
    print("Always runs")          # cleanup goes here
```

**Raising exceptions**:
```python
def divide(a, b):
    if b == 0:
        raise ValueError("Divisor cannot be zero")
    return a / b

# Custom exception
class InsufficientFundsError(Exception):
    def __init__(self, amount, balance):
        super().__init__(f"Cannot withdraw {amount}, balance is {balance}")
        self.amount  = amount
        self.balance = balance
```

## Context Managers

The `with` statement guarantees cleanup via `__enter__` and `__exit__`:

```python
class Timer:
    import time

    def __enter__(self):
        self.start = __import__('time').time()
        return self

    def __exit__(self, exc_type, exc_val, exc_tb):
        self.elapsed = __import__('time').time() - self.start
        print(f"Elapsed: {self.elapsed:.3f}s")
        return False   # don't suppress exceptions

with Timer():
    # expensive work here
    pass
```

**Using `contextlib`** for simpler context managers:
```python
from contextlib import contextmanager

@contextmanager
def managed_resource():
    resource = acquire_resource()
    try:
        yield resource
    finally:
        release_resource(resource)
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t2->id,
                'title'             => 'Modules, Packages & the Standard Library',
                'estimated_minutes' => 15,
                'display_order'     => 3,
                'content'           => <<<'MARKDOWN'
## Modules

A module is any Python file. Import it with `import`:

```python
# math_utils.py
def add(a, b):
    return a + b

PI = 3.14159
```

```python
# main.py
import math_utils
math_utils.add(1, 2)   # 3

from math_utils import add, PI
add(1, 2)              # 3

from math_utils import add as plus
plus(1, 2)             # 3

import math_utils as mu
mu.PI                  # 3.14159
```

## Packages

A package is a directory with `__init__.py`. Nested packages create a hierarchy:

```
my_app/
  __init__.py
  utils/
    __init__.py
    string_utils.py
    math_utils.py
```

```python
from my_app.utils.string_utils import slugify
```

## `__name__ == "__main__"`

```python
def main():
    print("Running directly")

if __name__ == "__main__":
    main()    # only runs when this file is executed directly, not when imported
```

## Essential Standard Library Modules

**`os` and `pathlib`**:
```python
import os
from pathlib import Path

p = Path("data") / "users" / "alice.json"
p.exists()          # True/False
p.suffix            # ".json"
p.stem              # "alice"
p.parent            # Path("data/users")
p.read_text()       # file contents
p.write_text("...")

os.environ.get("DATABASE_URL", "sqlite:///dev.db")
```

**`collections`**:
```python
from collections import Counter, defaultdict, deque, OrderedDict

# Counter — count occurrences
words = ["apple", "banana", "apple", "cherry"]
c = Counter(words)    # Counter({'apple': 2, 'banana': 1, 'cherry': 1})
c.most_common(2)      # [('apple', 2), ('banana', 1)]

# defaultdict — never raises KeyError
freq = defaultdict(int)
for word in words:
    freq[word] += 1

# deque — efficient prepend/append at both ends
q = deque([1, 2, 3])
q.appendleft(0)       # [0, 1, 2, 3]
q.popleft()           # 0
```

**`itertools`**:
```python
from itertools import chain, product, combinations, permutations, groupby

list(chain([1, 2], [3, 4]))              # [1, 2, 3, 4]
list(combinations("ABC", 2))            # [('A','B'), ('A','C'), ('B','C')]
list(permutations([1, 2, 3], 2))        # [(1,2), (1,3), (2,1), ...]
```

**`functools`**:
```python
from functools import reduce, partial, lru_cache, wraps

@lru_cache(maxsize=128)
def fibonacci(n):
    return n if n < 2 else fibonacci(n-1) + fibonacci(n-2)

double = partial(lambda factor, x: x * factor, 2)
double(5)  # 10
```
MARKDOWN,
            ],

            // ── LEVEL 3 ────────────────────────────────────────────────────
            [
                'topic_id'          => $t3->id,
                'title'             => 'Decorators & Functional Programming',
                'estimated_minutes' => 18,
                'display_order'     => 1,
                'content'           => <<<'MARKDOWN'
## First-Class Functions

In Python, functions are first-class objects — they can be passed as arguments, returned from functions, and stored in variables.

```python
def apply(func, value):
    return func(value)

apply(str.upper, "hello")    # "HELLO"
apply(len, [1, 2, 3])       # 3
```

## Closures

A closure is a function that captures variables from its enclosing scope:

```python
def multiplier(factor):
    def multiply(number):
        return number * factor    # captures 'factor'
    return multiply

double = multiplier(2)
triple = multiplier(3)
double(5)   # 10
triple(5)   # 15
```

## Decorators

A decorator is a function that wraps another function to add behaviour:

```python
def timer(func):
    import time
    from functools import wraps

    @wraps(func)                    # preserves the original function's metadata
    def wrapper(*args, **kwargs):
        start  = time.perf_counter()
        result = func(*args, **kwargs)
        end    = time.perf_counter()
        print(f"{func.__name__} took {end - start:.4f}s")
        return result
    return wrapper

@timer
def slow_function():
    import time
    time.sleep(0.1)
    return "done"

slow_function()  # prints timing, returns "done"
```

## Parametrised Decorators

A decorator factory returns a decorator:

```python
def retry(times=3, exceptions=(Exception,)):
    def decorator(func):
        from functools import wraps

        @wraps(func)
        def wrapper(*args, **kwargs):
            for attempt in range(1, times + 1):
                try:
                    return func(*args, **kwargs)
                except exceptions as e:
                    if attempt == times:
                        raise
                    print(f"Retry {attempt}/{times}: {e}")
        return wrapper
    return decorator

@retry(times=5, exceptions=(ConnectionError,))
def fetch_data(url):
    ...
```

## Class Decorators and `functools.wraps`

Always use `@wraps(func)` inside a decorator — it copies the original function's `__name__`, `__doc__`, and other attributes so introspection tools and tests work correctly.

## `map`, `filter`, `reduce`

```python
from functools import reduce

nums = [1, 2, 3, 4, 5]

# map — transform each element
list(map(lambda x: x * 2, nums))     # [2, 4, 6, 8, 10]

# filter — keep elements that pass the test
list(filter(lambda x: x % 2 == 0, nums))  # [2, 4]

# reduce — fold to a single value
reduce(lambda acc, x: acc + x, nums)  # 15

# Prefer comprehensions/generator expressions over map/filter in Python
[x * 2 for x in nums]
[x for x in nums if x % 2 == 0]
```

## `lambda`

Anonymous inline functions — keep them simple:

```python
sorted(users, key=lambda u: (u["age"], u["name"]))
min(words, key=len)
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t3->id,
                'title'             => 'Generators, Iterators & Memory Efficiency',
                'estimated_minutes' => 18,
                'display_order'     => 2,
                'content'           => <<<'MARKDOWN'
## The Iterator Protocol

An **iterator** is any object with `__iter__()` and `__next__()` methods. Calling `next()` on it advances to the next value; when exhausted it raises `StopIteration`.

```python
class CountUp:
    def __init__(self, limit):
        self.current = 0
        self.limit   = limit

    def __iter__(self):
        return self

    def __next__(self):
        if self.current >= self.limit:
            raise StopIteration
        self.current += 1
        return self.current

for n in CountUp(5):
    print(n)  # 1 2 3 4 5
```

## Generator Functions

Generators are the Pythonic way to create iterators. Use `yield` instead of `return`:

```python
def count_up(limit):
    current = 1
    while current <= limit:
        yield current
        current += 1

for n in count_up(5):
    print(n)   # 1 2 3 4 5

# Generators are lazy — values produced only when requested
gen = count_up(1_000_000)
next(gen)   # 1  — only one value computed
```

## Generator Expressions

```python
# List comprehension — all values in memory at once
squares_list = [x**2 for x in range(1_000_000)]   # uses ~8 MB

# Generator expression — lazy, O(1) memory
squares_gen  = (x**2 for x in range(1_000_000))   # uses negligible memory

total = sum(x**2 for x in range(1_000_000))        # no intermediate list
```

## Practical Generator Patterns

**Pipeline of generators**:
```python
def read_lines(filename):
    with open(filename) as f:
        yield from f

def filter_empty(lines):
    for line in lines:
        stripped = line.strip()
        if stripped:
            yield stripped

def parse_csv_row(lines):
    for line in lines:
        yield line.split(",")

# Process a 10 GB file with constant memory
pipeline = parse_csv_row(filter_empty(read_lines("huge.csv")))
for row in pipeline:
    process(row)
```

**`yield from`** — delegate to a sub-generator:
```python
def flatten(nested):
    for item in nested:
        if isinstance(item, list):
            yield from flatten(item)   # recursively flatten
        else:
            yield item

list(flatten([1, [2, [3, 4]], 5]))  # [1, 2, 3, 4, 5]
```

## `itertools` for Generator Pipelines

```python
from itertools import islice, takewhile, dropwhile, count, cycle

# Infinite counter — take first 5
list(islice(count(10), 5))       # [10, 11, 12, 13, 14]

# Take while condition holds
list(takewhile(lambda x: x < 5, [1, 2, 3, 6, 4]))   # [1, 2, 3]

# Cycle through a list indefinitely
gen = cycle(["A", "B", "C"])
[next(gen) for _ in range(7)]    # ['A','B','C','A','B','C','A']
```

## `send()` — Two-Way Generators

Generators can receive values via `send()`:
```python
def accumulator():
    total = 0
    while True:
        value = yield total   # yield current total, receive next value
        if value is None:
            break
        total += value

acc = accumulator()
next(acc)       # prime the generator — outputs 0
acc.send(10)    # total = 10
acc.send(20)    # total = 30
acc.send(5)     # total = 35
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t3->id,
                'title'             => 'Concurrency: Threading, Multiprocessing & the GIL',
                'estimated_minutes' => 20,
                'display_order'     => 3,
                'content'           => <<<'MARKDOWN'
## The Global Interpreter Lock (GIL)

The GIL is a mutex in CPython that prevents multiple native threads from executing Python bytecode simultaneously. Only one thread runs Python code at a time.

**Consequences**:
- Threading is good for **I/O-bound** tasks (network calls, file reads) — threads can release the GIL while waiting.
- Threading does NOT provide parallelism for **CPU-bound** tasks — only one thread runs at a time.
- Use multiprocessing for CPU parallelism — each process has its own interpreter and GIL.

## Threading

```python
import threading
import time

def download(url):
    print(f"Starting {url}")
    time.sleep(2)    # simulates I/O wait
    print(f"Done {url}")

urls = ["https://a.com", "https://b.com", "https://c.com"]

threads = [threading.Thread(target=download, args=(url,)) for url in urls]
for t in threads:
    t.start()
for t in threads:
    t.join()   # wait for all to complete

# All 3 downloads run "concurrently" — total ~2s instead of 6s
```

**Thread safety — use a Lock**:
```python
lock = threading.Lock()
counter = 0

def increment():
    global counter
    for _ in range(100_000):
        with lock:        # acquire → release automatically
            counter += 1
```

## `concurrent.futures` — Higher-Level API

```python
from concurrent.futures import ThreadPoolExecutor, ProcessPoolExecutor, as_completed

# ThreadPoolExecutor for I/O-bound work
with ThreadPoolExecutor(max_workers=8) as executor:
    futures = {executor.submit(fetch, url): url for url in urls}
    for future in as_completed(futures):
        url = futures[future]
        try:
            result = future.result()
        except Exception as e:
            print(f"{url} failed: {e}")

# ProcessPoolExecutor for CPU-bound work
def cpu_intensive(n):
    return sum(i * i for i in range(n))

with ProcessPoolExecutor() as executor:
    results = list(executor.map(cpu_intensive, [10**6, 10**6, 10**6]))
```

## Multiprocessing

```python
from multiprocessing import Pool, Queue, Process

def worker(n):
    return n * n

with Pool(processes=4) as pool:
    results = pool.map(worker, range(10))
    # [0, 1, 4, 9, 16, 25, 36, 49, 64, 81]
```

**Shared state** between processes requires explicit mechanisms:
```python
from multiprocessing import Value, Array

counter = Value('i', 0)   # shared integer
arr = Array('d', range(10))  # shared array of doubles
```

## Queue for Producer-Consumer

```python
import threading
import queue

q = queue.Queue()

def producer():
    for i in range(5):
        q.put(i)
    q.put(None)    # sentinel to stop consumer

def consumer():
    while True:
        item = q.get()
        if item is None:
            break
        print(f"Processing {item}")

t1 = threading.Thread(target=producer)
t2 = threading.Thread(target=consumer)
t1.start(); t2.start()
t1.join();  t2.join()
```

## Summary: When to Use What

| Task type     | Recommended tool           |
|---------------|----------------------------|
| I/O-bound     | `threading`, `asyncio`     |
| CPU-bound     | `multiprocessing`          |
| Simple tasks  | `concurrent.futures`       |
| Event-driven  | `asyncio`                  |
MARKDOWN,
            ],

            // ── LEVEL 4 ────────────────────────────────────────────────────
            [
                'topic_id'          => $t4->id,
                'title'             => 'Type Hints, Dataclasses & Modern Python',
                'estimated_minutes' => 18,
                'display_order'     => 1,
                'content'           => <<<'MARKDOWN'
## Type Hints (PEP 484+)

Python's type hints are optional annotations checked by tools like `mypy` and `pyright` — they have no runtime effect.

```python
def greet(name: str, times: int = 1) -> str:
    return f"Hello, {name}! " * times

greet("Alice", 3)    # type-correct
greet(42, "three")   # mypy error (still runs — hints are not enforced at runtime)
```

## Common Type Annotations

```python
from typing import Optional, Union, Any, Callable, TypeVar

# Optional[X] is Union[X, None]
def find(name: str) -> Optional[str]:
    ...

# Union
def process(value: Union[int, str]) -> str:
    return str(value)

# Python 3.10+: use | instead of Union
def process(value: int | str) -> str: ...

# Collections
from typing import List, Dict, Tuple, Set
def stats(nums: list[int]) -> dict[str, float]:   # Python 3.9+ — no import needed
    return {"mean": sum(nums) / len(nums)}
```

## TypeVar and Generics

```python
from typing import TypeVar, Generic

T = TypeVar('T')

def first(items: list[T]) -> T:
    return items[0]

first([1, 2, 3])    # inferred as int
first(["a", "b"])   # inferred as str

class Stack(Generic[T]):
    def __init__(self) -> None:
        self._items: list[T] = []

    def push(self, item: T) -> None:
        self._items.append(item)

    def pop(self) -> T:
        return self._items.pop()
```

## Dataclasses

`@dataclass` auto-generates `__init__`, `__repr__`, and `__eq__`:

```python
from dataclasses import dataclass, field

@dataclass
class User:
    name:  str
    email: str
    age:   int = 0
    tags:  list[str] = field(default_factory=list)

    def is_adult(self) -> bool:
        return self.age >= 18

alice = User(name="Alice", email="alice@example.com", age=25)
alice                    # User(name='Alice', email='alice@example.com', age=25, tags=[])
alice == User("Alice", "alice@example.com", 25)  # True — __eq__ by field values
```

**Frozen dataclasses** (immutable — hashable):
```python
@dataclass(frozen=True)
class Point:
    x: float
    y: float

p = Point(1.0, 2.0)
# p.x = 5.0  # FrozenInstanceError
{p}   # usable in a set
```

**`__post_init__`** for validation:
```python
@dataclass
class Product:
    name:  str
    price: float

    def __post_init__(self):
        if self.price < 0:
            raise ValueError("Price cannot be negative")
```

## Protocol (Structural Subtyping)

```python
from typing import Protocol

class Drawable(Protocol):
    def draw(self) -> None: ...

class Circle:
    def draw(self) -> None:
        print("○")

def render(shape: Drawable) -> None:
    shape.draw()

render(Circle())   # works — Circle satisfies Drawable without explicit inheritance
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t4->id,
                'title'             => 'Data Structures & Algorithms in Python',
                'estimated_minutes' => 20,
                'display_order'     => 2,
                'content'           => <<<'MARKDOWN'
## Built-in Data Structures — Complexity

| Structure  | Access  | Search  | Insert (end) | Delete |
|------------|---------|---------|--------------|--------|
| `list`     | O(1)    | O(n)    | O(1) amort.  | O(n)   |
| `dict`     | O(1)    | O(1)    | O(1)         | O(1)   |
| `set`      | —       | O(1)    | O(1)         | O(1)   |
| `deque`    | O(n)    | O(n)    | O(1) both ends | O(1) both ends |

## heapq — Priority Queue

Python's `heapq` is a **min-heap** (smallest element at index 0):

```python
import heapq

nums = [3, 1, 4, 1, 5, 9, 2]
heapq.heapify(nums)        # in-place, O(n)
heapq.heappush(nums, 0)    # O(log n)
heapq.heappop(nums)        # 0 — removes and returns smallest, O(log n)

# K largest / K smallest
heapq.nlargest(3, nums)    # [9, 5, 4]
heapq.nsmallest(3, nums)   # [1, 1, 2]

# Max-heap trick: negate values
max_heap = [-n for n in [3, 1, 4, 1, 5]]
heapq.heapify(max_heap)
-heapq.heappop(max_heap)   # 5
```

## bisect — Binary Search on Sorted Lists

```python
import bisect

sorted_list = [1, 3, 5, 7, 9]
bisect.bisect_left(sorted_list, 5)   # 2 — index where 5 would be inserted (leftmost)
bisect.bisect_right(sorted_list, 5)  # 3 — right of existing 5
bisect.insort(sorted_list, 6)        # insert 6 in sorted order
```

## Sorting

```python
# Timsort — O(n log n), stable
nums = [3, 1, 4, 1, 5]
sorted(nums)              # new list
nums.sort()               # in-place

# Key function
users = [{"name": "Charlie", "age": 30}, {"name": "Alice", "age": 25}]
sorted(users, key=lambda u: u["age"])
sorted(users, key=lambda u: (u["age"], u["name"]))  # multi-key

# operator module
from operator import itemgetter, attrgetter
sorted(users, key=itemgetter("age"))
```

## Common Algorithm Patterns

**Two pointers**:
```python
def two_sum_sorted(nums: list[int], target: int) -> tuple[int, int]:
    left, right = 0, len(nums) - 1
    while left < right:
        s = nums[left] + nums[right]
        if s == target:
            return (left, right)
        elif s < target:
            left += 1
        else:
            right -= 1
    return (-1, -1)
```

**Sliding window**:
```python
def max_subarray_sum(nums: list[int], k: int) -> int:
    window_sum = sum(nums[:k])
    max_sum    = window_sum
    for i in range(k, len(nums)):
        window_sum += nums[i] - nums[i - k]
        max_sum = max(max_sum, window_sum)
    return max_sum
```

**BFS with deque**:
```python
from collections import deque

def bfs(graph: dict, start: str) -> list[str]:
    visited, queue, result = {start}, deque([start]), []
    while queue:
        node = queue.popleft()
        result.append(node)
        for neighbour in graph.get(node, []):
            if neighbour not in visited:
                visited.add(neighbour)
                queue.append(neighbour)
    return result
```

**Memoisation with `@lru_cache`**:
```python
from functools import lru_cache

@lru_cache(maxsize=None)
def fib(n: int) -> int:
    return n if n < 2 else fib(n-1) + fib(n-2)

fib(100)  # fast — O(n) with caching
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t4->id,
                'title'             => 'Database Access: SQLAlchemy & Raw SQL',
                'estimated_minutes' => 18,
                'display_order'     => 3,
                'content'           => <<<'MARKDOWN'
## SQLite with the Standard Library

```python
import sqlite3

conn = sqlite3.connect("app.db")
conn.row_factory = sqlite3.Row   # results as dict-like objects

cursor = conn.cursor()
cursor.execute("""
    CREATE TABLE IF NOT EXISTS users (
        id    INTEGER PRIMARY KEY AUTOINCREMENT,
        name  TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL
    )
""")
conn.commit()

# Always use parameterised queries — never string interpolation (SQL injection)
cursor.execute("INSERT INTO users (name, email) VALUES (?, ?)", ("Alice", "alice@example.com"))
conn.commit()

cursor.execute("SELECT * FROM users WHERE name = ?", ("Alice",))
row = cursor.fetchone()
print(dict(row))   # {'id': 1, 'name': 'Alice', 'email': 'alice@example.com'}

conn.close()

# Context manager — auto-commits or rolls back
with sqlite3.connect("app.db") as conn:
    conn.execute("INSERT INTO users (name, email) VALUES (?, ?)", ("Bob", "bob@example.com"))
```

## SQLAlchemy Core

SQLAlchemy Core lets you write SQL with Python constructs:

```python
from sqlalchemy import create_engine, Table, Column, Integer, String, MetaData, select

engine = MetaData()
meta   = MetaData()

users = Table('users', meta,
    Column('id',    Integer, primary_key=True),
    Column('name',  String(50), nullable=False),
    Column('email', String(100), unique=True),
)

engine = create_engine("sqlite:///app.db")
meta.create_all(engine)

with engine.connect() as conn:
    conn.execute(users.insert(), {"name": "Alice", "email": "alice@example.com"})
    conn.commit()

    result = conn.execute(select(users).where(users.c.name == "Alice"))
    for row in result:
        print(row)
```

## SQLAlchemy ORM

```python
from sqlalchemy import create_engine
from sqlalchemy.orm import DeclarativeBase, Mapped, mapped_column, Session

class Base(DeclarativeBase):
    pass

class User(Base):
    __tablename__ = "users"

    id:    Mapped[int]  = mapped_column(primary_key=True)
    name:  Mapped[str]  = mapped_column(nullable=False)
    email: Mapped[str]  = mapped_column(unique=True)

    def __repr__(self):
        return f"User(id={self.id}, name={self.name!r})"

engine = create_engine("sqlite:///app.db")
Base.metadata.create_all(engine)

with Session(engine) as session:
    alice = User(name="Alice", email="alice@example.com")
    session.add(alice)
    session.commit()

    users = session.query(User).filter(User.name.like("A%")).all()
    # Or modern style:
    from sqlalchemy import select
    stmt  = select(User).where(User.name.startswith("A"))
    users = session.scalars(stmt).all()
```

## Connection Pooling

SQLAlchemy automatically pools connections. Configure via engine parameters:

```python
engine = create_engine(
    "postgresql://user:pass@localhost/mydb",
    pool_size=10,
    max_overflow=20,
    pool_timeout=30,
    pool_recycle=1800,  # recycle connections after 30 minutes
)
```
MARKDOWN,
            ],

            // ── LEVEL 5 ────────────────────────────────────────────────────
            [
                'topic_id'          => $t5->id,
                'title'             => 'Async Python: asyncio, aiohttp & Concurrency Patterns',
                'estimated_minutes' => 20,
                'display_order'     => 1,
                'content'           => <<<'MARKDOWN'
## asyncio Fundamentals

`asyncio` is Python's built-in library for writing concurrent code using coroutines. It is ideal for I/O-bound work: HTTP calls, database queries, file reads.

```python
import asyncio

async def greet(name: str, delay: float) -> str:
    await asyncio.sleep(delay)    # yield control back to the event loop
    return f"Hello, {name}!"

async def main():
    result = await greet("Alice", 1)
    print(result)

asyncio.run(main())
```

## Concurrent Coroutines

Run multiple coroutines concurrently with `asyncio.gather`:

```python
import asyncio
import time

async def fetch(url: str) -> str:
    await asyncio.sleep(1)    # simulates network I/O
    return f"Data from {url}"

async def main():
    start = time.perf_counter()

    # Sequential — takes 3 seconds
    # for url in urls: result = await fetch(url)

    # Concurrent — takes ~1 second
    results = await asyncio.gather(
        fetch("https://a.com"),
        fetch("https://b.com"),
        fetch("https://c.com"),
    )

    elapsed = time.perf_counter() - start
    print(f"{results} in {elapsed:.2f}s")

asyncio.run(main())
```

## Tasks and `create_task`

```python
async def main():
    # Create tasks — they start immediately
    task1 = asyncio.create_task(fetch("https://a.com"))
    task2 = asyncio.create_task(fetch("https://b.com"))

    # Do other work while tasks run...

    result1 = await task1
    result2 = await task2
```

## asyncio.gather vs asyncio.wait vs TaskGroup

```python
# gather — simple, returns results in order
results = await asyncio.gather(*coros, return_exceptions=True)

# wait — more control (FIRST_COMPLETED, FIRST_EXCEPTION)
done, pending = await asyncio.wait(tasks, timeout=5)

# TaskGroup (Python 3.11+) — structured concurrency, cancels all on failure
async with asyncio.TaskGroup() as tg:
    task1 = tg.create_task(fetch("a"))
    task2 = tg.create_task(fetch("b"))
# tasks complete here — exceptions propagate cleanly
```

## aiohttp — Async HTTP Client

```python
import aiohttp
import asyncio

async def fetch_json(session: aiohttp.ClientSession, url: str) -> dict:
    async with session.get(url) as response:
        response.raise_for_status()
        return await response.json()

async def main():
    async with aiohttp.ClientSession() as session:
        urls = ["https://api.example.com/users/1", "https://api.example.com/users/2"]
        tasks = [fetch_json(session, url) for url in urls]
        results = await asyncio.gather(*tasks)
        for r in results:
            print(r)
```

## Async Context Managers and Iterators

```python
class AsyncDatabase:
    async def __aenter__(self):
        await self.connect()
        return self

    async def __aexit__(self, *args):
        await self.close()

async with AsyncDatabase() as db:
    await db.query("SELECT 1")

# Async iterator
async def stream_records():
    for i in range(10):
        await asyncio.sleep(0.1)
        yield {"id": i}

async for record in stream_records():
    print(record)
```

## Semaphore — Rate Limiting

```python
async def fetch_limited(session, url, semaphore):
    async with semaphore:
        return await fetch_json(session, url)

async def main():
    semaphore = asyncio.Semaphore(5)   # max 5 concurrent requests
    async with aiohttp.ClientSession() as session:
        tasks = [fetch_limited(session, url, semaphore) for url in urls]
        results = await asyncio.gather(*tasks)
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t5->id,
                'title'             => 'Testing with pytest & Test-Driven Development',
                'estimated_minutes' => 20,
                'display_order'     => 2,
                'content'           => <<<'MARKDOWN'
## pytest Basics

pytest discovers tests in files matching `test_*.py` or `*_test.py`, in functions prefixed with `test_`.

```python
# calculator.py
def add(a: int, b: int) -> int:
    return a + b

def divide(a: float, b: float) -> float:
    if b == 0:
        raise ZeroDivisionError("Cannot divide by zero")
    return a / b
```

```python
# test_calculator.py
import pytest
from calculator import add, divide

def test_add_positive():
    assert add(2, 3) == 5

def test_add_negative():
    assert add(-1, 1) == 0

def test_divide_raises():
    with pytest.raises(ZeroDivisionError, match="Cannot divide by zero"):
        divide(10, 0)

def test_divide():
    assert divide(10, 4) == 2.5
```

Run: `pytest -v` (verbose) or `pytest -x` (stop on first failure).

## Fixtures

Fixtures provide reusable setup and teardown:

```python
import pytest
from myapp.db import Database

@pytest.fixture
def db():
    database = Database(":memory:")
    database.create_tables()
    yield database           # test runs here
    database.close()         # teardown

@pytest.fixture
def sample_user(db):
    user = db.create_user(name="Alice", email="alice@example.com")
    return user

def test_user_creation(sample_user):
    assert sample_user.name == "Alice"

def test_user_email(sample_user):
    assert "@" in sample_user.email
```

**Scope**: `@pytest.fixture(scope="module")` — share fixture across all tests in a module. Also: `"session"`, `"class"`, `"function"` (default).

## Parametrize

Test multiple inputs without code duplication:

```python
@pytest.mark.parametrize("a,b,expected", [
    (2, 3, 5),
    (-1, 1, 0),
    (0, 0, 0),
    (100, -100, 0),
])
def test_add(a, b, expected):
    assert add(a, b) == expected
```

## Mocking with `unittest.mock`

```python
from unittest.mock import MagicMock, patch, AsyncMock

# Patch an external dependency
def test_send_email(mocker):
    mock_smtp = mocker.patch("myapp.email.smtplib.SMTP")
    mock_smtp.return_value.__enter__.return_value.sendmail = MagicMock()

    from myapp.email import send_email
    send_email("alice@example.com", "Hello")

    mock_smtp.return_value.__enter__.return_value.sendmail.assert_called_once()

# Using patch as context manager
def test_fetch_user():
    with patch("myapp.api.requests.get") as mock_get:
        mock_get.return_value.json.return_value = {"id": 1, "name": "Alice"}
        mock_get.return_value.status_code = 200

        from myapp.api import get_user
        user = get_user(1)

    assert user["name"] == "Alice"
```

## Async Tests

```python
import pytest
import pytest_asyncio

@pytest.mark.asyncio
async def test_async_fetch():
    result = await fetch_data("https://api.example.com/users/1")
    assert result["id"] == 1
```

## Test-Driven Development (TDD)

TDD cycle: **Red → Green → Refactor**

1. **Red**: write a failing test for a feature that does not exist yet
2. **Green**: write the minimum code to make the test pass
3. **Refactor**: clean up the code while keeping tests green

```python
# Step 1 — Red: test first (Stack doesn't exist yet)
def test_stack_push_pop():
    stack = Stack()
    stack.push(1)
    stack.push(2)
    assert stack.pop() == 2
    assert stack.pop() == 1

# Step 2 — Green: minimal implementation
class Stack:
    def __init__(self):
        self._items = []
    def push(self, item):
        self._items.append(item)
    def pop(self):
        return self._items.pop()

# Step 3 — Refactor: add is_empty, size, type hints, etc.
```

Benefits: forces clear requirements, provides a safety net for refactoring, and drives better API design.
MARKDOWN,
            ],
            [
                'topic_id'          => $t5->id,
                'title'             => 'Packaging, Virtual Environments & the Python Ecosystem',
                'estimated_minutes' => 18,
                'display_order'     => 3,
                'content'           => <<<'MARKDOWN'
## Virtual Environments

Virtual environments isolate project dependencies from the system Python:

```bash
# Create
python -m venv .venv

# Activate
source .venv/bin/activate          # Linux/Mac
.venv\Scripts\activate             # Windows

# Deactivate
deactivate
```

**`requirements.txt`** — pin exact versions for reproducibility:
```bash
pip freeze > requirements.txt      # capture current state
pip install -r requirements.txt    # restore from snapshot
```

## pyproject.toml — Modern Project Standard

`pyproject.toml` (PEP 517/518) is the modern way to define projects:

```toml
[build-system]
requires      = ["setuptools>=68", "wheel"]
build-backend = "setuptools.backends.legacy:build"

[project]
name        = "my-package"
version     = "1.0.0"
description = "A great package"
requires-python = ">=3.11"
dependencies = [
    "requests>=2.31",
    "pydantic>=2.0",
]

[project.optional-dependencies]
dev = ["pytest", "mypy", "ruff"]

[project.scripts]
my-tool = "my_package.cli:main"
```

## pip and pip-tools

```bash
pip install package-name
pip install "package>=1.0,<2.0"
pip install -e .                    # editable install (development mode)
pip list
pip show package-name
pip uninstall package-name
```

**`pip-tools`** for deterministic dependency resolution:
```bash
pip install pip-tools
# requirements.in  ← your direct dependencies
# requirements.txt ← pip-compile generates this (full lockfile)
pip-compile requirements.in
pip-sync requirements.txt
```

## Project Structure Best Practices

```
my_project/
  src/
    my_package/
      __init__.py
      core.py
      utils.py
  tests/
    test_core.py
    test_utils.py
  pyproject.toml
  README.md
  .gitignore
```

Using `src/` layout prevents accidentally importing the local package instead of the installed one.

## Key Ecosystem Tools

| Tool        | Purpose                             |
|-------------|-------------------------------------|
| `ruff`      | Fast linter + formatter (replaces flake8, black, isort) |
| `mypy`      | Static type checker                 |
| `pytest`    | Testing framework                   |
| `pydantic`  | Data validation with type hints     |
| `poetry`    | Dependency management + publishing  |
| `hatch`     | Modern project manager (PEP-compliant) |
| `pre-commit`| Git hook manager for code quality   |
| `tox`       | Test against multiple Python versions |

## Metaclasses (Advanced)

A metaclass is the class of a class — it controls how classes are created:

```python
class SingletonMeta(type):
    _instances: dict = {}

    def __call__(cls, *args, **kwargs):
        if cls not in cls._instances:
            cls._instances[cls] = super().__call__(*args, **kwargs)
        return cls._instances[cls]

class Database(metaclass=SingletonMeta):
    def __init__(self):
        self.connection = "connected"

db1 = Database()
db2 = Database()
db1 is db2   # True — same instance
```

`type` is the default metaclass of all classes. `class Foo: ...` is equivalent to `Foo = type('Foo', (), {})`.
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

        $this->command->info('Lessons seeded for all 5 Python levels.');
    }

    // ── LEVEL 4 QUESTIONS ────────────────────────────────────────────────────

    private function seedLevel4Questions(Topic $topic): void
    {
        foreach ($this->level4Questions() as $qData) {
            $exists = Question::where('topic_id', $topic->id)
                ->where('question', $qData['question'])
                ->exists();
            if ($exists) {
                continue;
            }

            $q = Question::create([
                'topic_id'    => $topic->id,
                'type'        => 'MCQ',
                'difficulty'  => 'Hard',
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
        $this->command->info("Python Level 4: {$count} questions total.");
    }

    private function level4Questions(): array
    {
        return [
            [
                'question'    => 'What does `@dataclass` auto-generate in Python?',
                'explanation' => '`@dataclass` automatically generates `__init__`, `__repr__`, and `__eq__` based on the class fields. With `frozen=True` it also generates `__hash__`. With `order=True` it generates comparison methods. It does NOT generate `__hash__` by default unless `frozen=True` — mutable dataclasses are not hashable.',
                'options'     => [
                    ['text' => '__init__, __repr__, and __eq__ — and __hash__ only with frozen=True', 'correct' => true],
                    ['text' => '__init__, __str__, __hash__, and __eq__ for all dataclasses', 'correct' => false],
                    ['text' => 'Only __init__ — all other methods must still be written manually', 'correct' => false],
                    ['text' => '__new__, __init__, and __del__ to manage the full object lifecycle', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a TypeVar in Python\'s typing system?',
                'explanation' => 'A `TypeVar` is a placeholder for a type that is determined at usage time — it enables generic functions and classes. `T = TypeVar("T")` declares a type variable. When you write `def first(items: list[T]) -> T`, mypy infers `T` from the argument type at each call site. TypeVars can be constrained (`TypeVar("T", int, str)`) or bound (`TypeVar("T", bound=Comparable)`).',
                'options'     => [
                    ['text' => 'A placeholder type that enables generic, type-safe functions and classes', 'correct' => true],
                    ['text' => 'A variable that stores the type of another variable at runtime', 'correct' => false],
                    ['text' => 'A way to declare that a variable can hold any type (equivalent to Any)', 'correct' => false],
                    ['text' => 'A runtime type validator that raises TypeError on mismatch', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the time complexity of Python\'s `list.sort()` and what algorithm does it use?',
                'explanation' => 'Python\'s `list.sort()` and `sorted()` use Timsort — a hybrid merge sort/insertion sort. Average and worst case is O(n log n). It is stable (preserves relative order of equal elements). Timsort is highly optimised for real-world data that is often partially sorted — it detects existing sorted "runs" and merges them efficiently.',
                'options'     => [
                    ['text' => 'O(n log n) average and worst case — uses Timsort (hybrid merge/insertion sort), stable', 'correct' => true],
                    ['text' => 'O(n²) average case — uses quicksort, unstable', 'correct' => false],
                    ['text' => 'O(n log n) average, O(n²) worst case — uses quicksort, stable', 'correct' => false],
                    ['text' => 'O(n) for nearly-sorted data, O(n²) in all other cases', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `heapq.heappush` and `heapq.heappop` implement in Python?',
                'explanation' => '`heapq` implements a min-heap on top of a regular list. `heappush` inserts in O(log n); `heappop` removes and returns the smallest element in O(log n). It is a min-heap — the smallest value is always at index 0. To implement a max-heap, negate all values before pushing and negate again after popping.',
                'options'     => [
                    ['text' => 'A min-heap — smallest element at index 0, push/pop in O(log n)', 'correct' => true],
                    ['text' => 'A max-heap — largest element at index 0, push/pop in O(log n)', 'correct' => false],
                    ['text' => 'A balanced BST providing O(log n) search, insert, and delete', 'correct' => false],
                    ['text' => 'A FIFO queue with O(1) enqueue and dequeue', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `Optional[X]` and `Union[X, None]` in Python typing?',
                'explanation' => '`Optional[X]` is exactly equivalent to `Union[X, None]` — it is just a shorthand. Both indicate that a value can be of type X or None. In Python 3.10+, you can use `X | None` instead. Note: Optional does NOT mean the argument is optional in the function signature — it only means it can be None. To make an argument optional in the function call, give it a default value.',
                'options'     => [
                    ['text' => 'They are identical — Optional[X] is shorthand for Union[X, None]', 'correct' => true],
                    ['text' => 'Optional[X] means the parameter has a default value; Union[X, None] means it can be None', 'correct' => false],
                    ['text' => 'Optional[X] allows None only; Union[X, None] allows None or any subtype of X', 'correct' => false],
                    ['text' => 'Optional is only for function return types; Union is for parameters', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a Protocol in Python\'s typing module?',
                'explanation' => 'A `Protocol` defines a structural interface — sometimes called "duck typing" formalised. A class satisfies a Protocol if it has the required methods/attributes, WITHOUT needing to explicitly inherit from the Protocol. This is structural subtyping, as opposed to nominal subtyping (ABC requires explicit `class Foo(MyABC)`). Protocols enable type checking for duck-typed code without coupling classes together.',
                'options'     => [
                    ['text' => 'A structural interface — any class with the required methods satisfies it, without explicit inheritance', 'correct' => true],
                    ['text' => 'An abstract base class that must be explicitly subclassed', 'correct' => false],
                    ['text' => 'A runtime validator that checks method signatures at instantiation', 'correct' => false],
                    ['text' => 'A way to define methods that are auto-implemented based on type annotations', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `bisect.bisect_left(arr, x)` return?',
                'explanation' => '`bisect_left(arr, x)` returns the leftmost index where `x` can be inserted in the already-sorted `arr` to keep it sorted. If `x` already exists, `bisect_left` returns its index (before existing occurrences). `bisect_right` returns the index AFTER any existing occurrences. Use `bisect.insort(arr, x)` to insert in sorted order in O(log n) search + O(n) shift.',
                'options'     => [
                    ['text' => 'The leftmost index where x can be inserted to keep the sorted array sorted', 'correct' => true],
                    ['text' => 'The index of x if it exists, or -1 if not found', 'correct' => false],
                    ['text' => 'The rightmost index where x can be inserted in sorted order', 'correct' => false],
                    ['text' => 'The number of elements in arr that are less than x', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between a Python `dict` and `collections.OrderedDict`?',
                'explanation' => 'Since Python 3.7, regular `dict` guarantees insertion order as part of the language specification (CPython did this since 3.6 as an implementation detail). So `dict` and `OrderedDict` both maintain insertion order. The key difference: `OrderedDict` has `move_to_end()` and `popitem(last=False/True)` methods, and it considers order when comparing for equality (`od1 == od2` is False if same items but different order). Regular `dict` equality ignores order.',
                'options'     => [
                    ['text' => 'Both maintain insertion order since Python 3.7; OrderedDict adds move_to_end() and order-aware equality', 'correct' => true],
                    ['text' => 'dict does not maintain order in any Python version; OrderedDict always does', 'correct' => false],
                    ['text' => 'OrderedDict is faster than dict for large datasets', 'correct' => false],
                    ['text' => 'They are completely identical since Python 3.7 — OrderedDict is now an alias', 'correct' => false],
                ],
            ],
            [
                'question'    => 'In Python, what does `@lru_cache(maxsize=None)` do?',
                'explanation' => '`@lru_cache` memoises a function — it caches the return value for each set of input arguments. `maxsize=None` means an unbounded cache (equivalent to `@cache` in Python 3.9+). LRU stands for Least Recently Used — when the cache is full, the least recently used entry is discarded. The function must be called with hashable arguments (no lists or dicts as direct arguments). It adds `cache_info()` and `cache_clear()` methods.',
                'options'     => [
                    ['text' => 'Memoises the function — caches results by argument hash, maxsize=None means unlimited cache', 'correct' => true],
                    ['text' => 'Limits the number of times the function can be called to maxsize', 'correct' => false],
                    ['text' => 'Caches the function object itself to prevent re-compilation', 'correct' => false],
                    ['text' => 'Enables lazy evaluation — the function is only called when its result is first accessed', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the GIL and when does it matter?',
                'explanation' => 'The GIL (Global Interpreter Lock) is a mutex in CPython that ensures only one thread executes Python bytecode at a time. For I/O-bound tasks (network, disk), threads still help because the GIL is released during I/O waits. For CPU-bound tasks (computation), the GIL prevents true parallelism — use `multiprocessing` instead. The GIL does not affect `asyncio` (single-threaded) or extension code that releases it (e.g., NumPy).',
                'options'     => [
                    ['text' => 'A CPython mutex preventing parallel bytecode execution — matters for CPU-bound threads, not I/O-bound', 'correct' => true],
                    ['text' => 'A lock that prevents global variables from being modified by multiple threads', 'correct' => false],
                    ['text' => 'An asyncio primitive for synchronising coroutines', 'correct' => false],
                    ['text' => 'A garbage collector lock that pauses all threads during collection', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `collections.Counter` and what does `most_common(n)` return?',
                'explanation' => '`Counter` is a subclass of `dict` that counts hashable objects. Passing an iterable initialises counts. `most_common(n)` returns the `n` most common elements as a list of `(element, count)` tuples, in descending order. Counter supports arithmetic operations: `c1 + c2` (add counts), `c1 - c2` (subtract, keep only positive), `c1 & c2` (min), `c1 | c2` (max).',
                'options'     => [
                    ['text' => 'A dict subclass that counts items; most_common(n) returns n (element, count) tuples descending', 'correct' => true],
                    ['text' => 'A class that tracks unique elements; most_common returns only elements appearing more than once', 'correct' => false],
                    ['text' => 'A sorted set that automatically deduplicates; most_common sorts by insertion order', 'correct' => false],
                    ['text' => 'A performance profiler that counts function call frequencies', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are Python\'s `__slots__`?',
                'explanation' => 'Defining `__slots__` in a class prevents the creation of a `__dict__` for instances — instead, attributes are stored in a fixed-size array. This reduces memory usage (no per-instance dict) and speeds up attribute access. The trade-off: you can only use the attributes declared in `__slots__`; you cannot add arbitrary attributes at runtime. Subclasses without `__slots__` will still have `__dict__`.',
                'options'     => [
                    ['text' => 'Replaces per-instance __dict__ with a fixed array — reduces memory, restricts attribute addition', 'correct' => true],
                    ['text' => 'Declares which attributes are public vs private', 'correct' => false],
                    ['text' => 'Defines the order in which attributes are initialised in __init__', 'correct' => false],
                    ['text' => 'A way to make class attributes immutable after instantiation', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a deque in Python and when should you use it over a list?',
                'explanation' => '`collections.deque` is a double-ended queue with O(1) `appendleft()` and `popleft()`. Python\'s `list.insert(0, x)` and `list.pop(0)` are O(n) because they shift all elements. Use `deque` when you need a FIFO queue (add to right, remove from left), sliding window operations, or a stack with both-end access. Random access to middle elements is O(n) — use `list` if you need indexing.',
                'options'     => [
                    ['text' => 'A double-ended queue with O(1) appends/pops on both ends — use when list prepend/pop(0) would be O(n)', 'correct' => true],
                    ['text' => 'A sorted list that maintains order automatically on insert/delete', 'correct' => false],
                    ['text' => 'A thread-safe queue equivalent to Queue.Queue but faster', 'correct' => false],
                    ['text' => 'A circular buffer with fixed capacity and O(1) random access', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does parameterised SQL (prepared statements) protect against?',
                'explanation' => 'Parameterised queries (using `?` or `%s` placeholders with separate parameter values) prevent SQL injection. User input is never concatenated directly into the SQL string — the database driver escapes it safely. Never use f-strings or `.format()` to build SQL queries with user data. Both `sqlite3` and SQLAlchemy use parameterised queries by default. This is one of the OWASP Top 10 security controls.',
                'options'     => [
                    ['text' => 'SQL injection — user input is separated from SQL structure and safely escaped', 'correct' => true],
                    ['text' => 'Race conditions in concurrent database access', 'correct' => false],
                    ['text' => 'Deadlocks caused by conflicting transactions', 'correct' => false],
                    ['text' => 'N+1 query problems caused by ORM lazy loading', 'correct' => false],
                ],
            ],
        ];
    }

    // ── LEVEL 5 QUESTIONS ────────────────────────────────────────────────────

    private function seedLevel5Questions(Topic $topic): void
    {
        foreach ($this->level5Questions() as $qData) {
            $exists = Question::where('topic_id', $topic->id)
                ->where('question', $qData['question'])
                ->exists();
            if ($exists) {
                continue;
            }

            $q = Question::create([
                'topic_id'    => $topic->id,
                'type'        => 'MCQ',
                'difficulty'  => 'Hard',
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
        $this->command->info("Python Level 5: {$count} questions total.");
    }

    private function level5Questions(): array
    {
        return [
            [
                'question'    => 'What is the difference between `asyncio.gather` and `asyncio.wait`?',
                'explanation' => '`gather` runs coroutines concurrently and returns results in the SAME ORDER as the input coroutines. It cancels all if one fails (unless `return_exceptions=True`). `wait` gives more control: you pass tasks and a `return_when` flag (`FIRST_COMPLETED`, `FIRST_EXCEPTION`, `ALL_COMPLETED`). It returns `(done, pending)` sets. Use `gather` for simple fan-out; use `wait` when you want to process results as they finish or need a timeout.',
                'options'     => [
                    ['text' => 'gather returns ordered results, cancels on failure; wait returns done/pending sets with return_when control', 'correct' => true],
                    ['text' => 'gather is sequential; wait is concurrent', 'correct' => false],
                    ['text' => 'wait returns results in order; gather returns them as they complete', 'correct' => false],
                    ['text' => 'They are identical — wait is the deprecated name for gather', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `async with` do and when must you use it?',
                'explanation' => '`async with` uses an async context manager — an object with `__aenter__` and `__aexit__` that are coroutines. It must be used when the setup or teardown involves I/O (e.g., opening an async database connection, an aiohttp session). Regular `with` cannot `await` inside `__enter__`/`__exit__`, so async I/O resources need `async with`. If you use `async with` on a regular context manager, it will fail.',
                'options'     => [
                    ['text' => 'Awaits __aenter__ and __aexit__ — required when setup/teardown involves async I/O', 'correct' => true],
                    ['text' => 'A thread-safe context manager that locks a resource for the async block', 'correct' => false],
                    ['text' => 'Runs the context manager\'s body concurrently with other coroutines', 'correct' => false],
                    ['text' => 'Identical to regular with — the async keyword is optional', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a pytest fixture and what does `yield` inside one do?',
                'explanation' => 'A pytest fixture is a function decorated with `@pytest.fixture` that provides reusable setup to tests. Using `yield` instead of `return` splits the fixture into setup (before `yield`) and teardown (after `yield`). Everything before `yield` runs before the test; the yielded value is passed to the test parameter; everything after `yield` runs after the test (even if it fails). This replaces setUp/tearDown from unittest.',
                'options'     => [
                    ['text' => 'yield splits setup (before) and teardown (after) — teardown always runs even on test failure', 'correct' => true],
                    ['text' => 'yield makes the fixture lazy — setup only happens if the test actually uses the fixture value', 'correct' => false],
                    ['text' => 'yield marks where the test runs; setup runs before, but teardown must be explicit', 'correct' => false],
                    ['text' => 'yield is identical to return in fixtures — both run setup only, no teardown', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `pytest.mark.parametrize` do?',
                'explanation' => '`@pytest.mark.parametrize("params", [...])` runs the test function multiple times, once for each set of parameters in the list. Each run appears as a separate test case in the output, with its own pass/fail status. This avoids code duplication for testing multiple inputs. You can parametrize multiple arguments and even stack multiple `@parametrize` decorators to create a cartesian product of test cases.',
                'options'     => [
                    ['text' => 'Runs the test once per parameter set — each appears as a separate test case with its own result', 'correct' => true],
                    ['text' => 'Runs the test once but with all parameter sets passed as a list', 'correct' => false],
                    ['text' => 'Only runs the test if the parameter is not None — skips otherwise', 'correct' => false],
                    ['text' => 'Randomly selects one parameter set per test run', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `unittest.mock.patch` used for in Python testing?',
                'explanation' => '`patch` temporarily replaces an object in the module being tested with a Mock. It must target the name as it appears in the module under test (where it is used), not where it is defined. It can be used as a decorator, context manager, or called manually. This allows you to isolate units from their dependencies (databases, network, filesystem) and control what they return.',
                'options'     => [
                    ['text' => 'Replaces an object in the module under test with a Mock for the duration of the test', 'correct' => true],
                    ['text' => 'Applies a monkey-patch to a production module permanently', 'correct' => false],
                    ['text' => 'Validates that a function was called with specific arguments', 'correct' => false],
                    ['text' => 'Creates a copy of a function so the original is not affected during testing', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a metaclass in Python?',
                'explanation' => 'A metaclass is the class of a class — it controls how classes are created. `type` is the default metaclass. When Python sees `class Foo: ...`, it calls `type("Foo", (object,), {...})` to create the class object. By defining a custom metaclass (inheriting from `type`), you can intercept class creation to enforce constraints, auto-register subclasses, add methods, or implement ORMs (like Django\'s Model). Use metaclasses sparingly — `__init_subclass__` and class decorators solve most problems more simply.',
                'options'     => [
                    ['text' => 'The class of a class — controls class creation; type is the default metaclass', 'correct' => true],
                    ['text' => 'A class that can only be instantiated once (the Singleton pattern in Python)', 'correct' => false],
                    ['text' => 'An abstract base class that requires all subclasses to implement abstract methods', 'correct' => false],
                    ['text' => 'A class decorator that modifies the class after it is defined', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of `__init_subclass__` in Python?',
                'explanation' => '`__init_subclass__` is called whenever the class is subclassed. It lets a parent class customise subclass creation without needing a full metaclass. Common use cases: auto-registering subclasses, enforcing constraints on subclass definitions, setting default attributes. It is simpler than metaclasses for most use cases introduced in Python 3.6 (PEP 487).',
                'options'     => [
                    ['text' => 'Called when the class is subclassed — lets the parent customise subclass creation without a metaclass', 'correct' => true],
                    ['text' => 'Called when a subclass is instantiated — equivalent to __init__ but for the subclass only', 'correct' => false],
                    ['text' => 'Prevents the class from being subclassed — raises TypeError if subclassing is attempted', 'correct' => false],
                    ['text' => 'Initialises class-level variables that subclasses inherit but cannot override', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `asyncio.Semaphore` used for?',
                'explanation' => 'A Semaphore limits the number of coroutines that can access a resource concurrently. `asyncio.Semaphore(n)` creates a semaphore with a counter of n — at most n coroutines can hold the semaphore simultaneously. Additional coroutines `await` until a slot is released. Common use case: limit concurrent HTTP requests to avoid overwhelming a server or hitting rate limits.',
                'options'     => [
                    ['text' => 'Limits concurrent access to a resource — at most n coroutines hold it simultaneously', 'correct' => true],
                    ['text' => 'Signals between coroutines — one sets it, another waits for it (like a flag)', 'correct' => false],
                    ['text' => 'A lock that prevents any coroutine from running until it is released', 'correct' => false],
                    ['text' => 'A counter that tracks how many coroutines have completed', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `pip install -e .` do?',
                'explanation' => '`pip install -e .` installs the package in "editable" mode (also called development mode). Instead of copying files to site-packages, it creates a link pointing to your source directory. Changes to the source code take effect immediately without reinstalling. This is essential during development so you don\'t have to reinstall the package every time you change a file.',
                'options'     => [
                    ['text' => 'Installs the package in editable mode — source changes take effect without reinstalling', 'correct' => true],
                    ['text' => 'Executes the setup.py script with elevated permissions', 'correct' => false],
                    ['text' => 'Installs only the entry point scripts without the package itself', 'correct' => false],
                    ['text' => 'Installs the package and all optional extras defined in pyproject.toml', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `pyproject.toml` and what problem does it solve?',
                'explanation' => '`pyproject.toml` (PEP 517/518) is the modern standard for Python project configuration. It replaces the fragmented ecosystem of `setup.py`, `setup.cfg`, `MANIFEST.in`, and tool-specific config files by consolidating all configuration in one file. Build backends (setuptools, flit, poetry, hatch) read it to know how to build the package. It also holds tool configuration for mypy, ruff, pytest, etc. under `[tool.NAME]` sections.',
                'options'     => [
                    ['text' => 'The modern project config standard (PEP 517/518) that replaces setup.py and setup.cfg', 'correct' => true],
                    ['text' => 'A lock file that pins exact dependency versions (like package-lock.json)', 'correct' => false],
                    ['text' => 'A runtime configuration file read by Python on startup', 'correct' => false],
                    ['text' => 'A TOML-formatted replacement for requirements.txt', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is Test-Driven Development (TDD) and what are its three steps?',
                'explanation' => 'TDD is a development practice where you write tests BEFORE the code. The cycle: (1) Red — write a failing test for a new feature; (2) Green — write the minimum code to make it pass; (3) Refactor — improve the code while keeping tests green. Benefits: forces clear requirements upfront, produces a comprehensive test suite naturally, and encourages small, focused, well-tested units of code.',
                'options'     => [
                    ['text' => 'Red (failing test) → Green (minimum code to pass) → Refactor (improve while staying green)', 'correct' => true],
                    ['text' => 'Write → Test → Deploy — tests are written after code, before deployment', 'correct' => false],
                    ['text' => 'Plan → Code → Test — planning ensures correctness without iterative cycles', 'correct' => false],
                    ['text' => 'Mock → Implement → Verify — mocking defines the interface before the implementation', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the src layout in Python projects and why is it recommended?',
                'explanation' => 'The src layout puts package code in `src/my_package/` instead of the root. This prevents accidentally importing the local (uninstalled) package instead of the installed one — which can mask errors in your package metadata or import paths. When running tests with `pytest`, without `src/` the local directory is on `sys.path` and the uninstalled package might be imported. With `src/`, you must install the package (even in editable mode) to import it.',
                'options'     => [
                    ['text' => 'Puts code in src/ to prevent accidentally importing local source instead of the installed package', 'correct' => true],
                    ['text' => 'Required by pip — packages without src/ layout cannot be published to PyPI', 'correct' => false],
                    ['text' => 'Improves performance by reducing the number of directories Python searches on import', 'correct' => false],
                    ['text' => 'Separates source code from compiled bytecode files', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `yield from` in Python generators?',
                'explanation' => '`yield from iterable` delegates to a sub-generator or iterable — it yields each item from the sub-generator one by one. It is equivalent to `for item in sub_gen: yield item` but also correctly passes `send()` values and `throw()` exceptions into the sub-generator. It is essential for recursive generators (like flattening nested structures) and coroutine chaining in older asyncio code.',
                'options'     => [
                    ['text' => 'Delegates to a sub-generator, yielding each item and forwarding send()/throw() correctly', 'correct' => true],
                    ['text' => 'Returns all remaining values from the generator at once as a list', 'correct' => false],
                    ['text' => 'Marks the generator as finished — equivalent to raising StopIteration', 'correct' => false],
                    ['text' => 'Creates a new generator from a function and immediately starts it', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `functools.wraps` do inside a decorator?',
                'explanation' => '`@wraps(func)` copies the decorated function\'s `__name__`, `__doc__`, `__module__`, `__qualname__`, `__annotations__`, and `__dict__` to the wrapper function. Without it, the wrapper function has its own name ("wrapper") which breaks introspection, logging, error messages, and documentation tools. Always use `@wraps(func)` inside decorators to preserve the wrapped function\'s identity.',
                'options'     => [
                    ['text' => 'Copies __name__, __doc__, and other metadata from the wrapped function to the wrapper', 'correct' => true],
                    ['text' => 'Makes the decorator itself wrappable by another decorator', 'correct' => false],
                    ['text' => 'Ensures the wrapper function uses the same argument signature as the original', 'correct' => false],
                    ['text' => 'Prevents the decorator from being applied more than once to the same function', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `multiprocessing.Pool.map` and `concurrent.futures.ProcessPoolExecutor.map`?',
                'explanation' => 'Both distribute work across multiple processes. `Pool.map` blocks until all results are ready. `ProcessPoolExecutor.map` returns an iterator of futures — by default it also blocks (iterating the result), but you can use `executor.submit()` with `as_completed()` to process results as they finish. `ProcessPoolExecutor` is the newer, higher-level API that integrates with the `Future` interface and works consistently with `ThreadPoolExecutor`. `Pool` has more low-level options like `starmap`, `imap_unordered`, and `async_result`.',
                'options'     => [
                    ['text' => 'Both distribute work across processes; ProcessPoolExecutor integrates with Future and as_completed for streaming results', 'correct' => true],
                    ['text' => 'Pool.map uses threads; ProcessPoolExecutor uses processes', 'correct' => false],
                    ['text' => 'ProcessPoolExecutor is single-process; Pool truly parallelises across CPUs', 'correct' => false],
                    ['text' => 'They are identical — ProcessPoolExecutor is just a thin wrapper around Pool', 'correct' => false],
                ],
            ],
        ];
    }
}
