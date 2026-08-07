import { z } from 'zod';

export const targetRoleSchema = z
    .string()
    .min(1, 'Target role is required')
    .max(100, 'Max 100 characters');

export type TargetRoleValue = z.infer<typeof targetRoleSchema>;
