import { Link } from 'react-router-dom';
import { Target, BookOpen, ArrowRight, Layers } from 'lucide-react';
import type { DashboardSummary } from '../../../types/api';

export function SectionCards({ summary }: { summary: DashboardSummary }) {
    const practiceScore = summary.quizzes_taken > 0 ? summary.avg_quiz_score : null;
    const lessonPct     = summary.lessons_percentage ?? 0;

    return (
        <div className="dash-section-cards">

            {/* Practice shortcut */}
            <Link to="/practice" className="qcard qcard--practice">
                <div className="qcard-icon">
                    <Target size={20} />
                </div>
                <div className="qcard-body">
                    <div className="qcard-label">PRACTICE</div>
                    <div className="qcard-stat">
                        {practiceScore !== null
                            ? `${practiceScore}% avg · ${summary.quizzes_taken} quiz${summary.quizzes_taken !== 1 ? 'zes' : ''}`
                            : 'Not started yet'}
                    </div>
                    <div className="qcard-bar-track">
                        <div
                            className="qcard-bar-fill"
                            style={{ width: `${Math.min(summary.accuracy, 100)}%` }}
                        />
                    </div>
                </div>
                <div className="qcard-right">
                    {practiceScore !== null && (
                        <div className="qcard-big">{practiceScore}<span>%</span></div>
                    )}
                    <ArrowRight size={15} className="qcard-arrow" />
                </div>
            </Link>

            {/* Learning shortcut */}
            <Link to="/learning" className="qcard qcard--learning">
                <div className="qcard-icon">
                    <BookOpen size={20} />
                </div>
                <div className="qcard-body">
                    <div className="qcard-label">LEARNING</div>
                    <div className="qcard-stat">
                        {summary.lessons_completed > 0
                            ? `${summary.lessons_completed} lessons · ${summary.learning_levels_passed} level${summary.learning_levels_passed !== 1 ? 's' : ''} passed`
                            : 'Not started yet'}
                    </div>
                    <div className="qcard-bar-track">
                        <div className="qcard-bar-fill qcard-bar-fill--green" style={{ width: `${lessonPct}%` }} />
                    </div>
                </div>
                <div className="qcard-right">
                    {summary.learning_levels_passed > 0
                        ? <div className="qcard-big">{summary.learning_levels_passed}<span> lvl</span></div>
                        : <Layers size={18} className="qcard-icon-r" />}
                    <ArrowRight size={15} className="qcard-arrow" />
                </div>
            </Link>

        </div>
    );
}
