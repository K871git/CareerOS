import { useParams, useNavigate } from 'react-router-dom';
import { useTheoryAnswer } from '../hooks/useTheory';
import SubmissionStatus from '../components/SubmissionStatus';
import '../theory.css';

function AnswerSkeleton() {
    return (
        <div className="theory-page">
            <div className="theory-inner">
                <div className="skeleton" style={{ height: 32, width: 130, borderRadius: 8 }} />
                <div className="skeleton" style={{ height: 90, borderRadius: 14 }} />
                <div className="skeleton" style={{ height: 130, borderRadius: 14 }} />
                <div className="skeleton" style={{ height: 160, borderRadius: 14 }} />
            </div>
        </div>
    );
}

export default function TheoryAnswerPage() {
    const { answerId } = useParams<{ answerId: string }>();
    const navigate = useNavigate();
    const id = Number(answerId);

    const { data: answer, isLoading } = useTheoryAnswer(id);

    if (isLoading) return <AnswerSkeleton />;

    if (!answer) {
        return (
            <div className="theory-page">
                <div className="theory-inner">
                    <div className="theory-empty">
                        <p>Answer not found.</p>
                        <button
                            type="button"
                            className="theory-back-btn"
                            style={{ marginTop: '0.75rem' }}
                            onClick={() => navigate('/theory')}
                        >
                            ← Back to theory questions
                        </button>
                    </div>
                </div>
            </div>
        );
    }

    const submittedDate = new Date(answer.submitted_at).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });

    return (
        <div className="theory-page">
            <div className="theory-inner">
                <button
                    type="button"
                    className="theory-back-btn"
                    onClick={() => navigate('/theory')}
                >
                    ← Theory Questions
                </button>

                <div className="theory-question-display">
                    <p className="theory-question-display-label">Question</p>
                    <p className="theory-question-display-text">{answer.question}</p>
                </div>

                <SubmissionStatus answer={answer} />

                <div className="theory-answer-display">
                    <p className="theory-answer-display-label">Your Answer</p>
                    <p className="theory-answer-display-text">{answer.answer}</p>
                </div>

                <p className="theory-submitted-at">Submitted on {submittedDate}</p>
            </div>
        </div>
    );
}
