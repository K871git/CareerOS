<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReactLearningSeeder extends Seeder
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
            ['slug' => 'react'],
            [
                'learning_track_id' => $track->id,
                'title'             => 'React',
                'description'       => 'Master React from component basics to advanced patterns and architecture.',
                'display_order'     => 4,
            ]
        );

        // ── Step 1: Assign correct levels to existing practice topics ──────
        Topic::where('slug', 'react-junior')->update(['level' => 1]);
        Topic::where('slug', 'react-intermediate')->update(['level' => 2]);
        Topic::where('slug', 'react-advanced')->update(['level' => 3]);

        // ── Step 2: Create topics for levels 4 and 5 ──────────────────────
        $topic4 = Topic::firstOrCreate(
            ['slug' => 'react-level-4-patterns'],
            [
                'subject_id'    => $subject->id,
                'title'         => 'React Patterns & Performance',
                'description'   => 'Compound components, render props, code splitting, and React performance optimisation.',
                'display_order' => 4,
                'level'         => 4,
            ]
        );
        Topic::where('slug', 'react-level-4-patterns')->update(['level' => 4]);

        $topic5 = Topic::firstOrCreate(
            ['slug' => 'react-level-5-expert'],
            [
                'subject_id'    => $subject->id,
                'title'         => 'Expert React',
                'description'   => 'Reconciliation internals, state management architecture, and testing strategies.',
                'display_order' => 5,
                'level'         => 5,
            ]
        );
        Topic::where('slug', 'react-level-5-expert')->update(['level' => 5]);

        // ── Step 3: Seed lessons for all 5 levels ─────────────────────────
        $this->seedLessons($subject);

        // ── Step 4: Seed exam questions for levels 4 and 5 ────────────────
        $this->seedLevel4Questions($topic4);
        $this->seedLevel5Questions($topic5);

        $this->command->info('React Learning seeder complete — all 5 levels populated.');
    }

    // ── LESSONS ─────────────────────────────────────────────────────────────

    private function seedLessons(Subject $subject): void
    {
        $t1 = Topic::where('slug', 'react-junior')->first();
        $t2 = Topic::where('slug', 'react-intermediate')->first();
        $t3 = Topic::where('slug', 'react-advanced')->first();
        $t4 = Topic::where('slug', 'react-level-4-patterns')->first();
        $t5 = Topic::where('slug', 'react-level-5-expert')->first();

        $lessons = [
            // ── LEVEL 1 ────────────────────────────────────────────────────
            [
                'topic_id'          => $t1->id,
                'title'             => 'JSX, Components & Props',
                'estimated_minutes' => 15,
                'display_order'     => 1,
                'content'           => <<<'MARKDOWN'
## What is React?

React is a JavaScript library for building user interfaces. It lets you compose complex UIs from small, isolated pieces of code called **components**.

React runs in the browser. You write components, React figures out how to update the DOM efficiently when data changes.

## JSX

JSX is a syntax extension for JavaScript that looks like HTML. React uses JSX to describe what the UI should look like.

```jsx
const element = <h1>Hello, world!</h1>;
```

JSX is not valid JavaScript — it is compiled by Babel/Vite to `React.createElement()` calls:

```js
// What you write
const element = <h1 className="title">Hello</h1>;

// What Babel compiles it to
const element = React.createElement('h1', { className: 'title' }, 'Hello');
```

Key JSX rules:
- Use `className` instead of `class` (class is a reserved JS keyword)
- Use `htmlFor` instead of `for` on labels
- All tags must be closed: `<img />`, `<br />`
- Return a single root element (or use `<>...</>` fragment)
- Expressions go inside `{}`: `<p>{user.name}</p>`

## Components

A React component is a function that returns JSX:

```jsx
function Greeting({ name }) {
  return <h1>Hello, {name}!</h1>;
}

// Usage
<Greeting name="Alice" />
```

**Rules for components:**
- Component names must start with a capital letter (`Greeting`, not `greeting`)
- Must return JSX (or `null` to render nothing)
- Must be a pure function — same props → same output, no side effects in render

## Props

Props (properties) are how you pass data from a parent to a child component. They are read-only — a component must never modify its own props.

```jsx
function UserCard({ name, role, avatarUrl }) {
  return (
    <div className="card">
      <img src={avatarUrl} alt={name} />
      <h2>{name}</h2>
      <p>{role}</p>
    </div>
  );
}

// Parent passes values as attributes
<UserCard
  name="Alice"
  role="Engineer"
  avatarUrl="/avatars/alice.jpg"
/>
```

**Default props** — use default parameter values:

```jsx
function Button({ label = 'Click me', variant = 'primary' }) {
  return <button className={`btn btn--${variant}`}>{label}</button>;
}
```

**Children prop** — content placed between opening and closing tags:

```jsx
function Card({ title, children }) {
  return (
    <div className="card">
      <h3>{title}</h3>
      <div className="card-body">{children}</div>
    </div>
  );
}

// Usage
<Card title="Profile">
  <p>Name: Alice</p>
  <p>Role: Engineer</p>
</Card>
```

## Component Composition

Build complex UIs by composing smaller components:

```jsx
function App() {
  return (
    <div>
      <Header />
      <main>
        <UserCard name="Alice" role="Engineer" avatarUrl="/alice.jpg" />
        <UserCard name="Bob"   role="Designer" avatarUrl="/bob.jpg"   />
      </main>
      <Footer />
    </div>
  );
}
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t1->id,
                'title'             => 'useState & Event Handling',
                'estimated_minutes' => 15,
                'display_order'     => 2,
                'content'           => <<<'MARKDOWN'
## State in React

State is data that can change over time, causing the component to re-render. Unlike props (passed in from outside), state is owned and managed by the component itself.

`useState` is the primary hook for adding state to a function component:

```jsx
import { useState } from 'react';

function Counter() {
  const [count, setCount] = useState(0); // initial value = 0

  return (
    <div>
      <p>Count: {count}</p>
      <button onClick={() => setCount(count + 1)}>+1</button>
    </div>
  );
}
```

`useState` returns an array with two elements:
1. The current state value
2. A setter function to update it

**Always use the setter** — never mutate state directly:
```jsx
// WRONG: mutates state directly, React won't re-render
count = count + 1;

// CORRECT: React sees the new value and re-renders
setCount(count + 1);
```

## Functional Updates

When the next state depends on the previous state, use the functional form:

```jsx
// Safe: uses the actual current state, not a stale closure value
setCount(prev => prev + 1);

// Bug-prone: may use a stale `count` if called multiple times rapidly
setCount(count + 1);
```

## Multiple State Variables

```jsx
function Form() {
  const [name, setName]     = useState('');
  const [email, setEmail]   = useState('');
  const [agreed, setAgreed] = useState(false);

  return (
    <form>
      <input value={name}  onChange={e => setName(e.target.value)} />
      <input value={email} onChange={e => setEmail(e.target.value)} />
      <input type="checkbox" checked={agreed} onChange={e => setAgreed(e.target.checked)} />
    </form>
  );
}
```

## Event Handling

React events use camelCase (`onClick`, `onChange`, `onSubmit`) and pass a synthetic event object:

```jsx
function SaveButton() {
  function handleClick(event) {
    event.preventDefault(); // prevent default browser action
    console.log('Saving...');
  }

  return <button onClick={handleClick}>Save</button>;
}
```

**Passing arguments to handlers:**

```jsx
function List() {
  const items = ['Apple', 'Banana', 'Cherry'];

  return (
    <ul>
      {items.map((item, i) => (
        <li key={i} onClick={() => console.log(`Clicked: ${item}`)}>
          {item}
        </li>
      ))}
    </ul>
  );
}
```

## Controlled Inputs

A controlled input's value is driven by React state — the input always reflects the current state value:

```jsx
function SearchBox() {
  const [query, setQuery] = useState('');

  return (
    <input
      type="text"
      value={query}
      onChange={e => setQuery(e.target.value)}
      placeholder="Search..."
    />
  );
}
```

The `value` prop makes React the single source of truth. Without `onChange`, the input becomes read-only.
MARKDOWN,
            ],
            [
                'topic_id'          => $t1->id,
                'title'             => 'Rendering Lists & Conditional Rendering',
                'estimated_minutes' => 15,
                'display_order'     => 3,
                'content'           => <<<'MARKDOWN'
## Rendering Lists

Use `.map()` to render an array of items as a list of components:

```jsx
const fruits = ['Apple', 'Banana', 'Cherry'];

function FruitList() {
  return (
    <ul>
      {fruits.map((fruit, index) => (
        <li key={index}>{fruit}</li>
      ))}
    </ul>
  );
}
```

## The `key` Prop

Every element in a list needs a unique `key` prop. React uses keys to efficiently update the DOM when the list changes.

```jsx
// GOOD: use a stable unique ID
{users.map(user => (
  <UserCard key={user.id} user={user} />
))}

// AVOID: index as key (causes bugs when list is reordered/filtered)
{users.map((user, index) => (
  <UserCard key={index} user={user} />
))}
```

Keys must be unique among siblings, not globally. Keys are not passed as props — the child component cannot access `props.key`.

## Conditional Rendering

**Using `&&` (short-circuit)** — renders only when condition is truthy:

```jsx
function Notification({ message }) {
  return (
    <div>
      {message && <p className="alert">{message}</p>}
    </div>
  );
}
```

**Gotcha**: avoid `0 && <Component />` — renders `0` because 0 is falsy but renders as text. Use `count > 0 && <Component />` instead.

**Using ternary** — renders one of two options:

```jsx
function AuthButton({ isLoggedIn }) {
  return (
    <button>
      {isLoggedIn ? 'Log out' : 'Log in'}
    </button>
  );
}
```

**Using if/else before return** — for complex logic, extract to a variable:

```jsx
function StatusBadge({ status }) {
  let badge;

  if (status === 'active') {
    badge = <span className="badge badge--green">Active</span>;
  } else if (status === 'pending') {
    badge = <span className="badge badge--yellow">Pending</span>;
  } else {
    badge = <span className="badge badge--gray">Inactive</span>;
  }

  return <div>{badge}</div>;
}
```

## Returning null

A component that returns `null` renders nothing — useful for conditionally hiding entire components:

```jsx
function Banner({ show, text }) {
  if (!show) return null;
  return <div className="banner">{text}</div>;
}
```

## Fragments

When you need to return multiple elements without a wrapper div, use a Fragment:

```jsx
function TableRow({ item }) {
  return (
    <>
      <dt>{item.label}</dt>
      <dd>{item.value}</dd>
    </>
  );
}
```

`<>...</>` is shorthand for `<React.Fragment>...</React.Fragment>`. Fragments don't create extra DOM nodes.
MARKDOWN,
            ],

            // ── LEVEL 2 ────────────────────────────────────────────────────
            [
                'topic_id'          => $t2->id,
                'title'             => 'useEffect: Side Effects & Lifecycle',
                'estimated_minutes' => 18,
                'display_order'     => 1,
                'content'           => <<<'MARKDOWN'
## What is a Side Effect?

A side effect is anything that happens outside of rendering — fetching data, setting up subscriptions, manually updating the DOM, or setting timers.

`useEffect` is where you put side effects in React function components.

## Basic Syntax

```jsx
useEffect(() => {
  // side effect runs here
}, [dependencies]);
```

- **Effect function** — runs after every render by default
- **Dependency array** — controls when the effect re-runs

## Dependency Array Behaviour

```jsx
// Runs after EVERY render (no dependency array)
useEffect(() => {
  console.log('Rendered');
});

// Runs only ONCE on mount (empty array)
useEffect(() => {
  console.log('Mounted');
}, []);

// Runs when `userId` changes
useEffect(() => {
  fetchUser(userId);
}, [userId]);
```

**React 18 strict mode note**: in development, React mounts components twice to catch cleanup bugs. This is normal and does not happen in production.

## Data Fetching Pattern

```jsx
function UserProfile({ userId }) {
  const [user, setUser]       = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError]     = useState(null);

  useEffect(() => {
    let cancelled = false; // prevent stale updates

    setLoading(true);
    fetch(`/api/users/${userId}`)
      .then(r => r.json())
      .then(data => {
        if (!cancelled) {
          setUser(data);
          setLoading(false);
        }
      })
      .catch(err => {
        if (!cancelled) {
          setError(err.message);
          setLoading(false);
        }
      });

    return () => { cancelled = true; }; // cleanup
  }, [userId]);

  if (loading) return <p>Loading...</p>;
  if (error)   return <p>Error: {error}</p>;
  return <div>{user.name}</div>;
}
```

## Cleanup Functions

Return a function from the effect to clean up subscriptions, timers, or event listeners:

```jsx
useEffect(() => {
  const id = setInterval(() => {
    setTime(new Date().toLocaleTimeString());
  }, 1000);

  return () => clearInterval(id); // runs on unmount or before next effect
}, []);
```

```jsx
useEffect(() => {
  const handler = () => setScrollY(window.scrollY);
  window.addEventListener('scroll', handler);
  return () => window.removeEventListener('scroll', handler);
}, []);
```

## Common Mistakes

```jsx
// Bug: object/array in deps creates infinite loop
useEffect(() => {
  fetchData(options);
}, [options]); // options = {} re-creates every render → infinite loop
// Fix: use primitive values or memoize the object with useMemo

// Bug: missing dependency
useEffect(() => {
  setTitle(`Hello, ${name}`); // name is used but not in deps
}, []); // ESLint exhaustive-deps rule catches this
```

## When NOT to Use useEffect

React 18 introduced a principle: **don't synchronise with useEffect what you can compute during render**.

```jsx
// WRONG: using effect to derive state
const [doubled, setDoubled] = useState(0);
useEffect(() => {
  setDoubled(count * 2);
}, [count]);

// CORRECT: compute directly during render
const doubled = count * 2;
```

Use effects only for things that truly require synchronising with the external world.
MARKDOWN,
            ],
            [
                'topic_id'          => $t2->id,
                'title'             => 'useMemo, useCallback & useRef',
                'estimated_minutes' => 15,
                'display_order'     => 2,
                'content'           => <<<'MARKDOWN'
## useRef

`useRef` returns a mutable ref object whose `.current` property persists across renders without causing re-renders.

**Two main uses:**

**1. Accessing DOM elements:**
```jsx
function FocusInput() {
  const inputRef = useRef(null);

  function handleClick() {
    inputRef.current.focus(); // direct DOM access
  }

  return (
    <>
      <input ref={inputRef} type="text" />
      <button onClick={handleClick}>Focus</button>
    </>
  );
}
```

**2. Storing mutable values that don't trigger re-renders:**
```jsx
function StopwatchWithRef() {
  const [time, setTime]   = useState(0);
  const intervalRef = useRef(null);

  function start() {
    intervalRef.current = setInterval(() => {
      setTime(t => t + 1);
    }, 1000);
  }

  function stop() {
    clearInterval(intervalRef.current);
  }

  return (
    <div>
      <p>{time}s</p>
      <button onClick={start}>Start</button>
      <button onClick={stop}>Stop</button>
    </div>
  );
}
```

## useMemo

`useMemo` memoizes the result of an expensive computation — only recomputes when dependencies change:

```jsx
function ProductList({ products, searchTerm }) {
  const filtered = useMemo(
    () => products.filter(p =>
      p.name.toLowerCase().includes(searchTerm.toLowerCase())
    ),
    [products, searchTerm] // only recompute when these change
  );

  return (
    <ul>
      {filtered.map(p => <li key={p.id}>{p.name}</li>)}
    </ul>
  );
}
```

**When to use `useMemo`:**
- Expensive computations (large array filters/sorts, complex maths)
- When the result is a reference type passed to a memoized child (prevents unnecessary re-renders)

**When NOT to use it:** For cheap computations — the overhead of memoization can exceed the savings.

## useCallback

`useCallback` memoizes a function itself — returns the same function reference unless dependencies change:

```jsx
function Parent() {
  const [count, setCount] = useState(0);

  // Without useCallback: new function reference every render → Child always re-renders
  // With useCallback: same reference → Child only re-renders when count changes
  const handleIncrement = useCallback(() => {
    setCount(c => c + 1);
  }, []); // no deps — function never needs to change

  return <Child onIncrement={handleIncrement} />;
}

// Child is wrapped in React.memo to skip re-renders when props don't change
const Child = React.memo(({ onIncrement }) => {
  console.log('Child rendered');
  return <button onClick={onIncrement}>+1</button>;
});
```

**Rule of thumb:** Use `useCallback` when passing callbacks to:
- Components wrapped in `React.memo`
- `useEffect` dependency arrays (to prevent infinite loops)

## The Relationship

`useMemo` → memoizes a **value**
`useCallback` → memoizes a **function** (equivalent to `useMemo(() => fn, deps)`)
`useRef` → stores a **mutable value** that never triggers re-renders
MARKDOWN,
            ],
            [
                'topic_id'          => $t2->id,
                'title'             => 'Context API & useContext',
                'estimated_minutes' => 15,
                'display_order'     => 3,
                'content'           => <<<'MARKDOWN'
## The Problem: Prop Drilling

When data needs to reach a deeply nested component, passing props through every intermediate layer is called **prop drilling**:

```jsx
// App → Dashboard → Sidebar → UserMenu → Avatar all need `user`
// Without context: every level has to pass it down explicitly
function Dashboard({ user }) {
  return <Sidebar user={user} />;
}
function Sidebar({ user }) {
  return <UserMenu user={user} />;
}
// ...and so on
```

Context solves this by letting you share values across the component tree without passing props at every level.

## Creating & Providing Context

```jsx
import { createContext, useContext, useState } from 'react';

// 1. Create the context
const ThemeContext = createContext('light'); // default value

// 2. Provide it at the top of the tree
function App() {
  const [theme, setTheme] = useState('light');

  return (
    <ThemeContext.Provider value={{ theme, setTheme }}>
      <Dashboard />
    </ThemeContext.Provider>
  );
}
```

## Consuming Context

```jsx
// Any descendant can read the context — no props needed
function ThemeToggle() {
  const { theme, setTheme } = useContext(ThemeContext);

  return (
    <button onClick={() => setTheme(t => t === 'light' ? 'dark' : 'light')}>
      Current: {theme}
    </button>
  );
}
```

## Full Pattern: Custom Hook for Context

Wrap the context in a custom hook to:
1. Validate context is used inside the provider
2. Provide a clean API

```jsx
const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);

  function login(credentials) { /* ... */ }
  function logout() { setUser(null); }

  return (
    <AuthContext.Provider value={{ user, login, logout }}>
      {children}
    </AuthContext.Provider>
  );
}

// Custom hook — throws if used outside provider
export function useAuth() {
  const ctx = useContext(AuthContext);
  if (!ctx) throw new Error('useAuth must be used inside AuthProvider');
  return ctx;
}

// Usage anywhere in the app:
function NavBar() {
  const { user, logout } = useAuth();
  return <button onClick={logout}>{user?.name}</button>;
}
```

## When to Use Context

Context is designed for **global-ish** data that doesn't change frequently:
- Current user / auth state
- Theme (light/dark)
- Language / locale
- Feature flags

**Avoid context for frequently updating state** (like form fields or lists) — every consumer re-renders on every change. For high-frequency updates, use Zustand, Jotai, or Redux.

## Context vs Props

| | Props | Context |
|---|---|---|
| Data flow | Explicit, traceable | Implicit, magical |
| Scope | Local / small trees | Global / large trees |
| Performance | Only affected components re-render | All consumers re-render on change |
| Best for | Component communication | Cross-cutting concerns |
MARKDOWN,
            ],

            // ── LEVEL 3 ────────────────────────────────────────────────────
            [
                'topic_id'          => $t3->id,
                'title'             => 'Custom Hooks: Building Reusable Logic',
                'estimated_minutes' => 18,
                'display_order'     => 1,
                'content'           => <<<'MARKDOWN'
## What is a Custom Hook?

A custom hook is a JavaScript function whose name starts with `use` and that calls other hooks. They let you extract and share stateful logic between components — without changing the component hierarchy.

## Why Custom Hooks?

Before hooks, sharing stateful logic required render props or higher-order components — complex patterns that made code harder to follow.

With custom hooks, you extract logic into a plain function. Components that use it each get their own isolated state.

## Simple Example: useLocalStorage

```jsx
function useLocalStorage(key, initialValue) {
  const [value, setValue] = useState(() => {
    try {
      const item = localStorage.getItem(key);
      return item ? JSON.parse(item) : initialValue;
    } catch {
      return initialValue;
    }
  });

  function setStoredValue(newValue) {
    try {
      setValue(newValue);
      localStorage.setItem(key, JSON.stringify(newValue));
    } catch {
      console.error('localStorage write failed');
    }
  }

  return [value, setStoredValue];
}

// Usage — identical API to useState, but persisted
function Settings() {
  const [theme, setTheme] = useLocalStorage('theme', 'light');
  return <button onClick={() => setTheme(t => t === 'light' ? 'dark' : 'light')}>{theme}</button>;
}
```

## useFetch: Data Fetching Hook

```jsx
function useFetch(url) {
  const [data, setData]       = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError]     = useState(null);

  useEffect(() => {
    let cancelled = false;
    setLoading(true);
    setError(null);

    fetch(url)
      .then(r => {
        if (!r.ok) throw new Error(`HTTP ${r.status}`);
        return r.json();
      })
      .then(json => { if (!cancelled) { setData(json); setLoading(false); } })
      .catch(err => { if (!cancelled) { setError(err.message); setLoading(false); } });

    return () => { cancelled = true; };
  }, [url]);

  return { data, loading, error };
}

// Usage
function UserList() {
  const { data: users, loading, error } = useFetch('/api/users');
  if (loading) return <p>Loading...</p>;
  if (error)   return <p>Error: {error}</p>;
  return <ul>{users.map(u => <li key={u.id}>{u.name}</li>)}</ul>;
}
```

## useDebounce

```jsx
function useDebounce(value, delay = 300) {
  const [debounced, setDebounced] = useState(value);

  useEffect(() => {
    const timer = setTimeout(() => setDebounced(value), delay);
    return () => clearTimeout(timer);
  }, [value, delay]);

  return debounced;
}

// Usage
function SearchInput() {
  const [query, setQuery]   = useState('');
  const debouncedQuery      = useDebounce(query, 400);

  useEffect(() => {
    if (debouncedQuery) search(debouncedQuery); // API call only after typing stops
  }, [debouncedQuery]);

  return <input value={query} onChange={e => setQuery(e.target.value)} />;
}
```

## useEventListener

```jsx
function useEventListener(event, handler, element = window) {
  const savedHandler = useRef(handler);

  useLayoutEffect(() => {
    savedHandler.current = handler;
  }, [handler]);

  useEffect(() => {
    if (!element?.addEventListener) return;
    const listener = (e) => savedHandler.current(e);
    element.addEventListener(event, listener);
    return () => element.removeEventListener(event, listener);
  }, [event, element]);
}

// Usage
function KeyLogger() {
  const [key, setKey] = useState('');
  useEventListener('keydown', e => setKey(e.key));
  return <p>Last key: {key}</p>;
}
```

## Rules for Custom Hooks

1. Name must start with `use` — React uses this to enforce the Rules of Hooks
2. Can call other hooks inside
3. Each call to a custom hook gets its own isolated state — not shared between components
4. Cannot be called conditionally (same as built-in hooks)
MARKDOWN,
            ],
            [
                'topic_id'          => $t3->id,
                'title'             => 'React Query: Server State Management',
                'estimated_minutes' => 18,
                'display_order'     => 2,
                'content'           => <<<'MARKDOWN'
## What is React Query?

React Query (TanStack Query) is a server state management library. It handles fetching, caching, synchronisation, and updating of server data — removing the need to write manual `useEffect` + `useState` patterns for data fetching.

**Server state** is data that lives on the server: user profiles, posts, products. It has a different lifecycle from **client state** (UI state like modal open/closed).

## Setup

```jsx
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';

const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      staleTime: 1000 * 60 * 5, // data stays fresh for 5 minutes
      retry: 1,
    },
  },
});

function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <Router />
    </QueryClientProvider>
  );
}
```

## useQuery: Fetching Data

```jsx
import { useQuery } from '@tanstack/react-query';

function UserProfile({ userId }) {
  const { data: user, isLoading, isError, error } = useQuery({
    queryKey: ['users', userId], // cache key — unique identifier
    queryFn: () => fetch(`/api/users/${userId}`).then(r => r.json()),
    staleTime: 1000 * 60, // don't refetch for 1 minute
  });

  if (isLoading) return <Skeleton />;
  if (isError)   return <p>Error: {error.message}</p>;

  return <div>{user.name}</div>;
}
```

**Query key** is an array that uniquely identifies the query. When the key changes, React Query automatically refetches. It also uses the key for cache lookup — two components using the same key share the same cached data.

## useMutation: Modifying Data

```jsx
import { useMutation, useQueryClient } from '@tanstack/react-query';

function EditNameForm({ userId }) {
  const queryClient = useQueryClient();

  const mutation = useMutation({
    mutationFn: (newName) =>
      fetch(`/api/users/${userId}`, {
        method: 'PATCH',
        body: JSON.stringify({ name: newName }),
        headers: { 'Content-Type': 'application/json' },
      }).then(r => r.json()),

    onSuccess: () => {
      // Invalidate the cache so the profile refetches
      queryClient.invalidateQueries({ queryKey: ['users', userId] });
    },
  });

  return (
    <button
      onClick={() => mutation.mutate('Alice Updated')}
      disabled={mutation.isPending}
    >
      {mutation.isPending ? 'Saving...' : 'Save Name'}
    </button>
  );
}
```

## Key Features

**Automatic background refetch** — data is refetched when the window regains focus, the network reconnects, or the query key changes.

**Caching** — responses are cached by query key. Subsequent mounts of the same component return cached data instantly while refetching in the background (stale-while-revalidate).

**Optimistic updates** — update the UI before the server confirms:
```jsx
useMutation({
  mutationFn: toggleLike,
  onMutate: async (postId) => {
    await queryClient.cancelQueries({ queryKey: ['posts'] });
    const previous = queryClient.getQueryData(['posts']);
    queryClient.setQueryData(['posts'], old =>
      old.map(p => p.id === postId ? { ...p, liked: !p.liked } : p)
    );
    return { previous }; // for rollback
  },
  onError: (err, vars, context) => {
    queryClient.setQueryData(['posts'], context.previous); // rollback
  },
});
```

## React Query vs useEffect

| | useEffect + fetch | React Query |
|---|---|---|
| Loading state | Manual | Built-in |
| Error state | Manual | Built-in |
| Caching | None | Automatic |
| Deduplication | None | Same key = one request |
| Background refetch | No | Yes |
| Stale data handling | Manual | Stale-while-revalidate |
MARKDOWN,
            ],
            [
                'topic_id'          => $t3->id,
                'title'             => 'Code Splitting with React.lazy & Suspense',
                'estimated_minutes' => 15,
                'display_order'     => 3,
                'content'           => <<<'MARKDOWN'
## Why Code Splitting?

A React application built with Vite or webpack produces a single JavaScript bundle. As your app grows, this bundle grows — increasing the time to download, parse, and execute before the user sees anything.

Code splitting breaks the bundle into smaller chunks that are loaded only when needed.

## React.lazy

`React.lazy` lets you dynamically import a component — it won't be loaded until React renders it for the first time:

```jsx
import { lazy, Suspense } from 'react';

// NOT loaded until the route renders
const Dashboard = lazy(() => import('./pages/Dashboard'));
const Settings  = lazy(() => import('./pages/Settings'));
const Profile   = lazy(() => import('./pages/Profile'));
```

The import must be a default export. `lazy()` takes a function that returns a Promise (which `import()` does naturally).

## Suspense

While a lazy component is loading, React needs something to show. `Suspense` provides a fallback:

```jsx
function App() {
  return (
    <Suspense fallback={<div className="page-loader">Loading...</div>}>
      <Routes>
        <Route path="/dashboard" element={<Dashboard />} />
        <Route path="/settings"  element={<Settings  />} />
        <Route path="/profile"   element={<Profile   />} />
      </Routes>
    </Suspense>
  );
}
```

The `fallback` renders while the lazy component's JavaScript chunk is being downloaded. Once loaded, it's cached — subsequent renders are instant.

## Route-Based Splitting

The most impactful pattern: split by route. Each page is a separate chunk — users only download the code for pages they visit:

```jsx
import { lazy, Suspense } from 'react';
import { BrowserRouter, Routes, Route } from 'react-router-dom';

const Home      = lazy(() => import('./pages/Home'));
const Dashboard = lazy(() => import('./pages/Dashboard'));
const Admin     = lazy(() => import('./pages/Admin'));

function App() {
  return (
    <BrowserRouter>
      <Suspense fallback={<PageSkeleton />}>
        <Routes>
          <Route path="/"          element={<Home />} />
          <Route path="/dashboard" element={<Dashboard />} />
          <Route path="/admin"     element={<Admin />} />
        </Routes>
      </Suspense>
    </BrowserRouter>
  );
}
```

## Error Boundaries with Suspense

If a lazy import fails (network error), it throws. Wrap in an error boundary to handle gracefully:

```jsx
class ErrorBoundary extends React.Component {
  state = { hasError: false };

  static getDerivedStateFromError() {
    return { hasError: true };
  }

  render() {
    if (this.state.hasError) {
      return <div>Failed to load. <button onClick={() => window.location.reload()}>Retry</button></div>;
    }
    return this.props.children;
  }
}

function App() {
  return (
    <ErrorBoundary>
      <Suspense fallback={<Spinner />}>
        <LazyPage />
      </Suspense>
    </ErrorBoundary>
  );
}
```

## Preloading

Trigger the download before the user navigates to the route:

```jsx
// Hover over a nav link → preload the chunk
const DashboardPage = lazy(() => import('./pages/Dashboard'));

function NavLink() {
  return (
    <Link
      to="/dashboard"
      onMouseEnter={() => import('./pages/Dashboard')} // start download on hover
    >
      Dashboard
    </Link>
  );
}
```

## What Gets Split

Vite automatically splits:
- `React.lazy()` imports
- Manual dynamic `import()` calls
- Vendor chunks (node_modules)

You can see the split output by running `npm run build` — each chunk becomes a separate `.js` file.
MARKDOWN,
            ],

            // ── LEVEL 4 ────────────────────────────────────────────────────
            [
                'topic_id'          => $t4->id,
                'title'             => 'Compound Components & Render Props',
                'estimated_minutes' => 18,
                'display_order'     => 1,
                'content'           => <<<'MARKDOWN'
## Component Patterns

Advanced React patterns give you flexible, reusable component APIs. The two most important are **Compound Components** and **Render Props**.

## Compound Components

Compound components are a group of components that work together sharing implicit state — like `<select>` and `<option>` in HTML.

```jsx
// Instead of this rigid API:
<Tabs activeTab="profile" tabs={[{ id: 'profile', label: 'Profile', content: <Profile /> }]} />

// Use compound components for flexible composition:
<Tabs defaultTab="profile">
  <Tabs.List>
    <Tabs.Tab id="profile">Profile</Tabs.Tab>
    <Tabs.Tab id="settings">Settings</Tabs.Tab>
  </Tabs.List>
  <Tabs.Panel id="profile"><Profile /></Tabs.Panel>
  <Tabs.Panel id="settings"><Settings /></Tabs.Panel>
</Tabs>
```

**Implementation using Context:**

```jsx
const TabsContext = createContext(null);

function Tabs({ defaultTab, children }) {
  const [activeTab, setActiveTab] = useState(defaultTab);
  return (
    <TabsContext.Provider value={{ activeTab, setActiveTab }}>
      <div className="tabs">{children}</div>
    </TabsContext.Provider>
  );
}

function TabList({ children }) {
  return <div className="tab-list" role="tablist">{children}</div>;
}

function Tab({ id, children }) {
  const { activeTab, setActiveTab } = useContext(TabsContext);
  return (
    <button
      role="tab"
      aria-selected={activeTab === id}
      className={activeTab === id ? 'tab tab--active' : 'tab'}
      onClick={() => setActiveTab(id)}
    >
      {children}
    </button>
  );
}

function Panel({ id, children }) {
  const { activeTab } = useContext(TabsContext);
  return activeTab === id ? <div role="tabpanel">{children}</div> : null;
}

// Attach sub-components as properties
Tabs.List  = TabList;
Tabs.Tab   = Tab;
Tabs.Panel = Panel;
```

## Render Props

A render prop is a prop whose value is a function that returns JSX. The component calls the function — giving the caller full control over rendering while the component owns the logic.

```jsx
// DataFetcher owns fetch logic, caller controls rendering
function DataFetcher({ url, render }) {
  const { data, loading, error } = useFetch(url);
  return render({ data, loading, error });
}

// Usage
<DataFetcher
  url="/api/users"
  render={({ data, loading, error }) => {
    if (loading) return <Spinner />;
    if (error)   return <ErrorMessage text={error} />;
    return <UserList users={data} />;
  }}
/>
```

The `children` prop as a function is the most common form:

```jsx
function Toggle({ children }) {
  const [on, setOn] = useState(false);
  return children({ on, toggle: () => setOn(o => !o) });
}

// Usage
<Toggle>
  {({ on, toggle }) => (
    <div>
      <button onClick={toggle}>{on ? 'On' : 'Off'}</button>
      {on && <Modal />}
    </div>
  )}
</Toggle>
```

## When to Use Each

**Compound components** — when you have multiple related components with shared state, and you want the caller to control structure and composition (Tabs, Accordion, Select, Menu).

**Render props** — when you want to share stateful logic and let the caller decide how to render (DataFetcher, IntersectionObserver, Toggle). In most modern code, custom hooks have replaced render props — but render props still appear in libraries.

**Custom hooks** — the modern, simpler alternative to render props for sharing logic without affecting component structure.
MARKDOWN,
            ],
            [
                'topic_id'          => $t4->id,
                'title'             => 'React Performance Optimisation',
                'estimated_minutes' => 18,
                'display_order'     => 2,
                'content'           => <<<'MARKDOWN'
## How React Decides to Re-render

A component re-renders when:
1. Its own state changes
2. Its parent re-renders (even if props are unchanged)
3. Context it consumes changes

Understanding this is the foundation of React performance work.

## React.memo

`React.memo` is a higher-order component that skips re-rendering a component if its props haven't changed (shallow comparison):

```jsx
const ExpensiveList = React.memo(function ExpensiveList({ items, onItemClick }) {
  console.log('ExpensiveList rendered');
  return (
    <ul>
      {items.map(item => (
        <li key={item.id} onClick={() => onItemClick(item.id)}>{item.name}</li>
      ))}
    </ul>
  );
});

function Parent() {
  const [count, setCount] = useState(0);
  const [items]           = useState([{ id: 1, name: 'Apple' }]);

  // Without useCallback: new function every render → memo is useless
  const handleClick = useCallback((id) => {
    console.log('Clicked:', id);
  }, []);

  return (
    <div>
      <button onClick={() => setCount(c => c + 1)}>Count: {count}</button>
      <ExpensiveList items={items} onItemClick={handleClick} />
    </div>
  );
}
```

**Rule:** `React.memo` only helps when the component is actually expensive to render. Don't add it everywhere — the comparison itself has a cost.

## Virtualisation for Long Lists

Rendering 10,000 list items creates 10,000 DOM nodes. **Windowing** (virtualisation) renders only what's visible:

```jsx
import { FixedSizeList } from 'react-window';

function VirtualList({ items }) {
  const Row = ({ index, style }) => (
    <div style={style} className="list-row">
      {items[index].name}
    </div>
  );

  return (
    <FixedSizeList
      height={600}     // visible window height
      itemCount={items.length}
      itemSize={48}    // each row height in px
      width="100%"
    >
      {Row}
    </FixedSizeList>
  );
}
```

`react-window` and `react-virtual` (TanStack) are the main libraries. Use them when lists exceed ~100 items that are visually complex.

## State Colocation

Move state as close to where it's used as possible. Global state causes the whole tree to re-render unnecessarily.

```jsx
// SLOW: parent re-renders on every keystroke → all children re-render
function Parent() {
  const [search, setSearch] = useState('');
  return (
    <div>
      <input onChange={e => setSearch(e.target.value)} />
      <HeavyVisualization /> {/* re-renders on every keystroke! */}
      <Results search={search} />
    </div>
  );
}

// FAST: search state lives in its own component
function SearchSection() {
  const [search, setSearch] = useState('');
  return (
    <>
      <input onChange={e => setSearch(e.target.value)} />
      <Results search={search} />
    </>
  );
}
function Parent() {
  return (
    <div>
      <SearchSection />
      <HeavyVisualization /> {/* no longer affected by search state */}
    </div>
  );
}
```

## Measuring Performance

Use React DevTools Profiler (browser extension) to:
- Record a session and see which components re-rendered and why
- Identify "wasted renders" (components that re-rendered but output the same result)
- Check render duration

**Never optimise without measuring first.** Most apps have only a few real bottlenecks. Premature optimisation with `memo`/`useCallback` everywhere adds code complexity without meaningful gain.

## Transitions (React 18)

`startTransition` marks state updates as non-urgent — React can interrupt them to keep the UI responsive:

```jsx
import { startTransition, useTransition } from 'react';

function SearchPage() {
  const [query, setQuery]     = useState('');
  const [results, setResults] = useState([]);
  const [isPending, startTransition] = useTransition();

  function handleChange(e) {
    setQuery(e.target.value); // urgent: update input immediately

    startTransition(() => {
      setResults(searchData(e.target.value)); // non-urgent: can be interrupted
    });
  }

  return (
    <>
      <input value={query} onChange={handleChange} />
      {isPending && <Spinner />}
      <Results items={results} />
    </>
  );
}
```
MARKDOWN,
            ],
            [
                'topic_id'          => $t4->id,
                'title'             => 'Controlled vs Uncontrolled & Form Patterns',
                'estimated_minutes' => 15,
                'display_order'     => 3,
                'content'           => <<<'MARKDOWN'
## Controlled Components

In a controlled component, form data is driven by React state. The component renders the form, React owns the value:

```jsx
function LoginForm() {
  const [email, setEmail]       = useState('');
  const [password, setPassword] = useState('');
  const [errors, setErrors]     = useState({});

  function validate() {
    const errs = {};
    if (!email.includes('@'))    errs.email    = 'Invalid email';
    if (password.length < 8)     errs.password = 'Min 8 characters';
    return errs;
  }

  function handleSubmit(e) {
    e.preventDefault();
    const errs = validate();
    if (Object.keys(errs).length > 0) {
      setErrors(errs);
      return;
    }
    // submit to API
  }

  return (
    <form onSubmit={handleSubmit}>
      <input
        type="email"
        value={email}
        onChange={e => setEmail(e.target.value)}
      />
      {errors.email && <p className="error">{errors.email}</p>}

      <input
        type="password"
        value={password}
        onChange={e => setPassword(e.target.value)}
      />
      {errors.password && <p className="error">{errors.password}</p>}

      <button type="submit">Login</button>
    </form>
  );
}
```

**Pros:** Full control over validation, transformation, and formatting as the user types. Easy to reset. React is the single source of truth.

**Cons:** More boilerplate. Every input needs `value` + `onChange`.

## Uncontrolled Components

In an uncontrolled component, the DOM owns the form data. React accesses it via refs only when needed (usually on submit):

```jsx
function SimpleForm() {
  const nameRef  = useRef(null);
  const emailRef = useRef(null);

  function handleSubmit(e) {
    e.preventDefault();
    const data = {
      name:  nameRef.current.value,
      email: emailRef.current.value,
    };
    console.log(data);
  }

  return (
    <form onSubmit={handleSubmit}>
      <input ref={nameRef}  type="text"  defaultValue="" />
      <input ref={emailRef} type="email" defaultValue="" />
      <button type="submit">Submit</button>
    </form>
  );
}
```

Note: `defaultValue` (not `value`) to avoid making it controlled.

**Pros:** Less boilerplate. Better for large forms where you don't need real-time validation. Slightly better performance.

**Cons:** Harder to validate on change. Can't programmatically reset without manually clearing refs.

## React Hook Form

For complex forms, React Hook Form provides a high-performance controlled API using `register` (which actually uses uncontrolled inputs under the hood):

```jsx
import { useForm } from 'react-hook-form';

function RegistrationForm() {
  const { register, handleSubmit, formState: { errors } } = useForm();

  const onSubmit = (data) => console.log(data);

  return (
    <form onSubmit={handleSubmit(onSubmit)}>
      <input
        {...register('email', {
          required: 'Email is required',
          pattern: { value: /\S+@\S+/, message: 'Invalid email' },
        })}
      />
      {errors.email && <p>{errors.email.message}</p>}

      <input
        type="password"
        {...register('password', {
          required: true,
          minLength: { value: 8, message: 'Min 8 chars' },
        })}
      />
      {errors.password && <p>{errors.password.message}</p>}

      <button type="submit">Register</button>
    </form>
  );
}
```

React Hook Form re-renders only the field that changed (not the whole form) — significant performance gain for large forms.

## When to Use What

| Scenario | Approach |
|---|---|
| Simple form, few fields | Controlled with useState |
| Complex form, 10+ fields | React Hook Form |
| File upload only | Uncontrolled |
| Real-time validation / formatting | Controlled |
| Performance-critical large forms | React Hook Form |
MARKDOWN,
            ],

            // ── LEVEL 5 ────────────────────────────────────────────────────
            [
                'topic_id'          => $t5->id,
                'title'             => 'React Reconciliation & the Virtual DOM',
                'estimated_minutes' => 20,
                'display_order'     => 1,
                'content'           => <<<'MARKDOWN'
## The Virtual DOM

The Virtual DOM (VDOM) is an in-memory representation of the real DOM. React creates a VDOM tree from your JSX, then compares it to the previous VDOM tree on each render.

This comparison is called **reconciliation** (or diffing). React then computes the minimal set of changes needed to update the real DOM — which is significantly slower to manipulate than JavaScript objects.

## The Diffing Algorithm

React's diffing has two key heuristics that make it O(n) instead of O(n³):

**1. Elements of different types produce different trees:**
When a `<div>` changes to a `<section>`, React destroys the entire old subtree (including component state) and builds a new one. Don't change wrapper element types unnecessarily.

**2. Keys allow stable identity across renders:**
```jsx
// Without keys: React can't tell which item moved, so it re-renders all
{items.map(item => <Item value={item.value} />)}

// With keys: React matches by key — only moves/adds/removes what changed
{items.map(item => <Item key={item.id} value={item.value} />)}
```

## React Fiber

Fiber is React's internal reconciliation engine (introduced in React 16). Before Fiber, reconciliation was synchronous — once started, it had to complete before the browser could paint.

Fiber introduced:
- **Interruptible rendering** — React can pause work and resume later
- **Priority scheduling** — high-priority updates (user input) can interrupt low-priority ones (data re-renders)
- **Concurrent features** — the foundation for `Suspense`, `startTransition`, and streaming SSR

## Commit Phase vs Render Phase

Reconciliation has two phases:

**Render phase** (pure, can be interrupted):
- React calls your component functions
- Builds the new VDOM tree
- Diffs against the previous tree
- Calculates what needs to change

**Commit phase** (synchronous, cannot be interrupted):
- Applies DOM mutations
- Runs `useLayoutEffect` synchronously
- Schedules `useEffect` for after paint

This is why side effects must go in `useEffect` — the render phase can be called multiple times (React 18 Strict Mode calls it twice in development to detect impure renders).

## Component Keys & State Resets

Keys can be used intentionally to reset a component's state:

```jsx
// Force a full re-mount (resetting all state) when userId changes
function UserEditor({ userId }) {
  return <Editor key={userId} userId={userId} />;
}
```

Without the key, if `userId` changes, React reuses the same `Editor` instance and tries to update props — but state (draft, scroll position, etc.) persists from the old user. Adding the key forces a clean mount.

## useLayoutEffect vs useEffect

```jsx
// useEffect: runs AFTER the browser paints
useEffect(() => {
  // DOM is updated, browser has painted — user sees the result
});

// useLayoutEffect: runs synchronously AFTER DOM mutations, BEFORE paint
useLayoutEffect(() => {
  // Read layout measurements here (getBoundingClientRect, scrollHeight)
  // Mutate DOM without the user seeing a flash
});
```

Use `useLayoutEffect` only when you need to measure the DOM or make synchronous DOM mutations that would cause visual flicker. Use `useEffect` for everything else.

## Batching in React 18

React 18 introduced automatic batching — multiple state updates from async contexts (setTimeout, Promise.then) are now batched into a single re-render:

```jsx
setTimeout(() => {
  setA(1); // React 17: two renders
  setB(2); // React 18: one render (batched)
}, 1000);
```

In React 17, only event handlers were batched. React 18 batches everywhere. Use `flushSync` to opt out when you need an intermediate render.
MARKDOWN,
            ],
            [
                'topic_id'          => $t5->id,
                'title'             => 'State Management: Redux, Zustand & Context Tradeoffs',
                'estimated_minutes' => 20,
                'display_order'     => 2,
                'content'           => <<<'MARKDOWN'
## The State Management Landscape

React gives you two built-in state tools: `useState` (local) and Context (global-ish). For complex applications, dedicated state management libraries solve problems that Context alone doesn't.

## When Context Is Enough

Context is a good fit when:
- State changes infrequently (auth, theme, locale)
- Not many consumers
- The data is read-mostly

Context is a poor fit for:
- Frequently updating state (causes all consumers to re-render)
- Complex state transitions (hard to trace)
- Large applications with many independent state slices

## Redux (Redux Toolkit)

Redux is the most mature solution. Modern Redux uses Redux Toolkit (RTK) which eliminates most boilerplate:

```jsx
import { createSlice, configureStore } from '@reduxjs/toolkit';
import { Provider, useSelector, useDispatch } from 'react-redux';

const counterSlice = createSlice({
  name: 'counter',
  initialState: { value: 0 },
  reducers: {
    increment: state => { state.value += 1; }, // immer makes this safe
    decrement: state => { state.value -= 1; },
    reset:     state => { state.value = 0; },
  },
});

const store = configureStore({
  reducer: { counter: counterSlice.reducer },
});

export const { increment, decrement, reset } = counterSlice.actions;

// Component
function Counter() {
  const count    = useSelector(state => state.counter.value);
  const dispatch = useDispatch();
  return (
    <div>
      <p>{count}</p>
      <button onClick={() => dispatch(increment())}>+</button>
      <button onClick={() => dispatch(decrement())}>-</button>
    </div>
  );
}

// App
function App() {
  return (
    <Provider store={store}>
      <Counter />
    </Provider>
  );
}
```

**Redux strengths:** Predictable, time-travel debugging (Redux DevTools), strict unidirectional data flow, ideal for complex state logic and large teams.

## Zustand

Zustand is a minimal state management library with almost no boilerplate:

```jsx
import { create } from 'zustand';

const useStore = create((set) => ({
  count: 0,
  user:  null,
  increment: ()    => set(state => ({ count: state.count + 1 })),
  setUser:   (user) => set({ user }),
  reset:     ()    => set({ count: 0, user: null }),
}));

// No Provider needed — access from any component
function Counter() {
  const { count, increment } = useStore();
  return <button onClick={increment}>{count}</button>;
}

function Profile() {
  const user = useStore(state => state.user); // only re-renders when user changes
  return <p>{user?.name}</p>;
}
```

**Zustand strengths:** Minimal setup, no Provider, fine-grained subscriptions (each component subscribes to exactly what it needs), works outside React components, DevTools support.

## Jotai / Recoil (Atomic State)

Atomic state libraries define state as small atoms that components subscribe to individually:

```jsx
import { atom, useAtom } from 'jotai';

const countAtom = atom(0);
const userAtom  = atom(null);

function Counter() {
  const [count, setCount] = useAtom(countAtom);
  return <button onClick={() => setCount(c => c + 1)}>{count}</button>;
}
```

Fine-grained: components only re-render when the specific atom they subscribe to changes.

## Choosing a Solution

| Factor | useState/Context | Zustand | Redux Toolkit | Jotai/Recoil |
|---|---|---|---|---|
| Setup complexity | None | Minimal | Low (RTK) | Minimal |
| Boilerplate | None | None | Low | None |
| Debugging | Good | DevTools | Excellent | DevTools |
| Complex state logic | Limited | Manual | Reducers | Manual |
| Team size | Small | Any | Large | Any |
| Fine-grained updates | No | Yes | No | Yes |

**Guideline:**
- Small/medium apps: `useState` + Context + React Query
- Medium/large: Zustand + React Query
- Large, complex, teams: Redux Toolkit + RTK Query
MARKDOWN,
            ],
            [
                'topic_id'          => $t5->id,
                'title'             => 'React Testing: Unit, Integration & End-to-End',
                'estimated_minutes' => 20,
                'display_order'     => 3,
                'content'           => <<<'MARKDOWN'
## The Testing Pyramid

Three levels of tests, each with a different cost/value ratio:

```
       /▲\
      / E2E \       ← slow, expensive, tests real browser
     /-------\
    /Integration\   ← medium speed, tests components working together
   /-------------\
  /   Unit Tests   \ ← fast, cheap, tests small pieces in isolation
 /─────────────────\
```

**For React apps:** Integration tests (testing components with their real children and hooks) give the best return. They test user behaviour, not implementation details.

## Vitest + React Testing Library

The modern testing stack for React + Vite:

```bash
npm install -D vitest @testing-library/react @testing-library/jest-dom @testing-library/user-event jsdom
```

**React Testing Library philosophy:** Test what the user sees and does, not how the component is implemented. Find elements by role, label, or text — not by class names or component structure.

## Writing Component Tests

```jsx
// Counter.test.tsx
import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import Counter from './Counter';

describe('Counter', () => {
  it('starts at zero', () => {
    render(<Counter />);
    expect(screen.getByText('Count: 0')).toBeInTheDocument();
  });

  it('increments on click', async () => {
    const user = userEvent.setup();
    render(<Counter />);

    await user.click(screen.getByRole('button', { name: '+1' }));

    expect(screen.getByText('Count: 1')).toBeInTheDocument();
  });

  it('does not go below zero', async () => {
    const user = userEvent.setup();
    render(<Counter />);

    await user.click(screen.getByRole('button', { name: '-1' }));

    expect(screen.getByText('Count: 0')).toBeInTheDocument();
  });
});
```

## Testing Async Components

```jsx
// UserProfile.test.tsx
import { render, screen, waitFor } from '@testing-library/react';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import UserProfile from './UserProfile';

// Mock the fetch
vi.mock('../api/users', () => ({
  getUser: vi.fn(() => Promise.resolve({ id: 1, name: 'Alice', role: 'Engineer' })),
}));

function wrapper({ children }) {
  const qc = new QueryClient({ defaultOptions: { queries: { retry: false } } });
  return <QueryClientProvider client={qc}>{children}</QueryClientProvider>;
}

it('displays user name after loading', async () => {
  render(<UserProfile userId={1} />, { wrapper });

  expect(screen.getByText(/loading/i)).toBeInTheDocument();

  await waitFor(() => {
    expect(screen.getByText('Alice')).toBeInTheDocument();
  });
});
```

## Testing Forms

```jsx
it('shows error when email is invalid', async () => {
  const user = userEvent.setup();
  render(<LoginForm />);

  await user.type(screen.getByLabelText(/email/i), 'notanemail');
  await user.click(screen.getByRole('button', { name: /submit/i }));

  expect(screen.getByText(/invalid email/i)).toBeInTheDocument();
});

it('submits with valid data', async () => {
  const onSubmit = vi.fn();
  const user = userEvent.setup();
  render(<LoginForm onSubmit={onSubmit} />);

  await user.type(screen.getByLabelText(/email/i), 'alice@example.com');
  await user.type(screen.getByLabelText(/password/i), 'password123');
  await user.click(screen.getByRole('button', { name: /submit/i }));

  expect(onSubmit).toHaveBeenCalledWith({
    email: 'alice@example.com',
    password: 'password123',
  });
});
```

## End-to-End with Playwright

E2E tests run in a real browser:

```js
// tests/login.spec.ts
import { test, expect } from '@playwright/test';

test('user can log in', async ({ page }) => {
  await page.goto('/login');

  await page.getByLabel('Email').fill('alice@example.com');
  await page.getByLabel('Password').fill('password123');
  await page.getByRole('button', { name: 'Log in' }).click();

  await expect(page).toHaveURL('/dashboard');
  await expect(page.getByText('Welcome, Alice')).toBeVisible();
});
```

## What NOT to Test

- Implementation details (state variable names, internal methods)
- Third-party library internals
- Snapshot tests of JSX structure (they break on any UI change and provide little signal)
- Every single component in isolation (prefer integration tests of feature flows)
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

        $this->command->info('Lessons seeded for all 5 React levels.');
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
        $this->command->info("React Level 4: {$count} questions total.");
    }

    private function level4Questions(): array
    {
        return [
            [
                'question'    => 'What is the Compound Component pattern in React and when should you use it?',
                'explanation' => 'The Compound Component pattern groups related components that share implicit state via Context. The parent owns the state and exposes it to children through a Provider — children can then communicate without prop drilling. Use it when you want a flexible, composable API where callers control the structure (e.g. Tabs, Accordion, Select, Menu). The key benefit is that consumers can rearrange sub-components freely without changing the parent.',
                'options' => [
                    ['text' => 'A pattern where related components share implicit state via Context, giving callers control over composition', 'correct' => true],
                    ['text' => 'A pattern that combines multiple class components into a single function component', 'correct' => false],
                    ['text' => 'A pattern where one component renders inside another using the children prop only', 'correct' => false],
                    ['text' => 'A pattern for memoizing the output of expensive component trees', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does React.memo do and when should you use it?',
                'explanation' => 'React.memo is a higher-order component that wraps a component and skips re-rendering it when its props have not changed (shallow comparison). It is useful when: (1) the component is expensive to render, (2) it re-renders frequently due to parent re-renders, (3) its props rarely change. It must be paired with useCallback for function props — otherwise the parent creates a new function reference every render, defeating the memo. Avoid adding it everywhere — the comparison has a cost and most renders are cheap.',
                'options' => [
                    ['text' => 'Skips re-rendering when props are shallowly equal — useful for expensive components with stable props', 'correct' => true],
                    ['text' => 'Memoizes the return value of a function inside a component', 'correct' => false],
                    ['text' => 'Prevents a component from mounting more than once', 'correct' => false],
                    ['text' => 'Caches the component definition so React does not re-parse it', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the render props pattern and how does it compare to custom hooks?',
                'explanation' => 'The render props pattern passes a function as a prop (or children) that a component calls to let the caller control rendering while the component owns logic. Custom hooks achieve the same goal (sharing stateful logic) with less complexity — they extract logic into a plain function without wrapping the component tree. Modern React prefers custom hooks over render props. Render props still appear in libraries (react-table, Downshift) where the library component needs full control over rendering structure.',
                'options' => [
                    ['text' => 'Render props share logic by passing a function prop; custom hooks share logic as plain functions — hooks are simpler and preferred', 'correct' => true],
                    ['text' => 'Render props are for class components; custom hooks replace them only in function components', 'correct' => false],
                    ['text' => 'They are identical — render props were renamed to custom hooks in React 16.8', 'correct' => false],
                    ['text' => 'Custom hooks cannot share stateful logic; render props are required for that', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is virtualisation (windowing) in React lists and why is it important?',
                'explanation' => 'Virtualisation (windowing) renders only the items currently visible in the viewport instead of the entire list. For a list of 10,000 items, React would normally create 10,000 DOM nodes — causing slow initial paint, high memory usage, and sluggish scrolling. Libraries like react-window or TanStack Virtual render only ~20-30 visible items at a time, reusing DOM nodes as the user scrolls. This reduces the DOM size from thousands of nodes to a fixed small number, dramatically improving performance.',
                'options' => [
                    ['text' => 'Only renders items visible in the viewport — reduces DOM nodes from thousands to a fixed small number', 'correct' => true],
                    ['text' => 'Loads list items from the server one page at a time using pagination', 'correct' => false],
                    ['text' => 'Renders a virtual copy of the list in a Web Worker to avoid blocking the main thread', 'correct' => false],
                    ['text' => 'Caches list items in localStorage to avoid re-fetching', 'correct' => false],
                ],
            ],
            [
                'question'    => 'In React 18, what does startTransition do and when should you use it?',
                'explanation' => 'startTransition marks a state update as non-urgent (a "transition"). React can interrupt and deprioritise it in favour of urgent updates (user input, animations). This keeps the UI responsive during expensive re-renders. Use it for: slow list filtering/sorting, tab switching that triggers heavy rendering, or any update that does not need to be synchronous. The useTransition hook gives you an isPending flag to show a loading indicator while the transition is in progress.',
                'options' => [
                    ['text' => 'Marks a state update as non-urgent so React can interrupt it to keep the UI responsive', 'correct' => true],
                    ['text' => 'Delays a state update by a fixed timeout to prevent flicker', 'correct' => false],
                    ['text' => 'Starts a CSS transition animation on the component when it updates', 'correct' => false],
                    ['text' => 'Moves a state update off the main thread using a Web Worker', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between controlled and uncontrolled form components in React?',
                'explanation' => 'In a controlled component, React state drives the input value — the component renders the form and is the single source of truth. Every change goes through setState. In an uncontrolled component, the DOM owns the value — React accesses it via a ref only when needed (e.g. on submit). Controlled gives you real-time validation, transformation, and easy reset. Uncontrolled has less boilerplate and slightly better performance. React Hook Form uses uncontrolled inputs internally but gives you a controlled-like API.',
                'options' => [
                    ['text' => 'Controlled: React state drives value + onChange; Uncontrolled: DOM owns value, accessed via ref on submit', 'correct' => true],
                    ['text' => 'Controlled: form data is validated; Uncontrolled: form data is not validated', 'correct' => false],
                    ['text' => 'Controlled: for class components; Uncontrolled: for function components', 'correct' => false],
                    ['text' => 'Controlled: uses defaultValue; Uncontrolled: uses value', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is code splitting in React and how is it implemented?',
                'explanation' => 'Code splitting breaks the JavaScript bundle into smaller chunks loaded on demand. React implements this with React.lazy() + dynamic import(), wrapped in Suspense. The most impactful form is route-based splitting — each page route is a separate chunk, so users only download the JS for pages they visit. This reduces the initial bundle size and improves Time-to-Interactive (TTI). Vite and webpack handle the actual chunking; React.lazy() tells them where the split points are.',
                'options' => [
                    ['text' => 'Breaking the bundle into lazy-loaded chunks via React.lazy() + import() — reduces initial bundle and TTI', 'correct' => true],
                    ['text' => 'Splitting component logic into multiple smaller files for code organisation', 'correct' => false],
                    ['text' => 'Running React rendering in multiple threads to split CPU work', 'correct' => false],
                    ['text' => 'Separating CSS from JS into different bundle files', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is state colocation in React and why does it improve performance?',
                'explanation' => 'State colocation means placing state as close to where it is used as possible. If state lives higher in the tree than necessary, every state update re-renders the parent AND all its children — including siblings that don\'t use the state. Moving state down to the component (or small subtree) that actually uses it limits re-renders to just that subtree. This is often the simplest and most effective React performance optimisation — no memoisation needed.',
                'options' => [
                    ['text' => 'Placing state as close as possible to where it is used — limits re-renders to the minimal subtree', 'correct' => true],
                    ['text' => 'Storing all state in a global Redux store so components can share it', 'correct' => false],
                    ['text' => 'Co-locating state files next to component files in the folder structure', 'correct' => false],
                    ['text' => 'Using useRef instead of useState to avoid re-renders entirely', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the key prop do in React and what happens if you use array index as the key?',
                'explanation' => 'The key prop gives React a stable identity for list items across renders. React uses keys to match old and new trees — if a key moves, React moves the DOM node; if a key is new, React creates it; if a key disappears, React removes it. Using array index as key causes bugs when the list is reordered, filtered, or items are inserted/deleted — React reuses the wrong DOM node, causing state corruption and incorrect animations. Always use a stable unique ID from your data.',
                'options' => [
                    ['text' => 'Gives list items stable identity — index as key causes state bugs when list is reordered or filtered', 'correct' => true],
                    ['text' => 'Passes a unique prop to the child component for identification', 'correct' => false],
                    ['text' => 'Prevents React from re-rendering the item when the key matches the previous value', 'correct' => false],
                    ['text' => 'Marks the element as a target for React.memo optimisation', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is useCallback and when should it be used?',
                'explanation' => 'useCallback returns a memoized version of a function — the same function reference is returned across renders unless its dependencies change. It is useful when: (1) passing callbacks to React.memo-wrapped children (a new function reference each render defeats the memo), (2) including a function in a useEffect dependency array (without useCallback, the effect re-runs every render). Without these specific scenarios, useCallback adds overhead for no benefit — don\'t use it pre-emptively.',
                'options' => [
                    ['text' => 'Memoizes a function reference — useful for memo-wrapped children and useEffect deps to prevent unnecessary re-runs', 'correct' => true],
                    ['text' => 'Calls a callback function only when the component first mounts', 'correct' => false],
                    ['text' => 'Converts an async function to a synchronous callback', 'correct' => false],
                    ['text' => 'Caches the return value of the callback function', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of useLayoutEffect and how does it differ from useEffect?',
                'explanation' => 'useLayoutEffect fires synchronously after all DOM mutations but BEFORE the browser paints. useEffect fires after the browser paints. Use useLayoutEffect when you need to read layout measurements (getBoundingClientRect, scrollHeight) or make DOM mutations that would cause visible flicker if delayed. For everything else (data fetching, subscriptions, logging), use useEffect — it does not block painting and keeps the UI responsive. In SSR environments, useLayoutEffect may cause warnings because the server has no DOM.',
                'options' => [
                    ['text' => 'useLayoutEffect fires before paint — for DOM measurements and mutations that would flicker; useEffect fires after paint', 'correct' => true],
                    ['text' => 'useLayoutEffect is the async version of useEffect', 'correct' => false],
                    ['text' => 'useLayoutEffect runs during the render phase; useEffect runs during the commit phase', 'correct' => false],
                    ['text' => 'They are identical except useLayoutEffect runs twice in development', 'correct' => false],
                ],
            ],
            [
                'question'    => 'In the context of React forms, what does React Hook Form improve over plain controlled components?',
                'explanation' => 'React Hook Form uses uncontrolled inputs internally — it registers inputs with refs rather than driving them with state. This means only the changed field triggers a re-render, not the entire form on every keystroke. For a 20-field form, this is a significant performance difference. It also provides built-in validation, error management, and a clean schema-based validation integration (with Zod/Yup). The API is ergonomic while the internals stay fast.',
                'options' => [
                    ['text' => 'Uses uncontrolled inputs internally — re-renders only changed fields, not the whole form on every keystroke', 'correct' => true],
                    ['text' => 'Automatically submits the form to an API without needing a submit handler', 'correct' => false],
                    ['text' => 'Replaces useState entirely, removing the need for any React state in forms', 'correct' => false],
                    ['text' => 'Renders form fields server-side to avoid client-side validation', 'correct' => false],
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
        $this->command->info("React Level 5: {$count} questions total.");
    }

    private function level5Questions(): array
    {
        return [
            [
                'question'    => 'What is the React Virtual DOM and what problem does it solve?',
                'explanation' => 'The Virtual DOM is an in-memory JavaScript representation of the real DOM. Direct DOM manipulation is slow — reflows and repaints are expensive. React maintains a VDOM tree, and on each render it diffs the new tree against the previous one (reconciliation). Only the minimal set of real DOM changes is applied. This makes React efficient because JavaScript object comparison is much faster than DOM manipulation.',
                'options' => [
                    ['text' => 'An in-memory JS object tree that React diffs before applying minimal real DOM changes', 'correct' => true],
                    ['text' => 'A copy of the DOM stored in localStorage for offline use', 'correct' => false],
                    ['text' => 'A virtual machine that runs React components outside the browser', 'correct' => false],
                    ['text' => 'A Shadow DOM that React uses to isolate component styles', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is React Fiber and what did it enable that the previous reconciler could not do?',
                'explanation' => 'React Fiber (introduced in React 16) is the reconciliation engine that replaced the original synchronous stack reconciler. The original reconciler could not be interrupted — once it started reconciling, it ran to completion, potentially blocking the main thread for hundreds of milliseconds on large trees. Fiber introduced interruptible, incremental rendering: React can pause work, prioritise urgent updates (like user input), and resume low-priority work later. Fiber is the foundation for Concurrent Mode, Suspense, startTransition, and streaming SSR.',
                'options' => [
                    ['text' => 'Interruptible incremental rendering — React can pause and reprioritise work; the old reconciler could not be interrupted', 'correct' => true],
                    ['text' => 'Faster component mounting by pre-compiling JSX to machine code', 'correct' => false],
                    ['text' => 'Parallel rendering across multiple CPU cores', 'correct' => false],
                    ['text' => 'Server-side rendering support, which was impossible without Fiber', 'correct' => false],
                ],
            ],
            [
                'question'    => 'In React reconciliation, what are the two main heuristics that make diffing O(n) instead of O(n³)?',
                'explanation' => 'React\'s diffing algorithm has two key assumptions: (1) Elements of different types produce entirely different trees — React destroys and rebuilds rather than trying to diff across types. (2) Keys give elements stable identity across renders — React matches elements by key, so it can efficiently detect moves, additions, and deletions. These two assumptions allow React to diff in O(n) time. Without them, the optimal tree diff algorithm is O(n³) — impractical for even moderate-size UIs.',
                'options' => [
                    ['text' => 'Different element types → destroy and rebuild; keys → stable identity for efficient moves/adds/removes', 'correct' => true],
                    ['text' => 'Memoization of subtrees; component identity via display names', 'correct' => false],
                    ['text' => 'Lazy evaluation of children; batching all updates into one pass', 'correct' => false],
                    ['text' => 'Props comparison by reference; state comparison by deep equality', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is automatic batching in React 18 and how does it differ from React 17?',
                'explanation' => 'In React 17, batching (grouping multiple state updates into one re-render) only happened inside React event handlers. Updates inside setTimeout, Promise.then, or native event listeners triggered separate re-renders for each setState call. React 18 introduced automatic batching everywhere — multiple state updates in any async context are now batched into a single re-render. This reduces unnecessary renders. Use flushSync() to opt out and force an immediate synchronous render when needed.',
                'options' => [
                    ['text' => 'React 18 batches updates everywhere (async too); React 17 only batched inside React event handlers', 'correct' => true],
                    ['text' => 'React 18 batches DOM mutations; React 17 batched state updates', 'correct' => false],
                    ['text' => 'React 18 uses a worker thread for batching; React 17 batched on the main thread', 'correct' => false],
                    ['text' => 'Automatic batching replaces useReducer — no difference in re-render count', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between Zustand and Redux Toolkit for React state management?',
                'explanation' => 'Zustand is minimal — you create a store with a single `create` call, no Provider needed, and components subscribe to slices of state directly. Redux Toolkit is more structured — it uses a Provider, reducers, actions, and slices, providing strict unidirectional data flow and excellent DevTools with time-travel debugging. Zustand is ideal for most apps (less boilerplate, fine-grained subscriptions). Redux Toolkit fits large teams that need strict conventions, an audit trail of state changes, and complex state logic with middleware.',
                'options' => [
                    ['text' => 'Zustand: minimal, no Provider, fine-grained subscriptions; Redux Toolkit: structured, strict unidirectional flow, excellent DevTools', 'correct' => true],
                    ['text' => 'Zustand is for client state; Redux Toolkit is for server state', 'correct' => false],
                    ['text' => 'Redux Toolkit is faster because it uses Web Workers; Zustand runs on the main thread', 'correct' => false],
                    ['text' => 'They are identical in functionality — the choice is purely stylistic', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the React Testing Library philosophy and why does it discourage testing implementation details?',
                'explanation' => 'React Testing Library (RTL) is built around the principle: "The more your tests resemble the way your software is used, the more confidence they give you." It encourages querying by role, label, and text — things users see and interact with. Testing implementation details (state variable names, internal methods, CSS class names) means tests break when you refactor even if the behaviour is unchanged — low signal, high maintenance cost. Tests should verify that the component works from the user\'s perspective.',
                'options' => [
                    ['text' => 'Tests should resemble user behaviour (role/label/text queries) — implementation detail tests break on refactor without catching real bugs', 'correct' => true],
                    ['text' => 'RTL discourages testing because snapshot tests are faster and more reliable', 'correct' => false],
                    ['text' => 'RTL only supports class components — function component internals cannot be tested', 'correct' => false],
                    ['text' => 'RTL discourages async tests because they are too slow for CI pipelines', 'correct' => false],
                ],
            ],
            [
                'question'    => 'When would you use useReducer instead of useState in React?',
                'explanation' => 'useReducer is preferable to useState when: (1) state has complex transitions (multiple sub-values that change together), (2) the next state depends on the previous in non-trivial ways, (3) you want to centralise update logic for testability, (4) you have multiple useState calls that always change together. It follows the Redux pattern — dispatch an action, reducer computes the next state. For simple independent values, useState is cleaner. useReducer shines for form state, multi-step wizards, or any state machine.',
                'options' => [
                    ['text' => 'When state has complex transitions, multiple related sub-values, or you need centralised testable update logic', 'correct' => true],
                    ['text' => 'When you need state to persist across page reloads', 'correct' => false],
                    ['text' => 'When the component has more than 3 state variables', 'correct' => false],
                    ['text' => 'useReducer is always better — useState should be avoided in production code', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the Context API\'s main performance limitation and how do you mitigate it?',
                'explanation' => 'Every component that calls useContext(MyContext) re-renders whenever the context value changes — even if the component only uses one property of a large context object. This is because React compares context values by reference. Mitigations: (1) Split context into multiple smaller contexts (auth separate from theme separate from locale). (2) Memoize the context value with useMemo. (3) Use a library like Zustand or Jotai which support fine-grained subscriptions — components only re-render when their specific slice of state changes.',
                'options' => [
                    ['text' => 'All consumers re-render on any value change — split context, memoize value, or use Zustand/Jotai for fine-grained subscriptions', 'correct' => true],
                    ['text' => 'Context values are not cached — every render re-fetches from the Provider', 'correct' => false],
                    ['text' => 'Context only works with class components and is slow in function components', 'correct' => false],
                    ['text' => 'Context causes memory leaks because consumers are never garbage collected', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is Suspense in React and what can it be used for beyond lazy loading?',
                'explanation' => 'Suspense is a component that catches a "suspended" child — a component that is not ready to render yet — and shows a fallback until it is ready. Initially it worked only with React.lazy() for code splitting. In React 18+, Suspense supports data fetching when used with libraries that implement the Suspense protocol (React Query, Relay, SWR with Suspense mode). It also enables streaming SSR — React can send HTML progressively, "streaming" content in chunks as data resolves.',
                'options' => [
                    ['text' => 'Shows a fallback while a child is not ready — works for lazy loading, data fetching (with supported libraries), and streaming SSR', 'correct' => true],
                    ['text' => 'Only works with React.lazy() — data fetching requires useEffect instead', 'correct' => false],
                    ['text' => 'Suspends rendering on the server and resumes on the client (hydration only)', 'correct' => false],
                    ['text' => 'Provides error handling — Suspense replaces error boundaries', 'correct' => false],
                ],
            ],
            [
                'question'    => 'In React testing, what is the difference between unit tests and integration tests, and which gives more value for UI components?',
                'explanation' => 'Unit tests test a single function/component in isolation — dependencies are mocked. Integration tests test multiple components or a component with its real hooks and children, verifying they work together. For UI components, integration tests give more value: they test real user flows (type in input → see error → submit → see success), catch interaction bugs between components, and are less brittle than unit tests that test internal implementation. React Testing Library is designed for integration-style testing of components.',
                'options' => [
                    ['text' => 'Integration tests give more value for UI — they test real user flows across components with fewer brittle assumptions', 'correct' => true],
                    ['text' => 'Unit tests always give more value because they are faster and more isolated', 'correct' => false],
                    ['text' => 'Neither — only E2E tests (Playwright/Cypress) are meaningful for UI', 'correct' => false],
                    ['text' => 'They are equivalent — the testing pyramid applies equally to all software types', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is server-side rendering (SSR) in React and what problem does it solve?',
                'explanation' => 'In a standard React SPA, the server sends an empty HTML shell and a large JS bundle. The browser downloads, parses, and executes the JS before the user sees any content — poor Time-to-First-Paint (TTFP) and bad SEO (crawlers see empty HTML). SSR renders the component tree on the server and sends pre-rendered HTML. The user sees content immediately. React then "hydrates" the HTML (attaches event listeners) on the client. Frameworks like Next.js provide SSR, ISR (incremental static regeneration), and streaming SSR.',
                'options' => [
                    ['text' => 'Renders HTML on the server — improves TTFP and SEO; React hydrates on the client to attach interactivity', 'correct' => true],
                    ['text' => 'Runs React components in a Node.js server process permanently, replacing the browser', 'correct' => false],
                    ['text' => 'Caches component output on a CDN to eliminate server round-trips', 'correct' => false],
                    ['text' => 'Renders components in a Web Worker to free the main thread', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of forwardRef in React and when do you need it?',
                'explanation' => 'By default, React does not allow a parent to access a child component\'s internal DOM node via a ref — refs on components refer to the component instance (null for function components). forwardRef lets a component accept a ref from its parent and forward it to an internal DOM element. You need it when building reusable input components, modals, or any component where the parent needs to call focus(), scroll(), or getBoundingClientRect() on the underlying DOM node.',
                'options' => [
                    ['text' => 'Allows a component to accept a parent-provided ref and forward it to an internal DOM element', 'correct' => true],
                    ['text' => 'Forwards props from a parent to all child components automatically', 'correct' => false],
                    ['text' => 'Allows a child component to send data back to its parent via a ref', 'correct' => false],
                    ['text' => 'A performance optimisation that defers ref assignment until after paint', 'correct' => false],
                ],
            ],
        ];
    }
}
