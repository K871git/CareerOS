import { useParams, Link, useLocation } from 'react-router-dom';
import { Lock, CheckCircle2, ChevronRight, ArrowRight, ClipboardList, Clock } from 'lucide-react';
import { useTopics } from '../../learning/hooks/useLearning';
import type { Topic } from '../../../types/api';
import '../practice.css';

const STRIP_COLORS = [
    'linear-gradient(135deg,#4f46e5,#7c3aed)',
    'linear-gradient(135deg,#7c3aed,#a855f7)',
    'linear-gradient(135deg,#f59e0b,#ef4444)',
];

const LEVEL_META = [
    { label: 'J', name: 'Junior',  color: '#4f46e5', bg: 'rgba(79,70,229,0.1)' },
    { label: 'M', name: 'Mid',     color: '#7c3aed', bg: 'rgba(124,58,237,0.1)' },
    { label: 'S', name: 'Senior',  color: '#d97706', bg: 'rgba(217,119,6,0.1)' },
];

/* Extract skill chips from topic description */
function descToChips(description: string): string[] {
    if (!description || description === '10 multiple choice questions.') return [];
    return description
        .split(/[,;]+/)
        .map(s => s.trim().replace(/^(and|or)\s+/i, ''))
        .filter(s => s.length > 2 && s.length < 32)
        .slice(0, 5);
}

function LevelCard({ topic, index }: { topic: Topic; index: number }) {
    const meta       = LEVEL_META[index % LEVEL_META.length];
    const scoreBarW  = topic.best_score > 0 ? `${(topic.best_score / 10) * 100}%` : '0%';
    const isPassing  = topic.best_score >= 7;
    const chips      = descToChips(topic.description);

    return (
        <div className={[
            'prac-level-card',
            topic.is_locked    ? 'prac-level-card--locked'    : '',
            topic.is_completed ? 'prac-level-card--completed' : '',
        ].filter(Boolean).join(' ')}>

            {/* Gradient top strip */}
            <div className="prac-level-strip" style={{ background: STRIP_COLORS[index % STRIP_COLORS.length] }} />

            <div className="prac-level-body">
                {/* Level badge row */}
                <div className="prac-level-badge-row">
                    <div
                        className="prac-level-badge"
                        style={{ background: meta.bg, color: meta.color, border: `1.5px solid ${meta.color}30` }}
                    >
                        <span className="prac-level-badge-letter">{meta.label}</span>
                        <span className="prac-level-badge-name">{meta.name}</span>
                    </div>

                    {topic.is_locked && (
                        <div className="prac-lock-badge"><Lock size={12} /></div>
                    )}
                    {topic.is_completed && (
                        <div className="prac-completed-badge"><CheckCircle2 size={11} /> Passed</div>
                    )}
                </div>

                <h3 className="prac-level-title">{topic.title}</h3>

                {/* Skill chips from description */}
                {chips.length > 0 ? (
                    <div className="prac-level-chips">
                        {chips.map(chip => (
                            <span key={chip} className="prac-level-chip">{chip}</span>
                        ))}
                    </div>
                ) : (
                    <p className="prac-level-desc">
                        {topic.description || 'Multiple-choice questions on core concepts.'}
                    </p>
                )}

                {/* Quiz info row */}
                <div className="prac-level-info-row">
                    <span className="prac-level-info">
                        <ClipboardList size={11} /> 10 Questions
                    </span>
                    <span className="prac-level-info">
                        <Clock size={11} /> 15 min
                    </span>
                    <span className="prac-level-info prac-level-info--pass">
                        7/10 to unlock next
                    </span>
                </div>

                {/* Best score bar */}
                {topic.best_score > 0 && (
                    <div className="prac-score-row">
                        <span className="prac-score-label">Best</span>
                        <div className="prac-score-bar">
                            <div
                                className={`prac-score-fill${isPassing ? ' prac-score-fill--pass' : ''}`}
                                style={{ width: scoreBarW }}
                            />
                        </div>
                        <span className="prac-score-num">{topic.best_score}/10</span>
                    </div>
                )}
            </div>

            {/* Footer CTA */}
            <div className="prac-level-footer">
                {topic.is_locked ? (
                    <p className="prac-locked-hint">
                        <Lock size={12} />
                        {index === 0 ? 'Locked' : 'Complete the previous level to unlock'}
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
    const id            = Number(subjectId);
    const location      = useLocation();
    const subjectTitle  = (location.state as { subjectTitle?: string; trackTitle?: string; arenaId?: string } | null)?.subjectTitle;
    const trackTitle    = (location.state as { subjectTitle?: string; trackTitle?: string; arenaId?: string } | null)?.trackTitle;
    const arenaId       = (location.state as { subjectTitle?: string; trackTitle?: string; arenaId?: string } | null)?.arenaId;

    const { data: topics = [], isLoading } = useTopics(id);

    if (isLoading) {
        return (
            <div className="practice-page">
                <div className="practice-inner">
                    <div className="skeleton" style={{ height: 14, width: 200, borderRadius: 6, marginBottom: 24 }} />
                    <div className="skeleton" style={{ height: 32, width: 260, borderRadius: 8, marginBottom: 8 }} />
                    <div className="skeleton" style={{ height: 18, width: 380, borderRadius: 6, marginBottom: 32 }} />
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3,1fr)', gap: '1rem' }}>
                        {[0, 1, 2].map(i => (
                            <div key={i} className="skeleton" style={{ height: 280, borderRadius: 16 }} />
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
                    {trackTitle && <p className="prac-level-subject-desc">{trackTitle}</p>}
                </div>

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

            </div>
        </div>
    );
}
