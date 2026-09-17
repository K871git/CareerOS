import { useState, useEffect } from 'react';
import { Link, useParams, useLocation, Navigate } from 'react-router-dom';
import {
    ChevronRight, Trophy, XCircle, AlertCircle, ClipboardList,
    CheckCircle2, AlertTriangle, Lightbulb, BookOpen, RefreshCw,
    ChevronDown, Target, Lock, Star, Coins, Sparkles,
} from 'lucide-react';
import { useSubjectBySlug, useExamQuestions, useSubmitExam } from '../hooks/useLevel';
import type { ExamResult, ExamAnswerDetail } from '../../../types/api';
import '../learning.css';

/* ──────────────────────────────────────────────────────────────
   RESULT SCREEN COMPONENTS
   ────────────────────────────────────────────────────────────── */

/* Score progress bar */
function ScoreBar({ pct, passed }: { pct: number; passed: boolean }) {
    return (
        <div className="er-bar-wrap">
            <div className="er-bar-track">
                <div
                    className={`er-bar-fill${passed ? ' er-bar-fill--pass' : ' er-bar-fill--fail'}`}
                    style={{ width: `${pct}%` }}
                />
                <div className="er-bar-marker" style={{ left: '80%' }} />
            </div>
            <div className="er-bar-labels">
                <span>0%</span>
                <span className="er-bar-threshold">← 80% to pass</span>
                <span>100%</span>
            </div>
        </div>
    );
}

/* Top banner card */
function ResultBanner({
    score, total, passed, levelNum,
}: {
    score: number; total: number; passed: boolean; levelNum: number;
}) {
    const pct          = Math.round((score / total) * 100);
    const wrongCount   = total - score;
    const needMore     = 8 - score;

    return (
        <div className={`er-banner${passed ? ' er-banner--pass' : ' er-banner--fail'}`}>
            {/* Decorative dots */}
            <div className="er-banner-dots" aria-hidden />

            <div className="er-banner-body">
                <div className="er-banner-icon-wrap">
                    {passed
                        ? <Trophy size={34} strokeWidth={1.8} />
                        : <XCircle size={34} strokeWidth={1.8} />}
                </div>

                <div className="er-banner-text">
                    <div className={`er-banner-chip${passed ? '' : ' er-banner-chip--fail'}`}>
                        {passed ? `Level ${levelNum} Passed!` : 'Not Passed — Try Again'}
                    </div>
                    <h2 className="er-banner-title">
                        {passed ? `Level ${levelNum} Complete!` : 'Almost There — Keep Going'}
                    </h2>
                    <p className="er-banner-sub">
                        {passed
                            ? wrongCount === 0
                                ? `Flawless! Perfect score across all ${total} questions.`
                                : `You passed with ${score}/${total}. Level ${levelNum + 1} is now unlocked.`
                            : `You scored ${score}/${total}. Need ${needMore} more correct answer${needMore !== 1 ? 's' : ''} to pass.`}
                    </p>
                </div>

                <div className="er-banner-score-pill">
                    <span className="er-banner-score-num">{score}</span>
                    <span className="er-banner-score-of">/{total}</span>
                </div>
            </div>

            <ScoreBar pct={pct} passed={passed} />
        </div>
    );
}

/* Mini stat cards row */
function MiniStats({
    score, total, passed, levelNum,
}: {
    score: number; total: number; passed: boolean; levelNum: number;
}) {
    const pct      = Math.round((score / total) * 100);
    const wrong    = total - score;
    const needMore = 8 - score;

    const cards = [
        {
            value: String(score),
            label: 'Correct',
            sub: `out of ${total}`,
            accent: '#16a34a',
            bg: 'rgba(34,197,94,0.08)',
        },
        {
            value: String(wrong),
            label: wrong === 0 ? 'Missed' : 'Missed',
            sub: wrong === 0 ? 'None! Perfect' : `question${wrong !== 1 ? 's' : ''}`,
            accent: wrong === 0 ? '#16a34a' : '#dc2626',
            bg: wrong === 0 ? 'rgba(34,197,94,0.07)' : 'rgba(239,68,68,0.07)',
        },
        {
            value: `${pct}%`,
            label: 'Accuracy',
            sub: passed ? 'Above 80% ✓' : 'Need 80%',
            accent: passed ? '#6366f1' : '#d97706',
            bg: passed ? 'rgba(99,102,241,0.08)' : 'rgba(245,158,11,0.08)',
        },
        passed
            ? {
                value: `L${levelNum + 1}`,
                label: 'Unlocked',
                sub: 'Ready to study',
                accent: '#059669',
                bg: 'rgba(5,150,105,0.08)',
              }
            : {
                value: `+${needMore}`,
                label: 'More Needed',
                sub: 'to reach 80%',
                accent: '#d97706',
                bg: 'rgba(245,158,11,0.08)',
              },
    ];

    return (
        <div className="er-mini-row">
            {cards.map((c) => (
                <div key={c.label} className="er-mini-card" style={{ '--er-accent': c.accent, '--er-bg': c.bg } as React.CSSProperties}>
                    <span className="er-mini-value">{c.value}</span>
                    <span className="er-mini-label">{c.label}</span>
                    <span className="er-mini-sub">{c.sub}</span>
                </div>
            ))}
        </div>
    );
}

/* Level progress trail */
function LevelTrail({ currentLevel, passed }: { currentLevel: number; passed: boolean }) {
    const TOTAL = 5;

    return (
        <div className="er-trail-wrap">
            <p className="er-trail-heading">Your Level Journey</p>
            <div className="er-trail">
                {Array.from({ length: TOTAL }, (_, i) => {
                    const lvl         = i + 1;
                    const isDone      = lvl < currentLevel || (lvl === currentLevel && passed);
                    const isCurrent   = lvl === currentLevel;
                    const isNextUnlocked = lvl === currentLevel + 1 && passed;
                    const isFailed    = isCurrent && !passed;

                    let nodeClass = 'er-trail-node';
                    if (isDone)           nodeClass += ' er-trail-node--done';
                    else if (isFailed)    nodeClass += ' er-trail-node--fail';
                    else if (isNextUnlocked) nodeClass += ' er-trail-node--unlock';
                    else                  nodeClass += ' er-trail-node--lock';

                    const lineActive = lvl < TOTAL && (isDone || (lvl < currentLevel));

                    return (
                        <div key={lvl} className="er-trail-pair">
                            <div className={nodeClass}>
                                <div className="er-trail-dot">
                                    {isDone
                                        ? <CheckCircle2 size={12} />
                                        : isFailed
                                            ? <XCircle size={12} />
                                            : isNextUnlocked
                                                ? <Star size={12} />
                                                : <Lock size={10} />}
                                </div>
                                <span className="er-trail-lbl">
                                    L{lvl}
                                    {isNextUnlocked && <span className="er-trail-new">New!</span>}
                                    {isFailed && <span className="er-trail-retry">retry</span>}
                                </span>
                            </div>
                            {lvl < TOTAL && (
                                <div className={`er-trail-line${lineActive ? ' er-trail-line--done' : ''}`} />
                            )}
                        </div>
                    );
                })}
            </div>
        </div>
    );
}

/* Weak areas section */
function WeakAreasSection({ wrongAnswers }: { wrongAnswers: ExamAnswerDetail[] }) {
    if (wrongAnswers.length === 0) return null;

    const grouped = wrongAnswers.reduce<Record<string, ExamAnswerDetail[]>>((acc, a) => {
        (acc[a.topic_title] ??= []).push(a);
        return acc;
    }, {});

    return (
        <div className="er-section er-section--weak">
            <div className="er-section-hd">
                <AlertTriangle size={15} className="er-section-ico--warn" />
                <h3 className="er-section-title">
                    {wrongAnswers.length} Question{wrongAnswers.length !== 1 ? 's' : ''} to Review
                </h3>
                <span className="er-section-badge">{Object.keys(grouped).length} topic{Object.keys(grouped).length !== 1 ? 's' : ''}</span>
            </div>

            {Object.entries(grouped).map(([topic, items]) => (
                <div key={topic} className="er-topic-block">
                    <div className="er-topic-label">
                        <BookOpen size={11} />
                        <span>{topic}</span>
                        <span className="er-topic-count">{items.length} wrong</span>
                    </div>

                    {items.map((a) => (
                        <div key={a.question_id} className="er-qa-item">
                            <div className="er-qa-header">
                                <span className={`er-q-diff er-q-diff--${a.difficulty.toLowerCase()}`}>
                                    {a.difficulty}
                                </span>
                            </div>
                            <p className="er-qa-question">{a.question}</p>
                            <div className="er-qa-answers">
                                <div className="er-qa-row er-qa-row--wrong">
                                    <XCircle size={12} className="er-qa-ic" />
                                    <div>
                                        <span className="er-qa-row-label">Your answer</span>
                                        <span className="er-qa-row-text">{a.selected_option}</span>
                                    </div>
                                </div>
                                <div className="er-qa-row er-qa-row--correct">
                                    <CheckCircle2 size={12} className="er-qa-ic" />
                                    <div>
                                        <span className="er-qa-row-label">Correct answer</span>
                                        <span className="er-qa-row-text">{a.correct_option}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            ))}
        </div>
    );
}

/* Recommendations — numbered path */
function StudyPath({
    passed, levelNum, wrongCount, subjectSlug,
}: {
    passed: boolean;
    levelNum: number;
    wrongCount: number;
    subjectSlug: string;
}) {
    const steps = passed
        ? [
            wrongCount > 0 && {
                n: '1',
                icon: <AlertTriangle size={14} />,
                color: '#d97706',
                title: `Revisit ${wrongCount} missed question${wrongCount !== 1 ? 's' : ''}`,
                desc: 'Reviewing mistakes now prevents gaps in later, harder levels.',
            },
            {
                n: wrongCount > 0 ? '2' : '1',
                icon: <ChevronRight size={14} />,
                color: '#059669',
                title: `Explore Level ${levelNum + 1}`,
                desc: `You unlocked Level ${levelNum + 1} — keep the momentum going.`,
            },
            {
                n: wrongCount > 0 ? '3' : '2',
                icon: <Target size={14} />,
                color: '#6366f1',
                title: `Practice Quiz — ${subjectSlug.replace(/-/g, ' ')}`,
                desc: 'Timed quizzes test recall and reinforce long-term retention.',
            },
          ].filter(Boolean)
        : [
            {
                n: '1',
                icon: <BookOpen size={14} />,
                color: '#6366f1',
                title: `Review Level ${levelNum} content`,
                desc: 'Go through lessons again, paying attention to the weak areas above.',
            },
            {
                n: '2',
                icon: <Target size={14} />,
                color: '#d97706',
                title: 'Practice quiz first',
                desc: 'Build confidence on individual topics before retaking the exam.',
            },
            {
                n: '3',
                icon: <RefreshCw size={14} />,
                color: '#059669',
                title: 'Retry the exam',
                desc: 'Once you feel confident, come back. You only need 8/10 to pass.',
            },
          ];

    return (
        <div className="er-section er-section--path">
            <div className="er-section-hd">
                <Lightbulb size={15} className="er-section-ico--amber" />
                <h3 className="er-section-title">
                    {passed ? 'Recommended Next Steps' : 'Your Path to Passing'}
                </h3>
            </div>

            <div className="er-path">
                {(steps as NonNullable<(typeof steps)[number]>[]).map((step, i) => (
                    <div key={i} className="er-path-item">
                        <div className="er-path-step" style={{ background: step.color, boxShadow: `0 0 0 4px ${step.color}20` }}>
                            {step.icon}
                        </div>
                        {i < steps.length - 1 && <div className="er-path-line" />}
                        <div className="er-path-body">
                            <span className="er-path-num">Step {step.n}</span>
                            <p className="er-path-title">{step.title}</p>
                            <p className="er-path-desc">{step.desc}</p>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
}

/* Collapsible full answer review */
function AnswerReview({ answers }: { answers: ExamAnswerDetail[] }) {
    const [open, setOpen] = useState(false);
    const correct = answers.filter(a => a.is_correct).length;

    return (
        <div className="er-review-wrap">
            <button className="er-review-toggle" onClick={() => setOpen(o => !o)}>
                <span className="er-review-toggle-left">
                    <ChevronDown size={14} className={`er-review-chevron${open ? ' er-review-chevron--open' : ''}`} />
                    {open ? 'Hide' : 'Show'} all {answers.length} results
                </span>
                <span className="er-review-toggle-score">
                    <span style={{ color: '#16a34a' }}>{correct} ✓</span>
                    {' · '}
                    <span style={{ color: '#dc2626' }}>{answers.length - correct} ✗</span>
                </span>
            </button>

            {open && (
                <div className="er-review-list">
                    {answers.map((a, i) => (
                        <div
                            key={a.question_id}
                            className={`er-review-item${a.is_correct ? ' er-review-item--pass' : ' er-review-item--fail'}`}
                        >
                            <div className="er-review-badge">
                                {a.is_correct
                                    ? <CheckCircle2 size={15} className="er-review-ic--pass" />
                                    : <XCircle     size={15} className="er-review-ic--fail" />}
                                <span>Q{i + 1}</span>
                            </div>

                            <div className="er-review-body">
                                <p className="er-review-q">{a.question}</p>
                                {!a.is_correct && (
                                    <p className="er-review-wrong">✗ {a.selected_option}</p>
                                )}
                                <p className="er-review-right">✓ {a.correct_option}</p>
                            </div>

                            <div className="er-review-meta">
                                <span className={`er-q-diff er-q-diff--${a.difficulty.toLowerCase()}`}>{a.difficulty}</span>
                                <span className="er-review-topic">{a.topic_title}</span>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}

/* ── Animated score counter ─────────────────────────────────────── */
function AnimatedCount({ target, duration = 900 }: { target: number; duration?: number }) {
    const [display, setDisplay] = useState(0);
    useEffect(() => {
        let start = 0;
        const step = Math.ceil(target / (duration / 16));
        const tick = setInterval(() => {
            start = Math.min(start + step, target);
            setDisplay(start);
            if (start >= target) clearInterval(tick);
        }, 16);
        return () => clearInterval(tick);
    }, [target, duration]);
    return <>{display}</>;
}

/* ── Points earned banner ───────────────────────────────────────── */
function PointsEarnedBadge({ points, passed }: { points: number; passed: boolean }) {
    const [visible, setVisible] = useState(false);
    useEffect(() => { const t = setTimeout(() => setVisible(true), 600); return () => clearTimeout(t); }, []);
    if (points === 0) return null;
    return (
        <div className={`er-points-badge${visible ? ' er-points-badge--in' : ''}`}>
            <Sparkles size={14} />
            <span>+<AnimatedCount target={points} duration={700} /> pts earned</span>
            {passed && <span className="er-points-breakdown">({Math.max(0, points - 150)} correct + 150 pass bonus)</span>}
        </div>
    );
}

/* ─── Main result screen ─────────────────────────────────────── */
function ExamResultScreen({
    result, levelNum, category, subjectSlug, subjectId, onRetry,
}: {
    result: ExamResult;
    levelNum: number;
    category: string;
    subjectSlug: string;
    subjectId: number;
    onRetry: () => void;
}) {
    const { score, total, passed, answers = [], points_earned = 0 } = result;
    const wrongAnswers = answers.filter(a => !a.is_correct);

    /* staggered section entry — add class after mount */
    const [ready, setReady] = useState(false);
    useEffect(() => { const t = requestAnimationFrame(() => setReady(true)); return () => cancelAnimationFrame(t); }, []);

    return (
        <div className="learn-page">
            <div className={`er-container${ready ? ' er-container--ready' : ''}`}>

                {/* 1. Banner — score + status */}
                <div className="er-anim er-anim--1">
                    <ResultBanner score={score} total={total} passed={passed} levelNum={levelNum} />
                </div>

                {/* 2. Points earned */}
                {points_earned > 0 && (
                    <div className="er-anim er-anim--2">
                        <PointsEarnedBadge points={points_earned} passed={passed} />
                    </div>
                )}

                {/* 3. Mini stat cards */}
                <div className="er-anim er-anim--3">
                    <MiniStats score={score} total={total} passed={passed} levelNum={levelNum} />
                </div>

                {/* 4. Level progress trail */}
                <div className="er-anim er-anim--4">
                    <LevelTrail currentLevel={levelNum} passed={passed} />
                </div>

                {/* 5. Weak areas (only if wrong answers exist) */}
                {wrongAnswers.length > 0 && (
                    <div className="er-anim er-anim--5">
                        <WeakAreasSection wrongAnswers={wrongAnswers} />
                    </div>
                )}

                {/* 6. Recommendations / study path */}
                <div className="er-anim er-anim--6">
                    <StudyPath
                        passed={passed}
                        levelNum={levelNum}
                        wrongCount={wrongAnswers.length}
                        subjectSlug={subjectSlug}
                    />
                </div>

                {/* 7. CTA buttons */}
                <div className="er-actions er-anim er-anim--7">
                    {passed ? (
                        <>
                            <Link
                                to={`/learning/${category}/${subjectSlug}`}
                                state={{ subjectId }}
                                className="er-btn er-btn--outline"
                            >
                                Back to Levels
                            </Link>
                            <Link
                                to={`/learning/${category}/${subjectSlug}/${levelNum + 1}`}
                                state={{ subjectId }}
                                className="er-btn er-btn--primary"
                            >
                                Explore Level {levelNum + 1} →
                            </Link>
                        </>
                    ) : (
                        <>
                            <Link
                                to={`/learning/${category}/${subjectSlug}/${levelNum}`}
                                state={{ subjectId }}
                                className="er-btn er-btn--outline"
                            >
                                Review Content
                            </Link>
                            <button className="er-btn er-btn--primary" onClick={onRetry}>
                                Try Again
                            </button>
                        </>
                    )}
                </div>

                {/* 8. Full review — collapsible */}
                {answers.length > 0 && (
                    <div className="er-anim er-anim--8">
                        <AnswerReview answers={answers} />
                    </div>
                )}

            </div>
        </div>
    );
}

/* ──────────────────────────────────────────────────────────────
   EXAM PAGE
   ────────────────────────────────────────────────────────────── */
export default function LevelExamPage() {
    const { category, subjectSlug, level } = useParams<{
        category: string;
        subjectSlug: string;
        level: string;
    }>();
    const location = useLocation();
    const levelNum = Number(level);

    const stateSubjectId = (location.state as { subjectId?: number } | null)?.subjectId;
    const { data: subjectFromApi } = useSubjectBySlug(subjectSlug ?? '', !stateSubjectId);

    const subjectId    = stateSubjectId ?? subjectFromApi?.id ?? 0;
    const subjectTitle = subjectFromApi?.title ?? subjectSlug ?? '';

    const { data: questions = [], isLoading, isError } = useExamQuestions(subjectId, levelNum);
    const { mutate: submitExam, isPending } = useSubmitExam();

    const [answers, setAnswers] = useState<Record<number, number>>({});
    const [result, setResult]   = useState<ExamResult | null>(null);

    if (!category || !subjectSlug || !levelNum) return <Navigate to="/learning" replace />;

    const categoryLabel = category === 'languages' ? 'Languages'
                        : category === 'frontend'  ? 'Frontend'
                        : category;

    const answered    = Object.keys(answers).length;
    const allAnswered = questions.length > 0 && answered === questions.length;

    const handleSubmit = () => {
        if (!allAnswered || !subjectId) return;
        submitExam(
            { subjectId, level: levelNum, answers },
            { onSuccess: (data) => setResult(data) },
        );
    };

    if (result) {
        return (
            <ExamResultScreen
                result={result}
                levelNum={levelNum}
                category={category}
                subjectSlug={subjectSlug ?? ''}
                subjectId={subjectId}
                onRetry={() => { setResult(null); setAnswers({}); }}
            />
        );
    }

    return (
        <div className="learn-page">
            <nav className="breadcrumb" aria-label="Breadcrumb">
                <div className="breadcrumb-item">
                    <Link to="/learning" className="breadcrumb-link">Learning</Link>
                    <ChevronRight size={13} className="breadcrumb-separator" />
                </div>
                <div className="breadcrumb-item">
                    <Link to={`/learning/${category}`} className="breadcrumb-link">{categoryLabel}</Link>
                    <ChevronRight size={13} className="breadcrumb-separator" />
                </div>
                <div className="breadcrumb-item">
                    <Link
                        to={`/learning/${category}/${subjectSlug}`}
                        state={{ subjectId }}
                        className="breadcrumb-link"
                        style={{ textTransform: 'capitalize' }}
                    >
                        {subjectTitle || subjectSlug}
                    </Link>
                    <ChevronRight size={13} className="breadcrumb-separator" />
                </div>
                <div className="breadcrumb-item">
                    <Link
                        to={`/learning/${category}/${subjectSlug}/${levelNum}`}
                        state={{ subjectId }}
                        className="breadcrumb-link"
                    >
                        Level {levelNum}
                    </Link>
                    <ChevronRight size={13} className="breadcrumb-separator" />
                </div>
                <div className="breadcrumb-item">
                    <span className="breadcrumb-current">Exam</span>
                </div>
            </nav>

            <div className="page-header">
                <div>
                    <h1 className="page-header-title">Level {levelNum} Exam</h1>
                    <p className="page-header-description">
                        Score 8/10 or higher to pass. Answer all questions then submit.
                    </p>
                </div>
                <div className="exam-header-badge">
                    <ClipboardList size={14} />
                    8 / 10 to pass
                </div>
            </div>

            {isLoading ? (
                <div style={{ display: 'flex', flexDirection: 'column', gap: '1rem' }}>
                    {Array.from({ length: 5 }).map((_, i) => (
                        <div key={i} className="skeleton" style={{ height: 140, borderRadius: 12 }} />
                    ))}
                </div>
            ) : isError || questions.length === 0 ? (
                <div className="learn-empty">
                    <AlertCircle size={40} className="learn-empty-icon" />
                    <p>Not enough questions available for this level exam yet.</p>
                </div>
            ) : (
                <>
                    <div className="exam-progress-bar">
                        <span className="exam-progress-text">{answered} / {questions.length} answered</span>
                        <div className="exam-progress-track">
                            <div
                                className="exam-progress-fill"
                                style={{ width: `${(answered / questions.length) * 100}%` }}
                            />
                        </div>
                    </div>

                    <div className="exam-questions-list">
                        {questions.map((q, idx) => (
                            <div key={q.id} className="exam-question-card">
                                <div className="exam-q-header">
                                    <span className="exam-q-num">Q{idx + 1}</span>
                                    <span className={`exam-q-difficulty exam-q-difficulty--${q.difficulty.toLowerCase()}`}>
                                        {q.difficulty}
                                    </span>
                                </div>
                                <p className="exam-q-text">{q.question}</p>
                                <div className="exam-options">
                                    {q.options.map(opt => (
                                        <button
                                            key={opt.id}
                                            className={`exam-option${answers[q.id] === opt.id ? ' exam-option--selected' : ''}`}
                                            onClick={() => setAnswers(prev => ({ ...prev, [q.id]: opt.id }))}
                                        >
                                            <span className="exam-option-dot" />
                                            <span>{opt.option_text}</span>
                                        </button>
                                    ))}
                                </div>
                            </div>
                        ))}
                    </div>

                    <div className="exam-submit-row">
                        {!allAnswered && (
                            <p className="exam-submit-hint">Answer all {questions.length} questions to submit.</p>
                        )}
                        <button
                            className="lesson-complete-btn"
                            disabled={!allAnswered || isPending}
                            onClick={handleSubmit}
                            style={{ fontSize: '0.9375rem', padding: '0.625rem 1.875rem' }}
                        >
                            {isPending ? 'Submitting…' : 'Submit Exam'}
                        </button>
                    </div>
                </>
            )}
        </div>
    );
}
