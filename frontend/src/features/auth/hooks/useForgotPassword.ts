import { useMutation } from '@tanstack/react-query';
import { isAxiosError } from 'axios';
import api from '../../../api/axios';

export function useForgotPassword() {
    return useMutation({
        mutationFn: (email: string) =>
            api.post('/v1/auth/forgot-password', { email }),
        onError: (error) => {
            if (isAxiosError(error)) {
                // 422 / 429 — surface to the form via isError
            }
        },
    });
}
