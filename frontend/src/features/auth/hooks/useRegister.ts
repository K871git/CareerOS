import { useMutation } from '@tanstack/react-query';
import { useNavigate } from 'react-router-dom';
import toast from 'react-hot-toast';
import { useAuth } from '../../../store/authStore';
import { authService } from '../services/authService';
import type { RegisterFormData } from '../schemas';

export function useRegister() {
    const { login } = useAuth();
    const navigate = useNavigate();

    return useMutation({
        mutationFn: (data: RegisterFormData) => authService.register(data),
        onSuccess: ({ data: res }) => {
            login(res.data.user, res.data.token);
            toast.success('Account created! Welcome to CareerOS.');
            navigate('/dashboard');
        },
        onError: (error: any) => {
            const message = error.response?.data?.message ?? 'Registration failed. Please try again.';
            toast.error(message);
        },
    });
}
