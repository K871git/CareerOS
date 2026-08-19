import { useMutation } from '@tanstack/react-query';
import { playgroundService } from '../services/playgroundService';
import type { RunCodePayload } from '../types';

export function useRunCode() {
    return useMutation({
        mutationFn: (payload: RunCodePayload) => playgroundService.runCode(payload),
    });
}
