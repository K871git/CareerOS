import { useState, useEffect, useRef, useCallback } from 'react';
import { useParams, useNavigate, useLocation } from 'react-router-dom';
import { Bookmark, BookmarkCheck, Clock, TimerOff, SendHorizonal, AlertTriangle } from 'lucide-react';
import { useQuestions, useSubmitAttempt } from '../../assessment/hooks/useMCQ';
import QuestionProgress from '../../assessment/components/QuestionProgress';
import QuestionCard from '../../assessment/components/QuestionCard';
import QuestionOption from '../../assessment/components/QuestionOption';
import PageLoader from '../../../components/ui/PageLoader';
import '../../assessment/assessment.css';

const TIMER_SECONDS = 15 * 60;

function formatTime(s: number) {
    const m   = Math.floor(s / 60);
    const sec = s % 60;
    return `${m}:${sec.toString().padStart(2, '0')}`;
}

/* ── Submit confirm dialog ── */
function ConfirmDialog({
    total, flaggedCount, onConfirm, onCancel,
}: {
    total: number; flaggedCount: number; onConfirm: () => void; onCancel: () => void;
}) {
    return (
        <div className="quiz-confirm-overlay" onClick={onCancel}>
            <div className="quiz-confirm-modal" onClick={e => e.stopPropagation()}>
                <div className="quiz-confirm-icon">
                    <SendHorizonal size={22} />
                </div>
                <h3 className="quiz-confirm-title">Submit Quiz?</h3>
                <p className="quiz-confirm-desc">
                    You've answered all {total} questions. This action cannot be undone.
                </p>
                {flaggedCount > 0 && (
                    <div className="quiz-confirm-warn">
                        <AlertTriangle size={13} />
                        {flaggedCount} flagged question{flaggedCount !== 1 ? 's' : ''} — review before submitting?
                    </div>
                )}
                <div className="quiz-confirm-actions">
                    <button className="quiz-confirm-cancel" onClick={onCancel}>
                        Review First
                    </button>
                    <button className="quiz-confirm-submit" onClick={onConfirm}>
                        Submit Now
                    </button>
                </div>
            </div>
        </div>
    );
}

export default function PracticeSessionPage() {
    const { topicId } = useParams<{ topicId: string }>();
    const id          = Number(topicId);
    const navigate    = useNavigate();
    const location    = useLocation();
    const topicTitle  = (location.state as { topicTitle?: string } | null)?.topicTitle;

    const TIMER_KEY = `quiz-timer-${id}`;

    const [currentIndex, setCurrentIndex] = useState(0);
    const [answers, setAnswers]           = useState<Record<number, number>>({});
    const [flagged, setFlagged]           = useState<Set<number>>(new Set());
    const [submitError, setSubmitError]   = useState<string | null>(null);
    const [showConfirm, setShowConfirm]   = useState(false);

    /* Timer — restored from sessionStorage on mount */
    const [timerOn, setTimerOn]   = useState(false);
    const [timeLeft, setTimeLeft] = useState(TIMER_SECONDS);
    const timerRef = useRef<ReturnType<typeof setInterval> | null>(null);

    const { data: questions = [], isLoading } = useQuestions(id);
    const submitAttempt = useSubmitAttempt();

    /* Restore timer from sessionStorage */
    useEffect(() => {
        const saved = sessionStorage.getItem(TIMER_KEY);
        if (saved) {
            try {
                const { on, left } = JSON.parse(saved) as { on: boolean; left: number };
                if (left > 0) { setTimerOn(on); setTimeLeft(left); }
                else sessionStorage.removeItem(TIMER_KEY);
            } catch {
                sessionStorage.removeItem(TIMER_KEY);
            }
        }
    }, [TIMER_KEY]);

    /* Persist timer state */
    useEffect(() => {
        if (timerOn) {
            sessionStorage.setItem(TIMER_KEY, JSON.stringify({ on: timerOn, left: timeLeft }));
        } else {
            sessionStorage.removeItem(TIMER_KEY);
        }
    }, [timerOn, timeLeft, TIMER_KEY]);

    /* Tick */
    useEffect(() => {
        if (timerOn && timeLeft > 0) {
            timerRef.current = setInterval(() => setTimeLeft(t => t - 1), 1000);
        } else if (timerRef.current) {
            clearInterval(timerRef.current);
        }
        return () => { if (timerRef.current) clearInterval(timerRef.current); };
    }, [timerOn, timeLeft]);

    /* Auto-submit on zero */
    useEffect(() => {
        if (timerOn && timeLeft === 0) doSubmit();
    }, [timerOn, timeLeft]); // eslint-disable-line react-hooks/exhaustive-deps

    /* ── Handlers ── */
    function handleSelectAnswer(questionId: number, optionId: number) {
        setAnswers(prev => ({ ...prev, [questionId]: optionId }));
        setSubmitError(null);
    }

    function handleNext() { if (currentIndex < questions.length - 1) setCurrentIndex(i => i + 1); }
    function handlePrev() { if (currentIndex > 0) setCurrentIndex(i => i - 1); }

    function doSubmit() {
        sessionStorage.removeItem(TIMER_KEY);
        submitAttempt.mutate({
            answers: questions.map(q => ({
                question_id:        q.id,
                selected_option_id: answers[q.id],
            })),
        });
    }

    function handleSubmitRequest() {
        const firstUnanswered = questions.findIndex(q => answers[q.id] === undefined);
        if (firstUnanswered !== -1) {
            setCurrentIndex(firstUnanswered);
            setSubmitError(`Answer all ${questions.length} questions first. Jumped to Q${firstUnanswered + 1}.`);
            return;
        }
        setShowConfirm(true);
    }

    function toggleFlag(questionId: number) {
        setFlagged(prev => {
            const next = new Set(prev);
            next.has(questionId) ? next.delete(questionId) : next.add(questionId);
            return next;
        });
    }

    /* ── Keyboard ── */
    const handleKeyDown = useCallback((e: KeyboardEvent) => {
        if (submitAttempt.isPending || isLoading || questions.length === 0 || showConfirm) return;
        const tag = (e.target as HTMLElement).tagName;
        if (tag === 'INPUT' || tag === 'TEXTAREA') return;

        const q = questions[currentIndex];

        if (e.key === 'ArrowRight' || e.key === ' ') {
            e.preventDefault();
            handleNext();
        } else if (e.key === 'ArrowLeft') {
            e.preventDefault();
            handlePrev();
        } else if (e.key === 'Escape' && showConfirm) {
            setShowConfirm(false);
        } else if (e.key === 'b' || e.key === 'B') {
            toggleFlag(q.id);
        } else {
            const num = parseInt(e.key, 10);
            if (num >= 1 && num <= q.options.length) {
                handleSelectAnswer(q.id, q.options[num - 1].id);
            }
        }
    }, [currentIndex, questions, submitAttempt.isPending, isLoading, showConfirm]); // eslint-disable-line react-hooks/exhaustive-deps

    useEffect(() => {
        window.addEventListener('keydown', handleKeyDown);
        return () => window.removeEventListener('keydown', handleKeyDown);
    }, [handleKeyDown]);

    /* ── States ── */
    if (isLoading) {
        return (
            <div className="practice-page">
                <div className="practice-inner">
                    <div className="skeleton" style={{ height: 10, borderRadius: 8, marginBottom: 24 }} />
                    <div className="skeleton" style={{ height: 96, borderRadius: 14, marginBottom: 16 }} />
                    {[0, 1, 2, 3].map(i => (
                        <div key={i} className="skeleton" style={{ height: 60, borderRadius: 10, marginBottom: 10 }} />
                    ))}
                </div>
            </div>
        );
    }

    if (questions.length === 0) {
        return (
            <div className="practice-page">
                <div className="practice-inner">
                    <div className="practice-empty">
                        <p>No questions available for this topic yet.</p>
                        <button type="button" className="practice-back-link"
                            style={{ marginTop: '1rem', display: 'block' }}
                            onClick={() => navigate(-1)}>
                            ← Go back
                        </button>
                    </div>
                </div>
            </div>
        );
    }

    if (submitAttempt.isPending) {
        return <PageLoader label="Calculating Results" hint="Saving your answers…" />;
    }

    /* ── Quiz UI ── */
    const currentQuestion = questions[currentIndex];
    const answeredCount   = Object.keys(answers).length;
    const allAnswered     = answeredCount === questions.length;
    const isFlagged       = flagged.has(currentQuestion.id);
    const timerWarning    = timerOn && timeLeft <= 60;

    return (
        <div className="practice-page">
            <div className="practice-inner">

                {/* Confirm dialog */}
                {showConfirm && (
                    <ConfirmDialog
                        total={questions.length}
                        flaggedCount={flagged.size}
                        onConfirm={() => { setShowConfirm(false); doSubmit(); }}
                        onCancel={() => setShowConfirm(false)}
                    />
                )}

                {/* Top bar */}
                <div className="quiz-top-bar">
                    <button type="button" className="practice-back-btn"
                        onClick={() => navigate(-1)} disabled={submitAttempt.isPending}>
                        ← {topicTitle ?? 'Back'}
                    </button>

                    <div className="quiz-top-right">
                        {timerOn && (
                            <span className={`quiz-timer${timerWarning ? ' quiz-timer--warn' : ''}`}>
                                <Clock size={13} />
                                {formatTime(timeLeft)}
                            </span>
                        )}
                        <button
                            type="button"
                            className="quiz-icon-btn"
                            title={timerOn ? 'Stop timer' : 'Start 15-min timer'}
                            onClick={() => { setTimerOn(p => !p); if (!timerOn) setTimeLeft(TIMER_SECONDS); }}
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

                {/* Question + flag */}
                <div className="quiz-card-row">
                    <QuestionCard question={currentQuestion} questionNumber={currentIndex + 1} />
                    <button
                        type="button"
                        className={`quiz-flag-btn${isFlagged ? ' quiz-flag-btn--active' : ''}`}
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

                <p className="quiz-keyboard-hint">
                    Press <kbd>1</kbd>–<kbd>{currentQuestion.options.length}</kbd> to select · <kbd>→</kbd> next · <kbd>B</kbd> to flag
                </p>

                {submitError && <p className="quiz-error">{submitError}</p>}

                {/* Nav */}
                <div className="quiz-nav">
                    <button type="button" className="quiz-nav-btn"
                        onClick={handlePrev} disabled={currentIndex === 0 || submitAttempt.isPending}>
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

                    <button
                        type="button"
                        className="quiz-nav-btn"
                        onClick={handleNext}
                        disabled={currentIndex === questions.length - 1 || submitAttempt.isPending}
                    >
                        Next →
                    </button>
                </div>

                {/* Floating submit bar — appears once all answered */}
                {allAnswered && (
                    <div className="quiz-float-bar">
                        <div className="quiz-float-left">
                            <span className="quiz-float-check">✓</span>
                            <span className="quiz-float-text">All {questions.length} questions answered</span>
                            {flagged.size > 0 && (
                                <span className="quiz-float-flag">{flagged.size} flagged</span>
                            )}
                        </div>
                        <button
                            type="button"
                            className="quiz-float-btn"
                            onClick={handleSubmitRequest}
                            disabled={submitAttempt.isPending}
                        >
                            <SendHorizonal size={14} />
                            Submit Quiz
                        </button>
                    </div>
                )}

            </div>
        </div>
    );
}
