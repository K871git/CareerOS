import { useQuery } from '@tanstack/react-query';
import { dashboardService } from '../services/dashboardService';
import type { LearningTrack, RecentActivity, UserProgress } from '../../../types/api';

export function useUserProgress() {
    return useQuery<UserProgress | null>({
        queryKey: ['dashboard', 'progress'],
        queryFn: async () => {
            try {
                const res = await dashboardService.getProgress();
                return res.data.data ?? null;
            } catch {
                return null;
            }
        },
        retry: false,
    });
}

export function useLearningTracks() {
    return useQuery<LearningTrack[]>({
        queryKey: ['dashboard', 'tracks'],
        queryFn: async () => {
            try {
                const res = await dashboardService.getTracks();
                const d = res.data.data;
                if (Array.isArray(d)) return d;
                return (d as { tracks: LearningTrack[] })?.tracks ?? [];
            } catch {
                return [];
            }
        },
        retry: false,
    });
}

export function useRecentActivity() {
    return useQuery<RecentActivity[]>({
        queryKey: ['dashboard', 'activity'],
        queryFn: async () => {
            try {
                const res = await dashboardService.getRecentActivity();
                const d = res.data.data;
                if (Array.isArray(d)) return d;
                return (d as { activities: RecentActivity[] })?.activities ?? [];
            } catch {
                return [];
            }
        },
        retry: false,
    });
}
