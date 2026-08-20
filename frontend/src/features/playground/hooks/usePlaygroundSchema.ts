import { useQuery } from '@tanstack/react-query';
import { playgroundService } from '../services/playgroundService';
import type { Language } from '../types';

export function usePlaygroundSchema(language: Language) {
    return useQuery({
        queryKey:  ['playground', 'schema'],
        queryFn:   playgroundService.getSchema,
        enabled:   language === 'mysql',
        staleTime: 5 * 60 * 1000,
        retry:     1,
    });
}
