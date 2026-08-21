<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JsLearningSeeder extends Seeder
{
    public function run(): void
    {
        $track = LearningTrack::firstOrCreate(
            ['slug' => 'frontend-engineering'],
            [
                'title'         => 'Frontend Engineering',
                'description'   => 'Frontend engineering — JavaScript, React, and modern web technologies.',
                'display_order' => 2,
            ]
        );

        $subject = Subject::firstOrCreate(
            ['slug' => 'javascript'],
            [
                'learning_track_id' => $track->id,
                'title'             => 'JavaScript',
                'description'       => 'Master JavaScript from core fundamentals to advanced patterns used in modern development.',
                'display_order'     => 2,
            ]
        );

        // ── Step 1: Assign correct levels to existing practice topics ──────
        Topic::where('slug', 'js-basics-junior')->update(['level' => 1]);
        Topic::where('slug', 'js-intermediate')->update(['level' => 2]);
        Topic::where('slug', 'js-advanced')->update(['level' => 3]);

        // ── Step 2: Create topics for levels 4 and 5 ──────────────────────
        $topic4 = Topic::firstOrCreate(
            ['slug' => 'js-level-4-modern'],
            [
                'subject_id'    => $subject->id,
                'title'         => 'Modern JavaScript (ES6+)',
                'description'   => 'ES6 classes, modules, iterators, generators, Proxy, Reflect, Symbol, and WeakMap.',
                'display_order' => 4,
                'level'         => 4,
            ]
        );
        Topic::where('slug', 'js-level-4-modern')->update(['level' => 4]);

        $topic5 = Topic::firstOrCreate(
            ['slug' => 'js-level-5-expert'],
            [
                'subject_id'    => $subject->id,
                'title'         => 'Expert JavaScript',
                'description'   => 'TypeScript fundamentals, performance, memory management, architecture, and meta-programming.',
                'display_order' => 5,
                'level'         => 5,
            ]
        );
        Topic::where('slug', 'js-level-5-expert')->update(['level' => 5]);

        // ── Step 3: Seed lessons for all 5 levels ─────────────────────────
        $this->seedLessons($subject);

        // ── Step 4: Seed exam questions for levels 4 and 5 ────────────────
        $this->seedLevel4Questions($topic4);
        $this->seedLevel5Questions($topic5);

        $this->command->info('JS Learning seeder complete — all 5 levels populated.');
    }

    // ── LESSONS ─────────────────────────────────────────────────────────────

    private function seedLessons(Subject $subject): void
    {
        $t1 = Topic::where('slug', 'js-basics-junior')->first();
        $t2 = Topic::where('slug', 'js-intermediate')->first();
        $t3 = Topic::where('slug', 'js-advanced')->first();
        $t4 = Topic::where('slug', 'js-level-4-modern')->first();
        $t5 = Topic::where('slug', 'js-level-5-expert')->first();

        $lessons = [
            // ── LEVEL 1 ────────────────────────────────────────────────────
            [
                'topic_id'          => $t1->id,
                'title'             => 'Variables, Data Types & Type Coercion',
                'estimated_minutes' => 15,
                'display_order'     => 1,
                'content'           => <<<'MARKDOWN'
## Variables in JavaScript

JavaScript has three ways to declare variables: `var`, `let`, and `const`.

**var** — function-scoped, hoisted, avoid in modern code:
```js
var age = 25;
```

**let** — block-scoped, not accessible before declaration (temporal dead zone):
```js
let name = 'Alice';
```

**const** — block-scoped, cannot be reassigned after initialisation:
```js
const PI = 3.14159;
// PI = 3; // TypeError — reassignment not allowed
// But object properties can still be mutated:
const user = { name: 'Alice' };
user.name = 'Bob'; // this works fine
```

## The 7 Primitive Types

| Type      | Example                  |
|-----------|--------------------------|
| string    | `"hello"`, `'world'`     |
| number    | `42`, `3.14`, `NaN`, `Infinity` |
| bigint    | `9007199254740993n`       |
| boolean   | `true`, `false`           |
| undefined | unassigned variables      |
| null      | intentional empty value   |
| symbol    | `Symbol('id')`            |

Everything else (arrays, functions, objects) is of type `object`.

## Type Coercion

JavaScript silently converts types in many contexts:

```js
"5" + 3    // "53"  — + with a string concatenates
"5" - 3    // 2     — other arithmetic operators coerce to number
Boolean("") // false — empty string is falsy
Boolean("0") // true  — "0" is truthy (non-empty string)
typeof null  // "object" — a famous JS bug, null is NOT an object
```

**Falsy values**: `false`, `0`, `-0`, `0n`, `""`, `null`, `undefined`, `NaN`

Everything else is truthy — including `[]`, `{}`, and `"0"`.

Always use `===` (strict equality) to compare values. `==` performs type coercion which leads to surprising results:
```js
0 == false   // true  (coercion)
0 === false  // false (strict — type mismatch)
null == undefined  // true
null === undefined // false
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t1->id,
                'title'             => 'Functions: Declarations, Expressions & Arrow Syntax',
                'estimated_minutes' => 15,
                'display_order'     => 2,
                'content'           => <<<'MARKDOWN'
## Function Declarations vs Expressions

**Function declaration** — hoisted entirely, callable before definition:
```js
greet('Alice'); // works — declaration is hoisted

function greet(name) {
  return `Hello, ${name}!`;
}
```

**Function expression** — only the variable is hoisted (as `undefined`), not the function:
```js
// sayHi(); // TypeError: sayHi is not a function

const sayHi = function(name) {
  return `Hi, ${name}!`;
};
```

## Arrow Functions

Arrow functions (`=>`) are concise and do not have their own `this`, `arguments`, or `super`:

```js
// Traditional
const double = function(n) { return n * 2; };

// Arrow — implicit return when no braces
const double = n => n * 2;

// Arrow — explicit return with block body
const add = (a, b) => {
  const result = a + b;
  return result;
};
```

## Parameters

**Default parameters**:
```js
function connect(host = 'localhost', port = 3000) {
  return `${host}:${port}`;
}
connect(); // "localhost:3000"
```

**Rest parameters** — collect remaining args into an array:
```js
function sum(...numbers) {
  return numbers.reduce((acc, n) => acc + n, 0);
}
sum(1, 2, 3, 4); // 10
```

## Higher-Order Functions

Functions that take or return other functions:
```js
// Takes a function as argument
[1, 2, 3].map(n => n * 2); // [2, 4, 6]

// Returns a function (factory pattern)
function multiplier(factor) {
  return n => n * factor;
}
const triple = multiplier(3);
triple(5); // 15
```

A function with no `return` statement implicitly returns `undefined`.
MARKDOWN,
            ],
            [
                'topic_id'          => $t1->id,
                'title'             => 'Arrays & Objects: Core Operations',
                'estimated_minutes' => 18,
                'display_order'     => 3,
                'content'           => <<<'MARKDOWN'
## Array Methods You Must Know

**Transforming**: `map` creates a new array by transforming each element.
```js
const prices = [10, 20, 30];
const withTax = prices.map(p => p * 1.1); // [11, 22, 33]
```

**Filtering**: `filter` returns elements that pass a test.
```js
const nums = [1, 2, 3, 4, 5];
const evens = nums.filter(n => n % 2 === 0); // [2, 4]
```

**Reducing**: `reduce` folds an array into a single value.
```js
const total = [10, 20, 30].reduce((acc, n) => acc + n, 0); // 60
```

**Finding**: `find` returns the first match; `findIndex` returns its index.
```js
const users = [{ id: 1, name: 'Alice' }, { id: 2, name: 'Bob' }];
const user = users.find(u => u.id === 2); // { id: 2, name: 'Bob' }
```

**Checking**: `some`, `every`, `includes`.
```js
[1, 2, 3].includes(2);          // true
[1, 2, 3].some(n => n > 2);     // true
[1, 2, 3].every(n => n > 0);    // true
```

## Spread & Destructuring

**Spread** expands an iterable into individual elements:
```js
const a = [1, 2];
const b = [3, 4];
const merged = [...a, ...b]; // [1, 2, 3, 4]

const copy = [...a]; // shallow copy
```

**Array destructuring**:
```js
const [first, second, ...rest] = [10, 20, 30, 40];
// first=10, second=20, rest=[30, 40]
```

## Objects

**Object destructuring** — extract properties into variables:
```js
const { name, age, role = 'user' } = { name: 'Alice', age: 25 };
// role defaults to 'user' if not present
```

**Spread with objects** — shallow clone or merge:
```js
const base = { theme: 'dark', lang: 'en' };
const updated = { ...base, lang: 'fr' }; // override lang
```

**Useful static methods**:
```js
Object.keys(obj)    // array of own property names
Object.values(obj)  // array of own values
Object.entries(obj) // array of [key, value] pairs
Object.assign({}, obj) // shallow clone
Object.freeze(obj)  // make object immutable (shallow)
```

## Mutating vs Non-mutating

Methods like `push`, `pop`, `shift`, `unshift`, `splice`, `sort`, and `reverse` **mutate** the original array.
Methods like `map`, `filter`, `reduce`, `slice`, `concat`, and spread `[...arr]` return a **new array**.

Prefer non-mutating operations in functional-style code to avoid side effects.
MARKDOWN,
            ],

            // ── LEVEL 2 ────────────────────────────────────────────────────
            [
                'topic_id'          => $t2->id,
                'title'             => 'Closures & Lexical Scope',
                'estimated_minutes' => 15,
                'display_order'     => 1,
                'content'           => <<<'MARKDOWN'
## Scope in JavaScript

Scope determines where a variable is accessible. JavaScript uses **lexical scope** — a function's scope is determined by where it is written, not where it is called.

**Global scope**: variables declared outside any function.
**Function scope**: `var` declarations.
**Block scope**: `let` and `const` inside `{}`.

```js
let x = 'global';

function outer() {
  let x = 'outer';

  function inner() {
    let x = 'inner';
    console.log(x); // 'inner' — closest scope wins
  }

  inner();
  console.log(x); // 'outer'
}
console.log(x); // 'global'
```

The **scope chain**: when a variable is not found in the current scope, JavaScript looks up to the next outer scope, all the way to global.

## What is a Closure?

A closure is a function that **retains access to its outer scope** even after the outer function has returned.

```js
function makeCounter(start = 0) {
  let count = start;

  return {
    increment: () => ++count,
    decrement: () => --count,
    value:     () => count,
  };
}

const counter = makeCounter(10);
counter.increment(); // 11
counter.increment(); // 12
counter.decrement(); // 11
counter.value();     // 11
```

`count` lives in `makeCounter`'s scope. Even after `makeCounter` returns, the returned object still has a live reference to `count`. That reference *is* the closure.

## Practical Closure Patterns

**Private variables** — encapsulate state:
```js
function createBankAccount(initial) {
  let balance = initial;

  return {
    deposit:  amount => (balance += amount),
    withdraw: amount => (balance -= amount),
    getBalance: ()    => balance,
  };
}
```

**Partial application / factory functions**:
```js
function multiplier(factor) {
  return value => value * factor;
}
const double = multiplier(2);
const triple = multiplier(3);
double(5); // 10
triple(5); // 15
```

**IIFE (Immediately Invoked Function Expression)** — creates a private scope:
```js
const result = (function() {
  let secret = 42;
  return { get: () => secret };
})();
result.get(); // 42
```

## Common Closure Bug

The classic `var` in loop problem:
```js
// Bug: all callbacks log 5
for (var i = 0; i < 5; i++) {
  setTimeout(() => console.log(i), 100);
}

// Fix 1: use let (block-scoped, new binding each iteration)
for (let i = 0; i < 5; i++) {
  setTimeout(() => console.log(i), 100);
}

// Fix 2: IIFE to capture current i
for (var i = 0; i < 5; i++) {
  (function(j) {
    setTimeout(() => console.log(j), 100);
  })(i);
}
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t2->id,
                'title'             => 'The `this` Keyword & Context Binding',
                'estimated_minutes' => 15,
                'display_order'     => 2,
                'content'           => <<<'MARKDOWN'
## What is `this`?

`this` refers to the **execution context** — the object that is currently calling the function. Its value depends on *how* a function is called, not where it is defined.

## How `this` is Determined

**1. Global context**: in non-strict mode, `this` is `window` (browser) or `global` (Node.js). In strict mode, it is `undefined`.

**2. Object method**: `this` is the object before the dot.
```js
const user = {
  name: 'Alice',
  greet() {
    return `Hi, I'm ${this.name}`;
  },
};
user.greet(); // "Hi, I'm Alice"
```

**3. Standalone function call**: `this` is `undefined` (strict) or global.
```js
function show() {
  console.log(this); // undefined in strict mode
}
```

**4. Constructor (new)**: `this` is the newly created instance.
```js
function Person(name) {
  this.name = name;
}
const p = new Person('Bob');
p.name; // 'Bob'
```

## Explicit Binding: call, apply, bind

```js
function greet(greeting) {
  return `${greeting}, ${this.name}`;
}
const alice = { name: 'Alice' };

greet.call(alice, 'Hello');          // "Hello, Alice"
greet.apply(alice, ['Hello']);       // "Hello, Alice" — args as array
const boundGreet = greet.bind(alice);
boundGreet('Hi');                    // "Hi, Alice"
```

- `call` — invokes immediately, args comma-separated
- `apply` — invokes immediately, args as array
- `bind` — returns a new function with `this` locked in

## Arrow Functions & `this`

Arrow functions **do not have their own `this`**. They inherit it from the surrounding lexical scope.

```js
const timer = {
  count: 0,
  start() {
    setInterval(() => {
      this.count++; // 'this' is the timer object — arrow captures lexical this
      console.log(this.count);
    }, 1000);
  },
};
```

If `start` used a regular function for the callback, `this` would be `undefined` (strict) or the global object — a very common bug in event handlers and async callbacks.

## The Lost `this` Problem

```js
const user = {
  name: 'Alice',
  greet() { return `Hi, ${this.name}`; },
};

const fn = user.greet; // detached from object
fn(); // "Hi, undefined" — this is no longer user

// Fix: bind permanently
const fn2 = user.greet.bind(user);
fn2(); // "Hi, Alice"
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t2->id,
                'title'             => 'Prototypes & the Prototype Chain',
                'estimated_minutes' => 15,
                'display_order'     => 3,
                'content'           => <<<'MARKDOWN'
## How JavaScript Inherits

Every JavaScript object has an internal `[[Prototype]]` property pointing to another object (or `null`). When you access a property, JS looks up the **prototype chain** until it finds it or reaches `null`.

```js
const animal = {
  speak() {
    return `${this.name} makes a sound.`;
  },
};

const dog = Object.create(animal); // dog.__proto__ === animal
dog.name = 'Rex';
dog.speak(); // "Rex makes a sound." — found on animal via prototype chain
```

## Object.create vs new

**Object.create(proto)** — creates a new object with `proto` as its prototype:
```js
const base = { greet() { return 'hello'; } };
const obj = Object.create(base);
obj.greet(); // "hello"
```

**Constructor functions with new**:
```js
function Animal(name) {
  this.name = name;
}
Animal.prototype.speak = function() {
  return `${this.name} makes a sound.`;
};

const cat = new Animal('Whiskers');
cat.speak(); // "Whiskers makes a sound."
cat.__proto__ === Animal.prototype; // true
```

## The Prototype Chain Lookup

```
cat
  .name ────────── found on cat itself ✓
  .speak ─────────  not on cat → look at cat.__proto__ (Animal.prototype) → found ✓
  .toString ──────  not on Animal.prototype → look at Object.prototype → found ✓
  .xyz ───────────  not found anywhere → returns undefined
```

## hasOwnProperty

To check if a property is on the object itself (not inherited):
```js
cat.hasOwnProperty('name');  // true
cat.hasOwnProperty('speak'); // false — speak is on the prototype
```

## ES6 class is Syntactic Sugar

`class` does not introduce a new inheritance model — it is syntactic sugar over prototypes:
```js
class Animal {
  constructor(name) {
    this.name = name;
  }
  speak() {
    return `${this.name} makes a sound.`;
  }
}

class Dog extends Animal {
  speak() {
    return `${this.name} barks!`;
  }
}

const d = new Dog('Rex');
d.speak(); // "Rex barks!"
d instanceof Dog;    // true
d instanceof Animal; // true — prototype chain
```

`extends` sets up the prototype chain. `super(...)` calls the parent constructor.
MARKDOWN,
            ],

            // ── LEVEL 3 ────────────────────────────────────────────────────
            [
                'topic_id'          => $t3->id,
                'title'             => 'The JavaScript Event Loop & Runtime',
                'estimated_minutes' => 18,
                'display_order'     => 1,
                'content'           => <<<'MARKDOWN'
## JavaScript is Single-Threaded

JavaScript runs on a single thread — it can only execute one piece of code at a time. Yet it handles I/O, timers, and UI events concurrently. How? The **event loop**.

## The Runtime Model

```
┌──────────────────────────────────┐
│          Call Stack              │  ← executes synchronous code
│   (LIFO — last in, first out)    │
└──────────────────────────────────┘
         ↑ pops completed frames

┌──────────────────────────────────┐
│         Web APIs / Node APIs     │  ← timers, fetch, DOM events
└──────────────────────────────────┘
         ↓ completes → sends callback to queue

┌──────────────────────────────────┐
│      Microtask Queue             │  ← Promise .then, queueMicrotask
│      (higher priority)           │
└──────────────────────────────────┘
┌──────────────────────────────────┐
│      Macrotask Queue             │  ← setTimeout, setInterval, I/O
└──────────────────────────────────┘
         ↑ event loop picks next task when call stack is empty
```

**Priority**: microtasks run to completion before the next macrotask is picked.

## Execution Order Example

```js
console.log('1 — sync');

setTimeout(() => console.log('4 — macrotask'), 0);

Promise.resolve().then(() => console.log('3 — microtask'));

console.log('2 — sync');

// Output: 1, 2, 3, 4
```

Even with a 0ms timeout, the `setTimeout` callback runs after all microtasks because macrotasks have lower priority.

## Call Stack & Stack Overflow

Each function call adds a **frame** to the call stack. When a function returns, its frame is removed.

```js
function a() { b(); }
function b() { c(); }
function c() { throw new Error('!'); }
a();
// Stack: a → b → c → Error
```

Infinite recursion causes a **stack overflow** (`Maximum call stack size exceeded`).

## Blocking the Event Loop

Long synchronous operations block the entire event loop — no I/O, no rendering, no events:
```js
// BAD: blocks for ~2s
const start = Date.now();
while (Date.now() - start < 2000) {} // busy wait

// GOOD: use async, offload work to workers for CPU-intensive tasks
```

## setImmediate vs process.nextTick (Node.js)

- `process.nextTick` — runs before the next I/O event (before microtask queue in older docs, but actually runs before other microtasks in practice)
- `setImmediate` — runs in the check phase, after I/O callbacks
- `Promise.then` — microtask, runs before either of the above macrotasks
MARKDOWN,
            ],
            [
                'topic_id'          => $t3->id,
                'title'             => 'Promises: Chaining, Error Handling & Combinators',
                'estimated_minutes' => 18,
                'display_order'     => 2,
                'content'           => <<<'MARKDOWN'
## What is a Promise?

A Promise represents a future value — it is either pending, fulfilled, or rejected.

```js
const p = new Promise((resolve, reject) => {
  setTimeout(() => resolve('done'), 1000);
});

p.then(value => console.log(value)); // "done" after 1s
```

## Chaining

Each `.then` returns a new Promise, enabling chains:
```js
fetch('/api/user')
  .then(res => res.json())          // parse body
  .then(user => fetch(`/api/posts?userId=${user.id}`))
  .then(res => res.json())
  .then(posts => console.log(posts))
  .catch(err => console.error(err)); // catches any error in the chain
```

A `return` inside `.then` passes the value to the next `.then`. If you return a Promise, the chain waits for it to settle.

## async / await

`async/await` is syntactic sugar over Promises — it makes async code read like synchronous code:
```js
async function loadUserPosts(userId) {
  try {
    const userRes = await fetch(`/api/users/${userId}`);
    const user    = await userRes.json();

    const postsRes = await fetch(`/api/posts?userId=${user.id}`);
    const posts    = await postsRes.json();

    return posts;
  } catch (err) {
    console.error('Failed:', err.message);
    throw err; // re-throw if caller needs to handle it
  }
}
```

- `await` suspends the async function until the Promise settles
- `await` can only be used inside an `async` function
- Errors are caught with `try/catch`

## Promise Combinators

**`Promise.all`** — resolves when ALL resolve, rejects as soon as one rejects:
```js
const [users, posts] = await Promise.all([
  fetch('/api/users').then(r => r.json()),
  fetch('/api/posts').then(r => r.json()),
]);
```

**`Promise.allSettled`** — waits for ALL, never rejects, reports each outcome:
```js
const results = await Promise.allSettled([
  fetchUsers(),
  fetchPosts(),
]);
results.forEach(r => {
  if (r.status === 'fulfilled') console.log(r.value);
  else console.error(r.reason);
});
```

**`Promise.race`** — resolves/rejects with whichever settles first:
```js
const timeout = new Promise((_, reject) =>
  setTimeout(() => reject(new Error('Timeout')), 5000)
);
const result = await Promise.race([fetch('/api/data'), timeout]);
```

**`Promise.any`** — resolves with first fulfilled, rejects only if ALL reject.

## Common Mistakes

```js
// Bug: not awaiting inside async function
async function bad() {
  const data = fetch('/api'); // forgot await — data is a Promise, not the response
}

// Bug: not returning in .then chain
fetch('/api/user')
  .then(res => {
    res.json(); // forgot return — next .then gets undefined
  })
  .then(user => console.log(user)); // undefined
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t3->id,
                'title'             => 'Generators & Iterators',
                'estimated_minutes' => 18,
                'display_order'     => 3,
                'content'           => <<<'MARKDOWN'
## Iterators

An **iterator** is any object with a `next()` method that returns `{ value, done }`:

```js
function rangeIterator(start, end) {
  let current = start;
  return {
    next() {
      if (current <= end) {
        return { value: current++, done: false };
      }
      return { value: undefined, done: true };
    },
  };
}

const iter = rangeIterator(1, 3);
iter.next(); // { value: 1, done: false }
iter.next(); // { value: 2, done: false }
iter.next(); // { value: 3, done: false }
iter.next(); // { value: undefined, done: true }
```

## Iterables

An **iterable** is an object with `[Symbol.iterator]()` that returns an iterator. Arrays, strings, Maps, Sets, and generators are all iterables — they work with `for...of` and spread.

```js
const range = {
  [Symbol.iterator]() {
    let n = 1;
    return {
      next() {
        return n <= 5
          ? { value: n++, done: false }
          : { value: undefined, done: true };
      },
    };
  },
};

for (const n of range) console.log(n); // 1 2 3 4 5
const arr = [...range]; // [1, 2, 3, 4, 5]
```

## Generator Functions

Generators simplify iterator creation. A generator function uses `function*` and `yield` to pause and resume:

```js
function* counter(start = 1) {
  while (true) {
    yield start++;
  }
}

const gen = counter(10);
gen.next(); // { value: 10, done: false }
gen.next(); // { value: 11, done: false }
gen.next(); // { value: 12, done: false }
```

Each `yield` suspends execution, returning a value to the caller. Calling `next()` resumes from where it paused.

## Finite Generators

```js
function* fibonacci() {
  let [a, b] = [0, 1];
  while (true) {
    yield a;
    [a, b] = [b, a + b];
  }
}

const fib = fibonacci();
Array.from({ length: 8 }, () => fib.next().value);
// [0, 1, 1, 2, 3, 5, 8, 13]
```

## Async Generators

Combine async with generators for streaming data:

```js
async function* paginate(url) {
  let page = 1;
  while (true) {
    const res  = await fetch(`${url}?page=${page}`);
    const data = await res.json();
    if (!data.length) return;
    yield data;
    page++;
  }
}

for await (const batch of paginate('/api/posts')) {
  console.log('Batch:', batch);
}
```

`for await...of` is the async counterpart to `for...of` — it awaits each yielded value.
MARKDOWN,
            ],

            // ── LEVEL 4 ────────────────────────────────────────────────────
            [
                'topic_id'          => $t4->id,
                'title'             => 'ES6 Classes: Syntax, Inheritance & Private Fields',
                'estimated_minutes' => 18,
                'display_order'     => 1,
                'content'           => <<<'MARKDOWN'
## Class Syntax

ES6 `class` is syntactic sugar over prototypal inheritance. The underlying mechanism is identical — classes just provide cleaner syntax.

```js
class Animal {
  #sound; // private field (ES2022)

  constructor(name, sound) {
    this.name  = name;
    this.#sound = sound;
  }

  speak() {
    return `${this.name} goes ${this.#sound}.`;
  }

  static create(name, sound) {
    return new Animal(name, sound);
  }
}

const cat = Animal.create('Whiskers', 'meow');
cat.speak();    // "Whiskers goes meow."
cat.#sound;     // SyntaxError — private field
```

## Inheritance with extends & super

```js
class Dog extends Animal {
  #tricks = [];

  constructor(name) {
    super(name, 'woof'); // must call super() before using `this`
  }

  learn(trick) {
    this.#tricks.push(trick);
    return this;
  }

  perform() {
    return this.#tricks.length
      ? `${this.name} performs: ${this.#tricks.join(', ')}`
      : `${this.name} knows no tricks yet.`;
  }
}

const rex = new Dog('Rex');
rex.learn('sit').learn('roll');
rex.perform(); // "Rex performs: sit, roll"
rex.speak();   // "Rex goes woof." — inherited from Animal
```

## Static Members

Static methods/properties belong to the class, not instances:
```js
class MathUtils {
  static PI = 3.14159;

  static circleArea(r) {
    return MathUtils.PI * r * r;
  }
}

MathUtils.circleArea(5); // 78.54
// new MathUtils().circleArea(5) // TypeError
```

## Getter / Setter

```js
class Temperature {
  #celsius;

  constructor(celsius) {
    this.#celsius = celsius;
  }

  get fahrenheit() {
    return this.#celsius * 9 / 5 + 32;
  }

  set fahrenheit(f) {
    this.#celsius = (f - 32) * 5 / 9;
  }
}

const t = new Temperature(0);
t.fahrenheit;       // 32
t.fahrenheit = 212; // sets celsius to 100
```

## Mixins (Composing Without Inheritance)

JavaScript only supports single inheritance. Mixins compose behaviour:
```js
const Serializable = (Base) => class extends Base {
  serialize()   { return JSON.stringify(this); }
  toJSON()      { return { ...this }; }
};

const Timestamped = (Base) => class extends Base {
  constructor(...args) {
    super(...args);
    this.createdAt = new Date().toISOString();
  }
};

class User extends Serializable(Timestamped(Animal)) {}
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t4->id,
                'title'             => 'JavaScript Modules: ESM, CommonJS & Dynamic Import',
                'estimated_minutes' => 15,
                'display_order'     => 2,
                'content'           => <<<'MARKDOWN'
## ES Modules (ESM)

The native module system in modern JavaScript and browsers. Files are strict mode by default.

**Named exports** — export multiple things by name:
```js
// math.js
export const PI = 3.14159;
export function add(a, b) { return a + b; }
export function subtract(a, b) { return a - b; }
```

**Named imports**:
```js
import { PI, add } from './math.js';
add(2, 3); // 5
```

**Default export** — one per file, imported without braces:
```js
// logger.js
export default function log(msg) {
  console.log(`[LOG] ${msg}`);
}

// usage
import log from './logger.js';
log('hello'); // [LOG] hello
```

**Re-exporting** (barrel files):
```js
// index.js — re-export everything from sub-modules
export { add, subtract } from './math.js';
export { default as Logger }   from './logger.js';
```

## CommonJS (Node.js legacy)

CommonJS uses `require` / `module.exports`. Still widely used in older Node.js code and tooling:
```js
// math.js (CommonJS)
module.exports = { add: (a, b) => a + b };

// usage
const { add } = require('./math');
add(1, 2); // 3
```

| Feature       | ESM                      | CommonJS               |
|---------------|--------------------------|------------------------|
| Syntax        | `import` / `export`      | `require` / `module.exports` |
| Loading       | Static (compile-time)    | Dynamic (runtime)      |
| Tree-shaking  | ✓ supported              | ✗ harder               |
| `this` (top)  | `undefined`              | `module.exports`       |
| Circular deps | Handled by live bindings | Cached (can cause bugs) |

## Dynamic import()

Load modules lazily at runtime — useful for code splitting:

```js
// Loads module only when the button is clicked
button.addEventListener('click', async () => {
  const { default: Chart } = await import('./chart.js');
  new Chart(canvas, options);
});
```

`import()` returns a Promise. Use it for:
- Route-based code splitting (React lazy, Next.js)
- Feature flags (only load premium features if unlocked)
- Large optional libraries (moment, chart.js)

## Module Resolution

Node.js resolves module paths in this order:
1. Core modules (`fs`, `path`, `http`)
2. `node_modules` directory lookup
3. File extensions tried: `.js`, `.json`, `.node`

In browsers with bundlers (Vite, webpack), resolution is configured via `resolve.alias` and `tsconfig.paths`.
MARKDOWN,
            ],
            [
                'topic_id'          => $t4->id,
                'title'             => 'Proxy, Reflect & Meta-programming',
                'estimated_minutes' => 20,
                'display_order'     => 3,
                'content'           => <<<'MARKDOWN'
## What is Meta-programming?

Meta-programming means writing code that operates on code — inspecting, modifying, or intercepting behaviour at runtime.

JavaScript supports meta-programming through:
- `Proxy` — intercept object operations
- `Reflect` — perform default operations reflectively
- `Symbol` — define custom behaviour hooks

## Proxy

A `Proxy` wraps an object and intercepts operations (called **traps**):

```js
const handler = {
  get(target, prop) {
    console.log(`Getting ${prop}`);
    return prop in target ? target[prop] : `Property "${prop}" not found`;
  },

  set(target, prop, value) {
    if (typeof value !== 'number') {
      throw new TypeError(`${prop} must be a number`);
    }
    target[prop] = value;
    return true; // must return true to indicate success
  },
};

const data = new Proxy({}, handler);
data.age = 25;          // sets fine
data.age;               // "Getting age" → 25
data.name = 'Alice';    // TypeError: name must be a number
data.missing;           // 'Property "missing" not found'
```

Common traps: `get`, `set`, `has` (for `in` operator), `deleteProperty`, `apply` (for functions), `construct` (for `new`).

## Reflect

`Reflect` provides default implementations of Proxy traps — useful to forward operations inside traps:

```js
const logged = new Proxy(target, {
  set(target, prop, value, receiver) {
    console.log(`Setting ${prop} = ${value}`);
    return Reflect.set(target, prop, value, receiver); // perform the default set
  },
});
```

`Reflect.ownKeys(obj)` returns all keys including Symbols — unlike `Object.keys` which only returns string enumerable keys.

## Symbol

Symbols are unique, immutable primitive values — perfect for private-like keys:

```js
const id  = Symbol('id');
const obj = { [id]: 42, name: 'Alice' };

obj[id];          // 42
Object.keys(obj); // ['name'] — Symbol keys are excluded
```

**Well-known Symbols** hook into JS internals:

```js
class Range {
  constructor(start, end) {
    this.start = start;
    this.end   = end;
  }

  [Symbol.iterator]() {
    let current = this.start;
    const end   = this.end;
    return {
      next() {
        return current <= end
          ? { value: current++, done: false }
          : { value: undefined, done: true };
      },
    };
  }
}

[...new Range(1, 5)]; // [1, 2, 3, 4, 5]
```

Other well-known symbols: `Symbol.toPrimitive`, `Symbol.hasInstance`, `Symbol.toStringTag`, `Symbol.species`.

## WeakMap & WeakRef

`WeakMap` holds weak references to keys — if the key object is garbage collected, the entry is automatically removed:

```js
const cache = new WeakMap();

function getMetadata(obj) {
  if (!cache.has(obj)) {
    cache.set(obj, computeMetadata(obj));
  }
  return cache.get(obj);
}
// When `obj` is GC'd, the cache entry disappears automatically
```

`WeakRef` holds a weak reference to an object — lets you observe if it has been collected:
```js
let obj = { data: 'important' };
const ref = new WeakRef(obj);
obj = null; // eligible for GC

const val = ref.deref(); // undefined if GC'd, otherwise the object
```
MARKDOWN,
            ],

            // ── LEVEL 5 ────────────────────────────────────────────────────
            [
                'topic_id'          => $t5->id,
                'title'             => 'TypeScript Fundamentals for JavaScript Developers',
                'estimated_minutes' => 20,
                'display_order'     => 1,
                'content'           => <<<'MARKDOWN'
## What is TypeScript?

TypeScript is a **typed superset of JavaScript** — every valid JS file is valid TS. TypeScript adds optional static types, which are erased at compile time (no runtime overhead). It compiles to plain JavaScript.

```ts
// JavaScript
function add(a, b) { return a + b; }

// TypeScript
function add(a: number, b: number): number { return a + b; }
add(1, 2);   // 3
add(1, '2'); // TypeError at compile time
```

## Primitive Types & Type Annotations

```ts
let name:    string  = 'Alice';
let age:     number  = 25;
let active:  boolean = true;
let nothing: null    = null;
let missing: undefined = undefined;
let anything: any    = 'no type safety here';
let unknown: unknown  = fetchData(); // safer than any — must narrow before use
```

## Interfaces & Type Aliases

Both define the shape of an object:

```ts
// Interface — open (can be extended via declaration merging)
interface User {
  id:     number;
  name:   string;
  email?: string; // optional property
}

// Type alias — closed, can represent unions and intersections too
type ID = string | number;
type UserOrAdmin = User | Admin;
```

Prefer `interface` for object shapes (can be `extends`ed); prefer `type` for unions, tuples, and computed types.

## Generics

Generics let you write reusable code that works with any type while preserving type safety:

```ts
function identity<T>(value: T): T {
  return value;
}
identity<string>('hello'); // string
identity<number>(42);      // number

// Generic container
interface Box<T> {
  value: T;
  label: string;
}
const box: Box<number> = { value: 42, label: 'answer' };
```

## Utility Types

TypeScript ships utility types for transforming existing types:

```ts
interface User { id: number; name: string; email: string; }

Partial<User>         // all properties optional
Required<User>        // all properties required
Readonly<User>        // all properties read-only
Pick<User, 'id' | 'name'>  // only id and name
Omit<User, 'email'>        // everything except email
Record<string, number>     // { [key: string]: number }
ReturnType<typeof fn>      // infer return type of function
```

## Narrowing

TypeScript narrows types based on runtime checks:

```ts
function process(value: string | number) {
  if (typeof value === 'string') {
    return value.toUpperCase(); // TS knows it's string here
  }
  return value.toFixed(2);     // TS knows it's number here
}
```

## tsconfig.json Key Options

```json
{
  "compilerOptions": {
    "strict": true,          // enables all strict checks (recommended)
    "target": "ES2022",      // compile to this JS version
    "module": "ESNext",      // module format
    "jsx": "react-jsx",      // for React projects
    "baseUrl": ".",          // for path aliases
    "paths": {
      "@/*": ["src/*"]       // import '@/components/Button'
    }
  }
}
```

`"strict": true` enables: `strictNullChecks`, `noImplicitAny`, `strictFunctionTypes`, and more. Always use it.
MARKDOWN,
            ],
            [
                'topic_id'          => $t5->id,
                'title'             => 'Performance & Memory Management',
                'estimated_minutes' => 20,
                'display_order'     => 2,
                'content'           => <<<'MARKDOWN'
## JavaScript Engine: V8

V8 (used in Chrome and Node.js) compiles JavaScript to machine code using:

1. **Ignition** — an interpreter that produces bytecode quickly
2. **TurboFan** — an optimising JIT compiler that compiles hot functions to machine code

V8 uses **hidden classes** (shapes) to optimise property access. When you add properties in the same order every time, V8 reuses the same hidden class — making object creation and property access much faster.

```js
// Good: consistent shape
function makePoint(x, y) { return { x, y }; }

// Bad: inconsistent — V8 creates different hidden classes
const p1 = { x: 1, y: 2 };
const p2 = { y: 2, x: 1 }; // different order → different shape
```

## Garbage Collection

V8 uses a **generational garbage collector**:
- **Young generation (nursery)** — short-lived objects; collected frequently and fast (minor GC)
- **Old generation** — objects that survive multiple minor GCs; collected less often (major GC)

GC is triggered when memory pressure rises. You cannot control GC directly in JS.

## Common Memory Leaks

**1. Detached DOM nodes** — holding references to removed DOM elements:
```js
let el = document.getElementById('btn');
document.body.removeChild(el);
// el still references the node — it won't be GC'd
el = null; // fix: release the reference
```

**2. Event listeners not removed**:
```js
// Leak: new listener added every render but never removed
window.addEventListener('resize', handler);

// Fix: clean up in useEffect return / component unmount
return () => window.removeEventListener('resize', handler);
```

**3. Closures holding large objects**:
```js
function setup() {
  const largeData = new Array(1_000_000).fill('x');
  return function() {
    // closure keeps largeData alive even if never used here
    console.log('done');
  };
}
```

**4. Unbounded caches / maps**:
```js
const cache = new Map(); // grows forever if keys are never deleted
// Fix: use WeakMap (entries removed when key is GC'd) or a max-size LRU cache
```

## Performance Optimisation Techniques

**Memoization** — cache function results:
```js
function memoize(fn) {
  const cache = new Map();
  return function(...args) {
    const key = JSON.stringify(args);
    if (cache.has(key)) return cache.get(key);
    const result = fn(...args);
    cache.set(key, result);
    return result;
  };
}

const fib = memoize(function(n) {
  return n <= 1 ? n : fib(n - 1) + fib(n - 2);
});
```

**Debounce** — delay execution until after a burst of calls ends:
```js
function debounce(fn, ms) {
  let timer;
  return function(...args) {
    clearTimeout(timer);
    timer = setTimeout(() => fn(...args), ms);
  };
}
const onResize = debounce(() => recalcLayout(), 200);
```

**Throttle** — limit to one call per interval:
```js
function throttle(fn, ms) {
  let last = 0;
  return function(...args) {
    const now = Date.now();
    if (now - last >= ms) {
      last = now;
      fn(...args);
    }
  };
}
const onScroll = throttle(() => updateHeader(), 100);
```

**Virtual DOM / windowing**: for large lists, only render visible items (react-window, virtual scrolling).

**Web Workers**: offload CPU-intensive work off the main thread.
MARKDOWN,
            ],
            [
                'topic_id'          => $t5->id,
                'title'             => 'JavaScript Architecture & Design Patterns',
                'estimated_minutes' => 20,
                'display_order'     => 3,
                'content'           => <<<'MARKDOWN'
## Why Patterns Matter

Design patterns are proven solutions to recurring problems. Learning them lets you communicate intent clearly ("this is a Factory", "that's an Observer") and avoid reinventing the wheel.

## Creational Patterns

**Factory** — creates objects without specifying the exact class:
```js
function createUser(role) {
  const base = { role, createdAt: Date.now() };
  if (role === 'admin') return { ...base, canDelete: true };
  return { ...base, canDelete: false };
}
```

**Singleton** — ensures one instance exists:
```js
const Database = (() => {
  let instance;
  return {
    getInstance() {
      if (!instance) instance = { connected: false };
      return instance;
    },
  };
})();
Database.getInstance() === Database.getInstance(); // true
```

**Builder** — construct complex objects step by step:
```js
class QueryBuilder {
  #table = ''; #conditions = []; #limit = null;

  from(table)      { this.#table = table; return this; }
  where(cond)      { this.#conditions.push(cond); return this; }
  limitTo(n)       { this.#limit = n; return this; }
  build() {
    let q = `SELECT * FROM ${this.#table}`;
    if (this.#conditions.length) q += ` WHERE ${this.#conditions.join(' AND ')}`;
    if (this.#limit) q += ` LIMIT ${this.#limit}`;
    return q;
  }
}

new QueryBuilder().from('users').where('active = 1').limitTo(10).build();
// "SELECT * FROM users WHERE active = 1 LIMIT 10"
```

## Structural Patterns

**Decorator** — add behaviour without modifying the original:
```js
function withLogging(fn) {
  return function(...args) {
    console.log(`Calling ${fn.name} with`, args);
    const result = fn(...args);
    console.log(`${fn.name} returned`, result);
    return result;
  };
}
const loggedAdd = withLogging((a, b) => a + b);
loggedAdd(2, 3);
```

**Facade** — simplified interface to a complex subsystem:
```js
// Complex: low-level audio API
class AudioFacade {
  constructor() {
    this.ctx    = new AudioContext();
    this.gain   = this.ctx.createGain();
    this.gain.connect(this.ctx.destination);
  }
  play(buffer) { /* complex AudioContext calls */ }
  setVolume(v) { this.gain.gain.value = v; }
}
// Consumer just calls play() and setVolume() — complexity hidden
```

## Behavioural Patterns

**Observer / PubSub** — decoupled event communication:
```js
class EventEmitter {
  #listeners = new Map();

  on(event, fn)  {
    if (!this.#listeners.has(event)) this.#listeners.set(event, []);
    this.#listeners.get(event).push(fn);
    return () => this.off(event, fn); // returns unsubscribe fn
  }

  off(event, fn) {
    const fns = this.#listeners.get(event) ?? [];
    this.#listeners.set(event, fns.filter(f => f !== fn));
  }

  emit(event, data) {
    (this.#listeners.get(event) ?? []).forEach(fn => fn(data));
  }
}
```

**Strategy** — swap algorithms at runtime:
```js
const sortStrategies = {
  bubble: arr => { /* bubble sort */ },
  quick:  arr => [...arr].sort((a, b) => a - b),
  merge:  arr => { /* merge sort */ },
};

function sort(arr, strategy = 'quick') {
  return sortStrategies[strategy](arr);
}
```

## SOLID in JavaScript

- **S**ingle Responsibility: each function/class does one thing
- **O**pen/Closed: extend via composition, not modification
- **L**iskov Substitution: subclasses must honour the parent's contract
- **I**nterface Segregation: many focused interfaces over one bloated one
- **D**ependency Inversion: depend on abstractions (accept a `storage` param, not hardcode `localStorage`)
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

        $this->command->info('Lessons seeded for all 5 JS levels.');
    }

    // ── LEVEL 4 QUESTIONS ────────────────────────────────────────────────────

    private function seedLevel4Questions(Topic $topic): void
    {
        Question::where('topic_id', $topic->id)->delete();

        foreach ($this->level4Questions() as $qData) {
            $q = Question::create([
                'topic_id'    => $topic->id,
                'type'        => 'MCQ',
                'difficulty'  => 'Hard',
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
        $this->command->info("JS Level 4: {$count} questions total.");
    }

    private function level4Questions(): array
    {
        return [
            [
                'question'    => 'What is the key difference between a JavaScript class and a constructor function?',
                'explanation' => 'ES6 class is syntactic sugar over prototype-based inheritance. The biggest practical difference is that classes are NOT hoisted (you cannot use a class before its declaration), they are always in strict mode, and class methods are non-enumerable by default. Constructor functions are hoisted as `undefined` (if expressed) or fully (if declared).',
                'options'     => [
                    ['text' => 'Classes are not hoisted, always strict, and methods are non-enumerable; constructor functions are hoisted', 'correct' => true],
                    ['text' => 'Classes create a different kind of prototype chain than constructor functions', 'correct' => false],
                    ['text' => 'Class methods are enumerable; constructor prototype methods are not', 'correct' => false],
                    ['text' => 'Classes cannot be used with the instanceof operator', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `super` keyword do when used inside a class method?',
                'explanation' => '`super()` in a constructor calls the parent class constructor. `super.method()` in a method calls the parent class\'s method. In a derived class, you MUST call `super()` before accessing `this` in the constructor — otherwise a ReferenceError is thrown.',
                'options'     => [
                    ['text' => 'Calls the parent class constructor (in constructors) or parent method (in methods)', 'correct' => true],
                    ['text' => 'Creates a new instance of the parent class', 'correct' => false],
                    ['text' => 'Copies all parent properties to the child instance', 'correct' => false],
                    ['text' => 'Allows the method to be called statically on the parent', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a private class field in JavaScript and how is it declared?',
                'explanation' => 'Private class fields are declared with a `#` prefix inside the class body. They are only accessible within the class — not from outside or from subclasses. Accessing them outside throws a SyntaxError (not a runtime error — it is caught at parse time). This is a true language-enforced privacy, unlike the conventional `_prefix` naming convention.',
                'options'     => [
                    ['text' => 'Declared with # prefix, only accessible within the class, enforced by the language', 'correct' => true],
                    ['text' => 'Declared with _ prefix, accessible by convention only, no engine enforcement', 'correct' => false],
                    ['text' => 'Declared with private keyword, accessible within the class and its subclasses', 'correct' => false],
                    ['text' => 'Declared inside the constructor only, not accessible via this in methods', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between a named export and a default export in ES Modules?',
                'explanation' => 'A file can have MULTIPLE named exports but only ONE default export. Named exports must be imported with matching names (or aliases using `as`): `import { add } from "./math"`. Default exports are imported without braces and can be named anything: `import anything from "./math"`. Named exports are generally preferred because they are easier to tree-shake and IDE auto-import works better.',
                'options'     => [
                    ['text' => 'A file can have many named exports but only one default; named imports use braces, default does not', 'correct' => true],
                    ['text' => 'Default exports cannot be renamed; named exports can be aliased', 'correct' => false],
                    ['text' => 'Named exports are only for functions; default exports are for objects', 'correct' => false],
                    ['text' => 'They are functionally identical — only syntax differs', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `import()` (dynamic import) return?',
                'explanation' => '`import()` is a runtime function that returns a **Promise** which resolves to the module namespace object. It allows lazy loading — the module is not loaded until the `import()` call is executed. This is the foundation of code splitting in bundlers like Vite and webpack, enabling smaller initial bundle sizes.',
                'options'     => [
                    ['text' => 'A Promise that resolves to the module namespace object', 'correct' => true],
                    ['text' => 'The module synchronously — it is just alternative syntax for static import', 'correct' => false],
                    ['text' => 'An Observable that emits the module when loaded', 'correct' => false],
                    ['text' => 'undefined — the callback form must be used instead', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a JavaScript Proxy and what are "traps"?',
                'explanation' => 'A Proxy wraps an object (the target) and intercepts operations on it through handler functions called traps. Common traps: `get` (property read), `set` (property write), `has` (the `in` operator), `apply` (function call), `construct` (the `new` operator). If a trap is not defined, the operation passes through to the target.',
                'options'     => [
                    ['text' => 'An object wrapper that intercepts operations via handler functions called traps', 'correct' => true],
                    ['text' => 'A design pattern for handling errors in async code', 'correct' => false],
                    ['text' => 'A function wrapper that caches return values automatically', 'correct' => false],
                    ['text' => 'A way to prevent objects from being mutated', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `Symbol.iterator` define on an object?',
                'explanation' => '`Symbol.iterator` is a well-known Symbol that makes an object iterable. When you define `[Symbol.iterator]()`, the object can be used with `for...of`, the spread operator (`...`), destructuring, and `Array.from()`. The method must return an iterator object with a `next()` method.',
                'options'     => [
                    ['text' => 'The iteration protocol — makes the object usable with for...of, spread, and destructuring', 'correct' => true],
                    ['text' => 'A unique key used to identify the object in a WeakMap', 'correct' => false],
                    ['text' => 'An override for the default toString() behaviour', 'correct' => false],
                    ['text' => 'A way to make an object behave like a primitive in comparisons', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the key difference between Map and WeakMap in JavaScript?',
                'explanation' => 'Map holds strong references to its keys — keys are kept alive as long as the Map exists. WeakMap holds weak references to object keys — if the key object is garbage collected, the entry is automatically removed. WeakMap keys must be objects (not primitives). WeakMap is NOT iterable and has no `size` property, making it ideal for private data storage associated with objects.',
                'options'     => [
                    ['text' => 'WeakMap holds weak references — entries are removed when the key object is GC\'d; Map holds strong references', 'correct' => true],
                    ['text' => 'WeakMap is just a Map that accepts only strings as keys', 'correct' => false],
                    ['text' => 'Map is faster than WeakMap for large datasets', 'correct' => false],
                    ['text' => 'WeakMap allows primitives as keys; Map does not', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `Object.freeze()` do in JavaScript?',
                'explanation' => '`Object.freeze(obj)` prevents new properties from being added, existing properties from being removed, and property values from being changed. It is a **shallow** freeze — nested objects are not frozen. In strict mode, attempting to modify a frozen object throws a TypeError; in non-strict mode, modifications silently fail.',
                'options'     => [
                    ['text' => 'Makes an object immutable (shallow) — properties cannot be added, removed, or changed', 'correct' => true],
                    ['text' => 'Deeply freezes the object and all nested objects recursively', 'correct' => false],
                    ['text' => 'Prevents the object from being garbage collected', 'correct' => false],
                    ['text' => 'Converts the object to a JSON-serializable format', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `Reflect.ownKeys(obj)` return that `Object.keys(obj)` does not?',
                'explanation' => '`Reflect.ownKeys(obj)` returns ALL own property keys, including both string keys AND Symbol keys, including non-enumerable ones. `Object.keys(obj)` only returns enumerable string keys. `Object.getOwnPropertyNames()` returns all string keys including non-enumerable ones but excludes Symbols.',
                'options'     => [
                    ['text' => 'All own keys including Symbol keys and non-enumerable string keys', 'correct' => true],
                    ['text' => 'Only inherited prototype keys', 'correct' => false],
                    ['text' => 'Only Symbol keys, excluding string keys', 'correct' => false],
                    ['text' => 'The same result as Object.keys — Reflect.ownKeys is just an alias', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of a JavaScript generator function (`function*`)?',
                'explanation' => 'A generator function produces an iterator. It uses the `yield` keyword to pause execution and return a value to the caller. The caller resumes execution by calling `.next()`. This allows lazy evaluation of sequences — values are produced on demand rather than all at once, which is memory-efficient for large or infinite sequences.',
                'options'     => [
                    ['text' => 'Produces an iterator that lazily yields values one at a time on each .next() call', 'correct' => true],
                    ['text' => 'Generates random values of a specified type', 'correct' => false],
                    ['text' => 'Creates a function that runs asynchronously without async/await', 'correct' => false],
                    ['text' => 'Generates compiled machine code from the function for performance', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is tree-shaking in JavaScript build tools?',
                'explanation' => 'Tree-shaking is a dead code elimination technique used by bundlers (Rollup, Vite, webpack). It statically analyses the import/export graph and removes code that is imported but never used. It only works with ES Modules (static imports/exports) because CommonJS `require` is dynamic and cannot be statically analysed.',
                'options'     => [
                    ['text' => 'Dead code elimination — unused exports are removed from the final bundle', 'correct' => true],
                    ['text' => 'A technique to split a bundle into multiple chunks for lazy loading', 'correct' => false],
                    ['text' => 'Removing all console.log statements in production builds', 'correct' => false],
                    ['text' => 'A garbage collection strategy used by the V8 engine', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `Symbol.toPrimitive` well-known symbol used for?',
                'explanation' => '`Symbol.toPrimitive` lets you define how an object converts to a primitive value. When defined, it is called with a `hint` argument: "string" (for template literals, String()), "number" (for arithmetic), or "default" (for loose equality or +). Without it, JS falls back to `valueOf()` and `toString()`.',
                'options'     => [
                    ['text' => 'Defines how an object converts to a primitive value (string, number, or default)', 'correct' => true],
                    ['text' => 'Creates a primitive wrapper around a Symbol value', 'correct' => false],
                    ['text' => 'Converts all properties of an object to their primitive equivalents', 'correct' => false],
                    ['text' => 'Marks a method as primitive-only, preventing it from being called on objects', 'correct' => false],
                ],
            ],
            [
                'question'    => 'In JavaScript ES6 classes, what is the purpose of a static method?',
                'explanation' => 'Static methods belong to the class itself, not to instances. They are called on the class directly (`ClassName.method()`), not on objects (`new ClassName().method()` would fail). They are typically used for factory methods, utility functions, or operations that don\'t require access to instance data.',
                'options'     => [
                    ['text' => 'Called on the class itself, not on instances — used for factory methods and utilities', 'correct' => true],
                    ['text' => 'Can only be called once during the object\'s lifetime', 'correct' => false],
                    ['text' => 'Prevents subclasses from overriding the method', 'correct' => false],
                    ['text' => 'Makes the method available globally without needing to import the class', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the Temporal Dead Zone (TDZ) in JavaScript?',
                'explanation' => 'The Temporal Dead Zone is the period between entering a block scope and the actual declaration of a `let` or `const` variable. During the TDZ, the variable exists in scope but accessing it throws a `ReferenceError`. This is different from `var`, which is hoisted and initialised to `undefined`. TDZ enforces that you cannot use a variable before declaring it.',
                'options'     => [
                    ['text' => 'The period between scope entry and the let/const declaration where access throws ReferenceError', 'correct' => true],
                    ['text' => 'A gap in time when the garbage collector is running and cannot allocate memory', 'correct' => false],
                    ['text' => 'The period after a variable is declared but before it is assigned a value', 'correct' => false],
                    ['text' => 'A browser API that delays execution until the DOM is ready', 'correct' => false],
                ],
            ],
        ];
    }

    // ── LEVEL 5 QUESTIONS ────────────────────────────────────────────────────

    private function seedLevel5Questions(Topic $topic): void
    {
        Question::where('topic_id', $topic->id)->delete();

        foreach ($this->level5Questions() as $qData) {
            $q = Question::create([
                'topic_id'    => $topic->id,
                'type'        => 'MCQ',
                'difficulty'  => 'Hard',
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
        $this->command->info("JS Level 5: {$count} questions total.");
    }

    private function level5Questions(): array
    {
        return [
            [
                'question'    => 'What is TypeScript and how does it relate to JavaScript?',
                'explanation' => 'TypeScript is a statically typed superset of JavaScript developed by Microsoft. Every valid JavaScript file is valid TypeScript. TypeScript adds optional type annotations which are erased at compile time — there is zero runtime overhead. It compiles to plain JavaScript and runs wherever JavaScript runs. The main benefit is catching type errors at development time rather than at runtime.',
                'options'     => [
                    ['text' => 'A statically typed superset of JavaScript that compiles to plain JS with no runtime overhead', 'correct' => true],
                    ['text' => 'A different programming language that replaces JavaScript in modern browsers', 'correct' => false],
                    ['text' => 'A JavaScript runtime with built-in type checking like Deno', 'correct' => false],
                    ['text' => 'A library that adds type checking at runtime to JavaScript', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between a TypeScript interface and a type alias?',
                'explanation' => 'Both define object shapes, but: Interfaces support declaration merging (two interface declarations with the same name are merged). Interfaces can be extended with `extends`. Type aliases can represent unions, intersections, tuples, and primitive aliases — things interfaces cannot do. Type aliases are generally more flexible; interfaces are better for defining contracts that others can extend.',
                'options'     => [
                    ['text' => 'Interfaces support declaration merging and extends; type aliases support unions, intersections, and tuples', 'correct' => true],
                    ['text' => 'Interfaces can only be used with classes; type aliases can be used anywhere', 'correct' => false],
                    ['text' => 'Type aliases are compiled away; interfaces remain as runtime objects', 'correct' => false],
                    ['text' => 'They are completely identical — choice is purely stylistic', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are TypeScript generics and why are they useful?',
                'explanation' => 'Generics allow you to write reusable code that works with any type while maintaining full type safety. Instead of using `any` (which disables type checking), generics act as type parameters. For example, `function identity<T>(val: T): T` preserves the type of whatever is passed in. Generics are essential for utility types, data structures, and higher-order functions.',
                'options'     => [
                    ['text' => 'Type parameters that make code reusable while preserving type safety (unlike any)', 'correct' => true],
                    ['text' => 'A way to generate TypeScript types automatically from a JSON schema', 'correct' => false],
                    ['text' => 'Template strings that are evaluated at compile time', 'correct' => false],
                    ['text' => 'Optional parameters that default to any when omitted', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are the most common causes of memory leaks in JavaScript applications?',
                'explanation' => 'The most common causes: (1) Forgotten event listeners — adding listeners without removing them when the element is destroyed. (2) Holding references to detached DOM nodes. (3) Closures that capture large objects unintentionally. (4) Unbounded caches or Maps that grow indefinitely. (5) Global variables that persist for the application lifetime. WeakMap and WeakRef can help because they hold weak references that don\'t prevent GC.',
                'options'     => [
                    ['text' => 'Forgotten event listeners, detached DOM nodes, closure-captured objects, and unbounded caches', 'correct' => true],
                    ['text' => 'Using too many let declarations instead of const', 'correct' => false],
                    ['text' => 'Synchronous code that runs longer than 16ms', 'correct' => false],
                    ['text' => 'Using arrow functions instead of regular functions', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What optimisation technique does V8 use to speed up JavaScript execution?',
                'explanation' => 'V8 uses a Just-In-Time (JIT) compiler with two tiers: Ignition (interpreter, fast startup) and TurboFan (optimising compiler for hot code paths). It also uses hidden classes (shapes) — if objects are created with the same properties in the same order, V8 reuses the same hidden class, making property access much faster through inline caches.',
                'options'     => [
                    ['text' => 'JIT compilation with hidden classes (shapes) and inline caches for hot code paths', 'correct' => true],
                    ['text' => 'Ahead-of-time (AOT) compilation to WebAssembly at parse time', 'correct' => false],
                    ['text' => 'Transpilation to C++ for all functions called more than 10 times', 'correct' => false],
                    ['text' => 'Shared memory between all JavaScript files to reduce duplication', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the Singleton pattern and how is it commonly implemented in JavaScript?',
                'explanation' => 'The Singleton pattern ensures a class has exactly one instance and provides a global access point. In JavaScript, it is commonly implemented with a closure (an IIFE that stores the instance in a private variable), or with an ES module (since module-level variables are singletons by default — imported multiple times, they share the same instance). Singletons are used for shared resources: loggers, config objects, connection pools.',
                'options'     => [
                    ['text' => 'Ensures one instance exists — implemented via IIFE closure or ES module-level variable', 'correct' => true],
                    ['text' => 'Creates a single method on a class that can only be called once', 'correct' => false],
                    ['text' => 'Prevents a class from being subclassed', 'correct' => false],
                    ['text' => 'A pattern that wraps a single function with error handling', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is memoization and when should it be used?',
                'explanation' => 'Memoization is an optimisation technique where the results of function calls are cached by their input arguments. Subsequent calls with the same arguments return the cached result instead of recomputing. It is effective for pure functions (same input always produces same output) that are expensive to compute and called repeatedly with the same arguments — like recursive Fibonacci, complex data transformations, or API call deduplication.',
                'options'     => [
                    ['text' => 'Caching function results by input arguments — effective for pure, expensive, frequently-repeated calls', 'correct' => true],
                    ['text' => 'Storing function definitions in memory to avoid re-parsing', 'correct' => false],
                    ['text' => 'A technique to reduce the number of renders in a React component', 'correct' => false],
                    ['text' => 'Automatically converting synchronous functions to async', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the Observer pattern and how does it differ from EventEmitter?',
                'explanation' => 'The Observer pattern defines a one-to-many dependency: one subject notifies multiple observers of state changes. EventEmitter is a concrete implementation of this pattern — observers subscribe to named events, the emitter dispatches them. The broader Observer concept also includes RxJS Observables, but those add lazy execution and operators. Node.js EventEmitter, DOM addEventListener, and RxJS all implement variations of Observer.',
                'options'     => [
                    ['text' => 'Observer is the pattern (one subject → many listeners); EventEmitter is a named-event implementation of it', 'correct' => true],
                    ['text' => 'They are unrelated — Observer is for async code, EventEmitter is for synchronous', 'correct' => false],
                    ['text' => 'EventEmitter replaces Observer in modern JavaScript — Observer is deprecated', 'correct' => false],
                    ['text' => 'Observer uses push model; EventEmitter uses pull model', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is debouncing, and how does it differ from throttling?',
                'explanation' => 'Debouncing delays a function call until after a burst of events ends — the timer resets on each event. Use for search-as-you-type (wait until the user stops typing). Throttling guarantees the function is called at most once per interval, regardless of how many events fire — use for scroll/resize handlers where you want periodic updates. Both prevent excessive function calls during rapid events.',
                'options'     => [
                    ['text' => 'Debounce fires after the burst ends (timer resets each event); throttle fires at most once per interval', 'correct' => true],
                    ['text' => 'Throttle fires after the burst ends; debounce fires at most once per interval', 'correct' => false],
                    ['text' => 'They are identical — just different naming conventions', 'correct' => false],
                    ['text' => 'Debounce is for async functions; throttle is for synchronous ones', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the Facade pattern and when would you use it in JavaScript?',
                'explanation' => 'The Facade pattern provides a simplified, unified interface to a complex subsystem. It hides the complexity behind a clean API. Use it when: you have a complex library you want to wrap for easier use, you want to decouple your code from a third-party library (easy to swap later), or when you have multiple interdependent subsystems that callers should not need to know about.',
                'options'     => [
                    ['text' => 'A simplified interface wrapping a complex subsystem — hides implementation details from callers', 'correct' => true],
                    ['text' => 'A design pattern that prevents access to private class members', 'correct' => false],
                    ['text' => 'A visual pattern that mimics the appearance of native browser components', 'correct' => false],
                    ['text' => 'A pattern that caches the return value of complex calculations', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `Object.defineProperty()` used for in JavaScript?',
                'explanation' => '`Object.defineProperty(obj, prop, descriptor)` allows fine-grained control over a property using a property descriptor with options: `value`, `writable` (can be reassigned?), `enumerable` (shows in for...in/Object.keys?), `configurable` (can the descriptor be changed or property deleted?), and `get`/`set` for accessor properties. It is the underlying mechanism that `class` getters/setters and Vue 2\'s reactivity system use.',
                'options'     => [
                    ['text' => 'Defines a property with fine-grained control over writability, enumerability, and configurability', 'correct' => true],
                    ['text' => 'Defines a static property on a class that cannot be overridden', 'correct' => false],
                    ['text' => 'Creates a new object property that is automatically serialised to JSON', 'correct' => false],
                    ['text' => 'Prevents a specific property from being garbage collected', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is code splitting and how does it improve JavaScript application performance?',
                'explanation' => 'Code splitting breaks a large JavaScript bundle into smaller chunks that are loaded on demand (lazy loading). Instead of shipping all JS upfront, only the code needed for the current route/feature is loaded. This reduces the initial bundle size, improves Time-to-Interactive (TTI), and reduces the parse/compile time on first load. React.lazy() + Suspense and dynamic import() are the primary mechanisms.',
                'options'     => [
                    ['text' => 'Breaks the bundle into smaller chunks loaded on demand — reduces initial parse time and TTI', 'correct' => true],
                    ['text' => 'Splitting a function into multiple smaller functions for readability', 'correct' => false],
                    ['text' => 'Running JavaScript in parallel threads using Web Workers', 'correct' => false],
                    ['text' => 'Removing duplicate code across files during the build process', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the Decorator pattern in JavaScript and how does it relate to the TC39 decorators proposal?',
                'explanation' => 'The Decorator pattern adds behaviour to an object or function without modifying it — by wrapping it. In pure JavaScript, this is done with higher-order functions. The TC39 Decorators proposal (now Stage 3) formalises this with `@decorator` syntax for classes and class members, allowing annotations that transform classes/methods at definition time. TypeScript supports an experimental version of decorators used heavily in Angular and NestJS.',
                'options'     => [
                    ['text' => 'Adds behaviour via wrapping (HOF pattern); TC39 proposal adds @decorator syntax for classes/methods', 'correct' => true],
                    ['text' => 'A CSS pattern that adds visual styling to JavaScript components', 'correct' => false],
                    ['text' => 'A compiler plugin that transforms JavaScript into an optimised format', 'correct' => false],
                    ['text' => 'A runtime pattern for catching and re-throwing errors from functions', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a shallow copy vs a deep copy of an object in JavaScript?',
                'explanation' => 'A shallow copy duplicates only the top-level properties. Nested objects are still shared by reference — mutating a nested object in the copy also mutates the original. Methods: `Object.assign({}, obj)`, spread `{...obj}`, `Array.prototype.slice()`. A deep copy creates fully independent copies of all nested objects. Methods: `structuredClone(obj)` (built-in, modern), `JSON.parse(JSON.stringify(obj))` (limited — loses functions, dates become strings, undefined is omitted), or lodash `_.cloneDeep()`.',
                'options'     => [
                    ['text' => 'Shallow copies top-level properties only (nested refs shared); deep copies all nesting independently', 'correct' => true],
                    ['text' => 'Shallow copy is faster but creates a reference; deep copy creates a completely independent primitive', 'correct' => false],
                    ['text' => 'They are identical for objects without methods — different only when the object has functions', 'correct' => false],
                    ['text' => 'Shallow copy is for arrays; deep copy is for objects', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is dependency injection and how is it applied in JavaScript?',
                'explanation' => 'Dependency injection (DI) is a technique where a function or class receives its dependencies from the outside rather than creating them internally. This makes code easier to test (inject a mock), easier to swap implementations, and decouples modules. In JavaScript, this is done by accepting dependencies as constructor arguments or function parameters. Frameworks like Angular have built-in DI containers; in React, Context API and hooks like `useContext` serve a similar role.',
                'options'     => [
                    ['text' => 'Providing dependencies from the outside (via params/constructor) rather than creating them internally', 'correct' => true],
                    ['text' => 'Automatically importing modules based on usage patterns at runtime', 'correct' => false],
                    ['text' => 'A technique where Node.js injects built-in APIs into user code', 'correct' => false],
                    ['text' => 'Generating functions dynamically based on type annotations', 'correct' => false],
                ],
            ],
        ];
    }
}
