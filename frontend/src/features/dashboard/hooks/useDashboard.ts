import { useQuery } from '@tanstack/react-query';
import { dashboardService } from '../services/dashboardService';
import type { DashboardOverview } from '../../../types/api';

export function useDashboardOverview() {
    return useQuery<DashboardOverview | null>({
        queryKey: ['dashboard', 'overview'],
        queryFn: async () => {
            try {
                const res = await dashboardService.getOverview();
                return res.data.data ?? null;
            } catch {
                return null;
            }
        },
        retry: false,
    });
}
