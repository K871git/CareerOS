import { Navigate, Outlet } from 'react-router-dom';
import { useAuth } from '../store/authStore';

export default function ProtectedRoute() {
    const { state } = useAuth();

    if (state.isValidating) {
        return (
            <div style={{
                display: 'flex', alignItems: 'center', justifyContent: 'center',
                height: '100vh', color: 'var(--color-text-muted, #6b7280)', fontSize: '0.875rem',
            }}>
                Loading…
            </div>
        );
    }

    if (!state.isAuthenticated) {
        return <Navigate to="/?modal=login" replace />;
    }

    return <Outlet />;
}
