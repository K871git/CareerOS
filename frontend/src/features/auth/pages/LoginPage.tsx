import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Link } from 'react-router-dom';
import { Mail, Lock } from 'lucide-react';
import AuthLayout from '../components/AuthLayout';
import { loginSchema, type LoginFormData } from '../schemas';
import { useLogin } from '../hooks/useLogin';

export default function LoginPage() {
    const { mutate: login, isPending } = useLogin();

    const { register, handleSubmit, formState: { errors } } = useForm<LoginFormData>({
        resolver: zodResolver(loginSchema),
    });

    return (
        <AuthLayout title="Welcome back" subtitle="Sign in to continue your learning journey">
            <form onSubmit={handleSubmit((data) => login(data))} noValidate>
                <div className="auth-field">
                    <label className="auth-label">Email address</label>
                    <div className="auth-input-wrap">
                        <span className="auth-icon"><Mail size={16} /></span>
                        <input type="email" placeholder="you@example.com" className="auth-input" {...register('email')} />
                    </div>
                    {errors.email && <p className="auth-error">{errors.email.message}</p>}
                </div>

                <div className="auth-field">
                    <label className="auth-label">Password</label>
                    <div className="auth-input-wrap">
                        <span className="auth-icon"><Lock size={16} /></span>
                        <input type="password" placeholder="••••••••" className="auth-input" {...register('password')} />
                    </div>
                    {errors.password && <p className="auth-error">{errors.password.message}</p>}
                </div>

                <div className="auth-forgot">
                    <Link to="/auth/forgot-password" className="auth-link">Forgot password?</Link>
                </div>

                <button type="submit" disabled={isPending} className="auth-btn">
                    {isPending ? 'Signing in…' : 'Sign in'}
                </button>

                <p className="auth-footer">
                    Don't have an account?{' '}
                    <Link to="/auth/register" className="auth-link">Create account</Link>
                </p>
            </form>
        </AuthLayout>
    );
}
