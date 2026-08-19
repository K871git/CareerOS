import { useState, useRef, useEffect } from 'react';
import { NavLink, useNavigate } from 'react-router-dom';
import {
    LayoutDashboard,
    BookOpen,
    Target,
    Terminal,
    Settings,
    ChevronLeft,
    ChevronUp,
    Info,
    HelpCircle,
    LogOut,
    X,
} from 'lucide-react';
import { useAuth } from '../../store/authStore';
import { useLogout } from '../../features/auth/hooks/useLogout';
import './sidebar.css';

interface SidebarProps {
    isOpen: boolean;
    onClose: () => void;
    collapsed: boolean;
    onToggleCollapse: () => void;
}

const navItems = [
    { icon: LayoutDashboard, label: 'Overview',    to: '/dashboard' },
    { icon: Target,          label: 'Practice',    to: '/practice' },
    { icon: BookOpen,        label: 'Learning',    to: '/learning' },
    { icon: Terminal,        label: 'Playground',  to: '/playground' },
];

export default function Sidebar({ isOpen, onClose, collapsed, onToggleCollapse }: SidebarProps) {
    const { state } = useAuth();
    const { mutate: logout, isPending } = useLogout();
    const navigate = useNavigate();
    const [menuOpen, setMenuOpen] = useState(false);
    const menuRef = useRef<HTMLDivElement>(null);

    const initials = state.user?.name
        ? state.user.name.split(' ').map((n) => n[0]).join('').slice(0, 2).toUpperCase()
        : '?';

    useEffect(() => {
        if (!menuOpen) return;
        const handler = (e: MouseEvent) => {
            if (menuRef.current && !menuRef.current.contains(e.target as Node)) {
                setMenuOpen(false);
            }
        };
        document.addEventListener('mousedown', handler);
        return () => document.removeEventListener('mousedown', handler);
    }, [menuOpen]);

    const go = (path: string) => {
        setMenuOpen(false);
        onClose();
        navigate(path);
    };

    return (
        <aside className={`sidebar${isOpen ? ' open' : ''}${collapsed ? ' collapsed' : ''}${menuOpen ? ' menu-open' : ''}`}>
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

            {/* Footer with user menu */}
            <div className="sidebar-footer">
                <div className="sidebar-user-popup-root" ref={menuRef}>

                    {/* Popup panel — opens upward */}
                    {menuOpen && (
                        <div className="sup-panel" role="menu">
                            <div className="sup-header">
                                <div className="sup-header-avatar">{initials}</div>
                                <div className="sup-header-info">
                                    <div className="sup-header-name">{state.user?.name ?? 'User'}</div>
                                    <div className="sup-header-email">{state.user?.email}</div>
                                </div>
                            </div>
                            <div className="sup-sep" />
                            <div className="sup-menu">
                                <button className="sup-item" role="menuitem" onClick={() => go('/settings')}>
                                    <Settings size={15} />
                                    Settings
                                </button>
                                <button className="sup-item sup-item--disabled" role="menuitem" disabled>
                                    <Info size={15} />
                                    About CareerOS
                                    <span className="sup-badge">Soon</span>
                                </button>
                                <button className="sup-item sup-item--disabled" role="menuitem" disabled>
                                    <HelpCircle size={15} />
                                    Get Help
                                    <span className="sup-badge">Soon</span>
                                </button>
                            </div>
                            <div className="sup-sep" />
                            <div className="sup-menu">
                                <button
                                    className="sup-item sup-item--danger"
                                    role="menuitem"
                                    onClick={() => { setMenuOpen(false); logout(); }}
                                    disabled={isPending}
                                >
                                    <LogOut size={15} />
                                    {isPending ? 'Signing out…' : 'Log out'}
                                </button>
                            </div>
                        </div>
                    )}

                    {/* Trigger button */}
                    <button
                        className={`sidebar-user-trigger${menuOpen ? ' open' : ''}`}
                        onClick={() => setMenuOpen((o) => !o)}
                        aria-expanded={menuOpen}
                        aria-haspopup="true"
                        title={collapsed ? (state.user?.name ?? 'User') : undefined}
                    >
                        <div className="sidebar-avatar">{initials}</div>
                        <div className="sidebar-user-info">
                            <div className="sidebar-user-name">{state.user?.name ?? 'User'}</div>
                            <div className="sidebar-user-role">Student</div>
                        </div>
                        <ChevronUp size={13} className={`sidebar-user-chevron${menuOpen ? ' rotated' : ''}`} />
                    </button>
                </div>

                {/* Desktop collapse button */}
                <button
                    className="sidebar-collapse-btn"
                    onClick={onToggleCollapse}
                    aria-label="Collapse sidebar"
                >
                    <ChevronLeft size={14} />
                </button>
            </div>
        </aside>
    );
}
