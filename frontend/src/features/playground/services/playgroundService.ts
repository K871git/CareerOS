import apiClient from '../../../api/axios';
import type { RunCodePayload, RunCodeResult, SchemaResult } from '../types';

export const playgroundService = {
    runCode: async (payload: RunCodePayload, signal?: AbortSignal): Promise<RunCodeResult> => {
        const { data } = await apiClient.post('/v1/playground/run', payload, { signal });
        return data.data as RunCodeResult;
    },

    getSchema: async (): Promise<SchemaResult> => {
        const { data } = await apiClient.get('/v1/playground/schema');
        return data.data as SchemaResult;
    },

    resetData: async (): Promise<void> => {
        await apiClient.post('/v1/playground/reset-data');
    },
};
