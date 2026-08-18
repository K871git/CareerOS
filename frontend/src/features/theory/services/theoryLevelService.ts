import api from '../../../api/axios';
import type { TheoryArea, TheoryLevelStatus, TheoryExamResult, MCQQuestion } from '../../../types/api';

export const getTheoryAreas = async (): Promise<TheoryArea[]> => {
    const res = await api.get('/v1/theory/areas');
    return res.data.data;
};

export const getTheoryLevels = async (area: string): Promise<TheoryLevelStatus[]> => {
    const res = await api.get(`/v1/theory/${area}/levels`);
    return res.data.data;
};

export const getTheoryExamQuestions = async (area: string, level: number): Promise<MCQQuestion[]> => {
    const res = await api.get(`/v1/theory/${area}/levels/${level}/exam`);
    return res.data.data;
};

export const submitTheoryExam = async (
    area: string,
    level: number,
    answers: Record<number, number>,
): Promise<TheoryExamResult> => {
    const res = await api.post(`/v1/theory/${area}/levels/${level}/exam`, { answers });
    return res.data.data;
};
