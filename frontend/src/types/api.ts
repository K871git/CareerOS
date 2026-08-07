export interface ApiResponse<T> {
    success: boolean;
    message: string;
    data: T;
}

export interface User {
    id: number;
    name: string;
    email: string;
    created_at: string;
    updated_at: string;
}

export interface AuthTokenResponse {
    user: User;
    token: string;
}

export type LessonStatus = 'NOT_STARTED' | 'IN_PROGRESS' | 'COMPLETED';

export type TrackLevel = 'junior' | 'mid' | 'senior';

export interface LearningTrack {
    id: number;
    title: string;
    slug: string;
    description: string;
    level: TrackLevel;
    total_topics: number;
    progress_percentage: number;
    enrolled: boolean;
}

export interface ProgressTrackItem {
    id: number;
    title: string;
    slug: string;
    total_lessons: number;
    completed_lessons: number;
    percentage: number;
}

export interface UserProgress {
    summary: {
        total_lessons: number;
        completed_lessons: number;
        percentage: number;
    };
    tracks: ProgressTrackItem[];
}

export type ActivityType = 'lesson_completed' | 'question_answered' | 'track_started';

export interface RecentActivity {
    id: number;
    type: ActivityType;
    description: string;
    subject_name?: string;
    score?: number;
    created_at: string;
}

export interface Skill {
    id: number;
    name: string;
    slug: string;
    category: string;
}

export interface AssessmentSkill {
    id: number;
    skill_id: number;
    skill_name: string;
    level: string;
    score: number;
}

export interface CareerAssessment {
    target_role: string | null;
    skills: AssessmentSkill[];
}

export type ExperienceLevel = 'junior' | 'mid' | 'senior';

export interface UserProfile {
    id: number;
    user_id: number;
    current_role: string | null;
    experience_level: ExperienceLevel;
    target_role: string;
    career_goal: string | null;
    created_at: string;
    updated_at: string;
}
