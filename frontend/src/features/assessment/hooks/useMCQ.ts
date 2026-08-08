import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { useNavigate } from 'react-router-dom';
import toast from 'react-hot-toast';
import { assessmentService, type MCQSubmitPayload } from '../services/assessmentService';
import type { MCQQuestion, AssessmentAttemptResult } from '../../../types/api';

export function useQuestions(topicId: number) {
    return useQuery<MCQQuestion[]>({
        queryKey: ['questions', topicId],
        queryFn: async () => {
            const res = await assessmentService.getQuestions(topicId);
            return res.data.data ?? [];
        },
        enabled: topicId > 0,
        staleTime: 0,
        gcTime: 0,
        refetchOnMount: 'always',
    });
}

export function useAttemptResult(attemptId: number) {
    return useQuery<AssessmentAttemptResult | null>({
        queryKey: ['attempt', attemptId],
        queryFn: async () => {
            try {
                const res = await assessmentService.getAttemptResult(attemptId);
                return res.data.data ?? null;
            } catch {
                return null;
            }
        },
        enabled: attemptId > 0,
    });
}

export function useSubmitAttempt() {
    const navigate = useNavigate();
    const queryClient = useQueryClient();
    return useMutation({
        mutationFn: (data: MCQSubmitPayload) => assessmentService.submitAttempt(data),
        onSuccess: ({ data: res }) => {
            queryClient.invalidateQueries({ queryKey: ['topics'] });
            const attemptId = res.data?.id;
            if (attemptId) {
                navigate(`/practice/results/${attemptId}`);
            }
        },
        onError: (error: any) => {
            toast.error(error.response?.data?.message ?? 'Failed to submit quiz.');
        },
    });
}
