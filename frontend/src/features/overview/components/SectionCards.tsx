import { Link } from 'react-router-dom';
import { Target, BookOpen, ArrowRight } from 'lucide-react';
import type { DashboardSummary } from '../../../types/api';

export function SectionCards({ summary }: { summary: DashboardSummary }) {
    return (
        <div className="dash-section-cards">
            <Link to="/practice" className="dash-section-card">
                <div className="dash-section-icon dash-section-icon--practice"><Target size={18} /></div>
                <div className="dash-section-body">
                    <div className="dash-section-label">Practice</div>
                    <div className="dash-section-stat">
                        {summary.quizzes_taken > 0
                            ? `${summary.avg_quiz_score}% avg · ${summary.quizzes_taken} quiz${summary.quizzes_taken === 1 ? '' : 'zes'}`
                            : 'Not started yet'}
                    </div>
                    <div className="dash-section-bar">
                        <div className="dash-section-fill dash-section-fill--practice"
                            style={{ width: `${Math.min(summary.accuracy, 100)}%` }} />
                    </div>
                </div>
                <ArrowRight size={14} className="dash-section-arrow" />
            </Link>

            <Link to="/learning" className="dash-section-card">
                <div className="dash-section-icon dash-section-icon--learning"><BookOpen size={18} /></div>
                <div className="dash-section-body">
                    <div className="dash-section-label">Learning</div>
                    <div className="dash-section-stat">
                        {summary.lessons_completed > 0
                            ? `${summary.lessons_completed} lessons · ${summary.learning_levels_passed} levels passed`
                            : 'Not started yet'}
                    </div>
                    <div className="dash-section-bar">
                        <div className="dash-section-fill dash-section-fill--learning"
                            style={{ width: `${summary.lessons_percentage}%` }} />
                    </div>
                </div>
                <ArrowRight size={14} className="dash-section-arrow" />
            </Link>
        </div>
    );
}
