import type { QuestionOption as OptionType } from '../../../types/api';
import { looksLikeCode } from '../utils/codeFormat';

interface Props {
    option:     OptionType;
    index:      number;
    isSelected: boolean;
    onSelect:   () => void;
    disabled?:  boolean;
}

const LABELS = ['A', 'B', 'C', 'D', 'E', 'F'];

function OptionText({ text }: { text: string }) {
    // Backtick-wrapped inline code
    if (text.includes('`')) {
        const parts = text.split(/(`[^`]+`)/g);
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

    // Entire option looks like code (short snippet)
    if (looksLikeCode(text)) {
        return <code className="q-option-code">{text}</code>;
    }

    return <>{text}</>;
}

export default function QuestionOption({ option, index, isSelected, onSelect, disabled }: Props) {
    return (
        <button
            type="button"
            className={`q-option${isSelected ? ' q-option--selected' : ''}`}
            onClick={onSelect}
            disabled={disabled}
        >
            <span className="q-option-label">{LABELS[index] ?? String(index + 1)}</span>
            <span className="q-option-text">
                <OptionText text={option.option_text} />
            </span>
        </button>
    );
}
