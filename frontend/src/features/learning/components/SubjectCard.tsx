import { Link } from 'react-router-dom';
import { ChevronRight } from 'lucide-react';
import type { Subject } from '../../../types/api';

interface Props {
    subject: Subject;
    trackId: number;
}

export default function SubjectCard({ subject, trackId }: Props) {
    const num = String(subject.display_order).padStart(2, '0');

    return (
        <Link to={`/tracks/${trackId}/subjects/${subject.id}`} className="subject-card">
            <div className="subject-card-num">{num}</div>
            <div className="subject-card-body">
                <h3 className="subject-card-title">{subject.title}</h3>
                {subject.description && (
                    <p className="subject-card-desc">{subject.description}</p>
                )}
            </div>
            <ChevronRight size={18} className="subject-card-arrow" />
        </Link>
    );
}
