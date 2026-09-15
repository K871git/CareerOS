import { Suspense } from 'react';
import { RouterProvider } from 'react-router-dom';
import { QueryClientProvider } from '@tanstack/react-query';
import { Toaster } from 'react-hot-toast';
import { AuthProvider } from './store/authStore';
import { AuthOverlayProvider } from './contexts/AuthOverlayContext';
import AuthOverlay from './components/ui/AuthOverlay';
import ErrorBoundary from './components/ui/ErrorBoundary';
import queryClient from './api/queryClient';
import router from './routes';

function PageLoader() {
    return (
        <div style={{
            display: 'flex', alignItems: 'center', justifyContent: 'center',
            height: '100vh', color: 'var(--color-text-muted, #6b7280)', fontSize: '0.875rem',
        }}>
            Loading…
        </div>
    );
}

export default function App() {
    return (
        <AuthProvider>
            <AuthOverlayProvider>
                <QueryClientProvider client={queryClient}>
                    <ErrorBoundary>
                        <Suspense fallback={<PageLoader />}>
                            <RouterProvider router={router} />
                        </Suspense>
                    </ErrorBoundary>
                    <Toaster
                        position="top-right"
                        toastOptions={{
                            duration: 4000,
                            style: { fontFamily: 'inherit' },
                        }}
                    />
                </QueryClientProvider>
                {/* Rendered outside RouterProvider so it sits above all routes */}
                <AuthOverlay />
            </AuthOverlayProvider>
        </AuthProvider>
    );
}
