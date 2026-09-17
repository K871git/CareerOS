import type { MCQQuestion } from '../../../types/api';
import { splitQuestionAndCode, formatCode, tokenize, type CodeToken } from '../utils/codeFormat';

interface Props {
    question: MCQQuestion;
    questionNumber: number;
}

/* Token color map */
const TOKEN_CLASS: Record<CodeToken['type'], string> = {
    keyword:  'ck-kw',
    builtin:  'ck-bi',
    string:   'ck-str',
    comment:  'ck-cmt',
    number:   'ck-num',
    operator: 'ck-op',
    plain:    '',
};

function CodeBlock({ raw }: { raw: string }) {
    const formatted = formatCode(raw);
    const tokens    = tokenize(formatted);

    return (
        <div className="q-code-block">
            <div className="q-code-lang">code</div>
            <pre className="q-code-pre"><code>
                {tokens.map((tok, i) => {
                    if (tok.value === '\n') return <br key={i} />;
                    const cls = TOKEN_CLASS[tok.type];
                    return cls
                        ? <span key={i} className={cls}>{tok.value}</span>
                        : <span key={i}>{tok.value}</span>;
                })}
            </code></pre>
        </div>
    );
}

/* Inline backtick renderer for prose text */
function InlineText({ text }: { text: string }) {
    const parts = text.split(/(`[^`]+`)/g);
    if (parts.length === 1) return <>{text}</>;
    return (
        <>
            {parts.map((p, i) =>
                p.startsWith('`') && p.endsWith('`')
                    ? <code key={i} className="q-inline-code">{p.slice(1, -1)}</code>
                    : <span key={i}>{p}</span>
            )}
        </>
    );
}

export default function QuestionCard({ question, questionNumber }: Props) {
    const { prose, code } = splitQuestionAndCode(question.question);

    return (
        <div className="q-card">
            <span className="q-card-num">Q{questionNumber}</span>
            {prose && (
                <p className="q-card-text">
                    <InlineText text={prose} />
                </p>
            )}
            {code && <CodeBlock raw={code} />}
            {!prose && !code && (
                <p className="q-card-text">{question.question}</p>
            )}
        </div>
    );
}
