import type { TheoryQuestion } from '../../../types/api';

const DIFF_CLASS: Record<string, string> = {
    Easy: 'q-diff--easy',
    Medium: 'q-diff--medium',
    Hard: 'q-diff--hard',
};

interface Props {
    question: TheoryQuestion;
    questionNumber: number;
    onAnswer: () => void;
}

export default function TheoryQuestionCard({ question, questionNumber, onAnswer }: Props) {
    return (
        <div className={`theory-q-card theory-q-card--${question.difficulty.toLowerCase()}`}>
            <div className="theory-q-card-top">
                <span className="theory-q-num">Q{questionNumber}</span>
                <span className={`q-diff ${DIFF_CLASS[question.difficulty] ?? ''}`}>
                    {question.difficulty}
                </span>
            </div>
            <p className="theory-q-text">{question.question}</p>
            <div className="theory-q-card-footer">
                <button
                    type="button"
                    className="theory-answer-btn"
                    onClick={onAnswer}
                >
                    Answer this question
                </button>
            </div>
        </div>
    );
}
