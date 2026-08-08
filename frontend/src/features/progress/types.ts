export interface TrackDetailLesson {
    id: number;
    title: string;
    status: 'NOT_STARTED' | 'IN_PROGRESS' | 'COMPLETED';
    completed_at: string | null;
}

export interface TrackDetailTopic {
    id: number;
    title: string;
    total_lessons: number;
    completed_lessons: number;
    percentage: number;
    lessons: TrackDetailLesson[];
}

export interface TrackDetailSubject {
    id: number;
    title: string;
    total_lessons: number;
    completed_lessons: number;
    percentage: number;
    topics: TrackDetailTopic[];
}

export interface TrackDetailProgress {
    track: {
        id: number;
        title: string;
        slug: string;
        total_lessons: number;
        completed_lessons: number;
        percentage: number;
    };
    subjects: TrackDetailSubject[];
}
