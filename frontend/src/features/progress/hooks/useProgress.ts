import { useQuery } from '@tanstack/react-query';
import { progressService } from '../services/progressService';
import type { UserProgress, RecentActivity } from '../../../types/api';
import type { TrackDetailProgress } from '../types';

export function useOverallProgress() {
    return useQuery<UserProgress | null>({
        queryKey: ['dashboard:progress'],
        queryFn: async () => {
            try {
                const res = await progressService.getProgress();
                return res.data.data ?? null;
            } catch {
                return null;
            }
        },
    });
}

export function useRecentActivityFeed() {
    return useQuery<RecentActivity[]>({
        queryKey: ['dashboard:activity'],
        queryFn: async () => {
            try {
                const res = await progressService.getRecentActivity();
                return res.data.data ?? [];
            } catch {
                return [];
            }
        },
    });
}

export function useTrackDetailProgress(trackId: number) {
    return useQuery<TrackDetailProgress | null>({
        queryKey: ['progress', 'track', trackId],
        queryFn: async () => {
            try {
                const res = await progressService.getTrackProgress(trackId);
                return res.data.data ?? null;
            } catch {
                return null;
            }
        },
        enabled: trackId > 0,
    });
}
