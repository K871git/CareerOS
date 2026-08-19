import api from '../../../api/axios';
import type { ApiResponse, AuthTokenResponse } from '../../../types/api';
import type { LoginCredentials, OtpSendData, RegisterCredentials, SendOtpPayload, VerifyOtpPayload } from '../types';

export const authService = {
    login: (credentials: LoginCredentials) =>
        api.post<ApiResponse<AuthTokenResponse>>('/v1/auth/login', credentials),

    register: (credentials: RegisterCredentials) =>
        api.post<ApiResponse<AuthTokenResponse>>('/v1/auth/register', credentials),

    sendOtp: (payload: SendOtpPayload) =>
        api.post<ApiResponse<OtpSendData>>('/v1/auth/otp/send', payload),

    verifyOtp: (payload: VerifyOtpPayload) =>
        api.post<ApiResponse<AuthTokenResponse>>('/v1/auth/otp/verify', payload),

    logout: () =>
        api.post<ApiResponse<null>>('/v1/auth/logout'),

    me: () =>
        api.get<ApiResponse<{ user: AuthTokenResponse['user'] }>>('/v1/auth/me'),
};
