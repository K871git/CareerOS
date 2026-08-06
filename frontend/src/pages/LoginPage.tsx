import { useState } from 'react';
import { Link } from 'react-router-dom';

export default function LoginPage() {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');

    function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
        e.preventDefault();
        // TODO: connect to auth API
    }

    return (
        <main style={{ padding: '3rem 1rem', maxWidth: '400px', margin: '0 auto' }}>
            <h1 style={{ marginBottom: '1.5rem' }}>Login</h1>

            <form onSubmit={handleSubmit}>
                <div style={{ marginBottom: '1rem' }}>
                    <label htmlFor="email" style={{ display: 'block', marginBottom: '0.375rem', fontWeight: 500 }}>
                        Email
                    </label>
                    <input
                        id="email"
                        type="email"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        required
                        placeholder="you@example.com"
                        style={{ width: '100%', padding: '0.625rem', fontSize: '1rem', boxSizing: 'border-box' }}
                    />
                </div>

                <div style={{ marginBottom: '1.5rem' }}>
                    <label htmlFor="password" style={{ display: 'block', marginBottom: '0.375rem', fontWeight: 500 }}>
                        Password
                    </label>
                    <input
                        id="password"
                        type="password"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        required
                        placeholder="••••••••"
                        style={{ width: '100%', padding: '0.625rem', fontSize: '1rem', boxSizing: 'border-box' }}
                    />
                </div>

                <button
                    type="submit"
                    style={{ width: '100%', padding: '0.75rem', fontSize: '1rem', cursor: 'pointer' }}
                >
                    Login
                </button>
            </form>

            <p style={{ marginTop: '1.25rem', textAlign: 'center' }}>
                Don&apos;t have an account?{' '}
                <Link to="/register">Register</Link>
            </p>
        </main>
    );
}
