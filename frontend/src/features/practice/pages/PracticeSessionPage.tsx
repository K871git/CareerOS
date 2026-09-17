import { useState, useEffect, useRef, useCallback } from 'react';
import { useParams, useNavigate, useLocation } from 'react-router-dom';
import {
    Bookmark, BookmarkCheck, Clock, TimerOff, SendHorizonal,
    AlertTriangle, Lightbulb, GraduationCap, Info, X,
} from 'lucide-react';
import { useQuestions, useSubmitAttempt } from '../../assessment/hooks/useMCQ';
import { usePoints, useUnlockHint } from '../hooks/usePoints';
import QuestionProgress from '../../assessment/components/QuestionProgress';
import QuestionCard from '../../assessment/components/QuestionCard';
import QuestionOption from '../../assessment/components/QuestionOption';
import PageLoader from '../../../components/ui/PageLoader';
import type { MCQQuestion } from '../../../types/api';
import '../../assessment/assessment.css';
import '../practice.css';

const TIMER_SECONDS = 15 * 60;
const HINT_COST     = 50;

function formatTime(s: number) {
    const m   = Math.floor(s / 60);
    const sec = s % 60;
    return `${m}:${sec.toString().padStart(2, '0')}`;
}

interface SavedQuizState {
    questions:    MCQQuestion[];
    answers:      Record<number, number>;
    flagged:      number[];
    currentIndex: number;
}

/* ── Submit confirm dialog ─────────────────────────────────────────── */
function SubmitConfirmDialog({
    total, flaggedCount, hintedCount, onConfirm, onCancel,
}: {
    total: number; flaggedCount: number; hintedCount: number;
    onConfirm: () => void; onCancel: () => void;
}) {
    return (
        <div className="quiz-confirm-overlay" onClick={onCancel}>
            <div className="quiz-confirm-modal" onClick={e => e.stopPropagation()}>
                <div className="quiz-confirm-icon"><SendHorizonal size={22} /></div>
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
                {hintedCount > 0 && (
                    <div className="quiz-confirm-warn" style={{ borderColor: 'rgba(245,158,11,0.3)', background: 'rgba(245,158,11,0.05)', color: '#b45309' }}>
                        <Info size={13} />
                        {hintedCount} hinted question{hintedCount !== 1 ? 's' : ''} won't count toward your score.
                    </div>
                )}
                <div className="quiz-confirm-actions">
                    <button className="quiz-confirm-cancel" onClick={onCancel}>Review First</button>
                    <button className="quiz-confirm-submit" onClick={onConfirm}>Submit Now</button>
                </div>
            </div>
        </div>
    );
}

/* ── Hint confirm dialog ───────────────────────────────────────────── */
function HintConfirmDialog({
    balance, onConfirm, onCancel,
}: {
    balance: number; onConfirm: () => void; onCancel: () => void;
}) {
    return (
        <div className="quiz-confirm-overlay" onClick={onCancel}>
            <div className="quiz-confirm-modal" onClick={e => e.stopPropagation()}>
                <div className="quiz-confirm-icon" style={{ background: 'linear-gradient(135deg,rgba(245,158,11,.18),rgba(251,191,36,.1))', border: '1.5px solid rgba(245,158,11,.3)', color: '#b45309' }}>
                    <Lightbulb size={22} />
                </div>
                <h3 className="quiz-confirm-title">Use AI Hint?</h3>
                <p className="quiz-confirm-desc">
                    This will deduct <strong>{HINT_COST} pts</strong> from your balance.
                </p>
                <div className="quiz-confirm-warn">
                    <Info size={13} />
                    Hinted questions won't count toward your score.
                </div>
                <div className="quiz-confirm-actions">
                    <button className="quiz-confirm-cancel" onClick={onCancel}>Cancel</button>
                    <button
                        className="quiz-confirm-submit quiz-hint-confirm-btn"
                        onClick={onConfirm}
                    >
                        <Lightbulb size={14} strokeWidth={2.2} />
                        Use Hint
                        <span className="quiz-hint-confirm-cost">
                            <GraduationCap size={11} /> {HINT_COST} pts
                        </span>
                    </button>
                </div>
            </div>
        </div>
    );
}

/* ── Hint result panel (below question, no button) ─────────────────── */
function HintResultPanel({
    isLoading, apiError, hint,
}: {
    isLoading: boolean; apiError: string | null; hint: string | null;
}) {
    if (!isLoading && !apiError && !hint) return null;

    return (
        <div className="quiz-hint-wrap">
            {isLoading && (
                <div className="quiz-hint-panel">
                    <div className="quiz-hint-loading">
                        <div className="quiz-hint-dot" />
                        <div className="quiz-hint-dot" />
                        <div className="quiz-hint-dot" />
                        AI is thinking…
                    </div>
                </div>
            )}
            {apiError && !hint && (
                <div className="quiz-hint-insuff">{apiError}</div>
            )}
            {hint && (
                <div className="quiz-hint-panel">
                    <div className="quiz-hint-header">
                        <Lightbulb size={12} /> AI Hint
                    </div>
                    <p className="quiz-hint-text">{hint}</p>
                    <div className="quiz-hint-no-score">
                        <Info size={11} /> This question won't count toward your score.
                    </div>
                </div>
            )}
        </div>
    );
}

/* ── Main page ──────────────────────────────────────────────────────── */
export default function PracticeSessionPage() {
    const { topicId } = useParams<{ topicId: string }>();
    const id          = Number(topicId);
    const navigate    = useNavigate();
    const location    = useLocation();
    const topicTitle  = (location.state as { topicTitle?: string } | null)?.topicTitle;

    const TIMER_KEY = `quiz-timer-${id}`;
    const QUIZ_KEY  = `quiz-state-${id}`;

    const [savedQuiz] = useState<SavedQuizState | null>(() => {
        if (!id) return null;
        try {
            const raw = sessionStorage.getItem(QUIZ_KEY);
            return raw ? (JSON.parse(raw) as SavedQuizState) : null;
        } catch { return null; }
    });

    const [currentIndex, setCurrentIndex] = useState(savedQuiz?.currentIndex ?? 0);
    const [answers, setAnswers]           = useState<Record<number, number>>(savedQuiz?.answers ?? {});
    const [flagged, setFlagged]           = useState<Set<number>>(new Set(savedQuiz?.flagged ?? []));
    const [submitError, setSubmitError]   = useState<string | null>(null);
    const [showSubmitConfirm, setShowSubmitConfirm] = useState(false);

    /* ── Hint state ── */
    const [hintedIds,      setHintedIds]      = useState<Set<number>>(new Set());
    const [hintTexts,      setHintTexts]      = useState<Record<number, string>>({});
    const [hintLoading,    setHintLoading]    = useState<number | null>(null);
    const [hintApiErrors,  setHintApiErrors]  = useState<Record<number, string>>({});
    const [showHintConfirm, setShowHintConfirm] = useState(false);  // confirm dialog
    const [hintClickError,  setHintClickError]  = useState<string | null>(null); // on-click insuff error

    /* ── Data hooks ── */
    const { data: points }  = usePoints();
    const unlockHint        = useUnlockHint();
    const { data: questions = [], isLoading } = useQuestions(id, savedQuiz?.questions);
    const submitAttempt     = useSubmitAttempt();

    /* ── Timer ── */
    const [timerOn, setTimerOn]   = useState(false);
    const [timeLeft, setTimeLeft] = useState(TIMER_SECONDS);
    const timerRef = useRef<ReturnType<typeof setInterval> | null>(null);

    const answersRef   = useRef(answers);
    const questionsRef = useRef(questions);
    const hintedRef    = useRef(hintedIds);
    useEffect(() => { answersRef.current   = answers;   }, [answers]);
    useEffect(() => { questionsRef.current = questions; }, [questions]);
    useEffect(() => { hintedRef.current    = hintedIds; }, [hintedIds]);

    useEffect(() => {
        const saved = sessionStorage.getItem(TIMER_KEY);
        if (saved) {
            try {
                const { on, left } = JSON.parse(saved) as { on: boolean; left: number };
                if (left > 0) { setTimerOn(on); setTimeLeft(left); }
                else sessionStorage.removeItem(TIMER_KEY);
            } catch { sessionStorage.removeItem(TIMER_KEY); }
        }
    }, [TIMER_KEY]);

    useEffect(() => {
        if (timerOn) sessionStorage.setItem(TIMER_KEY, JSON.stringify({ on: timerOn, left: timeLeft }));
        else sessionStorage.removeItem(TIMER_KEY);
    }, [timerOn, timeLeft, TIMER_KEY]);

    useEffect(() => {
        if (questions.length === 0) return;
        sessionStorage.setItem(QUIZ_KEY, JSON.stringify({
            questions, answers, flagged: Array.from(flagged), currentIndex,
        }));
    }, [questions, answers, flagged, currentIndex, QUIZ_KEY]);

    useEffect(() => {
        if (timerOn && timeLeft > 0) {
            timerRef.current = setInterval(() => setTimeLeft(t => t - 1), 1000);
        } else if (timerRef.current) {
            clearInterval(timerRef.current);
        }
        return () => { if (timerRef.current) clearInterval(timerRef.current); };
    }, [timerOn, timeLeft]);

    /* Clear hint click error when navigating questions */
    useEffect(() => { setHintClickError(null); }, [currentIndex]);

    /* ── Handlers ── */
    function clearSavedState() {
        sessionStorage.removeItem(TIMER_KEY);
        sessionStorage.removeItem(QUIZ_KEY);
    }

    function doSubmit() {
        clearSavedState();
        submitAttempt.mutate({
            answers: questionsRef.current.map(q => ({
                question_id:        q.id,
                selected_option_id: answersRef.current[q.id],
            })),
            hinted_question_ids: Array.from(hintedRef.current),
        });
    }

    useEffect(() => {
        if (timerOn && timeLeft === 0) doSubmit();
    }, [timerOn, timeLeft]); // eslint-disable-line react-hooks/exhaustive-deps

    /* Hint button clicked → validate first, then show confirm or error */
    function handleHintButtonClick() {
        const qId = questions[currentIndex]?.id;
        if (!qId) return;
        if (hintedIds.has(qId)) return; // already hinted, panel is visible below
        setHintClickError(null);
        const balance = points?.balance ?? 0;
        if (balance < HINT_COST) {
            setHintClickError(`You need ${HINT_COST} pts to get a hint — you only have ${balance} pts. Complete more quizzes to earn points!`);
            return;
        }
        setShowHintConfirm(true);
    }

    async function handleConfirmHint() {
        setShowHintConfirm(false);
        const qId = questions[currentIndex]?.id;
        if (!qId) return;
        setHintLoading(qId);
        setHintApiErrors(prev => { const n = { ...prev }; delete n[qId]; return n; });
        try {
            const result = await unlockHint.mutateAsync({ question_id: qId });
            setHintTexts(prev => ({ ...prev, [qId]: result.hint }));
            setHintedIds(prev => new Set([...prev, qId]));
        } catch (err: any) {
            const msg = err?.response?.data?.message ?? err?.message ?? 'Failed to get hint.';
            setHintApiErrors(prev => ({ ...prev, [qId]: msg }));
        } finally {
            setHintLoading(null);
        }
    }

    function handleSelectAnswer(questionId: number, optionId: number) {
        setAnswers(prev => ({ ...prev, [questionId]: optionId }));
        setSubmitError(null);
    }

    function handleNext() { if (currentIndex < questions.length - 1) setCurrentIndex(i => i + 1); }
    function handlePrev() { if (currentIndex > 0) setCurrentIndex(i => i - 1); }

    function handleSubmitRequest() {
        const firstUnanswered = questions.findIndex(q => answers[q.id] === undefined);
        if (firstUnanswered !== -1) {
            setCurrentIndex(firstUnanswered);
            setSubmitError(`Answer all ${questions.length} questions first. Jumped to Q${firstUnanswered + 1}.`);
            return;
        }
        setShowSubmitConfirm(true);
    }

    function toggleFlag(questionId: number) {
        setFlagged(prev => {
            const next = new Set(prev);
            next.has(questionId) ? next.delete(questionId) : next.add(questionId);
            return next;
        });
    }

    const handleKeyDown = useCallback((e: KeyboardEvent) => {
        if (submitAttempt.isPending || isLoading || questions.length === 0 || showSubmitConfirm || showHintConfirm) return;
        const tag = (e.target as HTMLElement).tagName;
        if (tag === 'INPUT' || tag === 'TEXTAREA') return;
        const q = questions[currentIndex];
        if (e.key === 'ArrowRight' || e.key === ' ') { e.preventDefault(); handleNext(); }
        else if (e.key === 'ArrowLeft') { e.preventDefault(); handlePrev(); }
        else if (e.key === 'Escape') { setShowSubmitConfirm(false); setShowHintConfirm(false); }
        else if (e.key === 'b' || e.key === 'B') { toggleFlag(q.id); }
        else {
            const num = parseInt(e.key, 10);
            if (num >= 1 && num <= q.options.length) handleSelectAnswer(q.id, q.options[num - 1].id);
        }
    }, [currentIndex, questions, submitAttempt.isPending, isLoading, showSubmitConfirm, showHintConfirm]); // eslint-disable-line react-hooks/exhaustive-deps

    useEffect(() => {
        window.addEventListener('keydown', handleKeyDown);
        return () => window.removeEventListener('keydown', handleKeyDown);
    }, [handleKeyDown]);

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
                            onClick={() => navigate(-1)}>← Go back</button>
                    </div>
                </div>
            </div>
        );
    }

    if (submitAttempt.isPending) {
        return <PageLoader label="Calculating Results" hint="Saving your answers…" />;
    }

    const currentQuestion = questions[currentIndex];
    const answeredCount   = Object.keys(answers).length;
    const allAnswered     = answeredCount === questions.length;
    const isFlagged       = flagged.has(currentQuestion.id);
    const timerWarning    = timerOn && timeLeft <= 60;
    const balance         = points?.balance ?? 0;
    const isHintLoading   = hintLoading === currentQuestion.id;
    const currentHint     = hintTexts[currentQuestion.id] ?? null;
    const currentApiErr   = hintApiErrors[currentQuestion.id] ?? null;
    const hintUsed        = hintedIds.has(currentQuestion.id);

    return (
        <div className="practice-page">
            <div className="practice-inner">

                {showSubmitConfirm && (
                    <SubmitConfirmDialog
                        total={questions.length}
                        flaggedCount={flagged.size}
                        hintedCount={hintedIds.size}
                        onConfirm={() => { setShowSubmitConfirm(false); doSubmit(); }}
                        onCancel={() => setShowSubmitConfirm(false)}
                    />
                )}

                {showHintConfirm && (
                    <HintConfirmDialog
                        balance={balance}
                        onConfirm={handleConfirmHint}
                        onCancel={() => setShowHintConfirm(false)}
                    />
                )}

                {/* Top bar */}
                <div className="quiz-top-bar">
                    <button type="button" className="practice-back-btn"
                        onClick={() => navigate(-1)} disabled={submitAttempt.isPending}>
                        ← {topicTitle ?? 'Back'}
                    </button>

                    <div className="quiz-top-right">
                        {savedQuiz && answeredCount > 0 && (
                            <span className="quiz-restored-badge" title="Progress restored">↩ Resumed</span>
                        )}

                        {/* Hint button — lives in top bar */}
                        <button
                            type="button"
                            className={`quiz-hint-topbar-btn${hintUsed ? ' quiz-hint-topbar-btn--used' : ''}`}
                            onClick={handleHintButtonClick}
                            disabled={isHintLoading}
                            title={hintUsed ? 'Hint already used for this question' : `Get AI hint — costs ${HINT_COST} pts`}
                        >
                            {isHintLoading
                                ? <><span className="quiz-hint-dot" style={{ animationDelay: '0s' }} /><span className="quiz-hint-dot" style={{ animationDelay: '.15s' }} /><span className="quiz-hint-dot" style={{ animationDelay: '.3s' }} /></>
                                : <Lightbulb size={13} strokeWidth={2.2} />
                            }
                            <span className="quiz-hint-topbar-label">
                                {hintUsed ? 'Hint Used' : 'AI Hint'}
                            </span>
                            {!hintUsed && !isHintLoading && (
                                <span className="quiz-hint-topbar-cost">
                                    <GraduationCap size={10} /> {HINT_COST}
                                </span>
                            )}
                        </button>

                        {timerOn && (
                            <span className={`quiz-timer${timerWarning ? ' quiz-timer--warn' : ''}`}>
                                <Clock size={13} /> {formatTime(timeLeft)}
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

                {/* On-click insufficient points error */}
                {hintClickError && (
                    <div className="quiz-hint-insuff" style={{ display: 'flex', alignItems: 'flex-start', justifyContent: 'space-between', gap: '0.5rem' }}>
                        <span><GraduationCap size={12} style={{ display: 'inline', marginRight: 4 }} />{hintClickError}</span>
                        <button
                            type="button"
                            onClick={() => setHintClickError(null)}
                            style={{ background: 'none', border: 'none', cursor: 'pointer', padding: 0, color: 'inherit', opacity: 0.6, flexShrink: 0 }}
                            aria-label="Dismiss"
                        >
                            <X size={13} />
                        </button>
                    </div>
                )}

                {/* Hint result panel — loading / hint text / API error */}
                <HintResultPanel
                    isLoading={isHintLoading}
                    apiError={currentApiErr}
                    hint={currentHint}
                />

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
                                    hintedIds.has(q.id)         ? 'quiz-dot--hinted'   : '',
                                ].filter(Boolean).join(' ')}
                                onClick={() => setCurrentIndex(i)}
                                aria-label={`Q${i + 1}${flagged.has(q.id) ? ' flagged' : ''}${hintedIds.has(q.id) ? ' hinted' : ''}`}
                            />
                        ))}
                    </span>

                    <button type="button" className="quiz-nav-btn"
                        onClick={handleNext}
                        disabled={currentIndex === questions.length - 1 || submitAttempt.isPending}>
                        Next →
                    </button>
                </div>

                {/* Floating submit bar */}
                {allAnswered && (
                    <div className="quiz-float-bar">
                        <div className="quiz-float-left">
                            <span className="quiz-float-check">✓</span>
                            <span className="quiz-float-text">All {questions.length} answered</span>
                            {flagged.size > 0 && (
                                <span className="quiz-float-flag">{flagged.size} flagged</span>
                            )}
                            {hintedIds.size > 0 && (
                                <span className="quiz-float-flag" style={{ borderColor: 'rgba(245,158,11,0.4)', color: '#b45309', background: 'rgba(245,158,11,0.08)' }}>
                                    {hintedIds.size} hinted
                                </span>
                            )}
                        </div>
                        <button type="button" className="quiz-float-btn"
                            onClick={handleSubmitRequest} disabled={submitAttempt.isPending}>
                            <SendHorizonal size={14} /> Submit Quiz
                        </button>
                    </div>
                )}

            </div>
        </div>
    );
}
