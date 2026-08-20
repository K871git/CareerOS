import { useMutation } from '@tanstack/react-query';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../../../store/authStore';
import { useAuthOverlay } from '../../../contexts/AuthOverlayContext';
import { authService } from '../services/authService';
import type { LoginFormData } from '../schemas';

export function useLogin() {
    const { login } = useAuth();
    const navigate  = useNavigate();
    const { showWelcome } = useAuthOverlay();

    return useMutation({
        mutationFn: (data: LoginFormData) => authService.login(data),
        onSuccess: ({ data: res }) => {
            const { user, token } = res.data;
            // Show welcome overlay, then commit auth state and navigate.
            // Delaying login() keeps the user on the login page during the overlay
            // so there is no jarring layout flash before navigation.
            showWelcome(user.name, () => {
                login(user, token);
                navigate('/dashboard');
            });
        },
        onError: () => {},
    });
}
