<?php

namespace Database\Seeders;

use App\Models\LearningTrack;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;

class MySqlPracticeSeeder extends Seeder
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
                'description'       => 'Relational database management with MySQL — from basics to advanced performance tuning.',
                'display_order'     => 1,
            ]
        );

        $levels = [
            [
                'slug'       => 'mysql-junior',
                'title'      => 'MySQL Basics — Junior',
                'order'      => 1,
                'difficulty' => 'Easy',
                'questions'  => $this->juniorQuestions(),
            ],
            [
                'slug'       => 'mysql-intermediate',
                'title'      => 'MySQL Intermediate',
                'order'      => 2,
                'difficulty' => 'Medium',
                'questions'  => $this->intermediateQuestions(),
            ],
            [
                'slug'       => 'mysql-advanced',
                'title'      => 'MySQL Advanced',
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
                'question'    => 'What does SQL stand for?',
                'explanation' => 'SQL stands for Structured Query Language. It is the standard language used to communicate with relational databases like MySQL.',
                'options'     => [
                    ['text' => 'Structured Query Language', 'correct' => true],
                    ['text' => 'Sequential Query Logic',    'correct' => false],
                    ['text' => 'Simple Question Language',  'correct' => false],
                    ['text' => 'System Query Lookup',       'correct' => false],
                ],
            ],
            [
                'question'    => 'Which SQL command is used to retrieve data from a table?',
                'explanation' => 'SELECT is used to read data from one or more tables. It is one of the most fundamental SQL operations (DML).',
                'options'     => [
                    ['text' => 'SELECT', 'correct' => true],
                    ['text' => 'GET',    'correct' => false],
                    ['text' => 'FETCH',  'correct' => false],
                    ['text' => 'READ',   'correct' => false],
                ],
            ],
            [
                'question'    => 'Which clause is used to filter rows returned by a SELECT query?',
                'explanation' => 'The WHERE clause filters rows based on a condition before the result is returned. Example: SELECT * FROM users WHERE age > 18.',
                'options'     => [
                    ['text' => 'WHERE',  'correct' => true],
                    ['text' => 'FILTER', 'correct' => false],
                    ['text' => 'HAVING', 'correct' => false],
                    ['text' => 'LIMIT',  'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a PRIMARY KEY in MySQL?',
                'explanation' => 'A PRIMARY KEY uniquely identifies each row in a table. It must be unique and cannot be NULL. A table can have only one primary key.',
                'options'     => [
                    ['text' => 'A column that uniquely identifies each row and cannot be NULL',  'correct' => true],
                    ['text' => 'A column that stores the first record inserted into the table',  'correct' => false],
                    ['text' => 'A column used only for indexing and can have duplicate values',  'correct' => false],
                    ['text' => 'A column that links two tables together',                        'correct' => false],
                ],
            ],
            [
                'question'    => 'Which SQL command adds new rows to a table?',
                'explanation' => 'INSERT INTO adds new records to a table. Example: INSERT INTO users (name, email) VALUES (\'Alice\', \'alice@example.com\').',
                'options'     => [
                    ['text' => 'INSERT INTO', 'correct' => true],
                    ['text' => 'ADD INTO',    'correct' => false],
                    ['text' => 'PUT INTO',    'correct' => false],
                    ['text' => 'APPEND INTO', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which SQL command modifies existing data in a table?',
                'explanation' => 'UPDATE modifies existing records. It is paired with SET (to define new values) and WHERE (to target specific rows). Without WHERE, all rows are updated.',
                'options'     => [
                    ['text' => 'UPDATE', 'correct' => true],
                    ['text' => 'MODIFY', 'correct' => false],
                    ['text' => 'CHANGE', 'correct' => false],
                    ['text' => 'EDIT',   'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the key difference between DELETE and TRUNCATE in MySQL?',
                'explanation' => 'DELETE removes specific rows and can be rolled back in a transaction. TRUNCATE removes ALL rows instantly, cannot be easily rolled back, and resets AUTO_INCREMENT counters.',
                'options'     => [
                    ['text' => 'DELETE removes specific rows and is transactional; TRUNCATE removes all rows and resets AUTO_INCREMENT', 'correct' => true],
                    ['text' => 'DELETE is faster than TRUNCATE',                                                                       'correct' => false],
                    ['text' => 'TRUNCATE removes specific rows based on a WHERE clause',                                               'correct' => false],
                    ['text' => 'There is no difference; both commands work identically',                                               'correct' => false],
                ],
            ],
            [
                'question'    => 'Which SQL clause is used to sort the result set of a query?',
                'explanation' => 'ORDER BY sorts the result set in ascending (ASC, default) or descending (DESC) order. Example: SELECT * FROM products ORDER BY price DESC.',
                'options'     => [
                    ['text' => 'ORDER BY', 'correct' => true],
                    ['text' => 'SORT BY',  'correct' => false],
                    ['text' => 'GROUP BY', 'correct' => false],
                    ['text' => 'ARRANGE',  'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the DISTINCT keyword do in a SELECT query?',
                'explanation' => 'DISTINCT eliminates duplicate rows from the result set. Example: SELECT DISTINCT city FROM customers returns each city only once.',
                'options'     => [
                    ['text' => 'Removes duplicate rows from the result',    'correct' => true],
                    ['text' => 'Selects only the first row of each group',  'correct' => false],
                    ['text' => 'Filters rows based on a condition',         'correct' => false],
                    ['text' => 'Returns rows sorted alphabetically',        'correct' => false],
                ],
            ],
            [
                'question'    => 'Which SQL clause limits the number of rows returned by a query?',
                'explanation' => 'LIMIT restricts the number of rows returned. Example: SELECT * FROM users LIMIT 10 returns at most 10 rows. Often paired with OFFSET for pagination.',
                'options'     => [
                    ['text' => 'LIMIT',    'correct' => true],
                    ['text' => 'TOP',      'correct' => false],
                    ['text' => 'RESTRICT', 'correct' => false],
                    ['text' => 'MAX',      'correct' => false],
                ],
            ],
            [
                'question'    => 'What is NULL in MySQL?',
                'explanation' => 'NULL represents a missing or unknown value. NULL is not the same as 0 or an empty string. Comparisons with NULL use IS NULL or IS NOT NULL, not =.',
                'options'     => [
                    ['text' => 'A special marker indicating a missing or unknown value', 'correct' => true],
                    ['text' => 'An integer value of zero',                               'correct' => false],
                    ['text' => 'An empty string value',                                  'correct' => false],
                    ['text' => 'A boolean value representing false',                     'correct' => false],
                ],
            ],
            [
                'question'    => 'Which MySQL data type stores variable-length character strings?',
                'explanation' => 'VARCHAR stores variable-length strings up to a specified maximum (e.g., VARCHAR(255)). CHAR stores fixed-length strings, padding with spaces if shorter.',
                'options'     => [
                    ['text' => 'VARCHAR', 'correct' => true],
                    ['text' => 'CHAR',    'correct' => false],
                    ['text' => 'TEXT',    'correct' => false],
                    ['text' => 'STRING',  'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a FOREIGN KEY in MySQL?',
                'explanation' => 'A FOREIGN KEY is a column in one table that references the PRIMARY KEY of another table. It enforces referential integrity between two tables.',
                'options'     => [
                    ['text' => 'A column that references the primary key of another table to enforce referential integrity', 'correct' => true],
                    ['text' => 'A secondary primary key used when the main primary key is unavailable',                    'correct' => false],
                    ['text' => 'A key used to encrypt data stored in a column',                                             'correct' => false],
                    ['text' => 'A column that stores foreign language characters',                                          'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a relational database?',
                'explanation' => 'A relational database stores data in tables (relations) with rows and columns. Tables are linked by relationships through keys. MySQL is a relational database management system (RDBMS).',
                'options'     => [
                    ['text' => 'A database that organizes data into tables linked by relationships through keys', 'correct' => true],
                    ['text' => 'A database that stores data as key-value pairs',                                  'correct' => false],
                    ['text' => 'A database that stores data as documents in JSON format',                         'correct' => false],
                    ['text' => 'A database that stores data in a single flat file',                              'correct' => false],
                ],
            ],
            [
                'question'    => 'Which SQL command is used to create a new table?',
                'explanation' => 'CREATE TABLE defines a new table with its column names, data types, and constraints. Example: CREATE TABLE users (id INT PRIMARY KEY AUTO_INCREMENT, name VARCHAR(100)).',
                'options'     => [
                    ['text' => 'CREATE TABLE',  'correct' => true],
                    ['text' => 'NEW TABLE',     'correct' => false],
                    ['text' => 'BUILD TABLE',   'correct' => false],
                    ['text' => 'DEFINE TABLE',  'correct' => false],
                ],
            ],
            [
                'question'    => 'Which SQL command permanently removes a table and all its data?',
                'explanation' => 'DROP TABLE removes the entire table structure and all its data from the database permanently. This action cannot be rolled back (DDL statement). TRUNCATE only removes rows but keeps the table.',
                'options'     => [
                    ['text' => 'DROP TABLE',    'correct' => true],
                    ['text' => 'DELETE TABLE',  'correct' => false],
                    ['text' => 'REMOVE TABLE',  'correct' => false],
                    ['text' => 'DESTROY TABLE', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which SQL command modifies an existing table structure (e.g., adding a column)?',
                'explanation' => 'ALTER TABLE is used to modify a table\'s structure — adding, dropping, or modifying columns and constraints. Example: ALTER TABLE users ADD COLUMN age INT.',
                'options'     => [
                    ['text' => 'ALTER TABLE',   'correct' => true],
                    ['text' => 'MODIFY TABLE',  'correct' => false],
                    ['text' => 'CHANGE TABLE',  'correct' => false],
                    ['text' => 'UPDATE TABLE',  'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the UNIQUE constraint do in MySQL?',
                'explanation' => 'UNIQUE ensures that all values in a column (or combination of columns) are distinct. Unlike PRIMARY KEY, a UNIQUE column can contain NULL values (one NULL per column in most databases).',
                'options'     => [
                    ['text' => 'Ensures all values in the column are distinct, preventing duplicate entries',    'correct' => true],
                    ['text' => 'Ensures the column value is always set and cannot be NULL',                     'correct' => false],
                    ['text' => 'Automatically generates an index for the column',                               'correct' => false],
                    ['text' => 'Restricts the column to a predefined set of allowed values',                    'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the NOT NULL constraint enforce in MySQL?',
                'explanation' => 'NOT NULL prevents a column from storing NULL values. Every INSERT or UPDATE must provide a non-NULL value for that column, otherwise the operation fails.',
                'options'     => [
                    ['text' => 'The column must always have a value and cannot be NULL',    'correct' => true],
                    ['text' => 'The column must contain unique values only',                'correct' => false],
                    ['text' => 'The column value must be greater than zero',               'correct' => false],
                    ['text' => 'The column cannot be updated after the initial insert',    'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the DEFAULT keyword do when defining a column?',
                'explanation' => 'DEFAULT sets a fallback value for a column when no value is provided during INSERT. Example: status VARCHAR(20) DEFAULT \'active\' means if status is omitted, it automatically becomes \'active\'.',
                'options'     => [
                    ['text' => 'Sets a fallback value used when no value is provided for the column during INSERT', 'correct' => true],
                    ['text' => 'Sets the column as the primary key of the table',                                   'correct' => false],
                    ['text' => 'Marks the column as optional and allows NULL by default',                           'correct' => false],
                    ['text' => 'Resets the column value to zero on each UPDATE',                                    'correct' => false],
                ],
            ],
            [
                'question'    => 'Which MySQL aggregate function returns the total sum of a numeric column?',
                'explanation' => 'SUM() adds up all non-NULL numeric values in the specified column. Example: SELECT SUM(amount) FROM orders returns the total order value.',
                'options'     => [
                    ['text' => 'SUM()',   'correct' => true],
                    ['text' => 'TOTAL()', 'correct' => false],
                    ['text' => 'ADD()',   'correct' => false],
                    ['text' => 'PLUS()',  'correct' => false],
                ],
            ],
            [
                'question'    => 'Which MySQL aggregate function returns the average value of a numeric column?',
                'explanation' => 'AVG() calculates the arithmetic mean of non-NULL values. Example: SELECT AVG(salary) FROM employees returns the average salary across all employees.',
                'options'     => [
                    ['text' => 'AVG()',    'correct' => true],
                    ['text' => 'MEAN()',   'correct' => false],
                    ['text' => 'AVERAGE()', 'correct' => false],
                    ['text' => 'MID()',    'correct' => false],
                ],
            ],
            [
                'question'    => 'Which MySQL aggregate functions return the smallest and largest values in a column?',
                'explanation' => 'MIN() returns the smallest value and MAX() returns the largest value in a column. They work on numeric, date, and string types. NULLs are ignored.',
                'options'     => [
                    ['text' => 'MIN() and MAX()',     'correct' => true],
                    ['text' => 'SMALL() and LARGE()', 'correct' => false],
                    ['text' => 'FIRST() and LAST()',  'correct' => false],
                    ['text' => 'LOW() and HIGH()',    'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the LIKE operator do in a MySQL WHERE clause?',
                'explanation' => 'LIKE performs pattern matching on strings. Use % to match any sequence of characters and _ to match exactly one character. Example: WHERE name LIKE \'A%\' matches names starting with A.',
                'options'     => [
                    ['text' => 'Performs pattern matching using % (any characters) and _ (one character)',   'correct' => true],
                    ['text' => 'Checks if a value is equal to another value, similar to =',                  'correct' => false],
                    ['text' => 'Checks if a value exists in a list of values',                               'correct' => false],
                    ['text' => 'Compares two columns and returns rows where they are similar',               'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the IN operator do in a WHERE clause?',
                'explanation' => 'IN checks if a value matches any value in a given list or subquery. Example: WHERE status IN (\'active\', \'pending\') is equivalent to WHERE status = \'active\' OR status = \'pending\'.',
                'options'     => [
                    ['text' => 'Checks if a value matches any value in a specified list or subquery',  'correct' => true],
                    ['text' => 'Checks if a value is within a numeric range',                          'correct' => false],
                    ['text' => 'Checks if a column is part of an index',                               'correct' => false],
                    ['text' => 'Joins two tables based on a matching condition',                        'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the BETWEEN operator do in MySQL?',
                'explanation' => 'BETWEEN filters rows where a value falls within a range (inclusive on both ends). Example: WHERE price BETWEEN 100 AND 500 returns rows where price is 100, 500, or anything in between.',
                'options'     => [
                    ['text' => 'Filters rows where a value falls within an inclusive range (e.g., BETWEEN 10 AND 100)', 'correct' => true],
                    ['text' => 'Filters rows where a value is between two rows in the result set',                      'correct' => false],
                    ['text' => 'Selects all columns between the specified first and last column',                       'correct' => false],
                    ['text' => 'Joins two tables and returns only the rows between matching rows',                      'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the AS keyword do in a SQL query?',
                'explanation' => 'AS creates an alias — a temporary name for a column or table in the result set. Example: SELECT first_name AS name FROM users. Aliases are used for readability and are required when using expressions.',
                'options'     => [
                    ['text' => 'Creates a temporary alias (rename) for a column or table in the query result', 'correct' => true],
                    ['text' => 'Converts a column from one data type to another',                              'correct' => false],
                    ['text' => 'Creates a permanent rename of a column in the table',                          'correct' => false],
                    ['text' => 'Assigns a variable to store the query result',                                 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a schema in MySQL?',
                'explanation' => 'In MySQL, a schema and a database are essentially the same thing. A schema is a collection of tables, views, procedures, and other database objects. CREATE SCHEMA is an alias for CREATE DATABASE in MySQL.',
                'options'     => [
                    ['text' => 'In MySQL, a schema is equivalent to a database — a named container for tables and other objects', 'correct' => true],
                    ['text' => 'A schema is a single table structure definition without any data',                                'correct' => false],
                    ['text' => 'A schema is a read-only snapshot of a database at a point in time',                               'correct' => false],
                    ['text' => 'A schema is a collection of stored procedures and triggers only',                                 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which MySQL function joins two or more strings together?',
                'explanation' => 'CONCAT() joins multiple strings into one. Example: SELECT CONCAT(first_name, \' \', last_name) AS full_name FROM users. If any argument is NULL, the result is NULL (use CONCAT_WS to handle NULLs).',
                'options'     => [
                    ['text' => 'CONCAT()',   'correct' => true],
                    ['text' => 'JOIN()',     'correct' => false],
                    ['text' => 'MERGE()',    'correct' => false],
                    ['text' => 'COMBINE()', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between INT and BIGINT in MySQL?',
                'explanation' => 'INT stores integers up to ~2.1 billion (4 bytes). BIGINT stores much larger integers up to ~9.2 quintillion (8 bytes). Use BIGINT when your values may exceed the INT range, such as IDs in very large-scale systems.',
                'options'     => [
                    ['text' => 'INT stores up to ~2.1 billion (4 bytes); BIGINT stores up to ~9.2 quintillion (8 bytes)', 'correct' => true],
                    ['text' => 'INT stores decimals; BIGINT stores whole numbers only',                                   'correct' => false],
                    ['text' => 'BIGINT is faster than INT for all arithmetic operations',                                 'correct' => false],
                    ['text' => 'They are identical; BIGINT is just an alias for INT with a larger display width',         'correct' => false],
                ],
            ],
            [
                'question'    => 'What does DDL stand for in SQL?',
                'explanation' => 'DDL stands for Data Definition Language. DDL commands define and modify the database structure. Examples: CREATE, ALTER, DROP, TRUNCATE. These commands auto-commit and cannot usually be rolled back.',
                'options'     => [
                    ['text' => 'Data Definition Language — commands that define database structure (CREATE, ALTER, DROP)', 'correct' => true],
                    ['text' => 'Data Deletion Language — commands that delete records from tables',                       'correct' => false],
                    ['text' => 'Data Dependency Logic — rules that enforce referential integrity',                        'correct' => false],
                    ['text' => 'Data Distribution Layer — commands that replicate tables across servers',                 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does DML stand for in SQL?',
                'explanation' => 'DML stands for Data Manipulation Language. DML commands manipulate the data stored in tables. Examples: SELECT, INSERT, UPDATE, DELETE. DML operations are transactional and can be rolled back.',
                'options'     => [
                    ['text' => 'Data Manipulation Language — commands that read and modify table data (SELECT, INSERT, UPDATE, DELETE)', 'correct' => true],
                    ['text' => 'Data Migration Layer — commands that move data between databases',                                       'correct' => false],
                    ['text' => 'Data Management Language — commands that manage users and permissions',                                  'correct' => false],
                    ['text' => 'Data Modeling Language — commands that create relationships between tables',                            'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the COUNT(*) aggregate function return?',
                'explanation' => 'COUNT(*) returns the total number of rows in the result set, including rows with NULL values. COUNT(column) counts only non-NULL values in that column.',
                'options'     => [
                    ['text' => 'The total number of rows in the result set, including rows with NULL values', 'correct' => true],
                    ['text' => 'The number of distinct values in the specified column',                       'correct' => false],
                    ['text' => 'The sum of all values in the specified column',                              'correct' => false],
                    ['text' => 'The number of columns in the result set',                                    'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of the OFFSET clause in MySQL?',
                'explanation' => 'OFFSET skips a specified number of rows before returning results. Used with LIMIT for pagination. Example: SELECT * FROM products LIMIT 10 OFFSET 20 skips the first 20 rows and returns the next 10.',
                'options'     => [
                    ['text' => 'Skips a specified number of rows before returning results, used with LIMIT for pagination', 'correct' => true],
                    ['text' => 'Sets the starting value for AUTO_INCREMENT columns',                                        'correct' => false],
                    ['text' => 'Shifts column values by a fixed amount in mathematical operations',                         'correct' => false],
                    ['text' => 'Specifies the byte position to begin reading from a BLOB column',                          'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between TEXT and BLOB in MySQL?',
                'explanation' => 'TEXT stores large string data (character set aware — supports collation and case-insensitive comparison). BLOB stores large binary data (Binary Large Object — byte-for-byte, no character encoding). Use TEXT for prose/content and BLOB for images, files, or encrypted data.',
                'options'     => [
                    ['text' => 'TEXT stores character string data with charset/collation; BLOB stores raw binary data with no encoding', 'correct' => true],
                    ['text' => 'BLOB is larger than TEXT; TEXT is limited to 65KB while BLOB can hold up to 4GB',                       'correct' => false],
                    ['text' => 'They are identical; BLOB is just the older name for TEXT in MySQL',                                    'correct' => false],
                    ['text' => 'TEXT is compressed automatically by MySQL; BLOB is stored uncompressed',                               'correct' => false],
                ],
            ],
            [
                'question'    => 'Which MySQL data type should you use to store a date and time together?',
                'explanation' => 'DATETIME stores a date and time as \'YYYY-MM-DD HH:MM:SS\' in the range 1000-01-01 to 9999-12-31. TIMESTAMP also stores date+time but is stored as UTC and auto-converts to the session timezone. Use DATETIME for user-entered dates and TIMESTAMP for record creation/update tracking.',
                'options'     => [
                    ['text' => 'DATETIME stores date and time in \'YYYY-MM-DD HH:MM:SS\' format; TIMESTAMP stores as UTC and auto-converts timezone', 'correct' => true],
                    ['text' => 'DATE stores both date and time; DATETIME stores only the date portion',                                               'correct' => false],
                    ['text' => 'TIMESTAMP is preferred for all date-time storage as it is more accurate than DATETIME',                               'correct' => false],
                    ['text' => 'TIME stores the complete date and time; DATETIME stores only the time portion',                                       'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the SHOW TABLES statement do in MySQL?',
                'explanation' => 'SHOW TABLES lists all tables in the currently selected database. Use SHOW DATABASES to list all databases, SHOW COLUMNS FROM table to see column definitions, and SHOW CREATE TABLE table to see the full CREATE statement.',
                'options'     => [
                    ['text' => 'Lists all tables in the currently selected database', 'correct' => true],
                    ['text' => 'Lists all databases on the MySQL server',             'correct' => false],
                    ['text' => 'Lists all columns in the currently selected table',  'correct' => false],
                    ['text' => 'Shows the current table the cursor is pointing to',  'correct' => false],
                ],
            ],
        ];
    }

    private function intermediateQuestions(): array
    {
        return [
            [
                'question'    => 'What is the difference between INNER JOIN and LEFT JOIN in MySQL?',
                'explanation' => 'INNER JOIN returns only rows that have matching values in both tables. LEFT JOIN returns all rows from the left table plus matching rows from the right — NULLs fill unmatched right-side columns.',
                'options'     => [
                    ['text' => 'INNER JOIN returns matching rows only; LEFT JOIN returns all left rows and NULLs for unmatched right rows', 'correct' => true],
                    ['text' => 'LEFT JOIN is faster than INNER JOIN in all cases',                                                         'correct' => false],
                    ['text' => 'INNER JOIN includes NULL rows; LEFT JOIN excludes them',                                                   'correct' => false],
                    ['text' => 'They are identical — LEFT JOIN is just an alias for INNER JOIN',                                           'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an INDEX in MySQL and what is its primary purpose?',
                'explanation' => 'An INDEX is a data structure (typically a B-tree) that speeds up data retrieval by avoiding full table scans. Indexes improve SELECT performance but add overhead to INSERT, UPDATE, and DELETE.',
                'options'     => [
                    ['text' => 'A data structure that speeds up row lookups by avoiding full table scans',  'correct' => true],
                    ['text' => 'A backup copy of a table stored separately',                               'correct' => false],
                    ['text' => 'A constraint that prevents duplicate values in a column',                  'correct' => false],
                    ['text' => 'A numbered list of columns in a table',                                    'correct' => false],
                ],
            ],
            [
                'question'    => 'What does GROUP BY do in a SQL query?',
                'explanation' => 'GROUP BY groups rows that share the same values in specified columns, then allows aggregate functions (COUNT, SUM, AVG, MAX, MIN) to operate on each group.',
                'options'     => [
                    ['text' => 'Groups rows with the same column values so aggregate functions can be applied per group', 'correct' => true],
                    ['text' => 'Sorts rows in ascending order by the specified column',                                  'correct' => false],
                    ['text' => 'Filters rows based on a condition applied after the query runs',                         'correct' => false],
                    ['text' => 'Removes duplicate rows from the result set',                                             'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between WHERE and HAVING in MySQL?',
                'explanation' => 'WHERE filters rows BEFORE grouping. HAVING filters groups AFTER GROUP BY and can reference aggregate functions. Example: WHERE age > 18 vs HAVING COUNT(*) > 5.',
                'options'     => [
                    ['text' => 'WHERE filters rows before grouping; HAVING filters groups after GROUP BY and can use aggregates', 'correct' => true],
                    ['text' => 'HAVING filters rows before grouping; WHERE filters after grouping',                              'correct' => false],
                    ['text' => 'They are interchangeable and produce identical results',                                         'correct' => false],
                    ['text' => 'WHERE works on indexed columns only; HAVING works on all columns',                               'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a subquery in MySQL?',
                'explanation' => 'A subquery is a SELECT statement nested inside another query. It can appear in WHERE, FROM, or SELECT clauses. Example: SELECT * FROM users WHERE id IN (SELECT user_id FROM orders WHERE total > 1000).',
                'options'     => [
                    ['text' => 'A SELECT statement nested inside another SQL statement to provide values or result sets', 'correct' => true],
                    ['text' => 'A stored procedure that runs automatically after a main query',                          'correct' => false],
                    ['text' => 'A query that runs on a subset of table columns only',                                   'correct' => false],
                    ['text' => 'A query optimized to run on secondary indexes',                                         'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a transaction in MySQL?',
                'explanation' => 'A transaction is a sequence of SQL operations executed as a single unit. Either all operations succeed (COMMIT) or all are rolled back (ROLLBACK). Transactions are managed with START TRANSACTION, COMMIT, and ROLLBACK.',
                'options'     => [
                    ['text' => 'A sequence of SQL operations treated as one unit that either fully commits or fully rolls back', 'correct' => true],
                    ['text' => 'A single SELECT query that reads data from multiple tables',                                    'correct' => false],
                    ['text' => 'A scheduled backup operation in MySQL',                                                         'correct' => false],
                    ['text' => 'A network request from a client to the MySQL server',                                           'correct' => false],
                ],
            ],
            [
                'question'    => 'Which of the following best describes the ACID properties in MySQL?',
                'explanation' => 'ACID stands for Atomicity (all-or-nothing), Consistency (valid state before and after), Isolation (transactions do not interfere), and Durability (committed data persists). InnoDB fully supports ACID.',
                'options'     => [
                    ['text' => 'Atomicity, Consistency, Isolation, Durability — properties that guarantee reliable transactions', 'correct' => true],
                    ['text' => 'Authentication, Caching, Indexing, Distribution — database performance properties',               'correct' => false],
                    ['text' => 'Automation, Compression, Integration, Deployment — storage engine capabilities',                  'correct' => false],
                    ['text' => 'Availability, Concurrency, Integrity, Dependency — replication guarantees',                      'correct' => false],
                ],
            ],
            [
                'question'    => 'What does AUTO_INCREMENT do in MySQL?',
                'explanation' => 'AUTO_INCREMENT automatically generates a unique sequential integer for a column (usually the primary key) on each INSERT. The value starts at 1 and increments by 1 by default.',
                'options'     => [
                    ['text' => 'Automatically generates a unique sequential integer for a column on each INSERT',      'correct' => true],
                    ['text' => 'Automatically increments a counter column every time the table is queried',            'correct' => false],
                    ['text' => 'Automatically increases the column size when the maximum value is reached',            'correct' => false],
                    ['text' => 'Automatically creates an index on the column it is applied to',                       'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a VIEW in MySQL?',
                'explanation' => 'A VIEW is a named virtual table based on a stored SELECT query. It does not store data itself but presents data from underlying tables. Views simplify complex queries and can restrict data access.',
                'options'     => [
                    ['text' => 'A virtual table defined by a stored SELECT query that does not store data itself',  'correct' => true],
                    ['text' => 'A physical copy of a table used for backup purposes',                              'correct' => false],
                    ['text' => 'A UI component for displaying query results',                                       'correct' => false],
                    ['text' => 'A temporary table created during a JOIN operation',                                 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is database normalization?',
                'explanation' => 'Normalization is the process of organizing a database to reduce data redundancy and improve data integrity. It involves splitting data into multiple related tables following normal forms (1NF, 2NF, 3NF, etc.).',
                'options'     => [
                    ['text' => 'Organizing tables to eliminate data redundancy and ensure data integrity through normal forms', 'correct' => true],
                    ['text' => 'Converting all column names to lowercase for consistency',                                    'correct' => false],
                    ['text' => 'Compressing table data to reduce storage size',                                              'correct' => false],
                    ['text' => 'Reordering table rows to improve query performance',                                         'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between CHAR and VARCHAR in MySQL?',
                'explanation' => 'CHAR(n) is fixed-length — it always uses n bytes regardless of actual string length, padding with spaces. VARCHAR(n) is variable-length — it stores only as many bytes as needed plus 1-2 overhead bytes. CHAR is faster for fixed-size data; VARCHAR saves space for variable-size data.',
                'options'     => [
                    ['text' => 'CHAR is fixed-length and pads with spaces; VARCHAR is variable-length and stores only the actual characters', 'correct' => true],
                    ['text' => 'CHAR supports Unicode; VARCHAR supports only ASCII',                                                         'correct' => false],
                    ['text' => 'VARCHAR has a maximum of 255 characters; CHAR has no maximum',                                               'correct' => false],
                    ['text' => 'They are identical in storage and performance',                                                              'correct' => false],
                ],
            ],
            [
                'question'    => 'What aggregate function counts the number of rows matching a query?',
                'explanation' => 'COUNT() returns the number of rows. COUNT(*) counts all rows including NULLs. COUNT(column) counts rows where the column is not NULL. Example: SELECT COUNT(*) FROM orders WHERE status = \'shipped\'.',
                'options'     => [
                    ['text' => 'COUNT()',  'correct' => true],
                    ['text' => 'TOTAL()',  'correct' => false],
                    ['text' => 'NUMBER()', 'correct' => false],
                    ['text' => 'ROWS()',   'correct' => false],
                ],
            ],
            [
                'question'    => 'What does a RIGHT JOIN return in MySQL?',
                'explanation' => 'RIGHT JOIN returns all rows from the right table and the matching rows from the left table. Unmatched rows from the left table show NULLs. It is the mirror image of LEFT JOIN.',
                'options'     => [
                    ['text' => 'All rows from the right table plus matching left rows, with NULLs for unmatched left rows', 'correct' => true],
                    ['text' => 'Only rows where both tables have matching values (same as INNER JOIN)',                     'correct' => false],
                    ['text' => 'All rows from both tables regardless of matching',                                          'correct' => false],
                    ['text' => 'All rows from the left table plus matching right rows',                                     'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a SELF JOIN in MySQL?',
                'explanation' => 'A SELF JOIN joins a table to itself. It is used to compare rows within the same table. Example: finding employees who report to a manager from the same employees table using aliases.',
                'options'     => [
                    ['text' => 'A join where a table is joined to itself using aliases, used to compare rows within the same table', 'correct' => true],
                    ['text' => 'A join that automatically happens without writing JOIN syntax',                                      'correct' => false],
                    ['text' => 'A join where MySQL optimizes by reusing cached join results',                                        'correct' => false],
                    ['text' => 'A join that connects a table to its parent table using a foreign key',                              'correct' => false],
                ],
            ],
            [
                'question'    => 'What does UNION do in MySQL and how does it differ from UNION ALL?',
                'explanation' => 'UNION combines result sets from two SELECT statements and removes duplicates. UNION ALL combines them without removing duplicates and is faster because it skips the deduplication step.',
                'options'     => [
                    ['text' => 'UNION combines result sets and removes duplicates; UNION ALL combines without removing duplicates (faster)', 'correct' => true],
                    ['text' => 'UNION ALL returns only rows that appear in both result sets (intersection)',                                 'correct' => false],
                    ['text' => 'They are identical; UNION ALL is just a syntax variation',                                                  'correct' => false],
                    ['text' => 'UNION joins tables by column; UNION ALL joins by row',                                                      'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a Common Table Expression (CTE) in MySQL 8?',
                'explanation' => 'A CTE (defined with WITH) is a named temporary result set scoped to the current query. CTEs improve readability and allow recursive queries. Example: WITH active_users AS (SELECT * FROM users WHERE active = 1) SELECT * FROM active_users.',
                'options'     => [
                    ['text' => 'A temporary named result set defined with WITH, scoped to the current query, supporting recursive queries', 'correct' => true],
                    ['text' => 'A permanently stored expression that calculates a value when called',                                       'correct' => false],
                    ['text' => 'A column expression computed at query time and stored in a virtual column',                                 'correct' => false],
                    ['text' => 'A connection template that reuses query parameters across multiple queries',                                'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the CASE expression in MySQL?',
                'explanation' => 'CASE is a conditional expression similar to if-else. It evaluates conditions and returns a value for the first matching condition. Example: CASE WHEN score >= 90 THEN \'A\' WHEN score >= 80 THEN \'B\' ELSE \'C\' END.',
                'options'     => [
                    ['text' => 'A conditional expression that evaluates conditions and returns a value for the first match (if-else logic)', 'correct' => true],
                    ['text' => 'A keyword for switching between different SQL query modes',                                                  'correct' => false],
                    ['text' => 'A stored procedure that handles error conditions in transactions',                                           'correct' => false],
                    ['text' => 'A MySQL function for converting a value to uppercase or lowercase',                                          'correct' => false],
                ],
            ],
            [
                'question'    => 'What does COALESCE() do in MySQL?',
                'explanation' => 'COALESCE() returns the first non-NULL value from its argument list. Example: COALESCE(middle_name, \'\') returns middle_name if it is not NULL, otherwise an empty string. Useful for handling NULL fallbacks.',
                'options'     => [
                    ['text' => 'Returns the first non-NULL value from a list of arguments', 'correct' => true],
                    ['text' => 'Combines multiple columns into a single string',            'correct' => false],
                    ['text' => 'Checks if two values are equal and returns a boolean',      'correct' => false],
                    ['text' => 'Converts NULL to zero for arithmetic calculations',         'correct' => false],
                ],
            ],
            [
                'question'    => 'What is ON DELETE CASCADE in MySQL?',
                'explanation' => 'ON DELETE CASCADE is a foreign key action that automatically deletes child rows when the parent row is deleted. Example: if a user is deleted, all their orders are also automatically deleted.',
                'options'     => [
                    ['text' => 'Automatically deletes child rows in a related table when the parent row is deleted', 'correct' => true],
                    ['text' => 'Prevents deletion of a parent row if related child rows exist',                      'correct' => false],
                    ['text' => 'Sets the foreign key column to NULL when the parent row is deleted',                 'correct' => false],
                    ['text' => 'Copies deleted rows into an archive table before deletion',                          'correct' => false],
                ],
            ],
            [
                'question'    => 'What is ROLLBACK in MySQL transactions?',
                'explanation' => 'ROLLBACK undoes all changes made in the current transaction since START TRANSACTION (or the last SAVEPOINT). It restores the database to the state before the transaction began.',
                'options'     => [
                    ['text' => 'Undoes all changes made in the current transaction, restoring the database to its previous state', 'correct' => true],
                    ['text' => 'Saves the current transaction state as a checkpoint',                                              'correct' => false],
                    ['text' => 'Reverses the last SELECT query result',                                                            'correct' => false],
                    ['text' => 'Restores a deleted table from a backup',                                                           'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a SAVEPOINT in MySQL?',
                'explanation' => 'SAVEPOINT marks a point within a transaction to which you can later rollback partially. Example: SAVEPOINT sp1 ... ROLLBACK TO sp1 undoes only the work after sp1 without ending the entire transaction.',
                'options'     => [
                    ['text' => 'A named marker within a transaction allowing partial rollback to that point', 'correct' => true],
                    ['text' => 'A permanent checkpoint that saves the database state to disk',               'correct' => false],
                    ['text' => 'An automatic backup triggered when a transaction begins',                    'correct' => false],
                    ['text' => 'A named stored procedure checkpoint for debugging',                          'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a prepared statement in MySQL and why is it important?',
                'explanation' => 'A prepared statement is a precompiled SQL template where parameters are sent separately. Benefits: prevents SQL injection (parameters are never interpreted as SQL), and can be more efficient for repeated queries as the query plan is compiled once.',
                'options'     => [
                    ['text' => 'A precompiled SQL template with separate parameters that prevents SQL injection and improves repeated query performance', 'correct' => true],
                    ['text' => 'A stored procedure that prepares data before inserting it into a table',                                                  'correct' => false],
                    ['text' => 'A query that is cached in memory for instant execution on repeated calls',                                                'correct' => false],
                    ['text' => 'A type of trigger that runs before INSERT statements to validate data',                                                   'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an ENUM data type in MySQL?',
                'explanation' => 'ENUM is a string data type that restricts a column to one value from a predefined list. Example: status ENUM(\'active\', \'inactive\', \'pending\'). Stored efficiently as integers internally. The column can only hold one of the listed values.',
                'options'     => [
                    ['text' => 'A data type that restricts a column to one value from a predefined list of strings', 'correct' => true],
                    ['text' => 'A data type for storing enumerated integer sequences',                               'correct' => false],
                    ['text' => 'A data type for storing multiple selected values from a list (like a checkbox)',     'correct' => false],
                    ['text' => 'A data type for storing JSON arrays of string values',                               'correct' => false],
                ],
            ],
            [
                'question'    => 'What is First Normal Form (1NF) in database design?',
                'explanation' => '1NF requires that: (1) each column holds atomic (indivisible) values, (2) each column holds values of a single type, and (3) each row is uniquely identifiable (has a primary key). No repeating groups or arrays in a cell.',
                'options'     => [
                    ['text' => 'Each column holds atomic values, each row is unique, and there are no repeating groups', 'correct' => true],
                    ['text' => 'All non-key columns depend on the entire primary key, not just part of it',              'correct' => false],
                    ['text' => 'All non-key columns depend only on the primary key, not on other non-key columns',       'correct' => false],
                    ['text' => 'The table has no NULL values in any column',                                             'correct' => false],
                ],
            ],
            [
                'question'    => 'What does IFNULL() do in MySQL?',
                'explanation' => 'IFNULL(expr, alt) returns expr if it is not NULL, otherwise returns alt. Example: IFNULL(phone, \'N/A\') returns the phone number if set, or \'N/A\' if NULL. It is a two-argument shorthand for COALESCE().',
                'options'     => [
                    ['text' => 'Returns the first argument if it is not NULL, otherwise returns the second argument', 'correct' => true],
                    ['text' => 'Checks if a column is NULL and throws an error if it is',                            'correct' => false],
                    ['text' => 'Converts a NULL value to 0 for numeric columns',                                     'correct' => false],
                    ['text' => 'Filters out NULL rows from a SELECT result set',                                     'correct' => false],
                ],
            ],
            [
                'question'    => 'What are the transaction isolation levels in MySQL?',
                'explanation' => 'MySQL InnoDB supports four isolation levels (from least to most strict): READ UNCOMMITTED, READ COMMITTED, REPEATABLE READ (default), and SERIALIZABLE. Higher isolation reduces concurrency anomalies (dirty reads, non-repeatable reads, phantom reads) but increases lock contention.',
                'options'     => [
                    ['text' => 'READ UNCOMMITTED, READ COMMITTED, REPEATABLE READ (default), and SERIALIZABLE', 'correct' => true],
                    ['text' => 'NONE, BASIC, STANDARD, and STRICT',                                             'correct' => false],
                    ['text' => 'LOW, MEDIUM, HIGH, and FULL',                                                   'correct' => false],
                    ['text' => 'OPTIMISTIC, PESSIMISTIC, SHARED, and EXCLUSIVE',                                'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a CROSS JOIN in MySQL?',
                'explanation' => 'A CROSS JOIN returns the Cartesian product of two tables — every row from the first table paired with every row from the second. If table A has 3 rows and B has 4, the result has 12 rows. Use with caution as result sets grow exponentially.',
                'options'     => [
                    ['text' => 'Returns every combination of rows from both tables (Cartesian product), producing M × N rows', 'correct' => true],
                    ['text' => 'Returns only rows where a cross-reference condition is met between two tables',                'correct' => false],
                    ['text' => 'Joins tables across different databases on the same MySQL server',                             'correct' => false],
                    ['text' => 'Returns rows that exist in one table but not the other (set difference)',                      'correct' => false],
                ],
            ],
            [
                'question'    => 'Which MySQL function formats a date value into a specified string format?',
                'explanation' => 'DATE_FORMAT() formats a date or datetime value using a format string. Example: DATE_FORMAT(created_at, \'%Y-%m-%d\') returns \'2024-05-15\'. Common format codes: %Y (4-digit year), %m (month), %d (day), %H (hour), %i (minute).',
                'options'     => [
                    ['text' => 'DATE_FORMAT()',   'correct' => true],
                    ['text' => 'FORMAT_DATE()',   'correct' => false],
                    ['text' => 'TO_DATE()',       'correct' => false],
                    ['text' => 'CONVERT_DATE()',  'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a temporary table in MySQL?',
                'explanation' => 'A temporary table is created with CREATE TEMPORARY TABLE and exists only for the duration of the current session. It is automatically dropped when the session ends. Useful for storing intermediate results in complex queries without polluting the schema.',
                'options'     => [
                    ['text' => 'A table that exists only for the current session and is automatically dropped when the session ends', 'correct' => true],
                    ['text' => 'A table stored in memory instead of disk, automatically deleted after each query',                  'correct' => false],
                    ['text' => 'A read-only copy of a table created during a JOIN operation',                                       'correct' => false],
                    ['text' => 'A backup table automatically created before each TRUNCATE operation',                               'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the NATURAL JOIN do in MySQL?',
                'explanation' => 'NATURAL JOIN automatically joins two tables on all columns that share the same name and data type. It is implicit — no ON or USING clause is needed. While convenient, it is discouraged in production because adding a same-named column later silently changes the join behavior.',
                'options'     => [
                    ['text' => 'Automatically joins tables on all columns with the same name and type, without an explicit ON clause', 'correct' => true],
                    ['text' => 'Joins tables using the most efficient (natural) execution plan chosen by the optimizer',              'correct' => false],
                    ['text' => 'Joins tables in the natural (insertion) order of their rows',                                        'correct' => false],
                    ['text' => 'An alias for INNER JOIN that uses primary key columns automatically',                                'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the USING clause do in a MySQL JOIN?',
                'explanation' => 'USING(column) is a shorthand for ON table1.column = table2.column when both tables share a column with the same name. Example: SELECT * FROM orders JOIN users USING(user_id). It also removes the duplicate column from the result set.',
                'options'     => [
                    ['text' => 'Specifies a shared column name to join on, equivalent to ON t1.col = t2.col, and removes the duplicate column from results', 'correct' => true],
                    ['text' => 'Specifies which index to use for the join operation',                                                                         'correct' => false],
                    ['text' => 'Limits the join to use a maximum number of rows from each table',                                                             'correct' => false],
                    ['text' => 'Defines the join type (INNER, LEFT, RIGHT) in a shorthand syntax',                                                           'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an index prefix in MySQL and when is it useful?',
                'explanation' => 'An index prefix indexes only the first N characters of a text column. Example: INDEX(description(50)) indexes only the first 50 characters. Useful for BLOB/TEXT columns (which cannot be fully indexed) or long VARCHAR columns to reduce index size while still enabling prefix-based searches.',
                'options'     => [
                    ['text' => 'Indexing only the first N characters of a text column to reduce index size while enabling prefix searches', 'correct' => true],
                    ['text' => 'Adding a prefix string to all index names for namespace organization',                                       'correct' => false],
                    ['text' => 'The leading columns of a composite index that must be present in a query for the index to be used',          'correct' => false],
                    ['text' => 'A partial index that only indexes rows where a condition is true',                                          'correct' => false],
                ],
            ],
        ];
    }

    private function advancedQuestions(): array
    {
        return [
            [
                'question'    => 'What is the difference between the InnoDB and MyISAM storage engines in MySQL?',
                'explanation' => 'InnoDB supports transactions (ACID), foreign keys, row-level locking, and crash recovery. MyISAM does not support transactions or foreign keys, uses table-level locking, and is faster for read-heavy workloads without write concurrency. InnoDB is the default since MySQL 5.5.',
                'options'     => [
                    ['text' => 'InnoDB supports ACID transactions, foreign keys, and row-level locking; MyISAM lacks transactions and uses table-level locking', 'correct' => true],
                    ['text' => 'MyISAM is the default engine in MySQL 8 and supports all ACID properties',                                                       'correct' => false],
                    ['text' => 'InnoDB is faster for all query types; MyISAM is only used for legacy databases',                                                 'correct' => false],
                    ['text' => 'Both engines support transactions; the only difference is locking granularity',                                                   'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the MySQL EXPLAIN statement do?',
                'explanation' => 'EXPLAIN shows the query execution plan — how MySQL will execute a SELECT. It reveals which indexes are used, join types, estimated row counts, and potential full table scans. It is the primary tool for query optimization.',
                'options'     => [
                    ['text' => 'Shows the query execution plan including indexes used, join types, and estimated row counts', 'correct' => true],
                    ['text' => 'Executes the query and explains the result set in human-readable format',                    'correct' => false],
                    ['text' => 'Displays the table structure, column types, and constraints',                                'correct' => false],
                    ['text' => 'Benchmarks the query and returns execution time statistics',                                 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a composite index in MySQL and when should you use it?',
                'explanation' => 'A composite index covers multiple columns (e.g., INDEX(last_name, first_name)). MySQL uses it for queries filtering on the leading columns. The leftmost prefix rule applies — an index on (a, b, c) supports queries on a, (a, b), or (a, b, c), but not b or c alone.',
                'options'     => [
                    ['text' => 'An index on multiple columns used when queries filter on those columns together, following the leftmost prefix rule', 'correct' => true],
                    ['text' => 'An index that stores compressed data to reduce disk usage',                                                          'correct' => false],
                    ['text' => 'An index automatically created by MySQL for every table with a primary key',                                         'correct' => false],
                    ['text' => 'An index used only for full-text search across multiple text columns',                                               'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a deadlock in MySQL and how can it be prevented?',
                'explanation' => 'A deadlock occurs when two or more transactions wait on each other\'s locks indefinitely. InnoDB detects deadlocks and rolls back the transaction with the least cost. Prevention: access tables in a consistent order, keep transactions short, and prefer row-level locking.',
                'options'     => [
                    ['text' => 'A situation where two transactions wait on each other\'s locks indefinitely; prevented by consistent lock ordering and short transactions', 'correct' => true],
                    ['text' => 'A query that never finishes due to missing indexes; prevented by adding proper indexes',                                                   'correct' => false],
                    ['text' => 'A full table scan that locks the entire table; prevented by using SELECT FOR UPDATE',                                                      'correct' => false],
                    ['text' => 'A race condition in connection pooling; prevented by increasing the max connection limit',                                                  'correct' => false],
                ],
            ],
            [
                'question'    => 'What is MySQL replication and what are its two most common types?',
                'explanation' => 'Replication copies data from a primary (master) server to one or more replicas (slaves). Asynchronous replication (default) — the primary does not wait for replicas to confirm. Semi-synchronous — the primary waits for at least one replica to acknowledge. Used for read scaling and high availability.',
                'options'     => [
                    ['text' => 'Copying data from a primary to replicas; most common types are asynchronous (default) and semi-synchronous',  'correct' => true],
                    ['text' => 'A backup strategy that duplicates only modified rows; types are full and incremental',                        'correct' => false],
                    ['text' => 'A caching mechanism that copies query results to secondary servers for faster reads',                         'correct' => false],
                    ['text' => 'A sharding strategy that replicates schema across multiple databases; types are horizontal and vertical',     'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a stored procedure in MySQL and what are its advantages?',
                'explanation' => 'A stored procedure is a named block of SQL stored in the database and executed by calling it by name. Advantages: reduce network round-trips, reuse logic, encapsulate business rules at the DB layer, and grant permissions separately from tables.',
                'options'     => [
                    ['text' => 'A named SQL block stored in the database; advantages include reduced network trips and reusable logic', 'correct' => true],
                    ['text' => 'A SQL query stored in application code that is cached for repeated use',                               'correct' => false],
                    ['text' => 'An automatic procedure that runs when a table is modified (similar to a trigger)',                      'correct' => false],
                    ['text' => 'A scheduled job that runs SQL at a fixed time interval',                                               'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a trigger in MySQL?',
                'explanation' => 'A trigger is a stored program that automatically executes in response to a specific event (INSERT, UPDATE, DELETE) on a table. Triggers run BEFORE or AFTER the event and are used for auditing, enforcing business rules, and cascading changes.',
                'options'     => [
                    ['text' => 'A stored program that automatically runs before or after INSERT, UPDATE, or DELETE events on a table', 'correct' => true],
                    ['text' => 'A scheduled event that runs SQL at a specified time interval',                                        'correct' => false],
                    ['text' => 'An alert sent to the application when a specific column value changes',                               'correct' => false],
                    ['text' => 'A special index that fires when a query matches a specific pattern',                                  'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between a clustered index and a secondary index in MySQL InnoDB?',
                'explanation' => 'In InnoDB, the clustered index IS the table — rows are physically stored in primary key order. Every table has exactly one clustered index. Secondary indexes store the indexed columns plus the primary key value as a pointer to find the full row.',
                'options'     => [
                    ['text' => 'The clustered index physically orders rows by primary key; secondary indexes store column values plus a primary key pointer to locate the row', 'correct' => true],
                    ['text' => 'Clustered indexes are faster for writes; secondary indexes are faster for reads in all cases',                                                 'correct' => false],
                    ['text' => 'A clustered index can be on any column; a secondary index can only be on the primary key',                                                    'correct' => false],
                    ['text' => 'They are identical in InnoDB; the distinction only applies to MyISAM',                                                                        'correct' => false],
                ],
            ],
            [
                'question'    => 'What is table partitioning in MySQL?',
                'explanation' => 'Partitioning divides a large table into smaller physical pieces (partitions) based on a partitioning key. MySQL supports RANGE, LIST, HASH, and KEY partitioning. It improves query performance via partition pruning and simplifies maintenance like archiving old data.',
                'options'     => [
                    ['text' => 'Dividing a large table into smaller physical pieces by a key; enables partition pruning, faster queries, and easier data management', 'correct' => true],
                    ['text' => 'Splitting a table across multiple databases to enable horizontal scaling',                                                           'correct' => false],
                    ['text' => 'Creating a redundant copy of a table on a separate disk',                                                                           'correct' => false],
                    ['text' => 'Normalizing a table into multiple smaller tables to remove redundancy',                                                             'correct' => false],
                ],
            ],
            [
                'question'    => 'What are window functions in MySQL (available since MySQL 8.0)?',
                'explanation' => 'Window functions (e.g., ROW_NUMBER(), RANK(), LEAD(), LAG(), SUM() OVER()) compute values across a set of rows related to the current row without collapsing them into a group. They are defined with the OVER() clause and are essential for analytic queries.',
                'options'     => [
                    ['text' => 'Functions that compute values across related rows using OVER() without collapsing results (e.g., ROW_NUMBER, RANK, LAG)', 'correct' => true],
                    ['text' => 'Functions that open a connection window and stream results row by row',                                                    'correct' => false],
                    ['text' => 'Aggregate functions that work only on indexed columns',                                                                   'correct' => false],
                    ['text' => 'Built-in functions for string manipulation on VARCHAR columns',                                                           'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the N+1 query problem in MySQL and how do you solve it?',
                'explanation' => 'The N+1 problem occurs when code runs 1 query to fetch N rows, then N additional queries to fetch related data for each row (1+N total). The solution is eager loading: fetch all related data in a single JOIN or a single IN query.',
                'options'     => [
                    ['text' => 'Running 1 query for a list then N separate queries per row; solved by eager loading with JOIN or a batched IN query', 'correct' => true],
                    ['text' => 'Running N queries in parallel which overloads the DB; solved by connection pooling',                                  'correct' => false],
                    ['text' => 'A limit where a table cannot have more than N+1 indexes; solved by dropping redundant indexes',                      'correct' => false],
                    ['text' => 'A replication lag where replicas are N+1 transactions behind; solved by synchronous replication',                   'correct' => false],
                ],
            ],
            [
                'question'    => 'Which MySQL syntax forces the query optimizer to use a specific index?',
                'explanation' => 'USE INDEX(index_name) hints the optimizer to prefer the specified index. FORCE INDEX(index_name) instructs MySQL to use it even if the optimizer would otherwise choose a different path. Example: SELECT * FROM orders USE INDEX(idx_status) WHERE status = \'pending\'.',
                'options'     => [
                    ['text' => 'USE INDEX(index_name) or FORCE INDEX(index_name) in the FROM clause', 'correct' => true],
                    ['text' => 'HINT INDEX(index_name) before the WHERE clause',                      'correct' => false],
                    ['text' => 'SET optimizer_index = \'index_name\' before the query',               'correct' => false],
                    ['text' => 'INDEX OVERRIDE(index_name) after the table name',                     'correct' => false],
                ],
            ],
            [
                'question'    => 'What is connection pooling in MySQL and why is it important in production?',
                'explanation' => 'Connection pooling maintains a pool of pre-opened database connections reused across requests. Establishing a new MySQL connection costs ~10–100ms. Pooling eliminates that overhead, limits concurrent connections, and prevents connection exhaustion under high load.',
                'options'     => [
                    ['text' => 'Reusing pre-opened database connections across requests to eliminate setup overhead and limit concurrency', 'correct' => true],
                    ['text' => 'Caching frequently executed queries in memory to avoid hitting the database',                              'correct' => false],
                    ['text' => 'Distributing queries across multiple MySQL replicas to balance read load',                                 'correct' => false],
                    ['text' => 'Grouping multiple INSERT statements into a single batch to reduce disk I/O',                              'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a covering index in MySQL?',
                'explanation' => 'A covering index includes all columns needed by a query, so MySQL can satisfy the query entirely from the index without touching the actual table rows. This eliminates the extra lookup (key lookup) and is significantly faster. EXPLAIN shows "Using index" when a covering index is used.',
                'options'     => [
                    ['text' => 'An index that contains all columns needed by a query, allowing MySQL to answer it from the index alone without reading table rows', 'correct' => true],
                    ['text' => 'An index that spans all columns of a table, used to back up the entire row',                                                        'correct' => false],
                    ['text' => 'A composite index that automatically covers foreign key columns',                                                                    'correct' => false],
                    ['text' => 'An index that protects columns from being updated by concurrent transactions',                                                       'correct' => false],
                ],
            ],
            [
                'question'    => 'What is MVCC (Multi-Version Concurrency Control) in MySQL InnoDB?',
                'explanation' => 'MVCC allows readers to see a consistent snapshot of data at the start of their transaction without blocking writers, and writers do not block readers. InnoDB achieves this by storing multiple versions of rows (via undo logs). This enables high concurrency without read-write conflicts.',
                'options'     => [
                    ['text' => 'Storing multiple row versions so readers see a consistent snapshot without blocking writers, enabling high concurrency', 'correct' => true],
                    ['text' => 'A locking strategy where multiple transactions share a read lock simultaneously',                                        'correct' => false],
                    ['text' => 'A replication technique that maintains multiple copies of data across servers',                                          'correct' => false],
                    ['text' => 'A compression method that stores multiple column values in a single page version',                                       'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the InnoDB buffer pool and why is it critical for performance?',
                'explanation' => 'The InnoDB buffer pool is the main memory cache for InnoDB — it caches table data pages and index pages. Reads are served from RAM instead of disk when data is in the buffer pool. A large buffer pool (typically 70-80% of available RAM) dramatically reduces I/O. Monitored via innodb_buffer_pool_size.',
                'options'     => [
                    ['text' => 'The main memory cache for InnoDB data and index pages; larger buffer pool means more reads served from RAM instead of disk', 'correct' => true],
                    ['text' => 'A memory area that caches the results of recently executed SELECT queries',                                                   'correct' => false],
                    ['text' => 'A buffer that batches write operations and flushes them to disk periodically',                                               'correct' => false],
                    ['text' => 'A connection pool that keeps idle database connections ready for reuse',                                                     'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the MySQL binary log (binlog) and what is it used for?',
                'explanation' => 'The binary log records all changes to the database (DDL and DML) in a binary format. It is used for: (1) replication — replicas read the binlog to replay changes, and (2) point-in-time recovery — restoring a database to a specific moment by replaying events from a backup.',
                'options'     => [
                    ['text' => 'A log of all database changes used for replication and point-in-time recovery', 'correct' => true],
                    ['text' => 'A log of all SELECT queries for auditing and performance analysis',              'correct' => false],
                    ['text' => 'A crash recovery log that records uncommitted transaction data',                 'correct' => false],
                    ['text' => 'A log of all connection events including logins and authentication attempts',    'correct' => false],
                ],
            ],
            [
                'question'    => 'What is database sharding and when would you use it in MySQL?',
                'explanation' => 'Sharding partitions data across multiple database servers (shards), each holding a subset of the rows. Used when a single server cannot handle the data volume or write throughput. Example: user IDs 1-1M on shard 1, 1M-2M on shard 2. Sharding adds application complexity and makes cross-shard joins difficult.',
                'options'     => [
                    ['text' => 'Distributing rows across multiple database servers to scale beyond a single server\'s limits; used for extreme write/storage scale', 'correct' => true],
                    ['text' => 'Splitting a single table into multiple tables on the same server to reduce locking',                                                 'correct' => false],
                    ['text' => 'Replicating all data across multiple servers for redundancy and read scaling',                                                       'correct' => false],
                    ['text' => 'Partitioning columns of a wide table across separate tables for faster column access',                                               'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the slow query log in MySQL and how do you use it?',
                'explanation' => 'The slow query log records queries that take longer than long_query_time (default 10 seconds). Enable it with slow_query_log = ON. Use tools like mysqldumpslow or pt-query-digest to analyze patterns. It is the primary tool for identifying performance bottlenecks in production.',
                'options'     => [
                    ['text' => 'A log of queries exceeding long_query_time threshold, used to identify slow queries for optimization', 'correct' => true],
                    ['text' => 'A log that records queries that return 0 rows, helping find missing index issues',                     'correct' => false],
                    ['text' => 'A log of all queries in sequence for debugging application behavior',                                  'correct' => false],
                    ['text' => 'A log that records deadlocks and lock timeouts for concurrency debugging',                            'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a full-text index in MySQL and when should you use it?',
                'explanation' => 'A full-text index enables natural language search on TEXT/VARCHAR columns using MATCH() AGAINST() syntax. Unlike LIKE \'%word%\' (which cannot use indexes), full-text search uses an inverted index for efficient word lookups. Use it for search features on large text columns.',
                'options'     => [
                    ['text' => 'An inverted index on text columns enabling efficient word search via MATCH() AGAINST(), unlike slow LIKE \'%word%\'', 'correct' => true],
                    ['text' => 'An index that covers every column in a table for complete row retrieval speed',                                        'correct' => false],
                    ['text' => 'An index that performs a full scan of the table and caches the result',                                               'correct' => false],
                    ['text' => 'An index applied to BLOB columns to index binary content',                                                            'correct' => false],
                ],
            ],
            [
                'question'    => 'What is optimistic locking versus pessimistic locking in MySQL?',
                'explanation' => 'Pessimistic locking locks the row immediately when read (SELECT FOR UPDATE) to prevent concurrent modification. Optimistic locking reads without locking, then checks at update time if the data was changed (often via a version column). Optimistic is better for low-conflict scenarios; pessimistic for high-conflict.',
                'options'     => [
                    ['text' => 'Pessimistic locks on read (SELECT FOR UPDATE); optimistic reads freely and validates before writing (version check)', 'correct' => true],
                    ['text' => 'Optimistic locking uses table locks; pessimistic locking uses row-level locks',                                      'correct' => false],
                    ['text' => 'Pessimistic locking is used for SELECT; optimistic locking is used for INSERT and UPDATE',                           'correct' => false],
                    ['text' => 'They are the same concept with different names in MySQL vs other databases',                                         'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a JSON column in MySQL 8 and what are its advantages over storing JSON as TEXT?',
                'explanation' => 'MySQL 8 introduced a native JSON data type. It validates JSON on insert (rejects invalid JSON), stores it in an optimized binary format for faster reads, and supports path expressions (JSON_EXTRACT, ->>) and indexing on generated columns. TEXT stores JSON as-is with no validation or optimized access.',
                'options'     => [
                    ['text' => 'Validates JSON, stores in optimized binary format, and supports path operators; TEXT gives no validation or path access', 'correct' => true],
                    ['text' => 'JSON type is identical to TEXT but adds a maximum size limit of 65KB',                                                    'correct' => false],
                    ['text' => 'JSON type automatically indexes all keys for fast lookups; TEXT requires manual indexing',                                 'correct' => false],
                    ['text' => 'JSON type supports transactions; TEXT does not allow transactional updates',                                              'correct' => false],
                ],
            ],
            [
                'question'    => 'What is index cardinality in MySQL and why does it matter?',
                'explanation' => 'Cardinality is the number of distinct values in an indexed column. High cardinality (many unique values, e.g., user_id) makes indexes highly selective and efficient. Low cardinality (few unique values, e.g., boolean status) makes indexes less effective — the optimizer may skip them and prefer a full scan.',
                'options'     => [
                    ['text' => 'The number of distinct values in an indexed column; high cardinality means the index is highly selective and efficient', 'correct' => true],
                    ['text' => 'The total number of rows in the table that the index covers',                                                            'correct' => false],
                    ['text' => 'The maximum number of indexes allowed on a single table',                                                                'correct' => false],
                    ['text' => 'The size of the index in bytes stored on disk',                                                                         'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the redo log in InnoDB and how does it support crash recovery?',
                'explanation' => 'The InnoDB redo log (WAL — Write-Ahead Log) records every change before it is applied to the data pages. On crash, InnoDB replays the redo log to bring data pages to a consistent committed state. This ensures durability (D in ACID) — committed transactions survive crashes even if buffer pool pages were not yet flushed to disk.',
                'options'     => [
                    ['text' => 'A write-ahead log that records all changes before applying them to pages; replayed on crash to restore committed state (ACID Durability)', 'correct' => true],
                    ['text' => 'A log that records rolled-back transactions for undo operations',                                                                           'correct' => false],
                    ['text' => 'A log that stores the previous version of each row for MVCC snapshot reads',                                                                'correct' => false],
                    ['text' => 'A log that records failed queries for post-crash analysis',                                                                                 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does EXPLAIN ANALYZE do in MySQL 8 (vs plain EXPLAIN)?',
                'explanation' => 'EXPLAIN ANALYZE actually executes the query (unlike plain EXPLAIN which only estimates) and returns actual runtime statistics — real row counts, actual time per step, and loop counts. It is invaluable for finding cases where the optimizer\'s estimates are wrong, leading to poor plan choices.',
                'options'     => [
                    ['text' => 'Executes the query and returns actual runtime statistics (real rows, real time) rather than optimizer estimates', 'correct' => true],
                    ['text' => 'Analyzes all indexes and recommends which to add or drop for the query',                                        'correct' => false],
                    ['text' => 'Runs the query in dry-run mode and estimates the cost without touching data',                                   'correct' => false],
                    ['text' => 'Generates a visual query plan diagram for documentation',                                                       'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between Second Normal Form (2NF) and Third Normal Form (3NF)?',
                'explanation' => '2NF: Every non-key column must depend on the WHOLE primary key (no partial dependency — applies to composite keys). 3NF: Every non-key column must depend ONLY on the primary key, not on other non-key columns (eliminates transitive dependencies).',
                'options'     => [
                    ['text' => '2NF eliminates partial dependencies on composite keys; 3NF eliminates transitive dependencies between non-key columns', 'correct' => true],
                    ['text' => '2NF requires all columns to be atomic; 3NF requires a single-column primary key',                                      'correct' => false],
                    ['text' => '2NF applies to tables with foreign keys; 3NF applies to tables with composite primary keys',                           'correct' => false],
                    ['text' => 'They are identical; 3NF is just the stricter enforcement of 2NF rules',                                               'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the purpose of SELECT FOR UPDATE in MySQL?',
                'explanation' => 'SELECT FOR UPDATE acquires an exclusive row-level lock on selected rows within a transaction. Other transactions cannot modify or lock these rows until the first transaction commits or rolls back. Used for read-then-write patterns where you need to prevent concurrent modification.',
                'options'     => [
                    ['text' => 'Acquires exclusive row locks on selected rows, preventing other transactions from modifying them until the lock is released', 'correct' => true],
                    ['text' => 'Marks selected rows as read-only so they cannot be updated by the current transaction',                                      'correct' => false],
                    ['text' => 'Performs the SELECT and UPDATE in a single atomic operation without a transaction',                                           'correct' => false],
                    ['text' => 'Selects rows that are eligible for update based on a WHERE condition',                                                       'correct' => false],
                ],
            ],
            [
                'question'    => 'What is denormalization in databases and when is it appropriate?',
                'explanation' => 'Denormalization intentionally introduces redundancy by combining tables or duplicating data to reduce JOIN overhead and improve read performance. Appropriate in read-heavy systems, data warehouses, or reporting databases where query speed matters more than storage efficiency or write complexity.',
                'options'     => [
                    ['text' => 'Intentionally adding redundancy to reduce JOIN overhead and improve read performance in read-heavy or analytical systems', 'correct' => true],
                    ['text' => 'Removing all indexes from a table to speed up INSERT and UPDATE operations',                                              'correct' => false],
                    ['text' => 'Converting column names to a non-standard format for legacy system compatibility',                                        'correct' => false],
                    ['text' => 'Dropping foreign key constraints to allow faster bulk data imports',                                                      'correct' => false],
                ],
            ],
            [
                'question'    => 'What is MySQL Group Replication and how does it differ from standard replication?',
                'explanation' => 'Group Replication is a multi-primary replication plugin providing built-in high availability with automatic failover, conflict detection, and distributed recovery. Unlike standard async replication (one primary, passive replicas), Group Replication uses the Paxos protocol for consensus and can operate in single-primary or multi-primary mode.',
                'options'     => [
                    ['text' => 'A built-in HA plugin with automatic failover using Paxos consensus; unlike async replication which has no automatic failover', 'correct' => true],
                    ['text' => 'A feature that groups multiple replication streams to increase throughput to a single replica',                               'correct' => false],
                    ['text' => 'A load-balancing mechanism that groups read queries across multiple replicas automatically',                                  'correct' => false],
                    ['text' => 'A backup strategy where multiple replicas store different groups of tables',                                                  'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the doublewrite buffer in InnoDB and what problem does it solve?',
                'explanation' => 'InnoDB pages are 16KB but OS write units are 4KB. A partial write (torn page) due to a crash mid-write could corrupt data. The doublewrite buffer writes pages to a sequential area first, then to their actual locations. On recovery, InnoDB can restore a good copy from the doublewrite buffer if a page is torn.',
                'options'     => [
                    ['text' => 'Prevents torn page corruption by writing pages sequentially first, allowing recovery of a good copy if a partial write crashes', 'correct' => true],
                    ['text' => 'Doubles write throughput by writing to two disks simultaneously for redundancy',                                                'correct' => false],
                    ['text' => 'Buffers two transactions in memory before flushing to ensure they are committed together',                                      'correct' => false],
                    ['text' => 'A write cache that accumulates changes from two InnoDB log files before applying them',                                        'correct' => false],
                ],
            ],
            [
                'question'    => 'What is an adaptive hash index in InnoDB and how does it work?',
                'explanation' => 'InnoDB automatically builds a hash index in memory for B-tree index values that are accessed frequently. Hash lookups are O(1) versus O(log n) for B-trees. The adaptive hash index is fully automatic — InnoDB builds and discards it based on observed access patterns. It can be disabled with innodb_adaptive_hash_index=OFF.',
                'options'     => [
                    ['text' => 'An automatic in-memory hash index InnoDB builds on frequently accessed B-tree values to speed up lookups from O(log n) to O(1)', 'correct' => true],
                    ['text' => 'A user-defined hash index created with CREATE HASH INDEX syntax for equality lookups',                                             'correct' => false],
                    ['text' => 'An index that adapts its structure based on the column data distribution to improve range scans',                                  'correct' => false],
                    ['text' => 'A type of index that automatically adds columns to itself based on query patterns',                                               'correct' => false],
                ],
            ],
        ];
    }
}
