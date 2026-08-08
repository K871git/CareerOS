import { Check } from 'lucide-react';
import type { Skill } from '../../../types/api';
import type { SelectedSkill, SkillLevel } from '../types';
import SkillLevelSelect from './SkillLevelSelect';
import ScoreSlider from './ScoreSlider';

interface Props {
    skill: Skill;
    selected: boolean;
    selectedData?: SelectedSkill;
    onToggle: () => void;
    onUpdateLevel: (level: SkillLevel) => void;
    onUpdateScore: (score: number) => void;
}

export default function SkillCard({
    skill,
    selected,
    selectedData,
    onToggle,
    onUpdateLevel,
    onUpdateScore,
}: Props) {
    return (
        <div className={`skill-card${selected ? ' skill-card--selected' : ''}`}>
            <button
                type="button"
                className="skill-card-header"
                onClick={onToggle}
                aria-pressed={selected}
            >
                <span className="skill-card-name">{skill.name}</span>
                <div className={`skill-card-check${selected ? ' skill-card-check--on' : ''}`}>
                    {selected && <Check size={11} strokeWidth={3} />}
                </div>
            </button>

            {selected && selectedData && (
                <div className="skill-card-body">
                    <SkillLevelSelect value={selectedData.level} onChange={onUpdateLevel} />
                    <ScoreSlider value={selectedData.score} onChange={onUpdateScore} />
                </div>
            )}
        </div>
    );
}
