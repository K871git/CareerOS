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
