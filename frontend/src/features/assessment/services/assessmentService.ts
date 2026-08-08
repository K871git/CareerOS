import api from '../../../api/axios';
import type { ApiResponse, CareerAssessment, Skill, MCQQuestion, AssessmentAttemptResult } from '../../../types/api';

export interface AssessmentPayload {
    target_role: string;
    skills: { skill_id: number; level: string; score: number }[];
}

export interface MCQSubmitPayload {
    answers: { question_id: number; selected_option_id: number }[];
}

export const assessmentService = {
    getSkills: () =>
        api.get<ApiResponse<Skill[]>>('/v1/skills'),

    get: () =>
        api.get<ApiResponse<CareerAssessment>>('/v1/career-assessment'),

    create: (data: AssessmentPayload) =>
        api.post<ApiResponse<CareerAssessment>>('/v1/career-assessment', data),

    update: (data: AssessmentPayload) =>
        api.put<ApiResponse<CareerAssessment>>('/v1/career-assessment', data),

    getQuestions: (topicId: number) =>
        api.get<ApiResponse<MCQQuestion[]>>(`/v1/topics/${topicId}/questions`),

    submitAttempt: (data: MCQSubmitPayload) =>
        api.post<ApiResponse<AssessmentAttemptResult>>('/v1/assessments/submit', data),

    getAttemptResult: (attemptId: number) =>
        api.get<ApiResponse<AssessmentAttemptResult>>(`/v1/assessments/${attemptId}`),
};
