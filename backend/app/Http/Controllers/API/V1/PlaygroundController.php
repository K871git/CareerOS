<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RunCodeRequest;
use App\Traits\ExecutesCode;
use Illuminate\Http\JsonResponse;

class PlaygroundController extends Controller
{
    use ExecutesCode;

    private const PG_DB = 'careeros_playground';

    /* ── Public endpoints ─────────────────────────────────────── */

    public function run(RunCodeRequest $request): JsonResponse
    {
        $language = $request->language;
        $code     = $request->code;
        $stdin    = (string) $request->input('stdin', '');

        if ($language !== 'mysql' && $this->isDangerous($language, $code)) {
            return response()->json([
                'success' => false,
                'message' => 'Code contains restricted functions.',
            ], 422);
        }

        [$output, $exitCode] = $language === 'mysql'
            ? $this->executeMysql($code)
            : $this->executeCode($language, $code, $stdin);

        return response()->json([
            'success' => true,
            'message' => 'Code executed.',
            'data'    => ['output' => $output, 'exit_code' => $exitCode],
        ]);
    }

    public function schema(): JsonResponse
    {
        try {
            $pdo = $this->connectToPlayground();
            $this->ensurePlaygroundSeeded($pdo);

            $tableNames = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);
            $tables     = [];

            foreach ($tableNames as $name) {
                $cols     = $pdo->query("SHOW COLUMNS FROM `{$name}`")->fetchAll(\PDO::FETCH_ASSOC);
                $tables[] = [
                    'name'    => $name,
                    'columns' => array_map(fn ($c) => [
                        'name'     => $c['Field'],
                        'type'     => $c['Type'],
                        'nullable' => $c['Null'] === 'YES',
                        'key'      => $c['Key'],
                    ], $cols),
                ];
            }

            return response()->json(['success' => true, 'data' => ['tables' => $tables]]);

        } catch (\PDOException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function resetData(): JsonResponse
    {
        try {
            $pdo = $this->connectToPlayground();

            $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
            $pdo->exec('DROP TABLE IF EXISTS orders, employees, products, departments');
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

            $this->ensurePlaygroundSeeded($pdo);

            return response()->json(['success' => true, 'message' => 'Playground data reset.']);

        } catch (\PDOException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /* ── Execution handled by ExecutesCode trait ─────────────── */

    private function executeMysql(string $code): array
    {
        try {
            $pdo = $this->connectToPlayground();
            $this->ensurePlaygroundSeeded($pdo);

            // Limit runaway SELECT time — MySQL uses max_execution_time, MariaDB uses max_statement_time.
            try { $pdo->exec('SET SESSION max_execution_time = 5000'); } catch (\Throwable) {}
            try { $pdo->exec('SET SESSION max_statement_time = 5'); }   catch (\Throwable) {}

            $statements = array_filter(array_map('trim', explode(';', $code)));
            $output     = '';

            $pdo->beginTransaction();

            try {
                foreach ($statements as $sql) {
                    if ($sql === '' || preg_match('/^\s*--/', $sql)) {
                        continue;
                    }
                    $stmt    = $pdo->query($sql);
                    $output .= $this->formatQueryResult($stmt) . "\n\n";
                }
            } finally {
                // Rollback DML so sample data stays fresh each run.
                // If DDL auto-committed, this is a no-op (caught silently).
                try { $pdo->rollBack(); } catch (\Throwable) {}
            }

            $output = trim($output) ?: '(no output)';

            if (strlen($output) > 10000) {
                $output = substr($output, 0, 10000) . "\n\n[Output truncated]";
            }

            return [$output, 0];

        } catch (\PDOException $e) {
            return ['ERROR ' . $e->getCode() . ': ' . $e->getMessage(), 1];
        }
    }

    /* ── Formatting ───────────────────────────────────────────── */

    private function formatQueryResult(\PDOStatement $stmt): string
    {
        if ($stmt->columnCount() > 0) {
            return $this->formatTable($stmt->fetchAll(\PDO::FETCH_ASSOC));
        }

        $n = $stmt->rowCount();
        return 'Query OK, ' . $n . ($n === 1 ? ' row' : ' rows') . ' affected';
    }

    private function formatTable(array $rows): string
    {
        if (empty($rows)) {
            return 'Empty set';
        }

        $cols   = array_keys($rows[0]);
        $widths = [];

        foreach ($cols as $col) {
            $widths[$col] = mb_strlen((string) $col);
        }
        foreach ($rows as $row) {
            foreach ($cols as $col) {
                $widths[$col] = max($widths[$col], mb_strlen((string) ($row[$col] ?? 'NULL')));
            }
        }

        $bar  = '+' . implode('+', array_map(fn ($w) => str_repeat('-', $w + 2), $widths)) . '+';
        $head = '|' . implode('|', array_map(fn ($c) => ' ' . str_pad((string) $c, $widths[$c]) . ' ', $cols)) . '|';

        $lines = [$bar, $head, $bar];
        foreach ($rows as $row) {
            $line = '|';
            foreach ($cols as $col) {
                $line .= ' ' . str_pad((string) ($row[$col] ?? 'NULL'), $widths[$col]) . ' |';
            }
            $lines[] = $line;
        }

        $lines[] = $bar;
        $n       = count($rows);
        $lines[] = $n . ($n === 1 ? ' row in set' : ' rows in set');

        return implode("\n", $lines);
    }

    /* ── Database helpers ─────────────────────────────────────── */

    private function connectToPlayground(): \PDO
    {
        $pdo = new \PDO(
            sprintf('mysql:host=%s;port=%s;charset=utf8mb4', env('DB_HOST', '127.0.0.1'), env('DB_PORT', '3306')),
            env('DB_USERNAME', 'root'),
            env('DB_PASSWORD', ''),
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_TIMEOUT => 5]
        );

        $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . self::PG_DB . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $pdo->exec('USE `' . self::PG_DB . '`');

        return $pdo;
    }

    private function ensurePlaygroundSeeded(\PDO $pdo): void
    {
        if ($pdo->query("SHOW TABLES LIKE 'employees'")->fetch() !== false) {
            return;
        }

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS departments (
                id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name     VARCHAR(100) NOT NULL,
                location VARCHAR(100) NOT NULL
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS employees (
                id            INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
                name          VARCHAR(100)  NOT NULL,
                department_id INT UNSIGNED  NOT NULL,
                salary        DECIMAL(10,2) NOT NULL,
                hire_date     DATE          NOT NULL,
                manager_id    INT UNSIGNED  NULL
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS products (
                id       INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
                name     VARCHAR(100)  NOT NULL,
                category VARCHAR(50)   NOT NULL,
                price    DECIMAL(10,2) NOT NULL,
                stock    INT UNSIGNED  NOT NULL DEFAULT 0
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS orders (
                id            INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
                customer_name VARCHAR(100)  NOT NULL,
                product_id    INT UNSIGNED  NOT NULL,
                amount        DECIMAL(10,2) NOT NULL,
                status        ENUM('pending','shipped','delivered','cancelled') NOT NULL,
                order_date    DATE NOT NULL
            )
        ");

        $pdo->exec("
            INSERT INTO departments (name, location) VALUES
                ('Engineering', 'New York'),
                ('Marketing',   'Los Angeles'),
                ('Sales',       'Chicago'),
                ('HR',          'New York'),
                ('Finance',     'Chicago')
        ");

        $pdo->exec("
            INSERT INTO employees (name, department_id, salary, hire_date, manager_id) VALUES
                ('Alice Johnson', 1, 95000.00, '2020-03-15', NULL),
                ('Bob Smith',     2, 82000.00, '2019-07-22', NULL),
                ('Carol White',   1, 78000.00, '2021-01-10', 1),
                ('David Brown',   3, 65000.00, '2022-06-01', NULL),
                ('Eve Davis',     1, 91000.00, '2018-11-05', 1),
                ('Frank Wilson',  2, 73000.00, '2023-02-14', 2),
                ('Grace Lee',     4, 68000.00, '2020-09-30', NULL),
                ('Henry Taylor',  3, 71000.00, '2021-04-18', 4),
                ('Ivy Anderson',  5, 88000.00, '2017-12-01', NULL),
                ('Jack Thomas',   1, 85000.00, '2019-05-20', 1)
        ");

        $pdo->exec("
            INSERT INTO products (name, category, price, stock) VALUES
                ('Laptop',     'Electronics', 1199.99, 45),
                ('Phone',      'Electronics',  799.99, 120),
                ('Tablet',     'Electronics',  449.99, 78),
                ('Monitor',    'Electronics',  299.99, 55),
                ('Keyboard',   'Accessories',   74.99, 200),
                ('Mouse',      'Accessories',   44.99, 350),
                ('Headphones', 'Accessories',  149.99, 90),
                ('Webcam',     'Accessories',   89.99, 65)
        ");

        $pdo->exec("
            INSERT INTO orders (customer_name, product_id, amount, status, order_date) VALUES
                ('John Doe',      1, 1200.00, 'delivered', '2024-01-10'),
                ('Jane Smith',    2,  800.00, 'pending',   '2024-01-15'),
                ('Bob Johnson',   3,  450.00, 'delivered', '2024-01-20'),
                ('Alice Brown',   1, 1350.00, 'shipped',   '2024-02-01'),
                ('Charlie Davis', 4,  320.00, 'delivered', '2024-02-10'),
                ('Eve Wilson',    5,   75.00, 'pending',   '2024-02-15'),
                ('Frank Miller',  6,   45.00, 'cancelled', '2024-02-20'),
                ('Grace Lee',     1, 1100.00, 'delivered', '2024-03-01'),
                ('Henry Taylor',  2,  850.00, 'shipped',   '2024-03-05'),
                ('Ivy Anderson',  4,  280.00, 'pending',   '2024-03-10')
        ");
    }
}
