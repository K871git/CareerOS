import { CheckCircle2, TrendingUp, BookOpen, Award } from 'lucide-react';
import type { UserProgress } from '../../../types/api';

interface Props {
    data: UserProgress;
}

export default function ProgressCard({ data }: Props) {
    const { summary, tracks } = data;
    const tracksCompleted = tracks.filter((t) => t.percentage >= 100).length;

    return (
        <div className="prog-summary-grid">
            <div className="prog-stat-card">
                <div className="prog-stat-icon prog-stat-icon--blue">
                    <CheckCircle2 size={20} />
                </div>
                <div className="prog-stat-value">{summary.completed_lessons}</div>
                <div className="prog-stat-label">Lessons Completed</div>
            </div>
            <div className="prog-stat-card">
                <div className="prog-stat-icon prog-stat-icon--green">
                    <TrendingUp size={20} />
                </div>
                <div className="prog-stat-value">{summary.percentage}%</div>
                <div className="prog-stat-label">Overall Completion</div>
            </div>
            <div className="prog-stat-card">
                <div className="prog-stat-icon prog-stat-icon--purple">
                    <BookOpen size={20} />
                </div>
                <div className="prog-stat-value">{tracks.length}</div>
                <div className="prog-stat-label">Tracks Enrolled</div>
            </div>
            <div className="prog-stat-card">
                <div className="prog-stat-icon prog-stat-icon--amber">
                    <Award size={20} />
                </div>
                <div className="prog-stat-value">{tracksCompleted}</div>
                <div className="prog-stat-label">Tracks Completed</div>
            </div>
        </div>
    );
}
