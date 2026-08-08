import { CheckCircle2, Clock } from 'lucide-react';
import type { RecentActivity } from '../../../types/api';
import { timeAgo } from '../../../utils/time';

interface Props {
    items: RecentActivity[];
}

export default function RecentActivityList({ items }: Props) {
    if (items.length === 0) {
        return (
            <div className="prog-empty">
                <Clock size={36} className="prog-empty-icon" />
                <p>No activity yet. Complete a lesson to see your history here.</p>
            </div>
        );
    }

    return (
        <ul className="prog-activity-list">
            {items.map((item) => (
                <li key={item.id} className="prog-activity-item">
                    <div className="prog-activity-icon">
                        <CheckCircle2 size={15} />
                    </div>
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
