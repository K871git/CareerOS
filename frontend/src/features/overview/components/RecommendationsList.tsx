import { Link } from 'react-router-dom';
import { AlertTriangle, Zap, BookMarked, TrendingUp, ArrowRight } from 'lucide-react';
import type { Recommendation } from '../../../types/api';

function recIcon(type: string) {
    switch (type) {
        case 'weak_topic':     return <AlertTriangle size={14} />;
        case 'get_started':    return <Zap size={14} />;
        case 'start_learning': return <BookMarked size={14} />;
        default:               return <TrendingUp size={14} />;
    }
}

export function RecommendationsList({ items }: { items: Recommendation[] }) {
    const list = Array.isArray(items) ? items : [];
    return (
        <ul className="rec-list">
            {list.map((rec, i) => (
                <li key={i} className={`rec-item rec-item--${rec.type}`}>
                    <div className="rec-icon">{recIcon(rec.type)}</div>
                    <div className="rec-body">
                        <span className="rec-title">{rec.title}</span>
                        <span className="rec-desc">{rec.description}</span>
                    </div>
                    <Link to={rec.route} className="rec-action"><ArrowRight size={13} /></Link>
                </li>
            ))}
        </ul>
    );
}
