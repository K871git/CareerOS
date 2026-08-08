import { useAuth } from '../../../store/authStore';
import { useProfile } from '../hooks/useProfile';
import PageHeader from '../../../components/layout/PageHeader';
import ProfileCard from '../components/ProfileCard';
import ProfileForm from '../components/ProfileForm';
import '../profile.css';

function ProfileSkeleton() {
    return (
        <div className="profile-grid">
            <div className="profile-skeleton-card">
                <div className="skeleton profile-skeleton-avatar" />
                <div className="skeleton skeleton-block" style={{ width: '60%', margin: '0 auto 0.5rem' }} />
                <div className="skeleton skeleton-block" style={{ width: '80%', margin: '0 auto 1rem' }} />
                <div className="skeleton skeleton-block" style={{ width: '50%', margin: '0 auto 1.5rem', height: 24, borderRadius: 999 }} />
                {[0, 1, 2].map((i) => (
                    <div key={i} className="skeleton skeleton-block" style={{ height: 48, marginBottom: 8 }} />
                ))}
            </div>
            <div className="profile-skeleton-card">
                <div className="skeleton skeleton-block" style={{ width: '40%', height: 22, marginBottom: '0.5rem' }} />
                <div className="skeleton skeleton-block" style={{ width: '70%', marginBottom: '1.75rem' }} />
                {[0, 1, 2, 3].map((i) => (
                    <div key={i} className="skeleton skeleton-block" style={{ height: 42, marginBottom: '1.25rem' }} />
                ))}
            </div>
        </div>
    );
}

export default function ProfilePage() {
    const { state } = useAuth();
    const { data: profile, isLoading } = useProfile();

    return (
        <div className="profile-page">
            <PageHeader
                title="Profile"
                description="Manage your career profile and learning preferences"
            />

            {isLoading ? (
                <ProfileSkeleton />
            ) : (
                <div className="profile-grid">
                    <ProfileCard profile={profile ?? null} user={state.user} />
                    <ProfileForm profile={profile ?? null} />
                </div>
            )}
        </div>
    );
}
