import { Link } from 'react-router-dom';
import { BookOpen, AlertTriangle, Lightbulb } from 'lucide-react';
import { useAuth } from '../../../store/authStore';
import { useDashboardOverview, useOverallProgress, useRecentActivityFeed } from '../hooks/useOverview';
import TrackProgress from '../components/TrackProgress';
import RecentActivityList from '../components/RecentActivity';
import { SkeletonCard, SkeletonSections, SkeletonStats } from '../components/OverviewSkeletons';
import { WelcomeBanner, getUserJourneyState, getWelcomeConfig } from '../components/WelcomeBanner';
import { OnboardingCard } from '../components/OnboardingCard';
import { StatsRow } from '../components/StatsRow';
import { SectionCards } from '../components/SectionCards';
import { SubjectPerformance } from '../components/SubjectPerformance';
import { RecentAttemptsList } from '../components/RecentAttemptsList';
import { SkillLevelCard } from '../components/SkillLevelCard';
import { AchievementsCard, buildBadges } from '../components/AchievementsCard';
import { WeakAreasList } from '../components/WeakAreasList';
import { RecommendationsList } from '../components/RecommendationsList';
import type { ProgressTrackItem, QuizBySubject } from '../../../types/api';
import '../overview.css';

function LearningTracksSection({
    tracks,
    quizBySubject,
}: {
    tracks: ProgressTrackItem[];
    quizBySubject: QuizBySubject[];
}) {
    if (tracks.length === 0) {
        return (
            <div className="dash-empty">
                <BookOpen size={34} className="dash-empty-icon" />
                <p>No learning tracks found. <Link to="/learning">Browse all</Link>.</p>
            </div>
        );
    }
    return (
        <div className="tcard-list">
            {tracks.map(track => (
                <TrackProgress key={track.id} track={track} quizBySubject={quizBySubject} />
            ))}
        </div>
    );
}

export default function OverviewPage() {
    const { state } = useAuth();
    const firstName = state.user?.name?.split(' ')[0] ?? 'there';
    const { data: overview, isLoading: dashLoading } = useDashboardOverview();
    const { data: progress, isLoading: progressLoading } = useOverallProgress();
    const { data: activity, isLoading: activityLoading } = useRecentActivityFeed();

    const isLoading = dashLoading;
    const weakCount = overview?.weak_areas.length ?? 0;
    const hasQuizData = (overview?.summary.quizzes_taken ?? 0) > 0;

    const journeyState = overview ? getUserJourneyState(overview.summary) : 'new';
    const welcomeConfig = overview
        ? getWelcomeConfig(journeyState, firstName, overview.summary, overview.profile ?? null, weakCount)
        : { title: `Welcome, ${firstName}!`, subtitle: 'Loading your progress...', cta: { label: 'Start Quiz', to: '/practice' } };

    const badges = overview
        ? buildBadges(overview.summary, overview.weak_areas, overview.quiz_by_subject)
        : [];

    const showOnboarding = !isLoading && overview && (journeyState === 'new' || journeyState === 'starter');

    return (
        <div className="overview">

            {/* Welcome banner */}
            {isLoading ? (
                <div className="skeleton" style={{ height: 72, borderRadius: 16, marginBottom: '1.75rem' }} />
            ) : (
                <WelcomeBanner journeyState={journeyState} config={welcomeConfig} profile={overview?.profile ?? null} />
            )}

            {/* Onboarding checklist — new + starter only */}
            {showOnboarding && (
                <OnboardingCard summary={overview!.summary} profile={overview!.profile ?? null} />
            )}

            {/* Stats row */}
            {isLoading || !overview ? <SkeletonStats /> : <StatsRow summary={overview.summary} />}

            {/* Section shortcuts */}
            {isLoading || !overview ? <SkeletonSections /> : <SectionCards summary={overview.summary} />}

            {/* ── Main grid — named grid-areas, responsive ── */}
            <div className="dash-grid">

                {/* Learning Tracks */}
                <div className="dash-card dash-area--tracks">
                    <div className="dash-card-header">
                        <h2 className="dash-card-title">Learning Tracks</h2>
                        <Link to="/learning" className="dash-card-link">Browse all</Link>
                    </div>
                    {progressLoading
                        ? <SkeletonCard rows={3} />
                        : <LearningTracksSection
                            tracks={progress?.tracks ?? []}
                            quizBySubject={overview?.quiz_by_subject ?? []}
                        />}
                </div>

                {/* Subject Performance */}
                <div className="dash-card dash-area--perf">
                    <div className="dash-card-header">
                        <h2 className="dash-card-title">Performance by Subject</h2>
                        {hasQuizData && <span className="dash-card-meta">{overview!.summary.avg_quiz_score}% overall avg</span>}
                    </div>
                    {isLoading ? <SkeletonCard rows={3} /> : <SubjectPerformance subjects={overview?.quiz_by_subject ?? []} />}
                </div>

                {/* Recent Quiz Attempts */}
                <div className="dash-card dash-area--attempts">
                    <div className="dash-card-header">
                        <h2 className="dash-card-title">Recent Quiz Attempts</h2>
                        <Link to="/practice" className="dash-card-link">Practice more</Link>
                    </div>
                    {isLoading ? <SkeletonCard rows={4} /> : <RecentAttemptsList attempts={overview?.recent_attempts ?? []} />}
                </div>

                {/* Recent Activity */}
                <div className="dash-card dash-area--activity">
                    <div className="dash-card-header">
                        <h2 className="dash-card-title">Recent Activity</h2>
                    </div>
                    {activityLoading ? <SkeletonCard rows={3} /> : <RecentActivityList items={activity ?? []} />}
                </div>

                {/* Skill Level — component renders its own dash-card */}
                <div className="dash-area--skill">
                    {isLoading ? (
                        <div className="dash-card"><SkeletonCard rows={4} /></div>
                    ) : (
                        <SkillLevelCard summary={overview!.summary} userSkills={overview?.user_skills ?? []} />
                    )}
                </div>

                {/* Achievements */}
                <div className="dash-area--achieve">
                    {isLoading
                        ? <div className="dash-card"><SkeletonCard rows={3} /></div>
                        : overview
                            ? <AchievementsCard badges={badges} />
                            : null}
                </div>

                {/* Weak Areas */}
                <div className="dash-card dash-area--weak">
                    <div className="dash-card-header">
                        <h2 className="dash-card-title">
                            Weak Areas
                            {weakCount > 0 && <span className="dash-weak-badge">{weakCount}</span>}
                        </h2>
                        <AlertTriangle size={15} className={weakCount > 0 ? 'dash-warn-icon' : 'dash-ok-icon'} />
                    </div>
                    {isLoading ? <SkeletonCard rows={3} /> : <WeakAreasList areas={overview?.weak_areas ?? []} />}
                </div>

                {/* Recommendations */}
                <div className="dash-card dash-area--recs">
                    <div className="dash-card-header">
                        <h2 className="dash-card-title">Recommended Next Steps</h2>
                        <Lightbulb size={15} className="dash-rec-icon" />
                    </div>
                    {isLoading ? <SkeletonCard rows={2} /> : <RecommendationsList items={overview?.recommendations ?? []} />}
                </div>

            </div>
        </div>
    );
}
