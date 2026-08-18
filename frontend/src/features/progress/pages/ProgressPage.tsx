import { TrendingUp, BookOpen, Target, Zap, Brain, ChevronRight } from 'lucide-react';
import { Link } from 'react-router-dom';
import { useOverallProgress, useRecentActivityFeed } from '../hooks/useProgress';
import ProgressCard from '../components/ProgressCard';
import TrackProgress from '../components/TrackProgress';
import RecentActivityList from '../components/RecentActivity';
import type { UserProgress, TheoryAreaProgress, PracticeSubjectProgress } from '../../../types/api';
import '../progress.css';

const THEORY_AREA_NAMES: Record<string, string> = {
    languages:  'Languages',
    frameworks: 'Frameworks',
    networking: 'Networking',
};

function ProgressSkeleton() {
    return (
        <div className="prog-page">
            <div className="skeleton" style={{ height: 36, width: 220, borderRadius: 8, marginBottom: 6 }} />
            <div className="skeleton" style={{ height: 18, width: 340, borderRadius: 6, marginBottom: 32 }} />
            <div className="prog-summary-grid">
                {[0, 1, 2, 3, 4].map((i) => (
                    <div key={i} className="skeleton" style={{ height: 104, borderRadius: 14 }} />
                ))}
            </div>
            <div className="skeleton" style={{ height: 160, borderRadius: 14, marginBottom: 24 }} />
            <div className="skeleton" style={{ height: 200, borderRadius: 14 }} />
        </div>
    );
}

function AreaOverview({ progress }: { progress: UserProgress }) {
    const { summary } = progress;
    const theoryPct = summary.theory_levels_total > 0
        ? Math.round(summary.theory_levels_passed / summary.theory_levels_total * 100)
        : 0;

    return (
        <div className="prog-area-rows">
            <Link to="/learning" className="prog-area-row">
                <div className="prog-area-icon prog-area-icon--learning">
                    <BookOpen size={17} />
                </div>
                <span className="prog-area-name">Learning</span>
                <div className="prog-area-bar">
                    <div
                        className="prog-area-fill prog-area-fill--learning"
                        style={{ width: `${summary.percentage}%` }}
                    />
                </div>
                <span className="prog-area-pct">{summary.percentage}%</span>
                <span className="prog-area-stat">
                    {summary.completed_lessons} / {summary.total_lessons} lessons
                </span>
                <ChevronRight size={14} className="prog-area-arrow" />
            </Link>

            <Link to="/practice" className="prog-area-row">
                <div className="prog-area-icon prog-area-icon--practice">
                    <Target size={17} />
                </div>
                <span className="prog-area-name">Practice</span>
                <div className="prog-area-bar">
                    <div
                        className="prog-area-fill prog-area-fill--practice"
                        style={{ width: `${Math.min(summary.accuracy, 100)}%` }}
                    />
                </div>
                <span className="prog-area-pct">{summary.accuracy}%</span>
                <span className="prog-area-stat">
                    {summary.quizzes_taken} {summary.quizzes_taken === 1 ? 'quiz' : 'quizzes'} · accuracy
                </span>
                <ChevronRight size={14} className="prog-area-arrow" />
            </Link>

            <Link to="/theory" className="prog-area-row">
                <div className="prog-area-icon prog-area-icon--theory">
                    <Brain size={17} />
                </div>
                <span className="prog-area-name">Theory</span>
                <div className="prog-area-bar">
                    <div
                        className="prog-area-fill prog-area-fill--theory"
                        style={{ width: `${theoryPct}%` }}
                    />
                </div>
                <span className="prog-area-pct">{theoryPct}%</span>
                <span className="prog-area-stat">
                    {summary.theory_levels_passed} / {summary.theory_levels_total} levels passed
                </span>
                <ChevronRight size={14} className="prog-area-arrow" />
            </Link>
        </div>
    );
}

function AccuracyClass(acc: number) {
    if (acc >= 75) return 'prog-practice-fill--good';
    if (acc >= 60) return 'prog-practice-fill--avg';
    return 'prog-practice-fill--low';
}

function PracticeSection({ practice }: { practice: UserProgress['practice'] }) {
    if (practice.quizzes_taken === 0) {
        return (
            <div className="prog-empty">
                <Zap size={32} className="prog-empty-icon" />
                <p>
                    No quizzes taken yet.{' '}
                    <Link to="/practice" className="prog-link">Start practising</Link>{' '}
                    to track your performance.
                </p>
            </div>
        );
    }

    return (
        <div className="prog-practice-list">
            {practice.by_subject.map((s: PracticeSubjectProgress) => (
                <div key={s.subject_id} className="prog-practice-row">
                    <span className="prog-practice-name">{s.subject_title}</span>
                    <div className="prog-practice-bar-wrap">
                        <div className="prog-practice-bar">
                            <div
                                className={`prog-practice-fill ${AccuracyClass(s.accuracy)}`}
                                style={{ width: `${Math.min(s.accuracy, 100)}%` }}
                            />
                        </div>
                    </div>
                    <span className="prog-practice-pct">{s.accuracy}%</span>
                    <span className="prog-practice-meta">
                        {s.attempts} {s.attempts === 1 ? 'quiz' : 'quizzes'}
                    </span>
                </div>
            ))}
        </div>
    );
}

function TheorySection({ theory }: { theory: UserProgress['theory'] }) {
    return (
        <div className="prog-theory-areas">
            {theory.by_area.map((area: TheoryAreaProgress) => {
                const pct = area.total > 0 ? Math.round(area.passed / area.total * 100) : 0;
                return (
                    <Link to={`/theory/${area.area}`} key={area.area} className="prog-theory-card">
                        <div className="prog-theory-card-header">
                            <span className="prog-theory-name">
                                {THEORY_AREA_NAMES[area.area] ?? area.area}
                            </span>
                            <span className="prog-theory-pct">{pct}%</span>
                        </div>
                        <div className="prog-theory-bar">
                            <div
                                className="prog-theory-fill"
                                style={{ width: `${pct}%` }}
                            />
                        </div>
                        <div className="prog-theory-levels">
                            {Array.from({ length: area.total }, (_, i) => (
                                <div
                                    key={i}
                                    className={`prog-theory-dot${i < area.passed ? ' prog-theory-dot--done' : ''}`}
                                >
                                    L{i + 1}
                                </div>
                            ))}
                        </div>
                        <div className="prog-theory-sub">
                            {area.passed}/{area.total} levels passed
                        </div>
                    </Link>
                );
            })}
        </div>
    );
}

export default function ProgressPage() {
    const { data: progress, isLoading: progressLoading } = useOverallProgress();
    const { data: activity, isLoading: activityLoading } = useRecentActivityFeed();

    if (progressLoading) return <ProgressSkeleton />;

    return (
        <div className="prog-page">
            <div className="prog-header">
                <div className="prog-header-icon">
                    <TrendingUp size={22} />
                </div>
                <div>
                    <h1 className="prog-title">Your Progress</h1>
                    <p className="prog-subtitle">
                        Everything you've done across Learning, Practice and Theory
                    </p>
                </div>
            </div>

            {/* Stats */}
            {progress ? (
                <ProgressCard data={progress} />
            ) : (
                <div className="prog-empty">
                    <p>
                        No progress yet.{' '}
                        <Link to="/learning" className="prog-link">Start learning</Link>{' '}
                        to begin tracking.
                    </p>
                </div>
            )}

            {/* Area overview bars */}
            {progress && (
                <div className="prog-section">
                    <h2 className="prog-section-title">Progress by Area</h2>
                    <AreaOverview progress={progress} />
                </div>
            )}

            {/* Learning tracks */}
            <div className="prog-section">
                <h2 className="prog-section-title">Learning Tracks</h2>
                {progress && progress.tracks.length > 0 ? (
                    <div className="prog-tracks-list">
                        {progress.tracks.map((track) => (
                            <TrackProgress key={track.id} track={track} />
                        ))}
                    </div>
                ) : (
                    <div className="prog-empty">
                        <p>
                            No tracks started yet.{' '}
                            <Link to="/learning" className="prog-link">Browse learning tracks</Link>.
                        </p>
                    </div>
                )}
            </div>

            {/* Practice performance */}
            <div className="prog-section">
                <div className="prog-section-header">
                    <h2 className="prog-section-title">Practice Performance</h2>
                    {progress && progress.practice.quizzes_taken > 0 && (
                        <Link to="/practice" className="prog-section-link">
                            Go to Practice <ChevronRight size={13} />
                        </Link>
                    )}
                </div>
                {progress ? <PracticeSection practice={progress.practice} /> : null}
            </div>

            {/* Theory progress */}
            <div className="prog-section">
                <div className="prog-section-header">
                    <h2 className="prog-section-title">Theory Progress</h2>
                    <Link to="/theory" className="prog-section-link">
                        Go to Theory <ChevronRight size={13} />
                    </Link>
                </div>
                {progress ? <TheorySection theory={progress.theory} /> : null}
            </div>

            {/* Activity feed */}
            <div className="prog-section">
                <h2 className="prog-section-title">Recent Activity</h2>
                {activityLoading ? (
                    <div>
                        {[0, 1, 2, 3].map((i) => (
                            <div
                                key={i}
                                className="skeleton"
                                style={{ height: 56, borderRadius: 10, marginBottom: 10 }}
                            />
                        ))}
                    </div>
                ) : (
                    <RecentActivityList items={activity ?? []} />
                )}
            </div>
        </div>
    );
}
