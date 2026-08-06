import { Navigate, Outlet } from 'react-router-dom';
import { useAuth } from '../store/authStore';

export default function ProtectedRoute() {
    const { state } = useAuth();

    if (!state.isAuthenticated) {
        return <Navigate to="/login" replace />;
    }

    return <Outlet />;
}
