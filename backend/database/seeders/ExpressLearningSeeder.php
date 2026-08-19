<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpressLearningSeeder extends Seeder
{
    public function run(): void
    {
        $track = LearningTrack::firstOrCreate(
            ['slug' => 'backend-engineering'],
            [
                'title'         => 'Backend Engineering',
                'description'   => 'Backend engineering — server-side programming, APIs, databases, and infrastructure.',
                'display_order' => 3,
            ]
        );

        $subject = Subject::firstOrCreate(
            ['slug' => 'express'],
            [
                'learning_track_id' => $track->id,
                'title'             => 'Express.js',
                'description'       => 'Express.js is the most popular Node.js web framework. Master routing, middleware, REST API design, error handling, and production-ready application architecture.',
                'display_order'     => 7,
            ]
        );

        // Assign levels to existing practice topics
        Topic::where('slug', 'express-junior')->update(['level' => 1, 'subject_id' => $subject->id]);
        Topic::where('slug', 'express-intermediate')->update(['level' => 2, 'subject_id' => $subject->id]);
        Topic::where('slug', 'express-advanced')->update(['level' => 3, 'subject_id' => $subject->id]);

        $topic4 = Topic::firstOrCreate(
            ['slug' => 'express-level-4-scaling'],
            [
                'subject_id'    => $subject->id,
                'title'         => 'Express.js Performance & Scaling',
                'description'   => 'Caching, compression, WebSockets, SSE, health checks, and containerised deployment.',
                'display_order' => 4,
                'level'         => 4,
            ]
        );
        Topic::where('slug', 'express-level-4-scaling')->update(['level' => 4]);

        $topic5 = Topic::firstOrCreate(
            ['slug' => 'express-level-5-architecture'],
            [
                'subject_id'    => $subject->id,
                'title'         => 'Expert Express.js Architecture',
                'description'   => 'Microservices, API gateways, observability, GraphQL, and enterprise patterns.',
                'display_order' => 5,
                'level'         => 5,
            ]
        );
        Topic::where('slug', 'express-level-5-architecture')->update(['level' => 5]);

        $this->seedLessons($subject);
        $this->seedLevel4Questions($topic4);
        $this->seedLevel5Questions($topic5);

        $this->command->info('Express.js Learning seeder complete — all 5 levels populated.');
    }

    // ─── Lessons ──────────────────────────────────────────────────────────────

    private function seedLessons(Subject $subject): void
    {
        $topics = Topic::where('subject_id', $subject->id)
            ->orderBy('level')
            ->get()
            ->keyBy('level');

        $lessons = [
            1 => [
                ['title' => 'Getting Started: Setup, Routing & the Request/Response Cycle', 'content' => $this->l1_1(), 'estimated_minutes' => 20, 'display_order' => 1],
                ['title' => 'Middleware in Express: app.use(), Body Parsing & Static Files',  'content' => $this->l1_2(), 'estimated_minutes' => 18, 'display_order' => 2],
                ['title' => 'Route Parameters, Query Strings & Response Methods',              'content' => $this->l1_3(), 'estimated_minutes' => 18, 'display_order' => 3],
            ],
            2 => [
                ['title' => 'Express Router: Modular Route Files & Router-Level Middleware',  'content' => $this->l2_1(), 'estimated_minutes' => 22, 'display_order' => 1],
                ['title' => 'Error Handling: Global Error Middleware & Async Error Patterns',  'content' => $this->l2_2(), 'estimated_minutes' => 22, 'display_order' => 2],
                ['title' => 'Authentication Middleware: JWT, CORS & Request Validation',      'content' => $this->l2_3(), 'estimated_minutes' => 25, 'display_order' => 3],
            ],
            3 => [
                ['title' => 'Security: Helmet, Rate Limiting & Input Sanitisation',           'content' => $this->l3_1(), 'estimated_minutes' => 25, 'display_order' => 1],
                ['title' => 'Testing Express APIs with Supertest & Jest',                     'content' => $this->l3_2(), 'estimated_minutes' => 25, 'display_order' => 2],
                ['title' => 'Application Architecture: Controllers, Services & Repositories', 'content' => $this->l3_3(), 'estimated_minutes' => 25, 'display_order' => 3],
            ],
            4 => [
                ['title' => 'Performance: Compression, Caching & ETags',                     'content' => $this->l4_1(), 'estimated_minutes' => 28, 'display_order' => 1],
                ['title' => 'Real-Time: WebSockets & Server-Sent Events with Express',        'content' => $this->l4_2(), 'estimated_minutes' => 25, 'display_order' => 2],
                ['title' => 'Deployment: Docker, PM2, Health Checks & Production Config',    'content' => $this->l4_3(), 'estimated_minutes' => 28, 'display_order' => 3],
            ],
            5 => [
                ['title' => 'Microservices with Express: API Gateways & Service Communication', 'content' => $this->l5_1(), 'estimated_minutes' => 30, 'display_order' => 1],
                ['title' => 'Observability: Structured Logging, Metrics & OpenTelemetry',       'content' => $this->l5_2(), 'estimated_minutes' => 30, 'display_order' => 2],
                ['title' => 'Advanced Patterns: GraphQL, CQRS & Enterprise Architecture',       'content' => $this->l5_3(), 'estimated_minutes' => 30, 'display_order' => 3],
            ],
        ];

        foreach ($lessons as $level => $levelLessons) {
            if (!isset($topics[$level])) continue;
            $topic = $topics[$level];
            foreach ($levelLessons as $lesson) {
                DB::table('lessons')->updateOrInsert(
                    ['topic_id' => $topic->id, 'title' => $lesson['title']],
                    [
                        'content'           => $lesson['content'],
                        'estimated_minutes' => $lesson['estimated_minutes'],
                        'display_order'     => $lesson['display_order'],
                        'updated_at'        => now(),
                        'created_at'        => now(),
                    ]
                );
            }
        }
    }

    // ═══════════════════════════════════════════════════════════════
    //  LEVEL 1 LESSONS
    // ═══════════════════════════════════════════════════════════════

    private function l1_1(): string { return <<<'MD'
## Getting Started: Setup, Routing & the Request/Response Cycle

Express.js is a minimal, unopinionated web framework for Node.js. It gives you routing, middleware, and helper methods without forcing a rigid structure.

### Why Not Raw Node.js?

```js
// Raw Node.js — painful
const http = require('http');
http.createServer((req, res) => {
  if (req.method === 'GET' && req.url === '/users') {
    res.writeHead(200, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({ users: [] }));
  }
}).listen(3000);

// Express — clean
const express = require('express');
const app = express();
app.get('/users', (req, res) => res.json({ users: [] }));
app.listen(3000);
```

### Setup

```bash
mkdir my-api && cd my-api
npm init -y
npm install express
```

**index.js:**
```js
const express = require('express');
const app = express();

app.use(express.json()); // parse JSON bodies

app.get('/', (req, res) => {
  res.json({ message: 'Hello from Express!' });
});

app.listen(3000, () => console.log('Server on http://localhost:3000'));
```

### HTTP Method Routing

Express maps a method for every HTTP verb:

```js
app.get('/posts',     handler); // READ all
app.post('/posts',    handler); // CREATE
app.get('/posts/:id', handler); // READ one
app.put('/posts/:id', handler); // REPLACE
app.patch('/posts/:id', handler); // PARTIAL UPDATE
app.delete('/posts/:id', handler); // DELETE
```

### The req and res Objects

**req — data coming IN:**
| Property | What it contains |
|---|---|
| `req.params` | URL route params (`:id`) |
| `req.query` | Query string (`?page=2`) |
| `req.body` | Request body (JSON/form) |
| `req.headers` | All request headers |
| `req.method` | GET, POST, etc. |
| `req.get('Authorization')` | Specific header value |

**res — sending OUT:**
```js
res.json({ data })             // JSON response (sets Content-Type)
res.status(201).json(data)     // with status code
res.send('plain text')         // text/html
res.redirect('/new-path')      // 301/302 redirect
res.status(204).end()          // no body (DELETE success)
res.sendFile(absolutePath)     // send a file
```

### Minimal REST API Example

```js
const express = require('express');
const app = express();
app.use(express.json());

let posts = [{ id: 1, title: 'First Post' }];

app.get('/posts', (req, res) => res.json(posts));

app.post('/posts', (req, res) => {
  const post = { id: Date.now(), ...req.body };
  posts.push(post);
  res.status(201).json(post);
});

app.delete('/posts/:id', (req, res) => {
  posts = posts.filter(p => p.id !== Number(req.params.id));
  res.status(204).end();
});

app.listen(3000);
```

### Key Takeaways

- `express()` creates an app; `app.listen(port)` starts it
- Register routes with `app.get/post/put/patch/delete(path, handler)`
- `req` holds all incoming data; `res` is how you respond
- Always add `app.use(express.json())` before routes that receive JSON bodies
- Chain `res.status(code).json(data)` to set status and send response together
MD; }

    private function l1_2(): string { return <<<'MD'
## Middleware in Express: app.use(), Body Parsing & Static Files

Middleware is the backbone of Express. Every piece of functionality — body parsing, authentication, logging, error handling — is middleware.

### What is Middleware?

A middleware function receives `(req, res, next)`. It can:
1. Execute code
2. Modify `req` or `res`
3. Call `next()` to pass to the next middleware
4. End the request by sending a response

```
Request → middleware 1 → middleware 2 → route handler → Response
```

### app.use() — Registering Middleware

```js
// Runs for ALL requests to ALL paths
app.use(myMiddleware);

// Runs only for paths starting with /api
app.use('/api', myMiddleware);
```

**Order matters.** Middleware runs in the order it is registered.

### Built-in Middleware

Express ships with three important built-in middleware functions:

**1. express.json() — Parse JSON bodies**
```js
app.use(express.json());

app.post('/users', (req, res) => {
  console.log(req.body); // { name: 'Alice', age: 30 }
  res.status(201).json(req.body);
});
```
Without this, `req.body` is `undefined` for POST/PUT requests.

**2. express.urlencoded() — Parse HTML form data**
```js
app.use(express.urlencoded({ extended: false }));
// req.body now has form fields: { username: 'alice', password: '...' }
```

**3. express.static() — Serve static files**
```js
app.use(express.static('public'));
// Files in /public are served directly:
// /public/index.html → http://localhost:3000/index.html
// /public/style.css  → http://localhost:3000/style.css

// With a virtual prefix:
app.use('/assets', express.static('public'));
// → http://localhost:3000/assets/style.css
```

### Writing Your Own Middleware

```js
// Request logger
function logger(req, res, next) {
  console.log(`${req.method} ${req.url} — ${new Date().toISOString()}`);
  next(); // MUST call next() or the request hangs
}

app.use(logger);
```

**Middleware that modifies req:**
```js
function attachRequestId(req, res, next) {
  req.requestId = Math.random().toString(36).slice(2);
  next();
}

app.use(attachRequestId);

app.get('/test', (req, res) => {
  res.json({ requestId: req.requestId }); // available here
});
```

### The Middleware Chain

```js
app.use(express.json());   // 1. Parse body
app.use(logger);           // 2. Log request
app.use(authenticate);     // 3. Check auth token
app.get('/data', handler); // 4. Route handler
app.use(errorHandler);     // 5. Catch errors (must be last)
```

### morgan — HTTP Request Logger

The `morgan` package is the standard logging middleware:

```bash
npm install morgan
```

```js
const morgan = require('morgan');
app.use(morgan('dev'));     // colorized, concise — for development
app.use(morgan('combined')); // Apache format — for production
```

### Key Takeaways

- Middleware = function with `(req, res, next)` signature
- Always call `next()` unless you send a response
- `app.use(express.json())` — required for parsing JSON bodies
- `app.use(express.static('public'))` — serves files from a directory
- Middleware order is execution order — put auth before routes, error handler last
MD; }

    private function l1_3(): string { return <<<'MD'
## Route Parameters, Query Strings & Response Methods

Understanding how URL data flows into Express — and how to structure responses — is essential for any REST API.

### Route Parameters — req.params

Named URL segments prefixed with `:` become route parameters:

```js
app.get('/users/:id', (req, res) => {
  console.log(req.params.id); // "42" (always a string — parse it!)
  const id = Number(req.params.id);
  res.json({ userId: id });
});

// Multiple params
app.get('/users/:userId/posts/:postId', (req, res) => {
  const { userId, postId } = req.params;
  res.json({ userId, postId });
});
```

**Always parse params:** `req.params.id` is `"42"` (string), not `42` (number).

### Query Strings — req.query

Key-value pairs after `?` in the URL:

```
GET /products?category=books&page=2&limit=10
```

```js
app.get('/products', (req, res) => {
  const { category, page = 1, limit = 10 } = req.query;
  // category = 'books', page = '2', limit = '10' — all strings
  res.json({ category, page: Number(page), limit: Number(limit) });
});
```

Use query strings for: filtering, pagination, sorting, searching.

### params vs query — When to Use Each

| Use case | Mechanism |
|---|---|
| Resource identity (`/users/42`) | `req.params` |
| Filtering, pagination, search | `req.query` |
| Create/update payload | `req.body` |

### Response Status Codes

Always use the right status code:

| Code | Meaning | When to use |
|---|---|---|
| 200 | OK | Successful GET / PUT |
| 201 | Created | Successful POST (resource created) |
| 204 | No Content | Successful DELETE |
| 400 | Bad Request | Invalid input from client |
| 401 | Unauthorized | Missing/invalid auth |
| 403 | Forbidden | Authenticated but no permission |
| 404 | Not Found | Resource does not exist |
| 409 | Conflict | Duplicate (e.g. email taken) |
| 422 | Unprocessable | Validation failed |
| 500 | Server Error | Unexpected server error |

```js
app.get('/users/:id', async (req, res) => {
  const user = await User.findById(req.params.id);
  if (!user) return res.status(404).json({ error: 'User not found' });
  res.json(user); // 200 by default
});

app.post('/users', async (req, res) => {
  const user = await User.create(req.body);
  res.status(201).json(user);
});

app.delete('/users/:id', async (req, res) => {
  await User.deleteById(req.params.id);
  res.status(204).end(); // no body
});
```

### res.json() vs res.send() vs res.end()

```js
res.json({ data: 'hello' })  // Sets Content-Type: application/json, serialises object
res.send('Hello World')       // Auto-detects: string → text/html, object → application/json
res.end()                     // Raw Node.js — no Content-Type, no body — avoid in Express
```

**Use `res.json()` for all API responses.** It's explicit and always correct.

### Practical Pattern: CRUD Route Group

```js
// GET    /posts        → list all
// POST   /posts        → create
// GET    /posts/:id    → get one
// PUT    /posts/:id    → replace
// DELETE /posts/:id    → delete

app.get('/posts', async (req, res) => {
  const { page = 1, limit = 20, search } = req.query;
  const posts = await Post.findAll({ page, limit, search });
  res.json({ posts, page: Number(page), limit: Number(limit) });
});

app.get('/posts/:id', async (req, res) => {
  const post = await Post.findById(Number(req.params.id));
  if (!post) return res.status(404).json({ error: 'Post not found' });
  res.json(post);
});

app.post('/posts', async (req, res) => {
  const { title, body } = req.body;
  if (!title) return res.status(400).json({ error: 'title is required' });
  const post = await Post.create({ title, body });
  res.status(201).json(post);
});

app.delete('/posts/:id', async (req, res) => {
  await Post.delete(Number(req.params.id));
  res.status(204).end();
});
```

### Key Takeaways

- Route params (`:id`) → `req.params.id` — always strings, parse to number when needed
- Query strings → `req.query` — use for filters, pagination, search
- Request body → `req.body` — needs `express.json()` middleware
- Always respond with the correct HTTP status code
- `res.status(code).json(data)` is the standard response pattern for REST APIs
MD; }

    // ═══════════════════════════════════════════════════════════════
    //  LEVEL 2 LESSONS
    // ═══════════════════════════════════════════════════════════════

    private function l2_1(): string { return <<<'MD'
## Express Router: Modular Route Files & Router-Level Middleware

As your app grows, keeping all routes in one file becomes unmanageable. `express.Router()` lets you split routes into clean, focused modules.

### The Problem with a Monolithic app.js

```js
// app.js — gets messy fast
app.get('/users', ...)
app.post('/users', ...)
app.get('/posts', ...)
app.post('/posts', ...)
app.get('/comments', ...)
// hundreds more lines...
```

### express.Router() — Mini Express Applications

A Router is a mini-app: it has its own middleware stack and route methods.

**routes/users.js:**
```js
const express = require('express');
const router = express.Router();

router.get('/', async (req, res) => {
  const users = await User.findAll();
  res.json(users);
});

router.post('/', async (req, res) => {
  const user = await User.create(req.body);
  res.status(201).json(user);
});

router.get('/:id', async (req, res) => {
  const user = await User.findById(req.params.id);
  if (!user) return res.status(404).json({ error: 'Not found' });
  res.json(user);
});

module.exports = router;
```

**app.js — clean and minimal:**
```js
const express = require('express');
const usersRouter = require('./routes/users');
const postsRouter = require('./routes/posts');

const app = express();
app.use(express.json());

app.use('/api/v1/users', usersRouter);
app.use('/api/v1/posts', postsRouter);

app.listen(3000);
```

Now `GET /api/v1/users` hits the users router's `router.get('/')`.

### Router-Level Middleware

Middleware added to a router only applies to routes in that router:

```js
// routes/admin.js
const router = express.Router();

// This middleware runs for ALL routes in this router
router.use((req, res, next) => {
  if (!req.user?.isAdmin) {
    return res.status(403).json({ error: 'Admin only' });
  }
  next();
});

router.get('/stats', (req, res) => res.json({ stats: {} }));
router.delete('/users/:id', (req, res) => { /* ... */ });

module.exports = router;
```

### router.param() — Pre-load Route Parameters

Automatically run a function when a named parameter appears in a route:

```js
router.param('userId', async (req, res, next, id) => {
  const user = await User.findById(id);
  if (!user) return res.status(404).json({ error: 'User not found' });
  req.targetUser = user; // attach to req for downstream handlers
  next();
});

// req.targetUser is already populated here
router.get('/:userId', (req, res) => res.json(req.targetUser));
router.put('/:userId', (req, res) => { /* req.targetUser available */ });
router.delete('/:userId', (req, res) => { /* req.targetUser available */ });
```

### Recommended Project Structure

```
my-api/
├── app.js               ← create app, register middleware, mount routers
├── server.js            ← call app.listen() (separate from app for testing)
├── routes/
│   ├── index.js         ← combines all routers
│   ├── users.js
│   ├── posts.js
│   └── auth.js
├── controllers/
│   ├── userController.js
│   └── postController.js
├── services/
│   └── userService.js
└── middleware/
    ├── authenticate.js
    └── validate.js
```

**routes/index.js:**
```js
const express = require('express');
const router = express.Router();

router.use('/auth',  require('./auth'));
router.use('/users', require('./users'));
router.use('/posts', require('./posts'));

module.exports = router;
```

**app.js:**
```js
const express = require('express');
const routes = require('./routes');

const app = express();
app.use(express.json());
app.use('/api/v1', routes);

module.exports = app; // export for testing
```

**server.js:**
```js
const app = require('./app');
app.listen(3000, () => console.log('Server on :3000'));
```

### Key Takeaways

- `express.Router()` creates a modular, mountable mini-app
- `app.use('/prefix', router)` mounts the router — route paths inside are relative to the prefix
- Router middleware only runs for routes in that router — great for per-resource auth
- `router.param(name, fn)` pre-loads a resource for all routes using that param
- Separate `app.js` from `server.js` — makes `app` importable for Supertest without starting the server
MD; }

    private function l2_2(): string { return <<<'MD'
## Error Handling: Global Error Middleware & Async Error Patterns

Express has a powerful error-handling system. The key insight: errors flow through the middleware chain via `next(err)`, collected by a single error-handling middleware at the end.

### The 4-Parameter Error Handler

A middleware with exactly 4 parameters `(err, req, res, next)` is an error handler. Express recognises it by parameter count:

```js
// Must be registered AFTER all routes and middleware
app.use((err, req, res, next) => {
  console.error(err.stack);
  const status = err.status || err.statusCode || 500;
  res.status(status).json({
    error: err.message || 'Internal Server Error',
  });
});
```

### Triggering the Error Handler

From any route or middleware, call `next(err)`:

```js
app.get('/users/:id', async (req, res, next) => {
  try {
    const user = await User.findById(req.params.id);
    if (!user) {
      const err = new Error('User not found');
      err.status = 404;
      return next(err); // → goes to error handler
    }
    res.json(user);
  } catch (err) {
    next(err); // unexpected DB error → error handler
  }
});
```

### The Problem with Async Routes in Express 4

Express 4 does **not** automatically catch promise rejections. An unhandled rejection in a route crashes the process:

```js
// DANGEROUS — if DB throws, the error is unhandled
app.get('/posts', async (req, res) => {
  const posts = await Post.findAll(); // if this throws → crash
  res.json(posts);
});
```

### Solution 1: try/catch in every handler

```js
app.get('/posts', async (req, res, next) => {
  try {
    const posts = await Post.findAll();
    res.json(posts);
  } catch (err) {
    next(err); // pass to error handler
  }
});
```

Works but repetitive. DRY it up with a wrapper.

### Solution 2: asyncHandler Wrapper (Recommended)

```js
// middleware/asyncHandler.js
const asyncHandler = (fn) => (req, res, next) => {
  Promise.resolve(fn(req, res, next)).catch(next);
};

module.exports = asyncHandler;
```

```js
const asyncHandler = require('../middleware/asyncHandler');

// Clean — no try/catch needed
router.get('/posts', asyncHandler(async (req, res) => {
  const posts = await Post.findAll();
  res.json(posts);
}));

router.get('/posts/:id', asyncHandler(async (req, res) => {
  const post = await Post.findById(req.params.id);
  if (!post) {
    const err = new Error('Post not found');
    err.status = 404;
    throw err; // asyncHandler catches this and calls next(err)
  }
  res.json(post);
}));
```

### Custom AppError Class

Create a typed error to carry status codes:

```js
// utils/AppError.js
class AppError extends Error {
  constructor(message, statusCode = 500) {
    super(message);
    this.statusCode = statusCode;
    this.isOperational = true; // vs programming errors
  }
}

module.exports = AppError;
```

```js
const AppError = require('../utils/AppError');

router.get('/:id', asyncHandler(async (req, res) => {
  const user = await User.findById(req.params.id);
  if (!user) throw new AppError('User not found', 404);
  res.json(user);
}));
```

### Production Error Handler

```js
// middleware/errorHandler.js
module.exports = (err, req, res, next) => {
  const status = err.statusCode || err.status || 500;

  // Don't leak stack traces in production
  const response = {
    status: 'error',
    message: err.message,
  };
  if (process.env.NODE_ENV === 'development') {
    response.stack = err.stack;
  }

  res.status(status).json(response);
};
```

**app.js:**
```js
app.use('/api/v1', routes);
app.use(require('./middleware/errorHandler')); // ← always last
```

### 404 Handler for Unknown Routes

```js
// After all routes, before error handler:
app.use((req, res, next) => {
  next(new AppError(`Route ${req.method} ${req.url} not found`, 404));
});

app.use(errorHandler); // catches the 404 too
```

### Express 5

Express 5 (in beta) automatically catches async route handler rejections — `asyncHandler` won't be needed. Start adopting the try/catch pattern now and the upgrade will be seamless.

### Key Takeaways

- Error handler = middleware with `(err, req, res, next)` — register it last
- Trigger it with `next(err)` or `throw err` (inside asyncHandler)
- Express 4 does NOT catch async errors — use try/catch + next(err) or an asyncHandler wrapper
- Create a custom `AppError` class to carry HTTP status codes cleanly
- Never send stack traces to clients in production
MD; }

    private function l2_3(): string { return <<<'MD'
## Authentication Middleware: JWT, CORS & Request Validation

Almost every production API needs authentication, cross-origin resource sharing, and input validation. Express handles all three through middleware.

### JWT Authentication

JSON Web Tokens (JWT) are the standard for stateless API authentication.

```bash
npm install jsonwebtoken bcryptjs
```

**How it works:**
1. User logs in with credentials
2. Server verifies, issues a signed JWT
3. Client stores the token (localStorage or httpOnly cookie)
4. Client sends token in every request: `Authorization: Bearer <token>`
5. Server middleware verifies the token on every protected route

**Issuing a token:**
```js
const jwt = require('jsonwebtoken');
const bcrypt = require('bcryptjs');

router.post('/login', asyncHandler(async (req, res) => {
  const { email, password } = req.body;
  const user = await User.findByEmail(email);
  if (!user) throw new AppError('Invalid credentials', 401);

  const valid = await bcrypt.compare(password, user.password);
  if (!valid) throw new AppError('Invalid credentials', 401);

  const token = jwt.sign(
    { id: user.id, email: user.email, role: user.role },
    process.env.JWT_SECRET,
    { expiresIn: '7d' }
  );

  res.json({ token, user: { id: user.id, email: user.email } });
}));
```

**Authentication middleware:**
```js
// middleware/authenticate.js
const jwt = require('jsonwebtoken');

module.exports = (req, res, next) => {
  const header = req.get('Authorization');
  if (!header?.startsWith('Bearer ')) {
    return res.status(401).json({ error: 'No token provided' });
  }

  const token = header.slice(7);
  try {
    const decoded = jwt.verify(token, process.env.JWT_SECRET);
    req.user = decoded; // attach user to request
    next();
  } catch (err) {
    res.status(401).json({ error: 'Invalid or expired token' });
  }
};
```

**Using it selectively:**
```js
const authenticate = require('./middleware/authenticate');

// Public routes — no auth
router.post('/auth/register', registerHandler);
router.post('/auth/login', loginHandler);

// Protected routes — auth required
router.get('/profile', authenticate, getProfileHandler);
router.put('/profile', authenticate, updateProfileHandler);

// Or protect an entire router:
router.use(authenticate); // all routes below require auth
```

### Role-Based Authorisation

```js
// middleware/authorize.js
const authorize = (...roles) => (req, res, next) => {
  if (!roles.includes(req.user.role)) {
    return res.status(403).json({ error: 'Insufficient permissions' });
  }
  next();
};

// Usage: authenticate first, then authorize
router.delete('/users/:id', authenticate, authorize('admin'), deleteUserHandler);
```

### CORS — Cross-Origin Resource Sharing

When your React frontend (`http://localhost:5173`) calls your Express API (`http://localhost:3000`), the browser blocks it — different origins.

```bash
npm install cors
```

```js
const cors = require('cors');

// Development — allow all origins (DO NOT use in production)
app.use(cors());

// Production — whitelist specific origins
app.use(cors({
  origin: ['https://careeros.app', 'https://www.careeros.app'],
  methods: ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'],
  allowedHeaders: ['Content-Type', 'Authorization'],
  credentials: true, // allow cookies/auth headers cross-origin
}));
```

CORS must be registered **before** your routes.

### Request Validation with express-validator

Never trust client input. Validate at the route level before reaching your business logic.

```bash
npm install express-validator
```

```js
const { body, param, validationResult } = require('express-validator');

// Reusable middleware to check results
const validate = (req, res, next) => {
  const errors = validationResult(req);
  if (!errors.isEmpty()) {
    return res.status(422).json({ errors: errors.array() });
  }
  next();
};

// User registration with validation
router.post('/register',
  body('name').trim().isLength({ min: 2, max: 50 }).withMessage('Name must be 2-50 chars'),
  body('email').isEmail().normalizeEmail().withMessage('Invalid email'),
  body('password').isLength({ min: 8 }).withMessage('Password must be at least 8 chars'),
  validate,
  asyncHandler(async (req, res) => {
    const { name, email, password } = req.body;
    const user = await User.create({ name, email, password });
    res.status(201).json(user);
  })
);

// Route param validation
router.get('/:id',
  param('id').isInt({ min: 1 }).withMessage('ID must be a positive integer'),
  validate,
  asyncHandler(async (req, res) => {
    const user = await User.findById(Number(req.params.id));
    if (!user) throw new AppError('Not found', 404);
    res.json(user);
  })
);
```

### Complete Auth Flow in Practice

```
POST /auth/login
  ↓ validate body (email, password)
  ↓ find user by email
  ↓ compare password with bcrypt
  ↓ sign JWT
  ↓ return { token, user }

GET /profile
  ↓ authenticate middleware (verify JWT, attach req.user)
  ↓ route handler reads req.user.id
  ↓ return profile data
```

### Key Takeaways

- JWT: sign on login with `jwt.sign()`, verify in middleware with `jwt.verify()`
- Attach decoded user to `req.user` — available in all downstream middleware and handlers
- CORS must come before routes; configure specific origins in production
- Validate all user input with `express-validator` — return 422 on failure
- Authenticate (who are you?) and authorise (what can you do?) are two separate middleware
MD; }

    // ═══════════════════════════════════════════════════════════════
    //  LEVEL 3 LESSONS
    // ═══════════════════════════════════════════════════════════════

    private function l3_1(): string { return <<<'MD'
## Security: Helmet, Rate Limiting & Input Sanitisation

A publicly-exposed Express API is a target. These three layers cover the most common attack surfaces.

### Helmet — Security Headers in One Line

Helmet sets HTTP response headers that protect against well-known browser-based attacks.

```bash
npm install helmet
```

```js
const helmet = require('helmet');
app.use(helmet()); // Must come before routes
```

What helmet sets:
| Header | Protects Against |
|---|---|
| `Content-Security-Policy` | XSS — restricts scripts/styles sources |
| `X-Frame-Options: DENY` | Clickjacking |
| `X-Content-Type-Options: nosniff` | MIME-type sniffing |
| `Strict-Transport-Security` | SSL stripping (forces HTTPS) |
| Removes `X-Powered-By: Express` | Information disclosure |

```js
// Custom CSP for an API-only Express app:
app.use(helmet({
  contentSecurityPolicy: {
    directives: {
      defaultSrc: ["'self'"],
      scriptSrc: ["'self'"],
    },
  },
}));
```

### Rate Limiting — Preventing Brute Force & DoS

```bash
npm install express-rate-limit
```

```js
const rateLimit = require('express-rate-limit');

// Global: 100 requests per 15 minutes per IP
const globalLimiter = rateLimit({
  windowMs: 15 * 60 * 1000,
  max: 100,
  standardHeaders: true, // Returns RateLimit-* headers
  legacyHeaders: false,
  message: { error: 'Too many requests, please try again later.' },
});

// Strict: for auth endpoints (brute-force protection)
const authLimiter = rateLimit({
  windowMs: 15 * 60 * 1000,
  max: 5, // only 5 login attempts per 15 min
  message: { error: 'Too many login attempts, please try again in 15 minutes.' },
});

app.use(globalLimiter);
app.use('/api/v1/auth/login', authLimiter);
```

**For multi-server deployments**, use a Redis store so rate limit state is shared:
```bash
npm install rate-limit-redis ioredis
```

```js
const { RedisStore } = require('rate-limit-redis');
const Redis = require('ioredis');
const client = new Redis(process.env.REDIS_URL);

const limiter = rateLimit({
  windowMs: 15 * 60 * 1000,
  max: 100,
  store: new RedisStore({ sendCommand: (...args) => client.call(...args) }),
});
```

### Input Sanitisation — Preventing XSS & Injection

Validation checks format. Sanitisation cleans the data.

**With express-validator (sanitisers):**
```js
const { body } = require('express-validator');

body('name').trim().escape()              // remove HTML tags, trim whitespace
body('email').normalizeEmail()            // lowercase, remove dots from gmail
body('website').trim().isURL()           // validate and trim
body('age').toInt()                       // convert string to integer
body('isAdmin').toBoolean()              // convert to boolean
```

**SQL Injection — use parameterised queries:**
```js
// DANGEROUS — never do this:
db.query(`SELECT * FROM users WHERE email = '${email}'`);

// SAFE — parameterised:
db.query('SELECT * FROM users WHERE email = ?', [email]);
// or with an ORM (Sequelize/Prisma) — parameters are handled automatically
```

**Preventing MongoDB Injection:**
```bash
npm install express-mongo-sanitize
```
```js
const mongoSanitize = require('express-mongo-sanitize');
app.use(mongoSanitize()); // strips $ and . from user input
```

### HTTPS & Secure Cookies

```js
// Force HTTPS in production
app.use((req, res, next) => {
  if (process.env.NODE_ENV === 'production' && !req.secure) {
    return res.redirect(301, 'https://' + req.hostname + req.url);
  }
  next();
});

// Secure cookies
res.cookie('session', token, {
  httpOnly: true,  // not accessible to JavaScript
  secure: true,    // HTTPS only
  sameSite: 'strict', // no cross-site sending
  maxAge: 7 * 24 * 60 * 60 * 1000, // 7 days
});
```

### Security Checklist for Every Express API

```
✓ app.use(helmet())                    — security headers
✓ app.use(cors({ origin: whitelist })) — restrict origins
✓ app.use(rateLimit({ ... }))          — rate limiting
✓ body().trim().escape()               — sanitise input
✓ Parameterised queries                — no SQL injection
✓ bcrypt passwords (rounds ≥ 12)       — no plaintext
✓ JWT secrets in env vars              — not hardcoded
✓ HTTPS only in production             — no plaintext traffic
✓ httpOnly, Secure cookies             — protect session tokens
✓ Remove X-Powered-By                  — helmet does this
```

### Key Takeaways

- `helmet()` is one line — no reason not to use it on every Express app
- Rate limit auth endpoints tightly (5 per 15 min) and everything else loosely (100 per 15 min)
- Sanitise input after validation — trim, escape, normalise
- NEVER interpolate user input into SQL queries — always use parameterised queries
- Store secrets in environment variables — never hardcode JWT secrets or DB passwords
MD; }

    private function l3_2(): string { return <<<'MD'
## Testing Express APIs with Supertest & Jest

Well-tested APIs catch bugs before deployment. The standard stack for Express: **Jest** as the test runner + **Supertest** for HTTP assertions.

### Setup

```bash
npm install --save-dev jest supertest
```

**package.json:**
```json
{
  "scripts": {
    "test": "jest",
    "test:watch": "jest --watchAll"
  },
  "jest": {
    "testEnvironment": "node"
  }
}
```

**Critical: separate app from server**

```js
// app.js — exports the Express app (no listen call)
const express = require('express');
const app = express();
// ... routes, middleware ...
module.exports = app;

// server.js — starts the server (not imported in tests)
const app = require('./app');
app.listen(3000);
```

Supertest calls `app.listen()` internally on a random port — no port conflicts.

### Writing Tests

```js
// __tests__/posts.test.js
const request = require('supertest');
const app = require('../app');

describe('POST /api/v1/posts', () => {
  it('creates a post and returns 201', async () => {
    const res = await request(app)
      .post('/api/v1/posts')
      .send({ title: 'Hello', body: 'World' })
      .expect('Content-Type', /json/)
      .expect(201);

    expect(res.body).toMatchObject({ title: 'Hello', body: 'World' });
    expect(res.body.id).toBeDefined();
  });

  it('returns 422 when title is missing', async () => {
    const res = await request(app)
      .post('/api/v1/posts')
      .send({ body: 'No title' })
      .expect(422);

    expect(res.body.errors).toBeDefined();
  });
});

describe('GET /api/v1/posts/:id', () => {
  it('returns 404 for non-existent post', async () => {
    await request(app)
      .get('/api/v1/posts/99999')
      .expect(404);
  });
});
```

### Testing Protected Routes

```js
// helpers/auth.js
const jwt = require('jsonwebtoken');

const getTestToken = (overrides = {}) =>
  jwt.sign({ id: 1, email: 'test@test.com', role: 'user', ...overrides },
    process.env.JWT_SECRET || 'test-secret');

// In tests:
it('returns profile for authenticated user', async () => {
  const token = getTestToken();
  const res = await request(app)
    .get('/api/v1/profile')
    .set('Authorization', `Bearer ${token}`)
    .expect(200);
  expect(res.body.email).toBe('test@test.com');
});

it('returns 401 without token', async () => {
  await request(app).get('/api/v1/profile').expect(401);
});
```

### Unit Testing Services

Service functions (business logic) should be tested independently of HTTP:

```js
// services/userService.js
const userService = {
  async createUser({ name, email, password }) {
    // hash password, save to DB
  },
  async findByEmail(email) { /* ... */ }
};

// __tests__/userService.test.js
const userService = require('../services/userService');
const db = require('../db');

jest.mock('../db'); // mock the database

describe('userService.createUser', () => {
  it('hashes the password before saving', async () => {
    db.users.create.mockResolvedValue({ id: 1, name: 'Alice' });
    const user = await userService.createUser({ name: 'Alice', email: 'a@a.com', password: 'plaintext' });
    const calledWith = db.users.create.mock.calls[0][0];
    expect(calledWith.password).not.toBe('plaintext'); // should be hashed
  });
});
```

### Database in Tests

**Option 1: Mock the DB layer** — fast, but can miss real DB behaviour.

**Option 2: Use a test database** — more reliable, run migrations before tests.

```js
// jest.setup.js
const db = require('./db');

beforeAll(async () => {
  await db.migrate.latest(); // run migrations
  await db.seed.run();       // seed test data
});

afterAll(async () => {
  await db.destroy();
});
```

```json
// package.json
"jest": {
  "globalSetup": "./jest.setup.js"
}
```

### Test Coverage

```bash
npx jest --coverage
```

Aim for:
- 80%+ line coverage on routes and controllers
- 100% on critical paths (auth, payments, data mutations)
- Don't chase 100% everywhere — test behaviour, not implementation

### Key Takeaways

- Separate `app.js` (exports app) from `server.js` (calls `listen`) — essential for Supertest
- Supertest makes real HTTP calls without starting a server — fast and reliable
- Test happy paths, sad paths (missing fields, wrong type), and auth paths (401, 403)
- Unit test services in isolation with mocked dependencies — fast feedback
- Run tests in CI before every merge — catch regressions automatically
MD; }

    private function l3_3(): string { return <<<'MD'
## Application Architecture: Controllers, Services & Repositories

Good Express apps don't live in route files. Separating concerns makes code testable, readable, and maintainable as the project grows.

### The Layered Architecture

```
HTTP Request
    ↓
Router (routes/) — defines endpoints, delegates
    ↓
Controller (controllers/) — handles req/res, calls service
    ↓
Service (services/) — business logic, no HTTP objects
    ↓
Repository (repositories/) — database access
    ↓
Database
```

**The Golden Rule:** HTTP objects (`req`, `res`) must never leave the controller layer. Services receive plain data and return plain data.

### The Router Layer — Thin Route Definitions

```js
// routes/users.js
const router = require('express').Router();
const userController = require('../controllers/userController');
const authenticate = require('../middleware/authenticate');
const { validateCreateUser } = require('../middleware/validators/userValidators');

router.get('/',    authenticate, userController.index);
router.post('/',   validateCreateUser, userController.create);
router.get('/:id', authenticate, userController.show);
router.put('/:id', authenticate, userController.update);
router.delete('/:id', authenticate, userController.destroy);

module.exports = router;
```

### The Controller Layer — HTTP Glue

Controllers extract data from req, call the service, and format the response:

```js
// controllers/userController.js
const asyncHandler = require('../middleware/asyncHandler');
const userService = require('../services/userService');
const AppError = require('../utils/AppError');

const userController = {
  index: asyncHandler(async (req, res) => {
    const { page = 1, limit = 20 } = req.query;
    const result = await userService.findAll({ page: Number(page), limit: Number(limit) });
    res.json(result);
  }),

  create: asyncHandler(async (req, res) => {
    const { name, email, password } = req.body;
    const user = await userService.create({ name, email, password });
    res.status(201).json(user);
  }),

  show: asyncHandler(async (req, res) => {
    const user = await userService.findById(Number(req.params.id));
    if (!user) throw new AppError('User not found', 404);
    res.json(user);
  }),

  update: asyncHandler(async (req, res) => {
    const user = await userService.update(Number(req.params.id), req.body);
    res.json(user);
  }),

  destroy: asyncHandler(async (req, res) => {
    await userService.delete(Number(req.params.id));
    res.status(204).end();
  }),
};

module.exports = userController;
```

### The Service Layer — Business Logic

Services contain the WHAT and WHY of your app. No `req`, no `res`:

```js
// services/userService.js
const bcrypt = require('bcryptjs');
const userRepository = require('../repositories/userRepository');
const AppError = require('../utils/AppError');

const userService = {
  async findAll({ page, limit }) {
    const offset = (page - 1) * limit;
    return userRepository.findAll({ limit, offset });
  },

  async findById(id) {
    return userRepository.findById(id);
  },

  async create({ name, email, password }) {
    const existing = await userRepository.findByEmail(email);
    if (existing) throw new AppError('Email already in use', 409);

    const hashed = await bcrypt.hash(password, 12);
    const { password: _, ...user } = await userRepository.create({
      name, email, password: hashed,
    });
    return user;
  },

  async update(id, data) {
    const user = await userRepository.findById(id);
    if (!user) throw new AppError('User not found', 404);
    return userRepository.update(id, data);
  },

  async delete(id) {
    return userRepository.delete(id);
  },
};

module.exports = userService;
```

### The Repository Layer — Database Access

Repositories are the only layer that talks to the database:

```js
// repositories/userRepository.js
const db = require('../db'); // knex, sequelize, prisma, etc.

const userRepository = {
  findAll({ limit, offset }) {
    return db('users').select('id', 'name', 'email', 'created_at').limit(limit).offset(offset);
  },
  findById(id) {
    return db('users').where({ id }).first();
  },
  findByEmail(email) {
    return db('users').where({ email }).first();
  },
  create(data) {
    return db('users').insert(data).returning('*').then(r => r[0]);
  },
  update(id, data) {
    return db('users').where({ id }).update(data).returning('*').then(r => r[0]);
  },
  delete(id) {
    return db('users').where({ id }).delete();
  },
};

module.exports = userRepository;
```

### Why This Separation?

| Layer | Testable without | Because |
|---|---|---|
| Repository | Service/Controller | Just DB queries |
| Service | HTTP/Express | Receives plain objects |
| Controller | Database | Mocks the service |

### Key Takeaways

- **Router:** defines endpoints, applies middleware — no business logic
- **Controller:** handles req/res — calls service with plain data, sends response
- **Service:** business logic — no req/res, throws AppError for domain failures
- **Repository:** database queries only — no business logic, no HTTP
- This separation makes each layer independently testable and replaceable
MD; }

    // ═══════════════════════════════════════════════════════════════
    //  LEVEL 4 LESSONS
    // ═══════════════════════════════════════════════════════════════

    private function l4_1(): string { return <<<'MD'
## Performance: Compression, Caching & ETags

Fast APIs retain users. These techniques reduce response sizes, avoid redundant processing, and speed up every request.

### Compression — Smaller Payloads

The `compression` middleware applies gzip to responses, reducing JSON payload size by 70-90%:

```bash
npm install compression
```

```js
const compression = require('compression');

// Apply to all responses
app.use(compression());

// Or with options — skip small responses and already-compressed files
app.use(compression({
  threshold: 1024,     // only compress if response > 1KB
  filter: (req, res) => {
    if (req.headers['x-no-compression']) return false;
    return compression.filter(req, res);
  },
}));
```

Do **not** compress: images, videos, audio — they're already compressed. Do **not** compress if nginx or a CDN is in front — offload it there.

### HTTP Caching with Cache-Control

Tell clients and CDNs how long to cache responses:

```js
// Public — safe for CDNs (GET /products list that changes hourly)
res.set('Cache-Control', 'public, max-age=3600'); // cache 1 hour

// Private — user-specific data (cache in browser only, not CDN)
res.set('Cache-Control', 'private, max-age=300'); // cache 5 min

// No caching — always fresh (auth endpoints, mutation results)
res.set('Cache-Control', 'no-store');
```

**Middleware to add cache headers by route:**
```js
const cache = (seconds) => (req, res, next) => {
  res.set('Cache-Control', `public, max-age=${seconds}`);
  next();
};

app.get('/products', cache(3600), productController.index);
app.get('/users/profile', authenticate, cache(0), profileController.show);
```

### ETags — Conditional GET Requests

Express automatically generates ETags for `res.json()` and `res.send()` responses. The client sends `If-None-Match` on subsequent requests:

```
Client → GET /posts/1
Server → 200 { title: "Hello" }, ETag: "abc123"

Client → GET /posts/1, If-None-Match: "abc123"
Server → 304 Not Modified (no body — saves bandwidth)
```

```js
// ETags are on by default — disable if you don't want them:
app.set('etag', false);

// Manual ETag for dynamic data:
app.get('/posts/:id', asyncHandler(async (req, res) => {
  const post = await postService.findById(req.params.id);
  const etag = `"${post.id}-${post.updatedAt.getTime()}"`;
  res.set('ETag', etag);
  if (req.get('If-None-Match') === etag) {
    return res.status(304).end();
  }
  res.json(post);
}));
```

### In-Memory Caching with node-cache

For expensive computations or rarely-changing data:

```bash
npm install node-cache
```

```js
const NodeCache = require('node-cache');
const cache = new NodeCache({ stdTTL: 300 }); // 5 minutes default

const cacheMiddleware = (ttl = 300) => async (req, res, next) => {
  const key = req.originalUrl;
  const cached = cache.get(key);
  if (cached) return res.json(cached);

  const originalJson = res.json.bind(res);
  res.json = (data) => {
    cache.set(key, data, ttl);
    return originalJson(data);
  };
  next();
};

// Cache product list for 5 minutes
app.get('/api/v1/products', cacheMiddleware(300), productController.index);
```

### Redis Caching for Production

```bash
npm install ioredis
```

```js
const Redis = require('ioredis');
const redis = new Redis(process.env.REDIS_URL);

const redisCache = (ttl = 300) => async (req, res, next) => {
  const key = `cache:${req.originalUrl}`;
  const cached = await redis.get(key);
  if (cached) return res.json(JSON.parse(cached));

  const originalJson = res.json.bind(res);
  res.json = async (data) => {
    await redis.setex(key, ttl, JSON.stringify(data));
    return originalJson(data);
  };
  next();
};
```

### Response Streaming for Large Datasets

Instead of loading everything into memory, stream large results:

```js
const { Readable } = require('stream');

app.get('/export/users', authenticate, async (req, res) => {
  res.setHeader('Content-Type', 'application/json');
  res.setHeader('Transfer-Encoding', 'chunked');

  res.write('[');
  let first = true;

  const cursor = db('users').stream();
  cursor.on('data', (user) => {
    res.write((first ? '' : ',') + JSON.stringify(user));
    first = false;
  });
  cursor.on('end', () => { res.write(']'); res.end(); });
  cursor.on('error', (err) => next(err));
});
```

### Key Takeaways

- `compression()` middleware reduces JSON payloads 70-90% — use it unless nginx handles it
- `Cache-Control: public, max-age=N` for public data; `no-store` for auth/sensitive routes
- ETags are automatic in Express — clients get 304 Not Modified on unchanged data
- Use node-cache for simple in-process caching; Redis for distributed/multi-server caching
- Stream large datasets instead of buffering everything in memory
MD; }

    private function l4_2(): string { return <<<'MD'
## Real-Time: WebSockets & Server-Sent Events with Express

REST is request/response — great for most APIs. For real-time features (live notifications, chat, dashboards), you need a persistent connection.

### Server-Sent Events (SSE) — One-Way Push

SSE is the simplest real-time option: the server pushes events to the client over a long-lived HTTP connection. No extra package needed.

**When to use SSE:** live feeds, progress bars, notifications, dashboards — when you only need server → client data.

```js
// Express SSE endpoint
app.get('/events/notifications', authenticate, (req, res) => {
  // Required headers
  res.setHeader('Content-Type', 'text/event-stream');
  res.setHeader('Cache-Control', 'no-cache');
  res.setHeader('Connection', 'keep-alive');
  res.flushHeaders(); // send headers immediately

  // Helper to send an event
  const send = (event, data) => {
    res.write(`event: ${event}\n`);
    res.write(`data: ${JSON.stringify(data)}\n\n`);
  };

  // Send initial state
  send('connected', { message: 'Subscribed to notifications' });

  // Push a live event every 30 seconds (or from your business logic)
  const interval = setInterval(() => {
    send('heartbeat', { ts: Date.now() });
  }, 30000);

  // Cleanup when client disconnects
  req.on('close', () => {
    clearInterval(interval);
    console.log('Client disconnected from SSE');
  });
});
```

**Browser side:**
```js
const es = new EventSource('/events/notifications', { withCredentials: true });
es.addEventListener('connected', e => console.log(JSON.parse(e.data)));
es.addEventListener('heartbeat', e => console.log('ping', JSON.parse(e.data)));
```

### SSE with an Event Bus

For pushing real events (e.g., when a DB write happens elsewhere):

```js
// utils/sseClients.js — in-memory client registry
const clients = new Map();

module.exports = {
  add: (userId, res) => clients.set(userId, res),
  remove: (userId) => clients.delete(userId),
  push: (userId, event, data) => {
    const res = clients.get(userId);
    if (res) {
      res.write(`event: ${event}\n`);
      res.write(`data: ${JSON.stringify(data)}\n\n`);
    }
  },
  broadcast: (event, data) => {
    clients.forEach(res => {
      res.write(`event: ${event}\n`);
      res.write(`data: ${JSON.stringify(data)}\n\n`);
    });
  },
};
```

```js
// In a route handler — push to a specific user after an action
const sseClients = require('../utils/sseClients');

app.post('/orders', authenticate, asyncHandler(async (req, res) => {
  const order = await orderService.create(req.body, req.user.id);
  sseClients.push(req.user.id, 'order:created', order);
  res.status(201).json(order);
}));
```

### WebSockets with the `ws` Library

WebSockets are bidirectional — client and server can both send messages. Use for: chat, multiplayer games, collaborative editing, live trading.

```bash
npm install ws
```

```js
// server.js
const http = require('http');
const { WebSocketServer } = require('ws');
const app = require('./app');

const server = http.createServer(app); // share the HTTP server
const wss = new WebSocketServer({ server });

const rooms = new Map(); // Map<roomId, Set<WebSocket>>

wss.on('connection', (ws, req) => {
  console.log('Client connected');

  ws.on('message', (raw) => {
    const msg = JSON.parse(raw);

    if (msg.type === 'join') {
      if (!rooms.has(msg.room)) rooms.set(msg.room, new Set());
      rooms.get(msg.room).add(ws);
      ws.roomId = msg.room;
      ws.send(JSON.stringify({ type: 'joined', room: msg.room }));
    }

    if (msg.type === 'message') {
      const room = rooms.get(ws.roomId);
      if (room) {
        room.forEach(client => {
          if (client !== ws && client.readyState === ws.OPEN) {
            client.send(JSON.stringify({ type: 'message', text: msg.text }));
          }
        });
      }
    }
  });

  ws.on('close', () => {
    const room = rooms.get(ws.roomId);
    if (room) room.delete(ws);
    console.log('Client disconnected');
  });
});

server.listen(3000, () => console.log('Server + WS on :3000'));
```

**Client side:**
```js
const ws = new WebSocket('ws://localhost:3000');
ws.onopen = () => ws.send(JSON.stringify({ type: 'join', room: 'general' }));
ws.onmessage = (e) => console.log(JSON.parse(e.data));
ws.send(JSON.stringify({ type: 'message', text: 'Hello room!' }));
```

### SSE vs WebSockets

| | SSE | WebSocket |
|---|---|---|
| Direction | Server → Client only | Bidirectional |
| Protocol | HTTP | WS (upgrade from HTTP) |
| Reconnect | Automatic | Manual |
| Use case | Notifications, feeds | Chat, games |
| Complexity | Simple | More complex |
| HTTP/2 | Works great | Separate connection |

### Key Takeaways

- SSE is HTTP — works through proxies, no extra protocol, auto-reconnects — prefer it for server-push
- WebSockets are bidirectional — required for chat, collaborative apps, real-time games
- For WebSockets, share the HTTP server: `new WebSocketServer({ server })` where `server = http.createServer(app)`
- Track connected clients in a Map for broadcasting targeted events
- Clean up on `req.on('close')` (SSE) or `ws.on('close')` (WS) — prevent memory leaks
MD; }

    private function l4_3(): string { return <<<'MD'
## Deployment: Docker, PM2, Health Checks & Production Configuration

Writing the app is half the work. Running it reliably in production requires containerisation, process management, and operational readiness.

### Environment Configuration

Never hardcode configuration. Use environment variables:

```js
// config/index.js
module.exports = {
  port: parseInt(process.env.PORT, 10) || 3000,
  nodeEnv: process.env.NODE_ENV || 'development',
  db: {
    url: process.env.DATABASE_URL,
  },
  jwt: {
    secret: process.env.JWT_SECRET,
    expiresIn: process.env.JWT_EXPIRES_IN || '7d',
  },
  redis: {
    url: process.env.REDIS_URL || 'redis://localhost:6379',
  },
};
```

```bash
# .env (never commit this)
PORT=3000
NODE_ENV=production
DATABASE_URL=mysql://user:pass@db:3306/myapp
JWT_SECRET=a-very-long-random-string
```

```js
// Load .env in development only
if (process.env.NODE_ENV !== 'production') {
  require('dotenv').config();
}
```

### Health Check Endpoints

Health checks let load balancers and Kubernetes know the app is ready to receive traffic:

```js
// routes/health.js
const router = require('express').Router();
const db = require('../db');

// Liveness — is the process alive?
router.get('/health/live', (req, res) => {
  res.json({ status: 'ok', uptime: process.uptime() });
});

// Readiness — can it serve traffic? (checks dependencies)
router.get('/health/ready', async (req, res) => {
  try {
    await db.raw('SELECT 1'); // check DB connection
    res.json({ status: 'ready', db: 'connected' });
  } catch (err) {
    res.status(503).json({ status: 'not ready', db: 'disconnected' });
  }
});

module.exports = router;
```

```js
// app.js — health checks before auth middleware (no auth required)
app.use('/', require('./routes/health'));
app.use('/api/v1', authenticate, routes);
```

### Graceful Shutdown

On SIGTERM (from Docker/Kubernetes), finish in-flight requests before exiting:

```js
// server.js
const server = app.listen(config.port, () => {
  console.log(`Server on :${config.port}`);
});

const shutdown = (signal) => {
  console.log(`${signal} received — shutting down gracefully`);
  server.close(async () => {
    await db.destroy(); // close DB connections
    console.log('Server closed');
    process.exit(0);
  });

  // Force exit if shutdown takes too long
  setTimeout(() => process.exit(1), 10000);
};

process.on('SIGTERM', () => shutdown('SIGTERM'));
process.on('SIGINT',  () => shutdown('SIGINT'));
```

### Dockerising an Express App

**Dockerfile:**
```dockerfile
FROM node:20-alpine AS base
WORKDIR /app

# Install dependencies separately (Docker layer cache)
COPY package*.json ./
RUN npm ci --only=production

COPY . .

# Non-root user for security
RUN addgroup -g 1001 -S nodejs && adduser -S express -u 1001
USER express

EXPOSE 3000
CMD ["node", "server.js"]
```

**docker-compose.yml (local dev):**
```yaml
version: '3.9'
services:
  api:
    build: .
    ports: ["3000:3000"]
    environment:
      NODE_ENV: development
      DATABASE_URL: mysql://root:password@db:3306/myapp
    depends_on: [db]
    volumes: ["./src:/app/src"] # hot reload in dev

  db:
    image: mysql:8
    environment:
      MYSQL_ROOT_PASSWORD: password
      MYSQL_DATABASE: myapp
    ports: ["3306:3306"]
```

```bash
docker compose up       # start all services
docker compose down     # stop and remove containers
docker compose logs api # view logs
```

### PM2 — Process Manager for Node.js

PM2 keeps your Node.js process alive, handles clustering, and monitors memory:

```bash
npm install -g pm2
```

**ecosystem.config.js:**
```js
module.exports = {
  apps: [{
    name: 'careeros-api',
    script: 'server.js',
    instances: 'max',      // one process per CPU core
    exec_mode: 'cluster',  // share port across instances
    watch: false,
    max_memory_restart: '500M',
    env: { NODE_ENV: 'production' },
    error_file: 'logs/error.log',
    out_file: 'logs/out.log',
  }],
};
```

```bash
pm2 start ecosystem.config.js
pm2 status     # view processes
pm2 logs       # tail logs
pm2 reload all # zero-downtime reload
pm2 save       # save process list
pm2 startup    # auto-start on system boot
```

### Key Takeaways

- Store all config in environment variables — never hardcode secrets
- `/health/live` and `/health/ready` are required for containerised deployments
- Graceful shutdown: close the HTTP server, drain connections, close DB pool — then `process.exit(0)`
- Dockerfile: use `npm ci --only=production`, run as a non-root user, separate dependency install from code copy
- PM2 with `cluster` mode and `instances: "max"` fully utilises multi-core machines
MD; }

    // ═══════════════════════════════════════════════════════════════
    //  LEVEL 5 LESSONS
    // ═══════════════════════════════════════════════════════════════

    private function l5_1(): string { return <<<'MD'
## Microservices with Express: API Gateways & Service Communication

At scale, a single Express monolith becomes a bottleneck. Microservices split the app into independently deployable services. Express is an ideal building block for each service.

### Monolith vs Microservices

| | Monolith | Microservices |
|---|---|---|
| Deployment | One unit | Independent per service |
| Scaling | Scale everything | Scale individual services |
| Failure | One crash = all down | Isolated failures |
| Development | Simple at first | Complex coordination |
| Team | Small team | Multiple teams |

Start with a monolith. Migrate to microservices when you have specific pain points, not as a default.

### The API Gateway Pattern

The gateway is the single entry point for all clients. It handles cross-cutting concerns:

```js
// gateway/app.js
const express = require('express');
const { createProxyMiddleware } = require('http-proxy-middleware');

const app = express();

// Cross-cutting concerns at the gateway
app.use(helmet());
app.use(cors({ origin: process.env.ALLOWED_ORIGINS.split(',') }));
app.use(globalRateLimiter);
app.use(authenticate); // JWT verified once here — services trust the gateway

// Route to upstream services
app.use('/api/v1/users',   createProxyMiddleware({ target: 'http://user-service:3001', changeOrigin: true }));
app.use('/api/v1/posts',   createProxyMiddleware({ target: 'http://post-service:3002', changeOrigin: true }));
app.use('/api/v1/orders',  createProxyMiddleware({ target: 'http://order-service:3003', changeOrigin: true }));

app.listen(3000);
```

### Service-to-Service Communication

**Synchronous (HTTP):** one service calls another's API:

```js
// order-service calling user-service
const axios = require('axios');

async function getUser(userId) {
  const res = await axios.get(`http://user-service:3001/internal/users/${userId}`, {
    headers: { 'X-Internal-Token': process.env.INTERNAL_SECRET },
    timeout: 5000,
  });
  return res.data;
}

// In order controller
app.post('/orders', authenticate, asyncHandler(async (req, res) => {
  const user = await getUser(req.user.id); // call user service
  const order = await orderService.create({ ...req.body, user });
  res.status(201).json(order);
}));
```

**Asynchronous (Message Queue):** service publishes an event; others subscribe — decoupled:

```js
// Using Redis pub/sub as a simple message bus
const Redis = require('ioredis');
const publisher = new Redis(process.env.REDIS_URL);
const subscriber = new Redis(process.env.REDIS_URL);

// After creating an order — publish event
await publisher.publish('order:created', JSON.stringify({ orderId: order.id, userId: user.id }));

// In notification-service — subscribe to the event
subscriber.subscribe('order:created');
subscriber.on('message', async (channel, message) => {
  if (channel === 'order:created') {
    const { orderId, userId } = JSON.parse(message);
    await notificationService.sendOrderConfirmation(userId, orderId);
  }
});
```

### Circuit Breaker Pattern

Prevent cascading failures when a downstream service is down:

```js
// Using opossum circuit breaker
const CircuitBreaker = require('opossum');

const options = {
  timeout: 3000,         // if function takes longer than 3s, trigger failure
  errorThresholdPercentage: 50, // open circuit if 50% of requests fail
  resetTimeout: 30000,   // try again after 30s
};

const getUserBreaker = new CircuitBreaker(getUser, options);
getUserBreaker.fallback((userId) => ({ id: userId, name: 'Unknown' }));

// Usage — falls back gracefully if user-service is down
const user = await getUserBreaker.fire(req.user.id);
```

### Service Discovery & Health

Each service registers itself. The gateway discovers them dynamically:

```js
// Each service registers on startup
const consul = require('consul')();
consul.agent.service.register({
  name: 'user-service',
  address: process.env.HOST,
  port: parseInt(process.env.PORT),
  check: {
    http: `http://${process.env.HOST}:${process.env.PORT}/health/ready`,
    interval: '10s',
  },
});
```

### Distributed Tracing

Correlate requests across services with a Trace ID:

```js
// middleware/tracing.js
const { randomUUID } = require('crypto');

module.exports = (req, res, next) => {
  req.traceId = req.get('X-Trace-Id') || randomUUID();
  res.set('X-Trace-Id', req.traceId); // pass back in response
  next();
};

// Pass trace ID to downstream services
await axios.get(`http://user-service/users/${id}`, {
  headers: { 'X-Trace-Id': req.traceId },
});
```

### Key Takeaways

- The API gateway handles auth, CORS, rate limiting once — services trust it internally
- HTTP for synchronous calls (use circuit breakers); message queues for async events
- Circuit breakers prevent one failing service from cascading to the entire system
- Pass a Trace ID across all service calls for distributed debugging
- Start monolith-first — extract services only when teams or scaling demand it
MD; }

    private function l5_2(): string { return <<<'MD'
## Observability: Structured Logging, Metrics & OpenTelemetry

You cannot fix what you cannot see. Observability — logs, metrics, and traces — gives you visibility into production behaviour.

### Structured Logging with Pino

`console.log` is not production logging. Use `pino` — fast, JSON-structured, level-based:

```bash
npm install pino pino-http
```

```js
// utils/logger.js
const pino = require('pino');

const logger = pino({
  level: process.env.LOG_LEVEL || 'info',
  ...(process.env.NODE_ENV === 'development' && {
    transport: { target: 'pino-pretty' }, // human-readable in dev
  }),
});

module.exports = logger;
```

```js
// app.js
const pinoHttp = require('pino-http');
const logger = require('./utils/logger');

app.use(pinoHttp({ logger })); // logs every request as structured JSON

// In a service or controller:
logger.info({ userId: req.user.id, action: 'post:created' }, 'Post created');
logger.error({ err, userId: req.user.id }, 'Failed to create post');
logger.warn({ attempts: 3 }, 'Rate limit approaching');
```

**Structured JSON log output (production):**
```json
{ "level": "info", "time": 1704067200000, "userId": 42, "action": "post:created", "msg": "Post created" }
{ "level": "error", "time": 1704067201000, "err": { "message": "DB timeout", "stack": "..." }, "msg": "Failed to create post" }
```

JSON logs are searchable in Datadog, CloudWatch, Loki, or any log aggregator.

### Request Context with AsyncLocalStorage

Track request-scoped data (like the trace ID) without passing it through every function:

```js
// utils/context.js
const { AsyncLocalStorage } = require('async_hooks');
const storage = new AsyncLocalStorage();

module.exports = {
  run: (context, fn) => storage.run(context, fn),
  get: () => storage.getStore(),
};
```

```js
// middleware/requestContext.js
const { randomUUID } = require('crypto');
const context = require('../utils/context');

module.exports = (req, res, next) => {
  context.run({ traceId: req.get('X-Trace-Id') || randomUUID(), userId: req.user?.id }, next);
};

// In any service function — no need to pass req
const { get: getCtx } = require('../utils/context');
logger.info({ ...getCtx(), event: 'order:created' }, 'Order created');
```

### Metrics with Prometheus

Prometheus scrapes metrics from your app on a schedule:

```bash
npm install prom-client
```

```js
// utils/metrics.js
const client = require('prom-client');
client.collectDefaultMetrics(); // CPU, memory, event loop lag

const httpRequestDuration = new client.Histogram({
  name: 'http_request_duration_seconds',
  help: 'HTTP request duration in seconds',
  labelNames: ['method', 'route', 'status'],
  buckets: [0.01, 0.05, 0.1, 0.3, 0.5, 1, 2, 5],
});

const httpRequestTotal = new client.Counter({
  name: 'http_requests_total',
  help: 'Total HTTP requests',
  labelNames: ['method', 'route', 'status'],
});

module.exports = { client, httpRequestDuration, httpRequestTotal };
```

```js
// middleware/metricsMiddleware.js
const { httpRequestDuration, httpRequestTotal } = require('../utils/metrics');

module.exports = (req, res, next) => {
  const end = httpRequestDuration.startTimer();
  res.on('finish', () => {
    const labels = { method: req.method, route: req.route?.path || 'unknown', status: res.statusCode };
    end(labels);
    httpRequestTotal.inc(labels);
  });
  next();
};

// Expose /metrics endpoint for Prometheus scraping
app.get('/metrics', async (req, res) => {
  res.set('Content-Type', client.register.contentType);
  res.end(await client.register.metrics());
});
```

### OpenTelemetry — Distributed Tracing

```bash
npm install @opentelemetry/sdk-node @opentelemetry/auto-instrumentations-node @opentelemetry/exporter-trace-otlp-http
```

```js
// tracing.js — run BEFORE requiring app
const { NodeSDK } = require('@opentelemetry/sdk-node');
const { getNodeAutoInstrumentations } = require('@opentelemetry/auto-instrumentations-node');
const { OTLPTraceExporter } = require('@opentelemetry/exporter-trace-otlp-http');

const sdk = new NodeSDK({
  traceExporter: new OTLPTraceExporter({ url: process.env.OTEL_EXPORTER_OTLP_ENDPOINT }),
  instrumentations: [getNodeAutoInstrumentations()],
  serviceName: 'careeros-api',
});
sdk.start();
```

```js
// server.js
require('./tracing'); // must be first
const app = require('./app');
app.listen(3000);
```

OpenTelemetry auto-instruments: Express routes, HTTP client calls, database queries — traces appear in Jaeger, Honeycomb, or Grafana Tempo.

### Alerting — Know Before Users Do

Wire Prometheus metrics to Alertmanager or Grafana alerts:

```yaml
# alert.rules.yml
groups:
  - name: express
    rules:
      - alert: HighErrorRate
        expr: rate(http_requests_total{status=~"5.."}[5m]) / rate(http_requests_total[5m]) > 0.05
        for: 2m
        annotations:
          summary: "Error rate > 5% for 2 minutes"

      - alert: SlowResponses
        expr: histogram_quantile(0.95, http_request_duration_seconds_bucket) > 1
        for: 5m
        annotations:
          summary: "P95 latency > 1 second"
```

### Key Takeaways

- Replace `console.log` with `pino` — structured JSON logs are queryable in any log aggregator
- Expose `/metrics` for Prometheus — track request rate, duration, and error rate
- Use `AsyncLocalStorage` for request context propagation without prop-drilling
- OpenTelemetry auto-instruments Express with zero code changes — enables distributed tracing
- Alert on error rate and P95 latency — catch problems before users notice
MD; }

    private function l5_3(): string { return <<<'MD'
## Advanced Patterns: GraphQL, CQRS & Enterprise Architecture

These patterns solve real problems at scale. Understanding them separates junior developers from senior engineers.

### GraphQL with Express

GraphQL lets clients request exactly the data they need — no over-fetching, no under-fetching.

```bash
npm install graphql @apollo/server express4-apollo-express-middleware
```

```js
// schema/typeDefs.js
const { gql } = require('graphql-tag');
module.exports = gql`
  type User {
    id: ID!
    name: String!
    email: String!
    posts: [Post!]!
  }
  type Post {
    id: ID!
    title: String!
    author: User!
  }
  type Query {
    users: [User!]!
    user(id: ID!): User
    posts: [Post!]!
  }
  type Mutation {
    createPost(title: String!, body: String!): Post!
  }
`;
```

```js
// schema/resolvers.js
module.exports = {
  Query: {
    users: (_, __, { dataSources }) => dataSources.userService.findAll(),
    user: (_, { id }, { dataSources }) => dataSources.userService.findById(id),
  },
  Mutation: {
    createPost: (_, args, { user, dataSources }) => {
      if (!user) throw new Error('Unauthorized');
      return dataSources.postService.create({ ...args, authorId: user.id });
    },
  },
  User: {
    posts: (user, _, { dataSources }) => dataSources.postService.findByAuthor(user.id),
  },
};
```

```js
// app.js — mount Apollo Server on Express
const { ApolloServer } = require('@apollo/server');
const { expressMiddleware } = require('@apollo/server/express4');

const server = new ApolloServer({ typeDefs, resolvers });
await server.start();

app.use('/graphql', express.json(), expressMiddleware(server, {
  context: async ({ req }) => ({
    user: req.user, // from authenticate middleware
    dataSources: { userService, postService },
  }),
}));
```

### CQRS — Command Query Responsibility Segregation

CQRS separates read models from write models. Reads are optimised for display; writes are optimised for consistency.

```
Client
  ├─ Query  → QueryHandler  → Read DB (optimised, denormalised, cached)
  └─ Command → CommandHandler → Write DB (normalised, event sourced)
```

```js
// commands/CreateOrderCommand.js
class CreateOrderCommand {
  constructor({ userId, items, shippingAddress }) {
    this.userId = userId;
    this.items = items;
    this.shippingAddress = shippingAddress;
  }
}

// handlers/CreateOrderHandler.js
class CreateOrderHandler {
  constructor(orderRepository, eventBus) {
    this.orderRepository = orderRepository;
    this.eventBus = eventBus;
  }

  async handle(command) {
    const order = await this.orderRepository.create(command);
    // Publish event — read models update asynchronously
    await this.eventBus.publish('OrderCreated', { orderId: order.id, ...command });
    return order;
  }
}

// Express route — just a thin coordinator
app.post('/orders', authenticate, asyncHandler(async (req, res) => {
  const command = new CreateOrderCommand({ ...req.body, userId: req.user.id });
  const order = await commandBus.dispatch(command);
  res.status(201).json(order);
}));
```

### Repository Pattern with Unit of Work

For complex write operations spanning multiple entities:

```js
// utils/UnitOfWork.js
class UnitOfWork {
  constructor(db) {
    this.db = db;
  }

  async execute(work) {
    return this.db.transaction(async (trx) => {
      const repos = {
        orders: new OrderRepository(trx),
        inventory: new InventoryRepository(trx),
        payments: new PaymentRepository(trx),
      };
      return work(repos); // all DB ops in one atomic transaction
    });
  }
}

// In order service
await uow.execute(async ({ orders, inventory, payments }) => {
  await inventory.decrementStock(items);
  const payment = await payments.charge(amount);
  return orders.create({ items, payment });
});
```

### Domain-Driven Design (DDD) Concepts

```
careeros-api/
├── modules/
│   ├── auth/
│   │   ├── domain/           ← entities, value objects
│   │   ├── application/      ← commands, queries, handlers
│   │   ├── infrastructure/   ← repositories, external APIs
│   │   └── presentation/     ← routes, controllers, DTOs
│   ├── learning/
│   └── practice/
└── shared/
    ├── kernel/               ← base Entity, ValueObject, DomainEvent
    ├── infrastructure/       ← DB, Redis, email
    └── middleware/
```

Each module is self-contained. Cross-module communication goes through domain events, not direct imports.

### Feature Flags

Toggle features without deployment:

```js
// utils/featureFlags.js
const flags = {
  'new-dashboard': process.env.FLAG_NEW_DASHBOARD === 'true',
  'ai-evaluation': process.env.FLAG_AI_EVAL === 'true',
};

module.exports = {
  isEnabled: (flag) => flags[flag] ?? false,
};

// In a route
app.get('/dashboard', authenticate, asyncHandler(async (req, res) => {
  if (featureFlags.isEnabled('new-dashboard')) {
    return res.json(await dashboardV2Service.getData(req.user.id));
  }
  return res.json(await dashboardService.getData(req.user.id));
}));
```

### API Versioning

```js
// Version in URL (most common — explicit, easy to test)
app.use('/api/v1', v1Router);
app.use('/api/v2', v2Router);

// Version in header
app.use((req, res, next) => {
  const version = req.get('API-Version') || 'v1';
  req.apiVersion = version;
  next();
});
```

### Key Takeaways

- GraphQL solves over-fetching and under-fetching — great when many clients need different data shapes
- CQRS separates read and write models — reads can be denormalised/cached without compromising write consistency
- Unit of Work ensures complex multi-entity writes are atomic — all succeed or all roll back
- DDD modules keep domain boundaries explicit — prevents the "big ball of mud" as teams grow
- Feature flags decouple deployment from release — ship code before turning it on
MD; }

    // ═══════════════════════════════════════════════════════════════
    //  LEVEL 4 & 5 MCQ QUESTIONS
    // ═══════════════════════════════════════════════════════════════

    private function seedLevel4Questions(Topic $topic): void
    {
        $questions = [
            [
                'question'    => 'What does the `compression` middleware do and when should you NOT use it?',
                'explanation' => 'The `compression` package applies gzip/deflate to HTTP responses, reducing text-based payloads (JSON, HTML) by 70-90%. You should NOT apply it to: images, videos, or audio (already compressed — adds CPU overhead with no size benefit), or when nginx/CDN in front of Express handles compression — offloading it is more efficient.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'Applies gzip to response bodies — skip for already-compressed content (images/video) or when nginx handles it', 'correct' => true],
                    ['text' => 'Compresses incoming request bodies to reduce parsing time in route handlers', 'correct' => false],
                    ['text' => 'Minifies JavaScript and CSS files served by express.static()', 'correct' => false],
                    ['text' => 'Compresses database query results in memory before sending to Express', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between SSE and WebSockets in Express?',
                'explanation' => 'SSE (Server-Sent Events) is one-directional: server pushes to client over a long-lived HTTP connection. It auto-reconnects, works through HTTP proxies, and is simpler to implement. WebSockets are bidirectional: both client and server can send messages over a persistent WS connection (protocol upgrade from HTTP). Use SSE for notifications/feeds; WebSockets for chat/games/collaborative editing.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'SSE = server-to-client over HTTP (auto-reconnect); WebSocket = bidirectional (requires protocol upgrade)', 'correct' => true],
                    ['text' => 'SSE is faster than WebSockets for all use cases — WebSockets are deprecated', 'correct' => false],
                    ['text' => 'WebSockets work only on port 443; SSE works on any port', 'correct' => false],
                    ['text' => 'They are identical — SSE is just a simpler API for WebSockets', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `app.set("trust proxy", 1)` do in Express?',
                'explanation' => 'When Express runs behind a reverse proxy (nginx, AWS ALB, Cloudflare), the real client IP is in the `X-Forwarded-For` header. `app.set("trust proxy", 1)` tells Express to trust the first proxy hop — making `req.ip`, `req.protocol` (http vs https), and `req.secure` reflect the real client values rather than the proxy\'s. Required for correct rate limiting and HTTPS detection behind a proxy.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'Trusts X-Forwarded-For from the proxy — fixes req.ip, req.protocol, and req.secure behind a load balancer', 'correct' => true],
                    ['text' => 'Enables HTTPS proxying — all requests are forwarded over TLS to the upstream', 'correct' => false],
                    ['text' => 'Allows the proxy server to bypass authentication middleware', 'correct' => false],
                    ['text' => 'Distributes incoming requests to 1 worker process via PM2 clustering', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How does Cache-Control differ between `public`, `private`, and `no-store`?',
                'explanation' => '`Cache-Control: public, max-age=3600` — safe for CDNs and shared caches (e.g. product listings). `private, max-age=300` — browser can cache but CDNs cannot (user-specific data like profile pages). `no-store` — nothing is cached anywhere, ever (financial data, auth tokens). `no-cache` (different from no-store) — cache but revalidate with server before using.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'public = CDN-cacheable; private = browser-only cache; no-store = no caching anywhere', 'correct' => true],
                    ['text' => 'public = requires authentication; private = open access; no-store = memory only', 'correct' => false],
                    ['text' => 'They only differ in syntax — all three produce the same caching behaviour', 'correct' => false],
                    ['text' => 'public enables Redis caching; private enables in-memory caching; no-store disables both', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you implement graceful shutdown in an Express app?',
                'explanation' => 'On `SIGTERM` (from Docker stop, Kubernetes pod eviction), call `server.close(callback)` which stops accepting new connections but lets in-flight requests complete. In the callback, close database connections, then `process.exit(0)`. Add a safety timeout (`setTimeout(() => process.exit(1), 10000)`) to force-exit if shutdown hangs. Without graceful shutdown, in-flight requests are cut off mid-response.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'Listen for SIGTERM, call server.close() to drain in-flight requests, close DB, then process.exit(0)', 'correct' => true],
                    ['text' => 'Call process.exit(0) immediately on SIGTERM — Express handles cleanup automatically', 'correct' => false],
                    ['text' => 'Use app.close() — an Express method that waits for all requests to complete', 'correct' => false],
                    ['text' => 'Graceful shutdown is only needed for WebSocket connections, not HTTP', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of separating `app.js` from `server.js` in an Express project?',
                'explanation' => 'Separating the Express app definition (`app.js` — creates app, registers middleware and routes, exports app) from the server startup (`server.js` — imports app, calls `app.listen()`) allows tests to import `app` without starting a real HTTP server. Supertest calls `listen()` internally on a random port. This prevents port conflicts and allows the app to be tested cleanly.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'app.js exports the app for Supertest without starting a server; server.js calls listen() for production', 'correct' => true],
                    ['text' => 'It is purely a code organisation preference — there is no functional difference', 'correct' => false],
                    ['text' => 'server.js handles HTTPS; app.js handles HTTP — required for dual-protocol support', 'correct' => false],
                    ['text' => 'app.js runs in the main process; server.js runs in a cluster worker', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How does PM2 cluster mode differ from fork mode?',
                'explanation' => 'In `fork` mode, PM2 starts a single Node.js process. In `cluster` mode with `instances: "max"`, PM2 uses Node.js\'s built-in cluster module to spawn one worker per CPU core, all sharing the same port. This provides horizontal scaling on multi-core machines — a 4-core server runs 4 instances serving traffic in parallel. The master process manages workers; if one crashes, PM2 restarts it.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'Fork = single process; cluster = one process per CPU core sharing the port, managed by PM2', 'correct' => true],
                    ['text' => 'Cluster mode uses separate ports for each worker — requires a load balancer in front', 'correct' => false],
                    ['text' => 'Fork mode is for production; cluster mode is only for development hot reloading', 'correct' => false],
                    ['text' => 'They are identical — PM2 cluster just adds better log formatting', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What headers are required to implement SSE in Express?',
                'explanation' => 'SSE requires three response headers: `Content-Type: text/event-stream` (tells the browser this is an SSE stream), `Cache-Control: no-cache` (prevents proxies from buffering), and `Connection: keep-alive` (keeps the HTTP connection open). Then call `res.flushHeaders()` to send them immediately. Data is sent as `data: payload\\n\\n` — double newline terminates each event.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'Content-Type: text/event-stream, Cache-Control: no-cache, Connection: keep-alive — then flushHeaders()', 'correct' => true],
                    ['text' => 'Content-Type: application/stream, Transfer-Encoding: chunked, and Upgrade: SSE', 'correct' => false],
                    ['text' => 'Only Content-Type: text/event-stream is required — other headers are set automatically', 'correct' => false],
                    ['text' => 'SSE uses WebSocket headers — Upgrade: websocket and Connection: Upgrade', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `rate-limit-redis` and why is it needed in a multi-server deployment?',
                'explanation' => 'The default `express-rate-limit` stores counters in memory — each server instance has its own count. With 3 servers, a client can make 3x the allowed requests (one full quota per server). `rate-limit-redis` uses a shared Redis store so all instances share the same counter, enforcing the limit correctly across the cluster. Any distributed system with multiple Express instances needs a shared rate limit store.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'Shares rate limit counters across all server instances via Redis — prevents per-instance quota bypass', 'correct' => true],
                    ['text' => 'Caches rate limit config in Redis so each request does not re-read environment variables', 'correct' => false],
                    ['text' => 'rate-limit-redis is faster than in-memory rate limiting — always use it regardless of server count', 'correct' => false],
                    ['text' => 'It stores blocked IPs in Redis so nginx can also reject them at the proxy level', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you handle WebSocket disconnects to prevent memory leaks in Express + ws?',
                'explanation' => 'When a WebSocket client disconnects, the `ws.on("close", handler)` event fires. You must remove the client from any data structures tracking connected clients (Maps, Sets, arrays). If you leave disconnected sockets in your client registry, you accumulate dead references, waste memory, and will error when trying to send to them. Always clean up in the close handler.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'Listen for ws.on("close") and remove the socket from client Maps/Sets to prevent dead reference accumulation', 'correct' => true],
                    ['text' => 'ws handles cleanup automatically — no manual disconnect handling is needed', 'correct' => false],
                    ['text' => 'Call ws.terminate() in the close handler to force GC of the socket object', 'correct' => false],
                    ['text' => 'Dead WebSocket references are garbage collected automatically by Node.js — no cleanup needed', 'correct' => false],
                ],
            ],
        ];

        foreach ($questions as $q) {
            $exists = Question::where('topic_id', $topic->id)->where('question', $q['question'])->exists();
            if ($exists) continue;
            $question = Question::create([
                'topic_id'    => $topic->id,
                'type'        => 'MCQ',
                'difficulty'  => $q['difficulty'],
                'question'    => $q['question'],
                'explanation' => $q['explanation'],
            ]);
            foreach ($q['options'] as $opt) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $opt['text'],
                    'is_correct'  => $opt['correct'],
                ]);
            }
        }
    }

    private function seedLevel5Questions(Topic $topic): void
    {
        $questions = [
            [
                'question'    => 'What is the API Gateway pattern and what responsibilities does it centralise?',
                'explanation' => 'An API Gateway is the single entry point for all clients in a microservices architecture. It centralises cross-cutting concerns: authentication/authorisation, CORS, rate limiting, request logging, SSL termination, and routing to upstream services via a reverse proxy. Each downstream service trusts the gateway — no need to duplicate auth logic in every service. Implemented in Express with `http-proxy-middleware`.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'Single entry point that centralises auth, CORS, rate limiting, logging, and routes to upstream services', 'correct' => true],
                    ['text' => 'A database gateway that translates REST requests into SQL queries for microservices', 'correct' => false],
                    ['text' => 'A caching layer that stores responses from all microservices in Redis', 'correct' => false],
                    ['text' => 'A message broker that converts HTTP requests to message queue events', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the Circuit Breaker pattern and when does a circuit "open"?',
                'explanation' => 'The Circuit Breaker prevents cascading failures when a downstream service is unreliable. It wraps service calls and tracks failures. The circuit "opens" (stops calling the service) when failures exceed a threshold (e.g. 50% error rate). In open state, calls immediately return a fallback (cached data, default response) instead of waiting for a timeout. After a reset timeout, the circuit enters "half-open" — allows one test call. If it succeeds, the circuit closes again.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'Opens when failure rate exceeds threshold — returns fallback immediately; closes after a successful test call', 'correct' => true],
                    ['text' => 'Opens when a database connection times out — routes requests to a backup DB', 'correct' => false],
                    ['text' => 'A circuit breaker is the same as a rate limiter — both protect from excessive load', 'correct' => false],
                    ['text' => 'Opens when CPU usage exceeds 80% — pauses request processing until load drops', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is CQRS and what problem does it solve?',
                'explanation' => 'CQRS (Command Query Responsibility Segregation) separates read models from write models. The write side handles commands (CreateOrder, UpdateUser) with strict consistency and normalised data. The read side handles queries with denormalised, optimised data structures (materialized views, cached projections). This solves the problem of read and write operations having conflicting optimisation requirements — reads benefit from denormalisation; writes need normalisation for consistency.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'Separates read models (optimised for querying) from write models (optimised for consistency) — solves conflicting optimisation needs', 'correct' => true],
                    ['text' => 'Separates SQL queries from MongoDB queries in a polyglot persistence setup', 'correct' => false],
                    ['text' => 'A pattern for splitting a monolith — Commands go to one service, Queries to another', 'correct' => false],
                    ['text' => 'A REST API convention where GET requests use query params and POST requests use command bodies', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `AsyncLocalStorage` and how does it help with request context in Express?',
                'explanation' => '`AsyncLocalStorage` (from Node.js `async_hooks`) provides a storage context that persists across an async call chain without passing it as arguments. You run code in a context with `storage.run(ctx, fn)` — then any code called in that chain (services, repositories, loggers) can retrieve `storage.getStore()` without `ctx` being passed as a parameter. It\'s used to propagate request-scoped data like trace IDs, user IDs, and database transactions.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'Storage that persists across an async call chain — provides request context (traceId, userId) without prop-drilling', 'correct' => true],
                    ['text' => 'A localStorage-like API for Node.js that persists data between requests', 'correct' => false],
                    ['text' => 'An async queue that stores pending middleware calls for deferred execution', 'correct' => false],
                    ['text' => 'A thread-local storage mechanism for Node.js cluster workers to share data', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the Unit of Work pattern and why use it with repositories?',
                'explanation' => 'Unit of Work groups multiple repository operations into a single database transaction. Without it, you might save an order but fail to decrement inventory — leaving the DB in an inconsistent state. The Unit of Work runs all operations inside one transaction: `db.transaction(async (trx) => { ... })`. If any operation fails, the transaction rolls back. All repositories in the unit receive the same transaction object, ensuring atomicity.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'Groups multiple repository operations in one atomic DB transaction — all succeed or all roll back', 'correct' => true],
                    ['text' => 'A design pattern that batches multiple HTTP requests into one to reduce network round trips', 'correct' => false],
                    ['text' => 'A pattern for batching database writes and flushing them periodically for performance', 'correct' => false],
                    ['text' => 'A Unit of Work tracks which entities changed and emits domain events after commit', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How does OpenTelemetry auto-instrumentation work in an Express app?',
                'explanation' => 'OpenTelemetry\'s `@opentelemetry/auto-instrumentations-node` patches Node.js modules at load time (via `--require` or running tracing.js before the app). It hooks into Express routing, the http module, popular DB drivers (pg, mysql2, mongoose), and Redis clients — automatically creating spans for each operation without code changes. Spans are exported to a collector (Jaeger, Honeycomb, Grafana Tempo) via OTLP, enabling distributed tracing across services.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'Patches Express, http, and DB drivers at startup to auto-create spans — exports to Jaeger/Honeycomb via OTLP', 'correct' => true],
                    ['text' => 'Requires manually wrapping every function call with tracer.startSpan() and span.end()', 'correct' => false],
                    ['text' => 'OpenTelemetry instruments only HTTP calls — database tracing requires a separate package', 'correct' => false],
                    ['text' => 'Auto-instrumentation works only in development — production requires the manual API', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the key advantage of GraphQL over REST for a platform serving multiple client types?',
                'explanation' => 'REST returns fixed data shapes — a mobile app needing only `name` and `avatar` gets the full user object with 20 fields (over-fetching). A dashboard needing user + posts + comments requires 3 separate requests (under-fetching). GraphQL lets each client specify exactly the fields it needs in a single query. One endpoint, one request, right data. This eliminates over-fetching and under-fetching — especially valuable when web, mobile, and third-party clients have different data needs.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'Clients specify exactly the fields they need — eliminates over-fetching and under-fetching in one request', 'correct' => true],
                    ['text' => 'GraphQL is faster than REST because it skips HTTP and uses WebSockets directly', 'correct' => false],
                    ['text' => 'GraphQL automatically generates REST endpoints — no route definitions needed', 'correct' => false],
                    ['text' => 'GraphQL provides built-in authentication and CORS handling unlike REST', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is service discovery in microservices and how does it relate to health checks?',
                'explanation' => 'Service discovery is the mechanism by which services (or gateways) find the network addresses of other services dynamically — without hardcoding IPs. Tools like Consul, etcd, or Kubernetes DNS maintain a registry of available instances. Each service registers itself with its address and health check endpoint. The registry periodically calls the health check — if it fails, the service is removed from the pool. This allows zero-downtime deployments and automatic failover.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'Dynamic registry of service addresses — health checks tell the registry which instances are healthy and routable', 'correct' => true],
                    ['text' => 'A DNS-based system where each service gets a domain name instead of an IP address', 'correct' => false],
                    ['text' => 'The process of detecting which npm packages a service requires at runtime', 'correct' => false],
                    ['text' => 'Service discovery is only relevant for databases — HTTP services always use hardcoded URLs', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do feature flags improve the deployment process for Express APIs?',
                'explanation' => 'Feature flags decouple code deployment from feature release. You ship code with a flag (environment variable, DB row, feature flag service) and turn the feature on independently — for specific users, percentages, or all at once. Benefits: (1) deploy risky changes without exposing them, (2) A/B test features, (3) instant rollback by flipping the flag (no redeployment), (4) gradual rollout to catch issues early. Common tools: LaunchDarkly, Unleash, or simple env var checks.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'Decouple deployment from release — ship code, control exposure via flag, rollback instantly without redeployment', 'correct' => true],
                    ['text' => 'Feature flags add conditional logic that makes deployments slower — avoid in production', 'correct' => false],
                    ['text' => 'Feature flags are only for frontend — Express API changes should always be deployed immediately', 'correct' => false],
                    ['text' => 'A flag that marks a feature as complete in the project management tool — no code involvement', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does a Prometheus Histogram measure in Express, and what is P95 latency?',
                'explanation' => 'A Prometheus Histogram measures distributions of values over time with configurable buckets. For HTTP requests, it records how many requests fell within each latency bucket (e.g. under 10ms, under 50ms, under 100ms). From this, you calculate percentiles. P95 latency = the latency below which 95% of requests completed — 5% were slower. P95 > 1s means 1 in 20 users sees a slow response. Alerting on P95 (rather than averages) catches real-world user experience issues that averages mask.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'Histogram tracks request duration across buckets; P95 = the latency below which 95% of requests complete', 'correct' => true],
                    ['text' => 'Histogram counts total requests; P95 is the 95th percentile of memory usage', 'correct' => false],
                    ['text' => 'P95 means 95% of requests were successful — it is a success rate metric, not latency', 'correct' => false],
                    ['text' => 'Histogram measures CPU usage per route; P95 represents average CPU across 95% of cores', 'correct' => false],
                ],
            ],
        ];

        foreach ($questions as $q) {
            $exists = Question::where('topic_id', $topic->id)->where('question', $q['question'])->exists();
            if ($exists) continue;
            $question = Question::create([
                'topic_id'    => $topic->id,
                'type'        => 'MCQ',
                'difficulty'  => $q['difficulty'],
                'question'    => $q['question'],
                'explanation' => $q['explanation'],
            ]);
            foreach ($q['options'] as $opt) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $opt['text'],
                    'is_correct'  => $opt['correct'],
                ]);
            }
        }
    }
}
