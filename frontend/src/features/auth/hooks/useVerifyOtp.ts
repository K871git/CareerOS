import { useMutation } from '@tanstack/react-query';
import { useNavigate } from 'react-router-dom';
import toast from 'react-hot-toast';
import { useAuth } from '../../../store/authStore';
import { authService } from '../services/authService';
import type { VerifyOtpPayload } from '../types';

export function useVerifyOtp() {
    const { login } = useAuth();
    const navigate  = useNavigate();

    return useMutation({
        mutationFn: (payload: VerifyOtpPayload) => authService.verifyOtp(payload),
        onSuccess: ({ data: res }) => {
            login(res.data.user, res.data.token);
            toast.success('Welcome back!');
            navigate('/dashboard');
        },
    });
}
