import { Link, useParams, Navigate } from 'react-router-dom';
import { ChevronRight } from 'lucide-react';
import { useTracks, useSubjects } from '../hooks/useLearning';
import type { Subject } from '../../../types/api';
import '../learning.css';

const LANG_BADGE: Record<string, { text: string; bg: string; color: string }> = {
    javascript: { text: 'JS',  bg: '#F7DF1E', color: '#1a1a1a' },
    typescript: { text: 'TS',  bg: '#3178C6', color: '#ffffff' },
    python:     { text: 'Py',  bg: '#3776AB', color: '#FFD43B' },
    php:        { text: 'php', bg: '#777BB4', color: '#ffffff' },
    java:       { text: 'Ja',  bg: '#ED8B00', color: '#ffffff' },
    c:          { text: 'C',   bg: '#283593', color: '#ffffff' },
    cpp:        { text: 'C++', bg: '#00599C', color: '#ffffff' },
    csharp:     { text: 'C#',  bg: '#512BD4', color: '#ffffff' },
    react:      { text: 'Re',  bg: '#61DAFB', color: '#1a1a1a' },
    angular:    { text: 'Ng',  bg: '#DD0031', color: '#ffffff' },
    vue:        { text: 'Vu',  bg: '#42B883', color: '#ffffff' },
    nextjs:     { text: 'Nx',  bg: '#111111', color: '#ffffff' },
    laravel:    { text: 'Lv',  bg: '#FF2D20', color: '#ffffff' },
    nodejs:     { text: 'No',  bg: '#339933', color: '#ffffff' },
    express:    { text: 'Ex',  bg: '#444444', color: '#ffffff' },
    mysql:      { text: 'My',  bg: '#4479A1', color: '#ffffff' },
    sql:        { text: 'SQL', bg: '#336791', color: '#ffffff' },
    postgresql: { text: 'PG',  bg: '#336791', color: '#ffffff' },
    mongodb:    { text: 'Mo',  bg: '#47A248', color: '#ffffff' },
    redis:      { text: 'Re',  bg: '#DC382D', color: '#ffffff' },
    clickhouse: { text: 'CH',  bg: '#FFCC01', color: '#1a1a1a' },
};

const CATEGORY_CONFIG = {
    languages: {
        title: 'Programming Languages',
        desc: 'Master programming language fundamentals, syntax, and best practices level by level.',
        subjects: [
            { slug: 'javascript', label: 'JavaScript', active: true },
            { slug: 'typescript', label: 'TypeScript', active: true },
            { slug: 'python',     label: 'Python',     active: true },
            { slug: 'php',        label: 'PHP',        active: true },
            { slug: 'java',       label: 'Java',       active: false },
            { slug: 'c',          label: 'C',          active: false },
            { slug: 'cpp',        label: 'C++',        active: false },
            { slug: 'csharp',     label: 'C#',         active: false },
        ],
    },
    frontend: {
        title: 'Frontend Development',
        desc: 'Build production-grade interfaces with modern frameworks level by level.',
        subjects: [
            { slug: 'react',   label: 'React',   active: true },
            { slug: 'angular', label: 'Angular', active: true },
            { slug: 'vue',     label: 'Vue.js',  active: false },
            { slug: 'nextjs',  label: 'Next.js', active: false },
        ],
    },
    backend: {
        title: 'Backend Development',
        desc: 'Build server-side applications, REST APIs, and scalable systems level by level.',
        subjects: [
            { slug: 'laravel', label: 'Laravel', active: true },
            { slug: 'nodejs',  label: 'Node.js', active: true },
            { slug: 'express', label: 'Express', active: true },
        ],
    },
    databases: {
        title: 'Databases',
        desc: 'Master relational and NoSQL databases — queries, schemas, indexing, and scalability.',
        subjects: [
            { slug: 'mysql',      label: 'MySQL',      active: true },
            { slug: 'sql',        label: 'SQL Theory',  active: true },
            { slug: 'postgresql', label: 'PostgreSQL',  active: true },
            { slug: 'mongodb',    label: 'MongoDB',     active: false },
            { slug: 'redis',      label: 'Redis',       active: false },
            { slug: 'clickhouse', label: 'ClickHouse',  active: false },
        ],
    },
} as const;

type CategoryKey = keyof typeof CATEGORY_CONFIG;

function SubjectBadge({ slug }: { slug: string }) {
    const cfg = LANG_BADGE[slug];
    if (!cfg) {
        const letter = slug.charAt(0).toUpperCase();
        return (
            <div className="learn-subj-badge" style={{ background: '#6366f1', color: '#fff', fontSize: '1rem' }}>
                {letter}
            </div>
        );
    }
    const fontSize = cfg.text.length === 1 ? '1rem'
                   : cfg.text.length === 2 ? '0.8125rem'
                   : '0.675rem';
    return (
        <div className="learn-subj-badge" style={{ background: cfg.bg, color: cfg.color, fontSize }}>
            {cfg.text}
        </div>
    );
}

export default function LearningCategoryPage() {
    const { category } = useParams<{ category: string }>();
    const config = category && category in CATEGORY_CONFIG
        ? CATEGORY_CONFIG[category as CategoryKey]
        : null;

    const { data: tracks = [], isLoading: tracksLoading } = useTracks();
    const feId  = tracks.find(t => t.slug === 'frontend-engineering')?.id ?? 0;
    const beId  = tracks.find(t => t.slug === 'backend-engineering')?.id ?? 0;
    const fsdId = tracks.find(t => t.slug === 'full-stack-web-development')?.id ?? 0;
    const dbId  = tracks.find(t => t.slug === 'databases')?.id ?? 0;

    const { data: feSubjects  = [], isLoading: s1 } = useSubjects(feId);
    const { data: beSubjects  = [], isLoading: s2 } = useSubjects(beId);
    const { data: fsdSubjects = [], isLoading: s3 } = useSubjects(fsdId);
    const { data: dbSubjects  = [], isLoading: s4 } = useSubjects(dbId);

    const isLoading = tracksLoading || s1 || s2 || s3 || s4;

    if (!config) return <Navigate to="/learning" replace />;

    const allSubjects = [...feSubjects, ...beSubjects, ...fsdSubjects, ...dbSubjects];
    const subjectMap = new Map<string, Subject>(allSubjects.map(s => [s.slug, s]));

    const categoryLabel =
        category === 'languages' ? 'Languages' :
        category === 'frontend'  ? 'Frontend' :
        category === 'backend'   ? 'Backend' :
        category === 'databases' ? 'Databases' : category ?? '';

    return (
        <div className="learn-page">
            <nav className="breadcrumb" aria-label="Breadcrumb">
                <div className="breadcrumb-item">
                    <Link to="/learning" className="breadcrumb-link">Learning</Link>
                    <ChevronRight size={13} className="breadcrumb-separator" />
                </div>
                <div className="breadcrumb-item">
                    <span className="breadcrumb-current">{categoryLabel}</span>
                </div>
            </nav>

            <div className="page-header">
                <div>
                    <h1 className="page-header-title">{config.title}</h1>
                    <p className="page-header-description">{config.desc}</p>
                </div>
            </div>

            {isLoading ? (
                <div className="learn-subj-grid">
                    {[0, 1, 2, 3].map(i => (
                        <div key={i} className="skeleton" style={{ height: 88, borderRadius: 14 }} />
                    ))}
                </div>
            ) : (
                <div className="learn-subj-grid">
                    {config.subjects.map((item) => {
                        const subject = subjectMap.get(item.slug);

                        if (item.active && subject) {
                            return (
                                <Link
                                    key={item.slug}
                                    to={`/learning/${category}/${item.slug}`}
                                    state={{ subjectId: subject.id }}
                                    className="learn-subj-card learn-subj-card--active"
                                >
                                    <SubjectBadge slug={item.slug} />
                                    <div className="learn-subj-info">
                                        <span className="learn-subj-label">{item.label}</span>
                                        <span className="learn-subj-meta">5 levels · {subject.mcq_question_count} questions</span>
                                    </div>
                                    <span className="learn-subj-arrow">→</span>
                                </Link>
                            );
                        }

                        return (
                            <div key={item.slug} className="learn-subj-card learn-subj-card--soon">
                                <SubjectBadge slug={item.slug} />
                                <div className="learn-subj-info">
                                    <span className="learn-subj-label">{item.label}</span>
                                </div>
                                <span className="learn-soon-badge">Soon</span>
                            </div>
                        );
                    })}
                </div>
            )}
        </div>
    );
}
