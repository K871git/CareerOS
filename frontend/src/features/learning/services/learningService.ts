import api from '../../../api/axios';
import type { ApiResponse, LearningTrack, Subject, Topic, Lesson, LessonCompletionRecord } from '../../../types/api';

export const learningService = {
    getTracks: () =>
        api.get<ApiResponse<LearningTrack[]>>('/v1/tracks'),

    getTrack: (id: number) =>
        api.get<ApiResponse<LearningTrack>>(`/v1/tracks/${id}`),

    getSubjects: (trackId: number) =>
        api.get<ApiResponse<Subject[]>>(`/v1/tracks/${trackId}/subjects`),

    getTopics: (subjectId: number) =>
        api.get<ApiResponse<Topic[]>>(`/v1/subjects/${subjectId}/topics`),

    getLessons: (topicId: number) =>
        api.get<ApiResponse<Lesson[]>>(`/v1/topics/${topicId}/lessons`),

    getLesson: (lessonId: number) =>
        api.get<ApiResponse<Lesson>>(`/v1/lessons/${lessonId}`),

    completeLesson: (lessonId: number) =>
        api.post<ApiResponse<LessonCompletionRecord>>(`/v1/lessons/${lessonId}/complete`),
};
