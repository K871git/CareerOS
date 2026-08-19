import { useState, useEffect } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { Link } from "react-router-dom";
import { Mail, Lock, KeyRound } from "lucide-react";
import { isAxiosError } from "axios";
import AuthLayout from "../components/AuthLayout";
import {
  loginSchema,
  sendOtpSchema,
  verifyOtpSchema,
  type LoginFormData,
  type SendOtpFormData,
  type VerifyOtpFormData,
} from "../schemas";
import { useLogin } from "../hooks/useLogin";
import { useSendOtp } from "../hooks/useSendOtp";
import { useVerifyOtp } from "../hooks/useVerifyOtp";

type LoginTab = "email" | "emailOtp";
type OtpStep  = "enter_email" | "enter_otp";

function getApiMessage(error: unknown, fallback = "Something went wrong."): string {
  if (isAxiosError(error)) return error.response?.data?.message ?? fallback;
  return fallback;
}

function formatTimer(seconds: number): string {
  const m = Math.floor(seconds / 60).toString().padStart(2, "0");
  const s = (seconds % 60).toString().padStart(2, "0");
  return `${m}:${s}`;
}

export default function LoginPage() {
  const [tab, setTab]       = useState<LoginTab>("email");
  const [otpStep, setOtpStep] = useState<OtpStep>("enter_email");
  const [otpEmail, setOtpEmail] = useState("");
  const [timer, setTimer]   = useState(0);

  const { mutate: login, isPending: isLoginPending, error: loginError } = useLogin();
  const sendOtpMutation    = useSendOtp();
  const verifyOtpMutation  = useVerifyOtp();

  const emailForm = useForm<LoginFormData>({ resolver: zodResolver(loginSchema) });
  const sendForm  = useForm<SendOtpFormData>({ resolver: zodResolver(sendOtpSchema) });
  const otpForm   = useForm<VerifyOtpFormData>({ resolver: zodResolver(verifyOtpSchema) });

  useEffect(() => {
    if (timer <= 0) return;
    const id = setInterval(() => setTimer((t) => t - 1), 1000);
    return () => clearInterval(id);
  }, [timer]);

  const handleTabChange = (next: LoginTab) => {
    setTab(next);
    setOtpStep("enter_email");
    sendForm.reset();
    otpForm.reset();
    sendOtpMutation.reset();
    verifyOtpMutation.reset();
  };

  const handleSendOtp = sendForm.handleSubmit((data) => {
    sendOtpMutation.mutate(data.email, {
      onSuccess: () => {
        setOtpEmail(data.email);
        setOtpStep("enter_otp");
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

  const handleChangeEmail = () => {
    setOtpStep("enter_email");
    otpForm.reset();
    verifyOtpMutation.reset();
  };

  return (
    <AuthLayout title="Welcome back" subtitle="Sign in to continue your learning journey">
      {/* Tab switcher */}
      <div className="auth-tabs">
        <button
          type="button"
          className={`auth-tab${tab === "email" ? " auth-tab--active" : ""}`}
          onClick={() => handleTabChange("email")}
        >
          Email & Password
        </button>
        <button
          type="button"
          className={`auth-tab${tab === "emailOtp" ? " auth-tab--active" : ""}`}
          onClick={() => handleTabChange("emailOtp")}
        >
          Email OTP
        </button>
      </div>

      {/* Email + password login */}
      {tab === "email" && (
        <form onSubmit={emailForm.handleSubmit((data) => login(data))} noValidate>
          <div className="auth-field">
            <label className="auth-label">Email address</label>
            <div className="auth-input-wrap">
              <span className="auth-icon"><Mail size={16} /></span>
              <input
                type="email"
                placeholder="you@example.com"
                className="auth-input"
                {...emailForm.register("email")}
              />
            </div>
            {emailForm.formState.errors.email && (
              <p className="auth-error">{emailForm.formState.errors.email.message}</p>
            )}
          </div>

          <div className="auth-field">
            <label className="auth-label">Password</label>
            <div className="auth-input-wrap">
              <span className="auth-icon"><Lock size={16} /></span>
              <input
                type="password"
                placeholder="••••••••"
                className="auth-input"
                {...emailForm.register("password")}
              />
            </div>
            {emailForm.formState.errors.password && (
              <p className="auth-error">{emailForm.formState.errors.password.message}</p>
            )}
          </div>

          <div className="auth-forgot">
            <Link to="/auth/forgot-password" className="auth-link">Forgot password?</Link>
          </div>

          {loginError && (
            <p className="auth-error auth-error--server">{getApiMessage(loginError)}</p>
          )}

          <button type="submit" disabled={isLoginPending} className="auth-btn">
            {isLoginPending ? "Signing in…" : "Sign in"}
          </button>

          <p className="auth-footer">
            Don't have an account?{" "}
            <Link to="/auth/register" className="auth-link">Create account</Link>
          </p>
        </form>
      )}

      {/* Email OTP: enter email */}
      {tab === "emailOtp" && otpStep === "enter_email" && (
        <form onSubmit={handleSendOtp} noValidate>
          <div className="auth-field">
            <label className="auth-label">Email address</label>
            <div className="auth-input-wrap">
              <span className="auth-icon"><Mail size={16} /></span>
              <input
                type="email"
                placeholder="you@example.com"
                className="auth-input"
                {...sendForm.register("email")}
              />
            </div>
            {sendForm.formState.errors.email && (
              <p className="auth-error">{sendForm.formState.errors.email.message}</p>
            )}
          </div>

          {sendOtpMutation.error && (
            <p className="auth-error auth-error--server">{getApiMessage(sendOtpMutation.error)}</p>
          )}

          <button type="submit" disabled={sendOtpMutation.isPending} className="auth-btn">
            {sendOtpMutation.isPending ? "Sending OTP…" : "Get OTP"}
          </button>

          <p className="auth-footer">
            Don't have an account?{" "}
            <Link to="/auth/register" className="auth-link">Create account</Link>
          </p>
        </form>
      )}

      {/* Email OTP: enter code */}
      {tab === "emailOtp" && otpStep === "enter_otp" && (
        <form onSubmit={handleVerifyOtp} noValidate>
          <p className="auth-otp-hint">
            OTP sent to <strong>{otpEmail}</strong>
          </p>

          <div className="auth-field">
            <label className="auth-label">6-digit OTP</label>
            <div className="auth-input-wrap">
              <span className="auth-icon"><KeyRound size={16} /></span>
              <input
                type="text"
                inputMode="numeric"
                maxLength={6}
                placeholder="000000"
                className="auth-input auth-otp-input"
                autoComplete="one-time-code"
                {...otpForm.register("code")}
              />
            </div>
            {otpForm.formState.errors.code && (
              <p className="auth-error">{otpForm.formState.errors.code.message}</p>
            )}
          </div>

          <p className={`auth-otp-timer${timer === 0 ? " auth-otp-timer--expired" : ""}`}>
            {timer > 0 ? `Expires in ${formatTimer(timer)}` : "OTP expired"}
          </p>

          {verifyOtpMutation.error && (
            <p className="auth-error auth-error--server">{getApiMessage(verifyOtpMutation.error)}</p>
          )}

          <button
            type="submit"
            disabled={verifyOtpMutation.isPending || timer === 0}
            className="auth-btn"
          >
            {verifyOtpMutation.isPending ? "Verifying…" : "Verify & Sign in"}
          </button>

          <div className="auth-otp-resend-row">
            <span>Didn't receive it?</span>
            <button
              type="button"
              className="auth-otp-resend"
              disabled={timer > 0 || sendOtpMutation.isPending}
              onClick={handleResend}
            >
              {sendOtpMutation.isPending ? "Sending…" : "Resend"}
            </button>
            <span>·</span>
            <button type="button" className="auth-phone-change" onClick={handleChangeEmail}>
              Change email
            </button>
          </div>
        </form>
      )}
    </AuthLayout>
  );
}
