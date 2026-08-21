<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NodeJsLearningSeeder extends Seeder
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
            ['slug' => 'nodejs'],
            [
                'learning_track_id' => $track->id,
                'title'             => 'Node.js',
                'description'       => 'Node.js is a JavaScript runtime built on Chrome\'s V8 engine. Master the event loop, async patterns, streams, modules, and server-side JavaScript development.',
                'display_order'     => 6,
            ]
        );

        // Assign levels to existing practice topics
        Topic::where('slug', 'nodejs-junior')->update(['level' => 1]);
        Topic::where('slug', 'nodejs-intermediate')->update(['level' => 2]);
        Topic::where('slug', 'nodejs-advanced')->update(['level' => 3]);

        // Create Level 4 topic
        $topic4 = Topic::firstOrCreate(
            ['slug' => 'nodejs-level-4-architecture'],
            [
                'subject_id'    => $subject->id,
                'title'         => 'Node.js Architecture & Design Patterns',
                'description'   => 'Clean architecture, dependency injection, advanced streams, and professional-grade testing in Node.js.',
                'display_order' => 4,
                'level'         => 4,
            ]
        );
        Topic::where('slug', 'nodejs-level-4-architecture')->update(['level' => 4]);

        // Create Level 5 topic
        $topic5 = Topic::firstOrCreate(
            ['slug' => 'nodejs-level-5-expert'],
            [
                'subject_id'    => $subject->id,
                'title'         => 'Expert Node.js & Production Systems',
                'description'   => 'V8 internals, performance profiling, observability, microservices, and zero-downtime production deployments.',
                'display_order' => 5,
                'level'         => 5,
            ]
        );
        Topic::where('slug', 'nodejs-level-5-expert')->update(['level' => 5]);

        $this->seedLessons($subject);
        $this->seedLevel4Questions($topic4);
        $this->seedLevel5Questions($topic5);

        $this->command->info('Node.js Learning seeder complete — all 5 levels populated.');
    }

    // ─── Lessons ──────────────────────────────────────────────────────────────

    private function seedLessons(Subject $subject): void
    {
        $topics = Topic::where('subject_id', $subject->id)
            ->orderBy('level')
            ->get()
            ->keyBy('level');

        $lessons = [
            // Level 1
            1 => [
                [
                    'title'             => 'The Event Loop, Non-Blocking I/O & How Node.js Works',
                    'content'           => $this->lessonL1_1(),
                    'estimated_minutes' => 20,
                    'display_order'     => 1,
                ],
                [
                    'title'             => 'Modules: CommonJS, ES Modules & Built-in Core Modules',
                    'content'           => $this->lessonL1_2(),
                    'estimated_minutes' => 18,
                    'display_order'     => 2,
                ],
                [
                    'title'             => 'File System, Buffers & Streams: Reading, Writing & Piping',
                    'content'           => $this->lessonL1_3(),
                    'estimated_minutes' => 22,
                    'display_order'     => 3,
                ],
            ],
            // Level 2
            2 => [
                [
                    'title'             => 'Async JavaScript: Callbacks, Promises & async/await in Node.js',
                    'content'           => $this->lessonL2_1(),
                    'estimated_minutes' => 25,
                    'display_order'     => 1,
                ],
                [
                    'title'             => 'HTTP Servers, Request Lifecycle & Middleware Patterns',
                    'content'           => $this->lessonL2_2(),
                    'estimated_minutes' => 22,
                    'display_order'     => 2,
                ],
                [
                    'title'             => 'EventEmitter, Child Processes & Worker Threads',
                    'content'           => $this->lessonL2_3(),
                    'estimated_minutes' => 20,
                    'display_order'     => 3,
                ],
            ],
            // Level 3
            3 => [
                [
                    'title'             => 'Clustering, Scaling & the Cluster Module',
                    'content'           => $this->lessonL3_1(),
                    'estimated_minutes' => 22,
                    'display_order'     => 1,
                ],
                [
                    'title'             => 'Error Handling, Debugging & Production-Grade Logging',
                    'content'           => $this->lessonL3_2(),
                    'estimated_minutes' => 20,
                    'display_order'     => 2,
                ],
                [
                    'title'             => 'Security, Authentication & Protecting Node.js Applications',
                    'content'           => $this->lessonL3_3(),
                    'estimated_minutes' => 25,
                    'display_order'     => 3,
                ],
            ],
            // Level 4
            4 => [
                [
                    'title'             => 'Clean Architecture, Dependency Injection & SOLID in Node.js',
                    'content'           => $this->lessonL4_1(),
                    'estimated_minutes' => 28,
                    'display_order'     => 1,
                ],
                [
                    'title'             => 'Advanced Streams: Pipelines, Transform Streams & Backpressure',
                    'content'           => $this->lessonL4_2(),
                    'estimated_minutes' => 25,
                    'display_order'     => 2,
                ],
                [
                    'title'             => 'Testing Node.js: Unit Tests, Integration Tests & Mocking with Jest',
                    'content'           => $this->lessonL4_3(),
                    'estimated_minutes' => 28,
                    'display_order'     => 3,
                ],
            ],
            // Level 5
            5 => [
                [
                    'title'             => 'Performance Profiling: Flame Graphs, Heap Snapshots & V8 Optimization',
                    'content'           => $this->lessonL5_1(),
                    'estimated_minutes' => 30,
                    'display_order'     => 1,
                ],
                [
                    'title'             => 'Microservices, Message Queues & Node.js in Distributed Systems',
                    'content'           => $this->lessonL5_2(),
                    'estimated_minutes' => 30,
                    'display_order'     => 2,
                ],
                [
                    'title'             => 'Production Node.js: Zero-Downtime Deployment, Monitoring & OpenTelemetry',
                    'content'           => $this->lessonL5_3(),
                    'estimated_minutes' => 30,
                    'display_order'     => 3,
                ],
            ],
        ];

        foreach ($lessons as $level => $levelLessons) {
            if (!isset($topics[$level])) {
                continue;
            }
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

        $this->command->info('Lessons seeded for all 5 Node.js levels.');
    }

    // ─── Level 1 Lessons ──────────────────────────────────────────────────────

    private function lessonL1_1(): string
    {
        return <<<'MD'
## The Event Loop, Non-Blocking I/O & How Node.js Works

Node.js is a single-threaded JavaScript runtime built on Chrome's **V8** engine. Its superpower: handling thousands of concurrent connections without creating a new thread per connection.

### Why Single-Threaded Works

Most web server work is **I/O-bound** — waiting on databases, files, or APIs. Traditional servers block the thread while waiting. Node.js offloads I/O to the OS and **keeps executing other code** while waiting.

### The Event Loop

The event loop is the mechanism that makes this possible. It runs continuously, picking up callbacks from queues when their async operation completes.

**Event loop phases (simplified):**
```
timers → pending callbacks → idle/prepare → poll → check → close callbacks
```

- **timers**: runs `setTimeout` and `setInterval` callbacks
- **poll**: retrieves new I/O events; executes I/O callbacks
- **check**: runs `setImmediate` callbacks

### Priority Order

```js
// Order of execution:
process.nextTick(() => console.log('1 — nextTick'));     // before next phase
Promise.resolve().then(() => console.log('2 — microtask')); // microtask queue
setTimeout(() => console.log('3 — setTimeout'));         // timers phase
setImmediate(() => console.log('4 — setImmediate'));     // check phase
```

`process.nextTick` > microtasks (Promises) > timers > setImmediate

### Blocking vs Non-Blocking

```js
// BLOCKING — freezes everything:
const data = fs.readFileSync('big-file.txt'); // event loop is stuck

// NON-BLOCKING — event loop continues:
fs.readFile('big-file.txt', (err, data) => {
  // called when file is ready
});
// code here runs immediately — no waiting
```

### libuv Thread Pool

File I/O and DNS lookups don't have async OS APIs everywhere. libuv delegates them to an internal thread pool (default: **4 threads**). The callback fires back into the event loop when done.

Set the pool size: `UV_THREADPOOL_SIZE=8 node app.js`

### Key Takeaways

- Node.js is single-threaded but non-blocking
- The event loop continuously processes callback queues
- `nextTick` fires before I/O; `setImmediate` fires after I/O
- CPU-heavy work blocks the loop — use Worker Threads for that
MD;
    }

    private function lessonL1_2(): string
    {
        return <<<'MD'
## Modules: CommonJS, ES Modules & Built-in Core Modules

Every Node.js file is a **module**. Node.js supports two module systems and includes a rich set of built-in modules.

### CommonJS (CJS) — The Classic

```js
// math.js — export
function add(a, b) { return a + b; }
module.exports = { add };

// app.js — import
const { add } = require('./math');
console.log(add(2, 3)); // 5
```

- `require()` is synchronous — the file is loaded immediately
- The module is **cached** after first load — subsequent `require()` calls return the same object
- `module.exports` is what callers receive; `exports` is a shorthand reference

### ES Modules (ESM) — The Modern Standard

```js
// math.mjs
export function add(a, b) { return a + b; }

// app.mjs
import { add } from './math.mjs';
```

Enable ESM in `.js` files: set `"type": "module"` in `package.json`.

**CJS vs ESM key differences:**

| | CommonJS | ES Modules |
|---|---|---|
| Syntax | `require` / `module.exports` | `import` / `export` |
| Loading | Synchronous | Asynchronous |
| Static analysis | No | Yes (tree-shaking) |
| Top-level `await` | No | Yes |

### Built-in Core Modules

No installation needed:

```js
const fs   = require('fs');      // file system
const path = require('path');    // path utilities
const http = require('http');    // HTTP server/client
const os   = require('os');      // OS info
const url  = require('url');     // URL parsing
const crypto = require('crypto'); // hashing, encryption
const events = require('events'); // EventEmitter
```

### Useful Module Patterns

```js
// Export a class
module.exports = class UserService { ... };

// Export a function directly
module.exports = function createServer() { ... };

// Named exports
module.exports = { createUser, deleteUser };
```

### `__dirname` and `__filename`

```js
console.log(__dirname);  // /home/user/project/src
console.log(__filename); // /home/user/project/src/app.js

// Build paths relative to current file
const filePath = path.join(__dirname, 'data', 'users.json');
```

In ESM: `import.meta.url` replaces these — use `fileURLToPath(import.meta.url)`.
MD;
    }

    private function lessonL1_3(): string
    {
        return <<<'MD'
## File System, Buffers & Streams

### The `fs` Module

```js
const fs = require('fs');
const { promises: fsp } = require('fs'); // Promise API

// Async (callback)
fs.readFile('data.txt', 'utf8', (err, content) => {
    if (err) throw err;
    console.log(content);
});

// Async/await (Promise)
const content = await fsp.readFile('data.txt', 'utf8');

// Sync (blocks event loop — avoid in servers)
const content = fs.readFileSync('data.txt', 'utf8');
```

Common fs operations:
- `fs.writeFile` / `fs.appendFile` — write files
- `fs.mkdir` / `fs.rmdir` — manage directories
- `fs.readdir` — list directory contents
- `fs.stat` — file metadata (size, modified date)
- `fs.unlink` — delete a file
- `fs.watch` — watch for file changes

### Buffers

A `Buffer` holds raw binary data — useful for binary files, streams, and network data.

```js
const buf = Buffer.from('Hello', 'utf8');
console.log(buf);          // <Buffer 48 65 6c 6c 6f>
console.log(buf.toString()); // Hello

const zeros = Buffer.alloc(10);   // 10 zero bytes
const unsafe = Buffer.allocUnsafe(10); // fast but uninitialized
```

### Streams — Process Data in Chunks

Streams let you process large files **without loading them into memory**.

```js
// Read a large file and pipe to HTTP response
const readable = fs.createReadStream('large-video.mp4');
readable.pipe(res); // no buffering needed
```

**Four stream types:**

| Type | Description | Example |
|---|---|---|
| Readable | Source of data | `fs.createReadStream` |
| Writable | Destination | `fs.createWriteStream` |
| Duplex | Both read and write | TCP socket |
| Transform | Read, modify, write | `zlib.createGzip()` |

### Piping Streams

```js
const { createReadStream, createWriteStream } = require('fs');
const { createGzip } = require('zlib');
const { pipeline } = require('stream/promises');

// Compress a file — memory-efficient
await pipeline(
    createReadStream('input.txt'),
    createGzip(),
    createWriteStream('input.txt.gz')
);
```

Use `stream/promises` pipeline — it handles errors and cleanup automatically. Raw `.pipe()` does not propagate errors.

### Backpressure

When the writable can't keep up with the readable, `write()` returns `false`. You should pause the readable and wait for the `drain` event before resuming. `.pipe()` and `pipeline()` handle this automatically.
MD;
    }

    // ─── Level 2 Lessons ──────────────────────────────────────────────────────

    private function lessonL2_1(): string
    {
        return <<<'MD'
## Async JavaScript: Callbacks, Promises & async/await

### Callbacks — The Original Async

Node.js uses the **error-first callback** convention: `(err, result) => {}`.

```js
fs.readFile('file.txt', 'utf8', (err, data) => {
    if (err) return console.error(err);
    console.log(data);
});
```

**Callback hell** occurs when multiple async operations nest deeply — hard to read and error-prone.

### Promises — Composable Async

```js
const fsp = require('fs/promises');

fsp.readFile('file.txt', 'utf8')
    .then(data => console.log(data))
    .catch(err => console.error(err));

// Chaining
fetchUser(id)
    .then(user => fetchPosts(user.id))
    .then(posts => renderPage(posts))
    .catch(handleError);
```

**Promise combinators:**
```js
// All succeed — returns array of results
const [users, posts] = await Promise.all([getUsers(), getPosts()]);

// First to settle (resolve or reject)
const result = await Promise.race([fetch1(), fetch2()]);

// All settle — never rejects
const results = await Promise.allSettled([a(), b(), c()]);

// First to resolve (ignores rejections unless all reject)
const first = await Promise.any([slow(), fast()]);
```

### async/await — Synchronous-Looking Async

```js
async function loadDashboard(userId) {
    try {
        const user = await getUser(userId);
        const [posts, friends] = await Promise.all([
            getPosts(user.id),
            getFriends(user.id),
        ]);
        return { user, posts, friends };
    } catch (err) {
        throw new AppError('Dashboard load failed', err);
    }
}
```

### Common Async Mistakes

```js
// WRONG — forEach doesn't await
items.forEach(async (item) => {
    await processItem(item); // not awaited by forEach
});

// CORRECT — sequential
for (const item of items) {
    await processItem(item);
}

// CORRECT — parallel
await Promise.all(items.map(item => processItem(item)));
```

### Unhandled Promise Rejections

In Node.js 15+, unhandled rejections crash the process. Always handle them:

```js
// Global handler — log and exit gracefully
process.on('unhandledRejection', (reason) => {
    logger.error('Unhandled rejection:', reason);
    process.exit(1);
});
```

### util.promisify

Convert callback-based functions to return Promises:

```js
const { promisify } = require('util');
const readFile = promisify(fs.readFile);

const data = await readFile('file.txt', 'utf8');
```
MD;
    }

    private function lessonL2_2(): string
    {
        return <<<'MD'
## HTTP Servers, Request Lifecycle & Middleware Patterns

### Raw HTTP Server

```js
const http = require('http');

const server = http.createServer((req, res) => {
    // req = IncomingMessage (readable stream)
    // res = ServerResponse (writable stream)

    console.log(req.method, req.url);

    res.writeHead(200, { 'Content-Type': 'application/json' });
    res.end(JSON.stringify({ message: 'Hello' }));
});

server.listen(3000, () => console.log('Server on :3000'));
```

### Request Lifecycle

```
Client → DNS → TCP → TLS → HTTP Request → Node.js
         → parse headers/method/url
         → collect body (stream)
         → route to handler
         → build response
         → send response → Client
```

Reading the request body:
```js
function readBody(req) {
    return new Promise((resolve, reject) => {
        const chunks = [];
        req.on('data', chunk => chunks.push(chunk));
        req.on('end', () => resolve(Buffer.concat(chunks).toString()));
        req.on('error', reject);
    });
}
```

### Middleware Pattern

Middleware is a function with signature `(req, res, next)` — it processes the request and either responds or passes control forward.

```js
function logger(req, res, next) {
    console.log(`${req.method} ${req.url}`);
    next(); // pass to next middleware
}

function authGuard(req, res, next) {
    if (!req.headers.authorization) {
        res.writeHead(401);
        res.end('Unauthorized');
        return; // do NOT call next()
    }
    next();
}
```

### Express.js — Middleware Framework

Express wraps Node.js `http` and makes routing and middleware ergonomic:

```js
const express = require('express');
const app = express();

// Global middleware
app.use(express.json());  // body parser
app.use(logger);
app.use('/api', authGuard);

// Route handler
app.get('/users/:id', async (req, res) => {
    const user = await getUser(req.params.id);
    res.json(user);
});

// Error-handling middleware (4 params)
app.use((err, req, res, next) => {
    console.error(err);
    res.status(500).json({ error: err.message });
});
```

### CORS

```js
app.use((req, res, next) => {
    res.setHeader('Access-Control-Allow-Origin', 'https://yourapp.com');
    res.setHeader('Access-Control-Allow-Methods', 'GET,POST,PUT,DELETE');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type,Authorization');
    if (req.method === 'OPTIONS') return res.sendStatus(204);
    next();
});
```

### Response Best Practices

- Always set `Content-Type`
- Use proper HTTP status codes (200, 201, 400, 401, 404, 500)
- Never leave a request hanging — always call `res.end()` or `next(err)`
MD;
    }

    private function lessonL2_3(): string
    {
        return <<<'MD'
## EventEmitter, Child Processes & Worker Threads

### EventEmitter — Pub/Sub in Node.js

```js
const EventEmitter = require('events');

class OrderService extends EventEmitter {
    placeOrder(order) {
        // process order...
        this.emit('order:placed', order);
        this.emit('order:notify', order.userId);
    }
}

const svc = new OrderService();
svc.on('order:placed', (order) => console.log('Order:', order.id));
svc.once('order:notify', (userId) => sendEmail(userId)); // fires once only
svc.on('error', (err) => console.error(err)); // always handle errors
```

**Key methods:** `.on()`, `.once()`, `.off()`, `.emit()`, `.removeAllListeners()`

`process`, streams, and `http.Server` are all EventEmitters.

### Child Processes

Run external programs or shell commands:

```js
const { exec, spawn, fork } = require('child_process');

// exec — buffers stdout (for small output)
exec('ls -la', (err, stdout, stderr) => {
    console.log(stdout);
});

// spawn — streams output (for large output or long-running)
const ls = spawn('ls', ['-la']);
ls.stdout.pipe(process.stdout);

// fork — Node.js script with IPC channel
const child = fork('./worker.js');
child.send({ task: 'compress', file: 'video.mp4' });
child.on('message', (result) => console.log(result));
```

### Worker Threads — True Parallelism

```js
const { Worker, isMainThread, parentPort, workerData } = require('worker_threads');

if (isMainThread) {
    const worker = new Worker(__filename, {
        workerData: { numbers: [1, 2, 3, 4, 5] },
    });
    worker.on('message', (sum) => console.log('Sum:', sum));
    worker.on('error', console.error);
} else {
    const sum = workerData.numbers.reduce((a, b) => a + b, 0);
    parentPort.postMessage(sum);
}
```

**Child Process vs Worker Threads:**

| | Child Process | Worker Threads |
|---|---|---|
| Separate process | Yes | No (same process) |
| Shared memory | No | Yes (SharedArrayBuffer) |
| Use case | Shell commands, isolation | CPU-intensive computation |
| Memory overhead | Higher | Lower |

### SharedArrayBuffer & Atomics

Worker threads can share memory:
```js
const shared = new SharedArrayBuffer(4);
const arr = new Int32Array(shared);
Atomics.add(arr, 0, 1); // thread-safe increment
```

### When to Use Which

- **EventEmitter**: in-process pub/sub, loose coupling between modules
- **Child Process / fork**: run another script, need process isolation
- **Worker Threads**: CPU-heavy JS (image processing, parsing, compression)
MD;
    }

    // ─── Level 3 Lessons ──────────────────────────────────────────────────────

    private function lessonL3_1(): string
    {
        return <<<'MD'
## Clustering, Scaling & the Cluster Module

Node.js is single-threaded — a single process runs on one CPU core. On an 8-core machine, 7 cores are idle. The cluster module fixes this.

### The Cluster Module

```js
const cluster = require('cluster');
const http = require('http');
const os = require('os');

if (cluster.isPrimary) {
    const numCPUs = os.cpus().length;
    console.log(`Primary ${process.pid} is running`);

    for (let i = 0; i < numCPUs; i++) {
        cluster.fork();
    }

    cluster.on('exit', (worker, code, signal) => {
        console.log(`Worker ${worker.process.pid} died — restarting`);
        cluster.fork(); // restart dead worker
    });
} else {
    // Each worker runs this
    http.createServer((req, res) => {
        res.end(`Worker ${process.pid} handled this`);
    }).listen(3000);

    console.log(`Worker ${process.pid} started`);
}
```

The OS distributes incoming connections across workers (round-robin on Linux).

### PM2 — Production Process Manager

PM2 handles clustering, restarts, logs, and monitoring:

```bash
pm2 start app.js -i max          # fork one per CPU core
pm2 start app.js -i 4            # fork 4 workers
pm2 reload app.js                # zero-downtime restart
pm2 logs                         # tail logs
pm2 monit                        # live monitoring dashboard
pm2 save && pm2 startup          # auto-start on reboot
```

### Horizontal Scaling — Multiple Servers

Beyond clustering (vertical), scale horizontally with a **load balancer**:

```
Client → Load Balancer (nginx / AWS ALB)
           ├── Server 1 (Node.js cluster)
           ├── Server 2 (Node.js cluster)
           └── Server 3 (Node.js cluster)
```

### Stateless Design

For clustering and horizontal scaling to work, each request must be **stateless** — no in-memory session data.

```js
// WRONG — session stored in memory (lost on worker restart)
const sessions = {};
app.post('/login', (req, res) => {
    sessions[userId] = { user };
});

// CORRECT — store sessions in Redis
const session = require('express-session');
const RedisStore = require('connect-redis')(session);
app.use(session({ store: new RedisStore({ client: redisClient }) }));
```

### Sticky Sessions

If you must maintain state, configure the load balancer for **sticky sessions** (route same client to same server). This limits scalability — prefer stateless design with Redis.

### Event Loop Lag Monitoring

```js
const { monitorEventLoopDelay } = require('perf_hooks');
const h = monitorEventLoopDelay({ resolution: 20 });
h.enable();
setInterval(() => {
    console.log(`Event loop lag p99: ${h.percentile(99)}ms`);
}, 5000);
```

High event loop lag means something is blocking — investigate CPU-bound code.
MD;
    }

    private function lessonL3_2(): string
    {
        return <<<'MD'
## Error Handling, Debugging & Production-Grade Logging

### Error-First Callbacks

```js
fs.readFile('config.json', (err, data) => {
    if (err) {
        if (err.code === 'ENOENT') return handleMissing();
        throw err; // unexpected error — let it crash
    }
    process(data);
});
```

### Custom Error Classes

```js
class AppError extends Error {
    constructor(message, statusCode = 500, code = 'INTERNAL_ERROR') {
        super(message);
        this.name = 'AppError';
        this.statusCode = statusCode;
        this.code = code;
        Error.captureStackTrace(this, this.constructor);
    }
}

class NotFoundError extends AppError {
    constructor(resource) {
        super(`${resource} not found`, 404, 'NOT_FOUND');
    }
}
```

### Express Error Middleware

```js
// Async wrapper to catch thrown errors
const asyncHandler = (fn) => (req, res, next) => {
    Promise.resolve(fn(req, res, next)).catch(next);
};

app.get('/users/:id', asyncHandler(async (req, res) => {
    const user = await userService.findById(req.params.id);
    if (!user) throw new NotFoundError('User');
    res.json(user);
}));

// Centralized error handler
app.use((err, req, res, next) => {
    const status = err.statusCode ?? 500;
    res.status(status).json({
        error: { code: err.code, message: err.message },
    });
});
```

### Global Error Handlers

```js
process.on('uncaughtException', (err) => {
    logger.error('Uncaught exception:', err);
    process.exit(1); // cannot recover — exit and let PM2 restart
});

process.on('unhandledRejection', (reason) => {
    logger.error('Unhandled rejection:', reason);
    process.exit(1);
});
```

### Debugging with `--inspect`

```bash
node --inspect app.js         # listen on :9229
node --inspect-brk app.js     # pause at first line (good for startup bugs)
```

Open `chrome://inspect` in Chrome DevTools and attach.

VS Code: add `.vscode/launch.json`:
```json
{
  "type": "node",
  "request": "attach",
  "name": "Attach to Node",
  "port": 9229
}
```

### Production Logging — Structured JSON

Use a structured logger (Pino, Winston) — never use `console.log` in production:

```js
const pino = require('pino');
const logger = pino({ level: process.env.LOG_LEVEL || 'info' });

logger.info({ userId, action: 'login' }, 'User logged in');
logger.error({ err }, 'Database query failed');
```

Log levels: `trace` < `debug` < `info` < `warn` < `error` < `fatal`

Structured logs are machine-parseable — easy to filter in Datadog, Grafana, CloudWatch.
MD;
    }

    private function lessonL3_3(): string
    {
        return <<<'MD'
## Security, Authentication & Protecting Node.js Applications

### OWASP Top 10 in Node.js

**Injection (SQL, NoSQL, Command):**
```js
// WRONG — command injection
exec(`convert ${userInput} output.png`); // attacker passes "; rm -rf /"

// CORRECT — use arrays, never string concat for commands
spawn('convert', [userInput, 'output.png']);

// CORRECT — parameterized SQL
db.query('SELECT * FROM users WHERE id = ?', [userId]);
```

**XSS — Always escape output:**
```js
const escapeHtml = require('escape-html');
res.send(`<p>${escapeHtml(userContent)}</p>`);
```

### Helmet — Secure HTTP Headers

```js
const helmet = require('helmet');
app.use(helmet());
// Sets: Content-Security-Policy, X-Frame-Options,
//       X-Content-Type-Options, HSTS, etc.
```

### Rate Limiting

```js
const rateLimit = require('express-rate-limit');
app.use('/api/auth', rateLimit({
    windowMs: 15 * 60 * 1000, // 15 minutes
    max: 10,                   // max 10 requests per window
    message: 'Too many requests',
}));
```

### JWT Authentication

```js
const jwt = require('jsonwebtoken');
const SECRET = process.env.JWT_SECRET;

// Sign token on login
const token = jwt.sign({ userId: user.id, role: user.role }, SECRET, {
    expiresIn: '1h',
    algorithm: 'HS256',
});

// Verify on each request
function authMiddleware(req, res, next) {
    const token = req.headers.authorization?.split(' ')[1];
    if (!token) return res.status(401).json({ error: 'No token' });
    try {
        req.user = jwt.verify(token, SECRET);
        next();
    } catch {
        res.status(401).json({ error: 'Invalid token' });
    }
}
```

### Password Hashing

```js
const bcrypt = require('bcrypt');
const ROUNDS = 12;

// Hash on register
const hash = await bcrypt.hash(plainPassword, ROUNDS);

// Verify on login
const valid = await bcrypt.compare(plainPassword, storedHash);
```

### Input Validation

```js
const { body, validationResult } = require('express-validator');

app.post('/register',
    body('email').isEmail().normalizeEmail(),
    body('password').isLength({ min: 8 }),
    (req, res) => {
        const errors = validationResult(req);
        if (!errors.isEmpty()) return res.status(400).json({ errors: errors.array() });
        // proceed
    }
);
```

### HTTPS, Secrets & Environment

- Always serve over HTTPS (or terminate TLS at nginx/ALB)
- Never hardcode secrets — use `process.env` and a secrets manager
- Set `NODE_ENV=production` to disable stack traces in error responses
- Use `express-session` with `httpOnly`, `secure`, `sameSite` cookie flags
MD;
    }

    // ─── Level 4 Lessons ──────────────────────────────────────────────────────

    private function lessonL4_1(): string
    {
        return <<<'MD'
## Clean Architecture, Dependency Injection & SOLID in Node.js

### Why Architecture Matters

As Node.js apps grow, controllers that call databases directly become untestable and unmaintainable. Clean architecture enforces separation of concerns.

### Layered Architecture

```
HTTP Layer       (routes, controllers)
    ↓
Service Layer    (business logic)
    ↓
Repository Layer (data access)
    ↓
Database
```

```js
// userRepository.js — data access only
class UserRepository {
    constructor(db) { this.db = db; }
    async findById(id) { return this.db.query('SELECT * FROM users WHERE id = ?', [id]); }
    async save(user) { return this.db.query('INSERT INTO users SET ?', [user]); }
}

// userService.js — business logic only
class UserService {
    constructor(userRepo, emailService) {
        this.userRepo = userRepo;
        this.emailService = emailService;
    }
    async register(data) {
        const existing = await this.userRepo.findByEmail(data.email);
        if (existing) throw new ConflictError('Email already registered');
        const user = await this.userRepo.save(data);
        await this.emailService.sendWelcome(user);
        return user;
    }
}

// userController.js — HTTP only
async function registerUser(req, res, next) {
    try {
        const user = await userService.register(req.body);
        res.status(201).json(user);
    } catch (err) { next(err); }
}
```

### Dependency Injection

Instead of hardcoding dependencies, inject them:

```js
// WRONG — hardcoded, untestable
class UserService {
    async getUser(id) {
        const db = require('./db'); // tightly coupled
        return db.query(...);
    }
}

// CORRECT — injected
class UserService {
    constructor(userRepository) {
        this.repo = userRepository;
    }
    async getUser(id) { return this.repo.findById(id); }
}

// Wire it up in the composition root
const db = new Database(config.db);
const userRepo = new UserRepository(db);
const userService = new UserService(userRepo);
const userController = new UserController(userService);
```

### SOLID in Node.js

**Single Responsibility** — one reason to change per class
**Open/Closed** — open for extension, closed for modification (use strategy pattern)
**Liskov Substitution** — subclasses must be substitutable for their base
**Interface Segregation** — don't force classes to implement unused methods
**Dependency Inversion** — depend on abstractions, not concretions

```js
// Dependency Inversion — program to an interface
class EmailNotifier {
    async notify(user) { await sendEmail(user.email); }
}

class SmsNotifier {
    async notify(user) { await sendSms(user.phone); }
}

class UserService {
    constructor(notifier) { this.notifier = notifier; } // accepts any notifier
    async registerUser(data) {
        const user = await this.repo.save(data);
        await this.notifier.notify(user); // doesn't care which one
    }
}
```

### Container Pattern (Simple DI Container)

For larger apps, use a DI container (Awilix, InversifyJS, or DIY):

```js
const container = {
    db: new Database(config.db),
    get userRepo() { return new UserRepository(this.db); },
    get userService() { return new UserService(this.userRepo); },
};
```

### AsyncLocalStorage — Request Context

Propagate request-scoped data (user ID, trace ID) without threading it through every function:

```js
const { AsyncLocalStorage } = require('async_hooks');
const requestContext = new AsyncLocalStorage();

app.use((req, res, next) => {
    requestContext.run({ requestId: uuid(), user: req.user }, next);
});

// Anywhere in the call chain:
const ctx = requestContext.getStore();
logger.info({ requestId: ctx.requestId }, 'Processing');
```
MD;
    }

    private function lessonL4_2(): string
    {
        return <<<'MD'
## Advanced Streams: Pipelines, Transform Streams & Backpressure

### Why Streams for Production

Streams are critical for memory efficiency. Without streams, loading a 2GB file uses 2GB of RAM. With streams, only a small chunk is in memory at any time.

### stream.pipeline() — The Correct Way

Never use raw `.pipe()` in production — it doesn't propagate errors:

```js
const { pipeline } = require('stream/promises');
const { createReadStream, createWriteStream } = require('fs');
const { createGzip } = require('zlib');

async function compressFile(input, output) {
    await pipeline(
        createReadStream(input),
        createGzip(),
        createWriteStream(output)
    );
    console.log('Compression complete');
}
```

`pipeline` automatically destroys all streams on error and resolves when the pipeline is done.

### Transform Streams — Read, Modify, Write

```js
const { Transform } = require('stream');

class JSONLineParser extends Transform {
    constructor() {
        super({ objectMode: true });
        this._buffer = '';
    }

    _transform(chunk, encoding, callback) {
        this._buffer += chunk.toString();
        const lines = this._buffer.split('\n');
        this._buffer = lines.pop(); // keep incomplete last line

        for (const line of lines) {
            if (line.trim()) {
                try {
                    this.push(JSON.parse(line)); // push parsed object
                } catch (e) {
                    return callback(e); // propagate parse errors
                }
            }
        }
        callback(); // signal chunk processed
    }

    _flush(callback) {
        if (this._buffer.trim()) {
            try { this.push(JSON.parse(this._buffer)); }
            catch (e) { return callback(e); }
        }
        callback();
    }
}

// Usage
await pipeline(
    fs.createReadStream('data.ndjson'),
    new JSONLineParser(),
    new Writable({
        objectMode: true,
        write(obj, enc, cb) { processRecord(obj).then(() => cb()).catch(cb); },
    })
);
```

### Readable.from() — Create Streams from Iterables

```js
const { Readable } = require('stream');

// From async generator
async function* fetchPagesGenerator(baseUrl, totalPages) {
    for (let page = 1; page <= totalPages; page++) {
        const data = await fetch(`${baseUrl}?page=${page}`).then(r => r.json());
        yield data.items;
    }
}

const stream = Readable.from(fetchPagesGenerator(url, 10));
```

### Backpressure — Manual Control

```js
const readable = fs.createReadStream('huge.csv');
const writable = slowDatabase.createWriteStream();

readable.on('data', (chunk) => {
    const canContinue = writable.write(chunk);
    if (!canContinue) {
        readable.pause(); // stop reading until writable drains
        writable.once('drain', () => readable.resume());
    }
});

readable.on('end', () => writable.end());
```

### Object Mode Streams

By default, streams pass Buffers. With `{ objectMode: true }`, they pass any JavaScript value — great for database rows, parsed JSON, events.

### Async Iteration over Streams

Modern Node.js lets you iterate streams with `for await...of`:

```js
const rl = readline.createInterface({ input: fs.createReadStream('data.csv') });

for await (const line of rl) {
    await processLine(line);
}
```
MD;
    }

    private function lessonL4_3(): string
    {
        return <<<'MD'
## Testing Node.js: Unit Tests, Integration Tests & Mocking with Jest

### Unit vs Integration Testing

| | Unit Test | Integration Test |
|---|---|---|
| Scope | One function/class | Multiple layers together |
| Database | Mocked | Real (test DB) |
| Speed | Fast (<1ms) | Slower (I/O involved) |
| Goal | Business logic correctness | System wiring |

### Jest — Setup

```bash
npm install --save-dev jest
```

```json
// package.json
{
  "scripts": { "test": "jest --runInBand" },
  "jest": { "testEnvironment": "node" }
}
```

### Unit Testing with Mocks

```js
// userService.test.js
const UserService = require('./userService');

const mockUserRepo = {
    findByEmail: jest.fn(),
    save: jest.fn(),
};
const mockEmailService = { sendWelcome: jest.fn() };

const userService = new UserService(mockUserRepo, mockEmailService);

describe('UserService.register', () => {
    beforeEach(() => jest.clearAllMocks());

    it('creates a new user and sends welcome email', async () => {
        mockUserRepo.findByEmail.mockResolvedValue(null);
        mockUserRepo.save.mockResolvedValue({ id: 1, email: 'a@b.com' });

        const user = await userService.register({ email: 'a@b.com', password: 'secret' });

        expect(user.id).toBe(1);
        expect(mockEmailService.sendWelcome).toHaveBeenCalledWith({ id: 1, email: 'a@b.com' });
    });

    it('throws ConflictError if email already registered', async () => {
        mockUserRepo.findByEmail.mockResolvedValue({ id: 2 });
        await expect(userService.register({ email: 'taken@b.com' }))
            .rejects.toThrow('Email already registered');
    });
});
```

### jest.fn() Assertions

```js
const fn = jest.fn().mockReturnValue(42);

fn('hello');

expect(fn).toHaveBeenCalledTimes(1);
expect(fn).toHaveBeenCalledWith('hello');
expect(fn).toHaveReturnedWith(42);

// Async
const asyncFn = jest.fn().mockResolvedValue({ data: 'ok' });
const result = await asyncFn();
expect(result).toEqual({ data: 'ok' });
```

### Spies — Wrap Without Replacing

```js
const emailService = require('./emailService');
const spy = jest.spyOn(emailService, 'send');

await sendInvoice(order);

expect(spy).toHaveBeenCalledWith(order.email, expect.objectContaining({ orderId: order.id }));
spy.mockRestore(); // restore original
```

### Integration Testing with Supertest

```js
const request = require('supertest');
const app = require('./app');
const db = require('./db');

beforeAll(() => db.migrate());
afterAll(() => db.destroy());
afterEach(() => db.truncate(['users']));

it('POST /api/register creates a user', async () => {
    const res = await request(app)
        .post('/api/register')
        .send({ email: 'test@test.com', password: 'password123' });

    expect(res.status).toBe(201);
    expect(res.body.email).toBe('test@test.com');

    const user = await db('users').where({ email: 'test@test.com' }).first();
    expect(user).toBeDefined();
});
```

### Test Structure Best Practices

- **Arrange** → set up data and mocks
- **Act** → call the code under test
- **Assert** → verify outcome

One `expect` concept per test. Name tests as `it('does X when Y')`.
MD;
    }

    // ─── Level 5 Lessons ──────────────────────────────────────────────────────

    private function lessonL5_1(): string
    {
        return <<<'MD'
## Performance Profiling: Flame Graphs, Heap Snapshots & V8 Optimization

### Event Loop Lag — The Key Health Metric

High event loop lag means something is blocking. Measure it:

```js
const { monitorEventLoopDelay } = require('perf_hooks');
const h = monitorEventLoopDelay({ resolution: 10 });
h.enable();
setInterval(() => {
    console.log('p50:', h.percentile(50), 'p99:', h.percentile(99));
    h.reset();
}, 5000);
```

Target: p99 < 100ms. Over 500ms — something is blocking.

### CPU Profiling with `--prof`

```bash
node --prof app.js
# generates isolate-0x*.log

node --prof-process isolate-*.log > profile.txt
cat profile.txt | head -50
```

Shows which functions consumed the most CPU time. Look for high `%` in the top section.

### Clinic.js — Visual Profiling

```bash
npm install -g clinic
clinic flame -- node app.js   # flame graph (CPU hotspots)
clinic bubbleprof -- node app.js  # async waterfall
clinic doctor -- node app.js  # automatic issue detection
```

Flame graph: wide = more CPU time. Look for wide flat tops — optimization targets.

### Heap Snapshots — Memory Leak Detection

```js
const v8 = require('v8');
const fs = require('fs');

// Take a snapshot
const snapshot = v8.writeHeapSnapshot();
// Outputs: Heap.20230101.123456.heapsnapshot
```

Open in Chrome DevTools → Memory → Load snapshot.

Compare two snapshots over time:
1. Snapshot at startup
2. Generate traffic for 5 minutes
3. Snapshot again
4. Compare — objects that grew = memory leak candidates

### Common Memory Leaks in Node.js

```js
// 1. Growing cache without eviction
const cache = {};
app.get('/user/:id', async (req, res) => {
    cache[req.params.id] = await getUser(req.params.id); // grows forever
    // FIX: use a Map with max size or an LRU cache library
});

// 2. Unreleased event listeners
emitter.on('data', handler); // never removed
// FIX: emitter.off('data', handler) when done, or use .once()

// 3. Closures holding references
function createProcessor() {
    const bigData = loadHugeDataset(); // 100MB in closure
    return function process(item) { return bigData.lookup(item); };
}
// FIX: only pass what the closure needs, not the whole dataset
```

### V8 Optimization — What to Know

V8 compiles JS to machine code (JIT). It optimizes hot functions. Common deoptimizations:
- Changing object shapes after creation (add properties dynamically)
- Using `arguments` object in hot functions (use rest params `...args`)
- Mixing types in arrays (`[1, 'a', true]` — untyped array is slower)
- `try/catch` inside hot loops (move it outside)

```js
// SLOW — shape change
const obj = {};
obj.x = 1;
obj.y = 2; // V8 creates a new hidden class

// FAST — declare shape up front
const obj = { x: 1, y: 2 };
```

### `perf_hooks` — Custom Measurements

```js
const { performance, PerformanceObserver } = require('perf_hooks');

performance.mark('db-start');
await dbQuery();
performance.mark('db-end');
performance.measure('db-query', 'db-start', 'db-end');

const obs = new PerformanceObserver((list) => {
    list.getEntries().forEach(e => logger.info({ name: e.name, duration: e.duration }));
});
obs.observe({ entryTypes: ['measure'] });
```
MD;
    }

    private function lessonL5_2(): string
    {
        return <<<'MD'
## Microservices, Message Queues & Node.js in Distributed Systems

### Microservices in Node.js

Node.js is a natural fit for microservices:
- Fast startup time
- Lightweight HTTP servers
- Event-driven architecture matches async messaging
- Easy to containerize with Docker

**Service communication patterns:**
- **Synchronous**: HTTP/REST, gRPC
- **Asynchronous**: Message queues (RabbitMQ, Kafka, SQS)

### Message Queues — Why Use Them

Without a queue, a spike in traffic causes direct calls to overwhelm downstream services. With a queue:

```
Order Service → Queue → Inventory Service
                   ↓
             Email Service
                   ↓
             Analytics Service
```

Benefits: decoupling, backpressure, retry on failure, replay, audit trail.

### RabbitMQ with amqplib

```js
const amqp = require('amqplib');

// Publisher
const conn = await amqp.connect('amqp://localhost');
const ch = await conn.createChannel();
await ch.assertQueue('orders', { durable: true });
ch.sendToQueue('orders', Buffer.from(JSON.stringify(order)), {
    persistent: true, // survive broker restart
});

// Consumer
const conn = await amqp.connect('amqp://localhost');
const ch = await conn.createChannel();
await ch.assertQueue('orders', { durable: true });
ch.prefetch(1); // process one at a time
ch.consume('orders', async (msg) => {
    const order = JSON.parse(msg.content.toString());
    await processOrder(order);
    ch.ack(msg); // acknowledge — remove from queue
    // ch.nack(msg, false, true) — negative ack, requeue
});
```

### Kafka with kafkajs

```js
const { Kafka } = require('kafkajs');
const kafka = new Kafka({ brokers: ['localhost:9092'] });

// Producer
const producer = kafka.producer();
await producer.connect();
await producer.send({
    topic: 'user-events',
    messages: [{ key: String(userId), value: JSON.stringify(event) }],
});

// Consumer
const consumer = kafka.consumer({ groupId: 'analytics-service' });
await consumer.connect();
await consumer.subscribe({ topic: 'user-events', fromBeginning: false });
await consumer.run({
    eachMessage: async ({ message }) => {
        const event = JSON.parse(message.value.toString());
        await analyticsService.track(event);
    },
});
```

**RabbitMQ vs Kafka:**

| | RabbitMQ | Kafka |
|---|---|---|
| Model | Push (broker delivers) | Pull (consumer reads) |
| Message retention | Until consumed | Log-based (configured time) |
| Ordering | Per-queue | Per-partition |
| Replay | No | Yes |
| Use case | Task queues, RPC | Event streaming, audit log |

### Circuit Breaker Pattern

Prevents cascading failures — stop calling a failing service:

```js
const CircuitBreaker = require('opossum');

const options = {
    timeout: 3000,           // fail if takes >3s
    errorThresholdPercentage: 50, // open if >50% fail
    resetTimeout: 30000,     // retry after 30s
};

const breaker = new CircuitBreaker(callPaymentService, options);
breaker.on('open', () => logger.warn('Circuit open — payment service down'));
breaker.on('halfOpen', () => logger.info('Circuit half-open — testing'));

// Use it like the original function
const result = await breaker.fire(paymentData);
```

### Service Discovery & Health Checks

```js
// Express health endpoint — load balancers check this
app.get('/health', (req, res) => {
    res.json({
        status: 'ok',
        uptime: process.uptime(),
        memory: process.memoryUsage(),
    });
});

// Readiness — are dependencies ready?
app.get('/ready', async (req, res) => {
    const dbOk = await db.ping().then(() => true).catch(() => false);
    res.status(dbOk ? 200 : 503).json({ db: dbOk });
});
```
MD;
    }

    private function lessonL5_3(): string
    {
        return <<<'MD'
## Production Node.js: Zero-Downtime Deployment, Monitoring & OpenTelemetry

### Graceful Shutdown

Never kill a Node.js server abruptly — in-flight requests will fail:

```js
const server = app.listen(3000);

async function shutdown(signal) {
    logger.info(`Received ${signal} — shutting down gracefully`);

    server.close(async () => {
        logger.info('HTTP server closed');
        await db.end();       // close DB connections
        await mqChannel.close(); // close message queue
        logger.info('Shutdown complete');
        process.exit(0);
    });

    // Force exit after 10 seconds
    setTimeout(() => {
        logger.error('Forced shutdown after timeout');
        process.exit(1);
    }, 10_000);
}

process.on('SIGTERM', () => shutdown('SIGTERM')); // Docker stop
process.on('SIGINT',  () => shutdown('SIGINT'));  // Ctrl+C
```

### Zero-Downtime Deployment with PM2

```bash
# Rolling restart — workers restart one by one, no downtime
pm2 reload ecosystem.config.js

# Blue-green via PM2 (two versions running simultaneously)
pm2 start app-v2.js --name app-v2
# Update load balancer to point to v2
pm2 delete app-v1
```

### OpenTelemetry — Distributed Tracing

OpenTelemetry is the standard for traces, metrics, and logs across services:

```js
const { NodeSDK } = require('@opentelemetry/sdk-node');
const { getNodeAutoInstrumentations } = require('@opentelemetry/auto-instrumentations-node');
const { OTLPTraceExporter } = require('@opentelemetry/exporter-trace-otlp-http');

const sdk = new NodeSDK({
    traceExporter: new OTLPTraceExporter({ url: 'http://collector:4318/v1/traces' }),
    instrumentations: [getNodeAutoInstrumentations()],
});

sdk.start(); // auto-instruments http, express, pg, mysql2, redis, etc.
```

Custom spans:
```js
const { trace } = require('@opentelemetry/api');
const tracer = trace.getTracer('my-service');

async function processOrder(order) {
    const span = tracer.startSpan('processOrder');
    span.setAttribute('order.id', order.id);
    span.setAttribute('order.total', order.total);
    try {
        await chargePayment(order);
        span.setStatus({ code: SpanStatusCode.OK });
    } catch (err) {
        span.recordException(err);
        span.setStatus({ code: SpanStatusCode.ERROR });
        throw err;
    } finally {
        span.end();
    }
}
```

### Production Monitoring Checklist

**Metrics to track (Prometheus + Grafana or Datadog):**
- HTTP request rate, error rate, p95/p99 latency
- Event loop lag (p99)
- Memory (RSS, heap used)
- CPU usage per process
- Active DB connections
- Queue depth and consumer lag

**Alerts to configure:**
- Error rate > 1% for 5 minutes
- p99 latency > 500ms
- Event loop lag > 200ms
- Memory > 80% of limit
- Health check failure

### Docker & Container Production Tips

```dockerfile
FROM node:20-alpine
WORKDIR /app
COPY package*.json ./
RUN npm ci --omit=dev     # production deps only, reproducible
COPY . .
USER node                 # never run as root
EXPOSE 3000
CMD ["node", "--max-old-space-size=512", "dist/server.js"]
```

Set `NODE_ENV=production` — enables Express's production mode (no stack traces in errors, better caching).

### CI/CD Pipeline Structure

```
Push → Lint + Type Check → Unit Tests → Build
     → Integration Tests → Docker Build
     → Staging Deploy → Smoke Tests
     → Production Deploy (rolling) → Health Check
```
MD;
    }

    // ─── Level 4 Exam Questions ───────────────────────────────────────────────

    private function seedLevel4Questions(Topic $topic): void
    {
        $this->command->info('Node.js Level 4: exam questions seeding..');
        $questions = $this->level4Questions();
        $this->insertQuestions($topic, $questions);
        $this->command->info('Node.js Level 4: ' . count($questions) . ' questions total.');
    }

    private function level4Questions(): array
    {
        return [
            [
                'question' => 'What is the primary purpose of the Repository Pattern in a Node.js application?',
                'options'  => [
                    ['text' => 'Separate data access logic from business logic so the service layer is database-agnostic', 'is_correct' => true],
                    ['text' => 'Cache database query results to reduce round-trips to the database', 'is_correct' => false],
                    ['text' => 'Manage HTTP request routing and middleware registration', 'is_correct' => false],
                    ['text' => 'Automatically generate REST API endpoints from database schemas', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What does Dependency Injection (DI) enable in Node.js services?',
                'options'  => [
                    ['text' => 'Swap implementations (e.g., real vs mock) without changing the class that uses them, improving testability', 'is_correct' => true],
                    ['text' => 'Automatically inject middleware into Express routes at startup', 'is_correct' => false],
                    ['text' => 'Inject environment variables into process.env from a config file', 'is_correct' => false],
                    ['text' => 'Share a single database connection instance across all modules', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What does `stream.pipeline()` from `stream/promises` provide over raw `.pipe()`?',
                'options'  => [
                    ['text' => 'Propagates errors from any stream in the chain and destroys all streams on failure', 'is_correct' => true],
                    ['text' => 'Pipes data at a faster rate by using Worker Threads internally', 'is_correct' => false],
                    ['text' => 'Returns the data as a Buffer instead of streaming it', 'is_correct' => false],
                    ['text' => 'Automatically retries the pipeline on transient network errors', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What are the parameters of a Transform stream\'s `_transform(chunk, encoding, callback)` method?',
                'options'  => [
                    ['text' => 'chunk is the incoming data piece; encoding is the string encoding; callback signals chunk processing is done', 'is_correct' => true],
                    ['text' => 'chunk is the total data; encoding is the output format; callback returns the transformed result', 'is_correct' => false],
                    ['text' => 'chunk is the stream index; encoding is the compression type; callback is an error handler', 'is_correct' => false],
                    ['text' => 'All three are optional — only callback is required for a working Transform stream', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What happens when `writable.write()` returns `false` in a Node.js stream?',
                'options'  => [
                    ['text' => 'The writable buffer is full — pause the readable and wait for the "drain" event before resuming', 'is_correct' => true],
                    ['text' => 'The write failed permanently — the data must be resent', 'is_correct' => false],
                    ['text' => 'The stream has ended — no more data can be written', 'is_correct' => false],
                    ['text' => 'The write was queued but deferred to the next event loop tick', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What does `Readable.from(asyncIterable)` do in Node.js?',
                'options'  => [
                    ['text' => 'Creates a Readable stream from any async iterable or generator function', 'is_correct' => true],
                    ['text' => 'Creates a Readable stream from a Buffer or string only', 'is_correct' => false],
                    ['text' => 'Converts a Writable stream into a Readable stream', 'is_correct' => false],
                    ['text' => 'Reads all data from an async source into memory and emits it as one chunk', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What does `jest.fn()` create in a Jest test?',
                'options'  => [
                    ['text' => 'A mock function that records all calls, arguments, and return values for later assertion', 'is_correct' => true],
                    ['text' => 'A spy that wraps an existing function without replacing it', 'is_correct' => false],
                    ['text' => 'A function that throws a specific error when called', 'is_correct' => false],
                    ['text' => 'A timer function that automatically advances fake timers', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What is the difference between a stub and a spy in unit testing?',
                'options'  => [
                    ['text' => 'A stub replaces a function entirely with a controlled implementation; a spy wraps the original and records calls', 'is_correct' => true],
                    ['text' => 'A spy replaces the function; a stub observes without replacing', 'is_correct' => false],
                    ['text' => 'Both are identical — only the naming convention differs between testing libraries', 'is_correct' => false],
                    ['text' => 'Stubs work with async functions; spies only work with synchronous code', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What does `jest.spyOn(object, methodName)` do?',
                'options'  => [
                    ['text' => 'Wraps the method in a jest.fn() so you can assert calls while keeping the original implementation', 'is_correct' => true],
                    ['text' => 'Completely replaces the method with a no-op that returns undefined', 'is_correct' => false],
                    ['text' => 'Logs every call to the method to the Jest test report', 'is_correct' => false],
                    ['text' => 'Prevents the method from being called and throws if it is invoked', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What is `AsyncLocalStorage` used for in a Node.js production application?',
                'options'  => [
                    ['text' => 'Store per-request context (e.g., request ID, user) that flows automatically across all async operations', 'is_correct' => true],
                    ['text' => 'Cache async function results so they are not recomputed within the same request', 'is_correct' => false],
                    ['text' => 'Store data in localStorage on the client side from a Node.js server', 'is_correct' => false],
                    ['text' => 'Provide a shared memory area for Worker Threads to exchange data', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Which SOLID principle does the following demonstrate? `class UserService { constructor(notifier) { this.notifier = notifier; } }`',
                'options'  => [
                    ['text' => 'Dependency Inversion — UserService depends on an abstraction (any notifier), not a concrete implementation', 'is_correct' => true],
                    ['text' => 'Single Responsibility — UserService only manages user notifications', 'is_correct' => false],
                    ['text' => 'Open/Closed — UserService is open for extension by accepting new notifiers', 'is_correct' => false],
                    ['text' => 'Liskov Substitution — any notifier subtype can replace the base notifier', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What does `beforeEach(() => jest.clearAllMocks())` ensure in a Jest test suite?',
                'options'  => [
                    ['text' => 'Resets all mock call counts and instances before each test so tests do not interfere with each other', 'is_correct' => true],
                    ['text' => 'Deletes all mock functions and restores the original implementations before each test', 'is_correct' => false],
                    ['text' => 'Runs all mocked async functions synchronously for predictable test order', 'is_correct' => false],
                    ['text' => 'Clears the Jest module registry so modules are re-imported fresh in every test', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What is object mode (`{ objectMode: true }`) in a Node.js stream?',
                'options'  => [
                    ['text' => 'Allows a stream to pass arbitrary JavaScript values instead of only Buffers or strings', 'is_correct' => true],
                    ['text' => 'Makes a stream operate synchronously, processing each object before moving to the next', 'is_correct' => false],
                    ['text' => 'Enables JSON serialization so objects are automatically stringified before being emitted', 'is_correct' => false],
                    ['text' => 'Converts the stream from push to pull mode so consumers can request data on demand', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'In clean architecture for Node.js, what belongs in the Service Layer?',
                'options'  => [
                    ['text' => 'Business rules and use-case logic — no HTTP or database code', 'is_correct' => true],
                    ['text' => 'HTTP parsing, request validation, and response formatting', 'is_correct' => false],
                    ['text' => 'SQL queries, ORM calls, and database connection management', 'is_correct' => false],
                    ['text' => 'Authentication middleware and JWT verification', 'is_correct' => false],
                ],
            ],
        ];
    }

    // ─── Level 5 Exam Questions ───────────────────────────────────────────────

    private function seedLevel5Questions(Topic $topic): void
    {
        $this->command->info('Node.js Level 5: exam questions seeding..');
        $questions = $this->level5Questions();
        $this->insertQuestions($topic, $questions);
        $this->command->info('Node.js Level 5: ' . count($questions) . ' questions total.');
    }

    private function level5Questions(): array
    {
        return [
            [
                'question' => 'What does `node --prof app.js` do and how do you process the output?',
                'options'  => [
                    ['text' => 'Enables V8\'s sampling CPU profiler; run `node --prof-process isolate-*.log` to get a human-readable CPU report', 'is_correct' => true],
                    ['text' => 'Enables production mode with all V8 optimisations and disables DevTools debugging', 'is_correct' => false],
                    ['text' => 'Runs the app in profiled mode and outputs a JSON heap snapshot to disk', 'is_correct' => false],
                    ['text' => 'Counts function calls and writes a flamegraph directly to a .html file', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What does a heap snapshot reveal in a Node.js performance investigation?',
                'options'  => [
                    ['text' => 'All objects in the V8 heap — comparing two snapshots over time identifies memory leak candidates', 'is_correct' => true],
                    ['text' => 'CPU time spent in each function during a load test', 'is_correct' => false],
                    ['text' => 'The number of event loop iterations per second', 'is_correct' => false],
                    ['text' => 'The size of the libuv thread pool and pending I/O operations', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What V8 optimization issue does adding properties to an object after creation cause?',
                'options'  => [
                    ['text' => 'V8 creates a new hidden class for each new shape, preventing the JIT from optimizing the object access', 'is_correct' => true],
                    ['text' => 'The object is garbage collected immediately since its shape is unstable', 'is_correct' => false],
                    ['text' => 'The property is stored as a string key instead of an index, causing O(n) lookups', 'is_correct' => false],
                    ['text' => 'V8 falls back to the interpreter and disables JIT for the entire module', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What is the event loop lag metric and how is it measured with `perf_hooks`?',
                'options'  => [
                    ['text' => 'Time between scheduling a callback and when it actually runs — measured with `monitorEventLoopDelay()`', 'is_correct' => true],
                    ['text' => 'Total time the event loop has been running since the process started', 'is_correct' => false],
                    ['text' => 'The number of callbacks queued but not yet executed in the current tick', 'is_correct' => false],
                    ['text' => 'The time difference between two consecutive `process.nextTick` calls', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'In the message queue pattern, what is the difference between RabbitMQ and Kafka?',
                'options'  => [
                    ['text' => 'RabbitMQ is push-based with per-message acknowledgement; Kafka is pull-based with a persistent log and replay support', 'is_correct' => true],
                    ['text' => 'Kafka is a real-time database; RabbitMQ is a caching layer for Node.js APIs', 'is_correct' => false],
                    ['text' => 'They are identical in design — only the programming language of the broker differs', 'is_correct' => false],
                    ['text' => 'RabbitMQ stores messages forever; Kafka deletes them immediately after delivery', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What does `ch.prefetch(1)` do in a RabbitMQ consumer using amqplib?',
                'options'  => [
                    ['text' => 'Limits the consumer to processing one unacknowledged message at a time, preventing overload', 'is_correct' => true],
                    ['text' => 'Pre-downloads the first message in the queue before the consumer starts', 'is_correct' => false],
                    ['text' => 'Raises the queue priority so this consumer receives messages before others', 'is_correct' => false],
                    ['text' => 'Sets the batch size — the consumer receives exactly 1 message per polling interval', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What does the Circuit Breaker pattern protect against in a Node.js microservice?',
                'options'  => [
                    ['text' => 'Cascading failures — it stops calling a failing service after a threshold and resumes after a cooldown', 'is_correct' => true],
                    ['text' => 'SQL injection attacks by validating inputs before forwarding requests', 'is_correct' => false],
                    ['text' => 'Memory leaks by resetting service state when usage exceeds a threshold', 'is_correct' => false],
                    ['text' => 'Rate limiting clients by breaking the connection after too many requests', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What does OpenTelemetry auto-instrumentation do in a Node.js application?',
                'options'  => [
                    ['text' => 'Automatically adds distributed traces to HTTP, database, and Redis calls without manual span creation', 'is_correct' => true],
                    ['text' => 'Automatically optimises slow database queries identified during runtime', 'is_correct' => false],
                    ['text' => 'Generates documentation from route definitions and JSDoc comments', 'is_correct' => false],
                    ['text' => 'Automatically scales the Node.js cluster to match observed traffic patterns', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'Why must a Node.js server listen to `SIGTERM` and implement graceful shutdown?',
                'options'  => [
                    ['text' => 'SIGTERM is sent by orchestrators (Docker, Kubernetes) on deployment — without graceful shutdown, in-flight requests are dropped', 'is_correct' => true],
                    ['text' => 'SIGTERM is sent by the OS when memory exceeds the limit — the server must release memory before exiting', 'is_correct' => false],
                    ['text' => 'SIGTERM triggers a hot reload — the server must save state before the new version starts', 'is_correct' => false],
                    ['text' => 'SIGTERM is only sent in development — in production the process is always killed with SIGKILL', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What is a zero-downtime deployment strategy for a Node.js application running under PM2?',
                'options'  => [
                    ['text' => '`pm2 reload` restarts workers one at a time while the others keep serving traffic', 'is_correct' => true],
                    ['text' => '`pm2 restart` stops all workers simultaneously and starts the new version immediately', 'is_correct' => false],
                    ['text' => 'Deploy a new Node.js version and the old version automatically hands off connections', 'is_correct' => false],
                    ['text' => 'Zero-downtime is not achievable without a Kubernetes cluster', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What is the purpose of the `/health` and `/ready` endpoints in a production Node.js service?',
                'options'  => [
                    ['text' => '/health confirms the process is alive; /ready confirms dependencies (DB, queue) are ready to serve traffic', 'is_correct' => true],
                    ['text' => '/health exposes heap and CPU metrics; /ready locks the endpoint until all requests are processed', 'is_correct' => false],
                    ['text' => 'Both are identical — they serve as duplicate health-check endpoints for redundancy', 'is_correct' => false],
                    ['text' => '/health restarts the process; /ready warms up the cache before accepting requests', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'In a Node.js Docker container, why should the process run as a non-root user?',
                'options'  => [
                    ['text' => 'If the container is compromised, a non-root process has limited OS permissions — reducing the blast radius', 'is_correct' => true],
                    ['text' => 'Node.js cannot bind to port 3000 when running as root', 'is_correct' => false],
                    ['text' => 'npm install fails with EACCES errors when run as root inside Docker', 'is_correct' => false],
                    ['text' => 'Docker automatically terminates root processes when container memory exceeds the limit', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What causes a memory leak when using an event emitter inside a request handler?',
                'options'  => [
                    ['text' => 'Adding listeners with `.on()` inside handlers without removing them — they accumulate across requests', 'is_correct' => true],
                    ['text' => 'Emitting events inside an async function prevents the garbage collector from collecting the emitter', 'is_correct' => false],
                    ['text' => 'EventEmitter keeps a strong reference to the HTTP request object, preventing GC', 'is_correct' => false],
                    ['text' => 'Using `.once()` instead of `.on()` causes listeners to stay registered permanently', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What does `npm ci --omit=dev` do in a production Docker build?',
                'options'  => [
                    ['text' => 'Installs exact versions from package-lock.json and skips devDependencies — reproducible and lean', 'is_correct' => true],
                    ['text' => 'Installs only devDependencies and omits runtime packages for build-only containers', 'is_correct' => false],
                    ['text' => 'Clears the npm cache and performs a clean install with all dependencies', 'is_correct' => false],
                    ['text' => 'Runs npm audit before install and blocks if any critical vulnerabilities are found', 'is_correct' => false],
                ],
            ],
            [
                'question' => 'What does the `--max-old-space-size` flag control in a Node.js production container?',
                'options'  => [
                    ['text' => 'Sets the maximum size of the V8 old-generation heap in MB — prevents unbounded memory growth', 'is_correct' => true],
                    ['text' => 'Sets the maximum number of old log files to retain before rotation', 'is_correct' => false],
                    ['text' => 'Controls the maximum age of cached module exports before they are reloaded', 'is_correct' => false],
                    ['text' => 'Limits the size of the libuv thread pool to reduce memory usage', 'is_correct' => false],
                ],
            ],
        ];
    }

    // ─── Shared Helper ────────────────────────────────────────────────────────

    private function insertQuestions(Topic $topic, array $questions): void
    {
        Question::where('topic_id', $topic->id)->delete();

        foreach ($questions as $qData) {
            $question = Question::create([
                'topic_id'   => $topic->id,
                'question'   => $qData['question'],
                'type'       => 'MCQ',
                'difficulty' => 'Medium',
            ]);

            QuestionOption::insert(array_map(fn ($opt) => [
                'question_id' => $question->id,
                'option_text' => $opt['text'],
                'is_correct'  => $opt['is_correct'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ], $qData['options']));
        }
    }
}
