import type { Topic } from '../../../types/api';

interface Props {
    topic: Topic;
    isSelected: boolean;
    onSelect: () => void;
}

export default function TopicCard({ topic, isSelected, onSelect }: Props) {
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
            </div>
        </button>
    );
}
