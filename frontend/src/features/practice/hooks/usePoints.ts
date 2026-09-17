import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import api from '../../../api/axios';

interface PointsBalance {
    balance: number;
    lifetime_earned: number;
    lifetime_spent: number;
}

interface HintResponse {
    hint: string;
    points_spent: number;
    new_balance: number;
}

const LS_KEY = 'careeros_pts_balance';

function readCached(): number | null {
    try {
        const raw = localStorage.getItem(LS_KEY);
        return raw !== null ? Number(raw) : null;
    } catch { return null; }
}

function writeCache(balance: number) {
    try { localStorage.setItem(LS_KEY, String(balance)); } catch { /* */ }
}

export function usePoints() {
    return useQuery<PointsBalance>({
        queryKey: ['points', 'balance'],
        queryFn: async () => {
            const res = await api.get<{ data: PointsBalance }>('/v1/points');
            const data = res.data.data;
            writeCache(data.balance);
            return data;
        },
        // Seed from localStorage so the badge renders instantly on reload
        initialData: () => {
            const cached = readCached();
            if (cached === null) return undefined;
            return { balance: cached, lifetime_earned: cached, lifetime_spent: 0 };
        },
        initialDataUpdatedAt: 0, // treat cached as stale → refetch immediately
        staleTime: 30_000,
    });
}

export function useUnlockHint() {
    const queryClient = useQueryClient();

    return useMutation<HintResponse, Error, { question_id: number }>({
        mutationFn: async (payload) => {
            const res = await api.post<{ success: boolean; data: HintResponse; message?: string }>(
                '/v1/hints/unlock',
                payload
            );
            if (!res.data.success) throw new Error(res.data.message ?? 'Failed to unlock hint');
            return res.data.data;
        },
        onSuccess: (data) => {
            writeCache(data.new_balance);
            queryClient.setQueryData<PointsBalance>(['points', 'balance'], (old) =>
                old ? { ...old, balance: data.new_balance } : old
            );
        },
    });
}
