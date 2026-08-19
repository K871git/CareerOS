import { useState } from 'react';
import { Outlet, useLocation } from 'react-router-dom';
import Sidebar from '../components/layout/Sidebar';
import Navbar from '../components/layout/Navbar';
import './dashboard.css';

export default function DashboardLayout() {
    const location = useLocation();
    const [sidebarOpen, setSidebarOpen] = useState(false); // mobile drawer
    const [sidebarCollapsed, setSidebarCollapsed] = useState(() => {
        return localStorage.getItem('sidebar_collapsed') === 'true';
    });

    const toggleCollapse = () => {
        setSidebarCollapsed((prev) => {
            const next = !prev;
            localStorage.setItem('sidebar_collapsed', String(next));
            return next;
        });
    };

    return (
        <div className={`dash-root${sidebarCollapsed ? ' sidebar-collapsed' : ''}`}>
            <Navbar
                onToggleCollapse={toggleCollapse}
                onMenuToggle={() => setSidebarOpen((prev) => !prev)}
            />

            <div className="dash-body">
                <Sidebar
                    isOpen={sidebarOpen}
                    onClose={() => setSidebarOpen(false)}
                    collapsed={sidebarCollapsed}
                    onToggleCollapse={toggleCollapse}
                />

                {sidebarOpen && (
                    <div
                        className="sidebar-overlay"
                        onClick={() => setSidebarOpen(false)}
                        aria-hidden="true"
                    />
                )}

                <main className="dash-main">
                    <div
                        key={location.key}
                        className={`dash-content page-enter${location.pathname.startsWith('/playground') ? ' dash-content--full' : ''}`}
                    >
                        <Outlet />
                    </div>
                </main>
            </div>
        </div>
    );
}
