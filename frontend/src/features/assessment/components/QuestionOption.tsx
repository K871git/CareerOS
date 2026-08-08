import type { QuestionOption as OptionType } from '../../../types/api';

interface Props {
    option: OptionType;
    index: number;
    isSelected: boolean;
    onSelect: () => void;
    disabled?: boolean;
}

const LABELS = ['A', 'B', 'C', 'D', 'E', 'F'];

export default function QuestionOption({ option, index, isSelected, onSelect, disabled }: Props) {
    return (
        <button
            type="button"
            className={`q-option${isSelected ? ' q-option--selected' : ''}`}
            onClick={onSelect}
            disabled={disabled}
        >
            <span className="q-option-label">{LABELS[index] ?? String(index + 1)}</span>
            <span className="q-option-text">{option.option_text}</span>
        </button>
    );
}
