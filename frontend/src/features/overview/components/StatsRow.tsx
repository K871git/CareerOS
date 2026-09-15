import { Award, Brain, CheckCircle2, TrendingUp } from 'lucide-react';
import type { DashboardSummary } from '../../../types/api';

function statAccuracyCtx(quizzes: number, accuracy: number): string {
    if (quizzes === 0) return 'Take a quiz first';
    if (accuracy >= 80) return 'Excellent accuracy';
    if (accuracy >= 70) return 'Above target';
    if (accuracy >= 50) return 'Needs practice';
    return 'Focus area';
}

function statQuestionsCtx(n: number): string {
    if (n === 0) return 'Start practicing';
    if (n >= 50) return 'Veteran level';
    if (n >= 10) return 'Getting there';
    return 'Building up';
}

function statLessonsCtx(n: number): string {
    if (n === 0) return 'Read your first lesson';
    if (n >= 10) return 'Dedicated learner';
    if (n >= 5) return 'Consistent';
    return 'Keep reading';
}

function statLevelsCtx(n: number): string {
    if (n === 0) return 'Score 10/10 to pass';
    if (n === 1) return 'Level 1 mastered';
    return `${n} levels mastered`;
}

interface StatCardProps {
    value: string | number;
    label: string;
    context: string;
    icon: React.ElementType;
    variant?: 'default' | 'success' | 'warning' | 'danger';
}

function StatCard({ value, label, context, icon: Icon, variant = 'default' }: StatCardProps) {
    return (
        <div className={`stat-card stat-card--${variant}`}>
            <div className="stat-icon"><Icon size={18} /></div>
            <div className="stat-body">
                <div className="stat-value">{value}</div>
                <div className="stat-label">{label}</div>
                <div className="stat-context">{context}</div>
            </div>
        </div>
    );
}

export function StatsRow({ summary }: { summary: DashboardSummary }) {
    const accuracyVariant =
        summary.quizzes_taken === 0 ? 'default'
        : summary.accuracy >= 70 ? 'success'
        : summary.accuracy >= 50 ? 'warning'
        : 'danger';

    return (
        <div className="dash-stats">
            <StatCard
                value={summary.quizzes_taken === 0 ? '—' : `${summary.accuracy}%`}
                label="Quiz Accuracy"
                context={statAccuracyCtx(summary.quizzes_taken, summary.accuracy)}
                icon={Award}
                variant={accuracyVariant}
            />
            <StatCard
                value={summary.total_questions_answered}
                label="Questions Answered"
                context={statQuestionsCtx(summary.total_questions_answered)}
                icon={Brain}
            />
            <StatCard
                value={summary.lessons_completed}
                label="Lessons Completed"
                context={statLessonsCtx(summary.lessons_completed)}
                icon={CheckCircle2}
                variant={summary.lessons_completed > 0 ? 'success' : 'default'}
            />
            <StatCard
                value={summary.learning_levels_passed}
                label="Levels Passed"
                context={statLevelsCtx(summary.learning_levels_passed)}
                icon={TrendingUp}
                variant={summary.learning_levels_passed > 0 ? 'success' : 'default'}
            />
        </div>
    );
}
