import { useRouteError, isRouteErrorResponse, Link } from 'react-router-dom';

export default function RouteErrorPage() {
    const error = useRouteError();

    const message = isRouteErrorResponse(error)
        ? error.status === 404
            ? "Page not found."
            : `Error ${error.status}: ${error.statusText}`
        : error instanceof Error
            ? error.message
            : 'An unexpected error occurred.';

    return (
        <div style={{
            display: 'flex', flexDirection: 'column', alignItems: 'center',
            justifyContent: 'center', height: '100vh', gap: '0.75rem',
            fontFamily: 'inherit', color: '#d1d5db',
        }}>
            <h2 style={{ margin: 0, color: '#f3f4f6' }}>Something went wrong</h2>
            <p style={{ margin: 0, fontSize: '0.875rem', color: '#9ca3af' }}>{message}</p>
            <Link
                to="/dashboard"
                style={{
                    marginTop: '0.5rem', padding: '0.5rem 1.25rem',
                    border: '1px solid #374151', borderRadius: '6px',
                    color: '#f3f4f6', textDecoration: 'none', fontSize: '0.875rem',
                }}
            >
                Go to Dashboard
            </Link>
        </div>
    );
}
