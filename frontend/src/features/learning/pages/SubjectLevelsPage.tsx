import { Link, useParams, useLocation, Navigate } from 'react-router-dom';
import { Lock, CheckCircle2, ChevronRight, ClipboardList } from 'lucide-react';
import { useSubjectBySlug, useLevelStatus } from '../hooks/useLevel';
import '../learning.css';

const LEVEL_META = [
    { level: 1, title: 'Foundations',      desc: 'Core concepts, syntax, and fundamental patterns.' },
    { level: 2, title: 'Building Blocks',  desc: 'Intermediate patterns, standard library, real-world usage.' },
    { level: 3, title: 'Applied Skills',   desc: 'Solving practical problems and applying best practices.' },
    { level: 4, title: 'Advanced Concepts', desc: 'Deep internals, performance, and architectural decisions.' },
    { level: 5, title: 'Expert Level',     desc: 'Edge cases, optimisations, and production-grade mastery.' },
];

export default function SubjectLevelsPage() {
    const { category, subjectSlug } = useParams<{ category: string; subjectSlug: string }>();
    const location = useLocation();

    const stateSubjectId = (location.state as { subjectId?: number } | null)?.subjectId;
    const { data: subjectFromApi, isLoading: subjectLoading } = useSubjectBySlug(
        subjectSlug ?? '',
        !stateSubjectId,
    );

    const subjectId    = stateSubjectId ?? subjectFromApi?.id ?? 0;
    const subjectTitle = subjectFromApi?.title ?? subjectSlug ?? '';

    const { data: levels = [], isLoading: levelsLoading } = useLevelStatus(subjectId);

    if (!category || !subjectSlug) return <Navigate to="/learning" replace />;

    const categoryLabel = category === 'languages' ? 'Languages'
                        : category === 'frontend'  ? 'Frontend'
                        : category;

    const isLoading = (!stateSubjectId && subjectLoading) || levelsLoading;

    return (
        <div className="learn-page">
            <nav className="breadcrumb" aria-label="Breadcrumb">
                <div className="breadcrumb-item">
                    <Link to="/learning" className="breadcrumb-link">Learning</Link>
                    <ChevronRight size={13} className="breadcrumb-separator" />
                </div>
                <div className="breadcrumb-item">
                    <Link to={`/learning/${category}`} className="breadcrumb-link">{categoryLabel}</Link>
                    <ChevronRight size={13} className="breadcrumb-separator" />
                </div>
                <div className="breadcrumb-item">
                    <span className="breadcrumb-current" style={{ textTransform: 'capitalize' }}>
                        {subjectTitle || subjectSlug}
                    </span>
                </div>
            </nav>

            <div className="page-header">
                <div>
                    <h1 className="page-header-title" style={{ textTransform: 'capitalize' }}>
                        {subjectTitle || subjectSlug}
                    </h1>
                    <p className="page-header-description">
                        Progress through 5 levels. Pass each level exam (10/10) to unlock the next.
                    </p>
                </div>
            </div>

            {isLoading ? (
                <div className="level-list">
                    {[0, 1, 2, 3, 4].map(i => (
                        <div key={i} className="skeleton" style={{ height: 88, borderRadius: 14 }} />
                    ))}
                </div>
            ) : (
                <div className="level-list">
                    {LEVEL_META.map((meta, idx) => {
                        const levelData = levels[idx];
                        const locked    = levelData?.locked    ?? idx > 0;
                        const completed = levelData?.completed ?? false;
                        const score     = levelData?.score;

                        if (locked) {
                            return (
                                <div key={meta.level} className="level-card level-card--locked">
                                    <div className="level-card-num">
                                        <Lock size={16} className="level-lock-icon" />
                                    </div>
                                    <div className="level-card-body">
                                        <span className="level-card-title">{meta.title}</span>
                                        <span className="level-card-desc">
                                            Pass Level {meta.level - 1} exam to unlock.
                                        </span>
                                    </div>
                                </div>
                            );
                        }

                        return (
                            <Link
                                key={meta.level}
                                to={`/learning/${category}/${subjectSlug}/${meta.level}`}
                                state={{ subjectId }}
                                className={`level-card level-card--active${completed ? ' level-card--done' : ''}`}
                            >
                                <div className="level-card-num">
                                    {completed
                                        ? <CheckCircle2 size={20} className="level-check-icon" />
                                        : <span className="level-num-badge level-num-badge--active">{meta.level}</span>
                                    }
                                </div>
                                <div className="level-card-body">
                                    <span className="level-card-title">{meta.title}</span>
                                    <span className="level-card-desc">{meta.desc}</span>
                                </div>
                                {completed && score !== null && score !== undefined && (
                                    <span className="level-score-badge">
                                        <ClipboardList size={11} />
                                        {score}/10
                                    </span>
                                )}
                                <ChevronRight size={16} className="level-card-arrow" />
                            </Link>
                        );
                    })}
                </div>
            )}
        </div>
    );
}
