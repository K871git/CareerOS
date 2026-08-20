import { useMutation, useQueryClient } from '@tanstack/react-query';
import { playgroundService } from '../services/playgroundService';

export function useResetPlayground() {
    const qc = useQueryClient();
    return useMutation({
        mutationFn: playgroundService.resetData,
        onSuccess: () => {
            qc.invalidateQueries({ queryKey: ['playground', 'schema'] });
        },
    });
}
