import { Bell, Search, GraduationCap, ChevronRight, Menu, Moon, Sun } from 'lucide-react';
import { Link } from 'react-router-dom';
import { useAuth } from '../../store/authStore';
import { useTheme } from '../../hooks/useTheme';
import './navbar.css';

interface NavbarProps {
    title?: string;
    onToggleCollapse: () => void;
    onMenuToggle: () => void;
}

export default function Navbar({ title, onToggleCollapse, onMenuToggle }: NavbarProps) {
    const { state } = useAuth();
    const { theme, toggleTheme } = useTheme();

    return (
        <header className="dash-navbar">
            {/* Sidebar zone */}
            <div className="dash-navbar-sidebar-zone">
                <button
                    className="dash-navbar-menu-btn"
                    onClick={onMenuToggle}
                    aria-label="Open menu"
                >
                    <Menu size={20} />
                </button>

                <button
                    className="dash-navbar-brand-collapsed"
                    onClick={onToggleCollapse}
                    aria-label="Expand sidebar"
                >
                    <GraduationCap size={22} className="dash-navbar-brand-icon" />
                    <ChevronRight size={17} className="dash-navbar-brand-expand" />
                </button>

                <Link to="/dashboard" className="dash-navbar-brand-expanded">
                    <span className="dash-navbar-brand-text">CareerOS</span>
                </Link>
            </div>

            {/* Main navbar area */}
            <div className="dash-navbar-content">
                {title && <h1 className="dash-navbar-title">{title}</h1>}
                <div className="dash-navbar-right">
                    <button className="dash-navbar-icon-btn" aria-label="Search">
                        <Search size={17} />
                    </button>
                    <button className="dash-navbar-icon-btn" aria-label="Notifications">
                        <Bell size={17} />
                    </button>
                    <button
                        className="dash-navbar-icon-btn dash-navbar-theme-btn"
                        onClick={toggleTheme}
                        aria-label={theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'}
                        title={theme === 'dark' ? 'Light mode' : 'Dark mode'}
                    >
                        {theme === 'dark' ? <Sun size={17} /> : <Moon size={17} />}
                    </button>
                </div>
            </div>
        </header>
    );
}
