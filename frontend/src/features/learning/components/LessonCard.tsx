import { Link } from 'react-router-dom';
import { Clock, Play } from 'lucide-react';
import type { Lesson } from '../../../types/api';

interface Props {
    lesson: Lesson;
    index: number;
}

export default function LessonCard({ lesson, index }: Props) {
    return (
        <Link to={`/lessons/${lesson.id}`} className="lesson-card">
            <div className="lesson-card-num">{String(index + 1).padStart(2, '0')}</div>
            <div className="lesson-card-body">
                <span className="lesson-card-title">{lesson.title}</span>
                {lesson.estimated_minutes != null && lesson.estimated_minutes > 0 && (
                    <span className="lesson-card-meta">
                        <Clock size={12} /> {lesson.estimated_minutes} min
                    </span>
                )}
            </div>
            <Play size={14} className="lesson-card-play" />
        </Link>
    );
}
