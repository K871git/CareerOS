import { Link } from 'react-router-dom';
import { ArrowLeft } from 'lucide-react';

export default function NotFoundPage() {
    return (
        <div
            style={{
                minHeight: '100vh',
                display: 'flex',
                flexDirection: 'column',
                alignItems: 'center',
                justifyContent: 'center',
                textAlign: 'center',
                padding: '2rem',
                background: 'var(--gradient-bg)',
                fontFamily: 'var(--font)',
            }}
        >
            <p
                style={{
                    fontSize: '5rem',
                    fontWeight: 900,
                    letterSpacing: '-0.04em',
                    background: 'var(--gradient-text)',
                    WebkitBackgroundClip: 'text',
                    WebkitTextFillColor: 'transparent',
                    backgroundClip: 'text',
                    lineHeight: 1,
                    marginBottom: '1rem',
                }}
            >
                404
            </p>
            <h1
                style={{
                    fontSize: '1.5rem',
                    fontWeight: 700,
                    letterSpacing: '-0.02em',
                    marginBottom: '0.75rem',
                    color: 'var(--text-primary)',
                }}
            >
                Page not found
            </h1>
            <p
                style={{
                    fontSize: '1rem',
                    color: 'var(--text-secondary)',
                    marginBottom: '2rem',
                    maxWidth: '360px',
                    lineHeight: 1.6,
                }}
            >
                The page you're looking for doesn't exist or has been moved.
            </p>
            <Link
                to="/"
                style={{
                    display: 'inline-flex',
                    alignItems: 'center',
                    gap: '0.5rem',
                    padding: '0.625rem 1.25rem',
                    background: 'var(--gradient-brand)',
                    color: '#fff',
                    borderRadius: 'var(--radius-md)',
                    fontSize: '0.875rem',
                    fontWeight: 600,
                    textDecoration: 'none',
                    boxShadow: 'var(--shadow-brand)',
                }}
            >
                <ArrowLeft size={15} /> Back to home
            </Link>
        </div>
    );
}
