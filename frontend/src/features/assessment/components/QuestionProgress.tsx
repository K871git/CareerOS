import type { QuestionDifficulty } from '../../../types/api';

interface Props {
    current: number;
    total: number;
    answered: number;
    difficulty: QuestionDifficulty;
}

const DIFF_CLASS: Record<QuestionDifficulty, string> = {
    Easy:   'q-diff--easy',
    Medium: 'q-diff--medium',
    Hard:   'q-diff--hard',
};

export default function QuestionProgress({ current, total, answered, difficulty }: Props) {
    const pct = total > 0 ? ((current - 1) / total) * 100 : 0;

    return (
        <div className="q-progress">
            <div className="q-progress-meta">
                <span className="q-progress-label">Question {current} of {total}</span>
                <div className="q-progress-right">
                    <span className="q-answered-text">{answered} / {total} answered</span>
                    <span className={`q-diff ${DIFF_CLASS[difficulty]}`}>{difficulty}</span>
                </div>
            </div>
            <div className="q-progress-track">
                <div className="q-progress-fill" style={{ width: `${Math.max(pct, 2)}%` }} />
            </div>
        </div>
    );
}
