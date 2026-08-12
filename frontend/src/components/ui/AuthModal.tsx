import { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { X, Mail, Lock, User, AlertCircle, GraduationCap } from 'lucide-react';
import { loginSchema, registerSchema, type LoginFormData, type RegisterFormData } from '../../features/auth/schemas';
import { useLogin } from '../../features/auth/hooks/useLogin';
import { useRegister } from '../../features/auth/hooks/useRegister';
import './auth-modal.css';

type ModalMode = 'login' | 'register';

interface AuthModalProps {
    mode: ModalMode;
    onClose: () => void;
    onSwitch: (mode: ModalMode) => void;
}

function getApiError(error: unknown): string {
    const e = error as any;
    return e?.response?.data?.message ?? 'Something went wrong. Please try again.';
}

function isEmailTaken(error: unknown): boolean {
    const e = error as any;
    const emailErrors: string[] = e?.response?.data?.errors?.email ?? [];
    const message: string = e?.response?.data?.message ?? '';
    const combined = [...emailErrors, message].join(' ').toLowerCase();
    return combined.includes('taken') || combined.includes('already');
}

function LoginForm({ onSwitch }: { onSwitch: () => void }) {
    const { mutate: login, isPending, isError, error, reset } = useLogin();
    const { register, handleSubmit, formState: { errors } } = useForm<LoginFormData>({
        resolver: zodResolver(loginSchema),
    });

    const apiError = isError ? getApiError(error) : null;

    return (
        <form onSubmit={handleSubmit((data) => { reset(); login(data); })} noValidate>
            {apiError && (
                <div className="mf-alert" role="alert">
                    <AlertCircle size={15} />
                    <span>{apiError}</span>
                </div>
            )}

            <div className="mf">
                <label className="mf-label">Email address</label>
                <div className="mf-input-wrap">
                    <span className="mf-icon"><Mail size={15} /></span>
                    <input
                        type="email"
                        placeholder="you@example.com"
                        className={`mf-input${apiError ? ' mf-input-err' : ''}`}
                        {...register('email')}
                    />
                </div>
                {errors.email && <p className="mf-error">{errors.email.message}</p>}
            </div>

            <div className="mf">
                <label className="mf-label">Password</label>
                <div className="mf-input-wrap">
                    <span className="mf-icon"><Lock size={15} /></span>
                    <input
                        type="password"
                        placeholder="••••••••"
                        className={`mf-input${apiError ? ' mf-input-err' : ''}`}
                        {...register('password')}
                    />
                </div>
                {errors.password && <p className="mf-error">{errors.password.message}</p>}
            </div>

            <button type="submit" disabled={isPending} className="mf-btn">
                {isPending ? 'Signing in…' : 'Sign in'}
            </button>

            <p className="mf-footer">
                Don't have an account?{' '}
                <button type="button" className="mf-switch" onClick={onSwitch}>Create one</button>
            </p>
        </form>
    );
}

function RegisterForm({ onSwitch, onEmailExists }: { onSwitch: () => void; onEmailExists: () => void }) {
    const { mutate: register_, isPending, isError, error, reset } = useRegister();
    const { register, handleSubmit, formState: { errors } } = useForm<RegisterFormData>({
        resolver: zodResolver(registerSchema),
    });

    const apiError = isError ? getApiError(error) : null;
    const emailTaken = isError && isEmailTaken(error);

    useEffect(() => {
        if (emailTaken) onEmailExists();
    }, [emailTaken]);

    return (
        <form onSubmit={handleSubmit((data) => { reset(); register_(data); })} noValidate>
            {apiError && (
                <div className={`mf-alert${emailTaken ? ' mf-alert-warn' : ''}`} role="alert">
                    <AlertCircle size={15} />
                    <span>
                        {emailTaken ? 'This email is already registered.' : apiError}
                        {emailTaken && (
                            <>
                                {' '}
                                <button type="button" className="mf-alert-switch" onClick={onSwitch}>
                                    Sign in instead →
                                </button>
                            </>
                        )}
                    </span>
                </div>
            )}

            <div className="mf">
                <label className="mf-label">Full name</label>
                <div className="mf-input-wrap">
                    <span className="mf-icon"><User size={15} /></span>
                    <input type="text" placeholder="John Smith" className="mf-input" {...register('name')} />
                </div>
                {errors.name && <p className="mf-error">{errors.name.message}</p>}
            </div>

            <div className="mf">
                <label className="mf-label">Email address</label>
                <div className="mf-input-wrap">
                    <span className="mf-icon"><Mail size={15} /></span>
                    <input
                        type="email"
                        placeholder="you@example.com"
                        className={`mf-input${emailTaken ? ' mf-input-err' : ''}`}
                        {...register('email')}
                    />
                </div>
                {errors.email && <p className="mf-error">{errors.email.message}</p>}
            </div>

            <div className="mf">
                <label className="mf-label">Password</label>
                <div className="mf-input-wrap">
                    <span className="mf-icon"><Lock size={15} /></span>
                    <input type="password" placeholder="Min. 8 characters" className="mf-input" {...register('password')} />
                </div>
                {errors.password && <p className="mf-error">{errors.password.message}</p>}
            </div>

            <div className="mf">
                <label className="mf-label">Confirm password</label>
                <div className="mf-input-wrap">
                    <span className="mf-icon"><Lock size={15} /></span>
                    <input type="password" placeholder="Repeat your password" className="mf-input" {...register('password_confirmation')} />
                </div>
                {errors.password_confirmation && <p className="mf-error">{errors.password_confirmation.message}</p>}
            </div>

            <button type="submit" disabled={isPending} className="mf-btn">
                {isPending ? 'Creating account…' : 'Create account'}
            </button>

            <p className="mf-footer">
                Already have an account?{' '}
                <button type="button" className="mf-switch" onClick={onSwitch}>Sign in</button>
            </p>
        </form>
    );
}

export default function AuthModal({ mode, onClose, onSwitch }: AuthModalProps) {
    const [signInHighlight, setSignInHighlight] = useState(false);

    useEffect(() => {
        const handler = (e: KeyboardEvent) => { if (e.key === 'Escape') onClose(); };
        document.addEventListener('keydown', handler);
        return () => document.removeEventListener('keydown', handler);
    }, [onClose]);

    useEffect(() => {
        document.body.style.overflow = 'hidden';
        return () => { document.body.style.overflow = ''; };
    }, []);

    useEffect(() => { setSignInHighlight(false); }, [mode]);

    return (
        <div className="modal-backdrop" onClick={onClose}>
            <div
                className="modal-card"
                onClick={(e) => e.stopPropagation()}
                role="dialog"
                aria-modal="true"
            >
                <button className="modal-close" onClick={onClose} aria-label="Close">
                    <X size={16} />
                </button>

                <div className="modal-brand">
                    <div className="modal-brand-wordmark">
                        <span className="modal-brand-cap-letter">
                            <GraduationCap size={22} className="modal-brand-cap" />
                            <span className="modal-brand-letter">C</span>
                        </span>
                        <span className="modal-brand-rest">areerOS</span>
                    </div>
                </div>

                <div className="modal-tabs" role="tablist">
                    <button
                        role="tab"
                        aria-selected={mode === 'login'}
                        className={`modal-tab${mode === 'login' ? ' active' : ''}${signInHighlight && mode !== 'login' ? ' highlight' : ''}`}
                        onClick={() => onSwitch('login')}
                    >
                        Sign in
                    </button>
                    <button
                        role="tab"
                        aria-selected={mode === 'register'}
                        className={`modal-tab${mode === 'register' ? ' active' : ''}`}
                        onClick={() => onSwitch('register')}
                    >
                        Create account
                    </button>
                </div>

                {mode === 'login'
                    ? <LoginForm onSwitch={() => onSwitch('register')} />
                    : <RegisterForm
                        onSwitch={() => onSwitch('login')}
                        onEmailExists={() => setSignInHighlight(true)}
                      />
                }
            </div>
        </div>
    );
}
