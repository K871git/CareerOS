import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { useNavigate } from 'react-router-dom';
import toast from 'react-hot-toast';
import { assessmentService, type MCQSubmitPayload } from '../services/assessmentService';
import type { MCQQuestion, AssessmentAttemptResult } from '../../../types/api';

export function useQuestions(topicId: number, initialData?: MCQQuestion[]) {
    return useQuery<MCQQuestion[]>({
        queryKey: ['questions', topicId],
        queryFn: async () => {
            const res = await assessmentService.getQuestions(topicId);
            return res.data.data ?? [];
        },
        enabled: topicId > 0,
        /* When restoring a saved session, use persisted questions so the set
           stays identical to what the user was answering. */
        staleTime:      initialData ? Infinity : 0,
        gcTime:         0,
        refetchOnMount: initialData ? false : 'always',
        initialData,
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
