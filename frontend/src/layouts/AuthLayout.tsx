import { Navigate, Outlet } from 'react-router-dom';
import { useAuth } from '../store/authStore';

export default function AuthLayout() {
    const { state } = useAuth();

    if (state.isAuthenticated) {
        return <Navigate to="/dashboard" replace />;
    }

    return <Outlet />;
}
