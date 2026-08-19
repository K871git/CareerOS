<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RunCodeRequest;
use Illuminate\Http\JsonResponse;

class PlaygroundController extends Controller
{
    private const TIMEOUT    = 5;      // seconds
    private const MAX_OUTPUT = 10000;  // characters

    public function run(RunCodeRequest $request): JsonResponse
    {
        [$output, $exitCode] = $this->execute($request->language, $request->code);

        return response()->json([
            'success' => true,
            'message' => 'Code executed.',
            'data'    => [
                'output'    => $output,
                'exit_code' => $exitCode,
            ],
        ]);
    }

    private function execute(string $language, string $code): array
    {
        // PHP uses a temp file so `<?php` tags work naturally.
        // Node uses -e since JS has no opening-tag concept.
        $tmpFile = null;

        if ($language === 'php') {
            $tmpFile = tempnam(sys_get_temp_dir(), 'playground_') . '.php';
            file_put_contents($tmpFile, $code);
            $cmd = ['php', $tmpFile];
        } else {
            $cmd = ['node', '-e', $code];
        }

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $options = PHP_OS_FAMILY === 'Windows' ? ['bypass_shell' => true] : [];
        $proc    = proc_open($cmd, $descriptors, $pipes, null, null, $options);

        if (!is_resource($proc)) {
            return ['Failed to start execution engine.', 1];
        }

        fclose($pipes[0]);

        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);

        $output   = '';
        $deadline = microtime(true) + self::TIMEOUT;

        while (microtime(true) < $deadline) {
            $out = fread($pipes[1], 4096);
            $err = fread($pipes[2], 4096);
            if ($out) $output .= $out;
            if ($err) $output .= $err;

            $status = proc_get_status($proc);
            if (!$status['running']) {
                break;
            }

            usleep(50_000);
        }

        $status = proc_get_status($proc);
        if ($status['running']) {
            proc_terminate($proc);
            $output .= "\n\n[Timed out after " . self::TIMEOUT . "s]";
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

        if (strlen($output) > self::MAX_OUTPUT) {
            $output = substr($output, 0, self::MAX_OUTPUT) . "\n\n[Output truncated]";
        }

        return [trim($output) ?: '(no output)', $exit];
    }
}
