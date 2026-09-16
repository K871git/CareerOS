import { Zap } from 'lucide-react';
import type { DashboardSummary, DashboardUserSkill } from '../../../types/api';

const LEVELS = ['Beginner', 'Developing', 'Proficient', 'Advanced'];

const LEVEL_COLORS = [
    { bg: 'rgba(148,163,184,0.15)', fill: '#94a3b8' },
    { bg: 'rgba(99,102,241,0.12)',  fill: '#6366f1' },
    { bg: 'rgba(124,58,237,0.12)',  fill: '#7c3aed' },
    { bg: 'rgba(245,158,11,0.14)',  fill: '#f59e0b' },
];

export function SkillLevelCard({
    summary,
    userSkills,
}: {
    summary: DashboardSummary;
    userSkills: DashboardUserSkill[];
}) {
    const level  = summary.skill_level ?? 0;            // 0-4
    const label  = summary.skill_label ?? 'Not Started';
    const color  = LEVEL_COLORS[Math.min(level - 1, 3)];
    const radius = 48;
    const circ   = 2 * Math.PI * radius;
    const pct    = level > 0 ? (level / 4) * 100 : 0;
    const dash   = circ - (pct / 100) * circ;

    return (
        <div className="dash-card slcard">
            <div className="dash-card-header">
                <h2 className="dash-card-title">Your Level</h2>
                <Zap size={15} style={{ color: color?.fill ?? '#94a3b8' }} />
            </div>

            {/* Circular gauge */}
            <div className="slcard-gauge-wrap">
                <svg className="slcard-ring" viewBox="0 0 120 120" width="120" height="120">
                    <circle
                        cx="60" cy="60" r={radius}
                        fill="none"
                        stroke="var(--gray-100)"
                        strokeWidth="10"
                    />
                    {level > 0 && (
                        <circle
                            cx="60" cy="60" r={radius}
                            fill="none"
                            stroke={color?.fill ?? '#4f46e5'}
                            strokeWidth="10"
                            strokeLinecap="round"
                            strokeDasharray={circ}
                            strokeDashoffset={dash}
                            transform="rotate(-90 60 60)"
                            style={{ filter: `drop-shadow(0 0 6px ${color?.fill ?? '#4f46e5'}88)`, transition: 'stroke-dashoffset 1.2s cubic-bezier(0.4,0,0.2,1)' }}
                        />
                    )}
                    <text x="60" y="56" textAnchor="middle" className="slcard-ring-label">
                        {label}
                    </text>
                    <text x="60" y="72" textAnchor="middle" className="slcard-ring-sub">
                        Level {level}/4
                    </text>
                </svg>
            </div>

            {/* Segment bar */}
            <div className="slcard-bar">
                {LEVELS.map((name, i) => (
                    <div
                        key={name}
                        className={`slcard-seg${i < level ? ' slcard-seg--active' : ''}`}
                        style={i < level ? { background: LEVEL_COLORS[i]?.fill } : undefined}
                        title={name}
                    />
                ))}
            </div>
            <div className="slcard-seg-labels">
                {LEVELS.map(n => <span key={n}>{n}</span>)}
            </div>

            <p className="slcard-basis">
                {level === 0
                    ? 'Complete quizzes to determine your level.'
                    : `Based on ${summary.quizzes_taken} quiz${summary.quizzes_taken !== 1 ? 'zes' : ''} · ${summary.accuracy}% accuracy`}
            </p>

            {userSkills.length > 0 && (
                <div className="slcard-chips-wrap">
                    <div className="slcard-chips-label">Assessed Skills</div>
                    <div className="slcard-chips">
                        {userSkills.slice(0, 6).map(s => (
                            <span key={s.name} className="skill-chip">{s.name}</span>
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
}
