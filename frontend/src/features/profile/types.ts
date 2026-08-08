import type { ExperienceLevel } from '../../types/api';

export interface ExperienceLevelOption {
    value: ExperienceLevel;
    label: string;
}

export const EXPERIENCE_LEVELS: ExperienceLevelOption[] = [
    { value: 'junior', label: 'Junior (0–2 years)' },
    { value: 'mid',    label: 'Mid-Level (2–5 years)' },
    { value: 'senior', label: 'Senior (5+ years)' },
];

export const LEVEL_LABELS: Record<ExperienceLevel, string> = {
    junior: 'Junior',
    mid:    'Mid-Level',
    senior: 'Senior',
};
