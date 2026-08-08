import api from '../../../api/axios';
import type { ApiResponse, DashboardOverview } from '../../../types/api';

export const dashboardService = {
    getOverview: () =>
        api.get<ApiResponse<DashboardOverview>>('/v1/dashboard'),
};
