import { CheckCircle2, TrendingUp, Target, Zap, Brain } from 'lucide-react';
import type { UserProgress } from '../../../types/api';

interface Props {
    data: UserProgress;
}

export default function ProgressCard({ data }: Props) {
    const { summary } = data;

    return (
        <div className="prog-summary-grid">
            <div className="prog-stat-card">
                <div className="prog-stat-icon prog-stat-icon--blue">
                    <CheckCircle2 size={20} />
                </div>
                <div className="prog-stat-value">
                    {summary.completed_lessons}
                    <span className="prog-stat-total">/{summary.total_lessons}</span>
                </div>
                <div className="prog-stat-label">Lessons Done</div>
            </div>

            <div className="prog-stat-card">
                <div className="prog-stat-icon prog-stat-icon--green">
                    <TrendingUp size={20} />
                </div>
                <div className="prog-stat-value">{summary.percentage}%</div>
                <div className="prog-stat-label">Learning Progress</div>
            </div>

            <div className="prog-stat-card">
                <div className="prog-stat-icon prog-stat-icon--purple">
                    <Target size={20} />
                </div>
                <div className="prog-stat-value">{summary.quizzes_taken}</div>
                <div className="prog-stat-label">Quizzes Taken</div>
            </div>

            <div className="prog-stat-card">
                <div className="prog-stat-icon prog-stat-icon--amber">
                    <Zap size={20} />
                </div>
                <div className="prog-stat-value">{summary.accuracy}%</div>
                <div className="prog-stat-label">Quiz Accuracy</div>
            </div>

            <div className="prog-stat-card">
                <div className="prog-stat-icon prog-stat-icon--indigo">
                    <Brain size={20} />
                </div>
                <div className="prog-stat-value">
                    {summary.theory_levels_passed}
                    <span className="prog-stat-total">/{summary.theory_levels_total}</span>
                </div>
                <div className="prog-stat-label">Theory Levels</div>
            </div>
        </div>
    );
}
