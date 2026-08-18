import { useQuery, useMutation } from '@tanstack/react-query';
import {
    getTheoryAreas,
    getTheoryLevels,
    getTheoryExamQuestions,
    submitTheoryExam,
} from '../services/theoryLevelService';

export const useTheoryAreas = () =>
    useQuery({
        queryKey: ['theory-areas'],
        queryFn: getTheoryAreas,
    });

export const useTheoryLevels = (area: string) =>
    useQuery({
        queryKey: ['theory-levels', area],
        queryFn: () => getTheoryLevels(area),
        enabled: !!area,
    });

export const useTheoryExamQuestions = (area: string, level: number) =>
    useQuery({
        queryKey: ['theory-exam', area, level],
        queryFn: () => getTheoryExamQuestions(area, level),
        enabled: !!area && level > 0,
        staleTime: 0,
    });

export const useSubmitTheoryExam = () =>
    useMutation({
        mutationFn: ({
            area,
            level,
            answers,
        }: {
            area: string;
            level: number;
            answers: Record<number, number>;
        }) => submitTheoryExam(area, level, answers),
    });
