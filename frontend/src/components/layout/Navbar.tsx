import { Bell, Search, Menu } from 'lucide-react';
import { useAuth } from '../../store/authStore';
import './navbar.css';

interface NavbarProps {
    title?: string;
    onMenuToggle: () => void;
}

export default function Navbar({ title, onMenuToggle }: NavbarProps) {
    const { state } = useAuth();

    return (
        <header className="dash-navbar">
            <div className="dash-navbar-left">
                <button
                    className="dash-navbar-menu-btn dash-navbar-icon-btn"
                    onClick={onMenuToggle}
                    aria-label="Toggle menu"
                >
                    <Menu size={20} />
                </button>
                {title && <h1 className="dash-navbar-title">{title}</h1>}
            </div>
            <div className="dash-navbar-right">
                <button className="dash-navbar-icon-btn" aria-label="Search">
                    <Search size={17} />
                </button>
                <button className="dash-navbar-icon-btn" aria-label="Notifications">
                    <Bell size={17} />
                </button>
                <div className="dash-navbar-divider" />
                <span className="dash-navbar-user">{state.user?.name}</span>
            </div>
        </header>
    );
}
