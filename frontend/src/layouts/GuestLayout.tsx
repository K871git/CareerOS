import { useState, useEffect } from 'react';
import { Link, Navigate, Outlet, useSearchParams } from 'react-router-dom';
import { Moon, Sun } from 'lucide-react';
import { useAuth } from '../store/authStore';
import { useLogout } from '../features/auth/hooks/useLogout';
import { useTheme } from '../hooks/useTheme';
import Footer from '../components/layout/Footer';
import AuthModal from '../components/ui/AuthModal';
import './layout.css';

function useActiveSection(ids: string[]) {
    const [active, setActive] = useState('');
    useEffect(() => {
        let observer: IntersectionObserver;
        const raf = requestAnimationFrame(() => {
            observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) setActive(entry.target.id);
                    });
                },
                { threshold: 0.35, rootMargin: '-64px 0px -25% 0px' }
            );
            ids.forEach(id => {
                const el = document.getElementById(id);
                if (el) observer.observe(el);
            });
        });
        return () => { cancelAnimationFrame(raf); observer?.disconnect(); };
    }, []);
    return active;
}

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

    const { theme, toggleTheme } = useTheme();
    const activeSection = useActiveSection(['features', 'how-it-works', 'for-who']);

    const openModal = (mode: 'login' | 'register') => setModalMode(mode);
    const closeModal = () => setModalMode(null);
    const ctx: GuestOutletContext = { openModal };

    return (
        <>
            <header className="header">
                <Link to="/" className="header-brand">CareerOS</Link>
                <nav className="header-nav">
                    <div className="header-nav-links">
                        <a href="#features"    className={`header-nav-link${activeSection === 'features'    ? ' header-nav-link--active' : ''}`}>Features</a>
                        <a href="#how-it-works" className={`header-nav-link${activeSection === 'how-it-works' ? ' header-nav-link--active' : ''}`}>How it works</a>
                        <a href="#for-who"     className={`header-nav-link${activeSection === 'for-who'     ? ' header-nav-link--active' : ''}`}>For who</a>
                    </div>
                    <button
                        className="header-theme-btn"
                        onClick={toggleTheme}
                        aria-label={theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'}
                        title={theme === 'dark' ? 'Light mode' : 'Dark mode'}
                    >
                        {theme === 'dark' ? <Sun size={17} /> : <Moon size={17} />}
                    </button>
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
