import { useState } from 'react';
import { Link, useParams, Navigate } from 'react-router-dom';
import { Trophy, XCircle, AlertCircle, ClipboardList } from 'lucide-react';
import { useTheoryExamQuestions, useSubmitTheoryExam } from '../hooks/useTheoryLevel';
import type { TheoryExamResult } from '../../../types/api';
import '../theory.css';

const AREA_LABELS: Record<string, string> = {
    languages:          'Languages',
    frameworks:         'Frameworks',
    networking:         'Networking',
    'operating-systems':'Operating Systems',
    databases:          'Databases',
    'system-design':    'System Design',
    sdlc:               'SDLC',
    'data-structures':  'Data Structures',
};

const PASS_THRESHOLD: Record<number, number> = { 1: 8, 2: 9, 3: 10 };
const PASS_PCT: Record<number, number>       = { 1: 75, 2: 85, 3: 95 };

export default function TheoryExamPage() {
    const { area, level } = useParams<{ area: string; level: string }>();
    const levelNum = Number(level);

    if (!area || !levelNum || levelNum < 1 || levelNum > 3) {
        return <Navigate to="/theory" replace />;
    }

    const areaLabel = AREA_LABELS[area] ?? area;
    const threshold = PASS_THRESHOLD[levelNum] ?? 10;
    const pct       = PASS_PCT[levelNum] ?? 95;

    const { data: questions = [], isLoading, isError } = useTheoryExamQuestions(area, levelNum);
    const { mutate: submit, isPending } = useSubmitTheoryExam();

    const [answers, setAnswers] = useState<Record<number, number>>({});
    const [result, setResult]   = useState<TheoryExamResult | null>(null);

    const answered    = Object.keys(answers).length;
    const allAnswered = questions.length > 0 && answered === questions.length;

    const handleSubmit = () => {
        if (!allAnswered) return;
        submit(
            { area, level: levelNum, answers },
            { onSuccess: (data) => setResult(data) },
        );
    };

    // Result screen
    if (result) {
        const nextLevel = levelNum + 1;
        return (
            <div className="theory-v2-page">
                <div className="exam-result-wrap">
                    {result.passed ? (
                        <>
                            <div className="exam-result-icon-wrap exam-result-icon-wrap--pass">
                                <Trophy size={36} />
                            </div>
                            <h2 className="exam-result-title">Level {levelNum} Passed!</h2>
                            <p className="exam-result-score">{result.score} / {result.total}</p>
                            <p className="exam-result-sub">
                                {levelNum < 3
                                    ? `Great work. Level ${nextLevel} is now unlocked.`
                                    : 'You\'ve completed all three levels for this area.'}
                            </p>
                            <Link
                                to={`/theory/${area}`}
                                className="lesson-complete-btn"
                                style={{ marginTop: '1.75rem', display: 'inline-flex' }}
                            >
                                Back to {areaLabel}
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
                                {threshold}/10 ({pct}%) required to pass. Review and try again.
                            </p>
                            <div className="exam-result-actions">
                                <Link to={`/theory/${area}`} className="lesson-back-btn">
                                    Back to Levels
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
        <div className="theory-v2-page">
            {/* Breadcrumb */}
            <nav className="prac-breadcrumb" aria-label="Breadcrumb">
                <Link to="/theory" className="prac-breadcrumb-link">Theory</Link>
                <span className="prac-breadcrumb-sep">›</span>
                <Link to={`/theory/${area}`} className="prac-breadcrumb-link">{areaLabel}</Link>
                <span className="prac-breadcrumb-sep">›</span>
                <span>Level {levelNum} Exam</span>
            </nav>

            {/* Header */}
            <div className="page-header">
                <div>
                    <h1 className="page-header-title">Level {levelNum} Exam</h1>
                    <p className="page-header-description">
                        {threshold}/10 required to pass ({pct}%). Answer all questions to submit.
                    </p>
                </div>
                <div className="exam-header-badge">
                    <ClipboardList size={14} />
                    Pass: {threshold}/10
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
                    <p>Not enough questions available for this level yet.</p>
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
                                    {q.options.map((opt) => (
                                        <button
                                            key={opt.id}
                                            className={`exam-option${answers[q.id] === opt.id ? ' exam-option--selected' : ''}`}
                                            onClick={() => setAnswers((prev) => ({ ...prev, [q.id]: opt.id }))}
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
