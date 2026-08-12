import { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import { ChevronRight, BookOpen } from 'lucide-react';
import { useTrack, useSubjects, useTopics, useLessons } from '../hooks/useLearning';
import TopicCard from '../components/TopicCard';
import LessonCard from '../components/LessonCard';
import '../learning.css';

export default function TopicPage() {
    const { trackId, subjectId } = useParams<{ trackId: string; subjectId: string }>();
    const trackIdNum = Number(trackId);
    const subjectIdNum = Number(subjectId);

    const { data: track } = useTrack(trackIdNum);
    const { data: subjects = [] } = useSubjects(trackIdNum);
    const { data: topics = [], isLoading: topicsLoading } = useTopics(subjectIdNum);

    const [selectedTopicId, setSelectedTopicId] = useState<number | null>(null);
    const { data: lessons = [], isLoading: lessonsLoading } = useLessons(selectedTopicId);

    // Auto-select first topic once topics load
    useEffect(() => {
        if (topics.length > 0 && selectedTopicId === null) {
            setSelectedTopicId(topics[0].id);
        }
    }, [topics, selectedTopicId]);

    const subject = subjects.find((s) => s.id === subjectIdNum);
    const selectedTopic = topics.find((t) => t.id === selectedTopicId);

    return (
        <div className="learn-page">
            {/* Breadcrumb */}
            <nav className="breadcrumb" aria-label="Breadcrumb">
                <div className="breadcrumb-item">
                    <Link to="/tracks" className="breadcrumb-link">Tracks</Link>
                    <ChevronRight size={13} className="breadcrumb-separator" />
                </div>
                {track && (
                    <div className="breadcrumb-item">
                        <Link to={`/tracks/${trackIdNum}`} className="breadcrumb-link">
                            {track.title}
                        </Link>
                        <ChevronRight size={13} className="breadcrumb-separator" />
                    </div>
                )}
                <div className="breadcrumb-item">
                    <span className="breadcrumb-current">{subject?.title ?? 'Topics'}</span>
                </div>
            </nav>

            {/* Subject header */}
            {subject && (
                <div className="learn-hero">
                    <h1 className="page-header-title">{subject.title}</h1>
                    {subject.description && (
                        <p className="page-header-description">{subject.description}</p>
                    )}
                </div>
            )}

            {/* Split layout: topics list + lessons panel */}
            <div className="topic-layout">
                {/* Left: topics */}
                <div className="topic-sidebar">
                    <h2 className="learn-section-title">Topics</h2>
                    {topicsLoading ? (
                        <div className="topic-list">
                            {[0, 1, 2, 3].map((i) => (
                                <div key={i} className="skeleton" style={{ height: 72, borderRadius: 10 }} />
                            ))}
                        </div>
                    ) : topics.length === 0 ? (
                        <div className="learn-empty">
                            <p>No topics yet.</p>
                        </div>
                    ) : (
                        <div className="topic-list">
                            {topics.map((topic) => (
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

                {/* Right: lessons */}
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
                                    {[0, 1, 2, 3].map((i) => (
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
