import { useState, useEffect } from 'react';
import { Link, Navigate, Outlet, useSearchParams } from 'react-router-dom';
import { useAuth } from '../store/authStore';
import { useLogout } from '../features/auth/hooks/useLogout';
import Footer from '../components/layout/Footer';
import AuthModal from '../components/ui/AuthModal';
import './layout.css';

export type GuestOutletContext = {
    openModal: (mode: 'login' | 'register') => void;
};

export default function GuestLayout() {
    const { state } = useAuth();
    const { mutate: logout, isPending } = useLogout();
    const [searchParams, setSearchParams] = useSearchParams();
    const [modalMode, setModalMode] = useState<'login' | 'register' | null>(null);

    useEffect(() => {
        const modal = searchParams.get('modal');
        if (modal === 'login' || modal === 'register') {
            setModalMode(modal);
            setSearchParams({}, { replace: true });
        }
    }, []);

    if (state.isAuthenticated) {
        return <Navigate to="/dashboard" replace />;
    }

    const openModal = (mode: 'login' | 'register') => setModalMode(mode);
    const closeModal = () => setModalMode(null);
    const ctx: GuestOutletContext = { openModal };

    return (
        <>
            <header className="header">
                <Link to="/" className="header-brand">CareerOS</Link>
                <nav className="header-nav">
                    {state.isAuthenticated ? (
                        <>
                            <Link to="/dashboard" className="header-btn-ghost">Dashboard</Link>
                            <button
                                className="header-btn-ghost"
                                onClick={() => logout()}
                                disabled={isPending}
                            >
                                {isPending ? 'Logging out…' : 'Log out'}
                            </button>
                        </>
                    ) : (
                        <>
                            <button className="header-btn-ghost header-signin" onClick={() => openModal('login')}>
                                Sign in
                            </button>
                            <button className="header-btn-primary" onClick={() => openModal('register')}>
                                Get started free
                            </button>
                        </>
                    )}
                </nav>
            </header>
            <main className="page-content">
                <Outlet context={ctx} />
            </main>
            <Footer />

            {modalMode && (
                <AuthModal
                    mode={modalMode}
                    onClose={closeModal}
                    onSwitch={setModalMode}
                />
            )}
        </>
    );
}
