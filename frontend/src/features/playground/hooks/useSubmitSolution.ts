import { useMutation, useQueryClient } from '@tanstack/react-query';
import { battlegroundService } from '../services/battlegroundService';
import type { SubmitPayload } from '../types';

export function useSubmitSolution(slug: string) {
    const qc = useQueryClient();

    return useMutation({
        mutationFn: (payload: SubmitPayload) => battlegroundService.submit(slug, payload),
        onSuccess: () => {
            // Refresh problem detail to update last_submission badge
            qc.invalidateQueries({ queryKey: ['battleground', 'problem', slug] });
            qc.invalidateQueries({ queryKey: ['battleground', 'problems'] });
        },
    });
}
