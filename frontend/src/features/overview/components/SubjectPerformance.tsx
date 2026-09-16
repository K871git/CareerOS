import { Link } from 'react-router-dom';
import { BarChart2, Zap } from 'lucide-react';
import type { QuizBySubject } from '../../../types/api';

export function scoreColor(pct: number) {
    return pct >= 70 ? 'score-good' : pct >= 50 ? 'score-warn' : 'score-bad';
}

function barGradient(pct: number): string {
    if (pct >= 70) return 'linear-gradient(90deg,#22c55e,#16a34a)';
    if (pct >= 50) return 'linear-gradient(90deg,#f59e0b,#d97706)';
    return 'linear-gradient(90deg,#f87171,#dc2626)';
}

function glowColor(pct: number): string {
    if (pct >= 70) return 'rgba(34,197,94,0.35)';
    if (pct >= 50) return 'rgba(245,158,11,0.35)';
    return 'rgba(248,113,113,0.35)';
}

export function SubjectPerformance({ subjects }: { subjects: QuizBySubject[] }) {
    const list = Array.isArray(subjects) ? subjects : [];

    if (list.length === 0) {
        return (
            <div className="dash-empty">
                <BarChart2 size={34} className="dash-empty-icon" />
                <p>No quiz data yet. <Link to="/practice">Start practising</Link> to see your performance here.</p>
            </div>
        );
    }

    return (
        <div className="subj-list">
            {list.map((s, i) => (
                <div
                    key={s.subject_id}
                    className="subj-row"
                    style={{ animationDelay: `${i * 60}ms` }}
                >
                    <div className="subj-row-top">
                        <span className="subj-name">{s.subject_title}</span>
                        <div className="subj-right">
                            <span className="subj-attempts">{s.attempts} attempt{s.attempts !== 1 ? 's' : ''} · {s.total_questions} Qs</span>
                            <span
                                className="subj-pct"
                                style={{ color: s.avg_score >= 70 ? '#16a34a' : s.avg_score >= 50 ? '#d97706' : '#dc2626' }}
                            >
                                {s.avg_score}%
                            </span>
                        </div>
                    </div>
                    <div className="subj-bar-track">
                        <div
                            className="subj-bar-fill"
                            style={{
                                width:      `${s.avg_score}%`,
                                background: barGradient(s.avg_score),
                                boxShadow:  `0 0 8px 0 ${glowColor(s.avg_score)}`,
                                '--bar-w':  `${s.avg_score}%`,
                            } as React.CSSProperties}
                        />
                    </div>
                    {/* Performance label */}
                    <div className="subj-tag-row">
                        {s.avg_score >= 80 && <span className="subj-tag subj-tag--great"><Zap size={9} /> Excellent</span>}
                        {s.avg_score >= 70 && s.avg_score < 80 && <span className="subj-tag subj-tag--good">On track</span>}
                        {s.avg_score >= 50 && s.avg_score < 70 && <span className="subj-tag subj-tag--warn">Needs work</span>}
                        {s.avg_score < 50 && <span className="subj-tag subj-tag--bad">Weak area</span>}
                    </div>
                </div>
            ))}
        </div>
    );
}

export function scoreBarColor(pct: number) {
    return pct >= 70 ? 'bar-good' : pct >= 50 ? 'bar-warn' : 'bar-bad';
}
