import { SKILL_LEVELS, type SkillLevel } from '../types';

interface Props {
    value: SkillLevel;
    onChange: (level: SkillLevel) => void;
}

export default function SkillLevelSelect({ value, onChange }: Props) {
    return (
        <div className="skill-level-row">
            <span className="skill-level-label">Level</span>
            <div className="skill-level-btns">
                {SKILL_LEVELS.map((l) => (
                    <button
                        key={l.value}
                        type="button"
                        onClick={() => onChange(l.value)}
                        className={`skill-level-btn lvl-${l.value}${value === l.value ? ' active' : ''}`}
                    >
                        {l.label}
                    </button>
                ))}
            </div>
        </div>
    );
}
