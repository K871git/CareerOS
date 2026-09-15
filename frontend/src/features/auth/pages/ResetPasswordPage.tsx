import { useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Link, useSearchParams } from 'react-router-dom';
import { Lock, Eye, EyeOff, ArrowLeft, CheckCircle, AlertCircle } from 'lucide-react';
import { isAxiosError } from 'axios';
import AuthLayout from '../components/AuthLayout';
import { resetPasswordSchema, type ResetPasswordFormData } from '../schemas';
import { useResetPassword } from '../hooks/useResetPassword';

export default function ResetPasswordPage() {
    const [searchParams]                  = useSearchParams();
    const [showPassword,  setShowPw]      = useState(false);
    const [showConfirm,   setShowCfm]     = useState(false);
    const [done,          setDone]        = useState(false);

    const token = searchParams.get('token') ?? '';
    const email = searchParams.get('email') ?? '';

    const { mutate: resetPassword, isPending, isError, error } = useResetPassword();

    const { register, handleSubmit, formState: { errors } } = useForm<ResetPasswordFormData>({
        resolver: zodResolver(resetPasswordSchema),
    });

    const apiError = isError && isAxiosError(error)
        ? (error.response?.data?.message ?? 'Something went wrong. Please try again.')
        : null;

    if (!token || !email) {
        return (
            <AuthLayout title="Invalid link" subtitle="This password reset link is missing required parameters.">
                <div className="auth-success">
                    <p className="auth-success-text">
                        Please request a new password reset link from the sign-in page.
                    </p>
                    <Link to="/?modal=login" className="auth-back-link">
                        <ArrowLeft size={14} /> Back to sign in
                    </Link>
                </div>
            </AuthLayout>
        );
    }

    if (done) {
        return (
            <AuthLayout title="Password updated" subtitle="Your password has been reset successfully">
                <div className="auth-success">
                    <div className="auth-success-icon">
                        <CheckCircle size={52} color="#22c55e" strokeWidth={1.5} />
                    </div>
                    <p className="auth-success-text">
                        Your password has been changed. All existing sessions have been signed out.
                    </p>
                    <Link to="/?modal=login" className="auth-btn" style={{ display: 'block', textAlign: 'center', textDecoration: 'none' }}>
                        Sign in with new password
                    </Link>
                </div>
            </AuthLayout>
        );
    }

    return (
        <AuthLayout title="Choose a new password" subtitle="Enter your new password below">
            <form
                onSubmit={handleSubmit((data) =>
                    resetPassword(
                        { token, email, password: data.password, password_confirmation: data.password_confirmation },
                        { onSuccess: () => setDone(true) }
                    )
                )}
                noValidate
            >
                {apiError && (
                    <div className="auth-alert" role="alert">
                        <AlertCircle size={15} />
                        <span>{apiError}</span>
                    </div>
                )}

                <div className="auth-field">
                    <label className="auth-label">New password</label>
                    <div className="auth-input-wrap">
                        <span className="auth-icon"><Lock size={16} /></span>
                        <input
                            type={showPassword ? 'text' : 'password'}
                            placeholder="Min. 8 characters"
                            className="auth-input auth-input--eye"
                            {...register('password')}
                        />
                        <button
                            type="button"
                            className="auth-eye-btn"
                            onClick={() => setShowPw(p => !p)}
                            tabIndex={-1}
                            aria-label={showPassword ? 'Hide password' : 'Show password'}
                        >
                            {showPassword ? <EyeOff size={15} /> : <Eye size={15} />}
                        </button>
                    </div>
                    {errors.password && <p className="auth-error">{errors.password.message}</p>}
                </div>

                <div className="auth-field">
                    <label className="auth-label">Confirm new password</label>
                    <div className="auth-input-wrap">
                        <span className="auth-icon"><Lock size={16} /></span>
                        <input
                            type={showConfirm ? 'text' : 'password'}
                            placeholder="Repeat your new password"
                            className="auth-input auth-input--eye"
                            {...register('password_confirmation')}
                        />
                        <button
                            type="button"
                            className="auth-eye-btn"
                            onClick={() => setShowCfm(p => !p)}
                            tabIndex={-1}
                            aria-label={showConfirm ? 'Hide password' : 'Show password'}
                        >
                            {showConfirm ? <EyeOff size={15} /> : <Eye size={15} />}
                        </button>
                    </div>
                    {errors.password_confirmation && (
                        <p className="auth-error">{errors.password_confirmation.message}</p>
                    )}
                </div>

                <button type="submit" disabled={isPending} className="auth-btn">
                    {isPending ? 'Resetting…' : 'Reset password'}
                </button>

                <div className="auth-footer">
                    <Link to="/?modal=login" className="auth-back-link">
                        <ArrowLeft size={14} /> Back to sign in
                    </Link>
                </div>
            </form>
        </AuthLayout>
    );
}
