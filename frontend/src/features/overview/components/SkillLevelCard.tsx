import { Shield } from 'lucide-react';
import type { DashboardSummary, DashboardUserSkill } from '../../../types/api';

const SKILL_LEVEL_NAMES = ['Beginner', 'Developing', 'Proficient', 'Advanced'];

export function SkillLevelCard({ summary, userSkills }: { summary: DashboardSummary; userSkills: DashboardUserSkill[] }) {
    return (
        <div className="dash-card">
            <div className="dash-card-header">
                <h2 className="dash-card-title">Your Level</h2>
                <Shield size={15} className="dash-skill-icon" />
            </div>
            <div className="skill-level-display">
                <div className="skill-level-label">{summary.skill_label}</div>
                <div className="skill-level-bar">
                    {SKILL_LEVEL_NAMES.map((_, i) => (
                        <div key={i} className={`skill-level-seg${i < summary.skill_level ? ' skill-level-seg--active' : ''}`} />
                    ))}
                </div>
                <div className="skill-level-names">
                    {SKILL_LEVEL_NAMES.map(n => <span key={n} className="skill-level-lbl">{n}</span>)}
                </div>
                <p className="skill-level-basis">
                    {summary.skill_level === 0
                        ? 'Complete quizzes to determine your level.'
                        : `Based on ${summary.quizzes_taken} quiz${summary.quizzes_taken === 1 ? '' : 'zes'} · ${summary.accuracy}% accuracy`}
                </p>
            </div>
            {userSkills.length > 0 && (
                <div className="skill-chips-wrap">
                    <div className="skill-chips-label">Assessed Skills</div>
                    <div className="skill-chips">
                        {userSkills.slice(0, 6).map(s => (
                            <span key={s.name} className="skill-chip">{s.name}</span>
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
}
