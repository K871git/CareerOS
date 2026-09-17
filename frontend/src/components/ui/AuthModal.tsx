import { useCallback, useEffect, useRef, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { X, Mail, Lock, User, Phone, KeyRound, AlertCircle, ArrowRight, LogIn, Eye, EyeOff, ShieldCheck, FileText, GraduationCap } from 'lucide-react';
import '../../features/consent/consent.css';
import { isAxiosError } from 'axios';
import toast from 'react-hot-toast';
import {
    loginSchema, registerSchema, sendOtpSchema, verifyOtpSchema,
    type LoginFormData, type RegisterFormData, type SendOtpFormData, type VerifyOtpFormData,
} from '../../features/auth/schemas';
import { useLogin }      from '../../features/auth/hooks/useLogin';
import { useRegister }   from '../../features/auth/hooks/useRegister';
import { useSendOtp }    from '../../features/auth/hooks/useSendOtp';
import { useVerifyOtp }  from '../../features/auth/hooks/useVerifyOtp';
import './auth-modal.css';

// ── Icons ──────────────────────────────────────────────────────────────────────

const GoogleIcon = () => (
    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
    </svg>
);

const GitHubIcon = () => (
    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true">
        <path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.745 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
    </svg>
);

// ── Password strength ──────────────────────────────────────────────────────────

const PW_LEVELS = [
    { level: 1, label: 'Easy',        color: '#ef4444' },
    { level: 2, label: 'Medium',      color: '#f97316' },
    { level: 3, label: 'Excellent',   color: '#eab308' },
    { level: 4, label: 'Expert',      color: '#3b82f6' },
    { level: 5, label: 'Exceptional', color: '#22c55e' },
] as const;

function getStrength(pw: string) {
    if (!pw) return null;
    let score = 0;
    if (pw.length >= 6)  score++;
    if (pw.length >= 8)  score++;
    if (pw.length >= 12) score++;
    if (/[a-z]/.test(pw)) score++;
    if (/[A-Z]/.test(pw)) score++;
    if (/[0-9]/.test(pw)) score++;
    if (/[^a-zA-Z0-9]/.test(pw)) score += 2;
    // score 0-8 → level 1-5
    const idx = score <= 2 ? 0 : score <= 4 ? 1 : score === 5 ? 2 : score === 6 ? 3 : 4;
    return PW_LEVELS[idx];
}

function PasswordStrengthPopover({ password }: { password: string }) {
    const strength = getStrength(password);

    const reqs = [
        { label: '8+ characters',    met: password.length >= 8 },
        { label: 'Uppercase letter',  met: /[A-Z]/.test(password) },
        { label: 'Number',            met: /[0-9]/.test(password) },
        { label: 'Special character', met: /[^a-zA-Z0-9]/.test(password) },
    ];

    return (
        <div className="pw-popover" role="status" aria-live="polite">
            <div className="pw-bars-row">
                <div className="pw-bars">
                    {PW_LEVELS.map(({ level }) => (
                        <div
                            key={level}
                            className="pw-bar"
                            style={strength && level <= strength.level ? { background: strength.color } : {}}
                        />
                    ))}
                </div>
                <span className="pw-label" style={strength ? { color: strength.color } : {}}>
                    {strength ? strength.label : 'Strength'}
                </span>
            </div>
            <ul className="pw-reqs">
                {reqs.map(req => (
                    <li key={req.label} className={`pw-req${req.met ? ' pw-req--met' : ''}`}>
                        <span className="pw-req-dot" />
                        {req.label}
                    </li>
                ))}
            </ul>
        </div>
    );
}

// ── Shared helpers ─────────────────────────────────────────────────────────────

function SocialButtons() {
    const handleGoogle = async () => {
        try {
            const res = await fetch(`${import.meta.env.VITE_API_URL ?? 'http://localhost:8000/api'}/v1/auth/google`);
            const data = await res.json();
            window.location.href = data.url;
        } catch {
            toast.error('Could not connect to Google. Please try again.');
        }
    };

    return (
        <div className="mf-social">
            <button type="button" className="mf-social-btn mf-social-btn--full" onClick={handleGoogle}>
                <GoogleIcon /> Continue with Google
            </button>
        </div>
    );
}

function OrDivider() {
    return (
        <div className="mf-divider">
            <span>or continue with</span>
        </div>
    );
}

type ModalMode   = 'login' | 'register';
type LoginMethod = 'email' | 'emailOtp';
type OtpStep     = 'enter_email' | 'enter_otp';

interface AuthModalProps {
    mode: ModalMode;
    onClose: () => void;
    onSwitch: (mode: ModalMode) => void;
}

function getApiError(error: unknown): string {
    if (isAxiosError(error)) return error.response?.data?.message ?? 'Something went wrong. Please try again.';
    return 'Something went wrong. Please try again.';
}

function isEmailTaken(error: unknown): boolean {
    const e = error as any;
    const emailErrors: string[] = e?.response?.data?.errors?.email ?? [];
    const message: string = e?.response?.data?.message ?? '';
    const combined = [...emailErrors, message].join(' ').toLowerCase();
    return combined.includes('taken') || combined.includes('already');
}

function formatTimer(seconds: number): string {
    const m = Math.floor(seconds / 60).toString().padStart(2, '0');
    const s = (seconds % 60).toString().padStart(2, '0');
    return `${m}:${s}`;
}

// ── Login form ─────────────────────────────────────────────────────────────────

function LoginForm({ onSwitch, onClose, prefillEmail }: { onSwitch: () => void; onClose: () => void; prefillEmail?: string }) {
    const navigate                    = useNavigate();
    const [method, setMethod]         = useState<LoginMethod>('email');
    const [otpStep, setOtpStep]       = useState<OtpStep>('enter_email');
    const [otpEmail, setOtpEmail]     = useState('');
    const [timer, setTimer]           = useState(0);
    const [showPassword, setShowPw]   = useState(false);

    const { mutate: login, isPending, isError, error, reset } = useLogin();
    const sendOtpMutation   = useSendOtp();
    const verifyOtpMutation = useVerifyOtp();

    const { register, handleSubmit, setValue, formState: { errors } } = useForm<LoginFormData>({
        resolver: zodResolver(loginSchema),
    });
    const sendForm = useForm<SendOtpFormData>({ resolver: zodResolver(sendOtpSchema) });
    const otpForm  = useForm<VerifyOtpFormData>({ resolver: zodResolver(verifyOtpSchema) });

    useEffect(() => {
        if (prefillEmail) setValue('email', prefillEmail);
    }, [prefillEmail]);

    useEffect(() => {
        if (timer <= 0) return;
        const id = setInterval(() => setTimer((t) => t - 1), 1000);
        return () => clearInterval(id);
    }, [timer]);

    const apiError = isError ? getApiError(error) : null;

    const handleMethodChange = (next: LoginMethod) => {
        setMethod(next);
        setOtpStep('enter_email');
        setShowPw(false);
        sendForm.reset();
        otpForm.reset();
        sendOtpMutation.reset();
        verifyOtpMutation.reset();
    };

    const handleSendOtp = sendForm.handleSubmit((data) => {
        sendOtpMutation.mutate(data.email, {
            onSuccess: () => {
                setOtpEmail(data.email);
                setOtpStep('enter_otp');
                setTimer(300);
            },
        });
    });

    const handleVerifyOtp = otpForm.handleSubmit((data) => {
        verifyOtpMutation.mutate({ email: otpEmail, code: data.code });
    });

    const handleResend = () => {
        sendOtpMutation.mutate(otpEmail, {
            onSuccess: () => {
                setTimer(300);
                otpForm.reset();
                verifyOtpMutation.reset();
            },
        });
    };

    return (
        <>
            {/* Method tabs */}
            <div className="mf-method-tabs">
                <button
                    type="button"
                    className={`mf-method-tab${method === 'email' ? ' mf-method-tab--active' : ''}`}
                    onClick={() => handleMethodChange('email')}
                >
                    <Mail size={13} /> Email
                </button>
                <button
                    type="button"
                    className={`mf-method-tab${method === 'emailOtp' ? ' mf-method-tab--active' : ''}`}
                    onClick={() => handleMethodChange('emailOtp')}
                >
                    <KeyRound size={13} /> Email OTP
                </button>
            </div>

            {/* ── Email + password ── */}
            {method === 'email' && (
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
                        <div className="mf-label-row">
                            <label className="mf-label">Password</label>
                            <button
                                type="button"
                                className="mf-forgot"
                                tabIndex={-1}
                                onClick={() => { onClose(); navigate('/auth/forgot-password'); }}
                            >
                                Forgot password?
                            </button>
                        </div>
                        <div className="mf-input-wrap">
                            <span className="mf-icon"><Lock size={15} /></span>
                            <input
                                type={showPassword ? 'text' : 'password'}
                                placeholder="••••••••"
                                className={`mf-input mf-input--eye${apiError ? ' mf-input-err' : ''}`}
                                {...register('password')}
                            />
                            <button
                                type="button"
                                className="mf-eye-btn"
                                onClick={() => setShowPw(p => !p)}
                                tabIndex={-1}
                                aria-label={showPassword ? 'Hide password' : 'Show password'}
                            >
                                {showPassword ? <EyeOff size={15} /> : <Eye size={15} />}
                            </button>
                        </div>
                        {errors.password && <p className="mf-error">{errors.password.message}</p>}
                    </div>

                    <button type="submit" disabled={isPending} className="mf-btn">
                        {isPending ? 'Signing in…' : <><LogIn size={15} /> Sign in</>}
                    </button>

                    <p className="mf-footer">
                        No account?{' '}
                        <button type="button" className="mf-switch" onClick={onSwitch}>Create one free</button>
                    </p>
                </form>
            )}

            {/* ── Email OTP: enter email ── */}
            {method === 'emailOtp' && otpStep === 'enter_email' && (
                <form onSubmit={handleSendOtp} noValidate>
                    {sendOtpMutation.error && (
                        <div className="mf-alert" role="alert">
                            <AlertCircle size={15} />
                            <span>{getApiError(sendOtpMutation.error)}</span>
                        </div>
                    )}

                    <div className="mf">
                        <label className="mf-label">Email address</label>
                        <div className="mf-input-wrap">
                            <span className="mf-icon"><Mail size={15} /></span>
                            <input
                                type="email"
                                placeholder="you@example.com"
                                className="mf-input"
                                {...sendForm.register('email')}
                            />
                        </div>
                        {sendForm.formState.errors.email && (
                            <p className="mf-error">{sendForm.formState.errors.email.message}</p>
                        )}
                    </div>

                    <button type="submit" disabled={sendOtpMutation.isPending} className="mf-btn">
                        {sendOtpMutation.isPending ? 'Sending OTP…' : <><Mail size={15} /> Get OTP</>}
                    </button>

                    <p className="mf-footer">
                        No account?{' '}
                        <button type="button" className="mf-switch" onClick={onSwitch}>Create one free</button>
                    </p>
                </form>
            )}

            {/* ── Email OTP: enter code ── */}
            {method === 'emailOtp' && otpStep === 'enter_otp' && (
                <form onSubmit={handleVerifyOtp} noValidate>
                    <p className="mf-otp-hint">
                        OTP sent to <strong>{otpEmail}</strong>
                    </p>

                    {verifyOtpMutation.error && (
                        <div className="mf-alert" role="alert">
                            <AlertCircle size={15} />
                            <span>{getApiError(verifyOtpMutation.error)}</span>
                        </div>
                    )}

                    <div className="mf">
                        <label className="mf-label">6-digit OTP</label>
                        <div className="mf-input-wrap">
                            <span className="mf-icon"><KeyRound size={15} /></span>
                            <input
                                type="text"
                                inputMode="numeric"
                                maxLength={6}
                                placeholder="000000"
                                className="mf-input mf-otp-input"
                                autoComplete="one-time-code"
                                {...otpForm.register('code')}
                            />
                        </div>
                        {otpForm.formState.errors.code && (
                            <p className="mf-error">{otpForm.formState.errors.code.message}</p>
                        )}
                    </div>

                    <p className={`mf-otp-timer${timer === 0 ? ' mf-otp-timer--expired' : ''}`}>
                        {timer > 0 ? `Expires in ${formatTimer(timer)}` : 'OTP expired'}
                    </p>

                    <button
                        type="submit"
                        disabled={verifyOtpMutation.isPending || timer === 0}
                        className="mf-btn"
                    >
                        {verifyOtpMutation.isPending ? 'Verifying…' : <><LogIn size={15} /> Verify & Sign in</>}
                    </button>

                    <div className="mf-otp-resend-row">
                        <span>Didn't receive it?</span>
                        <button
                            type="button"
                            className="mf-otp-resend"
                            disabled={timer > 0 || sendOtpMutation.isPending}
                            onClick={handleResend}
                        >
                            {sendOtpMutation.isPending ? 'Sending…' : 'Resend'}
                        </button>
                        <span>·</span>
                        <button
                            type="button"
                            className="mf-phone-change"
                            onClick={() => { setOtpStep('enter_email'); }}
                        >
                            Change email
                        </button>
                    </div>
                </form>
            )}
        </>
    );
}

// ── Register form ──────────────────────────────────────────────────────────────

const TERMS_KEY   = 'careeros_terms_read';
const PRIVACY_KEY = 'careeros_privacy_read';

function checkLegalFlags() {
    return !!localStorage.getItem(TERMS_KEY) && !!localStorage.getItem(PRIVACY_KEY);
}

function RegisterForm({
    onSwitch,
    onEmailExists,
}: {
    onSwitch: (email?: string) => void;
    onEmailExists: (email: string) => void;
}) {
    const [showPassword, setShowPw]    = useState(false);
    const [showConfirm,  setShowCfm]   = useState(false);
    const [hoveredPw,    setHoveredPw]  = useState(false);
    const [dataConsent,  setDataConsent]  = useState(false);
    const [termsAccepted, setTermsAccepted] = useState(checkLegalFlags);

    const { mutate: register_, isPending, isError, error, reset } = useRegister();
    const { register, handleSubmit, watch, formState: { errors } } = useForm<RegisterFormData>({
        resolver: zodResolver(registerSchema),
    });

    // Auto-check terms box when user returns after reading both legal pages
    useEffect(() => {
        const onFocus = () => { if (checkLegalFlags()) setTermsAccepted(true); };
        window.addEventListener('focus', onFocus);
        return () => window.removeEventListener('focus', onFocus);
    }, []);

    const apiError       = isError ? getApiError(error) : null;
    const emailTaken     = isError && isEmailTaken(error);
    const watchedEmail   = watch('email', '');
    const watchedPassword = watch('password', '');
    const mobileReg      = register('mobile');

    useEffect(() => {
        if (emailTaken) onEmailExists(watchedEmail);
    }, [emailTaken]);

    const canSubmit = !isPending && dataConsent && termsAccepted;

    return (
        <form onSubmit={handleSubmit((data) => { if (!canSubmit) return; reset(); register_({ ...data, consent_accepted: true }); })} noValidate className="mf-compact">
            {apiError && !emailTaken && (
                <div className="mf-alert" role="alert">
                    <AlertCircle size={15} />
                    <span>{apiError}</span>
                </div>
            )}

            {emailTaken && (
                <div className="mf-email-taken" role="alert">
                    <div className="mf-email-taken-text">
                        <AlertCircle size={14} />
                        This email is already registered.
                    </div>
                    <button
                        type="button"
                        className="mf-email-taken-action"
                        onClick={() => onSwitch(watchedEmail)}
                    >
                        <LogIn size={14} />
                        Log in with this email
                        <ArrowRight size={13} />
                    </button>
                </div>
            )}

            {/* Full name */}
            <div className="mf">
                <label className="mf-label">Full name</label>
                <div className="mf-input-wrap">
                    <span className="mf-icon"><User size={15} /></span>
                    <input type="text" placeholder="John Smith" className="mf-input" {...register('name')} />
                </div>
                {errors.name && <p className="mf-error">{errors.name.message}</p>}
            </div>

            {/* Email */}
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

            {/* Mobile */}
            <div className="mf">
                <label className="mf-label">Mobile number</label>
                <div className="mf-input-wrap">
                    <span className="mf-icon"><Phone size={15} /></span>
                    <input
                        type="tel"
                        inputMode="numeric"
                        placeholder="10-digit number"
                        maxLength={10}
                        className="mf-input"
                        {...mobileReg}
                        onChange={(e) => {
                            e.target.value = e.target.value.replace(/\D/g, '').slice(0, 10);
                            mobileReg.onChange(e);
                        }}
                    />
                </div>
                {errors.mobile && <p className="mf-error">{errors.mobile.message}</p>}
            </div>

            {/* Password + strength meter */}
            <div className="mf">
                <label className="mf-label">Password</label>
                <div
                    className="pw-field"
                    onMouseEnter={() => setHoveredPw(true)}
                    onMouseLeave={() => setHoveredPw(false)}
                >
                    <div className="mf-input-wrap">
                        <span className="mf-icon"><Lock size={15} /></span>
                        <input
                            type={showPassword ? 'text' : 'password'}
                            placeholder="Min. 8 characters"
                            className="mf-input mf-input--eye"
                            {...register('password')}
                        />
                        <button
                            type="button"
                            className="mf-eye-btn"
                            onClick={() => setShowPw(p => !p)}
                            tabIndex={-1}
                            aria-label={showPassword ? 'Hide password' : 'Show password'}
                        >
                            {showPassword ? <EyeOff size={15} /> : <Eye size={15} />}
                        </button>
                    </div>
                    {hoveredPw && <PasswordStrengthPopover password={watchedPassword} />}
                </div>
                {errors.password && <p className="mf-error">{errors.password.message}</p>}
            </div>

            {/* Confirm password */}
            <div className="mf">
                <label className="mf-label">Confirm password</label>
                <div className="mf-input-wrap">
                    <span className="mf-icon"><Lock size={15} /></span>
                    <input
                        type={showConfirm ? 'text' : 'password'}
                        placeholder="Repeat your password"
                        className="mf-input mf-input--eye"
                        {...register('password_confirmation')}
                    />
                    <button
                        type="button"
                        className="mf-eye-btn"
                        onClick={() => setShowCfm(p => !p)}
                        tabIndex={-1}
                        aria-label={showConfirm ? 'Hide password' : 'Show password'}
                    >
                        {showConfirm ? <EyeOff size={15} /> : <Eye size={15} />}
                    </button>
                </div>
                {errors.password_confirmation && (
                    <p className="mf-error">{errors.password_confirmation.message}</p>
                )}
            </div>

            {/* ── DPDP Act 2023 — consent checkboxes ── */}
            <div className="reg-consent-block">

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
                        <span className="reg-consent-icon"><ShieldCheck size={12} /></span>
                        I consent to CareerOS collecting and using my personal data to
                        create and manage my account and deliver its services.
                    </div>
                </label>

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
                        <span className="reg-consent-icon"><FileText size={12} /></span>
                        I have read and agree to the{' '}
                        <a href="/terms" target="_blank" rel="noopener noreferrer" className="reg-consent-link">
                            Terms &amp; Conditions
                        </a>
                        {' '}and{' '}
                        <a href="/privacy" target="_blank" rel="noopener noreferrer" className="reg-consent-link">
                            Privacy Policy
                        </a>.
                        {!termsAccepted && (
                            <span className="reg-consent-hint">Open each link, scroll to end &amp; click Accept.</span>
                        )}
                    </div>
                </label>

            </div>

            <button type="submit" disabled={!canSubmit} className="mf-btn">
                {isPending ? 'Creating account…' : <>Create account <ArrowRight size={15} /></>}
            </button>

            <p className="mf-footer">
                Already have an account?{' '}
                <button type="button" className="mf-switch" onClick={() => onSwitch()}>Sign in</button>
            </p>
        </form>
    );
}

// ── Modal shell ────────────────────────────────────────────────────────────────

export default function AuthModal({ mode, onClose, onSwitch }: AuthModalProps) {
    const [signInHighlight, setSignInHighlight] = useState(false);
    const [prefillEmail, setPrefillEmail]       = useState<string | undefined>();
    const [isExiting, setIsExiting]             = useState(false);
    const exitTimer = useRef<ReturnType<typeof setTimeout> | undefined>(undefined);

    const handleClose = useCallback(() => {
        setIsExiting(true);
        exitTimer.current = setTimeout(onClose, 210);
    }, [onClose]);

    useEffect(() => () => clearTimeout(exitTimer.current), []);

    useEffect(() => {
        const handler = (e: KeyboardEvent) => { if (e.key === 'Escape') handleClose(); };
        document.addEventListener('keydown', handler);
        return () => document.removeEventListener('keydown', handler);
    }, [handleClose]);

    useEffect(() => {
        document.body.style.overflow = 'hidden';
        return () => { document.body.style.overflow = ''; };
    }, []);

    useEffect(() => {
        setSignInHighlight(false);
        if (mode === 'register') setPrefillEmail(undefined);
    }, [mode]);

    const handleEmailExists = (email: string) => {
        setPrefillEmail(email);
        setSignInHighlight(true);
    };

    const handleSwitchFromRegister = (email?: string) => {
        if (email) setPrefillEmail(email);
        onSwitch('login');
    };

    return (
        <div
            className={`modal-backdrop${isExiting ? ' modal-backdrop--out' : ''}`}
            onClick={handleClose}
        >
            <div
                className={`modal-card${isExiting ? ' modal-card--out' : ''}`}
                onClick={(e) => e.stopPropagation()}
                role="dialog"
                aria-modal="true"
            >
                <button className="modal-close" onClick={handleClose} aria-label="Close">
                    <X size={16} />
                </button>

                <div className="modal-brand">
                    <div className="modal-brand-icon">
                        <GraduationCap size={18} strokeWidth={2.5} />
                    </div>
                    <span className="modal-brand-wordmark">CareerOS</span>
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

                <div className="modal-headline" key={`headline-${mode}`}>
                    <h2 className="modal-title">
                        {mode === 'login' ? 'Welcome back' : 'Start for free'}
                    </h2>
                    <p className="modal-subtitle">
                        {mode === 'login'
                            ? 'Sign in to continue your journey'
                            : 'Join engineers building their foundations'}
                    </p>
                </div>

                <SocialButtons />
                <OrDivider />

                <div className="modal-form-area" key={`form-${mode}`}>
                    {mode === 'login'
                        ? <LoginForm
                            onSwitch={() => onSwitch('register')}
                            onClose={handleClose}
                            prefillEmail={prefillEmail}
                          />
                        : <RegisterForm
                            onSwitch={handleSwitchFromRegister}
                            onEmailExists={handleEmailExists}
                          />
                    }
                </div>
            </div>
        </div>
    );
}
