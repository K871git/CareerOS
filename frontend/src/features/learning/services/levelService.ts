import api from '../../../api/axios';
import type { ApiResponse, Subject, Topic, MCQQuestion, LevelStatus, ExamResult } from '../../../types/api';

export const levelService = {
    getSubjectBySlug: (slug: string) =>
        api.get<ApiResponse<Subject>>(`/v1/subjects/by-slug/${slug}`),

    getLevelStatus: (subjectId: number) =>
        api.get<ApiResponse<LevelStatus[]>>(`/v1/subjects/${subjectId}/levels`),

    getTopicsForLevel: (subjectId: number, level: number) =>
        api.get<ApiResponse<Topic[]>>(`/v1/subjects/${subjectId}/levels/${level}/topics`),

    getExamQuestions: (subjectId: number, level: number) =>
        api.get<ApiResponse<MCQQuestion[]>>(`/v1/subjects/${subjectId}/levels/${level}/exam`),

    submitExam: (subjectId: number, level: number, answers: Record<number, number>) =>
        api.post<ApiResponse<ExamResult>>(`/v1/subjects/${subjectId}/levels/${level}/exam`, { answers }),
};
