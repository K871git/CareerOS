import api from '../../../api/axios';
import type { ApiResponse, LearningTrack, UserProgress, RecentActivity } from '../../../types/api';

export const dashboardService = {
    getProgress: () =>
        api.get<ApiResponse<UserProgress>>('/v1/progress'),

    getTracks: () =>
        api.get<ApiResponse<LearningTrack[] | { tracks: LearningTrack[] }>>('/v1/tracks'),

    getRecentActivity: () =>
        api.get<ApiResponse<RecentActivity[] | { activities: RecentActivity[] }>>('/v1/activity/recent'),
};
