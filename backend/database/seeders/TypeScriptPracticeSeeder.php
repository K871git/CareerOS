<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;

class TypeScriptPracticeSeeder extends Seeder
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

        $levels = [
            [
                'title'         => 'TypeScript Basics — Junior',
                'slug'          => 'typescript-junior',
                'description'   => 'Type annotations, interfaces, union types, and core TypeScript fundamentals. Perfect for junior-level interview preparation.',
                'display_order' => 1,
                'difficulty'    => 'Easy',
                'questions'     => $this->juniorQuestions(),
            ],
            [
                'title'         => 'TypeScript Intermediate',
                'slug'          => 'typescript-intermediate',
                'description'   => 'Generics, enums, utility types, and advanced type compositions. For developers targeting mid-level roles.',
                'display_order' => 2,
                'difficulty'    => 'Medium',
                'questions'     => $this->intermediateQuestions(),
            ],
            [
                'title'         => 'TypeScript Advanced',
                'slug'          => 'typescript-advanced',
                'description'   => 'Conditional types, mapped types, decorators, and declaration merging. Essential for senior developer interviews.',
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

        $this->command->info('TypeScript Practice seeded: 1 subject, 3 topics, ~100 questions.');
    }

    private function juniorQuestions(): array
    {
        return [
            // ── Original 10 ──────────────────────────────────────────────────
            [
                'question'    => 'What is TypeScript?',
                'explanation' => 'TypeScript is a statically typed superset of JavaScript developed by Microsoft. It adds optional type annotations and compiles down to plain JavaScript. TypeScript catches type errors at compile-time rather than at runtime, making large codebases more maintainable.',
                'options'     => [
                    ['text' => 'A typed superset of JavaScript that compiles to plain JavaScript', 'correct' => true],
                    ['text' => 'A completely separate programming language unrelated to JavaScript', 'correct' => false],
                    ['text' => 'A JavaScript runtime like Node.js', 'correct' => false],
                    ['text' => 'A CSS preprocessor similar to SASS', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you declare a typed variable in TypeScript?',
                'explanation' => 'Type annotations in TypeScript are written after the variable name, separated by a colon. For example: `let name: string = "Alice";`. The type annotation is optional — TypeScript can infer types from the assigned value.',
                'options'     => [
                    ['text' => 'let name: string = "Alice";', 'correct' => true],
                    ['text' => 'let string name = "Alice";', 'correct' => false],
                    ['text' => 'let name = string("Alice");', 'correct' => false],
                    ['text' => 'string let name = "Alice";', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is type inference in TypeScript?',
                'explanation' => 'Type inference means TypeScript automatically deduces the type of a variable based on its initial value, without requiring an explicit type annotation. For example, `let count = 5;` infers `count` as `number`. TypeScript uses inference extensively to reduce annotation verbosity.',
                'options'     => [
                    ['text' => 'TypeScript automatically deduces the type from an assigned value', 'correct' => true],
                    ['text' => 'TypeScript converts one type to another at runtime', 'correct' => false],
                    ['text' => 'A feature that allows any type to be used anywhere', 'correct' => false],
                    ['text' => 'Explicit type annotations written by the developer', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an interface in TypeScript?',
                'explanation' => 'An interface defines a contract — the shape an object must conform to. It specifies which properties and methods an object should have, along with their types. Interfaces are purely a compile-time construct and produce no JavaScript output.',
                'options'     => [
                    ['text' => 'A contract describing the shape (properties and methods) an object must have', 'correct' => true],
                    ['text' => 'A built-in class that all TypeScript objects must extend', 'correct' => false],
                    ['text' => 'A runtime type-checking mechanism', 'correct' => false],
                    ['text' => 'A module that exports functions', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a union type in TypeScript?',
                'explanation' => 'A union type allows a variable to hold a value of multiple possible types, written with the pipe symbol: `string | number`. A variable typed as `string | number` can be either a string or a number. TypeScript ensures you handle all possible types when using the variable.',
                'options'     => [
                    ['text' => 'A type that allows a value to be one of several types: string | number', 'correct' => true],
                    ['text' => 'A type that merges two objects into one', 'correct' => false],
                    ['text' => 'A type that can only be used inside union functions', 'correct' => false],
                    ['text' => 'An array type that holds multiple values', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you mark an interface property as optional in TypeScript?',
                'explanation' => 'Adding a `?` after a property name in an interface makes it optional. For example: `interface User { name: string; age?: number; }` — `age` can be present or absent. Accessing an optional property that is absent returns `undefined`.',
                'options'     => [
                    ['text' => 'Add a ? after the property name: name?: string', 'correct' => true],
                    ['text' => 'Add the keyword optional before the property: optional name: string', 'correct' => false],
                    ['text' => 'Wrap the type in Optional<T>: name: Optional<string>', 'correct' => false],
                    ['text' => 'Assign a default value: name: string = ""', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `any` type in TypeScript?',
                'explanation' => '`any` is a special type that disables type checking for a variable — TypeScript will accept any value and allow any operations on it. While it provides an escape hatch, overusing `any` defeats the purpose of TypeScript. `unknown` is a safer alternative when the type is truly not known.',
                'options'     => [
                    ['text' => 'A type that opts out of type checking and accepts any value', 'correct' => true],
                    ['text' => 'A type alias for all primitive types', 'correct' => false],
                    ['text' => 'The default type when no annotation is provided', 'correct' => false],
                    ['text' => 'A type that can only hold objects', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you annotate an array of numbers in TypeScript?',
                'explanation' => 'There are two equivalent syntaxes for typed arrays in TypeScript: `number[]` (shorthand) or `Array<number>` (generic notation). Both produce the same type. The shorthand `number[]` is more common for simple cases; the generic form is clearer for complex nested types.',
                'options'     => [
                    ['text' => 'number[] or Array<number> — both are equivalent', 'correct' => true],
                    ['text' => 'Array(number) only', 'correct' => false],
                    ['text' => '[number] only', 'correct' => false],
                    ['text' => 'numbers[] only', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does a `void` return type indicate on a function?',
                'explanation' => 'A `void` return type means the function does not return a meaningful value (it returns `undefined`). It is commonly used for functions that perform side effects, like logging or event handlers. A function with `void` return type can still have a `return;` statement with no value.',
                'options'     => [
                    ['text' => 'The function returns nothing (no meaningful return value)', 'correct' => true],
                    ['text' => 'The function returns null', 'correct' => false],
                    ['text' => 'The function is asynchronous', 'correct' => false],
                    ['text' => 'The function never finishes executing', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a type alias in TypeScript?',
                'explanation' => 'A type alias creates a named reference to any type using the `type` keyword: `type UserId = string | number;`. Unlike interfaces, type aliases can represent primitives, unions, intersections, tuples, and more. Type aliases and interfaces are often interchangeable for object shapes.',
                'options'     => [
                    ['text' => 'A named reference to any type, created with the type keyword', 'correct' => true],
                    ['text' => 'An alias for importing a module with a different name', 'correct' => false],
                    ['text' => 'A runtime substitute for a class', 'correct' => false],
                    ['text' => 'A way to rename built-in TypeScript types', 'correct' => false],
                ],
            ],
            // ── New additions: 23 more ────────────────────────────────────────
            [
                'question'    => 'What does enabling `strict` mode in tsconfig.json do?',
                'explanation' => 'The `strict` flag in tsconfig.json is a shorthand that enables a suite of strict type-checking options, including `strictNullChecks`, `strictFunctionTypes`, `strictBindCallApply`, `noImplicitAny`, and others. It is the recommended setting for new projects and catches many common type errors that are missed without it.',
                'options'     => [
                    ['text' => 'Enables a suite of strict type-checking compiler options at once', 'correct' => true],
                    ['text' => 'Prevents all use of the any type', 'correct' => false],
                    ['text' => 'Adds runtime type validation on every function call', 'correct' => false],
                    ['text' => 'Locks the TypeScript version so it cannot be upgraded', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of `tsconfig.json`?',
                'explanation' => '`tsconfig.json` is the TypeScript compiler configuration file. It specifies the root files and compiler options (such as `target`, `module`, `strict`, `outDir`) for a TypeScript project. When present in a directory, it marks that directory as the root of a TypeScript project.',
                'options'     => [
                    ['text' => 'Configures the TypeScript compiler options and project root', 'correct' => true],
                    ['text' => 'Defines runtime environment variables for the application', 'correct' => false],
                    ['text' => 'Replaces package.json for TypeScript projects', 'correct' => false],
                    ['text' => 'Stores type definitions for third-party libraries', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a tuple type in TypeScript?',
                'explanation' => 'A tuple is a fixed-length array where each position has a known, specific type. For example: `let point: [number, number] = [10, 20];`. Unlike regular arrays, tuples enforce both the number of elements and the type at each index. Tuples are useful for representing structured data like coordinates or function return pairs.',
                'options'     => [
                    ['text' => 'A fixed-length array where each element has a specific type at each index', 'correct' => true],
                    ['text' => 'A generic array that can hold elements of any type', 'correct' => false],
                    ['text' => 'An object with numeric keys', 'correct' => false],
                    ['text' => 'A readonly array whose values cannot change', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are literal types in TypeScript?',
                'explanation' => 'Literal types restrict a variable to a specific, exact value rather than a broad type. For example: `let direction: "left" | "right";` — this variable can only hold the string `"left"` or `"right"`. Literal types enable type-safe state machines and discriminated unions.',
                'options'     => [
                    ['text' => 'Types that restrict a variable to a specific, exact value such as "left" or 42', 'correct' => true],
                    ['text' => 'Types that are written as plain text without generics', 'correct' => false],
                    ['text' => 'Types inferred from literal objects at runtime', 'correct' => false],
                    ['text' => 'String-only types used in template expressions', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are function overloads in TypeScript?',
                'explanation' => 'Function overloads let you declare multiple signatures for a single function. You write several overload signatures followed by one implementation signature. TypeScript uses the overload signatures for type checking; the implementation signature (which must be compatible with all overloads) is invisible to callers.',
                'options'     => [
                    ['text' => 'Multiple type signatures for a single function that handles different argument types', 'correct' => true],
                    ['text' => 'Defining the same function in two different files', 'correct' => false],
                    ['text' => 'Extending a parent class method with a new implementation', 'correct' => false],
                    ['text' => 'Using rest parameters to handle variable argument counts', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you create a readonly array in TypeScript?',
                'explanation' => '`ReadonlyArray<T>` (or the shorthand `readonly T[]`) creates an array type that cannot be mutated — methods like `push`, `pop`, and `splice` are removed from its type. This prevents accidental mutation. Note that it only enforces immutability at the type level, not at runtime.',
                'options'     => [
                    ['text' => 'ReadonlyArray<T> or readonly T[] — both remove mutating array methods', 'correct' => true],
                    ['text' => 'const arr: T[] — const makes arrays immutable', 'correct' => false],
                    ['text' => 'frozen<T[]> — the frozen generic makes arrays immutable', 'correct' => false],
                    ['text' => 'Immutable<T[]> — a built-in utility type for arrays', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `as const` do to an object or array literal?',
                'explanation' => '`as const` is a const assertion that tells TypeScript to infer the narrowest possible type for a value. For objects, all properties become `readonly` with literal types. For arrays, they become readonly tuples with literal element types. This is useful for configuration objects and lookup tables.',
                'options'     => [
                    ['text' => 'Infers the narrowest literal types and marks all properties as readonly', 'correct' => true],
                    ['text' => 'Calls Object.freeze() on the value at runtime', 'correct' => false],
                    ['text' => 'Converts the value to a constant expression evaluated at build time', 'correct' => false],
                    ['text' => 'Prevents the variable from being exported from its module', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you import only a type in TypeScript?',
                'explanation' => '`import type { Foo } from "./foo"` is a type-only import. It guarantees the import is erased at runtime and never emits any JavaScript. This is useful for avoiding circular dependencies and is required when `isolatedModules` is enabled. Regular `import` can also import types, but `import type` makes the intent explicit.',
                'options'     => [
                    ['text' => 'import type { Foo } from "./foo" — erased at compile time, no runtime output', 'correct' => true],
                    ['text' => 'import interface { Foo } from "./foo"', 'correct' => false],
                    ['text' => 'import { type: Foo } from "./foo"', 'correct' => false],
                    ['text' => 'require type("./foo").Foo', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is type narrowing using `typeof` in TypeScript?',
                'explanation' => 'Type narrowing is the process of refining a broad type to a more specific one within a conditional block. Using `typeof value === "string"` inside an `if` block causes TypeScript to treat `value` as `string` inside that block. This allows safe access to type-specific methods without a type assertion.',
                'options'     => [
                    ['text' => 'Using typeof in a condition to let TypeScript refine a union to a specific type', 'correct' => true],
                    ['text' => 'Converting a value from one type to another at runtime using typeof', 'correct' => false],
                    ['text' => 'A runtime check that throws if the type does not match', 'correct' => false],
                    ['text' => 'An alternative to type assertions that works on classes only', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the nullish coalescing operator (`??`) do in TypeScript?',
                'explanation' => 'The `??` operator returns the right-hand side operand when the left-hand side is `null` or `undefined`; otherwise it returns the left-hand side. Unlike `||`, it does not treat falsy values like `0`, `""`, or `false` as triggers. This makes it safer for providing default values for nullable fields.',
                'options'     => [
                    ['text' => 'Returns the right-hand value only when the left side is null or undefined', 'correct' => true],
                    ['text' => 'Returns the right-hand value for any falsy left-hand value (like || does)', 'correct' => false],
                    ['text' => 'Checks if a value is null and throws if it is', 'correct' => false],
                    ['text' => 'Merges two nullable objects into one non-null object', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does optional chaining (`?.`) do in TypeScript?',
                'explanation' => 'The `?.` operator short-circuits property access, method calls, or array indexing when the left-hand side is `null` or `undefined`, returning `undefined` instead of throwing a runtime error. For example, `user?.address?.city` will not throw if `user` or `address` is nullish.',
                'options'     => [
                    ['text' => 'Short-circuits to undefined when the left side is null or undefined instead of throwing', 'correct' => true],
                    ['text' => 'Makes a property optional in an interface definition', 'correct' => false],
                    ['text' => 'Checks if an optional property exists and converts it to required', 'correct' => false],
                    ['text' => 'Creates an optional chain of middleware functions', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are the three class access modifiers in TypeScript?',
                'explanation' => 'TypeScript classes have three access modifiers: `public` (accessible from anywhere — the default), `private` (accessible only within the class), and `protected` (accessible within the class and its subclasses). These are compile-time checks; they do not affect the emitted JavaScript.',
                'options'     => [
                    ['text' => 'public (default, anywhere), private (class only), protected (class and subclasses)', 'correct' => true],
                    ['text' => 'public, private, internal — same as C#', 'correct' => false],
                    ['text' => 'open, closed, sealed — borrowed from Kotlin', 'correct' => false],
                    ['text' => 'visible, hidden, shared — TypeScript-specific modifiers', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an abstract class in TypeScript?',
                'explanation' => 'An abstract class is a class that cannot be instantiated directly — it is only meant to be extended. It can contain abstract methods (declared but not implemented) that subclasses must implement. Abstract classes provide a template for a family of related classes while sharing common implementation.',
                'options'     => [
                    ['text' => 'A class that cannot be instantiated and may contain abstract methods subclasses must implement', 'correct' => true],
                    ['text' => 'A class with no methods or properties — a pure type definition', 'correct' => false],
                    ['text' => 'A class whose properties are all optional by default', 'correct' => false],
                    ['text' => 'A class that automatically implements all interface methods', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `implements` keyword do in TypeScript?',
                'explanation' => 'The `implements` keyword checks that a class satisfies the contract defined by an interface. If the class is missing required properties or methods, TypeScript reports a compile-time error. A class can implement multiple interfaces. `implements` does not copy any interface members into the class — it only enforces the contract.',
                'options'     => [
                    ['text' => 'Checks at compile time that a class satisfies an interface contract', 'correct' => true],
                    ['text' => 'Copies all interface members into the class automatically', 'correct' => false],
                    ['text' => 'Extends a class with additional behavior at runtime', 'correct' => false],
                    ['text' => 'Makes the class a singleton by implementing its own constructor', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between a numeric enum and a string enum in TypeScript?',
                'explanation' => 'Numeric enums (the default) auto-assign integer values starting at 0. They also generate reverse mappings so you can go from value back to name. String enums require each member to be initialized with a string literal. String enums are more readable in serialized output (JSON/logs) and do not generate reverse mappings.',
                'options'     => [
                    ['text' => 'Numeric enums auto-increment from 0 and have reverse mappings; string enums use string literals and do not', 'correct' => true],
                    ['text' => 'They are identical — just different initialization syntax', 'correct' => false],
                    ['text' => 'String enums are compiled to JavaScript classes; numeric enums are not', 'correct' => false],
                    ['text' => 'Numeric enums are deprecated; string enums are preferred in all cases', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a type predicate (`is`) in TypeScript?',
                'explanation' => 'A type predicate is a return type annotation of the form `paramName is Type`. When a function with a type predicate returns `true`, TypeScript narrows the argument to the specified type in the calling scope. This allows building custom type guards: `function isString(val: unknown): val is string { return typeof val === "string"; }`.',
                'options'     => [
                    ['text' => 'A return annotation (val is Type) that tells TypeScript to narrow the type when the function returns true', 'correct' => true],
                    ['text' => 'A runtime assertion that throws if the value is not of the given type', 'correct' => false],
                    ['text' => 'An operator similar to instanceof that works for primitives', 'correct' => false],
                    ['text' => 'A keyword for checking enum membership', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `satisfies` operator do in TypeScript?',
                'explanation' => 'The `satisfies` operator (introduced in TypeScript 4.9) validates that a value conforms to a type without widening the inferred type. Unlike a type annotation, `satisfies` keeps the specific literal type so you still benefit from auto-complete. Example: `const palette = { red: [255, 0, 0] } satisfies Record<string, number[]>;` — TypeScript knows `palette.red` is `number[]`, not just `number[] | string`.',
                'options'     => [
                    ['text' => 'Validates that a value matches a type while preserving its narrow inferred type', 'correct' => true],
                    ['text' => 'An alias for the as keyword that performs a type assertion', 'correct' => false],
                    ['text' => 'Forces a value to be compatible with an interface at runtime', 'correct' => false],
                    ['text' => 'Checks that all interface methods are implemented before compilation', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an index signature in TypeScript?',
                'explanation' => 'An index signature describes the types for properties whose names are not known ahead of time. The syntax is `{ [key: string]: number }` — meaning any string key maps to a number value. Index signatures are useful for dictionaries and dynamic objects, but they also apply to all known properties, so all named properties must match the value type.',
                'options'     => [
                    ['text' => 'A syntax { [key: string]: T } that describes dynamically named properties on an object type', 'correct' => true],
                    ['text' => 'A way to sort the keys of a type alphabetically', 'correct' => false],
                    ['text' => 'An array index that is typed as a number', 'correct' => false],
                    ['text' => 'A decorator that adds metadata to class property names', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `extends` do when used as a generic constraint?',
                'explanation' => 'When used in a generic definition like `<T extends SomeType>`, `extends` restricts T to types that are assignable to SomeType. For example: `function getLength<T extends { length: number }>(arg: T): number` — T must have a `length` property. This lets you call `arg.length` safely inside the function.',
                'options'     => [
                    ['text' => 'Restricts the generic type parameter to be assignable to the given type', 'correct' => true],
                    ['text' => 'Makes the generic parameter inherit all methods from the given type', 'correct' => false],
                    ['text' => 'Prevents the generic from being used with primitive types', 'correct' => false],
                    ['text' => 'An alias for implements when used with generics', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are default generic type parameters in TypeScript?',
                'explanation' => 'You can provide a default type for a generic parameter using `= Type`. For example: `interface Response<T = string> { data: T; }` — if T is not specified, it defaults to `string`. Default type parameters reduce verbosity when a generic has a common use case.',
                'options'     => [
                    ['text' => 'A fallback type for a generic parameter used when no type argument is provided', 'correct' => true],
                    ['text' => 'Runtime default values assigned to generic variables', 'correct' => false],
                    ['text' => 'The any type automatically applied when a generic is omitted', 'correct' => false],
                    ['text' => 'A way to make a generic function work without type arguments at all', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `Required<T>` utility type do?',
                'explanation' => '`Required<T>` constructs a type where all optional properties of T are made required. It is the opposite of `Partial<T>`. This is useful when you need to assert that a fully populated object is present, for example after applying defaults to a configuration object.',
                'options'     => [
                    ['text' => 'Converts all optional properties of T to required properties', 'correct' => true],
                    ['text' => 'Makes all properties of T readonly', 'correct' => false],
                    ['text' => 'Removes all optional properties from T entirely', 'correct' => false],
                    ['text' => 'Adds a required validator decorator to all properties of T', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `Pick<T, K>` utility type do?',
                'explanation' => '`Pick<T, K>` constructs a type by selecting only the properties K from type T. For example: `Pick<User, "id" | "name">` creates a new type with only the `id` and `name` properties. It is the complement of `Omit<T, K>` and is useful for creating lean subsets of large types.',
                'options'     => [
                    ['text' => 'Creates a new type containing only the specified properties K from T', 'correct' => true],
                    ['text' => 'Removes the specified properties K from T', 'correct' => false],
                    ['text' => 'Makes the specified properties K optional in T', 'correct' => false],
                    ['text' => 'Returns the type of the first matching property in T', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a declaration file (`.d.ts`) in TypeScript?',
                'explanation' => 'A `.d.ts` file is a TypeScript declaration file that contains only type information — no executable code. It describes the types of an existing JavaScript library or module so TypeScript can type-check code that uses it. Declaration files are published in `@types/*` packages and are also generated by `tsc` when `declaration: true` is set.',
                'options'     => [
                    ['text' => 'A file containing only type declarations that describes the shape of a JavaScript module', 'correct' => true],
                    ['text' => 'A compiled TypeScript file that contains minified JavaScript', 'correct' => false],
                    ['text' => 'A configuration file that declares environment variables', 'correct' => false],
                    ['text' => 'A module that re-exports types from multiple other files', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `strictNullChecks` do in TypeScript?',
                'explanation' => 'With `strictNullChecks` enabled, `null` and `undefined` are not assignable to other types by default. A variable of type `string` cannot hold `null` unless explicitly typed as `string | null`. This catches a large class of common null/undefined runtime errors at compile time. It is included in the `strict` flag bundle.',
                'options'     => [
                    ['text' => 'Makes null and undefined separate types that cannot be assigned to other types without explicit union', 'correct' => true],
                    ['text' => 'Throws a runtime error whenever null is accessed', 'correct' => false],
                    ['text' => 'Removes null and undefined from the TypeScript type system entirely', 'correct' => false],
                    ['text' => 'Automatically adds null checks to all generated JavaScript code', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `Omit<T, K>` utility type do?',
                'explanation' => '`Omit<T, K>` constructs a type by removing the properties K from type T. For example: `Omit<User, "password">` creates a type with all User properties except `password`. It is the complement of `Pick<T, K>` and is commonly used to create safe data-transfer types that exclude sensitive fields.',
                'options'     => [
                    ['text' => 'Creates a new type with the specified properties K removed from T', 'correct' => true],
                    ['text' => 'Creates a new type containing only the properties K from T', 'correct' => false],
                    ['text' => 'Makes the specified properties K optional in T', 'correct' => false],
                    ['text' => 'Deletes the properties K from an object value at runtime', 'correct' => false],
                ],
            ],
        ];
    }

    private function intermediateQuestions(): array
    {
        return [
            // ── Original 10 ──────────────────────────────────────────────────
            [
                'question'    => 'What are generics in TypeScript?',
                'explanation' => 'Generics allow you to write reusable code that works with any type while maintaining type safety. The `<T>` syntax defines a type parameter that acts as a placeholder: `function identity<T>(arg: T): T`. When called, T is inferred from the argument or explicitly provided.',
                'options'     => [
                    ['text' => 'Type parameters (<T>) that allow functions and classes to work with any type safely', 'correct' => true],
                    ['text' => 'A way to generate random values of any type', 'correct' => false],
                    ['text' => 'Built-in utility types like Partial and Required', 'correct' => false],
                    ['text' => 'Runtime type assertions using the as keyword', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the main difference between an interface and a type alias in TypeScript?',
                'explanation' => 'Interfaces can be reopened and extended via declaration merging — declaring the same interface twice merges their members. Type aliases cannot be merged this way. However, type aliases support features interfaces do not: union types, intersection types, tuples, and primitive aliases. For object shapes, both are nearly equivalent.',
                'options'     => [
                    ['text' => 'Interfaces support declaration merging; type aliases cannot be reopened or merged', 'correct' => true],
                    ['text' => 'Type aliases are slower at runtime; interfaces are compiled to classes', 'correct' => false],
                    ['text' => 'Interfaces can only describe object shapes; type aliases can only describe primitives', 'correct' => false],
                    ['text' => 'They are completely identical — just different syntax', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `readonly` modifier do in TypeScript?',
                'explanation' => '`readonly` prevents a property from being reassigned after it is initialized. For example: `interface Point { readonly x: number; readonly y: number; }`. Attempting to reassign a `readonly` property causes a compile-time error. It is similar to `const` but for object properties.',
                'options'     => [
                    ['text' => 'Prevents a property from being reassigned after initialization', 'correct' => true],
                    ['text' => 'Makes a property invisible to TypeScript (skips type checking)', 'correct' => false],
                    ['text' => 'Makes all methods on the type return immutable values', 'correct' => false],
                    ['text' => 'Equivalent to private — hides the property from outside the class', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an enum in TypeScript?',
                'explanation' => 'An enum is a named collection of related constants. TypeScript supports numeric enums (default, starting at 0) and string enums. Enums improve code readability by replacing magic numbers/strings with named values. Example: `enum Direction { Up, Down, Left, Right }`.',
                'options'     => [
                    ['text' => 'A named set of numeric or string constants for better code readability', 'correct' => true],
                    ['text' => 'A type that only allows values from a fixed list of runtime objects', 'correct' => false],
                    ['text' => 'A generic wrapper type for arrays', 'correct' => false],
                    ['text' => 'An alternative to interfaces for defining object shapes', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `Partial<T>` do in TypeScript?',
                'explanation' => '`Partial<T>` is a built-in utility type that constructs a type with all properties of T set to optional. This is useful when you want to represent an object where any subset of properties may be provided, such as update payloads in REST APIs: `function update(data: Partial<User>)`.',
                'options'     => [
                    ['text' => 'Makes all properties of T optional', 'correct' => true],
                    ['text' => 'Makes all properties of T required', 'correct' => false],
                    ['text' => 'Makes all properties of T readonly', 'correct' => false],
                    ['text' => 'Removes all optional properties from T', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an intersection type in TypeScript?',
                'explanation' => 'An intersection type combines multiple types into one using the `&` operator. A value of type `A & B` must satisfy all properties of both A and B. Example: `type AdminUser = User & Admin;` means AdminUser has all properties from both User and Admin.',
                'options'     => [
                    ['text' => 'A type that combines multiple types into one using the & operator', 'correct' => true],
                    ['text' => 'A type that allows only the properties shared by all combined types', 'correct' => false],
                    ['text' => 'An alias for union types using |', 'correct' => false],
                    ['text' => 'A type that represents the overlap between two arrays', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `unknown` type and how does it differ from `any`?',
                'explanation' => 'Both `unknown` and `any` can hold any value. The difference is that `unknown` requires a type check (narrowing) before you can perform operations on it, while `any` bypasses all type checking. `unknown` is the type-safe alternative to `any` — prefer it when the type is genuinely not known.',
                'options'     => [
                    ['text' => 'unknown requires type narrowing before use; any bypasses all type checks', 'correct' => true],
                    ['text' => 'unknown is the same as any but only valid in function parameters', 'correct' => false],
                    ['text' => 'any requires narrowing; unknown does not', 'correct' => false],
                    ['text' => 'They are identical — just different names for the same type', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a type assertion in TypeScript?',
                'explanation' => 'A type assertion (`value as Type`) tells the TypeScript compiler to treat a value as a specific type, overriding its inferred type. It does not perform any runtime conversion — it is purely a compile-time instruction. Use it when you know more about a value\'s type than TypeScript can infer.',
                'options'     => [
                    ['text' => 'A compile-time instruction to treat a value as a specific type using as', 'correct' => true],
                    ['text' => 'A runtime type conversion that changes the actual value', 'correct' => false],
                    ['text' => 'A way to assert that a value is never null', 'correct' => false],
                    ['text' => 'A decorator that validates types at runtime', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `Record<K, V>` do in TypeScript?',
                'explanation' => '`Record<K, V>` constructs a type with keys of type K and values of type V. Example: `Record<string, number>` creates an object where all keys are strings and all values are numbers. It is useful for representing dictionaries or maps with known key and value types.',
                'options'     => [
                    ['text' => 'Creates an object type with keys of type K and values of type V', 'correct' => true],
                    ['text' => 'Records all changes to a variable at runtime', 'correct' => false],
                    ['text' => 'Makes all properties in an object required', 'correct' => false],
                    ['text' => 'A type alias for Map<K, V>', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `never` type in TypeScript?',
                'explanation' => 'The `never` type represents values that never occur. A function that always throws an error or runs an infinite loop has return type `never`. It is also used for exhaustive type checks — when all union members are handled in if/switch branches, the remaining type is `never`, which TypeScript can verify.',
                'options'     => [
                    ['text' => 'A type for values that never occur — functions that always throw or loop forever', 'correct' => true],
                    ['text' => 'A type equivalent to undefined', 'correct' => false],
                    ['text' => 'A type that cannot be assigned any value, including null', 'correct' => false],
                    ['text' => 'An alias for void used in abstract methods', 'correct' => false],
                ],
            ],
            // ── New additions: 23 more ────────────────────────────────────────
            [
                'question'    => 'What does `ReturnType<T>` do in TypeScript?',
                'explanation' => '`ReturnType<T>` is a built-in utility type that extracts the return type of a function type T. For example: `type R = ReturnType<() => string>` gives `string`. It is implemented using `infer` inside a conditional type: `T extends (...args: any[]) => infer R ? R : never`.',
                'options'     => [
                    ['text' => 'Extracts the return type of a function type', 'correct' => true],
                    ['text' => 'Wraps a function so it always returns a defined value', 'correct' => false],
                    ['text' => 'Makes all return values of a function readonly', 'correct' => false],
                    ['text' => 'Converts a function type to its async equivalent', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `Parameters<T>` do in TypeScript?',
                'explanation' => '`Parameters<T>` extracts the parameter types of a function type T as a tuple. For example: `type P = Parameters<(a: string, b: number) => void>` gives `[string, number]`. It is useful for wrapping functions or forwarding arguments with type safety.',
                'options'     => [
                    ['text' => 'Extracts the parameter types of a function type as a tuple', 'correct' => true],
                    ['text' => 'Returns the number of parameters a function accepts', 'correct' => false],
                    ['text' => 'Makes all function parameters optional', 'correct' => false],
                    ['text' => 'Converts function parameters to an object type', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `InstanceType<T>` do in TypeScript?',
                'explanation' => '`InstanceType<T>` extracts the instance type of a constructor function type T. For example: `type I = InstanceType<typeof MyClass>` gives the type of instances of `MyClass`. This is useful when working with class references passed as arguments or stored in variables.',
                'options'     => [
                    ['text' => 'Extracts the instance type produced by a constructor function type', 'correct' => true],
                    ['text' => 'Creates a new instance of the given class type', 'correct' => false],
                    ['text' => 'Returns the prototype type of a class', 'correct' => false],
                    ['text' => 'Makes all class instance properties optional', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `Awaited<T>` do in TypeScript?',
                'explanation' => '`Awaited<T>` recursively unwraps the type that a Promise resolves to. For example: `Awaited<Promise<string>>` gives `string`, and `Awaited<Promise<Promise<number>>>` gives `number`. It is used to express the type of `await`-ing an expression and is used internally by `ReturnType` for async functions.',
                'options'     => [
                    ['text' => 'Recursively unwraps the resolved type of a Promise', 'correct' => true],
                    ['text' => 'Converts a synchronous function type to an async one', 'correct' => false],
                    ['text' => 'Waits for a type to be defined before compilation continues', 'correct' => false],
                    ['text' => 'Makes a Promise type nullable', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you define a generic function with multiple type parameters in TypeScript?',
                'explanation' => 'You list multiple type parameters inside the angle brackets separated by commas: `function merge<T, U>(a: T, b: U): T & U`. Each parameter is independent and can have its own constraints. Multiple type parameters are common in utility functions like `merge`, `zip`, or `map`.',
                'options'     => [
                    ['text' => 'List them comma-separated inside angle brackets: function merge<T, U>(a: T, b: U): T & U', 'correct' => true],
                    ['text' => 'Use nested generics: function merge<T<U>>(a: T, b: U)', 'correct' => false],
                    ['text' => 'Declare them as separate type aliases before the function', 'correct' => false],
                    ['text' => 'TypeScript only supports a single type parameter per function', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an overloaded function signature in TypeScript?',
                'explanation' => 'An overloaded function has multiple declaration signatures followed by one implementation signature. The overload signatures define the valid call patterns; the implementation signature (which must be broader) contains the actual body. TypeScript uses overloads to provide precise return types depending on what arguments are passed.',
                'options'     => [
                    ['text' => 'Multiple call signatures for a function to handle different argument/return type combinations precisely', 'correct' => true],
                    ['text' => 'Calling the same function from different files', 'correct' => false],
                    ['text' => 'Overriding a parent class method in a subclass', 'correct' => false],
                    ['text' => 'Using rest parameters to accept varying argument counts', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you make a class generic in TypeScript?',
                'explanation' => 'Add type parameters after the class name: `class Stack<T> { private items: T[] = []; push(item: T): void { this.items.push(item); } pop(): T | undefined { return this.items.pop(); } }`. The type parameter T is used throughout the class, and TypeScript enforces type safety when the class is instantiated with a specific type.',
                'options'     => [
                    ['text' => 'Add type parameters after the class name: class Stack<T> { ... }', 'correct' => true],
                    ['text' => 'Use the generic keyword before the class keyword', 'correct' => false],
                    ['text' => 'Pass types as constructor arguments: new Stack(string)', 'correct' => false],
                    ['text' => 'Extend the Generic base class: class Stack extends Generic', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are mapped type modifiers (`+`/`-` with `readonly`/`?`) in TypeScript?',
                'explanation' => 'In mapped types, you can add or remove modifiers using `+` (add) and `-` (remove) prefixes. For example, `-readonly` removes `readonly` from all properties, and `-?` removes optionality. This enables creating types like `Mutable<T>` (removes readonly) and `Required<T>` (removes optional) from existing types.',
                'options'     => [
                    ['text' => 'Prefixes in mapped types that add (+) or remove (-) readonly/optional modifiers from properties', 'correct' => true],
                    ['text' => 'Arithmetic operators applied to type parameters inside generics', 'correct' => false],
                    ['text' => 'Flags in tsconfig that control how modifiers are compiled', 'correct' => false],
                    ['text' => 'Decorators that add or remove class property attributes', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an advanced template literal type in TypeScript?',
                'explanation' => 'Template literal types can interpolate other types to produce a family of string types. For example: `type EventName<T extends string> = \`on\${Capitalize<T>}\`` generates types like `"onClick"`, `"onChange"` from `"click"`, `"change"`. Combined with mapped types they can transform entire sets of string keys.',
                'options'     => [
                    ['text' => 'Composing string types by interpolating other types inside backtick template expressions at the type level', 'correct' => true],
                    ['text' => 'Using backticks in tsconfig to specify file path patterns', 'correct' => false],
                    ['text' => 'Injecting runtime values into type names', 'correct' => false],
                    ['text' => 'A way to define multiline string literals in type definitions', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you enforce exhaustiveness checking on a discriminated union in TypeScript?',
                'explanation' => 'Assign the remaining union member to `never` in the default branch. If you add a new variant to the union without handling it, TypeScript will error because the unhandled variant is not assignable to `never`. A common pattern: `default: const _exhaustive: never = value; throw new Error("Unhandled case");`.',
                'options'     => [
                    ['text' => 'Assign the unhandled value to never in the default branch — TypeScript errors if a case is missed', 'correct' => true],
                    ['text' => 'Use the exhaustive() utility function from the TypeScript standard library', 'correct' => false],
                    ['text' => 'Add a required default case to every switch statement via tsconfig', 'correct' => false],
                    ['text' => 'TypeScript enforces exhaustiveness automatically without any extra code', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `this` type annotation in TypeScript?',
                'explanation' => 'TypeScript allows you to annotate `this` as a fake first parameter to a function to specify the type of the calling context. For example: `function greet(this: User): string { return this.name; }`. This prevents calling the function in a context where `this` does not have the expected shape. The `this` parameter is erased in the compiled JavaScript.',
                'options'     => [
                    ['text' => 'A fake first parameter that constrains the type of the calling context (this) for a function', 'correct' => true],
                    ['text' => 'A keyword that always refers to the class instance in methods', 'correct' => false],
                    ['text' => 'A type alias for the current module', 'correct' => false],
                    ['text' => 'A decorator that binds a method to the class instance automatically', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is path aliasing in tsconfig and how is it configured?',
                'explanation' => 'Path aliases map short import paths to longer filesystem paths. In tsconfig.json, set `"baseUrl"` and then `"paths": { "@utils/*": ["src/utils/*"] }`. This lets you write `import { helper } from "@utils/helper"` instead of relative paths. A bundler (Vite, webpack) or `tsc-alias` is typically needed to resolve these paths at runtime.',
                'options'     => [
                    ['text' => 'Configured via baseUrl and paths in tsconfig to map short import aliases to real paths', 'correct' => true],
                    ['text' => 'A runtime feature built into Node.js that requires no tsconfig changes', 'correct' => false],
                    ['text' => 'Set up in package.json under the imports field and read by TypeScript automatically', 'correct' => false],
                    ['text' => 'Defined in .env files and injected by the TypeScript compiler', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is declaration merging with interfaces in TypeScript?',
                'explanation' => 'When you declare the same interface name more than once, TypeScript merges all declarations into a single interface. For example, two `interface Window` declarations are merged. This is commonly used to augment global types or extend types from third-party libraries without modifying their source.',
                'options'     => [
                    ['text' => 'Declaring the same interface name multiple times causes TypeScript to merge their members', 'correct' => true],
                    ['text' => 'Combining two interface files into one during a build step', 'correct' => false],
                    ['text' => 'Merging two objects whose types are interfaces at runtime', 'correct' => false],
                    ['text' => 'Creating a union of two interfaces using the | operator', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an ambient declaration (`declare`) in TypeScript?',
                'explanation' => '`declare` tells the TypeScript compiler that a variable, function, class, or module exists at runtime but is defined elsewhere (e.g., loaded via a script tag or provided by the environment). The declared entity has a type but produces no JavaScript output. For example: `declare const __ENV__: string;`.',
                'options'     => [
                    ['text' => 'Tells TypeScript a value exists at runtime but is defined externally — no JS is emitted', 'correct' => true],
                    ['text' => 'Marks a variable as immutable throughout its scope', 'correct' => false],
                    ['text' => 'Declares a variable without assigning a type', 'correct' => false],
                    ['text' => 'A keyword for defining abstract methods in a class', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between TypeScript namespaces and ES modules?',
                'explanation' => 'Namespaces (legacy `namespace`/`module` keyword) are a TypeScript-specific way to organize code within a single file or across files using triple-slash references. ES modules use `import`/`export` syntax and are the modern standard. For new projects, ES modules are preferred; namespaces are mainly used in declaration files for ambient global APIs.',
                'options'     => [
                    ['text' => 'Namespaces are a TypeScript-specific legacy pattern; ES modules use import/export and are the modern standard', 'correct' => true],
                    ['text' => 'Namespaces are faster at runtime because they do not require a module loader', 'correct' => false],
                    ['text' => 'ES modules are TypeScript-only; namespaces work in plain JavaScript too', 'correct' => false],
                    ['text' => 'They are identical — namespace is just the old spelling of module', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `--strictFunctionTypes` enforce in TypeScript?',
                'explanation' => '`strictFunctionTypes` enables contravariant checking for function parameter types. Without it, function parameters are checked bivariantly (both covariantly and contravariantly), which can allow unsound assignments. With it, parameter types must be contravariantly compatible. This catches a class of subtle type-safety bugs with callbacks.',
                'options'     => [
                    ['text' => 'Enables contravariant (stricter) checking of function parameter types', 'correct' => true],
                    ['text' => 'Prevents assigning function types to variables typed as any', 'correct' => false],
                    ['text' => 'Requires all function parameters to have explicit type annotations', 'correct' => false],
                    ['text' => 'Disallows functions with more parameters than declared in their type', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is structural typing in TypeScript?',
                'explanation' => 'TypeScript uses structural typing (also called "duck typing") — type compatibility is determined by the shape (properties and their types) of a value, not by its declared class name or inheritance hierarchy. If object A has all the properties that type B requires, A is assignable to B, regardless of how A was defined.',
                'options'     => [
                    ['text' => 'Type compatibility is based on the shape/structure of a value, not its name or class hierarchy', 'correct' => true],
                    ['text' => 'Types must be explicitly declared to be compatible — nominal typing', 'correct' => false],
                    ['text' => 'Types are compared character by character at the string level', 'correct' => false],
                    ['text' => 'Compatibility is determined by the order properties are declared', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is excess property checking in TypeScript?',
                'explanation' => 'When assigning an object literal directly to a typed variable, TypeScript performs excess property checking and rejects properties not present in the target type. For example: `const p: Point = { x: 1, y: 2, z: 3 };` errors because `z` is not in `Point`. This check only applies to fresh object literals — passing through an intermediate variable bypasses it.',
                'options'     => [
                    ['text' => 'TypeScript rejects extra properties in object literals assigned directly to a typed variable', 'correct' => true],
                    ['text' => 'A check that counts the total number of properties on an object', 'correct' => false],
                    ['text' => 'A runtime validation that throws when unexpected properties are found', 'correct' => false],
                    ['text' => 'A tsconfig flag that strips excess properties from compiled output', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is type widening in TypeScript?',
                'explanation' => 'Type widening occurs when TypeScript infers a broader type from a value that could be narrower. For example, `let x = "hello"` infers `x` as `string` (widened), not `"hello"` (literal). Using `const x = "hello"` keeps the literal type `"hello"` because `const` signals the value will not change.',
                'options'     => [
                    ['text' => 'TypeScript inferring a broader type (e.g., string instead of "hello") when the value could change', 'correct' => true],
                    ['text' => 'Casting a narrow type to a wider type using as', 'correct' => false],
                    ['text' => 'Adding more properties to an interface after its initial declaration', 'correct' => false],
                    ['text' => 'Converting a union type to its widest member', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `symbol` type in TypeScript?',
                'explanation' => '`symbol` is a primitive type in TypeScript that corresponds to JavaScript\'s `Symbol`. Each symbol value is unique and immutable. TypeScript also has `unique symbol` — a subtype of `symbol` that represents a specific symbol value, useful for nominal-like typing. Symbols are commonly used as unique object keys.',
                'options'     => [
                    ['text' => 'A primitive type for unique, immutable symbol values — each Symbol() call produces a distinct value', 'correct' => true],
                    ['text' => 'A way to define mathematical symbols in type names', 'correct' => false],
                    ['text' => 'A special string type used for enum keys', 'correct' => false],
                    ['text' => 'A type alias for the built-in Symbol class in JavaScript', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does bivariance mean for function parameters in TypeScript?',
                'explanation' => 'Bivariance means a function parameter type is accepted both covariantly and contravariantly — a stricter type or a looser type is accepted. Without `strictFunctionTypes`, TypeScript checks method parameters bivariantly for compatibility with classes/interfaces. This can allow unsound assignments but was kept for practical reasons with certain patterns like event handlers.',
                'options'     => [
                    ['text' => 'Both covariant and contravariant assignments are allowed for the parameter type', 'correct' => true],
                    ['text' => 'Both the parameter type and return type must match exactly', 'correct' => false],
                    ['text' => 'The parameter can be assigned from both null and undefined', 'correct' => false],
                    ['text' => 'A parameter that accepts two values simultaneously', 'correct' => false],
                ],
            ],
        ];
    }

    private function advancedQuestions(): array
    {
        return [
            // ── Original 10 ──────────────────────────────────────────────────
            [
                'question'    => 'What are conditional types in TypeScript?',
                'explanation' => 'Conditional types follow the pattern `T extends U ? X : Y` — if type T is assignable to U, the result is X, otherwise Y. This enables powerful type-level logic. For example: `type IsString<T> = T extends string ? true : false;`. They are the foundation for many advanced utility types.',
                'options'     => [
                    ['text' => 'Type-level if/else expressions: T extends U ? X : Y', 'correct' => true],
                    ['text' => 'Runtime type guards using typeof and instanceof', 'correct' => false],
                    ['text' => 'Conditional import statements based on environment', 'correct' => false],
                    ['text' => 'Optional type annotations that may or may not apply', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a mapped type in TypeScript?',
                'explanation' => 'A mapped type creates a new type by iterating over the keys of an existing type: `{ [K in keyof T]: NewType }`. This allows transforming all properties systematically. Built-in utility types like `Partial<T>`, `Readonly<T>`, and `Required<T>` are all implemented as mapped types.',
                'options'     => [
                    ['text' => 'A type created by iterating over the keys of another type: { [K in keyof T]: ... }', 'correct' => true],
                    ['text' => 'A type that maps array values to object keys', 'correct' => false],
                    ['text' => 'A type for transforming values with Array.map() at compile time', 'correct' => false],
                    ['text' => 'An alternative to Record<K, V> for dynamic keys', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `infer` keyword do in TypeScript conditional types?',
                'explanation' => '`infer` allows capturing a type variable within the extends clause of a conditional type. Example: `type ReturnType<T> = T extends (...args: any[]) => infer R ? R : never;` — here `infer R` captures the actual return type of function T. Without infer, you could not extract inner types from complex type structures.',
                'options'     => [
                    ['text' => 'Extracts and captures a type variable from within a conditional type pattern', 'correct' => true],
                    ['text' => 'Infers a type from a runtime value instead of a static annotation', 'correct' => false],
                    ['text' => 'Forces TypeScript to re-check a type annotation it previously resolved', 'correct' => false],
                    ['text' => 'An alias for the extends keyword in generic constraints', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a discriminated union in TypeScript?',
                'explanation' => 'A discriminated union is a pattern where each member of a union type has a common literal type field (the discriminant) that uniquely identifies its type. Example: `type Shape = { kind: "circle"; radius: number } | { kind: "square"; side: number }`. Checking `shape.kind` allows TypeScript to narrow to the correct variant.',
                'options'     => [
                    ['text' => 'A union where each member has a literal discriminant field for type narrowing', 'correct' => true],
                    ['text' => 'A union type that filters out undefined and null', 'correct' => false],
                    ['text' => 'A union of types that are mutually exclusive at runtime only', 'correct' => false],
                    ['text' => 'A way to prevent overlapping properties between two types', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is declaration merging in TypeScript?',
                'explanation' => 'Declaration merging allows TypeScript to automatically combine multiple declarations of the same name. This applies mainly to interfaces — if you declare the same interface twice, TypeScript merges their members into a single interface. It is used to extend third-party type definitions or augment global types.',
                'options'     => [
                    ['text' => 'TypeScript automatically combines multiple declarations of the same interface name', 'correct' => true],
                    ['text' => 'Combining the output of two TypeScript compilation steps', 'correct' => false],
                    ['text' => 'Merging two JavaScript objects at runtime', 'correct' => false],
                    ['text' => 'Combining two .ts files into one during compilation', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `keyof` do in TypeScript?',
                'explanation' => '`keyof T` produces a union type of all property key names of type T. For example, if `interface User { id: number; name: string; }`, then `keyof User` is `"id" | "name"`. It is commonly used with generics to constrain a key argument to known properties of an object.',
                'options'     => [
                    ['text' => 'Produces a union type of all property key names of a given type', 'correct' => true],
                    ['text' => 'Returns an array of all keys in an object at runtime', 'correct' => false],
                    ['text' => 'Checks whether a key exists in an object type', 'correct' => false],
                    ['text' => 'Converts all key names of a type to string literals', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `as const` do in TypeScript?',
                'explanation' => '`as const` freezes a value\'s type to its most specific (narrowest) literal form. Without it: `const obj = { dir: "left" }` — TypeScript infers `dir: string`. With `as const`: TypeScript infers `dir: "left"` (a string literal type). It is useful for defining constant configuration objects, lookup tables, or tuples with known literal types.',
                'options'     => [
                    ['text' => 'Makes all inferred types as narrow/literal as possible (deepens type precision)', 'correct' => true],
                    ['text' => 'Prevents all object mutations at runtime using Object.freeze()', 'correct' => false],
                    ['text' => 'An alias for readonly that works on variables', 'correct' => false],
                    ['text' => 'Forces TypeScript to treat the value as a constant expression at build time', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are template literal types in TypeScript?',
                'explanation' => 'Template literal types construct new string types by combining literal types with template literal syntax: `` `on${Capitalize<Event>}` ``. They allow expressing string pattern constraints at the type level. For example: `type EventName = `on${string}`` creates a type matching any string starting with "on".',
                'options'     => [
                    ['text' => 'Type-level template strings that compose new string literal types', 'correct' => true],
                    ['text' => 'A runtime string interpolation feature specific to TypeScript', 'correct' => false],
                    ['text' => 'Template expressions evaluated by the TypeScript compiler to produce values', 'correct' => false],
                    ['text' => 'A way to define string enums using backtick syntax', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `Extract<T, U>` utility type?',
                'explanation' => '`Extract<T, U>` extracts from T only the members that are assignable to U. Example: `Extract<"a" | "b" | "c", "a" | "c">` results in `"a" | "c"`. It is the opposite of `Exclude<T, U>`, which removes matching members. Both are commonly used to narrow union types.',
                'options'     => [
                    ['text' => 'Extracts from T only the members that are assignable to U', 'correct' => true],
                    ['text' => 'Removes members of T that are assignable to U', 'correct' => false],
                    ['text' => 'Picks specific properties from an object type T', 'correct' => false],
                    ['text' => 'Extracts the return type from a function type T', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is module augmentation in TypeScript?',
                'explanation' => 'Module augmentation allows you to extend the types of an existing module from a separate file. You re-declare the module using `declare module "module-name"` and add new members. This is commonly used to add types to third-party libraries or extend Express Request/Response types for custom middleware.',
                'options'     => [
                    ['text' => 'Extending existing module types from a separate file using declare module', 'correct' => true],
                    ['text' => 'Adding new exports to a compiled JavaScript module at runtime', 'correct' => false],
                    ['text' => 'Merging two TypeScript project configurations into one', 'correct' => false],
                    ['text' => 'Dynamically importing modules based on type conditions', 'correct' => false],
                ],
            ],
            // ── New additions: 23 more ────────────────────────────────────────
            [
                'question'    => 'How can you simulate Higher-Kinded Types (HKT) in TypeScript?',
                'explanation' => 'TypeScript does not natively support higher-kinded types (type constructors as type parameters). A common workaround is the "URI map" pattern: define an interface that maps string keys to concrete types, then use a string key as the "kind" to look up the type via indexing. Libraries like `fp-ts` use this approach to express functors and monads.',
                'options'     => [
                    ['text' => 'Using a URI-map interface and string key lookup to simulate type constructor abstraction', 'correct' => true],
                    ['text' => 'Passing class constructors as generic parameters directly', 'correct' => false],
                    ['text' => 'HKTs are fully supported via the hkt keyword introduced in TypeScript 5', 'correct' => false],
                    ['text' => 'Using conditional types with infer to simulate type-level functions', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are recursive types in TypeScript?',
                'explanation' => 'A recursive type refers to itself in its own definition. For example: `type JSONValue = string | number | boolean | null | JSONValue[] | { [key: string]: JSONValue }`. This models infinitely nested structures like JSON or tree nodes. TypeScript supports recursive type aliases but has depth limits to prevent infinite type expansion.',
                'options'     => [
                    ['text' => 'Types that reference themselves to model infinitely nested structures like JSON or trees', 'correct' => true],
                    ['text' => 'Types generated by recursive function calls at compile time', 'correct' => false],
                    ['text' => 'Generic types that call themselves with a narrower type parameter each time', 'correct' => false],
                    ['text' => 'Types that extend themselves using class inheritance', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are variadic tuple types in TypeScript?',
                'explanation' => 'Variadic tuple types (introduced in TypeScript 4.0) allow spread elements in tuple type positions: `type Concat<T extends unknown[], U extends unknown[]> = [...T, ...U]`. This enables precise type-level concatenation and manipulation of tuple types, making typed wrappers around rest/spread operations possible.',
                'options'     => [
                    ['text' => 'Tuple types with spread elements (...T) that enable type-level tuple concatenation and manipulation', 'correct' => true],
                    ['text' => 'Arrays with a variable number of elements that can grow at runtime', 'correct' => false],
                    ['text' => 'Generic tuples where every element has the same type', 'correct' => false],
                    ['text' => 'Tuples that accept a rest parameter as their last element', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an advanced use of `infer` in conditional types?',
                'explanation' => '`infer` can be used in many positions inside the extends clause: to extract element types from arrays (`T extends (infer E)[] ? E : never`), to unwrap Promise chains (`T extends Promise<infer U> ? U : T`), or to extract function parameter tuples. Multiple `infer` variables can appear in a single conditional type.',
                'options'     => [
                    ['text' => 'Placing infer in different positions (array element, promise value, parameter tuple) to extract nested types', 'correct' => true],
                    ['text' => 'Using infer to compute values at compile time like a macro', 'correct' => false],
                    ['text' => 'Inferring types from JSON schema definitions at build time', 'correct' => false],
                    ['text' => 'Automatically generating type declarations from runtime values using infer', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are distributive conditional types in TypeScript?',
                'explanation' => 'When the checked type in a conditional type is a bare type parameter (not wrapped), the conditional type distributes over union members. For example: `type ToArray<T> = T extends any ? T[] : never` — applying `ToArray<string | number>` gives `string[] | number[]`, not `(string | number)[]`. Wrap T in a tuple `[T] extends [U]` to disable distribution.',
                'options'     => [
                    ['text' => 'Conditional types that automatically distribute over each member of a union when the parameter is bare', 'correct' => true],
                    ['text' => 'Conditional types that run in parallel for performance', 'correct' => false],
                    ['text' => 'Types that spread their members into an intersection', 'correct' => false],
                    ['text' => 'Types distributed across multiple files via module splitting', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What string manipulation utility types does TypeScript provide?',
                'explanation' => 'TypeScript provides four intrinsic string manipulation types: `Uppercase<S>`, `Lowercase<S>`, `Capitalize<S>`, and `Uncapitalize<S>`. These operate at the type level on string literal types. For example: `Uppercase<"hello">` produces `"HELLO"`. They are commonly combined with template literal types to transform key names.',
                'options'     => [
                    ['text' => 'Uppercase<S>, Lowercase<S>, Capitalize<S>, and Uncapitalize<S> operate on string literal types', 'correct' => true],
                    ['text' => 'StringToUpper<S>, StringToLower<S> — available from the string utility library', 'correct' => false],
                    ['text' => 'toUpper<S> and toLower<S> — equivalent to calling .toUpperCase() at the type level', 'correct' => false],
                    ['text' => 'TypeScript does not have built-in string manipulation types', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are experimental decorators in TypeScript?',
                'explanation' => 'Decorators are a stage-2 (legacy) proposal enabled via `"experimentalDecorators": true` in tsconfig. They are functions prefixed with `@` that can modify classes, methods, properties, or parameters. For example: `@Injectable()` on a class. TypeScript 5.0 also added support for the newer TC39 decorator standard without the experimental flag.',
                'options'     => [
                    ['text' => 'Functions prefixed with @ that modify classes, methods, or properties — enabled via experimentalDecorators in tsconfig', 'correct' => true],
                    ['text' => 'Comments that are processed by the TypeScript compiler to generate documentation', 'correct' => false],
                    ['text' => 'Macros that run at build time to transform source code', 'correct' => false],
                    ['text' => 'Attributes imported from the reflect-metadata library', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `reflect-metadata` and how does it relate to TypeScript decorators?',
                'explanation' => '`reflect-metadata` is a polyfill for the Metadata Reflection API. When used with TypeScript decorators and `"emitDecoratorMetadata": true` in tsconfig, TypeScript emits type metadata (parameter types, return type, property type) that can be read at runtime via `Reflect.getMetadata`. This enables dependency injection frameworks like NestJS and Angular to resolve constructor parameter types automatically.',
                'options'     => [
                    ['text' => 'A polyfill that enables runtime type metadata emission for use by DI frameworks with TypeScript decorators', 'correct' => true],
                    ['text' => 'A built-in TypeScript module for reading type information at runtime', 'correct' => false],
                    ['text' => 'A compiler plugin that generates JSON schema from type annotations', 'correct' => false],
                    ['text' => 'A library that replaces decorators with plain function calls', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the phantom type pattern in TypeScript?',
                'explanation' => 'A phantom type is a generic type parameter that does not appear in the runtime value but carries type-level information for the compiler. For example: `type USD<T> = { amount: number; _brand: T }` — `T` is never used in the value but prevents mixing currencies. Phantom types add type safety without any runtime overhead.',
                'options'     => [
                    ['text' => 'A type parameter present only at the type level (not in runtime value) to enforce type safety', 'correct' => true],
                    ['text' => 'A deprecated type that has been removed from newer TypeScript versions', 'correct' => false],
                    ['text' => 'A type that becomes undefined at runtime while remaining typed at compile time', 'correct' => false],
                    ['text' => 'An invisible type applied automatically by the compiler to all generics', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a branded/nominal type in TypeScript?',
                'explanation' => 'TypeScript uses structural typing, so `type UserId = string` and `type PostId = string` are interchangeable — a dangerous confusion. Branded types add a unique phantom property to prevent this: `type UserId = string & { _brand: "UserId" }`. Values can only be created via a cast function, enforcing distinct identity at the type level without runtime overhead.',
                'options'     => [
                    ['text' => 'A type with a phantom _brand property that prevents mixing structurally identical but semantically different types', 'correct' => true],
                    ['text' => 'A type that carries a registered trademark symbol for legal compliance', 'correct' => false],
                    ['text' => 'Types imported from branded UI component libraries', 'correct' => false],
                    ['text' => 'A type alias that is automatically documented with its brand name', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `IsNever` utility type and how is it implemented?',
                'explanation' => '`IsNever<T>` checks whether T resolves to the `never` type. A naive implementation `T extends never ? true : false` always returns `never` due to distributivity. The correct implementation wraps T in a tuple to disable distribution: `type IsNever<T> = [T] extends [never] ? true : false`.',
                'options'     => [
                    ['text' => 'A conditional type using [T] extends [never] (with tuple wrap) to avoid distributivity giving a wrong result', 'correct' => true],
                    ['text' => 'A built-in TypeScript utility type available in the standard library', 'correct' => false],
                    ['text' => 'A runtime check that returns true if a value is undefined', 'correct' => false],
                    ['text' => 'T extends never ? true : false — naive bare form works correctly', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How is `DeepPartial<T>` implemented as a custom utility type?',
                'explanation' => '`DeepPartial<T>` makes all properties optional recursively. The standard `Partial<T>` only works one level deep. A custom implementation: `type DeepPartial<T> = { [K in keyof T]?: T[K] extends object ? DeepPartial<T[K]> : T[K] }`. It recursively applies `DeepPartial` to any nested object types while leaving primitive types unchanged.',
                'options'     => [
                    ['text' => 'A recursive mapped type that makes properties optional at every depth level', 'correct' => true],
                    ['text' => 'A utility provided in the TypeScript standard library since version 4', 'correct' => false],
                    ['text' => 'Applying Partial<T> multiple times in a chain to go deeper', 'correct' => false],
                    ['text' => 'A type that sets all values to undefined regardless of nesting depth', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `UnionToIntersection<U>` utility type?',
                'explanation' => '`UnionToIntersection<U>` converts a union type to an intersection type. It exploits contravariance in function parameters: `type UnionToIntersection<U> = (U extends any ? (k: U) => void : never) extends (k: infer I) => void ? I : never`. When `infer I` captures a type that is contravariantly required to satisfy all union members, the result is their intersection.',
                'options'     => [
                    ['text' => 'Converts a union to an intersection by exploiting contravariance with infer in function parameters', 'correct' => true],
                    ['text' => 'Extracts the common properties shared by all union members', 'correct' => false],
                    ['text' => 'A built-in TypeScript utility type for combining union branches', 'correct' => false],
                    ['text' => 'Flattens a union of arrays into a single array type', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `IsAny` utility type and how is it typically implemented?',
                'explanation' => 'Detecting the `any` type is non-trivial because `any` is assignable to everything. A common implementation exploits this: `type IsAny<T> = 0 extends (1 & T) ? true : false`. The expression `1 & T` collapses to `1` for most types, but when T is `any`, the intersection also becomes `any`, making `0 extends any` evaluate to `true`.',
                'options'     => [
                    ['text' => 'Uses 0 extends (1 & T) — the intersection with any collapses to any, making the check pass', 'correct' => true],
                    ['text' => 'T extends any ? true : false — this works correctly because any is unique', 'correct' => false],
                    ['text' => 'IsAny<T> is a built-in TypeScript utility type', 'correct' => false],
                    ['text' => 'Checks if typeof T === "any" at the type level', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are TypeScript project references?',
                'explanation' => 'Project references (configured via `references` in tsconfig.json) allow splitting a large TypeScript codebase into smaller sub-projects that can be built incrementally. Each referenced project must have `"composite": true`. Running `tsc --build` compiles only changed projects. This significantly improves build times in monorepos.',
                'options'     => [
                    ['text' => 'A tsconfig feature that links sub-projects for incremental, isolated compilation in monorepos', 'correct' => true],
                    ['text' => 'A way to reference types from a remote TypeScript project via URL', 'correct' => false],
                    ['text' => 'Import aliases that reference other projects by name instead of path', 'correct' => false],
                    ['text' => 'TypeScript decorators that reference external project configurations', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is type-level programming in TypeScript?',
                'explanation' => 'Type-level programming uses TypeScript\'s type system as a computation engine — manipulating types rather than values. Using mapped types, conditional types, infer, recursive types, and template literal types, you can compute types from other types at compile time. Libraries like `ts-toolbelt` expose hundreds of such type-level utilities.',
                'options'     => [
                    ['text' => 'Using the type system (mapped types, conditional types, infer) to compute and transform types at compile time', 'correct' => true],
                    ['text' => 'Writing TypeScript programs that generate other TypeScript programs', 'correct' => false],
                    ['text' => 'Running TypeScript code directly in a REPL without compilation', 'correct' => false],
                    ['text' => 'Using the TypeScript compiler API to manipulate AST nodes programmatically', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a type-safe builder pattern in TypeScript?',
                'explanation' => 'The type-safe builder pattern uses generics to track which required fields have been set, preventing `build()` from being called until all required fields are present. Each setter method returns a new builder type with the field marked as set. This makes incomplete builders a compile-time error rather than a runtime one.',
                'options'     => [
                    ['text' => 'A pattern using generics to track set fields at the type level, preventing build() until all required fields are provided', 'correct' => true],
                    ['text' => 'A design pattern that validates builder inputs at runtime before construction', 'correct' => false],
                    ['text' => 'A class that uses protected setters to enforce immutable construction', 'correct' => false],
                    ['text' => 'Using the Builder interface from TypeScript standard library for object construction', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a type-safe REST API response type pattern in TypeScript?',
                'explanation' => 'A type-safe API response pattern uses generics and discriminated unions to express both success and error states. For example: `type ApiResponse<T> = { ok: true; data: T } | { ok: false; error: string }`. Callers must check `response.ok` before accessing `data`, and TypeScript narrows the type accordingly, preventing accidental access of error fields in the success branch.',
                'options'     => [
                    ['text' => 'A discriminated union with an ok field that forces callers to check success before accessing data', 'correct' => true],
                    ['text' => 'Generating TypeScript types automatically from an OpenAPI JSON schema at build time', 'correct' => false],
                    ['text' => 'Using the fetch API\'s built-in TypeScript generics for response typing', 'correct' => false],
                    ['text' => 'Typing REST responses as any to avoid versioning problems', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do `UnionToTuple` type utilities work in TypeScript?',
                'explanation' => '`UnionToTuple<U>` converts a union type to a tuple type. The implementation relies on `UnionToIntersection` to build a function intersection, then uses `infer` to pop the last union member iteratively. This is an advanced type trick because union member order is not guaranteed, making the result non-deterministic for same-priority members.',
                'options'     => [
                    ['text' => 'Combines UnionToIntersection and infer to pop union members into a tuple — but member order is not guaranteed', 'correct' => true],
                    ['text' => 'A built-in TypeScript utility since version 5.1', 'correct' => false],
                    ['text' => 'Spreads union members into a fixed-length array type deterministically', 'correct' => false],
                    ['text' => 'Converts each union branch to a single-element tuple then flattens them', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the opaque type pattern in TypeScript?',
                'explanation' => 'Opaque types (also called nominal types) hide the underlying type representation so the type can only be created and consumed through specific functions. The common approach combines a base type with a unique brand: `type Opaque<BaseType, BrandType> = BaseType & { _opaque: BrandType }`. This is stricter than a simple type alias and prevents accidental misuse.',
                'options'     => [
                    ['text' => 'A branded type pattern that hides the underlying type and restricts creation to designated factory functions', 'correct' => true],
                    ['text' => 'A type that is intentionally left as unknown to hide implementation details', 'correct' => false],
                    ['text' => 'A TypeScript module that prevents its exports from being re-exported', 'correct' => false],
                    ['text' => 'A class whose constructor is private, exposing only a static factory method', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is tail-call optimization in the context of recursive TypeScript types?',
                'explanation' => 'TypeScript has a recursion depth limit for type evaluation. Types that recurse in a "tail position" (where the recursive call is the outermost operation) are optimized starting from TypeScript 4.5, allowing much deeper recursion. Non-tail-recursive types hit the depth limit sooner and produce "Type instantiation is excessively deep" errors.',
                'options'     => [
                    ['text' => 'TypeScript 4.5+ optimizes tail-position recursive types to allow deeper recursion without depth limit errors', 'correct' => true],
                    ['text' => 'A runtime optimization applied by V8 to recursive TypeScript functions', 'correct' => false],
                    ['text' => 'Rewriting recursive types as iterative mapped types for better performance', 'correct' => false],
                    ['text' => 'TypeScript automatically memoizes recursive type evaluations to avoid recomputation', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a deep path-based type (like `DeepPick`) in TypeScript?',
                'explanation' => 'A `DeepPick` type allows picking nested properties using dot-notation string paths, e.g., `DeepPick<User, "address.city">`. Implementing it requires parsing the dot-separated string path using template literal types and recursive conditional types to traverse the nested object type structure. Libraries like `ts-toolbelt` provide this.',
                'options'     => [
                    ['text' => 'A type that uses template literal parsing and recursive types to pick nested properties via dot-notation paths', 'correct' => true],
                    ['text' => 'A utility that picks properties from objects using XPath-like syntax at runtime', 'correct' => false],
                    ['text' => 'A built-in TypeScript utility type for accessing deep object properties', 'correct' => false],
                    ['text' => 'A mapped type that flattens nested objects into a single-level type', 'correct' => false],
                ],
            ],
        ];
    }
}
