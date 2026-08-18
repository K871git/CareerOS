<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeScriptLearningSeeder extends Seeder
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
            ['slug' => 'typescript'],
            [
                'learning_track_id' => $track->id,
                'title'             => 'TypeScript',
                'description'       => 'TypeScript is a typed superset of JavaScript. Master static typing, interfaces, generics, and advanced type patterns used in professional codebases.',
                'display_order'     => 2,
            ]
        );

        // ── Step 1: Assign correct levels to existing practice topics ──────
        Topic::where('slug', 'typescript-junior')->update(['level' => 1]);
        Topic::where('slug', 'typescript-intermediate')->update(['level' => 2]);
        Topic::where('slug', 'typescript-advanced')->update(['level' => 3]);

        // ── Step 2: Create topics for levels 4 and 5 ──────────────────────
        $topic4 = Topic::firstOrCreate(
            ['slug' => 'typescript-level-4-patterns'],
            [
                'subject_id'    => $subject->id,
                'title'         => 'Advanced TypeScript Patterns',
                'description'   => 'Discriminated unions, exhaustiveness checking, TypeScript with React, configuration, and project references.',
                'display_order' => 4,
                'level'         => 4,
            ]
        );
        Topic::where('slug', 'typescript-level-4-patterns')->update(['level' => 4]);

        $topic5 = Topic::firstOrCreate(
            ['slug' => 'typescript-level-5-expert'],
            [
                'subject_id'    => $subject->id,
                'title'         => 'Expert TypeScript',
                'description'   => 'Type-level programming, branded types, recursive types, and full-stack type safety patterns.',
                'display_order' => 5,
                'level'         => 5,
            ]
        );
        Topic::where('slug', 'typescript-level-5-expert')->update(['level' => 5]);

        // ── Step 3: Seed lessons for all 5 levels ─────────────────────────
        $this->seedLessons($subject);

        // ── Step 4: Seed exam questions for levels 4 and 5 ────────────────
        $this->seedLevel4Questions($topic4);
        $this->seedLevel5Questions($topic5);

        $this->command->info('TypeScript Learning seeder complete — all 5 levels populated.');
    }

    // ── LESSONS ─────────────────────────────────────────────────────────────

    private function seedLessons(Subject $subject): void
    {
        $t1 = Topic::where('slug', 'typescript-junior')->first();
        $t2 = Topic::where('slug', 'typescript-intermediate')->first();
        $t3 = Topic::where('slug', 'typescript-advanced')->first();
        $t4 = Topic::where('slug', 'typescript-level-4-patterns')->first();
        $t5 = Topic::where('slug', 'typescript-level-5-expert')->first();

        $lessons = [
            // ── LEVEL 1 ────────────────────────────────────────────────────
            [
                'topic_id'          => $t1->id,
                'title'             => 'Types, Annotations & Type Inference',
                'estimated_minutes' => 15,
                'display_order'     => 1,
                'content'           => <<<'MARKDOWN'
## Why TypeScript?

TypeScript is a **typed superset of JavaScript** — every valid `.js` file is valid `.ts`. TypeScript adds optional static types that are checked at compile time and erased before the code reaches the browser or Node.js. The result: zero runtime overhead and full compatibility with the JavaScript ecosystem.

```ts
// Plain JavaScript — no types, runtime error discovered too late
function add(a, b) {
    return a + b;
}
add(1, '2'); // "12" — not what you intended

// TypeScript — type error caught immediately in your editor
function add(a: number, b: number): number {
    return a + b;
}
add(1, '2'); // Error: Argument of type 'string' is not assignable to parameter of type 'number'
```

## Primitive Types

TypeScript maps directly onto JavaScript's primitives:

```ts
let name:    string  = 'Alice';
let age:     number  = 25;
let active:  boolean = true;
let nothing: null    = null;
let missing: undefined = undefined;
let id:      bigint  = 9007199254740993n;
let key:     symbol  = Symbol('key');
```

Special types:

```ts
let anything: any     = 'opt out of type checking — use sparingly';
let safeAny:  unknown = fetchData(); // must narrow before use — safer than any
let never:    never;                 // a value that can never occur
let result:   void;                 // no meaningful return value (function side effects)
```

## Type Annotations

Annotations are optional — TypeScript infers types from assigned values:

```ts
// Explicit annotation
let score: number = 100;

// Inferred — TypeScript deduces number from the literal 100
let score = 100;

// Function parameters always benefit from explicit annotations
function greet(name: string, age: number): string {
    return `${name} is ${age}`;
}
```

## Type Inference

TypeScript infers types automatically in many situations:

```ts
// Variable initialisation
const x = 42;          // inferred: number
const msg = 'hello';   // inferred: string
const arr = [1, 2, 3]; // inferred: number[]

// Return type inference
function double(n: number) {
    return n * 2; // TypeScript infers return type: number
}

// Contextual typing — TypeScript infers from the expected type
const nums = [1, 2, 3];
nums.forEach(n => {
    // n is inferred as number from the array element type
    console.log(n.toFixed(2));
});
```

## Arrays & Tuples

```ts
// Arrays — two equivalent notations
let scores:  number[]      = [95, 87, 92];
let names:   Array<string> = ['Alice', 'Bob'];

// Readonly arrays — prevents mutation methods
let config: ReadonlyArray<string> = ['dev', 'prod'];

// Tuples — fixed-length arrays with typed positions
let point:  [number, number]         = [10, 20];
let entry:  [string, number, boolean] = ['Alice', 25, true];
const [x, y] = point; // destructuring works as expected
```

## The `any` vs `unknown` Distinction

```ts
let a: any = 'hello';
a.toUpperCase();  // OK — no type checking at all
a.nonExistent();  // also OK — any skips all checks

let u: unknown = 'hello';
u.toUpperCase();  // Error — must narrow first

// Narrowing unknown to string
if (typeof u === 'string') {
    u.toUpperCase(); // OK — TypeScript now knows it's a string
}
```

Prefer `unknown` over `any` when the type is genuinely not known — it forces you to write safe narrowing code.
MARKDOWN,
            ],
            [
                'topic_id'          => $t1->id,
                'title'             => 'Interfaces, Type Aliases & Object Shapes',
                'estimated_minutes' => 15,
                'display_order'     => 2,
                'content'           => <<<'MARKDOWN'
## Defining Object Shapes

TypeScript has two ways to describe the shape of an object: **interfaces** and **type aliases**.

## Interfaces

An interface defines a contract — properties, their types, and whether they're optional:

```ts
interface User {
    id:       number;
    name:     string;
    email?:   string;     // optional — may be absent or undefined
    readonly createdAt: Date; // readonly — cannot be reassigned after creation
}

const alice: User = {
    id:        1,
    name:      'Alice',
    createdAt: new Date(),
};

// alice.createdAt = new Date(); // Error: Cannot assign to 'createdAt' (readonly)
```

### Interface Methods

```ts
interface Repository<T> {
    findById(id: number): T | null;
    findAll(): T[];
    save(entity: T): T;
    delete(id: number): void;
}
```

### Extending Interfaces

```ts
interface Person {
    name: string;
    age:  number;
}

interface Employee extends Person {
    employeeId: string;
    department: string;
}

const emp: Employee = {
    name:       'Bob',
    age:        30,
    employeeId: 'E-001',
    department: 'Engineering',
};
```

## Type Aliases

Type aliases give a name to any type — not just objects:

```ts
// Object shape (same as interface)
type Point = {
    x: number;
    y: number;
};

// Primitive alias
type UserId = string;
type Score  = number;

// Union type — interface cannot do this
type ID = string | number;

// Intersection type — combining two types
type AdminUser = User & { permissions: string[] };
```

## Interface vs Type Alias

| Feature                | Interface | Type Alias |
|------------------------|-----------|------------|
| Object shape           | ✓         | ✓          |
| Union types            | ✗         | ✓          |
| Intersection types     | via extends | ✓ via & |
| Declaration merging    | ✓         | ✗          |
| Computed properties    | ✗         | ✓          |

**Guideline**: use `interface` for object shapes that others may extend; use `type` for unions, intersections, tuples, and computed types.

## Optional vs Required vs Readonly

```ts
interface Config {
    host:    string;          // required
    port?:   number;          // optional — can be omitted
    readonly apiKey: string;  // required, cannot be changed after creation
}

// TypeScript utility types achieve the same transformations:
type PartialConfig   = Partial<Config>;   // all optional
type RequiredConfig  = Required<Config>;  // all required
type ReadonlyConfig  = Readonly<Config>;  // all readonly
```

## Index Signatures

Describe objects with dynamic string keys:

```ts
interface Dictionary {
    [key: string]: string;
}

const translations: Dictionary = {
    hello: 'hola',
    world: 'mundo',
    // any string key is valid
};

// Mix named and index signatures — named props must match value type
interface StringMap {
    length: number; // known property
    [key: string]: string | number; // dynamic — must include number for 'length'
}
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t1->id,
                'title'             => 'Union Types, Intersection Types & Narrowing',
                'estimated_minutes' => 18,
                'display_order'     => 3,
                'content'           => <<<'MARKDOWN'
## Union Types

A union type allows a value to be one of several types, written with `|`:

```ts
type StringOrNumber = string | number;

function formatId(id: string | number): string {
    return `ID: ${id}`;
}
formatId(42);      // "ID: 42"
formatId('abc');   // "ID: abc"
```

Union types are common for nullable values and flexible APIs:

```ts
type MaybeString = string | null | undefined;

function getUsername(user: { name?: string }): string | undefined {
    return user.name;
}
```

## Intersection Types

Intersection types combine multiple types into one — the value must satisfy ALL types:

```ts
type Timestamped = {
    createdAt: Date;
    updatedAt: Date;
};

type User = {
    id:   number;
    name: string;
};

type UserRecord = User & Timestamped;

const record: UserRecord = {
    id:        1,
    name:      'Alice',
    createdAt: new Date(),
    updatedAt: new Date(),
};
```

## Type Narrowing

TypeScript narrows the type within conditional blocks based on runtime checks:

### typeof Narrowing

```ts
function process(value: string | number): string {
    if (typeof value === 'string') {
        // TypeScript knows: value is string here
        return value.toUpperCase();
    }
    // TypeScript knows: value is number here
    return value.toFixed(2);
}
```

### instanceof Narrowing

```ts
class Cat { meow() { return 'meow'; } }
class Dog { bark() { return 'woof'; } }

function makeNoise(animal: Cat | Dog): string {
    if (animal instanceof Cat) {
        return animal.meow(); // Cat
    }
    return animal.bark(); // Dog
}
```

### in Operator Narrowing

```ts
interface Circle  { kind: 'circle';  radius: number; }
interface Square  { kind: 'square';  side: number; }
interface Triangle { kind: 'triangle'; base: number; height: number; }

type Shape = Circle | Square | Triangle;

function area(shape: Shape): number {
    if ('radius' in shape) {
        return Math.PI * shape.radius ** 2; // Circle
    }
    if ('side' in shape) {
        return shape.side ** 2;              // Square
    }
    return 0.5 * shape.base * shape.height; // Triangle
}
```

### Discriminated Unions

The best pattern for narrowing over a union — each member has a shared literal type field (the discriminant):

```ts
type Result<T> =
    | { success: true;  data: T }
    | { success: false; error: string };

function handleResult<T>(result: Result<T>): T {
    if (result.success) {
        return result.data;   // TypeScript knows: result.data exists
    }
    throw new Error(result.error); // TypeScript knows: result.error exists
}
```

## Literal Types

Restrict a variable to an exact value:

```ts
type Direction = 'left' | 'right' | 'up' | 'down';
type HttpMethod = 'GET' | 'POST' | 'PUT' | 'DELETE' | 'PATCH';
type Bit = 0 | 1;

function move(direction: Direction, steps: number): void {
    console.log(`Moving ${direction} by ${steps}`);
}

move('left', 5);   // OK
move('diagonal', 5); // Error: "diagonal" is not assignable to Direction
```

## Type Guards

Custom functions that narrow types:

```ts
function isString(value: unknown): value is string {
    return typeof value === 'string';
}

function isUser(obj: unknown): obj is { id: number; name: string } {
    return (
        typeof obj === 'object' &&
        obj !== null &&
        'id' in obj &&
        'name' in obj
    );
}

const data: unknown = JSON.parse(response);
if (isUser(data)) {
    console.log(data.name); // TypeScript knows: data is User here
}
```
MARKDOWN,
            ],

            // ── LEVEL 2 ────────────────────────────────────────────────────
            [
                'topic_id'          => $t2->id,
                'title'             => 'Generics: Functions, Interfaces & Classes',
                'estimated_minutes' => 18,
                'display_order'     => 1,
                'content'           => <<<'MARKDOWN'
## What are Generics?

Generics let you write code that works with any type while preserving type safety. Instead of using `any` (which disables checking), generics act as type placeholders that are filled in at call time.

```ts
// Without generics — loses type info
function identity(value: any): any {
    return value;
}
const result = identity(42); // result is 'any', not 'number'

// With generics — type is preserved
function identity<T>(value: T): T {
    return value;
}
const n = identity<number>(42); // n is 'number'
const s = identity('hello');    // TypeScript infers T = string
```

## Generic Functions

```ts
// Generic pair function
function pair<A, B>(first: A, second: B): [A, B] {
    return [first, second];
}
const p = pair('Alice', 25); // [string, number]

// Generic filter
function filter<T>(arr: T[], predicate: (item: T) => boolean): T[] {
    return arr.filter(predicate);
}
const evens = filter([1, 2, 3, 4], n => n % 2 === 0); // number[]
```

## Generic Constraints

Use `extends` to restrict what types T can be:

```ts
// T must have a length property
function longest<T extends { length: number }>(a: T, b: T): T {
    return a.length >= b.length ? a : b;
}
longest('Alice', 'Bob');       // works — strings have length
longest([1, 2, 3], [1, 2]);    // works — arrays have length
longest(10, 20);               // Error: number doesn't have 'length'

// T must be a key of U
function getProperty<T, K extends keyof T>(obj: T, key: K): T[K] {
    return obj[key];
}
const user = { id: 1, name: 'Alice' };
getProperty(user, 'name');  // string
getProperty(user, 'age');   // Error: 'age' doesn't exist on user
```

## Generic Interfaces

```ts
interface Stack<T> {
    push(item: T): void;
    pop(): T | undefined;
    peek(): T | undefined;
    isEmpty(): boolean;
    size: number;
}

interface ApiResponse<T> {
    data:    T;
    status:  number;
    message: string;
}

type UserResponse = ApiResponse<User>;
type ListResponse  = ApiResponse<User[]>;
```

## Generic Classes

```ts
class Repository<T extends { id: number }> {
    private items: Map<number, T> = new Map();

    save(item: T): T {
        this.items.set(item.id, item);
        return item;
    }

    findById(id: number): T | undefined {
        return this.items.get(id);
    }

    findAll(): T[] {
        return [...this.items.values()];
    }

    delete(id: number): boolean {
        return this.items.delete(id);
    }
}

const userRepo = new Repository<User>();
userRepo.save({ id: 1, name: 'Alice' });
userRepo.findById(1); // User | undefined
```

## Default Type Parameters

```ts
interface Paginated<T, Meta = Record<string, unknown>> {
    items: T[];
    total: number;
    page:  number;
    meta:  Meta;
}

// Use with default: Meta = Record<string, unknown>
const basicList: Paginated<User> = { items: [], total: 0, page: 1, meta: {} };

// Use with explicit Meta
type CursorMeta = { nextCursor: string | null };
const cursorList: Paginated<Post, CursorMeta> = {
    items: [],
    total: 0,
    page:  1,
    meta:  { nextCursor: null },
};
```

## Generic Utility: Nullable & NonNullable

```ts
type Nullable<T>    = T | null;
type NonNullable<T> = T extends null | undefined ? never : T;

function requireValue<T>(value: Nullable<T>, errorMsg: string): T {
    if (value === null) throw new Error(errorMsg);
    return value;
}
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t2->id,
                'title'             => 'Utility Types: Partial, Pick, Omit, Record & More',
                'estimated_minutes' => 18,
                'display_order'     => 2,
                'content'           => <<<'MARKDOWN'
## Built-in Utility Types

TypeScript ships a library of built-in utility types that transform existing types. Mastering them eliminates the need to write redundant type declarations.

## Partial & Required

```ts
interface User {
    id:    number;
    name:  string;
    email: string;
    role:  string;
}

// All properties become optional — great for update payloads
type UserUpdate = Partial<User>;

// All properties become required
type UserFull = Required<User>;

function updateUser(id: number, data: Partial<User>): User {
    // data can have any subset of User's properties
    return { ...currentUser, ...data };
}
```

## Readonly

```ts
type ImmutableUser = Readonly<User>;

const user: ImmutableUser = { id: 1, name: 'Alice', email: 'a@example.com', role: 'user' };
// user.name = 'Bob'; // Error: Cannot assign to 'name' — it's readonly
```

## Pick & Omit

```ts
// Keep only specified properties
type UserPreview = Pick<User, 'id' | 'name'>;
// Result: { id: number; name: string }

// Remove specified properties — useful for safe data transfer
type PublicUser = Omit<User, 'email' | 'role'>;
// Result: { id: number; name: string }

// Common pattern: safe API response
function sanitizeUser(user: User): PublicUser {
    const { email, role, ...safe } = user;
    return safe;
}
```

## Record

```ts
// Create a type with specific keys and a value type
type RolePermissions = Record<string, boolean>;
type UserDirectory   = Record<string, User>;

// With a string union as keys — stronger than Record<string, ...>
type StatusCode = 200 | 400 | 401 | 403 | 404 | 500;
type StatusMessages = Record<StatusCode, string>;

const messages: StatusMessages = {
    200: 'OK',
    400: 'Bad Request',
    401: 'Unauthorized',
    403: 'Forbidden',
    404: 'Not Found',
    500: 'Internal Server Error',
};
```

## Exclude & Extract

```ts
// Remove types from a union
type NonString = Exclude<string | number | boolean, string>;
// Result: number | boolean

// Keep only the types in the second argument
type StringsOnly = Extract<string | number | boolean, string | symbol>;
// Result: string
```

## NonNullable

```ts
// Remove null and undefined from a union
type DefinitelyString = NonNullable<string | null | undefined>;
// Result: string

function assertDefined<T>(value: T | null | undefined): NonNullable<T> {
    if (value == null) throw new Error('Expected value, got nullish');
    return value as NonNullable<T>;
}
```

## ReturnType & Parameters

```ts
function fetchUser(id: number, token: string): Promise<User> {
    return fetch(`/users/${id}`).then(r => r.json());
}

type FetchUserReturn = ReturnType<typeof fetchUser>; // Promise<User>
type FetchUserParams = Parameters<typeof fetchUser>; // [number, string]

// Useful for wrapping functions without duplicating type signatures
function withLogging<T extends (...args: any[]) => any>(
    fn: T
): (...args: Parameters<T>) => ReturnType<T> {
    return (...args) => {
        console.log('Calling:', fn.name);
        return fn(...args);
    };
}
```

## Awaited

```ts
// Unwrap the resolved type of a Promise
type AwaitedUser = Awaited<Promise<User>>;               // User
type NestedAwaited = Awaited<Promise<Promise<string>>>;  // string

// Useful when you want the return type of an async function
async function loadConfig() {
    return { theme: 'dark', lang: 'en' };
}
type Config = Awaited<ReturnType<typeof loadConfig>>;
// Config = { theme: string; lang: string }
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t2->id,
                'title'             => 'Enums, Tuples & Literal Types in Practice',
                'estimated_minutes' => 15,
                'display_order'     => 3,
                'content'           => <<<'MARKDOWN'
## Enums

Enums define a named set of related constants.

### Numeric Enums

```ts
enum Direction {
    Up,    // 0
    Down,  // 1
    Left,  // 2
    Right, // 3
}

function move(dir: Direction): void {
    console.log(`Moving in direction ${dir}`);
}
move(Direction.Up); // Moving in direction 0

// Numeric enums have reverse mappings
Direction[0]; // "Up"
Direction.Up; // 0
```

### String Enums

```ts
enum Status {
    Active   = 'ACTIVE',
    Inactive = 'INACTIVE',
    Pending  = 'PENDING',
    Banned   = 'BANNED',
}

// String enums are readable in logs and JSON
const user = { status: Status.Active };
JSON.stringify(user); // {"status":"ACTIVE"}

// No reverse mapping — Direction["ACTIVE"] would fail
```

### Const Enums

```ts
// const enum is erased at compile time — replaced with literal values
const enum HttpMethod {
    GET    = 'GET',
    POST   = 'POST',
    PUT    = 'PUT',
    DELETE = 'DELETE',
}

// Compiles to: const method = 'GET';
const method = HttpMethod.GET;
```

### When to Avoid Enums

Many TypeScript experts prefer **union types** over enums for better tree-shaking and simpler JS output:

```ts
// Instead of enum
type Direction = 'Up' | 'Down' | 'Left' | 'Right';

// Or a const object with 'as const'
const Direction = {
    Up:    'Up',
    Down:  'Down',
    Left:  'Left',
    Right: 'Right',
} as const;

type Direction = typeof Direction[keyof typeof Direction];
// = 'Up' | 'Down' | 'Left' | 'Right'
```

## Tuples

Tuples are fixed-length arrays where each position has a known type:

```ts
type Point2D = [number, number];
type Point3D = [number, number, number];
type Entry   = [string, number];

const origin: Point2D = [0, 0];
const [x, y] = origin; // destructuring — x: number, y: number

// Named tuples (TypeScript 4.0+) — improves readability
type Range = [start: number, end: number];
const r: Range = [1, 100];
```

### Optional & Rest in Tuples

```ts
// Optional last element
type WithOptional = [string, number, boolean?];

// Rest elements
type AtLeastTwo = [string, string, ...string[]];
type MixedTail  = [number, ...string[], boolean];
```

### Practical Tuple Use: useState-style return

```ts
function useToggle(initial = false): [boolean, () => void] {
    let value = initial;
    const toggle = () => { value = !value; };
    return [value, toggle];
}

const [isOpen, toggleOpen] = useToggle();
```

## Literal Types in Practice

Literal types restrict to exact values. Combined with unions they create type-safe APIs:

```ts
type Theme     = 'light' | 'dark' | 'system';
type FontSize   = 'sm' | 'md' | 'lg' | 'xl';
type Alignment  = 'left' | 'center' | 'right' | 'justify';

function setTheme(theme: Theme): void {
    document.body.dataset.theme = theme;
}

// Template literal types (TypeScript 4.1+)
type EventName<T extends string> = `on${Capitalize<T>}`;
type ClickEvent  = EventName<'click'>;  // 'onClick'
type ChangeEvent = EventName<'change'>; // 'onChange'
```
MARKDOWN,
            ],

            // ── LEVEL 3 ────────────────────────────────────────────────────
            [
                'topic_id'          => $t3->id,
                'title'             => 'Conditional Types & the infer Keyword',
                'estimated_minutes' => 20,
                'display_order'     => 1,
                'content'           => <<<'MARKDOWN'
## Conditional Types

Conditional types express type-level if/else logic:

```ts
type IsString<T> = T extends string ? true : false;

type A = IsString<string>; // true
type B = IsString<number>; // false
type C = IsString<'hello'>; // true — 'hello' extends string
```

The general syntax: `T extends U ? TrueType : FalseType`

## Practical Conditional Types

```ts
// Make a type non-nullable
type NonNullable<T> = T extends null | undefined ? never : T;

// Extract the element type of an array
type ElementType<T> = T extends (infer E)[] ? E : never;
type Elem = ElementType<string[]>; // string

// Flatten nested arrays one level
type Flatten<T> = T extends Array<infer Item> ? Item : T;
type F1 = Flatten<string[]>;  // string
type F2 = Flatten<number>;    // number (not an array — unchanged)
```

## The `infer` Keyword

`infer` captures a type variable inside the extends clause of a conditional type:

```ts
// Extract function return type
type ReturnType<T> = T extends (...args: any[]) => infer R ? R : never;

type R1 = ReturnType<() => string>;         // string
type R2 = ReturnType<(n: number) => void>;  // void

// Extract function parameters as a tuple
type Parameters<T> = T extends (...args: infer P) => any ? P : never;

type P1 = Parameters<(a: string, b: number) => void>; // [string, number]

// Unwrap a Promise
type Awaited<T> = T extends Promise<infer U> ? Awaited<U> : T;

type A1 = Awaited<Promise<string>>;           // string
type A2 = Awaited<Promise<Promise<number>>>;  // number
```

## Distributive Conditional Types

When the checked type is a bare type parameter, the conditional distributes over union members:

```ts
type ToArray<T> = T extends any ? T[] : never;

type A = ToArray<string | number>;
// = string[] | number[]   (NOT (string | number)[])

// Distribution happens because T is a bare type parameter
// Each union member is processed independently
```

To disable distribution, wrap T in a tuple:

```ts
type ToArrayNonDist<T> = [T] extends [any] ? T[] : never;

type B = ToArrayNonDist<string | number>;
// = (string | number)[]
```

## Infer in Multiple Positions

```ts
// Extract first and rest of a tuple
type First<T extends any[]> = T extends [infer F, ...any[]] ? F : never;
type Rest<T extends any[]>  = T extends [any, ...infer R]   ? R : never;

type F = First<[string, number, boolean]>; // string
type R = Rest<[string, number, boolean]>;  // [number, boolean]

// Extract constructor argument types
type ConstructorParameters<T extends new (...args: any[]) => any> =
    T extends new (...args: infer P) => any ? P : never;

class Point {
    constructor(public x: number, public y: number) {}
}
type PointArgs = ConstructorParameters<typeof Point>; // [number, number]
```

## `IsNever` and the Tuple Trick

```ts
// Naive — WRONG: bare T extends never distributes and always gives never
type IsNeverWrong<T> = T extends never ? true : false;
type Bad = IsNeverWrong<never>; // never (not true!)

// Correct: wrap in tuple to disable distribution
type IsNever<T> = [T] extends [never] ? true : false;
type Good = IsNever<never>;    // true
type Good2 = IsNever<string>;  // false
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t3->id,
                'title'             => 'Mapped Types & Type Transformations',
                'estimated_minutes' => 20,
                'display_order'     => 2,
                'content'           => <<<'MARKDOWN'
## What are Mapped Types?

Mapped types create a new type by iterating over the keys of an existing type:

```ts
// Syntax: { [K in KeyUnion]: ValueType }
type Stringify<T> = {
    [K in keyof T]: string;
};

interface User { id: number; name: string; active: boolean; }

type StringifiedUser = Stringify<User>;
// { id: string; name: string; active: string }
```

## How Built-in Utility Types Work

```ts
// Partial<T> — all properties optional
type Partial<T> = {
    [K in keyof T]?: T[K];
};

// Readonly<T> — all properties readonly
type Readonly<T> = {
    readonly [K in keyof T]: T[K];
};

// Required<T> — remove optionality
type Required<T> = {
    [K in keyof T]-?: T[K]; // -? removes the optional modifier
};

// Mutable<T> — remove readonly
type Mutable<T> = {
    -readonly [K in keyof T]: T[K]; // -readonly removes readonly modifier
};
```

## Modifier Prefixes: `+` and `-`

```ts
// + adds a modifier (default, rarely written explicitly)
type WithOptional<T> = { [K in keyof T]+?: T[K] }; // same as Partial

// - removes a modifier
type WithoutOptional<T>  = { [K in keyof T]-?: T[K] };    // same as Required
type WithoutReadonly<T>  = { -readonly [K in keyof T]: T[K] }; // removes readonly
```

## Key Remapping (TypeScript 4.1+)

Use `as` to rename keys in a mapped type:

```ts
// Prefix all keys with 'get'
type Getters<T> = {
    [K in keyof T as `get${Capitalize<string & K>}`]: () => T[K];
};

interface User { id: number; name: string; }

type UserGetters = Getters<User>;
// { getId: () => number; getName: () => string }

// Filter out certain keys by remapping to never
type OmitNullable<T> = {
    [K in keyof T as T[K] extends null | undefined ? never : K]: T[K];
};
```

## Template Literal Types with Mapped Types

```ts
// Convert an interface into event handlers
type EventHandlers<T> = {
    [K in keyof T as `on${Capitalize<string & K>}Change`]: (value: T[K]) => void;
};

interface FormFields {
    name:  string;
    email: string;
    age:   number;
}

type FormHandlers = EventHandlers<FormFields>;
// {
//   onNameChange:  (value: string) => void;
//   onEmailChange: (value: string) => void;
//   onAgeChange:   (value: number) => void;
// }
```

## Recursive Mapped Types

```ts
// DeepPartial — Partial applied recursively to all nested objects
type DeepPartial<T> = {
    [K in keyof T]?: T[K] extends object ? DeepPartial<T[K]> : T[K];
};

interface Config {
    server: { host: string; port: number };
    db:     { url: string; pool: number };
}

const partial: DeepPartial<Config> = {
    server: { host: 'localhost' }, // port is optional — and so is 'db'
};
```

## `keyof` and `typeof`

```ts
// keyof — produces a union of all key names of a type
interface User { id: number; name: string; }
type UserKey = keyof User; // 'id' | 'name'

// typeof — captures the type of a value
const config = { theme: 'dark', lang: 'en', fontSize: 16 };
type Config = typeof config;
// { theme: string; lang: string; fontSize: number }

// Combined: keyof typeof — get keys of an object value
type ConfigKey = keyof typeof config; // 'theme' | 'lang' | 'fontSize'

function getConfig<K extends keyof typeof config>(key: K): typeof config[K] {
    return config[key];
}
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t3->id,
                'title'             => 'Decorators, Declaration Merging & Module Augmentation',
                'estimated_minutes' => 18,
                'display_order'     => 3,
                'content'           => <<<'MARKDOWN'
## Decorators

Decorators are functions prefixed with `@` that modify classes, methods, properties, or parameters at definition time. Enable them in `tsconfig.json`:

```json
{
  "compilerOptions": {
    "experimentalDecorators": true,
    "emitDecoratorMetadata": true
  }
}
```

### Class Decorator

```ts
function Singleton<T extends { new(...args: any[]): {} }>(constructor: T) {
    let instance: InstanceType<T> | null = null;
    return class extends constructor {
        constructor(...args: any[]) {
            if (instance) return instance;
            super(...args);
            instance = this as unknown as InstanceType<T>;
        }
    };
}

@Singleton
class Database {
    connect() { console.log('Connecting...'); }
}

const db1 = new Database();
const db2 = new Database();
db1 === db2; // true — same instance
```

### Method Decorator

```ts
function log(target: any, key: string, descriptor: PropertyDescriptor) {
    const original = descriptor.value;
    descriptor.value = function (...args: any[]) {
        console.log(`Calling ${key} with`, args);
        const result = original.apply(this, args);
        console.log(`${key} returned`, result);
        return result;
    };
    return descriptor;
}

class Calculator {
    @log
    add(a: number, b: number): number {
        return a + b;
    }
}

new Calculator().add(2, 3);
// Calling add with [2, 3]
// add returned 5
```

### Property Decorator

```ts
function Required(target: any, propertyKey: string) {
    let value: any;
    Object.defineProperty(target, propertyKey, {
        get: () => value,
        set: (v) => {
            if (v === null || v === undefined) {
                throw new Error(`${propertyKey} is required`);
            }
            value = v;
        },
    });
}

class User {
    @Required
    name!: string;
}
```

## Declaration Merging

When you declare the same interface name multiple times, TypeScript merges all declarations into a single interface:

```ts
// First declaration — in types/user.ts
interface User {
    id:   number;
    name: string;
}

// Second declaration — in types/user-extended.ts
interface User {
    email: string; // merged into the same interface
}

// TypeScript sees:
// interface User { id: number; name: string; email: string; }

const user: User = { id: 1, name: 'Alice', email: 'a@example.com' };
```

## Augmenting Global Types

Declaration merging lets you extend built-in or third-party types:

```ts
// Extend the global Window interface
declare global {
    interface Window {
        __APP_VERSION__: string;
        analytics: { track(event: string, data?: object): void };
    }
}

window.__APP_VERSION__; // typed — no 'any' cast needed
window.analytics.track('page_view');
```

## Module Augmentation

Add types to an external module from within your own code:

```ts
// Augmenting Express Request type
import 'express';

declare module 'express' {
    interface Request {
        user?: { id: number; role: string };
    }
}

// Now request.user is typed in your middleware
app.use((req, res, next) => {
    req.user = { id: 1, role: 'admin' };
    next();
});
```

## Ambient Declarations

Declare values that exist at runtime but are defined elsewhere:

```ts
// Declare global constants injected by the bundler (Vite, webpack DefinePlugin)
declare const __DEV__:     boolean;
declare const __VERSION__: string;
declare const __API_URL__: string;

if (__DEV__) {
    console.log('Running in development mode');
}
```
MARKDOWN,
            ],

            // ── LEVEL 4 ────────────────────────────────────────────────────
            [
                'topic_id'          => $t4->id,
                'title'             => 'Advanced Type Patterns: Discriminated Unions & Exhaustiveness',
                'estimated_minutes' => 20,
                'display_order'     => 1,
                'content'           => <<<'MARKDOWN'
## Discriminated Unions

A discriminated union is the most important TypeScript pattern. Each union member has a shared literal type field (the **discriminant**) that uniquely identifies its type:

```ts
type Circle   = { kind: 'circle';   radius: number };
type Square   = { kind: 'square';   side: number };
type Triangle = { kind: 'triangle'; base: number; height: number };

type Shape = Circle | Square | Triangle;

function area(shape: Shape): number {
    switch (shape.kind) {
        case 'circle':   return Math.PI * shape.radius ** 2;
        case 'square':   return shape.side ** 2;
        case 'triangle': return 0.5 * shape.base * shape.height;
    }
}
```

## Exhaustiveness Checking

Assign the remaining case to `never` in the default branch. If you add a new variant and forget to handle it, TypeScript errors:

```ts
function assertNever(x: never): never {
    throw new Error(`Unhandled case: ${JSON.stringify(x)}`);
}

function area(shape: Shape): number {
    switch (shape.kind) {
        case 'circle':   return Math.PI * shape.radius ** 2;
        case 'square':   return shape.side ** 2;
        case 'triangle': return 0.5 * shape.base * shape.height;
        default:         return assertNever(shape); // Error if Shape gets a new variant
    }
}
```

## Result / Either Pattern

Model success and failure explicitly — the TypeScript equivalent of Rust's `Result<T, E>`:

```ts
type Ok<T>  = { success: true;  value: T };
type Err<E> = { success: false; error: E };
type Result<T, E = string> = Ok<T> | Err<E>;

function divide(a: number, b: number): Result<number> {
    if (b === 0) return { success: false, error: 'Division by zero' };
    return { success: true, value: a / b };
}

const result = divide(10, 2);
if (result.success) {
    console.log(result.value);  // number
} else {
    console.error(result.error); // string
}
```

## Recursive Types

Types can reference themselves to model tree-like structures:

```ts
type JSONValue =
    | string
    | number
    | boolean
    | null
    | JSONValue[]
    | { [key: string]: JSONValue };

type TreeNode<T> = {
    value:    T;
    children: TreeNode<T>[];
};

const tree: TreeNode<string> = {
    value:    'root',
    children: [
        { value: 'a', children: [] },
        { value: 'b', children: [{ value: 'b1', children: [] }] },
    ],
};
```

## Template Literal Type Patterns

```ts
// CSS property validation
type CSSUnit  = 'px' | 'em' | 'rem' | '%' | 'vw' | 'vh';
type CSSValue = `${number}${CSSUnit}`;
// type CSSValue = "0px" | "0em" | ... — but this would be infinite for number

// Better approach: use string & for documented patterns
type HexColor   = `#${string}`;
type EventHandler = `on${Capitalize<string>}`;

// Transforming object keys with template literals
type WithPrefix<T, Prefix extends string> = {
    [K in keyof T as `${Prefix}${Capitalize<string & K>}`]: T[K];
};

interface User { id: number; name: string; }
type PrefixedUser = WithPrefix<User, 'user'>;
// { userId: number; userName: string }
```

## `satisfies` Operator (TypeScript 4.9+)

Validate a value against a type while keeping the narrow inferred type:

```ts
const palette = {
    red:   [255, 0, 0],
    green: '#00ff00',
    blue:  [0, 0, 255],
} satisfies Record<string, string | number[]>;

// Without satisfies: palette.red would be string | number[]
// With satisfies: palette.red is still number[] (narrow) — TypeScript remembers
palette.red.map(v => v / 255); // OK — TypeScript knows it's number[]
palette.green.toUpperCase();   // OK — TypeScript knows it's string
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t4->id,
                'title'             => 'TypeScript with React: Component Types, Props & Hooks',
                'estimated_minutes' => 20,
                'display_order'     => 2,
                'content'           => <<<'MARKDOWN'
## Typing React Components

### Function Components

```tsx
// React.FC is no longer recommended — use explicit return type instead
interface ButtonProps {
    label:     string;
    onClick:   () => void;
    variant?:  'primary' | 'secondary' | 'ghost';
    disabled?: boolean;
    children?: React.ReactNode;
}

function Button({ label, onClick, variant = 'primary', disabled = false }: ButtonProps): JSX.Element {
    return (
        <button
            className={`btn btn--${variant}`}
            onClick={onClick}
            disabled={disabled}
        >
            {label}
        </button>
    );
}
```

### Children Patterns

```tsx
// ReactNode — most permissive (string, number, JSX, null, undefined, array)
interface LayoutProps {
    children: React.ReactNode;
}

// ReactElement — only JSX elements (not string/number)
interface WrapperProps {
    children: React.ReactElement;
}

// PropsWithChildren utility type
type CardProps = React.PropsWithChildren<{ title: string }>;
```

## Typing useState

```tsx
// TypeScript infers from initial value
const [count, setCount] = useState(0);           // number
const [name,  setName]  = useState('');           // string
const [user,  setUser]  = useState<User | null>(null); // needs explicit type

// With discriminated union state
type FetchState<T> =
    | { status: 'idle' }
    | { status: 'loading' }
    | { status: 'success'; data: T }
    | { status: 'error';   message: string };

const [state, setState] = useState<FetchState<User[]>>({ status: 'idle' });
```

## Typing useRef

```tsx
// DOM element refs — always null initially
const inputRef = useRef<HTMLInputElement>(null);
const divRef   = useRef<HTMLDivElement>(null);

// Mutable ref for values (not DOM elements) — no null
const timerRef = useRef<ReturnType<typeof setInterval> | null>(null);

function Component() {
    const inputRef = useRef<HTMLInputElement>(null);
    const focus = () => inputRef.current?.focus();
    return <input ref={inputRef} />;
}
```

## Typing useReducer

```tsx
type Action =
    | { type: 'increment' }
    | { type: 'decrement' }
    | { type: 'reset';    payload: number };

interface CounterState {
    count: number;
    history: number[];
}

function reducer(state: CounterState, action: Action): CounterState {
    switch (action.type) {
        case 'increment': return { ...state, count: state.count + 1, history: [...state.history, state.count + 1] };
        case 'decrement': return { ...state, count: state.count - 1, history: [...state.history, state.count - 1] };
        case 'reset':     return { count: action.payload, history: [action.payload] };
    }
}

const [state, dispatch] = useReducer(reducer, { count: 0, history: [] });
dispatch({ type: 'increment' });
dispatch({ type: 'reset', payload: 10 });
```

## Typing Event Handlers

```tsx
// Event types from React
function Form() {
    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        console.log(e.target.value);
    };

    const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
    };

    const handleClick = (e: React.MouseEvent<HTMLButtonElement>) => {
        console.log(e.clientX, e.clientY);
    };

    return (
        <form onSubmit={handleSubmit}>
            <input onChange={handleChange} />
            <button onClick={handleClick} type="submit">Submit</button>
        </form>
    );
}
```

## Generic Components

```tsx
interface ListProps<T> {
    items:      T[];
    renderItem: (item: T, index: number) => React.ReactNode;
    keyExtractor: (item: T) => string | number;
}

function List<T>({ items, renderItem, keyExtractor }: ListProps<T>) {
    return (
        <ul>
            {items.map((item, i) => (
                <li key={keyExtractor(item)}>{renderItem(item, i)}</li>
            ))}
        </ul>
    );
}

// Usage
<List
    items={users}
    renderItem={(user) => <span>{user.name}</span>}
    keyExtractor={(user) => user.id}
/>
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t4->id,
                'title'             => 'TypeScript Configuration, Build Tools & Project References',
                'estimated_minutes' => 18,
                'display_order'     => 3,
                'content'           => <<<'MARKDOWN'
## tsconfig.json Deep Dive

### Core Compiler Options

```json
{
    "compilerOptions": {
        // Target JS version output
        "target": "ES2022",

        // Module system
        "module": "ESNext",
        "moduleResolution": "bundler",

        // Strictness — always enable in new projects
        "strict": true,

        // strict enables all of these:
        "strictNullChecks": true,       // null/undefined are distinct types
        "noImplicitAny": true,          // error on implicit any
        "strictFunctionTypes": true,     // contravariant function params
        "strictPropertyInitialization": true,

        // Additional quality checks
        "noImplicitReturns": true,       // all code paths must return
        "noFallthroughCasesInSwitch": true,
        "noUncheckedIndexedAccess": true, // T[] indexing returns T | undefined

        // Output
        "outDir": "./dist",
        "declaration": true,             // emit .d.ts files
        "declarationMap": true,          // source maps for .d.ts files
        "sourceMap": true
    }
}
```

### Path Aliases

```json
{
    "compilerOptions": {
        "baseUrl": ".",
        "paths": {
            "@/*":          ["src/*"],
            "@components/*": ["src/components/*"],
            "@utils/*":     ["src/utils/*"],
            "@types/*":     ["src/types/*"]
        }
    }
}
```

Bundlers (Vite, webpack) need matching alias configuration:

```ts
// vite.config.ts
import { defineConfig } from 'vite';
import path from 'path';

export default defineConfig({
    resolve: {
        alias: { '@': path.resolve(__dirname, 'src') },
    },
});
```

## Project References (Monorepos)

Project references split large codebases into independently compilable sub-projects:

```json
// tsconfig.json (root)
{
    "references": [
        { "path": "./packages/shared" },
        { "path": "./packages/api" },
        { "path": "./packages/web" }
    ]
}

// packages/shared/tsconfig.json
{
    "compilerOptions": {
        "composite": true,   // required for referenced projects
        "outDir":    "./dist",
        "declaration": true
    }
}
```

Build incrementally with `tsc --build` — only changed projects are recompiled.

## Type Checking Only (No Emit)

```json
{
    "compilerOptions": {
        "noEmit": true // type-check only — let bundler handle output
    }
}
```

This is the recommended pattern when using Vite, webpack, or esbuild — TypeScript handles type safety, bundler handles transpilation.

## Declaration Files (.d.ts)

```ts
// Declaring a global variable injected by the environment
declare const __VERSION__: string;
declare const __DEV__: boolean;

// Declaring a module without types
declare module '*.svg' {
    const url: string;
    export default url;
}

// Augmenting an existing module
declare module 'express-serve-static-core' {
    interface Request {
        user?: AuthenticatedUser;
    }
}
```

## tsc vs Babel/esbuild/SWC

| Tool      | Type Checking | Transpilation Speed |
|-----------|---------------|---------------------|
| tsc       | Full          | Slowest             |
| Babel      | None          | Fast                |
| esbuild    | None          | Very fast           |
| SWC        | None          | Very fast           |
| ts-jest    | Optional      | Moderate            |

Modern setup: use esbuild/SWC for transpilation in dev/CI, run `tsc --noEmit` separately for type checking. Vite uses esbuild internally — TypeScript errors only show in your editor via the TS language server.
MARKDOWN,
            ],

            // ── LEVEL 5 ────────────────────────────────────────────────────
            [
                'topic_id'          => $t5->id,
                'title'             => 'Type-Level Programming: Recursive Types & Distributive Conditionals',
                'estimated_minutes' => 22,
                'display_order'     => 1,
                'content'           => <<<'MARKDOWN'
## What is Type-Level Programming?

TypeScript's type system is Turing-complete. Using conditional types, mapped types, `infer`, template literal types, and recursive types, you can compute and transform types at compile time. This goes beyond typing — it is meta-programming at the type level.

## Variadic Tuple Types (TypeScript 4.0+)

```ts
// Type-level tuple concatenation
type Concat<T extends unknown[], U extends unknown[]> = [...T, ...U];

type Result = Concat<[string, number], [boolean, null]>;
// [string, number, boolean, null]

// Prepend / Append
type Prepend<T, Arr extends unknown[]> = [T, ...Arr];
type Append<Arr extends unknown[], T>  = [...Arr, T];

type P = Prepend<string, [number, boolean]>; // [string, number, boolean]
type A = Append<[number, boolean], string>;  // [number, boolean, string]
```

## Tail-Recursive Types (TypeScript 4.5+)

TypeScript 4.5 introduced tail-call optimisation for conditional types, allowing much deeper recursion:

```ts
// Tail-recursive flatten
type FlattenTail<T extends unknown[], Acc extends unknown[] = []> =
    T extends [infer Head, ...infer Tail]
        ? Head extends unknown[]
            ? FlattenTail<[...Head, ...Tail], Acc>
            : FlattenTail<Tail, [...Acc, Head]>
        : Acc;

type Flat = FlattenTail<[1, [2, [3, [4]]], 5]>; // [1, 2, 3, 4, 5]
```

## String Manipulation at the Type Level

```ts
// Type-level string split
type Split<S extends string, D extends string> =
    S extends `${infer Head}${D}${infer Tail}`
        ? [Head, ...Split<Tail, D>]
        : [S];

type Words = Split<'hello world foo', ' '>; // ['hello', 'world', 'foo']

// Type-level camelCase to snake_case
type CamelToSnake<S extends string> =
    S extends `${infer Head}${infer Tail}`
        ? Head extends Uppercase<Head>
            ? `_${Lowercase<Head>}${CamelToSnake<Tail>}`
            : `${Head}${CamelToSnake<Tail>}`
        : S;

type Snake = CamelToSnake<'helloWorldFoo'>; // 'hello_world_foo'
```

## `UnionToIntersection`

Exploits contravariance to convert a union to an intersection:

```ts
type UnionToIntersection<U> =
    (U extends any ? (k: U) => void : never) extends (k: infer I) => void
        ? I
        : never;

type A = UnionToIntersection<{ a: number } | { b: string }>;
// { a: number } & { b: string }
```

## DeepReadonly

```ts
type DeepReadonly<T> =
    T extends (infer E)[]
        ? ReadonlyArray<DeepReadonly<E>>
        : T extends object
            ? { readonly [K in keyof T]: DeepReadonly<T[K]> }
            : T;

interface Config {
    server: { host: string; port: number };
    db:     { url: string; pool: { min: number; max: number } };
}

type FrozenConfig = DeepReadonly<Config>;
// All nested properties are readonly
```

## Path-Based Types (DeepGet)

```ts
// Extract the type at a dot-notation path
type DeepGet<T, K extends string> =
    K extends `${infer Head}.${infer Tail}`
        ? Head extends keyof T
            ? DeepGet<T[Head], Tail>
            : never
        : K extends keyof T
            ? T[K]
            : never;

interface Config {
    server: { host: string; port: number };
    db:     { url: string };
}

type Host = DeepGet<Config, 'server.host'>; // string
type Port = DeepGet<Config, 'server.port'>; // number
type Bad  = DeepGet<Config, 'server.missing'>; // never
```

## The `Prettify` Helper

Make complex intersections readable in IDE tooltips:

```ts
type Prettify<T> = {
    [K in keyof T]: T[K];
} & {};

// Without Prettify: { a: number } & { b: string } & { c: boolean }
// With Prettify:    { a: number; b: string; c: boolean }

type Merged = Prettify<{ a: number } & { b: string } & { c: boolean }>;
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t5->id,
                'title'             => 'Branded Types, Phantom Types & Nominal Typing',
                'estimated_minutes' => 18,
                'display_order'     => 2,
                'content'           => <<<'MARKDOWN'
## The Structural Typing Problem

TypeScript uses structural typing — two types are compatible if they have the same shape, regardless of their names:

```ts
type UserId  = string;
type PostId  = string;
type Email   = string;

function getUser(id: UserId): User { ... }

const postId: PostId = 'post-123';
getUser(postId); // No error! Both are string — TypeScript can't tell them apart
```

This is dangerous when `UserId` and `PostId` are semantically different but structurally identical.

## Branded / Nominal Types

Add a phantom property to distinguish structurally identical types:

```ts
type Brand<T, B extends string> = T & { readonly _brand: B };

type UserId = Brand<string, 'UserId'>;
type PostId = Brand<string, 'PostId'>;
type Email  = Brand<string, 'Email'>;

// Factory functions create branded values
function UserId(id: string): UserId {
    return id as UserId;
}
function PostId(id: string): PostId {
    return id as PostId;
}

function getUser(id: UserId): User { ... }

const userId = UserId('user-123');
const postId = PostId('post-456');

getUser(userId); // OK
getUser(postId); // Error: PostId is not assignable to UserId
getUser('raw'); // Error: string is not assignable to UserId
```

## Phantom Types

A phantom type parameter appears only at the type level — not in the runtime value:

```ts
type Currency<C extends string> = {
    amount:   number;
    readonly _currency: C;
};

function USD(amount: number): Currency<'USD'> {
    return { amount } as Currency<'USD'>;
}
function EUR(amount: number): Currency<'EUR'> {
    return { amount } as Currency<'EUR'>;
}

function addUSD(a: Currency<'USD'>, b: Currency<'USD'>): Currency<'USD'> {
    return USD(a.amount + b.amount);
}

const price   = USD(100);
const tax     = USD(10);
const euroAmt = EUR(50);

addUSD(price, tax);      // OK
addUSD(price, euroAmt);  // Error: Currency<'EUR'> is not Currency<'USD'>
```

## Opaque Types

Opaque types hide the underlying representation entirely — only the module that creates them knows the internal type:

```ts
declare const __opaque: unique symbol;

type Opaque<T, Tag extends string> = T & {
    readonly [__opaque]: Tag;
};

type Password   = Opaque<string, 'Password'>;
type HashedPwd  = Opaque<string, 'HashedPwd'>;

function hashPassword(raw: Password): HashedPwd {
    const hashed = bcrypt.hashSync(raw as string, 10);
    return hashed as HashedPwd;
}

function verifyPassword(raw: Password, hashed: HashedPwd): boolean {
    return bcrypt.compareSync(raw as string, hashed as string);
}

// Must go through hash — can't pass raw strings as HashedPwd
verifyPassword('raw' as Password, 'hash' as unknown as HashedPwd); // runtime bypass — but at least typed
```

## Type-Safe Builder Pattern

Track which required fields have been set at the type level:

```ts
type Unset = { readonly _unset: unique symbol };
const UNSET = {} as Unset;

class QueryBuilder<
    TTable extends string | Unset = Unset,
    TCondition extends string | Unset = Unset,
> {
    private table:     TTable      = UNSET as any;
    private condition: TCondition  = UNSET as any;
    private limitVal:  number | null = null;

    from<T extends string>(table: T): QueryBuilder<T, TCondition> {
        const next = new QueryBuilder<T, TCondition>();
        (next as any).table     = table;
        (next as any).condition = this.condition;
        (next as any).limitVal  = this.limitVal;
        return next;
    }

    where<C extends string>(cond: C): QueryBuilder<TTable, C> {
        const next = new QueryBuilder<TTable, C>();
        (next as any).table     = this.table;
        (next as any).condition = cond;
        (next as any).limitVal  = this.limitVal;
        return next;
    }

    // build() only available when both table and condition are set
    build(
        this: QueryBuilder<string, string>
    ): string {
        return `SELECT * FROM ${this.table} WHERE ${this.condition}`;
    }
}

const q = new QueryBuilder()
    .from('users')
    .where('active = 1')
    .build(); // OK

new QueryBuilder().build(); // Error: 'build' is not available (table and condition unset)
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t5->id,
                'title'             => 'TypeScript Ecosystem: ORMs, APIs & Full-Stack Type Safety',
                'estimated_minutes' => 20,
                'display_order'     => 3,
                'content'           => <<<'MARKDOWN'
## End-to-End Type Safety

The ultimate goal of TypeScript in a full-stack application: a single source of truth for types shared across the frontend and backend. If the backend changes an API field, the frontend shows a compile error — not a runtime bug.

## Zod: Runtime Validation + Static Types

Zod generates both a runtime validator and a TypeScript type from the same schema:

```ts
import { z } from 'zod';

// Define the schema once
const UserSchema = z.object({
    id:    z.number().positive(),
    name:  z.string().min(1).max(100),
    email: z.string().email(),
    role:  z.enum(['admin', 'user', 'moderator']),
    age:   z.number().min(0).max(150).optional(),
});

// Infer the TypeScript type from the schema
type User = z.infer<typeof UserSchema>;
// { id: number; name: string; email: string; role: 'admin' | 'user' | 'moderator'; age?: number }

// Runtime validation — safe at API boundaries
function parseUser(data: unknown): User {
    return UserSchema.parse(data); // throws ZodError if invalid
}

// Safe parse — returns success/error without throwing
const result = UserSchema.safeParse(unknownData);
if (result.success) {
    const user = result.data; // typed User
} else {
    console.error(result.error.flatten());
}
```

## tRPC: Type-Safe Remote Procedure Calls

tRPC lets you call server functions from the client with full type safety — no code generation required:

```ts
// Server: define procedures
import { initTRPC } from '@trpc/server';

const t = initTRPC.create();

export const appRouter = t.router({
    getUser: t.procedure
        .input(z.object({ id: z.number() }))
        .query(async ({ input }) => {
            return db.users.findById(input.id); // return type inferred
        }),

    createUser: t.procedure
        .input(UserSchema.omit({ id: true }))
        .mutation(async ({ input }) => {
            return db.users.create(input);
        }),
});

export type AppRouter = typeof appRouter;

// Client: call server functions with full type safety
import { createTRPCReact } from '@trpc/react-query';

const trpc = createTRPCReact<AppRouter>();

function UserComponent({ id }: { id: number }) {
    const { data: user } = trpc.getUser.useQuery({ id });
    // user is fully typed — same type as server return
}
```

## Prisma: Type-Safe ORM

Prisma generates TypeScript types from your database schema:

```ts
import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

// All queries are fully typed
const user = await prisma.user.findUnique({
    where: { id: 1 },
    select: { name: true, email: true }, // only selected fields in return type
});
// user: { name: string; email: string } | null

// Relations are typed
const userWithPosts = await prisma.user.findUnique({
    where: { id: 1 },
    include: { posts: { where: { published: true } } },
});
// userWithPosts.posts is Post[]
```

## OpenAPI / Type Generation

For REST APIs not using tRPC, generate TypeScript types from OpenAPI specs:

```ts
// Generated from OpenAPI spec with openapi-typescript
import type { paths } from './api-types'; // auto-generated

type GetUserResponse = paths['/users/{id}']['get']['responses']['200']['content']['application/json'];

// Type-safe fetch wrapper
async function apiGet<P extends keyof paths>(
    path: P
): Promise<paths[P]['get']['responses']['200']['content']['application/json']> {
    const res = await fetch(path as string);
    return res.json();
}
```

## Sharing Types in Monorepos

```
packages/
  shared/       <- shared TypeScript types and schemas
    src/types/
      user.ts
      post.ts
    src/schemas/ <- Zod schemas
  api/          <- depends on @shared
  web/          <- depends on @shared
```

```json
// packages/shared/package.json
{
    "name": "@myapp/shared",
    "exports": {
        "./types": "./dist/types/index.js",
        "./schemas": "./dist/schemas/index.js"
    }
}
```

This ensures that when the shared types change, both `api` and `web` recompile together — catching breaking changes immediately.
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

        $this->command->info('Lessons seeded for all 5 TypeScript levels.');
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
        $this->command->info("TypeScript Level 4: {$count} questions total.");
    }

    private function level4Questions(): array
    {
        return [
            [
                'question'    => 'What is a discriminated union and why is it the preferred pattern for type-safe state machines in TypeScript?',
                'explanation' => 'A discriminated union is a union where each member has a shared literal type field (the discriminant) that uniquely identifies its variant. TypeScript narrows the type based on checking the discriminant. It is the preferred pattern because narrowing is automatic, exhaustiveness checking is possible (assign remaining case to `never`), and adding a new variant causes compile errors wherever it is not handled.',
                'options'     => [
                    ['text' => 'A union where each member has a shared literal discriminant field that enables automatic narrowing and exhaustiveness checking', 'correct' => true],
                    ['text' => 'A union that only allows two members — one for success and one for failure', 'correct' => false],
                    ['text' => 'A union created using the & operator to combine multiple types', 'correct' => false],
                    ['text' => 'A union that automatically resolves to its narrowest member at compile time', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How does exhaustiveness checking work with discriminated unions in TypeScript?',
                'explanation' => 'Assign the remaining union member to `never` in the default branch of a switch/if chain: `const _check: never = value`. If you add a new union variant but forget to handle it, TypeScript errors because the unhandled variant is not assignable to `never`. A helper function `assertNever(x: never): never { throw new Error(...) }` makes this pattern reusable.',
                'options'     => [
                    ['text' => 'Assign the remaining value to never in the default branch — TypeScript errors if any variant is unhandled', 'correct' => true],
                    ['text' => 'TypeScript automatically throws at runtime when an unhandled union variant is encountered', 'correct' => false],
                    ['text' => 'Use the exhaustive() built-in function from the TypeScript standard library', 'correct' => false],
                    ['text' => 'TypeScript enforces exhaustiveness automatically without any extra code', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `satisfies` operator do in TypeScript 4.9+ and how does it differ from a type annotation?',
                'explanation' => '`satisfies` validates that a value conforms to a type but does NOT widen the inferred type the way an annotation does. With a type annotation, TypeScript widens to the annotated type, losing specific information. With `satisfies`, TypeScript keeps the narrow inferred type while still checking conformance. This lets you benefit from both type checking AND precise auto-complete on the narrower type.',
                'options'     => [
                    ['text' => 'Validates against a type while keeping the narrow inferred type — unlike annotation which widens to the annotated type', 'correct' => true],
                    ['text' => 'An alias for the as keyword that performs a checked type assertion', 'correct' => false],
                    ['text' => 'Forces the value to implement all interface methods before compilation', 'correct' => false],
                    ['text' => 'A runtime assertion that throws if the value does not satisfy the type', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you correctly type a React `useRef` for a DOM element vs a mutable value?',
                'explanation' => 'For DOM element refs, use `useRef<HTMLElement>(null)` — the initial value is null (the DOM element is attached later). TypeScript requires the generic type to include `null` for this case. For mutable values that are NOT DOM elements (timers, counters), use `useRef<number>(0)` — the initial value is provided directly, so `null` is not part of the type unless you need it.',
                'options'     => [
                    ['text' => 'DOM refs: useRef<HTMLElement>(null) with null initial; mutable values: useRef<T>(initialValue) without null', 'correct' => true],
                    ['text' => 'Always use useRef<T | null>(null) — null initial is always required', 'correct' => false],
                    ['text' => 'DOM refs use useRef<Element>(); mutable refs use useState() instead', 'correct' => false],
                    ['text' => 'useRef is untyped in TypeScript — always use as HTMLElement cast instead', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of `noUncheckedIndexedAccess` in tsconfig.json?',
                'explanation' => 'With `noUncheckedIndexedAccess` enabled, TypeScript adds `undefined` to the type of index signature access: `arr[0]` returns `T | undefined` instead of `T`. This forces you to check if the value exists before using it, catching array out-of-bounds bugs and missing dictionary key access at compile time. It is stricter than the default `strict` bundle.',
                'options'     => [
                    ['text' => 'Makes array and index signature access return T | undefined, forcing null checks before use', 'correct' => true],
                    ['text' => 'Prevents using [] bracket notation on objects — requires dot notation', 'correct' => false],
                    ['text' => 'Makes all array indices readonly, preventing mutation', 'correct' => false],
                    ['text' => 'Converts undefined array access into runtime exceptions automatically', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `interface extends` and `type &` (intersection) for combining object types?',
                'explanation' => 'Both create a type that includes all members of both types. The key differences: (1) `interface extends` requires the extended interfaces to be compatible — conflicting property types cause errors. (2) `type &` intersection silently merges conflicting types into `never` for the conflicting property instead of erroring. (3) `interface extends` can extend multiple interfaces and supports declaration merging. Use `extends` for class-like contracts; use `&` for ad-hoc type composition.',
                'options'     => [
                    ['text' => 'extends errors on conflicting property types; & resolves conflicts to never for that property; extends also supports declaration merging', 'correct' => true],
                    ['text' => 'They are identical — just different syntax for the same operation', 'correct' => false],
                    ['text' => 'extends creates a subtype; & creates a new independent type with no inheritance', 'correct' => false],
                    ['text' => 'extends only works for interfaces; & only works for type aliases', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do template literal types combine with mapped types to transform object key names?',
                'explanation' => 'In a mapped type, the `as` clause remaps keys to new names. Template literal types inside `as` allow generating new key names from existing ones. For example: `{ [K in keyof T as \`get${Capitalize<string & K>}\`]: () => T[K] }` transforms `{ name: string }` into `{ getName: () => string }`. This enables powerful code-generation-style patterns at the type level.',
                'options'     => [
                    ['text' => 'Use the `as` clause in mapped types with template literal expressions to generate new key names from old ones', 'correct' => true],
                    ['text' => 'Template literal types cannot be used inside mapped types — they only work with string literals', 'correct' => false],
                    ['text' => 'Use Object.keys() with string template at runtime to rename mapped type keys', 'correct' => false],
                    ['text' => 'Template literal types in mapped types require the --experimental flag in tsconfig', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does TypeScript project references (`"composite": true`) enable in a monorepo?',
                'explanation' => '`"composite": true` in a tsconfig marks the project as a referenceable sub-project. Combined with `references` in the root tsconfig and `tsc --build`, TypeScript compiles only projects whose sources have changed since the last build (incremental compilation). Each project must also emit declaration files (`declaration: true`). This dramatically reduces build times in large monorepos.',
                'options'     => [
                    ['text' => 'Enables incremental compilation — only changed sub-projects rebuild, dramatically reducing build time in monorepos', 'correct' => true],
                    ['text' => 'Allows importing types from other npm packages without @types declarations', 'correct' => false],
                    ['text' => 'Combines multiple tsconfig files into one unified configuration', 'correct' => false],
                    ['text' => 'Enables sharing runtime values between projects without re-bundling', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the role of `moduleResolution: "bundler"` in tsconfig.json (TypeScript 5+)?',
                'explanation' => '`moduleResolution: "bundler"` is a new strategy in TypeScript 5 designed for projects using bundlers like Vite, webpack, or esbuild. It allows using bare extension-less imports (`import "./foo"` instead of `import "./foo.js"`), supports the `exports` field in package.json, and allows `import type` without `"type": "module"` in package.json. It is the recommended strategy for modern bundled TypeScript projects.',
                'options'     => [
                    ['text' => 'A resolution strategy for bundled projects that supports exports field, extension-less imports, and modern package conventions', 'correct' => true],
                    ['text' => 'Makes TypeScript use the bundler\'s own module resolver instead of the TypeScript resolver', 'correct' => false],
                    ['text' => 'A legacy mode for projects that use CommonJS require() instead of ESM import', 'correct' => false],
                    ['text' => 'Bundles all TypeScript files into a single output file automatically', 'correct' => false],
                ],
            ],
            [
                'question'    => 'In TypeScript with React, when should you use `React.FC` and why do many teams avoid it?',
                'explanation' => '`React.FC` (formerly `React.FunctionComponent`) used to implicitly include `children` in props and had a few other quirks. Since React 18 removed the implicit children, `React.FC` is less controversial, but many teams still avoid it because: (1) it prevents using function overloads for components, (2) it is verbose when you can just type the return value, (3) it adds a layer of indirection. The recommended modern pattern is explicit parameter types and return type `JSX.Element` or `React.ReactElement`.',
                'options'     => [
                    ['text' => 'Many teams avoid React.FC because it prevents overloads, is verbose, and pre-React 18 had implicit children — explicit types are cleaner', 'correct' => true],
                    ['text' => 'React.FC is required for all functional components — avoiding it is a TypeScript error', 'correct' => false],
                    ['text' => 'React.FC should be used exclusively — it provides automatic performance optimisations', 'correct' => false],
                    ['text' => 'React.FC works only for class components — function components must use React.FunctionComponent', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How does Zod\'s `z.infer<typeof Schema>` work and why is it better than manually writing types?',
                'explanation' => '`z.infer<typeof Schema>` is a conditional type that extracts the TypeScript type from a Zod schema. Since Zod schemas are values (not types), `typeof Schema` gives the TypeScript type of the schema object. Zod\'s `infer` then uses conditional types and `infer` to extract the output type. This is better than manually writing types because: the schema and type are always in sync, runtime validation and static types share the same source of truth, and changes to the schema automatically update the type.',
                'options'     => [
                    ['text' => 'Extracts the TypeScript type from a Zod schema — both stay in sync since they share one definition', 'correct' => true],
                    ['text' => 'Converts Zod runtime errors into TypeScript compile-time errors', 'correct' => false],
                    ['text' => 'Generates a Zod schema from an existing TypeScript type automatically', 'correct' => false],
                    ['text' => 'Infers what the validated output will be at runtime without type annotations', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the key benefit of tRPC over traditional REST APIs with OpenAPI codegen for TypeScript projects?',
                'explanation' => 'tRPC provides end-to-end type safety without any code generation step. The server defines procedures with typed inputs and outputs; the client automatically receives those types via the exported `AppRouter` type. Any change to the server\'s input or output type immediately shows as a TypeScript error on the client — no need to regenerate types, run OpenAPI tooling, or manually maintain separate type files. This eliminates an entire class of integration bugs.',
                'options'     => [
                    ['text' => 'Provides end-to-end type safety without code generation — server type changes immediately appear as client errors', 'correct' => true],
                    ['text' => 'tRPC is faster at runtime than REST because it uses a binary protocol instead of JSON', 'correct' => false],
                    ['text' => 'tRPC generates OpenAPI docs automatically from TypeScript types', 'correct' => false],
                    ['text' => 'tRPC replaces HTTP entirely — it uses WebSockets for all communication', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `DeepPartial<T>` and how does it differ from `Partial<T>`?',
                'explanation' => '`Partial<T>` makes only the top-level properties optional. Nested objects still require all their properties. `DeepPartial<T>` applies `Partial` recursively to all nested objects: `type DeepPartial<T> = { [K in keyof T]?: T[K] extends object ? DeepPartial<T[K]> : T[K] }`. This is useful for deeply nested update payloads, configuration merging, or test fixtures where only some nested fields need to be specified.',
                'options'     => [
                    ['text' => 'Partial makes only top-level properties optional; DeepPartial recursively makes all nested object properties optional too', 'correct' => true],
                    ['text' => 'They are identical — Partial already recurses through all nesting levels', 'correct' => false],
                    ['text' => 'DeepPartial removes all nested objects entirely, leaving only primitive properties', 'correct' => false],
                    ['text' => 'DeepPartial is a built-in TypeScript utility type available in the standard library', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `-?` mean in a mapped type modifier and which built-in utility type uses it?',
                'explanation' => 'In a mapped type, `-?` removes the optional modifier from properties. Without it, `?` makes a property optional. With `-?`, even originally optional properties become required. TypeScript\'s built-in `Required<T>` utility type is implemented as `{ [K in keyof T]-?: T[K] }`. Similarly, `-readonly` removes readonly modifiers.',
                'options'     => [
                    ['text' => '-? removes the optional modifier from properties — used by Required<T> to make all properties required', 'correct' => true],
                    ['text' => '-? marks a property as never-nullable (removes null from the type)', 'correct' => false],
                    ['text' => '-? is a syntax error — modifiers can only be added with +?, not removed', 'correct' => false],
                    ['text' => '-? prevents a property from appearing in JSON.stringify output', 'correct' => false],
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
        $this->command->info("TypeScript Level 5: {$count} questions total.");
    }

    private function level5Questions(): array
    {
        return [
            [
                'question'    => 'What is the structural typing problem that branded types solve in TypeScript?',
                'explanation' => 'TypeScript uses structural typing: two types are compatible if they have the same shape, regardless of their names. This means `type UserId = string` and `type PostId = string` are interchangeable — a function expecting UserId will accept PostId without error, because both are just `string`. Branded types add a phantom property to create distinct identities: `type UserId = string & { _brand: "UserId" }`, making them incompatible despite the same base type.',
                'options'     => [
                    ['text' => 'Structurally identical types (both string) are interchangeable — branded types add a phantom property to distinguish them', 'correct' => true],
                    ['text' => 'Branded types solve the problem of strings being mutable — they make the string immutable', 'correct' => false],
                    ['text' => 'Branded types convert runtime string values into type-safe integer IDs', 'correct' => false],
                    ['text' => 'TypeScript normally treats string subtypes as incompatible — branded types fix this', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Why does `type IsNever<T> = T extends never ? true : false` not work correctly, and what is the fix?',
                'explanation' => 'The issue is distributivity: when `T` is `never`, the conditional type distributes over its members. But `never` has zero members — so the distribution yields `never` (not `true`). The fix is to wrap `T` in a tuple to disable distribution: `type IsNever<T> = [T] extends [never] ? true : false`. With the tuple, TypeScript checks if `[T]` extends `[never]` without distributing, which works correctly when T is never.',
                'options'     => [
                    ['text' => 'Bare T distributes over never\'s zero members giving never; fix with [T] extends [never] to disable distribution', 'correct' => true],
                    ['text' => 'never extends never is always false in TypeScript — it is a known bug', 'correct' => false],
                    ['text' => 'The conditional type needs infer: T extends infer N ? (N extends never ? true : false) : false', 'correct' => false],
                    ['text' => 'The issue is that true/false are not valid conditional type results for never checks', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `UnionToIntersection<U>` and what TypeScript mechanism makes it possible?',
                'explanation' => '`UnionToIntersection<U>` converts a union to an intersection. It works by exploiting contravariance in function parameter positions: `(U extends any ? (k: U) => void : never) extends (k: infer I) => void ? I : never`. The distributive conditional produces a function overload intersection; `infer I` in the contravariant position captures the intersection of all union members. This is an advanced type trick used in libraries like `ts-toolbelt`.',
                'options'     => [
                    ['text' => 'Exploits contravariance in function parameter positions — distributive conditional creates an overload intersection, infer captures it', 'correct' => true],
                    ['text' => 'Uses the built-in & operator applied recursively to all union members', 'correct' => false],
                    ['text' => 'A built-in TypeScript utility type that reverses the | operator', 'correct' => false],
                    ['text' => 'Uses mapped types to iterate over union members and intersect them one by one', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are variadic tuple types (TypeScript 4.0+) and what problem do they solve?',
                'explanation' => 'Variadic tuple types allow spread elements in tuple type positions: `type Concat<T extends unknown[], U extends unknown[]> = [...T, ...U]`. Before TypeScript 4.0, it was impossible to type operations like tuple concatenation, argument forwarding, or curry at the type level — you could only express fixed-length tuples. Variadic tuples enable precise typing of rest/spread patterns, argument forwarding in higher-order functions, and type-level array manipulation.',
                'options'     => [
                    ['text' => 'Spread elements in tuple types enabling type-level concat, argument forwarding, and rest/spread operations — impossible before TypeScript 4.0', 'correct' => true],
                    ['text' => 'Arrays with a variable length that can grow or shrink at runtime', 'correct' => false],
                    ['text' => 'Tuples that can contain elements of different types at each position', 'correct' => false],
                    ['text' => 'A syntax for defining function overloads using tuple argument types', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is tail-call optimisation for recursive types introduced in TypeScript 4.5?',
                'explanation' => 'TypeScript has a depth limit for recursive type evaluation to prevent infinite loops. Types that recurse in "tail position" (where the recursive call is the outermost type operation — not nested inside another type) are optimised in TypeScript 4.5+, allowing much deeper recursion without hitting the "Type instantiation is excessively deep" error. Non-tail recursive types still hit the limit sooner. Using an accumulator parameter pattern (like tail-recursive functions) enables the optimisation.',
                'options'     => [
                    ['text' => 'Recursive types in tail position (outermost call) are optimised in TypeScript 4.5+, allowing deeper recursion before hitting depth limits', 'correct' => true],
                    ['text' => 'A runtime optimisation where V8 reuses stack frames for recursive TypeScript generics', 'correct' => false],
                    ['text' => 'TypeScript 4.5 removed all depth limits for recursive types', 'correct' => false],
                    ['text' => 'TypeScript converts tail-recursive types to iterative mapped types automatically for better performance', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you extract nested object types using dot-notation string paths at the type level?',
                'explanation' => 'Use template literal types with `infer` to parse the path, then recurse: `type DeepGet<T, K extends string> = K extends \`\${infer Head}.\${infer Tail}\` ? Head extends keyof T ? DeepGet<T[Head], Tail> : never : K extends keyof T ? T[K] : never`. This splits the path into Head and Tail at the dot, looks up Head in T, then recurses with Tail. Base case: a key with no dot is a direct keyof lookup.',
                'options'     => [
                    ['text' => 'Split path at dot using template literal infer, look up Head in T, recurse with Tail — base case is a direct keyof lookup', 'correct' => true],
                    ['text' => 'Use Object.get() built-in — TypeScript infers the nested type automatically', 'correct' => false],
                    ['text' => 'Convert the path to an array with Split<>, then use tuple indexing to traverse', 'correct' => false],
                    ['text' => 'TypeScript does not support dot-notation path types — you must use Pick for each level', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `Prettify<T>` helper type and when is it useful?',
                'explanation' => '`type Prettify<T> = { [K in keyof T]: T[K] } & {}` forces TypeScript to expand a complex intersection type into a flat object type in IDE tooltips and error messages. When you hover over a type like `A & B & C` in your editor, TypeScript shows the full intersection expression — which is hard to read. Prettify flattens it into `{ a: ...; b: ...; c: ... }`. The `& {}` at the end prevents TypeScript from collapsing it back.',
                'options'     => [
                    ['text' => 'Expands intersections into a flat object type for readable IDE tooltips — maps all keys to force expansion', 'correct' => true],
                    ['text' => 'Converts a type to a string representation of its shape for debugging', 'correct' => false],
                    ['text' => 'Removes all phantom and brand properties from a type to get the clean base type', 'correct' => false],
                    ['text' => 'A built-in TypeScript utility type introduced in TypeScript 5.0', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How does `type IsAny<T>` detect the any type in TypeScript?',
                'explanation' => 'Detecting `any` is non-trivial because `any` is assignable to and from everything. A common implementation: `type IsAny<T> = 0 extends (1 & T) ? true : false`. For most types, `1 & T` narrows to `1` — and `0 extends 1` is false. When T is `any`, the intersection `1 & any` collapses to `any` (any absorbs everything in intersections), making `0 extends any` evaluate to true. This exploits any\'s special absorption behaviour.',
                'options'     => [
                    ['text' => 'Uses `0 extends (1 & T)` — for any type, 1 & any collapses to any, making 0 extends any evaluate to true', 'correct' => true],
                    ['text' => 'T extends any ? true : false — correctly identifies any because it is the only type extending everything', 'correct' => false],
                    ['text' => 'IsAny<T> is a built-in TypeScript utility type in the standard library', 'correct' => false],
                    ['text' => 'Uses typeof at the type level to compare against the "any" string literal', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `unique symbol` type in TypeScript and how does it enable nominal typing?',
                'explanation' => '`unique symbol` is a subtype of `symbol` where each `const` declaration creates a distinct symbol type — even if two are structurally identical. `const a: unique symbol = Symbol()` and `const b: unique symbol = Symbol()` have different types: `typeof a` and `typeof b` are incompatible. This is used for branded/opaque types: `type UserId = string & { readonly _brand: typeof __userIdBrand }` where `declare const __userIdBrand: unique symbol` — making the brand truly unique.',
                'options'     => [
                    ['text' => 'A distinct symbol type per const declaration — two unique symbols are incompatible even if both are symbol, enabling true nominal branding', 'correct' => true],
                    ['text' => 'A built-in singleton symbol shared across all modules — useful for cross-module identity', 'correct' => false],
                    ['text' => 'A symbol that is guaranteed to be unique across all JavaScript runtimes', 'correct' => false],
                    ['text' => 'An alternative to string literals for enum-style type discrimination', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between covariance and contravariance in TypeScript\'s type system?',
                'explanation' => 'Covariance: if `Dog extends Animal`, then `Producer<Dog>` is assignable to `Producer<Animal>` — subtype in position → subtype of the wrapper. Return types of functions are covariant. Contravariance: `Consumer<Animal>` is assignable to `Consumer<Dog>` — the direction flips for consumers. Function parameter types are contravariant (with `strictFunctionTypes`). A function that accepts `Animal` can safely be used where a function accepting `Dog` is expected — because `Animal` is more general. TypeScript enforces contravariance for function params with `--strictFunctionTypes`.',
                'options'     => [
                    ['text' => 'Covariant: subtype in → subtype out (return types). Contravariant: subtype in → supertype out (parameter types). strictFunctionTypes enforces this.', 'correct' => true],
                    ['text' => 'Covariant means the type is readonly; contravariant means the type is writable', 'correct' => false],
                    ['text' => 'Covariance and contravariance only apply to generic classes, not function types', 'correct' => false],
                    ['text' => 'They are identical concepts — TypeScript uses them interchangeably for function types', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How does the `infer` keyword work in multiple positions within a single conditional type?',
                'explanation' => '`infer` can appear multiple times and in many positions within the extends clause of a conditional type. Each creates an independent type variable. For example: `T extends { method(a: infer A, b: infer B): infer R } ? [A, B, R] : never` captures two parameter types and the return type simultaneously. You can also use `infer` to extract tuple heads, tails, constructor arguments, and Promise-resolved types by placing it in the appropriate structural position.',
                'options'     => [
                    ['text' => 'Multiple infer variables can capture different structural positions simultaneously — parameters, return type, tuple head/tail, etc.', 'correct' => true],
                    ['text' => 'Only one infer is allowed per conditional type — multiple require nested conditionals', 'correct' => false],
                    ['text' => 'infer in multiple positions always produces a union of all captured types', 'correct' => false],
                    ['text' => 'Multiple infer usage requires the --experimentalInfer tsconfig flag', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are TypeScript\'s four intrinsic string manipulation types and how are they implemented?',
                'explanation' => 'TypeScript 4.1 added four intrinsic string manipulation types: `Uppercase<S>`, `Lowercase<S>`, `Capitalize<S>`, and `Uncapitalize<S>`. They are "intrinsic" — built directly into the TypeScript compiler, not implemented via conditional types in user-land. They operate on string literal types at compile time. They are commonly used with template literal types and mapped types to transform key names programmatically: `type EventName<T extends string> = \`on${Capitalize<T>}\``.',
                'options'     => [
                    ['text' => 'Uppercase, Lowercase, Capitalize, Uncapitalize — compiler-intrinsic types that operate on string literals, used with template literal types', 'correct' => true],
                    ['text' => 'toUpper, toLower, ucFirst, lcFirst — available as type utilities from the @types/typescript package', 'correct' => false],
                    ['text' => 'TypeScript does not have built-in string manipulation types — these must be implemented in user-land', 'correct' => false],
                    ['text' => 'StringUpper, StringLower, StringCap, StringUncap — available since TypeScript 3.0', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the type-safe builder pattern and how does it use generics to prevent calling `build()` prematurely?',
                'explanation' => 'The type-safe builder pattern uses generic type parameters to track which required fields have been set. Each setter returns a new builder type with that field\'s type parameter changed from `Unset` to the actual type. The `build()` method is only callable when the `this` type has all required fields set to real types (not `Unset`). TypeScript errors at compile time if `build()` is called before all required setters have been invoked — no runtime validation needed.',
                'options'     => [
                    ['text' => 'Generic parameters track set vs unset fields; build() uses a this: constraint requiring all fields to be non-Unset — TypeScript errors if called early', 'correct' => true],
                    ['text' => 'Uses runtime checks in build() that throw if required fields are missing', 'correct' => false],
                    ['text' => 'Uses optional fields in the builder — build() fills them with defaults if not set', 'correct' => false],
                    ['text' => 'The builder pattern is always safe in TypeScript — generics are not needed for build() safety', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How does TypeScript\'s `declare module` enable augmenting third-party library types?',
                'explanation' => '`declare module "module-name" { ... }` is a module augmentation that extends the type definitions of an existing module from a separate file. When TypeScript loads this declaration, it merges the new members with the original module\'s types. This is commonly used to extend Express Request/Response, add properties to Vue\'s `this`, or add custom properties to any external type without modifying the original `node_modules`. The augmentation file must itself be a module (have at least one import/export).',
                'options'     => [
                    ['text' => 'Re-declares the module name in a separate file — TypeScript merges new members with the original; file must be a module (have import/export)', 'correct' => true],
                    ['text' => 'Replaces the third-party module\'s types entirely with your custom definitions', 'correct' => false],
                    ['text' => 'Creates a patched version of the module that overrides it for your project only', 'correct' => false],
                    ['text' => 'declare module is only for ambient global declarations — it cannot augment npm packages', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `type-level programming` in TypeScript and what operations can be expressed at the type level?',
                'explanation' => 'Type-level programming uses TypeScript\'s type system as a compile-time computation engine. Using conditional types, mapped types, `infer`, recursive types, and template literal types, you can: split strings, concatenate tuples, count tuple length, filter union members, transform object keys, check if types are equal, convert unions to tuples, and more — all at compile time with zero runtime cost. Libraries like `ts-toolbelt` and `type-fest` expose hundreds of such utilities. TypeScript\'s type system is Turing-complete.',
                'options'     => [
                    ['text' => 'Compile-time computation using conditional/mapped/infer/recursive types — string splitting, tuple ops, union filtering, key transformation, and more', 'correct' => true],
                    ['text' => 'Writing TypeScript programs that generate other TypeScript programs as output', 'correct' => false],
                    ['text' => 'Using the TypeScript compiler API to manipulate AST nodes at build time', 'correct' => false],
                    ['text' => 'Running TypeScript code in a REPL to test types interactively before compilation', 'correct' => false],
                ],
            ],
        ];
    }
}
