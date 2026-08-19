<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SqlTheoryLearningSeeder extends Seeder
{
    public function run(): void
    {
        $track = LearningTrack::firstOrCreate(
            ['slug' => 'databases'],
            ['title' => 'Databases', 'description' => 'SQL, NoSQL, database design, and query optimization.']
        );

        $subject = Subject::firstOrCreate(
            ['slug' => 'sql'],
            [
                'learning_track_id' => $track->id,
                'title'             => 'SQL Theory',
                'description'       => 'The complete SQL mental model — from relational foundations to production-grade query design, normalization, and schema evolution.',
                'display_order'     => 2,
            ]
        );

        $topicDefs = [
            ['slug' => 'sql-theory-l1-foundations',  'title' => 'SQL Foundations',               'level' => 1, 'order' => 1],
            ['slug' => 'sql-theory-l2-execution',    'title' => 'Query Execution Model',          'level' => 2, 'order' => 2],
            ['slug' => 'sql-theory-l3-design',       'title' => 'Database Design & Normalization','level' => 3, 'order' => 3],
            ['slug' => 'sql-theory-l4-advanced',     'title' => 'Advanced SQL Concepts',          'level' => 4, 'order' => 4],
            ['slug' => 'sql-theory-l5-production',   'title' => 'Production SQL',                 'level' => 5, 'order' => 5],
        ];

        $topics = [];
        foreach ($topicDefs as $def) {
            $t = Topic::firstOrCreate(
                ['slug' => $def['slug']],
                ['subject_id' => $subject->id, 'title' => $def['title'], 'display_order' => $def['order'], 'level' => $def['level']]
            );
            Topic::where('slug', $def['slug'])->update(['level' => $def['level'], 'subject_id' => $subject->id]);
            $topics[$def['level']] = $t;
        }

        $this->seedLessons($topics);
        $this->seedQuestions($topics);

        $this->command->info('SQL Theory seeder complete — 5 levels, 15 lessons, 50 MCQs.');
    }

    private function seedLessons(array $topics): void
    {
        $all = [
            1 => [
                ['title' => 'What Is SQL? Language Categories: DDL, DML, DCL & TCL',           'content' => $this->l1_1(), 'estimated_minutes' => 18, 'display_order' => 1],
                ['title' => 'The Relational Model: Tables, Keys & Integrity Constraints',       'content' => $this->l1_2(), 'estimated_minutes' => 20, 'display_order' => 2],
                ['title' => 'SQL Data Types: Choosing the Right Type for Every Column',         'content' => $this->l1_3(), 'estimated_minutes' => 18, 'display_order' => 3],
            ],
            2 => [
                ['title' => 'How SQL Actually Executes: Logical Processing Order',              'content' => $this->l2_1(), 'estimated_minutes' => 22, 'display_order' => 1],
                ['title' => 'Relational Algebra: The Theory Behind JOINs',                     'content' => $this->l2_2(), 'estimated_minutes' => 22, 'display_order' => 2],
                ['title' => 'Set Operations: UNION, INTERSECT & EXCEPT',                       'content' => $this->l2_3(), 'estimated_minutes' => 18, 'display_order' => 3],
            ],
            3 => [
                ['title' => 'Normal Forms: 1NF → 2NF → 3NF → BCNF Explained',                 'content' => $this->l3_1(), 'estimated_minutes' => 28, 'display_order' => 1],
                ['title' => 'Entity-Relationship Modelling: Entities, Attributes & Cardinality','content' => $this->l3_2(), 'estimated_minutes' => 25, 'display_order' => 2],
                ['title' => 'Schema Design Patterns: Keys, Indexes & Referential Integrity',   'content' => $this->l3_3(), 'estimated_minutes' => 22, 'display_order' => 3],
            ],
            4 => [
                ['title' => 'Subqueries, CTEs & Derived Tables: When to Use Each',             'content' => $this->l4_1(), 'estimated_minutes' => 25, 'display_order' => 1],
                ['title' => 'Window Functions: Anatomy, Frames & Real-World Patterns',         'content' => $this->l4_2(), 'estimated_minutes' => 25, 'display_order' => 2],
                ['title' => 'Transactions, Isolation Levels & Concurrency Anomalies',          'content' => $this->l4_3(), 'estimated_minutes' => 25, 'display_order' => 3],
            ],
            5 => [
                ['title' => 'SQL Security: Injection, Parameterized Queries & Least Privilege','content' => $this->l5_1(), 'estimated_minutes' => 22, 'display_order' => 1],
                ['title' => 'Query Performance: Indexes, EXPLAIN & Anti-Patterns',             'content' => $this->l5_2(), 'estimated_minutes' => 25, 'display_order' => 2],
                ['title' => 'Schema Evolution: Migrations, Versioning & Zero-Downtime Changes','content' => $this->l5_3(), 'estimated_minutes' => 22, 'display_order' => 3],
            ],
        ];

        foreach ($all as $level => $lessons) {
            $topic = $topics[$level];
            foreach ($lessons as $ld) {
                DB::table('lessons')->updateOrInsert(
                    ['topic_id' => $topic->id, 'title' => $ld['title']],
                    ['content' => $ld['content'], 'estimated_minutes' => $ld['estimated_minutes'], 'display_order' => $ld['display_order'], 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }

    // ── LEVEL 1 LESSONS ───────────────────────────────────────────────────────

    private function l1_1(): string { return <<<'MD'
## What Is SQL? Language Categories: DDL, DML, DCL & TCL

### What Is SQL?

SQL (Structured Query Language) is the standard language for communicating with relational databases. It was standardised by ANSI in 1986 and by ISO in 1987. Almost every database system — MySQL, PostgreSQL, SQLite, Oracle, SQL Server — speaks SQL with minor dialect differences.

SQL is **declarative**: you describe *what* data you want, not *how* to retrieve it. The database engine decides the execution strategy.

---

### The Four Language Categories

SQL is not one uniform language — it is divided into four functional sublanguages.

---

#### 1. DDL — Data Definition Language

DDL defines and modifies the *structure* of the database — schemas, tables, columns, indexes.

```sql
CREATE TABLE users (id INT PRIMARY KEY, name VARCHAR(100));
ALTER TABLE users ADD COLUMN email VARCHAR(150);
DROP TABLE users;
TRUNCATE TABLE users;   -- removes all rows, resets structure
RENAME TABLE users TO app_users;
```

Key property: **DDL statements are auto-committed** in most databases. You cannot roll them back inside a transaction (with some exceptions in PostgreSQL).

---

#### 2. DML — Data Manipulation Language

DML reads and modifies the *data* inside tables.

```sql
SELECT * FROM users WHERE is_active = 1;
INSERT INTO users (name, email) VALUES ('Alice', 'alice@example.com');
UPDATE users SET name = 'Alice B.' WHERE id = 1;
DELETE FROM users WHERE id = 1;
```

DML statements are transactional — they can be rolled back.

---

#### 3. DCL — Data Control Language

DCL manages *permissions* — who can do what.

```sql
GRANT SELECT, INSERT ON users TO 'app_user'@'localhost';
REVOKE INSERT ON users FROM 'app_user'@'localhost';
```

Used by DBAs to enforce the principle of least privilege.

---

#### 4. TCL — Transaction Control Language

TCL manages *transaction boundaries*.

```sql
START TRANSACTION;
UPDATE accounts SET balance = balance - 500 WHERE id = 1;
UPDATE accounts SET balance = balance + 500 WHERE id = 2;
COMMIT;     -- make permanent
-- or ROLLBACK; to undo
SAVEPOINT checkpoint_a;
ROLLBACK TO SAVEPOINT checkpoint_a;
```

---

### SQL Standards vs Dialects

| Feature | SQL Standard | MySQL | PostgreSQL | SQL Server |
|---|---|---|---|---|
| String concat | `\|\|` | `CONCAT()` | Both | `+` |
| Auto-increment | GENERATED ALWAYS | `AUTO_INCREMENT` | `SERIAL` / `IDENTITY` | `IDENTITY` |
| Limit rows | `FETCH FIRST n ROWS` | `LIMIT n` | `LIMIT n` | `TOP n` |
| Full outer join | `FULL OUTER JOIN` | Via `UNION` | Supported | Supported |

When you learn SQL theory, learn the standard first — then the dialect tweaks are small.

---

### Key Takeaways

- DDL = structure (CREATE, ALTER, DROP) — auto-committed, not rollbackable in most DBs
- DML = data (SELECT, INSERT, UPDATE, DELETE) — transactional
- DCL = permissions (GRANT, REVOKE)
- TCL = transactions (COMMIT, ROLLBACK, SAVEPOINT)
- SQL is declarative — you state *what*, the engine decides *how*
MD; }

    private function l1_2(): string { return <<<'MD'
## The Relational Model: Tables, Keys & Integrity Constraints

### The Relational Model

Proposed by Edgar Codd in 1970, the relational model organises data into **relations** (tables). Each relation has:

- **Tuple** — a single row (record)
- **Attribute** — a named column with a domain (allowed values)
- **Relation** — the complete table (set of tuples)

The model guarantees that: every cell holds exactly one value (atomicity), row order is undefined (sets have no order), and each row is uniquely identifiable.

---

### Types of Keys

| Key Type | Definition |
|---|---|
| **Super key** | Any set of attributes that uniquely identifies a row |
| **Candidate key** | A minimal super key (no attribute can be removed and still be unique) |
| **Primary key** | The chosen candidate key — uniquely identifies every row |
| **Alternate key** | A candidate key not chosen as primary key |
| **Foreign key** | An attribute that references the primary key of another table |
| **Composite key** | A key made of two or more columns |
| **Surrogate key** | An artificial key (auto-increment ID) with no business meaning |
| **Natural key** | A key with real-world meaning (email, ISBN, SSN) |

```sql
CREATE TABLE orders (
    id         INT PRIMARY KEY,           -- surrogate primary key
    user_id    INT NOT NULL,
    order_no   VARCHAR(20) UNIQUE,        -- alternate key (natural)
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

---

### Integrity Constraints

Constraints enforce rules that keep data valid at the database level — not just in application code.

**Entity Integrity** — no primary key column can be NULL. Every row must be identifiable.

**Referential Integrity** — a foreign key value must either be NULL or match an existing primary key in the referenced table. You cannot have an orphan row.

**Domain Integrity** — column values must conform to the declared type and constraints (NOT NULL, CHECK, ENUM, DEFAULT).

**User-Defined Integrity** — business rules expressed as CHECK constraints or triggers.

```sql
CREATE TABLE products (
    id       INT PRIMARY KEY,
    price    DECIMAL(10,2) CHECK (price >= 0),
    status   ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
    stock    INT NOT NULL DEFAULT 0 CHECK (stock >= 0)
);
```

---

### Foreign Key Actions

When a referenced row is updated or deleted, what happens to the child rows?

```sql
FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE     -- delete children when parent deleted
    ON UPDATE CASCADE     -- update FK when parent PK changes
```

| Action | DELETE parent | UPDATE parent PK |
|---|---|---|
| `CASCADE` | Delete all children | Update FK in children |
| `SET NULL` | Set FK to NULL in children | Set FK to NULL |
| `RESTRICT` | Block the delete | Block the update |
| `NO ACTION` | Same as RESTRICT (deferred in some DBs) | Same |
| `SET DEFAULT` | Set FK to default value | Set FK to default |

---

### NULL in SQL

NULL is not zero, not empty string, not false. It means **absence of a known value**.

NULL has special comparison rules:

```sql
NULL = NULL   → NULL (unknown, not TRUE)
NULL <> NULL  → NULL
NULL = 1      → NULL

-- Correct NULL checks:
WHERE column IS NULL
WHERE column IS NOT NULL
```

Three-valued logic: SQL comparisons return TRUE, FALSE, or **UNKNOWN** when NULL is involved. A WHERE clause only keeps rows where the condition is TRUE — UNKNOWN rows are excluded.

---

### Key Takeaways

- Primary key = not null + unique + identifies every row
- Surrogate keys (INT AUTO_INCREMENT) are simpler; natural keys carry business meaning but can change
- Foreign keys enforce referential integrity at the DB layer — CASCADE deletes children automatically
- NULL ≠ zero — it means unknown; always use IS NULL, never = NULL
- Three-valued logic (TRUE/FALSE/UNKNOWN) is why NULL comparisons behave unexpectedly
MD; }

    private function l1_3(): string { return <<<'MD'
## SQL Data Types: Choosing the Right Type for Every Column

Choosing the wrong data type causes bugs, wasted storage, and failed queries. This lesson gives you the decision framework.

---

### Numeric Types

| Type | Storage | Range | Use For |
|---|---|---|---|
| `TINYINT` | 1 byte | -128 to 127 (or 0-255 UNSIGNED) | Boolean flags, small enums |
| `SMALLINT` | 2 bytes | -32,768 to 32,767 | Small counters |
| `INT` | 4 bytes | -2.1B to 2.1B | Most IDs and counts |
| `BIGINT` | 8 bytes | ±9.2 × 10¹⁸ | Large IDs, timestamps (millis) |
| `DECIMAL(p,s)` | Variable | Exact | **Money and anything needing exact decimals** |
| `FLOAT` | 4 bytes | ~7 digits precision | Scientific data (not money) |
| `DOUBLE` | 8 bytes | ~15 digits precision | Scientific data (not money) |

**Rule: never store money in FLOAT or DOUBLE.** `0.1 + 0.2 = 0.30000000000000004` in floating-point. Use `DECIMAL(10,2)`.

---

### String Types

| Type | Max Size | Notes |
|---|---|---|
| `CHAR(n)` | 255 bytes | Fixed-length, padded with spaces. Fast for fixed-size data (country codes, hashes) |
| `VARCHAR(n)` | 65,535 bytes | Variable-length. Stores only the bytes used + 1-2 byte length prefix |
| `TEXT` | 65,535 bytes | Cannot have DEFAULT or be used in index prefix beyond 767 bytes |
| `MEDIUMTEXT` | 16 MB | Long articles, HTML |
| `LONGTEXT` | 4 GB | Large documents |

**CHAR vs VARCHAR:** Use CHAR for columns that are always the same length (MD5 hash = always 32 chars → `CHAR(32)`). Use VARCHAR for everything else.

---

### Date & Time Types

| Type | Format | Notes |
|---|---|---|
| `DATE` | YYYY-MM-DD | Date only — no time |
| `TIME` | HH:MM:SS | Time only — no date |
| `DATETIME` | YYYY-MM-DD HH:MM:SS | Stores exactly what you insert — no timezone |
| `TIMESTAMP` | YYYY-MM-DD HH:MM:SS | Stored as UTC; auto-converts to session timezone |
| `YEAR` | YYYY | Year only |

**DATETIME vs TIMESTAMP:**
- Use `TIMESTAMP` for created_at/updated_at — timezone-aware, auto-sets on insert
- Use `DATETIME` when you need timezone-neutral storage (event date regardless of viewer location)

```sql
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
```

---

### Boolean

SQL has no true BOOLEAN in MySQL — use `TINYINT(1)`:

```sql
is_active TINYINT(1) NOT NULL DEFAULT 1
```

PostgreSQL has a native `BOOLEAN` type accepting `TRUE`/`FALSE`.

---

### Enumerations

```sql
-- MySQL ENUM — stored as integer internally, max 65,535 values
status ENUM('pending','active','cancelled') NOT NULL DEFAULT 'pending'

-- Better pattern for extensibility: use a lookup table or VARCHAR + CHECK
status VARCHAR(20) CHECK (status IN ('pending','active','cancelled'))
```

ENUM is fast and compact but hard to extend (ALTER TABLE required to add values).

---

### JSON

```sql
-- Native JSON type (MySQL 5.7+, PostgreSQL)
metadata JSON

-- Query JSON fields
SELECT metadata->>'$.plan' FROM subscriptions;        -- MySQL
SELECT metadata->>'plan'   FROM subscriptions;        -- PostgreSQL
```

Use JSON for truly schemaless auxiliary data. Never replace normalised columns with JSON — you lose indexing and type safety.

---

### Binary Types

```sql
BLOB         -- up to 65 KB
MEDIUMBLOB   -- up to 16 MB
LONGBLOB     -- up to 4 GB
UUID CHAR(36) or BINARY(16)   -- store UUIDs efficiently as BINARY(16)
```

---

### The Decision Checklist

1. Is it money? → `DECIMAL`
2. Is it a large integer ID? → `BIGINT UNSIGNED`
3. Fixed-length string? → `CHAR(n)`
4. Variable text? → `VARCHAR(n)`, switch to `TEXT` only if > 255 chars
5. Needs timezone handling? → `TIMESTAMP`; timezone-neutral? → `DATETIME`
6. True/false flag? → `TINYINT(1)`
7. Controlled set of values? → `ENUM` (or VARCHAR + CHECK)

---

### Key Takeaways

- DECIMAL = exact; FLOAT/DOUBLE = approximate — use DECIMAL for money always
- TIMESTAMP stores UTC and auto-converts; DATETIME is timezone-naive
- CHAR = fixed-length (padded); VARCHAR = variable-length (prefixed with length)
- JSON is a valid type for auxiliary data, not a replacement for proper columns
- NULL should be allowed only when absence of value is genuinely meaningful
MD; }

    // ── LEVEL 2 LESSONS ───────────────────────────────────────────────────────

    private function l2_1(): string { return <<<'MD'
## How SQL Actually Executes: Logical Processing Order

SQL is declarative — but the database engine processes your query in a specific order that is **different from the order you write it**. Understanding this order resolves most SQL confusion about aliases, WHERE vs HAVING, and column references.

---

### The Logical Processing Order

```
1.  FROM          — identify and join the source tables
2.  ON            — apply join conditions
3.  JOIN          — produce joined result set
4.  WHERE         — filter rows (before grouping)
5.  GROUP BY      — group remaining rows
6.  WITH ROLLUP / CUBE — add summary rows (if specified)
7.  HAVING        — filter groups (after grouping)
8.  SELECT        — compute output columns, apply DISTINCT
9.  ORDER BY      — sort the result
10. LIMIT / OFFSET — restrict the number of rows returned
```

This is the **logical** order — the engine may physically reorder steps for performance, but the result is always as if these steps ran in this sequence.

---

### Why This Order Matters

**You cannot use a SELECT alias in WHERE:**

```sql
-- WRONG — alias 'total' doesn't exist yet when WHERE runs
SELECT price * qty AS total FROM orders WHERE total > 100;

-- CORRECT — WHERE runs before SELECT
SELECT price * qty AS total FROM orders WHERE price * qty > 100;
```

**You cannot use a SELECT alias in GROUP BY (in standard SQL):**

```sql
-- Standard SQL: repeat the expression
SELECT YEAR(created_at) AS yr, COUNT(*) FROM orders GROUP BY YEAR(created_at);

-- MySQL extension: allows alias in GROUP BY (non-standard)
SELECT YEAR(created_at) AS yr, COUNT(*) FROM orders GROUP BY yr;
```

**HAVING runs after GROUP BY, WHERE runs before:**

```sql
SELECT user_id, COUNT(*) AS order_count
FROM orders
WHERE status = 'completed'      -- filter rows BEFORE grouping
GROUP BY user_id
HAVING order_count > 5;         -- filter groups AFTER grouping
```

---

### FROM + JOIN: The Starting Point

```sql
SELECT u.name, o.total
FROM users u                          -- (1) start with users
INNER JOIN orders o ON u.id = o.user_id  -- (2) join orders
```

The FROM clause produces a virtual table that all subsequent steps operate on.

**Implicit vs explicit JOIN:**

```sql
-- Old implicit syntax (avoid — ambiguous with outer joins)
SELECT * FROM users u, orders o WHERE u.id = o.user_id;

-- Modern explicit syntax (always use this)
SELECT * FROM users u JOIN orders o ON u.id = o.user_id;
```

---

### SELECT: Computed Last (Before ORDER BY)

SELECT is step 8 — it computes final columns from whatever survived WHERE, GROUP BY, and HAVING.

```sql
SELECT
    u.name,
    COUNT(o.id)        AS order_count,
    SUM(o.total)       AS lifetime_value,
    AVG(o.total)       AS avg_order
FROM users u
LEFT JOIN orders o ON u.id = o.user_id
GROUP BY u.id, u.name
HAVING lifetime_value > 1000       -- MySQL allows alias in HAVING
ORDER BY lifetime_value DESC;
```

---

### ORDER BY and LIMIT: The Final Steps

ORDER BY can reference SELECT aliases because it runs after SELECT.

```sql
SELECT name, created_at FROM users ORDER BY created_at DESC LIMIT 10;
```

LIMIT without ORDER BY returns arbitrary rows — the database has no guaranteed row order unless you sort first.

---

### Subquery Scope

Each subquery has its own processing scope. A correlated subquery re-executes steps 1–10 for each outer row.

```sql
-- Correlated subquery — steps 1-10 run once per outer user row
SELECT u.name,
    (SELECT COUNT(*) FROM orders o WHERE o.user_id = u.id) AS orders
FROM users u;
```

---

### Key Takeaways

- Logical order: FROM → WHERE → GROUP BY → HAVING → SELECT → ORDER BY → LIMIT
- WHERE cannot see SELECT aliases (runs before SELECT)
- HAVING filters groups; WHERE filters rows — they operate at different stages
- ORDER BY CAN use SELECT aliases (runs after SELECT)
- LIMIT without ORDER BY = undefined row order
MD; }

    private function l2_2(): string { return <<<'MD'
## Relational Algebra: The Theory Behind JOINs

Every SQL JOIN maps to an operation in **relational algebra** — the mathematical foundation of SQL. Understanding this makes JOIN behaviour predictable rather than magical.

---

### What Is Relational Algebra?

Relational algebra is a set of operations on relations (tables) that each produce a new relation. SQL is a surface syntax on top of these operations.

Core operations:

| Algebra Operation | SQL Equivalent |
|---|---|
| Selection (σ) | WHERE |
| Projection (π) | SELECT column list |
| Union (∪) | UNION |
| Difference (−) | EXCEPT / NOT IN |
| Intersection (∩) | INTERSECT / IN |
| Cartesian Product (×) | CROSS JOIN |
| Join (⋈) | INNER JOIN |
| Left Outer Join | LEFT JOIN |
| Division | No direct SQL — use NOT EXISTS |

---

### Cartesian Product → The Basis of All JOINs

```sql
-- Cartesian product: every row in A paired with every row in B
SELECT * FROM users CROSS JOIN departments;
-- If users has 100 rows and departments has 5 → 500 rows
```

All JOINs start as a cartesian product and then filter with a predicate.

---

### Selection — Filtering Rows (σ)

σ(condition)(relation) — keeps only rows satisfying the condition.

```sql
SELECT * FROM orders WHERE total > 100;
-- σ(total > 100)(orders)
```

---

### Projection — Filtering Columns (π)

π(columns)(relation) — keeps only specified columns.

```sql
SELECT id, name FROM users;
-- π(id, name)(users)
```

---

### Inner Join — Intersection of Matching Rows

```
users ⋈(users.id = orders.user_id) orders
```

```sql
SELECT u.name, o.total
FROM users u
INNER JOIN orders o ON u.id = o.user_id;
```

Rows with no match on either side are excluded. Result = subset of cartesian product where condition is true.

---

### Left Outer Join — Preserve Left Relation

```sql
SELECT u.name, o.total
FROM users u
LEFT JOIN orders o ON u.id = o.user_id;
```

Every row from `users` appears. If there's no matching order, `o.total` is NULL. Used to find: "all users, with their orders if any."

**Anti-join pattern** (users with NO orders):

```sql
SELECT u.name
FROM users u
LEFT JOIN orders o ON u.id = o.user_id
WHERE o.id IS NULL;
```

---

### Right Outer Join

Mirror of LEFT JOIN — every row from the right table is preserved. In practice, rewrite as LEFT JOIN with tables swapped for clarity.

---

### Natural Join (avoid in production)

A NATURAL JOIN automatically joins on columns with identical names. It's fragile — adding a column that happens to share a name breaks queries silently.

```sql
-- Dangerous — don't use
SELECT * FROM users NATURAL JOIN orders;

-- Better: explicit ON condition
SELECT * FROM users u JOIN orders o ON u.id = o.user_id;
```

---

### Self-Join — A Relation Joined to Itself

```sql
-- Each employee with their manager's name
SELECT e.name AS employee, m.name AS manager
FROM employees e
LEFT JOIN employees m ON e.manager_id = m.id;
```

A self-join is the standard SQL pattern for hierarchical data.

---

### Division — "For All" Queries

Relational division has no direct SQL syntax but answers questions like "find all students who enrolled in ALL of these courses."

```sql
-- Students enrolled in ALL courses in the courses table:
SELECT DISTINCT student_id FROM enrollments e1
WHERE NOT EXISTS (
    SELECT course_id FROM courses c
    WHERE NOT EXISTS (
        SELECT 1 FROM enrollments e2
        WHERE e2.student_id = e1.student_id AND e2.course_id = c.course_id
    )
);
```

Double NOT EXISTS is the relational algebra division pattern in SQL.

---

### Key Takeaways

- All JOINs are filtered cartesian products — the ON clause is the filter
- INNER JOIN = intersection of matching rows; LEFT JOIN = all left rows + NULLs for unmatched right
- Anti-join (LEFT JOIN + WHERE right.id IS NULL) = set difference
- NATURAL JOIN is fragile — always use explicit ON
- Self-join handles hierarchical data; double NOT EXISTS implements relational division
MD; }

    private function l2_3(): string { return <<<'MD'
## Set Operations: UNION, INTERSECT & EXCEPT

Set operations combine the results of two SELECT statements into one result set. They are the SQL implementation of set theory: union, intersection, and difference.

---

### Rules for All Set Operations

1. Both queries must return the **same number of columns**
2. Corresponding columns must have **compatible data types**
3. Column names in the result come from the **first query**
4. Result rows are treated as sets — order is undefined unless ORDER BY is added at the end

---

### UNION — Combine and Deduplicate

```sql
SELECT name, email FROM current_employees
UNION
SELECT name, email FROM former_employees;
```

UNION removes duplicate rows across both result sets. Each unique (name, email) pair appears once.

---

### UNION ALL — Combine Without Deduplication

```sql
SELECT product_id FROM web_orders
UNION ALL
SELECT product_id FROM store_orders;
```

UNION ALL keeps all rows including duplicates. It is **faster** than UNION because it skips the deduplication step. Use UNION ALL when you know there are no duplicates, or when duplicates are meaningful.

**Performance rule:** Default to UNION ALL and add deduplication only when needed.

---

### INTERSECT — Common Rows Only

```sql
SELECT user_id FROM email_subscribers
INTERSECT
SELECT user_id FROM push_subscribers;
-- Users subscribed to BOTH email AND push
```

INTERSECT returns only rows that appear in both result sets.

**MySQL has no INTERSECT before 8.0.31.** Use INNER JOIN or EXISTS instead:

```sql
SELECT DISTINCT user_id FROM email_subscribers
WHERE user_id IN (SELECT user_id FROM push_subscribers);
```

---

### EXCEPT (or MINUS) — Rows in First, Not in Second

```sql
SELECT user_id FROM all_users
EXCEPT
SELECT user_id FROM banned_users;
-- Active users only
```

MySQL calls this `EXCEPT` (added in 8.0.31). Oracle calls it `MINUS`.

**MySQL pre-8.0 workaround:**

```sql
SELECT DISTINCT a.user_id FROM all_users a
WHERE a.user_id NOT IN (SELECT user_id FROM banned_users);
-- OR (safer with NULLs):
SELECT a.user_id FROM all_users a
WHERE NOT EXISTS (SELECT 1 FROM banned_users b WHERE b.user_id = a.user_id);
```

---

### Combining Multiple Set Operations

```sql
(SELECT id FROM group_a
 UNION ALL
 SELECT id FROM group_b)
EXCEPT
SELECT id FROM excluded_ids;
```

Use parentheses to control precedence — set operations evaluate left-to-right by default.

---

### ORDER BY with Set Operations

ORDER BY applies to the entire result, not to individual SELECT statements:

```sql
SELECT name FROM employees
UNION
SELECT name FROM contractors
ORDER BY name ASC;   -- sorts the combined result
```

---

### UNION vs JOIN — Common Confusion

| | JOIN | UNION |
|---|---|---|
| Direction | Horizontal — adds columns | Vertical — adds rows |
| Use when | Combining related tables | Combining same-structure result sets |
| Result shape | More columns | More rows |

---

### Key Takeaways

- UNION deduplicates; UNION ALL keeps all rows — prefer UNION ALL for performance
- INTERSECT = common rows; EXCEPT = rows in first set not in second
- All set operations require identical column count and compatible types
- MySQL < 8.0.31 lacks INTERSECT and EXCEPT — use JOIN/NOT IN/NOT EXISTS instead
- ORDER BY at the end applies to the combined result, not individual queries
MD; }

    // ── LEVEL 3 LESSONS ───────────────────────────────────────────────────────

    private function l3_1(): string { return <<<'MD'
## Normal Forms: 1NF → 2NF → 3NF → BCNF Explained

Normalisation is the process of structuring a database to reduce redundancy and prevent data anomalies. Each normal form eliminates a specific class of problem.

---

### Why Normalise?

An unnormalised table causes **data anomalies**:

- **Insertion anomaly** — cannot insert new data without unrelated data (e.g., cannot add a course without a student)
- **Update anomaly** — changing one fact requires updating multiple rows
- **Deletion anomaly** — deleting a row loses unrelated facts

---

### Example: Unnormalised Table

```
| order_id | customer_name | customer_email     | product_name | category    | price |
|----------|---------------|--------------------|--------------|-------------|-------|
| 1        | Alice         | alice@example.com  | Laptop       | Electronics | 999   |
| 2        | Alice         | alice@example.com  | Mouse        | Electronics | 29    |
| 3        | Bob           | bob@example.com    | Laptop       | Electronics | 999   |
```

Problems: Alice's email is repeated; changing a product price requires updating every row.

---

### First Normal Form (1NF)

**Rule:** Every column must hold atomic (indivisible) values. No repeating groups or arrays.

**Violation:**

```
| id | name  | phones           |
|----|-------|------------------|
| 1  | Alice | 555-1111,555-2222|
```

**Fix:** Separate into a `user_phones` table — one phone per row.

After 1NF:
- No multi-valued columns
- Each row is uniquely identifiable (primary key exists)
- No repeating groups of columns (e.g., phone1, phone2, phone3)

---

### Second Normal Form (2NF)

**Rule:** Must be in 1NF. Every non-key attribute must depend on the **entire** primary key (no partial dependencies).

Partial dependency only exists when the primary key is **composite**.

**Violation:**

```
Primary key: (order_id, product_id)
Table: order_items(order_id, product_id, quantity, product_name)
```

`product_name` depends only on `product_id` — not on the full composite key. That's a partial dependency.

**Fix:** Move `product_name` to a `products` table.

---

### Third Normal Form (3NF)

**Rule:** Must be in 2NF. No **transitive dependencies** — non-key attributes must not depend on other non-key attributes.

**Violation:**

```
employees(id, name, department_id, department_name)
```

`department_name` depends on `department_id` (non-key), not on `id` (primary key). Transitive: id → department_id → department_name.

**Fix:** Extract to a `departments(id, name)` table.

After 3NF: every non-key column depends on **the key, the whole key, and nothing but the key**.

---

### Boyce-Codd Normal Form (BCNF)

**Rule:** For every functional dependency A → B, A must be a super key.

BCNF is a stronger version of 3NF. A table can be in 3NF but violate BCNF when there are multiple overlapping candidate keys.

**Example violation:**

```
course_enrolment(student, course, teacher)
-- Each teacher teaches only one course
-- A course can have multiple teachers
-- Candidate keys: (student, course) and (student, teacher)
-- Dependency: teacher → course (teacher is not a super key)
```

**Fix:** Split into `teaching(teacher, course)` and `enrolment(student, teacher)`.

---

### When to Denormalise

Strict normalisation optimises for write integrity. For read-heavy analytics:

- Denormalise to reduce joins
- Use materialised views or reporting tables
- Accept controlled redundancy with synchronisation strategy

**Rule of thumb:** Normalise to 3NF as default. Denormalise only with a measured performance reason.

---

### Key Takeaways

| Normal Form | Eliminates |
|---|---|
| 1NF | Repeating groups, non-atomic values |
| 2NF | Partial dependencies on composite key |
| 3NF | Transitive dependencies |
| BCNF | All functional dependency violations |

- Most production databases target 3NF
- Analytic/reporting databases often intentionally denormalise
- "The key, the whole key, nothing but the key" summarises 3NF
MD; }

    private function l3_2(): string { return <<<'MD'
## Entity-Relationship Modelling: Entities, Attributes & Cardinality

ER modelling is the design step before you write CREATE TABLE. It maps the real-world domain into a structure that SQL can represent.

---

### Entities

An **entity** is a thing with distinct existence — typically maps to a table.

- **Strong entity** — exists independently (Users, Products, Orders)
- **Weak entity** — depends on another entity for identification (OrderItem depends on Order; an order item without an order is meaningless)

```sql
-- Strong entity
CREATE TABLE users (id INT PRIMARY KEY, name VARCHAR(100));

-- Weak entity: order_items cannot exist without orders
CREATE TABLE order_items (
    order_id   INT NOT NULL,
    line_no    INT NOT NULL,
    product_id INT NOT NULL,
    qty        INT NOT NULL,
    PRIMARY KEY (order_id, line_no),   -- composite PK including owner's PK
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);
```

---

### Attributes

| Attribute Type | Description | Example |
|---|---|---|
| Simple | Atomic, single value | `first_name` |
| Composite | Made of sub-parts | `full_address` = street + city + zip |
| Single-valued | One value per entity | `date_of_birth` |
| Multi-valued | Multiple values per entity | `phone_numbers` → separate table |
| Derived | Calculated from other data | `age` from `date_of_birth` — compute in query |
| Key | Uniquely identifies entity | `id`, `email` |
| Null | May be absent | `middle_name` |

**Design rule:** Store atomic attributes. Do not store full_name if you need to sort by last_name. Derived values (age, total) should be computed in queries, not stored.

---

### Cardinality (Relationship Types)

Cardinality defines how many instances of one entity relate to instances of another.

**One-to-One (1:1)**

```sql
-- Each user has at most one profile
CREATE TABLE user_profiles (
    user_id INT PRIMARY KEY,
    bio TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

Use when: you want to split a wide table, restrict access to sensitive columns, or model optional extension data.

**One-to-Many (1:N)** — most common

```sql
-- One user, many orders
CREATE TABLE orders (
    id      INT PRIMARY KEY,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

FK goes on the "many" side (orders holds the user_id).

**Many-to-Many (M:N)**

```sql
-- Students enrol in many courses; courses have many students
CREATE TABLE enrolments (
    student_id INT NOT NULL,
    course_id  INT NOT NULL,
    enrolled_at DATE,
    PRIMARY KEY (student_id, course_id),
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (course_id)  REFERENCES courses(id)
);
```

Resolved with a **junction table** (also called associative, bridge, or pivot table). The junction table can carry additional attributes (enrolled_at, grade).

---

### Participation Constraints

- **Total participation** — every entity must participate in the relationship (e.g., every order MUST have a user → user_id NOT NULL)
- **Partial participation** — entity may optionally participate (e.g., a user may have no orders → FK in orders, not in users)

---

### ER → Schema Mapping Rules

| ER Construct | SQL Implementation |
|---|---|
| Strong entity | Table with PK |
| Weak entity | Table with composite PK including owner FK |
| 1:1 relationship | FK on either side (put FK on the optional side) |
| 1:N relationship | FK on the N (many) side |
| M:N relationship | Junction table with composite PK |
| Multi-valued attribute | Separate table with FK |
| Composite attribute | Flatten into individual columns |
| Derived attribute | Compute in queries; do not store |

---

### Key Takeaways

- Entity = table; attribute = column; relationship = FK or junction table
- Weak entities use composite PKs that include the parent's PK
- 1:N → FK on the many side; M:N → junction table
- Multi-valued attributes and composite attributes always get their own table or flat columns
- Derived values (age, total) belong in queries, not stored columns
MD; }

    private function l3_3(): string { return <<<'MD'
## Schema Design Patterns: Keys, Indexes & Referential Integrity

Good schema design combines normalisation theory with practical decisions about keys, indexing strategy, and constraint enforcement.

---

### Surrogate vs Natural Keys

**Surrogate key** — artificial, system-generated (AUTO_INCREMENT INT, UUID)

```sql
CREATE TABLE users (
    id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE
);
```

Pros: stable (never changes), simple, always unique
Cons: no business meaning — must join to get readable identifier

**Natural key** — real-world identifier (email, ISBN, SSN, order_number)

```sql
CREATE TABLE products (
    isbn  CHAR(13) PRIMARY KEY,
    title VARCHAR(200) NOT NULL
);
```

Pros: self-documenting, no extra join needed
Cons: can change (email changes, ISBN reused), longer = slower joins

**Best practice:** Use surrogate INT/BIGINT PKs for most tables. Add a UNIQUE constraint on natural identifiers (email, order_number) — this gives you both.

---

### UUID as Primary Key — Trade-offs

```sql
id CHAR(36) DEFAULT (UUID())   -- readable but large
id BINARY(16)                   -- compact (16 bytes vs 36)
```

UUIDs are globally unique — useful for distributed systems where multiple servers generate IDs. Trade-off: **random UUIDs fragment the B-tree index**, causing page splits and poor cache locality. Use UUID v7 (time-ordered) or ULID to keep insert locality.

---

### Index Design Principles

**Primary index (clustered):** Rows stored in PK order. Keep it small — every secondary index includes the PK.

**Secondary indexes:** Add only when you query by that column frequently.

```sql
-- Index for: WHERE email = ? (login query)
CREATE UNIQUE INDEX idx_users_email ON users(email);

-- Composite index for: WHERE user_id = ? AND status = ? ORDER BY created_at
CREATE INDEX idx_orders_user_status_date ON orders(user_id, status, created_at);
```

**Left-prefix rule:** The composite index above helps:
- `WHERE user_id = 1` ✓
- `WHERE user_id = 1 AND status = 'paid'` ✓
- `WHERE status = 'paid'` ✗ (no leftmost prefix)

---

### Referential Integrity Patterns

**Soft delete** — mark rows as deleted instead of removing them:

```sql
ALTER TABLE users ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;

-- "Delete"
UPDATE users SET deleted_at = NOW() WHERE id = 5;

-- Query active users only
SELECT * FROM users WHERE deleted_at IS NULL;
```

Benefit: preserve FK relationships and audit history. Drawback: every query needs the WHERE clause.

**Audit table** — track all changes:

```sql
CREATE TABLE users_audit (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT,
    action      ENUM('INSERT','UPDATE','DELETE'),
    old_data    JSON,
    new_data    JSON,
    changed_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    changed_by  INT
);
```

**Cascade strategy matrix:**

| Relationship | On Delete | On Update |
|---|---|---|
| Order → User | RESTRICT (don't delete users with orders) | CASCADE |
| OrderItem → Order | CASCADE (delete items when order deleted) | CASCADE |
| Optional profile → User | CASCADE | CASCADE |
| Log → User | SET NULL (keep logs, clear user reference) | SET NULL |

---

### Schema Naming Conventions

| Element | Convention | Example |
|---|---|---|
| Table | plural snake_case | `user_profiles` |
| Column | singular snake_case | `first_name` |
| Primary key | `id` | `id` |
| Foreign key | `{table}_id` | `user_id` |
| Boolean | `is_` or `has_` prefix | `is_active`, `has_verified` |
| Timestamp | `_at` suffix | `created_at`, `deleted_at` |
| Index | `idx_{table}_{columns}` | `idx_orders_user_id` |

Consistency matters more than the specific convention — pick one and stick to it across the whole schema.

---

### Key Takeaways

- Surrogate INT PKs are the default; add UNIQUE on natural keys alongside them
- UUID PKs have fragmentation risk — use time-ordered UUIDs (v7/ULID) if needed
- Index composite columns in equality-first, range-last order
- Soft delete preserves FK integrity and history but requires `WHERE deleted_at IS NULL` everywhere
- Match ON DELETE action to the business rule: RESTRICT protects data, CASCADE propagates deletions
MD; }

    // ── LEVEL 4 LESSONS ───────────────────────────────────────────────────────

    private function l4_1(): string { return <<<'MD'
## Subqueries, CTEs & Derived Tables: When to Use Each

All three let you build queries from intermediate result sets. Knowing when each is the right tool makes queries readable, maintainable, and fast.

---

### Subqueries

A subquery is a SELECT nested inside another SQL statement.

**In WHERE — scalar subquery:**

```sql
-- Users who placed an order above the average
SELECT * FROM users WHERE id IN (
    SELECT user_id FROM orders WHERE total > (SELECT AVG(total) FROM orders)
);
```

**In FROM — derived table:**

```sql
SELECT dept, avg_salary
FROM (
    SELECT department_id AS dept, AVG(salary) AS avg_salary
    FROM employees
    GROUP BY department_id
) AS dept_summary
WHERE avg_salary > 60000;
```

A derived table must have an alias. It is computed once and treated as a virtual table.

**Correlated subquery — references the outer query per row:**

```sql
-- Each employee with their department's average salary
SELECT name, salary,
    (SELECT AVG(salary) FROM employees e2 WHERE e2.department_id = e.department_id) AS dept_avg
FROM employees e;
```

Correlated subqueries execute once per outer row — O(n) in the number of rows. On large tables, rewrite as a JOIN or CTE.

**EXISTS — stops at first match (faster than IN for large subqueries):**

```sql
SELECT * FROM users u
WHERE EXISTS (SELECT 1 FROM orders o WHERE o.user_id = u.id AND o.total > 500);
```

---

### CTEs — Common Table Expressions

A CTE is a named, reusable temporary result set scoped to one query.

```sql
WITH
    completed_orders AS (
        SELECT user_id, SUM(total) AS lifetime
        FROM orders WHERE status = 'completed'
        GROUP BY user_id
    ),
    high_value AS (
        SELECT * FROM completed_orders WHERE lifetime > 1000
    )
SELECT u.name, hv.lifetime
FROM users u JOIN high_value hv ON u.id = hv.user_id;
```

**Advantages over subqueries:**
- Named — readable and self-documenting
- Reusable within the same query — reference it multiple times
- Easier to debug — test each CTE independently

**Recursive CTE — essential for hierarchical data:**

```sql
WITH RECURSIVE categories AS (
    SELECT id, name, parent_id, 0 AS depth
    FROM category WHERE parent_id IS NULL

    UNION ALL

    SELECT c.id, c.name, c.parent_id, r.depth + 1
    FROM category c JOIN categories r ON c.parent_id = r.id
)
SELECT * FROM categories ORDER BY depth, name;
```

The anchor query (first SELECT) provides the starting rows. The recursive part joins back to the CTE until no more rows are added.

---

### Derived Tables vs CTEs: Key Differences

| | Derived Table | CTE |
|---|---|---|
| Location | In FROM clause | Before main query |
| Reusable | No — define again each time | Yes — reference by name |
| Recursive | No | Yes |
| Readability | Poor for complex queries | High |
| Performance | Identical in most engines | Identical in most engines |

---

### When to Use Which

| Scenario | Best Choice |
|---|---|
| Simple one-time filter | Inline subquery |
| Reused intermediate result | CTE |
| Hierarchical / recursive data | Recursive CTE |
| Complex multi-step analysis | Multiple CTEs |
| EXISTS check | Correlated subquery with EXISTS |
| Aggregation before joining | CTE or derived table |

---

### Key Takeaways

- Subqueries in FROM are derived tables — they must be aliased
- Correlated subqueries run per outer row — rewrite as JOINs/CTEs on large data
- CTEs are readable, reusable, and testable — prefer them for anything beyond a simple one-liner
- Recursive CTEs are the SQL standard way to traverse trees and graphs
- EXISTS is faster than IN when the subquery returns many rows (stops at first match)
MD; }

    private function l4_2(): string { return <<<'MD'
## Window Functions: Anatomy, Frames & Real-World Patterns

Window functions compute values across a set of related rows **without collapsing them into one**. They are the most powerful tool in analytical SQL.

---

### Anatomy of a Window Function

```sql
function_name(expression) OVER (
    [PARTITION BY partition_expression]
    [ORDER BY sort_expression [ASC|DESC]]
    [frame_clause]
)
```

- **PARTITION BY** — divides rows into groups (like GROUP BY, but rows are kept)
- **ORDER BY** — defines row order within each partition
- **Frame clause** — defines which rows within the partition to include

---

### Ranking Functions

```sql
SELECT
    name, department, salary,
    RANK()        OVER (PARTITION BY department ORDER BY salary DESC) AS rnk,
    DENSE_RANK()  OVER (PARTITION BY department ORDER BY salary DESC) AS dense_rnk,
    ROW_NUMBER()  OVER (PARTITION BY department ORDER BY salary DESC) AS row_num,
    NTILE(4)      OVER (PARTITION BY department ORDER BY salary DESC) AS quartile
FROM employees;
```

| Salaries | RANK | DENSE_RANK | ROW_NUMBER |
|---|---|---|---|
| 100, 100, 80 | 1, 1, 3 | 1, 1, 2 | 1, 2, 3 |

- RANK — gaps after ties (1,1,3)
- DENSE_RANK — no gaps (1,1,2)
- ROW_NUMBER — always unique, arbitrary tie-breaking

**Top-N per group pattern:**

```sql
WITH ranked AS (
    SELECT *, ROW_NUMBER() OVER (PARTITION BY department ORDER BY salary DESC) AS rn
    FROM employees
)
SELECT * FROM ranked WHERE rn <= 3;   -- top 3 earners per department
```

---

### Offset Functions

```sql
SELECT month, revenue,
    LAG(revenue, 1, 0)  OVER (ORDER BY month) AS prev_month,
    LEAD(revenue, 1, 0) OVER (ORDER BY month) AS next_month
FROM monthly_sales;
```

- **LAG(col, n, default)** — value from n rows before current row
- **LEAD(col, n, default)** — value from n rows after current row

Month-over-month change:

```sql
revenue - LAG(revenue) OVER (ORDER BY month) AS mom_change
```

---

### Aggregate Window Functions

```sql
SELECT
    name, department, salary,
    SUM(salary) OVER (PARTITION BY department)                   AS dept_total,
    AVG(salary) OVER (PARTITION BY department)                   AS dept_avg,
    SUM(salary) OVER (ORDER BY hired_at ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS running_total
FROM employees;
```

---

### Frame Specification

The frame defines *which rows* within the partition the aggregate sees for each row.

```sql
ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW   -- from first row to current
ROWS BETWEEN 2 PRECEDING AND CURRENT ROW           -- rolling 3-row window
ROWS BETWEEN 1 PRECEDING AND 1 FOLLOWING           -- centred 3-row window
RANGE BETWEEN INTERVAL 7 DAY PRECEDING AND CURRENT ROW  -- 7-day rolling window
```

**ROWS vs RANGE:**
- `ROWS` — physical row positions
- `RANGE` — logical value range (all rows with same ORDER BY value are included together)

---

### FIRST_VALUE / LAST_VALUE

```sql
SELECT name, salary,
    FIRST_VALUE(name) OVER (PARTITION BY department ORDER BY salary DESC) AS top_earner,
    LAST_VALUE(name)  OVER (PARTITION BY department ORDER BY salary DESC
                            ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING) AS lowest_earner
FROM employees;
```

LAST_VALUE requires an explicit frame going to UNBOUNDED FOLLOWING — the default frame stops at CURRENT ROW.

---

### Key Takeaways

- Window functions keep all rows; GROUP BY collapses them — fundamentally different
- PARTITION BY = GROUP BY without collapse; ORDER BY within the window defines row order
- RANK vs DENSE_RANK vs ROW_NUMBER: gaps vs no-gaps vs always-unique
- LAG/LEAD access previous/next rows — essential for time-series comparisons
- Frame clause controls the rolling window — ROWS is physical, RANGE is value-based
MD; }

    private function l4_3(): string { return <<<'MD'
## Transactions, Isolation Levels & Concurrency Anomalies

When multiple users hit a database simultaneously, transactions and isolation levels determine what each transaction can see and guarantee about data correctness.

---

### ACID Revisited (Deep Dive)

**Atomicity** — a transaction is all-or-nothing. Implemented via undo logs: if the transaction aborts, every change is rolled back to the exact pre-transaction state.

**Consistency** — the DB moves from one valid state to another. Constraints (NOT NULL, FK, CHECK) are verified at commit time. If any constraint fails, the transaction is rolled back.

**Isolation** — concurrent transactions do not interfere. The degree of isolation is configurable (isolation levels).

**Durability** — once committed, data survives crashes. Implemented via redo logs written to disk before the commit acknowledgment is sent.

---

### Concurrency Anomalies

| Anomaly | Definition |
|---|---|
| **Dirty read** | Reading uncommitted data from another transaction |
| **Non-repeatable read** | Reading the same row twice and getting different values (another tx committed a change between reads) |
| **Phantom read** | Running the same range query twice and getting different rows (another tx inserted/deleted rows between runs) |
| **Lost update** | Two transactions read a value, both modify it, one overwrites the other's change |

---

### Isolation Levels

| Level | Dirty Read | Non-Repeatable | Phantom |
|---|---|---|---|
| READ UNCOMMITTED | Possible | Possible | Possible |
| READ COMMITTED | Prevented | Possible | Possible |
| **REPEATABLE READ** (MySQL default) | Prevented | Prevented | Possible* |
| SERIALIZABLE | Prevented | Prevented | Prevented |

*InnoDB's REPEATABLE READ prevents most phantoms via gap locks.

```sql
SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED;
```

---

### How Each Level Is Implemented

**READ COMMITTED** — takes a fresh snapshot at the start of each statement. Common in PostgreSQL default and many OLTP systems.

**REPEATABLE READ** — takes a snapshot at the start of the entire transaction. All reads within the transaction see the same data.

**SERIALIZABLE** — transactions run as if they were sequential. Implemented via predicate locking or by converting all reads to `SELECT ... FOR SHARE`. Highest safety, lowest throughput.

---

### Lost Update Problem & Solution

```sql
-- Transaction 1               | Transaction 2
SELECT balance FROM accounts WHERE id=1;  -- reads 1000
                               -- SELECT balance ... -- also reads 1000
UPDATE accounts SET balance = 1000-200;    -- writes 800
                               -- UPDATE ... SET balance = 1000+500; -- writes 1500
-- Final balance: 1500 (the -200 is lost)
```

**Fix: use FOR UPDATE to lock the row at read time:**

```sql
START TRANSACTION;
SELECT balance FROM accounts WHERE id = 1 FOR UPDATE;  -- locks the row
-- Now Transaction 2 must wait
UPDATE accounts SET balance = balance - 200 WHERE id = 1;
COMMIT;
```

---

### Optimistic vs Pessimistic Locking

**Pessimistic** — lock the row before reading (`FOR UPDATE`). No other writer can touch it until you commit. Safe, but reduces concurrency.

**Optimistic** — read without locking; at update time, check that the data hasn't changed:

```sql
-- Read with version
SELECT id, balance, version FROM accounts WHERE id = 1;
-- (Application computes new balance)
-- Update only if version matches
UPDATE accounts
SET balance = 950, version = version + 1
WHERE id = 1 AND version = 3;  -- fails if another tx already updated
-- Check rows affected — 0 means conflict, retry
```

Optimistic locking scales better for low-contention scenarios. Pessimistic is safer under high contention.

---

### Deadlock Prevention

Deadlocks occur when two transactions each wait for the other's lock. Prevention strategies:

1. Always acquire locks in the same order across all code paths
2. Keep transactions short — minimise time locks are held
3. Use `NOWAIT` to fail immediately rather than wait: `SELECT ... FOR UPDATE NOWAIT`
4. Use `SKIP LOCKED` for work-queue patterns (skip rows locked by other workers)

---

### Key Takeaways

- ACID = Atomicity, Consistency, Isolation, Durability — each property has a specific mechanism
- Default MySQL isolation level is REPEATABLE READ
- READ COMMITTED prevents dirty reads; REPEATABLE READ also prevents non-repeatable reads
- Use `FOR UPDATE` to prevent lost updates in read-modify-write patterns
- Optimistic locking (version column) scales better; pessimistic (FOR UPDATE) is safer under contention
MD; }

    // ── LEVEL 5 LESSONS ───────────────────────────────────────────────────────

    private function l5_1(): string { return <<<'MD'
## SQL Security: Injection, Parameterized Queries & Least Privilege

SQL security failures are consistently in the OWASP Top 10. This lesson covers the attack vectors and the defences.

---

### SQL Injection — The Attack

SQL injection occurs when user input is concatenated directly into a SQL string:

```php
// VULNERABLE — never do this
$query = "SELECT * FROM users WHERE email = '" . $_POST['email'] . "'";
```

**Attacker input:** `' OR '1'='1`

**Resulting query:**

```sql
SELECT * FROM users WHERE email = '' OR '1'='1'
-- Returns ALL users
```

**Attacker input for data exfiltration:** `' UNION SELECT username, password FROM admin_users -- `

**Resulting query:**

```sql
SELECT * FROM users WHERE email = ''
UNION SELECT username, password FROM admin_users -- '
-- Returns admin credentials
```

**Attacker input for destruction:** `'; DROP TABLE users; --`

---

### Parameterized Queries — The Fix

Never concatenate user input. Always use parameterized queries (prepared statements):

```php
// PHP PDO — safe
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute([':email' => $_POST['email']]);
```

```js
// Node.js mysql2 — safe
const [rows] = await db.execute('SELECT * FROM users WHERE email = ?', [req.body.email]);
```

```python
# Python — safe
cursor.execute("SELECT * FROM users WHERE email = %s", (request.form['email'],))
```

The database driver separates SQL code from data — user input is **never interpreted as SQL**.

---

### Stored Procedures — Partial Protection

Stored procedures reduce injection surface but are not a complete defence if they internally concatenate strings:

```sql
-- STILL VULNERABLE inside the procedure:
SET @sql = CONCAT('SELECT * FROM ', table_name);
PREPARE stmt FROM @sql;
EXECUTE stmt;
```

Dynamic SQL inside stored procedures must use parameterized execution too.

---

### Least Privilege — Principle

The application database user should have only the permissions it actually needs:

```sql
-- Application user: read + write data, nothing structural
GRANT SELECT, INSERT, UPDATE, DELETE ON careerdb.* TO 'app'@'%';

-- Reporting user: read only
GRANT SELECT ON careerdb.* TO 'reporting'@'%';

-- Never: app user should not have DROP, CREATE, GRANT, or FILE
```

This limits blast radius — even if injection occurs, the attacker cannot DROP tables or read system files.

---

### Input Validation & Output Escaping

Parameterized queries prevent injection in queries. Additionally:

- **Validate input type/format** before it reaches SQL (e.g., reject non-integer IDs)
- **Allowlists for dynamic column names** — if users can choose a sort column, check it against an allowlist:

```php
$allowed_columns = ['name', 'created_at', 'email'];
if (!in_array($sort, $allowed_columns)) {
    $sort = 'created_at';   // safe default
}
$query = "SELECT * FROM users ORDER BY {$sort}";
```

Column and table names cannot be parameterized — use allowlists.

---

### Row-Level Security

PostgreSQL supports row-level security (RLS) — filters rows at the database level based on the connected user:

```sql
ALTER TABLE projects ENABLE ROW LEVEL SECURITY;

CREATE POLICY user_projects ON projects
    USING (owner_id = current_setting('app.current_user_id')::INT);
```

MySQL alternative: views that embed the user filter.

---

### Key Takeaways

- SQL injection = concatenating user input into SQL strings — always use parameterized queries
- Parameterized queries are the only reliable defence — not escaping, not filtering
- Column/table names cannot be parameterized — validate against an allowlist
- Least privilege: grant only SELECT/INSERT/UPDATE/DELETE to the application user, never DDL
- RLS enforces access control at the database layer — strongest guarantee
MD; }

    private function l5_2(): string { return <<<'MD'
## Query Performance: Indexes, EXPLAIN & Anti-Patterns

Writing a correct query is step one. Writing one that performs at scale is step two.

---

### The Index Decision Framework

**Add an index when:**
- A column appears frequently in WHERE, JOIN ON, or ORDER BY
- The column has high cardinality (many distinct values)
- The query runs at high frequency or on large tables

**Don't add an index when:**
- The table is small (< 10,000 rows) — full scan is often faster
- The column has very low cardinality (boolean, status with 3 values)
- The table is write-heavy — every index slows INSERT/UPDATE/DELETE

---

### Reading EXPLAIN Output

```sql
EXPLAIN SELECT u.name, COUNT(o.id)
FROM users u LEFT JOIN orders o ON u.id = o.user_id
WHERE u.is_active = 1
GROUP BY u.id;
```

Key columns to check:

| Column | What to look for |
|---|---|
| `type` | Avoid `ALL` on large tables. Aim for `ref`, `eq_ref`, `const` |
| `key` | NULL = no index used = investigate |
| `rows` | Estimated rows scanned — higher = slower |
| `Extra` | `Using filesort` and `Using temporary` = add indexes |

---

### Common SQL Anti-Patterns

**N+1 Query Problem**

```php
// BAD: 1 query for users + N queries for each user's orders
$users = DB::query("SELECT * FROM users");
foreach ($users as $user) {
    $orders = DB::query("SELECT * FROM orders WHERE user_id = {$user->id}");
}
// 1 + N total queries
```

```sql
-- GOOD: one JOIN returns everything
SELECT u.*, o.id AS order_id, o.total
FROM users u LEFT JOIN orders o ON u.id = o.user_id;
```

**SELECT \* in production code**

```sql
-- BAD: fetches unused columns, breaks on schema change
SELECT * FROM users;

-- GOOD: explicit columns
SELECT id, name, email FROM users;
```

**OFFSET pagination on large tables**

```sql
-- BAD: MySQL scans and discards 100,000 rows
SELECT * FROM orders ORDER BY id LIMIT 10 OFFSET 100000;

-- GOOD: cursor-based (keyset) pagination
SELECT * FROM orders WHERE id > 100000 ORDER BY id LIMIT 10;
```

**Function on indexed column**

```sql
-- BAD: disables index on email
SELECT * FROM users WHERE LOWER(email) = 'alice@example.com';

-- GOOD: normalise data at insert time, query raw
SELECT * FROM users WHERE email = 'alice@example.com';

-- OR: function-based index (MySQL 8.0+)
CREATE INDEX idx_lower_email ON users ((LOWER(email)));
```

**OR on different columns breaks composite index**

```sql
-- BAD: can't use composite index on (status, created_at)
SELECT * FROM orders WHERE status = 'paid' OR created_at > '2024-01-01';

-- GOOD: UNION ALL
SELECT * FROM orders WHERE status = 'paid'
UNION ALL
SELECT * FROM orders WHERE created_at > '2024-01-01' AND status <> 'paid';
```

---

### Query Rewrite Patterns

| Anti-pattern | Rewrite |
|---|---|
| NOT IN with NULLs possible | Use NOT EXISTS |
| Correlated subquery per row | Rewrite as LEFT JOIN or CTE |
| DISTINCT to fix duplicate JOIN | Fix the JOIN cardinality instead |
| COUNT(*) on large table | Use approximate count or cached counter |

---

### Key Takeaways

- Add indexes on high-cardinality columns used in WHERE/JOIN/ORDER BY
- `type: ALL` + large `rows` = investigate; `Using filesort` = add an index on ORDER BY columns
- N+1 is the most common ORM performance bug — use eager loading / JOIN
- Cursor-based pagination scales; OFFSET pagination does not
- Functions on indexed columns disable the index — store normalised data or use function-based indexes
MD; }

    private function l5_3(): string { return <<<'MD'
## Schema Evolution: Migrations, Versioning & Zero-Downtime Changes

Production databases change constantly. How you manage those changes determines whether deployments are safe or dangerous.

---

### What Is a Database Migration?

A migration is a versioned, incremental change to the database schema. Each migration has:
- An **up** operation (apply the change)
- A **down** operation (revert it)

```php
// Laravel migration example
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('phone', 20)->nullable()->after('email');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('phone');
    });
}
```

Migrations are run in order, tracked in a `migrations` table, and committed to version control alongside application code.

---

### Migration Versioning Strategy

```
migrations/
  2024_01_01_000000_create_users_table.php
  2024_01_15_120000_add_phone_to_users.php
  2024_02_10_083000_create_orders_table.php
  2024_03_05_140000_add_index_to_orders_user_id.php
```

**Rules:**
- Never modify a migration that has already run in production
- Always write the `down()` method — even if you never use it
- Test rollbacks in staging before deploying
- Run migrations before deploying new application code (schema change first, code change second)

---

### Zero-Downtime Migration Patterns

Some schema changes are dangerous on live tables. Here's how to make them safe.

**Safe changes (can be done live):**
- Adding a nullable column
- Adding an index (if using `CREATE INDEX` which does not lock the table in MySQL 8.0+)
- Adding a new table
- Renaming a value in an ENUM (via ALTER — be careful)

**Unsafe changes (require care):**

**Renaming a column** — breaks queries using the old name immediately.

```
Safe pattern (3 deployments):
1. Add new_name column alongside old_name
2. Deploy app code that writes to both columns, reads from old
3. Backfill new_name from old_name
4. Deploy app code that reads from new_name
5. Drop old_name column
```

**Adding NOT NULL column without default** — fails for existing rows.

```sql
-- Step 1: Add nullable
ALTER TABLE users ADD COLUMN tier VARCHAR(20) NULL;

-- Step 2: Backfill
UPDATE users SET tier = 'free' WHERE tier IS NULL;

-- Step 3: Add constraint
ALTER TABLE users MODIFY COLUMN tier VARCHAR(20) NOT NULL DEFAULT 'free';
```

**Adding an index on a large table** — in MySQL 8.0, online DDL means the table is readable and writable during index creation. Confirm with:

```sql
-- MySQL 8: this is online (no table lock)
ALTER TABLE orders ADD INDEX idx_user_id (user_id), ALGORITHM=INPLACE, LOCK=NONE;
```

---

### Schema Diff & State Management

**Never run manual ALTER TABLE in production.** All changes go through migrations. Use tools to verify:

```bash
# Laravel: show pending migrations
php artisan migrate:status

# Compare schema against migrations
php artisan schema:dump   # snapshot current schema

# Rollback last batch
php artisan migrate:rollback
```

---

### Backward-Compatible Schema Changes

Code and schema are deployed independently. Design schema changes to be backward compatible:

- New optional columns (NULL or DEFAULT) — old code ignores new columns
- New tables — old code ignores them
- Remove a column only after the code that uses it is deployed and removed

**Avoid:** Removing or renaming a column in the same deployment as the code that stops using it — the code change and schema change may not roll out atomically.

---

### Key Takeaways

- Migrations are versioned, reversible, and checked into source control
- Never manually ALTER a production schema — always use migrations
- Additive changes (new nullable columns, new tables) are safe; destructive changes need multi-step plans
- Expand-contract pattern (add → backfill → remove) is the safe column-rename strategy
- Test rollbacks in staging; deploy schema changes before or separately from code changes
MD; }

    // ── QUESTIONS ─────────────────────────────────────────────────────────────

    private function seedQuestions(array $topics): void
    {
        $allQuestions = [
            1 => ['difficulty' => 'Easy',   'questions' => $this->l1Questions()],
            2 => ['difficulty' => 'Medium', 'questions' => $this->l2Questions()],
            3 => ['difficulty' => 'Hard',   'questions' => $this->l3Questions()],
            4 => ['difficulty' => 'Hard',   'questions' => $this->l4Questions()],
            5 => ['difficulty' => 'Hard',   'questions' => $this->l5Questions()],
        ];

        foreach ($allQuestions as $level => $data) {
            $topic = $topics[$level];
            foreach ($data['questions'] as $qData) {
                $exists = Question::where('topic_id', $topic->id)->where('question', $qData['question'])->exists();
                if ($exists) continue;
                $q = Question::create(['topic_id' => $topic->id, 'type' => 'MCQ', 'difficulty' => $data['difficulty'], 'question' => $qData['question'], 'explanation' => $qData['explanation']]);
                foreach ($qData['options'] as $opt) {
                    QuestionOption::create(['question_id' => $q->id, 'option_text' => $opt['text'], 'is_correct' => $opt['correct']]);
                }
            }
        }
    }

    private function l1Questions(): array { return [
        ['question' => 'Which SQL category does the CREATE TABLE statement belong to?', 'explanation' => 'CREATE TABLE is a DDL (Data Definition Language) statement — it defines the structure of the database. DDL includes CREATE, ALTER, DROP, TRUNCATE, and RENAME.', 'options' => [['text' => 'DDL', 'correct' => true], ['text' => 'DML', 'correct' => false], ['text' => 'DCL', 'correct' => false], ['text' => 'TCL', 'correct' => false]]],
        ['question' => 'Which SQL category includes GRANT and REVOKE?', 'explanation' => 'GRANT and REVOKE are DCL (Data Control Language) statements. They manage database permissions — who can perform which operations on which objects.', 'options' => [['text' => 'DCL', 'correct' => true], ['text' => 'DDL', 'correct' => false], ['text' => 'DML', 'correct' => false], ['text' => 'TCL', 'correct' => false]]],
        ['question' => 'What does NULL represent in SQL?', 'explanation' => 'NULL means the absence of a known value — it is not zero, not an empty string, and not false. SQL uses three-valued logic: TRUE, FALSE, and UNKNOWN (which is what comparisons with NULL return).', 'options' => [['text' => 'The absence of a known value', 'correct' => true], ['text' => 'Zero or an empty string', 'correct' => false], ['text' => 'A deleted row marker', 'correct' => false], ['text' => 'False in boolean context', 'correct' => false]]],
        ['question' => 'Which data type should you use to store monetary values accurately?', 'explanation' => 'DECIMAL stores exact decimal values — it is the correct choice for money. FLOAT and DOUBLE use binary floating-point which cannot represent most decimal fractions exactly (0.1 + 0.2 ≠ 0.3 in floating-point).', 'options' => [['text' => 'DECIMAL', 'correct' => true], ['text' => 'FLOAT', 'correct' => false], ['text' => 'DOUBLE', 'correct' => false], ['text' => 'BIGINT', 'correct' => false]]],
        ['question' => 'What is the correct way to check for NULL in a WHERE clause?', 'explanation' => 'NULL comparisons with = always return UNKNOWN (never TRUE), so WHERE column = NULL never matches any row. The correct syntax is WHERE column IS NULL or WHERE column IS NOT NULL.', 'options' => [['text' => 'WHERE column IS NULL', 'correct' => true], ['text' => 'WHERE column = NULL', 'correct' => false], ['text' => 'WHERE column == NULL', 'correct' => false], ['text' => 'WHERE column EQUALS NULL', 'correct' => false]]],
        ['question' => 'Which SQL statement is auto-committed in most databases and cannot be rolled back?', 'explanation' => 'DDL statements like DROP TABLE and ALTER TABLE are auto-committed in most databases (including MySQL). Once executed, they cannot be rolled back — unlike DML (INSERT, UPDATE, DELETE) which is transactional.', 'options' => [['text' => 'DROP TABLE (DDL)', 'correct' => true], ['text' => 'DELETE FROM table', 'correct' => false], ['text' => 'UPDATE table SET ...', 'correct' => false], ['text' => 'INSERT INTO table ...', 'correct' => false]]],
        ['question' => 'What is a FOREIGN KEY?', 'explanation' => 'A foreign key is a column (or set of columns) in one table that references the primary key of another table. It enforces referential integrity — the referenced value must exist in the parent table or be NULL.', 'options' => [['text' => 'A column that references the primary key of another table', 'correct' => true], ['text' => 'An alternate name for a primary key', 'correct' => false], ['text' => 'A key used to encrypt sensitive data', 'correct' => false], ['text' => 'An index on a non-unique column', 'correct' => false]]],
        ['question' => 'What is the difference between CHAR(10) and VARCHAR(10)?', 'explanation' => 'CHAR(10) always stores exactly 10 bytes, padding with spaces if the value is shorter. VARCHAR(10) stores only the actual bytes used plus a 1-2 byte length prefix. CHAR is faster for fixed-length data; VARCHAR is more space-efficient for variable-length data.', 'options' => [['text' => 'CHAR is fixed-length (padded); VARCHAR is variable-length (stores actual length)', 'correct' => true], ['text' => 'CHAR holds characters; VARCHAR holds numbers and characters', 'correct' => false], ['text' => 'CHAR is for single characters; VARCHAR is for up to 10 characters', 'correct' => false], ['text' => 'They are identical — CHAR is just an alias for VARCHAR', 'correct' => false]]],
        ['question' => 'Which TCL command permanently saves all changes made in the current transaction?', 'explanation' => 'COMMIT permanently saves all changes made since the last START TRANSACTION (or the previous COMMIT/ROLLBACK). After a COMMIT, changes are visible to other transactions and cannot be rolled back.', 'options' => [['text' => 'COMMIT', 'correct' => true], ['text' => 'SAVEPOINT', 'correct' => false], ['text' => 'ROLLBACK', 'correct' => false], ['text' => 'FLUSH', 'correct' => false]]],
        ['question' => 'What is a surrogate key?', 'explanation' => 'A surrogate key is an artificially generated identifier with no business meaning — typically an auto-increment integer or UUID. It is created purely to uniquely identify rows. In contrast, a natural key has real-world meaning (like an email address or ISBN).', 'options' => [['text' => 'An artificial key with no business meaning, generated by the system', 'correct' => true], ['text' => 'A secondary key used as a backup for the primary key', 'correct' => false], ['text' => 'A key shared between two tables', 'correct' => false], ['text' => 'A key derived from a real-world attribute', 'correct' => false]]],
    ]; }

    private function l2Questions(): array { return [
        ['question' => 'In what logical order does SQL process query clauses?', 'explanation' => 'SQL logical processing order: FROM → JOIN → WHERE → GROUP BY → HAVING → SELECT → ORDER BY → LIMIT. This order explains why SELECT aliases cannot be used in WHERE (SELECT hasn\'t run yet) but can be used in ORDER BY (which runs after SELECT).', 'options' => [['text' => 'FROM → WHERE → GROUP BY → HAVING → SELECT → ORDER BY', 'correct' => true], ['text' => 'SELECT → FROM → WHERE → GROUP BY → HAVING → ORDER BY', 'correct' => false], ['text' => 'FROM → SELECT → WHERE → GROUP BY → HAVING → ORDER BY', 'correct' => false], ['text' => 'WHERE → FROM → GROUP BY → SELECT → HAVING → ORDER BY', 'correct' => false]]],
        ['question' => 'Why can you not use a SELECT alias in the WHERE clause?', 'explanation' => 'WHERE is processed before SELECT in the logical execution order. The alias defined in SELECT does not exist yet when WHERE runs. You must repeat the full expression in WHERE, or use a CTE/subquery to make the alias available.', 'options' => [['text' => 'WHERE executes before SELECT — the alias does not exist yet at that stage', 'correct' => true], ['text' => 'SQL does not allow aliases in WHERE as a security measure', 'correct' => false], ['text' => 'Aliases are only allowed in ORDER BY clauses', 'correct' => false], ['text' => 'WHERE cannot reference column expressions, only column names', 'correct' => false]]],
        ['question' => 'What is the key difference between WHERE and HAVING?', 'explanation' => 'WHERE filters individual rows before grouping (operates on raw table data). HAVING filters groups after GROUP BY (operates on aggregated data). HAVING can reference aggregate functions like COUNT() and SUM(); WHERE cannot.', 'options' => [['text' => 'WHERE filters rows before grouping; HAVING filters groups after GROUP BY', 'correct' => true], ['text' => 'WHERE works on indexed columns; HAVING works on non-indexed columns', 'correct' => false], ['text' => 'They are identical — HAVING is just an older syntax for WHERE', 'correct' => false], ['text' => 'HAVING works before GROUP BY; WHERE works after', 'correct' => false]]],
        ['question' => 'What does UNION ALL do differently from UNION?', 'explanation' => 'UNION removes duplicate rows from the combined result (like DISTINCT). UNION ALL keeps all rows including duplicates and is faster because it skips the deduplication step. Use UNION ALL when you know results are distinct or when duplicates are acceptable.', 'options' => [['text' => 'UNION ALL keeps all rows including duplicates; UNION deduplicates', 'correct' => true], ['text' => 'UNION ALL returns more columns; UNION returns more rows', 'correct' => false], ['text' => 'UNION ALL is slower because it checks for duplicates', 'correct' => false], ['text' => 'They are identical — ALL is just a style keyword', 'correct' => false]]],
        ['question' => 'What is a CROSS JOIN?', 'explanation' => 'A CROSS JOIN produces the Cartesian product of two tables — every row from the left table paired with every row from the right table. If the left table has 10 rows and the right has 5, the result has 50 rows. All other JOINs are filtered Cartesian products.', 'options' => [['text' => 'A JOIN that returns every combination of rows from both tables (Cartesian product)', 'correct' => true], ['text' => 'A JOIN that returns only rows that exist in both tables', 'correct' => false], ['text' => 'A JOIN that combines two tables sharing a column name automatically', 'correct' => false], ['text' => 'A JOIN that returns rows from the left table not found in the right', 'correct' => false]]],
        ['question' => 'What does EXISTS return in a SQL subquery?', 'explanation' => 'EXISTS returns TRUE if the subquery produces at least one row, FALSE if it produces no rows. It stops evaluating as soon as it finds the first matching row (short-circuit), making it faster than IN when the subquery returns many rows.', 'options' => [['text' => 'TRUE if the subquery returns at least one row, FALSE otherwise', 'correct' => true], ['text' => 'The first row returned by the subquery', 'correct' => false], ['text' => 'The count of rows returned by the subquery', 'correct' => false], ['text' => 'The subquery result as a single value', 'correct' => false]]],
        ['question' => 'Which SQL set operation returns only rows that appear in both result sets?', 'explanation' => 'INTERSECT returns only the rows that are common to both SELECT statements. UNION combines all rows (with deduplication), EXCEPT removes the second set from the first, and CROSS JOIN is a JOIN type, not a set operation.', 'options' => [['text' => 'INTERSECT', 'correct' => true], ['text' => 'UNION', 'correct' => false], ['text' => 'EXCEPT', 'correct' => false], ['text' => 'CROSS JOIN', 'correct' => false]]],
        ['question' => 'What is a correlated subquery?', 'explanation' => 'A correlated subquery references columns from the outer query. It is re-executed for each row of the outer query, making it O(n) in performance. For large datasets, correlated subqueries should be rewritten as JOINs or CTEs.', 'options' => [['text' => 'A subquery that references a column from the outer query and re-executes per outer row', 'correct' => true], ['text' => 'A subquery that returns a single scalar value', 'correct' => false], ['text' => 'A subquery nested inside another subquery', 'correct' => false], ['text' => 'A subquery used in a FROM clause', 'correct' => false]]],
        ['question' => 'In which step of SQL logical processing is ORDER BY evaluated?', 'explanation' => 'ORDER BY is evaluated after SELECT — it is one of the last steps. This is why ORDER BY can reference aliases defined in SELECT, while WHERE cannot (WHERE runs before SELECT).', 'options' => [['text' => 'After SELECT — which is why ORDER BY can use SELECT aliases', 'correct' => true], ['text' => 'Before WHERE — to prepare sorted data for filtering', 'correct' => false], ['text' => 'Between GROUP BY and HAVING', 'correct' => false], ['text' => 'At the same time as WHERE', 'correct' => false]]],
        ['question' => 'What operation in relational algebra corresponds to the SQL WHERE clause?', 'explanation' => 'The SQL WHERE clause corresponds to the Selection operation (σ) in relational algebra. Selection filters rows based on a predicate. Projection (π) corresponds to SELECT column list. Join (⋈) corresponds to JOIN.', 'options' => [['text' => 'Selection (σ) — filters rows based on a condition', 'correct' => true], ['text' => 'Projection (π) — selects specific columns', 'correct' => false], ['text' => 'Union (∪) — combines two relations', 'correct' => false], ['text' => 'Join (⋈) — combines related rows from two tables', 'correct' => false]]],
    ]; }

    private function l3Questions(): array { return [
        ['question' => 'What type of dependency does Second Normal Form (2NF) eliminate?', 'explanation' => '2NF eliminates partial dependencies — where a non-key attribute depends on only part of a composite primary key. Partial dependencies can only exist when the primary key is composite. If the PK is a single column, a table in 1NF is automatically in 2NF.', 'options' => [['text' => 'Partial dependencies — non-key attributes depending on only part of a composite PK', 'correct' => true], ['text' => 'Transitive dependencies — non-key attributes depending on other non-key attributes', 'correct' => false], ['text' => 'Functional dependencies — all attribute dependencies', 'correct' => false], ['text' => 'Multi-valued dependencies — attributes determining sets of values', 'correct' => false]]],
        ['question' => 'What does Third Normal Form (3NF) require beyond 2NF?', 'explanation' => '3NF eliminates transitive dependencies: a non-key attribute must not depend on another non-key attribute. Example: employees(id, department_id, department_name) — department_name depends on department_id (non-key), not on id. Fix: move department_name to a departments table.', 'options' => [['text' => 'No transitive dependencies — non-key attributes must depend only on the primary key', 'correct' => true], ['text' => 'No partial dependencies on composite keys', 'correct' => false], ['text' => 'Every determinant must be a candidate key', 'correct' => false], ['text' => 'All columns must have atomic values', 'correct' => false]]],
        ['question' => 'What is a weak entity in ER modelling?', 'explanation' => 'A weak entity cannot be uniquely identified by its own attributes alone — it depends on an owning (strong) entity for identification. Example: OrderItem depends on Order. In SQL, weak entities use a composite PK that includes the parent\'s FK.', 'options' => [['text' => 'An entity that cannot be uniquely identified without its owner entity', 'correct' => true], ['text' => 'An entity with no foreign keys', 'correct' => false], ['text' => 'An optional entity that may have no rows', 'correct' => false], ['text' => 'An entity with a nullable primary key', 'correct' => false]]],
        ['question' => 'How is a Many-to-Many relationship implemented in SQL?', 'explanation' => 'A Many-to-Many (M:N) relationship is implemented with a junction table (also called associative, bridge, or pivot table). The junction table has foreign keys referencing both related tables and typically uses a composite primary key from both FKs.', 'options' => [['text' => 'With a junction (pivot) table that holds foreign keys to both related tables', 'correct' => true], ['text' => 'With two foreign keys — one in each table pointing to the other', 'correct' => false], ['text' => 'With a JSON column storing arrays of related IDs', 'correct' => false], ['text' => 'Many-to-many cannot be represented in relational databases', 'correct' => false]]],
        ['question' => 'What is the First Normal Form (1NF) requirement for columns?', 'explanation' => '1NF requires that every column hold atomic (indivisible) values — no arrays, no comma-separated lists, no repeating groups. Each cell must hold exactly one value of its declared type. Violating this makes queries and constraints nearly impossible to enforce.', 'options' => [['text' => 'Every column must hold atomic (indivisible, single) values', 'correct' => true], ['text' => 'Every column must have a NOT NULL constraint', 'correct' => false], ['text' => 'Every column must have a unique index', 'correct' => false], ['text' => 'Every column must be of the same data type', 'correct' => false]]],
        ['question' => 'What does ON DELETE CASCADE mean on a foreign key?', 'explanation' => 'ON DELETE CASCADE means that when a parent row is deleted, all child rows referencing it via foreign key are automatically deleted as well. This maintains referential integrity without requiring manual deletion of child rows first.', 'options' => [['text' => 'Child rows are automatically deleted when the parent row is deleted', 'correct' => true], ['text' => 'The delete is cascaded to the parent table as well', 'correct' => false], ['text' => 'The FK column in child rows is set to NULL when the parent is deleted', 'correct' => false], ['text' => 'The parent delete is blocked if any child rows exist', 'correct' => false]]],
        ['question' => 'What is a derived attribute in ER modelling?', 'explanation' => 'A derived attribute is one whose value can be calculated from other stored data — for example, age calculated from date_of_birth, or total calculated from price × quantity. Derived attributes should generally not be stored; they should be computed in queries to avoid update anomalies.', 'options' => [['text' => 'An attribute computed from other stored data — should be calculated in queries, not stored', 'correct' => true], ['text' => 'An attribute inherited from a parent entity', 'correct' => false], ['text' => 'A foreign key attribute', 'correct' => false], ['text' => 'An attribute with a default value', 'correct' => false]]],
        ['question' => 'What is BCNF (Boyce-Codd Normal Form)?', 'explanation' => 'BCNF requires that for every non-trivial functional dependency A → B, A must be a super key. It is a stricter version of 3NF that handles edge cases where multiple overlapping candidate keys exist. A table can be in 3NF but violate BCNF.', 'options' => [['text' => 'Every determinant in a functional dependency must be a super key', 'correct' => true], ['text' => 'All columns must be functionally dependent only on the primary key', 'correct' => false], ['text' => 'No two rows can have the same combination of non-key values', 'correct' => false], ['text' => 'Every column must have a unique constraint', 'correct' => false]]],
        ['question' => 'What is referential integrity?', 'explanation' => 'Referential integrity is the database rule that a foreign key value must either be NULL or match an existing primary key value in the referenced table. It prevents orphan rows — child records referencing a parent that does not exist.', 'options' => [['text' => 'The rule that a FK value must match an existing PK in the referenced table or be NULL', 'correct' => true], ['text' => 'The requirement that all primary keys be integers', 'correct' => false], ['text' => 'The guarantee that all queries return consistent results', 'correct' => false], ['text' => 'The rule that references between tables use the same data type', 'correct' => false]]],
        ['question' => 'What is the cardinality of a relationship in ER modelling?', 'explanation' => 'Cardinality describes how many instances of one entity can be associated with instances of another. The three main types are: One-to-One (1:1), One-to-Many (1:N), and Many-to-Many (M:N). Cardinality drives the physical schema design — particularly where to place foreign keys.', 'options' => [['text' => 'How many instances of one entity can relate to instances of another (1:1, 1:N, M:N)', 'correct' => true], ['text' => 'The number of columns in a table', 'correct' => false], ['text' => 'The number of unique values in a column', 'correct' => false], ['text' => 'The maximum number of rows a table can hold', 'correct' => false]]],
    ]; }

    private function l4Questions(): array { return [
        ['question' => 'What is the main advantage of a CTE over a subquery in a complex query?', 'explanation' => 'A CTE is named, reusable within the same query, and easier to read and debug — you can test each CTE independently. A subquery (especially a derived table in FROM) must be repeated if used more than once and becomes hard to read when nested deeply.', 'options' => [['text' => 'CTEs are named and reusable within the query — easier to read and debug than nested subqueries', 'correct' => true], ['text' => 'CTEs are always faster than subqueries due to caching', 'correct' => false], ['text' => 'CTEs allow JOINs; subqueries do not', 'correct' => false], ['text' => 'CTEs bypass query optimisation for guaranteed performance', 'correct' => false]]],
        ['question' => 'What does PARTITION BY do in a window function?', 'explanation' => 'PARTITION BY divides the result set into partitions (groups) on which the window function operates independently. Unlike GROUP BY, it does not collapse rows — each row is still returned, but the window function computes its value within the context of its partition.', 'options' => [['text' => 'Divides rows into groups within which the window function operates independently, without collapsing rows', 'correct' => true], ['text' => 'Sorts the rows within the window', 'correct' => false], ['text' => 'Limits the number of rows the window function processes', 'correct' => false], ['text' => 'Defines the physical storage partition for the table', 'correct' => false]]],
        ['question' => 'What is the difference between RANK() and DENSE_RANK()?', 'explanation' => 'For tied values, RANK() leaves gaps in the ranking sequence (1, 1, 3 — skips 2), while DENSE_RANK() does not leave gaps (1, 1, 2). ROW_NUMBER() always produces unique sequential numbers regardless of ties.', 'options' => [['text' => 'RANK() leaves gaps after ties; DENSE_RANK() does not (1,1,3 vs 1,1,2)', 'correct' => true], ['text' => 'RANK() is for text columns; DENSE_RANK() is for numeric columns', 'correct' => false], ['text' => 'DENSE_RANK() is slower because it counts all rows first', 'correct' => false], ['text' => 'They are identical — DENSE is just a style keyword', 'correct' => false]]],
        ['question' => 'What is a phantom read in database concurrency?', 'explanation' => 'A phantom read occurs when a transaction runs the same range query twice and gets different results because another transaction inserted or deleted rows in between. Prevented by the SERIALIZABLE isolation level (and partially by InnoDB\'s gap locks at REPEATABLE READ).', 'options' => [['text' => 'Running the same range query twice and getting different rows because another tx inserted/deleted between runs', 'correct' => true], ['text' => 'Reading a row that was deleted by another transaction', 'correct' => false], ['text' => 'Reading uncommitted data from another transaction', 'correct' => false], ['text' => 'A query that returns no rows due to a missing index', 'correct' => false]]],
        ['question' => 'What isolation level prevents dirty reads but still allows non-repeatable reads?', 'explanation' => 'READ COMMITTED prevents dirty reads (you only see committed data) but allows non-repeatable reads — if another transaction commits a change between your two reads of the same row, you will see different values. It is the default in PostgreSQL.', 'options' => [['text' => 'READ COMMITTED', 'correct' => true], ['text' => 'READ UNCOMMITTED', 'correct' => false], ['text' => 'REPEATABLE READ', 'correct' => false], ['text' => 'SERIALIZABLE', 'correct' => false]]],
        ['question' => 'What does SELECT ... FOR UPDATE do?', 'explanation' => 'SELECT ... FOR UPDATE acquires an exclusive lock on the selected rows at read time, preventing other transactions from modifying or locking them until the current transaction commits or rolls back. This prevents the lost update problem in read-modify-write patterns.', 'options' => [['text' => 'Acquires an exclusive lock on the rows, preventing other transactions from modifying them until commit', 'correct' => true], ['text' => 'Marks the rows as dirty so they are re-read on next query', 'correct' => false], ['text' => 'Allows the rows to be updated by any user without a transaction', 'correct' => false], ['text' => 'Creates a snapshot of the selected rows for rollback purposes', 'correct' => false]]],
        ['question' => 'When should you prefer EXISTS over IN for a subquery?', 'explanation' => 'EXISTS is more efficient than IN when the subquery returns many rows, because EXISTS short-circuits — it stops as soon as it finds the first matching row. IN must collect the complete subquery result set before evaluating. Also, NOT IN with NULLs in the subquery produces unexpected results; NOT EXISTS handles NULLs correctly.', 'options' => [['text' => 'When the subquery returns many rows — EXISTS short-circuits at first match, IN collects all results first', 'correct' => true], ['text' => 'When the subquery uses aggregate functions', 'correct' => false], ['text' => 'When the subquery references a different database', 'correct' => false], ['text' => 'EXISTS and IN are always identical in performance', 'correct' => false]]],
        ['question' => 'What is MVCC (Multi-Version Concurrency Control)?', 'explanation' => 'MVCC maintains multiple versions of rows so readers never block writers. When a transaction reads data, it sees the snapshot from when its transaction started — not the current state being modified by concurrent writers. InnoDB implements MVCC via the undo log.', 'options' => [['text' => 'Keeping multiple row versions so readers see a consistent snapshot without blocking writers', 'correct' => true], ['text' => 'A system for managing multiple concurrent connections', 'correct' => false], ['text' => 'Replicating data across multiple servers for consistency', 'correct' => false], ['text' => 'A locking protocol that prevents deadlocks', 'correct' => false]]],
        ['question' => 'What is the purpose of a recursive CTE?', 'explanation' => 'A recursive CTE allows a query to reference itself — each iteration builds on the previous result until no more rows are added. It is the standard SQL way to traverse hierarchical data like org charts, category trees, bill of materials, and file systems.', 'options' => [['text' => 'To traverse hierarchical or graph data by iteratively expanding rows', 'correct' => true], ['text' => 'To repeat a query a fixed number of times for performance testing', 'correct' => false], ['text' => 'To compute recursive aggregate functions like cumulative sums', 'correct' => false], ['text' => 'To join a table to itself more than twice', 'correct' => false]]],
        ['question' => 'What is the ROWS BETWEEN clause in a window function used for?', 'explanation' => 'The ROWS BETWEEN clause defines the window frame — which physical rows relative to the current row the window function includes in its calculation. For example, ROWS BETWEEN 2 PRECEDING AND CURRENT ROW creates a 3-row rolling window. Without a frame, the default for ordered windows is RANGE BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW.', 'options' => [['text' => 'Defines which rows relative to the current row are included in the window function calculation', 'correct' => true], ['text' => 'Limits the total number of rows the query returns', 'correct' => false], ['text' => 'Specifies the partition column for PARTITION BY', 'correct' => false], ['text' => 'Controls the sort order within each window partition', 'correct' => false]]],
    ]; }

    private function l5Questions(): array { return [
        ['question' => 'What is SQL injection?', 'explanation' => 'SQL injection is an attack where malicious SQL is inserted into a query by including it in user-supplied input that is directly concatenated into the SQL string. The attacker\'s SQL executes with the same privileges as the application\'s database user.', 'options' => [['text' => 'An attack where malicious SQL in user input is executed by the database because it was concatenated into a query', 'correct' => true], ['text' => 'A technique for inserting large amounts of data quickly', 'correct' => false], ['text' => 'A method for injecting SQL functions into stored procedures', 'correct' => false], ['text' => 'A database migration tool that inserts SQL from files', 'correct' => false]]],
        ['question' => 'How do parameterized queries prevent SQL injection?', 'explanation' => 'Parameterized queries (prepared statements) separate SQL code from data. The query structure is compiled by the database engine first; user input is then passed as a typed parameter. The database never interprets the input as SQL — only as a value.', 'options' => [['text' => 'They separate SQL code from data — user input is passed as a typed value, never parsed as SQL', 'correct' => true], ['text' => 'They escape all special characters in the input string', 'correct' => false], ['text' => 'They limit the length of user input to prevent buffer overflows', 'correct' => false], ['text' => 'They hash user input before inserting it into the query', 'correct' => false]]],
        ['question' => 'Why can column and table names NOT be parameterized in SQL queries?', 'explanation' => 'SQL parameters (? or :name) replace data values in WHERE clauses and INSERT values. They cannot replace structural SQL elements like column names, table names, or ORDER BY directions — these are compiled as part of the query structure, not passed as data. Use allowlists to safely handle dynamic column/table names.', 'options' => [['text' => 'Parameters replace data values only — structural SQL elements (column names, table names) must be allowlisted in code', 'correct' => true], ['text' => 'Column names are case-sensitive, making parameterisation ambiguous', 'correct' => false], ['text' => 'Database drivers do not support parameters for DDL operations', 'correct' => false], ['text' => 'Parameterizing column names would break query caching', 'correct' => false]]],
        ['question' => 'What is the N+1 query problem?', 'explanation' => 'The N+1 problem occurs when an application runs 1 query to fetch a list of N items, then N additional queries to fetch related data for each item — totaling N+1 queries. Fix: use a JOIN or eager loading to fetch everything in 1 or 2 queries.', 'options' => [['text' => '1 query fetches N items, then N queries fetch related data per item — totaling N+1 trips to the database', 'correct' => true], ['text' => 'A query returns N+1 rows instead of the expected N due to a JOIN', 'correct' => false], ['text' => 'An off-by-one error in LIMIT/OFFSET pagination', 'correct' => false], ['text' => 'A deadlock caused by N+1 concurrent transactions', 'correct' => false]]],
        ['question' => 'What is a zero-downtime database migration?', 'explanation' => 'A zero-downtime migration applies schema changes while the application continues serving traffic — no maintenance window required. It uses patterns like expand-contract (add column first, backfill, then remove old column) and online DDL to avoid table locks.', 'options' => [['text' => 'Applying schema changes while the application continues serving traffic with no maintenance window', 'correct' => true], ['text' => 'A migration that runs in under one second', 'correct' => false], ['text' => 'A migration that only adds new tables without modifying existing ones', 'correct' => false], ['text' => 'A migration performed during off-peak hours to minimise user impact', 'correct' => false]]],
        ['question' => 'What is the expand-contract migration pattern used for?', 'explanation' => 'The expand-contract (also called parallel-change) pattern makes breaking schema changes safely: (1) Expand — add the new structure alongside the old; (2) Migrate — backfill data and update application to use both; (3) Contract — remove the old structure once all code is updated. Used for renaming columns, changing types, etc.', 'options' => [['text' => 'Making breaking schema changes safely by adding new structure, migrating data, then removing old structure across multiple deployments', 'correct' => true], ['text' => 'Expanding table storage then contracting it by archiving old rows', 'correct' => false], ['text' => 'Using transactions to expand and immediately contract schema changes atomically', 'correct' => false], ['text' => 'A backup strategy that expands full backups with incremental ones', 'correct' => false]]],
        ['question' => 'What is the principle of least privilege in database security?', 'explanation' => 'Least privilege means granting a database user only the specific permissions it needs — no more. An application user typically needs SELECT, INSERT, UPDATE, DELETE on its tables but should never have DROP, CREATE, GRANT, or FILE privileges. This limits damage if credentials are compromised.', 'options' => [['text' => 'Granting database users only the minimum permissions required for their function', 'correct' => true], ['text' => 'Storing passwords with the minimum number of characters for performance', 'correct' => false], ['text' => 'Limiting the number of database connections per user', 'correct' => false], ['text' => 'Using the cheapest hardware tier for non-critical databases', 'correct' => false]]],
        ['question' => 'What is the difference between optimistic and pessimistic locking?', 'explanation' => 'Pessimistic locking acquires a lock before reading (SELECT ... FOR UPDATE), blocking other writers for the duration. Optimistic locking reads without a lock but checks at write time whether the data changed (via a version column). Optimistic scales better for low-contention; pessimistic is safer for high-contention scenarios.', 'options' => [['text' => 'Pessimistic locks at read time (FOR UPDATE); optimistic reads freely and checks for conflicts at write time (version column)', 'correct' => true], ['text' => 'Pessimistic locking is for reads; optimistic locking is for writes', 'correct' => false], ['text' => 'Optimistic locking uses database locks; pessimistic locking uses application-level flags', 'correct' => false], ['text' => 'They are identical — the names refer to the developer\'s attitude, not the implementation', 'correct' => false]]],
        ['question' => 'Why should LIMIT without ORDER BY be avoided in production queries?', 'explanation' => 'Without ORDER BY, the database returns rows in undefined order — typically the physical storage order, which can change after UPDATEs, vacuum operations, or index rebuilds. The same query may return different rows on different runs. Always pair LIMIT with ORDER BY for deterministic, reproducible results.', 'options' => [['text' => 'Without ORDER BY, the returned rows are undefined — the same query may return different rows on different runs', 'correct' => true], ['text' => 'LIMIT without ORDER BY is a SQL syntax error', 'correct' => false], ['text' => 'It causes a full table scan regardless of available indexes', 'correct' => false], ['text' => 'It prevents the query from using the LIMIT value correctly', 'correct' => false]]],
        ['question' => 'What makes cursor-based pagination faster than OFFSET pagination on large tables?', 'explanation' => 'OFFSET pagination (LIMIT 10 OFFSET 10000) causes the database to scan and discard 10,000 rows before returning the next 10. Cursor-based pagination (WHERE id > last_seen_id LIMIT 10) uses an index to jump directly to the right position — O(log n) instead of O(offset).', 'options' => [['text' => 'Cursor-based uses an index to find the start position; OFFSET scans and discards all preceding rows', 'correct' => true], ['text' => 'Cursor-based pagination uses a cache; OFFSET always hits the disk', 'correct' => false], ['text' => 'OFFSET is only slow because it lacks an ORDER BY clause', 'correct' => false], ['text' => 'They perform identically — cursor-based is only preferred for code readability', 'correct' => false]]],
    ]; }
}
