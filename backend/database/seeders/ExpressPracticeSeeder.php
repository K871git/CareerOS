<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;

class ExpressPracticeSeeder extends Seeder
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
                'description'       => 'Express.js is the most popular Node.js web framework. Master routing, middleware, REST API design, error handling, and building production-ready Express applications.',
                'display_order'     => 7,
            ]
        );

        $levels = [
            [
                'title'         => 'Express.js Basics — Junior',
                'slug'          => 'express-junior',
                'description'   => 'Express routing, request/response objects, and basic middleware. For junior backend developer interviews.',
                'display_order' => 1,
                'difficulty'    => 'Easy',
                'questions'     => $this->juniorQuestions(),
            ],
            [
                'title'         => 'Express.js Intermediate',
                'slug'          => 'express-intermediate',
                'description'   => 'Router organization, error handling, authentication middleware, and REST API design patterns.',
                'display_order' => 2,
                'difficulty'    => 'Medium',
                'questions'     => $this->intermediateQuestions(),
            ],
            [
                'title'         => 'Express.js Advanced',
                'slug'          => 'express-advanced',
                'description'   => 'Performance, security hardening, testing, and production architecture for Express APIs.',
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

        $this->command->info('Express.js Practice seeded: 1 subject, 3 topics, ~100 questions.');
    }

    private function juniorQuestions(): array
    {
        return [
            // ── Original 10 ──────────────────────────────────────────────────
            [
                'question'    => 'What is Express.js?',
                'explanation' => 'Express.js is a minimal, unopinionated web framework for Node.js. It provides a thin layer of fundamental web application features — routing, middleware support, and HTTP utility methods — without obscuring the Node.js core HTTP module. It is the most popular Node.js framework and the foundation of many full-stack solutions like the MEAN/MERN stack.',
                'options'     => [
                    ['text' => 'A minimal, unopinionated Node.js web framework for routing and middleware', 'correct' => true],
                    ['text' => 'A full-stack JavaScript framework like Next.js or Nuxt.js', 'correct' => false],
                    ['text' => 'A package manager for Node.js Express applications', 'correct' => false],
                    ['text' => 'A JavaScript runtime environment similar to Node.js', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you create a basic Express server?',
                'explanation' => 'Install Express (`npm install express`), then: `const express = require("express"); const app = express(); app.get("/", (req, res) => res.send("Hello")); app.listen(3000)`. The `express()` call creates an application instance. Routes are registered with HTTP method functions. `app.listen()` starts the HTTP server on the specified port.',
                'options'     => [
                    ['text' => 'const app = express(); app.get("/", handler); app.listen(3000)', 'correct' => true],
                    ['text' => 'new Express().start(3000, (req, res) => res.send("Hello"))', 'correct' => false],
                    ['text' => 'http.createExpressServer(3000, handler)', 'correct' => false],
                    ['text' => 'Express.init({ port: 3000, routes: [] })', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is routing in Express.js?',
                'explanation' => 'Routing defines how an application responds to client requests for specific URLs and HTTP methods. Express provides methods that match HTTP verbs: `app.get()`, `app.post()`, `app.put()`, `app.delete()`, `app.patch()`, `app.all()`. Each takes a path pattern and one or more handler functions. Route parameters are defined with a colon: `/users/:id`.',
                'options'     => [
                    ['text' => 'Mapping HTTP methods + URL paths to handler functions (app.get, app.post, etc.)', 'correct' => true],
                    ['text' => 'Redirecting incoming requests to a different URL', 'correct' => false],
                    ['text' => 'The middleware pipeline that all requests pass through', 'correct' => false],
                    ['text' => 'Load balancing requests between multiple Express server instances', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are `req` and `res` objects in an Express route handler?',
                'explanation' => '`req` (Request) represents the incoming HTTP request — it contains properties like `req.params` (route parameters), `req.query` (query string), `req.body` (request body, parsed by middleware), `req.headers`, and `req.method`. `res` (Response) is used to send a response back — `res.json()`, `res.send()`, `res.status()`, `res.redirect()`. Together they are the core of every Express handler.',
                'options'     => [
                    ['text' => 'req holds incoming request data (params, body, headers); res sends the HTTP response', 'correct' => true],
                    ['text' => 'req is the database query object; res is the resolved database result', 'correct' => false],
                    ['text' => 'req and res are the two parameters of the Express app constructor', 'correct' => false],
                    ['text' => 'req is the request validator; res is the response serializer middleware', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `res.json()` do in Express?',
                'explanation' => '`res.json(data)` sends a JSON response. It automatically sets the `Content-Type` header to `application/json` and serializes the data argument using `JSON.stringify()`. It also handles null, booleans, and arrays correctly. It is the standard way to respond from a REST API endpoint. Equivalent to `res.send()` with the Content-Type header manually set.',
                'options'     => [
                    ['text' => 'Sends a JSON response with Content-Type: application/json and JSON.stringify(data)', 'correct' => true],
                    ['text' => 'Parses an incoming JSON request body and attaches it to req.json', 'correct' => false],
                    ['text' => 'Validates that the response data matches a JSON schema before sending', 'correct' => false],
                    ['text' => 'Renders a JSON template file from the views directory', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `express.json()` middleware?',
                'explanation' => '`express.json()` is built-in middleware that parses incoming requests with JSON payloads and populates `req.body`. Without it, `req.body` is `undefined` for POST/PUT requests. It is the modern replacement for the deprecated `body-parser` package. You register it with `app.use(express.json())` before your routes. Pair it with `express.urlencoded()` for form data.',
                'options'     => [
                    ['text' => 'Middleware that parses incoming JSON request bodies and populates req.body', 'correct' => true],
                    ['text' => 'A method that converts the Express response to a JSON stream', 'correct' => false],
                    ['text' => 'A middleware that validates JSON format before passing to route handlers', 'correct' => false],
                    ['text' => 'An Express method for serving static JSON files from the public folder', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you access route parameters in Express?',
                'explanation' => 'Route parameters are named URL segments prefixed with `:`. They are accessible via `req.params`. Example: for route `/users/:id` and URL `/users/42`, `req.params.id` is `"42"`. Multiple parameters work too: `/posts/:year/:month`. Always parse parameters to the correct type — they arrive as strings.',
                'options'     => [
                    ['text' => 'Via req.params — route segments defined with : prefix (e.g., /users/:id → req.params.id)', 'correct' => true],
                    ['text' => 'Via req.body — route parameters are sent in the request body for security', 'correct' => false],
                    ['text' => 'Via req.query — route parameters appear after the ? in the URL', 'correct' => false],
                    ['text' => 'Via res.params — shared between req and res objects', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `req.params` and `req.query` in Express?',
                'explanation' => '`req.params` contains named route parameters from the URL path, defined with `:` (e.g., `/users/:id` → `req.params.id = "42"`). `req.query` contains the parsed query string — the key-value pairs after the `?` in the URL (e.g., `/search?q=node&limit=10` → `req.query.q = "node"`, `req.query.limit = "10"`). Both are strings — parse as needed.',
                'options'     => [
                    ['text' => 'params = named path segments (:id); query = key-value pairs after ? in the URL', 'correct' => true],
                    ['text' => 'params = POST body fields; query = GET URL parameters', 'correct' => false],
                    ['text' => 'They are the same — both parse the query string from the URL', 'correct' => false],
                    ['text' => 'params = validated parameters; query = raw unvalidated query string', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you serve static files in Express?',
                'explanation' => '`express.static()` is built-in middleware for serving static assets (HTML, CSS, images, JavaScript). Usage: `app.use(express.static("public"))`. Requests for files that exist in the `public` directory are served automatically. You can add a virtual prefix: `app.use("/static", express.static("public"))` makes files accessible at `/static/file.css`. It uses the `serve-static` package internally.',
                'options'     => [
                    ['text' => 'app.use(express.static("public")) — serves files from the named directory', 'correct' => true],
                    ['text' => 'app.serveFiles("public") — a dedicated static file serving method', 'correct' => false],
                    ['text' => 'app.get("*", express.sendFile("./public")) — catches all routes', 'correct' => false],
                    ['text' => 'Place files in /static directory — Express serves them automatically without config', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `app.use()` do in Express?',
                'explanation' => '`app.use()` mounts middleware or a router at a specified path (default: `/`). Middleware registered with `app.use()` runs for every request matching the path. It is the primary way to add: body parsers, authentication checks, CORS headers, logging, static file serving, and sub-routers. Order matters — middleware is applied in the order it is registered.',
                'options'     => [
                    ['text' => 'Mounts middleware or a router for all requests — order of registration matters', 'correct' => true],
                    ['text' => 'Marks a specific route as enabled/active in the Express app', 'correct' => false],
                    ['text' => 'Imports an npm package and makes it available throughout the app', 'correct' => false],
                    ['text' => 'Starts the Express server and begins accepting connections', 'correct' => false],
                ],
            ],
            // ── New additions (23 more) ───────────────────────────────────────
            [
                'question'    => 'How does Express differ from Node.js\'s built-in `http` module?',
                'explanation' => 'The built-in `http` module is low-level — you get a raw `req` (IncomingMessage) and `res` (ServerResponse) and must manually parse URLs, route by `req.url`, parse bodies, and set headers. Express wraps the `http` module and adds: automatic routing with pattern matching, a middleware pipeline, body parsing helpers, `req.params`/`req.query`, `res.json()`/`res.status()`, and much more. Express is essentially a DSL on top of `http`.',
                'options'     => [
                    ['text' => 'Express adds routing, middleware, and helper methods on top of the raw http module', 'correct' => true],
                    ['text' => 'Express replaces Node.js http entirely with its own networking layer', 'correct' => false],
                    ['text' => 'They are identical — Express is just an alias for the http module', 'correct' => false],
                    ['text' => 'The http module supports async/await; Express is callback-only', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `app.all()` do in Express?',
                'explanation' => '`app.all(path, handler)` matches all HTTP methods (GET, POST, PUT, DELETE, PATCH, etc.) for a given path. It is useful for applying middleware to a specific route regardless of the method — for example, authenticating or logging all requests to `/api/secret`. It is different from `app.use()` in that it matches the path exactly (not as a prefix) by default.',
                'options'     => [
                    ['text' => 'Matches all HTTP methods for a given path — useful for per-route middleware', 'correct' => true],
                    ['text' => 'Registers a catch-all handler that runs after every route has been checked', 'correct' => false],
                    ['text' => 'Enables all built-in Express features like compression and CORS at once', 'correct' => false],
                    ['text' => 'Aggregates all route definitions and validates them for conflicts', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `res.send()`, `res.json()`, and `res.end()` in Express?',
                'explanation' => '`res.send(body)` is a general-purpose response method — it auto-sets Content-Type based on the data type (Buffer → `application/octet-stream`, string → `text/html`, object → `application/json`). `res.json(data)` is specifically for JSON — it always sets `Content-Type: application/json`. `res.end()` is the raw Node.js method — it sends no body by default and does not set any headers. Prefer `res.json()` for APIs and `res.send()` for text/HTML.',
                'options'     => [
                    ['text' => 'res.send() auto-detects type; res.json() always sets application/json; res.end() is raw Node.js with no type detection', 'correct' => true],
                    ['text' => 'They are identical — all three serialize the body as JSON and send it', 'correct' => false],
                    ['text' => 'res.end() sends headers only; res.send() sends body only; res.json() sends both', 'correct' => false],
                    ['text' => 'res.json() is deprecated — use res.send() with JSON.stringify() manually', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you chain `res.status()` in Express?',
                'explanation' => '`res.status(code)` sets the HTTP status code for the response and returns the `res` object, enabling method chaining. Common pattern: `res.status(201).json({ id: newUser.id })` or `res.status(404).json({ error: "Not found" })`. Without calling `res.status()`, the default status is 200. Always set an appropriate status code before sending — it communicates success or failure to the client.',
                'options'     => [
                    ['text' => 'res.status(code) returns res for chaining — e.g., res.status(404).json({ error: "Not found" })', 'correct' => true],
                    ['text' => 'res.status() can only be called once and must be the last method in the chain', 'correct' => false],
                    ['text' => 'Status codes are set via res.set("Status", 404) — res.status() does not exist', 'correct' => false],
                    ['text' => 'res.status() sends the response immediately without a body', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between HTTP 301 and 302 redirects, and how does Express handle them?',
                'explanation' => 'HTTP 301 (Moved Permanently) tells browsers and search engines the resource has permanently moved — browsers cache it and future requests go directly to the new URL. HTTP 302 (Found/Temporary Redirect) means the move is temporary — browsers do not cache it. In Express: `res.redirect(301, "/new-url")` for permanent, `res.redirect("/new-url")` defaults to 302. Use 301 for URL changes you want search engines to index.',
                'options'     => [
                    ['text' => '301 is permanent (cached by browsers/SEO); 302 is temporary — res.redirect(301, url) or res.redirect(url)', 'correct' => true],
                    ['text' => '301 redirects POST requests; 302 redirects GET requests only', 'correct' => false],
                    ['text' => 'They are identical — browsers treat both 301 and 302 the same way', 'correct' => false],
                    ['text' => '301 requires authentication; 302 is for public redirect endpoints', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `res.sendFile()` do in Express?',
                'explanation' => '`res.sendFile(absolutePath)` sends a file as an HTTP response. It automatically sets the `Content-Type` header based on the file extension, handles range requests, and sets `Last-Modified`. You must pass an absolute path or use the `root` option: `res.sendFile("index.html", { root: __dirname + "/public" })`. It is commonly used for SPAs where you serve `index.html` for all unmatched routes.',
                'options'     => [
                    ['text' => 'Sends a file as the HTTP response, auto-setting Content-Type based on file extension', 'correct' => true],
                    ['text' => 'Uploads a file from the client to the server\'s filesystem', 'correct' => false],
                    ['text' => 'Streams a file into the request body for processing', 'correct' => false],
                    ['text' => 'Reads a file and attaches it to req.file for use in route handlers', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `res.download()` used for in Express?',
                'explanation' => '`res.download(path, filename, options, callback)` triggers a file download in the browser by setting the `Content-Disposition: attachment` header. The optional `filename` argument sets the suggested download filename. It is similar to `res.sendFile()` but forces the browser to download rather than display the file. Useful for export endpoints (CSV downloads, PDF reports, etc.).',
                'options'     => [
                    ['text' => 'Triggers a file download in the browser by setting Content-Disposition: attachment', 'correct' => true],
                    ['text' => 'Downloads a remote file from a URL and caches it on the Express server', 'correct' => false],
                    ['text' => 'Streams a large response in chunks to reduce memory usage', 'correct' => false],
                    ['text' => 'Sends the response body as a base64-encoded string for binary data', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you set custom response headers in Express using `res.set()`?',
                'explanation' => '`res.set(name, value)` sets a single response header. You can also pass an object to set multiple headers at once: `res.set({ "X-Request-Id": uuid, "Cache-Control": "no-store" })`. It is an alias for `res.header()`. Common use cases: setting CORS headers manually, adding custom tracing headers, setting Cache-Control policies, or sending API version headers.',
                'options'     => [
                    ['text' => 'res.set("Header-Name", "value") or res.set({ key: val, key2: val2 }) for multiple', 'correct' => true],
                    ['text' => 'res.addHeader("Header-Name", "value") — res.set() is used for app settings only', 'correct' => false],
                    ['text' => 'Headers can only be set before calling app.listen() — not inside route handlers', 'correct' => false],
                    ['text' => 'Use req.set() to set response headers — res.set() reads incoming headers', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `req.ip` contain in Express?',
                'explanation' => '`req.ip` contains the remote IP address of the request. When running behind a proxy (like nginx or a load balancer), the actual client IP is in the `X-Forwarded-For` header, not the socket IP. To get the real client IP, set `app.set("trust proxy", 1)` and Express will read `X-Forwarded-For` automatically into `req.ip`. Without trust proxy, `req.ip` shows the proxy IP instead.',
                'options'     => [
                    ['text' => 'The remote IP address of the request — use trust proxy setting when behind a load balancer', 'correct' => true],
                    ['text' => 'The server IP address that the Express app is bound to', 'correct' => false],
                    ['text' => 'The IP address of the database server connected to the request', 'correct' => false],
                    ['text' => 'An array of all IP addresses in the network path of the request', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What do `req.path`, `req.hostname`, and `req.protocol` provide?',
                'explanation' => '`req.path` contains the path part of the URL, excluding the query string (e.g., `/users/42`). `req.hostname` contains the hostname from the `Host` header (e.g., `api.example.com`) — does not include the port. `req.protocol` is `"http"` or `"https"` — behind a proxy that terminates TLS, trust proxy must be enabled for this to reflect `https` correctly.',
                'options'     => [
                    ['text' => 'req.path = URL path; req.hostname = Host header value; req.protocol = http or https', 'correct' => true],
                    ['text' => 'req.path includes query string; req.hostname includes port number; req.protocol is always http', 'correct' => false],
                    ['text' => 'These properties are only available after calling req.parse() on the incoming request', 'correct' => false],
                    ['text' => 'req.hostname is the IP address; req.protocol is the HTTP version (1.1 or 2)', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `req.secure` indicate in Express?',
                'explanation' => '`req.secure` is a boolean shorthand for `req.protocol === "https"`. It returns `true` when the request was made over HTTPS. This is useful for enforcing HTTPS in middleware or redirecting HTTP to HTTPS. When behind a reverse proxy that handles TLS termination, you must set `app.set("trust proxy", 1)` — otherwise `req.secure` will always be `false` because Express only sees the HTTP connection from the proxy.',
                'options'     => [
                    ['text' => 'A boolean that is true when the request is over HTTPS — requires trust proxy behind a load balancer', 'correct' => true],
                    ['text' => 'Indicates whether the route requires authentication before access', 'correct' => false],
                    ['text' => 'Returns true if the request body has been validated and sanitized', 'correct' => false],
                    ['text' => 'A flag indicating that the session cookie has the Secure attribute set', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `req.xhr` in Express and when is it true?',
                'explanation' => '`req.xhr` is a boolean that is `true` when the `X-Requested-With` request header equals `"XMLHttpRequest"`. jQuery and older AJAX libraries set this header automatically. It was commonly used to differentiate between regular browser page requests and AJAX requests to return different responses (HTML vs JSON). Modern fetch-based code does not set this header, so `req.xhr` is rarely used in new applications.',
                'options'     => [
                    ['text' => 'True when X-Requested-With header is XMLHttpRequest — indicates an AJAX request (mostly jQuery)', 'correct' => true],
                    ['text' => 'True when the request uses the XHR encryption protocol for secure data transfer', 'correct' => false],
                    ['text' => 'True when the client is using a cross-origin request with credentials', 'correct' => false],
                    ['text' => 'True when Express is running in Express XHR mode for binary responses', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you read a specific incoming request header in Express?',
                'explanation' => '`req.get("Header-Name")` (alias: `req.header("Header-Name")`) retrieves a specific incoming request header by name, case-insensitively. Example: `req.get("Authorization")` returns the Authorization header value. You can also access `req.headers` directly — it is an object with all headers in lowercase keys. `req.get()` is the preferred way because it is case-insensitive and handles the Referrer/Referer alias.',
                'options'     => [
                    ['text' => 'req.get("Header-Name") — case-insensitive; or access req.headers object directly', 'correct' => true],
                    ['text' => 'req.header.get("Header-Name") — using the Headers API from the browser', 'correct' => false],
                    ['text' => 'res.get("Header-Name") — response object is used to read both incoming and outgoing headers', 'correct' => false],
                    ['text' => 'app.getHeader("Header-Name") — headers are read at the application level, not per request', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `express.urlencoded()` middleware?',
                'explanation' => '`express.urlencoded({ extended: false })` is built-in middleware that parses URL-encoded bodies (the format used by HTML forms: `name=Alice&age=30`) and populates `req.body`. The `extended: false` option uses the `querystring` library (simple key-value); `extended: true` uses `qs` (supports nested objects). You need this alongside `express.json()` if your API receives both JSON and form-encoded data.',
                'options'     => [
                    ['text' => 'Parses HTML form data (application/x-www-form-urlencoded) into req.body', 'correct' => true],
                    ['text' => 'Encodes the response URL for safe transmission over HTTP', 'correct' => false],
                    ['text' => 'Decodes percent-encoded characters in route parameters automatically', 'correct' => false],
                    ['text' => 'Validates that incoming URLs match the registered route patterns', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `express.raw()` middleware do?',
                'explanation' => '`express.raw({ type: "application/octet-stream" })` parses incoming request bodies with a specified content type and exposes the raw body as a `Buffer` on `req.body`. It is useful when you need the raw bytes — for example, processing a binary file upload, a webhook payload that requires raw body for HMAC signature verification, or a custom binary protocol. You can specify any MIME type or use a function for dynamic matching.',
                'options'     => [
                    ['text' => 'Parses the request body into a Buffer — useful for binary data or HMAC signature verification', 'correct' => true],
                    ['text' => 'Returns the raw, unprocessed Express request object before any middleware runs', 'correct' => false],
                    ['text' => 'Disables body parsing so req.body remains a readable stream', 'correct' => false],
                    ['text' => 'Provides raw SQL query access to the database from within the route handler', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `express.text()` middleware do?',
                'explanation' => '`express.text()` parses incoming request bodies with `Content-Type: text/plain` and exposes the body as a plain string on `req.body`. By default it parses `text/plain` MIME type, but you can override with the `type` option. Less common than `express.json()` or `express.urlencoded()`, but useful for simple webhook endpoints or APIs that accept raw text payloads.',
                'options'     => [
                    ['text' => 'Parses text/plain request bodies into a string on req.body', 'correct' => true],
                    ['text' => 'Converts JSON responses to plain text by stripping all formatting', 'correct' => false],
                    ['text' => 'Sets the response Content-Type to text/plain for all responses', 'correct' => false],
                    ['text' => 'Reads the response body as plain text for logging purposes only', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do `app.set()` and `app.get()` work for Express application settings?',
                'explanation' => '`app.set(name, value)` stores an application-level setting. `app.get(name)` retrieves it. Express has built-in settings like `"env"` (development/production), `"trust proxy"`, `"view engine"`, and `"views"`. You can also store custom settings: `app.set("apiVersion", "v2")`. Note: `app.get(name)` for settings is different from `app.get(path, handler)` for routes — they share the same method name but different signatures.',
                'options'     => [
                    ['text' => 'app.set(name, val) stores an app setting; app.get(name) retrieves it — used for env, trust proxy, etc.', 'correct' => true],
                    ['text' => 'app.set() defines global middleware; app.get() retrieves the current middleware stack', 'correct' => false],
                    ['text' => 'They are environment variable helpers — equivalent to process.env.get() and process.env.set()', 'correct' => false],
                    ['text' => 'app.set() registers a GET route; these are aliases for app.get() route registration', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `app.locals` in Express?',
                'explanation' => '`app.locals` is an object that persists for the lifetime of the application. Properties set on it are available in all templates rendered during the app\'s lifecycle: `app.locals.siteName = "CareerOS"`. It is also accessible in request handlers via `req.app.locals`. Use it for app-wide constants like site name, version, or configuration that templates need.',
                'options'     => [
                    ['text' => 'An object for app-wide variables accessible in all templates and via req.app.locals', 'correct' => true],
                    ['text' => 'A local variable store that resets on every incoming request', 'correct' => false],
                    ['text' => 'The local filesystem path configuration for serving static assets', 'correct' => false],
                    ['text' => 'A cache object for storing database query results per application instance', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `res.locals` in Express?',
                'explanation' => '`res.locals` is an object scoped to a single request-response cycle. Data set on it is available in templates rendered during that request and in subsequent middleware. Middleware often uses it to pass data to templates: `res.locals.user = req.user`. Unlike `app.locals`, it resets for every new request, making it safe for request-specific data like the current authenticated user.',
                'options'     => [
                    ['text' => 'An object scoped to the current request — data set here is available in templates and next middleware', 'correct' => true],
                    ['text' => 'A global object shared by all requests — equivalent to app.locals', 'correct' => false],
                    ['text' => 'A local copy of the response body before it is sent to the client', 'correct' => false],
                    ['text' => 'A database result cache local to the response object', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you register a template engine with `app.engine()` in Express?',
                'explanation' => '`app.engine(ext, fn)` registers a template engine for a file extension. Express supports any engine that follows the `(path, options, callback)` API. Example: `app.engine("ejs", require("ejs").__express)` or `app.set("view engine", "ejs")` (which registers it implicitly). After setting `app.set("views", "./views")`, you call `res.render("template")` to render `./views/template.ejs`.',
                'options'     => [
                    ['text' => 'app.engine(ext, fn) registers a template engine; pair with app.set("view engine", ext)', 'correct' => true],
                    ['text' => 'Template engines are auto-detected — app.engine() is only for overriding defaults', 'correct' => false],
                    ['text' => 'app.engine() starts the Express rendering engine as a background process', 'correct' => false],
                    ['text' => 'app.engine() is deprecated — use res.render() with an inline render function instead', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `app.render()` do in Express?',
                'explanation' => '`app.render(view, options, callback)` renders a view template to an HTML string and passes it to the callback — without sending it as an HTTP response. It is useful for rendering templates in background jobs or email generation where you need the HTML string but are not inside a request context. `res.render()` is the per-request version — it renders and sends the response automatically.',
                'options'     => [
                    ['text' => 'Renders a view to an HTML string via callback — does not send the response automatically', 'correct' => true],
                    ['text' => 'Identical to res.render() — both send the rendered template as the HTTP response', 'correct' => false],
                    ['text' => 'Renders all registered views at startup and caches them for performance', 'correct' => false],
                    ['text' => 'Renders a React component server-side and hydrates it in Express', 'correct' => false],
                ],
            ],
        ];
    }

    private function intermediateQuestions(): array
    {
        return [
            // ── Original 10 ──────────────────────────────────────────────────
            [
                'question'    => 'What is `express.Router()` and why is it used?',
                'explanation' => '`express.Router()` creates a modular, mountable route handler — a mini Express application. You define routes on the router instance, export it, and mount it in the main app: `app.use("/users", userRouter)`. This separates routes into logical modules (users, products, auth), keeping the main `app.js` clean. Each router can also have its own middleware.',
                'options'     => [
                    ['text' => 'Creates a mini Express app for organizing routes into separate, mountable modules', 'correct' => true],
                    ['text' => 'A class for configuring URL redirects throughout the application', 'correct' => false],
                    ['text' => 'A built-in Express load balancer for distributing requests to multiple handlers', 'correct' => false],
                    ['text' => 'A function that generates route documentation automatically', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you implement global error handling in Express?',
                'explanation' => 'Express error-handling middleware has a signature with four parameters: `(err, req, res, next)`. It must be registered after all other middleware and routes: `app.use((err, req, res, next) => { res.status(err.status || 500).json({ error: err.message }) })`. Route handlers call `next(err)` to pass errors to this handler. Without it, errors result in the default HTML error page.',
                'options'     => [
                    ['text' => 'A middleware with (err, req, res, next) registered last — handlers pass errors via next(err)', 'correct' => true],
                    ['text' => 'Wrap all routes in try/catch and call res.error() to trigger the error handler', 'correct' => false],
                    ['text' => 'Use app.on("error", handler) — Express emits an error event for all route errors', 'correct' => false],
                    ['text' => 'Add an uncaughtException listener on process — it catches all Express errors', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is CORS and how do you enable it in an Express API?',
                'explanation' => 'CORS (Cross-Origin Resource Sharing) is a browser security policy that blocks HTTP requests from a different origin (protocol, domain, or port) than the server. To enable CORS in Express, install the `cors` npm package and use it as middleware: `app.use(cors())`. For production, configure it explicitly: `app.use(cors({ origin: "https://yourdomain.com", methods: ["GET","POST"] }))` to avoid exposing the API to all origins.',
                'options'     => [
                    ['text' => 'A browser security policy blocking cross-origin requests — use the cors npm package to enable', 'correct' => true],
                    ['text' => 'A server-to-server authentication mechanism — enable with app.use(express.cors())', 'correct' => false],
                    ['text' => 'An Express built-in that prevents SQL injection across origins', 'correct' => false],
                    ['text' => 'A CDN routing protocol — no Express configuration is needed', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you validate request data in Express?',
                'explanation' => 'The most popular approach is `express-validator` (wraps validator.js). Define validation rules with `body("email").isEmail()`, `param("id").isInt()`, then use `validationResult(req)` to check for errors. Alternative: `joi` for schema-based validation. Always validate user input on the server — never trust the client. Validation should happen in middleware before the route handler.',
                'options'     => [
                    ['text' => 'Use express-validator or joi to validate req.body/params in middleware before the handler', 'correct' => true],
                    ['text' => 'Express validates request data automatically based on the route type definitions', 'correct' => false],
                    ['text' => 'Use JSON.parse(req.body) to validate and sanitize incoming JSON data', 'correct' => false],
                    ['text' => 'Rely on TypeScript type annotations — they validate at runtime in Express', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the `next()` function in Express middleware?',
                'explanation' => '`next()` passes control to the next middleware function in the stack. If called with no arguments, the next normal middleware runs. If called with an argument (`next(err)`), it skips to the first error-handling middleware. If a route handler does not call `next()` or send a response, the request hangs. Call `return next()` to prevent executing code after it.',
                'options'     => [
                    ['text' => 'Passes control to the next middleware; next(err) skips to error handling middleware', 'correct' => true],
                    ['text' => 'Sends the response and moves to the next request in the queue', 'correct' => false],
                    ['text' => 'Calls the next route that matches the current URL', 'correct' => false],
                    ['text' => 'A callback that fires after the response has been sent to the client', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you implement authentication middleware in Express?',
                'explanation' => 'Authentication middleware typically: (1) reads the token from `Authorization: Bearer <token>` header, (2) verifies the token (e.g., `jwt.verify(token, secret)`), (3) attaches the decoded user to `req.user`, (4) calls `next()` to proceed, or returns `401 Unauthorized` if invalid. Apply it selectively per route or router rather than globally.',
                'options'     => [
                    ['text' => 'Verify token from Authorization header, attach user to req.user, call next() or return 401', 'correct' => true],
                    ['text' => 'Use app.authenticate() — a built-in Express authentication method', 'correct' => false],
                    ['text' => 'Add a login check to every route handler function individually', 'correct' => false],
                    ['text' => 'Set app.set("auth", true) — Express automatically validates JWT tokens', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What HTTP status codes should a REST API return for common operations?',
                'explanation' => 'Standard REST status codes: 200 OK (successful GET/PUT), 201 Created (successful POST that creates a resource), 204 No Content (successful DELETE), 400 Bad Request (invalid input), 401 Unauthorized (missing/invalid auth), 403 Forbidden (authenticated but no permission), 404 Not Found (resource doesn\'t exist), 409 Conflict (e.g., duplicate email), 422 Unprocessable Entity (validation failed), 500 Internal Server Error.',
                'options'     => [
                    ['text' => '200 GET/PUT, 201 POST create, 204 DELETE, 400 bad input, 401 unauth, 404 not found, 500 error', 'correct' => true],
                    ['text' => '200 for all success cases; only 400 and 500 are used for errors in REST APIs', 'correct' => false],
                    ['text' => '201 for all successful operations; 404 when a middleware is not found', 'correct' => false],
                    ['text' => 'Status codes are optional — REST APIs should always return 200 with an error field', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you handle async errors in Express route handlers?',
                'explanation' => 'Express 4 does not automatically catch async errors. If a promise rejects inside a route handler without a catch, the error is unhandled. Solutions: (1) Wrap the handler in `try/catch` and call `next(err)`. (2) Create an `asyncHandler` wrapper: `const wrap = fn => (req,res,next) => fn(req,res,next).catch(next)`. (3) Express 5 (currently in beta) automatically catches promise rejections in route handlers.',
                'options'     => [
                    ['text' => 'Use try/catch + next(err), or an asyncHandler wrapper that calls .catch(next)', 'correct' => true],
                    ['text' => 'Express 4 catches all async errors — no special handling needed', 'correct' => false],
                    ['text' => 'Use async/await with app.async() — a special Express async route method', 'correct' => false],
                    ['text' => 'Add a process.on("unhandledRejection") listener and call res.status(500) inside it', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `app.get("/")` and `app.use("/")`?',
                'explanation' => '`app.get("/")` matches only GET requests to exactly "/". `app.use("/")` matches ALL HTTP methods and any path that starts with "/" (which is everything). Middleware registered with `app.use()` is also passed the `next` function, allowing it to defer to subsequent handlers. Route methods (`get`, `post`, `put`, `delete`) are intended for final handlers that always send a response.',
                'options'     => [
                    ['text' => 'app.get matches only GET on exact path; app.use matches all methods on any path starting with /', 'correct' => true],
                    ['text' => 'app.use is for middleware; app.get is only for serving HTML pages', 'correct' => false],
                    ['text' => 'They are identical — both register handlers for GET requests', 'correct' => false],
                    ['text' => 'app.get is synchronous; app.use is asynchronous and runs in the background', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you set and read HTTP headers in Express?',
                'explanation' => 'Read incoming headers: `req.headers["authorization"]` or `req.get("Authorization")`. Set response headers: `res.set("X-Custom-Header", "value")` or `res.header("...", "...")`. Set multiple at once: `res.set({ "Content-Type": "application/json", "X-Foo": "bar" })`. Set status and headers together: `res.status(201).set("Location", "/users/1").json(user)`.',
                'options'     => [
                    ['text' => 'Read with req.get("Header-Name"); set with res.set("Header-Name", "value")', 'correct' => true],
                    ['text' => 'Read with req.headers.get(); set with res.headers.set()', 'correct' => false],
                    ['text' => 'Use app.setHeader() to configure response headers for all routes globally', 'correct' => false],
                    ['text' => 'Headers are read-only in Express — use middleware to add custom headers', 'correct' => false],
                ],
            ],
            // ── New additions (23 more) ───────────────────────────────────────
            [
                'question'    => 'How do you handle file uploads in Express using `multer`?',
                'explanation' => '`multer` is multipart/form-data middleware for Express. Configure it: `const upload = multer({ dest: "uploads/" })`. Use as middleware: `router.post("/upload", upload.single("avatar"), (req, res) => { console.log(req.file) })`. `req.file` contains the uploaded file metadata (originalname, mimetype, path, size). `upload.array("photos", 5)` handles multiple files. Use `multer({ storage: multer.memoryStorage() })` to keep files in memory.',
                'options'     => [
                    ['text' => 'Install multer, configure storage, use upload.single/array as middleware — file is on req.file', 'correct' => true],
                    ['text' => 'Express handles file uploads natively via express.multipart() middleware', 'correct' => false],
                    ['text' => 'File uploads require writing a raw readable stream handler on req.on("data")', 'correct' => false],
                    ['text' => 'Use busboy directly — multer is deprecated in modern Express versions', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `cookie-parser` middleware do in Express?',
                'explanation' => '`cookie-parser` is middleware that parses the `Cookie` request header and populates `req.cookies` with an object of cookie name-value pairs. For signed cookies (tamper-proof), pass a secret: `app.use(cookieParser("mySecret"))` and access them via `req.signedCookies`. Without this middleware, `req.cookies` is `undefined`. To set cookies, use `res.cookie("name", "value", { httpOnly: true, secure: true })`.',
                'options'     => [
                    ['text' => 'Parses Cookie header into req.cookies; signed cookies go to req.signedCookies', 'correct' => true],
                    ['text' => 'Encrypts all cookies automatically using AES-256 before sending to the client', 'correct' => false],
                    ['text' => 'Cookie-parser is built into Express 5 and no longer needs to be installed separately', 'correct' => false],
                    ['text' => 'It sets a default session cookie for every incoming request', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you configure `express-session` in Express?',
                'explanation' => '`express-session` stores server-side session data. Basic setup: `app.use(session({ secret: "s3cr3t", resave: false, saveUninitialized: false, cookie: { httpOnly: true, secure: true } }))`. `resave: false` prevents re-saving an unmodified session. `saveUninitialized: false` does not store empty sessions (good for GDPR). Data is stored in `req.session`. The default store is in-memory — use `connect-redis` for production.',
                'options'     => [
                    ['text' => 'session({ secret, resave: false, saveUninitialized: false }) — use a persistent store in production', 'correct' => true],
                    ['text' => 'Sessions are configured via app.set("session", { ... }) — no separate middleware needed', 'correct' => false],
                    ['text' => 'express-session stores data in JWT tokens — no server-side storage required', 'correct' => false],
                    ['text' => 'Sessions are created automatically when you use passport.js — no manual config needed', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What do `resave` and `saveUninitialized` options do in `express-session`?',
                'explanation' => '`resave: false` means the session is not re-saved to the store on every request if it was not modified — reduces unnecessary writes. `saveUninitialized: false` means a new, empty session is not saved to the store — important for GDPR (do not store cookies without user consent) and for preventing session store bloat from unauthenticated visitors. Both should be `false` in most production applications.',
                'options'     => [
                    ['text' => 'resave: false avoids re-saving unmodified sessions; saveUninitialized: false skips saving empty sessions', 'correct' => true],
                    ['text' => 'resave: true resets the session expiry on every request; saveUninitialized creates a session for every visitor', 'correct' => false],
                    ['text' => 'These options control whether session data is encrypted before saving to the store', 'correct' => false],
                    ['text' => 'resave triggers a database save; saveUninitialized triggers a cookie to be set immediately', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `connect-flash` and how is it used in Express?',
                'explanation' => '`connect-flash` stores one-time messages in the session that survive a redirect. After a successful login redirect, you can display "Login successful!". Usage: `app.use(flash())`. Set a message: `req.flash("success", "Login successful")`. Read and clear: `req.flash("success")` returns the array and clears it. It requires a session middleware to be configured first.',
                'options'     => [
                    ['text' => 'Stores one-time session messages that survive redirects — req.flash("key", msg) to set, req.flash("key") to read', 'correct' => true],
                    ['text' => 'A real-time WebSocket notification system for flash updates in the browser', 'correct' => false],
                    ['text' => 'Middleware that flushes the response buffer immediately after each route handler', 'correct' => false],
                    ['text' => 'A CSS flash animation library integrated into Express template rendering', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What problem does `method-override` middleware solve in Express?',
                'explanation' => '`method-override` allows HTML forms to use HTTP methods other than GET and POST. Since HTML `<form>` tags only support GET and POST, PUT/DELETE requests from forms are impossible without JavaScript. The middleware reads a `_method` query parameter or a custom header (e.g., `X-HTTP-Method-Override`) and overrides `req.method`. Usage: `app.use(methodOverride("_method"))` — then `<form method="POST" action="/users/1?_method=DELETE">`.',
                'options'     => [
                    ['text' => 'Allows HTML forms to fake PUT/DELETE by reading a _method query param or custom header', 'correct' => true],
                    ['text' => 'Overrides the HTTP version used by the Express server from HTTP/1.1 to HTTP/2', 'correct' => false],
                    ['text' => 'Replaces the default Express routing method matching algorithm', 'correct' => false],
                    ['text' => 'Converts all incoming POST requests to GET for idempotent caching', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between `morgan` "combined" and "dev" log formats?',
                'explanation' => 'Morgan\'s `"combined"` format logs: remote IP, date, method, URL, HTTP version, status code, response size, referrer, and user agent — the Apache combined log format, ideal for production log aggregators. The `"dev"` format logs: method, URL, status (colored), response time, and response size — concise and colorized for development. Use `"combined"` in production for full audit trails; `"dev"` in development for readability.',
                'options'     => [
                    ['text' => '"combined" logs full Apache-format details for production; "dev" is concise and colorized for development', 'correct' => true],
                    ['text' => '"combined" logs all middleware calls; "dev" logs only route handler execution', 'correct' => false],
                    ['text' => '"dev" format includes stack traces; "combined" omits errors for cleaner logs', 'correct' => false],
                    ['text' => 'They are identical — "combined" just runs "dev" on multiple CPU cores', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you mount a sub-application in Express?',
                'explanation' => 'You can create a separate Express application (`const subApp = express()`) with its own routes and middleware, then mount it in the parent app: `app.use("/api", subApp)`. This is called sub-app mounting. The sub-app handles all requests under `/api/`. Each sub-app has its own settings and middleware stack. This is similar to `express.Router()` but more isolated — the sub-app can even listen on its own port.',
                'options'     => [
                    ['text' => 'Create a separate express() instance and mount it: app.use("/prefix", subApp)', 'correct' => true],
                    ['text' => 'Sub-apps are created with app.createSubApp() and automatically mounted at /sub', 'correct' => false],
                    ['text' => 'Use app.mount(subApp) — Express automatically assigns a prefix based on the sub-app name', 'correct' => false],
                    ['text' => 'Sub-apps must run as a separate process and communicate via IPC with the parent', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `Router().param()` do in Express?',
                'explanation' => '`router.param(name, callback)` adds pre-processing middleware that runs whenever a route with that named parameter is matched. The callback receives `(req, res, next, value)`. Example: `router.param("userId", async (req, res, next, id) => { req.targetUser = await User.findById(id); next() })` — then every route with `:userId` automatically has `req.targetUser` populated. It DRYs up repeated parameter loading logic.',
                'options'     => [
                    ['text' => 'Registers pre-processing middleware that runs when a named route parameter is matched', 'correct' => true],
                    ['text' => 'Defines the allowed values for a route parameter using a regex or array', 'correct' => false],
                    ['text' => 'Converts route parameter types automatically (string to integer, etc.)', 'correct' => false],
                    ['text' => 'Stores route parameter values in the session for cross-request access', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `res.format()` do in Express?',
                'explanation' => '`res.format(obj)` performs content negotiation based on the `Accept` header. It selects the response format that best matches what the client accepts. Example: `res.format({ "application/json": () => res.json(data), "text/html": () => res.send("<p>" + data.name + "</p>") })`. If no format matches, Express responds with 406 Not Acceptable. Useful for APIs that serve both browsers and programmatic clients.',
                'options'     => [
                    ['text' => 'Performs content negotiation — selects the response format based on the Accept request header', 'correct' => true],
                    ['text' => 'Formats the response body using a printf-style template string', 'correct' => false],
                    ['text' => 'Converts all responses to a standardized JSON:API format automatically', 'correct' => false],
                    ['text' => 'Sets the date/time format for timestamp fields in JSON responses', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `req.accepts()` do in Express?',
                'explanation' => '`req.accepts(types)` checks if the incoming request accepts a given content type by inspecting the `Accept` header. It returns the best matching type or `false` if none match. Example: `req.accepts(["json", "html"])` returns `"json"` if the client prefers JSON. Useful in middleware or route handlers for manual content negotiation, complementary to `res.format()` for more complex logic.',
                'options'     => [
                    ['text' => 'Checks the Accept header and returns the best matching content type from the provided list', 'correct' => true],
                    ['text' => 'Validates that the client has accepted the terms of service before proceeding', 'correct' => false],
                    ['text' => 'Returns true if the request has an Authorization header with a valid token', 'correct' => false],
                    ['text' => 'Accepts and stores incoming request parameters into req.acceptedParams', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How does conditional GET work with ETags in Express?',
                'explanation' => 'ETags are response headers that uniquely identify a version of a resource. Express automatically generates weak ETags for `res.send()` and `res.json()` responses (enabled by default). When a client re-requests the resource with `If-None-Match: "etag-value"`, Express compares it with the current ETag. If they match, it responds with 304 Not Modified (no body), saving bandwidth. Disable with `app.set("etag", false)` if needed.',
                'options'     => [
                    ['text' => 'Express auto-generates ETags; if client sends If-None-Match matching the ETag, it returns 304 Not Modified', 'correct' => true],
                    ['text' => 'ETags are encryption keys — the client must include them for every authenticated request', 'correct' => false],
                    ['text' => '304 responses are only sent for static files — JSON responses always return 200', 'correct' => false],
                    ['text' => 'Conditional GET requires manually setting ETag headers in every route handler', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you implement response caching with Cache-Control headers in Express?',
                'explanation' => 'Set `Cache-Control` headers to control caching: `res.set("Cache-Control", "public, max-age=3600")` tells CDNs and browsers to cache for 1 hour. `"private, no-cache"` allows browser caching but requires revalidation. `"no-store"` disables all caching (for sensitive data like financial records). Pair with `ETag` or `Last-Modified` headers for revalidation. The `apicache` or `express-cache-controller` packages simplify this.',
                'options'     => [
                    ['text' => 'Set Cache-Control header: public/max-age for CDNs, private/no-cache for user data, no-store for sensitive data', 'correct' => true],
                    ['text' => 'Express caches all GET responses by default — set app.set("cache", false) to disable', 'correct' => false],
                    ['text' => 'Use res.cache(3600) — a built-in Express method for setting cache duration in seconds', 'correct' => false],
                    ['text' => 'Response caching is only possible at the nginx/CDN layer — Express cannot set cache headers', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you send a streaming response in Express using `res.write()` and `res.end()`?',
                'explanation' => '`res.write(chunk)` sends a chunk of the response body without closing the connection. `res.end()` flushes and closes. This allows sending data progressively: `res.setHeader("Content-Type", "text/plain"); res.write("chunk1"); res.write("chunk2"); res.end()`. Useful for long-running operations, chunked file streaming, or streaming database query results. For object/CSV streaming, pipe a Node.js Readable stream directly to `res`.',
                'options'     => [
                    ['text' => 'res.write(chunk) sends partial data without closing; res.end() closes the connection — useful for streaming', 'correct' => true],
                    ['text' => 'res.write() buffers data and res.end() sends it all at once — same as res.send()', 'correct' => false],
                    ['text' => 'Streaming requires WebSockets — res.write() only works for WebSocket connections', 'correct' => false],
                    ['text' => 'res.write() is deprecated — use res.stream() from the express-streaming package instead', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you implement Server-Sent Events (SSE) in Express?',
                'explanation' => 'SSE sends server-to-client events over a long-lived HTTP connection. Setup: set `Content-Type: text/event-stream`, `Cache-Control: no-cache`, `Connection: keep-alive`, then call `res.flushHeaders()`. Send events with `res.write("data: " + JSON.stringify(payload) + "\\n\\n")`. Handle client disconnect with `req.on("close", () => cleanup())`. SSE is one-directional and simpler than WebSockets for push notifications.',
                'options'     => [
                    ['text' => 'Set Content-Type: text/event-stream, keep the connection open, send data: payload\\n\\n chunks', 'correct' => true],
                    ['text' => 'SSE is not possible in Express — use Socket.io or WebSockets for server push', 'correct' => false],
                    ['text' => 'Use res.sse(payload) — a built-in Express method for Server-Sent Events', 'correct' => false],
                    ['text' => 'SSE uses the PUT method — clients subscribe by sending PUT /events to the Express server', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you handle WebSocket upgrades in an Express application using the `ws` library?',
                'explanation' => 'Express handles HTTP; WebSockets are a protocol upgrade. The `ws` library attaches to Node.js `http.Server`: `const server = app.listen(3000); const wss = new WebSocket.Server({ server })`. When a WS handshake occurs, the `http` server passes the upgrade event to `wss`. For path-based routing, listen for the `upgrade` event on the server and check `req.url`. `express-ws` is a convenience wrapper that adds `app.ws("/path", handler)`.',
                'options'     => [
                    ['text' => 'Attach ws.Server to the http.Server created by app.listen() — or use express-ws for app.ws()', 'correct' => true],
                    ['text' => 'Express natively supports WebSockets via app.ws() without any additional packages', 'correct' => false],
                    ['text' => 'WebSocket connections bypass Express entirely — configure a separate ws port', 'correct' => false],
                    ['text' => 'Use app.upgrade() — an Express method specifically for WebSocket protocol upgrades', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `app.set("trust proxy", 1)` do in Express?',
                'explanation' => 'When Express runs behind a reverse proxy (nginx, AWS ALB, Cloudflare), the proxy forwards requests and the real client IP is in the `X-Forwarded-For` header. `app.set("trust proxy", 1)` tells Express to trust the first hop in `X-Forwarded-For` as the client IP. This affects `req.ip`, `req.ips`, `req.protocol` (for HTTPS detection), and `req.secure`. Without it, `req.ip` shows the proxy\'s IP — breaking rate limiting and geolocation.',
                'options'     => [
                    ['text' => 'Tells Express to trust X-Forwarded-For from the proxy — fixes req.ip, req.protocol, req.secure behind a proxy', 'correct' => true],
                    ['text' => 'Enables HTTPS proxy mode — all requests are automatically forwarded over TLS', 'correct' => false],
                    ['text' => 'Allows the app to trust cookies from the proxy server for session management', 'correct' => false],
                    ['text' => 'Sets the Express reverse proxy to distribute requests to 1 worker process', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How does the `express-validator` chain API work?',
                'explanation' => 'The chain API is declarative: `body("email").trim().isEmail().withMessage("Invalid email").normalizeEmail()`. Chains start with a location (`body`, `param`, `query`, `header`) and build validation rules. After defining chains, run `validationResult(req)` in the route handler or a middleware. If `.isEmpty()` is false, map `.array()` to return errors to the client. Chains can include custom validators: `.custom(async (val) => { if (await User.exists(val)) throw new Error("Taken") })`.',
                'options'     => [
                    ['text' => 'Chains start with body/param/query, add validators (.isEmail()), run validationResult(req) to check errors', 'correct' => true],
                    ['text' => 'express-validator uses a JSON schema object — no method chaining is involved', 'correct' => false],
                    ['text' => 'Validation chains auto-reject the request — you do not need to call validationResult()', 'correct' => false],
                    ['text' => 'The chain API is only available in express-validator v7+ — earlier versions use a callback style', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you validate request data using Joi schema validation in Express middleware?',
                'explanation' => 'Joi provides schema-based validation: define a schema with `Joi.object({ name: Joi.string().required(), age: Joi.number().min(18) })`. In middleware: `const { error, value } = schema.validate(req.body, { abortEarly: false })`. If `error` exists, return 422 with the error details. If valid, optionally replace `req.body` with the sanitized `value`. Create reusable `validate(schema)` middleware factory for clean route definitions.',
                'options'     => [
                    ['text' => 'Define a Joi.object() schema, call schema.validate(req.body) in middleware, return 422 if error exists', 'correct' => true],
                    ['text' => 'Joi validates responses only — use express-validator for validating incoming request data', 'correct' => false],
                    ['text' => 'Joi is a database schema library — it validates data before saving to MongoDB or SQL', 'correct' => false],
                    ['text' => 'Use Joi.assert(req.body, schema) — it throws automatically and Express catches the error', 'correct' => false],
                ],
            ],
        ];
    }

    private function advancedQuestions(): array
    {
        return [
            // ── Original 10 ──────────────────────────────────────────────────
            [
                'question'    => 'How do you structure a large Express application for maintainability?',
                'explanation' => 'Best practice layered architecture: `routes/` (define endpoints, delegate to controllers), `controllers/` (handle req/res, call services), `services/` (business logic, no HTTP concerns), `models/` or `repositories/` (data access). Share no HTTP objects below the controller layer — services/repos receive plain data and return plain data. This makes services testable without HTTP context and keeps concerns separated.',
                'options'     => [
                    ['text' => 'Layer into routes → controllers → services → models; keep HTTP objects out of services', 'correct' => true],
                    ['text' => 'Put all logic in one large app.js file using comments to separate sections', 'correct' => false],
                    ['text' => 'Create one router file per HTTP method (getRoutes.js, postRoutes.js, etc.)', 'correct' => false],
                    ['text' => 'Use MVC strictly — Express enforces this pattern natively via app.mvc()', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `helmet` and why should every Express API use it?',
                'explanation' => '`helmet` is a middleware collection that sets security-related HTTP response headers. It configures: `Content-Security-Policy` (mitigates XSS), `X-Frame-Options` (prevents clickjacking), `X-Content-Type-Options: nosniff` (stops MIME sniffing), `Strict-Transport-Security` (enforces HTTPS), and removes the `X-Powered-By: Express` header (reduces information disclosure). One line: `app.use(helmet())` — no excuse not to use it.',
                'options'     => [
                    ['text' => 'Sets security HTTP headers (CSP, X-Frame-Options, HSTS, etc.) to protect against common attacks', 'correct' => true],
                    ['text' => 'Encrypts the request body end-to-end between client and Express server', 'correct' => false],
                    ['text' => 'A rate-limiting middleware that prevents brute-force attacks on login endpoints', 'correct' => false],
                    ['text' => 'A validation library that ensures all incoming data matches a defined schema', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you implement rate limiting in Express?',
                'explanation' => 'Use the `express-rate-limit` package: `const limiter = rateLimit({ windowMs: 15 * 60 * 1000, max: 100 })`. This allows a maximum of 100 requests per IP per 15-minute window. Apply globally (`app.use(limiter)`) or to specific routes (e.g., login endpoint with a stricter limit). For distributed environments (multiple servers), use a Redis store (`rate-limit-redis`) so limits are shared across instances.',
                'options'     => [
                    ['text' => 'Use express-rate-limit with windowMs and max; use Redis store for multi-server deployments', 'correct' => true],
                    ['text' => 'Set the X-Rate-Limit header — browsers automatically enforce it', 'correct' => false],
                    ['text' => 'Use app.throttle(100) — a built-in Express rate limiting method', 'correct' => false],
                    ['text' => 'Rate limiting must be done at the nginx/load balancer level — Express cannot do it', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between stateful sessions and JWT authentication in Express?',
                'explanation' => 'Stateful sessions store session data on the server (in memory or Redis); the client gets only a session ID cookie. The server must look up the session on every request. JWT (JSON Web Token) is stateless — all user data is in a signed token stored client-side (usually localStorage or httpOnly cookie). No server lookup needed. JWTs scale better horizontally; sessions are easier to revoke (delete from store vs wait for token expiry).',
                'options'     => [
                    ['text' => 'Sessions store data server-side (need lookup); JWTs are stateless tokens (no server lookup, harder to revoke)', 'correct' => true],
                    ['text' => 'Sessions are for REST APIs; JWTs are for traditional web apps with forms', 'correct' => false],
                    ['text' => 'They are equivalent — Express uses them interchangeably via passport.js', 'correct' => false],
                    ['text' => 'JWTs store data in encrypted cookies; sessions use plain text headers', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you test Express API endpoints?',
                'explanation' => 'Use `supertest` to make HTTP requests to your Express app without starting a real server: `const res = await request(app).post("/users").send({ name: "Alice" }).expect(201)`. Pair with Jest or Mocha as the test runner. For unit-testing services (no HTTP), use plain Jest with mocked dependencies. For integration tests, use a test database. Structure: unit tests for services, integration tests for routes.',
                'options'     => [
                    ['text' => 'supertest makes HTTP requests to the Express app in tests; Jest/Mocha as test runner', 'correct' => true],
                    ['text' => 'Express has a built-in .test() method that simulates requests in memory', 'correct' => false],
                    ['text' => 'Only end-to-end browser tests are valid for testing Express APIs', 'correct' => false],
                    ['text' => 'Use Postman — it generates automated tests from collections automatically', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is compression middleware in Express and when should you use it?',
                'explanation' => 'The `compression` npm package is middleware that applies gzip/deflate/br compression to HTTP responses. It reduces response size by 70-90% for text-based content (JSON, HTML, CSS). Usage: `app.use(compression())`. Do NOT compress small responses or already-compressed content (images, videos). In production, it is often better to offload compression to nginx or a CDN — but if Express is directly client-facing, use it.',
                'options'     => [
                    ['text' => 'Gzip/deflate response compression — reduces payload size for text content by 70-90%', 'correct' => true],
                    ['text' => 'Minifies JavaScript files before they are served as static assets', 'correct' => false],
                    ['text' => 'Compresses incoming request bodies to reduce parsing overhead', 'correct' => false],
                    ['text' => 'A database query optimizer that compresses SQL queries before execution', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is request logging in Express and how do you implement it?',
                'explanation' => '`morgan` is the standard HTTP request logger for Express. Usage: `app.use(morgan("combined"))`. The `combined` format logs IP, date, method, URL, status, response time, and user agent — useful for production. `dev` format is concise and colorized for development. For structured JSON logging, use `morgan` with a custom format and pipe to `winston` or `pino`. Log all requests, especially in production for debugging.',
                'options'     => [
                    ['text' => 'Use morgan middleware to log requests — combined format for production, dev for development', 'correct' => true],
                    ['text' => 'Add console.log(req.url) at the start of every route handler', 'correct' => false],
                    ['text' => 'Express logs all requests to stdout by default — no middleware needed', 'correct' => false],
                    ['text' => 'Use app.log({ format: "json" }) — a built-in Express logging configuration', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you prevent SQL injection and XSS in an Express application?',
                'explanation' => 'SQL injection: use a query builder or ORM (Knex, Sequelize, Prisma) with parameterized queries — never concatenate user input into SQL strings. XSS: never embed raw user input into HTML; use a template engine that auto-escapes (like EJS with `<%= %>`); set a strong Content-Security-Policy header via helmet. Also: `helmet.xssFilter()`, validate and sanitize all inputs with `express-validator`.',
                'options'     => [
                    ['text' => 'Use parameterized queries/ORM for SQL injection; helmet + input sanitization for XSS', 'correct' => true],
                    ['text' => 'Use HTTPS — it encrypts the request and prevents both SQL injection and XSS', 'correct' => false],
                    ['text' => 'Express automatically sanitizes req.body — no extra steps needed', 'correct' => false],
                    ['text' => 'Enable app.set("strict routing", true) to activate Express\'s built-in injection protection', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is `express-async-errors` and what problem does it solve?',
                'explanation' => '`express-async-errors` is a small package that monkey-patches Express 4 to automatically forward unhandled promise rejections from async route handlers to the error-handling middleware. Without it, you must manually wrap every async handler in `try/catch` and call `next(err)`. After `require("express-async-errors")` (at the top, before Express is used), async handlers that throw or reject are automatically caught.',
                'options'     => [
                    ['text' => 'Patches Express 4 to automatically catch async handler rejections — no manual try/catch needed', 'correct' => true],
                    ['text' => 'A polyfill that adds async/await support to older Node.js versions', 'correct' => false],
                    ['text' => 'Middleware that wraps errors in a standardized JSON response format', 'correct' => false],
                    ['text' => 'An Express plugin that logs async errors to an external monitoring service', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you implement pagination in an Express REST API?',
                'explanation' => 'Common approach: accept `page` and `limit` query parameters (`/users?page=2&limit=20`). Calculate the database offset: `offset = (page - 1) * limit`. Return total count alongside results so clients can calculate total pages: `{ data: [...], total: 100, page: 2, limit: 20, pages: 5 }`. Validate that page/limit are positive integers with sane maximums (e.g., limit ≤ 100). Cursor-based pagination is more efficient for large datasets.',
                'options'     => [
                    ['text' => 'Accept ?page and ?limit query params, compute offset = (page-1)*limit, return data + total', 'correct' => true],
                    ['text' => 'Use the built-in Express Paginator class with router.paginate()', 'correct' => false],
                    ['text' => 'Return all records and paginate on the client-side for simplicity', 'correct' => false],
                    ['text' => 'Add a Link header with next/prev URLs — Express generates these automatically', 'correct' => false],
                ],
            ],
            // ── New additions (23 more) ───────────────────────────────────────
            [
                'question'    => 'What are the key differences in Express 5 compared to Express 4?',
                'explanation' => 'Express 5 (released 2024) key changes: (1) Async route handlers are automatically caught — rejected promises forward to error middleware without a wrapper. (2) Path-to-regexp v8 is used, which is stricter — some Express 4 patterns (like `/foo/:bar*`) must be rewritten. (3) `app.param()` callbacks changed signature. (4) `res.json()` with circular references throws instead of crashing. (5) `next("router")` exits a router to the parent. Always read the migration guide.',
                'options'     => [
                    ['text' => 'Express 5 auto-catches async rejections and uses stricter path-to-regexp v8 — some route patterns must change', 'correct' => true],
                    ['text' => 'Express 5 is a complete rewrite — no backward compatibility with Express 4 middleware', 'correct' => false],
                    ['text' => 'Express 5 adds native TypeScript support and removes the need for @types/express', 'correct' => false],
                    ['text' => 'Express 5 replaces the middleware pattern with a pipeline configuration object', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you generate OpenAPI/Swagger documentation for an Express API using `swagger-jsdoc`?',
                'explanation' => '`swagger-jsdoc` parses JSDoc comments in route files to generate an OpenAPI spec. Setup: define a `swaggerDefinition` with info/servers, set `apis: ["./routes/*.js"]`, call `swaggerJsdoc(options)` to get the spec object. Then use `swagger-ui-express` to serve it: `app.use("/api-docs", swaggerUi.serve, swaggerUi.setup(swaggerSpec))`. Annotate routes with `@swagger` YAML in JSDoc blocks — they become path definitions in the spec.',
                'options'     => [
                    ['text' => 'swagger-jsdoc parses @swagger JSDoc comments into an OpenAPI spec; swagger-ui-express serves the UI', 'correct' => true],
                    ['text' => 'Express auto-generates Swagger docs — enable with app.set("swagger", true)', 'correct' => false],
                    ['text' => 'OpenAPI documentation must be written manually in a YAML file — no code-first tools exist for Express', 'correct' => false],
                    ['text' => 'swagger-jsdoc requires TypeScript decorators — it does not work with plain JavaScript', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are the main API versioning strategies for an Express application?',
                'explanation' => 'Three common strategies: (1) URL prefix — `/v1/users`, `/v2/users` (most visible, easy to route with separate routers). (2) Request header — `Accept: application/vnd.api+json;version=2` or `X-API-Version: 2` (cleaner URLs, harder to test in browser). (3) Query parameter — `/users?version=2` (simplest, but pollutes query strings). URL prefix is the most common and cacheable. Header-based is RESTful-purist. Maintain old versions until clients migrate.',
                'options'     => [
                    ['text' => 'URL prefix (/v1/), Accept header versioning, or query param (?version=) — URL prefix is most common', 'correct' => true],
                    ['text' => 'Express enforces a single versioning strategy — use the one defined in package.json', 'correct' => false],
                    ['text' => 'API versioning is handled automatically by npm semver — no Express config needed', 'correct' => false],
                    ['text' => 'Only header-based versioning is RESTful — URL versioning violates REST constraints', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you integrate GraphQL into an Express application?',
                'explanation' => 'Two main options: (1) `apollo-server-express` — `const { ApolloServer } = require("apollo-server-express"); const server = new ApolloServer({ typeDefs, resolvers }); await server.start(); server.applyMiddleware({ app, path: "/graphql" })`. (2) `express-graphql` — `app.use("/graphql", graphqlHTTP({ schema, graphiql: true }))`. Apollo is more feature-rich (subscriptions, plugins, federation). `express-graphql` is simpler and maintained by the GraphQL Foundation.',
                'options'     => [
                    ['text' => 'Use apollo-server-express (applyMiddleware) or express-graphql (graphqlHTTP) mounted at /graphql', 'correct' => true],
                    ['text' => 'Express has native GraphQL support — enable with app.set("graphql", schema)', 'correct' => false],
                    ['text' => 'GraphQL requires a separate server — it cannot share a port with an Express REST API', 'correct' => false],
                    ['text' => 'Use REST endpoints that accept GraphQL query strings — no special package needed', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you use tRPC with an Express adapter?',
                'explanation' => 'tRPC provides end-to-end type safety between server and client. With Express: define a tRPC router with procedures, then use `@trpc/server/adapters/express`: `app.use("/trpc", createExpressMiddleware({ router: appRouter, createContext }))`. The client uses the inferred type from `typeof appRouter` — no schemas or code generation needed. Best paired with a TypeScript monorepo. Not REST — procedures are called by name, not HTTP method.',
                'options'     => [
                    ['text' => 'Mount createExpressMiddleware from @trpc/server/adapters/express — provides end-to-end TypeScript type safety', 'correct' => true],
                    ['text' => 'tRPC replaces Express entirely — it has its own standalone server runtime', 'correct' => false],
                    ['text' => 'tRPC is a REST framework — each procedure maps to a standard CRUD HTTP endpoint', 'correct' => false],
                    ['text' => 'tRPC requires GraphQL schema definitions to generate its type-safe client stubs', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you stream file uploads to S3 using `multer-s3` in Express?',
                'explanation' => '`multer-s3` is a multer storage engine that streams uploads directly to S3 without writing to disk. Setup: `const upload = multer({ storage: multerS3({ s3: s3Client, bucket: "my-bucket", key: (req, file, cb) => cb(null, Date.now() + "-" + file.originalname) }) })`. The file is piped directly from the client to S3 — the Express process never holds the full file in memory. `req.file.location` contains the S3 URL after upload.',
                'options'     => [
                    ['text' => 'multer-s3 storage engine streams uploads directly to S3 — the file never touches the local disk', 'correct' => true],
                    ['text' => 'Upload to disk with multer first, then use the AWS SDK to move the file to S3 in a separate step', 'correct' => false],
                    ['text' => 'multer-s3 is deprecated — use the @aws-sdk/lib-storage Multipart Upload API directly in a route handler', 'correct' => false],
                    ['text' => 'S3 uploads require a pre-signed URL — Express cannot proxy the upload directly', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you implement a streaming file proxy in Express (piping an upstream response to the client)?',
                'explanation' => 'To proxy a file from another service without buffering: `const upstream = await axios.get(fileUrl, { responseType: "stream" }); upstream.data.pipe(res)`. Or with node-fetch: pipe the response body stream. Set the same `Content-Type` and `Content-Length` from the upstream response before piping. This is memory-efficient — the file bytes flow directly from the upstream to the client through the Express process. Useful for proxying CDN assets with auth checks.',
                'options'     => [
                    ['text' => 'Fetch the upstream with responseType: "stream" and pipe the response body directly to res', 'correct' => true],
                    ['text' => 'Download the full file to memory, then send it with res.send() — piping is not supported in Express', 'correct' => false],
                    ['text' => 'Use res.proxy(url) — a built-in Express method for transparent HTTP proxying', 'correct' => false],
                    ['text' => 'Redirect the client to the upstream URL with res.redirect() — no proxying needed', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you propagate request context across async calls in Express using `AsyncLocalStorage`?',
                'explanation' => 'Node.js `AsyncLocalStorage` (from `async_hooks`) maintains context across asynchronous boundaries. Create a store: `const als = new AsyncLocalStorage()`. In middleware: `als.run({ requestId: req.id, userId: req.user.id }, next)`. In any async function called during the request — even in services or repositories — access the context: `als.getStore().requestId`. This avoids threading `req` through every function call for logging, tracing, or caching.',
                'options'     => [
                    ['text' => 'AsyncLocalStorage.run() sets context in middleware; als.getStore() retrieves it in any async call', 'correct' => true],
                    ['text' => 'Pass req as a parameter to every function — AsyncLocalStorage is only for internal Node.js modules', 'correct' => false],
                    ['text' => 'Use global variables — they maintain request scope in Express automatically', 'correct' => false],
                    ['text' => 'Request context propagation requires Redis — in-process storage does not work across async calls', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you implement distributed tracing in an Express application using OpenTelemetry?',
                'explanation' => 'OpenTelemetry provides vendor-neutral tracing. Setup: `@opentelemetry/sdk-node` with auto-instrumentation. Initialize before importing Express: `const sdk = new NodeSDK({ traceExporter: new OTLPTraceExporter() }); sdk.start()`. The `@opentelemetry/instrumentation-express` package automatically creates spans for each route and middleware. Add manual spans for business logic: `const span = tracer.startSpan("db.query"); span.end()`. Export to Jaeger, Zipkin, or Datadog.',
                'options'     => [
                    ['text' => 'Initialize @opentelemetry/sdk-node before Express; auto-instrumentation creates spans per route automatically', 'correct' => true],
                    ['text' => 'OpenTelemetry is not compatible with Express — use Zipkin directly with a custom middleware', 'correct' => false],
                    ['text' => 'Distributed tracing requires a sidecar process — Express cannot generate trace spans itself', 'correct' => false],
                    ['text' => 'Use morgan\'s "combined" format — it automatically generates OpenTelemetry-compatible trace IDs', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you add structured JSON logging to an Express application using `pino-http`?',
                'explanation' => '`pino` is a fast JSON logger; `pino-http` is its Express/Node.js HTTP middleware. Setup: `const pino = require("pino-http")(); app.use(pino)`. Every request automatically logs: method, URL, status code, response time, and request ID as structured JSON. Inside route handlers: `req.log.info({ userId: req.user.id }, "User fetched")` — the log is correlated to the request. JSON logs are ingested easily by Datadog, Elastic, or CloudWatch.',
                'options'     => [
                    ['text' => 'app.use(pinoHttp()) logs every request as structured JSON; use req.log.info() for correlated logs', 'correct' => true],
                    ['text' => 'morgan already outputs JSON — configure it with morgan("json") for structured logging', 'correct' => false],
                    ['text' => 'Structured logging requires writing a custom logger class — no existing npm packages support Express', 'correct' => false],
                    ['text' => 'Use console.log(JSON.stringify(req)) — Express automatically formats it for log aggregators', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you expose a Prometheus metrics endpoint in an Express application?',
                'explanation' => '`prom-client` is the standard Prometheus client for Node.js. Setup: `const client = require("prom-client"); client.collectDefaultMetrics()`. Add a metrics route: `app.get("/metrics", async (req, res) => { res.set("Content-Type", client.register.contentType); res.end(await client.register.metrics()) })`. Custom metrics: `const httpDuration = new client.Histogram({ name: "http_duration_seconds", labelNames: ["method", "route", "status"] })`. Prometheus scrapes `/metrics` on a schedule.',
                'options'     => [
                    ['text' => 'prom-client.collectDefaultMetrics() + expose GET /metrics endpoint returning client.register.metrics()', 'correct' => true],
                    ['text' => 'Express has a built-in /metrics endpoint — enable with app.set("metrics", true)', 'correct' => false],
                    ['text' => 'Prometheus metrics require a Node.js agent — Express cannot expose them directly', 'correct' => false],
                    ['text' => 'Use morgan to log metrics — Prometheus parses morgan logs automatically', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What are the recommended health check endpoint patterns for an Express production API?',
                'explanation' => 'Three standard endpoints: (1) `/health` or `/healthz` — liveness check; returns 200 if the process is alive (no heavy checks). (2) `/ready` or `/readyz` — readiness check; confirms the app is ready to serve traffic (DB connected, cache warm). (3) `/live` — same as liveness, used in Kubernetes. Response format: `{ status: "ok", uptime: process.uptime(), timestamp: Date.now() }`. Kubernetes uses liveness/readiness probes to restart or route traffic.',
                'options'     => [
                    ['text' => '/health for liveness (process alive), /ready for readiness (DB connected), used by Kubernetes probes', 'correct' => true],
                    ['text' => 'A single /status endpoint returning 200 is sufficient for all health check needs', 'correct' => false],
                    ['text' => 'Health checks are done at the load balancer level — Express should not expose health endpoints', 'correct' => false],
                    ['text' => 'Return the full system metrics JSON on /health — monitoring systems need all data in one call', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the circuit breaker pattern and how do you implement it in Express with `opossum`?',
                'explanation' => 'A circuit breaker wraps calls to external services. When failures exceed a threshold, the "circuit opens" — subsequent calls fail immediately without hitting the service. After a timeout, the circuit "half-opens" and tries one request; if it succeeds, the circuit closes. With `opossum`: `const breaker = new CircuitBreaker(fetchFromExternalService, { timeout: 3000, errorThresholdPercentage: 50, resetTimeout: 30000 })`. Use `breaker.fire(args)` instead of calling the service directly.',
                'options'     => [
                    ['text' => 'opossum wraps external calls — after threshold failures it fails fast; half-opens after resetTimeout', 'correct' => true],
                    ['text' => 'Circuit breakers are implemented using try/catch — opossum is only for electrical simulations', 'correct' => false],
                    ['text' => 'The circuit breaker pattern only applies to database connections, not HTTP service calls', 'correct' => false],
                    ['text' => 'Express has a built-in circuit breaker — configure it with app.set("circuitBreaker", options)', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you implement dependency injection in an Express application using `awilix`?',
                'explanation' => '`awilix` is a DI container for Node.js. Register dependencies: `const container = awilix.createContainer(); container.register({ userRepository: awilix.asClass(UserRepository), userService: awilix.asClass(UserService) })`. Use the `awilix-express` package to auto-wire route controllers: `app.use(scopePerRequest(container))`. Each request gets its own scope — controllers receive resolved dependencies in their constructor. This makes testing easy: swap real implementations for mocks.',
                'options'     => [
                    ['text' => 'awilix.createContainer() registers services; awilix-express scopePerRequest injects into route controllers', 'correct' => true],
                    ['text' => 'Express has built-in dependency injection via app.inject() — no external package needed', 'correct' => false],
                    ['text' => 'Dependency injection in Node.js requires TypeScript decorators — it is not possible in plain JavaScript', 'correct' => false],
                    ['text' => 'awilix is a test mocking library — use it only in test files, not in production code', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you implement OpenID Connect authentication in Express using `passport-openidconnect`?',
                'explanation' => 'OpenID Connect is OAuth 2.0 + identity layer. With `passport-openidconnect`: configure the strategy with `issuer`, `authorizationURL`, `tokenURL`, `userInfoURL`, `clientID`, `clientSecret`, `callbackURL`, and a `verify` callback. Register: `passport.use(new OpenIDConnectStrategy(config, verify))`. Define routes: `app.get("/auth/login", passport.authenticate("openidconnect"))` and `app.get("/auth/callback", passport.authenticate("openidconnect", { successRedirect: "/" }))`. The provider handles login UI.',
                'options'     => [
                    ['text' => 'Configure OpenIDConnectStrategy with issuer/clientID/callbackURL; passport handles the redirect/callback flow', 'correct' => true],
                    ['text' => 'OpenID Connect requires manual token validation — passport-openidconnect is only for OAuth 2.0', 'correct' => false],
                    ['text' => 'Use express-openid-connect only — it does not integrate with passport.js', 'correct' => false],
                    ['text' => 'OpenID Connect is identical to basic OAuth 2.0 — no additional strategy or library is needed', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is refresh token rotation and how do you implement it in Express?',
                'explanation' => 'Refresh token rotation issues a new refresh token every time one is used. The old refresh token is immediately invalidated in the database. If an attacker steals and uses an old token, the legitimate user\'s next refresh will detect reuse (the token has been rotated away) and all tokens can be revoked. Implementation: on `POST /auth/refresh`, verify the refresh token exists in the DB, create new access + refresh tokens, delete the old refresh token, return both. Store refresh tokens in httpOnly cookies.',
                'options'     => [
                    ['text' => 'Issue a new refresh token on each use, invalidate the old one — reuse of rotated tokens triggers revocation', 'correct' => true],
                    ['text' => 'Refresh token rotation means extending the token expiry time on every request automatically', 'correct' => false],
                    ['text' => 'Rotation requires rotating the JWT secret key — existing tokens are invalidated when the key changes', 'correct' => false],
                    ['text' => 'Only one refresh token per user is ever issued — rotation means re-signing the same token data', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the PKCE flow and how do you implement it in an Express OAuth server?',
                'explanation' => 'PKCE (Proof Key for Code Exchange) prevents authorization code interception attacks, especially for public clients (SPAs, mobile). Flow: client generates a random `code_verifier`, hashes it as `code_challenge = base64url(SHA256(code_verifier))`, sends `code_challenge` with the auth request. The Express server stores the challenge. When the client exchanges the code, it sends `code_verifier` — the server hashes it and compares to the stored challenge. `oauth2orize` + a PKCE extension handles this server-side.',
                'options'     => [
                    ['text' => 'Client sends code_challenge (hashed verifier) with auth request; server verifies plain verifier on code exchange', 'correct' => true],
                    ['text' => 'PKCE is a client-side-only mechanism — the Express server does not need any changes to support it', 'correct' => false],
                    ['text' => 'PKCE replaces client_secret — it is only used for server-side confidential clients', 'correct' => false],
                    ['text' => 'PKCE stands for Private Key Certificate Exchange — it uses asymmetric keys, not hashed codes', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you verify webhook signatures in an Express endpoint?',
                'explanation' => 'Webhook providers (Stripe, GitHub, etc.) sign payloads with HMAC-SHA256. Verification: (1) get the raw request body — use `express.raw({ type: "application/json" })` before the route, (2) read the signature header (e.g., `Stripe-Signature`), (3) compute `HMAC-SHA256(rawBody, webhookSecret)`, (4) compare using `crypto.timingSafeEqual()` to prevent timing attacks. Never use the parsed `req.body` — JSON re-serialization changes byte order and breaks the signature.',
                'options'     => [
                    ['text' => 'Use express.raw() for the raw body, compute HMAC-SHA256, compare with timingSafeEqual() to prevent timing attacks', 'correct' => true],
                    ['text' => 'Parse req.body as JSON and call stripe.webhooks.constructEvent() — raw body is not needed', 'correct' => false],
                    ['text' => 'Webhook signatures are verified by the browser — the Express server can trust all incoming webhooks', 'correct' => false],
                    ['text' => 'Use a shared API key in the URL — HMAC signing is too complex for webhook verification', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you implement an idempotency key middleware in Express?',
                'explanation' => 'Idempotency ensures that repeating a request has the same effect as doing it once — critical for payment APIs. Middleware: (1) read the `Idempotency-Key` header, (2) look up the key in a Redis store, (3) if found, return the cached response immediately, (4) if not found, proceed with the request, cache the response (key + status + body) in Redis with a TTL (e.g., 24 hours), then return the response. This prevents double-charges from network retries.',
                'options'     => [
                    ['text' => 'Read Idempotency-Key header, check Redis — return cached response on duplicate, store new responses', 'correct' => true],
                    ['text' => 'Express handles idempotency automatically for all POST endpoints — no custom middleware needed', 'correct' => false],
                    ['text' => 'Idempotency is enforced by assigning sequential request IDs and rejecting out-of-order requests', 'correct' => false],
                    ['text' => 'Use database transactions — they make all operations idempotent without any additional middleware', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you design and implement bulk operation endpoints in Express?',
                'explanation' => 'Bulk endpoints accept arrays of operations in a single request to reduce HTTP overhead. Two patterns: (1) Batch create — `POST /users/bulk` with `[{name: "A"}, {name: "B"}]` in body, insert all in a single DB transaction. (2) Partial success — process each item independently and return mixed results: `[{ id: 1, status: "created" }, { id: null, status: "error", reason: "duplicate" }]`. Limit batch size (e.g., max 100 items) and validate each item individually. Use 207 Multi-Status for partial success responses.',
                'options'     => [
                    ['text' => 'Accept an array in the body, process in a single transaction, return per-item results with 207 for partial success', 'correct' => true],
                    ['text' => 'Express bulk endpoints use the PATCH method exclusively — POST is for single resource creation only', 'correct' => false],
                    ['text' => 'Bulk operations should be avoided — they do not follow REST principles and break API design', 'correct' => false],
                    ['text' => 'Use GraphQL mutations for bulk operations — REST bulk endpoints are not standardized', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you implement cursor-based pagination in an Express API?',
                'explanation' => 'Cursor-based pagination uses an opaque cursor (typically the last item\'s ID or timestamp) instead of offset. Query: `SELECT * FROM posts WHERE id > lastId ORDER BY id ASC LIMIT 20`. Return the cursor: `{ data: [...], nextCursor: btoa(JSON.stringify({ id: lastRow.id })) }`. The client passes `?cursor=<value>` on the next request. Advantages over offset: consistent results when items are inserted/deleted, and more efficient for large datasets (no OFFSET scan). Decode with `JSON.parse(atob(cursor))`.',
                'options'     => [
                    ['text' => 'Use last item\'s ID as cursor in WHERE id > lastId query; return nextCursor for client to pass on next request', 'correct' => true],
                    ['text' => 'Cursor pagination uses encrypted page tokens that contain the full query for the next page', 'correct' => false],
                    ['text' => 'Cursor pagination is only useful for MongoDB — SQL databases should always use offset pagination', 'correct' => false],
                    ['text' => 'Return a SQL ROWNUM in the response — clients pass it back as the cursor for the next page', 'correct' => false],
                ],
            ],
        ];
    }
}
