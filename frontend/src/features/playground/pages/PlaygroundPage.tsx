import { useState, useCallback, useEffect, useRef } from 'react';
import Editor from '@monaco-editor/react';
import { Play, Sun, Moon, ChevronDown, Check, Terminal } from 'lucide-react';
import { useRunCode } from '../hooks/useRunCode';
import type { Language } from '../types';
import './playground.css';

type Theme = 'dark' | 'light';

/* ── Language config ─────────────────────────────── */

const LANGUAGES = [
    {
        id:    'php' as Language,
        label: 'PHP',
        desc:  'Server-side scripting',
        color: '#a855f7',
    },
    {
        id:    'javascript' as Language,
        label: 'JavaScript',
        desc:  'Node.js runtime',
        color: '#f7df1e',
    },
];

/* ── Inline SVG icons ────────────────────────────── */

function PhpIcon({ size = 22 }: { size?: number }) {
    return (
        <svg width={size} height={size} viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect width="28" height="28" rx="6" fill="#a855f7" />
            <text x="14" y="19" textAnchor="middle" fill="white"
                fontSize="10" fontWeight="700"
                fontFamily="'JetBrains Mono','Fira Code',monospace">
                php
            </text>
        </svg>
    );
}

function JsIcon({ size = 22 }: { size?: number }) {
    return (
        <svg width={size} height={size} viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect width="28" height="28" rx="6" fill="#f7df1e" />
            <text x="14" y="20" textAnchor="middle" fill="#1a1200"
                fontSize="11" fontWeight="800"
                fontFamily="'JetBrains Mono','Fira Code',monospace">
                JS
            </text>
        </svg>
    );
}

const LANG_ICONS: Record<Language, (size?: number) => JSX.Element> = {
    php:        (s) => <PhpIcon size={s} />,
    javascript: (s) => <JsIcon size={s} />,
};

/* ── Starter snippets ────────────────────────────── */

const STARTER: Record<Language, string> = {
    php: `<?php

$name = "World";
echo "Hello, {$name}!" . PHP_EOL;

// Arrays & loops
$fruits = ["Apple", "Banana", "Cherry"];
foreach ($fruits as $i => $fruit) {
    echo ($i + 1) . ". {$fruit}" . PHP_EOL;
}

// Functions
function factorial(int $n): int {
    return $n <= 1 ? 1 : $n * factorial($n - 1);
}

echo "5! = " . factorial(5) . PHP_EOL;
`,
    javascript: `const name = "World";
console.log(\`Hello, \${name}!\`);

// Arrays & loops
const fruits = ["Apple", "Banana", "Cherry"];
fruits.forEach((fruit, i) => console.log(\`\${i + 1}. \${fruit}\`));

// Functions
const factorial = (n) => n <= 1 ? 1 : n * factorial(n - 1);
console.log(\`5! = \${factorial(5)}\`);
`,
};

/* ── Editor options ──────────────────────────────── */

const EDITOR_OPTIONS = {
    minimap:              { enabled: false },
    fontSize:             13.5,
    lineHeight:           22,
    fontFamily:           "'JetBrains Mono', 'Fira Code', 'Cascadia Code', Menlo, monospace",
    fontLigatures:        true,
    scrollBeyondLastLine: false,
    automaticLayout:      true,
    padding:              { top: 20, bottom: 20 },
    renderLineHighlight:  'line' as const,
    cursorBlinking:       'smooth' as const,
    smoothScrolling:      true,
    wordWrap:             'on' as const,
    bracketPairColorization: { enabled: true },
};

/* ── Language dropdown ───────────────────────────── */

interface LangDropdownProps {
    value:    Language;
    onChange: (lang: Language) => void;
}

function LangDropdown({ value, onChange }: LangDropdownProps) {
    const [open, setOpen] = useState(false);
    const ref             = useRef<HTMLDivElement>(null);
    const selected        = LANGUAGES.find(l => l.id === value)!;

    useEffect(() => {
        if (!open) return;
        const close = (e: MouseEvent) => {
            if (ref.current && !ref.current.contains(e.target as Node)) setOpen(false);
        };
        document.addEventListener('mousedown', close);
        return () => document.removeEventListener('mousedown', close);
    }, [open]);

    const pick = (lang: Language) => { onChange(lang); setOpen(false); };

    return (
        <div className={`pg-lang-wrap${open ? ' pg-lang-wrap--open' : ''}`} ref={ref}
            style={{ '--lang-color': selected.color } as React.CSSProperties}>

            <button
                type="button"
                className="pg-lang-trigger"
                onClick={() => setOpen(o => !o)}
                aria-haspopup="listbox"
                aria-expanded={open}
            >
                <span className="pg-lang-trigger-icon">
                    {LANG_ICONS[value](18)}
                </span>
                <span className="pg-lang-trigger-name">{selected.label}</span>
                <ChevronDown size={13} className={`pg-lang-chevron${open ? ' open' : ''}`} />
            </button>

            {open && (
                <div className="pg-lang-menu" role="listbox">
                    {LANGUAGES.map(lang => (
                        <button
                            key={lang.id}
                            type="button"
                            role="option"
                            aria-selected={lang.id === value}
                            className={`pg-lang-option${lang.id === value ? ' pg-lang-option--selected' : ''}`}
                            style={{ '--option-color': lang.color } as React.CSSProperties}
                            onClick={() => pick(lang.id)}
                        >
                            <span className="pg-lang-option-icon">
                                {LANG_ICONS[lang.id](26)}
                            </span>
                            <span className="pg-lang-option-text">
                                <span className="pg-lang-option-name">{lang.label}</span>
                                <span className="pg-lang-option-desc">{lang.desc}</span>
                            </span>
                            {lang.id === value && (
                                <Check size={13} className="pg-lang-option-check" />
                            )}
                        </button>
                    ))}
                </div>
            )}
        </div>
    );
}

/* ── Main page ───────────────────────────────────── */

export default function PlaygroundPage() {
    const [language, setLanguage] = useState<Language>('php');
    const [code, setCode]         = useState(STARTER.php);
    const [output, setOutput]     = useState<string | null>(null);
    const [exitCode, setExitCode] = useState<number | null>(null);
    const [theme, setTheme]       = useState<Theme>('dark');

    const { mutate: runCode, isPending } = useRunCode();

    const handleLanguageChange = (lang: Language) => {
        setLanguage(lang);
        setCode(STARTER[lang]);
        setOutput(null);
        setExitCode(null);
    };

    const handleRun = useCallback(() => {
        if (isPending) return;
        runCode(
            { language, code },
            {
                onSuccess: (result) => { setOutput(result.output); setExitCode(result.exit_code); },
                onError:   ()       => { setOutput('Failed to connect to the execution engine.'); setExitCode(1); },
            },
        );
    }, [isPending, runCode, language, code]);

    useEffect(() => {
        const handler = (e: KeyboardEvent) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') { e.preventDefault(); handleRun(); }
        };
        window.addEventListener('keydown', handler);
        return () => window.removeEventListener('keydown', handler);
    }, [handleRun]);

    const isError = exitCode !== null && exitCode !== 0;

    return (
        <div className={`playground${theme === 'light' ? ' pg--light' : ''}`}>

            {/* Top bar */}
            <div className="pg-topbar">
                <LangDropdown value={language} onChange={handleLanguageChange} />

                <div className="pg-topbar-right">
                    <button
                        type="button"
                        className="pg-icon-btn"
                        title={theme === 'dark' ? 'Light mode' : 'Dark mode'}
                        onClick={() => setTheme(t => t === 'dark' ? 'light' : 'dark')}
                    >
                        {theme === 'dark' ? <Sun size={14} /> : <Moon size={14} />}
                    </button>

                    <button
                        type="button"
                        className="pg-run-btn"
                        onClick={handleRun}
                        disabled={isPending}
                    >
                        <Play size={12} strokeWidth={2.5} />
                        {isPending ? 'Running…' : 'Run'}
                        <kbd className="pg-kbd">Ctrl+↵</kbd>
                    </button>
                </div>
            </div>

            {/* Editor + Output */}
            <div className="pg-body">

                <div className="pg-editor-panel">
                    <div className="pg-editor-wrap">
                        <Editor
                            height="100%"
                            language={language === 'javascript' ? 'javascript' : 'php'}
                            value={code}
                            onChange={(v) => setCode(v ?? '')}
                            theme={theme === 'dark' ? 'vs-dark' : 'vs'}
                            options={EDITOR_OPTIONS}
                        />
                    </div>
                </div>

                <div className="pg-output-panel">
                    <div className="pg-output-header">
                        <Terminal size={12} className="pg-output-header-icon" />
                        <span className="pg-output-label">Output</span>
                        <div className="pg-output-header-right">
                            {exitCode !== null && (
                                <span className={`pg-exit-badge ${isError ? 'error' : 'ok'}`}>
                                    exit {exitCode}
                                </span>
                            )}
                            {output !== null && (
                                <button type="button" className="pg-clear-btn"
                                    onClick={() => { setOutput(null); setExitCode(null); }}>
                                    Clear
                                </button>
                            )}
                        </div>
                    </div>

                    <div className="pg-output-body">
                        {isPending && (
                            <div className="pg-output-running">
                                <span className="pg-spinner" />
                                Executing…
                            </div>
                        )}
                        {!isPending && output !== null && (
                            <pre className={`pg-output-pre${isError ? ' is-error' : ''}`}>{output}</pre>
                        )}
                        {!isPending && output === null && (
                            <div className="pg-output-empty">
                                Press <strong>Run</strong> or <kbd className="pg-kbd pg-kbd--inline">Ctrl+↵</kbd> to execute
                            </div>
                        )}
                    </div>
                </div>

            </div>
        </div>
    );
}
