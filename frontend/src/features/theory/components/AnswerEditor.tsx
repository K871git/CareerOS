import { useState } from "react";
import type { TheoryQuestion } from "../../../types/api";
import { useSubmitTheoryAnswer } from "../hooks/useTheory";

const MIN_CHARS = 20;
const MAX_CHARS = 5000;
const WARN_THRESHOLD = 4500;

interface Props {
    question: TheoryQuestion;
    onBack: () => void;
}

export default function AnswerEditor({ question, onBack }: Props) {
    const [answer, setAnswer] = useState("");
    const submit = useSubmitTheoryAnswer();

    const len = answer.length;
    const isValid = len >= MIN_CHARS && len <= MAX_CHARS;

    const charInfoClass =
        len > MAX_CHARS
            ? "answer-char-info--over"
            : len > WARN_THRESHOLD
                ? "answer-char-info--warn"
                : "";

    function handleSubmit() {
        if (!isValid || submit.isPending) return;
        submit.mutate({ questionId: question.id, answer });
    }

    return (
        <div className="answer-editor-wrap">
            <p className="answer-editor-title">Your Answer</p>
            <textarea
                className="answer-textarea"
                value={answer}
                onChange={(e) => setAnswer(e.target.value)}
                placeholder="Write a detailed answer… (minimum 20 characters)"
                disabled={submit.isPending}
                rows={10}
            />
            <div className="answer-editor-footer">
                <div className="answer-editor-meta">
                    <span className={`answer-char-info ${charInfoClass}`}>
                        {len.toLocaleString()} / {MAX_CHARS.toLocaleString()} characters
                    </span>
                    {len < MIN_CHARS ? (
                        <span className="answer-min-hint">
                            {MIN_CHARS - len} more character{MIN_CHARS - len !== 1 ? "s" : ""}{" "}
                            required
                        </span>
                    ) : (
                        <span className="answer-min-hint answer-min-hint--active">
                            ✓ Minimum length met
                        </span>
                    )}
                </div>
                <div className="answer-editor-actions">
                    <button
                        type="button"
                        className="theory-back-btn"
                        onClick={onBack}
                        disabled={submit.isPending}
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        className="answer-submit-btn"
                        onClick={handleSubmit}
                        disabled={!isValid || submit.isPending}
                    >
                        {submit.isPending ? "Submitting…" : "Submit Answer"}
                    </button>
                </div>
            </div>
        </div>
    );
}
