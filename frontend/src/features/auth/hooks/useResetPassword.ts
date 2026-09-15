import { useMutation } from '@tanstack/react-query';
import api from '../../../api/axios';

interface ResetPasswordPayload {
    token:                 string;
    email:                 string;
    password:              string;
    password_confirmation: string;
}

export function useResetPassword() {
    return useMutation({
        mutationFn: (payload: ResetPasswordPayload) =>
            api.post('/v1/auth/reset-password', payload),
    });
}
