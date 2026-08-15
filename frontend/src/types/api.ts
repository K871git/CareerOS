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
    display_order: number;
    // These fields are not returned by the current backend
    level?: TrackLevel;
    total_topics?: number;
    progress_percentage?: number;
    enrolled?: boolean;
}

export interface Subject {
    id: number;
    title: string;
    slug: string;
    description: string;
    display_order: number;
    mcq_question_count: number;
}

export interface Topic {
    id: number;
    title: string;
    slug: string;
    description: string;
    display_order: number;
    level: number;
    is_locked: boolean;
    is_completed: boolean;
    best_score: number;
}

export interface LevelStatus {
    level: number;
    locked: boolean;
    completed: boolean;
    score: number | null;
}

export interface ExamResult {
    score: number;
    total: number;
    passed: boolean;
}

export interface Lesson {
    id: number;
    title: string;
    content: string;
    estimated_minutes: number | null;
    display_order: number;
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

export interface QuestionOption {
    id: number;
    option_text: string;
}

export type QuestionDifficulty = 'Easy' | 'Medium' | 'Hard';
export type QuestionType = 'MCQ' | 'THEORY';

export interface MCQQuestion {
    id: number;
    type: QuestionType;
    difficulty: QuestionDifficulty;
    question: string;
    options: QuestionOption[];
}

export interface AssessmentAnswerResult {
    question_id: number;
    question: string;
    selected_option_id: number | null;
    selected_option: string | null;
    is_correct: boolean;
    correct_option: string | null;
    explanation: string | null;
}

export interface AssessmentAttemptResult {
    id: number;
    topic_id: number | null;
    subject_id: number | null;
    score: number;
    total_questions: number;
    percentage: number;
    submitted_at: string;
    answers: AssessmentAnswerResult[];
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

export interface LessonCompletionRecord {
    id: number;
    lesson_id: number;
    lesson_title: string;
    status: LessonStatus;
    completed_at: string | null;
}

export interface TheoryQuestion {
    id: number;
    type: QuestionType;
    difficulty: QuestionDifficulty;
    question: string;
}

export type TheoryAnswerStatus = 'pending_review' | 'reviewed';

// --- Dashboard Overview ---

export interface DashboardSummary {
    lessons_completed: number;
    lessons_total: number;
    lessons_percentage: number;
    quizzes_taken: number;
    total_questions_answered: number;
    total_correct: number;
    accuracy: number;
    avg_quiz_score: number;
}

export interface QuizBySubject {
    subject_id: number;
    subject_title: string;
    attempts: number;
    total_questions: number;
    total_correct: number;
    avg_score: number;
}

export interface WeakArea {
    topic_id: number;
    topic_title: string;
    topic_slug: string;
    subject_title: string;
    attempts: number;
    avg_score: number;
}

export type RecommendationType = 'weak_topic' | 'get_started' | 'explore';

export interface Recommendation {
    type: RecommendationType;
    title: string;
    description: string;
    topic_id: number | null;
    topic_slug: string | null;
    subject_title: string | null;
}

export interface RecentAttempt {
    attempt_id: number;
    topic_title: string;
    subject_title: string;
    score: number;
    total_questions: number;
    percentage: number;
    submitted_at: string;
}

export interface DashboardActivity {
    type: string;
    description: string;
    subject_name: string | null;
    created_at: string;
}

export interface DashboardOverview {
    summary: DashboardSummary;
    quiz_by_subject: QuizBySubject[];
    weak_areas: WeakArea[];
    recommendations: Recommendation[];
    recent_attempts: RecentAttempt[];
    recent_activity: DashboardActivity[];
}

export interface TheoryAnswer {
    id: number;
    question_id: number;
    question: string;
    answer: string;
    status: TheoryAnswerStatus;
    score: number | null;
    feedback: string | null;
    explanation: string | null;
    submitted_at: string;
}
