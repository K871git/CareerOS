<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AngularLearningSeeder extends Seeder
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
                'description'       => 'Master Angular from components and templates to advanced architecture and the full Angular ecosystem.',
                'display_order'     => 6,
            ]
        );

        // ── Step 1: Assign correct levels to existing practice topics ──────
        Topic::where('slug', 'angular-junior')->update(['level' => 1]);
        Topic::where('slug', 'angular-intermediate')->update(['level' => 2]);
        Topic::where('slug', 'angular-advanced')->update(['level' => 3]);

        // ── Step 2: Create topics for levels 4 and 5 ──────────────────────
        $topic4 = Topic::firstOrCreate(
            ['slug' => 'angular-level-4-patterns'],
            [
                'subject_id'    => $subject->id,
                'title'         => 'Angular Architecture & Reactive Patterns',
                'description'   => 'NgRx state management, interceptors, guards, lazy loading, and Angular performance optimisation.',
                'display_order' => 4,
                'level'         => 4,
            ]
        );
        Topic::where('slug', 'angular-level-4-patterns')->update(['level' => 4]);

        $topic5 = Topic::firstOrCreate(
            ['slug' => 'angular-level-5-expert'],
            [
                'subject_id'    => $subject->id,
                'title'         => 'Expert Angular',
                'description'   => 'Advanced template APIs, custom directives, testing strategies, and Angular build tooling.',
                'display_order' => 5,
                'level'         => 5,
            ]
        );
        Topic::where('slug', 'angular-level-5-expert')->update(['level' => 5]);

        // ── Step 3: Seed lessons for all 5 levels ─────────────────────────
        $this->seedLessons($subject);

        // ── Step 4: Seed exam questions for levels 4 and 5 ────────────────
        $this->seedLevel4Questions($topic4);
        $this->seedLevel5Questions($topic5);

        $this->command->info('Angular Learning seeder complete — all 5 levels populated.');
    }

    // ── LESSONS ─────────────────────────────────────────────────────────────

    private function seedLessons(Subject $subject): void
    {
        $t1 = Topic::where('slug', 'angular-junior')->first();
        $t2 = Topic::where('slug', 'angular-intermediate')->first();
        $t3 = Topic::where('slug', 'angular-advanced')->first();
        $t4 = Topic::where('slug', 'angular-level-4-patterns')->first();
        $t5 = Topic::where('slug', 'angular-level-5-expert')->first();

        $lessons = [
            // ── LEVEL 1 ────────────────────────────────────────────────────
            [
                'topic_id'          => $t1->id,
                'title'             => 'Components, Templates & Data Binding',
                'estimated_minutes' => 18,
                'display_order'     => 1,
                'content'           => <<<'MARKDOWN'
## What is Angular?

Angular is a TypeScript-based web application framework developed by Google. Unlike React (a UI library), Angular is a full framework — it includes a component model, routing, HTTP client, forms, dependency injection, and build tooling out of the box.

Angular applications are built from **components** — the fundamental building blocks.

## Components

A component controls a patch of the screen called a **view**. Every component is a TypeScript class decorated with `@Component`:

```typescript
import { Component } from '@angular/core';

@Component({
  selector: 'app-greeting',
  template: `<h1>Hello, {{ name }}!</h1>`,
})
export class GreetingComponent {
  name = 'Angular';
}
```

Key parts of `@Component`:
- `selector` — the HTML tag used to embed this component: `<app-greeting />`
- `template` / `templateUrl` — the HTML template
- `styles` / `styleUrls` — scoped CSS

## Templates

Angular templates are HTML extended with Angular syntax. Inside a template you can use interpolation, directives, and event binding.

**Interpolation** — `{{ expression }}` renders the result of a TypeScript expression:

```html
<p>Welcome, {{ user.name }}!</p>
<p>Total: {{ items.length }} items</p>
```

## Data Binding

Angular has four types of data binding:

**1. Interpolation** — component → DOM (text content):
```html
<h1>{{ title }}</h1>
```

**2. Property binding** — component → DOM (element property):
```html
<img [src]="imageUrl" [alt]="imageAlt" />
<button [disabled]="isLoading">Save</button>
```
Square brackets bind a DOM property to a component property.

**3. Event binding** — DOM → component:
```html
<button (click)="handleSave()">Save</button>
<input (input)="onSearch($event)" />
```
Round brackets listen to DOM events and call component methods.

**4. Two-way binding** — syncs both ways using `[(ngModel)]`:
```html
<input [(ngModel)]="username" />
<p>You typed: {{ username }}</p>
```
`[(ngModel)]` requires `FormsModule` to be imported.

## Component Lifecycle

Angular calls lifecycle hooks at key moments:

```typescript
import { Component, OnInit, OnDestroy } from '@angular/core';

@Component({ selector: 'app-example', template: '' })
export class ExampleComponent implements OnInit, OnDestroy {
  ngOnInit(): void {
    // Component initialized — start subscriptions, fetch data
    console.log('Component ready');
  }

  ngOnDestroy(): void {
    // Component about to be removed — clean up subscriptions
    console.log('Component destroyed');
  }
}
```

Key hooks in order:
1. `ngOnInit` — runs once after inputs are set (most common hook)
2. `ngOnChanges` — runs when input properties change
3. `ngOnDestroy` — runs before removal

## Input & Output

**`@Input()`** — pass data from parent to child:

```typescript
// child.component.ts
@Component({ selector: 'app-user-card', template: `<p>{{ user.name }}</p>` })
export class UserCardComponent {
  @Input() user!: { name: string; role: string };
}

// parent template
<app-user-card [user]="currentUser" />
```

**`@Output()`** — emit events from child to parent:

```typescript
// child.component.ts
@Component({ selector: 'app-like-button', template: `<button (click)="like()">Like</button>` })
export class LikeButtonComponent {
  @Output() liked = new EventEmitter<void>();

  like() {
    this.liked.emit();
  }
}

// parent template
<app-like-button (liked)="handleLike()" />
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t1->id,
                'title'             => 'Directives: Built-in & Structural Directives',
                'estimated_minutes' => 15,
                'display_order'     => 2,
                'content'           => <<<'MARKDOWN'
## What Are Directives?

Directives are classes that add behaviour to elements in Angular templates. There are three types:

1. **Components** — directives with a template
2. **Structural directives** — change the DOM structure (`*ngIf`, `*ngFor`, `*ngSwitch`)
3. **Attribute directives** — change appearance or behaviour of an element (`[ngClass]`, `[ngStyle]`)

## Structural Directives

**`*ngIf`** — conditionally renders an element:

```html
<p *ngIf="isLoggedIn">Welcome back!</p>
<p *ngIf="!isLoggedIn">Please log in.</p>

<!-- With else template -->
<p *ngIf="isLoggedIn; else loginBlock">Welcome back!</p>
<ng-template #loginBlock>
  <p>Please log in.</p>
</ng-template>
```

**`*ngFor`** — iterates over a list:

```html
<ul>
  <li *ngFor="let item of items; let i = index; trackBy: trackById">
    {{ i + 1 }}. {{ item.name }}
  </li>
</ul>
```

`trackBy` is important for performance — Angular reuses DOM nodes instead of recreating them when the list changes:

```typescript
trackById(index: number, item: { id: number }) {
  return item.id;
}
```

**`*ngSwitch`** — renders one of multiple templates based on a value:

```html
<div [ngSwitch]="status">
  <p *ngSwitchCase="'active'">Active</p>
  <p *ngSwitchCase="'pending'">Pending approval</p>
  <p *ngSwitchDefault>Unknown status</p>
</div>
```

## Attribute Directives

**`[ngClass]`** — dynamically adds/removes CSS classes:

```html
<!-- Object syntax: key = class name, value = condition -->
<div [ngClass]="{ 'active': isActive, 'disabled': isDisabled }">
  Content
</div>

<!-- Array syntax -->
<div [ngClass]="['btn', isPrimary ? 'btn-primary' : 'btn-secondary']">
  Button
</div>
```

**`[ngStyle]`** — dynamically applies inline styles:

```html
<p [ngStyle]="{ color: textColor, 'font-size': fontSize + 'px' }">
  Styled text
</p>
```

## The `@if`, `@for`, `@switch` Control Flow (Angular 17+)

Angular 17 introduced a new built-in control flow syntax that replaces `*ngIf`, `*ngFor`, and `*ngSwitch`:

```html
<!-- @if -->
@if (isLoggedIn) {
  <p>Welcome back!</p>
} @else {
  <p>Please log in.</p>
}

<!-- @for -->
@for (item of items; track item.id) {
  <li>{{ item.name }}</li>
} @empty {
  <li>No items found.</li>
}

<!-- @switch -->
@switch (status) {
  @case ('active') { <p>Active</p> }
  @case ('pending') { <p>Pending</p> }
  @default { <p>Unknown</p> }
}
```

The new syntax is the preferred approach in modern Angular. It is more readable and has better type narrowing.

## Custom Attribute Directive

```typescript
import { Directive, ElementRef, HostListener, Input } from '@angular/core';

@Directive({ selector: '[appHighlight]' })
export class HighlightDirective {
  @Input() appHighlight = 'yellow';

  constructor(private el: ElementRef) {}

  @HostListener('mouseenter')
  onMouseEnter() {
    this.el.nativeElement.style.backgroundColor = this.appHighlight;
  }

  @HostListener('mouseleave')
  onMouseLeave() {
    this.el.nativeElement.style.backgroundColor = '';
  }
}

// Usage
<p appHighlight="lightblue">Hover over me</p>
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t1->id,
                'title'             => 'Angular Modules, Decorators & Project Structure',
                'estimated_minutes' => 15,
                'display_order'     => 3,
                'content'           => <<<'MARKDOWN'
## NgModules

`NgModule` is a container for a cohesive block of Angular code — components, directives, pipes, and services. Every Angular app has at least one root module: `AppModule`.

```typescript
import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { FormsModule } from '@angular/forms';
import { AppComponent } from './app.component';
import { UserCardComponent } from './user-card/user-card.component';

@NgModule({
  declarations: [
    AppComponent,
    UserCardComponent,   // Components/directives/pipes declared here
  ],
  imports: [
    BrowserModule,       // Required for browser apps
    FormsModule,         // Required for ngModel
  ],
  providers: [],         // Services provided at app level
  bootstrap: [AppComponent], // Root component
})
export class AppModule {}
```

`@NgModule` decorator properties:
- `declarations` — components, directives, and pipes belonging to this module
- `imports` — other modules whose exports are needed
- `exports` — components/directives/pipes to make available to other modules
- `providers` — services injectable in this module's scope
- `bootstrap` — root component (app module only)

## Standalone Components (Angular 14+)

Modern Angular moves away from NgModules. **Standalone components** declare their own dependencies directly:

```typescript
import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-search',
  standalone: true,
  imports: [CommonModule, FormsModule], // Import directly — no NgModule needed
  template: `
    <input [(ngModel)]="query" placeholder="Search..." />
    <p *ngIf="query">Searching for: {{ query }}</p>
  `,
})
export class SearchComponent {
  query = '';
}
```

Angular 17+ generates standalone components by default.

## Angular Project Structure

```
src/
├── app/
│   ├── app.component.ts        ← Root component
│   ├── app.module.ts           ← Root module (NgModule apps)
│   ├── app.routes.ts           ← Route definitions
│   ├── features/
│   │   ├── users/
│   │   │   ├── user-list/
│   │   │   │   ├── user-list.component.ts
│   │   │   │   ├── user-list.component.html
│   │   │   │   └── user-list.component.scss
│   │   │   └── user.service.ts
│   │   └── dashboard/
│   └── shared/
│       ├── components/         ← Reusable UI components
│       └── pipes/              ← Reusable pipes
├── assets/
├── environments/
│   ├── environment.ts          ← Development config
│   └── environment.prod.ts     ← Production config
├── main.ts                     ← Entry point
└── index.html
```

## Common Decorators

| Decorator | Purpose |
|---|---|
| `@Component` | Marks a class as a component |
| `@Directive` | Marks a class as a directive |
| `@Pipe` | Marks a class as a pipe |
| `@Injectable` | Marks a class as a service (injectable) |
| `@NgModule` | Marks a class as an Angular module |
| `@Input()` | Declares an input property |
| `@Output()` | Declares an output event emitter |
| `@ViewChild()` | Gets a reference to a child component/element |
| `@HostListener()` | Listens to events on the host element |

## Pipes

Pipes transform data in templates:

```html
<!-- Built-in pipes -->
<p>{{ price | currency:'USD' }}</p>         <!-- $1,234.56 -->
<p>{{ createdAt | date:'mediumDate' }}</p>  <!-- Aug 18, 2026 -->
<p>{{ name | uppercase }}</p>               <!-- ALICE -->
<p>{{ data | json }}</p>                    <!-- pretty JSON -->
<p>{{ items | slice:0:5 }}</p>              <!-- first 5 items -->
```

Custom pipe:

```typescript
import { Pipe, PipeTransform } from '@angular/core';

@Pipe({ name: 'truncate', standalone: true })
export class TruncatePipe implements PipeTransform {
  transform(value: string, limit = 50): string {
    return value.length > limit ? value.slice(0, limit) + '...' : value;
  }
}

// Usage
<p>{{ description | truncate:100 }}</p>
```
MARKDOWN,
            ],

            // ── LEVEL 2 ────────────────────────────────────────────────────
            [
                'topic_id'          => $t2->id,
                'title'             => 'Services & Dependency Injection',
                'estimated_minutes' => 18,
                'display_order'     => 1,
                'content'           => <<<'MARKDOWN'
## What is a Service?

A service is a class with a focused purpose — data fetching, business logic, shared state, or utility functions. Components should be thin: they delegate complex work to services.

```typescript
import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root', // Singleton — one instance for the entire app
})
export class UserService {
  private users: User[] = [];

  getUsers(): User[] {
    return this.users;
  }

  addUser(user: User): void {
    this.users.push(user);
  }
}
```

`providedIn: 'root'` makes the service available app-wide as a singleton. Angular's tree-shaker removes it from the bundle if it is never injected.

## Dependency Injection

**Dependency Injection (DI)** is a design pattern where a class receives its dependencies from an external source rather than creating them itself. Angular has a built-in DI container.

When Angular creates a component, it reads its constructor parameters and resolves the dependencies:

```typescript
import { Component, OnInit } from '@angular/core';
import { UserService } from '../services/user.service';

@Component({
  selector: 'app-user-list',
  template: `
    <ul>
      <li *ngFor="let user of users">{{ user.name }}</li>
    </ul>
  `,
})
export class UserListComponent implements OnInit {
  users: User[] = [];

  // Angular injects UserService automatically
  constructor(private userService: UserService) {}

  ngOnInit(): void {
    this.users = this.userService.getUsers();
  }
}
```

Angular 14+ supports **inject()** function as an alternative to constructor injection:

```typescript
import { inject } from '@angular/core';

export class UserListComponent implements OnInit {
  private userService = inject(UserService);

  ngOnInit(): void {
    this.users = this.userService.getUsers();
  }
}
```

## Injection Scopes

| `providedIn` | Scope |
|---|---|
| `'root'` | App-wide singleton |
| `'any'` | New instance per lazy-loaded module |
| `SomeModule` | Scoped to a specific module |
| `SomeComponent` (via `providers`) | Scoped to a component and its children |

```typescript
// Component-scoped service — each component instance gets its own
@Component({
  selector: 'app-cart',
  providers: [CartService], // scoped to this component subtree
  template: `...`,
})
export class CartComponent {
  constructor(private cart: CartService) {}
}
```

## HTTP Client

The `HttpClient` service (from `HttpClientModule`) handles HTTP requests:

```typescript
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class PostService {
  private apiUrl = 'https://api.example.com/posts';

  constructor(private http: HttpClient) {}

  getPosts(): Observable<Post[]> {
    return this.http.get<Post[]>(this.apiUrl);
  }

  createPost(post: Partial<Post>): Observable<Post> {
    return this.http.post<Post>(this.apiUrl, post);
  }

  deletePost(id: number): Observable<void> {
    return this.http.delete<void>(`${this.apiUrl}/${id}`);
  }
}
```

```typescript
// Using the service in a component
export class PostListComponent implements OnInit {
  posts: Post[] = [];

  constructor(private postService: PostService) {}

  ngOnInit(): void {
    this.postService.getPosts().subscribe({
      next: (posts) => { this.posts = posts; },
      error: (err)  => { console.error(err); },
    });
  }
}
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t2->id,
                'title'             => 'Routing & Navigation',
                'estimated_minutes' => 18,
                'display_order'     => 2,
                'content'           => <<<'MARKDOWN'
## Angular Router

The Angular Router maps URL paths to components. It enables navigation without full page reloads (SPA navigation).

## Setting Up Routes

```typescript
// app.routes.ts
import { Routes } from '@angular/router';
import { HomeComponent } from './features/home/home.component';
import { UserListComponent } from './features/users/user-list.component';
import { UserDetailComponent } from './features/users/user-detail.component';
import { NotFoundComponent } from './shared/not-found.component';

export const routes: Routes = [
  { path: '',         component: HomeComponent },
  { path: 'users',   component: UserListComponent },
  { path: 'users/:id', component: UserDetailComponent },
  { path: '**',      component: NotFoundComponent }, // wildcard — must be last
];
```

```typescript
// main.ts (standalone app)
import { bootstrapApplication } from '@angular/platform-browser';
import { provideRouter } from '@angular/router';
import { AppComponent } from './app/app.component';
import { routes } from './app/app.routes';

bootstrapApplication(AppComponent, {
  providers: [provideRouter(routes)],
});
```

## Router Outlet & Links

`<router-outlet>` is a placeholder where the router renders the matched component:

```html
<!-- app.component.html -->
<nav>
  <a routerLink="/">Home</a>
  <a routerLink="/users">Users</a>
  <a routerLink="/users/42">User 42</a>
</nav>

<router-outlet />
```

`routerLinkActive` adds a CSS class when the link's route is active:

```html
<a routerLink="/users" routerLinkActive="active">Users</a>
```

## Reading Route Parameters

```typescript
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';

@Component({ selector: 'app-user-detail', template: `<p>User ID: {{ userId }}</p>` })
export class UserDetailComponent implements OnInit {
  userId!: number;

  constructor(private route: ActivatedRoute) {}

  ngOnInit(): void {
    // Snapshot — for non-changing params
    this.userId = Number(this.route.snapshot.paramMap.get('id'));

    // Observable — reacts to param changes (same component, different param)
    this.route.paramMap.subscribe(params => {
      this.userId = Number(params.get('id'));
    });
  }
}
```

## Programmatic Navigation

```typescript
import { Router } from '@angular/router';

@Component({ selector: 'app-login', template: `<button (click)="login()">Login</button>` })
export class LoginComponent {
  constructor(private router: Router) {}

  login(): void {
    // ... authenticate
    this.router.navigate(['/dashboard']);
    // With query params:
    this.router.navigate(['/users'], { queryParams: { page: 1, search: 'alice' } });
  }
}
```

## Lazy Loading Routes

Lazy loading loads feature modules only when the user navigates to them — reduces the initial bundle:

```typescript
export const routes: Routes = [
  { path: '', component: HomeComponent },
  {
    path: 'admin',
    loadChildren: () => import('./features/admin/admin.routes').then(m => m.adminRoutes),
  },
  {
    path: 'users',
    loadComponent: () => import('./features/users/user-list.component').then(m => m.UserListComponent),
  },
];
```

`loadComponent` is the standalone-component equivalent — loads a single component lazily.

## Route Guards

Guards control navigation — run logic before a route activates:

```typescript
import { CanActivateFn, Router } from '@angular/router';
import { inject } from '@angular/core';
import { AuthService } from '../services/auth.service';

export const authGuard: CanActivateFn = (route, state) => {
  const auth   = inject(AuthService);
  const router = inject(Router);

  if (auth.isLoggedIn()) {
    return true;
  }
  return router.createUrlTree(['/login']);
};

// Apply to routes:
{ path: 'dashboard', component: DashboardComponent, canActivate: [authGuard] }
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t2->id,
                'title'             => 'Template-Driven & Reactive Forms',
                'estimated_minutes' => 18,
                'display_order'     => 3,
                'content'           => <<<'MARKDOWN'
## Two Approaches to Forms

Angular offers two form models: **Template-Driven** and **Reactive**. Both track form values, validation state, and errors — but they differ in where the logic lives.

## Template-Driven Forms

Template-driven forms put the logic in the HTML template using directives. Angular creates the form model automatically from the template.

```typescript
// app.module.ts or standalone imports
import { FormsModule } from '@angular/forms';
```

```html
<!-- login.component.html -->
<form #loginForm="ngForm" (ngSubmit)="onSubmit(loginForm)">
  <input
    name="email"
    [(ngModel)]="credentials.email"
    required
    email
    #emailField="ngModel"
  />
  <p *ngIf="emailField.invalid && emailField.touched">
    Valid email required.
  </p>

  <input
    type="password"
    name="password"
    [(ngModel)]="credentials.password"
    required
    minlength="8"
    #passField="ngModel"
  />
  <p *ngIf="passField.invalid && passField.touched">
    Min 8 characters required.
  </p>

  <button type="submit" [disabled]="loginForm.invalid">Login</button>
</form>
```

```typescript
export class LoginComponent {
  credentials = { email: '', password: '' };

  onSubmit(form: NgForm): void {
    if (form.valid) {
      console.log(this.credentials);
    }
  }
}
```

**Good for:** Simple forms, quick prototyping. Logic in template makes it harder to unit test.

## Reactive Forms

Reactive forms define the form model in TypeScript. The template binds to the model — no two-way binding.

```typescript
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [ReactiveFormsModule],
  templateUrl: './register.component.html',
})
export class RegisterComponent implements OnInit {
  form!: FormGroup;

  constructor(private fb: FormBuilder) {}

  ngOnInit(): void {
    this.form = this.fb.group({
      name:     ['', [Validators.required, Validators.minLength(2)]],
      email:    ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(8)]],
    });
  }

  get name()  { return this.form.get('name')!; }
  get email() { return this.form.get('email')!; }

  onSubmit(): void {
    if (this.form.valid) {
      console.log(this.form.value);
    }
  }
}
```

```html
<!-- register.component.html -->
<form [formGroup]="form" (ngSubmit)="onSubmit()">
  <input formControlName="name" placeholder="Name" />
  <p *ngIf="name.invalid && name.touched">
    {{ name.hasError('required') ? 'Name is required' : 'Min 2 characters' }}
  </p>

  <input formControlName="email" placeholder="Email" />
  <p *ngIf="email.invalid && email.touched">Valid email required.</p>

  <input type="password" formControlName="password" placeholder="Password" />

  <button type="submit" [disabled]="form.invalid">Register</button>
</form>
```

## Custom Validators

```typescript
import { AbstractControl, ValidationErrors, ValidatorFn } from '@angular/forms';

export function noSpacesValidator(): ValidatorFn {
  return (control: AbstractControl): ValidationErrors | null => {
    const hasSpaces = (control.value as string)?.includes(' ');
    return hasSpaces ? { noSpaces: true } : null;
  };
}

// Usage
this.form = this.fb.group({
  username: ['', [Validators.required, noSpacesValidator()]],
});
```

## Comparison

| | Template-Driven | Reactive |
|---|---|---|
| Logic location | Template | TypeScript class |
| Form model | Auto-created by Angular | Created explicitly |
| Testability | Harder (DOM needed) | Easy (pure TS) |
| Dynamic forms | Limited | Full control |
| Validation | HTML attributes | Validators array |
| Best for | Simple forms | Complex / dynamic forms |
MARKDOWN,
            ],

            // ── LEVEL 3 ────────────────────────────────────────────────────
            [
                'topic_id'          => $t3->id,
                'title'             => 'Change Detection: Default vs OnPush',
                'estimated_minutes' => 18,
                'display_order'     => 1,
                'content'           => <<<'MARKDOWN'
## What is Change Detection?

Angular's change detection (CD) is the mechanism that synchronises the component's data model with its template view. When data changes, Angular re-evaluates template expressions and updates the DOM.

Angular uses Zone.js to intercept async operations (setTimeout, HTTP calls, DOM events) and automatically trigger CD after each one.

## Default Change Detection

By default, Angular checks **every component** in the tree on every change detection cycle — from root down. This is safe but can be wasteful for large trees.

```typescript
@Component({
  selector: 'app-dashboard',
  template: `<p>{{ computeTotal() }}</p>`,
  // changeDetection: ChangeDetectionStrategy.Default  ← implicit
})
export class DashboardComponent {
  computeTotal(): number {
    // This runs on every CD cycle — every click, keypress, HTTP response
    return this.items.reduce((sum, i) => sum + i.price, 0);
  }
}
```

## OnPush Change Detection

`ChangeDetectionStrategy.OnPush` tells Angular to skip a component unless:
1. A reference-type `@Input()` reference changes
2. An event is triggered from within the component
3. An Observable used with `async` pipe emits
4. `markForCheck()` is called manually

```typescript
import { ChangeDetectionStrategy, Component, Input } from '@angular/core';

@Component({
  selector: 'app-user-card',
  changeDetection: ChangeDetectionStrategy.OnPush,
  template: `<p>{{ user.name }}</p>`,
})
export class UserCardComponent {
  @Input() user!: User;
}
```

For `OnPush` to work correctly, inputs must be **immutable** — replace objects/arrays rather than mutating them:

```typescript
// WRONG: mutation — OnPush won't detect this
this.user.name = 'Alice';

// CORRECT: new reference — Angular sees the change
this.user = { ...this.user, name: 'Alice' };
```

## The async Pipe

The `async` pipe subscribes to an Observable or Promise and returns the latest value. When the stream completes, it unsubscribes automatically. It also triggers CD in OnPush components when new values arrive.

```typescript
@Component({
  selector: 'app-posts',
  changeDetection: ChangeDetectionStrategy.OnPush,
  template: `
    <ul>
      <li *ngFor="let post of posts$ | async">{{ post.title }}</li>
    </ul>
  `,
})
export class PostsComponent {
  posts$ = this.postService.getPosts(); // Observable<Post[]>

  constructor(private postService: PostService) {}
}
```

No manual subscription or `ngOnDestroy` needed — `async` handles it.

## Manual Change Detection

When you need fine-grained control:

```typescript
import { ChangeDetectorRef, Component } from '@angular/core';

@Component({
  selector: 'app-manual-cd',
  changeDetection: ChangeDetectionStrategy.OnPush,
  template: `<p>{{ data }}</p>`,
})
export class ManualCdComponent {
  data = 'initial';

  constructor(private cdr: ChangeDetectorRef) {}

  update(): void {
    this.data = 'updated';
    this.cdr.markForCheck(); // Schedule this component for next CD cycle
  }

  detach(): void {
    this.cdr.detach(); // Completely opt out of CD — update manually only
  }

  reattach(): void {
    this.cdr.reattach();
    this.cdr.detectChanges(); // Force synchronous CD
  }
}
```

## Performance Guidelines

1. Use `OnPush` for presentational (dumb) components — they only receive data via `@Input()`
2. Use the `async` pipe instead of subscribing manually
3. Use `trackBy` in `*ngFor` to avoid full list re-renders
4. Avoid calling functions in templates — they execute on every CD cycle
5. Use signals (Angular 16+) for the most granular reactivity
MARKDOWN,
            ],
            [
                'topic_id'          => $t3->id,
                'title'             => 'RxJS in Angular: Observables, Subjects & Operators',
                'estimated_minutes' => 20,
                'display_order'     => 2,
                'content'           => <<<'MARKDOWN'
## RxJS & Angular

Angular is deeply integrated with **RxJS** (Reactive Extensions for JavaScript). The HTTP client, Router, and Forms module all return Observables. Understanding RxJS is essential for Angular development.

## Observable vs Promise

| | Observable | Promise |
|---|---|---|
| Values | Multiple values over time | Single value |
| Lazy | Yes — only runs when subscribed | Eager — runs immediately |
| Cancellable | Yes — unsubscribe | No |
| Operators | Rich transformation API | Limited (then/catch) |

## Core Concepts

```typescript
import { Observable, of, from, interval } from 'rxjs';

// Creating observables
const of$      = of(1, 2, 3);                    // emits 1, 2, 3 then completes
const array$   = from([10, 20, 30]);             // from array/promise/iterable
const timer$   = interval(1000);                 // emits 0, 1, 2... every second

// Subscribing
const sub = timer$.subscribe({
  next: (value) => console.log(value),
  error: (err)  => console.error(err),
  complete: ()  => console.log('Done'),
});

// Always unsubscribe from long-lived observables
sub.unsubscribe();
```

## Essential Operators

**`map`** — transform each value:
```typescript
import { map } from 'rxjs/operators';

this.http.get<ApiResponse>('/api/users').pipe(
  map(response => response.data)   // extract nested data
);
```

**`filter`** — only pass values that match a condition:
```typescript
import { filter } from 'rxjs/operators';

source$.pipe(filter(user => user.isActive));
```

**`switchMap`** — cancel the previous inner observable when a new value arrives (perfect for search):
```typescript
import { switchMap } from 'rxjs/operators';

searchControl.valueChanges.pipe(
  debounceTime(300),
  switchMap(query => this.api.search(query)) // cancels previous HTTP call
).subscribe(results => this.results = results);
```

**`mergeMap`** — run inner observables concurrently (all active at once):
```typescript
// Good for parallel requests where order doesn't matter
ids$.pipe(
  mergeMap(id => this.api.getUser(id))
).subscribe(user => console.log(user));
```

**`concatMap`** — queue inner observables (one at a time, in order):
```typescript
// Good for sequential operations that must not overlap
actions$.pipe(
  concatMap(action => this.api.process(action))
).subscribe();
```

**`catchError`** — handle errors without terminating the stream:
```typescript
import { catchError, EMPTY } from 'rxjs';

this.api.getUsers().pipe(
  catchError(err => {
    console.error(err);
    return EMPTY; // or return of([]) for default value
  })
);
```

**`takeUntil`** — unsubscribe when another observable emits:
```typescript
import { Subject, takeUntil } from 'rxjs';

private destroy$ = new Subject<void>();

ngOnInit(): void {
  this.api.getUsers()
    .pipe(takeUntil(this.destroy$))
    .subscribe(users => this.users = users);
}

ngOnDestroy(): void {
  this.destroy$.next();
  this.destroy$.complete();
}
```

## Subjects

A `Subject` is both an Observable and an Observer — you can manually emit values into it.

```typescript
import { Subject, BehaviorSubject } from 'rxjs';

// Subject — no initial value, only emits to current subscribers
const subject$ = new Subject<string>();
subject$.subscribe(v => console.log('A:', v));
subject$.next('hello'); // A: hello

// BehaviorSubject — has an initial value, new subscribers get the current value immediately
const currentUser$ = new BehaviorSubject<User | null>(null);
currentUser$.next(loggedInUser);
currentUser$.subscribe(user => console.log(user)); // gets current value immediately
```

`BehaviorSubject` is the most common pattern for storing and sharing state in Angular services.

## Service with BehaviorSubject Pattern

```typescript
@Injectable({ providedIn: 'root' })
export class CartService {
  private items$ = new BehaviorSubject<CartItem[]>([]);

  readonly cart$ = this.items$.asObservable(); // expose read-only

  addItem(item: CartItem): void {
    this.items$.next([...this.items$.getValue(), item]);
  }

  removeItem(id: number): void {
    this.items$.next(this.items$.getValue().filter(i => i.id !== id));
  }
}
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t3->id,
                'title'             => 'Angular Signals & Standalone Components',
                'estimated_minutes' => 18,
                'display_order'     => 3,
                'content'           => <<<'MARKDOWN'
## Angular Signals (Angular 16+)

**Signals** are a new reactive primitive in Angular. A signal is a value that Angular tracks — when the signal value changes, Angular knows exactly which templates need to update, without needing Zone.js or full component tree traversal.

```typescript
import { signal, computed, effect } from '@angular/core';

// Create a signal
const count = signal(0);

// Read a signal (call it like a function)
console.log(count()); // 0

// Update a signal
count.set(5);
count.update(prev => prev + 1); // 6

// Computed signal — derived from other signals, recalculates automatically
const doubleCount = computed(() => count() * 2);
console.log(doubleCount()); // 12

// Effect — runs side effects when signals change
effect(() => {
  console.log(`Count changed to: ${count()}`);
});
```

## Signals in Components

```typescript
import { Component, signal, computed } from '@angular/core';

@Component({
  selector: 'app-counter',
  standalone: true,
  template: `
    <p>Count: {{ count() }}</p>
    <p>Double: {{ double() }}</p>
    <button (click)="increment()">+1</button>
    <button (click)="decrement()">-1</button>
  `,
})
export class CounterComponent {
  count  = signal(0);
  double = computed(() => this.count() * 2);

  increment() { this.count.update(n => n + 1); }
  decrement() { this.count.update(n => n - 1); }
}
```

**Benefits of signals over RxJS for local state:**
- No subscription management (no `takeUntil`, no `unsubscribe`)
- Fine-grained: only the exact template expression that reads the signal re-evaluates
- Synchronous — no asynchronous complexity for simple values
- Works without Zone.js (enables future Zone-less Angular)

## Signal-based Inputs & Outputs (Angular 17.1+)

```typescript
import { Component, input, output, model } from '@angular/core';

@Component({
  selector: 'app-user-card',
  standalone: true,
  template: `<p>{{ user().name }}</p>`,
})
export class UserCardComponent {
  user  = input.required<User>();         // required signal input
  liked = output<void>();                 // typed output (replaces EventEmitter)
  name  = model<string>('');             // two-way bindable signal (replaces ngModel)

  sendLike() { this.liked.emit(); }
}
```

## Standalone Components

Standalone components do not need an NgModule:

```typescript
import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { UserCardComponent } from './user-card.component';

@Component({
  selector: 'app-user-list',
  standalone: true,
  imports: [CommonModule, RouterModule, UserCardComponent], // import directly
  template: `
    <app-user-card
      *ngFor="let user of users"
      [user]="user"
    />
  `,
})
export class UserListComponent {
  users: User[] = [];
}
```

## Bootstrapping a Standalone App

```typescript
// main.ts
import { bootstrapApplication } from '@angular/platform-browser';
import { provideRouter } from '@angular/router';
import { provideHttpClient } from '@angular/common/http';
import { AppComponent } from './app/app.component';
import { routes } from './app/app.routes';

bootstrapApplication(AppComponent, {
  providers: [
    provideRouter(routes),
    provideHttpClient(),
  ],
});
```

## Signals vs RxJS

| | Signals | RxJS Observables |
|---|---|---|
| Best for | Synchronous local state | Async streams, HTTP, events |
| Subscription | No subscription needed | Must subscribe and unsubscribe |
| Zone.js | Not needed | Required (or manual) |
| Complexity | Simple | Steep learning curve |
| Template use | Call as function: `count()` | `async` pipe: `count$ \| async` |

Use **signals** for component state and simple derived values. Use **RxJS** for HTTP requests, complex event streams, and async coordination.
MARKDOWN,
            ],

            // ── LEVEL 4 ────────────────────────────────────────────────────
            [
                'topic_id'          => $t4->id,
                'title'             => 'State Management: NgRx & Component Store',
                'estimated_minutes' => 20,
                'display_order'     => 1,
                'content'           => <<<'MARKDOWN'
## Why State Management?

For large Angular applications, managing state across many components becomes complex. A dedicated state management library provides:
- Single source of truth
- Predictable state transitions
- Debuggability (time-travel, action log)
- Separation of state from UI

## NgRx: Redux for Angular

NgRx is the most widely used state management library for Angular. It follows the **Redux pattern**: state is immutable, changes happen only through dispatching actions to a reducer.

**The NgRx data flow:**
```
Component dispatches Action
         ↓
Reducer creates new State
         ↓
Store updates, Selectors emit new values
         ↓
Component template re-renders
         ↓
Effects handle side effects (HTTP) → dispatch new Actions
```

## Core NgRx Concepts

**Actions** — describe what happened:
```typescript
import { createAction, props } from '@ngrx/store';

export const loadUsers   = createAction('[Users] Load');
export const loadSuccess = createAction('[Users] Load Success', props<{ users: User[] }>());
export const loadFailure = createAction('[Users] Load Failure', props<{ error: string }>());
```

**Reducer** — pure function that computes the next state:
```typescript
import { createReducer, on } from '@ngrx/store';

export interface UsersState {
  users: User[];
  loading: boolean;
  error: string | null;
}

const initialState: UsersState = { users: [], loading: false, error: null };

export const usersReducer = createReducer(
  initialState,
  on(loadUsers,   state => ({ ...state, loading: true, error: null })),
  on(loadSuccess, (state, { users }) => ({ ...state, loading: false, users })),
  on(loadFailure, (state, { error }) => ({ ...state, loading: false, error })),
);
```

**Selectors** — derive data from the store:
```typescript
import { createFeatureSelector, createSelector } from '@ngrx/store';

const selectUsersState = createFeatureSelector<UsersState>('users');

export const selectAllUsers   = createSelector(selectUsersState, s => s.users);
export const selectLoading    = createSelector(selectUsersState, s => s.loading);
export const selectActiveUsers = createSelector(
  selectAllUsers,
  users => users.filter(u => u.isActive)
);
```

**Effects** — handle side effects (async operations):
```typescript
import { Injectable } from '@angular/core';
import { Actions, createEffect, ofType } from '@ngrx/effects';
import { switchMap, map, catchError, of } from 'rxjs';

@Injectable()
export class UsersEffects {
  loadUsers$ = createEffect(() =>
    this.actions$.pipe(
      ofType(loadUsers),
      switchMap(() =>
        this.userService.getUsers().pipe(
          map(users => loadSuccess({ users })),
          catchError(err => of(loadFailure({ error: err.message })))
        )
      )
    )
  );

  constructor(private actions$: Actions, private userService: UserService) {}
}
```

**Using the Store in a Component:**
```typescript
@Component({ selector: 'app-user-list', template: `
  <ng-container *ngIf="!(loading$ | async); else spinner">
    <li *ngFor="let user of users$ | async">{{ user.name }}</li>
  </ng-container>
`})
export class UserListComponent implements OnInit {
  users$   = this.store.select(selectAllUsers);
  loading$ = this.store.select(selectLoading);

  constructor(private store: Store) {}

  ngOnInit(): void {
    this.store.dispatch(loadUsers());
  }
}
```

## NgRx Component Store

For local (feature-scoped) state, NgRx Component Store is lighter than the global store:

```typescript
import { ComponentStore } from '@ngrx/component-store';

interface SearchState {
  query: string;
  results: Product[];
  loading: boolean;
}

@Injectable()
export class SearchStore extends ComponentStore<SearchState> {
  constructor(private api: ProductService) {
    super({ query: '', results: [], loading: false });
  }

  // Selectors
  readonly results$ = this.select(s => s.results);
  readonly loading$ = this.select(s => s.loading);

  // Updaters (synchronous state changes)
  readonly setQuery = this.updater((state, query: string) => ({ ...state, query }));

  // Effects (async operations)
  readonly search = this.effect((query$: Observable<string>) =>
    query$.pipe(
      debounceTime(300),
      switchMap(query => this.api.search(query).pipe(
        tap(results => this.patchState({ results, loading: false })),
        catchError(() => EMPTY),
      ))
    )
  );
}
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t4->id,
                'title'             => 'Interceptors, Guards & Error Handling',
                'estimated_minutes' => 18,
                'display_order'     => 2,
                'content'           => <<<'MARKDOWN'
## HTTP Interceptors

Interceptors intercept HTTP requests and responses — ideal for adding auth headers, logging, and handling errors globally.

**Functional interceptor (Angular 15+):**

```typescript
import { HttpInterceptorFn, HttpRequest, HttpHandlerFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { catchError, throwError } from 'rxjs';
import { AuthService } from '../services/auth.service';

export const authInterceptor: HttpInterceptorFn = (
  req: HttpRequest<unknown>,
  next: HttpHandlerFn
) => {
  const auth  = inject(AuthService);
  const token = auth.getToken();

  if (token) {
    req = req.clone({
      setHeaders: { Authorization: `Bearer ${token}` },
    });
  }

  return next(req);
};

export const errorInterceptor: HttpInterceptorFn = (req, next) => {
  return next(req).pipe(
    catchError(error => {
      if (error.status === 401) {
        inject(AuthService).logout();
      }
      return throwError(() => error);
    })
  );
};
```

Register interceptors in `main.ts`:
```typescript
bootstrapApplication(AppComponent, {
  providers: [
    provideHttpClient(
      withInterceptors([authInterceptor, errorInterceptor])
    ),
  ],
});
```

## Route Guards

**`canActivate`** — prevent unauthorised navigation:
```typescript
export const authGuard: CanActivateFn = () => {
  const auth   = inject(AuthService);
  const router = inject(Router);
  return auth.isLoggedIn() ? true : router.createUrlTree(['/login']);
};
```

**`canActivateChild`** — protect child routes:
```typescript
{ path: 'admin', canActivateChild: [adminGuard], children: [...] }
```

**`canDeactivate`** — prevent accidental navigation away from unsaved forms:
```typescript
export const unsavedChangesGuard: CanDeactivateFn<EditFormComponent> = (component) => {
  if (component.hasUnsavedChanges()) {
    return confirm('You have unsaved changes. Leave anyway?');
  }
  return true;
};
```

**`resolve`** — pre-fetch data before the route activates:
```typescript
export const userResolver: ResolveFn<User> = (route) => {
  const id = Number(route.paramMap.get('id'));
  return inject(UserService).getUser(id);
};

// Route
{ path: 'users/:id', component: UserDetailComponent, resolve: { user: userResolver } }

// Component
ngOnInit(): void {
  this.user = this.route.snapshot.data['user'];
}
```

## Global Error Handling

**`ErrorHandler`** — catches uncaught errors app-wide:

```typescript
import { ErrorHandler, Injectable } from '@angular/core';

@Injectable()
export class GlobalErrorHandler implements ErrorHandler {
  handleError(error: unknown): void {
    console.error('Unhandled error:', error);
    // Send to logging service (Sentry, etc.)
  }
}

// Register in providers
{ provide: ErrorHandler, useClass: GlobalErrorHandler }
```

**HTTP error handling in services:**

```typescript
import { catchError, throwError } from 'rxjs';
import { HttpErrorResponse } from '@angular/common/http';

getUser(id: number): Observable<User> {
  return this.http.get<User>(`/api/users/${id}`).pipe(
    catchError((error: HttpErrorResponse) => {
      let message = 'An unexpected error occurred';
      if (error.status === 404) message = 'User not found';
      if (error.status === 403) message = 'Access denied';
      return throwError(() => new Error(message));
    })
  );
}
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t4->id,
                'title'             => 'Angular Performance: Lazy Loading, Preloading & SSR',
                'estimated_minutes' => 18,
                'display_order'     => 3,
                'content'           => <<<'MARKDOWN'
## Lazy Loading Routes

Lazy loading splits the application into feature chunks — users only download the JavaScript for the features they visit.

```typescript
// app.routes.ts
export const routes: Routes = [
  { path: '', component: HomeComponent },
  {
    path: 'dashboard',
    loadChildren: () => import('./features/dashboard/dashboard.routes')
      .then(m => m.dashboardRoutes),
  },
  {
    path: 'admin',
    loadComponent: () => import('./features/admin/admin.component')
      .then(m => m.AdminComponent),
    canActivate: [adminGuard],
  },
];
```

Vite/webpack automatically creates a separate JavaScript chunk for each lazy route.

## Preloading Strategies

By default, lazy routes are only loaded on first navigation. **Preloading** downloads them in the background after the initial load:

```typescript
import { PreloadAllModules, provideRouter, withPreloading } from '@angular/router';

bootstrapApplication(AppComponent, {
  providers: [
    provideRouter(routes, withPreloading(PreloadAllModules)),
  ],
});
```

**Custom preloading strategy** — preload only flagged routes:

```typescript
import { Injectable } from '@angular/core';
import { PreloadingStrategy, Route } from '@angular/router';
import { Observable, of } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class SelectivePreloadingStrategy implements PreloadingStrategy {
  preload(route: Route, load: () => Observable<unknown>): Observable<unknown> {
    return route.data?.['preload'] ? load() : of(null);
  }
}

// Mark routes to preload
{ path: 'users', data: { preload: true }, loadChildren: () => import('...') }
```

## Angular SSR (Universal)

Server-Side Rendering renders the Angular app on the server and sends pre-rendered HTML to the client. Benefits: improved Time-to-First-Paint and better SEO.

```bash
ng add @angular/ssr
```

SSR considerations:
- No browser APIs (`window`, `document`, `localStorage`) are available on the server
- Use `isPlatformBrowser` to conditionally run browser-only code:

```typescript
import { Inject, PLATFORM_ID } from '@angular/core';
import { isPlatformBrowser } from '@angular/common';

@Injectable({ providedIn: 'root' })
export class StorageService {
  constructor(@Inject(PLATFORM_ID) private platformId: object) {}

  get(key: string): string | null {
    if (isPlatformBrowser(this.platformId)) {
      return localStorage.getItem(key);
    }
    return null;
  }
}
```

## OnPush + Signals Performance

The highest-performance Angular architecture combines:
- `ChangeDetectionStrategy.OnPush` on all components
- Signals for local state (bypasses Zone.js entirely)
- `async` pipe for Observable streams
- `trackBy` on all `*ngFor` loops

```typescript
@Component({
  selector: 'app-product-list',
  changeDetection: ChangeDetectionStrategy.OnPush,
  template: `
    @for (product of products(); track product.id) {
      <app-product-card [product]="product" />
    }
  `,
})
export class ProductListComponent {
  products = signal<Product[]>([]);
}
```

## Bundle Analysis

Analyse your build output to find oversized chunks:

```bash
ng build --stats-json
npx webpack-bundle-analyzer dist/app/browser/stats.json
```

Common optimisations:
- Lazy load heavy libraries (chart.js, PDF viewers) on demand
- Use `import()` for one-off operations
- Tree-shake unused icon sets and third-party modules
MARKDOWN,
            ],

            // ── LEVEL 5 ────────────────────────────────────────────────────
            [
                'topic_id'          => $t5->id,
                'title'             => 'Custom Structural Directives & Advanced Template APIs',
                'estimated_minutes' => 20,
                'display_order'     => 1,
                'content'           => <<<'MARKDOWN'
## Custom Structural Directives

Structural directives manipulate the DOM layout — adding, removing, or replacing elements. The asterisk (`*`) is syntactic sugar that Angular expands to `<ng-template>`.

**Expanding `*ngIf`:**
```html
<!-- Shorthand -->
<p *ngIf="show">Hello</p>

<!-- Expanded by Angular -->
<ng-template [ngIf]="show">
  <p>Hello</p>
</ng-template>
```

**Building a custom `*appRepeat` directive:**

```typescript
import {
  Directive,
  Input,
  TemplateRef,
  ViewContainerRef,
  OnChanges,
} from '@angular/core';

@Directive({ selector: '[appRepeat]', standalone: true })
export class RepeatDirective implements OnChanges {
  @Input() appRepeat = 3;

  constructor(
    private templateRef: TemplateRef<{ $implicit: number }>,
    private viewContainer: ViewContainerRef,
  ) {}

  ngOnChanges(): void {
    this.viewContainer.clear();
    for (let i = 0; i < this.appRepeat; i++) {
      this.viewContainer.createEmbeddedView(this.templateRef, {
        $implicit: i, // expose index as implicit variable
      });
    }
  }
}

// Usage
<p *appRepeat="5; let i">Item {{ i }}</p>
```

## Advanced Template APIs

**`ViewChild` & `ContentChild`:**

```typescript
import { Component, ViewChild, ContentChild, ElementRef, AfterViewInit } from '@angular/core';

@Component({
  selector: 'app-panel',
  template: `
    <div #container>
      <ng-content /> <!-- projects content from parent -->
    </div>
  `,
})
export class PanelComponent implements AfterViewInit {
  @ViewChild('container') container!: ElementRef<HTMLDivElement>;
  @ContentChild('header') header!: ElementRef;

  ngAfterViewInit(): void {
    // DOM is ready here — not in ngOnInit
    console.log(this.container.nativeElement.offsetHeight);
  }
}
```

**`ng-content` with select** — named content projection slots:

```typescript
@Component({
  selector: 'app-card',
  template: `
    <div class="card">
      <div class="card-header">
        <ng-content select="[slot=header]" />
      </div>
      <div class="card-body">
        <ng-content />  <!-- default slot -->
      </div>
      <div class="card-footer">
        <ng-content select="[slot=footer]" />
      </div>
    </div>
  `,
})
export class CardComponent {}

// Usage
<app-card>
  <h2 slot="header">Card Title</h2>
  <p>Card content</p>
  <button slot="footer">Action</button>
</app-card>
```

## Dynamic Component Creation

```typescript
import { ViewContainerRef, ComponentRef } from '@angular/core';

@Component({ selector: 'app-host', template: `<div #host></div>` })
export class HostComponent {
  @ViewChild('host', { read: ViewContainerRef }) host!: ViewContainerRef;

  loadToast(message: string): void {
    this.host.clear();
    const ref: ComponentRef<ToastComponent> = this.host.createComponent(ToastComponent);
    ref.instance.message = message;
    ref.instance.closed.subscribe(() => ref.destroy());
  }
}
```

## HostBinding & HostListener

```typescript
import { Directive, HostBinding, HostListener, Input } from '@angular/core';

@Directive({ selector: '[appButton]', standalone: true })
export class ButtonDirective {
  @Input() variant: 'primary' | 'danger' = 'primary';

  @HostBinding('class.btn-primary') get isPrimary() { return this.variant === 'primary'; }
  @HostBinding('class.btn-danger')  get isDanger()  { return this.variant === 'danger';  }
  @HostBinding('attr.role') role = 'button';

  @HostListener('keydown.enter', ['$event'])
  onEnter(event: KeyboardEvent): void {
    (event.target as HTMLElement).click();
  }
}
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t5->id,
                'title'             => 'Testing Angular Applications: Unit & Integration',
                'estimated_minutes' => 20,
                'display_order'     => 2,
                'content'           => <<<'MARKDOWN'
## Angular Testing Stack

Angular ships with a built-in testing setup:
- **Jasmine** — test framework (describe/it/expect)
- **Karma** — test runner (browser-based)
- **Angular TestBed** — testing module that creates a mini Angular environment

Modern projects often swap Karma for **Jest** (faster, no browser needed).

## Testing Services

Services are plain TypeScript classes — test them without Angular overhead:

```typescript
// user.service.spec.ts
import { TestBed } from '@angular/core/testing';
import { HttpClientTestingModule, HttpTestingController } from '@angular/common/http/testing';
import { UserService } from './user.service';

describe('UserService', () => {
  let service: UserService;
  let http: HttpTestingController;

  beforeEach(() => {
    TestBed.configureTestingModule({
      imports: [HttpClientTestingModule],
      providers: [UserService],
    });
    service = TestBed.inject(UserService);
    http    = TestBed.inject(HttpTestingController);
  });

  afterEach(() => http.verify()); // assert no unexpected requests

  it('should fetch users', () => {
    const mockUsers = [{ id: 1, name: 'Alice' }];
    let result: User[] = [];

    service.getUsers().subscribe(users => result = users);

    const req = http.expectOne('/api/users');
    expect(req.request.method).toBe('GET');
    req.flush(mockUsers);

    expect(result).toEqual(mockUsers);
  });
});
```

## Testing Components with TestBed

```typescript
// counter.component.spec.ts
import { ComponentFixture, TestBed } from '@angular/core/testing';
import { CounterComponent } from './counter.component';

describe('CounterComponent', () => {
  let fixture: ComponentFixture<CounterComponent>;
  let component: CounterComponent;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CounterComponent], // standalone — import directly
    }).compileComponents();

    fixture   = TestBed.createComponent(CounterComponent);
    component = fixture.componentInstance;
    fixture.detectChanges(); // trigger ngOnInit + first render
  });

  it('should start at zero', () => {
    const p = fixture.nativeElement.querySelector('p');
    expect(p.textContent).toContain('0');
  });

  it('should increment on button click', () => {
    const button = fixture.nativeElement.querySelector('button');
    button.click();
    fixture.detectChanges(); // re-render

    const p = fixture.nativeElement.querySelector('p');
    expect(p.textContent).toContain('1');
  });

  it('should expose count via component', () => {
    component.increment();
    expect(component.count()).toBe(1); // testing signal
  });
});
```

## Mocking Services

```typescript
const mockUserService = {
  getUsers: jasmine.createSpy('getUsers').and.returnValue(of([{ id: 1, name: 'Alice' }])),
};

await TestBed.configureTestingModule({
  imports: [UserListComponent],
  providers: [{ provide: UserService, useValue: mockUserService }],
}).compileComponents();
```

## Testing Router Navigation

```typescript
import { RouterTestingModule } from '@angular/router/testing';
import { Router } from '@angular/router';
import { Location } from '@angular/common';

await TestBed.configureTestingModule({
  imports: [
    RouterTestingModule.withRoutes([
      { path: 'home',    component: HomeComponent },
      { path: 'profile', component: ProfileComponent },
    ]),
  ],
}).compileComponents();

const router   = TestBed.inject(Router);
const location = TestBed.inject(Location);

await router.navigate(['/profile']);
expect(location.path()).toBe('/profile');
```

## Testing with Spectator (Third-Party)

Spectator reduces TestBed boilerplate significantly:

```typescript
import { createComponentFactory, Spectator } from '@ngneat/spectator';
import { CounterComponent } from './counter.component';

describe('CounterComponent', () => {
  let spectator: Spectator<CounterComponent>;
  const createComponent = createComponentFactory(CounterComponent);

  beforeEach(() => spectator = createComponent());

  it('increments on click', () => {
    spectator.click('button.increment');
    expect(spectator.query('p')).toHaveText('1');
  });
});
```

## Testing Best Practices

- Test **behaviour**, not implementation — what the user sees, not internal state
- Prefer **integration tests** (component + template + real service) over isolated unit tests
- Mock only external boundaries (HTTP, third-party APIs) — not internal Angular services
- Use `fixture.detectChanges()` after every action that should trigger re-render
- Always `verify()` `HttpTestingController` in `afterEach` to catch unexpected requests
MARKDOWN,
            ],
            [
                'topic_id'          => $t5->id,
                'title'             => 'Angular Build System: Esbuild, Vite & Deployment',
                'estimated_minutes' => 18,
                'display_order'     => 3,
                'content'           => <<<'MARKDOWN'
## Angular's Build System Evolution

Angular historically used Webpack for building. Angular 16+ introduced the **esbuild** builder, and Angular 17 made it the default. Esbuild is 10–100× faster than Webpack for large projects.

```json
// angular.json — builder selection
{
  "architect": {
    "build": {
      "builder": "@angular-devkit/build-angular:application", // esbuild (default, Angular 17+)
      // OR: "@angular-devkit/build-angular:browser" for legacy webpack
    }
  }
}
```

## Key Build Commands

```bash
# Development server with HMR
ng serve

# Production build — minified, tree-shaken, fingerprinted assets
ng build

# Build with source maps (for debugging production issues)
ng build --source-map

# Analyse bundle size
ng build --stats-json
npx webpack-bundle-analyzer dist/my-app/browser/stats.json

# Run tests
ng test             # Karma/Jasmine
jest                # Jest (if configured)

# Run E2E tests
ng e2e              # Playwright or Cypress
```

## Environment Configuration

```typescript
// src/environments/environment.ts (development)
export const environment = {
  production: false,
  apiUrl: 'http://localhost:3000/api',
};

// src/environments/environment.prod.ts (production)
export const environment = {
  production: true,
  apiUrl: 'https://api.example.com',
};

// Usage in service
import { environment } from '../environments/environment';

@Injectable({ providedIn: 'root' })
export class ApiService {
  private base = environment.apiUrl; // Angular swaps the file at build time
}
```

## Angular Application Config (`angular.json`)

Key build options:

```json
{
  "configurations": {
    "production": {
      "optimization": true,           // minify, tree-shake
      "outputHashing": "all",         // fingerprint file names
      "sourceMap": false,
      "namedChunks": false,
      "budgets": [
        {
          "type": "initial",
          "maximumWarning": "500kb",
          "maximumError": "1mb"
        }
      ]
    }
  }
}
```

**Build budgets** fail the build if a chunk exceeds the limit — preventing accidental bundle bloat.

## Deployment

**Static hosting (GitHub Pages, Netlify, Vercel):**

```bash
ng build
# Upload dist/my-app/browser/ to your host
```

Configure the host to redirect all routes to `index.html` (SPA routing):

```nginx
# nginx.conf
location / {
  try_files $uri $uri/ /index.html;
}
```

**Docker:**

```dockerfile
# Stage 1: Build
FROM node:20 AS builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: Serve
FROM nginx:alpine
COPY --from=builder /app/dist/my-app/browser /usr/share/nginx/html
COPY nginx.conf /etc/nginx/conf.d/default.conf
EXPOSE 80
```

## Workspace & Libraries

Angular CLI supports monorepos with **libraries**:

```bash
# Create a shared UI library
ng generate library shared-ui

# Build it before using
ng build shared-ui

# Use in apps
import { ButtonComponent } from 'shared-ui';
```

## ng-packagr & Publishing

Libraries are packaged with **ng-packagr** (automatic in Angular CLI). Publishing to npm:

```bash
ng build my-lib --configuration production
cd dist/my-lib
npm publish
```
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

        $this->command->info('Lessons seeded for all 5 Angular levels.');
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
        $this->command->info("Angular Level 4: {$count} questions total.");
    }

    private function level4Questions(): array
    {
        return [
            [
                'question'    => 'What is NgRx and what problem does it solve in Angular applications?',
                'explanation' => 'NgRx is a state management library for Angular based on the Redux pattern. It solves the problem of managing shared, complex state across many components in large applications. Without NgRx, state is scattered across services and components — difficult to trace, debug, and reason about. NgRx provides a single store (single source of truth), immutable state transitions via reducers, a declarative action-based API, and Redux DevTools integration with time-travel debugging. The main trade-off is significant boilerplate for smaller apps.',
                'options' => [
                    ['text' => 'Redux-pattern state management for Angular — single store, immutable state via reducers, action log for debugging', 'correct' => true],
                    ['text' => 'An Angular module for managing NgModules and lazy loading', 'correct' => false],
                    ['text' => 'A replacement for Angular services — all business logic must move to NgRx', 'correct' => false],
                    ['text' => 'A reactive form library that extends Angular\'s built-in forms', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of NgRx Effects and when would you use them?',
                'explanation' => 'NgRx Effects handle side effects — operations that interact with the outside world: HTTP requests, localStorage, timers, or WebSocket connections. In the Redux pattern, reducers must be pure functions (no side effects). Effects listen to the action stream, perform async operations, and dispatch new actions (success or failure). Without Effects, you would call APIs directly in components and manually dispatch actions — violating separation of concerns and making the flow harder to test.',
                'options' => [
                    ['text' => 'Handle async side effects (HTTP, storage) — listen to actions, perform operations, dispatch result actions', 'correct' => true],
                    ['text' => 'Apply visual effects and animations to components when state changes', 'correct' => false],
                    ['text' => 'Intercept actions before they reach the reducer and transform them', 'correct' => false],
                    ['text' => 'Replace Angular services — all business logic moves to Effects', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How does an Angular HTTP Interceptor work and what are common use cases?',
                'explanation' => 'An HTTP interceptor sits in the pipeline between an Angular service and the server. It intercepts outgoing requests and/or incoming responses. The interceptor calls `next(req)` to pass the (potentially modified) request along. Common use cases: adding Authorization headers to every request, logging request/response metadata, showing a global loading indicator, handling 401/403 errors centrally by redirecting to login, retry logic on failure, and transforming response data into a standard format.',
                'options' => [
                    ['text' => 'Sits in the HTTP pipeline — intercepts requests/responses to add auth headers, log, or handle errors centrally', 'correct' => true],
                    ['text' => 'Intercepts router navigation and redirects unauthorised routes', 'correct' => false],
                    ['text' => 'A middleware that runs between the component and the service', 'correct' => false],
                    ['text' => 'Caches HTTP responses to avoid repeated network calls', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between canActivate and resolve route guards?',
                'explanation' => 'canActivate determines whether a route can be activated — it returns true/false or a UrlTree (redirect). If it returns false, navigation stops. resolve pre-fetches data before the route component activates — the router waits for the resolver to complete and injects the result into the route data. canActivate is for access control; resolve is for data loading. The key difference: canActivate can block navigation entirely; resolve always activates the route (once the data resolves), just with pre-loaded data.',
                'options' => [
                    ['text' => 'canActivate controls whether navigation proceeds (access control); resolve pre-loads data before the component activates', 'correct' => true],
                    ['text' => 'canActivate runs after the component loads; resolve runs before', 'correct' => false],
                    ['text' => 'They are interchangeable — both block navigation if they return false', 'correct' => false],
                    ['text' => 'canActivate is for lazy routes; resolve is for eagerly loaded routes', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is lazy loading in Angular and what performance benefit does it provide?',
                'explanation' => 'Lazy loading splits the Angular application into feature chunks using dynamic import(). When a user visits a route, Angular downloads only the JavaScript for that route — not the entire app bundle. This reduces the initial bundle size (Time-to-Interactive improves) and the amount of JavaScript the browser must parse and execute before the app becomes usable. Lazy loading is configured with loadChildren (module-based) or loadComponent (standalone). Preloading strategies can download lazy chunks in the background after the initial page load.',
                'options' => [
                    ['text' => 'Downloads feature code only when visited — reduces initial bundle, improves Time-to-Interactive', 'correct' => true],
                    ['text' => 'Delays rendering of components until they scroll into view', 'correct' => false],
                    ['text' => 'Loads components from a CDN rather than the same server', 'correct' => false],
                    ['text' => 'Defers HTTP requests until after the initial render completes', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is Angular\'s GlobalErrorHandler and when should you implement one?',
                'explanation' => 'Angular\'s ErrorHandler is a service with a single handleError(error) method. By default it logs errors to the console. You override it to send uncaught errors to a monitoring service (Sentry, Datadog), show a user-friendly toast/dialog, or redirect to an error page. It catches errors that escape component error boundaries — JavaScript runtime errors, unhandled promise rejections (in Angular\'s zone), and errors thrown in lifecycle hooks. You register it with { provide: ErrorHandler, useClass: GlobalErrorHandler } in your providers.',
                'options' => [
                    ['text' => 'Catches uncaught errors app-wide — override to report to monitoring services and show user-friendly messages', 'correct' => true],
                    ['text' => 'Handles HTTP errors only — for form validation errors use formGroup.setErrors()', 'correct' => false],
                    ['text' => 'Replaces try/catch in all services automatically', 'correct' => false],
                    ['text' => 'A directive that wraps components in error boundaries similar to React', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are NgRx Selectors and why should you memoize them?',
                'explanation' => 'NgRx selectors are pure functions that derive data from the store state. createSelector() returns a memoized selector — it only recomputes when its input selectors return different values (by reference). This is important because Angular\'s OnPush change detection combined with the store means components re-render whenever their selector emits a new value. Without memoization, selectors would recompute on every action (even unrelated ones), potentially returning new object references that trigger unnecessary component re-renders.',
                'options' => [
                    ['text' => 'Pure functions that derive store data — memoized to avoid recomputing on every action and prevent unnecessary re-renders', 'correct' => true],
                    ['text' => 'Functions that select which components receive store updates', 'correct' => false],
                    ['text' => 'CSS selectors used to bind store state to component templates', 'correct' => false],
                    ['text' => 'They are not memoized — they recompute on every store change by design', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the Angular Component Store and how does it differ from the global NgRx Store?',
                'explanation' => 'NgRx Component Store is scoped to a single component or feature — not the global app store. It lives and dies with the component it is provided in. This makes it ideal for local/feature state that does not need to be shared app-wide. The Component Store API (select, updater, effect) mirrors NgRx but with much less boilerplate — no actions, no action creators, no feature selectors. Choose Component Store for self-contained feature state; choose the global Store for state shared across multiple unrelated features.',
                'options' => [
                    ['text' => 'Component Store is scoped to a feature/component — less boilerplate, no actions needed; global Store is app-wide with full Redux pattern', 'correct' => true],
                    ['text' => 'Component Store is for synchronous state; global Store handles async operations', 'correct' => false],
                    ['text' => 'They are identical — Component Store is just a renamed global Store for smaller apps', 'correct' => false],
                    ['text' => 'Component Store replaces services entirely; global Store is for HTTP state only', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the canDeactivate route guard and when would you use it?',
                'explanation' => 'canDeactivate runs before leaving a route — it can return false to prevent navigation. The primary use case is warning users about unsaved changes: if a user has edited a form and tries to navigate away, canDeactivate can show a confirm dialog and abort navigation if they cancel. It takes the current component instance as a parameter, so the component can expose a method like hasUnsavedChanges() that the guard calls. This prevents data loss without requiring every link to check form state manually.',
                'options' => [
                    ['text' => 'Prevents navigation away from a route — used to warn users about unsaved form changes', 'correct' => true],
                    ['text' => 'Deactivates a route so it can no longer be navigated to', 'correct' => false],
                    ['text' => 'Runs cleanup logic (like unsubscribing) when a component is destroyed', 'correct' => false],
                    ['text' => 'Guards child routes from being activated when the parent route deactivates', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is preloading in Angular routing and how does it improve performance?',
                'explanation' => 'Preloading downloads lazy-loaded route chunks in the background after the initial app load, but before the user navigates to them. The result: the initial bundle stays small (fast first load), and subsequent navigations feel instant because the chunk is already downloaded. PreloadAllModules preloads all lazy routes. Custom preloading strategies (like SelectivePreloadingStrategy) preload only routes flagged with route.data.preload = true — a compromise between downloading everything and downloading nothing.',
                'options' => [
                    ['text' => 'Downloads lazy route chunks in the background after initial load — fast first load AND instant subsequent navigation', 'correct' => true],
                    ['text' => 'Pre-renders components on the server before the client requests them', 'correct' => false],
                    ['text' => 'Pre-fetches API data for lazy routes so they display instantly on first visit', 'correct' => false],
                    ['text' => 'Loads all routes eagerly — opposite of lazy loading', 'correct' => false],
                ],
            ],
            [
                'question'    => 'In Angular SSR, what is hydration and why is it needed?',
                'explanation' => 'Hydration is the process of attaching Angular\'s event listeners and state to the server-rendered HTML already in the browser. Without hydration, Angular would destroy the server-rendered DOM and rebuild it entirely on the client — causing a flash of blank content and wasting the server-render work. With hydration (Angular 16+), Angular reuses the existing DOM nodes and attaches interactivity without re-creating elements. This makes SSR truly beneficial — users see content immediately from the server HTML, and Angular picks up seamlessly.',
                'options' => [
                    ['text' => 'Attaches Angular event listeners to server-rendered HTML — avoids destroying and rebuilding the DOM on the client', 'correct' => true],
                    ['text' => 'Re-renders the component tree on the client after the server sends HTML', 'correct' => false],
                    ['text' => 'Sends pre-fetched API data alongside the HTML to avoid client-side HTTP calls', 'correct' => false],
                    ['text' => 'A technique to cache SSR output in a CDN for faster delivery', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a build budget in Angular and why is it useful?',
                'explanation' => 'Build budgets in angular.json define size thresholds for output bundles. If a bundle exceeds the warning threshold, Angular prints a warning; if it exceeds the error threshold, the build fails. This prevents bundle bloat from sneaking in undetected — a common problem when adding dependencies without checking their size impact. Budgets cover the initial bundle, lazy chunks, and the overall application. They are a CI gate: the build breaks before a large dependency makes it to production.',
                'options' => [
                    ['text' => 'Size thresholds for output bundles — warns/fails the build if exceeded, preventing undetected bundle bloat', 'correct' => true],
                    ['text' => 'A financial budget tracker for estimating Angular project costs', 'correct' => false],
                    ['text' => 'Limits on the number of HTTP requests a component can make during testing', 'correct' => false],
                    ['text' => 'Memory limits for Angular\'s change detection algorithm', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is switchMap in RxJS and why is it the correct operator for search-as-you-type?',
                'explanation' => 'switchMap maps each source value to an inner Observable and automatically cancels the previous inner Observable when a new source value arrives. For search-as-you-type, each keystroke triggers a new HTTP request. Without switchMap, all in-flight requests complete and emit — results could arrive out of order (the response for "ap" could arrive after "apple"). switchMap cancels the previous request (via unsubscription) whenever a new keystroke arrives, ensuring only the latest search result is used.',
                'options' => [
                    ['text' => 'Cancels the previous inner Observable on each new emission — prevents out-of-order results from stale search requests', 'correct' => true],
                    ['text' => 'Switches between multiple Observable sources round-robin style', 'correct' => false],
                    ['text' => 'Maps values synchronously without creating inner Observables', 'correct' => false],
                    ['text' => 'Merges all inner Observables — all concurrent requests run to completion', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the BehaviorSubject pattern in Angular services and why is it preferred over plain arrays?',
                'explanation' => 'BehaviorSubject is an RxJS Subject with an initial value that immediately emits the current value to new subscribers. In Angular services, it is used to store and share mutable state (like a shopping cart, auth user, or notification list). Unlike a plain array, BehaviorSubject: (1) immediately gives new subscribers the current value, (2) automatically notifies all subscribers when updated, (3) works with the async pipe and OnPush change detection. Expose it as asObservable() to prevent external code from calling .next() directly.',
                'options' => [
                    ['text' => 'Holds current state and emits it to new subscribers immediately — works with async pipe and OnPush; encapsulated via asObservable()', 'correct' => true],
                    ['text' => 'A type-safe array that extends JavaScript Array with Angular-specific methods', 'correct' => false],
                    ['text' => 'A Subject that buffers all past emissions and replays them to new subscribers', 'correct' => false],
                    ['text' => 'A replacement for @Input() that allows services to push data to components', 'correct' => false],
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
        $this->command->info("Angular Level 5: {$count} questions total.");
    }

    private function level5Questions(): array
    {
        return [
            [
                'question'    => 'How do Angular Signals differ from RxJS Observables for managing component state?',
                'explanation' => 'Signals are synchronous, simple values with change notification — no subscription, no unsubscription, no operators needed. Reading a signal inside a template or computed() automatically registers a dependency; when the signal changes, only the dependent expressions re-evaluate. Observables are async streams designed for complex event sequences, HTTP, and time-based operations. Signals are the preferred choice for local component state; RxJS remains necessary for HTTP, WebSockets, and complex async coordination. Signals bypass Zone.js, enabling future Zone-less Angular.',
                'options' => [
                    ['text' => 'Signals are synchronous, no-subscription primitives for simple state; RxJS is for async streams and complex event sequences', 'correct' => true],
                    ['text' => 'Signals are only for input bindings; Observables are for all other reactive state', 'correct' => false],
                    ['text' => 'They are interchangeable — Angular will deprecate Observables once Signals are stable', 'correct' => false],
                    ['text' => 'Signals require Zone.js; Observables work without it', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of TemplateRef and ViewContainerRef in Angular?',
                'explanation' => 'TemplateRef represents an embedded template — the content inside `<ng-template>`. It does not render anything by itself. ViewContainerRef is a container where you can dynamically insert views. Together they power structural directives: `*ngIf` uses a TemplateRef (the conditionally shown content) and a ViewContainerRef to create/destroy the embedded view. Programmatically: ViewContainerRef.createEmbeddedView(templateRef) renders the template; ViewContainerRef.createComponent(ComponentClass) dynamically creates a component. This is the foundation for modals, tooltips, and dynamic forms.',
                'options' => [
                    ['text' => 'TemplateRef holds the ng-template content; ViewContainerRef is the container for dynamically creating/destroying views', 'correct' => true],
                    ['text' => 'TemplateRef is the component instance; ViewContainerRef is the host DOM element', 'correct' => false],
                    ['text' => 'TemplateRef caches component templates; ViewContainerRef manages component lifecycle', 'correct' => false],
                    ['text' => 'They are only used internally by Angular — developers should not reference them directly', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between ViewChild and ContentChild in Angular?',
                'explanation' => 'ViewChild queries the component\'s own template (the HTML in the component\'s template property). ContentChild queries projected content — elements passed inside the component\'s tags by the parent (ng-content). If a component has `<ng-content />`, the parent can project content into it. ContentChild lets the component access those projected elements. ViewChild results are available in ngAfterViewInit; ContentChild results in ngAfterContentInit. Both can query child components, directives, template references, or DOM elements.',
                'options' => [
                    ['text' => 'ViewChild queries the component\'s own template; ContentChild queries projected content (ng-content) from the parent', 'correct' => true],
                    ['text' => 'ViewChild is for class-based components; ContentChild is for standalone components', 'correct' => false],
                    ['text' => 'ViewChild retrieves a read-only snapshot; ContentChild creates a live binding', 'correct' => false],
                    ['text' => 'They are interchangeable — both query child elements from any source', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is Zone.js\'s role in Angular change detection and what does Zone-less Angular mean?',
                'explanation' => 'Zone.js monkey-patches async browser APIs (setTimeout, Promise, addEventListener, XHR) to intercept their callbacks. When any async callback completes, Zone.js notifies Angular, which then runs change detection. This is what makes Angular "just work" — you update a variable and the template updates automatically without explicit notification. Zone-less Angular (experimentally available in Angular 18) removes this monkey-patching. Without Zone.js, Angular relies on Signals and explicit markForCheck() to know when to update. The benefit: smaller bundle, faster startup, better interoperability with non-Angular code.',
                'options' => [
                    ['text' => 'Zone.js intercepts async APIs to trigger automatic CD; Zone-less Angular uses Signals instead — smaller bundle, no monkey-patching', 'correct' => true],
                    ['text' => 'Zone.js is the DOM rendering engine; removing it requires a custom renderer', 'correct' => false],
                    ['text' => 'Zone.js handles routing; Zone-less apps use the browser\'s native navigation API instead', 'correct' => false],
                    ['text' => 'Zone-less Angular disables change detection entirely — manual re-renders only', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `ng-content` with named slots and when would you use it?',
                'explanation' => 'ng-content projects content from a parent into a child component template. The `select` attribute filters which content is projected by CSS selector. Named slots let a component define multiple projection points — for example, a Card component might have a header slot, a default body slot, and a footer slot. The parent populates each slot using matching attributes or elements. This gives the component\'s consumer full control over specific regions of the template without the component hardcoding the content.',
                'options' => [
                    ['text' => 'Projects parent-provided content into specific regions of a component\'s template using CSS selectors on select attribute', 'correct' => true],
                    ['text' => 'Creates multiple router outlets inside a component for nested navigation', 'correct' => false],
                    ['text' => 'Provides named content to a parent component via EventEmitter', 'correct' => false],
                    ['text' => 'A directive that replaces ng-template for structural content projection', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the @HostBinding decorator in Angular and how does it differ from using a template binding?',
                'explanation' => '@HostBinding binds a property or attribute of the directive/component\'s own host element. For a directive, the host element is the element the directive is applied to. Template bindings ([class], [attr]) require a template — you cannot use them in a directive because directives do not have templates. @HostBinding lets a directive modify the host element directly without template access. It is equivalent to setting ElementRef.nativeElement properties but is declarative, testable, and aware of Angular\'s change detection.',
                'options' => [
                    ['text' => 'Binds to the directive\'s host element — necessary for directives since they have no template of their own', 'correct' => true],
                    ['text' => 'Binds a component property to a parent element\'s attribute (upward binding)', 'correct' => false],
                    ['text' => 'An alias for @ViewChild — both query the host element', 'correct' => false],
                    ['text' => 'Identical to [property] template binding — just a different syntax choice', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How does Angular\'s HttpClientTestingModule work in unit tests?',
                'explanation' => 'HttpClientTestingModule replaces the real HttpClient with a mock backend. Tests call HttpTestingController.expectOne(url) to assert a request was made to a specific URL, then call req.flush(mockData) to simulate the server response. After each test, http.verify() asserts no unexpected requests were made. This approach tests the service\'s Observable handling, error mapping, and data transformation logic without making real network calls — tests are fast, deterministic, and do not depend on a running server.',
                'options' => [
                    ['text' => 'Replaces the real HTTP backend — tests assert requests with expectOne() and simulate responses with flush()', 'correct' => true],
                    ['text' => 'Records real HTTP requests and replays them in subsequent test runs', 'correct' => false],
                    ['text' => 'Mocks the entire service layer — all service methods return predefined values', 'correct' => false],
                    ['text' => 'An in-process HTTP server that runs real API endpoints during tests', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is Angular\'s esbuild builder and what advantage does it have over the Webpack builder?',
                'explanation' => 'esbuild is a JavaScript/TypeScript bundler written in Go. It is 10–100× faster than Webpack because it uses parallel native code rather than JavaScript. Angular 17 made the esbuild-based builder (@angular-devkit/build-angular:application) the default. Benefits: faster ng build, faster ng serve (with Vite\'s dev server for HMR), and smaller output in some cases. The trade-off: some Webpack-specific plugins and custom configurations are not compatible and must be migrated. For most Angular projects the migration is straightforward.',
                'options' => [
                    ['text' => 'A Go-based bundler 10–100× faster than Webpack — Angular 17\'s default for dramatically faster builds and dev server', 'correct' => true],
                    ['text' => 'A lighter version of Webpack that removes advanced features to improve speed', 'correct' => false],
                    ['text' => 'Angular\'s own custom bundler that supports SSR natively — Webpack does not', 'correct' => false],
                    ['text' => 'An experimental bundler only available in Angular 18+ — not yet production-ready', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between takeUntil and the async pipe for managing Observable subscriptions in Angular?',
                'explanation' => 'Both prevent memory leaks from long-lived subscriptions. The async pipe subscribes when the template renders and unsubscribes when the component destroys — automatically, with no manual cleanup. takeUntil is a manual approach: you create a destroy$ Subject, pipe all subscriptions through takeUntil(this.destroy$), and emit in ngOnDestroy. The async pipe is simpler and less error-prone — forget to emit in ngOnDestroy and takeUntil leaks. The async pipe also triggers OnPush change detection automatically. Use async pipe in templates; use takeUntil for imperative subscriptions where a template is not involved.',
                'options' => [
                    ['text' => 'async pipe auto-unsubscribes and triggers OnPush CD — preferred in templates; takeUntil is for imperative subscriptions in ngOnDestroy', 'correct' => true],
                    ['text' => 'takeUntil is for HTTP requests only; async pipe is for BehaviorSubjects', 'correct' => false],
                    ['text' => 'They are interchangeable — takeUntil is just an older pattern', 'correct' => false],
                    ['text' => 'async pipe is slower because it creates a new subscription on every render', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is dynamic component creation in Angular and when would you use it?',
                'explanation' => 'Dynamic component creation (ViewContainerRef.createComponent()) instantiates a component at runtime — not declared in any template. Use cases: a toast/notification system (toasts appear dynamically based on service calls), a modal service (modals are injected at the app root), a drag-and-drop canvas (components are placed on a canvas based on user actions), or a form builder (form sections added/removed at runtime). The created component is fully Angular-aware: it participates in change detection, gets injected services, and supports @Input bindings via componentRef.instance.',
                'options' => [
                    ['text' => 'Instantiates a component at runtime via ViewContainerRef.createComponent() — used for toasts, modals, and dynamic canvases', 'correct' => true],
                    ['text' => 'A technique for loading components from an external URL at runtime', 'correct' => false],
                    ['text' => 'Creates components without Angular\'s DI — used for performance-critical rendering', 'correct' => false],
                    ['text' => 'Equivalent to *ngIf — conditionally shows or hides a component', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of the custom structural directive\'s TypedTemplateDirective pattern in Angular?',
                'explanation' => 'When writing a custom structural directive in Angular, the template context object (the $implicit variable and named variables) can be typed using the static ngTemplateContextGuard method. This enables the type-checker and IDE to infer the correct type of template variables. Without this, the let-item variable in *myDirective="let item" has type unknown. With ngTemplateContextGuard, Angular\'s compiler knows the exact type, providing autocomplete and type errors. This is the pattern used internally by *ngFor to type the let-item variable.',
                'options' => [
                    ['text' => 'Provides TypeScript types for structural directive template context variables via ngTemplateContextGuard', 'correct' => true],
                    ['text' => 'A pattern for wrapping directives in type-safe Angular testing utilities', 'correct' => false],
                    ['text' => 'Allows structural directives to accept generic type parameters', 'correct' => false],
                    ['text' => 'The name for the interface that all structural directives must implement', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is Spectator in Angular testing and what problem does it solve?',
                'explanation' => 'Spectator is a third-party Angular testing library built on top of TestBed. TestBed requires verbose setup: configureTestingModule, compileComponents, createComponent, fixture.detectChanges — repeated for every test file. Spectator provides createComponentFactory() which handles this setup in one call. It adds a fluent query API (spectator.query, spectator.click, spectator.triggerEventHandler), easy service mocking, and host component creation for testing Input/Output interactions. It reduces test boilerplate significantly, letting developers focus on testing behaviour rather than TestBed plumbing.',
                'options' => [
                    ['text' => 'Reduces TestBed boilerplate with createComponentFactory — fluent queries, easy service mocking, less setup code', 'correct' => true],
                    ['text' => 'A visual regression testing tool that takes screenshots of Angular components', 'correct' => false],
                    ['text' => 'Angular\'s built-in alternative to Karma — runs tests in Node.js without a browser', 'correct' => false],
                    ['text' => 'A mocking library specifically for NgRx Store — replaces MockStore', 'correct' => false],
                ],
            ],
            [
                'question'    => 'In Angular, what is the difference between ngAfterViewInit and ngOnInit and which should you use for DOM measurements?',
                'explanation' => 'ngOnInit runs after the component\'s inputs are set and the component is initialized — but the component\'s own template (child views) has NOT been rendered yet. ViewChild queries are not available in ngOnInit. ngAfterViewInit runs after the component\'s view (and child views) are fully rendered — ViewChild and ViewChildren are populated. DOM measurements (getBoundingClientRect, scrollHeight, offsetWidth) must be done in ngAfterViewInit because the elements must exist in the DOM first. Note: modifying bound data in ngAfterViewInit requires calling detectChanges() or using setTimeout to avoid ExpressionChangedAfterCheckedError.',
                'options' => [
                    ['text' => 'ngOnInit: no view yet; ngAfterViewInit: view fully rendered — use AfterViewInit for DOM measurements and ViewChild access', 'correct' => true],
                    ['text' => 'ngOnInit: for synchronous setup; ngAfterViewInit: for async operations like HTTP calls', 'correct' => false],
                    ['text' => 'They run at the same time — the order depends on the component tree depth', 'correct' => false],
                    ['text' => 'ngAfterViewInit only runs in OnPush components; ngOnInit runs in all components', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the Angular workspace library pattern and what problem does it solve for large projects?',
                'explanation' => 'An Angular workspace can contain multiple projects: applications and libraries. Libraries (ng generate library) are shared, reusable code — UI components, utility functions, data access layers — packaged as importable modules. Problems solved: (1) Code reuse across multiple Angular apps without copy-paste. (2) Clear separation of concerns — shared UI in shared-ui, API services in data-access. (3) Independent versioning and publishing to npm. (4) Build caching — only changed libraries rebuild. Tools like Nx extend this pattern with dependency graphs, affected-project detection, and distributed caching.',
                'options' => [
                    ['text' => 'Packages shared code (UI, services, utilities) into libraries used across multiple apps — enables reuse, clear boundaries, and independent versioning', 'correct' => true],
                    ['text' => 'A pattern for splitting a single Angular app across multiple servers for load balancing', 'correct' => false],
                    ['text' => 'Creates separate Angular workspaces that share a common node_modules folder', 'correct' => false],
                    ['text' => 'Replaces Angular modules with workspace-level dependency injection', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the ExpressionChangedAfterItHasBeenCheckedError in Angular and how do you fix it?',
                'explanation' => 'This error occurs in development mode when Angular detects that a template expression changed value during the same change detection cycle in which it was checked. Common causes: (1) Modifying a bound value in ngAfterViewInit or ngAfterContentInit — Angular already checked the view, then the lifecycle hook changes it. (2) Functions in templates with side effects. Fixes: (1) Use setTimeout(() => ..., 0) to defer the update to the next cycle. (2) Use ChangeDetectorRef.detectChanges() after the update. (3) Move the update to ngOnInit instead of ngAfterViewInit. The error does not appear in production — but the view can show inconsistent data.',
                'options' => [
                    ['text' => 'Binding changed after CD already checked it — fix with setTimeout, detectChanges(), or moving updates to ngOnInit', 'correct' => true],
                    ['text' => 'A TypeScript type error when using template expressions that are not type-safe', 'correct' => false],
                    ['text' => 'Thrown when an @Input() value changes inside ngOnChanges after the view renders', 'correct' => false],
                    ['text' => 'Occurs when two-way binding creates a circular update loop between parent and child', 'correct' => false],
                ],
            ],
        ];
    }
}
