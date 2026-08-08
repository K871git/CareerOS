import type { Skill } from '../../../types/api';
import type { SelectedSkill, SkillLevel } from '../types';
import SkillCard from './SkillCard';

interface Props {
    skills: Skill[];
    selectedSkills: SelectedSkill[];
    onToggle: (skill: Skill) => void;
    onUpdateLevel: (skillId: number, level: SkillLevel) => void;
    onUpdateScore: (skillId: number, score: number) => void;
}

export default function SkillSelector({
    skills,
    selectedSkills,
    onToggle,
    onUpdateLevel,
    onUpdateScore,
}: Props) {
    const grouped = skills.reduce<Record<string, Skill[]>>((acc, skill) => {
        (acc[skill.category] ??= []).push(skill);
        return acc;
    }, {});

    return (
        <div className="skill-selector">
            {Object.entries(grouped).map(([category, catSkills]) => (
                <div key={category} className="skill-category-group">
                    <p className="skill-category-label">{category}</p>
                    <div className="skill-grid">
                        {catSkills.map((skill) => {
                            const sel = selectedSkills.find((s) => s.skill_id === skill.id);
                            return (
                                <SkillCard
                                    key={skill.id}
                                    skill={skill}
                                    selected={!!sel}
                                    selectedData={sel}
                                    onToggle={() => onToggle(skill)}
                                    onUpdateLevel={(level) => onUpdateLevel(skill.id, level)}
                                    onUpdateScore={(score) => onUpdateScore(skill.id, score)}
                                />
                            );
                        })}
                    </div>
                </div>
            ))}
        </div>
    );
}
