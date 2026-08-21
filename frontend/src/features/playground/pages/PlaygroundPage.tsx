import { useState, useCallback, useEffect, useRef, useMemo } from 'react';
import type { ReactElement } from 'react';
import Editor from '@monaco-editor/react';
import type { BeforeMount, OnMount } from '@monaco-editor/react';
import {
    Play, Square, Sun, Moon, ChevronDown, ChevronRight, Check,
    Terminal, Settings2, Database, RefreshCw, Copy, RotateCcw,
    Keyboard, Clock, X, Swords, FlaskConical,
} from 'lucide-react';
import { useRunCode }           from '../hooks/useRunCode';
import { usePlaygroundSchema }  from '../hooks/usePlaygroundSchema';
import { useResetPlayground }   from '../hooks/useResetPlayground';
import { BattlegroundMode }     from '../components/BattlegroundMode';
import type { Language, SchemaResult } from '../types';
import './playground.css';

/* ── Constants ───────────────────────────────────── */

const SCHEMA_W    = 240;  // px — fixed schema sidebar width
const HANDLE_W    = 5;    // px — drag-handle width
const MAX_HISTORY = 5;

/* ── Types ───────────────────────────────────────── */

type Theme = 'dark' | 'light';

interface EditorSettings {
    inlineSuggestions: boolean;
    quickSuggestions:  boolean;
    minimap:           boolean;
    wordWrap:          boolean;
    fontSize:          number;
}

interface HistoryEntry {
    code: string;
    ts:   number;
}

/* ── Helpers ─────────────────────────────────────── */

function relativeTime(ts: number): string {
    const diff = Date.now() - ts;
    if (diff < 60_000)      return 'just now';
    if (diff < 3_600_000)   return `${Math.floor(diff / 60_000)}m ago`;
    if (diff < 86_400_000)  return `${Math.floor(diff / 3_600_000)}h ago`;
    return `${Math.floor(diff / 86_400_000)}d ago`;
}

function loadHistory(lang: Language): HistoryEntry[] {
    try {
        const s = localStorage.getItem(`pg:history:${lang}`);
        return s ? (JSON.parse(s) as HistoryEntry[]) : [];
    } catch { return []; }
}

/* ── Language config ─────────────────────────────── */

const LANGUAGES = [
    { id: 'php'        as Language, label: 'PHP',        desc: 'Server-side scripting',   color: '#a855f7' },
    { id: 'javascript' as Language, label: 'JavaScript', desc: 'Node.js runtime',          color: '#f7df1e' },
    { id: 'python'     as Language, label: 'Python',     desc: 'Python 3 interpreter',     color: '#3776ab' },
    { id: 'mysql'      as Language, label: 'MySQL',      desc: 'SQL on sample database',   color: '#f29111' },
];

/* ── Inline SVG icons ────────────────────────────── */

function PhpIcon({ size = 22 }: { size?: number }) {
    return (
        <svg width={size} height={size} viewBox="0 0 28 28" fill="none">
            <rect width="28" height="28" rx="6" fill="#a855f7" />
            <text x="14" y="19" textAnchor="middle" fill="white"
                fontSize="10" fontWeight="700" fontFamily="'JetBrains Mono',monospace">php</text>
        </svg>
    );
}
function JsIcon({ size = 22 }: { size?: number }) {
    return (
        <svg width={size} height={size} viewBox="0 0 28 28" fill="none">
            <rect width="28" height="28" rx="6" fill="#f7df1e" />
            <text x="14" y="20" textAnchor="middle" fill="#1a1200"
                fontSize="11" fontWeight="800" fontFamily="'JetBrains Mono',monospace">JS</text>
        </svg>
    );
}
function PyIcon({ size = 22 }: { size?: number }) {
    return (
        <svg width={size} height={size} viewBox="0 0 28 28" fill="none">
            <rect width="28" height="28" rx="6" fill="#3776ab" />
            <text x="14" y="20" textAnchor="middle" fill="white"
                fontSize="10" fontWeight="800" fontFamily="'JetBrains Mono',monospace">py</text>
        </svg>
    );
}
function SqlIcon({ size = 22 }: { size?: number }) {
    return (
        <svg width={size} height={size} viewBox="0 0 28 28" fill="none">
            <rect width="28" height="28" rx="6" fill="#f29111" />
            <text x="14" y="20" textAnchor="middle" fill="white"
                fontSize="9" fontWeight="800" fontFamily="'JetBrains Mono',monospace">SQL</text>
        </svg>
    );
}

const LANG_ICONS: Record<Language, (size?: number) => ReactElement> = {
    php:        (s) => <PhpIcon size={s} />,
    javascript: (s) => <JsIcon size={s} />,
    python:     (s) => <PyIcon size={s} />,
    mysql:      (s) => <SqlIcon size={s} />,
};

/* ── Starter snippets ────────────────────────────── */

const STARTER: Record<Language, string> = {
    php: `<?php

$name = "World";
echo "Hello, {$name}!" . PHP_EOL;

$fruits = ["Apple", "Banana", "Cherry"];
foreach ($fruits as $i => $fruit) {
    echo ($i + 1) . ". {$fruit}" . PHP_EOL;
}

function factorial(int $n): int {
    return $n <= 1 ? 1 : $n * factorial($n - 1);
}

echo "5! = " . factorial(5) . PHP_EOL;
`,
    javascript: `const name = "World";
console.log(\`Hello, \${name}!\`);

const fruits = ["Apple", "Banana", "Cherry"];
fruits.forEach((fruit, i) => console.log(\`\${i + 1}. \${fruit}\`));

const factorial = (n) => n <= 1 ? 1 : n * factorial(n - 1);
console.log(\`5! = \${factorial(5)}\`);
`,
    python: `name = "World"
print(f"Hello, {name}!")

fruits = ["Apple", "Banana", "Cherry"]
for i, fruit in enumerate(fruits):
    print(f"{i + 1}. {fruit}")

def factorial(n):
    return 1 if n <= 1 else n * factorial(n - 1)

print(f"5! = {factorial(5)}")
`,
    mysql: `-- Tables: employees, departments, orders, products
-- DML is rolled back after each run — sample data always stays fresh.

-- Top earners with their department
SELECT e.name, d.name AS department, e.salary
FROM employees e
JOIN departments d ON e.department_id = d.id
ORDER BY e.salary DESC
LIMIT 5;

-- Average salary per department
SELECT d.name AS department,
       ROUND(AVG(e.salary), 2) AS avg_salary,
       COUNT(*)                 AS headcount
FROM employees e
JOIN departments d ON e.department_id = d.id
GROUP BY d.id, d.name
ORDER BY avg_salary DESC;
`,
};

/* ── PHP completion data ─────────────────────────── */

const PHP_KEYWORDS = [
    'echo', 'print', 'if', 'else', 'elseif', 'while', 'for', 'foreach', 'do',
    'switch', 'case', 'default', 'break', 'continue', 'return', 'function',
    'class', 'new', 'null', 'true', 'false', 'array', 'match', 'fn', 'yield',
    'public', 'private', 'protected', 'static', 'abstract', 'final', 'readonly',
    'interface', 'extends', 'implements', 'namespace', 'use', 'trait',
    'try', 'catch', 'finally', 'throw', 'isset', 'empty', 'unset', 'list',
    'include', 'require', 'include_once', 'require_once',
    'PHP_EOL', 'PHP_INT_MAX', 'PHP_INT_MIN', 'PHP_VERSION', 'PHP_OS',
];
const PHP_FUNCTIONS = [
    'var_dump', 'print_r', 'var_export',
    'strlen', 'substr', 'str_replace', 'str_pad', 'str_repeat',
    'strtolower', 'strtoupper', 'ucfirst', 'lcfirst', 'ucwords',
    'trim', 'ltrim', 'rtrim', 'nl2br', 'wordwrap',
    'strpos', 'strrpos', 'str_contains', 'str_starts_with', 'str_ends_with',
    'explode', 'implode', 'str_split', 'sprintf', 'printf',
    'number_format', 'round', 'floor', 'ceil', 'abs', 'max', 'min',
    'pow', 'sqrt', 'log', 'rand', 'mt_rand',
    'count', 'sizeof', 'range',
    'array_push', 'array_pop', 'array_shift', 'array_unshift',
    'array_merge', 'array_map', 'array_filter', 'array_reduce',
    'array_keys', 'array_values', 'array_flip', 'array_combine',
    'in_array', 'array_search', 'array_key_exists',
    'sort', 'rsort', 'asort', 'arsort', 'ksort', 'krsort', 'usort',
    'array_reverse', 'array_unique', 'array_slice', 'array_splice', 'array_chunk',
    'intval', 'floatval', 'strval', 'boolval', 'gettype',
    'is_int', 'is_float', 'is_string', 'is_array', 'is_object', 'is_null', 'is_bool',
    'date', 'time', 'mktime', 'strtotime', 'microtime',
    'json_encode', 'json_decode',
    'file_get_contents', 'file_put_contents', 'file_exists', 'unlink',
    'preg_match', 'preg_match_all', 'preg_replace', 'preg_split',
    'header', 'ob_start', 'ob_get_clean', 'die', 'exit',
    'compact', 'extract', 'call_user_func', 'call_user_func_array',
];

/* ── Python completion data ──────────────────────── */

const PYTHON_KEYWORDS = [
    'False', 'None', 'True', 'and', 'as', 'assert', 'async', 'await',
    'break', 'class', 'continue', 'def', 'del', 'elif', 'else', 'except',
    'finally', 'for', 'from', 'global', 'if', 'import', 'in', 'is',
    'lambda', 'nonlocal', 'not', 'or', 'pass', 'raise', 'return',
    'try', 'while', 'with', 'yield',
];
const PYTHON_BUILTINS = [
    'print', 'input', 'len', 'range', 'type', 'int', 'str', 'float',
    'list', 'dict', 'tuple', 'set', 'frozenset', 'bool', 'bytes',
    'abs', 'max', 'min', 'sum', 'round', 'pow', 'divmod',
    'sorted', 'reversed', 'enumerate', 'zip', 'map', 'filter',
    'any', 'all', 'next', 'iter',
    'open', 'repr', 'format', 'hash', 'id',
    'isinstance', 'issubclass', 'callable', 'hasattr', 'getattr', 'setattr', 'delattr',
    'dir', 'vars', 'help', 'chr', 'ord', 'hex', 'oct', 'bin',
    'staticmethod', 'classmethod', 'property', 'super',
    'Exception', 'ValueError', 'TypeError', 'KeyError', 'IndexError',
    'AttributeError', 'ImportError', 'OSError', 'RuntimeError',
];

/* ── SQL completion data ─────────────────────────── */

const SQL_KEYWORDS = [
    'SELECT', 'FROM', 'WHERE', 'JOIN', 'LEFT JOIN', 'RIGHT JOIN', 'INNER JOIN',
    'ON', 'AS', 'GROUP BY', 'ORDER BY', 'HAVING', 'LIMIT', 'OFFSET',
    'INSERT INTO', 'VALUES', 'UPDATE', 'SET', 'DELETE FROM',
    'CREATE TABLE', 'DROP TABLE', 'ALTER TABLE', 'ADD COLUMN',
    'PRIMARY KEY', 'FOREIGN KEY', 'REFERENCES', 'UNIQUE', 'NOT NULL', 'DEFAULT',
    'AND', 'OR', 'NOT', 'IN', 'NOT IN', 'BETWEEN', 'LIKE', 'IS NULL', 'IS NOT NULL',
    'DISTINCT', 'COUNT', 'SUM', 'AVG', 'MIN', 'MAX', 'ROUND', 'COALESCE', 'IFNULL',
    'CASE', 'WHEN', 'THEN', 'ELSE', 'END',
    'UNION', 'UNION ALL', 'ASC', 'DESC',
    'SHOW TABLES', 'SHOW DATABASES', 'DESCRIBE',
    'CONCAT', 'LENGTH', 'UPPER', 'LOWER', 'TRIM', 'SUBSTRING',
    'YEAR', 'MONTH', 'DAY', 'NOW', 'CURDATE', 'DATE_FORMAT',
    'FLOOR', 'CEIL', 'ABS', 'MOD',
];
const SQL_TABLES  = ['employees', 'departments', 'orders', 'products'];
const SQL_COLUMNS = [
    'id', 'name', 'salary', 'department_id', 'hire_date', 'manager_id',
    'location', 'customer_name', 'product_id', 'amount', 'status', 'order_date',
    'category', 'price', 'stock',
];

/* ── Monaco provider (module-level, registered once) ─ */

let _registered = false;

const registerProviders: BeforeMount = (monaco) => {
    if (_registered) return;
    _registered = true;

    const K   = monaco.languages.CompletionItemKind;
    const IST = monaco.languages.CompletionItemInsertTextRule.InsertAsSnippet;

    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const range = (model: any, pos: any) => {
        const w = model.getWordUntilPosition(pos);
        return { startLineNumber: pos.lineNumber, endLineNumber: pos.lineNumber, startColumn: w.startColumn, endColumn: w.endColumn };
    };

    monaco.languages.registerCompletionItemProvider('php', {
        triggerCharacters: ['_', '>', ':', '$'],
        // eslint-disable-next-line @typescript-eslint/no-explicit-any
        provideCompletionItems(model: any, pos: any) {
            const r = range(model, pos);
            return { suggestions: [
                ...PHP_KEYWORDS.map(kw => ({ label: kw, kind: K.Keyword,   insertText: kw,            range: r })),
                ...PHP_FUNCTIONS.map(fn => ({ label: fn, kind: K.Function, insertText: `${fn}($1)`,   insertTextRules: IST, range: r, detail: 'PHP built-in' })),
            ]};
        },
    });

    monaco.languages.registerCompletionItemProvider('python', {
        triggerCharacters: ['_', '.', ' '],
        // eslint-disable-next-line @typescript-eslint/no-explicit-any
        provideCompletionItems(model: any, pos: any) {
            const r = range(model, pos);
            return { suggestions: [
                ...PYTHON_KEYWORDS.map(kw => ({ label: kw, kind: K.Keyword,   insertText: kw,            range: r })),
                ...PYTHON_BUILTINS.map(fn => ({ label: fn, kind: K.Function, insertText: `${fn}($1)`,   insertTextRules: IST, range: r, detail: 'Python built-in' })),
            ]};
        },
    });

    monaco.languages.registerCompletionItemProvider('sql', {
        triggerCharacters: [' ', '.', '\n'],
        // eslint-disable-next-line @typescript-eslint/no-explicit-any
        provideCompletionItems(model: any, pos: any) {
            const r = range(model, pos);
            return { suggestions: [
                ...SQL_KEYWORDS.map(kw => ({ label: kw, kind: K.Keyword, insertText: kw, range: r })),
                ...SQL_TABLES.map(t   => ({ label: t,  kind: K.Class,   insertText: t,  range: r, detail: 'table' })),
                ...SQL_COLUMNS.map(c  => ({ label: c,  kind: K.Field,   insertText: c,  range: r, detail: 'column' })),
            ]};
        },
    });
};

/* ════════════════════════════════════════════════════
   SUB-COMPONENTS
════════════════════════════════════════════════════ */

/* ── Language dropdown ───────────────────────────── */

function LangDropdown({ value, onChange }: { value: Language; onChange: (l: Language) => void }) {
    const [open, setOpen] = useState(false);
    const ref             = useRef<HTMLDivElement>(null);
    const selected        = LANGUAGES.find(l => l.id === value)!;

    useEffect(() => {
        if (!open) return;
        const close = (e: MouseEvent) => { if (ref.current && !ref.current.contains(e.target as Node)) setOpen(false); };
        document.addEventListener('mousedown', close);
        return () => document.removeEventListener('mousedown', close);
    }, [open]);

    return (
        <div className={`pg-lang-wrap${open ? ' pg-lang-wrap--open' : ''}`} ref={ref}
            style={{ '--lang-color': selected.color } as React.CSSProperties}>
            <button type="button" className="pg-lang-trigger" onClick={() => setOpen(o => !o)}
                aria-haspopup="listbox" aria-expanded={open}>
                <span className="pg-lang-trigger-icon">{LANG_ICONS[value](18)}</span>
                <span className="pg-lang-trigger-name">{selected.label}</span>
                <ChevronDown size={13} className={`pg-lang-chevron${open ? ' open' : ''}`} />
            </button>
            {open && (
                <div className="pg-lang-menu" role="listbox">
                    {LANGUAGES.map(lang => (
                        <button key={lang.id} type="button" role="option"
                            aria-selected={lang.id === value}
                            className={`pg-lang-option${lang.id === value ? ' pg-lang-option--selected' : ''}`}
                            style={{ '--option-color': lang.color } as React.CSSProperties}
                            onClick={() => { onChange(lang.id); setOpen(false); }}>
                            <span className="pg-lang-option-icon">{LANG_ICONS[lang.id](26)}</span>
                            <span className="pg-lang-option-text">
                                <span className="pg-lang-option-name">{lang.label}</span>
                                <span className="pg-lang-option-desc">{lang.desc}</span>
                            </span>
                            {lang.id === value && <Check size={13} className="pg-lang-option-check" />}
                        </button>
                    ))}
                </div>
            )}
        </div>
    );
}

/* ── Settings panel ──────────────────────────────── */

function ToggleRow({ label, desc, checked, onChange }: {
    label: string; desc: string; checked: boolean; onChange: (v: boolean) => void;
}) {
    return (
        <div className="pg-settings-row" onClick={() => onChange(!checked)}>
            <div className="pg-settings-row-text">
                <span className="pg-settings-row-label">{label}</span>
                <span className="pg-settings-row-desc">{desc}</span>
            </div>
            <div className={`pg-toggle${checked ? ' pg-toggle--on' : ''}`}>
                <div className="pg-toggle-thumb" />
            </div>
        </div>
    );
}

function SettingsPanel({ settings, onChange }: {
    settings: EditorSettings; onChange: (p: Partial<EditorSettings>) => void;
}) {
    const [open, setOpen] = useState(false);
    const ref             = useRef<HTMLDivElement>(null);

    useEffect(() => {
        if (!open) return;
        const close = (e: MouseEvent) => { if (ref.current && !ref.current.contains(e.target as Node)) setOpen(false); };
        document.addEventListener('mousedown', close);
        return () => document.removeEventListener('mousedown', close);
    }, [open]);

    return (
        <div className={`pg-settings-wrap${open ? ' pg-settings-wrap--open' : ''}`} ref={ref}>
            <button type="button" className={`pg-icon-btn${open ? ' pg-icon-btn--active' : ''}`}
                title="Editor settings" onClick={() => setOpen(o => !o)}>
                <Settings2 size={14} />
            </button>
            {open && (
                <div className="pg-settings-menu">
                    <div className="pg-settings-header">Editor Settings</div>
                    <div className="pg-settings-section">
                        <ToggleRow label="Inline Suggestions" desc="Ghost-text completions as you type"
                            checked={settings.inlineSuggestions} onChange={v => onChange({ inlineSuggestions: v })} />
                        <ToggleRow label="Quick Suggestions" desc="Auto-complete dropdown while typing"
                            checked={settings.quickSuggestions} onChange={v => onChange({ quickSuggestions: v })} />
                        <ToggleRow label="Minimap" desc="Code overview scrollbar on the right"
                            checked={settings.minimap} onChange={v => onChange({ minimap: v })} />
                        <ToggleRow label="Word Wrap" desc="Wrap long lines instead of scrolling"
                            checked={settings.wordWrap} onChange={v => onChange({ wordWrap: v })} />
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

/* ── Shortcuts popover ───────────────────────────── */

function ShortcutsPopover({ open, onToggle }: { open: boolean; onToggle: () => void }) {
    const ref = useRef<HTMLDivElement>(null);

    useEffect(() => {
        if (!open) return;
        const close = (e: MouseEvent) => { if (ref.current && !ref.current.contains(e.target as Node)) onToggle(); };
        document.addEventListener('mousedown', close);
        return () => document.removeEventListener('mousedown', close);
    }, [open, onToggle]);

    return (
        <div className="pg-shortcuts-wrap" ref={ref}>
            <button type="button" className={`pg-icon-btn${open ? ' pg-icon-btn--active' : ''}`}
                title="Keyboard shortcuts" onClick={onToggle}>
                <Keyboard size={14} />
            </button>
            {open && (
                <div className="pg-shortcuts-menu">
                    <div className="pg-shortcuts-header">Shortcuts</div>
                    <div className="pg-shortcuts-list">
                        <div className="pg-shortcut-row">
                            <span>Run code</span>
                            <span className="pg-shortcut-keys"><kbd>F5</kbd><kbd>Ctrl+↵</kbd></span>
                        </div>
                        <div className="pg-shortcut-row">
                            <span>Stop execution</span>
                            <span className="pg-shortcut-keys"><kbd>Esc</kbd></span>
                        </div>
                        <div className="pg-shortcut-row">
                            <span>Toggle stdin</span>
                            <span className="pg-shortcut-keys"><kbd>Ctrl+Shift+I</kbd></span>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}

/* ── History dropdown ────────────────────────────── */

function HistoryDropdown({ history, open, onToggle, onRestore }: {
    history:   HistoryEntry[];
    open:      boolean;
    onToggle:  () => void;
    onRestore: (e: HistoryEntry) => void;
}) {
    const ref = useRef<HTMLDivElement>(null);

    useEffect(() => {
        if (!open) return;
        const close = (e: MouseEvent) => { if (ref.current && !ref.current.contains(e.target as Node)) onToggle(); };
        document.addEventListener('mousedown', close);
        return () => document.removeEventListener('mousedown', close);
    }, [open, onToggle]);

    return (
        <div className="pg-history-wrap" ref={ref}>
            <button type="button"
                className={`pg-icon-btn${open ? ' pg-icon-btn--active' : ''}${history.length === 0 ? ' pg-icon-btn--disabled' : ''}`}
                title={history.length ? 'Run history' : 'No history yet'}
                onClick={() => history.length && onToggle()}>
                <Clock size={14} />
            </button>
            {open && history.length > 0 && (
                <div className="pg-history-menu">
                    <div className="pg-history-header">Recent Runs</div>
                    {history.map((entry, i) => (
                        <button key={i} type="button" className="pg-history-item"
                            onClick={() => onRestore(entry)}>
                            <span className="pg-history-code">
                                {entry.code.trim().split('\n').find(l => l.trim() && !l.trim().startsWith('--') && !l.trim().startsWith('#'))?.trim().slice(0, 45) ?? entry.code.trim().slice(0, 45)}
                            </span>
                            <span className="pg-history-time">{relativeTime(entry.ts)}</span>
                        </button>
                    ))}
                </div>
            )}
        </div>
    );
}

/* ── Schema panel (MySQL only) ───────────────────── */

function SchemaPanel({ schema, isLoading, onReset, isResetting }: {
    schema:      SchemaResult | undefined;
    isLoading:   boolean;
    onReset:     () => void;
    isResetting: boolean;
}) {
    const [openTables, setOpenTables] = useState<Set<string>>(new Set(['employees']));

    const toggle = (name: string) =>
        setOpenTables(prev => { const n = new Set(prev); n.has(name) ? n.delete(name) : n.add(name); return n; });

    return (
        <div className="pg-schema">
            <div className="pg-schema-head">
                <Database size={12} />
                <span>Schema</span>
                <button type="button" className="pg-schema-reset-btn"
                    onClick={onReset} disabled={isResetting}
                    title="Reset sample data to defaults">
                    <RefreshCw size={11} className={isResetting ? 'pg-spin-icon' : ''} />
                </button>
            </div>

            <div className="pg-schema-body">
                {isLoading && [1, 2, 3, 4].map(i => (
                    <div key={i} className="pg-schema-skeleton" />
                ))}

                {!isLoading && schema?.tables.map(table => (
                    <div key={table.name} className="pg-schema-tbl">
                        <button type="button" className="pg-schema-tbl-btn" onClick={() => toggle(table.name)}>
                            <ChevronRight size={12}
                                className={`pg-schema-chevron${openTables.has(table.name) ? ' open' : ''}`} />
                            <span className="pg-schema-tbl-name">{table.name}</span>
                            <span className="pg-schema-col-count">{table.columns.length}</span>
                        </button>
                        {openTables.has(table.name) && (
                            <div className="pg-schema-cols">
                                {table.columns.map(col => (
                                    <div key={col.name} className="pg-schema-col">
                                        <span className="pg-schema-col-name">
                                            {col.key === 'PRI' && <span className="pg-pk-badge">PK</span>}
                                            {col.name}
                                        </span>
                                        <span className="pg-schema-col-type">{col.type}</span>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                ))}

                {!isLoading && !schema && (
                    <p className="pg-schema-empty">Connect to MySQL to load schema.</p>
                )}
            </div>

            <div className="pg-schema-foot">careeros_playground</div>
        </div>
    );
}

/* ════════════════════════════════════════════════════
   MAIN PAGE
════════════════════════════════════════════════════ */

export default function PlaygroundPage() {

    /* ── Mode toggle ─────────────────────────────── */

    const [playgroundMode, setPlaygroundMode] = useState<'free' | 'battleground'>(() => {
        return (localStorage.getItem('pg:mode') as 'free' | 'battleground' | null) ?? 'free';
    });

    const handleModeChange = (mode: 'free' | 'battleground') => {
        setPlaygroundMode(mode);
        localStorage.setItem('pg:mode', mode);
    };

    /* ── Persisted state ─────────────────────────── */

    const [language, setLanguage] = useState<Language>(() => {
        const s = localStorage.getItem('pg:language');
        return (s === 'php' || s === 'javascript' || s === 'python' || s === 'mysql') ? s : 'php';
    });

    const [code, setCode] = useState<string>(() => {
        const lang = (localStorage.getItem('pg:language') as Language | null) ?? 'php';
        return localStorage.getItem(`pg:code:${lang}`) ?? STARTER[lang];
    });

    const [settings, setSettings] = useState<EditorSettings>(() => {
        const defaults: EditorSettings = { inlineSuggestions: true, quickSuggestions: true, minimap: false, wordWrap: true, fontSize: 13 };
        try {
            const s = localStorage.getItem('pg:settings');
            return s ? { ...defaults, ...JSON.parse(s) } : defaults;
        } catch { return defaults; }
    });

    const [splitPct, setSplitPct] = useState<number>(() => {
        const s = localStorage.getItem('pg:split');
        return s ? Math.max(25, Math.min(75, Number(s))) : 55;
    });

    const [theme, setTheme] = useState<Theme>(() => {
        const s = localStorage.getItem('pg:theme');
        return (s === 'dark' || s === 'light') ? s : 'dark';
    });

    /* ── Transient state ─────────────────────────── */

    const [output,   setOutput]   = useState<string | null>(null);
    const [exitCode, setExitCode] = useState<number | null>(null);
    const [stdin,    setStdin]    = useState('');
    const [stdinOpen, setStdinOpen] = useState(false);
    const [copied,   setCopied]   = useState(false);
    const [shortcutsOpen, setShortcutsOpen] = useState(false);
    const [historyOpen,   setHistoryOpen]   = useState(false);
    const [history, setHistory] = useState<HistoryEntry[]>(() => loadHistory(
        (localStorage.getItem('pg:language') as Language | null) ?? 'php'
    ));

    /* ── Refs ────────────────────────────────────── */

    const abortRef   = useRef<AbortController | null>(null);
    const editorRef  = useRef<Parameters<OnMount>[0] | null>(null);
    const bodyRef    = useRef<HTMLDivElement>(null);
    const dragging   = useRef(false);
    const codeSaveTimer = useRef<ReturnType<typeof setTimeout> | null>(null);

    /* ── Data hooks ──────────────────────────────── */

    const { mutate: runCode, isPending }                   = useRunCode();
    const { data: schema, isLoading: schemaLoading }       = usePlaygroundSchema(language);
    const { mutate: resetPlayground, isPending: isResetting } = useResetPlayground();

    /* ── Persist settings ────────────────────────── */

    useEffect(() => { localStorage.setItem('pg:language', language); }, [language]);
    useEffect(() => { localStorage.setItem('pg:settings', JSON.stringify(settings)); }, [settings]);
    useEffect(() => { localStorage.setItem('pg:theme', theme); }, [theme]);

    /* ── Sync editor options on setting change ───── */

    const handleEditorMount: OnMount = useCallback((editor) => {
        editorRef.current = editor;
    }, []);

    useEffect(() => {
        editorRef.current?.updateOptions({
            quickSuggestions: settings.quickSuggestions,
            inlineSuggest:    { enabled: settings.inlineSuggestions },
            suggest:          { preview: settings.inlineSuggestions },
        });
    }, [settings.quickSuggestions, settings.inlineSuggestions]);

    const editorOptions = useMemo(() => ({
        fontSize:                settings.fontSize,
        lineHeight:              Math.round(settings.fontSize * 1.65),
        fontFamily:              "'JetBrains Mono', 'Fira Code', 'Cascadia Code', Menlo, monospace",
        fontLigatures:           true,
        minimap:                 { enabled: settings.minimap },
        wordWrap:                (settings.wordWrap ? 'on' : 'off') as 'on' | 'off',
        scrollBeyondLastLine:    false,
        automaticLayout:         true,
        padding:                 { top: 20, bottom: 20 },
        renderLineHighlight:     'line' as const,
        cursorBlinking:          'smooth' as const,
        smoothScrolling:         true,
        bracketPairColorization: { enabled: true },
        quickSuggestions:        settings.quickSuggestions,
        wordBasedSuggestions:    'allDocuments' as const,
        inlineSuggest:           { enabled: settings.inlineSuggestions },
        suggest:                 { preview: settings.inlineSuggestions },
        suggestOnTriggerCharacters: true,
        acceptSuggestionOnEnter: 'on' as const,
    }), [settings]);

    /* ── Reset stdin + history when language changes  */

    useEffect(() => {
        setStdin('');
        setStdinOpen(false);
        setHistory(loadHistory(language));
    }, [language]);

    /* ── Drag-to-resize ──────────────────────────── */

    useEffect(() => {
        const onMove = (e: MouseEvent) => {
            if (!dragging.current || !bodyRef.current) return;
            const rect    = bodyRef.current.getBoundingClientRect();
            const leftUsed = language === 'mysql' ? SCHEMA_W + HANDLE_W : 0;
            const usableW  = rect.width - leftUsed - HANDLE_W;
            const mouseX   = e.clientX - rect.left - leftUsed;
            const pct      = Math.max(25, Math.min(75, Math.round((mouseX / usableW) * 100)));
            setSplitPct(pct);
            localStorage.setItem('pg:split', String(pct));
        };
        const onUp = () => { dragging.current = false; };
        document.addEventListener('mousemove', onMove);
        document.addEventListener('mouseup', onUp);
        return () => { document.removeEventListener('mousemove', onMove); document.removeEventListener('mouseup', onUp); };
    }, [language]);

    /* ── Handlers ────────────────────────────────── */

    const handleCodeChange = useCallback((value: string) => {
        setCode(value);
        if (codeSaveTimer.current) clearTimeout(codeSaveTimer.current);
        codeSaveTimer.current = setTimeout(() => {
            localStorage.setItem(`pg:code:${language}`, value);
        }, 600);
    }, [language]);

    const handleLanguageChange = (lang: Language) => {
        localStorage.setItem(`pg:code:${language}`, code);
        const saved = localStorage.getItem(`pg:code:${lang}`);
        setLanguage(lang);
        setCode(saved ?? STARTER[lang]);
        setOutput(null);
        setExitCode(null);
    };

    const handleResetStarter = useCallback(() => {
        setCode(STARTER[language]);
        localStorage.setItem(`pg:code:${language}`, STARTER[language]);
        setOutput(null);
        setExitCode(null);
    }, [language]);

    const handleCopy = useCallback(() => {
        if (!output) return;
        navigator.clipboard.writeText(output).then(() => {
            setCopied(true);
            setTimeout(() => setCopied(false), 1500);
        }).catch(() => {});
    }, [output]);

    const handleRestoreHistory = useCallback((entry: HistoryEntry) => {
        setCode(entry.code);
        localStorage.setItem(`pg:code:${language}`, entry.code);
        setHistoryOpen(false);
        setOutput(null);
        setExitCode(null);
    }, [language]);

    const startDrag = useCallback((e: React.MouseEvent) => {
        dragging.current = true;
        e.preventDefault();
    }, []);

    const handleStop = useCallback(() => { abortRef.current?.abort(); }, []);

    const handleRun = useCallback(() => {
        if (isPending) return;

        // Save code to history before running
        const hist = loadHistory(language);
        const updated = [{ code, ts: Date.now() }, ...hist.filter(e => e.code !== code)].slice(0, MAX_HISTORY);
        localStorage.setItem(`pg:history:${language}`, JSON.stringify(updated));
        setHistory(updated);

        const controller = new AbortController();
        abortRef.current = controller;

        runCode(
            {
                language,
                code,
                stdin: (stdinOpen && language !== 'mysql') ? stdin : undefined,
                signal: controller.signal,
            },
            {
                onSuccess: (result) => { setOutput(result.output); setExitCode(result.exit_code); },
                onError: (err: unknown) => {
                    const isAbort = err instanceof Error && err.name === 'CanceledError';
                    setOutput(isAbort ? 'Execution stopped.' : 'Failed to connect to the execution engine.');
                    setExitCode(isAbort ? 130 : 1);
                },
            },
        );
    }, [isPending, runCode, language, code, stdin, stdinOpen]);

    /* ── Keyboard shortcuts ──────────────────────── */

    useEffect(() => {
        const handler = (e: KeyboardEvent) => {
            if (e.key === 'Escape' && isPending)                         { e.preventDefault(); handleStop(); return; }
            if (e.key === 'F5')                                          { e.preventDefault(); handleRun(); return; }
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter')          { e.preventDefault(); handleRun(); return; }
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'I'
                && language !== 'mysql')                                  { e.preventDefault(); setStdinOpen(o => !o); }
        };
        window.addEventListener('keydown', handler);
        return () => window.removeEventListener('keydown', handler);
    }, [handleRun, handleStop, isPending, language]);

    /* ── Derived ─────────────────────────────────── */

    const isError    = exitCode !== null && exitCode !== 0;
    const monacoLang = language === 'javascript' ? 'javascript'
                     : language === 'python'     ? 'python'
                     : language === 'mysql'      ? 'sql'
                     : 'php';

    /* ── Render ──────────────────────────────────── */

    return (
        <div className={`playground${theme === 'light' ? ' pg--light' : ''}`}>

            {/* ── Mode toggle strip ────────────────── */}
            <div className="pg-mode-strip">
                <button
                    type="button"
                    className={`pg-mode-tab${playgroundMode === 'free' ? ' active' : ''}`}
                    onClick={() => handleModeChange('free')}
                >
                    <FlaskConical size={13} />
                    Free Playground
                </button>
                <button
                    type="button"
                    className={`pg-mode-tab${playgroundMode === 'battleground' ? ' active' : ''}`}
                    onClick={() => handleModeChange('battleground')}
                >
                    <Swords size={13} />
                    Coding Battleground
                </button>
            </div>

            {/* ── Battleground mode ────────────────── */}
            {playgroundMode === 'battleground' ? (
                <BattlegroundMode
                    theme={theme}
                    onThemeToggle={() => setTheme(t => t === 'dark' ? 'light' : 'dark')}
                    settings={settings}
                    onSettingsChange={p => setSettings(s => ({ ...s, ...p }))}
                />
            ) : (
            <>

            {/* ── Top bar ─────────────────────────── */}
            <div className="pg-topbar">
                <div className="pg-topbar-left">
                    <LangDropdown value={language} onChange={handleLanguageChange} />
                    <button type="button" className="pg-icon-btn" title="Reset to starter code"
                        onClick={handleResetStarter}>
                        <RotateCcw size={13} />
                    </button>
                </div>

                <div className="pg-topbar-right">
                    <HistoryDropdown
                        history={history}
                        open={historyOpen}
                        onToggle={() => setHistoryOpen(o => !o)}
                        onRestore={handleRestoreHistory}
                    />

                    {language !== 'mysql' && (
                        <button type="button"
                            className={`pg-icon-btn${stdinOpen ? ' pg-icon-btn--active' : ''}`}
                            title="Standard input (Ctrl+Shift+I)"
                            onClick={() => setStdinOpen(o => !o)}>
                            <Terminal size={13} />
                        </button>
                    )}

                    <ShortcutsPopover open={shortcutsOpen} onToggle={() => setShortcutsOpen(o => !o)} />

                    <div className="pg-topbar-divider" />

                    <SettingsPanel settings={settings} onChange={p => setSettings(s => ({ ...s, ...p }))} />

                    <button type="button" className="pg-icon-btn"
                        title={theme === 'dark' ? 'Light mode' : 'Dark mode'}
                        onClick={() => setTheme(t => t === 'dark' ? 'light' : 'dark')}>
                        {theme === 'dark' ? <Sun size={14} /> : <Moon size={14} />}
                    </button>

                    <div className="pg-topbar-divider" />

                    {isPending && (
                        <button type="button" className="pg-stop-btn" onClick={handleStop} title="Stop (Esc)">
                            <Square size={11} strokeWidth={0} fill="currentColor" />
                            Stop
                        </button>
                    )}

                    <button type="button" className="pg-run-btn" onClick={handleRun} disabled={isPending}>
                        <Play size={12} strokeWidth={2.5} />
                        {isPending ? 'Running…' : 'Run'}
                        <kbd className="pg-kbd">F5</kbd>
                    </button>
                </div>
            </div>

            {/* ── Body ────────────────────────────── */}
            <div className="pg-body" ref={bodyRef}>

                {/* Schema sidebar — MySQL only */}
                {language === 'mysql' && (
                    <>
                        <SchemaPanel
                            schema={schema}
                            isLoading={schemaLoading}
                            onReset={() => resetPlayground()}
                            isResetting={isResetting}
                        />
                        <div className="pg-schema-divider" />
                    </>
                )}

                {/* Editor + optional stdin */}
                <div className="pg-editor-panel" style={{ flex: splitPct }}>
                    <div className="pg-editor-wrap">
                        <Editor
                            height="100%"
                            language={monacoLang}
                            value={code}
                            onChange={v => handleCodeChange(v ?? '')}
                            theme={theme === 'dark' ? 'vs-dark' : 'vs'}
                            options={editorOptions}
                            beforeMount={registerProviders}
                            onMount={handleEditorMount}
                        />
                    </div>

                    {stdinOpen && language !== 'mysql' && (
                        <div className="pg-stdin-panel">
                            <div className="pg-stdin-header">
                                <Terminal size={11} />
                                <span>stdin</span>
                                <button type="button" className="pg-stdin-close"
                                    onClick={() => setStdinOpen(false)}>
                                    <X size={12} />
                                </button>
                            </div>
                            <textarea
                                className="pg-stdin-textarea"
                                value={stdin}
                                onChange={e => setStdin(e.target.value)}
                                placeholder="Type program input here (line-by-line)…"
                                spellCheck={false}
                            />
                        </div>
                    )}
                </div>

                {/* Drag handle */}
                <div className="pg-drag-handle" onMouseDown={startDrag} title="Drag to resize" />

                {/* Output */}
                <div className="pg-output-panel" style={{ flex: 100 - splitPct }}>
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
                                <>
                                    <button type="button" className="pg-clear-btn"
                                        onClick={handleCopy} title="Copy output">
                                        {copied ? '✓ Copied' : <Copy size={11} />}
                                    </button>
                                    <button type="button" className="pg-clear-btn"
                                        onClick={() => { setOutput(null); setExitCode(null); }}>
                                        Clear
                                    </button>
                                </>
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
                                Press <strong>Run</strong>,{' '}
                                <kbd className="pg-kbd pg-kbd--inline">Ctrl+↵</kbd> or{' '}
                                <kbd className="pg-kbd pg-kbd--inline">F5</kbd> to execute
                            </div>
                        )}
                    </div>
                </div>

            </div>
            </>
            )}
        </div>
    );
}
