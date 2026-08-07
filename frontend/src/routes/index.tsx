import { createBrowserRouter, Navigate } from 'react-router-dom';
import AuthLayout from '../layouts/AuthLayout';
import GuestLayout from '../layouts/GuestLayout';
import DashboardLayout from '../layouts/DashboardLayout';
import ProtectedRoute from './ProtectedRoute';
import HomePage from '../pages/HomePage';
import NotFoundPage from '../pages/NotFoundPage';
import LoginPage from '../features/auth/pages/LoginPage';
import RegisterPage from '../features/auth/pages/RegisterPage';
import ForgotPasswordPage from '../features/auth/pages/ForgotPasswordPage';
import DashboardPage from '../features/dashboard/pages/DashboardPage';
import ProfilePage from '../features/profile/pages/ProfilePage';
import CareerAssessmentPage from '../features/assessment/pages/CareerAssessmentPage';

const router = createBrowserRouter([
    // Auth routes — each page handles its own card layout
    {
        path: '/auth',
        element: <AuthLayout />,
        children: [
            { index: true, element: <Navigate to="/auth/login" replace /> },
            { path: 'login', element: <LoginPage /> },
            { path: 'register', element: <RegisterPage /> },
            { path: 'forgot-password', element: <ForgotPasswordPage /> },
        ],
    },
    // Public routes — header + footer
    {
        path: '/',
        element: <GuestLayout />,
        children: [
            { index: true, element: <HomePage /> },
            { path: 'login', element: <Navigate to="/auth/login" replace /> },
        ],
    },
    // Protected routes — sidebar dashboard layout
    {
        element: <ProtectedRoute />,
        children: [
            {
                element: <DashboardLayout />,
                children: [
                    { path: '/dashboard', element: <DashboardPage /> },
                    { path: '/profile',    element: <ProfilePage /> },
                    { path: '/assessment', element: <CareerAssessmentPage /> },
                    // Future: /tracks, /practice, /progress
                ],
            },
        ],
    },
    // 404
    { path: '*', element: <NotFoundPage /> },
]);

export default router;
