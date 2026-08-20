import { RouterProvider } from 'react-router-dom';
import { QueryClientProvider } from '@tanstack/react-query';
import { Toaster } from 'react-hot-toast';
import { AuthProvider } from './store/authStore';
import { AuthOverlayProvider } from './contexts/AuthOverlayContext';
import AuthOverlay from './components/ui/AuthOverlay';
import queryClient from './api/queryClient';
import router from './routes';

export default function App() {
    return (
        <AuthProvider>
            <AuthOverlayProvider>
                <QueryClientProvider client={queryClient}>
                    <RouterProvider router={router} />
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
