import { lazy } from "react";
import { createBrowserRouter, Navigate } from "react-router-dom";
import GuestLayout from "../layouts/GuestLayout";
import DashboardLayout from "../layouts/DashboardLayout";
import ProtectedRoute from "./ProtectedRoute";
import RouteErrorPage from "../pages/RouteErrorPage";

const HomePage = lazy(() => import("../pages/HomePage"));
const NotFoundPage = lazy(() => import("../pages/NotFoundPage"));
const OverviewPage = lazy(
  () => import("../features/overview/pages/OverviewPage"),
);
const ProfilePage = lazy(() => import("../features/profile/pages/ProfilePage"));
const SettingsPage = lazy(
  () => import("../features/profile/pages/SettingsPage"),
);
const CareerAssessmentPage = lazy(
  () => import("../features/assessment/pages/CareerAssessmentPage"),
);
const LearningTracksPage = lazy(
  () => import("../features/learning/pages/LearningTracksPage"),
);
const LearningCategoryPage = lazy(
  () => import("../features/learning/pages/LearningCategoryPage"),
);
const SubjectLevelsPage = lazy(
  () => import("../features/learning/pages/SubjectLevelsPage"),
);
const LevelContentPage = lazy(
  () => import("../features/learning/pages/LevelContentPage"),
);
const LevelExamPage = lazy(
  () => import("../features/learning/pages/LevelExamPage"),
);
const TrackDetailsPage = lazy(
  () => import("../features/learning/pages/TrackDetailsPage"),
);
const TopicPage = lazy(() => import("../features/learning/pages/TopicPage"));
const LessonPage = lazy(() => import("../features/learning/pages/LessonPage"));
const PracticeHomePage = lazy(
  () => import("../features/practice/pages/PracticeHomePage"),
);
const PracticeFsdPage = lazy(
  () => import("../features/practice/pages/PracticeFsdPage"),
);
const PracticeFsdArenaPage = lazy(
  () => import("../features/practice/pages/PracticeFsdArenaPage"),
);
const PracticeDatabasesPage = lazy(
  () => import("../features/practice/pages/PracticeDatabasesPage"),
);
const PracticeLevelPage = lazy(
  () => import("../features/practice/pages/PracticeLevelPage"),
);
const PracticeSessionPage = lazy(
  () => import("../features/practice/pages/PracticeSessionPage"),
);
const AssessmentResultPage = lazy(
  () => import("../features/assessment/pages/AssessmentResultPage"),
);
const PlaygroundPage = lazy(
  () => import("../features/playground/pages/PlaygroundPage"),
);
const AuthCallbackPage = lazy(
  () => import("../features/auth/pages/AuthCallbackPage"),
);
const ForgotPasswordPage = lazy(
  () => import("../features/auth/pages/ForgotPasswordPage"),
);
const ResetPasswordPage = lazy(
  () => import("../features/auth/pages/ResetPasswordPage"),
);

const router = createBrowserRouter([
  // Old auth paths — redirect to landing page modal
  { path: "/auth/login", element: <Navigate to="/?modal=login" replace /> },
  {
    path: "/auth/register",
    element: <Navigate to="/?modal=register" replace />,
  },
  { path: "/auth", element: <Navigate to="/?modal=login" replace /> },
  { path: "/auth/callback", element: <AuthCallbackPage /> },

  // Password reset pages (public, standalone)
  { path: "/auth/forgot-password", element: <ForgotPasswordPage /> },
  { path: "/reset-password",       element: <ResetPasswordPage /> },

  // Public routes — landing page with modal auth
  {
    path: "/",
    element: <GuestLayout />,
    children: [{ index: true, element: <HomePage /> }],
  },

  // Protected routes — sidebar dashboard layout
  {
    element: <ProtectedRoute />,
    errorElement: <RouteErrorPage />,
    children: [
      {
        element: <DashboardLayout />,
        errorElement: <RouteErrorPage />,
        children: [
          { path: "/dashboard", element: <OverviewPage /> },
          { path: "/profile", element: <ProfilePage /> },
          { path: "/settings", element: <SettingsPage /> },
          { path: "/assessment", element: <CareerAssessmentPage /> },
          // Learning — new level-based structure
          { path: "/learning", element: <LearningTracksPage /> },
          { path: "/learning/:category", element: <LearningCategoryPage /> },
          {
            path: "/learning/:category/:subjectSlug",
            element: <SubjectLevelsPage />,
          },
          {
            path: "/learning/:category/:subjectSlug/:level",
            element: <LevelContentPage />,
          },
          {
            path: "/learning/:category/:subjectSlug/:level/exam",
            element: <LevelExamPage />,
          },
          // Legacy track routes — kept for backward compatibility
          { path: "/tracks", element: <LearningTracksPage /> },
          { path: "/tracks/:trackId", element: <TrackDetailsPage /> },
          {
            path: "/tracks/:trackId/subjects/:subjectId",
            element: <TopicPage />,
          },
          { path: "/lessons/:lessonId", element: <LessonPage /> },
          { path: "/practice", element: <PracticeHomePage /> },
          { path: "/practice/fsd", element: <PracticeFsdPage /> },
          { path: "/practice/fsd/:arena", element: <PracticeFsdArenaPage /> },
          { path: "/practice/databases", element: <PracticeDatabasesPage /> },
          {
            path: "/practice/subjects/:subjectId",
            element: <PracticeLevelPage />,
          },
          {
            path: "/practice/topics/:topicId",
            element: <PracticeSessionPage />,
          },
          {
            path: "/practice/results/:attemptId",
            element: <AssessmentResultPage />,
          },
          { path: "/progress", element: <OverviewPage /> },
          { path: "/playground", element: <PlaygroundPage /> },
        ],
      },
    ],
  },

  // 404
  { path: "*", element: <NotFoundPage /> },
]);

export default router;
