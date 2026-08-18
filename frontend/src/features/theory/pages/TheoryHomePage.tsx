import { Link } from 'react-router-dom';
import {
    Code2, Layers, Wifi, Cpu, Database,
    LayoutTemplate, RefreshCcw, GitBranch,
} from 'lucide-react';
import { useTheoryAreas } from '../hooks/useTheoryLevel';
import '../theory.css';

const ICONS: Record<string, React.ElementType> = {
    languages:          Code2,
    frameworks:         Layers,
    networking:         Wifi,
    'operating-systems': Cpu,
    databases:          Database,
    'system-design':    LayoutTemplate,
    sdlc:               RefreshCcw,
    'data-structures':  GitBranch,
};

export default function TheoryHomePage() {
    const { data: areas = [], isLoading } = useTheoryAreas();

    return (
        <div className="theory-v2-page">
            <div className="theory-v2-inner">
                <div className="th-home-header">
                    <h1 className="th-home-title">Theory</h1>
                    <p className="th-home-subtitle">
                        Master conceptual knowledge across programming areas. Complete all three
                        levels to unlock additional assessments.
                    </p>
                </div>

                {isLoading ? (
                    <div className="prac-cat-grid">
                        {Array.from({ length: 8 }).map((_, i) => (
                            <div key={i} className="skeleton" style={{ height: 88, borderRadius: 14 }} />
                        ))}
                    </div>
                ) : (
                    <div className="prac-cat-grid">
                        {areas.map((area) => {
                            const Icon = ICONS[area.slug] ?? Code2;
                            const allDone = area.levels_completed === area.total_levels;

                            if (area.available) {
                                return (
                                    <Link
                                        key={area.slug}
                                        to={`/theory/${area.slug}`}
                                        className="prac-cat-card prac-cat-card--active"
                                    >
                                        <div className="prac-cat-icon">
                                            <Icon size={20} strokeWidth={1.75} />
                                        </div>
                                        <div className="prac-cat-content">
                                            <h3 className="prac-cat-title">{area.title}</h3>
                                            <p className="prac-cat-desc">
                                                {area.levels_completed}/{area.total_levels} levels complete
                                            </p>
                                        </div>
                                        {allDone ? (
                                            <span className="th-done-badge">Done ✓</span>
                                        ) : (
                                            <span className="prac-cat-cta">Start →</span>
                                        )}
                                    </Link>
                                );
                            }

                            return (
                                <div key={area.slug} className="prac-cat-card prac-cat-card--soon">
                                    <div className="prac-cat-icon">
                                        <Icon size={20} strokeWidth={1.75} />
                                    </div>
                                    <div className="prac-cat-content">
                                        <h3 className="prac-cat-title">{area.title}</h3>
                                        <p className="prac-cat-desc">Conceptual theory questions</p>
                                    </div>
                                    <span className="prac-soon-badge">Coming Soon</span>
                                </div>
                            );
                        })}
                    </div>
                )}
            </div>
        </div>
    );
}
