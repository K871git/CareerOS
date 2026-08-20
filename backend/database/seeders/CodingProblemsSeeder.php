<?php

namespace Database\Seeders;

use App\Models\CodingProblem;
use App\Models\ProblemTestCase;
use Illuminate\Database\Seeder;

class CodingProblemsSeeder extends Seeder
{
    public function run(): void
    {
        $problems = [

            /* ── 1. Hello World ──────────────────────── */
            [
                'problem' => [
                    'title'        => 'Hello World',
                    'slug'         => 'hello-world',
                    'difficulty'   => 'easy',
                    'language'     => 'php',
                    'order'        => 1,
                    'description'  => "Write a PHP program that outputs exactly:\n\n```\nHello, World!\n```\n\nThis is your first PHP challenge. Use `echo` to print the text.",
                    'constraints'  => "- Output must match exactly (case-sensitive)\n- No extra spaces or blank lines",
                    'starter_code' => "<?php\n\n// Write your code below\n",
                    'solution_code' => "<?php\necho 'Hello, World!';\n",
                ],
                'test_cases' => [
                    ['input' => '', 'expected_output' => 'Hello, World!', 'is_hidden' => false, 'order' => 1, 'label' => 'Example 1'],
                    ['input' => '', 'expected_output' => 'Hello, World!', 'is_hidden' => true,  'order' => 2, 'label' => 'Hidden 1'],
                ],
            ],

            /* ── 2. Sum of Two Numbers ───────────────── */
            [
                'problem' => [
                    'title'        => 'Sum of Two Numbers',
                    'slug'         => 'sum-of-two-numbers',
                    'difficulty'   => 'easy',
                    'language'     => 'php',
                    'order'        => 2,
                    'description'  => "Read two space-separated integers from stdin. Print their sum.\n\n**Input:** A single line with two integers separated by a space.\n\n**Output:** A single integer — the sum.\n\n**Example:**\n```\nInput:  3 5\nOutput: 8\n```",
                    'constraints'  => "- -10,000 ≤ each number ≤ 10,000\n- Input is always two valid integers",
                    'starter_code' => "<?php\n\n\$line = trim(fgets(STDIN));\n// Parse and compute the sum\n",
                    'solution_code' => "<?php\n\$parts = explode(' ', trim(fgets(STDIN)));\necho (int)\$parts[0] + (int)\$parts[1];\n",
                ],
                'test_cases' => [
                    ['input' => '3 5',    'expected_output' => '8',   'is_hidden' => false, 'order' => 1, 'label' => 'Example 1'],
                    ['input' => '10 20',  'expected_output' => '30',  'is_hidden' => false, 'order' => 2, 'label' => 'Example 2'],
                    ['input' => '-5 15',  'expected_output' => '10',  'is_hidden' => true,  'order' => 3, 'label' => 'Hidden 1'],
                    ['input' => '0 0',    'expected_output' => '0',   'is_hidden' => true,  'order' => 4, 'label' => 'Hidden 2'],
                ],
            ],

            /* ── 3. FizzBuzz ─────────────────────────── */
            [
                'problem' => [
                    'title'        => 'FizzBuzz',
                    'slug'         => 'fizzbuzz',
                    'difficulty'   => 'easy',
                    'language'     => 'php',
                    'order'        => 3,
                    'description'  => "Read an integer **N** from stdin. Print numbers from **1 to N**, one per line, with these rules:\n\n- Multiples of **3** → print `Fizz`\n- Multiples of **5** → print `Buzz`\n- Multiples of **both 3 and 5** → print `FizzBuzz`\n- All others → print the number\n\n**Example (N=5):**\n```\n1\n2\nFizz\n4\nBuzz\n```",
                    'constraints'  => "- 1 ≤ N ≤ 100",
                    'starter_code' => "<?php\n\n\$n = (int) trim(fgets(STDIN));\n// Write FizzBuzz logic here\n",
                    'solution_code' => "<?php\n\$n = (int) trim(fgets(STDIN));\nfor (\$i = 1; \$i <= \$n; \$i++) {\n    if (\$i % 15 === 0) echo 'FizzBuzz';\n    elseif (\$i % 3 === 0) echo 'Fizz';\n    elseif (\$i % 5 === 0) echo 'Buzz';\n    else echo \$i;\n    if (\$i < \$n) echo \"\\n\";\n}\n",
                ],
                'test_cases' => [
                    [
                        'input'           => '15',
                        'expected_output' => "1\n2\nFizz\n4\nBuzz\nFizz\n7\n8\nFizz\nBuzz\n11\nFizz\n13\n14\nFizzBuzz",
                        'is_hidden'       => false,
                        'order'           => 1,
                        'label'           => 'Example 1',
                    ],
                    [
                        'input'           => '5',
                        'expected_output' => "1\n2\nFizz\n4\nBuzz",
                        'is_hidden'       => true,
                        'order'           => 2,
                        'label'           => 'Hidden 1',
                    ],
                    [
                        'input'           => '20',
                        'expected_output' => "1\n2\nFizz\n4\nBuzz\nFizz\n7\n8\nFizz\nBuzz\n11\nFizz\n13\n14\nFizzBuzz\n16\n17\nFizz\n19\nBuzz",
                        'is_hidden'       => true,
                        'order'           => 3,
                        'label'           => 'Hidden 2',
                    ],
                ],
            ],

            /* ── 4. Reverse a String ─────────────────── */
            [
                'problem' => [
                    'title'        => 'Reverse a String',
                    'slug'         => 'reverse-a-string',
                    'difficulty'   => 'easy',
                    'language'     => 'php',
                    'order'        => 4,
                    'description'  => "Read a string from stdin. Print the string reversed.\n\n**Example:**\n```\nInput:  hello\nOutput: olleh\n```\n\n**Hint:** PHP has a built-in function `strrev()` — but try to implement it yourself!",
                    'constraints'  => "- Input length: 1 to 200 characters\n- Input may include letters, digits, and spaces",
                    'starter_code' => "<?php\n\n\$str = trim(fgets(STDIN));\n// Print the reversed string\n",
                    'solution_code' => "<?php\n\$str = trim(fgets(STDIN));\necho strrev(\$str);\n",
                ],
                'test_cases' => [
                    ['input' => 'hello',   'expected_output' => 'olleh',   'is_hidden' => false, 'order' => 1, 'label' => 'Example 1'],
                    ['input' => 'Laravel', 'expected_output' => 'levaraL', 'is_hidden' => false, 'order' => 2, 'label' => 'Example 2'],
                    ['input' => '12345',   'expected_output' => '54321',   'is_hidden' => true,  'order' => 3, 'label' => 'Hidden 1'],
                    ['input' => 'racecar', 'expected_output' => 'racecar', 'is_hidden' => true,  'order' => 4, 'label' => 'Hidden 2'],
                ],
            ],

            /* ── 5. Count Vowels ─────────────────────── */
            [
                'problem' => [
                    'title'        => 'Count Vowels',
                    'slug'         => 'count-vowels',
                    'difficulty'   => 'easy',
                    'language'     => 'php',
                    'order'        => 5,
                    'description'  => "Read a string from stdin. Count and print the number of **vowels** (a, e, i, o, u — case insensitive).\n\n**Example:**\n```\nInput:  Hello World\nOutput: 3\n```\n\n*('e', 'o', 'o' are the vowels in \"Hello World\")*",
                    'constraints'  => "- Input length: 1 to 200 characters\n- Count vowels case-insensitively",
                    'starter_code' => "<?php\n\n\$str = trim(fgets(STDIN));\n// Count vowels and print the count\n",
                    'solution_code' => "<?php\n\$str = trim(fgets(STDIN));\necho preg_match_all('/[aeiou]/i', \$str);\n",
                ],
                'test_cases' => [
                    ['input' => 'Hello World',        'expected_output' => '3', 'is_hidden' => false, 'order' => 1, 'label' => 'Example 1'],
                    ['input' => 'PHP',                'expected_output' => '0', 'is_hidden' => false, 'order' => 2, 'label' => 'Example 2'],
                    ['input' => 'aeiou',              'expected_output' => '5', 'is_hidden' => true,  'order' => 3, 'label' => 'Hidden 1'],
                    ['input' => 'The quick brown fox', 'expected_output' => '5', 'is_hidden' => true,  'order' => 4, 'label' => 'Hidden 2'],
                ],
            ],

        ];

        foreach ($problems as $entry) {
            $problem = CodingProblem::updateOrCreate(
                ['slug' => $entry['problem']['slug']],
                $entry['problem']
            );

            // Only seed test cases if they don't exist yet
            if ($problem->testCases()->count() === 0) {
                foreach ($entry['test_cases'] as $tc) {
                    ProblemTestCase::create(array_merge($tc, ['problem_id' => $problem->id]));
                }
            }
        }
    }
}
