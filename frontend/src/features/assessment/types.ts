export type SkillLevel = 'beginner' | 'intermediate' | 'advanced' | 'expert';

export const SKILL_LEVELS: { value: SkillLevel; label: string }[] = [
    { value: 'beginner',     label: 'Beginner'     },
    { value: 'intermediate', label: 'Intermediate'  },
    { value: 'advanced',     label: 'Advanced'      },
    { value: 'expert',       label: 'Expert'        },
];

export interface SelectedSkill {
    skill_id:   number;
    skill_name: string;
    category:   string;
    level:      SkillLevel;
    score:      number;
}
