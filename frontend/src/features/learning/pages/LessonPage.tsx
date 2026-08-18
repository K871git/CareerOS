import { useState } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import { ChevronLeft, Clock, CheckCircle2 } from 'lucide-react';
import ReactMarkdown from 'react-markdown';
import remarkGfm from 'remark-gfm';
import toast from 'react-hot-toast';
import { useLesson, useCompleteLesson } from '../hooks/useLearning';
import '../learning.css';

function LessonSkeleton() {
    return (
        <div className="learn-page">
            <div className="lesson-page-inner">
                <div className="skeleton" style={{ height: 32, width: 70, borderRadius: 8, marginBottom: 28 }} />
                <div className="skeleton" style={{ height: 34, width: '60%', borderRadius: 8, marginBottom: 12 }} />
                <div className="skeleton" style={{ height: 16, width: 90, borderRadius: 6, marginBottom: 32 }} />
                <div className="lesson-content-skeleton">
                    {[100, 90, 85, 100, 60, 95, 75, 80].map((w, i) => (
                        <div key={i} className="skeleton" style={{ height: 16, width: `${w}%`, borderRadius: 4 }} />
                    ))}
                </div>
            </div>
        </div>
    );
}

export default function LessonPage() {
    const { lessonId } = useParams<{ lessonId: string }>();
    const navigate = useNavigate();
    const id = Number(lessonId);

    const { data: lesson, isLoading } = useLesson(id);
    const complete = useCompleteLesson();
    const [completed, setCompleted] = useState(false);

    function handleComplete() {
        if (complete.isPending || completed) return;
        complete.mutate(id, {
            onSuccess: () => {
                setCompleted(true);
                toast.success('Lesson marked as complete!');
                try {
                    const saved: number[] = JSON.parse(localStorage.getItem('careeros_completed_lessons') ?? '[]');
                    if (!saved.includes(id)) {
                        localStorage.setItem('careeros_completed_lessons', JSON.stringify([...saved, id]));
                    }
                } catch { /* ignore */ }
            },
        });
    }

    if (isLoading) return <LessonSkeleton />;

    if (!lesson) {
        return (
            <div className="learn-page">
                <div className="learn-empty">
                    <p>
                        Lesson not found.{' '}
                        <Link to="/tracks" className="learn-link">Browse tracks</Link>
                    </p>
                </div>
            </div>
        );
    }

    return (
        <div className="learn-page">
            <div className="lesson-page-inner">
                {/* Back */}
                <button
                    type="button"
                    className="lesson-back-btn"
                    onClick={() => navigate(-1)}
                >
                    <ChevronLeft size={16} /> Back
                </button>

                {/* Header */}
                <div className="lesson-header">
                    <h1 className="lesson-title">{lesson.title}</h1>
                    {lesson.estimated_minutes != null && lesson.estimated_minutes > 0 && (
                        <span className="lesson-duration">
                            <Clock size={14} /> {lesson.estimated_minutes} min read
                        </span>
                    )}
                </div>

                <div className="lesson-divider" />

                {/* Content */}
                <div className="lesson-content lesson-md">
                    {lesson.content
                        ? <ReactMarkdown remarkPlugins={[remarkGfm]}>{lesson.content}</ReactMarkdown>
                        : <p className="lesson-content-empty">No content available for this lesson yet.</p>}
                </div>

                <div className="lesson-divider" />

                <div className="lesson-footer">
                    <button
                        type="button"
                        className="lesson-back-btn"
                        onClick={() => navigate(-1)}
                    >
                        <ChevronLeft size={16} /> Back to lessons
                    </button>
                    <button
                        type="button"
                        className={`lesson-complete-btn${completed ? ' lesson-complete-btn--done' : ''}`}
                        onClick={handleComplete}
                        disabled={complete.isPending || completed}
                    >
                        <CheckCircle2 size={16} />
                        {complete.isPending
                            ? 'Saving…'
                            : completed
                            ? 'Completed!'
                            : 'Mark as Complete'}
                    </button>
                </div>
            </div>
        </div>
    );
}
