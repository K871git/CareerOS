<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MySqlLearningSeeder extends Seeder
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
            ['slug' => 'mysql'],
            [
                'learning_track_id' => $track->id,
                'title'             => 'MySQL',
                'description'       => 'Master MySQL from core SQL to production-grade optimization, replication, and high availability.',
                'display_order'     => 1,
            ]
        );

        // Assign levels to existing practice topics
        Topic::where('slug', 'mysql-junior')->update(['level' => 1, 'subject_id' => $subject->id]);
        Topic::where('slug', 'mysql-intermediate')->update(['level' => 2, 'subject_id' => $subject->id]);
        Topic::where('slug', 'mysql-advanced')->update(['level' => 3, 'subject_id' => $subject->id]);

        $topic4 = Topic::firstOrCreate(
            ['slug' => 'mysql-level-4-performance'],
            [
                'subject_id'    => $subject->id,
                'title'         => 'MySQL Performance & Optimization',
                'description'   => 'EXPLAIN, indexing strategies, InnoDB buffer pool, deadlock handling, and query tuning.',
                'display_order' => 4,
                'level'         => 4,
            ]
        );
        Topic::where('slug', 'mysql-level-4-performance')->update(['level' => 4]);

        $topic5 = Topic::firstOrCreate(
            ['slug' => 'mysql-level-5-architecture'],
            [
                'subject_id'    => $subject->id,
                'title'         => 'MySQL Architecture & High Availability',
                'description'   => 'Replication, partitioning, sharding, ProxySQL, and production HA patterns.',
                'display_order' => 5,
                'level'         => 5,
            ]
        );
        Topic::where('slug', 'mysql-level-5-architecture')->update(['level' => 5]);

        $this->seedLessons($subject);
        $this->seedLevel4Questions($topic4);
        $this->seedLevel5Questions($topic5);

        $this->command->info('MySQL Learning seeder complete — 5 levels, 15 lessons populated.');
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
                ['title' => 'Databases, Tables & MySQL Data Types',          'content' => $this->l1_1(), 'estimated_minutes' => 20, 'display_order' => 1],
                ['title' => 'Core SQL: SELECT, INSERT, UPDATE & DELETE',      'content' => $this->l1_2(), 'estimated_minutes' => 20, 'display_order' => 2],
                ['title' => 'Filtering & Sorting: WHERE, ORDER BY & LIKE',   'content' => $this->l1_3(), 'estimated_minutes' => 18, 'display_order' => 3],
            ],
            2 => [
                ['title' => 'JOINs: INNER, LEFT, RIGHT & Self-Joins',        'content' => $this->l2_1(), 'estimated_minutes' => 22, 'display_order' => 1],
                ['title' => 'Aggregation: GROUP BY, HAVING & Aggregate Functions', 'content' => $this->l2_2(), 'estimated_minutes' => 20, 'display_order' => 2],
                ['title' => 'Subqueries, Constraints & Basic Indexing',      'content' => $this->l2_3(), 'estimated_minutes' => 22, 'display_order' => 3],
            ],
            3 => [
                ['title' => 'Transactions & ACID: Data Integrity Under Concurrency', 'content' => $this->l3_1(), 'estimated_minutes' => 25, 'display_order' => 1],
                ['title' => 'Stored Procedures, Functions & Triggers',        'content' => $this->l3_2(), 'estimated_minutes' => 25, 'display_order' => 2],
                ['title' => 'Views, CTEs & Window Functions',                 'content' => $this->l3_3(), 'estimated_minutes' => 25, 'display_order' => 3],
            ],
            4 => [
                ['title' => 'Query Optimization & the EXPLAIN Statement',    'content' => $this->l4_1(), 'estimated_minutes' => 28, 'display_order' => 1],
                ['title' => 'Indexing Deep Dive: B-Trees, Composite & Covering Indexes', 'content' => $this->l4_2(), 'estimated_minutes' => 28, 'display_order' => 2],
                ['title' => 'InnoDB Internals: Buffer Pool, Redo Logs & Deadlock Handling', 'content' => $this->l4_3(), 'estimated_minutes' => 30, 'display_order' => 3],
            ],
            5 => [
                ['title' => 'Replication: Primary-Replica Setup & GTID',     'content' => $this->l5_1(), 'estimated_minutes' => 30, 'display_order' => 1],
                ['title' => 'Partitioning, Sharding & Connection Pooling',   'content' => $this->l5_2(), 'estimated_minutes' => 28, 'display_order' => 2],
                ['title' => 'High Availability: Group Replication, ProxySQL & Backups', 'content' => $this->l5_3(), 'estimated_minutes' => 30, 'display_order' => 3],
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

    // ─── Level 1: Easy ────────────────────────────────────────────────────────

    private function l1_1(): string
    {
        return <<<'MD'
## Databases, Tables & MySQL Data Types

### What Is a Relational Database?

A relational database stores data in **tables** (rows and columns), similar to a spreadsheet. MySQL is one of the world's most popular open-source relational database management systems (RDBMS).

Key terms:
- **Database** — a named container for tables
- **Table** — a structured grid of data
- **Row (record)** — a single entry in a table
- **Column (field)** — a named attribute with a fixed data type
- **Primary Key** — a column (or set) that uniquely identifies each row

---

### Creating a Database

```sql
CREATE DATABASE careerdb;
USE careerdb;
```

`USE` sets the active database for the session — all subsequent queries target it.

---

### Creating a Table

```sql
CREATE TABLE users (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    role       ENUM('admin','student','guest') NOT NULL DEFAULT 'student',
    bio        TEXT,
    balance    DECIMAL(10, 2) DEFAULT 0.00,
    is_active  TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

---

### Essential MySQL Data Types

| Category | Type | Notes |
|---|---|---|
| Integer | `INT`, `BIGINT`, `TINYINT` | `UNSIGNED` removes negatives, doubles positive range |
| Decimal | `DECIMAL(p,s)` | Exact — use for money. `FLOAT`/`DOUBLE` have rounding errors |
| String | `VARCHAR(n)` | Variable-length up to n chars. Faster for short strings |
| Large text | `TEXT`, `MEDIUMTEXT`, `LONGTEXT` | Not stored inline — can't have DEFAULT |
| Date/time | `DATE`, `TIME`, `DATETIME`, `TIMESTAMP` | `TIMESTAMP` auto-converts to UTC |
| Boolean | `TINYINT(1)` | MySQL has no native BOOL — use 0/1 |
| Set | `ENUM('a','b')` | Exactly one value from list |
| Binary | `BLOB`, `MEDIUMBLOB` | Raw bytes — images, files |
| JSON | `JSON` | Native JSON support since MySQL 5.7 |

---

### Inspecting Your Schema

```sql
SHOW DATABASES;               -- list all databases
SHOW TABLES;                  -- list tables in current DB
DESCRIBE users;               -- columns, types, nullability, keys
SHOW CREATE TABLE users;      -- full CREATE statement
```

---

### Modifying Tables

```sql
-- Add a column
ALTER TABLE users ADD COLUMN phone VARCHAR(20) AFTER email;

-- Rename a column
ALTER TABLE users RENAME COLUMN phone TO mobile;

-- Change data type
ALTER TABLE users MODIFY COLUMN name VARCHAR(200) NOT NULL;

-- Drop a column
ALTER TABLE users DROP COLUMN bio;

-- Rename a table
RENAME TABLE users TO app_users;
```

---

### Dropping Tables and Databases

```sql
DROP TABLE IF EXISTS temp_data;
DROP DATABASE old_project;    -- permanently deletes everything inside
```

> Always use `IF EXISTS` in scripts to prevent errors on fresh environments.

---

### Key Takeaways

- Use `INT UNSIGNED AUTO_INCREMENT PRIMARY KEY` for surrogate keys
- Use `DECIMAL` for money, never `FLOAT`
- Use `VARCHAR` for most text; switch to `TEXT` only when content is long and unpredictable
- `TIMESTAMP` stores UTC and auto-converts; `DATETIME` stores exactly what you insert
- `DESCRIBE table` is your quick schema reference
MD;
    }

    private function l1_2(): string
    {
        return <<<'MD'
## Core SQL: SELECT, INSERT, UPDATE & DELETE

These four statements are the backbone of every database-driven application.

---

### SELECT — Reading Data

```sql
-- All columns
SELECT * FROM users;

-- Specific columns
SELECT id, name, email FROM users;

-- With an alias
SELECT name AS full_name, email AS contact FROM users;

-- Computed column
SELECT name, balance * 1.1 AS adjusted_balance FROM users;
```

> Avoid `SELECT *` in production code — it fetches unnecessary columns and breaks when schema changes.

---

### INSERT — Adding Rows

```sql
-- Single row
INSERT INTO users (name, email, role)
VALUES ('Alice', 'alice@example.com', 'student');

-- Multiple rows in one statement (faster than looping)
INSERT INTO users (name, email, role) VALUES
    ('Bob',   'bob@example.com',   'student'),
    ('Carol', 'carol@example.com', 'admin');
```

**INSERT OR UPDATE — upsert pattern:**

```sql
INSERT INTO users (id, name, email)
VALUES (1, 'Alice', 'alice_new@example.com')
ON DUPLICATE KEY UPDATE email = VALUES(email);
```

---

### UPDATE — Modifying Rows

```sql
-- Update one column
UPDATE users SET role = 'admin' WHERE id = 3;

-- Update multiple columns
UPDATE users
SET name = 'Alice B.', is_active = 0
WHERE email = 'alice@example.com';

-- Update all rows (dangerous — no WHERE)
UPDATE users SET balance = 0;
```

> **Always include a WHERE clause** in UPDATE unless you intentionally target all rows. Run a SELECT first to verify the target rows.

---

### DELETE — Removing Rows

```sql
-- Remove specific rows
DELETE FROM users WHERE is_active = 0;

-- Remove one row by primary key (safest)
DELETE FROM users WHERE id = 7;
```

**DELETE vs TRUNCATE vs DROP**

| Command | Removes | Resets AUTO_INCREMENT | Triggers fire | Rollbackable |
|---|---|---|---|---|
| `DELETE` | Rows (with WHERE) | No | Yes | Yes |
| `TRUNCATE` | All rows | Yes | No | No (DDL) |
| `DROP` | Entire table | — | No | No |

```sql
TRUNCATE TABLE session_logs;   -- fast wipe, resets counter
```

---

### Safe UPDATE / DELETE Pattern

```sql
-- Step 1: preview what you'll change
SELECT * FROM users WHERE last_login < '2024-01-01';

-- Step 2: run the change
DELETE FROM users WHERE last_login < '2024-01-01';
```

Wrapping in a transaction adds a safety net:

```sql
START TRANSACTION;
DELETE FROM users WHERE last_login < '2024-01-01';
-- inspect rows affected, then:
COMMIT;   -- or ROLLBACK;
```

---

### Key Takeaways

- Always specify columns in INSERT — never rely on column order
- `ON DUPLICATE KEY UPDATE` is MySQL's upsert — one round-trip instead of SELECT then INSERT
- Check your WHERE clause before UPDATE/DELETE — a missing WHERE hits every row
- `TRUNCATE` is faster than `DELETE FROM table` for full wipes but cannot be rolled back
MD;
    }

    private function l1_3(): string
    {
        return <<<'MD'
## Filtering & Sorting: WHERE, ORDER BY, LIMIT & LIKE

Fetching data without filtering is rarely useful in real applications. This lesson covers every tool for narrowing and ordering results.

---

### WHERE — Basic Filtering

```sql
-- Equality
SELECT * FROM users WHERE role = 'admin';

-- Inequality
SELECT * FROM users WHERE role <> 'guest';   -- or !=

-- Numeric comparisons
SELECT * FROM orders WHERE total > 100;
SELECT * FROM orders WHERE total BETWEEN 50 AND 200;

-- NULL check (never use = NULL)
SELECT * FROM users WHERE bio IS NULL;
SELECT * FROM users WHERE bio IS NOT NULL;
```

---

### Combining Conditions

```sql
-- AND — both must be true
SELECT * FROM users WHERE role = 'student' AND is_active = 1;

-- OR — either true
SELECT * FROM users WHERE role = 'admin' OR role = 'student';

-- IN — shorthand for multiple OR
SELECT * FROM users WHERE role IN ('admin', 'student');

-- NOT IN
SELECT * FROM users WHERE role NOT IN ('guest');

-- Parentheses matter
SELECT * FROM users
WHERE is_active = 1 AND (role = 'admin' OR role = 'student');
```

---

### LIKE — Pattern Matching

```sql
-- Starts with "ali"
SELECT * FROM users WHERE name LIKE 'ali%';

-- Contains "smith"
SELECT * FROM users WHERE name LIKE '%smith%';

-- Single character wildcard
SELECT * FROM users WHERE name LIKE 'A_i%';   -- Ali, Abi, etc.

-- Case-insensitive by default in utf8mb4
```

For high-performance search across large text, use **FULLTEXT indexes** (covered in Level 4).

---

### ORDER BY — Sorting Results

```sql
-- Ascending (default)
SELECT * FROM users ORDER BY name ASC;

-- Descending
SELECT * FROM users ORDER BY created_at DESC;

-- Multiple columns — sort by role first, then name within each role
SELECT * FROM users ORDER BY role ASC, name ASC;

-- Sort by computed value
SELECT *, YEAR(created_at) AS join_year FROM users ORDER BY join_year DESC;
```

---

### LIMIT & OFFSET — Pagination

```sql
-- First 10 rows
SELECT * FROM users LIMIT 10;

-- Rows 11-20 (page 2, page_size=10)
SELECT * FROM users ORDER BY id LIMIT 10 OFFSET 10;

-- Short form: LIMIT offset, count
SELECT * FROM users LIMIT 10, 10;
```

**Cursor-based pagination (faster at scale):**

```sql
-- Instead of OFFSET (which scans skipped rows):
SELECT * FROM users WHERE id > 100 ORDER BY id LIMIT 10;
```

---

### DISTINCT — Removing Duplicates

```sql
-- Unique roles in the table
SELECT DISTINCT role FROM users;

-- Unique (role, is_active) combinations
SELECT DISTINCT role, is_active FROM users;
```

---

### Useful String & Date Functions in WHERE

```sql
-- String functions
SELECT * FROM users WHERE LOWER(name) = 'alice';
SELECT * FROM users WHERE LENGTH(name) > 10;

-- Date functions
SELECT * FROM users WHERE DATE(created_at) = '2024-06-01';
SELECT * FROM users WHERE YEAR(created_at) = 2024;
SELECT * FROM users WHERE created_at >= NOW() - INTERVAL 7 DAY;
```

---

### Key Takeaways

- Use `IN (...)` instead of multiple OR conditions — cleaner and equally fast
- Always test `LIKE '%search%'` on small datasets first — leading wildcards prevent index use
- Always pair `ORDER BY` with `LIMIT` — without ORDER BY, row order is undefined
- Cursor-based pagination (`WHERE id > last_id`) outperforms `OFFSET` on large tables
- `IS NULL` / `IS NOT NULL` — never `= NULL` which always returns false
MD;
    }

    // ─── Level 2: Medium ──────────────────────────────────────────────────────

    private function l2_1(): string
    {
        return <<<'MD'
## JOINs: INNER, LEFT, RIGHT & Self-Joins

JOINs combine rows from two or more tables based on a related column. Mastering JOINs is the difference between writing real queries and toy queries.

---

### The Data Model (Running Example)

```sql
CREATE TABLE departments (
    id   INT PRIMARY KEY,
    name VARCHAR(100)
);

CREATE TABLE employees (
    id            INT PRIMARY KEY,
    name          VARCHAR(100),
    department_id INT,           -- FK to departments.id (can be NULL)
    manager_id    INT            -- FK to employees.id (self-reference)
);
```

---

### INNER JOIN — Only Matching Rows

```sql
SELECT e.name AS employee, d.name AS department
FROM employees e
INNER JOIN departments d ON e.department_id = d.id;
```

- Returns only rows where the join condition is satisfied on **both sides**
- Employees without a department are excluded
- Departments without employees are excluded

---

### LEFT JOIN — All Left Rows + Matching Right

```sql
SELECT e.name, d.name AS department
FROM employees e
LEFT JOIN departments d ON e.department_id = d.id;
```

- Every employee is returned, even if `department_id` is NULL
- `d.name` will be NULL for unmatched employees
- Use this to find employees **without** a department:

```sql
SELECT e.name
FROM employees e
LEFT JOIN departments d ON e.department_id = d.id
WHERE d.id IS NULL;
```

---

### RIGHT JOIN — All Right Rows + Matching Left

```sql
SELECT e.name, d.name AS department
FROM employees e
RIGHT JOIN departments d ON e.department_id = d.id;
```

- Every department is returned, even if no employees are assigned
- Rarely used in practice — rewrite as a LEFT JOIN with table order swapped

---

### FULL OUTER JOIN (MySQL workaround)

MySQL does not have FULL OUTER JOIN. Use UNION:

```sql
SELECT e.name, d.name FROM employees e LEFT  JOIN departments d ON e.department_id = d.id
UNION
SELECT e.name, d.name FROM employees e RIGHT JOIN departments d ON e.department_id = d.id;
```

---

### CROSS JOIN — Every Combination

```sql
SELECT e.name, d.name
FROM employees e
CROSS JOIN departments d;
```

Returns m × n rows. Useful for generating combinations (e.g., sizes × colors), almost never for regular data queries.

---

### Self-Join — Joining a Table to Itself

```sql
-- Each employee with their manager's name
SELECT e.name AS employee, m.name AS manager
FROM employees e
LEFT JOIN employees m ON e.manager_id = m.id;
```

Self-joins are how you traverse hierarchical data (org charts, category trees) in SQL.

---

### Multi-Table JOIN

```sql
SELECT o.id, u.name AS customer, p.title AS product
FROM orders o
INNER JOIN users    u ON o.user_id    = u.id
INNER JOIN products p ON o.product_id = p.id
WHERE o.created_at >= '2024-01-01';
```

---

### JOIN on Multiple Conditions

```sql
SELECT *
FROM order_items oi
INNER JOIN price_history ph
    ON oi.product_id = ph.product_id
    AND oi.ordered_at BETWEEN ph.valid_from AND ph.valid_to;
```

---

### Key Takeaways

- `INNER JOIN` = intersection; `LEFT JOIN` = left table wins
- Unmatched LEFT JOIN rows produce NULLs on the right side — use `WHERE right.id IS NULL` to find orphans
- Always alias tables in multi-join queries for readability
- Self-join is the pattern for hierarchical data (manager/employee, category/subcategory)
- MySQL has no FULL OUTER JOIN — use `LEFT JOIN UNION RIGHT JOIN`
MD;
    }

    private function l2_2(): string
    {
        return <<<'MD'
## Aggregation: GROUP BY, HAVING & Aggregate Functions

Aggregation reduces many rows into summary values — totals, averages, counts. It's essential for analytics and reporting queries.

---

### Aggregate Functions

| Function | Description |
|---|---|
| `COUNT(*)` | Total rows in group (including NULLs) |
| `COUNT(col)` | Non-NULL values in column |
| `COUNT(DISTINCT col)` | Unique non-NULL values |
| `SUM(col)` | Total of numeric column |
| `AVG(col)` | Average (ignores NULLs) |
| `MIN(col)` | Smallest value |
| `MAX(col)` | Largest value |
| `GROUP_CONCAT(col)` | Concatenates values into a string |

```sql
SELECT
    COUNT(*)                    AS total_orders,
    COUNT(DISTINCT user_id)     AS unique_customers,
    SUM(total)                  AS revenue,
    AVG(total)                  AS avg_order,
    MIN(total)                  AS min_order,
    MAX(total)                  AS max_order
FROM orders;
```

---

### GROUP BY — Aggregating Per Category

```sql
-- Revenue per user
SELECT user_id, COUNT(*) AS orders, SUM(total) AS spent
FROM orders
GROUP BY user_id;

-- Orders per day
SELECT DATE(created_at) AS day, COUNT(*) AS orders
FROM orders
GROUP BY DATE(created_at)
ORDER BY day;
```

> Every column in SELECT that is NOT inside an aggregate function MUST appear in GROUP BY.

---

### HAVING — Filtering Aggregated Groups

`HAVING` filters **after** aggregation. `WHERE` filters **before**.

```sql
-- Only users who spent more than 500
SELECT user_id, SUM(total) AS spent
FROM orders
GROUP BY user_id
HAVING spent > 500;

-- Only days with more than 100 orders
SELECT DATE(created_at) AS day, COUNT(*) AS orders
FROM orders
GROUP BY day
HAVING orders > 100;
```

**WHERE vs HAVING:**

```sql
-- WHERE: filter rows before grouping
SELECT category, SUM(revenue)
FROM products
WHERE is_active = 1          -- filters rows first
GROUP BY category
HAVING SUM(revenue) > 10000; -- then filters groups
```

---

### GROUP BY Multiple Columns

```sql
-- Revenue breakdown by (year, category)
SELECT
    YEAR(created_at) AS yr,
    category,
    SUM(total) AS revenue
FROM orders
GROUP BY YEAR(created_at), category
ORDER BY yr, revenue DESC;
```

---

### GROUP_CONCAT — Aggregating Strings

```sql
SELECT department_id,
       GROUP_CONCAT(name ORDER BY name SEPARATOR ', ') AS members
FROM employees
GROUP BY department_id;
-- Result: department 1 → "Alice, Bob, Carol"
```

---

### WITH ROLLUP — Subtotals

```sql
SELECT region, category, SUM(revenue)
FROM sales
GROUP BY region, category WITH ROLLUP;
-- Adds a subtotal row for each region, plus a grand total at the end
```

---

### Query Execution Order (mental model)

```
FROM → JOIN → WHERE → GROUP BY → HAVING → SELECT → ORDER BY → LIMIT
```

This is why:
- `WHERE` cannot reference aliases from `SELECT` (not evaluated yet)
- `HAVING` can reference aliases (evaluated after SELECT in MySQL — a common extension)

---

### Key Takeaways

- `COUNT(*)` counts rows; `COUNT(col)` skips NULLs — know the difference
- `WHERE` filters rows before grouping; `HAVING` filters the groups after
- Every non-aggregate SELECT column must be in GROUP BY (in strict SQL mode)
- `GROUP_CONCAT` is MySQL-specific — great for building comma-separated lists
- Remember the execution order: WHERE runs before GROUP BY
MD;
    }

    private function l2_3(): string
    {
        return <<<'MD'
## Subqueries, Constraints & Basic Indexing

---

### Subqueries

A subquery is a SELECT inside another SQL statement. Use them to filter, compute, or transform before the outer query sees the data.

**Subquery in WHERE:**

```sql
-- Users who placed at least one order
SELECT * FROM users
WHERE id IN (SELECT DISTINCT user_id FROM orders);

-- Users who have NEVER ordered (anti-join via subquery)
SELECT * FROM users
WHERE id NOT IN (SELECT DISTINCT user_id FROM orders WHERE user_id IS NOT NULL);
```

**Correlated subquery** — references the outer query per row:

```sql
-- Each order with the user's total spend
SELECT o.id, o.total,
    (SELECT SUM(total) FROM orders o2 WHERE o2.user_id = o.user_id) AS user_lifetime
FROM orders o;
```

> Correlated subqueries run once per outer row — can be slow on large tables. Prefer a JOIN or a CTE when possible.

**EXISTS — more efficient than IN for large sets:**

```sql
SELECT * FROM users u
WHERE EXISTS (
    SELECT 1 FROM orders o WHERE o.user_id = u.id AND o.total > 500
);
```

`EXISTS` stops scanning as soon as it finds one match.

**Subquery in FROM (derived table):**

```sql
SELECT dept, avg_salary
FROM (
    SELECT department_id AS dept, AVG(salary) AS avg_salary
    FROM employees
    GROUP BY department_id
) AS dept_averages
WHERE avg_salary > 60000;
```

---

### Table Constraints

Constraints enforce data integrity at the database level — not just in application code.

```sql
CREATE TABLE products (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sku         VARCHAR(50)    NOT NULL UNIQUE,
    name        VARCHAR(200)   NOT NULL,
    price       DECIMAL(10,2)  NOT NULL CHECK (price >= 0),
    stock       INT            NOT NULL DEFAULT 0 CHECK (stock >= 0),
    category_id INT UNSIGNED,
    FOREIGN KEY (category_id) REFERENCES categories(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);
```

| Constraint | Purpose |
|---|---|
| `PRIMARY KEY` | Unique identifier; implies NOT NULL + UNIQUE + index |
| `UNIQUE` | No duplicate values in the column |
| `NOT NULL` | Value is required |
| `DEFAULT` | Value used when column is omitted from INSERT |
| `CHECK` | Row-level rule (MySQL 8.0.16+) |
| `FOREIGN KEY` | Referential integrity to another table |

**ON DELETE / ON UPDATE options:**

| Action | Effect |
|---|---|
| `CASCADE` | Propagate the change to child rows |
| `SET NULL` | Set FK column to NULL in child rows |
| `RESTRICT` | Block the parent change if children exist |
| `NO ACTION` | Same as RESTRICT in MySQL |

---

### Basic Indexing

An index is a data structure (B-tree by default) that lets MySQL find rows without scanning the entire table.

```sql
-- Single-column index
CREATE INDEX idx_users_email ON users(email);

-- Composite index (order matters — left-prefix rule applies)
CREATE INDEX idx_orders_user_date ON orders(user_id, created_at);

-- Unique index
CREATE UNIQUE INDEX idx_products_sku ON products(sku);

-- View indexes on a table
SHOW INDEX FROM users;

-- Drop an index
DROP INDEX idx_users_email ON users;
```

**When does MySQL use an index?**

- Columns in WHERE, JOIN ON, ORDER BY, GROUP BY
- The leftmost columns of a composite index must be present

**When does MySQL NOT use an index?**

- `WHERE LOWER(email) = ...` — function on indexed column bypasses index
- `WHERE email LIKE '%@gmail.com'` — leading wildcard skips index
- Low-cardinality columns (e.g., `is_active` with only 0/1) — full scan is faster

---

### Key Takeaways

- `IN (subquery)` is readable; `EXISTS` is faster when the subquery returns many rows
- Correlated subqueries are powerful but expensive — rewrite as JOINs where possible
- FOREIGN KEY + ON DELETE CASCADE keeps referential integrity automatic
- Index the columns you filter and join on — but don't over-index (writes get slower)
- Functions on indexed columns (`LOWER(email)`) disable index use — use function-based indexes or store a normalised column
MD;
    }

    // ─── Level 3: Hard ────────────────────────────────────────────────────────

    private function l3_1(): string
    {
        return <<<'MD'
## Transactions & ACID: Data Integrity Under Concurrency

When multiple operations must succeed or fail together, and multiple users hit the database simultaneously, transactions and isolation levels are the tools you need.

---

### What Is a Transaction?

A transaction groups SQL statements into an all-or-nothing unit. Either every statement commits, or none of them do.

```sql
START TRANSACTION;

UPDATE accounts SET balance = balance - 200 WHERE id = 1;  -- debit
UPDATE accounts SET balance = balance + 200 WHERE id = 2;  -- credit

COMMIT;   -- make both permanent
-- or ROLLBACK; -- undo both if something failed
```

Without a transaction, a crash between the two UPDATEs would leave money gone from account 1 but never arriving in account 2.

---

### ACID Properties

| Property | Meaning |
|---|---|
| **Atomicity** | All statements succeed, or none do |
| **Consistency** | Database moves from one valid state to another — constraints always satisfied |
| **Isolation** | Concurrent transactions don't interfere with each other |
| **Durability** | Once committed, data survives crashes (written to disk via redo log) |

---

### SAVEPOINT — Partial Rollback

```sql
START TRANSACTION;

INSERT INTO orders (user_id, total) VALUES (1, 100);
SAVEPOINT after_order;

INSERT INTO order_items (order_id, product_id) VALUES (LAST_INSERT_ID(), 99);
-- If product 99 doesn't exist (FK violation):
ROLLBACK TO SAVEPOINT after_order;  -- keeps the order row, undoes the item

COMMIT;
```

---

### Isolation Levels

Isolation controls what a transaction can see from other concurrent transactions.

```sql
SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;
```

| Level | Dirty Read | Non-Repeatable Read | Phantom Read |
|---|---|---|---|
| READ UNCOMMITTED | Yes | Yes | Yes |
| READ COMMITTED | No | Yes | Yes |
| **REPEATABLE READ** (MySQL default) | No | No | Yes* |
| SERIALIZABLE | No | No | No |

*InnoDB's REPEATABLE READ also prevents most phantom reads via gap locks.

**Concurrency anomalies explained:**

- **Dirty read** — reading uncommitted changes from another transaction
- **Non-repeatable read** — reading the same row twice and getting different values (another tx committed between reads)
- **Phantom read** — running the same range query twice and getting different row counts (another tx inserted/deleted rows)

---

### Autocommit Mode

By default, MySQL runs each statement in its own implicit transaction:

```sql
SET autocommit = 0;  -- disable for the session
-- now every statement is part of a manual transaction
-- must COMMIT or ROLLBACK explicitly
SET autocommit = 1;  -- re-enable (default)
```

---

### Locking

InnoDB uses **row-level locking** by default, which allows high concurrency.

```sql
-- Shared lock: others can read but not modify
SELECT * FROM accounts WHERE id = 1 LOCK IN SHARE MODE;

-- Exclusive lock: no other reader or writer
SELECT * FROM accounts WHERE id = 1 FOR UPDATE;
```

`FOR UPDATE` is the pattern for "read-then-update" to prevent lost updates.

**Deadlock** — two transactions each waiting for the other's lock:

```
Tx1: locks row A, wants row B
Tx2: locks row B, wants row A
```

InnoDB detects this, kills the transaction with less undo data, and returns `ERROR 1213: Deadlock found`. Always handle deadlock errors in application code with a retry.

---

### Key Takeaways

- Wrap multi-step writes in a transaction — never leave the database in a half-written state
- MySQL default isolation is REPEATABLE READ — good balance of safety and performance
- `FOR UPDATE` prevents lost updates in read-modify-write patterns
- Deadlocks are automatic in InnoDB — always retry the transaction on error 1213
- `SAVEPOINT` allows partial rollback within a transaction
MD;
    }

    private function l3_2(): string
    {
        return <<<'MD'
## Stored Procedures, Functions & Triggers

These server-side objects let you encapsulate logic in the database — useful for complex business rules, audit trails, and batch processing.

---

### Stored Procedures

A procedure is a named block of SQL you can call by name.

```sql
DELIMITER //

CREATE PROCEDURE transfer_funds(
    IN  from_account INT,
    IN  to_account   INT,
    IN  amount       DECIMAL(10,2),
    OUT success      TINYINT
)
BEGIN
    DECLARE current_balance DECIMAL(10,2);

    START TRANSACTION;

    SELECT balance INTO current_balance
    FROM accounts WHERE id = from_account FOR UPDATE;

    IF current_balance < amount THEN
        SET success = 0;
        ROLLBACK;
    ELSE
        UPDATE accounts SET balance = balance - amount WHERE id = from_account;
        UPDATE accounts SET balance = balance + amount WHERE id = to_account;
        SET success = 1;
        COMMIT;
    END IF;
END //

DELIMITER ;
```

**Calling the procedure:**

```sql
CALL transfer_funds(1, 2, 200.00, @ok);
SELECT @ok;   -- 1 = success, 0 = insufficient funds
```

**Parameter modes:**

| Mode | Direction |
|---|---|
| `IN` | Caller passes a value in (read-only inside proc) |
| `OUT` | Procedure writes a value back to the caller |
| `INOUT` | Both read and write |

---

### Stored Functions

Functions return a single value and can be used inside SELECT.

```sql
DELIMITER //

CREATE FUNCTION full_name(first_name VARCHAR(100), last_name VARCHAR(100))
RETURNS VARCHAR(205)
DETERMINISTIC
BEGIN
    RETURN CONCAT(first_name, ' ', last_name);
END //

DELIMITER ;

-- Usage
SELECT full_name(first_name, last_name) AS name FROM employees;
```

`DETERMINISTIC` — same inputs always produce same output (enables caching and safe replication).

---

### Triggers

A trigger fires automatically when a DML event occurs on a table.

```sql
DELIMITER //

-- Audit: record every salary change
CREATE TRIGGER trg_salary_audit
AFTER UPDATE ON employees
FOR EACH ROW
BEGIN
    IF OLD.salary <> NEW.salary THEN
        INSERT INTO salary_audit (employee_id, old_salary, new_salary, changed_at)
        VALUES (OLD.id, OLD.salary, NEW.salary, NOW());
    END IF;
END //

DELIMITER ;
```

**Trigger timing & events:**

| | BEFORE | AFTER |
|---|---|---|
| INSERT | Can modify NEW values | Cannot modify NEW |
| UPDATE | Can modify NEW values | Cannot modify NEW |
| DELETE | Can read OLD values | Can read OLD values |

**Common trigger use cases:**
- Audit trails (log changes to a history table)
- Enforcing business rules that CHECK constraints can't express
- Maintaining denormalised columns automatically

**Caution:** Triggers are invisible to application developers — use them sparingly and document them clearly.

---

### Control Flow Inside Procedures

```sql
-- IF / ELSEIF / ELSE
IF score >= 90 THEN
    SET grade = 'A';
ELSEIF score >= 75 THEN
    SET grade = 'B';
ELSE
    SET grade = 'C';
END IF;

-- WHILE loop
WHILE counter < 10 DO
    SET counter = counter + 1;
END WHILE;

-- LOOP with LEAVE
process_loop: LOOP
    -- do work
    IF done THEN LEAVE process_loop; END IF;
END LOOP;
```

---

### Managing Procedures, Functions, Triggers

```sql
-- List
SHOW PROCEDURE STATUS WHERE db = 'mydb';
SHOW FUNCTION STATUS WHERE db = 'mydb';
SHOW TRIGGERS FROM mydb;

-- View source
SHOW CREATE PROCEDURE transfer_funds;

-- Drop
DROP PROCEDURE IF EXISTS transfer_funds;
DROP FUNCTION  IF EXISTS full_name;
DROP TRIGGER   IF EXISTS trg_salary_audit;
```

---

### Key Takeaways

- Stored procedures are callable units; stored functions return a value and are usable in SELECT
- `DETERMINISTIC` is required for functions used in replication — always add it when inputs → outputs are 1:1
- Triggers are powerful for auditing but invisible to callers — document every trigger and keep logic simple
- `OLD` = row before change; `NEW` = row after change (in BEFORE triggers, NEW values are modifiable)
- Change `DELIMITER //` before any multi-statement block — the default `;` terminates your procedure early
MD;
    }

    private function l3_3(): string
    {
        return <<<'MD'
## Views, CTEs & Window Functions

These features let you write complex analytical queries cleanly and efficiently.

---

### Views

A view is a saved SELECT query that acts like a virtual table.

```sql
CREATE VIEW active_students AS
SELECT id, name, email, created_at
FROM users
WHERE role = 'student' AND is_active = 1;

-- Use like a table
SELECT * FROM active_students WHERE name LIKE 'A%';
```

**Benefits:**
- Simplify complex queries (define once, use everywhere)
- Provide a security layer (expose only certain columns/rows)
- Logical abstraction — rename or hide underlying schema details

**Updatable views:** If the view maps 1:1 to a table (no DISTINCT, GROUP BY, subqueries, or UNION), you can INSERT/UPDATE through it.

```sql
UPDATE active_students SET name = 'Alice B.' WHERE id = 1;
```

```sql
-- Replace or drop
CREATE OR REPLACE VIEW active_students AS ...;
DROP VIEW IF EXISTS active_students;
```

---

### Common Table Expressions (CTEs)

A CTE is a named temporary result set scoped to one query.

```sql
WITH high_spenders AS (
    SELECT user_id, SUM(total) AS lifetime
    FROM orders
    GROUP BY user_id
    HAVING lifetime > 1000
)
SELECT u.name, hs.lifetime
FROM users u
JOIN high_spenders hs ON u.id = hs.user_id
ORDER BY hs.lifetime DESC;
```

**Multiple CTEs:**

```sql
WITH
    orders_2024 AS (
        SELECT * FROM orders WHERE YEAR(created_at) = 2024
    ),
    summary AS (
        SELECT user_id, COUNT(*) AS total FROM orders_2024 GROUP BY user_id
    )
SELECT u.name, s.total FROM users u JOIN summary s ON u.id = s.user_id;
```

**Recursive CTE — traversing hierarchies:**

```sql
WITH RECURSIVE org_chart AS (
    -- anchor: start with the CEO
    SELECT id, name, manager_id, 0 AS depth
    FROM employees WHERE manager_id IS NULL

    UNION ALL

    -- recursive: add each employee's reports
    SELECT e.id, e.name, e.manager_id, oc.depth + 1
    FROM employees e
    JOIN org_chart oc ON e.manager_id = oc.id
)
SELECT depth, name FROM org_chart ORDER BY depth, name;
```

---

### Window Functions

Window functions compute a value across a set of related rows **without collapsing them** into a single row (unlike GROUP BY).

```sql
SELECT
    name,
    department_id,
    salary,
    RANK()       OVER (PARTITION BY department_id ORDER BY salary DESC) AS dept_rank,
    DENSE_RANK() OVER (PARTITION BY department_id ORDER BY salary DESC) AS dense_rank,
    ROW_NUMBER() OVER (PARTITION BY department_id ORDER BY salary DESC) AS row_num,
    SUM(salary)  OVER (PARTITION BY department_id)                      AS dept_total,
    AVG(salary)  OVER (PARTITION BY department_id)                      AS dept_avg
FROM employees;
```

**RANK vs DENSE_RANK vs ROW_NUMBER:**

| Scores | RANK | DENSE_RANK | ROW_NUMBER |
|---|---|---|---|
| 100, 100, 80 | 1, 1, 3 | 1, 1, 2 | 1, 2, 3 |

**LAG & LEAD — compare adjacent rows:**

```sql
SELECT
    month,
    revenue,
    LAG(revenue)  OVER (ORDER BY month) AS prev_month,
    LEAD(revenue) OVER (ORDER BY month) AS next_month,
    revenue - LAG(revenue) OVER (ORDER BY month) AS month_over_month_change
FROM monthly_revenue;
```

**NTILE — split into buckets:**

```sql
SELECT name, salary,
    NTILE(4) OVER (ORDER BY salary DESC) AS quartile
FROM employees;
-- 1 = top 25%, 4 = bottom 25%
```

**Running total:**

```sql
SELECT order_date, total,
    SUM(total) OVER (ORDER BY order_date ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS running_total
FROM orders;
```

---

### Key Takeaways

- Views simplify and encapsulate complex queries — use them for repeated analytical patterns
- CTEs replace deeply nested subqueries with readable, named steps
- Recursive CTEs are the SQL way to traverse trees and graphs
- Window functions compute per-row aggregates without GROUP BY — essential for ranking, running totals, and comparisons
- `PARTITION BY` in window functions = GROUP BY equivalent for the window scope
MD;
    }

    // ─── Level 4: Expert ──────────────────────────────────────────────────────

    private function l4_1(): string
    {
        return <<<'MD'
## Query Optimization & the EXPLAIN Statement

Understanding how MySQL executes queries is essential for writing fast database code. The `EXPLAIN` command is your primary diagnostic tool.

---

### How MySQL Executes a Query

1. **Parser** — checks syntax
2. **Preprocessor** — resolves table/column names, checks permissions
3. **Optimizer** — chooses the execution plan (which indexes, join order, etc.)
4. **Executor** — runs the plan and returns results

The optimizer picks the plan it estimates to be cheapest by cost (I/O + CPU). It can be wrong — that's why EXPLAIN exists.

---

### EXPLAIN

```sql
EXPLAIN SELECT u.name, COUNT(o.id) AS orders
FROM users u
LEFT JOIN orders o ON u.id = o.user_id
WHERE u.is_active = 1
GROUP BY u.id;
```

Key columns in the output:

| Column | Meaning |
|---|---|
| `id` | Step number — higher = executed first in subqueries |
| `select_type` | SIMPLE, PRIMARY, SUBQUERY, DERIVED, UNION |
| `table` | Which table this step reads |
| `type` | **Access method** — most important column |
| `possible_keys` | Indexes MySQL considered |
| `key` | Index actually used (NULL = full scan) |
| `key_len` | Bytes of the index used |
| `rows` | Estimated rows to examine |
| `filtered` | % of rows expected to pass WHERE after index |
| `Extra` | Using index, Using filesort, Using temporary, etc. |

---

### The `type` Column — Access Methods (Best to Worst)

| Type | Meaning |
|---|---|
| `system` | Table has exactly one row |
| `const` | PK or unique index lookup — single row guaranteed |
| `eq_ref` | One row per row in outer table — optimal JOIN |
| `ref` | Non-unique index lookup |
| `range` | Index range scan (BETWEEN, IN, >, <) |
| `index` | Full index scan (better than ALL — no heap read) |
| `ALL` | Full table scan — the bad one |

Goal: **avoid `ALL`** on large tables. Aim for `ref`, `eq_ref`, or `const`.

---

### Extra Column Red Flags

- **`Using filesort`** — MySQL sorts in memory/disk because no index covers ORDER BY. Add an index on the ORDER BY columns.
- **`Using temporary`** — MySQL creates a temp table (common with GROUP BY or DISTINCT). Often signals a missing index.
- **`Using index`** — covering index — data read from index alone, no heap access. Very fast.
- **`Using where`** — WHERE filter applied after index scan — fine if `filtered` is high.

---

### EXPLAIN ANALYZE (MySQL 8.0.18+)

```sql
EXPLAIN ANALYZE
SELECT * FROM orders WHERE user_id = 42 ORDER BY created_at DESC LIMIT 10;
```

Returns the actual execution time and row counts alongside the estimates — highlights optimizer miscalculations.

---

### Slow Query Log

```sql
-- Enable
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 1;           -- log queries > 1 second
SET GLOBAL log_queries_not_using_indexes = 'ON';

-- Find slow log location
SHOW VARIABLES LIKE 'slow_query_log_file';
```

Use `mysqldumpslow` or `pt-query-digest` (Percona Toolkit) to aggregate patterns.

---

### Optimizer Hints

```sql
-- Force a specific index
SELECT * FROM orders USE INDEX (idx_user_id) WHERE user_id = 1;
SELECT * FROM orders FORCE INDEX (idx_user_id) WHERE user_id = 1;

-- Ignore an index
SELECT * FROM orders IGNORE INDEX (idx_created_at) WHERE ...;

-- MySQL 8 structured hints
SELECT /*+ NO_RANGE_OPTIMIZATION(orders idx_created_at) */ * FROM orders WHERE ...;
```

---

### Key Takeaways

- `type: ALL` on large tables = always investigate
- `Using filesort` and `Using temporary` are performance warnings — fix with indexes
- `EXPLAIN ANALYZE` gives actual vs estimated rows — use it to spot optimizer bugs
- Enable the slow query log in production; review it weekly
- Trust EXPLAIN output but verify with EXPLAIN ANALYZE — estimates can be wrong
MD;
    }

    private function l4_2(): string
    {
        return <<<'MD'
## Indexing Deep Dive: B-Trees, Composite & Covering Indexes

Indexes are the single biggest lever for MySQL performance. Getting them right separates databases that scale from ones that crawl.

---

### How B-Tree Indexes Work

InnoDB's default index type is a B+ tree:
- Values are stored in sorted order in leaf nodes
- Interior nodes contain only keys (for navigation)
- All leaf nodes are linked — range scans are sequential reads
- Lookup time: O(log n)

**Primary index (clustered index):**
- The table's actual rows are stored in primary key order in the leaf nodes
- If you define `AUTO_INCREMENT PRIMARY KEY`, rows are inserted in monotonically increasing order — excellent for sequential writes

**Secondary indexes:**
- Leaf nodes contain the secondary key value + the primary key
- A secondary index lookup: find PK in secondary index → go back to clustered index to read the row ("double lookup")

---

### Index Selectivity & Cardinality

**Selectivity** = `unique_values / total_rows`. Values closer to 1.0 make better indexes.

```sql
-- Check cardinality (estimated unique values)
SHOW INDEX FROM orders;   -- Cardinality column

-- Calculate selectivity manually
SELECT COUNT(DISTINCT user_id) / COUNT(*) AS selectivity FROM orders;
```

Indexing a `gender` column (2 values, 0.0 selectivity) is counterproductive — MySQL may full-scan instead.

---

### Composite Indexes & the Left-Prefix Rule

```sql
CREATE INDEX idx_user_date ON orders(user_id, created_at, status);
```

This composite index can be used for:
- `WHERE user_id = 1` ✓
- `WHERE user_id = 1 AND created_at > '2024-01-01'` ✓
- `WHERE user_id = 1 AND created_at > '2024-01-01' AND status = 'paid'` ✓
- `WHERE created_at > '2024-01-01'` ✗ (doesn't start at left)
- `WHERE user_id = 1 AND status = 'paid'` — uses user_id only, skips created_at gap

**Rule:** MySQL uses index columns left-to-right until it hits a range condition or a gap.

**Design principle:** Put equality columns first, then range columns.

```sql
-- Query: WHERE user_id = 1 AND status = 'paid' ORDER BY created_at
-- Optimal index:
CREATE INDEX idx ON orders(user_id, status, created_at);
```

---

### Covering Index

A covering index contains **all columns** the query needs — MySQL reads the index alone and never touches the heap.

```sql
-- Query
SELECT user_id, created_at, total FROM orders WHERE user_id = 1;

-- Covering index
CREATE INDEX idx_cover ON orders(user_id, created_at, total);
```

EXPLAIN shows `Using index` — no heap access = much faster.

---

### Invisible Indexes (MySQL 8.0+)

Test the impact of dropping an index without actually dropping it:

```sql
ALTER TABLE orders ALTER INDEX idx_user_date INVISIBLE;
-- Run EXPLAIN — optimizer ignores it but it still updates on writes
ALTER TABLE orders ALTER INDEX idx_user_date VISIBLE;  -- restore
```

---

### Index Maintenance

```sql
-- Find duplicate / redundant indexes
SELECT * FROM sys.schema_redundant_indexes;

-- Unused indexes (never used since last restart)
SELECT * FROM sys.schema_unused_indexes;

-- Rebuild to reclaim fragmented space
OPTIMIZE TABLE orders;

-- Update statistics (forces optimizer to re-evaluate)
ANALYZE TABLE orders;
```

---

### Common Index Anti-Patterns

| Anti-Pattern | Problem |
|---|---|
| Function on indexed column: `WHERE YEAR(created_at) = 2024` | Disables index — use range instead |
| Leading wildcard: `WHERE name LIKE '%smith'` | Can't use B-tree index |
| Low-cardinality index: `WHERE is_active = 1` | Not selective enough |
| Over-indexing: 10+ indexes on one table | Each index slows writes |
| Indexing a column already covered by PK | Redundant |

---

### Key Takeaways

- Primary (clustered) index stores actual row data — keep it narrow (INT, not UUID)
- Composite index: equality columns first, then range, then include
- Covering index eliminates heap lookups — major win for read-heavy queries
- Invisible indexes let you safely test index removal in production
- `sys.schema_unused_indexes` and `schema_redundant_indexes` — audit regularly
MD;
    }

    private function l4_3(): string
    {
        return <<<'MD'
## InnoDB Internals: Buffer Pool, Redo Logs & Deadlock Handling

Understanding InnoDB's internals helps you tune configuration, diagnose issues, and design schemas that work with the engine rather than against it.

---

### InnoDB Architecture Overview

```
Client Query
     ↓
Query Cache (removed in MySQL 8)
     ↓
Buffer Pool (in-memory page cache)
  ├─ Data Pages       (table rows in clustered index order)
  ├─ Index Pages      (B-tree nodes for secondary indexes)
  ├─ Insert Buffer    (deferred secondary index updates)
  └─ Adaptive Hash Index (auto-built for hot pages)
     ↓
Write operations → Redo Log (crash recovery) → Disk flush
```

---

### Buffer Pool

The buffer pool is InnoDB's main memory structure — an LRU cache of 16KB pages.

```sql
-- Check buffer pool size (default: 128MB — far too small for production)
SHOW VARIABLES LIKE 'innodb_buffer_pool_size';

-- Recommended: 60-80% of total RAM on a dedicated DB server
-- innodb_buffer_pool_size = 12G   (in my.cnf)
```

**Buffer pool hit ratio** — should be > 99%:

```sql
SHOW STATUS LIKE 'Innodb_buffer_pool_read%';
-- innodb_buffer_pool_read_requests = total reads
-- innodb_buffer_pool_reads         = physical disk reads
-- hit_ratio = 1 - (reads / read_requests)
```

If hit ratio is low, increase buffer pool size or optimize queries to reduce working set.

---

### Redo Log & Write-Ahead Logging

InnoDB uses WAL (Write-Ahead Logging):
1. Write change to redo log (sequential, fast)
2. Confirm commit to client
3. Eventually flush dirty pages from buffer pool to disk (background)

**Key config:**

```ini
innodb_log_file_size     = 1G    # larger = fewer checkpoints, better write throughput
innodb_flush_log_at_trx_commit = 1   # 1 = fully durable (ACID), 2 = OS cache, 0 = per-second
```

Setting `innodb_flush_log_at_trx_commit = 2` gives ~10× write performance at the cost of up to 1 second of data loss on OS crash. Use 1 for financial data.

---

### MVCC — Multi-Version Concurrency Control

InnoDB keeps old row versions (in the undo log) so readers don't block writers:

- A SELECT sees the snapshot from when its transaction started (REPEATABLE READ)
- Writers create a new version; readers see the old version
- Undo log is purged by the background purge thread once no active transaction needs it

Long-running transactions prevent undo log cleanup — watch `History list length` in `SHOW ENGINE INNODB STATUS`.

---

### Deadlock Handling

```sql
SHOW ENGINE INNODB STATUS;
-- Look for: LATEST DETECTED DEADLOCK section
```

**Reading a deadlock:**

```
TRANSACTION 1: Waiting for lock on row in table `accounts` WHERE id=2
TRANSACTION 2: Waiting for lock on row in table `accounts` WHERE id=1
```

InnoDB kills the transaction with less undo data. The other succeeds.

**How to avoid deadlocks:**

1. Always lock rows in the same order across all transactions
2. Keep transactions short — acquire locks last, release early
3. Use `NOWAIT` / `SKIP LOCKED` for queue-like patterns (MySQL 8.0+):

```sql
-- Skip rows locked by other transactions (work-queue pattern)
SELECT * FROM jobs WHERE status = 'pending'
ORDER BY id LIMIT 1
FOR UPDATE SKIP LOCKED;
```

---

### Key System Variables to Know

```sql
SHOW VARIABLES LIKE 'innodb%';

-- Critical ones:
-- innodb_buffer_pool_size          (RAM for caching)
-- innodb_log_file_size             (redo log size)
-- innodb_flush_log_at_trx_commit   (durability vs performance)
-- innodb_io_capacity               (IOPS available to background threads)
-- innodb_deadlock_detect           (1 = enabled, 0 = off for extreme throughput)
```

---

### Key Takeaways

- Buffer pool is the most impactful config knob — size it to hold your working set
- WAL makes commits fast; dirty pages flush in the background — that's safe by design
- MVCC means reads don't block writes — but long transactions bloat the undo log
- Deadlocks are normal — always retry on error 1213; design lock order to minimise them
- `SHOW ENGINE INNODB STATUS` is the diagnostic window into InnoDB — learn to read it
MD;
    }

    // ─── Level 5: Architecture ────────────────────────────────────────────────

    private function l5_1(): string
    {
        return <<<'MD'
## Replication: Primary-Replica Setup & GTID

Replication copies data from one MySQL server (primary) to one or more others (replicas), enabling read scaling and fault tolerance.

---

### How Binary Log Replication Works

```
Primary
  └─ Executes write → Records event in binary log (binlog)

Replica
  ├─ IO thread: reads binlog from primary → writes to relay log
  └─ SQL thread: reads relay log → replays events on replica
```

**Binlog formats:**

| Format | What's logged | Pros | Cons |
|---|---|---|---|
| `STATEMENT` | SQL text | Small log size | Non-deterministic functions can diverge |
| `ROW` | Actual row changes | Deterministic, safe | Larger log size |
| `MIXED` | Auto-switches | Balance | Complexity |

Production default: `ROW` or `MIXED`.

---

### Setting Up Replication (Overview)

**Primary (my.cnf):**

```ini
[mysqld]
server-id       = 1
log-bin         = mysql-bin
binlog-format   = ROW
```

**Replica (my.cnf):**

```ini
[mysqld]
server-id       = 2
relay-log       = relay-bin
read-only       = 1          # prevent accidental writes on replica
```

**On replica:**

```sql
CHANGE REPLICATION SOURCE TO
    SOURCE_HOST     = 'primary-host',
    SOURCE_USER     = 'replication_user',
    SOURCE_PASSWORD = 'secret',
    SOURCE_LOG_FILE = 'mysql-bin.000001',
    SOURCE_LOG_POS  = 4;

START REPLICA;
SHOW REPLICA STATUS\G   -- check Seconds_Behind_Source
```

---

### GTID — Global Transaction Identifiers

GTID assigns a unique ID to every committed transaction, globally across all servers.

```ini
[mysqld]
gtid-mode       = ON
enforce-gtid-consistency = ON
```

**GTID format:** `server_uuid:transaction_id`
Example: `3E11FA47-71CA-11E1-9E33-C80AA9429562:1-100`

**Benefits of GTID:**
- Failover is trivial — replica knows exactly where to resume from new primary
- No more binary log file + position coordinates
- Easier to detect replication drift

```sql
-- Point replica to new primary with GTID:
CHANGE REPLICATION SOURCE TO SOURCE_AUTO_POSITION = 1;
```

---

### Replication Lag

`Seconds_Behind_Source` in `SHOW REPLICA STATUS` measures replica lag.

Common causes:
- Long-running queries on primary that take time to replay on replica
- Heavy write load that the single SQL thread can't keep up with
- Network latency

**Fixes:**
- Enable **multi-threaded replication** (`replica_parallel_workers > 1`)
- Use `replica_parallel_type = LOGICAL_CLOCK` (MySQL 5.7+) for parallel replay
- Offload long analytics queries to replica so they don't compete with replay

---

### Semi-Synchronous Replication

Default replication is **asynchronous** — primary commits before replica acknowledges. Data can be lost if primary crashes.

**Semi-sync:** Primary waits for at least one replica to acknowledge before committing.

```sql
INSTALL PLUGIN rpl_semi_sync_source SONAME 'semisync_source.so';
SET GLOBAL rpl_semi_sync_source_enabled = 1;
SET GLOBAL rpl_semi_sync_source_timeout = 1000;  -- ms before falling back to async
```

Semi-sync gives you near-zero data loss with < 1ms added latency on LAN.

---

### Key Takeaways

- Replication is asynchronous by default — replicas may lag behind primary
- GTID simplifies failover — use it for any new deployment
- `Seconds_Behind_Source = 0` is the health metric to monitor
- Semi-sync replication dramatically reduces data loss risk without full sync overhead
- Replicas are read-only by design — use `read_only = 1` in config
MD;
    }

    private function l5_2(): string
    {
        return <<<'MD'
## Partitioning, Sharding & Connection Pooling

As data volumes grow, single-server MySQL reaches its limits. Partitioning, sharding, and connection pooling are the tools for scaling.

---

### Table Partitioning

Partitioning splits one logical table into multiple physical storage segments, managed transparently by MySQL.

**RANGE Partitioning — by contiguous value ranges:**

```sql
CREATE TABLE orders (
    id         INT NOT NULL,
    created_at DATE NOT NULL,
    total      DECIMAL(10,2)
) PARTITION BY RANGE (YEAR(created_at)) (
    PARTITION p2022 VALUES LESS THAN (2023),
    PARTITION p2023 VALUES LESS THAN (2024),
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p_future VALUES LESS THAN MAXVALUE
);
```

**LIST Partitioning — by discrete values:**

```sql
PARTITION BY LIST (region_id) (
    PARTITION p_east VALUES IN (1, 2, 3),
    PARTITION p_west VALUES IN (4, 5, 6)
)
```

**HASH Partitioning — evenly distributed:**

```sql
PARTITION BY HASH (user_id) PARTITIONS 8;
```

**KEY Partitioning — hash on primary key:**

```sql
PARTITION BY KEY() PARTITIONS 8;
```

---

### Partition Pruning

MySQL skips partitions that can't contain matching rows — the key performance win.

```sql
-- Only reads p2024 — all other partitions pruned
SELECT * FROM orders WHERE created_at >= '2024-01-01' AND created_at < '2025-01-01';

-- Verify with EXPLAIN PARTITIONS
EXPLAIN PARTITIONS SELECT * FROM orders WHERE YEAR(created_at) = 2024;
```

Pruning only works when the WHERE clause uses the partition column directly.

---

### Managing Partitions

```sql
-- Add a new year partition
ALTER TABLE orders ADD PARTITION (
    PARTITION p2025 VALUES LESS THAN (2026)
);

-- Drop old data instantly (no DELETE scanning)
ALTER TABLE orders DROP PARTITION p2022;

-- Reorganise partitions
ALTER TABLE orders REORGANIZE PARTITION p_future INTO (
    PARTITION p2025 VALUES LESS THAN (2026),
    PARTITION p_future VALUES LESS THAN MAXVALUE
);
```

`DROP PARTITION` is near-instant — much faster than `DELETE WHERE year = 2022`.

---

### Sharding (Application-Level Horizontal Scaling)

Sharding distributes rows across multiple MySQL servers (shards), each holding a subset of the data.

```
Users 1–1M    → Shard 1 (server A)
Users 1M–2M   → Shard 2 (server B)
Users 2M–3M   → Shard 3 (server C)
```

**Range sharding:** Simple but creates hot spots if recent data is heavier.

**Hash sharding:** `shard = hash(user_id) % num_shards` — even distribution but range queries are hard.

**Consistent hashing:** Adding/removing shards only remaps a fraction of keys — used by distributed caches.

**Sharding challenges:**
- Cross-shard JOINs are not possible in SQL — must do in application
- Global unique IDs (use UUID v7, Twitter Snowflake, or a sequence service)
- Schema migrations must run on every shard
- Transactions cannot span shards

Tools: **Vitess** (YouTube's MySQL sharding layer), **PlanetScale** (cloud Vitess).

---

### Connection Pooling

Opening a MySQL connection has overhead (~5ms). Connection pooling maintains a warm pool.

**Application-level pooling (most ORMs):**
- Laravel: `DB_POOL_MAX` in connection config
- Node.js: mysql2 pool options `{ connectionLimit: 10 }`

**Proxy-level pooling with ProxySQL:**

```sql
-- ProxySQL sits between app and MySQL
-- App connects to ProxySQL (port 6033)
-- ProxySQL multiplexes connections to MySQL
-- Can route reads to replicas, writes to primary

-- ProxySQL admin interface (port 6032):
INSERT INTO mysql_servers (hostname, port, hostgroup_id)
VALUES ('primary', 3306, 10), ('replica1', 3306, 20);

-- Route: SELECT → hostgroup 20 (replicas), others → hostgroup 10
INSERT INTO mysql_query_rules (rule_id, match_pattern, destination_hostgroup)
VALUES (1, '^SELECT', 20);
```

ProxySQL benefits: read/write splitting, connection multiplexing (1000 app connections → 50 MySQL connections), failover, query caching.

---

### Key Takeaways

- Partitioning is transparent to queries — pruning provides the speedup; pair with partition column in WHERE
- `DROP PARTITION` is the fastest way to archive/delete large chunks of data
- Sharding scales writes horizontally but introduces cross-shard complexity — avoid unless truly needed
- Connection pooling at the proxy level (ProxySQL) is more powerful than app-level pools
- Vitess handles sharding, connection pooling, and schema migrations at scale
MD;
    }

    private function l5_3(): string
    {
        return <<<'MD'
## High Availability: Group Replication, ProxySQL & Backups

Production MySQL must survive server failures without downtime. This lesson covers the key HA patterns and backup strategies.

---

### Failure Modes to Protect Against

| Failure | Impact without HA | Solution |
|---|---|---|
| OS/hardware crash | Data loss, minutes of downtime | Semi-sync replication + automated failover |
| Disk full | Writes fail | Monitoring + ProxySQL circuit breaking |
| Primary failure | All writes stop | Automatic failover to replica |
| Replica lag | Stale reads | Read-only queries wait or use primary |
| Datacenter outage | Total loss | Cross-DC replication |

---

### MySQL Group Replication

Group Replication is MySQL's built-in multi-primary or single-primary HA cluster.

```sql
-- All nodes in the group see the same data
-- Consensus-based commit (Paxos-like protocol)
-- Automatic failover — no external orchestrator needed

-- Enable on each node (my.cnf):
plugin-load-add = group_replication.so
group_replication_group_name = "aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa"
group_replication_start_on_boot = ON
group_replication_local_address = "node1:33061"
group_replication_group_seeds = "node1:33061,node2:33061,node3:33061"
```

```sql
-- Bootstrap the first node
SET GLOBAL group_replication_bootstrap_group = ON;
START GROUP_REPLICATION;
SET GLOBAL group_replication_bootstrap_group = OFF;

-- Check group status
SELECT * FROM performance_schema.replication_group_members;
```

**Minimum 3 nodes** — quorum requires majority. Two nodes = split brain risk.

---

### InnoDB Cluster

MySQL's official HA stack:

```
InnoDB Cluster = Group Replication + MySQL Router + MySQL Shell
```

- **Group Replication** — synchronised multi-node state
- **MySQL Router** — routes app traffic to the primary; reroutes on failover
- **MySQL Shell** — AdminAPI for cluster management

```js
// MySQL Shell setup
var cluster = dba.createCluster('prodCluster');
cluster.addInstance('root@node2:3306');
cluster.addInstance('root@node3:3306');
cluster.status();
```

---

### External Failover with Orchestrator

For standard primary-replica setups, **Orchestrator** (open source, GitHub) provides:
- Topology visualisation
- Automatic promotion of best replica on primary failure
- GTID-aware failover
- Anti-split-brain protection

Alternative: **Percona XtraDB Cluster** (fork of Galera Cluster) for Galera-based multi-primary.

---

### Backup Strategies

**mysqldump — logical backup:**

```bash
# Full backup
mysqldump -u root -p --all-databases --single-transaction > full_backup.sql

# Restore
mysql -u root -p < full_backup.sql
```

- `--single-transaction` takes a consistent snapshot for InnoDB without locking tables
- Slow for large databases (100GB+ takes hours)

**Percona XtraBackup — physical backup:**

```bash
# Faster, hot backup (no downtime), incremental support
xtrabackup --backup --target-dir=/backup/full

# Incremental
xtrabackup --backup --incremental-basedir=/backup/full --target-dir=/backup/inc1

# Restore
xtrabackup --prepare --apply-log-only --target-dir=/backup/full
xtrabackup --copy-back --target-dir=/backup/full
```

**Point-in-Time Recovery (PITR):**

```bash
# Restore from last full backup, then replay binlog up to the failure point
mysqlbinlog --start-datetime="2024-06-01 00:00:00" \
            --stop-datetime="2024-06-01 14:30:00" \
            /var/lib/mysql/mysql-bin.* | mysql -u root -p
```

---

### Backup Best Practices

| Practice | Why |
|---|---|
| Test restores regularly | A backup you've never restored is not a backup |
| Store backups off-site | Protects against datacenter failure |
| Retain binlogs | Required for PITR |
| Encrypt backups | Compliance + security |
| Monitor backup age | Detect failed backup jobs early |
| Automate with `cron` + alerting | Manual backups get forgotten |

---

### Key Takeaways

- Group Replication / InnoDB Cluster provides automatic failover with no external tools
- Always run ≥ 3 nodes in a replication group to maintain quorum
- ProxySQL handles read/write splitting and transparent failover at the connection layer
- XtraBackup > mysqldump for large databases — hot backup, incremental, fast
- Binlog retention is required for PITR — never purge binlogs without checking replicas
MD;
    }

    // ─── L4 Assessment Questions ──────────────────────────────────────────────

    private function seedLevel4Questions(Topic $topic): void
    {
        $questions = [
            [
                'question'    => 'In MySQL EXPLAIN output, which `type` value indicates a full table scan?',
                'explanation' => '`ALL` means MySQL reads every row in the table. It is the worst access method and should be avoided on large tables by adding appropriate indexes.',
                'options'     => [
                    ['text' => 'ALL',   'correct' => true],
                    ['text' => 'index', 'correct' => false],
                    ['text' => 'ref',   'correct' => false],
                    ['text' => 'range', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does "Using filesort" in the EXPLAIN Extra column indicate?',
                'explanation' => '"Using filesort" means MySQL cannot satisfy ORDER BY using an index — it must sort the result set in a separate pass, either in memory or on disk. Adding an index on the ORDER BY columns eliminates this.',
                'options'     => [
                    ['text' => 'MySQL is performing a separate sort pass because no index covers the ORDER BY', 'correct' => true],
                    ['text' => 'MySQL is writing query results to a file on disk',                              'correct' => false],
                    ['text' => 'MySQL is using a file-based storage engine',                                   'correct' => false],
                    ['text' => 'MySQL cannot use any indexes for this query',                                  'correct' => false],
                ],
            ],
            [
                'question'    => 'Given a composite index on (user_id, status, created_at), which WHERE clause can use all three index columns?',
                'explanation' => 'The left-prefix rule requires equality on the leading columns before a range. WHERE user_id = 1 AND status = \'paid\' AND created_at > \'2024-01-01\' uses equality on user_id and status, then a range on created_at — all three columns are used.',
                'options'     => [
                    ['text' => 'WHERE user_id = 1 AND status = \'paid\' AND created_at > \'2024-01-01\'', 'correct' => true],
                    ['text' => 'WHERE status = \'paid\' AND created_at > \'2024-01-01\'',                  'correct' => false],
                    ['text' => 'WHERE created_at > \'2024-01-01\'',                                        'correct' => false],
                    ['text' => 'WHERE user_id = 1 AND created_at > \'2024-01-01\'',                        'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a covering index?',
                'explanation' => 'A covering index includes all the columns a query needs (SELECT, WHERE, ORDER BY) so MySQL can satisfy the query entirely from the index without reading the table rows. EXPLAIN shows "Using index".',
                'options'     => [
                    ['text' => 'An index that contains all columns needed by a query, eliminating heap lookups', 'correct' => true],
                    ['text' => 'An index that covers the entire table',                                          'correct' => false],
                    ['text' => 'A PRIMARY KEY index',                                                           'correct' => false],
                    ['text' => 'An index used with LIKE queries',                                                'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `innodb_buffer_pool_size` control?',
                'explanation' => 'The buffer pool is InnoDB\'s main in-memory cache for data and index pages. A larger buffer pool means fewer disk reads. On a dedicated DB server, set it to 60-80% of total RAM.',
                'options'     => [
                    ['text' => 'The amount of RAM allocated to cache data and index pages',         'correct' => true],
                    ['text' => 'The maximum number of simultaneous connections',                    'correct' => false],
                    ['text' => 'The size of the redo log files',                                    'correct' => false],
                    ['text' => 'The maximum size of a single query result set',                     'correct' => false],
                ],
            ],
            [
                'question'    => 'Why should you avoid using a function on an indexed column in a WHERE clause?',
                'explanation' => 'Functions like LOWER(email) or YEAR(created_at) prevent MySQL from using the B-tree index because the index is built on the raw column value. Use range queries or function-based indexes instead.',
                'options'     => [
                    ['text' => 'It prevents the index from being used, forcing a full table scan',  'correct' => true],
                    ['text' => 'It causes a syntax error in MySQL 8',                               'correct' => false],
                    ['text' => 'It produces incorrect results due to collation',                    'correct' => false],
                    ['text' => 'It forces MySQL to use a hash join',                                'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of `EXPLAIN ANALYZE` (available in MySQL 8.0.18+)?',
                'explanation' => 'EXPLAIN ANALYZE actually executes the query and returns both the estimated plan and the actual execution statistics (rows, time). This exposes cases where the optimizer\'s estimates are wrong.',
                'options'     => [
                    ['text' => 'It executes the query and returns actual row counts and timings alongside estimates', 'correct' => true],
                    ['text' => 'It shows only the estimated execution plan without running the query',               'correct' => false],
                    ['text' => 'It automatically adds indexes based on the query',                                    'correct' => false],
                    ['text' => 'It writes query statistics to the slow query log',                                    'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `innodb_flush_log_at_trx_commit = 2` trade off compared to the default value of 1?',
                'explanation' => 'Value 1 = flush to disk on every commit (fully durable, ACID compliant). Value 2 = write to OS cache on commit, flush to disk every second. Value 2 gives ~10× write throughput at the cost of up to 1 second of data loss on OS crash (not MySQL crash).',
                'options'     => [
                    ['text' => 'Higher write throughput in exchange for up to 1 second of data loss on OS crash', 'correct' => true],
                    ['text' => 'Better read performance but slower writes',                                        'correct' => false],
                    ['text' => 'Reduced redo log size',                                                           'correct' => false],
                    ['text' => 'Disables MVCC for faster single-threaded writes',                                  'correct' => false],
                ],
            ],
            [
                'question'    => 'What is index cardinality in MySQL?',
                'explanation' => 'Cardinality is the estimated number of unique values in an index. High cardinality (close to the total row count) means the index is selective — each lookup returns very few rows. Low cardinality (e.g. a boolean column) means the index is not worth using.',
                'options'     => [
                    ['text' => 'The estimated number of unique values in an index column',         'correct' => true],
                    ['text' => 'The total number of rows in the indexed table',                    'correct' => false],
                    ['text' => 'The number of columns in a composite index',                       'correct' => false],
                    ['text' => 'The physical size of the index on disk',                           'correct' => false],
                ],
            ],
            [
                'question'    => 'Which MySQL 8.0+ feature lets you test index removal safely in production?',
                'explanation' => 'An INVISIBLE index is maintained by MySQL (updated on writes) but ignored by the optimizer. You can flip an index invisible, run your queries to verify performance, then make it visible again — or drop it permanently if query plans remain acceptable.',
                'options'     => [
                    ['text' => 'Invisible indexes (ALTER TABLE ... ALTER INDEX ... INVISIBLE)',     'correct' => true],
                    ['text' => 'Disabled indexes (ALTER TABLE ... DISABLE KEYS)',                  'correct' => false],
                    ['text' => 'Soft-delete indexes via INFORMATION_SCHEMA',                       'correct' => false],
                    ['text' => 'Index hints using IGNORE INDEX',                                   'correct' => false],
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
                'difficulty'  => 'Hard',
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

    // ─── L5 Assessment Questions ──────────────────────────────────────────────

    private function seedLevel5Questions(Topic $topic): void
    {
        $questions = [
            [
                'question'    => 'What is a GTID in MySQL replication?',
                'explanation' => 'A Global Transaction Identifier (GTID) is a unique ID assigned to every committed transaction, globally across all servers in a replication topology. It is formatted as server_uuid:transaction_id. GTIDs simplify failover because replicas track their position by GTID, not by binary log file and position.',
                'options'     => [
                    ['text' => 'A unique identifier assigned to every committed transaction, enabling position-independent replication', 'correct' => true],
                    ['text' => 'The IP address and port of the group replication primary',                                               'correct' => false],
                    ['text' => 'A hash of the binary log file used for consistency checking',                                           'correct' => false],
                    ['text' => 'A token used for MySQL connection authentication',                                                      'correct' => false],
                ],
            ],
            [
                'question'    => 'In MySQL replication, what is the advantage of ROW binlog format over STATEMENT format?',
                'explanation' => 'ROW format logs the actual before/after values of changed rows, not the SQL statement. This makes replication deterministic — functions like NOW() or UUID() in the original statement produce the same result on the replica because the actual values are replicated, not the function calls.',
                'options'     => [
                    ['text' => 'ROW format is deterministic — it replicates actual row changes, not SQL statements that may behave differently on replicas', 'correct' => true],
                    ['text' => 'ROW format produces smaller binary log files',                                                                              'correct' => false],
                    ['text' => 'ROW format enables cross-version replication between MySQL and MariaDB',                                                    'correct' => false],
                    ['text' => 'ROW format is required for GTID-based replication',                                                                         'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `Seconds_Behind_Source` in `SHOW REPLICA STATUS` measure?',
                'explanation' => 'Seconds_Behind_Source is the estimated lag between the replica\'s SQL thread and the primary — how many seconds behind the primary the replica is in replaying events. A value of 0 means the replica is caught up. High values indicate replication lag.',
                'options'     => [
                    ['text' => 'How many seconds the replica SQL thread lags behind the primary in replaying events', 'correct' => true],
                    ['text' => 'The number of pending binary log events waiting to be applied',                       'correct' => false],
                    ['text' => 'The network round-trip time between primary and replica',                             'correct' => false],
                    ['text' => 'The age of the oldest open transaction on the replica',                               'correct' => false],
                ],
            ],
            [
                'question'    => 'Which MySQL table partitioning type splits data based on contiguous value ranges?',
                'explanation' => 'RANGE partitioning assigns rows to partitions based on whether a column value falls within defined ranges (e.g. YEAR(created_at) < 2024 goes to one partition). It is the most common type for time-series data.',
                'options'     => [
                    ['text' => 'RANGE',  'correct' => true],
                    ['text' => 'HASH',   'correct' => false],
                    ['text' => 'LIST',   'correct' => false],
                    ['text' => 'KEY',    'correct' => false],
                ],
            ],
            [
                'question'    => 'What is partition pruning in MySQL?',
                'explanation' => 'Partition pruning is the optimizer\'s ability to skip partitions that cannot contain rows matching the WHERE clause. For example, WHERE YEAR(created_at) = 2024 on a RANGE-partitioned table reads only the 2024 partition — all others are skipped without reading.',
                'options'     => [
                    ['text' => 'The optimizer skipping partitions that cannot contain matching rows for a query', 'correct' => true],
                    ['text' => 'Automatically deleting old partitions when they exceed a size limit',             'correct' => false],
                    ['text' => 'Merging small partitions to reduce overhead',                                     'correct' => false],
                    ['text' => 'Removing duplicate rows across partitions',                                       'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the primary role of ProxySQL in a MySQL architecture?',
                'explanation' => 'ProxySQL is a high-performance MySQL proxy that sits between the application and MySQL servers. It provides connection pooling (multiplexing thousands of app connections to fewer MySQL connections), read/write splitting (route SELECTs to replicas, writes to primary), and transparent failover.',
                'options'     => [
                    ['text' => 'A proxy that provides connection pooling, read/write splitting, and transparent failover', 'correct' => true],
                    ['text' => 'A monitoring tool that tracks MySQL query performance',                                     'correct' => false],
                    ['text' => 'A backup utility for MySQL databases',                                                     'correct' => false],
                    ['text' => 'An ORM layer that translates SQL to MySQL-specific syntax',                                'correct' => false],
                ],
            ],
            [
                'question'    => 'Why does MySQL Group Replication require a minimum of 3 nodes?',
                'explanation' => 'Group Replication uses a consensus protocol (Paxos) that requires a quorum (majority of nodes) to commit transactions. With 3 nodes, any 2 form a quorum — so one node can fail and the group continues. With only 2 nodes, losing one means no quorum and the group stops.',
                'options'     => [
                    ['text' => 'To maintain quorum — a majority must agree before committing; 2 nodes have no failsafe majority', 'correct' => true],
                    ['text' => 'Because MySQL Group Replication requires one dedicated monitoring node',                            'correct' => false],
                    ['text' => 'To enable multi-primary writes — minimum 3 primaries are required',                                'correct' => false],
                    ['text' => 'Because binary log shipping requires three copies to detect corruption',                           'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the main advantage of Percona XtraBackup over mysqldump for large databases?',
                'explanation' => 'XtraBackup performs a physical (binary) hot backup — it copies InnoDB data files while the server is running, without locking tables or interrupting writes. mysqldump is a logical backup that can take hours on large databases and requires a lock or --single-transaction, which holds a long snapshot.',
                'options'     => [
                    ['text' => 'XtraBackup takes a hot physical backup without locking tables, much faster for large DBs', 'correct' => true],
                    ['text' => 'XtraBackup produces a portable SQL file that is human-readable',                           'correct' => false],
                    ['text' => 'XtraBackup works across different database engines (InnoDB and MyISAM equally)',             'correct' => false],
                    ['text' => 'XtraBackup is built into MySQL and requires no additional installation',                    'correct' => false],
                ],
            ],
            [
                'question'    => 'What does `ALTER TABLE orders DROP PARTITION p2022` do differently from `DELETE FROM orders WHERE YEAR(created_at) = 2022`?',
                'explanation' => 'DROP PARTITION removes the partition\'s underlying data files directly — it is a metadata-only operation, completing nearly instantly regardless of row count. DELETE scans and removes rows one by one, holding undo log entries and potentially taking hours on millions of rows.',
                'options'     => [
                    ['text' => 'DROP PARTITION is near-instant (removes data files); DELETE scans rows one by one and is much slower', 'correct' => true],
                    ['text' => 'DROP PARTITION only marks rows as deleted; DELETE physically removes them',                             'correct' => false],
                    ['text' => 'They are functionally identical — both take the same time',                                            'correct' => false],
                    ['text' => 'DROP PARTITION requires a table lock; DELETE does not',                                                'correct' => false],
                ],
            ],
            [
                'question'    => 'What is Point-in-Time Recovery (PITR) in MySQL?',
                'explanation' => 'PITR lets you restore a database to any specific moment by combining a full backup with the binary logs generated after that backup. You restore the backup, then replay binlog events up to the desired timestamp — essential for recovering from accidental data deletion.',
                'options'     => [
                    ['text' => 'Restoring a database to a specific moment using a full backup plus binary log replay', 'correct' => true],
                    ['text' => 'A scheduled snapshot taken every hour by MySQL Group Replication',                     'correct' => false],
                    ['text' => 'Rolling back a transaction to a specific SAVEPOINT',                                   'correct' => false],
                    ['text' => 'Recovering individual corrupted pages using the redo log',                             'correct' => false],
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
                'difficulty'  => 'Hard',
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
