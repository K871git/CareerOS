import { User } from 'lucide-react';
import type { UserProfile } from '../../../types/api';
import type { AuthUser } from '../../../store/authStore';
import { LEVEL_LABELS } from '../types';

interface ProfileCardProps {
    profile: UserProfile | null;
    user: AuthUser | null;
}

export default function ProfileCard({ profile, user }: ProfileCardProps) {
    const initials = user?.name
        ? user.name
              .split(' ')
              .map((n) => n[0])
              .join('')
              .slice(0, 2)
              .toUpperCase()
        : '?';

    const levelLabel = profile ? LEVEL_LABELS[profile.experience_level] : null;

    return (
        <div className="profile-card">
            <div className="profile-avatar">{initials}</div>

            <div className="profile-card-col-right">
                <div className="profile-user-name">{user?.name ?? 'User'}</div>
                <div className="profile-user-email">{user?.email}</div>

                {profile ? (
                    <span className={`profile-level-badge ${profile.experience_level}`}>
                        {levelLabel}
                    </span>
                ) : (
                    <span className="profile-level-badge no-level">
                        <User size={11} />
                        No level set
                    </span>
                )}
            </div>

            <div className="profile-card-divider" />

            <div className="profile-meta-list">
                <div className="profile-meta-item">
                    <span className="profile-meta-label">Current Role</span>
                    {profile?.current_role ? (
                        <span className="profile-meta-value">{profile.current_role}</span>
                    ) : (
                        <span className="profile-meta-empty">Not set</span>
                    )}
                </div>

                <div className="profile-meta-item">
                    <span className="profile-meta-label">Target Role</span>
                    {profile?.target_role ? (
                        <span className="profile-meta-value">{profile.target_role}</span>
                    ) : (
                        <span className="profile-meta-empty">Not set</span>
                    )}
                </div>

                <div className="profile-meta-item">
                    <span className="profile-meta-label">Career Goal</span>
                    {profile?.career_goal ? (
                        <span className="profile-meta-value">{profile.career_goal}</span>
                    ) : (
                        <span className="profile-meta-empty">Not set</span>
                    )}
                </div>
            </div>
        </div>
    );
}
