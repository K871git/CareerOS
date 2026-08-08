import api from '../../../api/axios';
import type { ApiResponse, TheoryQuestion, TheoryAnswer } from '../../../types/api';

export interface TheoryAnswerPayload {
    answer: string;
}

export const theoryService = {
    getQuestions: (topicId: number) =>
        api.get<ApiResponse<TheoryQuestion[]>>(`/v1/topics/${topicId}/theory-questions`),

    submitAnswer: (questionId: number, data: TheoryAnswerPayload) =>
        api.post<ApiResponse<TheoryAnswer>>(`/v1/theory-questions/${questionId}/submit`, data),

    getAnswer: (answerId: number) =>
        api.get<ApiResponse<TheoryAnswer>>(`/v1/theory-answers/${answerId}`),
};
