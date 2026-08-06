import api from '../../../api/axios';
import type { ApiResponse, AuthTokenResponse } from '../../../types/api';
import type { LoginCredentials, RegisterCredentials } from '../types';

export const authService = {
    login: (credentials: LoginCredentials) =>
        api.post<ApiResponse<AuthTokenResponse>>('/v1/auth/login', credentials),

    register: (credentials: RegisterCredentials) =>
        api.post<ApiResponse<AuthTokenResponse>>('/v1/auth/register', credentials),

    logout: () =>
        api.post<ApiResponse<null>>('/v1/auth/logout'),

    me: () =>
        api.get<ApiResponse<{ user: AuthTokenResponse['user'] }>>('/v1/auth/me'),
};
