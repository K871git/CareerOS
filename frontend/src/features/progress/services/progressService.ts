import api from '../../../api/axios';
import type { ApiResponse, UserProgress, RecentActivity } from '../../../types/api';
import type { TrackDetailProgress } from '../types';

export const progressService = {
    getProgress: () =>
        api.get<ApiResponse<UserProgress>>('/v1/progress'),

    getTrackProgress: (trackId: number) =>
        api.get<ApiResponse<TrackDetailProgress>>(`/v1/tracks/${trackId}/progress`),

    getRecentActivity: () =>
        api.get<ApiResponse<RecentActivity[]>>('/v1/activity/recent'),
};
