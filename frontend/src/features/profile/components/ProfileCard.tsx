import { User, Briefcase, Target, Lightbulb, type LucideIcon } from 'lucide-react';
import type { UserProfile } from '../../../types/api';
import type { AuthUser } from '../../../store/authStore';
import { LEVEL_LABELS } from '../types';

interface ProfileCardProps {
    profile: UserProfile | null;
    user: AuthUser | null;
}

function profileCompleteness(profile: UserProfile | null): { pct: number; remaining: number } {
    if (!profile) return { pct: 0, remaining: 4 };
    const checks = [
        !!profile.experience_level,
        !!profile.current_role,
        !!profile.target_role,
        !!profile.career_goal,
    ];
    const filled = checks.filter(Boolean).length;
    return {
        pct: Math.round((filled / checks.length) * 100),
        remaining: checks.length - filled,
    };
}

function pctColor(pct: number): string {
    if (pct === 100) return '#10b981';
    if (pct >= 75)   return '#6366f1';
    if (pct >= 50)   return '#f59e0b';
    return '#ef4444';
}

export default function ProfileCard({ profile, user }: ProfileCardProps) {
    const initials = user?.name
        ? user.name.split(' ').map((n) => n[0]).join('').slice(0, 2).toUpperCase()
        : '?';

    const levelLabel = profile ? LEVEL_LABELS[profile.experience_level] : null;
    const { pct, remaining } = profileCompleteness(profile);
    const barColor = pctColor(pct);

    const meta: Array<{ label: string; value: string | null | undefined; Icon: LucideIcon }> = [
        { label: 'Current Role', value: profile?.current_role, Icon: Briefcase },
        { label: 'Target Role',  value: profile?.target_role,  Icon: Target    },
        { label: 'Career Goal',  value: profile?.career_goal,  Icon: Lightbulb },
    ];

    return (
        <div className="profile-card">
            {/* Gradient banner */}
            <div className="profile-card-banner">
                <div className="profile-card-banner-glow" />
            </div>

            <div className="profile-card-body">
                {/* Avatar — overlaps banner */}
                <div className="profile-avatar-wrap">
                    <div className="profile-avatar">{initials}</div>
                </div>

                {/* Identity */}
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

                {/* Completeness */}
                <div className="profile-completeness">
                    <div className="profile-completeness-header">
                        <span className="profile-completeness-label">Profile Completeness</span>
                        <span className="profile-completeness-pct" style={{ color: barColor }}>{pct}%</span>
                    </div>
                    <div className="profile-completeness-bar">
                        <div
                            className="profile-completeness-fill"
                            style={{ width: `${pct}%`, background: pct === 100 ? 'linear-gradient(90deg,#10b981,#34d399)' : undefined }}
                        />
                    </div>
                    {remaining > 0 && (
                        <p className="profile-completeness-hint">
                            {remaining} field{remaining !== 1 ? 's' : ''} left to complete your profile
                        </p>
                    )}
                    {pct === 100 && (
                        <p className="profile-completeness-hint profile-completeness-hint--done">
                            Profile complete
                        </p>
                    )}
                </div>

                <div className="profile-card-divider" />

                {/* Meta */}
                <div className="profile-meta-list">
                    {meta.map(({ label, value, Icon }) => (
                        <div key={label} className="profile-meta-item">
                            <div className="profile-meta-label-row">
                                <span className="profile-meta-icon"><Icon size={12} /></span>
                                <span className="profile-meta-label">{label}</span>
                            </div>
                            {value ? (
                                <span className="profile-meta-value">{value}</span>
                            ) : (
                                <span className="profile-meta-empty">Not set</span>
                            )}
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
}
