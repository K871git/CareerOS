import { Clock, CheckCircle2 } from 'lucide-react';
import type { TheoryAnswer } from '../../../types/api';

interface Props {
    answer: TheoryAnswer;
}

export default function SubmissionStatus({ answer }: Props) {
    const isReviewed = answer.status === 'reviewed';

    return (
        <div className="submission-status-card">
            <span
                className={`status-badge ${isReviewed ? 'status-badge--reviewed' : 'status-badge--pending'}`}
            >
                {isReviewed ? (
                    <><CheckCircle2 size={13} /> Reviewed</>
                ) : (
                    <><Clock size={13} /> Pending Review</>
                )}
            </span>

            {!isReviewed && (
                <div className="pending-message">
                    <Clock size={16} />
                    <span>
                        Your answer has been submitted and is awaiting review. Check back later to see your feedback and score.
                    </span>
                </div>
            )}

            {isReviewed && (
                <div className="reviewed-section">
                    {answer.score !== null && (
                        <div>
                            <p className="reviewed-score-label">Score</p>
                            <div className="score-pill">
                                {answer.score}
                                <span className="score-pill-label">/ 10</span>
                            </div>
                        </div>
                    )}
                    {answer.feedback && (
                        <div className="feedback-block">
                            <p className="feedback-block-label">Instructor Feedback</p>
                            <p className="feedback-block-text">{answer.feedback}</p>
                        </div>
                    )}
                    {answer.explanation && (
                        <div className="explanation-block">
                            <p className="explanation-block-label">Ideal Answer</p>
                            <p className="explanation-block-text">{answer.explanation}</p>
                        </div>
                    )}
                </div>
            )}
        </div>
    );
}
