import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import toast from 'react-hot-toast';
import { learningService } from '../services/learningService';
import type { LearningTrack, Subject, Topic, Lesson } from '../../../types/api';

export function useTracks() {
    return useQuery<LearningTrack[]>({
        queryKey: ['tracks'],
        queryFn: async () => {
            const res = await learningService.getTracks();
            return res.data.data ?? [];
        },
    });
}

export function useTrack(id: number) {
    return useQuery<LearningTrack | null>({
        queryKey: ['track', id],
        queryFn: async () => {
            try {
                const res = await learningService.getTrack(id);
                return res.data.data ?? null;
            } catch {
                return null;
            }
        },
        enabled: id > 0,
    });
}

export function useSubjects(trackId: number) {
    return useQuery<Subject[]>({
        queryKey: ['subjects', trackId],
        queryFn: async () => {
            const res = await learningService.getSubjects(trackId);
            return res.data.data ?? [];
        },
        enabled: trackId > 0,
    });
}

export function useTopics(subjectId: number) {
    return useQuery<Topic[]>({
        queryKey: ['topics', subjectId],
        queryFn: async () => {
            const res = await learningService.getTopics(subjectId);
            return res.data.data ?? [];
        },
        enabled: subjectId > 0,
    });
}

export function useLessons(topicId: number | null) {
    return useQuery<Lesson[]>({
        queryKey: ['lessons', topicId],
        queryFn: async () => {
            const res = await learningService.getLessons(topicId!);
            return res.data.data ?? [];
        },
        enabled: topicId !== null && topicId > 0,
    });
}

export function useLesson(lessonId: number) {
    return useQuery<Lesson | null>({
        queryKey: ['lesson', lessonId],
        queryFn: async () => {
            try {
                const res = await learningService.getLesson(lessonId);
                return res.data.data ?? null;
            } catch {
                return null;
            }
        },
        enabled: lessonId > 0,
    });
}

export function useCompleteLesson() {
    const queryClient = useQueryClient();
    return useMutation({
        mutationFn: (lessonId: number) => learningService.completeLesson(lessonId),
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['dashboard:progress'] });
            queryClient.invalidateQueries({ queryKey: ['dashboard:activity'] });
            queryClient.invalidateQueries({ queryKey: ['progress'] });
        },
        onError: (error: any) => {
            const msg = error.response?.data?.message ?? 'Failed to mark lesson as complete.';
            toast.error(msg);
        },
    });
}
