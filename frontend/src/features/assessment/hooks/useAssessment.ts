import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import toast from 'react-hot-toast';
import { assessmentService, type AssessmentPayload } from '../services/assessmentService';
import type { CareerAssessment, Skill } from '../../../types/api';

export function useSkills() {
    return useQuery<Skill[]>({
        queryKey: ['skills'],
        queryFn: async () => {
            const res = await assessmentService.getSkills();
            return res.data.data ?? [];
        },
        staleTime: Infinity,
    });
}

export function useCareerAssessment() {
    return useQuery<CareerAssessment | null>({
        queryKey: ['career-assessment'],
        queryFn: async () => {
            try {
                const res = await assessmentService.get();
                const d = res.data.data;
                if (!d?.target_role && (!d?.skills || d.skills.length === 0)) return null;
                return d ?? null;
            } catch {
                return null;
            }
        },
        retry: false,
    });
}

export function useCreateAssessment() {
    const queryClient = useQueryClient();
    return useMutation({
        mutationFn: (data: AssessmentPayload) => assessmentService.create(data),
        onSuccess: ({ data: res }) => {
            queryClient.setQueryData(['career-assessment'], res.data);
            toast.success('Career assessment saved!');
        },
        onError: (error: any) => {
            toast.error(error.response?.data?.message ?? 'Failed to save assessment.');
        },
    });
}

export function useUpdateAssessment() {
    const queryClient = useQueryClient();
    return useMutation({
        mutationFn: (data: AssessmentPayload) => assessmentService.update(data),
        onSuccess: ({ data: res }) => {
            queryClient.setQueryData(['career-assessment'], res.data);
            toast.success('Assessment updated!');
        },
        onError: (error: any) => {
            toast.error(error.response?.data?.message ?? 'Failed to update assessment.');
        },
    });
}
