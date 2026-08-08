import { useParams, Link } from 'react-router-dom';
import { ChevronRight, BookOpen } from 'lucide-react';
import { useTrack, useSubjects } from '../hooks/useLearning';
import SubjectCard from '../components/SubjectCard';
import '../learning.css';

function DetailsSkeleton() {
    return (
        <div className="learn-page">
            <div className="skeleton" style={{ height: 14, width: 200, borderRadius: 6, marginBottom: 24 }} />
            <div className="skeleton" style={{ height: 32, width: '55%', borderRadius: 8, marginBottom: 10 }} />
            <div className="skeleton" style={{ height: 16, width: '80%', borderRadius: 6, marginBottom: 32 }} />
            <div className="subjects-list">
                {[0, 1, 2].map((i) => (
                    <div key={i} className="skeleton" style={{ height: 80, borderRadius: 14 }} />
                ))}
            </div>
        </div>
    );
}

export default function TrackDetailsPage() {
    const { trackId } = useParams<{ trackId: string }>();
    const id = Number(trackId);

    const { data: track, isLoading: trackLoading } = useTrack(id);
    const { data: subjects = [], isLoading: subjectsLoading } = useSubjects(id);

    if (trackLoading) return <DetailsSkeleton />;

    if (!track) {
        return (
            <div className="learn-page">
                <div className="learn-empty">
                    <BookOpen size={40} className="learn-empty-icon" />
                    <p>Track not found. <Link to="/tracks" className="learn-link">Browse all tracks</Link></p>
                </div>
            </div>
        );
    }

    return (
        <div className="learn-page">
            {/* Breadcrumb */}
            <nav className="breadcrumb" aria-label="Breadcrumb">
                <div className="breadcrumb-item">
                    <Link to="/tracks" className="breadcrumb-link">Tracks</Link>
                    <ChevronRight size={13} className="breadcrumb-separator" />
                </div>
                <div className="breadcrumb-item">
                    <span className="breadcrumb-current">{track.title}</span>
                </div>
            </nav>

            {/* Hero */}
            <div className="learn-hero">
                <h1 className="page-header-title">{track.title}</h1>
                {track.description && (
                    <p className="page-header-description">{track.description}</p>
                )}
            </div>

            {/* Subjects */}
            <div className="learn-section">
                <h2 className="learn-section-title">Subjects</h2>
                {subjectsLoading ? (
                    <div className="subjects-list">
                        {[0, 1, 2].map((i) => (
                            <div key={i} className="skeleton" style={{ height: 80, borderRadius: 14 }} />
                        ))}
                    </div>
                ) : subjects.length === 0 ? (
                    <div className="learn-empty">
                        <p>No subjects added yet.</p>
                    </div>
                ) : (
                    <div className="subjects-list">
                        {subjects.map((subject) => (
                            <SubjectCard key={subject.id} subject={subject} trackId={id} />
                        ))}
                    </div>
                )}
            </div>
        </div>
    );
}
