import { Link } from 'react-router-dom';
import { BarChart2 } from 'lucide-react';
import type { QuizBySubject } from '../../../types/api';

export function scoreColor(pct: number) { return pct >= 70 ? 'score-good' : pct >= 50 ? 'score-warn' : 'score-bad'; }
export function scoreBarColor(pct: number) { return pct >= 70 ? 'bar-good' : pct >= 50 ? 'bar-warn' : 'bar-bad'; }

export function SubjectPerformance({ subjects }: { subjects: QuizBySubject[] }) {
    const list = Array.isArray(subjects) ? subjects : [];
    if (list.length === 0) {
        return (
            <div className="dash-empty">
                <BarChart2 size={34} className="dash-empty-icon" />
                <p>No quiz data yet. <Link to="/practice">Start practising</Link> to see performance by subject.</p>
            </div>
        );
    }
    return (
        <div className="subject-perf-list">
            {list.map(s => (
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
