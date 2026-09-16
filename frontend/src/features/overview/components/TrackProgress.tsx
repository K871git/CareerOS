import { useState } from 'react';
import {
    ChevronDown, ChevronRight, CheckCircle2, Circle,
    Globe, Server, Monitor, Database, BookOpen,
} from 'lucide-react';
import type { ProgressTrackItem, QuizBySubject } from '../../../types/api';
import { useTrackDetailProgress } from '../hooks/useOverview';
import type { TrackDetailSubject, TrackDetailLesson } from '../types';

// ── Track metadata ──────────────────────────────────────────────────────

const TRACK_META: Record<string, { Icon: React.ElementType; accent: string; bg: string }> = {
    'full-stack-web-development': { Icon: Globe,    accent: '#4f46e5', bg: 'rgba(79,70,229,0.10)' },
    'backend-engineering':        { Icon: Server,   accent: '#059669', bg: 'rgba(5,150,105,0.10)' },
    'frontend-engineering':       { Icon: Monitor,  accent: '#7c3aed', bg: 'rgba(124,58,237,0.10)' },
    'databases':                  { Icon: Database, accent: '#d97706', bg: 'rgba(217,119,6,0.10)'  },
};

const DEFAULT_META = { Icon: BookOpen, accent: '#4f46e5', bg: 'rgba(79,70,229,0.10)' };

// Keywords used to match quiz subjects to each track
const TRACK_KEYWORDS: Record<string, string[]> = {
    'databases':                  ['mysql', 'sql', 'postgresql', 'postgres', 'mongodb', 'redis', 'sqlite', 'oracle'],
    'frontend-engineering':       ['javascript', 'typescript', 'react', 'vue', 'angular', 'css', 'html', 'svelte'],
    'backend-engineering':        ['python', 'php', 'java', 'go', 'golang', 'rust', 'ruby', 'node', 'django', 'laravel'],
    'full-stack-web-development': ['javascript', 'typescript', 'python', 'php', 'react', 'node', 'mysql', 'postgresql'],
};

function matchedQuiz(slug: string, quiz: QuizBySubject[]): QuizBySubject[] {
    const kws = TRACK_KEYWORDS[slug] ?? [];
    return quiz.filter(s => kws.some(k => s.subject_title.toLowerCase().includes(k)));
}

function scoreColor(pct: number) {
    if (pct >= 75) return '#22c55e';
    if (pct >= 50) return '#f59e0b';
    return '#ef4444';
}

// ── LessonRow ───────────────────────────────────────────────────────────

function LessonRow({ lesson }: { lesson: TrackDetailLesson }) {
    return (
        <div className={`prog-lesson-row${lesson.status === 'COMPLETED' ? ' prog-lesson-row--done' : ''}`}>
            {lesson.status === 'COMPLETED'
                ? <CheckCircle2 size={13} className="prog-lesson-icon prog-lesson-icon--done" />
                : <Circle size={13} className="prog-lesson-icon" />}
            <span className="prog-lesson-title">{lesson.title}</span>
            {lesson.status === 'COMPLETED' && lesson.completed_at && (
                <span className="prog-lesson-date">
                    {new Date(lesson.completed_at).toLocaleDateString()}
                </span>
            )}
        </div>
    );
}

// ── SubjectAccordion ────────────────────────────────────────────────────

function SubjectAccordion({ subject }: { subject: TrackDetailSubject }) {
    const [open, setOpen] = useState(false);
    return (
        <div className="prog-subject-wrap">
            <button type="button" className="prog-subject-header" onClick={() => setOpen(v => !v)}>
                <div className="prog-subject-left">
                    {open ? <ChevronDown size={14} /> : <ChevronRight size={14} />}
                    <span className="prog-subject-title">{subject.title}</span>
                </div>
                <div className="prog-subject-right">
                    <span className="prog-subject-meta">{subject.completed_lessons}/{subject.total_lessons}</span>
                    <div className="prog-mini-bar">
                        <div className="prog-mini-fill" style={{ width: `${subject.percentage}%` }} />
                    </div>
                    <span className="prog-subject-pct">{subject.percentage}%</span>
                </div>
            </button>

            {open && (
                <div className="prog-subject-body">
                    {subject.topics.map(topic => (
                        <div key={topic.id} className="prog-topic-wrap">
                            <div className="prog-topic-header">
                                <span className="prog-topic-title">{topic.title}</span>
                                <span className="prog-topic-meta">{topic.completed_lessons}/{topic.total_lessons} lessons</span>
                            </div>
                            <div className="prog-lessons-list">
                                {topic.lessons.map(lesson => <LessonRow key={lesson.id} lesson={lesson} />)}
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}

// ── Main TrackProgress card ─────────────────────────────────────────────

interface Props {
    track: ProgressTrackItem;
    quizBySubject: QuizBySubject[];
}

export default function TrackProgress({ track, quizBySubject }: Props) {
    const [expanded, setExpanded] = useState(false);
    const { data: detail, isLoading } = useTrackDetailProgress(expanded ? track.id : 0);

    const { Icon, accent, bg } = TRACK_META[track.slug] ?? DEFAULT_META;
    const quizHits  = matchedQuiz(track.slug, quizBySubject);
    const pct       = track.percentage;
    const isStarted = track.completed_lessons > 0;

    return (
        <div className="tcard">
            {/* ── Header row ── */}
            <button
                type="button"
                className="tcard-header"
                onClick={() => setExpanded(v => !v)}
                aria-expanded={expanded}
            >
                {/* Accent strip */}
                <div className="tcard-strip" style={{ background: accent }} />

                {/* Icon */}
                <div className="tcard-icon" style={{ background: bg, color: accent }}>
                    <Icon size={17} />
                </div>

                {/* Info */}
                <div className="tcard-info">
                    <div className="tcard-title">{track.title}</div>
                    <div className="tcard-meta">
                        {isStarted
                            ? `${track.completed_lessons} of ${track.total_lessons} lessons complete`
                            : `${track.total_lessons} lessons · not started yet`}
                    </div>
                </div>

                {/* Pct + chevron */}
                <div className="tcard-right">
                    <span className="tcard-pct" style={{ color: accent }}>
                        {pct > 0 ? `${pct}%` : '—'}
                    </span>
                    <span className="tcard-chevron">
                        {expanded ? <ChevronDown size={15} /> : <ChevronRight size={15} />}
                    </span>
                </div>
            </button>

            {/* ── Progress bar ── */}
            <div className="tcard-bar-wrap">
                <div className="tcard-bar">
                    <div
                        className="tcard-fill"
                        style={{ width: pct > 0 ? `${pct}%` : '2px', background: accent }}
                    />
                </div>
            </div>

            {/* ── Quiz scores row (only when collapsed) ── */}
            {quizHits.length > 0 && !expanded && (
                <div className="tcard-quiz-row">
                    <span className="tcard-quiz-label">Quiz</span>
                    {quizHits.map(s => (
                        <span
                            key={s.subject_id}
                            className="tcard-quiz-badge"
                            style={{
                                color:       scoreColor(s.avg_score),
                                borderColor: scoreColor(s.avg_score) + '40',
                                background:  scoreColor(s.avg_score) + '12',
                            }}
                        >
                            {s.subject_title} {Math.round(s.avg_score)}%
                        </span>
                    ))}
                </div>
            )}

            {/* ── Expanded: subject tree ── */}
            {expanded && (
                <div className="tcard-body">
                    {isLoading ? (
                        <div className="prog-track-loading">
                            {[0, 1, 2].map(i => (
                                <div key={i} className="skeleton" style={{ height: 42, borderRadius: 8, marginBottom: 8 }} />
                            ))}
                        </div>
                    ) : detail?.subjects.length ? (
                        detail.subjects.map(subject => (
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
