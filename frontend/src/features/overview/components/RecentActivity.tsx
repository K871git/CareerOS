import { Target, BookOpen, Clock } from 'lucide-react';
import type { RecentActivity } from '../../../types/api';
import { timeAgo } from '../../../utils/time';

function ActivityDot({ type, score }: { type: RecentActivity['type']; score?: number }) {
    if (type === 'quiz_completed') {
        const color = score !== undefined
            ? score >= 70 ? '#22c55e' : score >= 50 ? '#f59e0b' : '#ef4444'
            : '#6366f1';
        return (
            <div className="act-dot" style={{ background: color, boxShadow: `0 0 0 4px ${color}22` }}>
                <Target size={11} />
            </div>
        );
    }
    return (
        <div className="act-dot" style={{ background: '#059669', boxShadow: '0 0 0 4px rgba(5,150,105,0.15)' }}>
            <BookOpen size={11} />
        </div>
    );
}

export default function RecentActivityList({ items }: { items: RecentActivity[] }) {
    if (items.length === 0) {
        return (
            <div className="dash-empty dash-empty--compact">
                <Clock size={28} className="dash-empty-icon" />
                <p>No activity yet. Complete a lesson or quiz to build your history.</p>
            </div>
        );
    }

    return (
        <div className="act-timeline">
            {items.map((item, idx) => (
                <div key={`${item.type}-${item.created_at}-${idx}`} className="act-item">
                    {/* Vertical line connecting dots */}
                    {idx < items.length - 1 && <div className="act-line" />}

                    <ActivityDot type={item.type} score={item.score} />

                    <div className="act-content">
                        <p className="act-desc">{item.description}</p>
                        <div className="act-meta">
                            {item.subject_name && (
                                <span className="act-subject">{item.subject_name}</span>
                            )}
                            <span className="act-time">{timeAgo(item.created_at)}</span>
                        </div>
                    </div>
                </div>
            ))}
        </div>
    );
}
