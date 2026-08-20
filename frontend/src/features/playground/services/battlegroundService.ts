import apiClient from '../../../api/axios';
import type { ProblemListItem, ProblemDetail, SubmitPayload, SubmitResult } from '../types';

export const battlegroundService = {
    getProblems: async (): Promise<ProblemListItem[]> => {
        const { data } = await apiClient.get('/v1/battleground/problems');
        return data.data as ProblemListItem[];
    },

    getProblem: async (slug: string): Promise<ProblemDetail> => {
        const { data } = await apiClient.get(`/v1/battleground/problems/${slug}`);
        return data.data as ProblemDetail;
    },

    submit: async (slug: string, payload: SubmitPayload): Promise<SubmitResult> => {
        const { data } = await apiClient.post(`/v1/battleground/problems/${slug}/submit`, payload);
        return data.data as SubmitResult;
    },
};
