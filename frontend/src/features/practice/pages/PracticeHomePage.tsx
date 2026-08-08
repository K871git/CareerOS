import { Link } from 'react-router-dom';
import { Target, ChevronRight } from 'lucide-react';
import { useTracks, useSubjects } from '../../learning/hooks/useLearning';
import type { LearningTrack, Subject } from '../../../types/api';
import '../practice.css';

function SubjectCard({ subject, trackTitle }: { subject: Subject; trackTitle: string }) {
    return (
        <Link
            to={`/practice/subjects/${subject.id}`}
            state={{ subjectTitle: subject.title, trackTitle }}
            className="prac-subject-card"
        >
            <div>
                <h3 className="prac-subject-title">{subject.title}</h3>
                <p className="prac-subject-desc">
                    {subject.description || 'Level-based interview practice questions.'}
                </p>
            </div>
            <div className="prac-subject-meta">
                <span className="prac-subject-levels">3 Levels</span>
                <ChevronRight size={15} />
            </div>
        </Link>
    );
}

function TrackSection({ track }: { track: LearningTrack }) {
    const { data: subjects = [], isLoading } = useSubjects(track.id);

    const practiceSubjects = subjects.filter((s) => s.mcq_question_count >= 10);

    if (isLoading) {
        return (
            <div className="prac-track-section">
                <div className="skeleton" style={{ height: 14, width: 160, borderRadius: 6, marginBottom: 14 }} />
                <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(260px, 1fr))', gap: '0.875rem' }}>
                    {[0, 1].map((i) => (
                        <div key={i} className="skeleton" style={{ height: 120, borderRadius: 14 }} />
                    ))}
                </div>
            </div>
        );
    }

    if (practiceSubjects.length === 0) return null;

    return (
        <div className="prac-track-section">
            <p className="prac-track-title">{track.title}</p>
            <div className="prac-subjects-grid">
                {practiceSubjects.map((s) => (
                    <SubjectCard key={s.id} subject={s} trackTitle={track.title} />
                ))}
            </div>
        </div>
    );
}

export default function PracticeHomePage() {
    const { data: tracks = [], isLoading } = useTracks();

    return (
        <div className="practice-page">
            <div className="practice-inner">
                {/* Hero */}
                <div className="prac-hero">
                    <div className="prac-hero-icon">
                        <Target size={22} />
                    </div>
                    <div>
                        <h1 className="prac-hero-title">Practice Mode</h1>
                        <p className="prac-hero-desc">
                            Level-based interview preparation — Junior to Senior
                        </p>
                    </div>
                </div>

                {/* How it works */}
                <div className="prac-how-it-works">
                    <div className="prac-how-step">
                        <span className="prac-how-num">1</span>
                        <span>Pick a subject</span>
                    </div>
                    <div className="prac-how-arrow">→</div>
                    <div className="prac-how-step">
                        <span className="prac-how-num">2</span>
                        <span>Start at Junior level</span>
                    </div>
                    <div className="prac-how-arrow">→</div>
                    <div className="prac-how-step">
                        <span className="prac-how-num">3</span>
                        <span>Score 7/10 to unlock next</span>
                    </div>
                    <div className="prac-how-arrow">→</div>
                    <div className="prac-how-step">
                        <span className="prac-how-num">4</span>
                        <span>Advance to Senior level</span>
                    </div>
                </div>

                {/* Subjects by track */}
                {isLoading ? (
                    <div style={{ display: 'flex', flexDirection: 'column', gap: 24 }}>
                        {[0, 1].map((i) => (
                            <div key={i}>
                                <div className="skeleton" style={{ height: 14, width: 160, borderRadius: 6, marginBottom: 14 }} />
                                <div className="skeleton" style={{ height: 120, borderRadius: 14 }} />
                            </div>
                        ))}
                    </div>
                ) : (
                    tracks.map((track) => <TrackSection key={track.id} track={track} />)
                )}
            </div>
        </div>
    );
}
