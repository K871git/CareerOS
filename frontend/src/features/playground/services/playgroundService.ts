import apiClient from '../../../api/axios';
import type { RunCodePayload, RunCodeResult } from '../types';

export const playgroundService = {
    runCode: async (payload: RunCodePayload): Promise<RunCodeResult> => {
        const { data } = await apiClient.post('/v1/playground/run', payload);
        return data.data as RunCodeResult;
    },
};
