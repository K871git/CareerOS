import type { AssessmentAttemptResult } from '../../../types/api';

interface Props {
    result: AssessmentAttemptResult;
}

function ScoreCircle({ pct }: { pct: number }) {
    const r = 52;
    const circumference = 2 * Math.PI * r;
    const offset = circumference - (pct / 100) * circumference;
    const color = pct >= 70 ? '#10b981' : pct >= 50 ? '#f59e0b' : '#ef4444';

    return (
        <div className="result-circle-wrap">
            <svg width="136" height="136" viewBox="0 0 136 136">
                <circle cx="68" cy="68" r={r} fill="none" stroke="var(--gray-100)" strokeWidth="10" />
                <circle
                    cx="68" cy="68" r={r}
                    fill="none"
                    stroke={color}
                    strokeWidth="10"
                    strokeLinecap="round"
                    strokeDasharray={circumference}
                    strokeDashoffset={offset}
                    transform="rotate(-90 68 68)"
                    style={{ transition: 'stroke-dashoffset 1.2s ease' }}
                />
            </svg>
            <div className="result-circle-inner">
                <span className="result-pct-num">{pct}%</span>
            </div>
        </div>
    );
}

const DIFFICULTY_ORDER = ['easy', 'medium', 'hard'];
const DIFFICULTY_LABEL: Record<string, string> = { easy: 'Easy', medium: 'Medium', hard: 'Hard' };
const DIFFICULTY_COLOR: Record<string, string> = {
    easy:   '#10b981',
    medium: '#f59e0b',
    hard:   '#ef4444',
};

export default function ResultSummary({ result }: Props) {
    const wrong  = result.total_questions - result.score;
    const passed = result.percentage >= 60;

    // Group answers by difficulty
    const byDiff: Record<string, { correct: number; total: number }> = {};
    for (const a of result.answers) {
        const key = (a.difficulty ?? 'unknown').toLowerCase();
        if (!byDiff[key]) byDiff[key] = { correct: 0, total: 0 };
        byDiff[key].total++;
        if (a.is_correct) byDiff[key].correct++;
    }
    const diffEntries = DIFFICULTY_ORDER.filter(d => byDiff[d]);

    return (
        <div className="result-summary">
            <p className={`result-badge ${passed ? 'result-badge--pass' : 'result-badge--fail'}`}>
                {passed ? 'Passed' : 'Keep Practicing'}
            </p>
            <ScoreCircle pct={result.percentage} />
            <p className="result-score-label">{result.score} / {result.total_questions} correct</p>
            <div className="result-stats-row">
                <div className="result-stat result-stat--correct">
                    <span className="result-stat-val">{result.score}</span>
                    <span className="result-stat-key">Correct</span>
                </div>
                <div className="result-stat-divider" />
                <div className="result-stat result-stat--wrong">
                    <span className="result-stat-val">{wrong}</span>
                    <span className="result-stat-key">Wrong</span>
                </div>
                <div className="result-stat-divider" />
                <div className="result-stat">
                    <span className="result-stat-val">{result.total_questions}</span>
                    <span className="result-stat-key">Total</span>
                </div>
            </div>

            {diffEntries.length > 1 && (
                <div className="result-diff-row">
                    {diffEntries.map(d => {
                        const { correct, total } = byDiff[d];
                        const pct = Math.round((correct / total) * 100);
                        return (
                            <div key={d} className="result-diff-chip">
                                <span
                                    className="result-diff-dot"
                                    style={{ background: DIFFICULTY_COLOR[d] }}
                                />
                                <span className="result-diff-label">{DIFFICULTY_LABEL[d]}</span>
                                <span className="result-diff-score">{correct}/{total}</span>
                                <div className="result-diff-bar-bg">
                                    <div
                                        className="result-diff-bar-fill"
                                        style={{
                                            width: `${pct}%`,
                                            background: DIFFICULTY_COLOR[d],
                                        }}
                                    />
                                </div>
                            </div>
                        );
                    })}
                </div>
            )}
        </div>
    );
}
