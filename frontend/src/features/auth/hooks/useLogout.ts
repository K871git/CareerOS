import { useMutation } from '@tanstack/react-query';
import { useNavigate } from 'react-router-dom';
import toast from 'react-hot-toast';
import { useAuth } from '../../../store/authStore';
import { authService } from '../services/authService';
import queryClient from '../../../api/queryClient';

export function useLogout() {
    const { logout } = useAuth();
    const navigate = useNavigate();

    return useMutation({
        mutationFn: () => authService.logout(),
        onSettled: () => {
            logout();
            queryClient.clear();
            toast.success('Logged out successfully.');
            navigate('/?modal=login');
        },
    });
}
