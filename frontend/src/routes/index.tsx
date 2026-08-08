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
import LearningTracksPage from '../features/learning/pages/LearningTracksPage';
import TrackDetailsPage from '../features/learning/pages/TrackDetailsPage';
import TopicPage from '../features/learning/pages/TopicPage';
import LessonPage from '../features/learning/pages/LessonPage';
import PracticeHomePage from '../features/practice/pages/PracticeHomePage';
import PracticeLevelPage from '../features/practice/pages/PracticeLevelPage';
import PracticeSessionPage from '../features/practice/pages/PracticeSessionPage';
import AssessmentResultPage from '../features/assessment/pages/AssessmentResultPage';
import TheoryQuestionsPage from '../features/theory/pages/TheoryQuestionsPage';
import TheoryAnswerPage from '../features/theory/pages/TheoryAnswerPage';
import ProgressPage from '../features/progress/pages/ProgressPage';

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
                    { path: '/dashboard',  element: <DashboardPage /> },
                    { path: '/profile',    element: <ProfilePage /> },
                    { path: '/assessment', element: <CareerAssessmentPage /> },
                    { path: '/tracks',                                        element: <LearningTracksPage /> },
                    { path: '/tracks/:trackId',                               element: <TrackDetailsPage /> },
                    { path: '/tracks/:trackId/subjects/:subjectId',           element: <TopicPage /> },
                    { path: '/lessons/:lessonId',                             element: <LessonPage /> },
                    { path: '/practice',                                      element: <PracticeHomePage /> },
                    { path: '/practice/subjects/:subjectId',                  element: <PracticeLevelPage /> },
                    { path: '/practice/topics/:topicId',                      element: <PracticeSessionPage /> },
                    { path: '/practice/results/:attemptId',                   element: <AssessmentResultPage /> },
                    { path: '/theory',                                        element: <TheoryQuestionsPage /> },
                    { path: '/theory/answers/:answerId',                      element: <TheoryAnswerPage /> },
                    { path: '/progress',                                      element: <ProgressPage /> },
                ],
            },
        ],
    },
    // 404
    { path: '*', element: <NotFoundPage /> },
]);

export default router;
