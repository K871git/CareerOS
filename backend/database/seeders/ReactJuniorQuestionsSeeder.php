<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Database\Seeder;

class ReactJuniorQuestionsSeeder extends Seeder
{
    public function run(): void
    {
        $track = LearningTrack::firstOrCreate(
            ['slug' => 'full-stack-web-development'],
            ['title' => 'Full Stack Web Development', 'display_order' => 1]
        );

        $subject = Subject::firstOrCreate(
            ['slug' => 'react'],
            [
                'learning_track_id' => $track->id,
                'title'             => 'React',
                'display_order'     => 4,
                'description'       => 'React practice questions — Junior, Intermediate, Advanced',
            ]
        );

        // Create ALL 3 topics upfront so subsequent seeders can reference them safely
        Topic::firstOrCreate(
            ['slug' => 'react-junior'],
            ['subject_id' => $subject->id, 'title' => 'React Basics — Junior', 'display_order' => 1]
        );
        Topic::firstOrCreate(
            ['slug' => 'react-intermediate'],
            ['subject_id' => $subject->id, 'title' => 'React Intermediate', 'display_order' => 2]
        );
        Topic::firstOrCreate(
            ['slug' => 'react-advanced'],
            ['subject_id' => $subject->id, 'title' => 'React Advanced', 'display_order' => 3]
        );

        $topic = Topic::where('slug', 'react-junior')->firstOrFail();

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
                'difficulty' => 'Easy',
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

        $this->command->info("React Junior: {$count} questions total.");
    }

    private function questions(): array
    {
        return [
            // --- What is React ---
            [
                'question' => 'What is React?',
                'options'  => [
                    ['text' => 'A JavaScript library for building user interfaces', 'correct' => true],
                    ['text' => 'A full-stack MVC framework like Angular', 'correct' => false],
                    ['text' => 'A CSS preprocessor', 'correct' => false],
                    ['text' => 'A server-side rendering engine', 'correct' => false],
                ],
            ],
            [
                'question' => 'Who maintains React?',
                'options'  => [
                    ['text' => 'Facebook (Meta)', 'correct' => true],
                    ['text' => 'Google', 'correct' => false],
                    ['text' => 'Microsoft', 'correct' => false],
                    ['text' => 'The Apache Foundation', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does the "Virtual DOM" mean in React?',
                'options'  => [
                    ['text' => 'A lightweight in-memory representation of the real DOM that React uses to calculate minimal updates', 'correct' => true],
                    ['text' => 'A separate browser built by Facebook', 'correct' => false],
                    ['text' => 'A DOM that exists only in a web worker thread', 'correct' => false],
                    ['text' => 'A shadow DOM used for CSS scoping', 'correct' => false],
                ],
            ],

            // --- JSX ---
            [
                'question' => 'What is JSX?',
                'options'  => [
                    ['text' => 'A syntax extension that lets you write HTML-like code inside JavaScript', 'correct' => true],
                    ['text' => 'A new programming language created by Meta', 'correct' => false],
                    ['text' => 'A JavaScript module bundler', 'correct' => false],
                    ['text' => 'A CSS-in-JS library', 'correct' => false],
                ],
            ],
            [
                'question' => 'Which tool is typically used to transform JSX into plain JavaScript?',
                'options'  => [
                    ['text' => 'Babel', 'correct' => true],
                    ['text' => 'Webpack', 'correct' => false],
                    ['text' => 'ESLint', 'correct' => false],
                    ['text' => 'Prettier', 'correct' => false],
                ],
            ],
            [
                'question' => 'In JSX, how do you add a CSS class to an element?',
                'options'  => [
                    ['text' => 'className="my-class"', 'correct' => true],
                    ['text' => 'class="my-class"', 'correct' => false],
                    ['text' => 'cssClass="my-class"', 'correct' => false],
                    ['text' => 'style="my-class"', 'correct' => false],
                ],
            ],
            [
                'question' => 'In JSX, how do you embed a JavaScript expression?',
                'options'  => [
                    ['text' => 'Wrap it in curly braces: {expression}', 'correct' => true],
                    ['text' => 'Wrap it in double curly braces: {{expression}}', 'correct' => false],
                    ['text' => 'Prefix with a dollar sign: $expression', 'correct' => false],
                    ['text' => 'Use template literals inside the tag', 'correct' => false],
                ],
            ],
            [
                'question' => 'A JSX element must have how many root elements?',
                'options'  => [
                    ['text' => 'Exactly one root element (or a Fragment)', 'correct' => true],
                    ['text' => 'As many as needed with no restriction', 'correct' => false],
                    ['text' => 'Two root elements for proper diffing', 'correct' => false],
                    ['text' => 'Zero — JSX does not require a root', 'correct' => false],
                ],
            ],

            // --- Components ---
            [
                'question' => 'What is a functional component in React?',
                'options'  => [
                    ['text' => 'A plain JavaScript function that accepts props and returns JSX', 'correct' => true],
                    ['text' => 'A component that uses class syntax and extends React.Component', 'correct' => false],
                    ['text' => 'A component that only renders once and never re-renders', 'correct' => false],
                    ['text' => 'A component with no props or state', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is the minimum requirement for a React functional component to render something on screen?',
                'options'  => [
                    ['text' => 'It must return JSX (or null)', 'correct' => true],
                    ['text' => 'It must call this.setState()', 'correct' => false],
                    ['text' => 'It must have a constructor', 'correct' => false],
                    ['text' => 'It must extend React.Component', 'correct' => false],
                ],
            ],
            [
                'question' => 'Component names in React must start with:',
                'options'  => [
                    ['text' => 'An uppercase letter', 'correct' => true],
                    ['text' => 'A lowercase letter', 'correct' => false],
                    ['text' => 'An underscore', 'correct' => false],
                    ['text' => 'The word "component"', 'correct' => false],
                ],
            ],

            // --- Props ---
            [
                'question' => 'What are props in React?',
                'options'  => [
                    ['text' => 'Read-only inputs passed from a parent component to a child component', 'correct' => true],
                    ['text' => 'Mutable values stored inside a component', 'correct' => false],
                    ['text' => 'Global variables shared across the entire app', 'correct' => false],
                    ['text' => 'CSS properties applied to JSX elements', 'correct' => false],
                ],
            ],
            [
                'question' => 'Can a child component directly modify the props it receives?',
                'options'  => [
                    ['text' => 'No — props are read-only in the child', 'correct' => true],
                    ['text' => 'Yes — the child can reassign any prop', 'correct' => false],
                    ['text' => 'Yes — using this.props.setProp()', 'correct' => false],
                    ['text' => 'Only if the prop is an object or array', 'correct' => false],
                ],
            ],
            [
                'question' => 'How do you pass a prop named "title" with value "Hello" to a component called Header?',
                'options'  => [
                    ['text' => '<Header title="Hello" />', 'correct' => true],
                    ['text' => '<Header props={title: "Hello"} />', 'correct' => false],
                    ['text' => 'Header.title = "Hello"', 'correct' => false],
                    ['text' => '<Header>{title: "Hello"}</Header>', 'correct' => false],
                ],
            ],
            [
                'question' => 'What special prop lets you pass child elements between opening and closing component tags?',
                'options'  => [
                    ['text' => 'children', 'correct' => true],
                    ['text' => 'content', 'correct' => false],
                    ['text' => 'slots', 'correct' => false],
                    ['text' => 'innerJSX', 'correct' => false],
                ],
            ],

            // --- State & useState ---
            [
                'question' => 'What does the useState hook return?',
                'options'  => [
                    ['text' => 'An array containing the current state value and a setter function', 'correct' => true],
                    ['text' => 'Only the current state value', 'correct' => false],
                    ['text' => 'An object with get and set methods', 'correct' => false],
                    ['text' => 'A Promise that resolves to the new state', 'correct' => false],
                ],
            ],
            [
                'question' => 'What happens to the component when you call the state setter from useState?',
                'options'  => [
                    ['text' => 'React schedules a re-render of the component with the new state value', 'correct' => true],
                    ['text' => 'Only the DOM node is updated without re-rendering', 'correct' => false],
                    ['text' => 'The entire application reloads', 'correct' => false],
                    ['text' => 'Nothing — state updates are batched until a timer fires', 'correct' => false],
                ],
            ],
            [
                'question' => 'Which syntax correctly initializes a count state variable at 0?',
                'options'  => [
                    ['text' => 'const [count, setCount] = useState(0);', 'correct' => true],
                    ['text' => 'const count = useState(0);', 'correct' => false],
                    ['text' => 'let count = useStateValue(0);', 'correct' => false],
                    ['text' => 'this.state = { count: 0 };', 'correct' => false],
                ],
            ],
            [
                'question' => 'State in React is:',
                'options'  => [
                    ['text' => 'Local and mutable data managed inside a component that triggers re-renders when updated', 'correct' => true],
                    ['text' => 'Immutable data that never causes a re-render', 'correct' => false],
                    ['text' => 'Data shared automatically across all components', 'correct' => false],
                    ['text' => 'A synonym for props', 'correct' => false],
                ],
            ],

            // --- Event Handling ---
            [
                'question' => 'How do you attach an onClick handler in JSX?',
                'options'  => [
                    ['text' => '<button onClick={handleClick}>Click</button>', 'correct' => true],
                    ['text' => '<button onclick="handleClick()">Click</button>', 'correct' => false],
                    ['text' => '<button @click={handleClick}>Click</button>', 'correct' => false],
                    ['text' => '<button on-click={handleClick}>Click</button>', 'correct' => false],
                ],
            ],
            [
                'question' => 'In React event handlers, what argument do they receive by default?',
                'options'  => [
                    ['text' => 'A SyntheticEvent object that wraps the native browser event', 'correct' => true],
                    ['text' => 'A plain string describing the event type', 'correct' => false],
                    ['text' => 'The component\'s current props object', 'correct' => false],
                    ['text' => 'No arguments — you must use window.event', 'correct' => false],
                ],
            ],
            [
                'question' => 'How do you prevent the default browser action inside a React event handler?',
                'options'  => [
                    ['text' => 'Call event.preventDefault() inside the handler', 'correct' => true],
                    ['text' => 'Return false from the JSX attribute value', 'correct' => false],
                    ['text' => 'Add the "prevent" prop to the element', 'correct' => false],
                    ['text' => 'Set event.default = false', 'correct' => false],
                ],
            ],

            // --- Conditional Rendering ---
            [
                'question' => 'Which of the following correctly renders a component only when "isLoggedIn" is true?',
                'options'  => [
                    ['text' => '{isLoggedIn && <Dashboard />}', 'correct' => true],
                    ['text' => '{isLoggedIn ? <Dashboard />}', 'correct' => false],
                    ['text' => 'if (isLoggedIn) return <Dashboard />', 'correct' => false],
                    ['text' => '<Dashboard if={isLoggedIn} />', 'correct' => false],
                ],
            ],
            [
                'question' => 'Which operator is best for rendering one of two components based on a condition?',
                'options'  => [
                    ['text' => 'Ternary operator: condition ? <A /> : <B />', 'correct' => true],
                    ['text' => 'Logical AND: condition && <A /> && <B />', 'correct' => false],
                    ['text' => 'Switch expression', 'correct' => false],
                    ['text' => 'The <If> JSX tag built into React', 'correct' => false],
                ],
            ],

            // --- Lists & Keys ---
            [
                'question' => 'Why does React require a "key" prop when rendering a list of elements?',
                'options'  => [
                    ['text' => 'It helps React identify which items have changed, been added, or removed for efficient re-renders', 'correct' => true],
                    ['text' => 'It is used to apply a CSS ID to each list item automatically', 'correct' => false],
                    ['text' => 'It is required for accessibility reasons by the browser', 'correct' => false],
                    ['text' => 'It triggers a database lookup for each item', 'correct' => false],
                ],
            ],
            [
                'question' => 'What is the recommended value to use for the key prop when rendering a list?',
                'options'  => [
                    ['text' => 'A stable, unique identifier such as an item ID from the data', 'correct' => true],
                    ['text' => 'The array index is always the best choice', 'correct' => false],
                    ['text' => 'A random number generated on each render', 'correct' => false],
                    ['text' => 'The component name', 'correct' => false],
                ],
            ],
            [
                'question' => 'Which array method is most commonly used to render a list of JSX elements from an array?',
                'options'  => [
                    ['text' => '.map()', 'correct' => true],
                    ['text' => '.forEach()', 'correct' => false],
                    ['text' => '.reduce()', 'correct' => false],
                    ['text' => '.filter()', 'correct' => false],
                ],
            ],

            // --- useEffect ---
            [
                'question' => 'What is the purpose of the useEffect hook?',
                'options'  => [
                    ['text' => 'To perform side effects such as data fetching, subscriptions, or manual DOM manipulation after render', 'correct' => true],
                    ['text' => 'To update the component state synchronously before painting', 'correct' => false],
                    ['text' => 'To replace the render method in functional components', 'correct' => false],
                    ['text' => 'To add CSS effects to components', 'correct' => false],
                ],
            ],
            [
                'question' => 'If you pass an empty array [] as the second argument to useEffect, when does the effect run?',
                'options'  => [
                    ['text' => 'Only once after the initial render', 'correct' => true],
                    ['text' => 'After every render', 'correct' => false],
                    ['text' => 'Never', 'correct' => false],
                    ['text' => 'Only when state changes', 'correct' => false],
                ],
            ],
            [
                'question' => 'What does the cleanup function returned from useEffect do?',
                'options'  => [
                    ['text' => 'It runs before the next effect or when the component unmounts, used to clean up subscriptions or timers', 'correct' => true],
                    ['text' => 'It clears the component state back to the initial value', 'correct' => false],
                    ['text' => 'It prevents the component from re-rendering', 'correct' => false],
                    ['text' => 'It removes the component from the DOM immediately', 'correct' => false],
                ],
            ],
            [
                'question' => 'If no dependency array is passed to useEffect, when does the effect run?',
                'options'  => [
                    ['text' => 'After every render', 'correct' => true],
                    ['text' => 'Only on the first render', 'correct' => false],
                    ['text' => 'Only when state changes', 'correct' => false],
                    ['text' => 'Only when props change', 'correct' => false],
                ],
            ],

            // --- Fragments ---
            [
                'question' => 'What is a React Fragment used for?',
                'options'  => [
                    ['text' => 'To group multiple elements without adding an extra DOM node', 'correct' => true],
                    ['text' => 'To split a large component into smaller files', 'correct' => false],
                    ['text' => 'To lazy-load part of a component', 'correct' => false],
                    ['text' => 'To create a portal outside the main DOM tree', 'correct' => false],
                ],
            ],
            [
                'question' => 'Which of the following is the shorthand syntax for a React Fragment?',
                'options'  => [
                    ['text' => '<></>', 'correct' => true],
                    ['text' => '<Fragment />', 'correct' => false],
                    ['text' => '<Group></Group>', 'correct' => false],
                    ['text' => '<React.Empty></React.Empty>', 'correct' => false],
                ],
            ],

            // --- One-way Data Flow ---
            [
                'question' => 'React follows a "one-way data flow." What does this mean?',
                'options'  => [
                    ['text' => 'Data flows from parent to child via props; children cannot directly update parent state', 'correct' => true],
                    ['text' => 'Data flows from child to parent via props automatically', 'correct' => false],
                    ['text' => 'Data is shared bidirectionally between all components', 'correct' => false],
                    ['text' => 'Only one component in the app can hold state', 'correct' => false],
                ],
            ],

            // --- Forms ---
            [
                'question' => 'What is a "controlled component" in React forms?',
                'options'  => [
                    ['text' => 'A form element whose value is controlled by React state, keeping the UI and state in sync', 'correct' => true],
                    ['text' => 'A component that prevents all user input', 'correct' => false],
                    ['text' => 'A form element that reads its value directly from the DOM', 'correct' => false],
                    ['text' => 'A component wrapped with React.memo to prevent re-renders', 'correct' => false],
                ],
            ],
            [
                'question' => 'In a controlled text input, which event is typically used to update state as the user types?',
                'options'  => [
                    ['text' => 'onChange', 'correct' => true],
                    ['text' => 'onInput', 'correct' => false],
                    ['text' => 'onKeyPress', 'correct' => false],
                    ['text' => 'onType', 'correct' => false],
                ],
            ],
            [
                'question' => 'How do you access the current value of an input in an onChange handler?',
                'options'  => [
                    ['text' => 'event.target.value', 'correct' => true],
                    ['text' => 'event.value', 'correct' => false],
                    ['text' => 'this.input.value', 'correct' => false],
                    ['text' => 'event.currentTarget.val', 'correct' => false],
                ],
            ],

            // --- StrictMode ---
            [
                'question' => 'What does React.StrictMode do?',
                'options'  => [
                    ['text' => 'Activates additional development warnings by intentionally double-invoking certain functions to detect side effects', 'correct' => true],
                    ['text' => 'Prevents components from re-rendering more than once', 'correct' => false],
                    ['text' => 'Enables strict TypeScript checking inside JSX files', 'correct' => false],
                    ['text' => 'Locks state so it cannot be changed after initial render', 'correct' => false],
                ],
            ],

            // --- ReactDOM ---
            [
                'question' => 'What does ReactDOM.createRoot().render() do?',
                'options'  => [
                    ['text' => 'Mounts the root React component into a real DOM element', 'correct' => true],
                    ['text' => 'Creates a new browser window for the React app', 'correct' => false],
                    ['text' => 'Fetches the component from a remote server', 'correct' => false],
                    ['text' => 'Pre-renders the component to a static HTML string', 'correct' => false],
                ],
            ],

            // --- Styling ---
            [
                'question' => 'How do you apply inline styles to a JSX element?',
                'options'  => [
                    ['text' => 'Use the style prop with a JavaScript object: style={{ color: "red" }}', 'correct' => true],
                    ['text' => 'Use the style prop with a CSS string: style="color: red;"', 'correct' => false],
                    ['text' => 'Use the css prop: css="color: red;"', 'correct' => false],
                    ['text' => 'Inline styles are not supported in React', 'correct' => false],
                ],
            ],

            // --- Default Exports / Imports ---
            [
                'question' => 'Which import statement correctly imports the default export from a file?',
                'options'  => [
                    ['text' => 'import MyComponent from "./MyComponent"', 'correct' => true],
                    ['text' => 'import { MyComponent } from "./MyComponent"', 'correct' => false],
                    ['text' => 'import * MyComponent from "./MyComponent"', 'correct' => false],
                    ['text' => 'require("./MyComponent")', 'correct' => false],
                ],
            ],

            // --- Hooks rules ---
            [
                'question' => 'Which of the following is a rule of React Hooks?',
                'options'  => [
                    ['text' => 'Only call hooks at the top level of a function — never inside loops, conditions, or nested functions', 'correct' => true],
                    ['text' => 'Hooks can only be used inside class components', 'correct' => false],
                    ['text' => 'You must call hooks inside useEffect', 'correct' => false],
                    ['text' => 'Hooks must be defined before the return statement and after useState', 'correct' => false],
                ],
            ],
            [
                'question' => 'Where can you call React Hooks?',
                'options'  => [
                    ['text' => 'Only inside React functional components or custom hooks', 'correct' => true],
                    ['text' => 'Anywhere in regular JavaScript functions', 'correct' => false],
                    ['text' => 'Only inside class component constructors', 'correct' => false],
                    ['text' => 'Only inside event handler functions', 'correct' => false],
                ],
            ],

            // --- Project Setup ---
            [
                'question' => 'Which tool is the modern recommended way to scaffold a new React project?',
                'options'  => [
                    ['text' => 'Vite (npm create vite@latest)', 'correct' => true],
                    ['text' => 'create-react-app is still the only official way', 'correct' => false],
                    ['text' => 'Webpack CLI directly', 'correct' => false],
                    ['text' => 'Parcel CLI', 'correct' => false],
                ],
            ],

            // --- Re-renders ---
            [
                'question' => 'What causes a React component to re-render?',
                'options'  => [
                    ['text' => 'A state or prop change that React detects', 'correct' => true],
                    ['text' => 'Any variable change inside the function body', 'correct' => false],
                    ['text' => 'Only when the user interacts with the UI', 'correct' => false],
                    ['text' => 'Re-renders are triggered on a fixed timer', 'correct' => false],
                ],
            ],

            // --- null rendering ---
            [
                'question' => 'What happens if a React component returns null?',
                'options'  => [
                    ['text' => 'Nothing is rendered to the DOM for that component', 'correct' => true],
                    ['text' => 'React throws an error', 'correct' => false],
                    ['text' => 'The component renders an empty <div>', 'correct' => false],
                    ['text' => 'The parent component is also removed from the DOM', 'correct' => false],
                ],
            ],

            // --- PropTypes ---
            [
                'question' => 'What is the purpose of PropTypes in React?',
                'options'  => [
                    ['text' => 'Runtime type-checking of props to catch incorrect prop types during development', 'correct' => true],
                    ['text' => 'To compile TypeScript types into JavaScript', 'correct' => false],
                    ['text' => 'To enforce prop immutability at the browser level', 'correct' => false],
                    ['text' => 'To automatically generate documentation for components', 'correct' => false],
                ],
            ],
        ];
    }
}
