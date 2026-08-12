import { useState } from 'react';
import { BookMarked } from 'lucide-react';
import { useTracks, useSubjects, useTopics } from '../../learning/hooks/useLearning';
import { useTheoryQuestions } from '../hooks/useTheory';
import TheoryQuestionCard from '../components/TheoryQuestionCard';
import AnswerEditor from '../components/AnswerEditor';
import type { TheoryQuestion } from '../../../types/api';
import '../theory.css';

type TheoryView = 'select' | 'list' | 'answering';

export default function TheoryQuestionsPage() {
    const [view, setView] = useState<TheoryView>('select');
    const [selectedTrackId, setSelectedTrackId] = useState(0);
    const [selectedSubjectId, setSelectedSubjectId] = useState(0);
    const [selectedTopicId, setSelectedTopicId] = useState(0);
    const [selectedQuestion, setSelectedQuestion] = useState<TheoryQuestion | null>(null);
    const [selectedQuestionIndex, setSelectedQuestionIndex] = useState(0);

    const { data: tracks = [] } = useTracks();
    const { data: subjects = [] } = useSubjects(selectedTrackId);
    const { data: topics = [] } = useTopics(selectedSubjectId);
    const { data: questions = [], isLoading: questionsLoading } = useTheoryQuestions(
        view !== 'select' ? selectedTopicId : 0
    );

    function handleTrackChange(id: number) {
        setSelectedTrackId(id);
        setSelectedSubjectId(0);
        setSelectedTopicId(0);
    }

    function handleSubjectChange(id: number) {
        setSelectedSubjectId(id);
        setSelectedTopicId(0);
    }

    function handleLoadQuestions() {
        if (!selectedTopicId) return;
        setView('list');
    }

    function handleAnswerQuestion(q: TheoryQuestion, index: number) {
        setSelectedQuestion(q);
        setSelectedQuestionIndex(index);
        setView('answering');
    }

    // ── SELECT VIEW ────────────────────────────────────────────────
    if (view === 'select') {
        return (
            <div className="theory-page">
                <div className="theory-inner">
                    <div className="theory-hero">
                        <div className="theory-hero-icon">
                            <BookMarked size={22} />
                        </div>
                        <div>
                            <h1 className="theory-hero-title">Theory Questions</h1>
                            <p className="theory-hero-desc">
                                Practise written explanations and get instructor feedback
                            </p>
                        </div>
                    </div>

                    <div className="theory-selector-card">
                        <p className="theory-selector-heading">Select a Topic</p>
                        <div className="theory-selector-fields">
                            <div className="theory-selector-field">
                                <label className="theory-selector-label">Track</label>
                                <select
                                    className="theory-selector-select"
                                    value={selectedTrackId || ''}
                                    onChange={(e) => handleTrackChange(Number(e.target.value))}
                                >
                                    <option value="">Choose a track…</option>
                                    {tracks.map((t) => (
                                        <option key={t.id} value={t.id}>{t.title}</option>
                                    ))}
                                </select>
                            </div>

                            <div className="theory-selector-field">
                                <label className="theory-selector-label">Subject</label>
                                <select
                                    className="theory-selector-select"
                                    value={selectedSubjectId || ''}
                                    disabled={!selectedTrackId}
                                    onChange={(e) => handleSubjectChange(Number(e.target.value))}
                                >
                                    <option value="">Choose a subject…</option>
                                    {subjects.map((s) => (
                                        <option key={s.id} value={s.id}>{s.title}</option>
                                    ))}
                                </select>
                            </div>

                            <div className="theory-selector-field">
                                <label className="theory-selector-label">Topic</label>
                                <select
                                    className="theory-selector-select"
                                    value={selectedTopicId || ''}
                                    disabled={!selectedSubjectId}
                                    onChange={(e) => setSelectedTopicId(Number(e.target.value))}
                                >
                                    <option value="">Choose a topic…</option>
                                    {topics.map((t) => (
                                        <option key={t.id} value={t.id}>{t.title}</option>
                                    ))}
                                </select>
                            </div>
                        </div>

                        <button
                            type="button"
                            className="theory-start-btn"
                            onClick={handleLoadQuestions}
                            disabled={!selectedTopicId}
                        >
                            Load Questions
                        </button>
                    </div>
                </div>
            </div>
        );
    }

    // ── ANSWERING VIEW ─────────────────────────────────────────────
    if (view === 'answering' && selectedQuestion) {
        return (
            <div className="theory-page">
                <div className="theory-inner">
                    <button
                        type="button"
                        className="theory-back-btn"
                        onClick={() => setView('list')}
                    >
                        ← Back to questions
                    </button>

                    <div className="theory-question-display">
                        <p className="theory-question-display-label">Q{selectedQuestionIndex + 1}</p>
                        <p className="theory-question-display-text">{selectedQuestion.question}</p>
                    </div>

                    <AnswerEditor
                        question={selectedQuestion}
                        onBack={() => setView('list')}
                    />
                </div>
            </div>
        );
    }

    // ── LOADING ────────────────────────────────────────────────────
    if (questionsLoading) {
        return (
            <div className="theory-page">
                <div className="theory-inner">
                    {[0, 1, 2, 3].map((i) => (
                        <div key={i} className="skeleton" style={{ height: 120, borderRadius: 14 }} />
                    ))}
                </div>
            </div>
        );
    }

    // ── EMPTY ──────────────────────────────────────────────────────
    if (questions.length === 0) {
        return (
            <div className="theory-page">
                <div className="theory-inner">
                    <button
                        type="button"
                        className="theory-back-btn"
                        onClick={() => setView('select')}
                    >
                        ← Change topic
                    </button>
                    <div className="theory-empty">
                        <p>No theory questions available for this topic yet.</p>
                    </div>
                </div>
            </div>
        );
    }

    // ── LIST VIEW ──────────────────────────────────────────────────
    return (
        <div className="theory-page">
            <div className="theory-inner">
                <div className="theory-list-header">
                    <button
                        type="button"
                        className="theory-back-btn"
                        onClick={() => setView('select')}
                    >
                        ← Change topic
                    </button>
                    <span className="theory-list-count">{questions.length} questions</span>
                </div>

                <div>
                    <h2 className="theory-list-title">Theory Questions</h2>
                    <div className="theory-q-list">
                        {questions.map((q, i) => (
                            <TheoryQuestionCard
                                key={q.id}
                                question={q}
                                questionNumber={i + 1}
                                onAnswer={() => handleAnswerQuestion(q, i)}
                            />
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
}
