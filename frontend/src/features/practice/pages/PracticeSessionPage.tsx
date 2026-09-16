import { useState, useEffect, useRef, useCallback } from 'react';
import { useParams, useNavigate, useLocation } from 'react-router-dom';
import { Bookmark, BookmarkCheck, Clock, TimerOff } from 'lucide-react';
import { useQuestions, useSubmitAttempt } from '../../assessment/hooks/useMCQ';
import QuestionProgress from '../../assessment/components/QuestionProgress';
import QuestionCard from '../../assessment/components/QuestionCard';
import QuestionOption from '../../assessment/components/QuestionOption';
import PageLoader from '../../../components/ui/PageLoader';
import '../../assessment/assessment.css';

const TIMER_SECONDS = 15 * 60; // 15 minutes

function formatTime(s: number) {
    const m = Math.floor(s / 60);
    const sec = s % 60;
    return `${m}:${sec.toString().padStart(2, '0')}`;
}

export default function PracticeSessionPage() {
    const { topicId } = useParams<{ topicId: string }>();
    const id = Number(topicId);
    const navigate = useNavigate();
    const location = useLocation();
    const topicTitle = (location.state as any)?.topicTitle as string | undefined;

    const [currentIndex, setCurrentIndex] = useState(0);
    const [answers, setAnswers] = useState<Record<number, number>>({});
    const [flagged, setFlagged]   = useState<Set<number>>(new Set());
    const [submitError, setSubmitError] = useState<string | null>(null);

    // Timer
    const [timerOn, setTimerOn]     = useState(false);
    const [timeLeft, setTimeLeft]   = useState(TIMER_SECONDS);
    const timerRef = useRef<ReturnType<typeof setInterval> | null>(null);

    const { data: questions = [], isLoading } = useQuestions(id);
    const submitAttempt = useSubmitAttempt();

    // ── Timer logic ────────────────────────────────────────────────────
    useEffect(() => {
        if (timerOn && timeLeft > 0) {
            timerRef.current = setInterval(() => setTimeLeft((t) => t - 1), 1000);
        } else if (timerRef.current) {
            clearInterval(timerRef.current);
        }
        return () => { if (timerRef.current) clearInterval(timerRef.current); };
    }, [timerOn, timeLeft]);

    // Auto-submit when timer hits zero
    useEffect(() => {
        if (timerOn && timeLeft === 0) handleSubmit();
    }, [timerOn, timeLeft]); // eslint-disable-line react-hooks/exhaustive-deps

    // ── Handlers ───────────────────────────────────────────────────────
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

    function toggleFlag(questionId: number) {
        setFlagged((prev) => {
            const next = new Set(prev);
            next.has(questionId) ? next.delete(questionId) : next.add(questionId);
            return next;
        });
    }

    // ── Keyboard navigation ────────────────────────────────────────────
    const handleKeyDown = useCallback((e: KeyboardEvent) => {
        if (submitAttempt.isPending || isLoading || questions.length === 0) return;
        const tag = (e.target as HTMLElement).tagName;
        if (tag === 'INPUT' || tag === 'TEXTAREA') return;

        const currentQuestion = questions[currentIndex];

        if (e.key === 'ArrowRight' || e.key === ' ') {
            e.preventDefault();
            if (currentIndex < questions.length - 1) setCurrentIndex((i) => i + 1);
        } else if (e.key === 'ArrowLeft') {
            e.preventDefault();
            if (currentIndex > 0) setCurrentIndex((i) => i - 1);
        } else if (e.key === 'Enter' && currentIndex === questions.length - 1) {
            handleSubmit();
        } else if (e.key === 'b' || e.key === 'B') {
            toggleFlag(currentQuestion.id);
        } else {
            const num = parseInt(e.key, 10);
            if (num >= 1 && num <= currentQuestion.options.length) {
                const opt = currentQuestion.options[num - 1];
                handleSelectAnswer(currentQuestion.id, opt.id);
            }
        }
    }, [currentIndex, questions, submitAttempt.isPending, isLoading]); // eslint-disable-line react-hooks/exhaustive-deps

    useEffect(() => {
        window.addEventListener('keydown', handleKeyDown);
        return () => window.removeEventListener('keydown', handleKeyDown);
    }, [handleKeyDown]);

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

    // ── Submitting overlay ───────────────────────────────────────────────
    if (submitAttempt.isPending) {
        return <PageLoader label="Calculating Results" hint="Saving your answers…" />;
    }

    // ── Quiz ─────────────────────────────────────────────────────────────
    const currentQuestion = questions[currentIndex];
    const isLastQuestion  = currentIndex === questions.length - 1;
    const answeredCount   = Object.keys(answers).length;
    const isFlagged       = flagged.has(currentQuestion.id);
    const timerWarning    = timerOn && timeLeft <= 60;

    return (
        <div className="practice-page">
            <div className="practice-inner">
                {/* Top bar: back + timer */}
                <div className="quiz-top-bar">
                    <button
                        type="button"
                        className="practice-back-btn"
                        onClick={() => navigate(-1)}
                        disabled={submitAttempt.isPending}
                    >
                        ← {topicTitle ?? 'Back'}
                    </button>

                    <div className="quiz-top-right">
                        {timerOn && (
                            <span className={`quiz-timer ${timerWarning ? 'quiz-timer--warn' : ''}`}>
                                <Clock size={13} />
                                {formatTime(timeLeft)}
                            </span>
                        )}
                        <button
                            type="button"
                            className="quiz-icon-btn"
                            title={timerOn ? 'Stop timer' : 'Start 15-min timer'}
                            onClick={() => { setTimerOn(p => !p); setTimeLeft(TIMER_SECONDS); }}
                        >
                            {timerOn ? <TimerOff size={16} /> : <Clock size={16} />}
                        </button>
                    </div>
                </div>

                <QuestionProgress
                    current={currentIndex + 1}
                    total={questions.length}
                    answered={answeredCount}
                    difficulty={currentQuestion.difficulty}
                />

                {/* Question card + flag button */}
                <div className="quiz-card-row">
                    <QuestionCard question={currentQuestion} questionNumber={currentIndex + 1} />
                    <button
                        type="button"
                        className={`quiz-flag-btn ${isFlagged ? 'quiz-flag-btn--active' : ''}`}
                        title={isFlagged ? 'Remove flag (B)' : 'Flag for review (B)'}
                        onClick={() => toggleFlag(currentQuestion.id)}
                    >
                        {isFlagged ? <BookmarkCheck size={16} /> : <Bookmark size={16} />}
                    </button>
                </div>

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

                {/* Keyboard hint */}
                <p className="quiz-keyboard-hint">
                    Press <kbd>1</kbd>–<kbd>{currentQuestion.options.length}</kbd> to select · <kbd>→</kbd> next · <kbd>B</kbd> to flag
                </p>

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
                                    i === currentIndex          ? 'quiz-dot--current'  : '',
                                    answers[q.id] !== undefined ? 'quiz-dot--answered' : '',
                                    flagged.has(q.id)           ? 'quiz-dot--flagged'  : '',
                                ].filter(Boolean).join(' ')}
                                onClick={() => setCurrentIndex(i)}
                                aria-label={`Question ${i + 1}${flagged.has(q.id) ? ' (flagged)' : ''}`}
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
