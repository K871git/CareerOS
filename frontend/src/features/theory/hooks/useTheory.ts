import { useMutation, useQuery } from '@tanstack/react-query';
import { useNavigate } from 'react-router-dom';
import toast from 'react-hot-toast';
import { theoryService } from '../services/theoryService';
import type { TheoryQuestion, TheoryAnswer } from '../../../types/api';

export function useTheoryQuestions(topicId: number) {
    return useQuery<TheoryQuestion[]>({
        queryKey: ['theory-questions', topicId],
        queryFn: async () => {
            const res = await theoryService.getQuestions(topicId);
            return res.data.data ?? [];
        },
        enabled: topicId > 0,
    });
}

export function useTheoryAnswer(answerId: number) {
    return useQuery<TheoryAnswer | null>({
        queryKey: ['theory-answer', answerId],
        queryFn: async () => {
            try {
                const res = await theoryService.getAnswer(answerId);
                return res.data.data ?? null;
            } catch {
                return null;
            }
        },
        enabled: answerId > 0,
    });
}

export function useSubmitTheoryAnswer() {
    const navigate = useNavigate();
    return useMutation({
        mutationFn: ({ questionId, answer }: { questionId: number; answer: string }) =>
            theoryService.submitAnswer(questionId, { answer }),
        onSuccess: ({ data: res }) => {
            const answerId = res.data?.id;
            if (answerId) {
                toast.success('Answer submitted! Pending review.');
                navigate(`/theory/answers/${answerId}`);
            }
        },
        onError: (error: any) => {
            const errors = error.response?.data?.errors;
            const msg =
                errors?.answer?.[0] ??
                error.response?.data?.message ??
                'Failed to submit answer.';
            toast.error(msg);
        },
    });
}
