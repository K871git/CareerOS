<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostgreSqlLearningSeeder extends Seeder
{
    public function run(): void
    {
        $track = LearningTrack::firstOrCreate(
            ['slug' => 'databases'],
            [
                'title'       => 'Databases',
                'description' => 'SQL, NoSQL, database design, and query optimization.',
            ]
        );

        $subject = Subject::firstOrCreate(
            ['slug' => 'postgresql'],
            [
                'learning_track_id' => $track->id,
                'title'             => 'PostgreSQL',
                'description'       => 'Master PostgreSQL from core SQL and JSONB to MVCC, indexing strategies, replication, and production operations.',
                'display_order'     => 3,
            ]
        );

        // Assign levels to existing practice topics
        Topic::where('slug', 'postgresql-junior')->update(['level' => 1, 'subject_id' => $subject->id]);
        Topic::where('slug', 'postgresql-intermediate')->update(['level' => 2, 'subject_id' => $subject->id]);
        Topic::where('slug', 'postgresql-advanced')->update(['level' => 3, 'subject_id' => $subject->id]);

        $topic4 = Topic::firstOrCreate(
            ['slug' => 'postgresql-l4-performance'],
            [
                'subject_id'    => $subject->id,
                'title'         => 'PostgreSQL Performance & Internals',
                'description'   => 'MVCC, VACUUM, EXPLAIN ANALYZE, query planner, connection pooling, and configuration tuning.',
                'display_order' => 4,
                'level'         => 4,
            ]
        );
        Topic::where('slug', 'postgresql-l4-performance')->update(['level' => 4]);

        $topic5 = Topic::firstOrCreate(
            ['slug' => 'postgresql-l5-production'],
            [
                'subject_id'    => $subject->id,
                'title'         => 'PostgreSQL Production & High Availability',
                'description'   => 'Streaming/logical replication, WAL, backup, PITR, FTS, RLS, zero-downtime migrations, and extensions.',
                'display_order' => 5,
                'level'         => 5,
            ]
        );
        Topic::where('slug', 'postgresql-l5-production')->update(['level' => 5]);

        $this->seedLessons($subject);
        $this->seedLevel4Questions($topic4);
        $this->seedLevel5Questions($topic5);

        $this->command->info('PostgreSQL Learning seeder complete — 5 levels, 15 lessons, 20 new MCQs.');
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
                ['title' => 'PostgreSQL Architecture: Processes, Memory & Storage',   'content' => $this->l1_1(), 'estimated_minutes' => 20, 'display_order' => 1],
                ['title' => 'Core SQL: DDL, DML, Constraints & PostgreSQL Data Types','content' => $this->l1_2(), 'estimated_minutes' => 22, 'display_order' => 2],
                ['title' => 'Querying Data: SELECT, JOIN, Aggregation & Pagination',  'content' => $this->l1_3(), 'estimated_minutes' => 20, 'display_order' => 3],
            ],
            2 => [
                ['title' => 'Transactions, ACID & Isolation Levels',                  'content' => $this->l2_1(), 'estimated_minutes' => 22, 'display_order' => 1],
                ['title' => 'Views, CTEs, Subqueries & Recursive Queries',            'content' => $this->l2_2(), 'estimated_minutes' => 22, 'display_order' => 2],
                ['title' => 'PostgreSQL-Specific Features: JSONB, Arrays & UPSERT',  'content' => $this->l2_3(), 'estimated_minutes' => 22, 'display_order' => 3],
            ],
            3 => [
                ['title' => 'Indexing Deep Dive: B-tree, GIN, GiST, BRIN & Partial', 'content' => $this->l3_1(), 'estimated_minutes' => 28, 'display_order' => 1],
                ['title' => 'Window Functions, Materialized Views & LATERAL Joins',   'content' => $this->l3_2(), 'estimated_minutes' => 28, 'display_order' => 2],
                ['title' => 'Schema Design: Normalization, Partitioning & Inheritance','content' => $this->l3_3(), 'estimated_minutes' => 28, 'display_order' => 3],
            ],
            4 => [
                ['title' => 'MVCC, VACUUM & Table Bloat',                             'content' => $this->l4_1(), 'estimated_minutes' => 30, 'display_order' => 1],
                ['title' => 'Query Optimization: EXPLAIN ANALYZE & the Planner',     'content' => $this->l4_2(), 'estimated_minutes' => 30, 'display_order' => 2],
                ['title' => 'Connection Pooling, Memory Config & Diagnostics',        'content' => $this->l4_3(), 'estimated_minutes' => 28, 'display_order' => 3],
            ],
            5 => [
                ['title' => 'Replication: WAL, Streaming & Logical Pub/Sub',          'content' => $this->l5_1(), 'estimated_minutes' => 32, 'display_order' => 1],
                ['title' => 'Full-Text Search, FDW & the Extensions Ecosystem',       'content' => $this->l5_2(), 'estimated_minutes' => 28, 'display_order' => 2],
                ['title' => 'Production Ops: Backup, PITR, RLS & Zero-Downtime Migrations', 'content' => $this->l5_3(), 'estimated_minutes' => 32, 'display_order' => 3],
            ],
        ];

        foreach ($lessons as $level => $levelLessons) {
            $topic = $topics->get($level);
            if (!$topic) continue;

            foreach ($levelLessons as $lessonData) {
                DB::table('lessons')->updateOrInsert(
                    ['topic_id' => $topic->id, 'title' => $lessonData['title']],
                    [
                        'content'           => $lessonData['content'],
                        'estimated_minutes' => $lessonData['estimated_minutes'],
                        'display_order'     => $lessonData['display_order'],
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]
                );
            }
        }
    }

    // ─── Level 1 ───────────────────────────────────────────────────────────────

    private function l1_1(): string
    {
        return <<<'MD'
## PostgreSQL Architecture: Processes, Memory & Storage

### What Is PostgreSQL?

PostgreSQL (pronounced *post-GRES-cue-ell*, often called Postgres) is a free, open-source, advanced relational database management system (RDBMS). It is ACID-compliant, highly extensible, and supports both relational (SQL) and document-style (JSONB) data.

Key facts:
- Born at UC Berkeley in 1986, open-source since 1996
- Runs on Linux, macOS, Windows
- The world's most advanced open-source RDBMS
- Powers companies like Apple, Reddit, Instagram, and Twitch

---

### Process Architecture

PostgreSQL uses a **multi-process** model (not multi-threaded like MySQL).

```
Client App
    ↓
postmaster (listener)
    ↓ forks a new process per connection
backend process (one per connection)
    ↓
shared memory (shared_buffers, WAL buffer)
    ↓
data files on disk
```

**Key server processes:**

| Process | Role |
|---|---|
| `postmaster` | Master daemon; accepts connections, forks backends |
| `backend` | One per client connection; executes queries |
| `autovacuum` | Reclaims dead row space automatically |
| `WAL writer` | Flushes WAL (Write-Ahead Log) buffers to disk |
| `checkpointer` | Periodically writes dirty pages from shared_buffers to disk |
| `bgwriter` | Proactively writes dirty pages to reduce checkpoint pressure |

**Implication for engineers:** Every connection = a new OS process (~5MB RAM). With 1,000 connections you consume ~5GB just for process overhead. This is why **PgBouncer** (connection pooler) is essential in production.

---

### Memory Architecture

```
PostgreSQL Memory
├── Shared Memory (global — all backends share it)
│   ├── shared_buffers   ← data page cache (default 128 MB, tune to 25% RAM)
│   ├── WAL buffers      ← WAL data before flush
│   └── Lock space       ← row/table lock tables
│
└── Per-Backend Memory (each connection's private workspace)
    ├── work_mem         ← per sort/hash operation (default 4 MB)
    ├── temp_buffers     ← temp table cache (default 8 MB)
    └── maintenance_work_mem ← VACUUM, CREATE INDEX (default 64 MB)
```

**shared_buffers** is PostgreSQL's own buffer cache — the most impactful setting. When data is needed:
1. Check shared_buffers → cache hit (fast)
2. Not there → check OS page cache → slower
3. Not there → read from disk → slowest

**work_mem** applies **per sort or hash node per query**. A complex query with 4 sort nodes uses up to `4 × work_mem`. Set it carefully to avoid OOM.

---

### Storage: Heap Files & Pages

PostgreSQL stores table data in **heap files** — unordered arrays of fixed-size 8 KB pages.

```
Table heap file
├── Page 0   [row1, row2, row3, ... free space ...]
├── Page 1   [row4, row5, ...]
└── ...
```

Each page holds rows (called **tuples**). Rows have headers storing:
- `xmin` — transaction ID that inserted this row
- `xmax` — transaction ID that deleted/updated this row (MVCC)
- Actual column data

**TOAST (The Oversized-Attribute Storage Technique):** PostgreSQL stores large values (> ~2 KB) out-of-line in a separate TOAST table, automatically compressing or chunking them. You rarely need to think about TOAST — it is fully transparent.

---

### Write-Ahead Log (WAL)

The WAL (also called the *redo log*) is PostgreSQL's durability mechanism.

```
Every change:
1. Write the change to WAL on disk   ← durable first
2. Apply the change to the heap      ← later (in shared_buffers)
```

On crash, PostgreSQL replays the WAL from the last checkpoint to restore consistency.

WAL is also the foundation for **streaming replication** — replicas receive and replay the WAL stream from the primary.

---

### Key Takeaways

- PostgreSQL is multi-process: one OS process per client connection
- **shared_buffers** is the primary memory cache; tune to 25% of RAM
- **work_mem** is per-sort-node — set conservatively for OLTP
- Data is stored in 8 KB heap pages; MVCC stores multiple row versions
- WAL provides crash safety and enables replication
MD;
    }

    private function l1_2(): string
    {
        return <<<'MD'
## Core SQL: DDL, DML, Constraints & PostgreSQL Data Types

### SQL Language Categories

| Category | Statements | Purpose |
|---|---|---|
| **DDL** | CREATE, ALTER, DROP, TRUNCATE | Define/modify structure |
| **DML** | SELECT, INSERT, UPDATE, DELETE | Read/write data |
| **DCL** | GRANT, REVOKE | Manage permissions |
| **TCL** | BEGIN, COMMIT, ROLLBACK, SAVEPOINT | Control transactions |

PostgreSQL is **fully transactional for DDL** — you can roll back a CREATE TABLE inside a transaction. This is rare among databases and invaluable for safe migrations.

---

### Creating & Managing Tables

```sql
-- Create a table
CREATE TABLE users (
    id          BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    email       TEXT        NOT NULL UNIQUE,
    username    VARCHAR(50) NOT NULL,
    role        TEXT        NOT NULL DEFAULT 'viewer',
    is_active   BOOLEAN     NOT NULL DEFAULT true,
    score       NUMERIC(5,2),
    created_at  TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Add a column
ALTER TABLE users ADD COLUMN avatar_url TEXT;

-- Rename a column
ALTER TABLE users RENAME COLUMN username TO display_name;

-- Change a column type
ALTER TABLE users ALTER COLUMN score TYPE NUMERIC(8,2);

-- Drop a column
ALTER TABLE users DROP COLUMN avatar_url;

-- Drop a table
DROP TABLE IF EXISTS users CASCADE;
```

---

### PostgreSQL Data Types — Choosing the Right One

**Integers:**

| Type | Storage | Range |
|---|---|---|
| SMALLINT | 2 bytes | ±32,767 |
| INTEGER (INT) | 4 bytes | ±2.1 billion |
| BIGINT | 8 bytes | ±9.2 quintillion |

Use BIGINT for primary keys in growing systems — you do not want to migrate when you hit 2 billion rows.

**Auto-Increment:**

```sql
-- Modern way (SQL standard, PostgreSQL 10+)
id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY

-- Legacy shorthand (still widely used)
id BIGSERIAL PRIMARY KEY
```

Prefer `GENERATED ALWAYS AS IDENTITY` — it prevents accidental manual inserts into the identity column.

**Text:**

```sql
TEXT        -- unlimited length, no performance difference from VARCHAR in PG
VARCHAR(n)  -- limits to n characters
CHAR(n)     -- fixed-length, pads with spaces — rarely useful
```

In PostgreSQL, TEXT and VARCHAR perform identically. Use TEXT unless you need the database to enforce a character limit.

**Exact Numbers:**

```sql
NUMERIC(precision, scale)   -- exact; use for money and scores
NUMERIC(10, 2)              -- up to 10 digits, 2 decimal places
```

Never use FLOAT for money — floating-point rounding causes bugs.

**Date & Time:**

```sql
DATE            -- date only: 2026-08-19
TIME            -- time without timezone
TIMESTAMP       -- date+time without timezone
TIMESTAMPTZ     -- date+time WITH timezone (recommended — always use this)
INTERVAL        -- a duration: '3 days 2 hours'
```

Always use `TIMESTAMPTZ` in production — it stores UTC and converts to the session timezone transparently.

**Other Important Types:**

```sql
BOOLEAN         -- true / false / NULL
UUID            -- 128-bit globally unique ID
JSONB           -- binary JSON with indexing support (see L2)
ARRAY           -- e.g., TEXT[], INTEGER[]
BYTEA           -- raw binary data
```

---

### Constraints

```sql
CREATE TABLE orders (
    id          BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    user_id     BIGINT      NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    status      TEXT        NOT NULL CHECK (status IN ('pending','paid','shipped','cancelled')),
    amount      NUMERIC(10,2) NOT NULL CHECK (amount > 0),
    coupon_code TEXT        UNIQUE,
    created_at  TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
```

| Constraint | Effect |
|---|---|
| `PRIMARY KEY` | NOT NULL + UNIQUE; the row identifier |
| `NOT NULL` | Column must have a value |
| `UNIQUE` | All values in the column must be distinct; NULLs are allowed and treated as distinct |
| `CHECK (expr)` | Row is only inserted/updated when expr is true |
| `REFERENCES` | Foreign key — enforces referential integrity |
| `DEFAULT value` | Used when INSERT omits the column |

**ON DELETE actions for foreign keys:**

| Action | Behaviour |
|---|---|
| `CASCADE` | Delete child rows when parent is deleted |
| `SET NULL` | Set FK column to NULL |
| `SET DEFAULT` | Set FK column to its default value |
| `RESTRICT` | Prevent parent deletion if children exist |
| `NO ACTION` | Same as RESTRICT but checked at end of statement |

---

### Inserting, Updating, Deleting

```sql
-- Insert one row; RETURNING gets back the generated id
INSERT INTO users (email, display_name)
VALUES ('alice@example.com', 'Alice')
RETURNING id, created_at;

-- Insert multiple rows
INSERT INTO users (email, display_name) VALUES
    ('bob@example.com',   'Bob'),
    ('carol@example.com', 'Carol');

-- Update with RETURNING
UPDATE users
SET role = 'admin'
WHERE id = 1
RETURNING id, role;

-- Delete
DELETE FROM users WHERE is_active = false;

-- TRUNCATE — removes all rows, faster than DELETE, transactional in PostgreSQL
TRUNCATE TABLE sessions RESTART IDENTITY;
```

---

### Key Takeaways

- Use `BIGINT GENERATED ALWAYS AS IDENTITY` for modern auto-increment PKs
- Use `TEXT` over `VARCHAR` unless you need a character limit enforced by the DB
- Use `TIMESTAMPTZ` (not `TIMESTAMP`) for all date-time columns
- Use `NUMERIC` (not `FLOAT`) for money and exact decimals
- `RETURNING` clause makes INSERT/UPDATE/DELETE return the affected rows — very useful
MD;
    }

    private function l1_3(): string
    {
        return <<<'MD'
## Querying Data: SELECT, JOIN, Aggregation & Pagination

### The SELECT Anatomy

SQL logical processing order differs from the written order:

```
Written order:        Logical execution order:
SELECT                1. FROM / JOIN
FROM                  2. WHERE
WHERE                 3. GROUP BY
GROUP BY              4. HAVING
HAVING                5. SELECT (window functions here)
ORDER BY              6. DISTINCT
LIMIT / OFFSET        7. ORDER BY
                      8. LIMIT / OFFSET
```

Understanding this prevents mistakes like using an alias defined in SELECT inside WHERE (it doesn't exist yet at that stage).

---

### Filtering with WHERE

```sql
-- Comparison
SELECT * FROM products WHERE price > 100;

-- Range (inclusive)
SELECT * FROM products WHERE price BETWEEN 50 AND 200;

-- Pattern matching (case-sensitive)
SELECT * FROM users WHERE email LIKE '%@gmail.com';

-- Case-insensitive (PostgreSQL extension)
SELECT * FROM users WHERE email ILIKE '%@gmail.com';

-- List membership
SELECT * FROM orders WHERE status IN ('pending', 'processing');

-- NULL check (never use = NULL)
SELECT * FROM users WHERE phone IS NULL;
SELECT * FROM users WHERE avatar_url IS NOT NULL;

-- Logical operators
SELECT * FROM products WHERE category = 'laptop' AND price < 1500;
SELECT * FROM users WHERE role = 'admin' OR role = 'moderator';
```

---

### JOINs

```sql
-- INNER JOIN — only matching rows from both tables
SELECT o.id, u.email, o.amount
FROM orders o
INNER JOIN users u ON u.id = o.user_id;

-- LEFT JOIN — all orders including those without a matching coupon
SELECT o.id, c.code
FROM orders o
LEFT JOIN coupons c ON c.id = o.coupon_id;

-- FULL OUTER JOIN — all rows from both sides, NULLs where no match
SELECT u.email, o.id AS order_id
FROM users u
FULL OUTER JOIN orders o ON o.user_id = u.id;

-- SELF JOIN — employees and their managers (same table)
SELECT e.name AS employee, m.name AS manager
FROM employees e
LEFT JOIN employees m ON m.id = e.manager_id;

-- CROSS JOIN — cartesian product (every combination)
SELECT s.size, c.color FROM sizes s CROSS JOIN colors c;
```

**PostgreSQL DISTINCT ON** — keep only the first row per group value (PostgreSQL-specific):

```sql
-- Latest order per user
SELECT DISTINCT ON (user_id) user_id, id AS order_id, created_at
FROM orders
ORDER BY user_id, created_at DESC;
```

---

### Sorting, Limiting & Pagination

```sql
-- Sort ascending (default)
SELECT * FROM products ORDER BY price;

-- Sort descending, NULLs last (default in DESC)
SELECT * FROM products ORDER BY price DESC NULLS LAST;

-- Pagination
SELECT * FROM products
ORDER BY id
LIMIT 20 OFFSET 40;   -- page 3 (0-indexed pages of 20)
```

**Keyset pagination** is more efficient than OFFSET for large datasets:

```sql
-- After the last seen id (cursor-based)
SELECT * FROM products
WHERE id > :last_seen_id
ORDER BY id
LIMIT 20;
```

OFFSET scans and discards rows — at page 1,000 it skips 20,000 rows. Keyset pagination uses an index seek instead.

---

### Aggregate Functions

```sql
SELECT
    COUNT(*)                    AS total_orders,
    COUNT(DISTINCT user_id)     AS unique_customers,
    SUM(amount)                 AS revenue,
    AVG(amount)                 AS avg_order,
    MIN(amount)                 AS min_order,
    MAX(amount)                 AS max_order
FROM orders
WHERE created_at >= '2026-01-01';
```

**GROUP BY and HAVING:**

```sql
-- Revenue per user, only those who spent more than 500
SELECT user_id, SUM(amount) AS total_spent
FROM orders
GROUP BY user_id
HAVING SUM(amount) > 500
ORDER BY total_spent DESC;
```

Rule: every column in SELECT that is not inside an aggregate must appear in GROUP BY.

---

### String & Date Functions

```sql
-- String
UPPER(email), LOWER(email)
LENGTH('hello')         -- 5
TRIM('  hello  ')
SUBSTRING(email, 1, 5)
email || '@domain.com'  -- concatenation with ||
CONCAT(first, ' ', last)  -- ignores NULLs

-- Date/time
NOW()                   -- current timestamp with timezone
CURRENT_DATE            -- today's date
AGE(created_at)         -- interval since created_at
DATE_TRUNC('month', created_at)  -- truncate to month start
EXTRACT(YEAR FROM created_at)    -- extract part
```

---

### Key Takeaways

- SQL executes in logical order: FROM → WHERE → GROUP BY → HAVING → SELECT → ORDER BY → LIMIT
- Use `ILIKE` for case-insensitive pattern matching (PostgreSQL extension)
- `DISTINCT ON (col)` is a powerful PostgreSQL feature for top-N-per-group queries
- For high-page-number pagination, keyset (cursor) pagination beats OFFSET
- Use `HAVING` to filter aggregated results; `WHERE` filters before aggregation
MD;
    }

    // ─── Level 2 ───────────────────────────────────────────────────────────────

    private function l2_1(): string
    {
        return <<<'MD'
## Transactions, ACID & Isolation Levels

### Why Transactions?

Without transactions, a bank transfer of $100 from Alice to Bob could fail halfway — deducting from Alice but never crediting Bob. A transaction bundles multiple operations into an **all-or-nothing** unit.

---

### ACID Properties

| Property | Meaning | PostgreSQL Mechanism |
|---|---|---|
| **Atomicity** | All operations succeed or none do | ROLLBACK undoes everything |
| **Consistency** | DB moves from one valid state to another | Constraints, triggers |
| **Isolation** | Concurrent transactions don't interfere | MVCC + locks |
| **Durability** | Committed data survives crashes | WAL (Write-Ahead Log) |

---

### Transaction Syntax

```sql
BEGIN;

UPDATE accounts SET balance = balance - 100 WHERE id = 1;
UPDATE accounts SET balance = balance + 100 WHERE id = 2;

COMMIT;    -- save both changes

-- or if something went wrong:
ROLLBACK;  -- undo both changes
```

PostgreSQL runs in **auto-commit mode** by default — each statement is its own transaction. Use `BEGIN` to start an explicit multi-statement transaction.

**SAVEPOINT — partial rollback:**

```sql
BEGIN;

INSERT INTO orders (...) VALUES (...);

SAVEPOINT before_payment;

INSERT INTO payments (...) VALUES (...);   -- this fails

ROLLBACK TO SAVEPOINT before_payment;     -- undo only the payment insert

-- Order still exists; continue
COMMIT;
```

---

### Concurrency Anomalies

| Anomaly | Description |
|---|---|
| **Dirty read** | Read uncommitted data from another transaction |
| **Non-repeatable read** | Re-read gives different result (row was updated) |
| **Phantom read** | Re-read gives different row count (rows inserted/deleted) |
| **Serialization anomaly** | Two transactions produce a result impossible in serial execution |

---

### Isolation Levels

```sql
-- Set isolation level for the current transaction
BEGIN ISOLATION LEVEL REPEATABLE READ;
```

| Level | Dirty Read | Non-Repeatable Read | Phantom Read |
|---|---|---|---|
| READ UNCOMMITTED | Possible (but PG treats as RC) | Possible | Possible |
| **READ COMMITTED** (default) | Not possible | Possible | Possible |
| REPEATABLE READ | Not possible | Not possible | Not possible in PG |
| SERIALIZABLE | Not possible | Not possible | Not possible |

PostgreSQL's **READ UNCOMMITTED is treated as READ COMMITTED** — you can never read uncommitted data in PostgreSQL.

PostgreSQL's **SERIALIZABLE** uses SSI (Serializable Snapshot Isolation) — a more sophisticated algorithm than traditional lock-based serializable that detects and aborts conflicting transactions.

**Choosing a level:**
- **READ COMMITTED** — default; fine for most OLTP
- **REPEATABLE READ** — reporting queries that must see a consistent snapshot
- **SERIALIZABLE** — financial critical logic where correctness matters more than throughput

---

### Row-Level Locking

```sql
-- Exclusive lock — block other transactions from updating selected rows
BEGIN;
SELECT * FROM orders WHERE id = 42 FOR UPDATE;
-- ... safe to update, no one else can touch row 42
UPDATE orders SET status = 'processing' WHERE id = 42;
COMMIT;

-- Shared lock — allows other shared locks, blocks exclusive
SELECT * FROM orders WHERE id = 42 FOR SHARE;

-- Skip locked rows (queue-style processing)
SELECT * FROM jobs WHERE status = 'pending' LIMIT 1 FOR UPDATE SKIP LOCKED;

-- Fail immediately if can't lock
SELECT * FROM orders WHERE id = 42 FOR UPDATE NOWAIT;
```

`SKIP LOCKED` is very useful for implementing job queues — workers each grab a different row without blocking each other.

---

### Deadlocks

A deadlock occurs when Transaction A holds lock X and waits for Y, while Transaction B holds Y and waits for X.

PostgreSQL **detects deadlocks automatically** every `deadlock_timeout` (default 1 second) and kills one transaction, returning an error. The killed transaction's caller must retry.

**Prevention:** always acquire locks in the same order across transactions.

---

### Key Takeaways

- ACID is enforced by WAL (durability), MVCC (isolation), and constraints (consistency)
- Default isolation level is READ COMMITTED — sufficient for most workloads
- `FOR UPDATE SKIP LOCKED` is the standard pattern for concurrent job queues
- PostgreSQL auto-detects and resolves deadlocks — callers must handle the error and retry
MD;
    }

    private function l2_2(): string
    {
        return <<<'MD'
## Views, CTEs, Subqueries & Recursive Queries

### Regular Views

A view is a **named saved SELECT query** — a virtual table that does not store data.

```sql
-- Create a view
CREATE VIEW active_users AS
SELECT id, email, role, created_at
FROM users
WHERE is_active = true;

-- Query it like a table
SELECT * FROM active_users WHERE role = 'admin';

-- Update (replaces the view definition)
CREATE OR REPLACE VIEW active_users AS ...;

-- Drop
DROP VIEW IF EXISTS active_users;
```

**Uses:** simplify complex queries, restrict column access (security), provide stable API over changing schemas.

**Limitation:** views execute the underlying SELECT every time — no caching.

---

### Materialized Views

A materialized view **stores the result on disk** — you trade freshness for speed.

```sql
-- Create materialized view
CREATE MATERIALIZED VIEW user_stats AS
SELECT
    user_id,
    COUNT(*)     AS total_orders,
    SUM(amount)  AS total_spent
FROM orders
GROUP BY user_id;

-- Refresh data (blocks reads during refresh)
REFRESH MATERIALIZED VIEW user_stats;

-- Refresh without locking (requires unique index on the view)
CREATE UNIQUE INDEX ON user_stats(user_id);
REFRESH MATERIALIZED VIEW CONCURRENTLY user_stats;
```

Use materialized views for expensive analytical queries where slightly stale data is acceptable.

---

### Subqueries

**Scalar subquery** — returns a single value:

```sql
SELECT name, salary,
       (SELECT AVG(salary) FROM employees) AS company_avg
FROM employees;
```

**IN subquery:**

```sql
SELECT * FROM orders
WHERE user_id IN (SELECT id FROM users WHERE role = 'premium');
```

**EXISTS subquery** — often faster than IN for large sets:

```sql
SELECT * FROM users u
WHERE EXISTS (
    SELECT 1 FROM orders o WHERE o.user_id = u.id AND o.status = 'paid'
);
```

**Correlated subquery** — references the outer query's row:

```sql
SELECT name,
       (SELECT COUNT(*) FROM orders o WHERE o.user_id = u.id) AS order_count
FROM users u;
```

**Derived table (subquery in FROM):**

```sql
SELECT dept, avg_salary
FROM (
    SELECT department AS dept, AVG(salary) AS avg_salary
    FROM employees
    GROUP BY department
) dept_summary
WHERE avg_salary > 80000;
```

---

### Common Table Expressions (CTEs)

CTEs (`WITH` clause) are named temporary result sets scoped to one query. They improve readability over nested subqueries.

```sql
WITH
high_value_users AS (
    SELECT user_id
    FROM orders
    GROUP BY user_id
    HAVING SUM(amount) > 1000
),
recent_orders AS (
    SELECT * FROM orders WHERE created_at >= NOW() - INTERVAL '30 days'
)
SELECT u.email, ro.id AS order_id, ro.amount
FROM recent_orders ro
JOIN high_value_users hvu ON hvu.user_id = ro.user_id
JOIN users u ON u.id = ro.user_id;
```

**Important PostgreSQL behavior:** CTEs are **materialized by default** — the query inside is executed once and the result is cached. This can improve performance (avoid repeated execution) or hurt it (optimizer can't push predicates into the CTE).

```sql
-- Force non-materialized (let optimizer inline it)
WITH ranked AS NOT MATERIALIZED (
    SELECT *, ROW_NUMBER() OVER (ORDER BY created_at DESC) AS rn FROM orders
)
SELECT * FROM ranked WHERE rn <= 10;
```

---

### Recursive CTEs

Recursive CTEs query **hierarchical / tree-structured data** (org charts, category trees, file systems).

```sql
-- Employee hierarchy
WITH RECURSIVE org AS (
    -- Anchor: start from the CEO (no manager)
    SELECT id, name, manager_id, 0 AS depth
    FROM employees
    WHERE manager_id IS NULL

    UNION ALL

    -- Recursive step: join each level's manager to their reports
    SELECT e.id, e.name, e.manager_id, org.depth + 1
    FROM employees e
    INNER JOIN org ON org.id = e.manager_id
)
SELECT id, name, depth FROM org ORDER BY depth, name;
```

```sql
-- Path finding: all ancestors of category 42
WITH RECURSIVE ancestors AS (
    SELECT id, parent_id, name FROM categories WHERE id = 42
    UNION ALL
    SELECT c.id, c.parent_id, c.name
    FROM categories c
    JOIN ancestors a ON a.parent_id = c.id
)
SELECT * FROM ancestors;
```

Always include a termination condition (the base case has no match) to prevent infinite loops. PostgreSQL will run recursion until no rows are returned by the recursive step.

---

### Key Takeaways

- Views are virtual — no storage, no caching; materialized views cache results on disk
- `REFRESH MATERIALIZED VIEW CONCURRENTLY` requires a unique index but avoids table locks
- CTEs are materialized by default in PostgreSQL — useful but can prevent optimizer push-down
- Recursive CTEs are the standard way to query hierarchical data (trees, graphs)
- `EXISTS` is usually faster than `IN` for large correlated subqueries
MD;
    }

    private function l2_3(): string
    {
        return <<<'MD'
## PostgreSQL-Specific Features: JSONB, Arrays & UPSERT

### JSONB — Binary JSON

PostgreSQL supports two JSON types:

| Type | Storage | Query Speed | Indexing |
|---|---|---|---|
| `JSON` | Raw text, insertion order preserved | Slower (re-parses every time) | Limited |
| `JSONB` | Binary decomposed format | Fast | Full GIN index support |

**Always use JSONB unless you need to preserve exact JSON formatting or insertion key order.**

```sql
-- Table with JSONB column
CREATE TABLE products (
    id      BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name    TEXT NOT NULL,
    attrs   JSONB
);

-- Insert JSON
INSERT INTO products (name, attrs) VALUES
('Laptop', '{"brand":"Dell","ram":16,"tags":["ultrabook","business"]}');

-- Extract a value as JSONB (object)
SELECT attrs -> 'brand' FROM products;          -- "Dell" (JSONB, with quotes)

-- Extract as text
SELECT attrs ->> 'brand' FROM products;         -- Dell (text, no quotes)

-- Nested path
SELECT attrs #>> '{specs,cpu}' FROM products;   -- text at nested key

-- Contains operator (great with GIN index)
SELECT * FROM products WHERE attrs @> '{"brand":"Dell"}';

-- Key exists
SELECT * FROM products WHERE attrs ? 'ram';

-- Any key from list exists
SELECT * FROM products WHERE attrs ?| ARRAY['brand', 'sku'];

-- Array element at index (1-based in path, 0-based in @> check)
SELECT attrs -> 'tags' -> 0 FROM products;      -- "ultrabook"
```

**Indexing JSONB:**

```sql
-- GIN index enables @>, ?, ?| operators
CREATE INDEX ON products USING GIN(attrs);

-- B-tree on a specific key for range queries
CREATE INDEX ON products ((attrs->>'brand'));
CREATE INDEX ON products ((attrs->>'price')::numeric);
```

---

### Updating JSONB

```sql
-- Replace a key
UPDATE products
SET attrs = attrs || '{"ram": 32}'
WHERE id = 1;

-- Remove a key
UPDATE products
SET attrs = attrs - 'legacy_field'
WHERE id = 1;

-- Set nested path
UPDATE products
SET attrs = jsonb_set(attrs, '{specs,cpu}', '"Intel i9"')
WHERE id = 1;
```

---

### ARRAY Type

PostgreSQL supports native arrays of any type.

```sql
CREATE TABLE posts (
    id       BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    title    TEXT NOT NULL,
    tags     TEXT[],
    scores   INTEGER[]
);

-- Insert
INSERT INTO posts (title, tags) VALUES
('PostgreSQL Guide', ARRAY['database', 'sql', 'postgres']);

-- Query: contains element
SELECT * FROM posts WHERE 'sql' = ANY(tags);

-- Contains all elements (use @>)
SELECT * FROM posts WHERE tags @> ARRAY['sql', 'database'];

-- Overlap (any element in common)
SELECT * FROM posts WHERE tags && ARRAY['sql', 'nosql'];

-- Array element by 1-based index
SELECT tags[1] FROM posts;    -- 'database'

-- Append element
UPDATE posts SET tags = tags || ARRAY['tutorial'] WHERE id = 1;

-- Array length
SELECT array_length(tags, 1) FROM posts;
```

**GIN index on arrays:**

```sql
CREATE INDEX ON posts USING GIN(tags);
```

This makes `@>`, `&&`, and `ANY` queries very fast.

---

### ENUM Types

```sql
-- Create the ENUM type
CREATE TYPE order_status AS ENUM ('pending', 'paid', 'shipped', 'cancelled');

-- Use it
CREATE TABLE orders (
    id     BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    status order_status NOT NULL DEFAULT 'pending'
);

-- Add a new value to existing ENUM (non-destructive)
ALTER TYPE order_status ADD VALUE 'refunded' AFTER 'paid';

-- List values
SELECT enum_range(NULL::order_status);
```

Unlike MySQL ENUMs (inline in column definition), PostgreSQL ENUMs are **catalog objects** — they have a name, can be shared across tables, and can be extended with `ALTER TYPE`.

---

### UPSERT — INSERT ... ON CONFLICT

UPSERT atomically inserts a row or updates it if a conflict occurs.

```sql
-- Do nothing on conflict
INSERT INTO user_settings (user_id, theme)
VALUES (42, 'dark')
ON CONFLICT (user_id) DO NOTHING;

-- Update on conflict (upsert)
INSERT INTO user_settings (user_id, theme, updated_at)
VALUES (42, 'light', NOW())
ON CONFLICT (user_id) DO UPDATE
    SET theme      = EXCLUDED.theme,
        updated_at = EXCLUDED.updated_at;

-- EXCLUDED refers to the row that would have been inserted
```

UPSERT is atomic — it does not suffer from the race condition of a manual SELECT-then-INSERT pattern.

---

### The RETURNING Clause

```sql
-- Get the generated ID after insert
INSERT INTO users (email) VALUES ('alice@example.com')
RETURNING id;

-- Get full updated row after update
UPDATE orders SET status = 'shipped' WHERE id = 99
RETURNING id, status, updated_at;

-- Get deleted rows
DELETE FROM sessions WHERE expires_at < NOW()
RETURNING id, user_id;
```

`RETURNING` eliminates the need for a follow-up SELECT, reducing round-trips.

---

### Key Takeaways

- JSONB is the right JSON type for almost every use case — binary, indexable, queryable with operators
- `@>` (contains) is the workhorse JSONB operator; pair it with a GIN index
- PostgreSQL ARRAYs are native; use GIN indexes for `@>` and `&&` queries
- ENUMs are catalog objects in PostgreSQL — they can be extended without breaking existing data
- `INSERT ... ON CONFLICT ... DO UPDATE` is the standard UPSERT pattern — prefer it over separate SELECT + INSERT
MD;
    }

    // ─── Level 3 ───────────────────────────────────────────────────────────────

    private function l3_1(): string
    {
        return <<<'MD'
## Indexing Deep Dive: B-tree, GIN, GiST, BRIN & Partial Indexes

### Why Indexes Matter

Without an index, PostgreSQL reads every row in a table (**sequential scan**). On a 10-million row table, that can take seconds. With the right index, the same query takes milliseconds.

**The trade-off:** indexes speed up reads but slow down writes (every INSERT/UPDATE/DELETE must also update each index). Never index every column — be deliberate.

---

### B-tree (Default Index)

B-trees are balanced tree structures. PostgreSQL uses them by default for almost all queries.

```sql
-- Create a B-tree index (default)
CREATE INDEX ON users(email);

-- Multi-column (composite) index
CREATE INDEX ON orders(user_id, created_at DESC);
```

B-tree supports: `=`, `<`, `>`, `<=`, `>=`, `BETWEEN`, `LIKE 'prefix%'` (prefix only), `IS NULL`, `ORDER BY`.

**Leftmost prefix rule:** a composite index on `(a, b, c)` is used for queries on `a` alone, `(a, b)`, or `(a, b, c)` — but NOT on `b` alone or `c` alone.

---

### Covering Index (INCLUDE)

A covering index stores extra columns in the index so queries can be answered without touching the heap (table).

```sql
CREATE INDEX ON orders(user_id) INCLUDE (status, amount);
```

PostgreSQL shows **Index Only Scan** in EXPLAIN when a query is fully served by the index. Dramatically faster because it avoids heap access (especially beneficial when the heap is on slow storage).

---

### Expression Indexes

Index the result of a function or expression:

```sql
-- Case-insensitive email lookup
CREATE INDEX ON users(LOWER(email));

-- Query must use the same expression
SELECT * FROM users WHERE LOWER(email) = 'alice@example.com';

-- Extract year for filtering
CREATE INDEX ON events(EXTRACT(YEAR FROM event_date));
```

---

### Partial Indexes

Index only a subset of rows using a WHERE clause:

```sql
-- Only index pending orders (much smaller than full-table index)
CREATE INDEX ON orders(user_id) WHERE status = 'pending';

-- Only non-deleted users
CREATE INDEX ON users(email) WHERE deleted_at IS NULL;
```

Partial indexes are smaller, faster to maintain, and more cache-friendly. Use them when queries always filter on the same condition.

---

### Hash Indexes

Hash indexes are slightly smaller than B-trees and only support equality (`=`). They do not support range queries or sorting.

```sql
CREATE INDEX ON sessions USING HASH(session_token);
```

In practice B-tree is usually preferred even for equality-only columns because it supports sorting and range scans too. Hash indexes are only worth it when the column is queried exclusively with `=` and index size matters.

---

### GIN — Generalized Inverted Index

GIN indexes are designed for columns containing **multiple values**: arrays, JSONB, and full-text search vectors.

```sql
-- Full-text search
CREATE INDEX ON articles USING GIN(to_tsvector('english', content));

-- JSONB
CREATE INDEX ON products USING GIN(attrs);

-- Array
CREATE INDEX ON posts USING GIN(tags);
```

GIN is fast for lookups (`@>`, `?`, `@@`) but slow to build and update. Use when reads dominate.

---

### GiST — Generalized Search Tree

GiST supports **geometric data**, IP ranges, and custom types:

```sql
-- Geometric: find all locations within a bounding box
CREATE INDEX ON locations USING GiST(coordinates);

-- IP range queries (with pg_trgm or btree_gist extensions)
CREATE INDEX ON ip_ranges USING GiST(network);
```

GiST also supports full-text search (alternative to GIN, slightly slower for FTS).

---

### BRIN — Block Range Index

BRIN stores the **min and max value** per block range (128 pages by default). Tiny index size. Works well when the physical order of data matches the query predicate — classic example: time-series data inserted in chronological order.

```sql
CREATE INDEX ON sensor_readings USING BRIN(recorded_at);
```

BRIN is not appropriate when the column has no correlation with physical storage order — it would produce too many false positives, forcing heap scans.

---

### CREATE INDEX CONCURRENTLY

Regular `CREATE INDEX` locks the table against writes. In production, always use `CONCURRENTLY`:

```sql
CREATE INDEX CONCURRENTLY ON orders(user_id);
```

`CONCURRENTLY` builds the index in the background while writes continue. It takes longer but avoids downtime. If it fails, a INVALID index is left behind — check with `\d orders` and drop it manually.

---

### Choosing the Right Index

| Scenario | Index Type |
|---|---|
| Equality or range on a scalar column | B-tree (default) |
| Equality only, size matters | Hash |
| JSONB fields, arrays, full-text | GIN |
| Geometric, IP ranges | GiST |
| Time-series ordered data | BRIN |
| Filtering on a partial condition | Partial |
| Query uses a function on a column | Expression |
| Avoid heap access entirely | Covering (INCLUDE) |

---

### Key Takeaways

- B-tree is the universal default — supports equality, range, sorting
- Composite indexes follow the leftmost prefix rule — column order matters
- Covering indexes (`INCLUDE`) enable Index Only Scans — the fastest path
- GIN is for JSONB, arrays, and full-text; BRIN is for large time-ordered tables
- Always use `CREATE INDEX CONCURRENTLY` in production to avoid write locks
MD;
    }

    private function l3_2(): string
    {
        return <<<'MD'
## Window Functions, Materialized Views & LATERAL Joins

### Window Functions Overview

Window functions compute values across a **window** of related rows without collapsing them — unlike GROUP BY which collapses groups into single rows.

```sql
function_name() OVER (
    [PARTITION BY col1, col2]  -- divide rows into groups
    [ORDER BY col3]            -- define order within each partition
    [frame_clause]             -- optional: limit which rows in the partition
)
```

---

### Ranking Functions

```sql
SELECT
    user_id,
    amount,
    ROW_NUMBER()   OVER (ORDER BY amount DESC)  AS row_num,
    RANK()         OVER (ORDER BY amount DESC)  AS rank,
    DENSE_RANK()   OVER (ORDER BY amount DESC)  AS dense_rank,
    NTILE(4)       OVER (ORDER BY amount DESC)  AS quartile
FROM orders;
```

| Function | Ties |
|---|---|
| `ROW_NUMBER()` | Unique — ties get consecutive numbers |
| `RANK()` | Ties get same rank, next rank skips (1,1,3) |
| `DENSE_RANK()` | Ties get same rank, no skip (1,1,2) |
| `NTILE(n)` | Divides rows into n equal buckets |

**Top-N per group (PARTITION BY):**

```sql
-- Rank orders per user by amount
SELECT user_id, id AS order_id, amount,
       RANK() OVER (PARTITION BY user_id ORDER BY amount DESC) AS user_rank
FROM orders;

-- Get only the top order per user
SELECT * FROM (
    SELECT *, RANK() OVER (PARTITION BY user_id ORDER BY amount DESC) AS rk
    FROM orders
) ranked
WHERE rk = 1;
```

---

### Aggregate Window Functions

Run aggregates without collapsing rows:

```sql
SELECT
    user_id,
    amount,
    SUM(amount)   OVER (PARTITION BY user_id ORDER BY created_at) AS running_total,
    AVG(amount)   OVER (PARTITION BY user_id)                     AS user_avg,
    COUNT(*)      OVER ()                                          AS total_rows
FROM orders;
```

---

### Offset Functions: LAG & LEAD

Access the previous or next row's value:

```sql
SELECT
    created_at,
    amount,
    LAG(amount, 1, 0)  OVER (ORDER BY created_at) AS prev_amount,
    LEAD(amount, 1, 0) OVER (ORDER BY created_at) AS next_amount,
    amount - LAG(amount) OVER (ORDER BY created_at) AS change
FROM daily_sales;
```

Common use: compare a metric with the previous period (day-over-day, month-over-month).

---

### FIRST_VALUE & LAST_VALUE

```sql
SELECT
    product_id,
    sale_date,
    revenue,
    FIRST_VALUE(revenue) OVER (PARTITION BY product_id ORDER BY sale_date) AS first_sale,
    LAST_VALUE(revenue)  OVER (
        PARTITION BY product_id
        ORDER BY sale_date
        ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING
    ) AS last_sale
FROM sales;
```

Note: `LAST_VALUE` requires an explicit frame clause that extends to the end — by default the frame ends at the current row.

---

### Frame Clauses

```sql
-- Rolling 7-day sum
SUM(amount) OVER (
    ORDER BY day
    ROWS BETWEEN 6 PRECEDING AND CURRENT ROW
)

-- Cumulative sum from the start
SUM(amount) OVER (ORDER BY day ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW)

-- RANGE vs ROWS: RANGE groups rows with equal ORDER BY values
SUM(amount) OVER (ORDER BY day RANGE BETWEEN INTERVAL '7 days' PRECEDING AND CURRENT ROW)
```

---

### Materialized Views (Deep Dive)

```sql
-- Create
CREATE MATERIALIZED VIEW monthly_revenue AS
SELECT
    DATE_TRUNC('month', created_at) AS month,
    SUM(amount)                     AS revenue
FROM orders
WHERE status = 'paid'
GROUP BY 1
ORDER BY 1;

-- Required for CONCURRENTLY refresh
CREATE UNIQUE INDEX ON monthly_revenue(month);

-- Refresh without locking readers
REFRESH MATERIALIZED VIEW CONCURRENTLY monthly_revenue;

-- Schedule refresh via pg_cron extension or a cron job
```

**When to use:** dashboard queries that are expensive (seconds) but data can be minutes/hours stale.

---

### LATERAL Joins

LATERAL allows a subquery in FROM to reference columns from tables listed before it:

```sql
-- Latest 3 orders per user (efficient)
SELECT u.email, lo.id, lo.amount
FROM users u
CROSS JOIN LATERAL (
    SELECT id, amount
    FROM orders
    WHERE user_id = u.id   -- references outer table
    ORDER BY created_at DESC
    LIMIT 3
) lo;
```

Without LATERAL, the subquery cannot reference `u.id`. LATERAL is essential for per-row subqueries, similar to a correlated subquery but in the FROM clause.

---

### Key Takeaways

- Window functions compute over related rows without collapsing — they pair with OVER() / PARTITION BY / ORDER BY
- `ROW_NUMBER`, `RANK`, `DENSE_RANK` differ only in how they handle ties
- `LAG`/`LEAD` are the go-to functions for period-over-period comparisons
- Frame clauses (`ROWS BETWEEN`) control the rolling window for aggregate window functions
- LATERAL joins allow FROM subqueries to reference outer table columns — essential for per-row top-N queries
MD;
    }

    private function l3_3(): string
    {
        return <<<'MD'
## Schema Design: Normalization, Partitioning & Inheritance

### Why Schema Design Matters

A well-designed schema prevents data anomalies, keeps queries fast, and makes the codebase maintainable. Poor schema design causes subtle bugs, redundant data, and painful migrations later.

---

### Normal Forms

**1NF — First Normal Form:**
- Every column holds atomic (indivisible) values
- No repeating groups or arrays in a cell
- All rows are uniquely identifiable

```sql
-- VIOLATES 1NF (tags as comma-separated string)
CREATE TABLE posts (id INT, tags TEXT);  -- 'php, laravel, api'

-- 1NF compliant (separate table)
CREATE TABLE post_tags (post_id INT, tag TEXT);
```

Note: PostgreSQL's ARRAY type technically violates 1NF. It is a deliberate trade-off — acceptable when you need fast array operations without joins.

**2NF — Second Normal Form:**
- Must be 1NF
- Every non-key column depends on the **entire** primary key (no partial dependency)
- Applies when the primary key is composite

```sql
-- VIOLATES 2NF: course_name depends on course_id alone, not the full (student_id, course_id) PK
CREATE TABLE enrollments (student_id INT, course_id INT, course_name TEXT, grade CHAR(1));

-- Fix: separate tables
CREATE TABLE courses (id INT PRIMARY KEY, name TEXT);
CREATE TABLE enrollments (student_id INT, course_id INT REFERENCES courses(id), grade CHAR(1));
```

**3NF — Third Normal Form:**
- Must be 2NF
- No transitive dependencies (non-key column depends on another non-key column)

```sql
-- VIOLATES 3NF: zip_code → city → state (city depends on zip, not on the PK)
CREATE TABLE addresses (id INT, zip_code TEXT, city TEXT, state TEXT);

-- Fix: extract zip reference
CREATE TABLE zip_codes (zip TEXT PRIMARY KEY, city TEXT, state TEXT);
CREATE TABLE addresses (id INT, zip_code TEXT REFERENCES zip_codes(zip));
```

**BCNF (Boyce-Codd Normal Form):** stricter than 3NF — every determinant must be a candidate key. Handles edge cases 3NF misses with overlapping composite keys.

---

### When to Denormalize

Normalization optimizes for data integrity and writes. Sometimes you denormalize for read performance:

- Pre-compute totals (`orders.total_amount` instead of summing items every query)
- Duplicate data to avoid expensive JOINs in hot read paths
- Use JSONB to store a set of attributes without separate rows

Always denormalize consciously — document the redundancy and maintain consistency with triggers or application logic.

---

### Declarative Partitioning (PostgreSQL 10+)

Partitioning splits a large table into smaller physical partitions while keeping a single logical table.

**Range Partitioning (time-series):**

```sql
CREATE TABLE orders (
    id         BIGINT,
    amount     NUMERIC(10,2),
    created_at TIMESTAMPTZ NOT NULL
) PARTITION BY RANGE (created_at);

-- Create partitions for each quarter
CREATE TABLE orders_2026_q1 PARTITION OF orders
    FOR VALUES FROM ('2026-01-01') TO ('2026-04-01');

CREATE TABLE orders_2026_q2 PARTITION OF orders
    FOR VALUES FROM ('2026-04-01') TO ('2026-07-01');
```

**List Partitioning:**

```sql
CREATE TABLE users (
    id      BIGINT,
    country TEXT NOT NULL
) PARTITION BY LIST (country);

CREATE TABLE users_us PARTITION OF users FOR VALUES IN ('US');
CREATE TABLE users_eu PARTITION OF users FOR VALUES IN ('DE', 'FR', 'GB');
CREATE TABLE users_other PARTITION OF users DEFAULT;
```

**Hash Partitioning:**

```sql
CREATE TABLE events (id BIGINT, user_id BIGINT)
PARTITION BY HASH (user_id);

CREATE TABLE events_0 PARTITION OF events FOR VALUES WITH (MODULUS 4, REMAINDER 0);
CREATE TABLE events_1 PARTITION OF events FOR VALUES WITH (MODULUS 4, REMAINDER 1);
-- ...
```

**Partition Pruning:** PostgreSQL automatically skips irrelevant partitions in WHERE clauses:

```sql
-- Only scans orders_2026_q1
SELECT * FROM orders WHERE created_at BETWEEN '2026-01-01' AND '2026-03-31';
```

Partitioning benefits: faster queries (pruning), efficient bulk deletes (`DROP TABLE orders_2026_q1`), better autovacuum (works on smaller partitions).

---

### Table Inheritance

PostgreSQL's unique table inheritance lets a child table inherit all columns from a parent:

```sql
CREATE TABLE vehicles (
    id      BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    make    TEXT NOT NULL,
    year    INT  NOT NULL
);

CREATE TABLE cars   () INHERITS (vehicles);
CREATE TABLE trucks () INHERITS (vehicles);

-- Adds brand-specific column
ALTER TABLE cars   ADD COLUMN doors INT;
ALTER TABLE trucks ADD COLUMN payload_kg NUMERIC;

-- Query parent: returns rows from all children
SELECT * FROM vehicles;

-- Query only one table
SELECT * FROM ONLY vehicles;
```

In practice, **declarative partitioning** has mostly superseded inheritance for partitioning needs. Inheritance is still useful for type hierarchy modeling.

---

### Common Schema Design Patterns

| Pattern | When to Use |
|---|---|
| Soft delete (`deleted_at TIMESTAMPTZ`) | Audit trail, recoverable deletion |
| Audit columns (`created_at`, `updated_at`) | Every table in production |
| UUID primary keys | Distributed systems, predictable IDs |
| BIGINT IDENTITY PKs | High-performance single-instance systems |
| Polymorphic association (`entity_type + entity_id`) | Comments/reactions on multiple entity types |
| EAV (Entity-Attribute-Value) with JSONB | Dynamic attributes without schema changes |

---

### Key Takeaways

- 3NF is the practical target for most OLTP schemas — eliminate partial and transitive dependencies
- Denormalize deliberately for read-heavy paths; document and maintain consistency
- Range partitioning is ideal for time-series data — enables instant partition drops for old data
- Partition pruning makes large partitioned tables nearly as fast as small tables for scoped queries
- GENERATED ALWAYS AS IDENTITY is the modern, standard way to define auto-increment primary keys
MD;
    }

    // ─── Level 4 ───────────────────────────────────────────────────────────────

    private function l4_1(): string
    {
        return <<<'MD'
## MVCC, VACUUM & Table Bloat

### Multi-Version Concurrency Control (MVCC)

PostgreSQL avoids reader-writer blocking by keeping **multiple versions of each row**.

Every row (tuple) has two hidden system columns:

| Column | Meaning |
|---|---|
| `xmin` | Transaction ID of the INSERT that created this version |
| `xmax` | Transaction ID of the DELETE/UPDATE that invalidated this version (0 = still live) |

When you `UPDATE` a row, PostgreSQL does **not modify it in place**. Instead:
1. The old row is marked dead by setting `xmax`
2. A new version is inserted with the new `xmin`

```
Before UPDATE: row1 [xmin=100, xmax=0, name='Alice']

After UPDATE (txid=200):
    row1 [xmin=100, xmax=200, name='Alice']   ← dead version
    row2 [xmin=200, xmax=0,   name='Ali']     ← live version
```

**Snapshot isolation:** each transaction gets a snapshot at its start, seeing only tuples committed before the snapshot was taken. A long-running transaction sees an older snapshot and cannot see newer committed rows — this is correct but can cause issues (more below).

---

### The Dead Tuple Problem

Every UPDATE and DELETE leaves behind dead tuples. PostgreSQL cannot immediately reclaim their space because:
- Other transactions might still need to see the old version (their snapshot predates the delete)
- Only after all concurrent transactions that could reference the old row have ended can it be cleaned up

Without cleanup, dead tuples accumulate → **table bloat**.

---

### VACUUM

`VACUUM` reclaims dead tuple space, making it available for future INSERTs on the same page.

```sql
-- Reclaim dead tuple space (non-blocking, concurrent with reads/writes)
VACUUM orders;

-- Verbose output
VACUUM VERBOSE orders;

-- Update planner statistics (normally done by ANALYZE)
VACUUM ANALYZE orders;
```

`VACUUM` does **not** return space to the OS — it marks pages as reusable within the table file.

**VACUUM FULL:**

```sql
-- Rewrites the entire table — returns space to OS
-- Requires an exclusive lock — no reads or writes during this time
VACUUM FULL orders;
```

`VACUUM FULL` is a table rewrite under an exclusive lock. In production, use `pg_repack` instead — it rewrites the table without locking.

---

### AUTOVACUUM

PostgreSQL runs `autovacuum` automatically in the background. It triggers when:
- Dead tuples exceed `autovacuum_vacuum_threshold + autovacuum_vacuum_scale_factor × total_rows`

Default: triggers when dead rows exceed 50 + 20% of total rows.

For tables with very frequent writes (high-churn OLTP), tune autovacuum aggressively:

```sql
-- Per-table autovacuum tuning (overrides global defaults)
ALTER TABLE orders SET (
    autovacuum_vacuum_scale_factor = 0.01,    -- trigger at 1% dead tuples (not 20%)
    autovacuum_vacuum_threshold   = 100       -- minimum 100 dead tuples
);
```

---

### XID Wraparound

PostgreSQL uses 32-bit transaction IDs (XID). After ~2.1 billion transactions, XIDs wrap around.

Without VACUUM **freezing** old rows (replacing old xmin with a special FrozenXID), those rows would appear as future transactions — invisible to queries. This would cause **data loss**.

PostgreSQL emits warnings at 10 million transactions before wraparound and forces a shutdown to prevent corruption. Emergency `VACUUM FREEZE` resolves it.

**Prevention:** ensure autovacuum is healthy and runs on all tables. Monitor `pg_stat_user_tables.n_dead_tup` and `age(datfrozenxid)`.

---

### Diagnosing Table Bloat

```sql
-- Dead tuples per table
SELECT relname, n_live_tup, n_dead_tup,
       ROUND(n_dead_tup::numeric / NULLIF(n_live_tup,0) * 100, 2) AS dead_pct,
       last_vacuum, last_autovacuum
FROM pg_stat_user_tables
ORDER BY n_dead_tup DESC;

-- Transaction age (XID wraparound risk)
SELECT datname, age(datfrozenxid) AS xid_age FROM pg_database ORDER BY xid_age DESC;
```

---

### Key Takeaways

- MVCC gives every transaction a consistent snapshot without blocking reads — at the cost of dead tuples
- Dead tuples from UPDATE/DELETE must be reclaimed by VACUUM or they cause table bloat
- AUTOVACUUM runs automatically but must be tuned for high-write tables
- VACUUM FULL returns space to the OS but requires an exclusive lock — use pg_repack in production
- XID wraparound is a critical risk — monitor transaction age and ensure VACUUM keeps up
MD;
    }

    private function l4_2(): string
    {
        return <<<'MD'
## Query Optimization: EXPLAIN ANALYZE & the Planner

### The Query Planner

When you submit a SQL query, PostgreSQL's **planner** generates a query plan — a tree of operations describing how to execute the query. The planner chooses:
- Which indexes to use (or whether to do a sequential scan)
- Which join algorithm to use
- In what order to join tables

The planner relies on **table statistics** (row counts, value distributions) maintained by `ANALYZE` / `AUTOVACUUM ANALYZE`.

---

### EXPLAIN vs EXPLAIN ANALYZE

```sql
-- Show planner's estimated plan (does NOT execute the query)
EXPLAIN SELECT * FROM orders WHERE user_id = 42;

-- Execute the query and show real timing + actual row counts
EXPLAIN ANALYZE SELECT * FROM orders WHERE user_id = 42;

-- Full diagnostic output
EXPLAIN (ANALYZE, BUFFERS, FORMAT TEXT)
SELECT * FROM orders WHERE user_id = 42;
```

`BUFFERS` shows cache hits (`shared hit`) vs disk reads (`shared read`). High disk reads indicate the data is not in `shared_buffers` or the OS page cache.

---

### Reading EXPLAIN Output

```
Hash Join  (cost=150.00..5200.00 rows=1000 width=64)
           (actual time=12.3..18.7 rows=982 loops=1)
  Hash Cond: (o.user_id = u.id)
  ->  Seq Scan on orders o  (cost=0..3500 rows=50000 width=48)
      (actual time=0.05..8.2 rows=50000 loops=1)
  ->  Hash  (cost=100..100 rows=100 width=16)
      (actual time=1.2..1.2 rows=100 loops=1)
        ->  Index Scan on users u  (cost=0.29..100 rows=100 width=16)
            (actual time=0.05..1.1 rows=100 loops=1)
```

`cost=start..total`: planner's estimated work units (arbitrary units, relative comparison only)
`actual time=start..total`: real milliseconds (first row .. last row)
`rows=N`: planner estimate vs actual rows — large discrepancy means stale statistics

---

### Scan Types

| Scan Type | When Used | Speed |
|---|---|---|
| **Seq Scan** | No useful index; small table; low selectivity | Slowest on large tables |
| **Index Scan** | Selective query; few rows returned | Fast for low cardinality result |
| **Index Only Scan** | Covering index; no heap access needed | Fastest |
| **Bitmap Heap Scan** | Multiple index scans combined; medium selectivity | Medium |

PostgreSQL may choose Seq Scan even when an index exists if it estimates that > ~10-15% of rows will be returned (sequential I/O is faster than random I/O for large fractions).

---

### Join Algorithms

| Algorithm | When Chosen |
|---|---|
| **Nested Loop** | Small outer table; inner side has an index |
| **Hash Join** | No index on join column; larger datasets |
| **Merge Join** | Both inputs sorted on the join key |

---

### Common Performance Anti-Patterns

**Function on indexed column:**
```sql
-- BAD: cannot use index on email because LOWER() wraps it
WHERE LOWER(email) = 'alice@example.com'

-- FIX: create an expression index
CREATE INDEX ON users(LOWER(email));
```

**Leading wildcard:**
```sql
-- BAD: cannot use B-tree index
WHERE name LIKE '%smith'

-- FIX: use pg_trgm extension with GIN index for arbitrary substring search
CREATE EXTENSION IF NOT EXISTS pg_trgm;
CREATE INDEX ON users USING GIN(name gin_trgm_ops);
WHERE name LIKE '%smith'   -- now uses the GIN index
```

**Implicit type cast:**
```sql
-- BAD: user_id is INTEGER but '42' is TEXT — cast prevents index use
WHERE user_id = '42'

-- FIX: match types
WHERE user_id = 42
```

**SELECT * on wide tables:** fetches unnecessary columns, increases I/O and network. Select only what you need.

---

### pg_stat_statements

`pg_stat_statements` tracks cumulative stats for every distinct query:

```sql
-- Enable (add to postgresql.conf, then restart or reload)
-- shared_preload_libraries = 'pg_stat_statements'

CREATE EXTENSION IF NOT EXISTS pg_stat_statements;

-- Top 10 slowest queries by total time
SELECT query, calls, total_exec_time, mean_exec_time, rows
FROM pg_stat_statements
ORDER BY total_exec_time DESC
LIMIT 10;

-- Reset stats
SELECT pg_stat_statements_reset();
```

`pg_stat_statements` is the first tool to reach for when diagnosing slow database performance.

---

### log_min_duration_statement

Log queries exceeding a threshold:

```sql
-- In postgresql.conf (or per-session)
log_min_duration_statement = 1000   -- log queries taking > 1 second
```

Parse the log file with `pgBadger` for aggregated slow query analysis.

---

### Key Takeaways

- `EXPLAIN ANALYZE` is the primary diagnostic tool — always use it with `BUFFERS` for full I/O visibility
- Large plan vs actual row discrepancy means statistics are stale — run `ANALYZE` or tune autovacuum
- Index Only Scan is the fastest path — design covering indexes for hot queries
- Functions on indexed columns prevent index use — use expression indexes or rewrite the query
- `pg_stat_statements` reveals which queries consume the most cumulative time in production
MD;
    }

    private function l4_3(): string
    {
        return <<<'MD'
## Connection Pooling, Memory Config & Diagnostics

### The Connection Problem

PostgreSQL creates one OS process per client connection. Each process consumes ~5 MB of RAM just for process overhead.

```
100 connections  × 5 MB =   500 MB overhead
1,000 connections × 5 MB = 5,000 MB overhead   ← before any actual work
```

Additionally, PostgreSQL has a hard `max_connections` limit (default 100). Exceeding it returns:
```
FATAL: sorry, too many clients already
```

For web applications with hundreds of concurrent users, direct connections do not scale. **Connection poolers** solve this.

---

### PgBouncer — Connection Pooling

PgBouncer sits between application servers and PostgreSQL, maintaining a pool of real PostgreSQL connections that are shared across many application connections.

```
App instances (1,000 connections to PgBouncer)
        ↓
    PgBouncer pool
        ↓
PostgreSQL (20-100 real connections)
```

**Pooling modes:**

| Mode | Description | Use Case |
|---|---|---|
| **Session pooling** | 1 server connection held for the full client session | Legacy apps; rarely used |
| **Transaction pooling** | Server connection released after each transaction | OLTP web apps — most common |
| **Statement pooling** | Released after each statement | Very restrictive; limited compatibility |

Transaction pooling is the recommended mode for most web applications. It allows 1,000 app connections to share ~20 PostgreSQL connections.

**Caveat:** transaction pooling is incompatible with some features: advisory locks, prepared statements (session-scoped), `SET LOCAL`, `LISTEN/NOTIFY`. Handle these via separate non-pooled connections if needed.

---

### Key Memory Settings

**shared_buffers** — PostgreSQL's data page cache:
```
# postgresql.conf
shared_buffers = 4GB   # Recommended: 25% of total RAM
```

**effective_cache_size** — hint to the planner about total memory available (shared_buffers + OS page cache). Does not allocate memory — only affects cost estimates:
```
effective_cache_size = 12GB   # ~75% of RAM on a dedicated server
```

**work_mem** — memory per sort/hash **node** (not per query, not per connection):
```
work_mem = 16MB   # Start conservative; increase per-session for analytics
```

A complex query with 5 sort nodes can use 5 × work_mem. 100 connections × 5 nodes × 16 MB = 8 GB potential allocation.

Set `work_mem` conservatively globally. For specific analytical sessions:
```sql
SET work_mem = '256MB';   -- applies to current session only
```

**maintenance_work_mem** — used by VACUUM, CREATE INDEX, ALTER TABLE:
```
maintenance_work_mem = 512MB   # Higher = faster index builds and VACUUM
```

---

### Diagnosing Active Connections — pg_stat_activity

```sql
-- All active connections
SELECT pid, usename, application_name, state, wait_event_type, wait_event, query
FROM pg_stat_activity
ORDER BY state;

-- Long-running queries (> 5 minutes)
SELECT pid, now() - query_start AS duration, query, state
FROM pg_stat_activity
WHERE state != 'idle' AND query_start < NOW() - INTERVAL '5 minutes'
ORDER BY duration DESC;

-- Idle in transaction (holding locks — dangerous)
SELECT pid, state, wait_event, now() - xact_start AS txn_duration
FROM pg_stat_activity
WHERE state = 'idle in transaction'
ORDER BY txn_duration DESC;
```

`idle in transaction` connections hold locks and dead tuple xmin — a common source of bloat and lock contention.

---

### Killing Stuck Connections

```sql
-- Cancel a query (graceful)
SELECT pg_cancel_backend(pid);

-- Terminate a connection (forceful)
SELECT pg_terminate_backend(pid);

-- Terminate all idle-in-transaction connections older than 5 minutes
SELECT pg_terminate_backend(pid)
FROM pg_stat_activity
WHERE state = 'idle in transaction'
  AND now() - xact_start > INTERVAL '5 minutes';
```

---

### Lock Monitoring

```sql
-- Find blocking locks
SELECT
    blocked_locks.pid       AS blocked_pid,
    blocking_locks.pid      AS blocking_pid,
    blocked_activity.query  AS blocked_query,
    blocking_activity.query AS blocking_query
FROM pg_locks blocked_locks
JOIN pg_locks blocking_locks ON blocking_locks.locktype  = blocked_locks.locktype
                             AND blocking_locks.relation = blocked_locks.relation
                             AND blocking_locks.granted  = true
                             AND blocked_locks.granted   = false
JOIN pg_stat_activity blocked_activity  ON blocked_activity.pid  = blocked_locks.pid
JOIN pg_stat_activity blocking_activity ON blocking_activity.pid = blocking_locks.pid;
```

---

### Key Takeaways

- One OS process per connection is expensive — use PgBouncer in transaction pooling mode for OLTP
- `shared_buffers` at 25% of RAM is the most impactful memory setting
- `work_mem` applies per-sort-node-per-query — set conservatively globally, override in sessions
- `pg_stat_activity` is the first tool for diagnosing slow or stuck connections
- `idle in transaction` connections are dangerous — they hold locks and prevent VACUUM from cleaning
MD;
    }

    // ─── Level 5 ───────────────────────────────────────────────────────────────

    private function l5_1(): string
    {
        return <<<'MD'
## Replication: WAL, Streaming & Logical Pub/Sub

### WAL and Crash Recovery (Recap)

The Write-Ahead Log (WAL) is PostgreSQL's durability mechanism. All changes are written to WAL before being applied to heap files. On crash, PostgreSQL replays WAL from the last checkpoint.

WAL records contain the physical changes: "page X, offset Y changed from A to B." Replicas receive and replay the same WAL stream.

---

### Streaming Replication

Streaming replication sends WAL bytes from the **primary** to one or more **standby** servers in near real-time.

```
Primary (read-write)
    │
    └── WAL stream (TCP)
         ↓
    Standby 1 (read-only — hot standby)
    Standby 2 (read-only)
```

**Setup overview (primary postgresql.conf):**
```
wal_level = replica           # Enable WAL content needed for replication
max_wal_senders = 5           # Max concurrent WAL sender processes
```

**Setup overview (standby recovery.conf or postgresql.conf):**
```
primary_conninfo = 'host=primary user=replicator password=...'
hot_standby = on              # Allow read queries on standby
```

**Hot standby:** standbys with `hot_standby = on` accept read-only queries. Direct analytics and reporting to standbys to offload the primary.

---

### Replication Slots

A replication slot tracks the WAL position a standby has consumed, preventing the primary from discarding WAL the standby still needs.

```sql
-- Create a physical slot
SELECT pg_create_physical_replication_slot('standby_1_slot');

-- List slots
SELECT slot_name, slot_type, active, restart_lsn FROM pg_replication_slots;
```

**Warning:** unused replication slots cause WAL files to accumulate on the primary, potentially filling the disk. Monitor `pg_replication_slots.restart_lsn` lag. Drop slots that are no longer needed.

---

### Synchronous vs Asynchronous Replication

**Asynchronous (default):** COMMIT returns immediately. Standby applies WAL slightly behind. Risk: failover may lose very recent transactions (replication lag).

**Synchronous:** COMMIT waits until at least one standby confirms it has received and written the WAL.

```
synchronous_standby_names = 'standby1'
```

Synchronous replication eliminates data loss on failover at the cost of added latency per COMMIT.

---

### Logical Replication (PostgreSQL 10+)

Logical replication operates at the **row level** using a publish/subscribe model, not raw WAL bytes.

```sql
-- On the publisher (primary):
CREATE PUBLICATION orders_pub FOR TABLE orders, users;

-- On the subscriber:
CREATE SUBSCRIPTION orders_sub
    CONNECTION 'host=primary dbname=mydb user=replica_user'
    PUBLICATION orders_pub;
```

**Key differences from streaming replication:**

| | Streaming | Logical |
|---|---|---|
| Granularity | Entire database cluster (all WAL) | Selected tables |
| Version compatibility | Must be same PostgreSQL version | Can differ (within limits) |
| Cross-DB | No | Yes |
| Schema changes | Replicated automatically | Manual (ALTER must be applied to subscriber) |
| Use case | HA / read scaling | Selective sync, migrations, upgrades |

Logical replication is ideal for **zero-downtime major version upgrades**: bring up a new-version subscriber, sync data, then do a brief switchover.

---

### Failover with Patroni

In production, manual failover is risky and slow. **Patroni** is the standard tool for automatic failover:

- Patroni is a Python daemon running on each PostgreSQL node
- It uses **etcd, Consul, or ZooKeeper** as a distributed lock (DCS) to elect a leader
- On primary failure, Patroni promotes the most up-to-date standby automatically
- Other standbys repoint themselves to the new primary
- HAProxy or a VIP (virtual IP) routes traffic to the current primary

```
┌─────────┐     ┌─────────┐     ┌─────────┐
│ PG + Patroni│ │ PG + Patroni│ │  etcd   │
│ primary     │ │ standby     │ │ (DCS)   │
└────────────┘ └────────────┘ └─────────┘
```

---

### Monitoring Replication Lag

```sql
-- On primary: lag per standby
SELECT application_name, state,
       pg_size_pretty(pg_wal_lsn_diff(pg_current_wal_lsn(), replay_lsn)) AS lag
FROM pg_stat_replication;

-- On standby: local lag
SELECT now() - pg_last_xact_replay_timestamp() AS replication_lag;
```

---

### Key Takeaways

- Streaming replication sends WAL bytes continuously; standbys can serve read queries (hot standby)
- Replication slots prevent WAL deletion — monitor unused slots to avoid disk exhaustion
- Logical replication replicates specific tables at row level — enables cross-version migrations
- Patroni + etcd is the production-standard for automatic primary failover
- Monitor replication lag with `pg_stat_replication` and alert on lag exceeding SLO thresholds
MD;
    }

    private function l5_2(): string
    {
        return <<<'MD'
## Full-Text Search, FDW & the Extensions Ecosystem

### Full-Text Search (FTS)

PostgreSQL has native full-text search — no Elasticsearch required for moderate workloads.

**Core types:**
- `tsvector` — a processed, sorted, de-duplicated list of lexemes (indexed representation of a document)
- `tsquery` — a search query with AND (`&`), OR (`|`), NOT (`!`), phrase operators

```sql
-- Convert text to tsvector
SELECT to_tsvector('english', 'The quick brown fox jumps over the lazy dog');
-- Result: 'brown':3 'dog':9 'fox':4 'jump':5 'lazi':8 'quick':2

-- Convert search string to tsquery
SELECT to_tsquery('english', 'quick & fox');
SELECT plainto_tsquery('english', 'quick fox');        -- &-joins words
SELECT phraseto_tsquery('english', 'quick brown fox'); -- phrase match

-- Match operator
SELECT * FROM articles
WHERE to_tsvector('english', body) @@ to_tsquery('english', 'postgresql & index');
```

---

### FTS Indexing

Store the tsvector in a generated column and index it:

```sql
ALTER TABLE articles ADD COLUMN ts_body TSVECTOR
    GENERATED ALWAYS AS (to_tsvector('english', body)) STORED;

CREATE INDEX ON articles USING GIN(ts_body);

-- Query uses the GIN index
SELECT id, title FROM articles
WHERE ts_body @@ plainto_tsquery('english', 'postgresql indexing');
```

**Ranking results:**

```sql
SELECT title,
       ts_rank(ts_body, query) AS rank
FROM articles, plainto_tsquery('english', 'postgresql indexing') query
WHERE ts_body @@ query
ORDER BY rank DESC;
```

`ts_headline()` highlights matching terms in the source text.

---

### pg_trgm — Trigram Similarity

For fuzzy matching and substring search:

```sql
CREATE EXTENSION IF NOT EXISTS pg_trgm;

-- Index for LIKE/ILIKE with wildcards
CREATE INDEX ON products USING GIN(name gin_trgm_ops);

SELECT * FROM products WHERE name ILIKE '%laptop%';    -- uses GIN index
SELECT * FROM products WHERE name % 'labtop';          -- similarity ~
SELECT similarity('laptop', 'labtop');                 -- 0.36 (0..1)
```

---

### Foreign Data Wrappers (FDW)

FDWs allow querying external data sources as if they were local PostgreSQL tables.

```sql
-- Query another PostgreSQL database
CREATE EXTENSION IF NOT EXISTS postgres_fdw;

CREATE SERVER remote_db
    FOREIGN DATA WRAPPER postgres_fdw
    OPTIONS (host 'analytics.internal', dbname 'analytics');

CREATE USER MAPPING FOR current_user
    SERVER remote_db
    OPTIONS (user 'reader', password 'secret');

CREATE FOREIGN TABLE remote_events (
    id         BIGINT,
    event_type TEXT,
    created_at TIMESTAMPTZ
) SERVER remote_db OPTIONS (schema_name 'public', table_name 'events');

SELECT * FROM remote_events WHERE created_at > NOW() - INTERVAL '1 day';
```

Other FDWs: `file_fdw` (CSV files), `mysql_fdw` (MySQL), `redis_fdw` (Redis), `multicorn` (Python-based custom FDWs).

---

### Essential Extensions

| Extension | Purpose |
|---|---|
| `pg_stat_statements` | Track query performance stats |
| `uuid-ossp` | Generate UUIDs (before `gen_random_uuid()` was built-in) |
| `pgcrypto` | Cryptographic functions (hashing, encryption) |
| `pg_trgm` | Trigram similarity and fuzzy text matching |
| `btree_gist` | B-tree behaviors for GiST index (enables multi-column with ranges) |
| `hstore` | Key-value storage (largely superseded by JSONB) |
| `PostGIS` | Geospatial data and queries |
| `pg_cron` | Schedule SQL jobs inside PostgreSQL |
| `pg_repack` | Online table rewrite (alternative to VACUUM FULL without lock) |
| `timescaledb` | Time-series data (partitioning, compression, continuous aggregates) |

```sql
-- Install an extension
CREATE EXTENSION IF NOT EXISTS pg_stat_statements;

-- List installed
SELECT * FROM pg_extension;
```

---

### pg_repack — Online Table Reorganization

`VACUUM FULL` reclaims bloat but requires an exclusive lock. `pg_repack` rewrites the table in the background without locking:

```bash
# CLI tool
pg_repack -d mydb -t orders
```

It creates a new copy of the table, applies ongoing changes via triggers, then swaps them with a brief lock. Safe for production use on large bloated tables.

---

### Key Takeaways

- PostgreSQL native FTS handles most search needs — avoid Elasticsearch overhead for non-massive scales
- Use `GENERATED ALWAYS AS (to_tsvector(...)) STORED` + GIN index for indexable FTS columns
- `pg_trgm` enables fuzzy matching and wildcard LIKE/ILIKE queries with GIN indexes
- FDWs allow querying external databases, files, and services as local tables
- `pg_repack` is the production-safe alternative to `VACUUM FULL` for bloated tables
MD;
    }

    private function l5_3(): string
    {
        return <<<'MD'
## Production Ops: Backup, PITR, RLS & Zero-Downtime Migrations

### Backup Strategies

**pg_dump — logical backup (single database):**

```bash
# Plain SQL format
pg_dump -U postgres -d mydb -f mydb_backup.sql

# Custom format (compressed, fast restore)
pg_dump -U postgres -d mydb -Fc -f mydb.dump

# Table-only backup
pg_dump -U postgres -d mydb -t orders -Fc -f orders.dump

# Restore
pg_restore -U postgres -d mydb mydb.dump
```

Custom format (`-Fc`) is recommended for production — it supports parallel restore and selective table restoration.

**pg_dumpall — backup all databases + global objects:**

```bash
pg_dumpall -U postgres -f all_databases.sql
```

Includes roles, tablespaces, and all database content. Required when migrating an entire server.

---

### Continuous Archiving & Point-in-Time Recovery (PITR)

`pg_dump` gives you a point-in-time snapshot. PITR allows restoring to **any moment in time**.

**Setup:**

```
# postgresql.conf
wal_level = replica
archive_mode = on
archive_command = 'aws s3 cp %p s3://my-pg-wal/%f'   # WAL archiving to S3
```

Every completed WAL segment is copied to the archive. Combined with a base backup, you can replay to any point.

**Base backup:**

```bash
pg_basebackup -U replicator -D /var/lib/postgresql/backup -Fp -Xs -P
```

**Restore to a specific time:**

```
# recovery.conf (PG 11-) or postgresql.conf (PG 12+)
restore_command = 'aws s3 cp s3://my-pg-wal/%f %p'
recovery_target_time = '2026-08-19 14:30:00'
```

---

### Row Level Security (RLS)

RLS enforces data access at the row level — users only see rows matching a policy.

```sql
-- Enable RLS on the table
ALTER TABLE orders ENABLE ROW LEVEL SECURITY;

-- Policy: users can only see their own orders
CREATE POLICY user_own_orders ON orders
    USING (user_id = current_setting('app.current_user_id')::BIGINT);

-- Superusers and table owners bypass RLS by default
-- Force RLS even for the table owner:
ALTER TABLE orders FORCE ROW LEVEL SECURITY;

-- Create a separate policy for admins
CREATE POLICY admin_all ON orders TO admin_role USING (true);
```

RLS is ideal for multi-tenant applications — one table can safely serve multiple tenants with a policy enforcing tenant isolation at the database layer.

---

### Roles & Security Model

```sql
-- Create a role (same as CREATE USER but without login by default)
CREATE ROLE readonly_role;

-- Grant specific privileges
GRANT CONNECT ON DATABASE mydb TO readonly_role;
GRANT USAGE ON SCHEMA public TO readonly_role;
GRANT SELECT ON ALL TABLES IN SCHEMA public TO readonly_role;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT SELECT ON TABLES TO readonly_role;

-- Create a user and assign role
CREATE USER bob WITH PASSWORD 'securepassword';
GRANT readonly_role TO bob;

-- Revoke
REVOKE SELECT ON orders FROM readonly_role;
```

**Principle of least privilege:** application service accounts should never be superusers. Give them the minimum GRANT required.

---

### Zero-Downtime Migrations

Schema migrations in production require care — some operations lock tables and block traffic.

**Safe operations (no lock / brief lock):**
- `CREATE INDEX CONCURRENTLY` — background build, no write lock
- `ALTER TABLE ADD COLUMN ... DEFAULT NULL` — metadata-only in PG 11+
- `ALTER TABLE ADD COLUMN ... DEFAULT constant` — instant in PG 11+
- `CREATE TABLE`, `ADD FOREIGN KEY NOT VALID` (validates later)

**Dangerous operations (heavy lock):**
- `ALTER TABLE ALTER COLUMN TYPE` — full table rewrite
- `ADD COLUMN ... DEFAULT non-trivial-expression` (pre-PG 11)
- `CREATE INDEX` without CONCURRENTLY
- `VACUUM FULL`, `CLUSTER`

**Pattern for changing a column type (zero-downtime):**

```sql
-- 1. Add new column
ALTER TABLE orders ADD COLUMN amount_cents BIGINT;

-- 2. Backfill in batches (don't lock the whole table)
UPDATE orders SET amount_cents = (amount * 100)::BIGINT
WHERE id BETWEEN 1 AND 10000;
-- repeat in batches with LIMIT

-- 3. Add NOT NULL after backfill (requires brief lock in older PG versions)
-- In PG 12+: use CHECK NOT NULL with NOT VALID then VALIDATE CONSTRAINT
ALTER TABLE orders ADD CONSTRAINT orders_amount_cents_not_null
    CHECK (amount_cents IS NOT NULL) NOT VALID;
ALTER TABLE orders VALIDATE CONSTRAINT orders_amount_cents_not_null;

-- 4. Deploy code that writes to both columns
-- 5. Switch reads to new column
-- 6. Drop old column
ALTER TABLE orders DROP COLUMN amount;
```

---

### Monitoring Key Metrics

```sql
-- Table I/O stats
SELECT relname, heap_blks_read, heap_blks_hit,
       ROUND(heap_blks_hit::numeric / NULLIF(heap_blks_hit + heap_blks_read, 0) * 100, 2) AS cache_hit_pct
FROM pg_statio_user_tables ORDER BY heap_blks_read DESC;

-- Index usage — find unused indexes
SELECT schemaname, relname, indexrelname, idx_scan
FROM pg_stat_user_indexes
WHERE idx_scan = 0
ORDER BY relname;

-- Checkpoint frequency (high = write pressure)
SELECT checkpoints_timed, checkpoints_req FROM pg_stat_bgwriter;
```

---

### Key Takeaways

- Use `pg_dump -Fc` for logical backups — supports parallel restore and selective table recovery
- PITR (WAL archiving + base backup) enables restore to any second — essential for production
- Row Level Security enforces per-row access control at the database layer — ideal for multi-tenant systems
- Zero-downtime migrations require `ADD COLUMN DEFAULT NULL`, `CREATE INDEX CONCURRENTLY`, and batched backfills — avoid full-table rewrites under traffic
- Monitor `pg_stat_user_indexes` for unused indexes and `pg_statio_user_tables` for cache hit rates
MD;
    }

    // ─── Level 4 MCQs ─────────────────────────────────────────────────────────

    private function seedLevel4Questions(Topic $topic): void
    {
        $questions = [
            [
                'question'    => 'How does MVCC in PostgreSQL allow readers and writers to operate concurrently without blocking each other?',
                'explanation' => 'MVCC stores multiple versions of each row (tuples). Readers see a consistent snapshot of the database at their transaction start time, reading old row versions. Writers create new row versions. Neither operation blocks the other. Dead old versions are cleaned up by VACUUM.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'Readers see a consistent snapshot of old row versions; writers create new versions — neither blocks the other; VACUUM cleans dead tuples', 'correct' => true],
                    ['text' => 'PostgreSQL uses shared locks for reads and exclusive locks for writes, so readers and writers never conflict',                             'correct' => false],
                    ['text' => 'Readers are queued behind writers and execute after each write transaction commits',                                                     'correct' => false],
                    ['text' => 'PostgreSQL serializes all concurrent transactions so they run in a strict order without overlap',                                        'correct' => false],
                ],
            ],
            [
                'question'    => 'What does VACUUM do in PostgreSQL and why is it required?',
                'explanation' => 'VACUUM reclaims space occupied by dead tuples (old row versions left by MVCC after UPDATE/DELETE). It marks the space as reusable, updates planner statistics, and advances the XID horizon to prevent transaction ID wraparound. Without VACUUM, table bloat and eventually XID wraparound cause critical problems.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'Reclaims dead tuple space from MVCC, updates planner statistics, and prevents XID wraparound; AUTOVACUUM runs it automatically', 'correct' => true],
                    ['text' => 'VACUUM compresses table data to reduce disk usage by removing duplicate column values',                                            'correct' => false],
                    ['text' => 'VACUUM rewrites the table in primary key order for faster sequential reads',                                                      'correct' => false],
                    ['text' => 'VACUUM removes unused indexes from tables that no longer benefit from them',                                                      'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between VACUUM and VACUUM FULL in PostgreSQL?',
                'explanation' => 'Regular VACUUM reclaims dead tuple space for internal reuse without returning it to the OS, and runs concurrently with reads and writes. VACUUM FULL rewrites the entire table to a new file (returning disk space to the OS) but requires an exclusive lock — blocking all reads and writes during the operation.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'VACUUM reclaims space for internal reuse (non-blocking); VACUUM FULL rewrites the table and returns space to the OS but requires an exclusive lock', 'correct' => true],
                    ['text' => 'VACUUM FULL is faster because it skips planner statistics updates',                                                                                  'correct' => false],
                    ['text' => 'VACUUM FULL removes all rows; regular VACUUM only removes rows older than 30 days',                                                                  'correct' => false],
                    ['text' => 'They are identical; FULL is just a verbose alias',                                                                                                   'correct' => false],
                ],
            ],
            [
                'question'    => 'What does EXPLAIN (ANALYZE, BUFFERS) output in PostgreSQL and what does "shared hit" mean?',
                'explanation' => 'EXPLAIN ANALYZE actually executes the query and returns real timing and row counts alongside planner estimates. The BUFFERS option adds cache statistics: "shared hit" is the number of data blocks served from shared_buffers (fast, in-memory), while "shared read" is blocks fetched from disk (slow).',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'Executes the query, shows real timing and row counts; "shared hit" = blocks served from shared_buffers (cache); "shared read" = blocks from disk', 'correct' => true],
                    ['text' => 'Shows the planner estimate without executing; "shared hit" = index rows matching the predicate',                                                   'correct' => false],
                    ['text' => '"shared hit" counts the number of concurrent transactions that accessed the same rows',                                                             'correct' => false],
                    ['text' => 'BUFFERS shows network bytes transferred between client and server for the query',                                                                   'correct' => false],
                ],
            ],
            [
                'question'    => 'When does PostgreSQL choose a Seq Scan over an Index Scan even when an index exists?',
                'explanation' => 'The planner chooses Seq Scan when it estimates that a large fraction of the table will be returned — typically when the selectivity is low (e.g., > 10-20% of rows match). For large fractions, sequential I/O (reading pages in order) is faster than random I/O (jumping to scattered index pages). The planner uses table statistics to make this decision.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'When the query returns a large fraction of rows — sequential I/O is faster than scattered random index I/O for low-selectivity queries', 'correct' => true],
                    ['text' => 'When the table has more than 1 million rows, PostgreSQL always prefers Seq Scan',                                                         'correct' => false],
                    ['text' => 'PostgreSQL never chooses Seq Scan if an index exists on the queried column',                                                              'correct' => false],
                    ['text' => 'Seq Scan is used only for UPDATE and DELETE statements; SELECT always uses indexes',                                                      'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an Index Only Scan in PostgreSQL and when does it occur?',
                'explanation' => 'An Index Only Scan occurs when the query can be answered entirely from the index without accessing the heap (table). This requires a covering index — either all needed columns are in the index key, or stored via the INCLUDE clause (PostgreSQL 11+). It is the fastest scan type because it avoids heap I/O.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'A scan that returns results entirely from the index without touching the heap; requires a covering index (key columns + INCLUDE)', 'correct' => true],
                    ['text' => 'A scan on a partial index where only a subset of rows is indexed',                                                                 'correct' => false],
                    ['text' => 'A scan used when the query has no WHERE clause and only reads index metadata',                                                      'correct' => false],
                    ['text' => 'A scan used exclusively with hash indexes for equality-only queries',                                                               'correct' => false],
                ],
            ],
            [
                'question'    => 'What is XID wraparound in PostgreSQL and why is it dangerous?',
                'explanation' => 'PostgreSQL uses 32-bit transaction IDs (XID) which wrap around after ~2.1 billion transactions. Without VACUUM freezing old XIDs, rows from before wraparound appear to be "in the future" and become invisible — effectively data loss. PostgreSQL emits warnings and eventually forces a shutdown to prevent this. AUTOVACUUM prevents it via periodic freezing.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'After ~2.1 billion transactions, 32-bit XIDs wrap around; old rows appear invisible (data loss); prevented by VACUUM freezing old XIDs', 'correct' => true],
                    ['text' => 'XID wraparound causes replication to stop when primary and replica XIDs diverge beyond 2 billion',                                        'correct' => false],
                    ['text' => 'A circular reference between two transactions waiting on each other\'s XID, causing a deadlock',                                          'correct' => false],
                    ['text' => 'Wraparound happens when work_mem is exceeded, causing transaction IDs to overflow into disk temp files',                                  'correct' => false],
                ],
            ],
            [
                'question'    => 'What is pg_stat_statements and how do you use it to find the slowest queries?',
                'explanation' => 'pg_stat_statements is a PostgreSQL extension that tracks cumulative execution statistics for every distinct query. It must be added to shared_preload_libraries and the extension created. Sort by total_exec_time to find the most time-consuming queries overall, or by mean_exec_time for the slowest per-call.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'An extension tracking cumulative stats per query; sort by total_exec_time for worst total impact, or mean_exec_time for slowest per call', 'correct' => true],
                    ['text' => 'A system table that logs individual slow queries exceeding log_min_duration_statement',                                                     'correct' => false],
                    ['text' => 'A built-in view showing only currently running queries; it resets after each statement',                                                    'correct' => false],
                    ['text' => 'A PostgreSQL config parameter that enables slow query logging in postgresql.log',                                                            'correct' => false],
                ],
            ],
            [
                'question'    => 'What is connection pooling in PostgreSQL and why is PgBouncer used instead of relying on max_connections alone?',
                'explanation' => 'Each PostgreSQL connection is an OS process consuming ~5 MB RAM. max_connections limits total connections but does not help efficiency. PgBouncer sits in front of PostgreSQL and multiplexes many application connections over a small pool of real database connections (especially in transaction pooling mode), drastically reducing connection overhead.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'PgBouncer multiplexes many app connections over a small real connection pool — each PG connection is an ~5 MB process; max_connections alone does not reduce overhead', 'correct' => true],
                    ['text' => 'PgBouncer is needed because PostgreSQL does not have built-in connection management; max_connections is a deprecated setting',                                          'correct' => false],
                    ['text' => 'PgBouncer caches query results across connections, reducing database load',                                                                                           'correct' => false],
                    ['text' => 'PgBouncer is only needed for read replicas; the primary can handle unlimited connections natively',                                                                   'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you find connections that are "idle in transaction" in PostgreSQL and why are they dangerous?',
                'explanation' => '"Idle in transaction" connections have an open transaction but are not executing any query. They hold locks and prevent VACUUM from cleaning dead tuples in the locked rows (their xmin snapshot is kept alive). They are diagnosed via pg_stat_activity WHERE state = \'idle in transaction\'. Long-running ones should be terminated.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'Query pg_stat_activity WHERE state = \'idle in transaction\'; they hold locks and block VACUUM — terminate with pg_terminate_backend()', 'correct' => true],
                    ['text' => 'They are safe — idle connections consume no resources and can be left indefinitely',                                                      'correct' => false],
                    ['text' => 'Use SHOW PROCESSLIST to find idle transactions; PostgreSQL auto-kills them after idle_in_transaction_session_timeout',                    'correct' => false],
                    ['text' => 'Idle in transaction connections are automatically rolled back after 1 second by autovacuum',                                               'correct' => false],
                ],
            ],
        ];

        foreach ($questions as $qData) {
            $exists = Question::where('topic_id', $topic->id)
                ->where('question', $qData['question'])
                ->exists();
            if ($exists) continue;

            $question = Question::create([
                'topic_id'    => $topic->id,
                'type'        => 'MCQ',
                'difficulty'  => $qData['difficulty'],
                'question'    => $qData['question'],
                'explanation' => $qData['explanation'],
            ]);

            foreach ($qData['options'] as $opt) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $opt['text'],
                    'is_correct'  => $opt['correct'],
                ]);
            }
        }
    }

    // ─── Level 5 MCQs ─────────────────────────────────────────────────────────

    private function seedLevel5Questions(Topic $topic): void
    {
        $questions = [
            [
                'question'    => 'What is WAL archiving in PostgreSQL and how does it enable Point-in-Time Recovery (PITR)?',
                'explanation' => 'WAL archiving copies completed WAL segment files to an external location (e.g., S3) via archive_command. Combined with a base backup (pg_basebackup), you can restore the base backup and then replay archived WAL segments up to any target time, enabling recovery to any specific moment — not just the last backup.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'archive_command copies WAL files to external storage; combined with pg_basebackup, WAL replay enables restore to any second in time', 'correct' => true],
                    ['text' => 'WAL archiving compresses WAL files on disk to save space; PITR is unrelated and uses pg_dump snapshots',                               'correct' => false],
                    ['text' => 'WAL archiving replicates WAL to standbys; PITR restores from the most recent standby checkpoint',                                       'correct' => false],
                    ['text' => 'PITR requires a special backup extension; WAL archiving is only used for replication',                                                   'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the key difference between streaming replication and logical replication in PostgreSQL?',
                'explanation' => 'Streaming replication sends raw WAL bytes, replicating the entire cluster with the same PostgreSQL version. Logical replication sends row-level changes using a publish/subscribe model, can replicate specific tables, works across different PostgreSQL versions, and enables cross-database replication.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'Streaming sends raw WAL (whole cluster, same version); logical replication sends row changes (specific tables, cross-version, pub/sub model)', 'correct' => true],
                    ['text' => 'Streaming replication is asynchronous; logical replication is always synchronous',                                                             'correct' => false],
                    ['text' => 'Logical replication is slower than streaming and is only used for development environments',                                                    'correct' => false],
                    ['text' => 'They are identical; logical replication is just the new name for streaming replication in PostgreSQL 14+',                                     'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a replication slot in PostgreSQL and what is its risk?',
                'explanation' => 'A replication slot tracks the WAL position consumed by a standby, preventing the primary from discarding WAL the standby still needs. The risk: if the standby falls behind or the slot is unused (e.g., standby destroyed), the primary accumulates undeleted WAL files, potentially filling the disk and causing an outage.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'Tracks standby WAL consumption so the primary keeps necessary WAL; risk: unused slots cause WAL accumulation and disk exhaustion', 'correct' => true],
                    ['text' => 'A slot reserves a connection slot on the primary for the standby\'s WAL receiver process',                                         'correct' => false],
                    ['text' => 'A replication slot caches the standby\'s query results to reduce primary read load',                                               'correct' => false],
                    ['text' => 'Slots are only used for logical replication; streaming replication does not need them',                                             'correct' => false],
                ],
            ],
            [
                'question'    => 'How does Row Level Security (RLS) work in PostgreSQL?',
                'explanation' => 'RLS filters rows returned by queries based on policies. You enable it with ALTER TABLE ... ENABLE ROW LEVEL SECURITY, then create policies with CREATE POLICY that define which rows each role can see or modify. Superusers and table owners bypass RLS by default; use FORCE ROW LEVEL SECURITY to enforce it for everyone.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'Policies defined with CREATE POLICY filter rows per role; enabled with ALTER TABLE ENABLE RLS; table owners bypass it unless FORCE RLS is set', 'correct' => true],
                    ['text' => 'RLS encrypts specific rows so only users with the decryption key can read them',                                                                'correct' => false],
                    ['text' => 'RLS is a view-based feature — a view is automatically created per user to restrict visible rows',                                              'correct' => false],
                    ['text' => 'RLS restricts which columns a user can query; row filtering is handled by GRANT SELECT with conditions',                                       'correct' => false],
                ],
            ],
            [
                'question'    => 'How does full-text search work in PostgreSQL using tsvector and tsquery?',
                'explanation' => 'to_tsvector() converts text to a tsvector — a normalized, stemmed list of lexemes. to_tsquery() converts a search string to a tsquery. The @@ operator tests if a tsquery matches a tsvector. Create a GIN index on a STORED generated tsvector column for efficient search. ts_rank() scores results by relevance.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'to_tsvector() produces a lexeme list; to_tsquery() produces a search query; @@ matches them; GIN index on stored tsvector column for speed', 'correct' => true],
                    ['text' => 'Full-text search uses LIKE \'%term%\' internally optimized by a special inverted index type',                                                  'correct' => false],
                    ['text' => 'tsvector stores raw text; tsquery is a SELECT template; @@ executes the template against the stored text',                                     'correct' => false],
                    ['text' => 'PostgreSQL FTS requires the pg_fulltext extension; it is not built into the core engine',                                                       'correct' => false],
                ],
            ],
            [
                'question'    => 'Why does REFRESH MATERIALIZED VIEW CONCURRENTLY require a unique index and what does it offer?',
                'explanation' => 'REFRESH MATERIALIZED VIEW CONCURRENTLY updates the materialized view\'s data without acquiring an exclusive lock, so reads continue during the refresh. To implement this, PostgreSQL computes a diff between old and new data and applies changes incrementally — which requires a unique index to identify rows for the diff.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'The unique index lets PostgreSQL diff old vs new rows and apply changes incrementally without locking — reads are unblocked during refresh', 'correct' => true],
                    ['text' => 'The unique index is needed to prevent duplicate rows that would appear during concurrent refresh',                                             'correct' => false],
                    ['text' => 'CONCURRENTLY only works with hash indexes; the unique B-tree index is just a documentation requirement',                                       'correct' => false],
                    ['text' => 'The unique index is used to shard the refresh across multiple CPU cores in parallel',                                                          'correct' => false],
                ],
            ],
            [
                'question'    => 'What does CREATE INDEX CONCURRENTLY do and what is its trade-off compared to regular CREATE INDEX?',
                'explanation' => 'CREATE INDEX CONCURRENTLY builds the index in the background while reads and writes continue — no write lock is held. The trade-off: it takes longer (multiple table scans), cannot run inside a transaction, and if it fails, leaves an INVALID index that must be manually dropped and recreated.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'Builds the index without a write lock so production traffic continues; slower and cannot run in a transaction; failure leaves an INVALID index', 'correct' => true],
                    ['text' => 'CONCURRENTLY means multiple indexes are created in parallel in one statement',                                                                    'correct' => false],
                    ['text' => 'It creates a partial index concurrently with an ongoing VACUUM operation',                                                                        'correct' => false],
                    ['text' => 'CONCURRENTLY is identical to regular CREATE INDEX but adds a progress bar to the output',                                                         'correct' => false],
                ],
            ],
            [
                'question'    => 'What is pg_repack and when should you use it instead of VACUUM FULL?',
                'explanation' => 'pg_repack is a PostgreSQL extension that rewrites a bloated table online — without an exclusive lock. It creates a new copy, applies live changes via triggers, then swaps tables with only a brief lock. VACUUM FULL requires a full exclusive lock for the entire duration. pg_repack is preferred in production where downtime is not acceptable.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'pg_repack rewrites the table online (brief lock at swap); VACUUM FULL holds an exclusive lock for the full duration — use pg_repack in production', 'correct' => true],
                    ['text' => 'pg_repack is only for index rebuilding; VACUUM FULL handles table data',                                                                             'correct' => false],
                    ['text' => 'VACUUM FULL is always preferred — pg_repack is an unofficial community fork without PostgreSQL core support',                                          'correct' => false],
                    ['text' => 'pg_repack archives old rows to a separate table; VACUUM FULL deletes them',                                                                          'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the safe pattern for a zero-downtime column type change in PostgreSQL under production traffic?',
                'explanation' => 'ALTER TABLE ALTER COLUMN TYPE rewrites the table with an exclusive lock. The safe pattern: (1) add a new column, (2) backfill data in small batches with WHERE id BETWEEN..., (3) deploy code writing to both columns, (4) add NOT NULL constraint as NOT VALID then VALIDATE CONSTRAINT, (5) switch reads to new column, (6) drop old column.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'Add new column → batch backfill → dual-write → NOT VALID constraint → VALIDATE → switch reads → drop old column', 'correct' => true],
                    ['text' => 'Run ALTER TABLE ALTER COLUMN TYPE inside a transaction to make it atomic and safe',                                  'correct' => false],
                    ['text' => 'Use CREATE TABLE AS SELECT with the new type, then swap table names with RENAME',                                    'correct' => false],
                    ['text' => 'ALTER TABLE ADD COLUMN with a DEFAULT automatically migrates the type without locking',                              'correct' => false],
                ],
            ],
            [
                'question'    => 'How does pg_stat_user_indexes help identify unused indexes in PostgreSQL?',
                'explanation' => 'pg_stat_user_indexes tracks idx_scan — the number of times each index has been used in a query since the last statistics reset. An index with idx_scan = 0 (after sufficient traffic) is unused. Unused indexes waste disk space and slow down every INSERT/UPDATE/DELETE without providing read benefit.',
                'difficulty'  => 'Hard',
                'options'     => [
                    ['text' => 'idx_scan = 0 after sufficient traffic means the index is unused — it wastes write overhead and space with no read benefit; drop it', 'correct' => true],
                    ['text' => 'pg_stat_user_indexes shows the size of each index; drop the largest ones to save space',                                             'correct' => false],
                    ['text' => 'Unused indexes are automatically dropped by autovacuum when idx_scan stays at 0 for 7 days',                                          'correct' => false],
                    ['text' => 'idx_scan counts read rows, not index scans — a value of 0 means the index is write-only',                                             'correct' => false],
                ],
            ],
        ];

        foreach ($questions as $qData) {
            $exists = Question::where('topic_id', $topic->id)
                ->where('question', $qData['question'])
                ->exists();
            if ($exists) continue;

            $question = Question::create([
                'topic_id'    => $topic->id,
                'type'        => 'MCQ',
                'difficulty'  => $qData['difficulty'],
                'question'    => $qData['question'],
                'explanation' => $qData['explanation'],
            ]);

            foreach ($qData['options'] as $opt) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $opt['text'],
                    'is_correct'  => $opt['correct'],
                ]);
            }
        }
    }
}
