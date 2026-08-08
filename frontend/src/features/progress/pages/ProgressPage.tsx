import { TrendingUp } from 'lucide-react';
import { Link } from 'react-router-dom';
import { useOverallProgress, useRecentActivityFeed } from '../hooks/useProgress';
import ProgressCard from '../components/ProgressCard';
import TrackProgress from '../components/TrackProgress';
import RecentActivityList from '../components/RecentActivity';
import '../progress.css';

function ProgressSkeleton() {
    return (
        <div className="prog-page">
            <div className="skeleton" style={{ height: 36, width: 220, borderRadius: 8, marginBottom: 6 }} />
            <div className="skeleton" style={{ height: 18, width: 340, borderRadius: 6, marginBottom: 32 }} />
            <div className="prog-summary-grid">
                {[0, 1, 2, 3].map((i) => (
                    <div key={i} className="skeleton" style={{ height: 104, borderRadius: 14 }} />
                ))}
            </div>
        </div>
    );
}

export default function ProgressPage() {
    const { data: progress, isLoading: progressLoading } = useOverallProgress();
    const { data: activity, isLoading: activityLoading } = useRecentActivityFeed();

    if (progressLoading) return <ProgressSkeleton />;

    return (
        <div className="prog-page">
            {/* Header */}
            <div className="prog-header">
                <div className="prog-header-icon">
                    <TrendingUp size={22} />
                </div>
                <div>
                    <h1 className="prog-title">Your Progress</h1>
                    <p className="prog-subtitle">
                        Track your learning journey across all tracks
                    </p>
                </div>
            </div>

            {/* Summary stats */}
            {progress ? (
                <ProgressCard data={progress} />
            ) : (
                <div className="prog-empty">
                    <p>
                        No progress yet.{' '}
                        <Link to="/tracks" className="prog-link">Browse learning tracks</Link>{' '}
                        to get started.
                    </p>
                </div>
            )}

            {/* Track breakdown */}
            <div className="prog-section">
                <h2 className="prog-section-title">Learning Tracks</h2>
                {progress && progress.tracks.length > 0 ? (
                    <div className="prog-tracks-list">
                        {progress.tracks.map((track) => (
                            <TrackProgress key={track.id} track={track} />
                        ))}
                    </div>
                ) : (
                    <div className="prog-empty">
                        <p>
                            No tracks yet.{' '}
                            <Link to="/tracks" className="prog-link">Browse learning tracks</Link>.
                        </p>
                    </div>
                )}
            </div>

            {/* Recent activity */}
            <div className="prog-section">
                <h2 className="prog-section-title">Recent Activity</h2>
                {activityLoading ? (
                    <div>
                        {[0, 1, 2].map((i) => (
                            <div
                                key={i}
                                className="skeleton"
                                style={{ height: 56, borderRadius: 10, marginBottom: 10 }}
                            />
                        ))}
                    </div>
                ) : (
                    <RecentActivityList items={activity ?? []} />
                )}
            </div>
        </div>
    );
}
