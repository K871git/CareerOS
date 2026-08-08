import { BookOpen } from 'lucide-react';
import { useTracks } from '../hooks/useLearning';
import TrackCard from '../components/TrackCard';
import type { LearningTrack } from '../../../types/api';
import '../learning.css';

function TracksSkeleton() {
    return (
        <div className="tracks-grid">
            {[0, 1, 2].map((i) => (
                <div key={i} className="skeleton" style={{ height: 252, borderRadius: 16 }} />
            ))}
        </div>
    );
}

function TracksGrid({ tracks }: { tracks: LearningTrack[] }) {
    if (tracks.length === 0) {
        return (
            <div className="learn-empty">
                <BookOpen size={40} className="learn-empty-icon" />
                <p>No learning tracks available yet. Check back soon.</p>
            </div>
        );
    }
    return (
        <div className="tracks-grid">
            {tracks.map((track) => (
                <TrackCard key={track.id} track={track} />
            ))}
        </div>
    );
}

export default function LearningTracksPage() {
    const { data: tracks = [], isLoading } = useTracks();

    return (
        <div className="learn-page">
            <div className="page-header">
                <div>
                    <h1 className="page-header-title">Learning Tracks</h1>
                    <p className="page-header-description">
                        Choose a structured path and start building your engineering skills.
                    </p>
                </div>
            </div>
            {isLoading ? <TracksSkeleton /> : <TracksGrid tracks={tracks} />}
        </div>
    );
}
