import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Link } from 'react-router-dom';
import { User, Mail, Lock } from 'lucide-react';
import AuthLayout from '../components/AuthLayout';
import { registerSchema, type RegisterFormData } from '../schemas';
import { useRegister } from '../hooks/useRegister';

export default function RegisterPage() {
    const { mutate: register_, isPending } = useRegister();

    const { register, handleSubmit, formState: { errors } } = useForm<RegisterFormData>({
        resolver: zodResolver(registerSchema),
    });

    return (
        <AuthLayout title="Create your account" subtitle="Start your engineering career journey today">
            <form onSubmit={handleSubmit((data) => register_(data))} noValidate>
                <div className="auth-field">
                    <label className="auth-label">Full name</label>
                    <div className="auth-input-wrap">
                        <span className="auth-icon"><User size={16} /></span>
                        <input type="text" placeholder="John Smith" className="auth-input" {...register('name')} />
                    </div>
                    {errors.name && <p className="auth-error">{errors.name.message}</p>}
                </div>

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
                        <input type="password" placeholder="Min. 8 characters" className="auth-input" {...register('password')} />
                    </div>
                    {errors.password && <p className="auth-error">{errors.password.message}</p>}
                </div>

                <div className="auth-field">
                    <label className="auth-label">Confirm password</label>
                    <div className="auth-input-wrap">
                        <span className="auth-icon"><Lock size={16} /></span>
                        <input type="password" placeholder="Repeat your password" className="auth-input" {...register('password_confirmation')} />
                    </div>
                    {errors.password_confirmation && <p className="auth-error">{errors.password_confirmation.message}</p>}
                </div>

                <button type="submit" disabled={isPending} className="auth-btn">
                    {isPending ? 'Creating account…' : 'Create account'}
                </button>

                <p className="auth-footer">
                    Already have an account?{' '}
                    <Link to="/auth/login" className="auth-link">Sign in</Link>
                </p>
            </form>
        </AuthLayout>
    );
}
