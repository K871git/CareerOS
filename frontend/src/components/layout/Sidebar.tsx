import { Link, NavLink } from 'react-router-dom';
import {
    LayoutDashboard,
    BookOpen,
    Target,
    TrendingUp,
    User,
    LogOut,
    X,
} from 'lucide-react';
import { useAuth } from '../../store/authStore';
import { useLogout } from '../../features/auth/hooks/useLogout';
import './sidebar.css';

interface SidebarProps {
    isOpen: boolean;
    onClose: () => void;
}

const navItems = [
    { icon: LayoutDashboard, label: 'Dashboard',       to: '/dashboard' },
    { icon: BookOpen,        label: 'Learning Tracks',  to: '/tracks' },
    { icon: Target,          label: 'Practice',          to: '/practice' },
    { icon: TrendingUp,      label: 'Progress',          to: '/progress' },
    { icon: User,            label: 'Profile',           to: '/profile' },
];

export default function Sidebar({ isOpen, onClose }: SidebarProps) {
    const { state } = useAuth();
    const { mutate: logout, isPending } = useLogout();

    const initials = state.user?.name
        ? state.user.name
              .split(' ')
              .map((n) => n[0])
              .join('')
              .slice(0, 2)
              .toUpperCase()
        : '?';

    return (
        <aside className={`sidebar${isOpen ? ' open' : ''}`}>
            <div className="sidebar-header">
                <Link to="/dashboard" className="sidebar-brand" onClick={onClose}>
                    <span className="sidebar-brand-text">CareerOS</span>
                </Link>
                <button
                    className="sidebar-close-btn"
                    onClick={onClose}
                    aria-label="Close sidebar"
                >
                    <X size={18} />
                </button>
            </div>

            <div className="sidebar-section">
                <span className="sidebar-section-label">Menu</span>
                {navItems.map((item) => (
                    <NavLink
                        key={item.to}
                        to={item.to}
                        onClick={onClose}
                        className={({ isActive }) =>
                            `sidebar-nav-item${isActive ? ' active' : ''}`
                        }
                    >
                        <item.icon size={18} className="sidebar-nav-icon" />
                        {item.label}
                    </NavLink>
                ))}
            </div>

            <div className="sidebar-footer">
                <div className="sidebar-user">
                    <div className="sidebar-avatar">{initials}</div>
                    <div className="sidebar-user-info">
                        <div className="sidebar-user-name">{state.user?.name ?? 'User'}</div>
                        <div className="sidebar-user-role">Student</div>
                    </div>
                </div>
                <button
                    className="sidebar-logout-btn"
                    onClick={() => logout()}
                    disabled={isPending}
                >
                    <LogOut size={16} className="sidebar-nav-icon" />
                    {isPending ? 'Logging out…' : 'Log out'}
                </button>
            </div>
        </aside>
    );
}
