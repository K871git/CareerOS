import { Link } from 'react-router-dom';
import { Clock } from 'lucide-react';
import { timeAgo } from '../../../utils/time';
import type { RecentAttempt } from '../../../types/api';

function scoreColor(pct: number) { return pct >= 70 ? 'score-good' : pct >= 50 ? 'score-warn' : 'score-bad'; }

export function RecentAttemptsList({ attempts }: { attempts: RecentAttempt[] }) {
    const list = Array.isArray(attempts) ? attempts : [];
    if (list.length === 0) {
        return (
            <div className="dash-empty">
                <Clock size={34} className="dash-empty-icon" />
                <p>No quiz attempts yet. <Link to="/practice">Take a quiz</Link> to see your history here.</p>
            </div>
        );
    }
    return (
        <ul className="attempt-list">
            {list.map(a => (
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
