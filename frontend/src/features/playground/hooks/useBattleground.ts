import { useQuery } from '@tanstack/react-query';
import { battlegroundService } from '../services/battlegroundService';

export function useProblemList() {
    return useQuery({
        queryKey: ['battleground', 'problems'],
        queryFn:  battlegroundService.getProblems,
        staleTime: 5 * 60 * 1000,
    });
}

export function useProblemDetail(slug: string | null) {
    return useQuery({
        queryKey: ['battleground', 'problem', slug],
        queryFn:  () => battlegroundService.getProblem(slug!),
        enabled:  !!slug,
        staleTime: 5 * 60 * 1000,
    });
}
