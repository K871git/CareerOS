import { useAuth } from '../../../store/authStore';
import { useProfile } from '../hooks/useProfile';
import ProfileForm from '../components/ProfileForm';
import PageHeader from '../../../components/layout/PageHeader';
import { User, Mail, CheckCircle2 } from 'lucide-react';
import { LEVEL_LABELS } from '../types';
import type { UserProfile } from '../../../types/api';
import '../settings.css';

function profileCompleteness(profile: UserProfile | null): number {
    if (!profile) return 0;
    const checks = [
        !!profile.experience_level,
        !!profile.current_role,
        !!profile.target_role,
        !!profile.career_goal,
    ];
    return Math.round((checks.filter(Boolean).length / checks.length) * 100);
}

function SettingsSkeleton() {
    return (
        <div className="settings-content">
            <div className="settings-identity skeleton-block" style={{ height: 120 }} />
            <div className="skeleton-block" style={{ height: 22, width: 140, margin: '2rem 0 1rem' }} />
            <div className="skeleton-block" style={{ height: 280 }} />
            <div className="skeleton-block" style={{ height: 22, width: 100, margin: '2rem 0 1rem' }} />
            <div className="skeleton-block" style={{ height: 140 }} />
        </div>
    );
}

export default function SettingsPage() {
    const { state } = useAuth();
    const { data: profile, isLoading } = useProfile();

    const initials = state.user?.name
        ? state.user.name.split(' ').map((n) => n[0]).join('').slice(0, 2).toUpperCase()
        : '?';

    const levelLabel = profile ? LEVEL_LABELS[profile.experience_level] : null;
    const pct = profileCompleteness(profile);

    return (
        <div className="settings-page">
            <PageHeader
                title="Settings"
                description="Manage your profile, career goals, and account"
            />

            {isLoading ? (
                <SettingsSkeleton />
            ) : (
                <div className="settings-content">

                    {/* ── Identity block ───────────────────────────────── */}
                    <div className="settings-identity">
                        <div className="settings-avatar">{initials}</div>
                        <div className="settings-identity-body">
                            <div className="settings-identity-top">
                                <div className="settings-identity-text">
                                    <div className="settings-user-name">{state.user?.name ?? 'User'}</div>
                                    <div className="settings-user-email">{state.user?.email}</div>
                                </div>
                                {profile ? (
                                    <span className={`settings-level-badge settings-level-badge--${profile.experience_level}`}>
                                        {levelLabel}
                                    </span>
                                ) : (
                                    <span className="settings-level-badge settings-level-badge--none">
                                        <User size={11} />
                                        No level set
                                    </span>
                                )}
                            </div>
                            <div className="settings-completeness">
                                <div className="settings-completeness-row">
                                    <span className="settings-completeness-label">Profile completeness</span>
                                    <span className="settings-completeness-pct">{pct}%</span>
                                </div>
                                <div className="settings-completeness-track">
                                    <div className="settings-completeness-fill" style={{ width: `${pct}%` }} />
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* ── Career Profile ───────────────────────────────── */}
                    <div className="settings-section-head">
                        <h2 className="settings-section-title">Career Profile</h2>
                        <p className="settings-section-desc">
                            Your experience level and goals used for personalised learning recommendations.
                        </p>
                    </div>

                    <ProfileForm profile={profile ?? null} />

                    {/* ── Account ──────────────────────────────────────── */}
                    <div className="settings-section-head">
                        <h2 className="settings-section-title">Account</h2>
                        <p className="settings-section-desc">
                            Manage your account details and session.
                        </p>
                    </div>

                    <div className="settings-account-card">
                        <div className="settings-account-row">
                            <div className="settings-account-icon">
                                <Mail size={15} />
                            </div>
                            <div className="settings-account-field">
                                <span className="settings-account-label">Email address</span>
                                <span className="settings-account-value">{state.user?.email}</span>
                            </div>
                            <span className="settings-readonly-pill">
                                <CheckCircle2 size={11} />
                                Verified
                            </span>
                        </div>

                    </div>

                </div>
            )}
        </div>
    );
}
