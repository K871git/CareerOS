import { Award, Target, BookOpen, Star, Brain, Shield, Zap, Flame, Lock } from 'lucide-react';
import type { DashboardSummary, WeakArea, QuizBySubject } from '../../../types/api';

export interface BadgeDef {
    id: string;
    label: string;
    desc: string;
    Icon: React.ElementType;
    color: 'indigo' | 'green' | 'amber' | 'violet';
    earned: boolean;
}

export function buildBadges(
    summary: DashboardSummary,
    weakAreas: WeakArea[],
    subjects: QuizBySubject[],
): BadgeDef[] {
    return [
        { id: 'first_quiz',     label: 'First Quiz',     desc: 'Completed first practice quiz',        Icon: Target,    color: 'indigo', earned: summary.quizzes_taken >= 1 },
        { id: 'first_lesson',   label: 'First Lesson',   desc: 'Completed first lesson',               Icon: BookOpen,  color: 'green',  earned: summary.lessons_completed >= 1 },
        { id: 'level_champ',    label: 'Level Champion', desc: 'Passed a level exam with 10/10',       Icon: Star,      color: 'amber',  earned: summary.learning_levels_passed >= 1 },
        { id: 'sharp_mind',     label: 'Sharp Mind',     desc: '80%+ accuracy across 5+ quizzes',     Icon: Brain,     color: 'violet', earned: summary.accuracy >= 80 && summary.quizzes_taken >= 5 },
        { id: 'veteran',        label: 'Veteran',        desc: 'Answered 50+ questions in practice',  Icon: Award,     color: 'amber',  earned: summary.total_questions_answered >= 50 },
        { id: 'no_weak_spots',  label: 'No Weak Spots',  desc: 'Zero weak areas with 5+ quizzes',     Icon: Shield,    color: 'green',  earned: weakAreas.length === 0 && summary.quizzes_taken >= 5 },
        { id: 'explorer',       label: 'Explorer',       desc: 'Practiced 3+ different subjects',     Icon: Zap,       color: 'indigo', earned: subjects.length >= 3 },
        { id: 'dedicated',      label: 'Dedicated',      desc: '10+ lessons completed',               Icon: Flame,     color: 'violet', earned: summary.lessons_completed >= 10 },
    ];
}

function BadgePill({ badge, locked }: { badge: BadgeDef; locked?: boolean }) {
    const { Icon } = badge;
    return (
        <div
            className={`badge-pill badge-pill--${badge.color}${locked ? ' badge-pill--locked' : ''}`}
            title={locked ? `Locked: ${badge.desc}` : badge.desc}
        >
            <div className="badge-pill-icon">
                {locked ? <Lock size={10} /> : <Icon size={10} />}
            </div>
            <span className="badge-pill-label">{badge.label}</span>
        </div>
    );
}

export function AchievementsCard({ badges }: { badges: BadgeDef[] }) {
    const earned = badges.filter(b => b.earned);
    const locked = badges.filter(b => !b.earned);
    const pct = Math.round((earned.length / badges.length) * 100);

    return (
        <div className="dash-card">
            <div className="dash-card-header">
                <h2 className="dash-card-title">
                    Achievements
                    {earned.length > 0 && <span className="achieve-count">{earned.length}</span>}
                </h2>
                <Award size={15} className="achieve-trophy-icon" />
            </div>

            {earned.length === 0 && (
                <p className="achieve-empty-hint">Complete quizzes and lessons to earn your first badge.</p>
            )}

            {earned.length > 0 && (
                <div className="achieve-section-wrap">
                    <div className="badge-grid">
                        {earned.map(b => <BadgePill key={b.id} badge={b} />)}
                    </div>
                </div>
            )}

            {locked.length > 0 && (
                <div className="achieve-section-wrap">
                    <div className="achieve-sublabel">Upcoming</div>
                    <div className="badge-grid">
                        {locked.slice(0, earned.length === 0 ? 4 : 3).map(b => <BadgePill key={b.id} badge={b} locked />)}
                    </div>
                </div>
            )}

            {locked.length === 0 && earned.length > 0 && (
                <p className="achieve-complete-msg">All badges earned. Excellent work!</p>
            )}

            <div className="achieve-footer">
                <div className="achieve-bar">
                    <div className="achieve-bar-fill" style={{ width: `${pct}%` }} />
                </div>
                <span className="achieve-bar-label">{earned.length}/{badges.length}</span>
            </div>
        </div>
    );
}
