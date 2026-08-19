import { useQuery } from '@tanstack/react-query';
import { overviewService } from '../services/overviewService';
import type { DashboardOverview, UserProgress, RecentActivity } from '../../../types/api';
import type { TrackDetailProgress } from '../types';

export function useDashboardOverview() {
    return useQuery<DashboardOverview | null>({
        queryKey: ['dashboard', 'overview'],
        queryFn: async () => {
            try {
                const res = await overviewService.getDashboard();
                return res.data.data ?? null;
            } catch {
                return null;
            }
        },
        retry: false,
    });
}

export function useOverallProgress() {
    return useQuery<UserProgress | null>({
        queryKey: ['dashboard:progress'],
        queryFn: async () => {
            try {
                const res = await overviewService.getProgress();
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
                const res = await overviewService.getRecentActivity();
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
                const res = await overviewService.getTrackProgress(trackId);
                return res.data.data ?? null;
            } catch {
                return null;
            }
        },
        enabled: trackId > 0,
    });
}
