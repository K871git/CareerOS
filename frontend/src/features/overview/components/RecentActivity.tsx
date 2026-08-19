import { CheckCircle2, Target, Clock } from 'lucide-react';
import type { RecentActivity } from '../../../types/api';
import { timeAgo } from '../../../utils/time';

interface Props {
    items: RecentActivity[];
}

function ActivityIcon({ type }: { type: RecentActivity['type'] }) {
    if (type === 'quiz_completed') {
        return (
            <div className="prog-activity-icon prog-activity-icon--quiz">
                <Target size={15} />
            </div>
        );
    }
    return (
        <div className="prog-activity-icon">
            <CheckCircle2 size={15} />
        </div>
    );
}

export default function RecentActivityList({ items }: Props) {
    if (items.length === 0) {
        return (
            <div className="dash-empty dash-empty--compact">
                <Clock size={28} className="dash-empty-icon" />
                <p>No activity yet. Complete a lesson or quiz to see your history here.</p>
            </div>
        );
    }

    return (
        <ul className="prog-activity-list">
            {items.map((item, idx) => (
                <li key={`${item.type}-${item.created_at}-${idx}`} className="prog-activity-item">
                    <ActivityIcon type={item.type} />
                    <div className="prog-activity-content">
                        <p className="prog-activity-desc">{item.description}</p>
                        {item.subject_name && (
                            <span className="prog-activity-subject">{item.subject_name}</span>
                        )}
                    </div>
                    <span className="prog-activity-time">{timeAgo(item.created_at)}</span>
                </li>
            ))}
        </ul>
    );
}
