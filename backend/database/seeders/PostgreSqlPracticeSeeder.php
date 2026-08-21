<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;

class PostgreSqlPracticeSeeder extends Seeder
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
                'description'       => 'Advanced open-source relational database — from core SQL to JSONB, MVCC, and production tuning.',
                'display_order'     => 2,
            ]
        );

        $levels = [
            [
                'slug'       => 'postgresql-junior',
                'title'      => 'PostgreSQL Basics — Junior',
                'order'      => 1,
                'difficulty' => 'Easy',
                'questions'  => $this->juniorQuestions(),
            ],
            [
                'slug'       => 'postgresql-intermediate',
                'title'      => 'PostgreSQL Intermediate',
                'order'      => 2,
                'difficulty' => 'Medium',
                'questions'  => $this->intermediateQuestions(),
            ],
            [
                'slug'       => 'postgresql-advanced',
                'title'      => 'PostgreSQL Advanced',
                'order'      => 3,
                'difficulty' => 'Hard',
                'questions'  => $this->advancedQuestions(),
            ],
        ];

        foreach ($levels as $levelData) {
            $topic = Topic::firstOrCreate(
                ['slug' => $levelData['slug']],
                [
                    'subject_id'    => $subject->id,
                    'title'         => $levelData['title'],
                    'display_order' => $levelData['order'],
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
    }

    private function juniorQuestions(): array
    {
        return [
            [
                'question'    => 'What is PostgreSQL?',
                'explanation' => 'PostgreSQL is a free, open-source, advanced relational database management system (RDBMS). It is known for ACID compliance, extensibility, support for complex queries, JSON, and a rich set of data types beyond the SQL standard.',
                'options'     => [
                    ['text' => 'A free, open-source RDBMS known for ACID compliance, extensibility, and rich data type support', 'correct' => true],
                    ['text' => 'A proprietary relational database developed and maintained by Oracle Corporation',                 'correct' => false],
                    ['text' => 'A NoSQL document database that stores data as JSON collections',                                 'correct' => false],
                    ['text' => 'A distributed in-memory database used primarily for caching',                                   'correct' => false],
                ],
            ],
            [
                'question'    => 'Which SQL command retrieves data from a PostgreSQL table?',
                'explanation' => 'SELECT is used to read data from one or more tables. Example: SELECT * FROM users returns all columns and rows from the users table.',
                'options'     => [
                    ['text' => 'SELECT', 'correct' => true],
                    ['text' => 'GET',    'correct' => false],
                    ['text' => 'FETCH',  'correct' => false],
                    ['text' => 'READ',   'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a PRIMARY KEY in PostgreSQL?',
                'explanation' => 'A PRIMARY KEY uniquely identifies each row in a table. It combines NOT NULL and UNIQUE constraints automatically. A table can have only one primary key, though it can span multiple columns (composite key).',
                'options'     => [
                    ['text' => 'A column (or set of columns) that uniquely identifies every row and cannot be NULL', 'correct' => true],
                    ['text' => 'The first column defined in the CREATE TABLE statement',                            'correct' => false],
                    ['text' => 'A column used only to create indexes on the table',                                 'correct' => false],
                    ['text' => 'A column that links two tables in a JOIN operation',                               'correct' => false],
                ],
            ],
            [
                'question'    => 'Which SQL command adds new rows to a PostgreSQL table?',
                'explanation' => 'INSERT INTO adds one or more rows to a table. Example: INSERT INTO products (name, price) VALUES (\'Laptop\', 999.99). PostgreSQL also supports INSERT ... RETURNING to get back the inserted row.',
                'options'     => [
                    ['text' => 'INSERT INTO', 'correct' => true],
                    ['text' => 'ADD INTO',    'correct' => false],
                    ['text' => 'PUT INTO',    'correct' => false],
                    ['text' => 'APPEND INTO', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which SQL command modifies existing rows in a PostgreSQL table?',
                'explanation' => 'UPDATE modifies existing records. It uses SET to specify new values and WHERE to target specific rows. Without WHERE, every row in the table is updated. PostgreSQL supports UPDATE ... RETURNING to fetch the updated rows.',
                'options'     => [
                    ['text' => 'UPDATE', 'correct' => true],
                    ['text' => 'MODIFY', 'correct' => false],
                    ['text' => 'CHANGE', 'correct' => false],
                    ['text' => 'EDIT',   'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the key difference between DELETE and TRUNCATE in PostgreSQL?',
                'explanation' => 'DELETE removes specific rows (can use WHERE) and is transactional — it can be rolled back. TRUNCATE removes ALL rows instantly, is faster, and also transactional in PostgreSQL (unlike MySQL). TRUNCATE resets sequences on the table when RESTART IDENTITY is specified.',
                'options'     => [
                    ['text' => 'DELETE removes specific rows with optional WHERE and is transactional; TRUNCATE removes all rows instantly and is also transactional in PostgreSQL', 'correct' => true],
                    ['text' => 'TRUNCATE removes specific rows based on a WHERE clause; DELETE removes all rows',                                                                   'correct' => false],
                    ['text' => 'DELETE is faster than TRUNCATE because it bypasses the WAL',                                                                                       'correct' => false],
                    ['text' => 'There is no difference; both commands work identically in PostgreSQL',                                                                             'correct' => false],
                ],
            ],
            [
                'question'    => 'Which clause filters rows returned by a SELECT query?',
                'explanation' => 'The WHERE clause filters rows based on a condition before aggregation or return. Example: SELECT * FROM orders WHERE status = \'shipped\'.',
                'options'     => [
                    ['text' => 'WHERE',  'correct' => true],
                    ['text' => 'FILTER', 'correct' => false],
                    ['text' => 'HAVING', 'correct' => false],
                    ['text' => 'MATCH',  'correct' => false],
                ],
            ],
            [
                'question'    => 'Which SQL clause sorts the result set of a query in PostgreSQL?',
                'explanation' => 'ORDER BY sorts the result in ascending (ASC, default) or descending (DESC) order. PostgreSQL also supports NULLS FIRST and NULLS LAST to control where NULL values appear in sorted output.',
                'options'     => [
                    ['text' => 'ORDER BY',  'correct' => true],
                    ['text' => 'SORT BY',   'correct' => false],
                    ['text' => 'GROUP BY',  'correct' => false],
                    ['text' => 'ARRANGE BY', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the DISTINCT keyword do in a PostgreSQL SELECT query?',
                'explanation' => 'DISTINCT eliminates duplicate rows from the result set. Example: SELECT DISTINCT country FROM customers returns each country only once. PostgreSQL also supports DISTINCT ON (column) to keep only the first row for each distinct value of the specified column.',
                'options'     => [
                    ['text' => 'Removes duplicate rows from the result set', 'correct' => true],
                    ['text' => 'Selects only the first row of each group',   'correct' => false],
                    ['text' => 'Filters rows based on a condition',          'correct' => false],
                    ['text' => 'Returns rows sorted alphabetically',         'correct' => false],
                ],
            ],
            [
                'question'    => 'Which clause limits the number of rows returned in PostgreSQL?',
                'explanation' => 'LIMIT restricts the number of rows returned. Example: SELECT * FROM products LIMIT 20 returns at most 20 rows. LIMIT combined with OFFSET is the standard pagination pattern in PostgreSQL.',
                'options'     => [
                    ['text' => 'LIMIT',    'correct' => true],
                    ['text' => 'TOP',      'correct' => false],
                    ['text' => 'RESTRICT', 'correct' => false],
                    ['text' => 'ROWNUM',   'correct' => false],
                ],
            ],
            [
                'question'    => 'What is NULL in PostgreSQL?',
                'explanation' => 'NULL represents a missing or unknown value. It is not the same as 0, false, or an empty string. Comparisons with NULL always produce NULL, so use IS NULL or IS NOT NULL instead of = NULL.',
                'options'     => [
                    ['text' => 'A special marker representing a missing or unknown value', 'correct' => true],
                    ['text' => 'An integer value of zero',                                 'correct' => false],
                    ['text' => 'An empty string value \'\'',                               'correct' => false],
                    ['text' => 'A boolean value representing false',                       'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between VARCHAR and TEXT in PostgreSQL?',
                'explanation' => 'In PostgreSQL, VARCHAR(n) limits the string to n characters, while TEXT has no length limit. Importantly, both are stored identically internally — there is no performance difference. TEXT is often preferred in PostgreSQL for its simplicity.',
                'options'     => [
                    ['text' => 'VARCHAR(n) limits length to n characters; TEXT has no limit — both perform identically in PostgreSQL', 'correct' => true],
                    ['text' => 'TEXT is slower than VARCHAR because it does not use indexes',                                         'correct' => false],
                    ['text' => 'VARCHAR stores Unicode; TEXT stores ASCII only',                                                      'correct' => false],
                    ['text' => 'TEXT is deprecated in PostgreSQL 14+; VARCHAR should always be used',                                 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a FOREIGN KEY in PostgreSQL?',
                'explanation' => 'A FOREIGN KEY constrains a column to only hold values that exist in the referenced table\'s primary key (or unique key). It enforces referential integrity. PostgreSQL supports ON DELETE CASCADE, SET NULL, RESTRICT, and SET DEFAULT actions.',
                'options'     => [
                    ['text' => 'A column that references the primary key of another table to enforce referential integrity', 'correct' => true],
                    ['text' => 'A secondary key used when the main primary key is unavailable',                            'correct' => false],
                    ['text' => 'A key used to encrypt sensitive column data',                                               'correct' => false],
                    ['text' => 'A column that stores IDs from external (foreign) systems',                                  'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the SERIAL data type in PostgreSQL?',
                'explanation' => 'SERIAL is a shorthand that creates an INTEGER column with an auto-incrementing sequence attached. It is equivalent to defining an INTEGER column and using nextval() from a sequence. BIGSERIAL does the same for BIGINT. PostgreSQL 10+ recommends using GENERATED ALWAYS AS IDENTITY instead.',
                'options'     => [
                    ['text' => 'An integer column backed by an auto-incrementing sequence, used for surrogate primary keys', 'correct' => true],
                    ['text' => 'A special string type that stores sequential alphanumeric codes',                            'correct' => false],
                    ['text' => 'A data type for storing ordered lists of integers',                                         'correct' => false],
                    ['text' => 'An alias for the SEQUENCE object used to number rows after a query',                        'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between INTEGER and BIGINT in PostgreSQL?',
                'explanation' => 'INTEGER (INT4) stores values from -2,147,483,648 to 2,147,483,647 (4 bytes). BIGINT (INT8) stores values from about -9.2 quintillion to 9.2 quintillion (8 bytes). Use BIGINT for IDs or counters that might exceed 2 billion.',
                'options'     => [
                    ['text' => 'INTEGER is 4 bytes (~2.1 billion range); BIGINT is 8 bytes (~9.2 quintillion range)', 'correct' => true],
                    ['text' => 'INTEGER stores decimals; BIGINT stores whole numbers only',                          'correct' => false],
                    ['text' => 'BIGINT is faster for arithmetic; INTEGER is better for storage',                     'correct' => false],
                    ['text' => 'They are identical; BIGINT is just an alias with a wider display width',             'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the NOT NULL constraint enforce in PostgreSQL?',
                'explanation' => 'NOT NULL prevents a column from holding NULL values. Every INSERT or UPDATE must supply a non-NULL value for the column or the operation fails with a constraint violation error.',
                'options'     => [
                    ['text' => 'The column must always have a value and cannot be NULL', 'correct' => true],
                    ['text' => 'The column must have unique values across all rows',     'correct' => false],
                    ['text' => 'The column value must be a positive number',            'correct' => false],
                    ['text' => 'The column cannot be modified after the initial insert', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the UNIQUE constraint do in PostgreSQL?',
                'explanation' => 'UNIQUE ensures all values in a column (or group of columns) are distinct. PostgreSQL automatically creates a unique index to enforce this. Unlike PRIMARY KEY, a UNIQUE column can hold NULL values (and NULLs are considered distinct from each other).',
                'options'     => [
                    ['text' => 'Ensures all values in the column are distinct; NULLs are allowed and treated as distinct', 'correct' => true],
                    ['text' => 'Ensures the column value is never NULL and always set',                                    'correct' => false],
                    ['text' => 'Creates a hash index on the column automatically',                                         'correct' => false],
                    ['text' => 'Restricts the column to a predefined set of allowed values',                              'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the DEFAULT keyword do when defining a column in PostgreSQL?',
                'explanation' => 'DEFAULT specifies a value to use when no value is provided during INSERT. PostgreSQL allows default expressions including functions like NOW(), gen_random_uuid(), or any constant value.',
                'options'     => [
                    ['text' => 'Sets a fallback value (including expressions or functions) used when no value is provided on INSERT', 'correct' => true],
                    ['text' => 'Sets the column as the primary key of the table',                                                    'correct' => false],
                    ['text' => 'Marks the column as optional and nullable by default',                                               'correct' => false],
                    ['text' => 'Resets the column to zero on every UPDATE',                                                         'correct' => false],
                ],
            ],
            [
                'question'    => 'Which SQL command creates a new table in PostgreSQL?',
                'explanation' => 'CREATE TABLE defines a new table with its columns, data types, and constraints. Example: CREATE TABLE users (id SERIAL PRIMARY KEY, name TEXT NOT NULL, email TEXT UNIQUE).',
                'options'     => [
                    ['text' => 'CREATE TABLE',  'correct' => true],
                    ['text' => 'NEW TABLE',     'correct' => false],
                    ['text' => 'BUILD TABLE',   'correct' => false],
                    ['text' => 'DEFINE TABLE',  'correct' => false],
                ],
            ],
            [
                'question'    => 'Which SQL command permanently removes a table and all its data in PostgreSQL?',
                'explanation' => 'DROP TABLE removes the table structure and all its data permanently. Use DROP TABLE IF EXISTS to avoid an error when the table does not exist. Add CASCADE to also drop dependent objects (views, foreign keys referencing it).',
                'options'     => [
                    ['text' => 'DROP TABLE',    'correct' => true],
                    ['text' => 'DELETE TABLE',  'correct' => false],
                    ['text' => 'REMOVE TABLE',  'correct' => false],
                    ['text' => 'ERASE TABLE',   'correct' => false],
                ],
            ],
            [
                'question'    => 'Which SQL command modifies an existing table structure in PostgreSQL?',
                'explanation' => 'ALTER TABLE changes a table\'s structure. Common uses: ADD COLUMN, DROP COLUMN, ALTER COLUMN TYPE, RENAME COLUMN, ADD CONSTRAINT. Example: ALTER TABLE users ADD COLUMN phone TEXT.',
                'options'     => [
                    ['text' => 'ALTER TABLE',   'correct' => true],
                    ['text' => 'MODIFY TABLE',  'correct' => false],
                    ['text' => 'CHANGE TABLE',  'correct' => false],
                    ['text' => 'UPDATE TABLE',  'correct' => false],
                ],
            ],
            [
                'question'    => 'What does DDL stand for in SQL?',
                'explanation' => 'DDL stands for Data Definition Language. DDL statements define or modify database structure: CREATE, ALTER, DROP, TRUNCATE. In PostgreSQL, DDL is transactional — you can roll back a CREATE TABLE inside a transaction.',
                'options'     => [
                    ['text' => 'Data Definition Language — statements that define database structure (CREATE, ALTER, DROP)', 'correct' => true],
                    ['text' => 'Data Deletion Language — statements that delete records from tables',                       'correct' => false],
                    ['text' => 'Data Distribution Layer — commands that replicate data across servers',                    'correct' => false],
                    ['text' => 'Data Dependency Logic — rules that enforce referential integrity',                         'correct' => false],
                ],
            ],
            [
                'question'    => 'What does DML stand for in SQL?',
                'explanation' => 'DML stands for Data Manipulation Language. DML statements read and modify table data: SELECT, INSERT, UPDATE, DELETE. DML operations in PostgreSQL are transactional and can be rolled back.',
                'options'     => [
                    ['text' => 'Data Manipulation Language — statements that read and modify table data (SELECT, INSERT, UPDATE, DELETE)', 'correct' => true],
                    ['text' => 'Data Migration Layer — commands that move data between databases',                                        'correct' => false],
                    ['text' => 'Data Management Language — commands that manage users and roles',                                         'correct' => false],
                    ['text' => 'Data Modeling Language — commands that define relationships between tables',                             'correct' => false],
                ],
            ],
            [
                'question'    => 'Which PostgreSQL aggregate function returns the total sum of a numeric column?',
                'explanation' => 'SUM() adds all non-NULL numeric values. Example: SELECT SUM(amount) FROM payments returns the total of all payment amounts. SUM() returns NULL if there are no non-NULL rows (use COALESCE to default to 0).',
                'options'     => [
                    ['text' => 'SUM()',    'correct' => true],
                    ['text' => 'TOTAL()', 'correct' => false],
                    ['text' => 'ADD()',   'correct' => false],
                    ['text' => 'PLUS()',  'correct' => false],
                ],
            ],
            [
                'question'    => 'Which PostgreSQL aggregate function returns the average of a numeric column?',
                'explanation' => 'AVG() computes the arithmetic mean of non-NULL values, returning a numeric result. Example: SELECT AVG(rating) FROM reviews. AVG() ignores NULLs automatically.',
                'options'     => [
                    ['text' => 'AVG()',     'correct' => true],
                    ['text' => 'MEAN()',    'correct' => false],
                    ['text' => 'AVERAGE()', 'correct' => false],
                    ['text' => 'MID()',    'correct' => false],
                ],
            ],
            [
                'question'    => 'Which PostgreSQL aggregate functions return the smallest and largest values?',
                'explanation' => 'MIN() returns the smallest and MAX() the largest non-NULL value in a column. They work on numeric, text, date, and other comparable types. NULLs are ignored in both.',
                'options'     => [
                    ['text' => 'MIN() and MAX()',     'correct' => true],
                    ['text' => 'SMALL() and LARGE()', 'correct' => false],
                    ['text' => 'FIRST() and LAST()',  'correct' => false],
                    ['text' => 'LOW() and HIGH()',    'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the LIKE operator do in a PostgreSQL WHERE clause?',
                'explanation' => 'LIKE performs case-sensitive pattern matching. Use % to match any sequence of characters and _ for a single character. PostgreSQL also provides ILIKE for case-insensitive matching, which is a PostgreSQL extension.',
                'options'     => [
                    ['text' => 'Performs case-sensitive pattern matching using % (any chars) and _ (one char); ILIKE is the case-insensitive version', 'correct' => true],
                    ['text' => 'Checks if a value is equal to another value, similar to =',                                                           'correct' => false],
                    ['text' => 'Checks if a value exists in a list, similar to IN',                                                                   'correct' => false],
                    ['text' => 'Compares two columns and returns rows where they are similar',                                                        'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the IN operator do in a PostgreSQL WHERE clause?',
                'explanation' => 'IN checks if a value matches any value in a list or subquery. Example: WHERE status IN (\'active\', \'pending\'). It is equivalent to chaining OR conditions. NOT IN excludes those values.',
                'options'     => [
                    ['text' => 'Checks if a value matches any value in a specified list or subquery', 'correct' => true],
                    ['text' => 'Checks if a value falls within a numeric range',                      'correct' => false],
                    ['text' => 'Checks if a column belongs to a specific table',                      'correct' => false],
                    ['text' => 'Joins two tables based on a shared column name',                     'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the BETWEEN operator do in PostgreSQL?',
                'explanation' => 'BETWEEN filters rows where a value falls within an inclusive range. Example: WHERE price BETWEEN 100 AND 500 is equivalent to WHERE price >= 100 AND price <= 500. It works on numbers, dates, and strings.',
                'options'     => [
                    ['text' => 'Filters rows where a value is within an inclusive range (both endpoints included)', 'correct' => true],
                    ['text' => 'Selects rows between two specific row numbers in the result set',                   'correct' => false],
                    ['text' => 'Joins tables and returns only rows that exist between two matching rows',           'correct' => false],
                    ['text' => 'Checks if a column value is between two columns in the same row',                  'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the AS keyword do in a PostgreSQL query?',
                'explanation' => 'AS creates an alias — a temporary name for a column or table within the query. Aliases improve readability and are required when computing expressions. Example: SELECT price * 0.9 AS discounted_price FROM products.',
                'options'     => [
                    ['text' => 'Creates a temporary alias (rename) for a column or table in the query result', 'correct' => true],
                    ['text' => 'Converts a column from one data type to another',                              'correct' => false],
                    ['text' => 'Creates a permanent rename for a column in the table',                         'correct' => false],
                    ['text' => 'Assigns a variable to store the query result',                                 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you concatenate strings in PostgreSQL?',
                'explanation' => 'PostgreSQL uses the || operator for string concatenation. Example: \'Hello\' || \' \' || \'World\' returns \'Hello World\'. The CONCAT() function also works and handles NULLs differently (ignores them). If either operand of || is NULL, the result is NULL.',
                'options'     => [
                    ['text' => 'Using the || operator; CONCAT() function also works and ignores NULLs', 'correct' => true],
                    ['text' => 'Using the + operator the same as in most other databases',               'correct' => false],
                    ['text' => 'Using the APPEND() function',                                            'correct' => false],
                    ['text' => 'Using the MERGE() operator',                                             'correct' => false],
                ],
            ],
            [
                'question'    => 'What does COUNT(*) return in PostgreSQL?',
                'explanation' => 'COUNT(*) returns the total number of rows in the result set, including rows with NULL values. COUNT(column) counts only non-NULL values in that column. COUNT(DISTINCT column) counts distinct non-NULL values.',
                'options'     => [
                    ['text' => 'The total number of rows, including those with NULL values', 'correct' => true],
                    ['text' => 'The number of distinct values in the first selected column', 'correct' => false],
                    ['text' => 'The sum of all numeric values in the result set',           'correct' => false],
                    ['text' => 'The number of columns returned by the query',               'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of OFFSET in a PostgreSQL query?',
                'explanation' => 'OFFSET skips a specified number of rows before returning results. Combined with LIMIT, it implements pagination. Example: SELECT * FROM articles LIMIT 10 OFFSET 30 skips the first 30 rows and returns rows 31-40.',
                'options'     => [
                    ['text' => 'Skips a specified number of rows before returning results, used with LIMIT for pagination', 'correct' => true],
                    ['text' => 'Sets the starting value for SERIAL sequences',                                              'correct' => false],
                    ['text' => 'Shifts column values by a fixed numeric amount',                                            'correct' => false],
                    ['text' => 'Specifies the byte offset to read from a BYTEA column',                                    'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the BOOLEAN data type in PostgreSQL?',
                'explanation' => 'PostgreSQL has a native BOOLEAN type that stores true/false values. Accepted literals include TRUE/FALSE, \'t\'/\'f\', \'yes\'/\'no\', \'on\'/\'off\', and \'1\'/\'0\'. Boolean columns can also hold NULL for unknown.',
                'options'     => [
                    ['text' => 'A native type storing true/false values, accepting TRUE/FALSE, \'t\'/\'f\', \'yes\'/\'no\', and similar literals', 'correct' => true],
                    ['text' => 'A numeric type where 0 means false and any non-zero value means true',                                            'correct' => false],
                    ['text' => 'A string type limited to the values \'true\' and \'false\'',                                                      'correct' => false],
                    ['text' => 'A bit type that stores 0 or 1 in a single bit of storage',                                                       'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the UUID data type in PostgreSQL?',
                'explanation' => 'UUID (Universally Unique Identifier) is a 128-bit value stored as 16 bytes. PostgreSQL has a native UUID type and can generate UUIDs with gen_random_uuid() (built-in since PostgreSQL 13) or the uuid-ossp extension. UUIDs are commonly used as globally unique primary keys.',
                'options'     => [
                    ['text' => 'A 128-bit unique identifier stored as 16 bytes, generated with gen_random_uuid() or extensions', 'correct' => true],
                    ['text' => 'A user-defined type that stores up to 36 characters of text',                                   'correct' => false],
                    ['text' => 'An auto-incrementing integer managed by a sequence, similar to SERIAL',                         'correct' => false],
                    ['text' => 'A binary type used for storing file checksums and hash digests',                                'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a schema in PostgreSQL?',
                'explanation' => 'A schema is a named namespace within a database that contains tables, views, functions, and other objects. PostgreSQL databases can have multiple schemas. The default schema is "public". Schemas help organize objects and manage permissions — different users can have different search_path settings.',
                'options'     => [
                    ['text' => 'A named namespace within a database containing tables and other objects; "public" is the default schema', 'correct' => true],
                    ['text' => 'In PostgreSQL, a schema and a database are identical — the terms are interchangeable',                   'correct' => false],
                    ['text' => 'A schema is a read-only snapshot of database structure without any data',                               'correct' => false],
                    ['text' => 'A schema is the binary format PostgreSQL uses to store table data on disk',                             'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between NUMERIC and FLOAT in PostgreSQL?',
                'explanation' => 'NUMERIC (or DECIMAL) stores exact values with arbitrary precision — required for financial data. FLOAT (double precision) stores approximate floating-point values with potential rounding errors. Example: NUMERIC(10, 2) stores up to 10 digits with exactly 2 decimal places.',
                'options'     => [
                    ['text' => 'NUMERIC stores exact values (required for money); FLOAT stores approximate values with potential rounding errors', 'correct' => true],
                    ['text' => 'FLOAT is more precise than NUMERIC because it uses more bits internally',                                        'correct' => false],
                    ['text' => 'NUMERIC and FLOAT are identical in PostgreSQL; the difference is only in syntax',                                'correct' => false],
                    ['text' => 'NUMERIC stores integers only; FLOAT stores decimal values with a fractional part',                              'correct' => false],
                ],
            ],
        ];
    }

    private function intermediateQuestions(): array
    {
        return [
            [
                'question'    => 'What is the difference between INNER JOIN and LEFT JOIN in PostgreSQL?',
                'explanation' => 'INNER JOIN returns only rows with matching values in both tables. LEFT JOIN returns all rows from the left table; unmatched right-side columns appear as NULL. PostgreSQL also supports FULL OUTER JOIN which returns all rows from both tables with NULLs for missing matches.',
                'options'     => [
                    ['text' => 'INNER JOIN returns only matching rows; LEFT JOIN returns all left rows with NULLs for unmatched right rows', 'correct' => true],
                    ['text' => 'LEFT JOIN is always faster than INNER JOIN',                                                                'correct' => false],
                    ['text' => 'INNER JOIN includes NULL rows; LEFT JOIN excludes them',                                                    'correct' => false],
                    ['text' => 'They are identical; LEFT JOIN is just an alias for INNER JOIN',                                            'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an INDEX in PostgreSQL and what is its primary purpose?',
                'explanation' => 'An index is a data structure that speeds up data retrieval by allowing PostgreSQL to find rows without scanning the entire table. The default index type is B-tree. Indexes speed up reads but add overhead to writes.',
                'options'     => [
                    ['text' => 'A data structure that speeds up row lookups by avoiding full table scans', 'correct' => true],
                    ['text' => 'A backup copy of a table stored separately',                               'correct' => false],
                    ['text' => 'A constraint that prevents duplicate values in a column',                  'correct' => false],
                    ['text' => 'A numbered list of columns in a table',                                    'correct' => false],
                ],
            ],
            [
                'question'    => 'What does GROUP BY do in a PostgreSQL query?',
                'explanation' => 'GROUP BY groups rows sharing the same values in specified columns, allowing aggregate functions (COUNT, SUM, AVG, MAX, MIN) to be applied per group. In PostgreSQL, non-aggregate SELECT columns must appear in the GROUP BY clause (or be functionally dependent on it).',
                'options'     => [
                    ['text' => 'Groups rows with matching column values so aggregate functions can be applied to each group', 'correct' => true],
                    ['text' => 'Sorts rows in ascending order by the specified column',                                      'correct' => false],
                    ['text' => 'Filters rows based on a condition applied after grouping',                                   'correct' => false],
                    ['text' => 'Removes duplicate rows from the result set',                                                 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between WHERE and HAVING in PostgreSQL?',
                'explanation' => 'WHERE filters individual rows BEFORE grouping. HAVING filters groups AFTER GROUP BY and can reference aggregate functions. Example: WHERE age > 18 vs HAVING COUNT(*) > 5.',
                'options'     => [
                    ['text' => 'WHERE filters rows before grouping; HAVING filters groups after GROUP BY and can use aggregate functions', 'correct' => true],
                    ['text' => 'HAVING filters rows before grouping; WHERE filters after grouping',                                       'correct' => false],
                    ['text' => 'They are interchangeable and produce the same result',                                                    'correct' => false],
                    ['text' => 'WHERE works on indexed columns only; HAVING works on all columns',                                        'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a subquery in PostgreSQL?',
                'explanation' => 'A subquery is a SELECT nested inside another query. It can appear in WHERE (correlated or non-correlated), FROM (derived table), or SELECT clauses. PostgreSQL also supports EXISTS subqueries and lateral joins for referencing the outer query in FROM.',
                'options'     => [
                    ['text' => 'A SELECT statement nested inside another SQL statement to provide values or a result set', 'correct' => true],
                    ['text' => 'A stored procedure that runs automatically after a main query',                           'correct' => false],
                    ['text' => 'A query that runs on a subset of table columns only',                                    'correct' => false],
                    ['text' => 'A query automatically optimized by the planner on secondary indexes',                    'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you start a transaction in PostgreSQL?',
                'explanation' => 'In PostgreSQL, use BEGIN (or BEGIN TRANSACTION / START TRANSACTION) to start a transaction. Use COMMIT to save changes and ROLLBACK to undo them. PostgreSQL operates in auto-commit mode by default — each statement is its own transaction unless you use BEGIN.',
                'options'     => [
                    ['text' => 'BEGIN (or START TRANSACTION); end with COMMIT to save or ROLLBACK to undo', 'correct' => true],
                    ['text' => 'START TRANSACTION is required; BEGIN is not valid PostgreSQL syntax',        'correct' => false],
                    ['text' => 'Transactions are automatic in PostgreSQL; no BEGIN is needed',               'correct' => false],
                    ['text' => 'OPEN TRANSACTION; end with SAVE or DISCARD',                                'correct' => false],
                ],
            ],
            [
                'question'    => 'Which ACID property ensures that committed data survives a server crash in PostgreSQL?',
                'explanation' => 'Durability (D in ACID) guarantees that once a transaction is committed, it persists even through crashes. PostgreSQL ensures durability via WAL (Write-Ahead Logging) — all changes are written to the WAL before being applied to data files.',
                'options'     => [
                    ['text' => 'Durability — committed data persists through crashes, ensured by WAL (Write-Ahead Logging)',  'correct' => true],
                    ['text' => 'Atomicity — the transaction completes all operations or none',                                'correct' => false],
                    ['text' => 'Consistency — the database remains in a valid state after each transaction',                  'correct' => false],
                    ['text' => 'Isolation — concurrent transactions do not interfere with each other',                        'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between SERIAL and GENERATED ALWAYS AS IDENTITY in PostgreSQL?',
                'explanation' => 'SERIAL is a legacy shorthand that creates a sequence and a default. GENERATED ALWAYS AS IDENTITY (SQL standard, PostgreSQL 10+) is more strict — it prevents manual inserts into the column unless you use OVERRIDING SYSTEM VALUE, making it safer. GENERATED BY DEFAULT AS IDENTITY allows manual values.',
                'options'     => [
                    ['text' => 'SERIAL is a legacy shorthand; GENERATED ALWAYS AS IDENTITY is the SQL standard and prevents manual value insertion', 'correct' => true],
                    ['text' => 'GENERATED ALWAYS AS IDENTITY is deprecated; SERIAL is the modern approach',                                          'correct' => false],
                    ['text' => 'They are identical; GENERATED AS IDENTITY is just an alias for SERIAL',                                              'correct' => false],
                    ['text' => 'SERIAL generates UUIDs; GENERATED AS IDENTITY generates sequential integers',                                        'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a VIEW in PostgreSQL?',
                'explanation' => 'A VIEW is a named virtual table defined by a stored SELECT query. It does not store data. PostgreSQL also supports MATERIALIZED VIEWs which cache the result set on disk and must be refreshed with REFRESH MATERIALIZED VIEW.',
                'options'     => [
                    ['text' => 'A virtual table defined by a stored SELECT query that does not store data (see also: MATERIALIZED VIEW)', 'correct' => true],
                    ['text' => 'A physical copy of a table stored in a separate schema',                                                  'correct' => false],
                    ['text' => 'A UI component for rendering query results in a web application',                                         'correct' => false],
                    ['text' => 'A temporary table created automatically during a JOIN operation',                                         'correct' => false],
                ],
            ],
            [
                'question'    => 'What is database normalization?',
                'explanation' => 'Normalization organizes a database to reduce redundancy and improve data integrity. It involves decomposing tables according to normal forms (1NF, 2NF, 3NF, BCNF). It prevents update anomalies and ensures each fact is stored once.',
                'options'     => [
                    ['text' => 'Organizing tables to eliminate redundancy and ensure data integrity through normal forms', 'correct' => true],
                    ['text' => 'Converting all column names to a consistent lowercase format',                            'correct' => false],
                    ['text' => 'Compressing table data to minimize storage size',                                        'correct' => false],
                    ['text' => 'Reordering table rows to improve query performance',                                     'correct' => false],
                ],
            ],
            [
                'question'    => 'What does a RIGHT JOIN return in PostgreSQL?',
                'explanation' => 'RIGHT JOIN returns all rows from the right table and matching rows from the left table. Unmatched left-side columns show NULL. It is the mirror of LEFT JOIN. PostgreSQL also supports FULL OUTER JOIN to return all rows from both sides.',
                'options'     => [
                    ['text' => 'All rows from the right table plus matching left rows, with NULLs for unmatched left rows', 'correct' => true],
                    ['text' => 'Only rows where both tables have matching values (same as INNER JOIN)',                    'correct' => false],
                    ['text' => 'All rows from both tables regardless of matching',                                         'correct' => false],
                    ['text' => 'All rows from the left table plus matching right rows',                                    'correct' => false],
                ],
            ],
            [
                'question'    => 'What does UNION do and how does it differ from UNION ALL in PostgreSQL?',
                'explanation' => 'UNION combines result sets from two SELECT statements and removes duplicates. UNION ALL combines without deduplication and is faster. Both require compatible column types. PostgreSQL also supports INTERSECT and EXCEPT for set operations.',
                'options'     => [
                    ['text' => 'UNION combines results and removes duplicates; UNION ALL combines without deduplication (faster)', 'correct' => true],
                    ['text' => 'UNION ALL returns only rows that appear in both result sets',                                      'correct' => false],
                    ['text' => 'They are identical; UNION ALL is just a syntax variation',                                         'correct' => false],
                    ['text' => 'UNION joins tables by column; UNION ALL joins by row',                                            'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a Common Table Expression (CTE) in PostgreSQL?',
                'explanation' => 'A CTE (defined with WITH) is a named temporary result set scoped to the current query. PostgreSQL CTEs are materialized by default (evaluated once) which can improve or worsen performance. They support recursive queries for hierarchical data.',
                'options'     => [
                    ['text' => 'A temporary named result set defined with WITH, supporting recursive queries and materialized by default in PostgreSQL', 'correct' => true],
                    ['text' => 'A permanently stored expression that calculates a value when called',                                                    'correct' => false],
                    ['text' => 'A column expression stored in a virtual generated column',                                                              'correct' => false],
                    ['text' => 'A connection template that reuses query parameters across multiple queries',                                            'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the CASE expression in PostgreSQL?',
                'explanation' => 'CASE is a conditional expression (if-else logic) that evaluates conditions and returns the value for the first matching WHEN clause. Example: CASE WHEN score >= 90 THEN \'A\' WHEN score >= 80 THEN \'B\' ELSE \'C\' END.',
                'options'     => [
                    ['text' => 'A conditional expression returning the first matching WHEN value (if-else logic)', 'correct' => true],
                    ['text' => 'A keyword for switching between different PostgreSQL execution modes',             'correct' => false],
                    ['text' => 'A stored procedure that handles different error types in transactions',           'correct' => false],
                    ['text' => 'A PostgreSQL extension for pattern matching on character data',                  'correct' => false],
                ],
            ],
            [
                'question'    => 'What does COALESCE() do in PostgreSQL?',
                'explanation' => 'COALESCE() returns the first non-NULL argument from its list. Example: COALESCE(nickname, first_name, \'Anonymous\') returns the first non-NULL value. It is standard SQL and very common for NULL fallback handling.',
                'options'     => [
                    ['text' => 'Returns the first non-NULL value from its argument list', 'correct' => true],
                    ['text' => 'Merges multiple columns into a single concatenated string', 'correct' => false],
                    ['text' => 'Converts NULL to zero for numeric operations',              'correct' => false],
                    ['text' => 'Checks if two values are equal and returns a boolean',     'correct' => false],
                ],
            ],
            [
                'question'    => 'What is ON DELETE CASCADE in PostgreSQL?',
                'explanation' => 'ON DELETE CASCADE is a foreign key referential action: when a parent row is deleted, all child rows referencing it are automatically deleted. PostgreSQL also supports ON DELETE SET NULL, SET DEFAULT, and RESTRICT.',
                'options'     => [
                    ['text' => 'Automatically deletes child rows when the parent row is deleted', 'correct' => true],
                    ['text' => 'Prevents deletion of a parent row if child rows exist',           'correct' => false],
                    ['text' => 'Sets the foreign key column to NULL when the parent is deleted',  'correct' => false],
                    ['text' => 'Copies deleted rows into an archive table before deletion',       'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a SAVEPOINT in PostgreSQL?',
                'explanation' => 'SAVEPOINT marks a named point within a transaction. You can ROLLBACK TO SAVEPOINT name to undo only work done after that savepoint, without ending the full transaction. Use RELEASE SAVEPOINT to remove it.',
                'options'     => [
                    ['text' => 'A named marker within a transaction allowing partial rollback to that specific point', 'correct' => true],
                    ['text' => 'A permanent checkpoint that saves the database state to disk',                        'correct' => false],
                    ['text' => 'An automatic backup created when a transaction starts',                              'correct' => false],
                    ['text' => 'A named stored procedure checkpoint for debugging purposes',                         'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a prepared statement in PostgreSQL and why is it important?',
                'explanation' => 'A prepared statement is a precompiled SQL template with placeholders ($1, $2, ...). It prevents SQL injection and can improve performance for repeated queries (parse/plan once, execute many). In psql: PREPARE stmt AS SELECT * FROM users WHERE id = $1; EXECUTE stmt(42);',
                'options'     => [
                    ['text' => 'A precompiled SQL template with $1/$2 placeholders preventing SQL injection and allowing plan reuse', 'correct' => true],
                    ['text' => 'A stored procedure that prepares data before inserting into a table',                                'correct' => false],
                    ['text' => 'A query cached in memory for instant repeated execution',                                            'correct' => false],
                    ['text' => 'A trigger that runs before INSERT statements to validate input',                                     'correct' => false],
                ],
            ],
            [
                'question'    => 'How does PostgreSQL implement ENUM types?',
                'explanation' => 'PostgreSQL implements ENUMs as user-defined types: CREATE TYPE status AS ENUM (\'active\', \'inactive\', \'pending\'). Then use: status status. Unlike MySQL ENUMs, PostgreSQL ENUMs are full catalog objects and can be altered with ALTER TYPE ... ADD VALUE.',
                'options'     => [
                    ['text' => 'As user-defined types via CREATE TYPE ... AS ENUM; can be extended with ALTER TYPE ... ADD VALUE', 'correct' => true],
                    ['text' => 'Inline in column definitions like MySQL: status ENUM(\'active\', \'inactive\')',                   'correct' => false],
                    ['text' => 'As CHECK constraints on a text column to restrict allowed values',                                 'correct' => false],
                    ['text' => 'PostgreSQL does not support ENUM types; use a lookup table instead',                              'correct' => false],
                ],
            ],
            [
                'question'    => 'What are the transaction isolation levels in PostgreSQL?',
                'explanation' => 'PostgreSQL supports four isolation levels: READ UNCOMMITTED (treated as READ COMMITTED), READ COMMITTED (default), REPEATABLE READ, and SERIALIZABLE. SERIALIZABLE in PostgreSQL uses SSI (Serializable Snapshot Isolation) which detects more anomalies than standard locking-based approaches.',
                'options'     => [
                    ['text' => 'READ UNCOMMITTED (acts as READ COMMITTED), READ COMMITTED (default), REPEATABLE READ, SERIALIZABLE', 'correct' => true],
                    ['text' => 'NONE, BASIC, STANDARD, and STRICT',                                                                  'correct' => false],
                    ['text' => 'LOW, MEDIUM, HIGH, and FULL',                                                                        'correct' => false],
                    ['text' => 'OPTIMISTIC, PESSIMISTIC, SHARED, and EXCLUSIVE',                                                     'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a CROSS JOIN in PostgreSQL?',
                'explanation' => 'A CROSS JOIN returns the Cartesian product — every row from the first table paired with every row from the second. Result count = M × N rows. Use with caution as it grows exponentially. In PostgreSQL, listing two tables separated by a comma (FROM a, b) produces the same result.',
                'options'     => [
                    ['text' => 'Returns every combination of rows from both tables (Cartesian product), producing M × N rows', 'correct' => true],
                    ['text' => 'Returns rows matching a cross-reference condition between two tables',                         'correct' => false],
                    ['text' => 'Joins tables from two different PostgreSQL databases',                                         'correct' => false],
                    ['text' => 'Returns rows that exist in one table but not the other',                                      'correct' => false],
                ],
            ],
            [
                'question'    => 'Which PostgreSQL function returns the current date and time?',
                'explanation' => 'NOW() returns the current timestamp with time zone. CURRENT_TIMESTAMP is equivalent. CURRENT_DATE returns today\'s date. CURRENT_TIME returns the current time. These are stable within a transaction — they reflect the transaction start time, not the current wall clock.',
                'options'     => [
                    ['text' => 'NOW() or CURRENT_TIMESTAMP returns the transaction start timestamp; CURRENT_DATE returns today\'s date', 'correct' => true],
                    ['text' => 'GETDATE() returns the current date and time in PostgreSQL',                                             'correct' => false],
                    ['text' => 'SYSDATE() returns the current server time in PostgreSQL',                                               'correct' => false],
                    ['text' => 'TODAY() returns the current date without time in PostgreSQL',                                           'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a temporary table in PostgreSQL?',
                'explanation' => 'A temporary table (CREATE TEMP TABLE or CREATE TEMPORARY TABLE) exists only for the current session and is automatically dropped when the session ends. Each session gets its own private copy. Temporary tables are useful for storing intermediate results.',
                'options'     => [
                    ['text' => 'A table that exists only for the current session and is automatically dropped when the session ends', 'correct' => true],
                    ['text' => 'A table stored in RAM that is deleted after each query executes',                                    'correct' => false],
                    ['text' => 'A read-only snapshot of a table created during a transaction',                                       'correct' => false],
                    ['text' => 'A backup table automatically created before each TRUNCATE',                                          'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the JSONB data type in PostgreSQL?',
                'explanation' => 'JSONB stores JSON in a decomposed binary format that is faster to query (supports indexing with GIN) and validates JSON structure on insert. JSON stores the raw text and is faster to insert. JSONB is preferred for most use cases because it supports operators like ->, ->>, @>, and GIN indexes.',
                'options'     => [
                    ['text' => 'A binary JSON type that validates JSON, supports GIN indexes, and enables fast querying with ->/->>/@ > operators', 'correct' => true],
                    ['text' => 'A type that stores JSON as plain text with no validation or indexing support',                                       'correct' => false],
                    ['text' => 'A compressed JSON type that reduces storage by removing whitespace',                                                 'correct' => false],
                    ['text' => 'A streaming JSON type for storing very large JSON documents',                                                        'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the ARRAY data type in PostgreSQL?',
                'explanation' => 'PostgreSQL supports arrays of any data type natively. Example: tags TEXT[], scores INTEGER[]. Use array literals: ARRAY[\'php\', \'python\'] or \'{"php","python"}\'. Query with ANY(), @> (contains), and array indexing (arr[1]). Arrays are useful but normalization is usually preferred for relational data.',
                'options'     => [
                    ['text' => 'A native type for storing a list of values of any type; supports array operators and indexing', 'correct' => true],
                    ['text' => 'A special row type for storing an entire row as a single column value',                        'correct' => false],
                    ['text' => 'A type for storing binary arrays equivalent to BLOB in other databases',                       'correct' => false],
                    ['text' => 'A PostgreSQL extension type requiring installation of the array extension',                    'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a SEQUENCE in PostgreSQL and how does it relate to SERIAL?',
                'explanation' => 'A SEQUENCE is a database object that generates unique sequential integers. SERIAL is syntactic sugar that creates a sequence and attaches it as the column\'s DEFAULT via nextval(). You can use CREATE SEQUENCE directly for more control (start, increment, min/max, cache, cycle).',
                'options'     => [
                    ['text' => 'A SEQUENCE is a standalone object generating sequential integers; SERIAL is shorthand that creates a sequence and attaches it to a column', 'correct' => true],
                    ['text' => 'A SEQUENCE is the value produced; SERIAL is the type stored in the column',                                                                   'correct' => false],
                    ['text' => 'They are completely different: SEQUENCE is for date ordering; SERIAL is for numeric auto-increment',                                          'correct' => false],
                    ['text' => 'A SEQUENCE can wrap around; SERIAL never repeats values even after reaching its maximum',                                                    'correct' => false],
                ],
            ],
            [
                'question'    => 'What does FULL OUTER JOIN return in PostgreSQL?',
                'explanation' => 'FULL OUTER JOIN returns all rows from both tables. Where there is a match, columns from both sides are filled. Where there is no match, the missing side\'s columns are NULL. It combines the results of LEFT JOIN and RIGHT JOIN.',
                'options'     => [
                    ['text' => 'All rows from both tables; NULLs fill the missing side where there is no match', 'correct' => true],
                    ['text' => 'Only rows that exist in both tables (same as INNER JOIN)',                        'correct' => false],
                    ['text' => 'All rows from the outer (first) table with no join applied',                     'correct' => false],
                    ['text' => 'All rows where at least one column from either table is NULL',                   'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a SELF JOIN in PostgreSQL?',
                'explanation' => 'A SELF JOIN joins a table to itself using aliases. It is used to compare rows within the same table. Classic example: employees table where each row has a manager_id referencing another row in the same table.',
                'options'     => [
                    ['text' => 'A join where a table is joined to itself using aliases, used to compare rows within the same table', 'correct' => true],
                    ['text' => 'A join that reuses a cached result from a previous query',                                           'correct' => false],
                    ['text' => 'A join where PostgreSQL automatically selects the optimal join algorithm',                           'correct' => false],
                    ['text' => 'A join that connects a table to its parent via table inheritance',                                   'correct' => false],
                ],
            ],
            [
                'question'    => 'What is First Normal Form (1NF) in database design?',
                'explanation' => '1NF requires: (1) each column holds atomic (indivisible) values, (2) each column holds values of a single type, and (3) rows are uniquely identifiable. No repeating groups or arrays in a cell. PostgreSQL\'s ARRAY type technically violates 1NF — that trade-off should be a conscious decision.',
                'options'     => [
                    ['text' => 'Each column holds atomic values, each row is unique, and there are no repeating groups or arrays in a cell', 'correct' => true],
                    ['text' => 'All non-key columns depend on the entire composite primary key',                                            'correct' => false],
                    ['text' => 'All non-key columns depend only on the primary key, not on each other',                                    'correct' => false],
                    ['text' => 'The table has no NULL values in any column',                                                               'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the USING clause do in a PostgreSQL JOIN?',
                'explanation' => 'USING(column) is shorthand for ON t1.column = t2.column when both tables share a column with the same name. Example: JOIN orders USING(customer_id). It also removes the duplicate column from the result set, so the column appears only once.',
                'options'     => [
                    ['text' => 'Specifies a shared column name for joining, equivalent to ON t1.col = t2.col, and removes the duplicate column from results', 'correct' => true],
                    ['text' => 'Specifies which index PostgreSQL should use for the join operation',                                                          'correct' => false],
                    ['text' => 'Defines the join type (INNER, LEFT, RIGHT) using a shorthand syntax',                                                        'correct' => false],
                    ['text' => 'Limits the join to use a maximum number of rows from each table',                                                            'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the IFNULL() equivalent look like in PostgreSQL?',
                'explanation' => 'PostgreSQL does not have IFNULL(). Use COALESCE(expr, fallback) instead — it is equivalent and standard SQL. Example: COALESCE(phone, \'N/A\') returns phone if not NULL, else \'N/A\'. PostgreSQL also has NULLIF(a, b) which returns NULL if a equals b, otherwise returns a.',
                'options'     => [
                    ['text' => 'PostgreSQL uses COALESCE(expr, fallback) — there is no IFNULL(); NULLIF(a, b) returns NULL when a equals b', 'correct' => true],
                    ['text' => 'IFNULL() works in PostgreSQL exactly as in MySQL',                                                           'correct' => false],
                    ['text' => 'PostgreSQL uses NVL(expr, fallback) the same as Oracle',                                                    'correct' => false],
                    ['text' => 'PostgreSQL uses ISNULL(expr, fallback) the same as SQL Server',                                             'correct' => false],
                ],
            ],
        ];
    }

    private function advancedQuestions(): array
    {
        return [
            [
                'question'    => 'What does EXPLAIN ANALYZE do in PostgreSQL compared to plain EXPLAIN?',
                'explanation' => 'Plain EXPLAIN shows the planner\'s estimated execution plan without running the query. EXPLAIN ANALYZE actually executes the query and shows real timing and row counts alongside the planner estimates. Use EXPLAIN (ANALYZE, BUFFERS) to also see cache hit/miss statistics.',
                'options'     => [
                    ['text' => 'EXPLAIN ANALYZE executes the query and returns real runtime statistics; plain EXPLAIN only shows planner estimates without executing', 'correct' => true],
                    ['text' => 'EXPLAIN ANALYZE recommends indexes to add; plain EXPLAIN just shows the query plan',                                                  'correct' => false],
                    ['text' => 'Both run the query; ANALYZE adds buffer statistics to the output',                                                                   'correct' => false],
                    ['text' => 'EXPLAIN ANALYZE runs the query in dry-run mode without touching any data',                                                           'correct' => false],
                ],
            ],
            [
                'question'    => 'What are the index types available in PostgreSQL?',
                'explanation' => 'PostgreSQL supports: B-tree (default, equality and range), Hash (equality only), GIN (Generalized Inverted Index — full-text search, arrays, JSONB), GiST (Generalized Search Tree — geometric, full-text, custom), BRIN (Block Range Index — large sorted tables like time-series), and SP-GiST.',
                'options'     => [
                    ['text' => 'B-tree (default), Hash, GIN (arrays/JSONB/full-text), GiST (geometric/custom), BRIN (block range), SP-GiST', 'correct' => true],
                    ['text' => 'B-tree, Clustered, Non-clustered, and Full-text only',                                                        'correct' => false],
                    ['text' => 'Primary, Secondary, Composite, and Unique indexes only',                                                      'correct' => false],
                    ['text' => 'B-tree and Hash only; the others require external extensions',                                                 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is MVCC (Multi-Version Concurrency Control) in PostgreSQL?',
                'explanation' => 'PostgreSQL\'s MVCC keeps multiple row versions (tuples) so readers see a consistent snapshot of the database at their transaction start without blocking writers. Old versions are not deleted immediately — they become "dead tuples" cleaned up by VACUUM. This enables high read concurrency without locks.',
                'options'     => [
                    ['text' => 'Storing multiple row versions so readers see a consistent snapshot without blocking writers; dead tuples cleaned by VACUUM', 'correct' => true],
                    ['text' => 'A locking strategy where multiple readers share a single read lock',                                                         'correct' => false],
                    ['text' => 'A replication technique maintaining multiple consistent copies of data across servers',                                       'correct' => false],
                    ['text' => 'A compression method that stores multiple column values in a single data page version',                                       'correct' => false],
                ],
            ],
            [
                'question'    => 'What is VACUUM in PostgreSQL and why is it essential?',
                'explanation' => 'Because MVCC keeps dead row versions, VACUUM reclaims space by removing dead tuples, updating planner statistics, and advancing the transaction ID horizon to prevent XID wraparound. AUTOVACUUM runs automatically in the background. VACUUM FULL rewrites the table to reclaim disk space but locks the table.',
                'options'     => [
                    ['text' => 'Reclaims dead tuple space from MVCC, updates planner statistics, and prevents XID wraparound; AUTOVACUUM runs automatically', 'correct' => true],
                    ['text' => 'A command that physically reorders table rows to match the primary key for faster reads',                                      'correct' => false],
                    ['text' => 'A scheduled job that compresses table data to reduce disk usage',                                                             'correct' => false],
                    ['text' => 'A tool that removes unused indexes and reclaims their storage',                                                              'correct' => false],
                ],
            ],
            [
                'question'    => 'What is WAL (Write-Ahead Logging) in PostgreSQL?',
                'explanation' => 'WAL ensures durability: all changes are written to the WAL before being applied to data files. On crash, PostgreSQL replays the WAL to recover to the last consistent state. WAL is also the foundation for streaming replication — replicas replay the primary\'s WAL stream.',
                'options'     => [
                    ['text' => 'A log recording all changes before applying them to data files; used for crash recovery and is the basis for streaming replication', 'correct' => true],
                    ['text' => 'A log of all SELECT queries for auditing query performance',                                                                        'correct' => false],
                    ['text' => 'A log of failed transactions for post-crash analysis',                                                                              'correct' => false],
                    ['text' => 'A log of connection events and authentication attempts',                                                                            'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a covering index in PostgreSQL and how do you create one?',
                'explanation' => 'A covering index stores additional columns using the INCLUDE clause (PostgreSQL 11+), so a query can be satisfied entirely from the index without accessing the heap (table). Example: CREATE INDEX ON orders(customer_id) INCLUDE (total, status). EXPLAIN shows "Index Only Scan" when effective.',
                'options'     => [
                    ['text' => 'An index with INCLUDE columns that lets PostgreSQL answer a query from the index alone, shown as "Index Only Scan"', 'correct' => true],
                    ['text' => 'An index that covers all columns of a table for complete row retrieval',                                             'correct' => false],
                    ['text' => 'A composite index created automatically on foreign key columns',                                                     'correct' => false],
                    ['text' => 'An index that protects columns from concurrent updates',                                                            'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a partial index in PostgreSQL?',
                'explanation' => 'A partial index indexes only the rows satisfying a WHERE condition. Example: CREATE INDEX ON orders(customer_id) WHERE status = \'pending\' indexes only pending orders. This creates a smaller, more efficient index when queries frequently filter on a subset of rows.',
                'options'     => [
                    ['text' => 'An index on a subset of rows defined by a WHERE clause, reducing index size and improving performance for selective queries', 'correct' => true],
                    ['text' => 'An index on a subset of columns in a table',                                                                              'correct' => false],
                    ['text' => 'An index that is only partially built — completed lazily in the background',                                              'correct' => false],
                    ['text' => 'An index prefix that covers only the first N characters of a text column',                                               'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a materialized view in PostgreSQL?',
                'explanation' => 'A materialized view (CREATE MATERIALIZED VIEW) physically stores the result of a SELECT query on disk. It is much faster to query than a regular view because the data is precomputed. Refresh it with REFRESH MATERIALIZED VIEW (optionally CONCURRENTLY to avoid locking).',
                'options'     => [
                    ['text' => 'A view that physically stores its SELECT result on disk; refreshed manually with REFRESH MATERIALIZED VIEW', 'correct' => true],
                    ['text' => 'A view that automatically refreshes whenever the underlying table changes',                                   'correct' => false],
                    ['text' => 'A cached query result stored in shared_buffers for fast re-execution',                                       'correct' => false],
                    ['text' => 'A read-only replica of a table for reporting use cases',                                                    'correct' => false],
                ],
            ],
            [
                'question'    => 'What is table inheritance in PostgreSQL?',
                'explanation' => 'PostgreSQL supports table inheritance: a child table inherits all columns of a parent table. Example: CREATE TABLE car () INHERITS (vehicle). Queries on the parent automatically include child rows unless you use ONLY. Useful for partitioning or type hierarchies but mostly superseded by declarative partitioning.',
                'options'     => [
                    ['text' => 'A child table that inherits parent columns; parent queries include child rows; use ONLY to query one table', 'correct' => true],
                    ['text' => 'A PostgreSQL feature where foreign keys automatically inherit constraints from parent tables',                'correct' => false],
                    ['text' => 'A view hierarchy where child views extend parent view queries',                                             'correct' => false],
                    ['text' => 'A schema design where all tables in a schema inherit a common audit column',                               'correct' => false],
                ],
            ],
            [
                'question'    => 'What is declarative table partitioning in PostgreSQL?',
                'explanation' => 'Declarative partitioning (PostgreSQL 10+) divides a table into child partitions based on a partition key. Types: RANGE (e.g., date ranges), LIST (specific values), HASH (hash of key). Queries use partition pruning to skip irrelevant partitions automatically.',
                'options'     => [
                    ['text' => 'Dividing a table into child partitions by key (RANGE, LIST, HASH); queries skip irrelevant partitions via pruning', 'correct' => true],
                    ['text' => 'Splitting a table across multiple PostgreSQL servers for horizontal scaling',                                       'correct' => false],
                    ['text' => 'Creating redundant copies of a table on separate disks for high availability',                                      'correct' => false],
                    ['text' => 'Normalizing a table into multiple smaller related tables to remove redundancy',                                     'correct' => false],
                ],
            ],
            [
                'question'    => 'What are window functions in PostgreSQL?',
                'explanation' => 'Window functions compute values across a set of rows related to the current row, defined by OVER(). They do not collapse rows like GROUP BY. Examples: ROW_NUMBER(), RANK(), DENSE_RANK(), LEAD(), LAG(), SUM() OVER(), NTILE(). PostgreSQL has extensive window function support.',
                'options'     => [
                    ['text' => 'Functions that compute values across related rows using OVER() without collapsing them (e.g., ROW_NUMBER, RANK, LAG, LEAD)', 'correct' => true],
                    ['text' => 'Functions that stream results row by row through a network connection window',                                              'correct' => false],
                    ['text' => 'Aggregate functions that work only on indexed columns',                                                                    'correct' => false],
                    ['text' => 'Built-in functions for string manipulation on TEXT columns',                                                              'correct' => false],
                ],
            ],
            [
                'question'    => 'What is streaming replication in PostgreSQL?',
                'explanation' => 'Streaming replication sends WAL records from the primary to one or more standby servers in near real-time. Standbys replay the WAL to stay synchronized. Standbys can serve read queries (hot standby). It provides high availability and read scaling but standbys are read-only.',
                'options'     => [
                    ['text' => 'Sending WAL records from primary to standby servers in real-time; standbys are read-only hot standbys for HA and read scaling', 'correct' => true],
                    ['text' => 'A backup strategy that streams table data to a remote server periodically',                                                      'correct' => false],
                    ['text' => 'A technique to stream query results to clients without loading all rows into memory',                                            'correct' => false],
                    ['text' => 'A multi-primary replication mode where all nodes accept writes simultaneously',                                                  'correct' => false],
                ],
            ],
            [
                'question'    => 'What is logical replication in PostgreSQL?',
                'explanation' => 'Logical replication (PostgreSQL 10+) replicates changes at the row level using a publication/subscription model. Unlike streaming replication (which copies the full WAL), logical replication can replicate specific tables, allows the subscriber to be a different PostgreSQL version, and supports cross-database replication.',
                'options'     => [
                    ['text' => 'Row-level replication using publish/subscribe; replicates specific tables and allows version-mismatch between primary and subscriber', 'correct' => true],
                    ['text' => 'A backup method that creates a logical dump (pg_dump) and ships it to a replica server',                                             'correct' => false],
                    ['text' => 'Physical replication using WAL streaming to identical server configurations only',                                                    'correct' => false],
                    ['text' => 'A mode where SQL statements (not rows) are replicated for cross-database compatibility',                                             'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you analyze slow queries in PostgreSQL?',
                'explanation' => 'Enable pg_stat_statements extension to track cumulative statistics for all executed queries (total time, calls, rows). Use log_min_duration_statement to log slow queries. Use EXPLAIN (ANALYZE, BUFFERS) to inspect individual query plans. pgBadger can analyze log files.',
                'options'     => [
                    ['text' => 'Use pg_stat_statements for aggregate stats, log_min_duration_statement for slow query logging, and EXPLAIN (ANALYZE, BUFFERS) per query', 'correct' => true],
                    ['text' => 'PostgreSQL has a built-in slow query log at /var/log/postgres/slow.log enabled by default',                                               'correct' => false],
                    ['text' => 'Use SHOW SLOW QUERIES to list queries that exceeded the execution threshold',                                                             'correct' => false],
                    ['text' => 'Use ANALYZE table_name which outputs slow queries detected on that table',                                                               'correct' => false],
                ],
            ],
            [
                'question'    => 'What is full-text search in PostgreSQL?',
                'explanation' => 'PostgreSQL provides native full-text search using tsvector (indexed document) and tsquery (search query). Convert text with to_tsvector(\'english\', text_col) and search with @@ operator: WHERE document @@ to_tsquery(\'english\', \'postgres & query\'). Create a GIN index on the tsvector column for performance.',
                'options'     => [
                    ['text' => 'Native FTS using tsvector/tsquery with @@ operator and GIN indexes for efficient word-level search', 'correct' => true],
                    ['text' => 'Full-text search is not built in; it requires installing the pg_fulltext external extension',        'correct' => false],
                    ['text' => 'Full-text search uses LIKE \'%word%\' queries optimized by a special text index',                   'correct' => false],
                    ['text' => 'Full-text search uses a SEARCH column constraint added with ALTER TABLE',                           'correct' => false],
                ],
            ],
            [
                'question'    => 'What is SELECT FOR UPDATE in PostgreSQL?',
                'explanation' => 'SELECT FOR UPDATE locks the selected rows exclusively. Other transactions cannot update or lock these rows until the current transaction commits or rolls back. Use for read-then-write patterns. PostgreSQL also supports SELECT FOR SHARE (shared lock — blocks updates but allows other shared locks) and SKIP LOCKED / NOWAIT options.',
                'options'     => [
                    ['text' => 'Acquires exclusive row locks, blocking other transactions from modifying those rows; supports SKIP LOCKED and NOWAIT options', 'correct' => true],
                    ['text' => 'Marks rows as read-only so the current transaction cannot update them',                                                        'correct' => false],
                    ['text' => 'Performs SELECT and UPDATE in a single atomic operation without a transaction',                                                 'correct' => false],
                    ['text' => 'Selects only rows that are eligible for update based on permissions',                                                          'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you index a JSONB column in PostgreSQL for efficient querying?',
                'explanation' => 'Use a GIN index on a JSONB column to efficiently search for keys and values. Example: CREATE INDEX ON docs USING GIN(data). This enables operators like @> (contains), ? (key exists), and ?| (any key). For specific key paths, a B-tree index on a generated column or expression index is more efficient.',
                'options'     => [
                    ['text' => 'A GIN index enables @> (contains) and ? (key exists) queries; a B-tree expression index works for specific key paths', 'correct' => true],
                    ['text' => 'JSONB is automatically indexed by PostgreSQL; no manual index creation is needed',                                       'correct' => false],
                    ['text' => 'Use a standard B-tree index on the JSONB column; it works the same as on text columns',                                 'correct' => false],
                    ['text' => 'JSONB columns cannot be indexed; queries always require a full sequential scan',                                        'correct' => false],
                ],
            ],
            [
                'question'    => 'What is connection pooling in PostgreSQL and what tool is commonly used?',
                'explanation' => 'PostgreSQL creates a new OS process per connection (~5MB RAM each). Connection pooling reuses existing connections across requests. PgBouncer is the most popular PostgreSQL connection pooler. It supports session pooling, transaction pooling (best for scalability), and statement pooling.',
                'options'     => [
                    ['text' => 'Reusing database connections to reduce per-connection overhead; PgBouncer is the most popular pooler with session/transaction modes', 'correct' => true],
                    ['text' => 'PostgreSQL has built-in connection pooling enabled with max_connections; no external tool is needed',                                  'correct' => false],
                    ['text' => 'Distributing queries across multiple PostgreSQL replicas for load balancing',                                                         'correct' => false],
                    ['text' => 'Grouping multiple INSERT statements into batches to reduce round trips',                                                             'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an expression index in PostgreSQL?',
                'explanation' => 'An expression index indexes the result of a function or expression rather than a raw column. Example: CREATE INDEX ON users(LOWER(email)) makes queries like WHERE LOWER(email) = \'alice@example.com\' use the index. The expression in the WHERE clause must match the index expression exactly.',
                'options'     => [
                    ['text' => 'An index on a computed expression (e.g., LOWER(email)) so queries using that same expression can use the index', 'correct' => true],
                    ['text' => 'An index created using a SQL expression instead of the CREATE INDEX syntax',                                     'correct' => false],
                    ['text' => 'An index that automatically evaluates expressions in WHERE clauses for optimization',                            'correct' => false],
                    ['text' => 'A virtual index built in memory for complex GROUP BY expressions',                                              'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a deadlock in PostgreSQL and how does it handle them?',
                'explanation' => 'A deadlock occurs when two or more transactions permanently wait for each other\'s locks. PostgreSQL detects deadlocks automatically using a deadlock detection algorithm (running every deadlock_timeout, default 1 second) and kills one transaction with an error, allowing the others to proceed.',
                'options'     => [
                    ['text' => 'Transactions permanently waiting on each other\'s locks; PostgreSQL auto-detects and kills one transaction after deadlock_timeout', 'correct' => true],
                    ['text' => 'A query that never completes due to missing indexes; PostgreSQL cancels it after statement_timeout',                               'correct' => false],
                    ['text' => 'A full table scan that locks the entire table; PostgreSQL avoids it with row-level locking',                                       'correct' => false],
                    ['text' => 'A connection pool exhaustion state; handled by rejecting new connections',                                                         'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between 2NF and 3NF in database design?',
                'explanation' => '2NF: All non-key columns depend on the WHOLE primary key — no partial dependencies (relevant when the primary key is composite). 3NF: All non-key columns depend ONLY on the primary key — no transitive dependencies (non-key column → another non-key column → primary key).',
                'options'     => [
                    ['text' => '2NF eliminates partial dependencies on composite keys; 3NF eliminates transitive dependencies between non-key columns', 'correct' => true],
                    ['text' => '2NF requires all values to be atomic; 3NF requires a single-column primary key',                                       'correct' => false],
                    ['text' => '2NF applies only to tables with foreign keys; 3NF applies to tables with composite keys',                             'correct' => false],
                    ['text' => 'They are the same; 3NF is just the stricter enforcement of 2NF rules',                                               'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a Foreign Data Wrapper (FDW) in PostgreSQL?',
                'explanation' => 'FDWs allow PostgreSQL to query external data sources as if they were local tables. Examples: postgres_fdw (another PostgreSQL server), file_fdw (CSV files), mysql_fdw (MySQL). Install with CREATE EXTENSION, then CREATE SERVER, CREATE USER MAPPING, and CREATE FOREIGN TABLE.',
                'options'     => [
                    ['text' => 'An extension that allows querying external sources (other databases, files) as local tables via CREATE FOREIGN TABLE', 'correct' => true],
                    ['text' => 'A constraint type that enforces foreign key references across different schemas',                                      'correct' => false],
                    ['text' => 'A wrapper that converts foreign key columns into UUID types for global uniqueness',                                   'correct' => false],
                    ['text' => 'A driver interface for connecting PostgreSQL to NoSQL databases',                                                    'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the N+1 query problem and how do you solve it in PostgreSQL?',
                'explanation' => 'The N+1 problem: 1 query fetches N parent rows, then N separate queries fetch child data per row. Solution: use a single JOIN or a single WHERE id IN (...) query to fetch all related data at once. PostgreSQL\'s LATERAL join is also useful for complex eager-loading scenarios.',
                'options'     => [
                    ['text' => '1 query fetches N rows then N more for related data; solved by JOIN, IN(...), or LATERAL for all related data in one query', 'correct' => true],
                    ['text' => 'N queries running in parallel that overload the database; solved by connection pooling',                                     'correct' => false],
                    ['text' => 'A table cannot have more than N+1 indexes; solved by dropping redundant indexes',                                           'correct' => false],
                    ['text' => 'Replication lag where replicas are N+1 transactions behind; solved by synchronous replication',                            'correct' => false],
                ],
            ],
            [
                'question'    => 'What are roles in PostgreSQL and how do they differ from users?',
                'explanation' => 'In PostgreSQL, roles and users are the same thing. CREATE USER is an alias for CREATE ROLE WITH LOGIN. Roles can own database objects, be granted privileges (GRANT SELECT, INSERT, etc.), and be members of other roles for inheritance. SUPERUSER and CREATEDB are common role attributes.',
                'options'     => [
                    ['text' => 'Roles and users are the same; CREATE USER is CREATE ROLE WITH LOGIN. Roles own objects, hold privileges, and can be nested.', 'correct' => true],
                    ['text' => 'Roles are groups; users are individuals — a user must belong to at least one role to access the database',                    'correct' => false],
                    ['text' => 'Roles are for permissions only; users are for authentication only — they serve different purposes',                          'correct' => false],
                    ['text' => 'PostgreSQL only has roles; the concept of a user does not exist in PostgreSQL',                                             'correct' => false],
                ],
            ],
            [
                'question'    => 'What is table bloat in PostgreSQL and how do you resolve it?',
                'explanation' => 'Table bloat occurs when MVCC dead tuples accumulate faster than AUTOVACUUM can reclaim them, wasting disk space and degrading performance. Causes: high UPDATE/DELETE churn, AUTOVACUUM not tuned. Resolution: VACUUM (reclaims space for reuse), VACUUM FULL (rewrites table — requires exclusive lock), or pg_repack (online rewrite).',
                'options'     => [
                    ['text' => 'Accumulation of MVCC dead tuples wasting space; resolved by VACUUM, VACUUM FULL, or pg_repack for an online rewrite', 'correct' => true],
                    ['text' => 'Too many indexes on a table slowing down writes; resolved by dropping unused indexes',                                 'correct' => false],
                    ['text' => 'Excessively wide rows due to many columns; resolved by vertical table splitting',                                      'correct' => false],
                    ['text' => 'A table growing beyond its partition limit; resolved by adding new partitions',                                        'correct' => false],
                ],
            ],
            [
                'question'    => 'What is shared_buffers in PostgreSQL and how does it affect performance?',
                'explanation' => 'shared_buffers is the PostgreSQL memory cache for data and index pages (equivalent to InnoDB\'s buffer pool). Setting it to 25% of RAM is the starting recommendation. Larger shared_buffers means more data served from memory. OS page cache also caches data, so the effective cache is shared_buffers + OS cache.',
                'options'     => [
                    ['text' => 'The main memory cache for data and index pages; recommended at 25% of RAM; effective cache includes OS page cache too', 'correct' => true],
                    ['text' => 'A buffer that accumulates writes before flushing to WAL for improved write throughput',                               'correct' => false],
                    ['text' => 'The memory limit for each individual client connection\'s working set',                                              'correct' => false],
                    ['text' => 'A shared memory segment that stores query results for plan caching',                                                'correct' => false],
                ],
            ],
            [
                'question'    => 'What does CLUSTER do in PostgreSQL?',
                'explanation' => 'CLUSTER physically rewrites a table in the order of a specified index. This improves sequential index scans by colocating logically related rows on the same disk pages. Unlike MySQL InnoDB (always clustered on primary key), PostgreSQL tables are heap-organized by default and CLUSTER is a one-time manual operation.',
                'options'     => [
                    ['text' => 'Physically rewrites the table rows in index order for better sequential scan performance; a one-time manual operation (not maintained on insert)', 'correct' => true],
                    ['text' => 'Creates a database cluster with multiple PostgreSQL instances sharing the same data directory',                                                     'correct' => false],
                    ['text' => 'Groups related tables into a named cluster for easier management and shared connection pools',                                                     'correct' => false],
                    ['text' => 'An automatic process that runs nightly to reorganize heap pages for optimal read performance',                                                    'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a composite index in PostgreSQL and what is the leftmost prefix rule?',
                'explanation' => 'A composite index covers multiple columns. Example: CREATE INDEX ON orders(customer_id, status). The leftmost prefix rule means the index can be used for queries filtering on customer_id alone, or (customer_id + status) together, but NOT on status alone.',
                'options'     => [
                    ['text' => 'An index on multiple columns; effective for queries filtering on leading columns; status alone without customer_id skips the index', 'correct' => true],
                    ['text' => 'An index that compresses data across multiple columns to save disk space',                                                           'correct' => false],
                    ['text' => 'An index automatically created for every composite primary key',                                                                    'correct' => false],
                    ['text' => 'An index used only for full-text search across multiple text columns',                                                              'correct' => false],
                ],
            ],
            [
                'question'    => 'What is pg_stat_activity in PostgreSQL?',
                'explanation' => 'pg_stat_activity is a system view that shows one row per server process, including: pid, username, database, application_name, current query, query state (idle, active, idle in transaction), and wait_event. Invaluable for diagnosing locks, long-running queries, and connection pool exhaustion.',
                'options'     => [
                    ['text' => 'A system view showing active server processes with their current query, state, wait events, and connection details', 'correct' => true],
                    ['text' => 'A table that stores historical statistics about past query executions',                                              'correct' => false],
                    ['text' => 'A configuration view showing all active PostgreSQL configuration parameters',                                        'correct' => false],
                    ['text' => 'A log table that records all activity from pg_stat_statements for archiving',                                        'correct' => false],
                ],
            ],
            [
                'question'    => 'What is denormalization in databases and when is it appropriate in PostgreSQL?',
                'explanation' => 'Denormalization intentionally adds redundancy (duplicating data, pre-joining tables) to improve read performance by reducing JOIN overhead. Appropriate for read-heavy workloads, reporting, or analytical queries. PostgreSQL\'s JSONB and ARRAY types also allow semi-structured denormalization within rows.',
                'options'     => [
                    ['text' => 'Intentionally adding redundancy to reduce JOIN overhead and improve read performance; used in read-heavy or analytical workloads', 'correct' => true],
                    ['text' => 'Removing all indexes to speed up INSERT and UPDATE operations',                                                                   'correct' => false],
                    ['text' => 'Converting column names to a non-standard format for legacy compatibility',                                                       'correct' => false],
                    ['text' => 'Dropping foreign key constraints to allow faster bulk imports',                                                                   'correct' => false],
                ],
            ],
            [
                'question'    => 'What is transaction ID (XID) wraparound in PostgreSQL and why is it dangerous?',
                'explanation' => 'PostgreSQL uses 32-bit transaction IDs that wrap around after ~2.1 billion transactions. Without VACUUM running regularly to freeze old XIDs, wraparound can make old committed data appear as future (invisible) data — causing data loss. PostgreSQL emits warnings and eventually forces a shutdown to prevent this.',
                'options'     => [
                    ['text' => 'XID wraparound occurs when 32-bit transaction IDs exhaust, making old data appear invisible; prevented by regular VACUUM/autovacuum', 'correct' => true],
                    ['text' => 'XID wraparound means too many transactions have been aborted, filling the rollback log',                                              'correct' => false],
                    ['text' => 'A circular transaction dependency where two transactions reference each other\'s XID',                                               'correct' => false],
                    ['text' => 'A replication issue where transaction IDs on primary and replica get out of sync',                                                   'correct' => false],
                ],
            ],
            [
                'question'    => 'What is work_mem in PostgreSQL and how does it affect query performance?',
                'explanation' => 'work_mem sets the amount of memory available per sort or hash operation within a query. If a sort exceeds work_mem, PostgreSQL spills to disk (temp files), making it much slower. A single query with multiple sort/hash nodes can use work_mem multiple times. Set conservatively for OLTP; increase in sessions for analytical queries.',
                'options'     => [
                    ['text' => 'Memory per sort/hash operation; if exceeded PostgreSQL spills to disk; one query can use it multiple times per node', 'correct' => true],
                    ['text' => 'Total memory allocated to each client connection for all operations',                                                 'correct' => false],
                    ['text' => 'The memory limit for the entire PostgreSQL server process',                                                          'correct' => false],
                    ['text' => 'Memory reserved for WAL buffer before flushing to disk',                                                            'correct' => false],
                ],
            ],
        ];
    }
}
