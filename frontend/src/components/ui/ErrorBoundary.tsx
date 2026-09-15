import { Component } from 'react';
import type { ErrorInfo, ReactNode } from 'react';

interface Props { children: ReactNode; }
interface State { hasError: boolean; error: Error | null; }

export default class ErrorBoundary extends Component<Props, State> {
    state: State = { hasError: false, error: null };

    static getDerivedStateFromError(error: Error): State {
        return { hasError: true, error };
    }

    componentDidCatch(error: Error, info: ErrorInfo) {
        console.error('[ErrorBoundary]', error, info.componentStack);
    }

    render() {
        if (this.state.hasError) {
            return (
                <div style={{
                    display: 'flex', flexDirection: 'column', alignItems: 'center',
                    justifyContent: 'center', height: '100vh', gap: '0.75rem',
                    fontFamily: 'inherit', color: '#d1d5db',
                }}>
                    <h2 style={{ margin: 0, color: '#f3f4f6' }}>Something went wrong</h2>
                    <p style={{ margin: 0, fontSize: '0.875rem', color: '#9ca3af' }}>
                        {this.state.error?.message ?? 'An unexpected error occurred.'}
                    </p>
                    <button
                        onClick={() => { window.location.href = '/dashboard'; }}
                        style={{
                            marginTop: '0.5rem', padding: '0.5rem 1.25rem',
                            cursor: 'pointer', border: '1px solid #374151',
                            borderRadius: '6px', background: 'transparent', color: '#f3f4f6',
                        }}
                    >
                        Go to Dashboard
                    </button>
                </div>
            );
        }
        return this.props.children;
    }
}
