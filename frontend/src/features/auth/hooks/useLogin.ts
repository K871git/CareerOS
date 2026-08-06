import { useMutation } from '@tanstack/react-query';
import { useNavigate } from 'react-router-dom';
import toast from 'react-hot-toast';
import { useAuth } from '../../../store/authStore';
import { authService } from '../services/authService';
import type { LoginFormData } from '../schemas';

export function useLogin() {
    const { login } = useAuth();
    const navigate = useNavigate();

    return useMutation({
        mutationFn: (data: LoginFormData) => authService.login(data),
        onSuccess: ({ data: res }) => {
            login(res.data.user, res.data.token);
            toast.success('Welcome back!');
            navigate('/');
        },
        onError: (error: any) => {
            const message = error.response?.data?.message ?? 'Login failed. Please try again.';
            toast.error(message);
        },
    });
}
