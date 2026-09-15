import { Link } from 'react-router-dom';
import { Play, ArrowRight } from 'lucide-react';
import type { DashboardSummary, DashboardProfile } from '../../../types/api';

export type UserJourneyState = 'new' | 'starter' | 'learner' | 'achiever' | 'advanced';

export interface WelcomeConfig {
    title: string;
    subtitle: string;
    cta: { label: string; to: string };
}

export function getUserJourneyState(summary: DashboardSummary): UserJourneyState {
    const { quizzes_taken, lessons_completed, learning_levels_passed, accuracy } = summary;
    if (quizzes_taken === 0 && lessons_completed === 0) return 'new';
    if (learning_levels_passed >= 2 && accuracy >= 80) return 'advanced';
    if (learning_levels_passed >= 1 || lessons_completed >= 5) return 'achiever';
    if (quizzes_taken < 5) return 'starter';
    return 'learner';
}

export function getWelcomeConfig(
    state: UserJourneyState,
    firstName: string,
    summary: DashboardSummary,
    profile: DashboardProfile | null,
    weakCount: number,
): WelcomeConfig {
    const role = profile?.target_role;
    switch (state) {
        case 'new':
            return {
                title: `Welcome to CareerOS, ${firstName}!`,
                subtitle: 'Start your journey — take your first quiz to see where you stand as an engineer.',
                cta: { label: 'Take First Quiz', to: '/practice' },
            };
        case 'starter':
            return {
                title: `Good start, ${firstName}!`,
                subtitle: `${summary.quizzes_taken} quiz${summary.quizzes_taken !== 1 ? 'zes' : ''} done · ${summary.accuracy}% accuracy${role ? ` · Targeting ${role}` : ''}. Keep the momentum going.`,
                cta: { label: 'Practice More', to: '/practice' },
            };
        case 'learner':
            return {
                title: `Keep building, ${firstName}!`,
                subtitle: role
                    ? `Preparing for ${role} · ${weakCount > 0 ? `${weakCount} area${weakCount > 1 ? 's' : ''} to sharpen` : 'All topics above 70%'}`
                    : `${summary.lessons_completed} lessons · ${summary.quizzes_taken} quizzes · ${summary.accuracy}% accuracy. Consistency wins.`,
                cta: { label: 'Continue Learning', to: '/learning' },
            };
        case 'achiever':
            return {
                title: `Great progress, ${firstName}!`,
                subtitle: `${summary.learning_levels_passed} level${summary.learning_levels_passed !== 1 ? 's' : ''} passed · ${summary.accuracy}% accuracy · ${summary.lessons_completed} lessons. You're ahead of the curve.`,
                cta: { label: 'Next Level', to: '/learning' },
            };
        case 'advanced':
            return {
                title: `Outstanding, ${firstName}!`,
                subtitle: `${summary.accuracy}% accuracy across ${summary.quizzes_taken} quizzes · ${summary.learning_levels_passed} levels mastered. Keep pushing.`,
                cta: { label: 'Push Further', to: '/practice' },
            };
    }
}

export function WelcomeBanner({
    journeyState,
    config,
    profile,
}: {
    journeyState: UserJourneyState;
    config: WelcomeConfig;
    profile: DashboardProfile | null;
}) {
    return (
        <div className={`dash-welcome dash-welcome--${journeyState}`}>
            <div className="dash-welcome-content">
                <div className="dash-welcome-top">
                    <h1 className="dash-welcome-title">{config.title}</h1>
                    {profile?.experience_level && (
                        <span className="dash-exp-badge">{profile.experience_level}</span>
                    )}
                </div>
                <p className="dash-welcome-sub">{config.subtitle}</p>
            </div>
            <Link to={config.cta.to} className="dash-continue-btn">
                <Play size={13} /> {config.cta.label} <ArrowRight size={13} />
            </Link>
        </div>
    );
}
