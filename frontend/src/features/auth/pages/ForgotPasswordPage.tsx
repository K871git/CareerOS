import { useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Link } from 'react-router-dom';
import { Mail, ArrowLeft, CheckCircle, AlertCircle } from 'lucide-react';
import { isAxiosError } from 'axios';
import AuthLayout from '../components/AuthLayout';
import { forgotPasswordSchema, type ForgotPasswordFormData } from '../schemas';
import { useForgotPassword } from '../hooks/useForgotPassword';

export default function ForgotPasswordPage() {
    const [submitted, setSubmitted] = useState(false);
    const { mutate: sendResetLink, isPending, isError, error } = useForgotPassword();

    const { register, handleSubmit, formState: { errors } } = useForm<ForgotPasswordFormData>({
        resolver: zodResolver(forgotPasswordSchema),
    });

    const apiError = isError && isAxiosError(error)
        ? (error.response?.data?.message ?? 'Something went wrong. Please try again.')
        : null;

    if (submitted) {
        return (
            <AuthLayout title="Check your inbox" subtitle="Reset instructions sent if that email is registered">
                <div className="auth-success">
                    <div className="auth-success-icon">
                        <CheckCircle size={52} color="#22c55e" strokeWidth={1.5} />
                    </div>
                    <p className="auth-success-text">
                        If that email address is registered, you'll receive password reset instructions shortly. Check your spam folder if you don't see it.
                    </p>
                    <Link to="/?modal=login" className="auth-back-link">
                        <ArrowLeft size={14} /> Back to sign in
                    </Link>
                </div>
            </AuthLayout>
        );
    }

    return (
        <AuthLayout title="Reset your password" subtitle="Enter your email and we'll send you a reset link">
            <form
                onSubmit={handleSubmit((data) =>
                    sendResetLink(data.email, { onSuccess: () => setSubmitted(true) })
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
                    <label className="auth-label">Email address</label>
                    <div className="auth-input-wrap">
                        <span className="auth-icon"><Mail size={16} /></span>
                        <input
                            type="email"
                            placeholder="you@example.com"
                            className="auth-input"
                            {...register('email')}
                        />
                    </div>
                    {errors.email && <p className="auth-error">{errors.email.message}</p>}
                </div>

                <button type="submit" disabled={isPending} className="auth-btn">
                    {isPending ? 'Sending…' : 'Send reset link'}
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
