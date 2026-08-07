import { Link } from 'react-router-dom';
import {
    BookOpen,
    Target,
    TrendingUp,
    Clock,
    ArrowRight,
    CheckCircle2,
    Play,
    Zap,
} from 'lucide-react';
import { useAuth } from '../../../store/authStore';
import { useUserProgress, useLearningTracks, useRecentActivity } from '../hooks/useDashboard';
import { timeAgo } from '../../../utils/time';
import type { UserProgress, LearningTrack, RecentActivity } from '../../../types/api';
import '../dashboard.css';

function StatCard({
    value,
    label,
    icon: Icon,
}: {
    value: string | number;
    label: string;
    icon: React.ElementType;
}) {
    return (
        <div className="stat-card">
            <div className="stat-icon">
                <Icon size={18} />
            </div>
            <div>
                <div className="stat-value">{value}</div>
                <div className="stat-label">{label}</div>
            </div>
        </div>
    );
}

function StatsRow({ data }: { data: UserProgress | null | undefined }) {
    if (!data) {
        return (
            <div className="dash-stats">
                {[0, 1, 2, 3].map((i) => (
                    <div key={i} className="skeleton skeleton-stat" />
                ))}
            </div>
        );
    }
    const tracksCompleted = data.tracks.filter((t) => t.percentage >= 100).length;
    return (
        <div className="dash-stats">
            <StatCard
                value={data.summary.completed_lessons}
                label="Lessons Completed"
                icon={CheckCircle2}
            />
            <StatCard
                value={`${data.summary.percentage}%`}
                label="Completion Rate"
                icon={TrendingUp}
            />
            <StatCard
                value={data.tracks.length}
                label="Tracks Enrolled"
                icon={BookOpen}
            />
            <StatCard
                value={tracksCompleted}
                label="Tracks Completed"
                icon={Target}
            />
        </div>
    );
}

function ActiveTrackCard({ track }: { track: LearningTrack | undefined }) {
    if (!track) {
        return (
            <div className="dash-empty">
                <BookOpen size={36} className="dash-empty-icon" />
                <p>
                    No active track yet.{' '}
                    <Link to="/tracks">Browse learning tracks</Link> to get started.
                </p>
            </div>
        );
    }
    return (
        <div className="track-card">
            <span className="track-level-badge">{track.level}</span>
            <h3 className="track-title">{track.title}</h3>
            <p className="track-desc">{track.description}</p>
            <div className="track-progress-wrap">
                <div className="track-progress-bar">
                    <div
                        className="track-progress-fill"
                        style={{ width: `${track.progress_percentage ?? 0}%` }}
                    />
                </div>
                <div className="track-progress-meta">
                    <span className="track-progress-pct">{track.progress_percentage ?? 0}% complete</span>
                    <span className="track-progress-label">{track.total_topics} topics</span>
                </div>
            </div>
            <Link to="/tracks" className="track-cta">
                <Play size={13} /> Continue track
            </Link>
        </div>
    );
}

function ActivityFeed({ items }: { items: RecentActivity[] | undefined }) {
    if (!items || items.length === 0) {
        return (
            <div className="dash-empty">
                <Clock size={36} className="dash-empty-icon" />
                <p>No activity yet. Complete a lesson to see your history here.</p>
            </div>
        );
    }
    return (
        <ul className="activity-list">
            {items.map((item) => (
                <li key={item.id} className="activity-item">
                    <div className="activity-icon">
                        <CheckCircle2 size={15} />
                    </div>
                    <div className="activity-content">
                        <p className="activity-desc">{item.description}</p>
                        {item.subject_name && (
                            <span className="activity-subject">{item.subject_name}</span>
                        )}
                    </div>
                    <span className="activity-time">{timeAgo(item.created_at)}</span>
                </li>
            ))}
        </ul>
    );
}

export default function DashboardPage() {
    const { state } = useAuth();
    const firstName = state.user?.name?.split(' ')[0] ?? 'there';

    const { data: progress, isLoading: progressLoading } = useUserProgress();
    const { data: tracks, isLoading: tracksLoading } = useLearningTracks();
    const { data: activity, isLoading: activityLoading } = useRecentActivity();

    const activeTrack = tracks?.find((t) => t.enrolled && t.progress_percentage > 0) ?? tracks?.[0];

    return (
        <div className="dashboard">
            {/* Welcome */}
            <div className="dash-welcome">
                <div>
                    <h1 className="dash-welcome-title">Welcome back, {firstName}! 👋</h1>
                    <p className="dash-welcome-sub">
                        Keep the momentum — consistency is what separates great engineers.
                    </p>
                </div>
                {activeTrack && (
                    <Link to="/tracks" className="dash-continue-btn">
                        Continue Learning <ArrowRight size={15} />
                    </Link>
                )}
            </div>

            {/* Stats */}
            {progressLoading ? (
                <div className="dash-stats">
                    {[0, 1, 2, 3].map((i) => (
                        <div key={i} className="skeleton skeleton-stat" />
                    ))}
                </div>
            ) : (
                <StatsRow data={progress} />
            )}

            {/* Main grid */}
            <div className="dash-grid">
                {/* Left column */}
                <div className="dash-col-main">
                    {/* Active Track */}
                    <div className="dash-card">
                        <div className="dash-card-header">
                            <h2 className="dash-card-title">Active Track</h2>
                            <Link to="/tracks" className="dash-card-link">
                                Browse all
                            </Link>
                        </div>
                        {tracksLoading ? (
                            <div>
                                <div className="skeleton skeleton-block" style={{ width: '40%', height: 18 }} />
                                <div className="skeleton skeleton-block" style={{ height: 26, marginTop: 8 }} />
                                <div className="skeleton skeleton-block" style={{ height: 14, width: '80%', marginTop: 8 }} />
                            </div>
                        ) : (
                            <ActiveTrackCard track={activeTrack} />
                        )}
                    </div>

                    {/* Recent Activity */}
                    <div className="dash-card">
                        <div className="dash-card-header">
                            <h2 className="dash-card-title">Recent Activity</h2>
                        </div>
                        {activityLoading ? (
                            <div>
                                {[0, 1, 2].map((i) => (
                                    <div
                                        key={i}
                                        className="skeleton skeleton-block"
                                        style={{ height: 48, marginBottom: 12 }}
                                    />
                                ))}
                            </div>
                        ) : (
                            <ActivityFeed items={activity} />
                        )}
                    </div>
                </div>

                {/* Right column */}
                <div className="dash-col-side">
                    {/* Quick Actions */}
                    <div className="dash-card">
                        <div className="dash-card-header">
                            <h2 className="dash-card-title">Quick Actions</h2>
                        </div>
                        <div className="quick-actions">
                            <Link to="/tracks" className="quick-action-item">
                                <div className="quick-action-icon">
                                    <BookOpen size={17} />
                                </div>
                                <div className="quick-action-body">
                                    <div className="quick-action-label">Browse Tracks</div>
                                    <div className="quick-action-desc">Find your next learning path</div>
                                </div>
                                <ArrowRight size={14} className="quick-action-arrow" />
                            </Link>
                            <Link to="/practice" className="quick-action-item">
                                <div className="quick-action-icon">
                                    <Target size={17} />
                                </div>
                                <div className="quick-action-body">
                                    <div className="quick-action-label">Start Practice</div>
                                    <div className="quick-action-desc">Answer interview questions</div>
                                </div>
                                <ArrowRight size={14} className="quick-action-arrow" />
                            </Link>
                            <Link to="/progress" className="quick-action-item">
                                <div className="quick-action-icon">
                                    <TrendingUp size={17} />
                                </div>
                                <div className="quick-action-body">
                                    <div className="quick-action-label">View Progress</div>
                                    <div className="quick-action-desc">Track your improvement</div>
                                </div>
                                <ArrowRight size={14} className="quick-action-arrow" />
                            </Link>
                        </div>
                    </div>

                    {/* Skill Assessment CTA */}
                    <div className="dash-card dash-card-cta">
                        <div className="dash-cta-icon">
                            <Zap size={20} />
                        </div>
                        <h3 className="dash-cta-title">Test your skills</h3>
                        <p className="dash-cta-desc">
                            Take a skill assessment to find your level and get a personalised learning plan.
                        </p>
                        <Link to="/assessment" className="dash-cta-btn">
                            Take Assessment <ArrowRight size={13} />
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    );
}
