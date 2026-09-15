import { Link } from 'react-router-dom';
import { CheckCircle2, AlertTriangle, ArrowRight } from 'lucide-react';
import type { WeakArea } from '../../../types/api';

function scoreColor(pct: number) { return pct >= 70 ? 'score-good' : pct >= 50 ? 'score-warn' : 'score-bad'; }

export function WeakAreasList({ areas }: { areas: WeakArea[] }) {
    const list = Array.isArray(areas) ? areas : [];
    if (list.length === 0) {
        return (
            <div className="dash-empty dash-empty--compact">
                <CheckCircle2 size={28} className="dash-empty-icon" style={{ color: '#16a34a', opacity: 1 }} />
                <p style={{ color: 'var(--text-secondary)' }}>All attempted topics score above 70%.</p>
            </div>
        );
    }
    return (
        <ul className="weak-list">
            {list.map(area => (
                <li key={area.topic_id} className="weak-item">
                    <div className="weak-item-left">
                        <AlertTriangle size={14} className="weak-icon" />
                        <div>
                            <span className="weak-topic-name">{area.topic_title}</span>
                            <span className="weak-subject-tag">{area.subject_title}</span>
                        </div>
                    </div>
                    <div className="weak-item-right">
                        <span className={`weak-score ${scoreColor(area.avg_score)}`}>{area.avg_score}%</span>
                        <Link to={`/practice/topics/${area.topic_id}`} className="weak-retry-link">
                            Retry <ArrowRight size={11} />
                        </Link>
                    </div>
                </li>
            ))}
        </ul>
    );
}
