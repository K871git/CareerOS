import { Link, Outlet } from 'react-router-dom';
import { useAuth } from '../store/authStore';
import { useLogout } from '../features/auth/hooks/useLogout';
import './layout.css';

export default function MainLayout() {
    const { state } = useAuth();
    const { mutate: logout, isPending } = useLogout();

    return (
        <>
            <header className="header">
                <Link to="/" className="header-brand">CareerOS</Link>

                <nav className="header-nav">
                    {state.isAuthenticated ? (
                        <>
                            <span className="header-user">{state.user?.name}</span>
                            <button
                                className="header-btn-ghost"
                                onClick={() => logout()}
                                disabled={isPending}
                            >
                                {isPending ? 'Logging out…' : 'Log out'}
                            </button>
                        </>
                    ) : (
                        <Link to="/auth/login" className="header-btn-primary">
                            Sign in
                        </Link>
                    )}
                </nav>
            </header>

            <main className="page-content">
                <Outlet />
            </main>
        </>
    );
}
