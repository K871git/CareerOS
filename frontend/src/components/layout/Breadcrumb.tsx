import { Link } from 'react-router-dom';
import { ChevronRight } from 'lucide-react';
import './navbar.css';

export interface BreadcrumbItem {
    label: string;
    to?: string;
}

interface BreadcrumbProps {
    items: BreadcrumbItem[];
}

export default function Breadcrumb({ items }: BreadcrumbProps) {
    return (
        <nav className="breadcrumb" aria-label="Breadcrumb">
            {items.map((item, index) => (
                <span key={index} className="breadcrumb-item">
                    {index > 0 && (
                        <ChevronRight size={13} className="breadcrumb-separator" />
                    )}
                    {item.to ? (
                        <Link to={item.to} className="breadcrumb-link">
                            {item.label}
                        </Link>
                    ) : (
                        <span className="breadcrumb-current">{item.label}</span>
                    )}
                </span>
            ))}
        </nav>
    );
}
