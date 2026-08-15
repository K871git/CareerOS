import { useState } from 'react';
import { Link, useParams, useLocation, Navigate } from 'react-router-dom';
import { ChevronRight, Trophy, XCircle, AlertCircle, ClipboardList } from 'lucide-react';
import { useSubjectBySlug, useExamQuestions, useSubmitExam } from '../hooks/useLevel';
import type { ExamResult } from '../../../types/api';
import '../learning.css';

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
    const [result, setResult] = useState<ExamResult | null>(null);

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

    // Result screen
    if (result) {
        return (
            <div className="learn-page">
                <div className="exam-result-wrap">
                    {result.passed ? (
                        <>
                            <div className="exam-result-icon-wrap exam-result-icon-wrap--pass">
                                <Trophy size={36} />
                            </div>
                            <h2 className="exam-result-title">Level {levelNum} Complete!</h2>
                            <p className="exam-result-score">{result.score} / {result.total}</p>
                            <p className="exam-result-sub">Perfect score. You've unlocked Level {levelNum + 1}.</p>
                            <Link
                                to={`/learning/${category}/${subjectSlug}`}
                                state={{ subjectId }}
                                className="lesson-complete-btn"
                                style={{ marginTop: '1.75rem', display: 'inline-flex' }}
                            >
                                Back to Levels
                            </Link>
                        </>
                    ) : (
                        <>
                            <div className="exam-result-icon-wrap exam-result-icon-wrap--fail">
                                <XCircle size={36} />
                            </div>
                            <h2 className="exam-result-title">Not Quite</h2>
                            <p className="exam-result-score">{result.score} / {result.total}</p>
                            <p className="exam-result-sub">
                                10/10 required to pass. Review the topics and try again.
                            </p>
                            <div className="exam-result-actions">
                                <Link
                                    to={`/learning/${category}/${subjectSlug}/${levelNum}`}
                                    state={{ subjectId }}
                                    className="lesson-back-btn"
                                >
                                    Review Level
                                </Link>
                                <button
                                    className="lesson-complete-btn"
                                    onClick={() => { setResult(null); setAnswers({}); }}
                                >
                                    Try Again
                                </button>
                            </div>
                        </>
                    )}
                </div>
            </div>
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
                        Answer all 10 questions correctly to pass. No partial credit.
                    </p>
                </div>
                <div className="exam-header-badge">
                    <ClipboardList size={14} />
                    10 / 10 to pass
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
