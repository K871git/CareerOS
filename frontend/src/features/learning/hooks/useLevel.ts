import { useQuery, useMutation } from '@tanstack/react-query';
import { levelService } from '../services/levelService';
import type { LevelStatus, ExamResult } from '../../../types/api';

export function useSubjectBySlug(slug: string, enabled = true) {
    return useQuery({
        queryKey: ['subject-by-slug', slug],
        queryFn: () => levelService.getSubjectBySlug(slug).then(r => r.data.data),
        enabled: enabled && slug.length > 0,
    });
}

export function useLevelStatus(subjectId: number) {
    return useQuery<LevelStatus[]>({
        queryKey: ['level-status', subjectId],
        queryFn: () => levelService.getLevelStatus(subjectId).then(r => r.data.data),
        enabled: subjectId > 0,
    });
}

export function useTopicsForLevel(subjectId: number, level: number) {
    return useQuery({
        queryKey: ['topics-level', subjectId, level],
        queryFn: () => levelService.getTopicsForLevel(subjectId, level).then(r => r.data.data),
        enabled: subjectId > 0 && level > 0,
    });
}

export function useExamQuestions(subjectId: number, level: number) {
    return useQuery({
        queryKey: ['exam-questions', subjectId, level],
        queryFn: () => levelService.getExamQuestions(subjectId, level).then(r => r.data.data),
        enabled: subjectId > 0 && level > 0,
        retry: false,
    });
}

export function useSubmitExam() {
    return useMutation<ExamResult, Error, { subjectId: number; level: number; answers: Record<number, number> }>({
        mutationFn: ({ subjectId, level, answers }) =>
            levelService.submitExam(subjectId, level, answers).then(r => r.data.data),
    });
}
