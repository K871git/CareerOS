import { useState, useEffect } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Link } from 'react-router-dom';
import { User, Mail, Phone, Lock, ShieldCheck, FileText } from 'lucide-react';
import AuthLayout from '../components/AuthLayout';
import { registerSchema, type RegisterFormData } from '../schemas';
import { useRegister } from '../hooks/useRegister';
import '../../consent/consent.css';

const TERMS_KEY   = 'careeros_terms_read';
const PRIVACY_KEY = 'careeros_privacy_read';

function checkLegalFlags() {
    return !!localStorage.getItem(TERMS_KEY) && !!localStorage.getItem(PRIVACY_KEY);
}

export default function RegisterPage() {
    const { mutate: register_, isPending } = useRegister();

    const { register, handleSubmit, formState: { errors } } = useForm<RegisterFormData>({
        resolver: zodResolver(registerSchema),
    });

    const [dataConsent,   setDataConsent]   = useState(false);
    const [termsAccepted, setTermsAccepted] = useState(checkLegalFlags);

    // Auto-check the terms box when the user comes back after reading both legal pages
    useEffect(() => {
        const onFocus = () => {
            if (checkLegalFlags()) setTermsAccepted(true);
        };
        window.addEventListener('focus', onFocus);
        return () => window.removeEventListener('focus', onFocus);
    }, []);

    const canSubmit = !isPending && dataConsent && termsAccepted;

    const onSubmit = (data: RegisterFormData) => {
        if (!canSubmit) return;
        register_({ ...data, consent_accepted: true });
    };

    return (
        <AuthLayout title="Create your account" subtitle="Start your engineering career journey today">
            <form onSubmit={handleSubmit(onSubmit)} noValidate>

                <div className="auth-field">
                    <label className="auth-label">Full name</label>
                    <div className="auth-input-wrap">
                        <span className="auth-icon"><User size={16} /></span>
                        <input
                            type="text"
                            placeholder="John Smith"
                            className="auth-input"
                            {...register('name')}
                        />
                    </div>
                    {errors.name && <p className="auth-error">{errors.name.message}</p>}
                </div>

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

                <div className="auth-field">
                    <label className="auth-label">Mobile number</label>
                    <div className="auth-input-wrap">
                        <span className="auth-icon"><Phone size={16} /></span>
                        <input
                            type="tel"
                            placeholder="9876543210"
                            className="auth-input"
                            {...register('mobile')}
                        />
                    </div>
                    {errors.mobile && <p className="auth-error">{errors.mobile.message}</p>}
                </div>

                <div className="auth-field">
                    <label className="auth-label">Password</label>
                    <div className="auth-input-wrap">
                        <span className="auth-icon"><Lock size={16} /></span>
                        <input
                            type="password"
                            placeholder="Min. 8 characters"
                            className="auth-input"
                            {...register('password')}
                        />
                    </div>
                    {errors.password && <p className="auth-error">{errors.password.message}</p>}
                </div>

                <div className="auth-field">
                    <label className="auth-label">Confirm password</label>
                    <div className="auth-input-wrap">
                        <span className="auth-icon"><Lock size={16} /></span>
                        <input
                            type="password"
                            placeholder="Repeat your password"
                            className="auth-input"
                            {...register('password_confirmation')}
                        />
                    </div>
                    {errors.password_confirmation && <p className="auth-error">{errors.password_confirmation.message}</p>}
                </div>

                {/* ── Consent checkboxes (DPDP Act 2023) ─────────────────── */}
                <div className="reg-consent-block">

                    {/* Checkbox 1 — Data processing consent */}
                    <label className={`reg-consent-row ${dataConsent ? 'checked' : ''}`}>
                        <div className="reg-consent-check-wrap">
                            <input
                                type="checkbox"
                                className="reg-consent-checkbox"
                                checked={dataConsent}
                                onChange={e => setDataConsent(e.target.checked)}
                            />
                            {dataConsent && <span className="reg-consent-tick">✓</span>}
                        </div>
                        <div className="reg-consent-text">
                            <span className="reg-consent-icon"><ShieldCheck size={13} /></span>
                            I consent to CareerOS collecting and processing my personal data for account creation
                            and service delivery, in accordance with the <strong>DPDP Act, 2023</strong>.
                        </div>
                    </label>

                    {/* Checkbox 2 — Terms & Privacy read confirmation */}
                    <label className={`reg-consent-row ${termsAccepted ? 'checked' : ''}`}>
                        <div className="reg-consent-check-wrap">
                            <input
                                type="checkbox"
                                className="reg-consent-checkbox"
                                checked={termsAccepted}
                                onChange={e => setTermsAccepted(e.target.checked)}
                            />
                            {termsAccepted && <span className="reg-consent-tick">✓</span>}
                        </div>
                        <div className="reg-consent-text">
                            <span className="reg-consent-icon"><FileText size={13} /></span>
                            I have read and agree to the{' '}
                            <a href="/terms" target="_blank" rel="noopener noreferrer" className="reg-consent-link">
                                Terms &amp; Conditions
                            </a>
                            {' '}and{' '}
                            <a href="/privacy" target="_blank" rel="noopener noreferrer" className="reg-consent-link">
                                Privacy Policy
                            </a>
                            .{' '}
                            {!termsAccepted && (
                                <span className="reg-consent-hint">
                                    Open each link, read to the end, and click Accept.
                                </span>
                            )}
                        </div>
                    </label>

                </div>

                <button type="submit" disabled={!canSubmit} className="auth-btn">
                    {isPending ? 'Creating account…' : 'Create account →'}
                </button>

                <p className="auth-footer">
                    Already have an account?{' '}
                    <Link to="/auth/login" className="auth-link">Sign in</Link>
                </p>
            </form>
        </AuthLayout>
    );
}
