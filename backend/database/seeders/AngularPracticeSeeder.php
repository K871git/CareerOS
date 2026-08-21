<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;

class AngularPracticeSeeder extends Seeder
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
            ['slug' => 'angular'],
            [
                'learning_track_id' => $track->id,
                'title'             => 'Angular',
                'description'       => 'Angular is a TypeScript-based web framework by Google. Master components, services, routing, reactive forms, and the Angular ecosystem.',
                'display_order'     => 6,
            ]
        );

        $levels = [
            [
                'title'         => 'Angular Basics — Junior',
                'slug'          => 'angular-junior',
                'description'   => 'Components, directives, data binding, and the Angular fundamentals. Perfect for junior-level interview preparation.',
                'display_order' => 1,
                'difficulty'    => 'Easy',
                'questions'     => $this->juniorQuestions(),
            ],
            [
                'title'         => 'Angular Intermediate',
                'slug'          => 'angular-intermediate',
                'description'   => 'Services, dependency injection, routing, forms, and HTTP. For developers targeting mid-level roles.',
                'display_order' => 2,
                'difficulty'    => 'Medium',
                'questions'     => $this->intermediateQuestions(),
            ],
            [
                'title'         => 'Angular Advanced',
                'slug'          => 'angular-advanced',
                'description'   => 'Change detection, RxJS, signals, interceptors, and standalone components. Essential for senior developer interviews.',
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

        $this->command->info('Angular Practice seeded: 1 subject, 3 topics, ~100 questions.');
    }

    private function juniorQuestions(): array
    {
        return [
            // --- Original 10 questions ---
            [
                'question'    => 'What is Angular?',
                'explanation' => 'Angular is a TypeScript-based open-source web application framework developed and maintained by Google. It is a full-featured platform for building single-page applications (SPAs), providing built-in tools for routing, forms, HTTP communication, dependency injection, and more — unlike React which focuses only on the view layer.',
                'options'     => [
                    ['text' => 'A TypeScript-based web framework by Google for building single-page applications', 'correct' => true],
                    ['text' => 'A JavaScript library for rendering UI components (similar to React)', 'correct' => false],
                    ['text' => 'A server-side rendering framework for Node.js', 'correct' => false],
                    ['text' => 'A CSS framework for building responsive layouts', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an Angular component?',
                'explanation' => 'A component is the fundamental building block of an Angular application. It consists of a TypeScript class decorated with `@Component`, an HTML template, and optional styles. The decorator specifies the `selector` (custom HTML tag), `templateUrl`, and `styleUrls`. Components control a view — a patch of the screen.',
                'options'     => [
                    ['text' => 'A class decorated with @Component that controls a template and its view logic', 'correct' => true],
                    ['text' => 'A standalone JavaScript function that returns HTML', 'correct' => false],
                    ['text' => 'A CSS class that defines the visual appearance of an element', 'correct' => false],
                    ['text' => 'An Angular module that groups related services together', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `*ngIf` directive do in Angular?',
                'explanation' => '`*ngIf` is a structural directive that conditionally adds or removes an element from the DOM based on an expression. When the expression is truthy the element is in the DOM; when falsy it is removed entirely (not just hidden). It can be combined with `else`: `*ngIf="isLoaded; else loadingTemplate"`.',
                'options'     => [
                    ['text' => 'Conditionally adds or removes an element from the DOM based on an expression', 'correct' => true],
                    ['text' => 'Hides an element visually but keeps it in the DOM', 'correct' => false],
                    ['text' => 'Loops through an array and renders elements for each item', 'correct' => false],
                    ['text' => 'Binds an Angular variable to an if statement in TypeScript', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `*ngFor` directive do in Angular?',
                'explanation' => '`*ngFor` is a structural directive that iterates over a collection and renders a template for each item. Syntax: `*ngFor="let item of items"`. The exported variables `index`, `first`, `last`, `even`, `odd` are available. A `trackBy` function can be provided to optimize DOM updates by tracking items by identity.',
                'options'     => [
                    ['text' => 'Iterates over a collection and renders the host element for each item', 'correct' => true],
                    ['text' => 'Filters a list and renders only items matching a condition', 'correct' => false],
                    ['text' => 'Repeats a component a fixed number of times', 'correct' => false],
                    ['text' => 'Loops through the DOM tree and applies styles to each element', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is two-way data binding in Angular and how is it used?',
                'explanation' => 'Two-way data binding synchronizes data between the component property and the template input field in both directions. Syntax: `[(ngModel)]="propertyName"` (the "banana in a box" syntax). Changes in the view update the component property, and changes in the component update the view. Requires `FormsModule` to be imported.',
                'options'     => [
                    ['text' => 'Syncs component property and template input in both directions using [(ngModel)]', 'correct' => true],
                    ['text' => 'Binds two components together so their data is always equal', 'correct' => false],
                    ['text' => 'Connects a parent and child component\'s properties automatically', 'correct' => false],
                    ['text' => 'A one-directional binding that updates the view whenever the model changes', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `@Input()` decorator in Angular?',
                'explanation' => '`@Input()` marks a component property as an input — it allows a parent component to pass data into a child component via property binding. Syntax in template: `<child [childProp]="parentValue">`. The child declares: `@Input() childProp: string`. This enables component composition and parent-to-child communication.',
                'options'     => [
                    ['text' => 'Marks a property so a parent component can pass data into the child via property binding', 'correct' => true],
                    ['text' => 'Imports an external module into the current component', 'correct' => false],
                    ['text' => 'Subscribes the component to user input events like keypress', 'correct' => false],
                    ['text' => 'Injects a service into the component class', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `@Output()` and `EventEmitter` used for in Angular?',
                'explanation' => '`@Output()` marks a property as an output that emits events from the child to the parent. The type is `EventEmitter<T>`. The child calls `this.myEvent.emit(value)` to fire the event. The parent listens with event binding: `<child (myEvent)="onEvent($event)">`. This enables child-to-parent communication.',
                'options'     => [
                    ['text' => 'Allows a child component to emit events that the parent listens to', 'correct' => true],
                    ['text' => 'Outputs debugging information from the component to the console', 'correct' => false],
                    ['text' => 'Subscribes to HTTP events from an API response', 'correct' => false],
                    ['text' => 'Sends component state updates to an Angular store', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is interpolation in Angular templates?',
                'explanation' => 'Interpolation uses double curly braces `{{ expression }}` to embed component properties or expressions in the template as text. Angular evaluates the expression and inserts the string result into the HTML. Example: `<h1>Hello, {{ user.name }}</h1>`. Expressions can include simple operations but should not have side effects.',
                'options'     => [
                    ['text' => 'Double curly brace syntax {{ }} that outputs a component expression value as text', 'correct' => true],
                    ['text' => 'A method of inserting raw HTML into the template from the component', 'correct' => false],
                    ['text' => 'A way to interpolate CSS values from TypeScript into stylesheets', 'correct' => false],
                    ['text' => 'An Angular pipe that transforms data in the template', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an Angular module (`@NgModule`)?',
                'explanation' => 'An Angular module is a class decorated with `@NgModule` that groups related components, directives, pipes, and services. Every Angular app has at least one module — `AppModule`. The decorator takes `declarations` (components/directives/pipes), `imports` (other modules), `providers` (services), and `bootstrap` (root component). Modules define compilation boundaries.',
                'options'     => [
                    ['text' => 'A class decorated with @NgModule that groups related components, directives, pipes, and services', 'correct' => true],
                    ['text' => 'A Node.js module that contains Angular framework source code', 'correct' => false],
                    ['text' => 'A TypeScript file that exports component functions', 'correct' => false],
                    ['text' => 'An Angular route configuration object', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you generate a new component using Angular CLI?',
                'explanation' => '`ng generate component component-name` (or `ng g c component-name`) creates a new component with four files: `.ts` (class), `.html` (template), `.css` (styles), and `.spec.ts` (tests). It also automatically declares the component in the nearest module. The CLI is the standard way to scaffold Angular artifacts.',
                'options'     => [
                    ['text' => 'ng generate component component-name (or ng g c component-name)', 'correct' => true],
                    ['text' => 'ng create component component-name', 'correct' => false],
                    ['text' => 'ng new component component-name', 'correct' => false],
                    ['text' => 'angular component --name component-name', 'correct' => false],
                ],
            ],
            // --- 23 new Junior questions ---
            [
                'question'    => 'What is property binding in Angular and how does it differ from interpolation?',
                'explanation' => 'Property binding uses square brackets to bind a component property to a DOM property: `[src]="imageUrl"`. Unlike interpolation (`{{ }}`), property binding can set non-string values (booleans, objects, arrays) and binds to DOM properties rather than HTML attributes. Example: `[disabled]="isLoading"` correctly sets the DOM `disabled` property to a boolean.',
                'options'     => [
                    ['text' => 'Square-bracket syntax [property]="expression" that binds component data to DOM properties', 'correct' => true],
                    ['text' => 'A way to bind two component properties together so they share the same value', 'correct' => false],
                    ['text' => 'Identical to interpolation but uses [] instead of {{ }}', 'correct' => false],
                    ['text' => 'A binding that updates the component property when the DOM property changes', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is event binding in Angular?',
                'explanation' => 'Event binding uses parentheses to listen to DOM events and call component methods: `(click)="onButtonClick()"`. The `$event` object is available to pass the native event: `(input)="onInput($event)"`. Event binding enables responding to user interactions such as clicks, key presses, mouse movements, and form submissions.',
                'options'     => [
                    ['text' => 'Parenthesis syntax (event)="method()" that calls a component method on a DOM event', 'correct' => true],
                    ['text' => 'A way to bind an Angular service to a native browser event', 'correct' => false],
                    ['text' => 'A decorator that subscribes the component to keyboard events', 'correct' => false],
                    ['text' => 'The @Output() decorator combined with EventEmitter', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is attribute binding in Angular and when is it needed?',
                'explanation' => 'Attribute binding uses `[attr.attributeName]="expression"` to set HTML attributes that have no corresponding DOM property — for example, ARIA attributes: `[attr.aria-label]="labelText"` or `[attr.colspan]="colSpan"`. Unlike property binding, attribute binding targets the HTML attribute directly. Use it when the attribute does not exist as a DOM property.',
                'options'     => [
                    ['text' => '[attr.x]="val" syntax that sets HTML attributes without a matching DOM property', 'correct' => true],
                    ['text' => 'A binding that attaches custom CSS attributes to an element', 'correct' => false],
                    ['text' => 'The same as property binding but written with the attr prefix for clarity', 'correct' => false],
                    ['text' => 'A directive that reads HTML attributes and exposes them as component inputs', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How does class binding work in Angular?',
                'explanation' => 'Class binding conditionally adds or removes a CSS class based on a boolean expression: `[class.active]="isActive"`. When `isActive` is truthy, the class `active` is added; when falsy, it is removed. For multiple classes, use `[class]="classString"` or the `ngClass` directive. Class binding is the preferred approach for toggling a single class.',
                'options'     => [
                    ['text' => '[class.active]="bool" adds or removes the CSS class based on a boolean expression', 'correct' => true],
                    ['text' => 'Binds a TypeScript class to an HTML element to attach business logic', 'correct' => false],
                    ['text' => 'A way to import an external CSS class file into the component', 'correct' => false],
                    ['text' => 'An alternative to [ngClass] that only works with a single class name', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How does style binding work in Angular?',
                'explanation' => 'Style binding dynamically sets an inline CSS style on an element: `[style.color]="textColor"`. You can include units: `[style.font-size.px]="fontSize"`. For multiple styles, use `[style]="styleObject"` where the value is an object like `{ color: "red", fontSize: "16px" }`. The `ngStyle` directive is an alternative for applying multiple styles.',
                'options'     => [
                    ['text' => '[style.color]="val" sets an inline CSS style dynamically from a component expression', 'correct' => true],
                    ['text' => 'A way to link an external stylesheet to a component at runtime', 'correct' => false],
                    ['text' => 'A pipe that transforms component values into valid CSS strings', 'correct' => false],
                    ['text' => 'Binding the component\'s styleUrls property to a dynamic file path', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `ngClass` directive in Angular?',
                'explanation' => '`ngClass` adds or removes multiple CSS classes based on an expression. It accepts three forms: a string (`ngClass="\'foo bar\'"`), an array (`[ngClass]="[\'foo\', \'bar\']"`), or an object (`[ngClass]="{ active: isActive, disabled: isDisabled }"`). The object form is most common — keys are class names and values are boolean conditions.',
                'options'     => [
                    ['text' => 'A directive that adds/removes multiple CSS classes using a string, array, or object expression', 'correct' => true],
                    ['text' => 'An Angular class that defines the base styles for all components', 'correct' => false],
                    ['text' => 'A structural directive that renders elements only when a CSS class matches', 'correct' => false],
                    ['text' => 'A pipe that transforms component data into CSS class names', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `ngStyle` directive in Angular?',
                'explanation' => '`ngStyle` applies multiple inline CSS styles to an element at once: `[ngStyle]="{ color: textColor, fontSize: fontSize + \'px\' }"`. The value is an object where keys are CSS property names (camelCase or kebab-case) and values are the style values. It is useful when you need to set several styles dynamically based on component state.',
                'options'     => [
                    ['text' => 'A directive that applies multiple inline CSS styles using an object expression', 'correct' => true],
                    ['text' => 'A directive that loads an external stylesheet from a URL at runtime', 'correct' => false],
                    ['text' => 'An Angular service for managing global component styles', 'correct' => false],
                    ['text' => 'A structural directive that conditionally renders a styled element', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `ng-container` in Angular and why is it useful?',
                'explanation' => '`ng-container` is a grouping element that does not render any actual DOM element. It is used to apply structural directives (`*ngIf`, `*ngFor`) without adding extra wrapper elements to the DOM. Example: `<ng-container *ngIf="loaded">...</ng-container>` adds the condition without introducing a `<div>` or `<span>`. This keeps the DOM clean and avoids CSS side effects from extra wrappers.',
                'options'     => [
                    ['text' => 'A grouping element that applies directives without adding a real DOM element', 'correct' => true],
                    ['text' => 'A placeholder where Angular inserts lazily loaded child components', 'correct' => false],
                    ['text' => 'A component that wraps other components and provides shared context', 'correct' => false],
                    ['text' => 'An Angular container for dependency injection providers', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `ng-template` in Angular?',
                'explanation' => '`ng-template` defines a reusable block of HTML that Angular will not render by default. It is used with structural directives like `*ngIf; else`, `*ngFor`, and with `ngTemplateOutlet` to render it explicitly. The template is referenced via a template reference variable: `<ng-template #loading><spinner/></ng-template>`. It enables reusable template fragments and advanced rendering patterns.',
                'options'     => [
                    ['text' => 'A template block not rendered by default; used with structural directives and ngTemplateOutlet', 'correct' => true],
                    ['text' => 'The root template file for an Angular application (like app.component.html)', 'correct' => false],
                    ['text' => 'An HTML template tag that Angular replaces with a component', 'correct' => false],
                    ['text' => 'A directive that caches rendered templates to improve performance', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is content projection (`ng-content`) in Angular?',
                'explanation' => 'Content projection allows a parent component to insert HTML content into a child component\'s template via `<ng-content>`. It works like the slot mechanism in Web Components. The child defines `<ng-content></ng-content>` as a placeholder; the parent passes content between the child\'s opening and closing tags. This enables flexible, reusable wrapper components (cards, modals, dialogs).',
                'options'     => [
                    ['text' => 'ng-content is a placeholder in the child template where the parent projects HTML content', 'correct' => true],
                    ['text' => 'A directive that fetches content from an API and projects it into the template', 'correct' => false],
                    ['text' => 'A way for child components to emit content events to the parent', 'correct' => false],
                    ['text' => 'An Angular module that manages dynamic content loading', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a template reference variable in Angular?',
                'explanation' => 'A template reference variable is declared with the `#` prefix on an element: `<input #emailInput type="email">`. It gives a reference to the DOM element (or component/directive instance) that can be used elsewhere in the same template: `<button (click)="save(emailInput.value)">`. For components, the variable holds the component instance, exposing its public properties and methods.',
                'options'     => [
                    ['text' => 'A #name declaration on an element that provides a reference usable within the same template', 'correct' => true],
                    ['text' => 'A TypeScript variable declared in the component class for template use', 'correct' => false],
                    ['text' => 'A reference to another component injected via the DI system', 'correct' => false],
                    ['text' => 'A way to define template literal strings inside Angular HTML', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is pipe chaining in Angular?',
                'explanation' => 'Pipe chaining applies multiple pipes to a value in sequence using the `|` operator repeatedly: `{{ birthday | date:\'shortDate\' | uppercase }}`. Angular evaluates pipes left to right — the output of each pipe becomes the input of the next. This keeps transformations composable and readable without adding logic to the component.',
                'options'     => [
                    ['text' => 'Applying multiple pipes in sequence: {{ value | pipe1 | pipe2 | pipe3 }}', 'correct' => true],
                    ['text' => 'Connecting two custom pipes together in a shared module', 'correct' => false],
                    ['text' => 'Linking pipe output directly to a component property without interpolation', 'correct' => false],
                    ['text' => 'Using RxJS pipe() inside a custom pipe\'s transform method', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `async` pipe do in Angular?',
                'explanation' => 'The `async` pipe subscribes to an Observable or Promise, returns the latest emitted value, and automatically unsubscribes when the component is destroyed. Usage: `{{ data$ | async }}` or `*ngIf="user$ | async as user"`. It eliminates manual subscribe/unsubscribe in the component class and prevents memory leaks. It also triggers change detection when a new value arrives.',
                'options'     => [
                    ['text' => 'Subscribes to an Observable or Promise in the template and auto-unsubscribes on destroy', 'correct' => true],
                    ['text' => 'Makes a component method asynchronous so it can use await in the template', 'correct' => false],
                    ['text' => 'Delays rendering of the element until the component finishes loading', 'correct' => false],
                    ['text' => 'A pipe that converts a callback-based function into a Promise', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the Angular built-in `date` pipe do?',
                'explanation' => 'The `date` pipe formats a date value (Date object, timestamp, or ISO string) into a human-readable string: `{{ today | date:\'dd/MM/yyyy\' }}`. It uses Angular\'s internationalization (i18n) API. Common format shorthands include `short`, `medium`, `long`, `shortDate`, `shortTime`. The locale can be configured with `LOCALE_ID`.',
                'options'     => [
                    ['text' => 'Formats a date value into a locale-aware string using a format pattern', 'correct' => true],
                    ['text' => 'Returns the current date and time as a formatted string', 'correct' => false],
                    ['text' => 'Converts a date string from the API into a JavaScript Date object', 'correct' => false],
                    ['text' => 'Calculates the difference between two dates in the template', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What do the `uppercase` and `lowercase` pipes do in Angular?',
                'explanation' => '`uppercase` transforms a string to ALL CAPS: `{{ "hello" | uppercase }}` outputs `"HELLO"`. `lowercase` transforms to all lowercase: `{{ "WORLD" | lowercase }}` outputs `"world"`. Both are pure pipes that only recompute when the input reference changes. They are commonly used for display formatting without modifying the underlying data.',
                'options'     => [
                    ['text' => 'uppercase transforms a string to uppercase; lowercase transforms it to lowercase', 'correct' => true],
                    ['text' => 'They convert CSS class names to consistent casing conventions', 'correct' => false],
                    ['text' => 'They are the same pipe — Angular chooses the direction based on the current locale', 'correct' => false],
                    ['text' => 'They modify the component property value permanently when used in a template', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `currency` pipe do in Angular?',
                'explanation' => 'The `currency` pipe formats a number as a currency string: `{{ price | currency:\'USD\':\'symbol\':\'1.2-2\' }}` outputs `"$12.50"`. Parameters are: currency code (ISO 4217), display format (`symbol`, `code`, `name`, or custom), and digit info. It is locale-aware and uses Angular\'s i18n infrastructure to apply correct formatting rules.',
                'options'     => [
                    ['text' => 'Formats a number as a locale-aware currency string with symbol and decimal places', 'correct' => true],
                    ['text' => 'Converts currency strings from one currency to another using exchange rates', 'correct' => false],
                    ['text' => 'A pipe that validates currency input fields in reactive forms', 'correct' => false],
                    ['text' => 'Formats numbers with a comma separator but without a currency symbol', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `json` pipe do in Angular and when is it useful?',
                'explanation' => 'The `json` pipe converts any value to a JSON string representation: `{{ myObject | json }}`. It is the template equivalent of `JSON.stringify()` with indentation. It is primarily used during development and debugging to inspect the current state of objects and arrays directly in the template without opening DevTools.',
                'options'     => [
                    ['text' => 'Converts an object or array to a pretty-printed JSON string — useful for debugging', 'correct' => true],
                    ['text' => 'Parses a JSON string received from an API into a JavaScript object', 'correct' => false],
                    ['text' => 'Serializes component state to JSON and stores it in localStorage', 'correct' => false],
                    ['text' => 'Formats HTTP response bodies that contain JSON data', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the `keyvalue` pipe do in Angular?',
                'explanation' => 'The `keyvalue` pipe transforms an object or Map into an array of `{ key, value }` pairs that can be iterated with `*ngFor`: `*ngFor="let item of myObj | keyvalue"`. Without this pipe, iterating over an object\'s properties in a template is not directly possible. The pairs are sorted by key alphabetically by default.',
                'options'     => [
                    ['text' => 'Transforms an object or Map into an iterable array of { key, value } pairs for *ngFor', 'correct' => true],
                    ['text' => 'Extracts only the keys from an object into an array', 'correct' => false],
                    ['text' => 'Sorts an array of objects by a specified key property', 'correct' => false],
                    ['text' => 'Converts a key-value query string into a JavaScript object', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `ng serve` do in the Angular CLI?',
                'explanation' => '`ng serve` builds the application in development mode, starts a local development server (default port 4200), and watches for file changes — recompiling and live-reloading the browser automatically. It uses Webpack (or esbuild in newer Angular versions) under the hood. Common flags: `--port` to change the port, `--open` to launch the browser, `--ssl` for HTTPS.',
                'options'     => [
                    ['text' => 'Builds and serves the app locally with live-reload for development (port 4200 by default)', 'correct' => true],
                    ['text' => 'Serves the production build to an Nginx or Apache server', 'correct' => false],
                    ['text' => 'Creates a static server to preview the ng build output', 'correct' => false],
                    ['text' => 'Runs Angular unit tests and serves a coverage report on a local port', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `ng build` do in the Angular CLI?',
                'explanation' => '`ng build` compiles the Angular application and outputs the build artifacts (HTML, CSS, JS bundles) to the `dist/` folder. By default it produces a development build. Use `ng build --configuration=production` (or `ng build --prod` in older versions) for production — this enables AOT compilation, minification, tree-shaking, and source map generation control.',
                'options'     => [
                    ['text' => 'Compiles the Angular application and outputs build artifacts to the dist/ folder', 'correct' => true],
                    ['text' => 'Builds and immediately deploys the application to a production server', 'correct' => false],
                    ['text' => 'Installs all Angular dependencies listed in package.json', 'correct' => false],
                    ['text' => 'Runs the build pipeline inside a Docker container', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `ng test` do in the Angular CLI?',
                'explanation' => '`ng test` launches the Karma test runner, which executes all `.spec.ts` test files using Jasmine as the testing framework. It opens a browser instance, runs tests, and watches for file changes — re-running on each save. Code coverage reports can be generated with `ng test --code-coverage`. Angular also supports Jest as an alternative test runner.',
                'options'     => [
                    ['text' => 'Runs unit tests using the Karma test runner and Jasmine testing framework', 'correct' => true],
                    ['text' => 'Runs end-to-end tests using Protractor or Cypress', 'correct' => false],
                    ['text' => 'Validates TypeScript code for type errors without running tests', 'correct' => false],
                    ['text' => 'Generates test stubs for all components that do not have spec files', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are the three types of component selectors in Angular?',
                'explanation' => 'Angular components can use three selector types in their `@Component` decorator: (1) Element selector: `selector: "app-hero"` — used as `<app-hero>`; (2) Attribute selector: `selector: "[appHero]"` — used as `<div appHero>`; (3) Class selector: `selector: ".app-hero"` — used as `<div class="app-hero">`. Element selectors are the most common convention for components.',
                'options'     => [
                    ['text' => 'Element (app-hero), attribute ([appHero]), and class (.app-hero) selectors', 'correct' => true],
                    ['text' => 'ID, class, and tag selectors — the same as CSS selector types', 'correct' => false],
                    ['text' => 'Selector, template, and style — the three @Component metadata properties', 'correct' => false],
                    ['text' => 'Global, scoped, and shadow selectors based on encapsulation mode', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the correct order of Angular component lifecycle hooks?',
                'explanation' => 'The Angular component lifecycle hooks fire in this order: (1) ngOnChanges — when input properties change; (2) ngOnInit — once after first ngOnChanges; (3) ngDoCheck — every change detection run; (4) ngAfterContentInit — after content projection; (5) ngAfterContentChecked; (6) ngAfterViewInit — after view and child views are initialized; (7) ngAfterViewChecked; (8) ngOnDestroy — just before the component is destroyed.',
                'options'     => [
                    ['text' => 'ngOnChanges → ngOnInit → ngDoCheck → ngAfterContentInit → ngAfterViewInit → ngOnDestroy', 'correct' => true],
                    ['text' => 'ngOnInit → ngOnChanges → ngAfterViewInit → ngOnDestroy', 'correct' => false],
                    ['text' => 'constructor → ngOnInit → ngAfterViewInit → ngOnDestroy (no ngOnChanges)', 'correct' => false],
                    ['text' => 'ngOnInit → ngAfterContentInit → ngAfterViewInit → ngDoCheck → ngOnDestroy', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the relationship between `index.html` and `main.ts` in an Angular application?',
                'explanation' => '`index.html` is the HTML entry point — it contains the `<app-root>` placeholder and is served by the web server. `main.ts` is the TypeScript entry point — it calls `platformBrowserDynamic().bootstrapModule(AppModule)` (or `bootstrapApplication(AppComponent)` for standalone). The Angular CLI Webpack config maps `main.ts` as the entry point, which injects bundled scripts into `index.html` at build time.',
                'options'     => [
                    ['text' => 'index.html is the HTML shell with <app-root>; main.ts bootstraps the Angular app into it', 'correct' => true],
                    ['text' => 'main.ts generates index.html dynamically at runtime using server-side rendering', 'correct' => false],
                    ['text' => 'index.html imports main.ts directly with a <script> tag written by the developer', 'correct' => false],
                    ['text' => 'They are interchangeable — Angular can use either as the application entry point', 'correct' => false],
                ],
            ],
        ];
    }

    private function intermediateQuestions(): array
    {
        return [
            // --- Original 10 questions ---
            [
                'question'    => 'What is dependency injection (DI) in Angular?',
                'explanation' => 'Dependency Injection is a design pattern where dependencies (typically services) are provided to a class through its constructor rather than created internally. Angular\'s DI system instantiates and provides services automatically. Services decorated with `@Injectable({ providedIn: "root" })` are singletons available throughout the app. DI enables loose coupling and easier testing.',
                'options'     => [
                    ['text' => 'A pattern where Angular automatically provides services through component constructors', 'correct' => true],
                    ['text' => 'Importing Angular modules into other modules', 'correct' => false],
                    ['text' => 'Injecting HTML templates into components at runtime', 'correct' => false],
                    ['text' => 'A way to pass @Input() values between sibling components', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between reactive forms and template-driven forms in Angular?',
                'explanation' => 'Template-driven forms are defined in the template using directives like `ngModel` — simple but less testable and less scalable. Reactive forms are defined programmatically in the component class using `FormControl`, `FormGroup`, and `FormBuilder` — synchronous, more predictable, better for complex validation, and easier to test. Reactive forms are generally preferred for non-trivial forms.',
                'options'     => [
                    ['text' => 'Reactive forms are defined in the class with FormGroup; template-driven use ngModel in the template', 'correct' => true],
                    ['text' => 'Template-driven forms support validation; reactive forms do not', 'correct' => false],
                    ['text' => 'Reactive forms use two-way binding; template-driven forms use one-way binding', 'correct' => false],
                    ['text' => 'They are identical — just different import paths for the same API', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is Angular Router and how do you define routes?',
                'explanation' => 'Angular Router enables navigation between views in a SPA without full page reloads. Routes are defined as an array of objects with `path` and `component` properties, passed to `RouterModule.forRoot()` in the app module. The `<router-outlet>` directive marks where matched components are rendered. `routerLink` directive is used in templates for navigation.',
                'options'     => [
                    ['text' => 'A module that maps URL paths to components; routes defined in RouterModule.forRoot(routes)', 'correct' => true],
                    ['text' => 'A third-party library for managing navigation history in Angular', 'correct' => false],
                    ['text' => 'The Angular HTTP module for routing API requests to different servers', 'correct' => false],
                    ['text' => 'A directive that wraps links and prevents full page reloads', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `ngOnInit()` and when is it called?',
                'explanation' => '`ngOnInit()` is a lifecycle hook called once after Angular has initialized the component\'s data-bound properties (`@Input` values are set). It is the ideal place to fetch initial data from services or set up the component state. It runs after the constructor but before the view is rendered. Implement the `OnInit` interface to get TypeScript type safety.',
                'options'     => [
                    ['text' => 'A lifecycle hook called once after @Input properties are set — ideal for data fetching', 'correct' => true],
                    ['text' => 'A lifecycle hook that fires every time the component updates', 'correct' => false],
                    ['text' => 'The constructor of every Angular component (same as JavaScript constructor)', 'correct' => false],
                    ['text' => 'A hook that fires when the component is removed from the DOM', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an Angular service?',
                'explanation' => 'A service is a class decorated with `@Injectable` that encapsulates reusable business logic, data access, or shared state — separate from components. Services follow the single responsibility principle. With `providedIn: "root"`, Angular creates a singleton instance available throughout the entire app. Services are injected into components via constructor parameters.',
                'options'     => [
                    ['text' => 'A @Injectable class that encapsulates reusable business logic and shared state', 'correct' => true],
                    ['text' => 'A REST API endpoint that Angular calls to fetch data', 'correct' => false],
                    ['text' => 'A special type of Angular component without a template', 'correct' => false],
                    ['text' => 'A module that provides HTTP communication functionality', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an Angular pipe and how do you use one?',
                'explanation' => 'Pipes transform data in templates: `{{ value | pipeName:param }}`. Built-in pipes include `date`, `uppercase`, `lowercase`, `currency`, `percent`, `json`, `async`, and `keyvalue`. Pipes keep templates clean by moving transformation logic out of components. Custom pipes are classes decorated with `@Pipe({ name: "pipeName" })`.',
                'options'     => [
                    ['text' => 'A class that transforms template data using the | operator: {{ value | date }}', 'correct' => true],
                    ['text' => 'A way to pipe HTTP responses directly into component properties', 'correct' => false],
                    ['text' => 'An Angular directive for streaming data from an Observable', 'correct' => false],
                    ['text' => 'A tool for piping data between Angular modules at build time', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an Angular route guard (CanActivate)?',
                'explanation' => 'A route guard is a service that implements `CanActivate` (or `CanDeactivate`, `CanLoad`, etc.) and decides whether a route can be accessed. The `canActivate` method returns a boolean or Observable/Promise of boolean. Common use cases: restricting routes to authenticated users, preventing navigation with unsaved changes, or checking permissions before loading a feature module.',
                'options'     => [
                    ['text' => 'A service that determines whether a route can be accessed before navigation occurs', 'correct' => true],
                    ['text' => 'A decorator that marks a route as protected in the router configuration', 'correct' => false],
                    ['text' => 'A middleware that encrypts data passed between routes', 'correct' => false],
                    ['text' => 'A special component that wraps protected routes with an authentication UI', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is lazy loading in Angular?',
                'explanation' => 'Lazy loading defers the loading of a feature module until the user navigates to its route. Instead of `component: XyzComponent`, the route uses `loadChildren: () => import("./xyz/xyz.module").then(m => m.XyzModule)`. This reduces the initial bundle size, improving startup time. With standalone components (Angular 14+), lazy loading uses `loadComponent` instead.',
                'options'     => [
                    ['text' => 'Loading feature modules only when their route is navigated to, reducing initial bundle size', 'correct' => true],
                    ['text' => 'Delaying HTTP requests until the user interacts with a component', 'correct' => false],
                    ['text' => 'Loading images lazily using the browser\'s native loading="lazy"', 'correct' => false],
                    ['text' => 'Deferring component initialization until its data is fully loaded', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `@ViewChild()` do in Angular?',
                'explanation' => '`@ViewChild()` queries the component\'s template and returns the first matching element, directive, or child component instance. Example: `@ViewChild("myInput") inputRef!: ElementRef` gives a reference to a DOM element with `#myInput` in the template. Available from `ngAfterViewInit()`. Used to directly access child component methods or manipulate DOM elements.',
                'options'     => [
                    ['text' => 'Queries the template and returns a reference to a child element or component', 'correct' => true],
                    ['text' => 'Creates a child view for a template inside the current component', 'correct' => false],
                    ['text' => 'Subscribes to events emitted by a child component', 'correct' => false],
                    ['text' => 'Injects a child service into the parent component', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `HttpClient` in Angular and how do you use it?',
                'explanation' => 'HttpClient is Angular\'s built-in HTTP module for making requests to REST APIs. It returns Observables (not Promises), making it easy to compose with RxJS operators. Import `HttpClientModule` in `AppModule` (or `provideHttpClient()` in standalone). Inject `HttpClient` into a service: `constructor(private http: HttpClient)`. Methods: `get()`, `post()`, `put()`, `delete()`, `patch()`.',
                'options'     => [
                    ['text' => 'Angular\'s built-in HTTP service that returns Observables for API communication', 'correct' => true],
                    ['text' => 'A global fetch wrapper that converts all HTTP responses to promises', 'correct' => false],
                    ['text' => 'A third-party library that must be installed for HTTP requests in Angular', 'correct' => false],
                    ['text' => 'A service that caches HTTP responses locally to avoid duplicate requests', 'correct' => false],
                ],
            ],
            // --- 23 new Intermediate questions ---
            [
                'question'    => 'What is the `FormBuilder` service in Angular reactive forms?',
                'explanation' => '`FormBuilder` is an Angular service that provides shorthand factory methods for creating `FormGroup`, `FormControl`, and `FormArray` instances. Instead of `new FormGroup({ name: new FormControl("") })`, you write `this.fb.group({ name: [""] })`. It is injected via DI and reduces boilerplate when building complex reactive forms. Import `ReactiveFormsModule` and inject `FormBuilder` in the constructor.',
                'options'     => [
                    ['text' => 'A service with shorthand factory methods (group, control, array) for building reactive forms', 'correct' => true],
                    ['text' => 'A service that validates form fields and generates error messages automatically', 'correct' => false],
                    ['text' => 'A directive that converts a template-driven form to a reactive form at runtime', 'correct' => false],
                    ['text' => 'A utility class that serializes form data into a JSON payload for HTTP requests', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What built-in validators does Angular provide for reactive forms?',
                'explanation' => 'Angular\'s `Validators` class provides built-in validation functions: `Validators.required` (non-empty), `Validators.email` (valid email format), `Validators.minLength(n)` (minimum character count), `Validators.maxLength(n)`, `Validators.min(n)` (numeric minimum), `Validators.max(n)`, `Validators.pattern(regex)`. They are passed as the second argument to `FormControl`: `new FormControl("", [Validators.required, Validators.email])`.',
                'options'     => [
                    ['text' => 'Validators.required, Validators.email, Validators.minLength, Validators.maxLength, Validators.pattern', 'correct' => true],
                    ['text' => 'required, email, and minLength — only three built-in validators exist', 'correct' => false],
                    ['text' => 'Angular provides no built-in validators — all validation must be custom', 'correct' => false],
                    ['text' => 'Validators are HTML attributes (required, pattern, minlength) applied to template inputs', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `FormArray` in Angular reactive forms?',
                'explanation' => '`FormArray` is a collection of `FormControl`, `FormGroup`, or nested `FormArray` instances — used when the number of controls is dynamic (e.g., a list of phone numbers the user can add/remove). You use `push()` to add controls and `removeAt(index)` to remove them. Access the array in the template using `.controls` to iterate with `*ngFor`.',
                'options'     => [
                    ['text' => 'A reactive form collection for a dynamic number of controls (add/remove at runtime)', 'correct' => true],
                    ['text' => 'An array of FormGroup instances representing multiple independent forms on the page', 'correct' => false],
                    ['text' => 'A directive that binds a TypeScript array to a list of form inputs', 'correct' => false],
                    ['text' => 'An Angular service that stores submitted form data in an in-memory array', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you write a custom validator function for a reactive form control?',
                'explanation' => 'A custom validator is a function that takes an `AbstractControl` and returns `null` (valid) or a validation error object (invalid). Example: `function noSpaces(control: AbstractControl): ValidationErrors | null { return control.value?.includes(" ") ? { noSpaces: true } : null; }`. Pass it in the `FormControl` validators array. For async validators, return an Observable or Promise.',
                'options'     => [
                    ['text' => 'A function (control: AbstractControl) => null | ValidationErrors — returns null when valid', 'correct' => true],
                    ['text' => 'A class that extends Validator and overrides the validate() lifecycle hook', 'correct' => false],
                    ['text' => 'A directive with selector "[customValidator]" applied to the input element', 'correct' => false],
                    ['text' => 'A method on FormControl called addValidator() that receives a regex pattern', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `AbstractControl` in Angular forms?',
                'explanation' => '`AbstractControl` is the base class for `FormControl`, `FormGroup`, and `FormArray`. It provides shared properties and methods: `value`, `valid`, `invalid`, `errors`, `dirty`, `touched`, `pristine`, `status`, `valueChanges`, `statusChanges` Observables, `setValue()`, `patchValue()`, `reset()`, `markAsTouched()`, `disable()`, `enable()`. Custom validators receive an `AbstractControl` so they work with any form control type.',
                'options'     => [
                    ['text' => 'The base class for FormControl, FormGroup, and FormArray providing shared state and methods', 'correct' => true],
                    ['text' => 'An abstract Angular service that must be extended to create form services', 'correct' => false],
                    ['text' => 'A generic interface for custom form components that host multiple inputs', 'correct' => false],
                    ['text' => 'A way to declare a form control without an initial value (undefined by default)', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `markAsTouched()` do in Angular reactive forms?',
                'explanation' => '`markAsTouched()` marks a form control (or all controls in a group/array) as touched, which triggers the display of validation error messages that are conditionally shown only when `control.touched` is true. This is typically called when the user submits the form without interacting with all fields — programmatically simulating the "blur" event to reveal validation errors.',
                'options'     => [
                    ['text' => 'Marks a control as touched to trigger validation error display without user interaction', 'correct' => true],
                    ['text' => 'Marks a control as dirty, indicating the user has changed its value', 'correct' => false],
                    ['text' => 'Disables the control and prevents further user input', 'correct' => false],
                    ['text' => 'Resets the control to its initial value and clears validation errors', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `setValue()` and `patchValue()` in Angular reactive forms?',
                'explanation' => '`setValue()` requires you to provide values for ALL controls in a `FormGroup` — omitting any control throws an error. `patchValue()` is more flexible — it only updates the controls you specify and ignores missing ones. Use `setValue()` when you have complete data (e.g., loading a full entity from the API). Use `patchValue()` for partial updates or when the shape of your data may not exactly match the form.',
                'options'     => [
                    ['text' => 'setValue() requires all control values; patchValue() accepts partial objects and ignores missing keys', 'correct' => true],
                    ['text' => 'setValue() triggers validation; patchValue() skips validation and updates silently', 'correct' => false],
                    ['text' => 'They are identical — setValue() is the older API replaced by patchValue()', 'correct' => false],
                    ['text' => 'patchValue() deep-merges nested objects; setValue() replaces the entire form tree', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `RouterModule.forChild()` and when is it used?',
                'explanation' => '`RouterModule.forChild(routes)` registers routes for a feature module without re-registering the Router service. `forRoot()` is called once in `AppModule` and sets up the Router singleton. Each lazy-loaded feature module calls `forChild()` to register its own routes. Calling `forRoot()` in a feature module would create a second Router instance, breaking navigation.',
                'options'     => [
                    ['text' => 'Registers feature module routes without re-creating the Router singleton — used in feature modules', 'correct' => true],
                    ['text' => 'A method for registering child routes inside a parent route configuration', 'correct' => false],
                    ['text' => 'An alternative to forRoot() for apps that use standalone components instead of NgModules', 'correct' => false],
                    ['text' => 'A way to configure lazy-loaded routes without importing RouterModule', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `ActivatedRoute.params` and `ActivatedRoute.queryParams`?',
                'explanation' => '`params` is an Observable of route parameters defined in the route path: `/users/:id` → `params` emits `{ id: "42" }`. `queryParams` is an Observable of the URL query string: `/users?page=2&size=10` → `queryParams` emits `{ page: "2", size: "10" }`. Both are Observables, so subscribe to react to navigation changes within the same component instance.',
                'options'     => [
                    ['text' => 'params holds route path segments (:id); queryParams holds URL query string (?key=val)', 'correct' => true],
                    ['text' => 'params is for required parameters; queryParams is for optional parameters', 'correct' => false],
                    ['text' => 'queryParams is synchronous; params is an Observable', 'correct' => false],
                    ['text' => 'They are the same — Angular merges path and query params into one params object', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you programmatically navigate in Angular using the Router service?',
                'explanation' => '`Router.navigate()` accepts a commands array (like `routerLink`): `this.router.navigate([\'/users\', userId])`. `Router.navigateByUrl()` accepts a string URL: `this.router.navigateByUrl(\'/dashboard\')`. Both return a Promise resolving to `true` (navigation succeeded) or `false` (cancelled). Use `navigate()` when building paths programmatically; use `navigateByUrl()` for absolute paths.',
                'options'     => [
                    ['text' => 'router.navigate(["/path", id]) or router.navigateByUrl("/absolute-path")', 'correct' => true],
                    ['text' => 'router.go("/path") is the standard method for programmatic navigation', 'correct' => false],
                    ['text' => 'Use Location.pushState() from the Angular platform browser package', 'correct' => false],
                    ['text' => 'Inject ActivatedRoute and call activatedRoute.navigate(["/path"])', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are `NavigationExtras` in Angular Router?',
                'explanation' => '`NavigationExtras` is an interface passed as the second argument to `router.navigate()` or `router.navigateByUrl()` to configure navigation behaviour. Common options include `queryParams` (add query parameters), `fragment` (URL fragment/hash), `queryParamsHandling` (`merge` or `preserve` existing params), `relativeTo` (navigate relative to an `ActivatedRoute`), and `replaceUrl` (replace history entry instead of pushing).',
                'options'     => [
                    ['text' => 'An options object for navigate() — sets queryParams, fragment, relativeTo, replaceUrl, etc.', 'correct' => true],
                    ['text' => 'Extra route metadata that is accessible in the target component via ActivatedRoute.extras', 'correct' => false],
                    ['text' => 'A class that extends Router with additional navigation helper methods', 'correct' => false],
                    ['text' => 'HTTP headers attached to API calls triggered during navigation', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `data` property on a route configuration object in Angular?',
                'explanation' => 'The `data` property in a route definition allows you to attach static metadata to a route: `{ path: "admin", component: AdminComponent, data: { role: "admin", title: "Admin Dashboard" } }`. The component reads it via `ActivatedRoute.data` Observable or `ActivatedRoute.snapshot.data`. Common uses: page titles, breadcrumb labels, required permissions, or animation metadata.',
                'options'     => [
                    ['text' => 'Static metadata attached to a route, readable in the component via ActivatedRoute.data', 'correct' => true],
                    ['text' => 'The data resolved by a Resolver before the route component is activated', 'correct' => false],
                    ['text' => 'A property for passing query parameters to the route without showing them in the URL', 'correct' => false],
                    ['text' => 'The component\'s @Input() data that Angular sets automatically on route activation', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you configure nested (child) routes in Angular?',
                'explanation' => 'Child routes are defined in a `children` array within a parent route: `{ path: "users", component: UsersComponent, children: [{ path: ":id", component: UserDetailComponent }] }`. The parent component must include a `<router-outlet>` to render the matched child component. The full URL for the child is the concatenation of parent and child paths: `/users/42`.',
                'options'     => [
                    ['text' => 'Using a children array in the parent route; the parent template must contain a <router-outlet>', 'correct' => true],
                    ['text' => 'Calling RouterModule.forChild() inside the parent component\'s constructor', 'correct' => false],
                    ['text' => 'Nesting <router-outlet> tags — Angular automatically creates child routes for each outlet', 'correct' => false],
                    ['text' => 'Adding a parent: "users" property to the child route configuration object', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `CanDeactivate` route guard in Angular?',
                'explanation' => '`CanDeactivate<T>` is a route guard that runs before a user navigates away from the current route. It receives the current component instance and can return a boolean or Observable<boolean>. Common use case: prompting the user to confirm navigation when a form has unsaved changes — "You have unsaved changes. Are you sure you want to leave?" The `T` generic is the component type being deactivated.',
                'options'     => [
                    ['text' => 'A guard that runs before navigating away, used to warn about unsaved changes', 'correct' => true],
                    ['text' => 'A guard that prevents a deactivated (disabled) route from being navigated to', 'correct' => false],
                    ['text' => 'A lifecycle hook called when a component is about to be destroyed', 'correct' => false],
                    ['text' => 'A guard that deactivates a route after a session timeout period', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `Resolve` guard in Angular Router?',
                'explanation' => 'A `Resolve<T>` guard pre-fetches data before the route is activated. The router waits for the Observable or Promise returned by `resolve()` to complete, then provides the data via `ActivatedRoute.data`. This avoids the blank/loading state that occurs when fetching data inside `ngOnInit`. If the resolver throws or returns an error, navigation is cancelled by default.',
                'options'     => [
                    ['text' => 'A guard that pre-fetches data before navigation completes, available in ActivatedRoute.data', 'correct' => true],
                    ['text' => 'A guard that resolves route path conflicts when multiple routes match a URL', 'correct' => false],
                    ['text' => 'A service that resolves dependency injection tokens inside lazy-loaded modules', 'correct' => false],
                    ['text' => 'A guard that resolves query parameters into component @Input() properties', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `PreloadAllModules` preloading strategy in Angular?',
                'explanation' => '`PreloadAllModules` is a built-in preloading strategy that loads all lazy modules in the background after the initial app loads. Configure it in `RouterModule.forRoot(routes, { preloadingStrategy: PreloadAllModules })`. This provides the fast initial load of lazy loading while minimizing navigation delay for subsequent routes. Custom strategies can implement `PreloadingStrategy` to preload selectively.',
                'options'     => [
                    ['text' => 'A strategy that preloads all lazy modules in the background after the app starts', 'correct' => true],
                    ['text' => 'A strategy that loads all route modules eagerly before the app renders', 'correct' => false],
                    ['text' => 'A configuration that forces all modules to use HTTP/2 push preloading', 'correct' => false],
                    ['text' => 'A Webpack plugin that pre-bundles all lazy chunks into the main bundle', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you attach an authentication token to every HTTP request using an interceptor?',
                'explanation' => 'An interceptor implementing `HttpInterceptor` clones the request with the `Authorization` header: `const authReq = req.clone({ setHeaders: { Authorization: "Bearer " + token } })`. Then call `next.handle(authReq)`. Register it in `AppModule` providers: `{ provide: HTTP_INTERCEPTORS, useClass: AuthInterceptor, multi: true }`. The `multi: true` is required because multiple interceptors can be chained.',
                'options'     => [
                    ['text' => 'Clone the request with the Authorization header in an HttpInterceptor, registered with HTTP_INTERCEPTORS multi', 'correct' => true],
                    ['text' => 'Set the token in HttpClient.defaults.headers in the app module', 'correct' => false],
                    ['text' => 'Use an HTTP_HEADERS injection token configured in the HttpClientModule import', 'correct' => false],
                    ['text' => 'Add an Authorization property to each HttpClient.get() call\'s options object', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `multi: true` mean in an Angular provider configuration?',
                'explanation' => '`multi: true` tells Angular\'s DI system that multiple providers can be registered for the same token and they should all be collected into an array rather than overriding each other. This is required for `HTTP_INTERCEPTORS`, `APP_INITIALIZER`, and other tokens that support multiple values. Without `multi: true`, the second registration would silently overwrite the first.',
                'options'     => [
                    ['text' => 'Allows multiple providers for the same token — all are injected as an array', 'correct' => true],
                    ['text' => 'Creates multiple singleton instances of a service (one per component)', 'correct' => false],
                    ['text' => 'Registers a provider in multiple modules simultaneously', 'correct' => false],
                    ['text' => 'Enables a service to be provided in both root and platform injectors', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you retry a failed HTTP request in Angular using RxJS?',
                'explanation' => 'Use the `retry(n)` operator in the pipe chain: `this.http.get("/api/data").pipe(retry(3))`. This resubscribes up to `n` times on error before propagating the failure. For more control, use `retryWhen()` or `retry({ count: 3, delay: 1000 })` (RxJS 7+) to add a delay between retries. Combine with `catchError()` to handle the final failure gracefully.',
                'options'     => [
                    ['text' => 'Pipe the Observable through retry(n) to resubscribe up to n times on error', 'correct' => true],
                    ['text' => 'Wrap the http.get() call in a try/catch block and call it again on error', 'correct' => false],
                    ['text' => 'Use the retryPolicy option in the HttpClient.get() options object', 'correct' => false],
                    ['text' => 'Configure a retry count in the HTTP interceptor\'s request options', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are `catchError` and `throwError` in RxJS used for in Angular HTTP calls?',
                'explanation' => '`catchError` intercepts an Observable error and lets you recover — either returning a fallback Observable or re-throwing: `pipe(catchError(err => of([])))`. `throwError(() => err)` creates an Observable that immediately errors, used to re-throw after logging: `pipe(catchError(err => { console.error(err); return throwError(() => err); }))`. Together they form the standard error-handling pattern for Angular HTTP services.',
                'options'     => [
                    ['text' => 'catchError handles Observable errors and can recover; throwError creates an error Observable', 'correct' => true],
                    ['text' => 'They are identical — catchError is an alias for throwError', 'correct' => false],
                    ['text' => 'catchError catches JavaScript runtime errors; throwError handles HTTP error responses', 'correct' => false],
                    ['text' => 'throwError retries the HTTP call; catchError swallows the error silently', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `switchMap` and when is it used with HTTP requests in Angular?',
                'explanation' => '`switchMap` maps each source value to an inner Observable, cancelling the previous inner Observable if a new value arrives. This is ideal for search-as-you-type: `searchTerm$.pipe(switchMap(term => this.http.get("/search?q=" + term)))` — if the user types quickly, only the latest HTTP request survives. Use `mergeMap` when all inner Observables should run concurrently; use `exhaustMap` when the first should complete before starting a new one.',
                'options'     => [
                    ['text' => 'Cancels the previous inner Observable when a new value arrives — ideal for typeahead search', 'correct' => true],
                    ['text' => 'Switches the HTTP method from GET to POST when the source emits a non-null value', 'correct' => false],
                    ['text' => 'Maps each item to an HTTP request and merges all responses simultaneously', 'correct' => false],
                    ['text' => 'An alias for flatMap that runs each inner Observable sequentially', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `forkJoin` in RxJS and when is it used in Angular?',
                'explanation' => '`forkJoin([obs1, obs2, obs3])` waits for all provided Observables to complete and emits a single array (or object) of their last values. It is used to make multiple parallel HTTP calls and wait for all to finish: `forkJoin({ users: this.http.get("/users"), roles: this.http.get("/roles") })`. If any Observable errors, `forkJoin` errors immediately. Use `combineLatest` if sources emit multiple values over time.',
                'options'     => [
                    ['text' => 'Runs multiple Observables in parallel and emits all results when every one completes', 'correct' => true],
                    ['text' => 'Forks the Observable stream into multiple branches that execute independently', 'correct' => false],
                    ['text' => 'An alias for Promise.all() that only works with Angular HTTP Observables', 'correct' => false],
                    ['text' => 'Joins an array of Observables sequentially — each starts after the previous completes', 'correct' => false],
                ],
            ],
        ];
    }

    private function advancedQuestions(): array
    {
        return [
            // --- Original 10 questions ---
            [
                'question'    => 'How does Angular\'s change detection mechanism work?',
                'explanation' => 'Angular\'s default change detection checks every component in the tree from top to bottom whenever an async event occurs (user interaction, HTTP response, timer). It compares current and previous values of template bindings and updates the DOM where differences are found. Zone.js patches async APIs to notify Angular when to run detection. This process is called a "digest cycle".',
                'options'     => [
                    ['text' => 'Checks every component top-to-bottom after async events, comparing binding values', 'correct' => true],
                    ['text' => 'Uses a virtual DOM to diff and patch only changed nodes', 'correct' => false],
                    ['text' => 'Observes all component Observables and re-renders when any emits', 'correct' => false],
                    ['text' => 'Runs once at startup and only when manually triggered with detectChanges()', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `ChangeDetectionStrategy.OnPush` and what are its benefits?',
                'explanation' => 'With `OnPush`, Angular skips change detection for a component unless: an `@Input` reference changes, an event originates from the component or its children, an Observable bound with `async` pipe emits, or `markForCheck()` is called. This dramatically reduces the number of components checked per cycle, improving performance for large component trees with immutable data.',
                'options'     => [
                    ['text' => 'Skips change detection unless input references change or events fire — improves performance', 'correct' => true],
                    ['text' => 'Pushes all change detection to the Zone.js microtask queue', 'correct' => false],
                    ['text' => 'Forces Angular to re-render the component on every frame', 'correct' => false],
                    ['text' => 'Removes the component from the change detection tree entirely', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are Angular Signals (Angular 17+)?',
                'explanation' => 'Signals are Angular\'s new fine-grained reactivity primitive. A signal holds a value and notifies its consumers when that value changes: `const count = signal(0); count.set(1)`. `computed()` creates derived signals that automatically update. Signals enable change detection without Zone.js and allow OnPush-like performance without manual effort — the future of Angular reactivity.',
                'options'     => [
                    ['text' => 'Reactive state primitives that track value changes and enable fine-grained updates without Zone.js', 'correct' => true],
                    ['text' => 'A replacement for @Output() EventEmitter for component communication', 'correct' => false],
                    ['text' => 'WebSocket signals for real-time data pushed from the server', 'correct' => false],
                    ['text' => 'An NgRx feature for managing global state with fewer imports', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an Angular HTTP Interceptor?',
                'explanation' => 'An interceptor implements `HttpInterceptor` and intercepts all outgoing requests and/or incoming responses globally. Common uses: adding auth headers (`Authorization: Bearer token`), logging, error handling, showing a loading spinner, or caching. Interceptors are registered as `multi` providers in the HTTP_INTERCEPTORS injection token.',
                'options'     => [
                    ['text' => 'Middleware that intercepts all HTTP requests/responses globally for transformation or logging', 'correct' => true],
                    ['text' => 'A route guard that intercepts navigation before HTTP requests are made', 'correct' => false],
                    ['text' => 'A service that blocks HTTP requests from unauthorized components', 'correct' => false],
                    ['text' => 'An Angular directive that wraps HTTP calls with retry logic', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are standalone components in Angular (Angular 14+)?',
                'explanation' => 'Standalone components are declared with `standalone: true` in their `@Component` decorator and do not need to be declared in an `@NgModule`. They directly import their dependencies (other components, directives, pipes) in their `imports` array. This simplifies the Angular architecture, removes boilerplate, and is the recommended approach in modern Angular.',
                'options'     => [
                    ['text' => 'Components with standalone: true that manage their own imports without NgModule', 'correct' => true],
                    ['text' => 'Components that have no @Input() dependencies and manage their own state', 'correct' => false],
                    ['text' => 'Components that can run outside of an Angular application in plain HTML', 'correct' => false],
                    ['text' => 'Components compiled to standalone Web Components using Angular Elements', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is Zone.js and its role in Angular?',
                'explanation' => 'Zone.js is a library that monkey-patches async browser APIs (setTimeout, Promises, XHR, etc.) to create an execution context called a "zone". Angular uses its NgZone to detect when async operations complete and trigger change detection. Without Zone.js, developers would need to manually call `detectChanges()`. Signals are Angular\'s path towards removing the Zone.js dependency.',
                'options'     => [
                    ['text' => 'A library that patches async APIs to notify Angular when to run change detection', 'correct' => true],
                    ['text' => 'A time-zone library used by Angular\'s DatePipe for locale formatting', 'correct' => false],
                    ['text' => 'A browser security zone that sandboxes Angular applications', 'correct' => false],
                    ['text' => 'A webpack plugin that defines code splitting zones for lazy loading', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an Angular Resolver?',
                'explanation' => 'A Resolver is a service that pre-fetches data before a route is activated. It implements the `Resolve<T>` interface and returns an Observable or Promise. The router waits for it to complete before rendering the component. The resolved data is available via `ActivatedRoute.data`. This avoids the loading flicker of fetching data inside `ngOnInit`.',
                'options'     => [
                    ['text' => 'A service that pre-fetches data before a route is rendered, via the route\'s resolve config', 'correct' => true],
                    ['text' => 'A service that resolves Observable errors from HTTP requests', 'correct' => false],
                    ['text' => 'A dependency injection token resolver for services', 'correct' => false],
                    ['text' => 'A class that resolves route path conflicts when multiple routes match', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is RxJS and how is it used in Angular?',
                'explanation' => 'RxJS (Reactive Extensions for JavaScript) is a library for reactive programming using Observables. Angular heavily uses RxJS — HttpClient returns Observables, Router events are Observables, and reactive forms use Observables. Common operators: `map`, `switchMap`, `mergeMap`, `catchError`, `takeUntil`, `debounceTime`, `distinctUntilChanged`. The `async` pipe subscribes to Observables directly in templates.',
                'options'     => [
                    ['text' => 'A reactive programming library used by Angular for async streams (HTTP, Router, forms)', 'correct' => true],
                    ['text' => 'Angular\'s built-in state management solution', 'correct' => false],
                    ['text' => 'A testing library for Angular that simplifies async test assertions', 'correct' => false],
                    ['text' => 'A CSS-in-JS library for reactive Angular component styles', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is Angular Universal and what problem does it solve?',
                'explanation' => 'Angular Universal is Angular\'s Server-Side Rendering (SSR) solution. By default, Angular renders entirely in the browser (CSR), which causes slow time-to-first-contentful-paint and poor SEO (search engine crawlers may not execute JavaScript). Universal renders the app on the server to plain HTML, which is sent to the client immediately — improving perceived performance and search engine indexability.',
                'options'     => [
                    ['text' => 'Angular\'s SSR solution — renders the app on the server to improve performance and SEO', 'correct' => true],
                    ['text' => 'A package that makes Angular apps work offline using Service Workers', 'correct' => false],
                    ['text' => 'A universal component library that works in Angular, React, and Vue', 'correct' => false],
                    ['text' => 'A build optimization tool that compiles Angular apps for multiple platforms', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the Angular Ivy compiler?',
                'explanation' => 'Ivy is Angular\'s default compilation and rendering pipeline since Angular 9. It compiles components to incremental DOM instructions (adds/removes nodes directly) rather than generating a component factory. Ivy produces smaller bundle sizes (better tree-shaking), faster compilation, better debugging (component names visible in DevTools), and enables new features like standalone components.',
                'options'     => [
                    ['text' => 'Angular\'s default compiler since v9 — produces smaller bundles and faster incremental compilation', 'correct' => true],
                    ['text' => 'An Angular package for rendering server-side HTML with the Express framework', 'correct' => false],
                    ['text' => 'The legacy Angular View Engine compiler replaced in v9', 'correct' => false],
                    ['text' => 'A plugin for integrating Angular with native mobile apps', 'correct' => false],
                ],
            ],
            // --- 23 new Advanced questions ---
            [
                'question'    => 'What is the Angular CDK (Component Dev Kit)?',
                'explanation' => 'The Angular CDK provides a set of behaviour primitives and utilities for building UI components — without prescribing visual styles. Key CDK packages: `@angular/cdk/overlay` (floating panels), `@angular/cdk/drag-drop` (drag-and-drop), `@angular/cdk/virtual-scroll` (virtualized lists), `@angular/cdk/a11y` (accessibility), `@angular/cdk/portal` (dynamic component insertion). Angular Material is built on top of the CDK.',
                'options'     => [
                    ['text' => 'A library of behaviour primitives (overlays, drag-drop, a11y, portals) without visual styles', 'correct' => true],
                    ['text' => 'Angular\'s code generation toolkit for scaffolding components from design files', 'correct' => false],
                    ['text' => 'A development kit for building Angular applications inside Docker containers', 'correct' => false],
                    ['text' => 'The Angular compiler development kit used to create custom Schematics', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How does Angular Material theming work?',
                'explanation' => 'Angular Material uses a Sass-based theming system built around palettes and design tokens. You define a theme by combining a primary palette, an accent palette, and a warn palette using `mat.define-light-theme()` or `mat.define-dark-theme()`, then apply it with `mat.all-component-themes($theme)` in your global stylesheet. Angular Material 17+ uses M3 (Material Design 3) tokens. Custom component themes can be created with `mat.component-theme()` mixins.',
                'options'     => [
                    ['text' => 'A Sass-based system of palettes and design tokens applied via mat.all-component-themes()', 'correct' => true],
                    ['text' => 'A CSS variables file that Angular Material reads at runtime to apply brand colors', 'correct' => false],
                    ['text' => 'A JSON configuration object passed to MaterialModule.forRoot() in AppModule', 'correct' => false],
                    ['text' => 'Theming is handled exclusively by Angular Material\'s pre-built CSS themes (indigo-pink.css)', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is multi-slot content projection in Angular and how is it achieved?',
                'explanation' => 'Multi-slot content projection uses the `select` attribute on `<ng-content>` to target specific projected content using CSS selectors: `<ng-content select="[header]"></ng-content>` matches `<div header>...</div>`. The `ngProjectAs` attribute overrides an element\'s selector for projection purposes — useful for projecting components rather than raw HTML elements. A default `<ng-content>` without `select` captures remaining content.',
                'options'     => [
                    ['text' => 'Multiple <ng-content select="[attr]"> slots target specific content by CSS selector', 'correct' => true],
                    ['text' => 'Multiple <ng-content> tags are indexed numerically — Angular fills them in order', 'correct' => false],
                    ['text' => 'Named slots are declared with <ng-content name="header"> and matched with slot="header"', 'correct' => false],
                    ['text' => 'Multi-slot projection requires the ContentChild decorator for each slot', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you dynamically create and insert a component in Angular?',
                'explanation' => 'In modern Angular (14+), inject `ViewContainerRef` and call `viewContainerRef.createComponent(MyComponent)` — no factory needed. The returned `ComponentRef` exposes the instance, inputs, and a `destroy()` method. Before Angular 14, you needed `ComponentFactoryResolver.resolveComponentFactory(MyComponent)`. Dynamic components are useful for modals, toasts, and any UI that cannot be determined at compile time.',
                'options'     => [
                    ['text' => 'Inject ViewContainerRef and call createComponent(MyComponent) to instantiate it dynamically', 'correct' => true],
                    ['text' => 'Use *ngIf with a component variable — Angular creates the component when the variable is set', 'correct' => false],
                    ['text' => 'Call document.createElement() and bootstrap the component manually using ApplicationRef', 'correct' => false],
                    ['text' => 'Inject ComponentFactory and call factory.create() inside ngAfterViewInit', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What was `ComponentFactoryResolver` in Angular and why is it considered legacy?',
                'explanation' => '`ComponentFactoryResolver` was the pre-Angular 13 API for dynamic component creation. You called `resolver.resolveComponentFactory(MyComponent)` to get a factory, then used it with `ViewContainerRef.createComponent(factory)`. Since Angular 13, `ViewContainerRef.createComponent(MyComponent)` accepts the component class directly — `ComponentFactoryResolver` is deprecated and no longer needed with the Ivy compiler\'s improved linker.',
                'options'     => [
                    ['text' => 'A pre-v13 API for getting a component factory — replaced by direct createComponent(Class) in Ivy', 'correct' => true],
                    ['text' => 'A service that resolves component selector conflicts in the same module', 'correct' => false],
                    ['text' => 'A compiler plugin that resolves factory patterns in component class decorators', 'correct' => false],
                    ['text' => 'The internal Ivy mechanism for creating component instances during bootstrapping', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the Angular CDK Portal and PortalOutlet?',
                'explanation' => 'A `Portal` is a piece of UI (a `TemplatePortal` for `ng-template` or a `ComponentPortal` for a component) that can be attached to a `PortalOutlet` (a DOM location). This enables rendering content outside its normal template position — for example, rendering a component created in one part of the app into a modal overlay managed by another. Angular Material\'s overlay system is built on portals.',
                'options'     => [
                    ['text' => 'A CDK abstraction for rendering UI (template or component) into a detached DOM location', 'correct' => true],
                    ['text' => 'A router outlet alternative that renders components in multiple named regions', 'correct' => false],
                    ['text' => 'An Angular Universal feature for sending server-rendered HTML to the client', 'correct' => false],
                    ['text' => 'An Angular service that proxies component events to a portal (external app)', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `@HostListener` decorator in Angular?',
                'explanation' => '`@HostListener("eventName", ["$event"])` decorates a method in a directive or component to listen to events on the host element. Example: `@HostListener("click", ["$event"]) onClick(e: MouseEvent) { ... }`. It can also listen to global events: `@HostListener("window:resize")`. It is cleaner than manually adding/removing event listeners and integrates with Angular\'s change detection.',
                'options'     => [
                    ['text' => 'Decorates a method to listen to an event on the host element or a global target', 'correct' => true],
                    ['text' => 'A decorator that registers the host component as an event listener for its children', 'correct' => false],
                    ['text' => 'A lifecycle hook that fires when a DOM event occurs inside the component\'s view', 'correct' => false],
                    ['text' => 'A way to host Angular event listeners outside the component in a shared service', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `@HostBinding` decorator in Angular?',
                'explanation' => '`@HostBinding("property")` binds a class property to a property or attribute of the host element. Example: `@HostBinding("class.active") isActive = false` adds/removes the `active` class based on `isActive`. `@HostBinding("attr.aria-expanded") expanded = false` sets an attribute. It is commonly used in attribute directives to modify the host element\'s appearance or behaviour without needing a template.',
                'options'     => [
                    ['text' => 'Binds a directive/component property to a host element DOM property, attribute, or class', 'correct' => true],
                    ['text' => 'Binds the host component\'s selector to a CSS class applied across the app', 'correct' => false],
                    ['text' => 'A decorator that makes a property available as an @Input() on the host element', 'correct' => false],
                    ['text' => 'Creates a two-way binding between the host element and the component\'s template', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `ElementRef` and `Renderer2` for DOM access in Angular?',
                'explanation' => '`ElementRef` gives direct access to the native DOM element via `nativeElement`. While convenient, direct DOM manipulation bypasses Angular\'s safety checks and breaks server-side rendering. `Renderer2` is an abstraction layer — use methods like `renderer.addClass(el, "foo")`, `renderer.setStyle(el, "color", "red")` — that works across platforms (browser, server, WebWorker) and respects Angular\'s security context. Always prefer `Renderer2`.',
                'options'     => [
                    ['text' => 'ElementRef gives raw nativeElement access; Renderer2 provides safe cross-platform DOM manipulation', 'correct' => true],
                    ['text' => 'ElementRef is for reading DOM properties; Renderer2 is for setting component @Input properties', 'correct' => false],
                    ['text' => 'Renderer2 is deprecated — ElementRef is the current recommended API for DOM access', 'correct' => false],
                    ['text' => 'They are identical — Renderer2 is a typed wrapper around ElementRef.nativeElement', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you create a custom structural directive in Angular?',
                'explanation' => 'A custom structural directive injects `TemplateRef` (the embedded template) and `ViewContainerRef` (the container to render into). Call `viewContainerRef.createEmbeddedView(templateRef)` to add the element and `viewContainerRef.clear()` to remove it. Use the `@Input()` setter to react to the condition. The `*` prefix in the template is syntactic sugar for `<ng-template [directive]="expr">`. Register the directive in the module declarations.',
                'options'     => [
                    ['text' => 'Inject TemplateRef and ViewContainerRef; call createEmbeddedView() or clear() based on logic', 'correct' => true],
                    ['text' => 'Extend the NgIf class and override its condition property', 'correct' => false],
                    ['text' => 'Use @Structural() decorator on an attribute directive class', 'correct' => false],
                    ['text' => 'Implement the StructuralDirective interface and override the render() method', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you create a custom attribute directive in Angular?',
                'explanation' => 'A custom attribute directive is a class decorated with `@Directive({ selector: "[appHighlight]" })`. Inject `ElementRef` and/or `Renderer2` to access/modify the host element. Use `@HostListener` to react to events and `@Input()` to accept configuration. Example: `@Directive({ selector: "[appHighlight]" }) export class HighlightDirective { @Input() appHighlight = "yellow"; @HostListener("mouseenter") onEnter() { this.renderer.setStyle(this.el.nativeElement, "background", this.appHighlight); } }`.',
                'options'     => [
                    ['text' => 'A @Directive class using ElementRef/Renderer2 and @HostListener to modify the host element', 'correct' => true],
                    ['text' => 'A component with no template that adds attributes to its parent component', 'correct' => false],
                    ['text' => 'A service that exports an applyDirective() function called inside ngAfterViewInit', 'correct' => false],
                    ['text' => 'An extended HTML element registered with customElements.define() and wrapped in Angular', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `provideHttpClient()` in modern Angular and how does it differ from `HttpClientModule`?',
                'explanation' => '`provideHttpClient()` is the standalone-friendly way to configure the HTTP client, used in `bootstrapApplication()`: `bootstrapApplication(AppComponent, { providers: [provideHttpClient(withInterceptorsFromDi())] })`. It replaces importing `HttpClientModule` in `AppModule`. It enables tree-shakable HTTP interceptor registration and supports the functional interceptor API introduced in Angular 15. `HttpClientModule` is still valid but considered legacy for new apps.',
                'options'     => [
                    ['text' => 'A functional provider for HttpClient in standalone apps — replaces HttpClientModule import', 'correct' => true],
                    ['text' => 'A standalone HTTP client library that does not depend on RxJS', 'correct' => false],
                    ['text' => 'A configuration function that registers all HTTP services without the providers array', 'correct' => false],
                    ['text' => 'An Angular CLI schematic that auto-generates HTTP services from an OpenAPI spec', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `providedIn: "root"`, `"any"`, and `"platform"` in Angular?',
                'explanation' => '`providedIn: "root"` creates a single app-wide singleton in the root injector — the most common option. `providedIn: "any"` creates a separate instance for each lazy-loaded module that injects it (and a single instance for all eagerly loaded code). `providedIn: "platform"` creates a singleton shared across all Angular apps on the same page (useful for micro-frontend architectures where multiple Angular apps share a service).',
                'options'     => [
                    ['text' => 'root = app singleton; any = one per lazy module; platform = shared across multiple Angular apps', 'correct' => true],
                    ['text' => 'root = lazy loaded; any = eagerly loaded; platform = loaded in main.ts bootstrap', 'correct' => false],
                    ['text' => 'They are all equivalent — Angular normalizes them to root-level singletons', 'correct' => false],
                    ['text' => 'platform and root are the same; any creates a new instance for every component injection', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an `InjectionToken` in Angular and when do you use one?',
                'explanation' => '`InjectionToken<T>` creates a DI token for non-class values (strings, objects, configs, functions). Example: `export const API_URL = new InjectionToken<string>("API_URL")`. Provide it: `{ provide: API_URL, useValue: "https://api.example.com" }`. Inject it: `constructor(@Inject(API_URL) private apiUrl: string)`. Use injection tokens when you cannot use a class as the token — for configuration, primitives, or interfaces (which have no runtime representation in TypeScript).',
                'options'     => [
                    ['text' => 'A typed DI token for non-class values like strings or config objects — injected with @Inject()', 'correct' => true],
                    ['text' => 'A token that grants permission to inject a service across module boundaries', 'correct' => false],
                    ['text' => 'A way to create anonymous (unnamed) services without a class definition', 'correct' => false],
                    ['text' => 'An Angular CLI token used for environment-specific variable injection at build time', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `forwardRef` in Angular and why is it needed?',
                'explanation' => '`forwardRef(() => SomeClass)` wraps a class reference that is used before it is declared. TypeScript evaluates decorators at the point of class declaration, so if class A references class B that is declared later in the same file, `forwardRef` defers evaluation to runtime. Common use: providing a component as its own `NG_VALUE_ACCESSOR` token — `provide: NG_VALUE_ACCESSOR, useExisting: forwardRef(() => MyInputComponent)`.',
                'options'     => [
                    ['text' => 'Wraps a class reference to defer evaluation — used when referencing a class before it is declared', 'correct' => true],
                    ['text' => 'Forwards a DI injection from a parent injector to a child injector', 'correct' => false],
                    ['text' => 'A router method that redirects users to a future (not yet created) route', 'correct' => false],
                    ['text' => 'A utility that imports a module before its NgModule decorator is evaluated', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `APP_INITIALIZER` in Angular?',
                'explanation' => '`APP_INITIALIZER` is a DI token that accepts an array of factory functions run during application initialization, before the app renders. If a factory returns a Promise or Observable, Angular waits for it to resolve before bootstrapping. Use case: fetching configuration from a remote server, loading translations, or checking authentication state before rendering the first view. Register with `{ provide: APP_INITIALIZER, useFactory: ..., deps: [...], multi: true }`.',
                'options'     => [
                    ['text' => 'A multi-provider token whose factory functions run and complete before the app renders', 'correct' => true],
                    ['text' => 'The entry-point function in main.ts that bootstraps the Angular application', 'correct' => false],
                    ['text' => 'A lifecycle hook that fires once when the root AppComponent is first initialized', 'correct' => false],
                    ['text' => 'An Angular CLI configuration for pre-rendering specific routes at build time', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `ENVIRONMENT_INITIALIZER` in Angular?',
                'explanation' => '`ENVIRONMENT_INITIALIZER` (Angular 14+) is similar to `APP_INITIALIZER` but scoped to an environment injector (such as a route\'s injector in standalone routing). It accepts a factory function that runs when the environment injector is created. Use it to perform side effects (e.g., registering icons, initializing logging) when a lazy-loaded feature\'s providers are created — without affecting the root injector.',
                'options'     => [
                    ['text' => 'A provider token whose factory runs when an environment (feature) injector is created', 'correct' => true],
                    ['text' => 'A configuration token for setting environment variables accessible throughout the app', 'correct' => false],
                    ['text' => 'An Angular Universal hook that runs initializers on the server before SSR', 'correct' => false],
                    ['text' => 'An alias for APP_INITIALIZER introduced to support standalone components', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is Angular SSR hydration and why does it matter?',
                'explanation' => 'With traditional Angular Universal SSR, the server sends HTML to the browser, Angular bootstraps in the browser and destroys the server-rendered DOM before re-rendering (causing a flash). Hydration (Angular 16+) allows Angular to reuse the server-rendered DOM nodes rather than destroying them — the app "hydrates" by attaching event listeners and state to existing HTML. This eliminates the content flash and improves Core Web Vitals (LCP, CLS).',
                'options'     => [
                    ['text' => 'Reusing server-rendered DOM instead of destroying and re-rendering it in the browser', 'correct' => true],
                    ['text' => 'Pre-fetching data on the server and injecting it into the Angular state as JSON', 'correct' => false],
                    ['text' => 'Running Angular change detection on the server to reduce client-side work', 'correct' => false],
                    ['text' => 'A process that converts static HTML into a fully interactive Angular application at build time', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are deferrable views (`@defer`) in Angular 17+?',
                'explanation' => '`@defer` is a built-in template block that lazily loads a chunk of the template (and its associated components/pipes/directives) based on a trigger condition. Example: `@defer (on viewport) { <heavy-chart /> } @placeholder { <div>Loading...</div> }`. Triggers include `on idle`, `on viewport`, `on hover`, `on timer(2s)`, and `when condition`. It enables code-splitting at the template level without any routing configuration.',
                'options'     => [
                    ['text' => 'A template block that lazy-loads a section and its dependencies based on a trigger condition', 'correct' => true],
                    ['text' => 'An async/await syntax extension for Angular templates to defer data binding', 'correct' => false],
                    ['text' => 'A route-level preloading strategy that defers module loading until user interaction', 'correct' => false],
                    ['text' => 'A decorator that marks a component to be loaded lazily the first time its route is visited', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the new control flow syntax (`@if`, `@for`, `@switch`) introduced in Angular 17?',
                'explanation' => 'Angular 17 introduced built-in control flow syntax as an alternative to structural directives. `@if (condition) { } @else { }` replaces `*ngIf`. `@for (item of items; track item.id) { } @empty { }` replaces `*ngFor` (with mandatory `track` for performance). `@switch (value) { @case (x) { } @default { } }` replaces `ngSwitch`. The new syntax is built into the compiler — no directive imports needed and better type narrowing.',
                'options'     => [
                    ['text' => 'Built-in template control flow blocks replacing *ngIf/*ngFor/ngSwitch with better type narrowing', 'correct' => true],
                    ['text' => 'JavaScript if/for/switch statements that Angular compiles into templates at build time', 'correct' => false],
                    ['text' => 'New TypeScript decorators for conditional component rendering', 'correct' => false],
                    ['text' => 'Signal-based directives that only re-render when the tracked signal changes', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `effect()` function in Angular Signals?',
                'explanation' => '`effect()` creates a reactive side effect that runs whenever any signal it reads changes. Example: `effect(() => { console.log("Count changed:", count()); })`. Effects are useful for syncing signals to non-reactive systems (e.g., localStorage, analytics, third-party libraries). They run after change detection. Effects must be created in an injection context (constructor, or with `injector` option). Use sparingly — prefer derived signals with `computed()` when possible.',
                'options'     => [
                    ['text' => 'Creates a reactive side effect that re-runs automatically when its read signals change', 'correct' => true],
                    ['text' => 'A function that applies an effect (CSS animation) to a component when a signal changes', 'correct' => false],
                    ['text' => 'The signals equivalent of ngOnChanges — runs when @Input signal properties change', 'correct' => false],
                    ['text' => 'A RxJS operator for applying side effects inside an Observable pipe without modifying values', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are `toSignal()` and `toObservable()` in Angular?',
                'explanation' => '`toSignal(observable$)` converts an RxJS Observable into an Angular Signal — the signal holds the latest emitted value and updates reactively. `toObservable(signal)` converts a Signal into an Observable that emits whenever the signal changes (using an `effect` internally). Both are in `@angular/core/rxjs-interop` and enable seamless interoperability between the Observable and Signal reactivity models. `toSignal` requires an injection context.',
                'options'     => [
                    ['text' => 'toSignal() wraps an Observable as a Signal; toObservable() wraps a Signal as an Observable', 'correct' => true],
                    ['text' => 'toSignal() converts a Promise to a Signal; toObservable() converts a Signal to a Promise', 'correct' => false],
                    ['text' => 'They are utility functions for converting Angular forms to signal-based reactive forms', 'correct' => false],
                    ['text' => 'toObservable() subscribes to a signal; toSignal() unsubscribes and returns the last value', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is Angular DevTools and what profiling capabilities does it offer?',
                'explanation' => 'Angular DevTools is a browser extension (Chrome/Firefox) for debugging and profiling Angular applications. Its Profiler tab records change detection cycles and displays which components were checked, how long each check took, and which source event triggered the cycle. This makes it easy to identify components causing performance bottlenecks. The Component Explorer tab shows the component tree, inputs, outputs, and injected services for any selected component.',
                'options'     => [
                    ['text' => 'A browser extension that profiles change detection cycles and visualizes the component tree', 'correct' => true],
                    ['text' => 'A CLI tool that analyses bundle sizes and generates a performance report', 'correct' => false],
                    ['text' => 'The built-in DevTools panel added to Chrome by Angular CLI during development', 'correct' => false],
                    ['text' => 'A VSCode extension for stepping through Angular component lifecycle hooks in the debugger', 'correct' => false],
                ],
            ],
        ];
    }
}
