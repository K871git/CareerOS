<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Topic;
use Illuminate\Database\Seeder;

class PhpJuniorQuestionsSeeder extends Seeder
{
    public function run(): void
    {
        $topic = Topic::where('slug', 'php-basics-junior')->firstOrFail();

        foreach ($this->questions() as $qData) {
            $exists = Question::where('topic_id', $topic->id)
                ->where('question', $qData['question'])
                ->exists();
            if ($exists) continue;

            $q = Question::create([
                'topic_id'    => $topic->id,
                'type'        => 'MCQ',
                'difficulty'  => 'Easy',
                'question'    => $qData['question'],
                'explanation' => $qData['explanation'],
            ]);

            foreach ($qData['options'] as $opt) {
                QuestionOption::create([
                    'question_id' => $q->id,
                    'option_text' => $opt['text'],
                    'is_correct'  => $opt['correct'],
                ]);
            }
        }

        $count = Question::where('topic_id', $topic->id)->count();
        $this->command->info("PHP Junior: {$count} questions total.");
    }

    private function questions(): array
    {
        return [
            // ── Variables & Data Types ──────────────────────────────────
            [
                'question'    => 'Which of the following is a valid PHP variable name?',
                'explanation' => 'PHP variable names must start with a letter or underscore, followed by letters, numbers, or underscores. They cannot start with a number.',
                'options'     => [
                    ['text' => '$_userName', 'correct' => true],
                    ['text' => '$1name', 'correct' => false],
                    ['text' => '$user-name', 'correct' => false],
                    ['text' => '$user name', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does PHP stand for?',
                'explanation' => 'PHP originally stood for "Personal Home Page" but now stands for "PHP: Hypertext Preprocessor" — a recursive acronym.',
                'options'     => [
                    ['text' => 'PHP: Hypertext Preprocessor', 'correct' => true],
                    ['text' => 'Personal Home Page', 'correct' => false],
                    ['text' => 'Preprocessed Hypertext Pages', 'correct' => false],
                    ['text' => 'Private Hypertext Processor', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the result of gettype(3.14)?',
                'explanation' => 'gettype() returns the data type of a variable as a string. 3.14 is a floating-point number, so gettype(3.14) returns "double" (PHP uses "double" internally for float values).',
                'options'     => [
                    ['text' => '"double"', 'correct' => true],
                    ['text' => '"float"', 'correct' => false],
                    ['text' => '"decimal"', 'correct' => false],
                    ['text' => '"number"', 'correct' => false],
                ],
            ],
            [
                'question'    => 'PHP variable names are case-sensitive. What is the output of: $Name = "Alice"; echo $name;',
                'explanation' => 'PHP variable names are case-sensitive. $Name and $name are two different variables. Since $name was never defined, this produces a warning and outputs an empty string (or null in strict mode).',
                'options'     => [
                    ['text' => 'A notice/warning and empty output', 'correct' => true],
                    ['text' => '"Alice"', 'correct' => false],
                    ['text' => 'Fatal error', 'correct' => false],
                    ['text' => '"null"', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which function checks if a variable is set and is not NULL?',
                'explanation' => 'isset() returns true if a variable exists and its value is not NULL. It returns false if the variable has not been set or has been explicitly set to NULL.',
                'options'     => [
                    ['text' => 'isset()', 'correct' => true],
                    ['text' => 'defined()', 'correct' => false],
                    ['text' => 'exists()', 'correct' => false],
                    ['text' => 'is_set()', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the value of $x after: $x = 5; $x += 3;',
                'explanation' => '$x += 3 is shorthand for $x = $x + 3. Starting from 5, adding 3 gives 8.',
                'options'     => [
                    ['text' => '8', 'correct' => true],
                    ['text' => '53', 'correct' => false],
                    ['text' => '15', 'correct' => false],
                    ['text' => '5', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What type does PHP assign to: $x = true;',
                'explanation' => 'true is a boolean literal. PHP assigns the boolean type to variables set to true or false.',
                'options'     => [
                    ['text' => 'boolean', 'correct' => true],
                    ['text' => 'integer', 'correct' => false],
                    ['text' => 'string', 'correct' => false],
                    ['text' => 'null', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the output of: var_dump(NULL);',
                'explanation' => 'var_dump() outputs type and value information. For NULL it outputs "NULL" (the type is null with no value).',
                'options'     => [
                    ['text' => 'NULL', 'correct' => true],
                    ['text' => 'null', 'correct' => false],
                    ['text' => 'bool(false)', 'correct' => false],
                    ['text' => 'int(0)', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you convert a string to an integer in PHP?',
                'explanation' => '(int) is the casting operator in PHP to convert a value to an integer. intval() is the function equivalent. Both are valid, but (int) is more common in modern PHP code.',
                'options'     => [
                    ['text' => '(int) $var or intval($var)', 'correct' => true],
                    ['text' => 'parseInt($var)', 'correct' => false],
                    ['text' => 'toInt($var)', 'correct' => false],
                    ['text' => 'int_cast($var)', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the unset() function do?',
                'explanation' => 'unset() destroys the given variable. After calling unset($var), $var no longer exists and isset($var) returns false.',
                'options'     => [
                    ['text' => 'Destroys a variable and frees its memory', 'correct' => true],
                    ['text' => 'Sets the variable to NULL', 'correct' => false],
                    ['text' => 'Resets the variable to its default type', 'correct' => false],
                    ['text' => 'Removes the variable from the superglobal array', 'correct' => false],
                ],
            ],

            // ── Operators ───────────────────────────────────────────────
            [
                'question'    => 'What is the result of 10 % 3 in PHP?',
                'explanation' => 'The % operator returns the remainder of division. 10 divided by 3 is 3 with a remainder of 1.',
                'options'     => [
                    ['text' => '1', 'correct' => true],
                    ['text' => '3', 'correct' => false],
                    ['text' => '0', 'correct' => false],
                    ['text' => '3.33', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which operator is used for string concatenation in PHP?',
                'explanation' => 'PHP uses the dot (.) operator to concatenate strings. For example: "Hello" . " World" produces "Hello World".',
                'options'     => [
                    ['text' => '.', 'correct' => true],
                    ['text' => '+', 'correct' => false],
                    ['text' => '&', 'correct' => false],
                    ['text' => '~', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the result of: echo 2 ** 3;',
                'explanation' => 'The ** operator is the exponentiation operator (introduced in PHP 5.6). 2 ** 3 means 2 raised to the power of 3, which equals 8.',
                'options'     => [
                    ['text' => '8', 'correct' => true],
                    ['text' => '6', 'correct' => false],
                    ['text' => '23', 'correct' => false],
                    ['text' => '5', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the null coalescing operator ?? do?',
                'explanation' => 'The null coalescing operator ?? returns its left operand if it exists and is not NULL; otherwise returns the right operand. It replaces the pattern isset($x) ? $x : $default.',
                'options'     => [
                    ['text' => 'Returns left operand if it exists and is not null, otherwise returns right', 'correct' => true],
                    ['text' => 'Checks if a variable equals null', 'correct' => false],
                    ['text' => 'Sets a variable to null', 'correct' => false],
                    ['text' => 'Combines two nullable types', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the result of: var_dump(0 == "foo");',
                'explanation' => 'In PHP 8, comparing 0 == "foo" returns false because PHP 8 changed the behavior: a non-numeric string compared with == to an integer now casts the integer to string, not the string to integer. In PHP 7 this returned true.',
                'options'     => [
                    ['text' => 'bool(false) in PHP 8', 'correct' => true],
                    ['text' => 'bool(true) always', 'correct' => false],
                    ['text' => 'bool(false) in PHP 7', 'correct' => false],
                    ['text' => 'Error', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the spaceship operator <=> return?',
                'explanation' => 'The spaceship operator <=> returns -1 if the left is less than right, 0 if equal, and 1 if left is greater than right. It is used primarily for sorting callbacks.',
                'options'     => [
                    ['text' => '-1, 0, or 1 depending on comparison result', 'correct' => true],
                    ['text' => 'true or false', 'correct' => false],
                    ['text' => 'The difference between the two values', 'correct' => false],
                    ['text' => 'Always returns 0', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the value of $x after: $x = 10; $x--;',
                'explanation' => 'The post-decrement operator $x-- decrements the value of $x by 1 after returning the current value. After this operation, $x equals 9.',
                'options'     => [
                    ['text' => '9', 'correct' => true],
                    ['text' => '10', 'correct' => false],
                    ['text' => '11', 'correct' => false],
                    ['text' => '-10', 'correct' => false],
                ],
            ],

            // ── Control Flow ────────────────────────────────────────────
            [
                'question'    => 'Which statement is used to exit a loop immediately in PHP?',
                'explanation' => 'The break statement immediately terminates the innermost loop or switch statement. continue skips the rest of the current iteration and moves to the next.',
                'options'     => [
                    ['text' => 'break', 'correct' => true],
                    ['text' => 'exit', 'correct' => false],
                    ['text' => 'stop', 'correct' => false],
                    ['text' => 'end', 'correct' => false],
                ],
            ],
            [
                'question'    => 'In a switch statement, what happens if there is no break?',
                'explanation' => 'Without a break, execution "falls through" to the next case block, regardless of whether it matches. This is intentional behavior sometimes used to group cases together.',
                'options'     => [
                    ['text' => 'Execution falls through to the next case', 'correct' => true],
                    ['text' => 'The switch exits automatically', 'correct' => false],
                    ['text' => 'A fatal error occurs', 'correct' => false],
                    ['text' => 'Only the first matching case runs', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the ternary operator syntax in PHP?',
                'explanation' => 'The ternary operator has the form: condition ? value_if_true : value_if_false. It is a shorthand for if-else and returns a value.',
                'options'     => [
                    ['text' => 'condition ? true_value : false_value', 'correct' => true],
                    ['text' => 'if(condition) { } else { }', 'correct' => false],
                    ['text' => 'condition ?? true_value : false_value', 'correct' => false],
                    ['text' => 'condition -> true_value || false_value', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does continue 2; do inside a nested loop?',
                'explanation' => 'continue with a numeric argument skips to the next iteration of the loop that many levels up. continue 2; skips to the next iteration of the second outer loop.',
                'options'     => [
                    ['text' => 'Skips to the next iteration of the outer loop', 'correct' => true],
                    ['text' => 'Continues the loop twice', 'correct' => false],
                    ['text' => 'Same as continue; with no argument', 'correct' => false],
                    ['text' => 'Breaks out of two loops', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the output of: if (0) { echo "yes"; } else { echo "no"; }',
                'explanation' => '0 is falsy in PHP (as is 0.0, "", "0", null, false, and []). The condition evaluates to false, so "no" is output.',
                'options'     => [
                    ['text' => 'no', 'correct' => true],
                    ['text' => 'yes', 'correct' => false],
                    ['text' => 'Error', 'correct' => false],
                    ['text' => '0', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which of these values is considered falsy in PHP?',
                'explanation' => 'In PHP, the following are falsy: false, 0, 0.0, "0", "", null, and []. The string "false" is truthy because it is a non-empty, non-"0" string.',
                'options'     => [
                    ['text' => '"0"', 'correct' => true],
                    ['text' => '"false"', 'correct' => false],
                    ['text' => '-1', 'correct' => false],
                    ['text' => '"null"', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the correct syntax for a PHP elseif?',
                'explanation' => 'In PHP you can write either elseif (one word) or else if (two words) — both are valid. elseif is the single-keyword version used in the standard if/elseif/else chain.',
                'options'     => [
                    ['text' => 'elseif or else if — both work', 'correct' => true],
                    ['text' => 'elif', 'correct' => false],
                    ['text' => 'else-if', 'correct' => false],
                    ['text' => 'else_if', 'correct' => false],
                ],
            ],

            // ── Loops ───────────────────────────────────────────────────
            [
                'question'    => 'What is the difference between while and do-while loops?',
                'explanation' => 'A while loop checks the condition before executing the body — if the condition is false initially, the body never runs. A do-while loop executes the body first, then checks the condition — the body always runs at least once.',
                'options'     => [
                    ['text' => 'do-while runs at least once; while may not run at all', 'correct' => true],
                    ['text' => 'while runs at least once; do-while may not run at all', 'correct' => false],
                    ['text' => 'They are identical', 'correct' => false],
                    ['text' => 'do-while only works with integer counters', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the output of: for ($i = 0; $i < 3; $i++) { echo $i; }',
                'explanation' => 'The for loop initializes $i at 0, runs while $i < 3, and increments $i by 1 each iteration. It prints 0, 1, 2.',
                'options'     => [
                    ['text' => '012', 'correct' => true],
                    ['text' => '123', 'correct' => false],
                    ['text' => '0123', 'correct' => false],
                    ['text' => '234', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you get the key and value in a foreach loop?',
                'explanation' => 'The foreach syntax "foreach ($array as $key => $value)" provides access to both the key and the value of each element. Without => $value, only the value is available.',
                'options'     => [
                    ['text' => 'foreach ($arr as $key => $value)', 'correct' => true],
                    ['text' => 'foreach ($arr as [$key, $value])', 'correct' => false],
                    ['text' => 'for ($arr as $key : $value)', 'correct' => false],
                    ['text' => 'foreach ($arr => $key => $value)', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What happens when you use break 2; in a switch inside a loop?',
                'explanation' => 'break 2; breaks out of the switch (level 1) AND the enclosing loop (level 2). Without the numeric argument, break only exits the switch.',
                'options'     => [
                    ['text' => 'Breaks out of both the switch and the loop', 'correct' => true],
                    ['text' => 'Breaks the switch only', 'correct' => false],
                    ['text' => 'Breaks the loop only', 'correct' => false],
                    ['text' => 'Causes a parse error', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which loop is best for iterating over an array when you also need the index?',
                'explanation' => 'foreach ($arr as $index => $value) is the most readable and idiomatic way to iterate with both index and value. A for loop also works but requires manual array access.',
                'options'     => [
                    ['text' => 'foreach with key => value syntax', 'correct' => true],
                    ['text' => 'while loop with next()', 'correct' => false],
                    ['text' => 'do-while with each()', 'correct' => false],
                    ['text' => 'for loop with $arr[$i] access', 'correct' => false],
                ],
            ],

            // ── Functions ───────────────────────────────────────────────
            [
                'question'    => 'What keyword is used to return a value from a function?',
                'explanation' => 'The return keyword exits the function and optionally returns a value to the caller. Without return, the function implicitly returns null.',
                'options'     => [
                    ['text' => 'return', 'correct' => true],
                    ['text' => 'echo', 'correct' => false],
                    ['text' => 'output', 'correct' => false],
                    ['text' => 'yield', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the scope of a variable defined inside a function?',
                'explanation' => 'Variables defined inside a function are local to that function. They do not exist outside the function and cannot be accessed in the global scope without using the "global" keyword.',
                'options'     => [
                    ['text' => 'Local — only accessible within the function', 'correct' => true],
                    ['text' => 'Global — accessible everywhere', 'correct' => false],
                    ['text' => 'Static — persists between calls but only within the function', 'correct' => false],
                    ['text' => 'Session — available until the session ends', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you access a global variable inside a function?',
                'explanation' => 'Inside a function, you must use the "global" keyword to access a variable from the global scope: global $varName; After this declaration, changes to $varName affect the global variable.',
                'options'     => [
                    ['text' => 'Use the global keyword: global $varName;', 'correct' => true],
                    ['text' => 'Prefix it with GLOBAL::$varName', 'correct' => false],
                    ['text' => 'Variables are automatically available in all scopes', 'correct' => false],
                    ['text' => 'Use $_GLOBAL[\'varName\']', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does a static variable inside a function do?',
                'explanation' => 'A static variable is initialized only once and retains its value between function calls. It is useful for implementing counters or caches within a function without using global variables.',
                'options'     => [
                    ['text' => 'Retains its value between function calls', 'correct' => true],
                    ['text' => 'Makes the variable read-only', 'correct' => false],
                    ['text' => 'Makes the variable global', 'correct' => false],
                    ['text' => 'Converts the variable to a constant', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you pass a variable by reference to a function in PHP?',
                'explanation' => 'To pass by reference, add & before the parameter name in the function definition. Changes to the parameter inside the function will affect the original variable outside.',
                'options'     => [
                    ['text' => 'function add(&$value) { $value += 1; }', 'correct' => true],
                    ['text' => 'function add(ref $value) { $value += 1; }', 'correct' => false],
                    ['text' => 'function add(*$value) { $value += 1; }', 'correct' => false],
                    ['text' => 'function add($value ref) { $value += 1; }', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a variadic function in PHP?',
                'explanation' => 'A variadic function accepts a variable number of arguments. The ... (splat) operator before the last parameter collects all extra arguments into an array.',
                'options'     => [
                    ['text' => 'A function that accepts any number of arguments using ...', 'correct' => true],
                    ['text' => 'A function with no parameters', 'correct' => false],
                    ['text' => 'A function that returns multiple values', 'correct' => false],
                    ['text' => 'A function defined inside another function', 'correct' => false],
                ],
            ],

            // ── Arrays ──────────────────────────────────────────────────
            [
                'question'    => 'What is the difference between array_push() and $arr[] = $value?',
                'explanation' => '$arr[] = $value is faster and preferred for appending a single element. array_push() adds one or more elements but has function call overhead. For multiple elements, array_push() is convenient; for one, $arr[] is idiomatic.',
                'options'     => [
                    ['text' => 'They are functionally similar; $arr[] is faster for single values', 'correct' => true],
                    ['text' => 'array_push() adds to the beginning; $arr[] appends', 'correct' => false],
                    ['text' => 'array_push() works only on associative arrays', 'correct' => false],
                    ['text' => '$arr[] replaces the array; array_push() appends', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does array_keys() return?',
                'explanation' => 'array_keys() returns all the keys of an array as a new indexed array. This works for both numeric and associative arrays.',
                'options'     => [
                    ['text' => 'A new array containing all the keys', 'correct' => true],
                    ['text' => 'A new array containing all the values', 'correct' => false],
                    ['text' => 'The total number of keys', 'correct' => false],
                    ['text' => 'The first key of the array', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does in_array("apple", $fruits) do?',
                'explanation' => 'in_array() checks if a value exists in an array. It returns true if found, false otherwise. By default it uses loose comparison (==); pass true as the third argument for strict comparison (===).',
                'options'     => [
                    ['text' => 'Returns true if "apple" exists in $fruits', 'correct' => true],
                    ['text' => 'Adds "apple" to $fruits', 'correct' => false],
                    ['text' => 'Returns the index of "apple" in $fruits', 'correct' => false],
                    ['text' => 'Removes "apple" from $fruits', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does array_merge() do when two arrays have the same string key?',
                'explanation' => 'When merging associative arrays, if two arrays share the same string key, the value from the later array overwrites the earlier one. For numeric keys, values are appended with re-indexing.',
                'options'     => [
                    ['text' => 'The value from the later array overwrites the earlier one', 'correct' => true],
                    ['text' => 'Both values are kept as a sub-array', 'correct' => false],
                    ['text' => 'A duplicate key error is thrown', 'correct' => false],
                    ['text' => 'The first value is kept and the second discarded', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the output of: echo count([]);',
                'explanation' => 'count() returns the number of elements in an array. An empty array [] has 0 elements, so count([]) returns 0.',
                'options'     => [
                    ['text' => '0', 'correct' => true],
                    ['text' => '1', 'correct' => false],
                    ['text' => 'NULL', 'correct' => false],
                    ['text' => 'false', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which function removes the last element from an array?',
                'explanation' => 'array_pop() removes and returns the last element of an array. array_shift() removes the first element. array_splice() can remove from any position.',
                'options'     => [
                    ['text' => 'array_pop()', 'correct' => true],
                    ['text' => 'array_shift()', 'correct' => false],
                    ['text' => 'array_remove()', 'correct' => false],
                    ['text' => 'array_delete()', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you sort an array in ascending order in PHP?',
                'explanation' => 'sort() sorts an indexed array in ascending order (lowest to highest). It modifies the array in-place and re-indexes it. For associative arrays, use asort() to preserve keys.',
                'options'     => [
                    ['text' => 'sort($array)', 'correct' => true],
                    ['text' => 'asc($array)', 'correct' => false],
                    ['text' => 'array_sort($array)', 'correct' => false],
                    ['text' => 'order($array)', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does array_slice($arr, 1, 3) return?',
                'explanation' => 'array_slice() extracts a portion of an array. array_slice($arr, 1, 3) returns 3 elements starting from index 1 (0-indexed), so it returns elements at indexes 1, 2, and 3.',
                'options'     => [
                    ['text' => 'A new array of 3 elements starting from index 1', 'correct' => true],
                    ['text' => 'A new array from index 1 to index 3 (2 elements)', 'correct' => false],
                    ['text' => 'The original array without the first element', 'correct' => false],
                    ['text' => 'An array of elements 1 and 3 only', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does array_unique() do?',
                'explanation' => 'array_unique() removes duplicate values from an array, keeping the first occurrence. The resulting array preserves the original keys, which may leave gaps in numeric arrays.',
                'options'     => [
                    ['text' => 'Removes duplicate values, keeping first occurrences', 'correct' => true],
                    ['text' => 'Removes duplicate keys', 'correct' => false],
                    ['text' => 'Sorts and removes duplicates', 'correct' => false],
                    ['text' => 'Returns true if all values are unique', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you check if a key exists in an associative array?',
                'explanation' => 'array_key_exists($key, $array) returns true if the specified key is in the array. isset($array[$key]) also works but returns false for null values, while array_key_exists returns true.',
                'options'     => [
                    ['text' => 'array_key_exists($key, $array)', 'correct' => true],
                    ['text' => 'in_key($key, $array)', 'correct' => false],
                    ['text' => 'key_exists($key, $array)', 'correct' => false],
                    ['text' => 'has_key($array, $key)', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does implode(",", $array) do?',
                'explanation' => 'implode() joins array elements into a string using the specified separator. implode(",", ["a","b","c"]) returns "a,b,c". The alias join() does the same thing.',
                'options'     => [
                    ['text' => 'Joins array elements into a string with comma separator', 'correct' => true],
                    ['text' => 'Splits a string into an array by commas', 'correct' => false],
                    ['text' => 'Removes commas from an array', 'correct' => false],
                    ['text' => 'Counts comma-separated values', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does explode(",", "a,b,c") return?',
                'explanation' => 'explode() splits a string by a delimiter and returns an array. explode(",", "a,b,c") returns ["a", "b", "c"]. The first argument is the delimiter, the second is the string.',
                'options'     => [
                    ['text' => '["a", "b", "c"]', 'correct' => true],
                    ['text' => '"a,b,c"', 'correct' => false],
                    ['text' => '3', 'correct' => false],
                    ['text' => '["a,b,c"]', 'correct' => false],
                ],
            ],

            // ── Strings ─────────────────────────────────────────────────
            [
                'question'    => 'What is the difference between single-quoted and double-quoted strings in PHP?',
                'explanation' => 'Double-quoted strings parse variables and escape sequences like \n and \t. Single-quoted strings treat everything literally — $var inside single quotes is NOT expanded.',
                'options'     => [
                    ['text' => 'Double quotes parse variables; single quotes are literal', 'correct' => true],
                    ['text' => 'Single quotes parse variables; double quotes are literal', 'correct' => false],
                    ['text' => 'Both parse variables the same way', 'correct' => false],
                    ['text' => 'Neither parses variables', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does str_replace("o", "0", "foo bar") return?',
                'explanation' => 'str_replace() replaces all occurrences of the search string with the replacement. "foo bar" has two "o" characters, so both are replaced with "0", giving "f00 bar".',
                'options'     => [
                    ['text' => '"f00 bar"', 'correct' => true],
                    ['text' => '"f0o bar"', 'correct' => false],
                    ['text' => '"foo 0ar"', 'correct' => false],
                    ['text' => '"f0 bar"', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which function converts a string to all uppercase?',
                'explanation' => 'strtoupper() converts all characters in a string to uppercase. strtolower() converts to lowercase. ucfirst() capitalizes only the first character.',
                'options'     => [
                    ['text' => 'strtoupper()', 'correct' => true],
                    ['text' => 'uppercase()', 'correct' => false],
                    ['text' => 'str_upper()', 'correct' => false],
                    ['text' => 'toUpper()', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does strpos("hello world", "world") return?',
                'explanation' => 'strpos() returns the position (0-indexed) of the first occurrence of the substring. "world" starts at index 6 in "hello world" (h=0,e=1,l=2,l=3,o=4,space=5,w=6).',
                'options'     => [
                    ['text' => '6', 'correct' => true],
                    ['text' => '7', 'correct' => false],
                    ['text' => 'true', 'correct' => false],
                    ['text' => '1', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does trim() do to a string?',
                'explanation' => 'trim() removes whitespace (spaces, tabs, newlines) from both the beginning and end of a string. ltrim() removes from the left only; rtrim() removes from the right only.',
                'options'     => [
                    ['text' => 'Removes whitespace from both ends of a string', 'correct' => true],
                    ['text' => 'Removes all whitespace from the string', 'correct' => false],
                    ['text' => 'Removes only leading whitespace', 'correct' => false],
                    ['text' => 'Removes only trailing whitespace', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does substr("abcdef", 2, 3) return?',
                'explanation' => 'substr($string, $start, $length) returns a portion of a string. Starting at index 2 (0-indexed) with length 3 gives characters c, d, e — so "cde".',
                'options'     => [
                    ['text' => '"cde"', 'correct' => true],
                    ['text' => '"bcd"', 'correct' => false],
                    ['text' => '"def"', 'correct' => false],
                    ['text' => '"cd"', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you check if a string contains a substring in PHP 8?',
                'explanation' => 'PHP 8 introduced str_contains($haystack, $needle) which returns true if the string contains the substring. In older versions, strpos() !== false was the standard way.',
                'options'     => [
                    ['text' => 'str_contains($string, $substring)', 'correct' => true],
                    ['text' => 'string_contains($string, $substring)', 'correct' => false],
                    ['text' => 'in_string($string, $substring)', 'correct' => false],
                    ['text' => 'contains($string, $substring)', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does number_format(1234567.891, 2) return?',
                'explanation' => 'number_format() formats a number with grouped thousands and decimal places. number_format(1234567.891, 2) returns "1,234,567.89" — grouped with commas and 2 decimal places.',
                'options'     => [
                    ['text' => '"1,234,567.89"', 'correct' => true],
                    ['text' => '"1234567.89"', 'correct' => false],
                    ['text' => '"1.234.567,89"', 'correct' => false],
                    ['text' => '"1,234,568"', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does sprintf("%05d", 42) return?',
                'explanation' => 'sprintf() formats a string like printf. %05d means: integer (%d) padded with zeros (0) to a total width of 5. 42 becomes "00042".',
                'options'     => [
                    ['text' => '"00042"', 'correct' => true],
                    ['text' => '"42000"', 'correct' => false],
                    ['text' => '"  042"', 'correct' => false],
                    ['text' => '"42"', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does str_pad("5", 3, "0", STR_PAD_LEFT) return?',
                'explanation' => 'str_pad() pads a string to a given length. STR_PAD_LEFT pads on the left side. Padding "5" to length 3 with "0" on the left gives "005".',
                'options'     => [
                    ['text' => '"005"', 'correct' => true],
                    ['text' => '"500"', 'correct' => false],
                    ['text' => '"050"', 'correct' => false],
                    ['text' => '"  5"', 'correct' => false],
                ],
            ],

            // ── Superglobals & Forms ─────────────────────────────────────
            [
                'question'    => 'Which superglobal contains server and execution environment information?',
                'explanation' => '$_SERVER is a superglobal array containing information like headers, paths, and script locations. It includes keys like REQUEST_METHOD, HTTP_HOST, SCRIPT_FILENAME, etc.',
                'options'     => [
                    ['text' => '$_SERVER', 'correct' => true],
                    ['text' => '$_ENV', 'correct' => false],
                    ['text' => '$_REQUEST', 'correct' => false],
                    ['text' => '$_GLOBALS', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is $_REQUEST?',
                'explanation' => '$_REQUEST is a superglobal that by default contains the contents of $_GET, $_POST, and $_COOKIE. It is generally discouraged since the origin of the data is ambiguous.',
                'options'     => [
                    ['text' => 'A superglobal containing $_GET, $_POST and $_COOKIE data', 'correct' => true],
                    ['text' => 'A superglobal for the current HTTP request headers', 'correct' => false],
                    ['text' => 'A superglobal only for AJAX requests', 'correct' => false],
                    ['text' => 'A superglobal for request body data only', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which PHP function is used to sanitize user input to prevent XSS?',
                'explanation' => 'htmlspecialchars() converts special HTML characters (<, >, &, ", \') to their HTML entity equivalents, preventing the browser from interpreting user input as HTML/JavaScript.',
                'options'     => [
                    ['text' => 'htmlspecialchars()', 'correct' => true],
                    ['text' => 'sanitize()', 'correct' => false],
                    ['text' => 'clean_input()', 'correct' => false],
                    ['text' => 'strip_all()', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does filter_var($email, FILTER_VALIDATE_EMAIL) return if the email is invalid?',
                'explanation' => 'filter_var() with FILTER_VALIDATE_EMAIL returns false if the email address does not match a valid email format, and the validated email string if it is valid.',
                'options'     => [
                    ['text' => 'false', 'correct' => true],
                    ['text' => 'null', 'correct' => false],
                    ['text' => 'An empty string', 'correct' => false],
                    ['text' => 'A validation error object', 'correct' => false],
                ],
            ],

            // ── Constants ────────────────────────────────────────────────
            [
                'question'    => 'How do you define a constant in PHP?',
                'explanation' => 'define("CONSTANT_NAME", value) creates a global constant. Alternatively, the const keyword can be used at class level or in the global scope. Constants do not use the $ prefix and cannot be changed.',
                'options'     => [
                    ['text' => 'define("NAME", value) or const NAME = value;', 'correct' => true],
                    ['text' => '$NAME = const value;', 'correct' => false],
                    ['text' => 'constant NAME = value;', 'correct' => false],
                    ['text' => 'DEFINE $NAME = value;', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the value of PHP_INT_MAX?',
                'explanation' => 'PHP_INT_MAX is a built-in constant that holds the maximum integer value PHP can represent on the current platform. On 64-bit systems this is 9223372036854775807 (2^63 - 1).',
                'options'     => [
                    ['text' => 'The maximum integer value on the current platform', 'correct' => true],
                    ['text' => '65535', 'correct' => false],
                    ['text' => '2147483647 always', 'correct' => false],
                    ['text' => 'Infinity', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between define() and const for constants?',
                'explanation' => 'define() can be used at runtime (inside if blocks, functions). const must be at the top level or in a class, and is evaluated at compile time. const is slightly faster; define() is more flexible.',
                'options'     => [
                    ['text' => 'define() works at runtime; const is compile-time only', 'correct' => true],
                    ['text' => 'const works at runtime; define() is compile-time only', 'correct' => false],
                    ['text' => 'They are identical', 'correct' => false],
                    ['text' => 'define() only works inside functions', 'correct' => false],
                ],
            ],

            // ── Math & Date ─────────────────────────────────────────────
            [
                'question'    => 'What does round(4.5) return in PHP?',
                'explanation' => 'round() rounds a float to the nearest integer. 4.5 rounds up to 5 (PHP uses "round half away from zero" by default).',
                'options'     => [
                    ['text' => '5', 'correct' => true],
                    ['text' => '4', 'correct' => false],
                    ['text' => '4.5', 'correct' => false],
                    ['text' => '5.0', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does abs(-7) return?',
                'explanation' => 'abs() returns the absolute value — the non-negative magnitude of a number. abs(-7) returns 7.',
                'options'     => [
                    ['text' => '7', 'correct' => true],
                    ['text' => '-7', 'correct' => false],
                    ['text' => '0', 'correct' => false],
                    ['text' => 'false', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you generate a random integer between 1 and 100 in PHP?',
                'explanation' => 'rand(1, 100) returns a random integer between 1 and 100 inclusive. mt_rand(1, 100) uses the Mersenne Twister algorithm and is faster and more random. random_int(1, 100) is cryptographically secure.',
                'options'     => [
                    ['text' => 'rand(1, 100) or mt_rand(1, 100)', 'correct' => true],
                    ['text' => 'random(100)', 'correct' => false],
                    ['text' => 'Math.random() * 100', 'correct' => false],
                    ['text' => 'randomInt(1, 100)', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does date("Y") return?',
                'explanation' => 'date() formats a Unix timestamp as a string. "Y" is the format character for the full 4-digit year. date("Y") returns the current year, e.g. "2025".',
                'options'     => [
                    ['text' => 'The current 4-digit year as a string', 'correct' => true],
                    ['text' => 'The current timestamp', 'correct' => false],
                    ['text' => 'The current month number', 'correct' => false],
                    ['text' => 'The letter Y', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does time() return in PHP?',
                'explanation' => 'time() returns the current Unix timestamp — the number of seconds elapsed since January 1, 1970 00:00:00 UTC. It is commonly used for calculations involving dates and times.',
                'options'     => [
                    ['text' => 'Current Unix timestamp in seconds', 'correct' => true],
                    ['text' => 'Current time as a formatted string', 'correct' => false],
                    ['text' => 'Current time in milliseconds', 'correct' => false],
                    ['text' => 'A DateTime object', 'correct' => false],
                ],
            ],

            // ── Type Juggling ────────────────────────────────────────────
            [
                'question'    => 'What is the output of: echo "5 bottles" + 3;',
                'explanation' => 'PHP type-juggles "5 bottles" to the integer 5 (the numeric prefix). 5 + 3 = 8. This is type juggling — PHP automatically converts the string to a number when used in arithmetic.',
                'options'     => [
                    ['text' => '8', 'correct' => true],
                    ['text' => '"5 bottles3"', 'correct' => false],
                    ['text' => 'Error', 'correct' => false],
                    ['text' => '53', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the result of: (bool) "";',
                'explanation' => 'An empty string "" is falsy in PHP. Casting it to bool with (bool) returns false. Only "" and "0" are falsy strings — all other strings (including " ") are truthy.',
                'options'     => [
                    ['text' => 'false', 'correct' => true],
                    ['text' => 'true', 'correct' => false],
                    ['text' => 'null', 'correct' => false],
                    ['text' => '0', 'correct' => false],
                ],
            ],

            // ── Include/Require ─────────────────────────────────────────
            [
                'question'    => 'What is the difference between include and require in PHP?',
                'explanation' => 'Both include and require execute and include a PHP file. If the file is not found, include emits a warning (E_WARNING) and continues execution, while require emits a fatal error (E_COMPILE_ERROR) and stops execution.',
                'options'     => [
                    ['text' => 'require stops execution on failure; include only warns', 'correct' => true],
                    ['text' => 'include stops execution on failure; require only warns', 'correct' => false],
                    ['text' => 'They are completely identical', 'correct' => false],
                    ['text' => 'require works for classes only; include for functions', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does require_once do differently than require?',
                'explanation' => 'require_once checks if the file has already been included/required in the current script. If it has, it does not include it again. This prevents function/class redeclaration errors.',
                'options'     => [
                    ['text' => 'It only includes the file if it has not been included before', 'correct' => true],
                    ['text' => 'It requires the file exactly once per request regardless', 'correct' => false],
                    ['text' => 'It is faster than require', 'correct' => false],
                    ['text' => 'It works asynchronously', 'correct' => false],
                ],
            ],

            // ── Error Handling Basics ────────────────────────────────────
            [
                'question'    => 'What function displays a human-readable version of a variable for debugging?',
                'explanation' => 'print_r() outputs a human-readable representation of a variable — arrays and objects are shown with their structure. var_dump() is similar but also shows data types and lengths.',
                'options'     => [
                    ['text' => 'print_r() or var_dump()', 'correct' => true],
                    ['text' => 'debug()', 'correct' => false],
                    ['text' => 'inspect()', 'correct' => false],
                    ['text' => 'trace()', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does the @ operator do in PHP?',
                'explanation' => 'The @ error suppression operator suppresses error messages for the expression it precedes. It is generally discouraged because it hides real problems and makes debugging harder.',
                'options'     => [
                    ['text' => 'Suppresses error messages for that expression', 'correct' => true],
                    ['text' => 'Marks a variable as a reference', 'correct' => false],
                    ['text' => 'Sends an email with the error', 'correct' => false],
                    ['text' => 'Casts the expression to a string', 'correct' => false],
                ],
            ],

            // ── Miscellaneous ────────────────────────────────────────────
            [
                'question'    => 'What does die() or exit() do in PHP?',
                'explanation' => 'die() and exit() are equivalent. They terminate script execution immediately. An optional message or status code can be passed as an argument.',
                'options'     => [
                    ['text' => 'Terminates the current script immediately', 'correct' => true],
                    ['text' => 'Throws a fatal exception', 'correct' => false],
                    ['text' => 'Exits the current function only', 'correct' => false],
                    ['text' => 'Ends the current loop', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the PHP tag used to output a value directly (short echo tag)?',
                'explanation' => '<?= $var ?> is the short echo tag, equivalent to <?php echo $var; ?>. It is enabled when short_open_tag is on (default in PHP 5.4+). It is widely used in view/template files.',
                'options'     => [
                    ['text' => '<?= $var ?>', 'correct' => true],
                    ['text' => '<? $var ?>', 'correct' => false],
                    ['text' => '<%=$var%>', 'correct' => false],
                    ['text' => '<?output $var?>', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which PHP function checks if a value is a number (int or float)?',
                'explanation' => 'is_numeric() returns true if the variable is a number or a numeric string (like "42" or "3.14"). is_int() checks only for integer type. is_float() checks only for float type.',
                'options'     => [
                    ['text' => 'is_numeric()', 'correct' => true],
                    ['text' => 'is_number()', 'correct' => false],
                    ['text' => 'is_digit()', 'correct' => false],
                    ['text' => 'isnumber()', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the output of: echo intval("42abc");',
                'explanation' => 'intval() converts a value to integer. For "42abc", it reads the leading integer portion "42" and stops at the non-numeric character "a". The result is 42.',
                'options'     => [
                    ['text' => '42', 'correct' => true],
                    ['text' => '0', 'correct' => false],
                    ['text' => 'Error', 'correct' => false],
                    ['text' => '"42abc"', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does list() or the [] syntax do in PHP?',
                'explanation' => 'list() assigns array values to variables in a single operation. In PHP 7.1+, the shorthand [] syntax is preferred. For example: [$a, $b] = [1, 2]; assigns 1 to $a and 2 to $b.',
                'options'     => [
                    ['text' => 'Assigns array elements to individual variables', 'correct' => true],
                    ['text' => 'Creates a new list data structure', 'correct' => false],
                    ['text' => 'Converts an array to a list format', 'correct' => false],
                    ['text' => 'Removes all elements from an array', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does json_encode(["name" => "Alice", "age" => 25]) return?',
                'explanation' => 'json_encode() converts a PHP value to a JSON string. An associative array with string keys becomes a JSON object. The result is: {"name":"Alice","age":25}.',
                'options'     => [
                    ['text' => '\'{"name":"Alice","age":25}\'', 'correct' => true],
                    ['text' => '"name=Alice&age=25"', 'correct' => false],
                    ['text' => '"Alice, 25"', 'correct' => false],
                    ['text' => 'An array object', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does json_decode(\'{"score":10}\', true) return?',
                'explanation' => 'json_decode() parses a JSON string. The second argument true converts JSON objects to PHP associative arrays instead of stdClass objects. So this returns ["score" => 10].',
                'options'     => [
                    ['text' => 'An associative array: ["score" => 10]', 'correct' => true],
                    ['text' => 'A stdClass object with ->score = 10', 'correct' => false],
                    ['text' => 'The string "10"', 'correct' => false],
                    ['text' => 'null', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which of the following correctly uses heredoc syntax in PHP?',
                'explanation' => 'Heredoc starts with <<<IDENTIFIER and ends with IDENTIFIER; on its own line. Variables are interpolated inside it, like double-quoted strings. The closing identifier must be at the start of the line with no indentation (before PHP 7.3).',
                'options'     => [
                    ['text' => '$str = <<<EOT\nHello $name\nEOT;', 'correct' => true],
                    ['text' => '$str = <<<"Hello $name">>>;', 'correct' => false],
                    ['text' => '$str = HERE{ Hello $name }END;', 'correct' => false],
                    ['text' => '$str = `Hello $name`;', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the output of: echo str_repeat("ab", 3);',
                'explanation' => 'str_repeat() returns a string repeated a given number of times. str_repeat("ab", 3) returns "ababab".',
                'options'     => [
                    ['text' => '"ababab"', 'correct' => true],
                    ['text' => '"ab3"', 'correct' => false],
                    ['text' => '"aabbbb"', 'correct' => false],
                    ['text' => '"aaabbb"', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does PHP\'s compact() function do?',
                'explanation' => 'compact() creates an array from variables in the current scope. compact("name", "age") returns ["name" => $name, "age" => $age]. It is the opposite of extract().',
                'options'     => [
                    ['text' => 'Creates an associative array from existing variable names', 'correct' => true],
                    ['text' => 'Compresses a string using gzip', 'correct' => false],
                    ['text' => 'Removes whitespace from a string', 'correct' => false],
                    ['text' => 'Merges multiple arrays into one', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the PHP_EOL constant?',
                'explanation' => 'PHP_EOL is a built-in constant containing the correct end-of-line character for the current platform: \\n on Unix/Linux/macOS, \\r\\n on Windows. Use it instead of hard-coding line endings.',
                'options'     => [
                    ['text' => 'The platform-specific end-of-line character', 'correct' => true],
                    ['text' => 'The PHP end-of-life date', 'correct' => false],
                    ['text' => 'Always \\n regardless of platform', 'correct' => false],
                    ['text' => 'A constant for the closing PHP tag', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you check the PHP version at runtime?',
                'explanation' => 'phpversion() returns the current PHP version as a string (e.g., "8.2.1"). The PHP_VERSION constant also provides the same information. PHP_MAJOR_VERSION gives just the major version number.',
                'options'     => [
                    ['text' => 'phpversion() or PHP_VERSION constant', 'correct' => true],
                    ['text' => 'getphpversion()', 'correct' => false],
                    ['text' => '$_SERVER["PHP_VERSION"]', 'correct' => false],
                    ['text' => 'version_get()', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does array_reverse() do?',
                'explanation' => 'array_reverse() returns a new array with the elements in reverse order. By default, numeric keys are re-indexed; pass true as the second argument to preserve keys.',
                'options'     => [
                    ['text' => 'Returns a new array with elements in reverse order', 'correct' => true],
                    ['text' => 'Reverses the string representation of the array', 'correct' => false],
                    ['text' => 'Modifies the array in-place to be reversed', 'correct' => false],
                    ['text' => 'Returns the last element', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is the difference between echo and print in PHP?',
                'explanation' => 'echo and print both output strings. echo can take multiple comma-separated arguments, has no return value, and is marginally faster. print always returns 1 and takes exactly one argument. Both are language constructs, not functions.',
                'options'     => [
                    ['text' => 'echo accepts multiple arguments; print returns 1 and takes one argument', 'correct' => true],
                    ['text' => 'echo returns 1; print has no return value', 'correct' => false],
                    ['text' => 'print is faster than echo', 'correct' => false],
                    ['text' => 'They are completely identical', 'correct' => false],
                ],
            ],
            [
                'question'    => 'Which of the following correctly declares an indexed array?',
                'explanation' => 'PHP supports two array syntax styles: array() (works in all PHP versions) and [] shorthand (PHP 5.4+). Both create an indexed array when no keys are specified.',
                'options'     => [
                    ['text' => '$a = [1, 2, 3]; or $a = array(1, 2, 3);', 'correct' => true],
                    ['text' => '$a = {1, 2, 3};', 'correct' => false],
                    ['text' => '$a = (1, 2, 3);', 'correct' => false],
                    ['text' => '$a = list(1, 2, 3);', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does array_search("b", ["a","b","c"]) return?',
                'explanation' => 'array_search() searches an array for a value and returns the corresponding key if found, or false if not found. "b" is at index 1, so it returns 1.',
                'options'     => [
                    ['text' => '1', 'correct' => true],
                    ['text' => '"b"', 'correct' => false],
                    ['text' => 'true', 'correct' => false],
                    ['text' => '2', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does strrev("hello") return?',
                'explanation' => 'strrev() reverses a string. "hello" reversed is "olleh".',
                'options'     => [
                    ['text' => '"olleh"', 'correct' => true],
                    ['text' => '"hello"', 'correct' => false],
                    ['text' => '"Helloh"', 'correct' => false],
                    ['text' => '"OLLEH"', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does floor(4.9) return?',
                'explanation' => 'floor() rounds a number down to the nearest integer. floor(4.9) returns 4.0 (a float). ceil() would round up to 5.',
                'options'     => [
                    ['text' => '4', 'correct' => true],
                    ['text' => '5', 'correct' => false],
                    ['text' => '4.9', 'correct' => false],
                    ['text' => '-4', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is a PHP heredoc string useful for?',
                'explanation' => 'Heredoc is useful for writing multi-line strings without escaping double quotes. Variables are interpolated. It is commonly used for multi-line SQL queries or HTML templates in PHP files.',
                'options'     => [
                    ['text' => 'Multi-line strings with variable interpolation without escaping quotes', 'correct' => true],
                    ['text' => 'Importing external text files', 'correct' => false],
                    ['text' => 'Defining read-only string constants', 'correct' => false],
                    ['text' => 'Strings that execute shell commands', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What does max(3, 1, 7, 2) return?',
                'explanation' => 'max() returns the highest value from its arguments (or from an array if an array is passed). Among 3, 1, 7, 2 — the maximum is 7.',
                'options'     => [
                    ['text' => '7', 'correct' => true],
                    ['text' => '3', 'correct' => false],
                    ['text' => '1', 'correct' => false],
                    ['text' => '13', 'correct' => false],
                ],
            ],
            [
                'question'    => 'How do you convert an array to a JSON string in PHP?',
                'explanation' => 'json_encode() converts a PHP value (array, object, scalar) to its JSON string representation. For an indexed array it produces a JSON array; for an associative array, a JSON object.',
                'options'     => [
                    ['text' => 'json_encode($array)', 'correct' => true],
                    ['text' => 'to_json($array)', 'correct' => false],
                    ['text' => 'serialize($array)', 'correct' => false],
                    ['text' => 'array_to_json($array)', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What happens when you use "." to concatenate a string and an integer in PHP?',
                'explanation' => 'PHP auto-converts the integer to a string when concatenating with ".". So "Score: " . 10 produces "Score: 10" without any explicit casting.',
                'options'     => [
                    ['text' => 'The integer is automatically cast to string', 'correct' => true],
                    ['text' => 'A type error is thrown', 'correct' => false],
                    ['text' => 'The result is 0', 'correct' => false],
                    ['text' => 'The integer is ignored', 'correct' => false],
                ],
            ],
            [
                'question'    => 'What is array_fill(0, 5, "x") used for?',
                'explanation' => 'array_fill($start_index, $num, $value) fills an array with a given value. array_fill(0, 5, "x") creates ["x", "x", "x", "x", "x"] — an array of 5 elements all equal to "x" starting at index 0.',
                'options'     => [
                    ['text' => 'Creates an array of 5 elements all set to "x"', 'correct' => true],
                    ['text' => 'Fills position 0 to 5 of an existing array', 'correct' => false],
                    ['text' => 'Creates an associative array from 0 to 5', 'correct' => false],
                    ['text' => 'Fills empty array positions with "x"', 'correct' => false],
                ],
            ],
        ];
    }
}
