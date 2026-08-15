import { Link, useParams, Navigate } from 'react-router-dom';
import {
    FileCode2, Code2, Layers, Boxes, Globe, Palette,
    Monitor, Server, Package, GitBranch,
} from 'lucide-react';
import { useTracks, useSubjects } from '../../learning/hooks/useLearning';
import type { Subject } from '../../../types/api';
import '../practice.css';

// Brand colors for each language — makes the icon instantly recognizable
const LANG_BADGE: Record<string, { text: string; bg: string; color: string }> = {
    javascript: { text: 'JS',  bg: '#F7DF1E', color: '#1a1a1a' },
    typescript: { text: 'TS',  bg: '#3178C6', color: '#ffffff' },
    python:     { text: 'Py',  bg: '#3776AB', color: '#FFD43B' },
    php:        { text: 'php', bg: '#777BB4', color: '#ffffff' },
    java:       { text: 'Ja',  bg: '#ED8B00', color: '#ffffff' },
    c:          { text: 'C',   bg: '#283593', color: '#ffffff' },
    cpp:        { text: 'C++', bg: '#00599C', color: '#ffffff' },
    csharp:     { text: 'C#',  bg: '#512BD4', color: '#ffffff' },
};

function LanguageBadge({ slug }: { slug: string }) {
    const cfg = LANG_BADGE[slug];
    if (!cfg) return null;
    const fontSize = cfg.text.length === 1 ? '1rem'
                   : cfg.text.length === 2 ? '0.8125rem'
                   : '0.675rem';
    return (
        <div className="lang-badge" style={{ background: cfg.bg, color: cfg.color, fontSize }}>
            {cfg.text}
        </div>
    );
}

const ARENA_CONFIG = {
    languages: {
        title: 'Language Arena',
        breadcrumb: 'Language Arena',
        desc: 'Practice programming languages and strengthen your fundamentals.',
        technologies: [
            { slug: 'javascript', label: 'JavaScript', icon: FileCode2 },
            { slug: 'python',     label: 'Python',     icon: Code2 },
            { slug: 'php',        label: 'PHP',        icon: Code2 },
            { slug: 'typescript', label: 'TypeScript', icon: FileCode2 },
            { slug: 'java',       label: 'Java',       icon: Code2 },
            { slug: 'c',          label: 'C',          icon: Code2 },
            { slug: 'cpp',        label: 'C++',        icon: Code2 },
            { slug: 'csharp',     label: 'C#',         icon: Code2 },
        ],
    },
    frontend: {
        title: 'Frontend',
        breadcrumb: 'Frontend',
        desc: 'Practice frontend frameworks and modern web development tools.',
        technologies: [
            { slug: 'react',   label: 'React',   icon: Layers },
            { slug: 'angular', label: 'Angular', icon: Boxes },
            { slug: 'vue',     label: 'Vue.js',  icon: Layers },
            { slug: 'nextjs',  label: 'Next.js', icon: Monitor },
            { slug: 'html',    label: 'HTML',    icon: Globe },
            { slug: 'css',     label: 'CSS',     icon: Palette },
        ],
    },
    backend: {
        title: 'Backend',
        breadcrumb: 'Backend',
        desc: 'Practice backend frameworks, server-side development and APIs.',
        technologies: [
            { slug: 'laravel', label: 'Laravel',     icon: Package },
            { slug: 'nodejs',  label: 'Node.js',     icon: Server },
            { slug: 'express', label: 'Express',     icon: Server },
            { slug: 'django',  label: 'Django',      icon: Server },
            { slug: 'spring',  label: 'Spring Boot', icon: GitBranch },
        ],
    },
} as const;

type ArenaKey = keyof typeof ARENA_CONFIG;

interface ResolvedTech {
    slug: string;
    label: string;
    icon: React.ElementType;
    subject: Subject | null;
}

export default function PracticeFsdArenaPage() {
    const { arena } = useParams<{ arena: string }>();
    const config = arena && arena in ARENA_CONFIG
        ? ARENA_CONFIG[arena as ArenaKey]
        : null;

    // Fetch all FSD-related tracks, then their subjects
    const { data: tracks = [], isLoading: tracksLoading } = useTracks();

    const fsdId = tracks.find(t => t.slug === 'full-stack-web-development')?.id ?? 0;
    const feId  = tracks.find(t => t.slug === 'frontend-engineering')?.id ?? 0;
    const beId  = tracks.find(t => t.slug === 'backend-engineering')?.id ?? 0;

    // enabled: id > 0 is already in the hook — safe to call with 0
    const { data: fsdSubjects = [], isLoading: s1 } = useSubjects(fsdId);
    const { data: feSubjects  = [], isLoading: s2 } = useSubjects(feId);
    const { data: beSubjects  = [], isLoading: s3 } = useSubjects(beId);

    const isLoading = tracksLoading || s1 || s2 || s3;

    if (!config) return <Navigate to="/practice/fsd" replace />;

    // Build slug → subject map across all practice tracks
    const allSubjects = [...fsdSubjects, ...feSubjects, ...beSubjects];
    const subjectMap = new Map<string, Subject>(
        allSubjects.filter(s => s.mcq_question_count >= 1).map(s => [s.slug, s])
    );

    const technologies: ResolvedTech[] = config.technologies.map(tech => ({
        ...tech,
        subject: subjectMap.get(tech.slug) ?? null,
    }));

    // Back-link trackId used only so PracticeLevelPage breadcrumb can navigate
    const anyTrackId = fsdId || feId || beId;
    const isLang = arena === 'languages';

    return (
        <div className="practice-page">
            <div className="practice-inner">
                <div className="prac-breadcrumb">
                    <Link to="/practice" className="prac-breadcrumb-link">Practice</Link>
                    <span className="prac-breadcrumb-sep">›</span>
                    <Link to="/practice/fsd" className="prac-breadcrumb-link">Full Stack Development</Link>
                    <span className="prac-breadcrumb-sep">›</span>
                    <span>{config.breadcrumb}</span>
                </div>

                <div className="prac-fsd-header">
                    <h1 className="prac-fsd-title">{config.title}</h1>
                    <p className="prac-fsd-desc">{config.desc}</p>
                </div>

                {isLoading ? (
                    <div className="prac-tech-grid">
                        {[0, 1, 2, 3, 4, 5].map(i => (
                            <div key={i} className="skeleton" style={{ height: 72, borderRadius: 12 }} />
                        ))}
                    </div>
                ) : (
                    <div className="prac-tech-grid">
                        {technologies.map((tech) => {
                            const Icon = tech.icon;
                            const iconEl = isLang
                                ? <LanguageBadge slug={tech.slug} />
                                : <Icon size={20} strokeWidth={1.75} />;

                            if (tech.subject) {
                                return (
                                    <Link
                                        key={tech.slug}
                                        to={`/practice/subjects/${tech.subject.id}`}
                                        state={{
                                            subjectTitle: tech.subject.title,
                                            trackTitle: config.title,
                                            arenaId: arena,
                                            trackId: anyTrackId,
                                        }}
                                        className="prac-tech-card prac-tech-card--active"
                                    >
                                        <div className={`prac-tech-icon${isLang ? ' prac-tech-icon--lang' : ''}`}>
                                            {iconEl}
                                        </div>
                                        <div className="prac-tech-info">
                                            <span className="prac-tech-label">{tech.label}</span>
                                            <span className="prac-tech-count">
                                                {tech.subject.mcq_question_count} questions
                                            </span>
                                        </div>
                                        <span className="prac-tech-arrow">→</span>
                                    </Link>
                                );
                            }
                            return (
                                <div key={tech.slug} className="prac-tech-card prac-tech-card--soon">
                                    <div className={`prac-tech-icon${isLang ? ' prac-tech-icon--lang' : ''}`}>
                                        {iconEl}
                                    </div>
                                    <div className="prac-tech-info">
                                        <span className="prac-tech-label">{tech.label}</span>
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
