import { useState } from 'react';
import { CheckSquare } from 'lucide-react';
import { useTracks, useSubjects, useTopics } from '../../learning/hooks/useLearning';
import { useQuestions, useSubmitAttempt } from '../hooks/useMCQ';
import QuestionProgress from '../components/QuestionProgress';
import QuestionCard from '../components/QuestionCard';
import QuestionOption from '../components/QuestionOption';
import '../assessment.css';

type QuizView = 'select' | 'quiz';

export default function MCQPage() {
    const [view, setView] = useState<QuizView>('select');
    const [selectedTrackId, setSelectedTrackId] = useState(0);
    const [selectedSubjectId, setSelectedSubjectId] = useState(0);
    const [selectedTopicId, setSelectedTopicId] = useState(0);
    const [currentIndex, setCurrentIndex] = useState(0);
    const [answers, setAnswers] = useState<Record<number, number>>({});
    const [submitError, setSubmitError] = useState<string | null>(null);

    const { data: tracks = [] } = useTracks();
    const { data: subjects = [] } = useSubjects(selectedTrackId);
    const { data: topics = [] } = useTopics(selectedSubjectId);
    const { data: questions = [], isLoading: questionsLoading } = useQuestions(
        view === 'quiz' ? selectedTopicId : 0
    );

    const submitAttempt = useSubmitAttempt();

    function handleTrackChange(id: number) {
        setSelectedTrackId(id);
        setSelectedSubjectId(0);
        setSelectedTopicId(0);
    }

    function handleSubjectChange(id: number) {
        setSelectedSubjectId(id);
        setSelectedTopicId(0);
    }

    function handleStartQuiz() {
        if (!selectedTopicId) return;
        setView('quiz');
        setCurrentIndex(0);
        setAnswers({});
        setSubmitError(null);
    }

    function handleSelectAnswer(questionId: number, optionId: number) {
        setAnswers((prev) => ({ ...prev, [questionId]: optionId }));
        setSubmitError(null);
    }

    function handleNext() {
        if (currentIndex < questions.length - 1) setCurrentIndex((i) => i + 1);
    }

    function handlePrev() {
        if (currentIndex > 0) setCurrentIndex((i) => i - 1);
    }

    function handleSubmit() {
        const firstUnanswered = questions.findIndex((q) => answers[q.id] === undefined);
        if (firstUnanswered !== -1) {
            setCurrentIndex(firstUnanswered);
            setSubmitError(
                `Answer all ${questions.length} questions first. Jumped to question ${firstUnanswered + 1}.`
            );
            return;
        }
        submitAttempt.mutate({
            answers: questions.map((q) => ({
                question_id: q.id,
                selected_option_id: answers[q.id],
            })),
        });
    }

    // ── Select Topic View ─────────────────────────────────────────────────
    if (view === 'select') {
        return (
            <div className="practice-page">
                <div className="practice-inner">
                    <div className="practice-hero">
                        <div className="practice-hero-icon">
                            <CheckSquare size={22} />
                        </div>
                        <div>
                            <h1 className="practice-hero-title">Practice Questions</h1>
                            <p className="practice-hero-desc">
                                Test your knowledge with multiple choice questions
                            </p>
                        </div>
                    </div>

                    <div className="topic-selector-card">
                        <p className="topic-selector-heading">Select a Topic to Practice</p>
                        <div className="topic-selector-fields">
                            <div className="selector-field">
                                <label className="selector-label">Track</label>
                                <select
                                    className="selector-select"
                                    value={selectedTrackId || ''}
                                    onChange={(e) => handleTrackChange(Number(e.target.value))}
                                >
                                    <option value="">Choose a track...</option>
                                    {tracks.map((t) => (
                                        <option key={t.id} value={t.id}>{t.title}</option>
                                    ))}
                                </select>
                            </div>

                            <div className="selector-field">
                                <label className="selector-label">Subject</label>
                                <select
                                    className="selector-select"
                                    value={selectedSubjectId || ''}
                                    disabled={!selectedTrackId}
                                    onChange={(e) => handleSubjectChange(Number(e.target.value))}
                                >
                                    <option value="">Choose a subject...</option>
                                    {subjects.map((s) => (
                                        <option key={s.id} value={s.id}>{s.title}</option>
                                    ))}
                                </select>
                            </div>

                            <div className="selector-field">
                                <label className="selector-label">Topic</label>
                                <select
                                    className="selector-select"
                                    value={selectedTopicId || ''}
                                    disabled={!selectedSubjectId}
                                    onChange={(e) => setSelectedTopicId(Number(e.target.value))}
                                >
                                    <option value="">Choose a topic...</option>
                                    {topics.map((t) => (
                                        <option key={t.id} value={t.id}>{t.title}</option>
                                    ))}
                                </select>
                            </div>
                        </div>

                        <button
                            type="button"
                            className="practice-start-btn"
                            onClick={handleStartQuiz}
                            disabled={!selectedTopicId}
                        >
                            Start Quiz
                        </button>
                    </div>
                </div>
            </div>
        );
    }

    // ── Quiz Loading ───────────────────────────────────────────────────────
    if (questionsLoading) {
        return (
            <div className="practice-page">
                <div className="practice-inner">
                    <div className="skeleton" style={{ height: 10, width: '100%', borderRadius: 8, marginBottom: 24 }} />
                    <div className="skeleton" style={{ height: 96, borderRadius: 14, marginBottom: 16 }} />
                    {[0, 1, 2, 3].map((i) => (
                        <div key={i} className="skeleton" style={{ height: 60, borderRadius: 10, marginBottom: 10 }} />
                    ))}
                </div>
            </div>
        );
    }

    // ── Empty ──────────────────────────────────────────────────────────────
    if (questions.length === 0) {
        return (
            <div className="practice-page">
                <div className="practice-inner">
                    <div className="practice-empty">
                        <p>No questions available for this topic yet.</p>
                        <button
                            type="button"
                            className="practice-back-link"
                            style={{ marginTop: '1rem', display: 'block' }}
                            onClick={() => setView('select')}
                        >
                            ← Choose another topic
                        </button>
                    </div>
                </div>
            </div>
        );
    }

    // ── Quiz View ──────────────────────────────────────────────────────────
    const currentQuestion = questions[currentIndex];
    const isLastQuestion = currentIndex === questions.length - 1;
    const answeredCount = Object.keys(answers).length;

    return (
        <div className="practice-page">
            <div className="practice-inner">
                <button
                    type="button"
                    className="practice-back-btn"
                    onClick={() => setView('select')}
                >
                    ← Change Topic
                </button>

                <QuestionProgress
                    current={currentIndex + 1}
                    total={questions.length}
                    answered={answeredCount}
                    difficulty={currentQuestion.difficulty}
                />

                <QuestionCard question={currentQuestion} questionNumber={currentIndex + 1} />

                <div className="q-options-list">
                    {currentQuestion.options.map((opt, i) => (
                        <QuestionOption
                            key={opt.id}
                            option={opt}
                            index={i}
                            isSelected={answers[currentQuestion.id] === opt.id}
                            onSelect={() => handleSelectAnswer(currentQuestion.id, opt.id)}
                            disabled={submitAttempt.isPending}
                        />
                    ))}
                </div>

                {submitError && <p className="quiz-error">{submitError}</p>}

                <div className="quiz-nav">
                    <button
                        type="button"
                        className="quiz-nav-btn"
                        onClick={handlePrev}
                        disabled={currentIndex === 0 || submitAttempt.isPending}
                    >
                        ← Previous
                    </button>

                    <span className="quiz-nav-dots">
                        {questions.map((q, i) => (
                            <button
                                key={q.id}
                                type="button"
                                className={[
                                    'quiz-dot',
                                    i === currentIndex ? 'quiz-dot--current' : '',
                                    answers[q.id] !== undefined ? 'quiz-dot--answered' : '',
                                ].filter(Boolean).join(' ')}
                                onClick={() => setCurrentIndex(i)}
                                aria-label={`Question ${i + 1}`}
                            />
                        ))}
                    </span>

                    {isLastQuestion ? (
                        <button
                            type="button"
                            className="practice-start-btn"
                            onClick={handleSubmit}
                            disabled={submitAttempt.isPending}
                        >
                            {submitAttempt.isPending
                                ? 'Submitting...'
                                : `Submit (${answeredCount}/${questions.length})`}
                        </button>
                    ) : (
                        <button
                            type="button"
                            className="quiz-nav-btn"
                            onClick={handleNext}
                            disabled={submitAttempt.isPending}
                        >
                            Next →
                        </button>
                    )}
                </div>
            </div>
        </div>
    );
}
