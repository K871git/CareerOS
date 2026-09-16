import { Award, Brain, BookOpen, Layers } from 'lucide-react';
import type { DashboardSummary } from '../../../types/api';
import { useCountUp } from '../../../hooks/useCountUp';

const CARDS = [
    {
        key:      'accuracy',
        label:    'Quiz Accuracy',
        icon:     Award,
        gradient: 'linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%)',
        glow:     'rgba(79,70,229,0.30)',
    },
    {
        key:      'questions',
        label:    'Questions Answered',
        icon:     Brain,
        gradient: 'linear-gradient(135deg,#0891b2 0%,#0e7490 100%)',
        glow:     'rgba(8,145,178,0.26)',
    },
    {
        key:      'lessons',
        label:    'Lessons Completed',
        icon:     BookOpen,
        gradient: 'linear-gradient(135deg,#059669 0%,#047857 100%)',
        glow:     'rgba(5,150,105,0.26)',
    },
    {
        key:      'levels',
        label:    'Levels Passed',
        icon:     Layers,
        gradient: 'linear-gradient(135deg,#d97706 0%,#b45309 100%)',
        glow:     'rgba(217,119,6,0.26)',
    },
] as const;

function ctxLabel(key: string, s: DashboardSummary): string {
    if (key === 'accuracy') {
        if (s.quizzes_taken === 0) return 'No quizzes yet';
        if (s.accuracy >= 80) return '🔥 Excellent!';
        if (s.accuracy >= 70) return '✓ Above target';
        if (s.accuracy >= 50) return '↗ Needs practice';
        return '⚠ Focus area';
    }
    if (key === 'questions') {
        if (s.total_questions_answered === 0) return 'Start practising';
        if (s.total_questions_answered >= 100) return 'Veteran level 🏆';
        if (s.total_questions_answered >= 50) return 'Great momentum';
        return 'Getting there';
    }
    if (key === 'lessons') {
        if (s.lessons_completed === 0) return 'Read your first lesson';
        if (s.lessons_completed >= 20) return 'Dedicated learner';
        return 'Keep reading';
    }
    if (key === 'levels') {
        if (s.learning_levels_passed === 0) return 'Score 10/10 to pass';
        return `${s.learning_levels_passed} level${s.learning_levels_passed > 1 ? 's' : ''} mastered`;
    }
    return '';
}

function rawValue(key: string, s: DashboardSummary): number {
    if (key === 'accuracy')  return s.quizzes_taken > 0 ? s.accuracy : 0;
    if (key === 'questions') return s.total_questions_answered;
    if (key === 'lessons')   return s.lessons_completed;
    if (key === 'levels')    return s.learning_levels_passed;
    return 0;
}

function StatCard({
    label, icon: Icon, gradient, glow, target, suffix, context, delay,
}: {
    label: string;
    icon: React.ElementType;
    gradient: string;
    glow: string;
    target: number;
    suffix: string;
    context: string;
    delay: number;
}) {
    const count = useCountUp(target, 1000);

    return (
        <div className="scard" style={{ '--scard-delay': `${delay}ms` } as React.CSSProperties}>
            <div className="scard-strip" style={{ background: gradient }} />
            <div className="scard-top">
                <div className="scard-icon" style={{ background: gradient, boxShadow: `0 4px 16px ${glow}` }}>
                    <Icon size={19} />
                </div>
            </div>
            <div className="scard-num">
                {target === 0 && suffix === '%' ? (
                    <span className="scard-dash">—</span>
                ) : (
                    <>{count}<span className="scard-suffix">{suffix}</span></>
                )}
            </div>
            <div className="scard-label">{label}</div>
            <div className="scard-ctx">{context}</div>
        </div>
    );
}

export function StatsRow({ summary }: { summary: DashboardSummary }) {
    return (
        <div className="dash-stats">
            {CARDS.map((c, i) => (
                <StatCard
                    key={c.key}
                    label={c.label}
                    icon={c.icon}
                    gradient={c.gradient}
                    glow={c.glow}
                    target={rawValue(c.key, summary)}
                    suffix={c.key === 'accuracy' ? '%' : ''}
                    context={ctxLabel(c.key, summary)}
                    delay={i * 90}
                />
            ))}
        </div>
    );
}
