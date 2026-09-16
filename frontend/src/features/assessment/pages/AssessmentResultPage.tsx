import { useState } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import { CheckCircle2, XCircle, Sparkles } from 'lucide-react';
import { useAttemptResult } from '../hooks/useMCQ';
import ResultSummary from '../components/ResultSummary';
import '../assessment.css';

const API_BASE = import.meta.env.VITE_API_URL ?? 'http://localhost:8000/api';

type ExplainState =
    | { status: 'idle' }
    | { status: 'loading' }
    | { status: 'streaming'; text: string }
    | { status: 'done'; text: string }
    | { status: 'unavailable' };

// Content comes from our OllamaService — not raw user input — so HTML injection is safe here.
function inlineFormat(text: string, autoCodeTerm?: string): string {
    let t = text;

    // Auto-wrap the correct answer only when it's a code-like term:
    // ≤ 3 words so we don't wrap full sentences (e.g. "Removes duplicate rows from the result")
    if (autoCodeTerm?.trim()) {
        const words = autoCodeTerm.trim().split(/\s+/);
        if (words.length <= 3) {
            const escaped = autoCodeTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            t = t.replace(new RegExp(`(?<!\`)\\b(${escaped})\\b(?!\`)`, 'gi'), '`$1`');
        }
    }

    // Merge adjacent backtick terms that have only short plain text between them.
    // The model often writes `SELECT` CustomerName `FROM` table → collapse into one chip.
    let prev = '';
    while (prev !== t) {
        prev = t;
        t = t.replace(/`([^`\n]+)`([^`\n]{0,18})`([^`\n]+)`/, '`$1$2$3`');
    }

    return t
        .replace(/\*\*([^*\n]+)\*\*/g, '<mark class="explain-hl">$1</mark>')
        .replace(/`([^`\n]+)`/g,        '<code class="explain-code">$1</code>')
        .replace(/\*/g, ''); // strip stray asterisks
}

// Hardcoded labels by section position — not dependent on model output
const POSITION_LABELS: Record<string, string> = {
    '1': 'WHY CORRECT',
    '2': 'CONCEPT',
    '3': 'REAL WORLD',
    '4': 'WHY WRONG',
};

// Strip any "Label: " prefix the model adds (we show our own hardcoded label)
function stripModelLabel(text: string): string {
    const match = text.match(/^[^:]{2,20}:\s+(.+)$/s);
    return match ? match[1] : text;
}

type Block =
    | { kind: 'numbered'; num: string; label: string; html: string }
    | { kind: 'code'; code: string; lang?: string }
    | { kind: 'para'; html: string };

function parseBlocks(raw: string, correctAnswer?: string): Block[] {
    const cleaned = raw
        .replace(/^\*{0,2}(?:answer\s+)?explanation\*{0,2}[\r\n]*/i, '')
        .trim();

    // ── Fallback: model outputs literal "N." as section marker → convert to 1. 2. 3. ──
    let nCounter = 0;
    const deN = /\bN\.\s/.test(cleaned)
        ? cleaned.replace(/(?:^|\n)\s*N\.\s*/g, () => `\n${++nCounter}. `).trimStart()
        : cleaned;

    // ── Normalize: "N Uppercase" → "N. Uppercase" for sections 1–9 ──
    // Safe: "In 2 sentences" has lowercase 's' → unaffected.
    const normalized = deN.replace(/\b([1-9])\s+(?=[A-Z`*])/g, '$1. ');

    // ── Split before each "N. " marker that appears mid-line ──
    const preSplit = normalized.replace(/([^\n])\s+([1-9][.)]\s)/g, '$1\n$2');

    const rawLines = preSplit.split(/\n/);
    const blocks: Block[] = [];
    let proseBuf: string[] = [];
    let inFence   = false;
    let fenceLang = '';
    let fenceBuf: string[] = [];

    const flushProse = () => {
        const text = proseBuf.map(l => l.trim()).filter(Boolean).join(' ');
        if (text) blocks.push({ kind: 'para', html: inlineFormat(text, correctAnswer) });
        proseBuf = [];
    };

    const flushFence = () => {
        blocks.push({ kind: 'code', code: fenceBuf.join('\n'), lang: fenceLang });
        fenceBuf = [];
        fenceLang = '';
        inFence = false;
    };

    for (const rawLine of rawLines) {
        const trimmed = rawLine.trim();

        // ── Inside fence: collect raw lines to preserve indentation ──
        if (inFence) {
            if (trimmed.startsWith('```')) flushFence();
            else fenceBuf.push(rawLine);
            continue;
        }

        // ── Opening fence ──
        if (trimmed.startsWith('```')) {
            flushProse();
            inFence   = true;
            fenceLang = trimmed.slice(3).trim().toLowerCase();
            continue;
        }

        if (!trimmed) continue;

        // ── Numbered section marker ──
        const numMatch = trimmed.match(/^([1-9])[.)]\s+(.*)/);
        if (numMatch) {
            flushProse();
            const num  = numMatch[1];
            const rest = stripModelLabel(numMatch[2].trim());

            // Edge case: section opens immediately with a fence ("3. ```python")
            if (rest.startsWith('```')) {
                blocks.push({ kind: 'numbered', num, label: POSITION_LABELS[num] ?? `SECTION ${num}`, html: '' });
                inFence   = true;
                fenceLang = rest.slice(3).trim().toLowerCase();
            } else {
                blocks.push({
                    kind:  'numbered',
                    num,
                    label: POSITION_LABELS[num] ?? `SECTION ${num}`,
                    html:  inlineFormat(rest, correctAnswer),
                });
            }
        } else {
            proseBuf.push(trimmed);
        }
    }

    if (inFence) flushFence(); // unclosed fence — still emit what the model wrote
    flushProse();
    return blocks;
}

function ExplainPanel({
    questionId,
    correctAnswer,
    wrongOptionId,
    variant = 'explain',
}: {
    questionId: number;
    correctAnswer?: string;
    wrongOptionId?: number | null;
    variant?: 'explain' | 'learn-more';
}) {
    const [state, setState] = useState<ExplainState>({ status: 'idle' });

    async function fetchExplanation() {
        setState({ status: 'loading' });
        let accumulated = '';

        try {
            const body: Record<string, unknown> = { question_id: questionId };
            if (wrongOptionId) body.wrong_option_id = wrongOptionId;

            const token = localStorage.getItem('careeros_token');
            const response = await fetch(`${API_BASE}/v1/ai/explain/stream`, {
                method: 'POST',
                headers: {
                    'Content-Type':  'application/json',
                    'Authorization': `Bearer ${token ?? ''}`,
                },
                body: JSON.stringify(body),
            });

            if (!response.ok || !response.body) {
                setState({ status: 'unavailable' });
                return;
            }

            const reader  = response.body.getReader();
            const decoder = new TextDecoder();
            let sseBuffer = '';

            while (true) {
                const { done, value } = await reader.read();
                if (done) break;

                sseBuffer += decoder.decode(value, { stream: true });

                // Process all complete SSE lines (split on newline, keep incomplete tail)
                const lines = sseBuffer.split('\n');
                sseBuffer = lines.pop() ?? '';

                for (const line of lines) {
                    if (!line.startsWith('data: ')) continue;
                    const jsonStr = line.slice(6).trim();
                    if (!jsonStr) continue;

                    try {
                        const data = JSON.parse(jsonStr) as Record<string, unknown>;

                        if (data.unavailable) {
                            setState({ status: 'unavailable' });
                            return;
                        }

                        if (data.done) {
                            // Cache hit → full text arrives immediately in data.text
                            const finalText = typeof data.text === 'string' ? data.text : accumulated;
                            setState({ status: 'done', text: finalText });
                            return;
                        }

                        if (typeof data.token === 'string' && data.token) {
                            accumulated += data.token;
                            setState({ status: 'streaming', text: accumulated });
                        }
                    } catch {
                        // malformed SSE line — skip
                    }
                }
            }

            // Stream ended without explicit done event
            if (accumulated) setState({ status: 'done', text: accumulated });
            else setState({ status: 'unavailable' });

        } catch {
            // Network error — if we have partial text, show it
            if (accumulated) setState({ status: 'done', text: accumulated });
            else setState({ status: 'unavailable' });
        }
    }

    if (state.status === 'unavailable') return null;

    if (state.status === 'idle') {
        return variant === 'learn-more' ? (
            <button className="explain-btn explain-btn--subtle" onClick={fetchExplanation}>
                <Sparkles size={11} /> Learn more
            </button>
        ) : (
            <button className="explain-btn" onClick={fetchExplanation}>
                <Sparkles size={12} /> Explain this answer
            </button>
        );
    }

    if (state.status === 'loading') {
        return (
            <div className="explain-panel explain-panel--loading">
                <div className="explain-dots-row">
                    <span className="explain-dot" style={{ animationDelay: '0ms' }} />
                    <span className="explain-dot" style={{ animationDelay: '160ms' }} />
                    <span className="explain-dot" style={{ animationDelay: '320ms' }} />
                </div>
                <span className="explain-loading-text">CareerOS AI is thinking…</span>
            </div>
        );
    }

    // ── streaming + done share the same panel layout ──
    const isStreaming = state.status === 'streaming';
    const blocks      = parseBlocks(state.text, correctAnswer);

    return (
        <div className="explain-panel">
            <div className="explain-header">
                <span className="explain-badge">
                    <Sparkles size={11} />
                    CareerOS AI
                </span>
                {isStreaming && <span className="explain-gen-tag">generating…</span>}
            </div>
            <div className="explain-body">
                {blocks.map((block, i) => {
                    if (block.kind === 'numbered') {
                        return (
                            <div key={i} className="explain-item">
                                <span className="explain-num">{block.num}</span>
                                <div className="explain-item-content">
                                    <span className="explain-section-label">{block.label}</span>
                                    {block.html && (
                                        <span
                                            className="explain-item-text"
                                            dangerouslySetInnerHTML={{ __html: block.html }}
                                        />
                                    )}
                                </div>
                            </div>
                        );
                    }
                    if (block.kind === 'code') {
                        return (
                            <div key={i} className="explain-codeblock">
                                <pre><code>{block.code}</code></pre>
                            </div>
                        );
                    }
                    return (
                        <p
                            key={i}
                            className="explain-para"
                            dangerouslySetInnerHTML={{ __html: block.html }}
                        />
                    );
                })}
                {isStreaming && <span className="explain-cursor" aria-hidden="true" />}
            </div>
        </div>
    );
}

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
    const [showWrongOnly, setShowWrongOnly] = useState(false);

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

    const subjectId      = result.subject_id;
    const topicId        = result.topic_id;
    const wrongCount     = result.answers.filter(a => !a.is_correct).length;
    const visibleAnswers = showWrongOnly
        ? result.answers.filter(a => !a.is_correct)
        : result.answers;

    return (
        <div className="practice-page">
            <div className="practice-inner">

                {/* ── Top Nav ── */}
                <div className="result-topnav">
                    <div className="result-topnav-left">
                        {subjectId ? (
                            <button type="button" className="result-nav-back" onClick={() => navigate(`/practice/subjects/${subjectId}`)}>
                                ← Back to Levels
                            </button>
                        ) : (
                            <button type="button" className="result-nav-back" onClick={() => navigate('/practice')}>
                                ← New Practice
                            </button>
                        )}
                    </div>
                    {topicId && (
                        <button type="button" className="result-nav-action" onClick={() => navigate(`/practice/topics/${topicId}`)}>
                            Practice Again →
                        </button>
                    )}
                </div>

                {/* ── Score Summary ── */}
                <ResultSummary result={result} />

                {/* ── Smart CTA ── */}
                {result.percentage < 70 ? (
                    <div className="result-cta result-cta--retry">
                        <div className="result-cta-text">
                            <strong>Room to grow.</strong> Review what went wrong below, then try again — repetition is how it sticks.
                        </div>
                        {topicId && (
                            <button type="button" className="result-cta-btn result-cta-btn--retry" onClick={() => navigate(`/practice/topics/${topicId}`)}>
                                Retry this topic →
                            </button>
                        )}
                    </div>
                ) : result.percentage >= 90 ? (
                    <div className="result-cta result-cta--excellent">
                        <div className="result-cta-text">
                            <strong>Excellent!</strong> You've mastered this topic. Ready to push further?
                        </div>
                        {subjectId && (
                            <button type="button" className="result-cta-btn result-cta-btn--next" onClick={() => navigate(`/practice/subjects/${subjectId}`)}>
                                Try next level →
                            </button>
                        )}
                    </div>
                ) : (
                    <div className="result-cta result-cta--good">
                        <div className="result-cta-text">
                            <strong>Good work!</strong> Check what went wrong below, then tackle a harder level.
                        </div>
                        {subjectId && (
                            <button type="button" className="result-cta-btn result-cta-btn--next" onClick={() => navigate(`/practice/subjects/${subjectId}`)}>
                                Try next level →
                            </button>
                        )}
                    </div>
                )}

                {/* ── Review Section ── */}
                <div className="result-review">
                    <div className="result-review-header">
                        <h2 className="result-review-title">Review Answers</h2>
                        {wrongCount > 0 && (
                            <button
                                type="button"
                                className={`result-filter-pill ${showWrongOnly ? 'result-filter-pill--active' : ''}`}
                                onClick={() => setShowWrongOnly(p => !p)}
                            >
                                <XCircle size={12} />
                                {showWrongOnly ? 'Show all' : `Wrong only (${wrongCount})`}
                            </button>
                        )}
                    </div>

                    <div className="result-cards">
                        {visibleAnswers.map((answer) => {
                            const originalIndex = result.answers.indexOf(answer);
                            return (
                                <div
                                    key={answer.question_id}
                                    className={`rcard ${answer.is_correct ? 'rcard--correct' : 'rcard--wrong'}`}
                                >
                                    {/* Card header */}
                                    <div className="rcard-head">
                                        <div className="rcard-meta">
                                            <span className="rcard-num">Q{originalIndex + 1}</span>
                                            {answer.difficulty && (
                                                <span className={`rcard-diff rcard-diff--${answer.difficulty.toLowerCase()}`}>
                                                    {answer.difficulty}
                                                </span>
                                            )}
                                        </div>
                                        <span className={`rcard-verdict ${answer.is_correct ? 'rcard-verdict--correct' : 'rcard-verdict--wrong'}`}>
                                            {answer.is_correct
                                                ? <><CheckCircle2 size={13} /> Correct</>
                                                : <><XCircle size={13} /> Wrong</>
                                            }
                                        </span>
                                    </div>

                                    {/* Question */}
                                    <p className="rcard-question">{answer.question}</p>

                                    {/* Options */}
                                    {answer.options?.length > 0 ? (
                                        <div className="rcard-options">
                                            {answer.options.map((opt) => {
                                                // ← Bug fix: compare by ID, not by option text string
                                                const isSelected = opt.id === answer.selected_option_id;
                                                return (
                                                    <div
                                                        key={opt.id}
                                                        className={`ropt ${opt.is_correct ? 'ropt--correct' : isSelected ? 'ropt--wrong' : 'ropt--neutral'}`}
                                                    >
                                                        <span className="ropt-icon">
                                                            {opt.is_correct
                                                                ? <CheckCircle2 size={13} />
                                                                : isSelected
                                                                    ? <XCircle size={13} />
                                                                    : <span className="ropt-dot" />
                                                            }
                                                        </span>
                                                        <span className="ropt-text">{opt.option_text}</span>
                                                        {opt.is_correct && (
                                                            <span className="ropt-badge ropt-badge--correct">Correct</span>
                                                        )}
                                                        {isSelected && !opt.is_correct && (
                                                            <span className="ropt-badge ropt-badge--wrong">Your answer</span>
                                                        )}
                                                    </div>
                                                );
                                            })}
                                        </div>
                                    ) : (
                                        /* Fallback: no options array (older data) */
                                        <div className="rcard-options">
                                            {!answer.is_correct && answer.selected_option && (
                                                <div className="ropt ropt--wrong">
                                                    <span className="ropt-icon"><XCircle size={13} /></span>
                                                    <span className="ropt-text">{answer.selected_option}</span>
                                                    <span className="ropt-badge ropt-badge--wrong">Your answer</span>
                                                </div>
                                            )}
                                            <div className="ropt ropt--correct">
                                                <span className="ropt-icon"><CheckCircle2 size={13} /></span>
                                                <span className="ropt-text">{answer.correct_option ?? '—'}</span>
                                                <span className="ropt-badge ropt-badge--correct">Correct</span>
                                            </div>
                                        </div>
                                    )}

                                    {/* DB explanation (not AI) */}
                                    {answer.explanation && (
                                        <p className="rcard-explanation">{answer.explanation}</p>
                                    )}

                                    {/* AI explain / Learn more */}
                                    <div className="rcard-ai">
                                        {answer.is_correct ? (
                                            <ExplainPanel
                                                questionId={answer.question_id}
                                                correctAnswer={answer.correct_option ?? undefined}
                                                variant="learn-more"
                                            />
                                        ) : (
                                            <ExplainPanel
                                                questionId={answer.question_id}
                                                correctAnswer={answer.correct_option ?? undefined}
                                                wrongOptionId={answer.selected_option_id}
                                            />
                                        )}
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </div>

            </div>
        </div>
    );
}
