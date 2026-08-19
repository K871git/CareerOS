import { z } from 'zod';

const mobileValidator = z
    .string()
    .min(10, 'Enter a valid mobile number')
    .max(15, 'Enter a valid mobile number')
    .regex(/^\+?[0-9]+$/, 'Enter a valid mobile number');

export const loginSchema = z.object({
    email:    z.string().email('Enter a valid email address'),
    password: z.string().min(8, 'Password must be at least 8 characters'),
});

export const registerSchema = z
    .object({
        name:                  z.string().min(2, 'Name must be at least 2 characters'),
        email:                 z.string().email('Enter a valid email address'),
        mobile:                mobileValidator,
        password:              z.string().min(8, 'Password must be at least 8 characters'),
        password_confirmation: z.string(),
    })
    .refine((data) => data.password === data.password_confirmation, {
        message: 'Passwords do not match',
        path:    ['password_confirmation'],
    });

export const sendOtpSchema = z.object({
    email: z.string().email('Enter a valid email address'),
});

export const verifyOtpSchema = z.object({
    code: z.string().length(6, 'OTP must be 6 digits').regex(/^\d+$/, 'Digits only'),
});

export const forgotPasswordSchema = z.object({
    email: z.string().email('Enter a valid email address'),
});

export type LoginFormData          = z.infer<typeof loginSchema>;
export type RegisterFormData       = z.infer<typeof registerSchema>;
export type SendOtpFormData        = z.infer<typeof sendOtpSchema>;
export type VerifyOtpFormData      = z.infer<typeof verifyOtpSchema>;
export type ForgotPasswordFormData = z.infer<typeof forgotPasswordSchema>;
