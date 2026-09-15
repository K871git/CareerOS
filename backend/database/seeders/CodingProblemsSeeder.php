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

            // ═══════════════════════════════════════════════════════════════
            // EASY  (orders 6-8)  — always unlocked
            // ═══════════════════════════════════════════════════════════════

            /* ── 6. Find Maximum in Array ─────────────── */
            [
                'problem' => [
                    'title'        => 'Find Maximum in Array',
                    'slug'         => 'find-maximum-in-array',
                    'difficulty'   => 'easy',
                    'language'     => 'php',
                    'order'        => 6,
                    'description'  => "Read **N** and then **N** space-separated integers. Print the **maximum** value.\n\n**Input:**\n```\nLine 1: N\nLine 2: N integers separated by spaces\n```\n\n**Example:**\n```\nInput:\n5\n3 1 4 1 5\n\nOutput: 5\n```",
                    'constraints'  => "- 1 ≤ N ≤ 1000\n- -10,000 ≤ each integer ≤ 10,000",
                    'starter_code' => "<?php\n\n\$n    = (int) trim(fgets(STDIN));\n\$nums = array_map('intval', explode(' ', trim(fgets(STDIN))));\n// Find and print the maximum value\n",
                    'solution_code' => "<?php\n\$n    = (int) trim(fgets(STDIN));\n\$nums = array_map('intval', explode(' ', trim(fgets(STDIN))));\necho max(\$nums);\n",
                ],
                'test_cases' => [
                    ['input' => "5\n3 1 4 1 5",            'expected_output' => '5',   'is_hidden' => false, 'order' => 1, 'label' => 'Example 1'],
                    ['input' => "4\n-3 -1 -4 -2",          'expected_output' => '-1',  'is_hidden' => false, 'order' => 2, 'label' => 'Example 2'],
                    ['input' => "1\n42",                    'expected_output' => '42',  'is_hidden' => true,  'order' => 3, 'label' => 'Hidden 1'],
                    ['input' => "6\n100 200 150 300 50 250",'expected_output' => '300', 'is_hidden' => true,  'order' => 4, 'label' => 'Hidden 2'],
                ],
            ],

            /* ── 7. Palindrome Check ─────────────────── */
            [
                'problem' => [
                    'title'        => 'Palindrome Check',
                    'slug'         => 'palindrome-check',
                    'difficulty'   => 'easy',
                    'language'     => 'php',
                    'order'        => 7,
                    'description'  => "Read a string from stdin. Print **Yes** if it is a palindrome, otherwise print **No**.\n\nA palindrome reads the same forwards and backwards.\n\n**Example:**\n```\nInput:  racecar\nOutput: Yes\n\nInput:  hello\nOutput: No\n```",
                    'constraints'  => "- Input length: 1 to 200 characters\n- Check is case-sensitive",
                    'starter_code' => "<?php\n\n\$str = trim(fgets(STDIN));\n// Print Yes if palindrome, No otherwise\n",
                    'solution_code' => "<?php\n\$str = trim(fgets(STDIN));\necho (\$str === strrev(\$str)) ? 'Yes' : 'No';\n",
                ],
                'test_cases' => [
                    ['input' => 'racecar', 'expected_output' => 'Yes', 'is_hidden' => false, 'order' => 1, 'label' => 'Example 1'],
                    ['input' => 'hello',   'expected_output' => 'No',  'is_hidden' => false, 'order' => 2, 'label' => 'Example 2'],
                    ['input' => 'madam',   'expected_output' => 'Yes', 'is_hidden' => true,  'order' => 3, 'label' => 'Hidden 1'],
                    ['input' => 'PHP',     'expected_output' => 'No',  'is_hidden' => true,  'order' => 4, 'label' => 'Hidden 2'],
                ],
            ],

            /* ── 8. Factorial ────────────────────────── */
            [
                'problem' => [
                    'title'        => 'Factorial',
                    'slug'         => 'factorial',
                    'difficulty'   => 'easy',
                    'language'     => 'php',
                    'order'        => 8,
                    'description'  => "Read a non-negative integer **N** from stdin. Print **N!** (N factorial).\n\nFactorial is defined as:\n- 0! = 1\n- N! = N x (N-1) x ... x 1\n\n**Example:**\n```\nInput:  5\nOutput: 120\n\nInput:  0\nOutput: 1\n```",
                    'constraints'  => "- 0 ≤ N ≤ 12\n- Output fits in a 32-bit integer",
                    'starter_code' => "<?php\n\n\$n = (int) trim(fgets(STDIN));\n// Compute and print n!\n",
                    'solution_code' => "<?php\n\$n = (int) trim(fgets(STDIN));\n\$result = 1;\nfor (\$i = 2; \$i <= \$n; \$i++) \$result *= \$i;\necho \$result;\n",
                ],
                'test_cases' => [
                    ['input' => '5',  'expected_output' => '120',     'is_hidden' => false, 'order' => 1, 'label' => 'Example 1'],
                    ['input' => '0',  'expected_output' => '1',       'is_hidden' => false, 'order' => 2, 'label' => 'Example 2'],
                    ['input' => '10', 'expected_output' => '3628800', 'is_hidden' => true,  'order' => 3, 'label' => 'Hidden 1'],
                    ['input' => '7',  'expected_output' => '5040',    'is_hidden' => true,  'order' => 4, 'label' => 'Hidden 2'],
                ],
            ],

            // ═══════════════════════════════════════════════════════════════
            // MEDIUM  (orders 9-12)  — unlocks after 3 easy accepted
            // ═══════════════════════════════════════════════════════════════

            /* ── 9. Fibonacci N Terms ─────────────────── */
            [
                'problem' => [
                    'title'        => 'Fibonacci N Terms',
                    'slug'         => 'fibonacci-n-terms',
                    'difficulty'   => 'medium',
                    'language'     => 'php',
                    'order'        => 9,
                    'description'  => "Read an integer **N** from stdin. Print the **first N Fibonacci numbers** separated by spaces, starting from 0.\n\nThe Fibonacci sequence: 0, 1, 1, 2, 3, 5, 8, 13, 21, ...\n\n**Example:**\n```\nInput:  7\nOutput: 0 1 1 2 3 5 8\n\nInput:  1\nOutput: 0\n```",
                    'constraints'  => "- 1 ≤ N ≤ 20",
                    'starter_code' => "<?php\n\n\$n = (int) trim(fgets(STDIN));\n// Print the first \$n Fibonacci numbers separated by spaces\n",
                    'solution_code' => "<?php\n\$n = (int) trim(fgets(STDIN));\n\$fibs = [0, 1];\nfor (\$i = 2; \$i < \$n; \$i++) \$fibs[] = \$fibs[\$i-1] + \$fibs[\$i-2];\necho implode(' ', array_slice(\$fibs, 0, \$n));\n",
                ],
                'test_cases' => [
                    ['input' => '7',  'expected_output' => '0 1 1 2 3 5 8',           'is_hidden' => false, 'order' => 1, 'label' => 'Example 1'],
                    ['input' => '1',  'expected_output' => '0',                        'is_hidden' => false, 'order' => 2, 'label' => 'Example 2'],
                    ['input' => '10', 'expected_output' => '0 1 1 2 3 5 8 13 21 34',  'is_hidden' => true,  'order' => 3, 'label' => 'Hidden 1'],
                    ['input' => '5',  'expected_output' => '0 1 1 2 3',               'is_hidden' => true,  'order' => 4, 'label' => 'Hidden 2'],
                ],
            ],

            /* ── 10. Second Largest Number ───────────── */
            [
                'problem' => [
                    'title'        => 'Second Largest Number',
                    'slug'         => 'second-largest-number',
                    'difficulty'   => 'medium',
                    'language'     => 'php',
                    'order'        => 10,
                    'description'  => "Read **N** on the first line, then **N distinct integers** on the second line. Print the **second largest** value.\n\n**Example:**\n```\nInput:\n5\n3 1 4 2 5\n\nOutput: 4\n```",
                    'constraints'  => "- 2 ≤ N ≤ 1000\n- All integers are distinct\n- -10,000 ≤ each integer ≤ 10,000",
                    'starter_code' => "<?php\n\n\$n    = (int) trim(fgets(STDIN));\n\$nums = array_map('intval', explode(' ', trim(fgets(STDIN))));\n// Find and print the second largest number\n",
                    'solution_code' => "<?php\n\$n    = (int) trim(fgets(STDIN));\n\$nums = array_map('intval', explode(' ', trim(fgets(STDIN))));\nrsort(\$nums);\necho \$nums[1];\n",
                ],
                'test_cases' => [
                    ['input' => "5\n3 1 4 2 5",  'expected_output' => '4',  'is_hidden' => false, 'order' => 1, 'label' => 'Example 1'],
                    ['input' => "4\n10 20 30 40", 'expected_output' => '30', 'is_hidden' => false, 'order' => 2, 'label' => 'Example 2'],
                    ['input' => "3\n-5 -1 -3",   'expected_output' => '-3', 'is_hidden' => true,  'order' => 3, 'label' => 'Hidden 1'],
                    ['input' => "6\n1 5 3 9 7 2", 'expected_output' => '7',  'is_hidden' => true,  'order' => 4, 'label' => 'Hidden 2'],
                ],
            ],

            /* ── 11. Anagram Check ───────────────────── */
            [
                'problem' => [
                    'title'        => 'Anagram Check',
                    'slug'         => 'anagram-check',
                    'difficulty'   => 'medium',
                    'language'     => 'php',
                    'order'        => 11,
                    'description'  => "Read two strings on separate lines. Print **Yes** if they are anagrams of each other, otherwise print **No**.\n\nTwo strings are anagrams if one can be rearranged to form the other (case-sensitive).\n\n**Example:**\n```\nInput:\nlisten\nsilent\n\nOutput: Yes\n```",
                    'constraints'  => "- Length: 1 to 100 characters each\n- Check is case-sensitive\n- Alphabetic characters only",
                    'starter_code' => "<?php\n\n\$a = trim(fgets(STDIN));\n\$b = trim(fgets(STDIN));\n// Print Yes if anagrams, No otherwise\n",
                    'solution_code' => "<?php\n\$a = trim(fgets(STDIN));\n\$b = trim(fgets(STDIN));\n\$sa = str_split(\$a); sort(\$sa);\n\$sb = str_split(\$b); sort(\$sb);\necho (\$sa === \$sb) ? 'Yes' : 'No';\n",
                ],
                'test_cases' => [
                    ['input' => "listen\nsilent",     'expected_output' => 'Yes', 'is_hidden' => false, 'order' => 1, 'label' => 'Example 1'],
                    ['input' => "hello\nworld",       'expected_output' => 'No',  'is_hidden' => false, 'order' => 2, 'label' => 'Example 2'],
                    ['input' => "triangle\nintegral", 'expected_output' => 'Yes', 'is_hidden' => true,  'order' => 3, 'label' => 'Hidden 1'],
                    ['input' => "PHP\nhpp",           'expected_output' => 'No',  'is_hidden' => true,  'order' => 4, 'label' => 'Hidden 2'],
                ],
            ],

            /* ── 12. Valid Brackets ───────────────────── */
            [
                'problem' => [
                    'title'        => 'Valid Brackets',
                    'slug'         => 'valid-brackets',
                    'difficulty'   => 'medium',
                    'language'     => 'php',
                    'order'        => 12,
                    'description'  => "Read a string containing only bracket characters: `(`, `)`, `{`, `}`, `[`, `]`.\n\nPrint **Yes** if the brackets are balanced and properly nested, otherwise print **No**.\n\n**Example:**\n```\nInput:  ([{}])\nOutput: Yes\n\nInput:  ([)]\nOutput: No\n```\n\n**Hint:** Use a stack (array) — push opening brackets, pop and match on closing brackets.",
                    'constraints'  => "- Input length: 1 to 200 characters\n- Only bracket characters in input",
                    'starter_code' => "<?php\n\n\$str = trim(fgets(STDIN));\n// Use a stack to check if brackets are balanced\n// Print Yes or No\n",
                    'solution_code' => "<?php\n\$str   = trim(fgets(STDIN));\n\$stack = [];\n\$map   = [')' => '(', '}' => '{', ']' => '['];\nforeach (str_split(\$str) as \$ch) {\n    if (in_array(\$ch, ['(', '{', '['])) {\n        \$stack[] = \$ch;\n    } elseif (isset(\$map[\$ch])) {\n        if (empty(\$stack) || end(\$stack) !== \$map[\$ch]) { echo 'No'; exit; }\n        array_pop(\$stack);\n    }\n}\necho empty(\$stack) ? 'Yes' : 'No';\n",
                ],
                'test_cases' => [
                    ['input' => '([{}])',   'expected_output' => 'Yes', 'is_hidden' => false, 'order' => 1, 'label' => 'Example 1'],
                    ['input' => '([)]',     'expected_output' => 'No',  'is_hidden' => false, 'order' => 2, 'label' => 'Example 2'],
                    ['input' => '{[]{}}',   'expected_output' => 'Yes', 'is_hidden' => true,  'order' => 3, 'label' => 'Hidden 1'],
                    ['input' => '(((',      'expected_output' => 'No',  'is_hidden' => true,  'order' => 4, 'label' => 'Hidden 2'],
                ],
            ],

            // ═══════════════════════════════════════════════════════════════
            // HARD  (orders 13-15)  — unlocks after 2 medium accepted
            // ═══════════════════════════════════════════════════════════════

            /* ── 13. Longest Common Prefix ───────────── */
            [
                'problem' => [
                    'title'        => 'Longest Common Prefix',
                    'slug'         => 'longest-common-prefix',
                    'difficulty'   => 'hard',
                    'language'     => 'php',
                    'order'        => 13,
                    'description'  => "Read **N** on the first line, then **N** words on separate lines. Print the **longest common prefix** shared by all words. If there is no common prefix, print **None**.\n\n**Example:**\n```\nInput:\n3\nflower\nflow\nflight\n\nOutput: fl\n```",
                    'constraints'  => "- 2 ≤ N ≤ 50\n- Word length: 1 to 100 characters\n- Lowercase alphabetic characters only",
                    'starter_code' => "<?php\n\n\$n = (int) trim(fgets(STDIN));\n\$words = [];\nfor (\$i = 0; \$i < \$n; \$i++) \$words[] = trim(fgets(STDIN));\n// Find and print the longest common prefix, or \"None\"\n",
                    'solution_code' => "<?php\n\$n = (int) trim(fgets(STDIN));\n\$words = [];\nfor (\$i = 0; \$i < \$n; \$i++) \$words[] = trim(fgets(STDIN));\n\$prefix = \$words[0];\nfor (\$i = 1; \$i < \$n; \$i++) {\n    while (strpos(\$words[\$i], \$prefix) !== 0) {\n        \$prefix = substr(\$prefix, 0, -1);\n        if (\$prefix === '') { echo 'None'; exit; }\n    }\n}\necho \$prefix;\n",
                ],
                'test_cases' => [
                    ['input' => "3\nflower\nflow\nflight",                     'expected_output' => 'fl',    'is_hidden' => false, 'order' => 1, 'label' => 'Example 1'],
                    ['input' => "3\ndog\nracecar\ncar",                        'expected_output' => 'None',  'is_hidden' => false, 'order' => 2, 'label' => 'Example 2'],
                    ['input' => "4\ninterface\ninternal\ninteresting\ninterview",'expected_output' => 'inter', 'is_hidden' => true,  'order' => 3, 'label' => 'Hidden 1'],
                    ['input' => "2\nabc\nabc",                                  'expected_output' => 'abc',   'is_hidden' => true,  'order' => 4, 'label' => 'Hidden 2'],
                ],
            ],

            /* ── 14. Roman to Integer ────────────────── */
            [
                'problem' => [
                    'title'        => 'Roman to Integer',
                    'slug'         => 'roman-to-integer',
                    'difficulty'   => 'hard',
                    'language'     => 'php',
                    'order'        => 14,
                    'description'  => "Read a Roman numeral string from stdin. Convert it to an integer and print the result.\n\n**Roman numeral values:**\n```\nI=1  V=5  X=10  L=50  C=100  D=500  M=1000\n```\n\n**Subtraction rule:** If a smaller value appears before a larger value, subtract it.\nExamples: IV=4, IX=9, XL=40, XC=90, CD=400, CM=900\n\n**Example:**\n```\nInput:  MCMXCIV\nOutput: 1994\n```",
                    'constraints'  => "- Valid Roman numerals only (I, V, X, L, C, D, M)\n- 1 ≤ value ≤ 3999",
                    'starter_code' => "<?php\n\n\$roman = trim(fgets(STDIN));\n// Convert the Roman numeral to an integer and print it\n",
                    'solution_code' => "<?php\n\$roman  = trim(fgets(STDIN));\n\$map    = ['I'=>1,'V'=>5,'X'=>10,'L'=>50,'C'=>100,'D'=>500,'M'=>1000];\n\$result = 0;\n\$len    = strlen(\$roman);\nfor (\$i = 0; \$i < \$len; \$i++) {\n    \$curr = \$map[\$roman[\$i]];\n    \$next = \$i + 1 < \$len ? \$map[\$roman[\$i+1]] : 0;\n    \$result += (\$curr < \$next) ? -\$curr : \$curr;\n}\necho \$result;\n",
                ],
                'test_cases' => [
                    ['input' => 'III',     'expected_output' => '3',    'is_hidden' => false, 'order' => 1, 'label' => 'Example 1'],
                    ['input' => 'MCMXCIV', 'expected_output' => '1994', 'is_hidden' => false, 'order' => 2, 'label' => 'Example 2'],
                    ['input' => 'IX',      'expected_output' => '9',    'is_hidden' => true,  'order' => 3, 'label' => 'Hidden 1'],
                    ['input' => 'LVIII',   'expected_output' => '58',   'is_hidden' => true,  'order' => 4, 'label' => 'Hidden 2'],
                ],
            ],

            /* ── 15. Matrix Transpose ────────────────── */
            [
                'problem' => [
                    'title'        => 'Matrix Transpose',
                    'slug'         => 'matrix-transpose',
                    'difficulty'   => 'hard',
                    'language'     => 'php',
                    'order'        => 15,
                    'description'  => "Read a matrix and print its **transpose**.\n\n**Input format:**\n```\nLine 1: N M   (rows and columns)\nNext N lines: M space-separated integers per row\n```\n\nThe transpose flips rows and columns — row i becomes column i.\n\n**Example:**\n```\nInput:\n2 3\n1 2 3\n4 5 6\n\nOutput:\n1 4\n2 5\n3 6\n```",
                    'constraints'  => "- 1 ≤ N, M ≤ 10\n- -1000 ≤ each element ≤ 1000",
                    'starter_code' => "<?php\n\n[\$n, \$m] = array_map('intval', explode(' ', trim(fgets(STDIN))));\n\$matrix = [];\nfor (\$i = 0; \$i < \$n; \$i++) {\n    \$matrix[] = array_map('intval', explode(' ', trim(fgets(STDIN))));\n}\n// Print the transpose\n",
                    'solution_code' => "<?php\n[\$n, \$m] = array_map('intval', explode(' ', trim(fgets(STDIN))));\n\$matrix = [];\nfor (\$i = 0; \$i < \$n; \$i++) {\n    \$matrix[] = array_map('intval', explode(' ', trim(fgets(STDIN))));\n}\nfor (\$j = 0; \$j < \$m; \$j++) {\n    \$row = [];\n    for (\$i = 0; \$i < \$n; \$i++) \$row[] = \$matrix[\$i][\$j];\n    echo implode(' ', \$row);\n    if (\$j < \$m - 1) echo \"\\n\";\n}\n",
                ],
                'test_cases' => [
                    ['input' => "2 3\n1 2 3\n4 5 6",      'expected_output' => "1 4\n2 5\n3 6",      'is_hidden' => false, 'order' => 1, 'label' => 'Example 1'],
                    ['input' => "3 3\n1 2 3\n4 5 6\n7 8 9",'expected_output' => "1 4 7\n2 5 8\n3 6 9",'is_hidden' => false, 'order' => 2, 'label' => 'Example 2'],
                    ['input' => "1 4\n10 20 30 40",        'expected_output' => "10\n20\n30\n40",      'is_hidden' => true,  'order' => 3, 'label' => 'Hidden 1'],
                    ['input' => "2 2\n5 6\n7 8",           'expected_output' => "5 7\n6 8",            'is_hidden' => true,  'order' => 4, 'label' => 'Hidden 2'],
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
