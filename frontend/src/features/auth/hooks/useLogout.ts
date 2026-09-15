import { useCallback, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../../../store/authStore';
import { useAuthOverlay } from '../../../contexts/AuthOverlayContext';
import { authService } from '../services/authService';
import queryClient from '../../../api/queryClient';

export function useLogout() {
    const { state, logout } = useAuth();
    const navigate          = useNavigate();
    const { showGoodbye }   = useAuthOverlay();
    const [isPending, setIsPending] = useState(false);

    const mutate = useCallback(() => {
        if (isPending) return;
        setIsPending(true);

        const name = state.user?.name ?? '';

        // Show goodbye overlay. After it fades out, fire the API and clear state.
        // API call is fire-and-forget — we clear auth regardless of the response.
        showGoodbye(name, () => {
            // Clear auth + navigate before waiting for API — prevents flash of protected page
            logout();
            queryClient.clear();
            // Clear all non-auth localStorage keys so the next user starts fresh.
            const keysToRemove = Object.keys(localStorage).filter(
                (k) => !['careeros_token', 'careeros_user'].includes(k)
            );
            keysToRemove.forEach((k) => localStorage.removeItem(k));
            navigate('/');
            // Tell server to invalidate token — genuinely fire-and-forget
            authService.logout().catch(() => {});
        });
    }, [isPending, state.user, showGoodbye, logout, navigate]);

    return { mutate, isPending };
}
