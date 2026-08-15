import { NavLink } from 'react-router-dom';
import {
    LayoutDashboard,
    BookOpen,
    Target,
    BookMarked,
    TrendingUp,
    User,
    LogOut,
    ChevronLeft,
    X,
} from 'lucide-react';
import { useAuth } from '../../store/authStore';
import { useLogout } from '../../features/auth/hooks/useLogout';
import './sidebar.css';

interface SidebarProps {
    isOpen: boolean;       // mobile drawer state
    onClose: () => void;   // mobile close
    collapsed: boolean;    // desktop icon-rail state
    onToggleCollapse: () => void;
}

const navItems = [
    { icon: LayoutDashboard, label: 'Dashboard',      to: '/dashboard' },
    { icon: Target,          label: 'Practice',        to: '/practice' },
    { icon: BookOpen,        label: 'Learning',        to: '/learning' },
    { icon: BookMarked,      label: 'Theory',          to: '/theory' },
    { icon: TrendingUp,      label: 'Progress',        to: '/progress' },
    { icon: User,            label: 'Profile',         to: '/profile' },
];

export default function Sidebar({ isOpen, onClose, collapsed, onToggleCollapse }: SidebarProps) {
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
        <aside className={`sidebar${isOpen ? ' open' : ''}${collapsed ? ' collapsed' : ''}`}>
            {/* Mobile close button */}
            <button className="sidebar-mobile-close" onClick={onClose} aria-label="Close menu">
                <X size={18} />
            </button>

            <div className="sidebar-section">
                <span className="sidebar-section-label">Menu</span>
                {navItems.map((item) => (
                    <NavLink
                        key={item.to}
                        to={item.to}
                        onClick={onClose}
                        title={collapsed ? item.label : undefined}
                        className={({ isActive }) =>
                            `sidebar-nav-item${isActive ? ' active' : ''}`
                        }
                    >
                        <item.icon size={18} className="sidebar-nav-icon" />
                        <span className="sidebar-nav-label">{item.label}</span>
                    </NavLink>
                ))}
            </div>

            <div className="sidebar-footer">
                <div className="sidebar-user" title={collapsed ? (state.user?.name ?? 'User') : undefined}>
                    <div className="sidebar-avatar">{initials}</div>
                    <div className="sidebar-user-info">
                        <div className="sidebar-user-name">{state.user?.name ?? 'User'}</div>
                        <div className="sidebar-user-role">Student</div>
                    </div>
                    {/* Desktop collapse button — right of user panel */}
                    <button
                        className="sidebar-collapse-btn"
                        onClick={onToggleCollapse}
                        aria-label="Collapse sidebar"
                    >
                        <ChevronLeft size={14} />
                    </button>
                </div>
                <button
                    className="sidebar-logout-btn"
                    onClick={() => logout()}
                    disabled={isPending}
                    title={collapsed ? 'Log out' : undefined}
                >
                    <LogOut size={16} className="sidebar-nav-icon" />
                    <span className="sidebar-nav-label">
                        {isPending ? 'Logging out…' : 'Log out'}
                    </span>
                </button>
            </div>
        </aside>
    );
}
