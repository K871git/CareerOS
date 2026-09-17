/* ── Code detection / formatting / highlighting for MCQ questions ── */

const JS_KEYWORDS = new Set([
    'const','let','var','function','return','if','else','for','while','do',
    'class','new','this','true','false','null','undefined','typeof','instanceof',
    'import','export','default','async','await','try','catch','finally','throw',
    'in','of','switch','case','break','continue','extends','super','static',
    'get','set','from','delete','void','yield','module','require',
]);

const SQL_KEYWORDS = new Set([
    'SELECT','FROM','WHERE','JOIN','INNER','LEFT','RIGHT','OUTER','ON','AND','OR',
    'NOT','IN','INSERT','INTO','VALUES','UPDATE','SET','DELETE','CREATE','TABLE',
    'DROP','ALTER','ADD','COLUMN','INDEX','UNIQUE','PRIMARY','KEY','FOREIGN',
    'REFERENCES','NULL','IS','AS','GROUP','BY','ORDER','HAVING','LIMIT','OFFSET',
    'DISTINCT','COUNT','SUM','AVG','MAX','MIN','BETWEEN','LIKE','EXISTS','ALL',
    'UNION','EXCEPT','INTERSECT','COMMIT','ROLLBACK','BEGIN','TRANSACTION',
]);

const BUILTINS = new Set([
    'console','log','Array','Object','String','Number','Boolean','Math','JSON',
    'Promise','Map','Set','WeakMap','WeakSet','Symbol','Error','TypeError',
    'parseInt','parseFloat','isNaN','isFinite','setTimeout','setInterval',
    'clearTimeout','clearInterval','fetch','document','window','process',
]);

/* ── Code block detection ──────────────────────────────────────────── */

const CODE_TRIGGERS = [
    /\bconst\s+\w/,  /\blet\s+\w/,  /\bvar\s+\w/,
    /\bfunction\s*\(/, /\bclass\s+\w/, /=>/,
    /\bSELECT\b/i,   /\bCREATE\s+TABLE\b/i, /\bINSERT\s+INTO\b/i,
    /console\.\w+/,  /\bfor\s*\(/, /\breturn\s/,
];

export function splitQuestionAndCode(text: string): { prose: string; code: string | null } {
    const trimmed = text.trim();

    // Check if any code trigger exists in the text
    const hasCode = CODE_TRIGGERS.some(p => p.test(trimmed));
    if (!hasCode) return { prose: trimmed, code: null };

    // Find first code trigger position
    let triggerIdx = Infinity;
    for (const pattern of CODE_TRIGGERS) {
        const m = trimmed.match(pattern);
        if (m && m.index !== undefined && m.index < triggerIdx) {
            triggerIdx = m.index;
        }
    }

    // Look for the last sentence boundary (? or :) before the trigger
    const before = trimmed.slice(0, triggerIdx);
    const breaks = [
        before.lastIndexOf('? '),
        before.lastIndexOf(': '),
        before.lastIndexOf('.\n'),
    ].filter(i => i >= 0);

    if (breaks.length === 0) {
        // Entire text is code if no prose boundary found and text starts with code
        if (triggerIdx < 10) return { prose: '', code: trimmed };
        return { prose: trimmed, code: null };
    }

    const splitAt = Math.max(...breaks) + 2;
    return {
        prose: trimmed.slice(0, splitAt).trim(),
        code:  trimmed.slice(splitAt).trim(),
    };
}

/* ── Code formatter — adds indentation ────────────────────────────── */

export function formatCode(raw: string): string {
    let out     = '';
    let indent  = 0;
    let i       = 0;
    const PAD   = '  ';

    const nl = () => '\n' + PAD.repeat(Math.max(0, indent));

    while (i < raw.length) {
        const ch   = raw[i];
        const next = raw[i + 1] ?? '';

        // Skip whitespace (we re-add it ourselves)
        if (ch === ' ' || ch === '\t' || ch === '\n' || ch === '\r') {
            i++;
            continue;
        }

        // String literals — pass through verbatim
        if (ch === '"' || ch === "'" || ch === '`') {
            const q = ch;
            out += ch; i++;
            while (i < raw.length && raw[i] !== q) {
                if (raw[i] === '\\') { out += raw[i]; i++; }
                out += raw[i]; i++;
            }
            out += raw[i] ?? ''; i++;
            continue;
        }

        // Line/block comments
        if (ch === '/' && next === '/') {
            while (i < raw.length && raw[i] !== '\n') { out += raw[i]; i++; }
            out += nl();
            continue;
        }
        if (ch === '/' && next === '*') {
            while (i < raw.length - 1 && !(raw[i] === '*' && raw[i+1] === '/')) { out += raw[i]; i++; }
            out += '*/'; i += 2;
            continue;
        }

        // Opening brace/bracket
        if (ch === '{' || ch === '[' || ch === '(') {
            const peek = raw.slice(i + 1).trimStart();
            // Keep short inline structures on one line (e.g. arrow fns)
            const closing = ch === '{' ? '}' : ch === '[' ? ']' : ')';
            const closePos = findMatchingClose(raw, i);
            const inner = closePos > 0 ? raw.slice(i + 1, closePos).trim() : '';

            if (inner.length <= 40 && !inner.includes('{') && !inner.includes(';')) {
                // short — keep inline
                out += ch;
                if (inner) { out += ' ' + inner + ' '; i = closePos; } else { i++; }
                continue;
            }

            out += ch;
            indent++;
            out += nl();
            i++;
            continue;
        }

        // Closing brace/bracket
        if (ch === '}' || ch === ']' || ch === ')') {
            indent = Math.max(0, indent - 1);
            out  = out.trimEnd();
            out += nl() + ch;
            i++;
            // Semicolon right after closing brace
            if (raw[i] === ';') { out += ';'; i++; }
            out += nl();
            continue;
        }

        // Semicolon → new line
        if (ch === ';') {
            out += ';';
            out += nl();
            i++;
            continue;
        }

        // Comma in object/array — new line
        if (ch === ',') {
            out += ',';
            out += nl();
            i++;
            continue;
        }

        out += ch;
        i++;
    }

    // Collapse multiple blank lines
    return out.replace(/\n{3,}/g, '\n\n').trim();
}

function findMatchingClose(src: string, openIdx: number): number {
    const open  = src[openIdx];
    const close = open === '{' ? '}' : open === '[' ? ']' : ')';
    let depth   = 0;
    for (let i = openIdx; i < src.length; i++) {
        if (src[i] === open)  depth++;
        if (src[i] === close) { depth--; if (depth === 0) return i; }
    }
    return -1;
}

/* ── Syntax tokenizer ──────────────────────────────────────────────── */

export type CodeToken = {
    type: 'keyword' | 'builtin' | 'string' | 'comment' | 'number' | 'operator' | 'plain';
    value: string;
};

export function tokenize(code: string): CodeToken[] {
    const tokens: CodeToken[] = [];
    let i = 0;

    while (i < code.length) {
        // Newlines / whitespace — preserve
        if (code[i] === '\n' || code[i] === ' ' || code[i] === '\t') {
            tokens.push({ type: 'plain', value: code[i] });
            i++; continue;
        }

        // Line comment
        if (code[i] === '/' && code[i+1] === '/') {
            let v = '';
            while (i < code.length && code[i] !== '\n') { v += code[i]; i++; }
            tokens.push({ type: 'comment', value: v }); continue;
        }

        // Block comment
        if (code[i] === '/' && code[i+1] === '*') {
            let v = '';
            while (i < code.length - 1 && !(code[i] === '*' && code[i+1] === '/')) { v += code[i]; i++; }
            v += '*/'; i += 2;
            tokens.push({ type: 'comment', value: v }); continue;
        }

        // String
        if (code[i] === '"' || code[i] === "'" || code[i] === '`') {
            const q = code[i];
            let v   = q; i++;
            while (i < code.length && code[i] !== q) {
                if (code[i] === '\\') { v += code[i]; i++; }
                v += code[i]; i++;
            }
            v += code[i] ?? ''; i++;
            tokens.push({ type: 'string', value: v }); continue;
        }

        // Number
        if (/\d/.test(code[i]) || (code[i] === '.' && /\d/.test(code[i+1] ?? ''))) {
            let v = '';
            while (i < code.length && /[\d.xXa-fA-F_]/.test(code[i])) { v += code[i]; i++; }
            tokens.push({ type: 'number', value: v }); continue;
        }

        // Word (keyword / builtin / identifier)
        if (/[a-zA-Z_$]/.test(code[i])) {
            let v = '';
            while (i < code.length && /[\w$]/.test(code[i])) { v += code[i]; i++; }
            const upper = v.toUpperCase();
            if (JS_KEYWORDS.has(v))       tokens.push({ type: 'keyword', value: v });
            else if (SQL_KEYWORDS.has(upper)) tokens.push({ type: 'keyword', value: v });
            else if (BUILTINS.has(v))     tokens.push({ type: 'builtin', value: v });
            else                          tokens.push({ type: 'plain',   value: v });
            continue;
        }

        // Operators / punctuation
        const op2 = code.slice(i, i + 2);
        if (['===','!==','=>','==','!=','<=','>=','&&','||','??','++','--','+=','-='].includes(code.slice(i, i + 3))) {
            tokens.push({ type: 'operator', value: code.slice(i, i + 3) }); i += 3; continue;
        }
        if (['===','!==','=>','==','!=','<=','>=','&&','||','??'].includes(op2)) {
            tokens.push({ type: 'operator', value: op2 }); i += 2; continue;
        }
        if ('=<>!+-*/%&|^~'.includes(code[i])) {
            tokens.push({ type: 'operator', value: code[i] }); i++; continue;
        }

        tokens.push({ type: 'plain', value: code[i] }); i++;
    }

    return tokens;
}

/* ── Inline code detection for option text ─────────────────────────── */

export function looksLikeCode(text: string): boolean {
    if (text.length > 80) return false;
    return (
        /[{}()\[\];=>]/.test(text) ||
        /\b(true|false|null|undefined|NaN|Infinity)\b/.test(text) ||
        /^['"`]/.test(text) ||
        /^\d+$/.test(text.trim()) ||
        /\.\w+\(/.test(text) ||
        JS_KEYWORDS.has(text.trim()) ||
        /^[A-Z][a-zA-Z]+Error$/.test(text.trim()) // TypeError, ReferenceError etc.
    );
}
