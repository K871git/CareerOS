import { Link } from 'react-router-dom';
import {
    BookOpen,
    Target,
    TrendingUp,
    CheckCircle2,
    ArrowRight,
    AlertTriangle,
    Award,
    Lightbulb,
    Play,
    Brain,
    Clock,
    Zap,
    BarChart2,
    Shield,
    BookMarked,
    Layers,
} from 'lucide-react';
import { useAuth } from '../../../store/authStore';
import { useDashboardOverview } from '../hooks/useDashboard';
import { timeAgo } from '../../../utils/time';
import type {
    DashboardSummary,
    DashboardProfile,
    DashboardUserSkill,
    QuizBySubject,
    WeakArea,
    Recommendation,
    RecentAttempt,
    DashboardActivity,
} from '../../../types/api';
import '../dashboard.css';

// ── helpers ───────────────────────────────────────────────────────────────────

function scoreColor(pct: number): string {
    if (pct >= 70) return 'score-good';
    if (pct >= 50) return 'score-warn';
    return 'score-bad';
}

function scoreBarColor(pct: number): string {
    if (pct >= 70) return 'bar-good';
    if (pct >= 50) return 'bar-warn';
    return 'bar-bad';
}

// ── Skeletons ─────────────────────────────────────────────────────────────────

function SkeletonStats() {
    return (
        <div className="dash-stats">
            {[0, 1, 2, 3].map((i) => (
                <div key={i} className="skeleton skeleton-stat" />
            ))}
        </div>
    );
}

function SkeletonSections() {
    return (
        <div className="dash-section-cards">
            {[0, 1, 2].map((i) => (
                <div key={i} className="skeleton" style={{ height: 82, borderRadius: 16 }} />
            ))}
        </div>
    );
}

function SkeletonCard({ rows = 3 }: { rows?: number }) {
    return (
        <div>
            {Array.from({ length: rows }).map((_, i) => (
                <div
                    key={i}
                    className="skeleton skeleton-block"
                    style={{ height: 48, marginBottom: 10, width: i === rows - 1 ? '70%' : '100%' }}
                />
            ))}
        </div>
    );
}

// ── Welcome Banner ────────────────────────────────────────────────────────────

function WelcomeBanner({
    firstName,
    profile,
    weakCount,
}: {
    firstName: string;
    profile: DashboardProfile | null;
    weakCount: number;
}) {
    const hasGoal = profile?.target_role;

    return (
        <div className="dash-welcome">
            <div>
                <div className="dash-welcome-top">
                    <h1 className="dash-welcome-title">Welcome back, {firstName}!</h1>
                    {profile?.experience_level && (
                        <span className="dash-exp-badge">{profile.experience_level}</span>
                    )}
                </div>
                <p className="dash-welcome-sub">
                    {hasGoal
                        ? `Preparing for ${profile!.target_role} · ${weakCount > 0 ? `${weakCount} area${weakCount > 1 ? 's' : ''} to improve` : 'Keep it up!'}`
                        : weakCount > 0
                            ? `You have ${weakCount} weak area${weakCount > 1 ? 's' : ''} to focus on — keep going.`
                            : 'Consistency is what separates great engineers. Keep the momentum.'}
                </p>
            </div>
            <Link to="/practice" className="dash-continue-btn">
                <Play size={13} /> Start Quiz <ArrowRight size={13} />
            </Link>
        </div>
    );
}

// ── Stats Row ─────────────────────────────────────────────────────────────────

interface StatCardProps {
    value: string | number;
    label: string;
    icon: React.ElementType;
    variant?: 'default' | 'success' | 'warning' | 'danger';
}

function StatCard({ value, label, icon: Icon, variant = 'default' }: StatCardProps) {
    return (
        <div className={`stat-card stat-card--${variant}`}>
            <div className="stat-icon">
                <Icon size={18} />
            </div>
            <div>
                <div className="stat-value">{value}</div>
                <div className="stat-label">{label}</div>
            </div>
        </div>
    );
}

function StatsRow({ summary }: { summary: DashboardSummary }) {
    const accuracyVariant =
        summary.quizzes_taken === 0
            ? 'default'
            : summary.accuracy >= 70
                ? 'success'
                : summary.accuracy >= 50
                    ? 'warning'
                    : 'danger';

    const totalLevels = summary.learning_levels_passed + summary.theory_levels_passed;

    return (
        <div className="dash-stats">
            <StatCard
                value={summary.quizzes_taken === 0 ? '—' : `${summary.accuracy}%`}
                label="Quiz Accuracy"
                icon={Award}
                variant={accuracyVariant}
            />
            <StatCard
                value={summary.total_questions_answered}
                label="Questions Answered"
                icon={Brain}
            />
            <StatCard
                value={summary.lessons_completed}
                label="Lessons Completed"
                icon={CheckCircle2}
                variant={summary.lessons_completed > 0 ? 'success' : 'default'}
            />
            <StatCard
                value={totalLevels}
                label="Levels Passed"
                icon={TrendingUp}
                variant={totalLevels > 0 ? 'success' : 'default'}
            />
        </div>
    );
}

// ── Section Overview Cards ────────────────────────────────────────────────────

function SectionCards({ summary }: { summary: DashboardSummary }) {
    const theoryTotal = 3; // only Languages is active (3 levels)

    return (
        <div className="dash-section-cards">
            <Link to="/practice" className="dash-section-card">
                <div className="dash-section-icon dash-section-icon--practice">
                    <Target size={18} />
                </div>
                <div className="dash-section-body">
                    <div className="dash-section-label">Practice</div>
                    <div className="dash-section-stat">
                        {summary.quizzes_taken > 0
                            ? `${summary.avg_quiz_score}% avg · ${summary.quizzes_taken} quiz${summary.quizzes_taken === 1 ? '' : 'zes'}`
                            : 'Not started yet'}
                    </div>
                    <div className="dash-section-bar">
                        <div
                            className="dash-section-fill dash-section-fill--practice"
                            style={{ width: `${Math.min(summary.accuracy, 100)}%` }}
                        />
                    </div>
                </div>
                <ArrowRight size={14} className="dash-section-arrow" />
            </Link>

            <Link to="/learning" className="dash-section-card">
                <div className="dash-section-icon dash-section-icon--learning">
                    <BookOpen size={18} />
                </div>
                <div className="dash-section-body">
                    <div className="dash-section-label">Learning</div>
                    <div className="dash-section-stat">
                        {summary.lessons_completed > 0
                            ? `${summary.lessons_completed} lessons · ${summary.learning_levels_passed} levels passed`
                            : 'Not started yet'}
                    </div>
                    <div className="dash-section-bar">
                        <div
                            className="dash-section-fill dash-section-fill--learning"
                            style={{ width: `${summary.lessons_percentage}%` }}
                        />
                    </div>
                </div>
                <ArrowRight size={14} className="dash-section-arrow" />
            </Link>

            <Link to="/theory" className="dash-section-card">
                <div className="dash-section-icon dash-section-icon--theory">
                    <Layers size={18} />
                </div>
                <div className="dash-section-body">
                    <div className="dash-section-label">Theory</div>
                    <div className="dash-section-stat">
                        {summary.theory_levels_passed > 0
                            ? `${summary.theory_levels_passed}/${theoryTotal} levels passed`
                            : 'Not started yet'}
                    </div>
                    <div className="dash-section-bar">
                        <div
                            className="dash-section-fill dash-section-fill--theory"
                            style={{ width: `${(summary.theory_levels_passed / theoryTotal) * 100}%` }}
                        />
                    </div>
                </div>
                <ArrowRight size={14} className="dash-section-arrow" />
            </Link>
        </div>
    );
}

// ── Quiz Performance by Subject ───────────────────────────────────────────────

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
            {subjects.map((s) => (
                <div key={s.subject_id} className="subject-perf-row">
                    <div className="subject-perf-meta">
                        <span className="subject-perf-name">{s.subject_title}</span>
                        <span className="subject-perf-attempts">
                            {s.attempts} {s.attempts === 1 ? 'attempt' : 'attempts'} · {s.total_questions} Qs
                        </span>
                    </div>
                    <div className="subject-perf-bar-wrap">
                        <div className="subject-perf-bar">
                            <div
                                className={`subject-perf-fill ${scoreBarColor(s.avg_score)}`}
                                style={{ width: `${s.avg_score}%` }}
                            />
                        </div>
                        <span className={`subject-perf-pct ${scoreColor(s.avg_score)}`}>
                            {s.avg_score}%
                        </span>
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
            {attempts.map((a) => (
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

function SkillLevelCard({
    summary,
    userSkills,
}: {
    summary: DashboardSummary;
    userSkills: DashboardUserSkill[];
}) {
    const level = summary.skill_level; // 0 = not started, 1–4
    const label = summary.skill_label;

    return (
        <div className="dash-card">
            <div className="dash-card-header">
                <h2 className="dash-card-title">Your Level</h2>
                <Shield size={15} className="dash-skill-icon" />
            </div>

            <div className="skill-level-display">
                <div className="skill-level-label">{label}</div>
                <div className="skill-level-bar">
                    {SKILL_LEVEL_NAMES.map((_, i) => (
                        <div
                            key={i}
                            className={`skill-level-seg${i < level ? ' skill-level-seg--active' : ''}`}
                        />
                    ))}
                </div>
                <div className="skill-level-names">
                    {SKILL_LEVEL_NAMES.map((n) => (
                        <span key={n} className="skill-level-lbl">{n}</span>
                    ))}
                </div>
                <p className="skill-level-basis">
                    {level === 0
                        ? 'Complete quizzes to determine your level.'
                        : `Based on ${summary.quizzes_taken} quiz${summary.quizzes_taken === 1 ? '' : 'zes'} · ${summary.accuracy}% accuracy`}
                </p>
            </div>

            {userSkills.length > 0 && (
                <div className="skill-chips-wrap">
                    <div className="skill-chips-label">Assessed Skills</div>
                    <div className="skill-chips">
                        {userSkills.slice(0, 6).map((s) => (
                            <span key={s.name} className="skill-chip">{s.name}</span>
                        ))}
                    </div>
                </div>
            )}
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
            {areas.map((area) => (
                <li key={area.topic_id} className="weak-item">
                    <div className="weak-item-left">
                        <AlertTriangle size={14} className="weak-icon" />
                        <div>
                            <span className="weak-topic-name">{area.topic_title}</span>
                            <span className="weak-subject-tag">{area.subject_title}</span>
                        </div>
                    </div>
                    <div className="weak-item-right">
                        <span className={`weak-score ${scoreColor(area.avg_score)}`}>
                            {area.avg_score}%
                        </span>
                        <Link
                            to={`/practice/topics/${area.topic_id}`}
                            className="weak-retry-link"
                        >
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
        case 'weak_topic':    return <AlertTriangle size={14} />;
        case 'get_started':   return <Zap size={14} />;
        case 'start_theory':  return <Brain size={14} />;
        case 'start_learning':return <BookMarked size={14} />;
        default:              return <TrendingUp size={14} />;
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
                    <Link to={rec.route} className="rec-action">
                        <ArrowRight size={13} />
                    </Link>
                </li>
            ))}
        </ul>
    );
}

// ── Activity Feed ─────────────────────────────────────────────────────────────

function ActivityFeed({ items }: { items: DashboardActivity[] }) {
    if (items.length === 0) {
        return (
            <div className="dash-empty dash-empty--compact">
                <Clock size={28} className="dash-empty-icon" />
                <p>No lessons completed yet.</p>
            </div>
        );
    }
    return (
        <ul className="activity-list">
            {items.map((item, i) => (
                <li key={i} className="activity-item">
                    <div className="activity-icon">
                        <CheckCircle2 size={14} />
                    </div>
                    <div className="activity-content">
                        <p className="activity-desc">{item.description}</p>
                        {item.subject_name && (
                            <span className="activity-subject">{item.subject_name}</span>
                        )}
                    </div>
                    <span className="activity-time">{timeAgo(item.created_at)}</span>
                </li>
            ))}
        </ul>
    );
}

// ── Main Page ─────────────────────────────────────────────────────────────────

export default function DashboardPage() {
    const { state } = useAuth();
    const firstName = state.user?.name?.split(' ')[0] ?? 'there';
    const { data: overview, isLoading } = useDashboardOverview();

    const weakCount  = overview?.weak_areas.length ?? 0;
    const hasQuizData = (overview?.summary.quizzes_taken ?? 0) > 0;

    return (
        <div className="dashboard">

            {/* Welcome / career goal banner */}
            {isLoading ? (
                <div className="skeleton" style={{ height: 72, borderRadius: 16, marginBottom: '1.75rem' }} />
            ) : (
                <WelcomeBanner
                    firstName={firstName}
                    profile={overview?.profile ?? null}
                    weakCount={weakCount}
                />
            )}

            {/* Stats row */}
            {isLoading || !overview ? (
                <SkeletonStats />
            ) : (
                <StatsRow summary={overview.summary} />
            )}

            {/* Section overview cards */}
            {isLoading || !overview ? (
                <SkeletonSections />
            ) : (
                <SectionCards summary={overview.summary} />
            )}

            {/* Main content grid */}
            <div className="dash-grid">

                {/* ── Left column ── */}
                <div className="dash-col-main">

                    {/* Quiz Performance by Subject */}
                    <div className="dash-card">
                        <div className="dash-card-header">
                            <h2 className="dash-card-title">Performance by Subject</h2>
                            {hasQuizData && (
                                <span className="dash-card-meta">
                                    {overview!.summary.avg_quiz_score}% overall avg
                                </span>
                            )}
                        </div>
                        {isLoading ? (
                            <SkeletonCard rows={3} />
                        ) : (
                            <SubjectPerformance subjects={overview?.quiz_by_subject ?? []} />
                        )}
                    </div>

                    {/* Recent Quiz Attempts */}
                    <div className="dash-card">
                        <div className="dash-card-header">
                            <h2 className="dash-card-title">Recent Quiz Attempts</h2>
                            <Link to="/practice" className="dash-card-link">Practice more</Link>
                        </div>
                        {isLoading ? (
                            <SkeletonCard rows={4} />
                        ) : (
                            <RecentAttemptsList attempts={overview?.recent_attempts ?? []} />
                        )}
                    </div>

                    {/* Lesson Activity */}
                    <div className="dash-card">
                        <div className="dash-card-header">
                            <h2 className="dash-card-title">Lesson Activity</h2>
                            <Link to="/learning" className="dash-card-link">Go to Learning</Link>
                        </div>
                        {isLoading ? (
                            <SkeletonCard rows={3} />
                        ) : (
                            <ActivityFeed items={overview?.recent_activity ?? []} />
                        )}
                    </div>
                </div>

                {/* ── Right column ── */}
                <div className="dash-col-side">

                    {/* Skill Level */}
                    {isLoading ? (
                        <div className="dash-card">
                            <SkeletonCard rows={4} />
                        </div>
                    ) : (
                        <SkillLevelCard
                            summary={overview!.summary}
                            userSkills={overview?.user_skills ?? []}
                        />
                    )}

                    {/* Weak Areas */}
                    <div className="dash-card">
                        <div className="dash-card-header">
                            <h2 className="dash-card-title">
                                Weak Areas
                                {weakCount > 0 && (
                                    <span className="dash-weak-badge">{weakCount}</span>
                                )}
                            </h2>
                            <AlertTriangle size={15} className={weakCount > 0 ? 'dash-warn-icon' : 'dash-ok-icon'} />
                        </div>
                        {isLoading ? (
                            <SkeletonCard rows={3} />
                        ) : (
                            <WeakAreasList areas={overview?.weak_areas ?? []} />
                        )}
                    </div>

                    {/* Recommendations */}
                    <div className="dash-card">
                        <div className="dash-card-header">
                            <h2 className="dash-card-title">Recommended Next Steps</h2>
                            <Lightbulb size={15} className="dash-rec-icon" />
                        </div>
                        {isLoading ? (
                            <SkeletonCard rows={2} />
                        ) : (
                            <RecommendationsList items={overview?.recommendations ?? []} />
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}
