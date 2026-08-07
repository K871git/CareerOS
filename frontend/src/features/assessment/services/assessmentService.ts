import api from '../../../api/axios';
import type { ApiResponse, CareerAssessment, Skill } from '../../../types/api';

export interface AssessmentPayload {
    target_role: string;
    skills: { skill_id: number; level: string; score: number }[];
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
};
