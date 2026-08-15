import { useState, useEffect } from 'react';
import { Link, useParams, useLocation, Navigate } from 'react-router-dom';
import { ChevronRight, BookOpen, ClipboardList } from 'lucide-react';
import { useSubjectBySlug, useTopicsForLevel } from '../hooks/useLevel';
import { useLessons } from '../hooks/useLearning';
import TopicCard from '../components/TopicCard';
import LessonCard from '../components/LessonCard';
import '../learning.css';

export default function LevelContentPage() {
    const { category, subjectSlug, level } = useParams<{
        category: string;
        subjectSlug: string;
        level: string;
    }>();
    const location = useLocation();
    const levelNum = Number(level);

    const stateSubjectId = (location.state as { subjectId?: number } | null)?.subjectId;
    const { data: subjectFromApi } = useSubjectBySlug(subjectSlug ?? '', !stateSubjectId);

    const subjectId    = stateSubjectId ?? subjectFromApi?.id ?? 0;
    const subjectTitle = subjectFromApi?.title ?? subjectSlug ?? '';

    const { data: topics = [], isLoading: topicsLoading } = useTopicsForLevel(subjectId, levelNum);

    const [selectedTopicId, setSelectedTopicId] = useState<number | null>(null);
    const { data: lessons = [], isLoading: lessonsLoading } = useLessons(selectedTopicId);

    useEffect(() => {
        if (topics.length > 0 && selectedTopicId === null) {
            setSelectedTopicId(topics[0].id);
        }
    }, [topics, selectedTopicId]);

    useEffect(() => {
        setSelectedTopicId(null);
    }, [subjectSlug, levelNum]);

    const selectedTopic = topics.find(t => t.id === selectedTopicId);

    if (!category || !subjectSlug || !levelNum) return <Navigate to="/learning" replace />;

    const categoryLabel = category === 'languages' ? 'Languages'
                        : category === 'frontend'  ? 'Frontend'
                        : category;

    return (
        <div className="learn-page">
            <nav className="breadcrumb" aria-label="Breadcrumb">
                <div className="breadcrumb-item">
                    <Link to="/learning" className="breadcrumb-link">Learning</Link>
                    <ChevronRight size={13} className="breadcrumb-separator" />
                </div>
                <div className="breadcrumb-item">
                    <Link to={`/learning/${category}`} className="breadcrumb-link">{categoryLabel}</Link>
                    <ChevronRight size={13} className="breadcrumb-separator" />
                </div>
                <div className="breadcrumb-item">
                    <Link
                        to={`/learning/${category}/${subjectSlug}`}
                        state={{ subjectId }}
                        className="breadcrumb-link"
                        style={{ textTransform: 'capitalize' }}
                    >
                        {subjectTitle || subjectSlug}
                    </Link>
                    <ChevronRight size={13} className="breadcrumb-separator" />
                </div>
                <div className="breadcrumb-item">
                    <span className="breadcrumb-current">Level {levelNum}</span>
                </div>
            </nav>

            <div className="level-content-header">
                <div>
                    <h1 className="page-header-title" style={{ textTransform: 'capitalize' }}>
                        {subjectTitle || subjectSlug} — Level {levelNum}
                    </h1>
                    <p className="page-header-description">
                        Study the topics and lessons, then take the level exam when ready.
                    </p>
                </div>
                <Link
                    to={`/learning/${category}/${subjectSlug}/${levelNum}/exam`}
                    state={{ subjectId }}
                    className="level-exam-cta"
                >
                    <ClipboardList size={15} />
                    Take Level {levelNum} Exam
                </Link>
            </div>

            <div className="topic-layout">
                <div className="topic-sidebar">
                    <h2 className="learn-section-title">Topics</h2>
                    {topicsLoading ? (
                        <div className="topic-list">
                            {[0, 1, 2, 3].map(i => (
                                <div key={i} className="skeleton" style={{ height: 72, borderRadius: 10 }} />
                            ))}
                        </div>
                    ) : topics.length === 0 ? (
                        <div className="learn-empty"><p>No topics for this level yet.</p></div>
                    ) : (
                        <div className="topic-list">
                            {topics.map(topic => (
                                <TopicCard
                                    key={topic.id}
                                    topic={topic}
                                    isSelected={selectedTopicId === topic.id}
                                    onSelect={() => setSelectedTopicId(topic.id)}
                                />
                            ))}
                        </div>
                    )}
                </div>

                <div className="lessons-panel">
                    {selectedTopicId === null ? (
                        <div className="learn-empty">
                            <p>Select a topic to view its lessons.</p>
                        </div>
                    ) : (
                        <>
                            <h2 className="learn-section-title">{selectedTopic?.title ?? 'Lessons'}</h2>
                            {lessonsLoading ? (
                                <div className="lessons-list">
                                    {[0, 1, 2, 3].map(i => (
                                        <div key={i} className="skeleton" style={{ height: 64, borderRadius: 10 }} />
                                    ))}
                                </div>
                            ) : lessons.length === 0 ? (
                                <div className="learn-empty">
                                    <BookOpen size={32} className="learn-empty-icon" />
                                    <p>No lessons available for this topic yet.</p>
                                </div>
                            ) : (
                                <div className="lessons-list">
                                    {lessons.map((lesson, i) => (
                                        <LessonCard key={lesson.id} lesson={lesson} index={i} />
                                    ))}
                                </div>
                            )}
                        </>
                    )}
                </div>
            </div>
        </div>
    );
}
