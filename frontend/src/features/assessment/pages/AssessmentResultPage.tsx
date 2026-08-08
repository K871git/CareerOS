import { useParams, Link, useNavigate } from 'react-router-dom';
import { CheckCircle2, XCircle } from 'lucide-react';
import { useAttemptResult } from '../hooks/useMCQ';
import ResultSummary from '../components/ResultSummary';
import '../assessment.css';

function ResultSkeleton() {
    return (
        <div className="practice-page">
            <div className="practice-inner">
                <div className="skeleton" style={{ height: 32, width: 110, borderRadius: 8, marginBottom: 28 }} />
                <div className="result-summary" style={{ background: 'transparent', border: 'none', padding: 0, gap: '1rem' }}>
                    <div className="skeleton" style={{ width: 136, height: 136, borderRadius: '50%' }} />
                    <div className="skeleton" style={{ height: 20, width: 160, borderRadius: 6 }} />
                    <div className="skeleton" style={{ height: 64, width: 280, borderRadius: 12 }} />
                </div>
                <div style={{ marginTop: '2rem', display: 'flex', flexDirection: 'column', gap: '0.75rem' }}>
                    {[0, 1, 2, 3].map((i) => (
                        <div key={i} className="skeleton" style={{ height: 100, borderRadius: 12 }} />
                    ))}
                </div>
            </div>
        </div>
    );
}

export default function AssessmentResultPage() {
    const { attemptId } = useParams<{ attemptId: string }>();
    const navigate = useNavigate();
    const id = Number(attemptId);

    const { data: result, isLoading } = useAttemptResult(id);

    if (isLoading) return <ResultSkeleton />;

    if (!result) {
        return (
            <div className="practice-page">
                <div className="practice-inner">
                    <div className="practice-empty">
                        <p>
                            Result not found.{' '}
                            <Link to="/practice" className="practice-link">Back to practice</Link>
                        </p>
                    </div>
                </div>
            </div>
        );
    }

    const subjectId = result.subject_id;
    const topicId   = result.topic_id;

    return (
        <div className="practice-page">
            <div className="practice-inner">
                <div style={{ display: 'flex', gap: '0.75rem', flexWrap: 'wrap', marginBottom: '0.5rem' }}>
                    {subjectId && (
                        <button
                            type="button"
                            className="practice-back-btn"
                            onClick={() => navigate(`/practice/subjects/${subjectId}`)}
                        >
                            ← Back to Levels
                        </button>
                    )}
                    {topicId && (
                        <button
                            type="button"
                            className="practice-start-btn"
                            style={{ fontSize: '0.85rem', padding: '0.45rem 1rem' }}
                            onClick={() => navigate(`/practice/topics/${topicId}`)}
                        >
                            Practice Again →
                        </button>
                    )}
                    {!subjectId && (
                        <button
                            type="button"
                            className="practice-back-btn"
                            onClick={() => navigate('/practice')}
                        >
                            ← New Practice
                        </button>
                    )}
                </div>

                <ResultSummary result={result} />

                <div className="review-section">
                    <h2 className="review-heading">Review Answers</h2>
                    <div className="review-list">
                        {result.answers.map((answer, i) => (
                            <div
                                key={answer.question_id}
                                className={`review-card ${answer.is_correct ? 'review-card--correct' : 'review-card--wrong'}`}
                            >
                                <div className="review-card-head">
                                    <div className="review-card-q">
                                        <span className="review-q-num">Q{i + 1}</span>
                                        <p className="review-q-text">{answer.question}</p>
                                    </div>
                                    {answer.is_correct ? (
                                        <span className="review-verdict review-verdict--correct">
                                            <CheckCircle2 size={15} /> Correct
                                        </span>
                                    ) : (
                                        <span className="review-verdict review-verdict--wrong">
                                            <XCircle size={15} /> Wrong
                                        </span>
                                    )}
                                </div>

                                <div className="review-answers-block">
                                    {!answer.is_correct && answer.selected_option && (
                                        <div className="review-answer review-answer--wrong">
                                            <span className="review-answer-label">Your answer</span>
                                            <span className="review-answer-text">{answer.selected_option}</span>
                                        </div>
                                    )}
                                    <div className="review-answer review-answer--correct">
                                        <span className="review-answer-label">Correct answer</span>
                                        <span className="review-answer-text">{answer.correct_option ?? '—'}</span>
                                    </div>
                                </div>

                                {answer.explanation && (
                                    <p className="review-explanation">{answer.explanation}</p>
                                )}
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
}
