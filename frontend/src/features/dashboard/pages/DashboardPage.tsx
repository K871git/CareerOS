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
} from 'lucide-react';
import { useAuth } from '../../../store/authStore';
import { useDashboardOverview } from '../hooks/useDashboard';
import { timeAgo } from '../../../utils/time';
import type {
    DashboardSummary,
    QuizBySubject,
    WeakArea,
    Recommendation,
    RecentAttempt,
    DashboardActivity,
} from '../../../types/api';
import '../dashboard.css';

// ── helpers ──────────────────────────────────────────────────────────────────

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

// ── Skeleton ──────────────────────────────────────────────────────────────────

function SkeletonStats() {
    return (
        <div className="dash-stats">
            {[0, 1, 2, 3].map((i) => (
                <div key={i} className="skeleton skeleton-stat" />
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
        summary.accuracy >= 70 ? 'success' : summary.accuracy >= 50 ? 'warning' : 'danger';

    return (
        <div className="dash-stats">
            <StatCard
                value={summary.quizzes_taken}
                label="Quizzes Taken"
                icon={Target}
            />
            <StatCard
                value={summary.quizzes_taken === 0 ? '—' : `${summary.accuracy}%`}
                label="Quiz Accuracy"
                icon={Award}
                variant={summary.quizzes_taken > 0 ? accuracyVariant : 'default'}
            />
            <StatCard
                value={summary.lessons_completed}
                label="Lessons Completed"
                icon={CheckCircle2}
                variant={summary.lessons_completed > 0 ? 'success' : 'default'}
            />
            <StatCard
                value={summary.total_questions_answered}
                label="Questions Answered"
                icon={Brain}
            />
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
                        <Link to="/practice" className="weak-retry-link">
                            Retry <ArrowRight size={11} />
                        </Link>
                    </div>
                </li>
            ))}
        </ul>
    );
}

// ── Recommendations ───────────────────────────────────────────────────────────

function RecommendationsList({ items }: { items: Recommendation[] }) {
    return (
        <ul className="rec-list">
            {items.map((rec, i) => (
                <li key={i} className={`rec-item rec-item--${rec.type}`}>
                    <div className="rec-icon">
                        {rec.type === 'weak_topic' && <AlertTriangle size={14} />}
                        {rec.type === 'get_started' && <Zap size={14} />}
                        {rec.type === 'explore' && <TrendingUp size={14} />}
                    </div>
                    <div className="rec-body">
                        <span className="rec-title">{rec.title}</span>
                        <span className="rec-desc">{rec.description}</span>
                    </div>
                    <Link to="/practice" className="rec-action">
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

// ── Quick Actions ─────────────────────────────────────────────────────────────

function QuickActions() {
    return (
        <div className="quick-actions">
            <Link to="/practice" className="quick-action-item">
                <div className="quick-action-icon"><Target size={16} /></div>
                <div className="quick-action-body">
                    <div className="quick-action-label">Practice Quiz</div>
                    <div className="quick-action-desc">Answer interview questions</div>
                </div>
                <ArrowRight size={13} className="quick-action-arrow" />
            </Link>
            <Link to="/tracks" className="quick-action-item">
                <div className="quick-action-icon"><BookOpen size={16} /></div>
                <div className="quick-action-body">
                    <div className="quick-action-label">Browse Tracks</div>
                    <div className="quick-action-desc">Find your learning path</div>
                </div>
                <ArrowRight size={13} className="quick-action-arrow" />
            </Link>
            <Link to="/progress" className="quick-action-item">
                <div className="quick-action-icon"><TrendingUp size={16} /></div>
                <div className="quick-action-body">
                    <div className="quick-action-label">Full Progress</div>
                    <div className="quick-action-desc">Detailed breakdown</div>
                </div>
                <ArrowRight size={13} className="quick-action-arrow" />
            </Link>
        </div>
    );
}

// ── Main Page ─────────────────────────────────────────────────────────────────

export default function DashboardPage() {
    const { state } = useAuth();
    const firstName = state.user?.name?.split(' ')[0] ?? 'there';
    const { data: overview, isLoading } = useDashboardOverview();

    const weakCount = overview?.weak_areas.length ?? 0;
    const hasQuizData = (overview?.summary.quizzes_taken ?? 0) > 0;

    return (
        <div className="dashboard">

            {/* Welcome */}
            <div className="dash-welcome">
                <div>
                    <h1 className="dash-welcome-title">Welcome back, {firstName}!</h1>
                    <p className="dash-welcome-sub">
                        {weakCount > 0
                            ? `You have ${weakCount} weak area${weakCount > 1 ? 's' : ''} to focus on — keep going.`
                            : 'Consistency is what separates great engineers. Keep the momentum.'}
                    </p>
                </div>
                <Link to="/practice" className="dash-continue-btn">
                    <Play size={13} /> Start Quiz <ArrowRight size={13} />
                </Link>
            </div>

            {/* Stats row */}
            {isLoading || !overview ? (
                <SkeletonStats />
            ) : (
                <StatsRow summary={overview.summary} />
            )}

            {/* Main grid */}
            <div className="dash-grid">

                {/* ── Left column ── */}
                <div className="dash-col-main">

                    {/* Quiz Performance by Subject */}
                    <div className="dash-card">
                        <div className="dash-card-header">
                            <h2 className="dash-card-title">Quiz Performance by Subject</h2>
                            {hasQuizData && (
                                <span className="dash-card-meta">
                                    avg {overview!.summary.avg_quiz_score}% overall
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

                    {/* Recent Lesson Activity */}
                    <div className="dash-card">
                        <div className="dash-card-header">
                            <h2 className="dash-card-title">Lesson Activity</h2>
                            <Link to="/tracks" className="dash-card-link">Browse tracks</Link>
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

                    {/* Quick Actions */}
                    <div className="dash-card">
                        <div className="dash-card-header">
                            <h2 className="dash-card-title">Quick Actions</h2>
                        </div>
                        <QuickActions />
                    </div>

                    {/* Lesson progress mini */}
                    {overview && (
                        <div className="dash-card dash-card--muted">
                            <div className="dash-card-header" style={{ marginBottom: '0.875rem' }}>
                                <h2 className="dash-card-title">Lesson Progress</h2>
                                <span className="dash-card-meta">
                                    {overview.summary.lessons_percentage}%
                                </span>
                            </div>
                            <div className="track-progress-bar">
                                <div
                                    className="track-progress-fill"
                                    style={{ width: `${overview.summary.lessons_percentage}%` }}
                                />
                            </div>
                            <p className="dash-progress-label">
                                {overview.summary.lessons_completed} / {overview.summary.lessons_total} lessons completed
                            </p>
                            <Link to="/tracks" className="track-cta" style={{ marginTop: '1rem' }}>
                                <BookOpen size={13} /> Continue Learning
                            </Link>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
