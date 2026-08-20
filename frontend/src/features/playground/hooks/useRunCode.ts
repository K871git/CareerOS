import { useMutation } from '@tanstack/react-query';
import { playgroundService } from '../services/playgroundService';
import type { RunCodePayload } from '../types';

type RunCodeInput = RunCodePayload & { signal?: AbortSignal };

export function useRunCode() {
    return useMutation({
        mutationFn: ({ signal, ...payload }: RunCodeInput) =>
            playgroundService.runCode(payload, signal),
    });
}
