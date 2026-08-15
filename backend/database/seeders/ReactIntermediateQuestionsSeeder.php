<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Database\Seeder;

class ReactIntermediateQuestionsSeeder extends Seeder
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
            ['slug' => 'react-intermediate'],
            ['subject_id' => $subject->id, 'title' => 'React Intermediate', 'display_order' => 2]
        );

        $count = 0;
        foreach ($this->questions() as $qData) {
            $exists = Question::where('topic_id', $topic->id)
                ->where('question', $qData['question'])
                ->exists();

            if ($exists) {
                continue;
            }

            $question = Question::create([
                'topic_id'   => $topic->id,
                'question'   => $qData['question'],
                'type'       => 'mcq',
                'difficulty' => 'Medium',
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

        $this->command->info("React Intermediate: {$count} questions total.");
    }

    private function questions(): array
    {
        return [
            // --- useEffect dependencies ---
            [
                'question' => 'What does it mean when a value is listed in the useEffect dependency array?',
                'options'  => [
                    ['text' => 'The effect re-runs whenever any of those values change between renders', 'correct' => true],
                    ['text' => 'Those values are blocked from changing inside the effect', 'correct' => false],
                    ['text' => 'The effect runs before those values are available', 'correct' => false],
                    ['text' => 'Those values are passed as arguments to the effect function', 'correct' => false],
                ],
            ],
            [
                'question' => 'What problem occurs when you omit a variable from the useEffect dependency array that is used inside the effect?',
                'options'  => [
                    ['text' => 'The effect captures a stale closure and uses the outdated value from the initial render', 'correct' => true],
                    ['text' => 'React throws an error at runtime', 'correct' => false],
                    ['text' => 'The effect runs infinitely', 'correct' => false],
                    ['text' => 'The variable is reset to undefined inside the effect', 'correct' => false],
                ],
            ],

            // --- useRef ---
            [
                'question' => 'What does the useRef hook return?',
                'options'  => [
                    ['text' => 'A mutable object with a .current property that persists across renders without causing re-renders', 'correct' => true],
                    ['text' => 'A state variable and a setter function', 'correct' => false],
                    ['text' => 'A reference to the parent component', 'correct' => false],
                    ['text' => 'A callback for accessing context values', 'correct' => false],
                ],
            ],
            [
                'question' => 'Which of the following is a valid use case for useRef?',
                'options'  => [
                    ['text' => 'Accessing a DOM element imperatively (e.g., focusing an input)', 'correct' => true],
                    ['text' => 'Sharing state between sibling components', 'correct' => false],
                    ['text' => 'Persisting async API call results across pages', 'correct' => false],
                    ['text' => 'Subscribing to a context value', 'correct' => false],
                ],
            ],
            [
                'question' => 'Updating ref.current does NOT:',
                'options'  => [
                    ['text' => 'Trigger a component re-render', 'correct' => true],
                    ['text' => 'Persist the value between renders', 'correct' => false],
                    ['text' => 'Work inside event handlers', 'correct' => false],
                    ['text' => 'Allow storing mutable values', 'correct' => false],
                ],
            ],

            // --- useContext ---
            [
                'question' => 'What problem does the useContext hook solve?',
                'options'  => [
                    ['text' => 'Prop drilling — it lets deeply nested components read values from context without passing props through every layer', 'correct' => true],
                    ['text' => 'It replaces useState for all global state management', 'correct' => false],
                    ['text' => 'It automatically fetches data from an API', 'correct' => false],
                    ['text' => 'It syncs component state with localStorage', 'correct' => false],
                ],
            ],
            [
                'question' => 'What do you need to create before you can use useContext?',
                'options'  => [
                    ['text' => 'A context object created with React.createContext()', 'correct' => true],
                    ['text' => 'A Redux store', 'correct' => false],
                    ['text' => 'A global window variable', 'correct' => false],
                    ['text' => 'A custom hook named after the context', 'correct' => false],
                ],
            ],
            [
                'question' => 'When a context value changes, which components re-render?',
                'options'  => [
                    ['text' => 'All components that consume that context via useContext', 'correct' => true],
                    ['text' => 'Only the Provider component', 'correct' => false],
                    ['text' => 'Every component in the application', 'correct' => false],
                    ['text' => 'Only components that are direct children of the Provider', 'correct' => false],
                ],
            ],

            // --- Custom Hooks ---
            [
                'question' => 'What is a custom hook in React?',
                'options'  => [
                    ['text' => 'A regular JavaScript function whose name starts with "use" and that can call other hooks to extract reusable stateful logic', 'correct' => true],
                    ['text' => 'A hook provided by React that is not documented publicly', 'correct' => false],
                    ['text' => 'A class method that wraps useState calls', 'correct' => false],
                    ['text' => 'A hook that only works with external libraries', 'correct' => false],
                ],
            ],
            [
                'question' => 'Why must custom hook names start with "use"?',
                'options'  => [
                    ['text' => 'So that React (and linters) can enforce the rules of hooks for those functions', 'correct' => true],
                    ['text' => 'It is a naming convention but has no technical effect', 'correct' => false],
                    ['text' => 'It prevents the function from being called outside React', 'correct' => false],
                    ['text' => 'It enables automatic memoization of the hook result', 'correct' => false],
                ],
            ],

            // --- React.memo ---
            [
                'question' => 'What does React.memo do?',
                'options'  => [
                    ['text' => 'Wraps a functional component so React skips re-rendering it when its props have not changed (shallow comparison)', 'correct' => true],
                    ['text' => 'Memoizes the return value of an expensive calculation inside a component', 'correct' => false],
                    ['text' => 'Caches component state between navigations', 'correct' => false],
                    ['text' => 'Converts a class component to a functional component automatically', 'correct' => false],
                ],
            ],
            [
                'question' => 'React.memo uses what kind of comparison to decide whether to skip re-rendering?',
                'options'  => [
                    ['text' => 'Shallow comparison of props', 'correct' => true],
                    ['text' => 'Deep (recursive) comparison of props', 'correct' => false],
                    ['text' => 'Reference equality of the entire props object', 'correct' => false],
                    ['text' => 'No comparison — it always skips re-rendering', 'correct' => false],
                ],
            ],

            // --- useMemo ---
            [
                'question' => 'What is the purpose of useMemo?',
                'options'  => [
                    ['text' => 'To memoize the result of an expensive calculation so it is only recomputed when its dependencies change', 'correct' => true],
                    ['text' => 'To memoize a function reference so it stays stable between renders', 'correct' => false],
                    ['text' => 'To cache the entire component\'s render output', 'correct' => false],
                    ['text' => 'To skip useEffect calls when props have not changed', 'correct' => false],
                ],
            ],
            [
                'question' => 'useMemo(() => expensiveCalc(a, b), [a, b]) — when is the calculation re-run?',
                'options'  => [
                    ['text' => 'Only when a or b changes', 'correct' => true],
                    ['text' => 'On every render regardless of dependencies', 'correct' => false],
                    ['text' => 'Only on the first render', 'correct' => false],
                    ['text' => 'When the component unmounts', 'correct' => false],
                ],
            ],

            // --- useCallback ---
            [
                'question' => 'What is the purpose of useCallback?',
                'options'  => [
                    ['text' => 'To return a memoized function reference that only changes when its dependencies change', 'correct' => true],
                    ['text' => 'To memoize a computed value returned from a function', 'correct' => false],
                    ['text' => 'To defer a callback until after the browser has painted', 'correct' => false],
                    ['text' => 'To bind a callback to the correct "this" context', 'correct' => false],
                ],
            ],
            [
                'question' => 'When is useCallback most beneficial?',
                'options'  => [
                    ['text' => 'When passing a callback to a child component wrapped in React.memo, to prevent unnecessary re-renders', 'correct' => true],
                    ['text' => 'For every function in a component to save memory', 'correct' => false],
                    ['text' => 'When making API calls from event handlers', 'correct' => false],
                    ['text' => 'To replace useEffect for asynchronous side effects', 'correct' => false],
                ],
            ],

            // --- Lifting State Up ---
            [
                'question' => 'What does "lifting state up" mean in React?',
                'options'  => [
                    ['text' => 'Moving shared state to the closest common ancestor so multiple children can access and update it', 'correct' => true],
                    ['text' => 'Migrating local component state to a global store like Redux', 'correct' => false],
                    ['text' => 'Passing state from child to parent via the virtual DOM', 'correct' => false],
                    ['text' => 'Converting class component state to functional component hooks', 'correct' => false],
                ],
            ],

            // --- Controlled vs Uncontrolled ---
            [
                'question' => 'What is an "uncontrolled component" in React?',
                'options'  => [
                    ['text' => 'A form element that stores its own value in the DOM, accessed via a ref rather than React state', 'correct' => true],
                    ['text' => 'A component that cannot receive props', 'correct' => false],
                    ['text' => 'A component that is not wrapped in React.memo', 'correct' => false],
                    ['text' => 'A component whose state is managed by a parent', 'correct' => false],
                ],
            ],

            // --- React Router ---
            [
                'question' => 'In React Router v6, which component do you use to define routes?',
                'options'  => [
                    ['text' => '<Routes> containing <Route> elements', 'correct' => true],
                    ['text' => '<Switch> containing <Route> elements', 'correct' => false],
                    ['text' => '<Router> containing <Page> elements', 'correct' => false],
                    ['text' => '<Nav> containing <Link> elements', 'correct' => false],
                ],
            ],
            [
                'question' => 'Which hook from React Router v6 gives you the current URL params?',
                'options'  => [
                    ['text' => 'useParams()', 'correct' => true],
                    ['text' => 'useRouteParams()', 'correct' => false],
                    ['text' => 'useLocation().params', 'correct' => false],
                    ['text' => 'useHistory().params', 'correct' => false],
                ],
            ],
            [
                'question' => 'Which component from React Router should you use for client-side navigation instead of an <a> tag?',
                'options'  => [
                    ['text' => '<Link to="/path">', 'correct' => true],
                    ['text' => '<a href="/path">', 'correct' => false],
                    ['text' => '<Navigate to="/path" />', 'correct' => false],
                    ['text' => '<Redirect to="/path" />', 'correct' => false],
                ],
            ],

            // --- Error Boundaries ---
            [
                'question' => 'What is an Error Boundary in React?',
                'options'  => [
                    ['text' => 'A class component that catches JavaScript errors in its child tree and displays a fallback UI', 'correct' => true],
                    ['text' => 'A try/catch block placed inside a functional component', 'correct' => false],
                    ['text' => 'A network interceptor that catches failed API calls', 'correct' => false],
                    ['text' => 'A hook that catches errors from useEffect', 'correct' => false],
                ],
            ],
            [
                'question' => 'Can functional components be Error Boundaries?',
                'options'  => [
                    ['text' => 'No — Error Boundaries must be class components (as of React 18)', 'correct' => true],
                    ['text' => 'Yes — using the useErrorBoundary hook', 'correct' => false],
                    ['text' => 'Yes — by wrapping the return in a try/catch', 'correct' => false],
                    ['text' => 'Yes — by using React.memo with an error handler', 'correct' => false],
                ],
            ],

            // --- Portals ---
            [
                'question' => 'What is a React Portal?',
                'options'  => [
                    ['text' => 'A way to render a child component into a different DOM node outside the parent component\'s DOM hierarchy', 'correct' => true],
                    ['text' => 'A mechanism for server-side rendering', 'correct' => false],
                    ['text' => 'A built-in modal component provided by React', 'correct' => false],
                    ['text' => 'A method for communicating between different React apps', 'correct' => false],
                ],
            ],

            // --- Composition ---
            [
                'question' => 'What is "component composition" in React?',
                'options'  => [
                    ['text' => 'Building complex UI by combining simpler, reusable components together', 'correct' => true],
                    ['text' => 'Extending a base component class using inheritance', 'correct' => false],
                    ['text' => 'Mixing multiple CSS classes into one component', 'correct' => false],
                    ['text' => 'Splitting a component into multiple files', 'correct' => false],
                ],
            ],

            // --- State Batching ---
            [
                'question' => 'In React 18, multiple setState calls inside the same event handler are:',
                'options'  => [
                    ['text' => 'Automatically batched into a single re-render', 'correct' => true],
                    ['text' => 'Each processed in a separate re-render immediately', 'correct' => false],
                    ['text' => 'Queued and processed on the next browser paint without batching', 'correct' => false],
                    ['text' => 'Only batched if you wrap them in unstable_batchedUpdates', 'correct' => false],
                ],
            ],

            // --- Reconciliation ---
            [
                'question' => 'What is reconciliation in React?',
                'options'  => [
                    ['text' => 'The process React uses to diff the previous and new virtual DOM trees to determine the minimal set of real DOM updates', 'correct' => true],
                    ['text' => 'Merging conflicting state updates from multiple components', 'correct' => false],
                    ['text' => 'Syncing React state with a backend database', 'correct' => false],
                    ['text' => 'Resolving naming conflicts between props and state', 'correct' => false],
                ],
            ],

            // --- Functional updates ---
            [
                'question' => 'When the new state depends on the previous state, which pattern should you use?',
                'options'  => [
                    ['text' => 'Pass a function to the setter: setState(prev => prev + 1)', 'correct' => true],
                    ['text' => 'Read the state variable directly: setState(count + 1)', 'correct' => false],
                    ['text' => 'Use useEffect to update state after render', 'correct' => false],
                    ['text' => 'Use useRef to track the previous value and add it', 'correct' => false],
                ],
            ],

            // --- Prop drilling ---
            [
                'question' => 'What is "prop drilling"?',
                'options'  => [
                    ['text' => 'Passing props through multiple intermediate components that do not need them, just to reach a deeply nested child', 'correct' => true],
                    ['text' => 'Mutating props inside a child component', 'correct' => false],
                    ['text' => 'Passing too many props to a single component', 'correct' => false],
                    ['text' => 'Extracting default prop values from a prop object', 'correct' => false],
                ],
            ],

            // --- Keys on reconciliation ---
            [
                'question' => 'What happens if two sibling list items share the same key?',
                'options'  => [
                    ['text' => 'React may update or remove the wrong element, causing bugs', 'correct' => true],
                    ['text' => 'React throws an immediate runtime error', 'correct' => false],
                    ['text' => 'React automatically generates unique keys for them', 'correct' => false],
                    ['text' => 'Only the first item renders; the second is silently ignored', 'correct' => false],
                ],
            ],

            // --- useLayoutEffect ---
            [
                'question' => 'How does useLayoutEffect differ from useEffect?',
                'options'  => [
                    ['text' => 'useLayoutEffect fires synchronously after DOM mutations but before the browser paints, while useEffect fires after painting', 'correct' => true],
                    ['text' => 'useLayoutEffect only runs on mobile browsers', 'correct' => false],
                    ['text' => 'useLayoutEffect replaces CSS layout calculations', 'correct' => false],
                    ['text' => 'useLayoutEffect runs before any state update', 'correct' => false],
                ],
            ],

            // --- Lazy initialization ---
            [
                'question' => 'What is lazy initialization of state in useState?',
                'options'  => [
                    ['text' => 'Passing a function to useState so the initial state is computed only on the first render: useState(() => expensiveCalc())', 'correct' => true],
                    ['text' => 'Delaying state initialization until the component mounts', 'correct' => false],
                    ['text' => 'Using useEffect to set state after a network call', 'correct' => false],
                    ['text' => 'Using React.lazy to defer loading the component', 'correct' => false],
                ],
            ],
        ];
    }
}
