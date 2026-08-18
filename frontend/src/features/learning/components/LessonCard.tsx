import { Link } from 'react-router-dom';
import { Clock, Play, CheckCircle2 } from 'lucide-react';
import type { Lesson } from '../../../types/api';

interface Props {
    lesson: Lesson;
    index: number;
    completed?: boolean;
}

export default function LessonCard({ lesson, index, completed = false }: Props) {
    return (
        <Link to={`/lessons/${lesson.id}`} className={`lesson-card${completed ? ' lesson-card--done' : ''}`}>
            <div className="lesson-card-num">{String(index + 1).padStart(2, '0')}</div>
            <div className="lesson-card-body">
                <span className="lesson-card-title">{lesson.title}</span>
                {lesson.estimated_minutes != null && lesson.estimated_minutes > 0 && (
                    <span className="lesson-card-meta">
                        <Clock size={12} /> {lesson.estimated_minutes} min
                    </span>
                )}
            </div>
            {completed
                ? <CheckCircle2 size={15} style={{ color: '#10b981', flexShrink: 0 }} />
                : <Play size={14} className="lesson-card-play" />
            }
        </Link>
    );
}
