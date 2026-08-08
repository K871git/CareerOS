import { useState } from 'react';
import { ChevronDown, ChevronRight, CheckCircle2, Circle } from 'lucide-react';
import type { ProgressTrackItem } from '../../../types/api';
import { useTrackDetailProgress } from '../hooks/useProgress';
import type { TrackDetailSubject, TrackDetailLesson } from '../types';

function LessonRow({ lesson }: { lesson: TrackDetailLesson }) {
    return (
        <div className={`prog-lesson-row${lesson.status === 'COMPLETED' ? ' prog-lesson-row--done' : ''}`}>
            {lesson.status === 'COMPLETED' ? (
                <CheckCircle2 size={14} className="prog-lesson-icon prog-lesson-icon--done" />
            ) : (
                <Circle size={14} className="prog-lesson-icon" />
            )}
            <span className="prog-lesson-title">{lesson.title}</span>
            {lesson.status === 'COMPLETED' && lesson.completed_at && (
                <span className="prog-lesson-date">
                    {new Date(lesson.completed_at).toLocaleDateString()}
                </span>
            )}
        </div>
    );
}

function SubjectAccordion({ subject }: { subject: TrackDetailSubject }) {
    const [open, setOpen] = useState(false);

    return (
        <div className="prog-subject-wrap">
            <button
                type="button"
                className="prog-subject-header"
                onClick={() => setOpen((v) => !v)}
            >
                <div className="prog-subject-left">
                    {open ? <ChevronDown size={15} /> : <ChevronRight size={15} />}
                    <span className="prog-subject-title">{subject.title}</span>
                </div>
                <div className="prog-subject-right">
                    <span className="prog-subject-meta">
                        {subject.completed_lessons}/{subject.total_lessons}
                    </span>
                    <div className="prog-mini-bar">
                        <div
                            className="prog-mini-fill"
                            style={{ width: `${subject.percentage}%` }}
                        />
                    </div>
                    <span className="prog-subject-pct">{subject.percentage}%</span>
                </div>
            </button>

            {open && (
                <div className="prog-subject-body">
                    {subject.topics.map((topic) => (
                        <div key={topic.id} className="prog-topic-wrap">
                            <div className="prog-topic-header">
                                <span className="prog-topic-title">{topic.title}</span>
                                <span className="prog-topic-meta">
                                    {topic.completed_lessons}/{topic.total_lessons} lessons
                                </span>
                            </div>
                            <div className="prog-lessons-list">
                                {topic.lessons.map((lesson) => (
                                    <LessonRow key={lesson.id} lesson={lesson} />
                                ))}
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}

interface Props {
    track: ProgressTrackItem;
}

export default function TrackProgress({ track }: Props) {
    const [expanded, setExpanded] = useState(false);
    const { data: detail, isLoading } = useTrackDetailProgress(expanded ? track.id : 0);

    return (
        <div className="prog-track-card">
            <button
                type="button"
                className="prog-track-header"
                onClick={() => setExpanded((v) => !v)}
            >
                <div className="prog-track-left">
                    {expanded ? <ChevronDown size={16} /> : <ChevronRight size={16} />}
                    <div>
                        <div className="prog-track-title">{track.title}</div>
                        <div className="prog-track-meta">
                            {track.completed_lessons} of {track.total_lessons} lessons
                        </div>
                    </div>
                </div>
                <div className="prog-track-right">
                    <div className="prog-track-bar">
                        <div
                            className="prog-track-fill"
                            style={{ width: `${track.percentage}%` }}
                        />
                    </div>
                    <span className="prog-track-pct">{track.percentage}%</span>
                </div>
            </button>

            {expanded && (
                <div className="prog-track-body">
                    {isLoading ? (
                        <div className="prog-track-loading">
                            {[0, 1, 2].map((i) => (
                                <div
                                    key={i}
                                    className="skeleton"
                                    style={{ height: 44, borderRadius: 8, marginBottom: 8 }}
                                />
                            ))}
                        </div>
                    ) : detail ? (
                        detail.subjects.map((subject) => (
                            <SubjectAccordion key={subject.id} subject={subject} />
                        ))
                    ) : (
                        <p className="prog-track-empty">No subjects found for this track.</p>
                    )}
                </div>
            )}
        </div>
    );
}
