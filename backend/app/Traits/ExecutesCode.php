<?php

namespace App\Traits;

trait ExecutesCode
{
    private const EXEC_TIMEOUT = 5;
    private const EXEC_MAX_OUT = 10000;

    private const BLOCKED = [
        'php'        => '/\b(exec|shell_exec|system|passthru|popen|pcntl_exec)\s*\(/i',
        'javascript' => '/require\s*\(\s*[\'"]child_process[\'"]\s*\)/',
        'python'     => '/\b(os\.system|subprocess\.(run|call|Popen|check_output))\s*\(/',
    ];

    protected function isDangerous(string $language, string $code): bool
    {
        $pattern = self::BLOCKED[$language] ?? null;
        return $pattern ? (bool) preg_match($pattern, $code) : false;
    }

    protected function executeCode(string $language, string $code, string $stdin = ''): array
    {
        $tmpFile = null;

        if ($language === 'php') {
            $tmpFile = tempnam(sys_get_temp_dir(), 'bg_') . '.php';
            file_put_contents($tmpFile, $code);
            $cmd = ['php', $tmpFile];
        } elseif ($language === 'python') {
            $tmpFile = tempnam(sys_get_temp_dir(), 'bg_') . '.py';
            file_put_contents($tmpFile, $code);
            $cmd = [PHP_OS_FAMILY === 'Windows' ? 'python' : 'python3', $tmpFile];
        } else {
            $cmd = ['node', '-e', $code];
        }

        $descriptors = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        $options     = PHP_OS_FAMILY === 'Windows' ? ['bypass_shell' => true] : [];
        $proc        = proc_open($cmd, $descriptors, $pipes, null, null, $options);

        if (!is_resource($proc)) {
            return ['Failed to start execution engine.', 1];
        }

        if ($stdin !== '') {
            fwrite($pipes[0], $stdin);
        }
        fclose($pipes[0]);

        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);

        $output   = '';
        $deadline = microtime(true) + self::EXEC_TIMEOUT;

        while (microtime(true) < $deadline) {
            $out = fread($pipes[1], 4096);
            $err = fread($pipes[2], 4096);
            if ($out) $output .= $out;
            if ($err) $output .= $err;
            $status = proc_get_status($proc);
            if (!$status['running']) break;
            usleep(50_000);
        }

        $timedOut = false;
        $status   = proc_get_status($proc);
        if ($status['running']) {
            proc_terminate($proc);
            $timedOut = true;
        }

        $out = @stream_get_contents($pipes[1]);
        $err = @stream_get_contents($pipes[2]);
        if ($out) $output .= $out;
        if ($err) $output .= $err;

        fclose($pipes[1]);
        fclose($pipes[2]);
        $exit = proc_close($proc);

        if ($tmpFile && file_exists($tmpFile)) {
            @unlink($tmpFile);
        }

        if ($timedOut) {
            $output .= "\n[TLE]";
            $exit = 124;
        }

        if (strlen($output) > self::EXEC_MAX_OUT) {
            $output = substr($output, 0, self::EXEC_MAX_OUT) . "\n[Output truncated]";
        }

        return [trim($output) ?: '(no output)', $exit];
    }
}
