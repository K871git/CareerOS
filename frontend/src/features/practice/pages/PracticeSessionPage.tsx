import { useState } from 'react';
import { useParams, useNavigate, useLocation } from 'react-router-dom';
import { useQuestions, useSubmitAttempt } from '../../assessment/hooks/useMCQ';
import QuestionProgress from '../../assessment/components/QuestionProgress';
import QuestionCard from '../../assessment/components/QuestionCard';
import QuestionOption from '../../assessment/components/QuestionOption';
import '../../assessment/assessment.css';

export default function PracticeSessionPage() {
    const { topicId } = useParams<{ topicId: string }>();
    const id = Number(topicId);
    const navigate = useNavigate();
    const location = useLocation();
    const topicTitle = (location.state as any)?.topicTitle as string | undefined;

    const [currentIndex, setCurrentIndex] = useState(0);
    const [answers, setAnswers] = useState<Record<number, number>>({});
    const [submitError, setSubmitError] = useState<string | null>(null);

    const { data: questions = [], isLoading } = useQuestions(id);
    const submitAttempt = useSubmitAttempt();

    function handleSelectAnswer(questionId: number, optionId: number) {
        setAnswers((prev) => ({ ...prev, [questionId]: optionId }));
        setSubmitError(null);
    }

    function handleNext() {
        if (currentIndex < questions.length - 1) setCurrentIndex((i) => i + 1);
    }

    function handlePrev() {
        if (currentIndex > 0) setCurrentIndex((i) => i - 1);
    }

    function handleSubmit() {
        const firstUnanswered = questions.findIndex((q) => answers[q.id] === undefined);
        if (firstUnanswered !== -1) {
            setCurrentIndex(firstUnanswered);
            setSubmitError(
                `Answer all ${questions.length} questions first. Jumped to question ${firstUnanswered + 1}.`
            );
            return;
        }
        submitAttempt.mutate({
            answers: questions.map((q) => ({
                question_id: q.id,
                selected_option_id: answers[q.id],
            })),
        });
    }

    // ── Loading ──────────────────────────────────────────────────────────
    if (isLoading) {
        return (
            <div className="practice-page">
                <div className="practice-inner">
                    <div className="skeleton" style={{ height: 10, borderRadius: 8, marginBottom: 24 }} />
                    <div className="skeleton" style={{ height: 96, borderRadius: 14, marginBottom: 16 }} />
                    {[0, 1, 2, 3].map((i) => (
                        <div key={i} className="skeleton" style={{ height: 60, borderRadius: 10, marginBottom: 10 }} />
                    ))}
                </div>
            </div>
        );
    }

    // ── Empty ────────────────────────────────────────────────────────────
    if (questions.length === 0) {
        return (
            <div className="practice-page">
                <div className="practice-inner">
                    <div className="practice-empty">
                        <p>No questions available for this topic yet.</p>
                        <button
                            type="button"
                            className="practice-back-link"
                            style={{ marginTop: '1rem', display: 'block' }}
                            onClick={() => navigate(-1)}
                        >
                            ← Go back
                        </button>
                    </div>
                </div>
            </div>
        );
    }

    // ── Quiz ─────────────────────────────────────────────────────────────
    const currentQuestion = questions[currentIndex];
    const isLastQuestion = currentIndex === questions.length - 1;
    const answeredCount = Object.keys(answers).length;

    return (
        <div className="practice-page">
            <div className="practice-inner">
                <button
                    type="button"
                    className="practice-back-btn"
                    onClick={() => navigate(-1)}
                    disabled={submitAttempt.isPending}
                >
                    ← {topicTitle ?? 'Back'}
                </button>

                <QuestionProgress
                    current={currentIndex + 1}
                    total={questions.length}
                    answered={answeredCount}
                    difficulty={currentQuestion.difficulty}
                />

                <QuestionCard question={currentQuestion} questionNumber={currentIndex + 1} />

                <div className="q-options-list">
                    {currentQuestion.options.map((opt, i) => (
                        <QuestionOption
                            key={opt.id}
                            option={opt}
                            index={i}
                            isSelected={answers[currentQuestion.id] === opt.id}
                            onSelect={() => handleSelectAnswer(currentQuestion.id, opt.id)}
                            disabled={submitAttempt.isPending}
                        />
                    ))}
                </div>

                {submitError && <p className="quiz-error">{submitError}</p>}

                <div className="quiz-nav">
                    <button
                        type="button"
                        className="quiz-nav-btn"
                        onClick={handlePrev}
                        disabled={currentIndex === 0 || submitAttempt.isPending}
                    >
                        ← Previous
                    </button>

                    <span className="quiz-nav-dots">
                        {questions.map((q, i) => (
                            <button
                                key={q.id}
                                type="button"
                                className={[
                                    'quiz-dot',
                                    i === currentIndex ? 'quiz-dot--current' : '',
                                    answers[q.id] !== undefined ? 'quiz-dot--answered' : '',
                                ]
                                    .filter(Boolean)
                                    .join(' ')}
                                onClick={() => setCurrentIndex(i)}
                                aria-label={`Question ${i + 1}`}
                            />
                        ))}
                    </span>

                    {isLastQuestion ? (
                        <button
                            type="button"
                            className="practice-start-btn"
                            onClick={handleSubmit}
                            disabled={submitAttempt.isPending}
                        >
                            {submitAttempt.isPending
                                ? 'Submitting...'
                                : `Submit (${answeredCount}/${questions.length})`}
                        </button>
                    ) : (
                        <button
                            type="button"
                            className="quiz-nav-btn"
                            onClick={handleNext}
                            disabled={submitAttempt.isPending}
                        >
                            Next →
                        </button>
                    )}
                </div>
            </div>
        </div>
    );
}
