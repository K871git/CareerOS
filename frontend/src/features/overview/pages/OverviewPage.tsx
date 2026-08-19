import { Link } from 'react-router-dom';
import {
    BookOpen, Target, TrendingUp, CheckCircle2, ArrowRight, AlertTriangle,
    Award, Lightbulb, Play, Brain, Clock, Zap, BarChart2, Shield, BookMarked,
    Lock, Star, Flame,
} from 'lucide-react';
import { useAuth } from '../../../store/authStore';
import { useDashboardOverview, useOverallProgress, useRecentActivityFeed } from '../hooks/useOverview';
import TrackProgress from '../components/TrackProgress';
import RecentActivityList from '../components/RecentActivity';
import { timeAgo } from '../../../utils/time';
import type {
    DashboardSummary, DashboardProfile, DashboardUserSkill,
    QuizBySubject, WeakArea, Recommendation, RecentAttempt, ProgressTrackItem,
} from '../../../types/api';
import '../overview.css';

// ── Journey State ─────────────────────────────────────────────────────────────

type UserJourneyState = 'new' | 'starter' | 'learner' | 'achiever' | 'advanced';

function getUserJourneyState(summary: DashboardSummary): UserJourneyState {
    const { quizzes_taken, lessons_completed, learning_levels_passed, accuracy } = summary;
    if (quizzes_taken === 0 && lessons_completed === 0) return 'new';
    if (learning_levels_passed >= 2 && accuracy >= 80) return 'advanced';
    if (learning_levels_passed >= 1 || lessons_completed >= 5) return 'achiever';
    if (quizzes_taken < 5) return 'starter';
    return 'learner';
}

// ── Badge System ──────────────────────────────────────────────────────────────

interface BadgeDef {
    id: string;
    label: string;
    desc: string;
    Icon: React.ElementType;
    color: 'indigo' | 'green' | 'amber' | 'violet';
    earned: boolean;
}

function buildBadges(
    summary: DashboardSummary,
    weakAreas: WeakArea[],
    subjects: QuizBySubject[],
): BadgeDef[] {
    return [
        { id: 'first_quiz',     label: 'First Quiz',     desc: 'Completed first practice quiz',        Icon: Target,    color: 'indigo', earned: summary.quizzes_taken >= 1 },
        { id: 'first_lesson',   label: 'First Lesson',   desc: 'Completed first lesson',               Icon: BookOpen,  color: 'green',  earned: summary.lessons_completed >= 1 },
        { id: 'level_champ',    label: 'Level Champion', desc: 'Passed a level exam with 10/10',       Icon: Star,      color: 'amber',  earned: summary.learning_levels_passed >= 1 },
        { id: 'sharp_mind',     label: 'Sharp Mind',     desc: '80%+ accuracy across 5+ quizzes',     Icon: Brain,     color: 'violet', earned: summary.accuracy >= 80 && summary.quizzes_taken >= 5 },
        { id: 'veteran',        label: 'Veteran',        desc: 'Answered 50+ questions in practice',  Icon: Award,     color: 'amber',  earned: summary.total_questions_answered >= 50 },
        { id: 'no_weak_spots',  label: 'No Weak Spots',  desc: 'Zero weak areas with 5+ quizzes',     Icon: Shield,    color: 'green',  earned: weakAreas.length === 0 && summary.quizzes_taken >= 5 },
        { id: 'explorer',       label: 'Explorer',       desc: 'Practiced 3+ different subjects',     Icon: Zap,       color: 'indigo', earned: subjects.length >= 3 },
        { id: 'dedicated',      label: 'Dedicated',      desc: '10+ lessons completed',               Icon: Flame,     color: 'violet', earned: summary.lessons_completed >= 10 },
    ];
}

// ── Welcome Config ────────────────────────────────────────────────────────────

interface WelcomeConfig {
    title: string;
    subtitle: string;
    cta: { label: string; to: string };
}

function getWelcomeConfig(
    state: UserJourneyState,
    firstName: string,
    summary: DashboardSummary,
    profile: DashboardProfile | null,
    weakCount: number,
): WelcomeConfig {
    const role = profile?.target_role;
    switch (state) {
        case 'new':
            return {
                title: `Welcome to CareerOS, ${firstName}!`,
                subtitle: 'Start your journey — take your first quiz to see where you stand as an engineer.',
                cta: { label: 'Take First Quiz', to: '/practice' },
            };
        case 'starter':
            return {
                title: `Good start, ${firstName}!`,
                subtitle: `${summary.quizzes_taken} quiz${summary.quizzes_taken !== 1 ? 'zes' : ''} done · ${summary.accuracy}% accuracy${role ? ` · Targeting ${role}` : ''}. Keep the momentum going.`,
                cta: { label: 'Practice More', to: '/practice' },
            };
        case 'learner':
            return {
                title: `Keep building, ${firstName}!`,
                subtitle: role
                    ? `Preparing for ${role} · ${weakCount > 0 ? `${weakCount} area${weakCount > 1 ? 's' : ''} to sharpen` : 'All topics above 70%'}`
                    : `${summary.lessons_completed} lessons · ${summary.quizzes_taken} quizzes · ${summary.accuracy}% accuracy. Consistency wins.`,
                cta: { label: 'Continue Learning', to: '/learning' },
            };
        case 'achiever':
            return {
                title: `Great progress, ${firstName}!`,
                subtitle: `${summary.learning_levels_passed} level${summary.learning_levels_passed !== 1 ? 's' : ''} passed · ${summary.accuracy}% accuracy · ${summary.lessons_completed} lessons. You're ahead of the curve.`,
                cta: { label: 'Next Level', to: '/learning' },
            };
        case 'advanced':
            return {
                title: `Outstanding, ${firstName}!`,
                subtitle: `${summary.accuracy}% accuracy across ${summary.quizzes_taken} quizzes · ${summary.learning_levels_passed} levels mastered. Keep pushing.`,
                cta: { label: 'Push Further', to: '/practice' },
            };
    }
}

// ── Score helpers ─────────────────────────────────────────────────────────────

function scoreColor(pct: number) { return pct >= 70 ? 'score-good' : pct >= 50 ? 'score-warn' : 'score-bad'; }
function scoreBarColor(pct: number) { return pct >= 70 ? 'bar-good' : pct >= 50 ? 'bar-warn' : 'bar-bad'; }

// ── Skeletons ─────────────────────────────────────────────────────────────────

function SkeletonStats() {
    return (
        <div className="dash-stats">
            {[0, 1, 2, 3].map(i => <div key={i} className="skeleton skeleton-stat" />)}
        </div>
    );
}

function SkeletonSections() {
    return (
        <div className="dash-section-cards">
            {[0, 1].map(i => <div key={i} className="skeleton" style={{ height: 82, borderRadius: 16 }} />)}
        </div>
    );
}

function SkeletonCard({ rows = 3 }: { rows?: number }) {
    return (
        <div>
            {Array.from({ length: rows }).map((_, i) => (
                <div key={i} className="skeleton skeleton-block"
                    style={{ height: 48, marginBottom: 10, width: i === rows - 1 ? '70%' : '100%' }} />
            ))}
        </div>
    );
}

// ── Welcome Banner ────────────────────────────────────────────────────────────

function WelcomeBanner({
    journeyState,
    config,
    profile,
}: {
    journeyState: UserJourneyState;
    config: WelcomeConfig;
    profile: DashboardProfile | null;
}) {
    return (
        <div className={`dash-welcome dash-welcome--${journeyState}`}>
            <div className="dash-welcome-content">
                <div className="dash-welcome-top">
                    <h1 className="dash-welcome-title">{config.title}</h1>
                    {profile?.experience_level && (
                        <span className="dash-exp-badge">{profile.experience_level}</span>
                    )}
                </div>
                <p className="dash-welcome-sub">{config.subtitle}</p>
            </div>
            <Link to={config.cta.to} className="dash-continue-btn">
                <Play size={13} /> {config.cta.label} <ArrowRight size={13} />
            </Link>
        </div>
    );
}

// ── Onboarding Card (new + starter users only) ────────────────────────────────

function OnboardingCard({
    summary,
    profile,
}: {
    summary: DashboardSummary;
    profile: DashboardProfile | null;
}) {
    const steps = [
        {
            label: 'Take your first quiz',
            desc: 'Find out which topics you already know',
            to: '/practice',
            Icon: Target,
            done: summary.quizzes_taken > 0,
        },
        {
            label: 'Complete a lesson',
            desc: 'Learn engineering concepts step by step',
            to: '/learning',
            Icon: BookOpen,
            done: summary.lessons_completed > 0,
        },
        {
            label: 'Set your career goal',
            desc: 'Tell us which engineering role you are targeting',
            to: '/profile',
            Icon: TrendingUp,
            done: !!profile?.target_role,
        },
    ];

    const doneCount = steps.filter(s => s.done).length;
    const allDone = doneCount === steps.length;

    return (
        <div className="onboard-card">
            <div className="onboard-header">
                <div>
                    <h3 className="onboard-title">{allDone ? 'Setup complete!' : 'Getting started'}</h3>
                    <p className="onboard-subtitle">
                        {allDone
                            ? 'All set — keep building your skills consistently.'
                            : 'Complete these steps to unlock your full learning journey.'}
                    </p>
                </div>
                <div className={`onboard-pill${allDone ? ' onboard-pill--done' : ''}`}>
                    {doneCount}/{steps.length}
                </div>
            </div>
            <div className="onboard-steps">
                {steps.map((step, i) => (
                    <Link key={i} to={step.to} className={`onboard-step${step.done ? ' onboard-step--done' : ''}`}>
                        <div className={`onboard-step-icon${step.done ? ' onboard-step-icon--done' : ''}`}>
                            {step.done ? <CheckCircle2 size={16} /> : <step.Icon size={16} />}
                        </div>
                        <div className="onboard-step-body">
                            <span className="onboard-step-label">{step.label}</span>
                            <span className="onboard-step-desc">{step.desc}</span>
                        </div>
                        {!step.done && <ArrowRight size={13} className="onboard-step-arrow" />}
                    </Link>
                ))}
            </div>
        </div>
    );
}

// ── Stats Row ─────────────────────────────────────────────────────────────────

function statAccuracyCtx(quizzes: number, accuracy: number): string {
    if (quizzes === 0) return 'Take a quiz first';
    if (accuracy >= 80) return 'Excellent accuracy';
    if (accuracy >= 70) return 'Above target';
    if (accuracy >= 50) return 'Needs practice';
    return 'Focus area';
}

function statQuestionsCtx(n: number): string {
    if (n === 0) return 'Start practicing';
    if (n >= 50) return 'Veteran level';
    if (n >= 10) return 'Getting there';
    return 'Building up';
}

function statLessonsCtx(n: number): string {
    if (n === 0) return 'Read your first lesson';
    if (n >= 10) return 'Dedicated learner';
    if (n >= 5) return 'Consistent';
    return 'Keep reading';
}

function statLevelsCtx(n: number): string {
    if (n === 0) return 'Score 10/10 to pass';
    if (n === 1) return 'Level 1 mastered';
    return `${n} levels mastered`;
}

interface StatCardProps {
    value: string | number;
    label: string;
    context: string;
    icon: React.ElementType;
    variant?: 'default' | 'success' | 'warning' | 'danger';
}

function StatCard({ value, label, context, icon: Icon, variant = 'default' }: StatCardProps) {
    return (
        <div className={`stat-card stat-card--${variant}`}>
            <div className="stat-icon"><Icon size={18} /></div>
            <div className="stat-body">
                <div className="stat-value">{value}</div>
                <div className="stat-label">{label}</div>
                <div className="stat-context">{context}</div>
            </div>
        </div>
    );
}

function StatsRow({ summary }: { summary: DashboardSummary }) {
    const accuracyVariant =
        summary.quizzes_taken === 0 ? 'default'
        : summary.accuracy >= 70 ? 'success'
        : summary.accuracy >= 50 ? 'warning'
        : 'danger';

    return (
        <div className="dash-stats">
            <StatCard
                value={summary.quizzes_taken === 0 ? '—' : `${summary.accuracy}%`}
                label="Quiz Accuracy"
                context={statAccuracyCtx(summary.quizzes_taken, summary.accuracy)}
                icon={Award}
                variant={accuracyVariant}
            />
            <StatCard
                value={summary.total_questions_answered}
                label="Questions Answered"
                context={statQuestionsCtx(summary.total_questions_answered)}
                icon={Brain}
            />
            <StatCard
                value={summary.lessons_completed}
                label="Lessons Completed"
                context={statLessonsCtx(summary.lessons_completed)}
                icon={CheckCircle2}
                variant={summary.lessons_completed > 0 ? 'success' : 'default'}
            />
            <StatCard
                value={summary.learning_levels_passed}
                label="Levels Passed"
                context={statLevelsCtx(summary.learning_levels_passed)}
                icon={TrendingUp}
                variant={summary.learning_levels_passed > 0 ? 'success' : 'default'}
            />
        </div>
    );
}

// ── Section Cards ─────────────────────────────────────────────────────────────

function SectionCards({ summary }: { summary: DashboardSummary }) {
    return (
        <div className="dash-section-cards">
            <Link to="/practice" className="dash-section-card">
                <div className="dash-section-icon dash-section-icon--practice"><Target size={18} /></div>
                <div className="dash-section-body">
                    <div className="dash-section-label">Practice</div>
                    <div className="dash-section-stat">
                        {summary.quizzes_taken > 0
                            ? `${summary.avg_quiz_score}% avg · ${summary.quizzes_taken} quiz${summary.quizzes_taken === 1 ? '' : 'zes'}`
                            : 'Not started yet'}
                    </div>
                    <div className="dash-section-bar">
                        <div className="dash-section-fill dash-section-fill--practice"
                            style={{ width: `${Math.min(summary.accuracy, 100)}%` }} />
                    </div>
                </div>
                <ArrowRight size={14} className="dash-section-arrow" />
            </Link>

            <Link to="/learning" className="dash-section-card">
                <div className="dash-section-icon dash-section-icon--learning"><BookOpen size={18} /></div>
                <div className="dash-section-body">
                    <div className="dash-section-label">Learning</div>
                    <div className="dash-section-stat">
                        {summary.lessons_completed > 0
                            ? `${summary.lessons_completed} lessons · ${summary.learning_levels_passed} levels passed`
                            : 'Not started yet'}
                    </div>
                    <div className="dash-section-bar">
                        <div className="dash-section-fill dash-section-fill--learning"
                            style={{ width: `${summary.lessons_percentage}%` }} />
                    </div>
                </div>
                <ArrowRight size={14} className="dash-section-arrow" />
            </Link>
        </div>
    );
}

// ── Learning Tracks ───────────────────────────────────────────────────────────

function LearningTracksSection({ tracks }: { tracks: ProgressTrackItem[] }) {
    if (tracks.length === 0) {
        return (
            <div className="dash-empty">
                <BookOpen size={34} className="dash-empty-icon" />
                <p>No tracks started yet. <Link to="/learning">Browse learning tracks</Link>.</p>
            </div>
        );
    }
    return (
        <div className="prog-tracks-list">
            {tracks.map(track => <TrackProgress key={track.id} track={track} />)}
        </div>
    );
}

// ── Subject Performance ───────────────────────────────────────────────────────

function SubjectPerformance({ subjects }: { subjects: QuizBySubject[] }) {
    if (subjects.length === 0) {
        return (
            <div className="dash-empty">
                <BarChart2 size={34} className="dash-empty-icon" />
                <p>No quiz data yet. <Link to="/practice">Start practising</Link> to see performance by subject.</p>
            </div>
        );
    }
    return (
        <div className="subject-perf-list">
            {subjects.map(s => (
                <div key={s.subject_id} className="subject-perf-row">
                    <div className="subject-perf-meta">
                        <span className="subject-perf-name">{s.subject_title}</span>
                        <span className="subject-perf-attempts">{s.attempts} {s.attempts === 1 ? 'attempt' : 'attempts'} · {s.total_questions} Qs</span>
                    </div>
                    <div className="subject-perf-bar-wrap">
                        <div className="subject-perf-bar">
                            <div className={`subject-perf-fill ${scoreBarColor(s.avg_score)}`}
                                style={{ width: `${s.avg_score}%` }} />
                        </div>
                        <span className={`subject-perf-pct ${scoreColor(s.avg_score)}`}>{s.avg_score}%</span>
                    </div>
                </div>
            ))}
        </div>
    );
}

// ── Recent Attempts ───────────────────────────────────────────────────────────

function RecentAttemptsList({ attempts }: { attempts: RecentAttempt[] }) {
    if (attempts.length === 0) {
        return (
            <div className="dash-empty">
                <Clock size={34} className="dash-empty-icon" />
                <p>No quiz attempts yet. <Link to="/practice">Take a quiz</Link> to see your history here.</p>
            </div>
        );
    }
    return (
        <ul className="attempt-list">
            {attempts.map(a => (
                <li key={a.attempt_id} className="attempt-item">
                    <div className="attempt-info">
                        <span className="attempt-topic">{a.topic_title}</span>
                        <span className="attempt-subject-tag">{a.subject_title}</span>
                    </div>
                    <div className="attempt-right">
                        <span className={`attempt-badge ${scoreColor(a.percentage)}`}>
                            {a.score}/{a.total_questions} · {a.percentage}%
                        </span>
                        <span className="attempt-time">{timeAgo(a.submitted_at)}</span>
                    </div>
                </li>
            ))}
        </ul>
    );
}

// ── Skill Level Card ──────────────────────────────────────────────────────────

const SKILL_LEVEL_NAMES = ['Beginner', 'Developing', 'Proficient', 'Advanced'];

function SkillLevelCard({ summary, userSkills }: { summary: DashboardSummary; userSkills: DashboardUserSkill[] }) {
    return (
        <div className="dash-card">
            <div className="dash-card-header">
                <h2 className="dash-card-title">Your Level</h2>
                <Shield size={15} className="dash-skill-icon" />
            </div>
            <div className="skill-level-display">
                <div className="skill-level-label">{summary.skill_label}</div>
                <div className="skill-level-bar">
                    {SKILL_LEVEL_NAMES.map((_, i) => (
                        <div key={i} className={`skill-level-seg${i < summary.skill_level ? ' skill-level-seg--active' : ''}`} />
                    ))}
                </div>
                <div className="skill-level-names">
                    {SKILL_LEVEL_NAMES.map(n => <span key={n} className="skill-level-lbl">{n}</span>)}
                </div>
                <p className="skill-level-basis">
                    {summary.skill_level === 0
                        ? 'Complete quizzes to determine your level.'
                        : `Based on ${summary.quizzes_taken} quiz${summary.quizzes_taken === 1 ? '' : 'zes'} · ${summary.accuracy}% accuracy`}
                </p>
            </div>
            {userSkills.length > 0 && (
                <div className="skill-chips-wrap">
                    <div className="skill-chips-label">Assessed Skills</div>
                    <div className="skill-chips">
                        {userSkills.slice(0, 6).map(s => (
                            <span key={s.name} className="skill-chip">{s.name}</span>
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
}

// ── Achievements Card ─────────────────────────────────────────────────────────

function BadgePill({ badge, locked }: { badge: BadgeDef; locked?: boolean }) {
    const { Icon } = badge;
    return (
        <div
            className={`badge-pill badge-pill--${badge.color}${locked ? ' badge-pill--locked' : ''}`}
            title={locked ? `Locked: ${badge.desc}` : badge.desc}
        >
            <div className="badge-pill-icon">
                {locked ? <Lock size={10} /> : <Icon size={10} />}
            </div>
            <span className="badge-pill-label">{badge.label}</span>
        </div>
    );
}

function AchievementsCard({ badges }: { badges: BadgeDef[] }) {
    const earned = badges.filter(b => b.earned);
    const locked = badges.filter(b => !b.earned);
    const pct = Math.round((earned.length / badges.length) * 100);

    return (
        <div className="dash-card">
            <div className="dash-card-header">
                <h2 className="dash-card-title">
                    Achievements
                    {earned.length > 0 && <span className="achieve-count">{earned.length}</span>}
                </h2>
                <Award size={15} className="achieve-trophy-icon" />
            </div>

            {earned.length === 0 && (
                <p className="achieve-empty-hint">Complete quizzes and lessons to earn your first badge.</p>
            )}

            {earned.length > 0 && (
                <div className="achieve-section-wrap">
                    <div className="badge-grid">
                        {earned.map(b => <BadgePill key={b.id} badge={b} />)}
                    </div>
                </div>
            )}

            {locked.length > 0 && (
                <div className="achieve-section-wrap">
                    <div className="achieve-sublabel">Upcoming</div>
                    <div className="badge-grid">
                        {locked.slice(0, earned.length === 0 ? 4 : 3).map(b => <BadgePill key={b.id} badge={b} locked />)}
                    </div>
                </div>
            )}

            {locked.length === 0 && earned.length > 0 && (
                <p className="achieve-complete-msg">All badges earned. Excellent work!</p>
            )}

            <div className="achieve-footer">
                <div className="achieve-bar">
                    <div className="achieve-bar-fill" style={{ width: `${pct}%` }} />
                </div>
                <span className="achieve-bar-label">{earned.length}/{badges.length}</span>
            </div>
        </div>
    );
}

// ── Weak Areas ────────────────────────────────────────────────────────────────

function WeakAreasList({ areas }: { areas: WeakArea[] }) {
    if (areas.length === 0) {
        return (
            <div className="dash-empty dash-empty--compact">
                <CheckCircle2 size={28} className="dash-empty-icon" style={{ color: '#16a34a', opacity: 1 }} />
                <p style={{ color: 'var(--text-secondary)' }}>All attempted topics score above 70%.</p>
            </div>
        );
    }
    return (
        <ul className="weak-list">
            {areas.map(area => (
                <li key={area.topic_id} className="weak-item">
                    <div className="weak-item-left">
                        <AlertTriangle size={14} className="weak-icon" />
                        <div>
                            <span className="weak-topic-name">{area.topic_title}</span>
                            <span className="weak-subject-tag">{area.subject_title}</span>
                        </div>
                    </div>
                    <div className="weak-item-right">
                        <span className={`weak-score ${scoreColor(area.avg_score)}`}>{area.avg_score}%</span>
                        <Link to={`/practice/topics/${area.topic_id}`} className="weak-retry-link">
                            Retry <ArrowRight size={11} />
                        </Link>
                    </div>
                </li>
            ))}
        </ul>
    );
}

// ── Recommendations ───────────────────────────────────────────────────────────

function recIcon(type: string) {
    switch (type) {
        case 'weak_topic':     return <AlertTriangle size={14} />;
        case 'get_started':    return <Zap size={14} />;
        case 'start_learning': return <BookMarked size={14} />;
        default:               return <TrendingUp size={14} />;
    }
}

function RecommendationsList({ items }: { items: Recommendation[] }) {
    return (
        <ul className="rec-list">
            {items.map((rec, i) => (
                <li key={i} className={`rec-item rec-item--${rec.type}`}>
                    <div className="rec-icon">{recIcon(rec.type)}</div>
                    <div className="rec-body">
                        <span className="rec-title">{rec.title}</span>
                        <span className="rec-desc">{rec.description}</span>
                    </div>
                    <Link to={rec.route} className="rec-action"><ArrowRight size={13} /></Link>
                </li>
            ))}
        </ul>
    );
}

// ── Main Page ─────────────────────────────────────────────────────────────────

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

            {/* Onboarding checklist — new + starter users only */}
            {showOnboarding && (
                <OnboardingCard summary={overview!.summary} profile={overview!.profile ?? null} />
            )}

            {/* Stats row */}
            {isLoading || !overview ? <SkeletonStats /> : <StatsRow summary={overview.summary} />}

            {/* Section cards */}
            {isLoading || !overview ? <SkeletonSections /> : <SectionCards summary={overview.summary} />}

            {/* Main grid */}
            <div className="dash-grid">

                {/* ── Left column ── */}
                <div className="dash-col-main">

                    <div className="dash-card">
                        <div className="dash-card-header">
                            <h2 className="dash-card-title">Learning Tracks</h2>
                            <Link to="/learning" className="dash-card-link">Browse all</Link>
                        </div>
                        {progressLoading ? <SkeletonCard rows={3} /> : <LearningTracksSection tracks={progress?.tracks ?? []} />}
                    </div>

                    <div className="dash-card">
                        <div className="dash-card-header">
                            <h2 className="dash-card-title">Performance by Subject</h2>
                            {hasQuizData && <span className="dash-card-meta">{overview!.summary.avg_quiz_score}% overall avg</span>}
                        </div>
                        {isLoading ? <SkeletonCard rows={3} /> : <SubjectPerformance subjects={overview?.quiz_by_subject ?? []} />}
                    </div>

                    <div className="dash-card">
                        <div className="dash-card-header">
                            <h2 className="dash-card-title">Recent Quiz Attempts</h2>
                            <Link to="/practice" className="dash-card-link">Practice more</Link>
                        </div>
                        {isLoading ? <SkeletonCard rows={4} /> : <RecentAttemptsList attempts={overview?.recent_attempts ?? []} />}
                    </div>

                    <div className="dash-card">
                        <div className="dash-card-header">
                            <h2 className="dash-card-title">Recent Activity</h2>
                        </div>
                        {activityLoading ? <SkeletonCard rows={3} /> : <RecentActivityList items={activity ?? []} />}
                    </div>
                </div>

                {/* ── Right column ── */}
                <div className="dash-col-side">

                    {/* Skill Level */}
                    {isLoading ? (
                        <div className="dash-card"><SkeletonCard rows={4} /></div>
                    ) : (
                        <SkillLevelCard summary={overview!.summary} userSkills={overview?.user_skills ?? []} />
                    )}

                    {/* Achievements */}
                    {!isLoading && overview && <AchievementsCard badges={badges} />}

                    {/* Weak Areas */}
                    <div className="dash-card">
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
                    <div className="dash-card">
                        <div className="dash-card-header">
                            <h2 className="dash-card-title">Recommended Next Steps</h2>
                            <Lightbulb size={15} className="dash-rec-icon" />
                        </div>
                        {isLoading ? <SkeletonCard rows={2} /> : <RecommendationsList items={overview?.recommendations ?? []} />}
                    </div>
                </div>
            </div>
        </div>
    );
}
