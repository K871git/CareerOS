import { Link, useParams, Navigate } from 'react-router-dom';
import { Lock, CheckCircle2, ChevronRight, Sparkles } from 'lucide-react';
import { useTheoryLevels } from '../hooks/useTheoryLevel';
import '../theory.css';

const LEVEL_META = [
    {
        num: 1,
        title: 'Foundation',
        desc: 'Core concepts and fundamental knowledge. Pass 8/10 to advance.',
        strip: '#6366f1',
        iconBg: 'rgba(99,102,241,0.12)',
        iconColor: '#6366f1',
    },
    {
        num: 2,
        title: 'Intermediate',
        desc: 'Deeper understanding and practical patterns. Pass 9/10 to advance.',
        strip: '#8b5cf6',
        iconBg: 'rgba(139,92,246,0.12)',
        iconColor: '#8b5cf6',
    },
    {
        num: 3,
        title: 'Advanced',
        desc: 'Edge cases, internals, and expert-level knowledge. Pass 10/10 to complete.',
        strip: '#d97706',
        iconBg: 'rgba(245,158,11,0.12)',
        iconColor: '#d97706',
    },
];

const AREA_LABELS: Record<string, string> = {
    languages:          'Languages',
    frameworks:         'Frameworks',
    networking:         'Networking',
    'operating-systems':'Operating Systems',
    databases:          'Databases',
    'system-design':    'System Design',
    sdlc:               'SDLC',
    'data-structures':  'Data Structures',
};

export default function TheoryAreaPage() {
    const { area } = useParams<{ area: string }>();
    if (!area) return <Navigate to="/theory" replace />;

    const areaLabel = AREA_LABELS[area] ?? area;
    const { data: levels = [], isLoading } = useTheoryLevels(area);

    const allComplete = levels.length === 3 && levels.every((l) => l.completed);

    return (
        <div className="theory-v2-page">
            <div className="theory-v2-inner">
                {/* Breadcrumb */}
                <nav className="prac-breadcrumb" aria-label="Breadcrumb">
                    <Link to="/theory" className="prac-breadcrumb-link">Theory</Link>
                    <span className="prac-breadcrumb-sep">›</span>
                    <span>{areaLabel}</span>
                </nav>

                {/* Header */}
                <div className="th-area-header">
                    <h1 className="th-area-title">{areaLabel}</h1>
                    <p className="th-area-subtitle">
                        Three levels · 10 questions each · Pass thresholds increase per level
                    </p>
                </div>

                {/* Level cards */}
                {isLoading ? (
                    <div className="th-level-grid">
                        {[1, 2, 3].map((i) => (
                            <div key={i} className="skeleton" style={{ height: 220, borderRadius: 20 }} />
                        ))}
                    </div>
                ) : (
                    <div className="th-level-grid">
                        {LEVEL_META.map((meta) => {
                            const level = levels.find((l) => l.level === meta.num);
                            const locked    = level?.locked ?? meta.num > 1;
                            const completed = level?.completed ?? false;
                            const score     = level?.score ?? null;
                            const threshold = level?.pass_threshold ?? (meta.num === 1 ? 8 : meta.num === 2 ? 9 : 10);
                            const pct       = level?.pass_percentage ?? (meta.num === 1 ? 75 : meta.num === 2 ? 85 : 95);

                            return (
                                <div
                                    key={meta.num}
                                    className={[
                                        'th-level-card',
                                        locked    ? 'th-level-card--locked'    : '',
                                        completed ? 'th-level-card--completed' : '',
                                    ].join(' ')}
                                >
                                    <div className="th-level-strip" style={{ background: completed ? '#10b981' : meta.strip }} />
                                    <div className="th-level-body">
                                        <div className="th-level-icon-row">
                                            <div
                                                className="th-level-num"
                                                style={{ background: meta.iconBg, color: meta.iconColor }}
                                            >
                                                L{meta.num}
                                            </div>
                                            {locked && <Lock size={16} className="th-level-lock" />}
                                            {completed && (
                                                <span className="th-completed-badge">
                                                    <CheckCircle2 size={12} /> Passed
                                                </span>
                                            )}
                                        </div>

                                        <h3 className="th-level-title">Level {meta.num} — {meta.title}</h3>
                                        <p className="th-level-desc">{meta.desc}</p>

                                        {score !== null && (
                                            <div className="th-score-row">
                                                <span className="th-score-label">Best score</span>
                                                <div className="th-score-bar">
                                                    <div
                                                        className={`th-score-fill${completed ? ' th-score-fill--pass' : ''}`}
                                                        style={{ width: `${(score / 10) * 100}%` }}
                                                    />
                                                </div>
                                                <span className="th-score-num">{score}/10</span>
                                            </div>
                                        )}

                                        <div className="th-pass-info">
                                            Pass {threshold}/10 ({pct}%) to {meta.num < 3 ? `unlock Level ${meta.num + 1}` : 'complete'}
                                        </div>
                                    </div>

                                    <div className="th-level-footer">
                                        {locked ? (
                                            <p className="th-locked-hint">
                                                <Lock size={13} /> Complete Level {meta.num - 1} to unlock
                                            </p>
                                        ) : completed ? (
                                            <Link
                                                to={`/theory/${area}/${meta.num}`}
                                                className="th-retake-btn"
                                            >
                                                Retake Level {meta.num}
                                            </Link>
                                        ) : (
                                            <Link
                                                to={`/theory/${area}/${meta.num}`}
                                                className="th-start-btn"
                                            >
                                                Start Level {meta.num}
                                                <ChevronRight size={15} />
                                            </Link>
                                        )}
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                )}

                {/* All-complete banner */}
                {allComplete && (
                    <div className="th-assessment-banner">
                        <div className="th-assessment-icon">
                            <Sparkles size={20} />
                        </div>
                        <div>
                            <h3 className="th-assessment-title">Additional Assessment</h3>
                            <p className="th-assessment-desc">
                                You've completed all three levels. A comprehensive assessment is coming soon.
                            </p>
                        </div>
                        <span className="prac-soon-badge">Coming Soon</span>
                    </div>
                )}
            </div>
        </div>
    );
}
