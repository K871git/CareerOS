import { createBrowserRouter, Navigate } from 'react-router-dom';
import GuestLayout from '../layouts/GuestLayout';
import DashboardLayout from '../layouts/DashboardLayout';
import ProtectedRoute from './ProtectedRoute';
import HomePage from '../pages/HomePage';
import NotFoundPage from '../pages/NotFoundPage';
import OverviewPage from '../features/overview/pages/OverviewPage';
import ProfilePage from '../features/profile/pages/ProfilePage';
import SettingsPage from '../features/profile/pages/SettingsPage';
import CareerAssessmentPage from '../features/assessment/pages/CareerAssessmentPage';
import LearningTracksPage from '../features/learning/pages/LearningTracksPage';
import LearningCategoryPage from '../features/learning/pages/LearningCategoryPage';
import SubjectLevelsPage from '../features/learning/pages/SubjectLevelsPage';
import LevelContentPage from '../features/learning/pages/LevelContentPage';
import LevelExamPage from '../features/learning/pages/LevelExamPage';
import TrackDetailsPage from '../features/learning/pages/TrackDetailsPage';
import TopicPage from '../features/learning/pages/TopicPage';
import LessonPage from '../features/learning/pages/LessonPage';
import PracticeHomePage from '../features/practice/pages/PracticeHomePage';
import PracticeFsdPage from '../features/practice/pages/PracticeFsdPage';
import PracticeFsdArenaPage from '../features/practice/pages/PracticeFsdArenaPage';
import PracticeDatabasesPage from '../features/practice/pages/PracticeDatabasesPage';
import PracticeLevelPage from '../features/practice/pages/PracticeLevelPage';
import PracticeSessionPage from '../features/practice/pages/PracticeSessionPage';
import AssessmentResultPage from '../features/assessment/pages/AssessmentResultPage';
import PlaygroundPage from '../features/playground/pages/PlaygroundPage';

const router = createBrowserRouter([
    // Old auth paths — redirect to landing page modal
    { path: '/auth/login',          element: <Navigate to="/?modal=login" replace /> },
    { path: '/auth/register',       element: <Navigate to="/?modal=register" replace /> },
    { path: '/auth/forgot-password',element: <Navigate to="/?modal=login" replace /> },
    { path: '/auth',                element: <Navigate to="/?modal=login" replace /> },

    // Public routes — landing page with modal auth
    {
        path: '/',
        element: <GuestLayout />,
        children: [
            { index: true, element: <HomePage /> },
        ],
    },

    // Protected routes — sidebar dashboard layout
    {
        element: <ProtectedRoute />,
        children: [
            {
                element: <DashboardLayout />,
                children: [
                    { path: '/dashboard',  element: <OverviewPage /> },
                    { path: '/profile',    element: <ProfilePage /> },
                    { path: '/settings',   element: <SettingsPage /> },
                    { path: '/assessment', element: <CareerAssessmentPage /> },
                    // Learning — new level-based structure
                    { path: '/learning',                                                          element: <LearningTracksPage /> },
                    { path: '/learning/:category',                                                element: <LearningCategoryPage /> },
                    { path: '/learning/:category/:subjectSlug',                                   element: <SubjectLevelsPage /> },
                    { path: '/learning/:category/:subjectSlug/:level',                            element: <LevelContentPage /> },
                    { path: '/learning/:category/:subjectSlug/:level/exam',                       element: <LevelExamPage /> },
                    // Legacy track routes — kept for backward compatibility
                    { path: '/tracks',                                        element: <LearningTracksPage /> },
                    { path: '/tracks/:trackId',                               element: <TrackDetailsPage /> },
                    { path: '/tracks/:trackId/subjects/:subjectId',           element: <TopicPage /> },
                    { path: '/lessons/:lessonId',                             element: <LessonPage /> },
                    { path: '/practice',                                      element: <PracticeHomePage /> },
                    { path: '/practice/fsd',                                  element: <PracticeFsdPage /> },
                    { path: '/practice/fsd/:arena',                           element: <PracticeFsdArenaPage /> },
                    { path: '/practice/databases',                            element: <PracticeDatabasesPage /> },
                    { path: '/practice/subjects/:subjectId',                  element: <PracticeLevelPage /> },
                    { path: '/practice/topics/:topicId',                      element: <PracticeSessionPage /> },
                    { path: '/practice/results/:attemptId',                   element: <AssessmentResultPage /> },
                    { path: '/progress',                                      element: <OverviewPage /> },
                    { path: '/playground',                                    element: <PlaygroundPage /> },
                ],
            },
        ],
    },

    // 404
    { path: '*', element: <NotFoundPage /> },
]);

export default router;
