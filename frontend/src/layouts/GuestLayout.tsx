import { Link, Outlet } from 'react-router-dom';
import { useAuth } from '../store/authStore';
import { useLogout } from '../features/auth/hooks/useLogout';
import Footer from '../components/layout/Footer';
import './layout.css';

export default function GuestLayout() {
    const { state } = useAuth();
    const { mutate: logout, isPending } = useLogout();

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
                            <Link to="/auth/login" className="header-btn-ghost">Sign in</Link>
                            <Link to="/auth/register" className="header-btn-primary">
                                Get started free
                            </Link>
                        </>
                    )}
                </nav>
            </header>
            <main className="page-content">
                <Outlet />
            </main>
            <Footer />
        </>
    );
}
