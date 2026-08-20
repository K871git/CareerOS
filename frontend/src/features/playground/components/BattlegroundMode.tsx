import { useState, useEffect, useCallback, useRef } from 'react';
import Editor from '@monaco-editor/react';
import {
    ChevronDown, Play, Check, X, Clock, AlertTriangle,
    ChevronRight, Loader2, Trophy, Sun, Moon, Settings2,
} from 'lucide-react';
import { useProblemList, useProblemDetail } from '../hooks/useBattleground';
import { useSubmitSolution }                from '../hooks/useSubmitSolution';
import type { Difficulty, SubmitStatus, TestCaseResult, SubmitResult } from '../types';

interface EditorSettings {
    minimap:   boolean;
    wordWrap:  boolean;
    fontSize:  number;
}

/* ── Helpers ─────────────────────────────────────── */

function diffBadge(d: Difficulty) {
    const map: Record<Difficulty, string> = {
        easy:   'bg-badge-easy',
        medium: 'bg-badge-medium',
        hard:   'bg-badge-hard',
    };
    return <span className={`bg-diff-badge ${map[d]}`}>{d}</span>;
}

function statusIcon(s: SubmitStatus | null, size = 14) {
    if (!s)                           return null;
    if (s === 'accepted')             return <Check size={size} className="bg-icon-ok" />;
    if (s === 'time_limit_exceeded')  return <Clock size={size} className="bg-icon-warn" />;
    if (s === 'error')                return <AlertTriangle size={size} className="bg-icon-err" />;
    return <X size={size} className="bg-icon-err" />;
}

function statusLabel(s: SubmitStatus): string {
    const map: Record<SubmitStatus, string> = {
        accepted:             'Accepted',
        wrong_answer:         'Wrong Answer',
        time_limit_exceeded:  'Time Limit Exceeded',
        error:                'Runtime Error',
        pending:              'Pending',
    };
    return map[s];
}

/* ── Problem list panel ──────────────────────────── */

function ProblemListPanel({
    selectedSlug,
    onSelect,
}: {
    selectedSlug: string | null;
    onSelect: (slug: string) => void;
}) {
    const { data: problems, isLoading } = useProblemList();

    return (
        <div className="bg-problem-list">
            <div className="bg-panel-title">Problems</div>
            {isLoading && (
                <div className="bg-loading"><Loader2 size={14} className="bg-spin" /> Loading…</div>
            )}
            {problems?.map((p, i) => (
                <button
                    key={p.slug}
                    type="button"
                    className={`bg-problem-row${selectedSlug === p.slug ? ' active' : ''}`}
                    onClick={() => onSelect(p.slug)}
                >
                    <span className="bg-problem-num">{i + 1}.</span>
                    <span className="bg-problem-title">{p.title}</span>
                    <div className="bg-problem-meta">
                        {diffBadge(p.difficulty)}
                        {statusIcon(p.status, 12)}
                    </div>
                </button>
            ))}
        </div>
    );
}

/* ── Problem description panel ───────────────────── */

function ProblemDescription({ slug }: { slug: string }) {
    const { data: problem, isLoading } = useProblemDetail(slug);

    if (isLoading) {
        return (
            <div className="bg-desc-panel">
                <div className="bg-loading"><Loader2 size={14} className="bg-spin" /> Loading problem…</div>
            </div>
        );
    }

    if (!problem) return null;

    return (
        <div className="bg-desc-panel">
            <div className="bg-desc-header">
                <h2 className="bg-desc-title">{problem.title}</h2>
                {diffBadge(problem.difficulty)}
                {problem.last_submission && (
                    <span className={`bg-last-status ${problem.last_submission.status}`}>
                        {statusIcon(problem.last_submission.status, 12)}
                        {statusLabel(problem.last_submission.status)}
                    </span>
                )}
            </div>

            <div className="bg-desc-body">
                <div className="bg-desc-text">
                    {problem.description.split('\n').map((line, i) => {
                        if (line.startsWith('```')) return null;
                        if (line.startsWith('**') && line.endsWith('**')) {
                            return <p key={i} className="bg-desc-bold">{line.replace(/\*\*/g, '')}</p>;
                        }
                        if (line.trim() === '') return <br key={i} />;
                        return <p key={i}>{line}</p>;
                    })}
                </div>

                {problem.examples.length > 0 && (
                    <div className="bg-examples">
                        <div className="bg-section-title">Examples</div>
                        {problem.examples.map(ex => (
                            <div key={ex.id} className="bg-example">
                                <div className="bg-example-label">{ex.label}</div>
                                {ex.input && (
                                    <div className="bg-io-row">
                                        <span className="bg-io-key">Input:</span>
                                        <pre className="bg-io-val">{ex.input}</pre>
                                    </div>
                                )}
                                <div className="bg-io-row">
                                    <span className="bg-io-key">Output:</span>
                                    <pre className="bg-io-val">{ex.expected_output}</pre>
                                </div>
                            </div>
                        ))}
                    </div>
                )}

                {problem.constraints && (
                    <div className="bg-constraints">
                        <div className="bg-section-title">Constraints</div>
                        {problem.constraints.split('\n').map((line, i) => (
                            <p key={i} className="bg-constraint-line">{line.replace(/^-\s*/, '')}</p>
                        ))}
                    </div>
                )}
            </div>
        </div>
    );
}

/* ── Test results panel ──────────────────────────── */

function TestResults({ result }: { result: SubmitResult | null }) {
    const [expanded, setExpanded] = useState<number | null>(null);

    if (!result) {
        return (
            <div className="bg-results-empty">
                Submit your solution to see test results
            </div>
        );
    }

    const allPassed = result.status === 'accepted';

    return (
        <div className="bg-results">
            <div className={`bg-results-summary ${allPassed ? 'ok' : 'fail'}`}>
                {allPassed
                    ? <><Trophy size={14} /> All {result.test_cases_total} test cases passed!</>
                    : <>{result.test_cases_passed}/{result.test_cases_total} test cases passed — {statusLabel(result.status)}</>
                }
                <span className="bg-results-time">{result.execution_time_ms}ms</span>
            </div>

            <div className="bg-results-list">
                {result.results.map((tc: TestCaseResult) => (
                    <div key={tc.id} className={`bg-tc-row${tc.hidden ? ' hidden' : ''}`}>
                        <button
                            type="button"
                            className="bg-tc-header"
                            onClick={() => !tc.hidden && setExpanded(expanded === tc.id ? null : tc.id)}
                            disabled={tc.hidden}
                        >
                            <span className={`bg-tc-icon ${tc.passed ? 'pass' : 'fail'}`}>
                                {tc.passed ? <Check size={11} /> : <X size={11} />}
                            </span>
                            <span className="bg-tc-label">
                                {tc.hidden ? `Hidden Test Case ${tc.order}` : tc.label}
                            </span>
                            {!tc.hidden && tc.status !== 'accepted' && (
                                <span className="bg-tc-status">{statusLabel(tc.status)}</span>
                            )}
                            {!tc.hidden && (
                                <ChevronRight
                                    size={12}
                                    className={`bg-tc-chevron${expanded === tc.id ? ' open' : ''}`}
                                />
                            )}
                        </button>

                        {expanded === tc.id && !tc.hidden && (
                            <div className="bg-tc-detail">
                                {tc.input !== undefined && tc.input !== '' && (
                                    <div className="bg-io-row">
                                        <span className="bg-io-key">Input:</span>
                                        <pre className="bg-io-val">{tc.input}</pre>
                                    </div>
                                )}
                                <div className="bg-io-row">
                                    <span className="bg-io-key">Expected:</span>
                                    <pre className="bg-io-val ok">{tc.expected}</pre>
                                </div>
                                <div className="bg-io-row">
                                    <span className="bg-io-key">Your output:</span>
                                    <pre className={`bg-io-val${tc.passed ? ' ok' : ' err'}`}>{tc.actual}</pre>
                                </div>
                            </div>
                        )}
                    </div>
                ))}
            </div>
        </div>
    );
}

/* ── Limited settings panel (no suggestion options) ─ */

function BgSettingsPanel({
    settings,
    onChange,
}: {
    settings: EditorSettings;
    onChange: (patch: Partial<EditorSettings>) => void;
}) {
    const [open, setOpen] = useState(false);
    const ref = useRef<HTMLDivElement>(null);

    useEffect(() => {
        const handler = (e: MouseEvent) => {
            if (ref.current && !ref.current.contains(e.target as Node)) setOpen(false);
        };
        document.addEventListener('mousedown', handler);
        return () => document.removeEventListener('mousedown', handler);
    }, []);

    return (
        <div className="pg-settings-wrap" ref={ref}>
            <button type="button" className="pg-icon-btn" title="Editor settings" onClick={() => setOpen(o => !o)}>
                <Settings2 size={14} />
            </button>
            {open && (
                <div className="pg-settings-menu">
                    <div className="pg-settings-header">Editor Settings</div>
                    <div className="pg-settings-section">
                        <label className="pg-toggle-row">
                            <span className="pg-toggle-info">
                                <span className="pg-toggle-label">Minimap</span>
                                <span className="pg-toggle-desc">Code overview scrollbar on the right</span>
                            </span>
                            <button
                                type="button"
                                role="switch"
                                aria-checked={settings.minimap}
                                className={`pg-toggle${settings.minimap ? ' on' : ''}`}
                                onClick={() => onChange({ minimap: !settings.minimap })}
                            />
                        </label>
                        <label className="pg-toggle-row">
                            <span className="pg-toggle-info">
                                <span className="pg-toggle-label">Word Wrap</span>
                                <span className="pg-toggle-desc">Wrap long lines instead of scrolling</span>
                            </span>
                            <button
                                type="button"
                                role="switch"
                                aria-checked={settings.wordWrap}
                                className={`pg-toggle${settings.wordWrap ? ' on' : ''}`}
                                onClick={() => onChange({ wordWrap: !settings.wordWrap })}
                            />
                        </label>
                    </div>
                    <div className="pg-settings-section pg-settings-section--font">
                        <div className="pg-settings-row-label">Font Size</div>
                        <div className="pg-font-size-row">
                            <button type="button" className="pg-font-btn"
                                disabled={settings.fontSize <= 11}
                                onClick={() => onChange({ fontSize: Math.max(11, settings.fontSize - 1) })}>−</button>
                            <span className="pg-font-value">{settings.fontSize}px</span>
                            <button type="button" className="pg-font-btn"
                                disabled={settings.fontSize >= 22}
                                onClick={() => onChange({ fontSize: Math.min(22, settings.fontSize + 1) })}>+</button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}

/* ── Main BattlegroundMode ───────────────────────── */

interface FullEditorSettings {
    inlineSuggestions: boolean;
    quickSuggestions:  boolean;
    minimap:           boolean;
    wordWrap:          boolean;
    fontSize:          number;
}

interface Props {
    theme:            'dark' | 'light';
    onThemeToggle:    () => void;
    settings:         FullEditorSettings;
    onSettingsChange: (patch: Partial<FullEditorSettings>) => void;
}

export function BattlegroundMode({ theme, onThemeToggle, settings, onSettingsChange }: Props) {
    const [selectedSlug, setSelectedSlug] = useState<string | null>(null);
    const [code, setCode]                 = useState<string>('');
    const [result, setResult]             = useState<SubmitResult | null>(null);
    const [splitPct, setSplitPct]         = useState(45);
    const [showList, setShowList]         = useState(true);
    const bodyRef                          = useRef<HTMLDivElement>(null);
    const dragging                         = useRef(false);

    const { data: problem }              = useProblemDetail(selectedSlug);
    const { mutate: submit, isPending }  = useSubmitSolution(selectedSlug ?? '');

    // When a problem loads for the first time, load its starter code
    useEffect(() => {
        if (problem) {
            const saved = localStorage.getItem(`bg:code:${problem.slug}`);
            setCode(saved ?? problem.starter_code ?? '<?php\n\n');
            setResult(null);
        }
    }, [problem?.slug]);

    // Auto-select first problem on load
    const handleSelectProblem = useCallback((slug: string) => {
        if (selectedSlug) {
            localStorage.setItem(`bg:code:${selectedSlug}`, code);
        }
        setSelectedSlug(slug);
        setResult(null);
    }, [selectedSlug, code]);

    const handleSubmit = useCallback(() => {
        if (!selectedSlug || !problem || isPending) return;
        localStorage.setItem(`bg:code:${selectedSlug}`, code);
        submit(
            { language: problem.language, code },
            { onSuccess: (res) => setResult(res) },
        );
    }, [selectedSlug, problem, code, isPending, submit]);

    // Drag-to-resize
    const startDrag = (e: React.MouseEvent) => { e.preventDefault(); dragging.current = true; };
    useEffect(() => {
        const onMove = (e: MouseEvent) => {
            if (!dragging.current || !bodyRef.current) return;
            const rect = bodyRef.current.getBoundingClientRect();
            const pct = Math.max(25, Math.min(65, Math.round(((e.clientX - rect.left) / rect.width) * 100)));
            setSplitPct(pct);
        };
        const onUp = () => { dragging.current = false; };
        document.addEventListener('mousemove', onMove);
        document.addEventListener('mouseup', onUp);
        return () => { document.removeEventListener('mousemove', onMove); document.removeEventListener('mouseup', onUp); };
    }, []);

    return (
        <div className="bg-root">

            {/* ── Left sidebar: problem list + description ── */}
            <div className="bg-left" style={{ width: `${splitPct}%` }}>

                {/* Problem list toggle */}
                <button
                    type="button"
                    className="bg-list-toggle"
                    onClick={() => setShowList(v => !v)}
                >
                    <ChevronDown
                        size={13}
                        style={{ transform: showList ? 'rotate(0deg)' : 'rotate(-90deg)', transition: 'transform 0.15s' }}
                    />
                    All Problems ({showList ? 'hide' : 'show'})
                </button>

                {showList && (
                    <ProblemListPanel selectedSlug={selectedSlug} onSelect={handleSelectProblem} />
                )}

                {selectedSlug && <ProblemDescription slug={selectedSlug} />}

                {!selectedSlug && (
                    <div className="bg-empty-state">
                        <Trophy size={32} className="bg-empty-icon" />
                        <p>Select a problem to start coding</p>
                    </div>
                )}
            </div>

            {/* ── Drag handle ── */}
            <div className="pg-drag-handle" onMouseDown={startDrag} title="Drag to resize" />

            {/* ── Right: editor + results ── */}
            <div className="bg-right" ref={bodyRef} style={{ flex: 1 }}>

                {/* Editor toolbar */}
                <div className="bg-editor-bar">
                    <span className="bg-lang-badge">PHP</span>
                    {problem && (
                        <span className="bg-problem-name">{problem.title}</span>
                    )}
                    <div className="bg-editor-actions">
                        <BgSettingsPanel
                            settings={{ minimap: settings.minimap, wordWrap: settings.wordWrap, fontSize: settings.fontSize }}
                            onChange={onSettingsChange}
                        />
                        <button
                            type="button"
                            className="pg-icon-btn"
                            title={theme === 'dark' ? 'Light mode' : 'Dark mode'}
                            onClick={onThemeToggle}
                        >
                            {theme === 'dark' ? <Sun size={14} /> : <Moon size={14} />}
                        </button>
                        <div className="pg-topbar-divider" />
                        <button
                            type="button"
                            className="pg-run-btn"
                            onClick={handleSubmit}
                            disabled={!selectedSlug || isPending}
                        >
                            {isPending
                                ? <><Loader2 size={12} className="bg-spin" /> Running…</>
                                : <><Play size={12} strokeWidth={2.5} /> Submit</>
                            }
                        </button>
                    </div>
                </div>

                {/* Monaco editor */}
                <div className="bg-editor-wrap">
                    <Editor
                        height="100%"
                        language="php"
                        value={code}
                        onChange={v => setCode(v ?? '')}
                        theme={theme === 'dark' ? 'vs-dark' : 'vs'}
                        options={{
                            fontSize:             settings.fontSize,
                            minimap:              { enabled: settings.minimap },
                            wordWrap:             settings.wordWrap ? 'on' : 'off',
                            scrollBeyondLastLine: false,
                            lineNumbers:          'on',
                            renderLineHighlight:  'line',
                            padding:              { top: 12 },
                        }}
                    />
                </div>

                {/* Results */}
                <div className="bg-results-wrap">
                    <TestResults result={result} />
                </div>
            </div>
        </div>
    );
}
