import { Link } from 'react-router-dom';
import { CheckCircle2, ArrowRight, Target, BookOpen, TrendingUp } from 'lucide-react';
import type { DashboardSummary, DashboardProfile } from '../../../types/api';

export function OnboardingCard({
    summary,
    profile,
}: {
    summary: DashboardSummary;
    profile: DashboardProfile | null;
}) {
    const steps = [
        {
            label: 'Take your first quiz',
            desc: 'Find out which topics you already know',
            to: '/practice',
            Icon: Target,
            done: summary.quizzes_taken > 0,
        },
        {
            label: 'Complete a lesson',
            desc: 'Learn engineering concepts step by step',
            to: '/learning',
            Icon: BookOpen,
            done: summary.lessons_completed > 0,
        },
        {
            label: 'Set your career goal',
            desc: 'Tell us which engineering role you are targeting',
            to: '/profile',
            Icon: TrendingUp,
            done: !!profile?.target_role,
        },
    ];

    const doneCount = steps.filter(s => s.done).length;
    const allDone = doneCount === steps.length;

    return (
        <div className="onboard-card">
            <div className="onboard-header">
                <div>
                    <h3 className="onboard-title">{allDone ? 'Setup complete!' : 'Getting started'}</h3>
                    <p className="onboard-subtitle">
                        {allDone
                            ? 'All set — keep building your skills consistently.'
                            : 'Complete these steps to unlock your full learning journey.'}
                    </p>
                </div>
                <div className={`onboard-pill${allDone ? ' onboard-pill--done' : ''}`}>
                    {doneCount}/{steps.length}
                </div>
            </div>
            <div className="onboard-steps">
                {steps.map((step, i) => (
                    <Link key={i} to={step.to} className={`onboard-step${step.done ? ' onboard-step--done' : ''}`}>
                        <div className={`onboard-step-icon${step.done ? ' onboard-step-icon--done' : ''}`}>
                            {step.done ? <CheckCircle2 size={16} /> : <step.Icon size={16} />}
                        </div>
                        <div className="onboard-step-body">
                            <span className="onboard-step-label">{step.label}</span>
                            <span className="onboard-step-desc">{step.desc}</span>
                        </div>
                        {!step.done && <ArrowRight size={13} className="onboard-step-arrow" />}
                    </Link>
                ))}
            </div>
        </div>
    );
}
