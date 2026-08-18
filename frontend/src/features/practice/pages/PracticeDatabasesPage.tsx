import { Link } from 'react-router-dom';
import { Database } from 'lucide-react';
import { useTracks, useSubjects } from '../../learning/hooks/useLearning';
import type { Subject } from '../../../types/api';
import '../practice.css';

const DB_BADGE: Record<string, { text: string; bg: string; color: string }> = {
    mysql:      { text: 'MY',  bg: '#00618A', color: '#ffffff' },
    postgresql: { text: 'PG',  bg: '#336791', color: '#ffffff' },
    clickhouse: { text: 'CH',  bg: '#FFCC01', color: '#1a1a1a' },
    mongodb:    { text: 'Mg',  bg: '#47A248', color: '#ffffff' },
    sqlite:     { text: 'SL',  bg: '#003B57', color: '#ffffff' },
    mariadb:    { text: 'Ma',  bg: '#C0765A', color: '#ffffff' },
};

const DATABASES = [
    { slug: 'mysql',      label: 'MySQL' },
    { slug: 'postgresql', label: 'PostgreSQL' },
    { slug: 'clickhouse', label: 'ClickHouse' },
    { slug: 'mongodb',    label: 'MongoDB' },
    { slug: 'sqlite',     label: 'SQLite' },
    { slug: 'mariadb',    label: 'MariaDB' },
] as const;

function DbBadge({ slug }: { slug: string }) {
    const cfg = DB_BADGE[slug];
    if (!cfg) return <div className="lang-badge" style={{ background: '#6366f1', color: '#fff' }}><Database size={14} /></div>;
    const fontSize = cfg.text.length <= 2 ? '0.8125rem' : '0.675rem';
    return (
        <div className="lang-badge" style={{ background: cfg.bg, color: cfg.color, fontSize }}>
            {cfg.text}
        </div>
    );
}

export default function PracticeDatabasesPage() {
    const { data: tracks = [], isLoading: tracksLoading } = useTracks();
    const dbId = tracks.find(t => t.slug === 'databases')?.id ?? 0;
    const { data: dbSubjects = [], isLoading: subjectsLoading } = useSubjects(dbId);

    const isLoading = tracksLoading || subjectsLoading;

    const subjectMap = new Map<string, Subject>(
        dbSubjects.filter(s => s.mcq_question_count >= 1).map(s => [s.slug, s])
    );

    return (
        <div className="practice-page">
            <div className="practice-inner">
                <div className="prac-breadcrumb">
                    <Link to="/practice" className="prac-breadcrumb-link">Practice</Link>
                    <span className="prac-breadcrumb-sep">›</span>
                    <span>Databases</span>
                </div>

                <div className="prac-fsd-header">
                    <h1 className="prac-fsd-title">Databases</h1>
                    <p className="prac-fsd-desc">
                        Practice SQL, NoSQL, indexing, transactions, and database design fundamentals.
                    </p>
                </div>

                {isLoading ? (
                    <div className="prac-tech-grid">
                        {[0, 1, 2, 3, 4, 5].map(i => (
                            <div key={i} className="skeleton" style={{ height: 72, borderRadius: 12 }} />
                        ))}
                    </div>
                ) : (
                    <div className="prac-tech-grid">
                        {DATABASES.map((db) => {
                            const subject = subjectMap.get(db.slug) ?? null;

                            if (subject) {
                                return (
                                    <Link
                                        key={db.slug}
                                        to={`/practice/subjects/${subject.id}`}
                                        state={{
                                            subjectTitle: subject.title,
                                            trackTitle: 'Databases',
                                            arenaId: 'databases',
                                            trackId: dbId,
                                        }}
                                        className="prac-tech-card prac-tech-card--active"
                                    >
                                        <div className="prac-tech-icon prac-tech-icon--lang">
                                            <DbBadge slug={db.slug} />
                                        </div>
                                        <div className="prac-tech-info">
                                            <span className="prac-tech-label">{db.label}</span>
                                            <span className="prac-tech-count">
                                                {subject.mcq_question_count} questions
                                            </span>
                                        </div>
                                        <span className="prac-tech-arrow">→</span>
                                    </Link>
                                );
                            }

                            return (
                                <div key={db.slug} className="prac-tech-card prac-tech-card--soon">
                                    <div className="prac-tech-icon prac-tech-icon--lang">
                                        <DbBadge slug={db.slug} />
                                    </div>
                                    <div className="prac-tech-info">
                                        <span className="prac-tech-label">{db.label}</span>
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
