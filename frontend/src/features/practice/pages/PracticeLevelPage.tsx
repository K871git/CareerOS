import { useParams, Link, useLocation } from 'react-router-dom';
import { Lock, CheckCircle2, ChevronRight, ArrowRight } from 'lucide-react';
import { useTopics } from '../../learning/hooks/useLearning';
import type { Topic } from '../../../types/api';
import '../practice.css';

const STRIP_COLORS = [
    'linear-gradient(90deg, #6366f1, #8b5cf6)',
    'linear-gradient(90deg, #8b5cf6, #a855f7)',
    'linear-gradient(90deg, #f59e0b, #ef4444)',
];

const LEVEL_LABELS = ['J', 'M', 'S'];

function LevelCard({ topic, index }: { topic: Topic; index: number }) {
    const isFirst = index === 0;

    const scoreBarWidth = topic.best_score > 0 ? `${(topic.best_score / 10) * 100}%` : '0%';
    const isPassing = topic.best_score >= 7;

    return (
        <div
            className={[
                'prac-level-card',
                topic.is_locked ? 'prac-level-card--locked' : '',
                topic.is_completed ? 'prac-level-card--completed' : '',
            ]
                .filter(Boolean)
                .join(' ')}
        >
            {/* Top color strip */}
            <div
                className="prac-level-strip"
                style={{ background: STRIP_COLORS[index % STRIP_COLORS.length] }}
            />

            {/* Body */}
            <div className="prac-level-body">
                <div className="prac-level-icon-wrap">
                    <div className={`prac-level-icon prac-level-icon--${(index % 3) + 1}`}>
                        {LEVEL_LABELS[index % LEVEL_LABELS.length]}
                    </div>
                    {topic.is_locked && (
                        <div className="prac-lock-badge">
                            <Lock size={16} />
                        </div>
                    )}
                    {topic.is_completed && (
                        <div className="prac-completed-badge">
                            <CheckCircle2 size={11} /> Passed
                        </div>
                    )}
                </div>

                <h3 className="prac-level-title">{topic.title}</h3>
                <p className="prac-level-desc">
                    {topic.description || '10 multiple choice questions.'}
                </p>

                {/* Score bar — only shown if attempted */}
                {topic.best_score > 0 && (
                    <div className="prac-score-row">
                        <span className="prac-score-label">Best</span>
                        <div className="prac-score-bar">
                            <div
                                className={`prac-score-fill${isPassing ? ' prac-score-fill--pass' : ''}`}
                                style={{ width: scoreBarWidth }}
                            />
                        </div>
                        <span className="prac-score-num">{topic.best_score}/10</span>
                    </div>
                )}
            </div>

            {/* Footer */}
            <div className="prac-level-footer">
                {topic.is_locked ? (
                    <p className="prac-locked-hint">
                        <Lock size={12} />
                        {isFirst
                            ? 'Locked'
                            : 'Complete the previous level to unlock'}
                    </p>
                ) : topic.is_completed ? (
                    <Link
                        to={`/practice/topics/${topic.id}`}
                        state={{ topicTitle: topic.title }}
                        className="prac-retake-btn"
                    >
                        <ArrowRight size={14} /> Retake
                    </Link>
                ) : (
                    <Link
                        to={`/practice/topics/${topic.id}`}
                        state={{ topicTitle: topic.title }}
                        className="prac-start-btn"
                    >
                        Start Quiz <ChevronRight size={14} />
                    </Link>
                )}
            </div>
        </div>
    );
}

export default function PracticeLevelPage() {
    const { subjectId } = useParams<{ subjectId: string }>();
    const id = Number(subjectId);
    const location = useLocation();
    const subjectTitle = (location.state as any)?.subjectTitle as string | undefined;
    const trackTitle   = (location.state as any)?.trackTitle  as string | undefined;
    const arenaId      = (location.state as any)?.arenaId     as string | undefined;

    const { data: topics = [], isLoading } = useTopics(id);

    if (isLoading) {
        return (
            <div className="practice-page">
                <div className="practice-inner">
                    <div className="skeleton" style={{ height: 14, width: 200, borderRadius: 6, marginBottom: 24 }} />
                    <div className="skeleton" style={{ height: 32, width: 260, borderRadius: 8, marginBottom: 8 }} />
                    <div className="skeleton" style={{ height: 18, width: 380, borderRadius: 6, marginBottom: 32 }} />
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: '1rem' }}>
                        {[0, 1, 2].map((i) => (
                            <div key={i} className="skeleton" style={{ height: 260, borderRadius: 16 }} />
                        ))}
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="practice-page">
            <div className="practice-inner">
                {/* Breadcrumb */}
                <div className="prac-breadcrumb">
                    <Link to="/practice" className="prac-breadcrumb-link">Practice</Link>
                    {arenaId && (
                        <>
                            <span className="prac-breadcrumb-sep">›</span>
                            <Link to="/practice/fsd" className="prac-breadcrumb-link">Full Stack Development</Link>
                            <span className="prac-breadcrumb-sep">›</span>
                            <Link to={`/practice/fsd/${arenaId}`} className="prac-breadcrumb-link">{trackTitle}</Link>
                        </>
                    )}
                    <span className="prac-breadcrumb-sep">›</span>
                    <span>{subjectTitle ?? 'Levels'}</span>
                </div>

                {/* Header */}
                <div className="prac-level-header">
                    <h1 className="prac-level-subject-title">{subjectTitle ?? 'Practice Levels'}</h1>
                    {trackTitle && (
                        <p className="prac-level-subject-desc">{trackTitle}</p>
                    )}
                </div>

                {/* Empty state */}
                {topics.length === 0 ? (
                    <div className="practice-empty">
                        <p>No practice levels available for this subject yet.</p>
                        <Link to="/practice" className="practice-link" style={{ marginTop: '0.75rem', display: 'inline-block' }}>
                            ← Back to practice
                        </Link>
                    </div>
                ) : (
                    <div className="prac-level-grid">
                        {topics.map((topic, i) => (
                            <LevelCard key={topic.id} topic={topic} index={i} />
                        ))}
                    </div>
                )}

                {/* Unlock rule */}
                {topics.length > 0 && (
                    <p style={{ fontSize: '0.8rem', color: 'var(--text-muted)', textAlign: 'center' }}>
                        Score 7 or more out of 10 to unlock the next level.
                    </p>
                )}
            </div>
        </div>
    );
}
