import type { ReactNode } from 'react';
import './navbar.css';

interface PageHeaderProps {
    title: string;
    description?: string;
    children?: ReactNode;
}

export default function PageHeader({ title, description, children }: PageHeaderProps) {
    return (
        <div className="page-header">
            <div>
                <h1 className="page-header-title">{title}</h1>
                {description && <p className="page-header-description">{description}</p>}
            </div>
            {children && <div className="page-header-actions">{children}</div>}
        </div>
    );
}
