import type { MCQQuestion } from '../../../types/api';

interface Props {
    question: MCQQuestion;
    questionNumber: number;
}

export default function QuestionCard({ question, questionNumber }: Props) {
    return (
        <div className="q-card">
            <span className="q-card-num">Q{questionNumber}</span>
            <p className="q-card-text">{question.question}</p>
        </div>
    );
}
