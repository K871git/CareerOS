import { Link } from 'react-router-dom';
import {
    Globe, Wifi, LayoutTemplate, RefreshCcw,
    Cpu, Database, GitBranch, Code2,
    Target, TrendingUp, Zap, ArrowRight, Sparkles,
} from 'lucide-react';
import { useDashboardOverview } from '../../overview/hooks/useOverview';
import '../practice.css';

const AVAILABLE = [
    {
        id:    'fsd',
        title: 'Full Stack Development',
        desc:  'Languages, frontend frameworks, and backend engineering.',
        icon:  Globe,
        to:    '/practice/fsd',
        tags:  ['Python', 'JavaScript', 'React', 'Node.js', 'PHP'],
        gradient: 'linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%)',
        glow: 'rgba(79,70,229,0.22)',
    },
    {
        id:    'databases',
        title: 'Databases',
        desc:  'SQL, normalization, indexing, transactions, and NoSQL.',
        icon:  Database,
        to:    '/practice/databases',
        tags:  ['MySQL', 'PostgreSQL', 'MongoDB', 'Redis'],
        gradient: 'linear-gradient(135deg,#0891b2 0%,#0e7490 100%)',
        glow: 'rgba(8,145,178,0.22)',
    },
] as const;

const COMING_SOON = [
    { id: 'networking',    title: 'Networking',       icon: Wifi },
    { id: 'system-design', title: 'System Design',    icon: LayoutTemplate },
    { id: 'sdlc',          title: 'SDLC',             icon: RefreshCcw },
    { id: 'os',            title: 'Operating Systems', icon: Cpu },
    { id: 'git',           title: 'Git & VCS',        icon: GitBranch },
    { id: 'dsa',           title: 'DSA',              icon: Code2 },
] as const;

function StatChip({ value, label, color }: { value: string; label: string; color: string }) {
    return (
        <div className="ph-stat-chip" style={{ '--ph-chip-color': color } as React.CSSProperties}>
            <span className="ph-stat-val">{value}</span>
            <span className="ph-stat-lbl">{label}</span>
        </div>
    );
}

export default function PracticeHomePage() {
    const { data: overview } = useDashboardOverview();
    const summary     = overview?.summary;
    const bySubject   = overview?.quiz_by_subject ?? [];
    const hasActivity = (summary?.quizzes_taken ?? 0) > 0;

    /* Best-candidate "focus area": lowest avg_score with at least 1 attempt */
    const focusSubject = bySubject.length > 0
        ? [...bySubject].sort((a, b) => a.avg_score - b.avg_score)[0]
        : null;

    return (
        <div className="practice-page">
            <div className="practice-inner">

                {/* ── Header ── */}
                <div className="prac-home-header">
                    <h1 className="prac-home-title">Practice</h1>
                    <p className="prac-home-subtitle">
                        {hasActivity
                            ? 'Keep building — consistent practice beats cramming.'
                            : 'Pick a category and start answering interview questions.'}
                    </p>
                </div>

                {/* ── Stats strip (shown once user has activity) ── */}
                {hasActivity && summary && (
                    <div className="ph-stats-row">
                        <StatChip
                            value={String(summary.quizzes_taken)}
                            label="Quizzes"
                            color="#6366f1"
                        />
                        <StatChip
                            value={`${summary.accuracy}%`}
                            label="Accuracy"
                            color={summary.accuracy >= 70 ? '#16a34a' : summary.accuracy >= 50 ? '#d97706' : '#dc2626'}
                        />
                        <StatChip
                            value={String(summary.total_questions_answered)}
                            label="Questions"
                            color="#0891b2"
                        />
                        <StatChip
                            value={summary.avg_quiz_score > 0 ? `${summary.avg_quiz_score}/10` : '—'}
                            label="Avg Score"
                            color="#7c3aed"
                        />
                    </div>
                )}

                {/* ── Focus area card (needs work suggestion) ── */}
                {focusSubject && (
                    <div className="ph-focus-card">
                        <div className="ph-focus-icon">
                            <Target size={18} />
                        </div>
                        <div className="ph-focus-body">
                            <span className="ph-focus-eyebrow">
                                {focusSubject.avg_score < 70 ? 'Needs work' : 'Keep going'}
                            </span>
                            <p className="ph-focus-title">{focusSubject.subject_title}</p>
                            <p className="ph-focus-sub">
                                {focusSubject.attempts} attempt{focusSubject.attempts !== 1 ? 's' : ''} · {focusSubject.avg_score}% avg score
                            </p>
                        </div>
                        <div className="ph-focus-score">
                            <div
                                className="ph-focus-ring"
                                style={{
                                    background: `conic-gradient(
                                        ${focusSubject.avg_score >= 70 ? '#16a34a' : focusSubject.avg_score >= 50 ? '#d97706' : '#dc2626'}
                                        ${focusSubject.avg_score * 3.6}deg,
                                        var(--gray-100) 0deg
                                    )`,
                                }}
                            >
                                <span>{focusSubject.avg_score}%</span>
                            </div>
                        </div>
                        <Link
                            to={`/practice/subjects/${focusSubject.subject_id}`}
                            state={{ subjectTitle: focusSubject.subject_title }}
                            className="ph-focus-btn"
                        >
                            Practice <ArrowRight size={13} />
                        </Link>
                    </div>
                )}

                {/* ── Available categories ── */}
                <div className="ph-section-label">
                    <Zap size={13} />
                    Available Now
                </div>

                <div className="ph-avail-grid">
                    {AVAILABLE.map((cat) => {
                        const Icon = cat.icon;
                        return (
                            <Link key={cat.id} to={cat.to} className="ph-avail-card">
                                <div className="ph-avail-card-top">
                                    <div
                                        className="ph-avail-icon"
                                        style={{ background: cat.gradient, boxShadow: `0 4px 14px ${cat.glow}` }}
                                    >
                                        <Icon size={22} strokeWidth={1.6} />
                                    </div>
                                    <span className="ph-avail-arrow">→</span>
                                </div>
                                <h3 className="ph-avail-title">{cat.title}</h3>
                                <p className="ph-avail-desc">{cat.desc}</p>
                                <div className="ph-avail-tags">
                                    {cat.tags.map(tag => (
                                        <span key={tag} className="ph-avail-tag">{tag}</span>
                                    ))}
                                </div>
                            </Link>
                        );
                    })}
                </div>

                {/* ── Coming soon — compact ── */}
                <div className="ph-coming-wrap">
                    <div className="ph-section-label">
                        <Sparkles size={13} />
                        Coming Soon
                    </div>
                    <div className="ph-coming-row">
                        {COMING_SOON.map((cat) => {
                            const Icon = cat.icon;
                            return (
                                <div key={cat.id} className="ph-coming-chip">
                                    <Icon size={13} />
                                    <span>{cat.title}</span>
                                </div>
                            );
                        })}
                    </div>
                </div>

                {/* ── Performance by subject (if any) ── */}
                {bySubject.length > 0 && (
                    <div className="ph-perf-section">
                        <div className="ph-section-label">
                            <TrendingUp size={13} />
                            Your Performance
                        </div>
                        <div className="ph-perf-list">
                            {bySubject.slice(0, 5).map((s) => {
                                const pct   = s.avg_score;
                                const color = pct >= 70 ? '#16a34a' : pct >= 50 ? '#d97706' : '#dc2626';
                                return (
                                    <Link
                                        key={s.subject_id}
                                        to={`/practice/subjects/${s.subject_id}`}
                                        state={{ subjectTitle: s.subject_title }}
                                        className="ph-perf-row"
                                    >
                                        <span className="ph-perf-name">{s.subject_title}</span>
                                        <div className="ph-perf-bar-wrap">
                                            <div className="ph-perf-bar">
                                                <div
                                                    className="ph-perf-fill"
                                                    style={{ width: `${pct}%`, background: color }}
                                                />
                                            </div>
                                            <span className="ph-perf-pct" style={{ color }}>{pct}%</span>
                                        </div>
                                        <ArrowRight size={13} className="ph-perf-arrow" />
                                    </Link>
                                );
                            })}
                        </div>
                    </div>
                )}

            </div>
        </div>
    );
}
