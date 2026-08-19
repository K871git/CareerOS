import { useMutation } from '@tanstack/react-query';
import { authService } from '../services/authService';

export function useSendOtp() {
    return useMutation({
        mutationFn: (email: string) => authService.sendOtp({ email }),
    });
}
