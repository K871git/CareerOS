<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Database\Seeder;

class ReactAdvancedQuestionsSeeder extends Seeder
{
    public function run(): void
    {
        $track = LearningTrack::firstOrCreate(
            ['slug' => 'frontend-engineering'],
            ['title' => 'Frontend Engineering', 'display_order' => 2]
        );

        $subject = Subject::firstOrCreate(
            ['slug' => 'react'],
            ['learning_track_id' => $track->id, 'title' => 'React', 'display_order' => 4]
        );

        $topic = Topic::firstOrCreate(
            ['slug' => 'react-advanced'],
            ['subject_id' => $subject->id, 'title' => 'React Advanced', 'display_order' => 3]
        );

        Question::where('topic_id', $topic->id)->delete();

        foreach ($this->questions() as $qData) {
            $question = Question::create([
                'topic_id'   => $topic->id,
                'question'   => $qData['question'],
                'type'       => 'mcq',
                'difficulty' => 'Hard',
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
        $this->command->info("React Advanced: {$count} questions total.");
    }

    private function questions(): array
    {
        return [
            // --- useReducer ---
            [
                'question' => 'When should you prefer useReducer over useState?',
                'options'  => [
                    ['text' => 'When state logic is complex, involves multiple sub-values, or the next state depends on the previous in non-trivial ways', 'correct' => true],
                    ['text' => 'Whenever a component needs more than two state variables', 'correct' => false],
                    ['text' => 'Only when building forms with many fields', 'correct' => false],
                    ['text' => 'useReducer is always preferred because it is faster', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does the dispatch function returned by useReducer do?',
                'options'  => [
                    ['text' => 'Sends an action object to the reducer function, which computes and returns the next state', 'correct' => true],
                    ['text' => 'Directly mutates the state object', 'correct' => false],
                    ['text' => 'Schedules an async state update via a Promise', 'correct' => false],
                    ['text' => 'Broadcasts a custom DOM event to the document', 'correct' => false],
                ],
            ],

            // --- Context + useReducer ---
            [
                'question' => 'What is the benefit of combining useReducer with Context?',
                'options'  => [
                    ['text' => 'You can provide a global dispatch function and state to deeply nested components without prop drilling, approximating a lightweight Redux', 'correct' => true],
                    ['text' => 'It makes useReducer asynchronous automatically', 'correct' => false],
                    ['text' => 'It eliminates re-renders across the entire component tree', 'correct' => false],
                    ['text' => 'Context automatically persists reducer state to localStorage', 'correct' => false],
                ],
            ],

            // --- React.lazy + Suspense ---
            [
                'question' => 'What does React.lazy() enable?',
                'options'  => [
                    ['text' => 'Dynamic import of a component so its code is loaded in a separate bundle only when it is first rendered', 'correct' => true],
                    ['text' => 'Deferring state updates until the browser is idle', 'correct' => false],
                    ['text' => 'Pre-fetching all routes at build time', 'correct' => false],
                    ['text' => 'Lazy evaluation of prop values', 'correct' => false],
                ],
            ],
            [
                'question' => 'What must you wrap a React.lazy() component with to handle the loading state?',
                'options'  => [
                    ['text' => '<Suspense fallback={<Spinner />}>', 'correct' => true],
                    ['text' => '<ErrorBoundary>', 'correct' => false],
                    ['text' => '<React.StrictMode>', 'correct' => false],
                    ['text' => '<Await resolve={promise}>', 'correct' => false],
                ],
            ],

            // --- forwardRef ---
            [
                'question' => 'What problem does React.forwardRef solve?',
                'options'  => [
                    ['text' => 'It lets a parent component pass a ref down to a DOM node inside a child functional component', 'correct' => true],
                    ['text' => 'It forwards props from parent to child automatically', 'correct' => false],
                    ['text' => 'It enables a child to call parent methods via a ref', 'correct' => false],
                    ['text' => 'It creates a ref that is shared between all instances of a component', 'correct' => false],
                ],
            ],
            [
                'question' => 'Which hook lets a component expose a custom imperative API to a parent via a ref?',
                'options'  => [
                    ['text' => 'useImperativeHandle', 'correct' => true],
                    ['text' => 'useRef', 'correct' => false],
                    ['text' => 'forwardRef', 'correct' => false],
                    ['text' => 'useExpose', 'correct' => false],
                ],
            ],

            // --- Higher-Order Components ---
            [
                'question' => 'What is a Higher-Order Component (HOC) in React?',
                'options'  => [
                    ['text' => 'A function that takes a component as input and returns a new enhanced component', 'correct' => true],
                    ['text' => 'A component that renders at the top of the React tree', 'correct' => false],
                    ['text' => 'A component that provides context to its children', 'correct' => false],
                    ['text' => 'A component with a higher priority in the rendering queue', 'correct' => false],
                ],
            ],
            [
                'question' => 'What naming convention is used for Higher-Order Components?',
                'options'  => [
                    ['text' => 'The "with" prefix: e.g., withAuth(Component)', 'correct' => true],
                    ['text' => 'The "hoc" prefix: e.g., hocAuth(Component)', 'correct' => false],
                    ['text' => 'The "use" prefix: e.g., useAuth(Component)', 'correct' => false],
                    ['text' => 'No convention — any name is acceptable', 'correct' => false],
                ],
            ],

            // --- Render Props ---
            [
                'question' => 'What is the "render prop" pattern?',
                'options'  => [
                    ['text' => 'A technique where a component receives a function as a prop and calls it to determine what to render, sharing logic without HOCs', 'correct' => true],
                    ['text' => 'A prop that accepts a pre-rendered JSX element', 'correct' => false],
                    ['text' => 'A prop that triggers a re-render when changed', 'correct' => false],
                    ['text' => 'The children prop when children is a string', 'correct' => false],
                ],
            ],

            // --- Concurrent Features ---
            [
                'question' => 'What does the useTransition hook do in React 18?',
                'options'  => [
                    ['text' => 'Marks a state update as non-urgent so React can interrupt it to keep the UI responsive', 'correct' => true],
                    ['text' => 'Animates a component when it mounts or unmounts', 'correct' => false],
                    ['text' => 'Defers an effect until after the browser has painted', 'correct' => false],
                    ['text' => 'Transitions the component between error and success states', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does useDeferredValue do?',
                'options'  => [
                    ['text' => 'Returns a deferred (possibly stale) copy of a value so expensive renders triggered by it do not block urgent updates', 'correct' => true],
                    ['text' => 'Defers a function call until the component unmounts', 'correct' => false],
                    ['text' => 'Caches an API response and returns it asynchronously', 'correct' => false],
                    ['text' => 'Delays a useState setter call by one render cycle', 'correct' => false],
                ],
            ],

            // --- Fiber architecture ---
            [
                'question' => 'What is React Fiber?',
                'options'  => [
                    ['text' => 'The internal reconciliation engine rewrite introduced in React 16 that enables incremental rendering and prioritized updates', 'correct' => true],
                    ['text' => 'A third-party library for React animations', 'correct' => false],
                    ['text' => 'The name of React\'s virtual DOM', 'correct' => false],
                    ['text' => 'React\'s built-in state management solution', 'correct' => false],
                ],
            ],

            // --- Profiler ---
            [
                'question' => 'What is the React Profiler used for?',
                'options'  => [
                    ['text' => 'Measuring how often and how long components take to render, to identify performance bottlenecks', 'correct' => true],
                    ['text' => 'Profiling network requests made by useEffect', 'correct' => false],
                    ['text' => 'Tracking which users visit each route', 'correct' => false],
                    ['text' => 'Linting JSX code for accessibility issues', 'correct' => false],
                ],
            ],

            // --- Server Components ---
            [
                'question' => 'What is the key characteristic of React Server Components?',
                'options'  => [
                    ['text' => 'They render on the server only and send no JavaScript to the client, reducing bundle size', 'correct' => true],
                    ['text' => 'They use WebSockets to stream state from server to client', 'correct' => false],
                    ['text' => 'They replace useEffect for server-side data fetching in traditional React apps', 'correct' => false],
                    ['text' => 'They are React components that run inside a Node.js Lambda function', 'correct' => false],
                ],
            ],
            [
                'question' => 'Which of the following CANNOT be used inside a React Server Component?',
                'options'  => [
                    ['text' => 'useState, useEffect, or any other hooks', 'correct' => true],
                    ['text' => 'async/await for data fetching', 'correct' => false],
                    ['text' => 'Importing other Server Components', 'correct' => false],
                    ['text' => 'Accessing a database directly', 'correct' => false],
                ],
            ],

            // --- Testing ---
            [
                'question' => 'What does React Testing Library encourage you to test?',
                'options'  => [
                    ['text' => 'Component behaviour from the user\'s perspective — querying by accessible roles, text, and labels rather than implementation details', 'correct' => true],
                    ['text' => 'Internal state values and private methods', 'correct' => false],
                    ['text' => 'The virtual DOM structure directly', 'correct' => false],
                    ['text' => 'Snapshot testing of every component by default', 'correct' => false],
                ],
            ],
            [
                'question' => 'Which function from React Testing Library simulates user interactions?',
                'options'  => [
                    ['text' => 'userEvent (from @testing-library/user-event)', 'correct' => true],
                    ['text' => 'fireEvent is the only option', 'correct' => false],
                    ['text' => 'simulate from Enzyme', 'correct' => false],
                    ['text' => 'trigger from jest-dom', 'correct' => false],
                ],
            ],

            // --- Zustand / State Management ---
            [
                'question' => 'What distinguishes Zustand from Redux for React state management?',
                'options'  => [
                    ['text' => 'Zustand has a minimal API with no boilerplate (no actions/reducers/providers required), while Redux requires more setup', 'correct' => true],
                    ['text' => 'Zustand only works with class components', 'correct' => false],
                    ['text' => 'Zustand does not support async operations', 'correct' => false],
                    ['text' => 'Zustand stores state in the browser URL instead of memory', 'correct' => false],
                ],
            ],

            // --- Memoization pitfalls ---
            [
                'question' => 'What is a common mistake when overusing useMemo and useCallback?',
                'options'  => [
                    ['text' => 'The overhead of memoization (storing the cached value, running comparisons) can cost more than the re-render it was meant to prevent', 'correct' => true],
                    ['text' => 'They cause infinite loops when used together', 'correct' => false],
                    ['text' => 'They prevent useEffect from detecting dependency changes', 'correct' => false],
                    ['text' => 'They are deprecated in React 18 and should be removed', 'correct' => false],
                ],
            ],

            // --- Compound Components ---
            [
                'question' => 'What is the Compound Components pattern?',
                'options'  => [
                    ['text' => 'A set of components that work together sharing implicit state via context, giving consumers a flexible compositional API (e.g., <Select><Option /></Select>)', 'correct' => true],
                    ['text' => 'Nesting more than three components deep inside a parent', 'correct' => false],
                    ['text' => 'Combining a class component with a functional component', 'correct' => false],
                    ['text' => 'A HOC that merges multiple components into one', 'correct' => false],
                ],
            ],

            // --- Strict Mode double-invoke ---
            [
                'question' => 'In React 18 StrictMode development, function components and their hooks are intentionally invoked twice. Why?',
                'options'  => [
                    ['text' => 'To detect side effects that break when components are mounted, unmounted, and remounted — catching impure renders', 'correct' => true],
                    ['text' => 'To test concurrent mode performance', 'correct' => false],
                    ['text' => 'To warm up the V8 JIT compiler with the component code', 'correct' => false],
                    ['text' => 'To apply React DevTools instrumentation', 'correct' => false],
                ],
            ],

            // --- Suspense for data ---
            [
                'question' => 'In React 18+ with framework support, Suspense can be used for:',
                'options'  => [
                    ['text' => 'Showing a fallback while waiting for async data to load, not just lazy-loaded components', 'correct' => true],
                    ['text' => 'Only code-splitting — data fetching still requires useEffect', 'correct' => false],
                    ['text' => 'Pausing animations until all data is ready', 'correct' => false],
                    ['text' => 'Replacing Error Boundaries for error handling', 'correct' => false],
                ],
            ],

            // --- Hydration ---
            [
                'question' => 'What is "hydration" in the context of React SSR?',
                'options'  => [
                    ['text' => 'Attaching React event listeners and making the server-rendered HTML interactive on the client', 'correct' => true],
                    ['text' => 'Sending component state from the server to the client via cookies', 'correct' => false],
                    ['text' => 'Re-rendering the entire page from scratch on the client', 'correct' => false],
                    ['text' => 'Fetching JSON data and injecting it into props', 'correct' => false],
                ],
            ],

            // --- key prop trick ---
            [
                'question' => 'Besides lists, what is a practical use of the "key" prop on a single component?',
                'options'  => [
                    ['text' => 'Changing the key forces React to unmount and remount the component, effectively resetting its state', 'correct' => true],
                    ['text' => 'It improves the component\'s CSS specificity', 'correct' => false],
                    ['text' => 'It enables server-side caching of the component output', 'correct' => false],
                    ['text' => 'It links the component to a Redux action', 'correct' => false],
                ],
            ],

            // --- Custom hook design ---
            [
                'question' => 'A well-designed custom hook should:',
                'options'  => [
                    ['text' => 'Encapsulate a single concern, expose a clean API, and not leak internal implementation details', 'correct' => true],
                    ['text' => 'Always return a tuple of [state, setState] to mirror useState', 'correct' => false],
                    ['text' => 'Call at least one useEffect to justify its existence', 'correct' => false],
                    ['text' => 'Accept a component as an argument to behave like a HOC', 'correct' => false],
                ],
            ],

            // --- Batching edge case ---
            [
                'question' => 'Before React 18, where were setState calls NOT automatically batched?',
                'options'  => [
                    ['text' => 'Inside async callbacks such as setTimeout, Promise handlers, or native event listeners', 'correct' => true],
                    ['text' => 'Inside React synthetic event handlers', 'correct' => false],
                    ['text' => 'Inside useEffect cleanup functions', 'correct' => false],
                    ['text' => 'Inside class component lifecycle methods', 'correct' => false],
                ],
            ],

            // --- Zustand selector ---
            [
                'question' => 'In Zustand, what is a "selector" used for when calling the store hook?',
                'options'  => [
                    ['text' => 'To subscribe a component only to a specific slice of the store, avoiding re-renders when unrelated state changes', 'correct' => true],
                    ['text' => 'To filter which actions can be dispatched to the store', 'correct' => false],
                    ['text' => 'To pick which components are allowed to access the store', 'correct' => false],
                    ['text' => 'To memoize the entire store state', 'correct' => false],
                ],
            ],

            // --- React Query / SWR ---
            [
                'question' => 'What is the main benefit of a data-fetching library like React Query or SWR over plain useEffect + fetch?',
                'options'  => [
                    ['text' => 'They manage caching, background re-fetching, loading and error states, deduplication, and stale-while-revalidate out of the box', 'correct' => true],
                    ['text' => 'They make fetch requests faster by using WebSockets instead of HTTP', 'correct' => false],
                    ['text' => 'They automatically generate TypeScript types from API responses', 'correct' => false],
                    ['text' => 'They replace the need for a backend by caching all data in localStorage', 'correct' => false],
                ],
            ],
        ];
    }
}
