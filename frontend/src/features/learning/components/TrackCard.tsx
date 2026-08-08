import { Link } from 'react-router-dom';
import { ArrowRight } from 'lucide-react';
import type { LearningTrack } from '../../../types/api';

interface Props {
    track: LearningTrack;
}

const BANNER_CLASSES = ['track-banner-0', 'track-banner-1', 'track-banner-2', 'track-banner-3'];

export default function TrackCard({ track }: Props) {
    const bannerClass = BANNER_CLASSES[(track.display_order - 1) % BANNER_CLASSES.length] ?? BANNER_CLASSES[0];
    const initial = track.title.charAt(0).toUpperCase();

    return (
        <Link to={`/tracks/${track.id}`} className="lrn-track-card">
            <div className={`lrn-track-banner ${bannerClass}`}>
                <span className="lrn-track-initial">{initial}</span>
            </div>
            <div className="lrn-track-body">
                <h3 className="lrn-track-title">{track.title}</h3>
                <p className="lrn-track-desc">{track.description}</p>
                <div className="lrn-track-cta">
                    Explore Track <ArrowRight size={14} />
                </div>
            </div>
        </Link>
    );
}
