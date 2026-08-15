import { Link } from 'react-router-dom';
import { Code2, Monitor, Server, ChevronRight } from 'lucide-react';
import '../practice.css';

const ARENAS = [
    {
        id: 'languages',
        title: 'Language Arena',
        desc: 'Practice programming languages and strengthen your core fundamentals.',
        icon: Code2,
        chips: ['C', 'C++', 'C#', 'Java', 'JavaScript', 'Python', 'PHP', 'TypeScript'],
    },
    {
        id: 'frontend',
        title: 'Frontend',
        desc: 'Practice modern frontend technologies and web development fundamentals.',
        icon: Monitor,
        chips: ['React', 'Angular', 'HTML', 'CSS', 'Next.js', 'Vue.js'],
    },
    {
        id: 'backend',
        title: 'Backend',
        desc: 'Practice server-side development, APIs and backend frameworks.',
        icon: Server,
        chips: ['Laravel', 'Node.js', 'Express', 'Django', 'Spring Boot', 'REST APIs'],
    },
] as const;

export default function PracticeFsdPage() {
    return (
        <div className="practice-page">
            <div className="practice-inner">
                <div className="prac-breadcrumb">
                    <Link to="/practice" className="prac-breadcrumb-link">Practice</Link>
                    <span className="prac-breadcrumb-sep">›</span>
                    <span>Full Stack Development</span>
                </div>

                <div className="prac-fsd-header">
                    <h1 className="prac-fsd-title">Full Stack Development</h1>
                    <p className="prac-fsd-desc">
                        Practice the core technologies used in modern full stack development.
                    </p>
                </div>

                <div className="prac-arena-grid">
                    {ARENAS.map((arena) => {
                        const Icon = arena.icon;
                        return (
                            <Link
                                key={arena.id}
                                to={`/practice/fsd/${arena.id}`}
                                className={`prac-arena-card prac-arena-card--${arena.id}`}
                            >
                                <div className="prac-arena-head">
                                    <div className="prac-arena-icon">
                                        <Icon size={24} strokeWidth={1.75} />
                                    </div>
                                    <h2 className="prac-arena-title">{arena.title}</h2>
                                </div>
                                <div className="prac-arena-body">
                                    <p className="prac-arena-desc">{arena.desc}</p>
                                    <div className="prac-arena-chips">
                                        {arena.chips.map(chip => (
                                            <span key={chip} className="prac-arena-chip">{chip}</span>
                                        ))}
                                    </div>
                                </div>
                                <div className="prac-arena-footer">
                                    <span className="prac-arena-cta">
                                        Enter Arena <ChevronRight size={14} />
                                    </span>
                                </div>
                            </Link>
                        );
                    })}
                </div>
            </div>
        </div>
    );
}
