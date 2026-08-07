import api from '../../../api/axios';
import type { ApiResponse, UserProfile } from '../../../types/api';
import type { ProfileFormData } from '../schemas';

export const profileService = {
    get: () =>
        api.get<ApiResponse<UserProfile>>('/v1/profile'),

    update: (data: ProfileFormData) =>
        api.put<ApiResponse<UserProfile>>('/v1/profile', data),
};
