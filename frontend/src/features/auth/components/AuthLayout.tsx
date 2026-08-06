import type { ReactNode } from 'react';
import { Link } from 'react-router-dom';
import '../auth.css';

interface AuthLayoutProps {
    children: ReactNode;
    title: string;
    subtitle?: string;
}

export default function AuthLayout({ children, title, subtitle }: AuthLayoutProps) {
    return (
        <div className="auth-root">
            <div className="auth-card">
                <div className="auth-header">
                    <Link to="/" className="auth-brand">CareerOS</Link>
                    <h1 className="auth-title">{title}</h1>
                    {subtitle && <p className="auth-subtitle">{subtitle}</p>}
                </div>
                {children}
            </div>
        </div>
    );
}
