import { z } from 'zod';

export const profileSchema = z.object({
    current_role:     z.string().max(100, 'Max 100 characters'),
    experience_level: z.enum(['junior', 'mid', 'senior'], {
        error: 'Select your experience level',
    }),
    target_role:  z.string().min(1, 'Target role is required').max(100, 'Max 100 characters'),
    career_goal:  z.string(),
});

export type ProfileFormData = z.infer<typeof profileSchema>;
