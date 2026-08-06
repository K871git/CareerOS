<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TheoryQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $getTopicId = fn (string $slug) => DB::table('topics')->where('slug', $slug)->value('id');

        $questions = [
            // PHP OOP
            [
                'topic_slug' => 'php-oop',
                'difficulty' => 'Medium',
                'question'   => 'What is Dependency Injection and why is it important in PHP applications?',
                'explanation' => 'Dependency Injection (DI) is a design pattern where an object receives its dependencies from outside rather than creating them internally. It promotes loose coupling, makes classes easier to test (dependencies can be mocked), and follows the Dependency Inversion Principle (SOLID). In Laravel, DI is handled by the Service Container which automatically resolves type-hinted dependencies in constructors and method signatures.',
            ],
            [
                'topic_slug' => 'php-oop',
                'difficulty' => 'Hard',
                'question'   => 'Explain the difference between an abstract class and an interface in PHP. When would you use each?',
                'explanation' => 'An abstract class can have concrete methods, properties, and constructor logic — it is used when classes share common implementation. An interface defines only method signatures (a contract) with no implementation, and a class can implement multiple interfaces. Use an abstract class when subclasses share behaviour (e.g., a base Model class). Use an interface when you need a contract that unrelated classes must fulfil (e.g., Arrayable, Jsonable in Laravel).',
            ],

            // Laravel Framework
            [
                'topic_slug' => 'laravel-eloquent',
                'difficulty' => 'Medium',
                'question'   => 'What is the N+1 query problem in Eloquent and how do you solve it?',
                'explanation' => 'The N+1 problem occurs when you load a collection of models and then access a relationship inside a loop — one query fetches the parent collection, then one additional query fires per item to load the relation. This results in N+1 total queries. The solution is eager loading using with(): User::with("posts")->get() — this fires exactly two queries regardless of the number of users. You can also use withCount() for counts and load() to eager-load on an already-retrieved collection.',
            ],
            [
                'topic_slug' => 'laravel-routing-middleware',
                'difficulty' => 'Medium',
                'question'   => 'What is middleware in Laravel and how does the middleware pipeline work?',
                'explanation' => 'Middleware is a layer that intercepts HTTP requests and responses. In Laravel, middleware is registered in the HTTP kernel and applied globally or per-route. When a request arrives, it passes through each middleware\'s handle() method before reaching the controller. Each middleware can pass the request to the next layer via $next($request), reject it, or modify the request/response. Common uses: authentication, rate limiting, CORS headers, logging. Laravel uses a pipeline pattern where each middleware wraps the next, creating a chain of responsibility.',
            ],

            // REST API Design
            [
                'topic_slug' => 'rest-principles',
                'difficulty' => 'Medium',
                'question'   => 'What makes an API truly RESTful? Explain the key constraints of REST.',
                'explanation' => 'REST (Representational State Transfer) has six constraints: (1) Client-Server — separation of concerns between UI and data storage; (2) Stateless — each request contains all information needed, no session state on the server; (3) Cacheable — responses must indicate if they are cacheable; (4) Uniform Interface — consistent resource identification via URIs, HTTP verbs, and standard response formats; (5) Layered System — client cannot tell whether it is connected directly to the server or through intermediaries; (6) Code on Demand (optional) — servers can send executable code. Most APIs violate one or more of these, making them REST-like rather than truly RESTful.',
            ],

            // System Design
            [
                'topic_slug' => 'scalability-basics',
                'difficulty' => 'Hard',
                'question'   => 'Explain the difference between horizontal and vertical scaling. What are the trade-offs?',
                'explanation' => 'Vertical scaling (scale up) means adding more resources to a single server — more CPU, RAM, or faster disk. It is simple to implement but has a hard ceiling (the biggest available machine) and a single point of failure. Horizontal scaling (scale out) means adding more servers behind a load balancer. It has virtually no ceiling and provides redundancy, but introduces complexity: you need stateless application design, distributed sessions/cache (Redis), and a load balancer. For databases, horizontal scaling requires sharding or read replicas. Most modern cloud architectures prefer horizontal scaling for stateless application tiers and vertical scaling for databases up to a point.',
            ],

            // JavaScript Async
            [
                'topic_slug' => 'js-async',
                'difficulty' => 'Medium',
                'question'   => 'Explain the JavaScript event loop. How does it handle asynchronous operations?',
                'explanation' => 'JavaScript is single-threaded — it has one call stack. The event loop is the mechanism that allows non-blocking async behaviour. When an async operation (setTimeout, fetch, I/O) is called, it is handed off to the browser/Node.js runtime (Web APIs / libuv). When complete, the callback is placed in the task queue (macrotasks) or microtask queue (Promises). The event loop constantly checks: if the call stack is empty, it picks the next item from the microtask queue first (Promise callbacks), then from the macrotask queue (setTimeout, setInterval). This is why Promise.resolve().then() runs before setTimeout(() => {}, 0) — microtasks are always drained before the next macrotask.',
            ],

            // React
            [
                'topic_slug' => 'react-hooks',
                'difficulty' => 'Medium',
                'question'   => 'What are React hooks and why were they introduced? Explain useState and useEffect.',
                'explanation' => 'Hooks were introduced in React 16.8 to allow functional components to use state and lifecycle features previously only available in class components. This eliminated the need for class components, HOCs, and render props for most use cases. useState returns a state value and a setter function — calling the setter triggers a re-render. useEffect runs side effects after render: data fetching, subscriptions, DOM manipulation. The dependency array controls when it re-runs — empty array means once after mount, specific dependencies mean it fires when those values change. Returning a cleanup function from useEffect handles unmounting (e.g., clearing timers or unsubscribing).',
            ],
        ];

        foreach ($questions as $item) {
            $topicId = $getTopicId($item['topic_slug']);

            $exists = DB::table('questions')
                ->where('topic_id', $topicId)
                ->where('question', $item['question'])
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('questions')->insert([
                'topic_id'    => $topicId,
                'type'        => 'THEORY',
                'difficulty'  => $item['difficulty'],
                'question'    => $item['question'],
                'explanation' => $item['explanation'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }
}
