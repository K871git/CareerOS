import type { Topic } from '../../../types/api';

interface Props {
    topic: Topic;
    isSelected: boolean;
    onSelect: () => void;
    totalLessons?: number;
    completedLessons?: number;
}

export default function TopicCard({ topic, isSelected, onSelect, totalLessons, completedLessons = 0 }: Props) {
    const showProgress = totalLessons !== undefined && totalLessons > 0;
    const pct = showProgress ? Math.round((completedLessons / totalLessons) * 100) : 0;

    return (
        <button
            type="button"
            className={`topic-card${isSelected ? ' topic-card--active' : ''}`}
            onClick={onSelect}
        >
            <span className="topic-card-num">
                {String(topic.display_order).padStart(2, '0')}
            </span>
            <div className="topic-card-body">
                <span className="topic-card-title">{topic.title}</span>
                {topic.description && (
                    <span className="topic-card-desc">{topic.description}</span>
                )}
                {showProgress && (
                    <div className="topic-card-progress">
                        <span className="topic-prog-label">
                            {completedLessons}/{totalLessons} lessons done
                        </span>
                        <div className="topic-prog-track">
                            <div className="topic-prog-fill" style={{ width: `${pct}%` }} />
                        </div>
                    </div>
                )}
            </div>
        </button>
    );
}
