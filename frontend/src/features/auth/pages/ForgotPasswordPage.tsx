import { useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Link } from 'react-router-dom';
import { Mail, ArrowLeft, CheckCircle } from 'lucide-react';
import AuthLayout from '../components/AuthLayout';
import { forgotPasswordSchema, type ForgotPasswordFormData } from '../schemas';

export default function ForgotPasswordPage() {
    const [submitted, setSubmitted] = useState(false);

    const { register, handleSubmit, formState: { errors } } = useForm<ForgotPasswordFormData>({
        resolver: zodResolver(forgotPasswordSchema),
    });

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
                    <Link to="/auth/login" className="auth-back-link">
                        <ArrowLeft size={14} /> Back to sign in
                    </Link>
                </div>
            </AuthLayout>
        );
    }

    return (
        <AuthLayout title="Reset your password" subtitle="Enter your email and we'll send you a reset link">
            <form onSubmit={handleSubmit(() => setSubmitted(true))} noValidate>
                <div className="auth-field">
                    <label className="auth-label">Email address</label>
                    <div className="auth-input-wrap">
                        <span className="auth-icon"><Mail size={16} /></span>
                        <input type="email" placeholder="you@example.com" className="auth-input" {...register('email')} />
                    </div>
                    {errors.email && <p className="auth-error">{errors.email.message}</p>}
                </div>

                <button type="submit" className="auth-btn">
                    Send reset link
                </button>

                <div className="auth-footer">
                    <Link to="/auth/login" className="auth-back-link">
                        <ArrowLeft size={14} /> Back to sign in
                    </Link>
                </div>
            </form>
        </AuthLayout>
    );
}
