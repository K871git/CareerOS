import { Link } from 'react-router-dom';
import {
    Globe, Wifi, LayoutTemplate, RefreshCcw,
    Cpu, Database, GitBranch, Code2,
} from 'lucide-react';
import '../practice.css';

const CATEGORIES = [
    {
        id: 'fsd',
        title: 'Full Stack Development',
        desc: 'Languages, frontend frameworks, and backend technologies.',
        icon: Globe,
        to: '/practice/fsd',
        available: true,
    },
    {
        id: 'networking',
        title: 'Networking',
        desc: 'TCP/IP, HTTP, DNS, OSI model, and network protocols.',
        icon: Wifi,
        available: false,
    },
    {
        id: 'system-design',
        title: 'System Design',
        desc: 'Scalability, load balancing, caching, and distributed systems.',
        icon: LayoutTemplate,
        available: false,
    },
    {
        id: 'sdlc',
        title: 'SDLC',
        desc: 'Software development life cycle, Agile, Scrum, and methodologies.',
        icon: RefreshCcw,
        available: false,
    },
    {
        id: 'os',
        title: 'Operating Systems',
        desc: 'Processes, threads, memory management, and system calls.',
        icon: Cpu,
        available: false,
    },
    {
        id: 'databases',
        title: 'Databases',
        desc: 'SQL, normalization, indexing, transactions, and NoSQL.',
        icon: Database,
        to: '/practice/databases',
        available: true,
    },
    {
        id: 'git',
        title: 'Git & Version Control',
        desc: 'Branching, merging, rebasing, and collaboration workflows.',
        icon: GitBranch,
        available: false,
    },
    {
        id: 'dsa',
        title: 'Data Structures & Algorithms',
        desc: 'Arrays, trees, graphs, sorting, searching, and complexity.',
        icon: Code2,
        available: false,
    },
] as const;

export default function PracticeHomePage() {
    return (
        <div className="practice-page">
            <div className="practice-inner">
                <div className="prac-home-header">
                    <h1 className="prac-home-title">Practice</h1>
                    <p className="prac-home-subtitle">
                        Select a category to begin practicing interview questions.
                    </p>
                </div>

                <div className="prac-cat-grid">
                    {CATEGORIES.map((cat) => {
                        const Icon = cat.icon;
                        if (cat.available) {
                            return (
                                <Link
                                    key={cat.id}
                                    to={cat.to}
                                    className="prac-cat-card prac-cat-card--active"
                                >
                                    <div className="prac-cat-icon">
                                        <Icon size={20} strokeWidth={1.75} />
                                    </div>
                                    <div className="prac-cat-content">
                                        <h3 className="prac-cat-title">{cat.title}</h3>
                                        <p className="prac-cat-desc">{cat.desc}</p>
                                    </div>
                                    <span className="prac-cat-cta">Practice →</span>
                                </Link>
                            );
                        }
                        return (
                            <div key={cat.id} className="prac-cat-card prac-cat-card--soon">
                                <div className="prac-cat-icon">
                                    <Icon size={20} strokeWidth={1.75} />
                                </div>
                                <div className="prac-cat-content">
                                    <h3 className="prac-cat-title">{cat.title}</h3>
                                    <p className="prac-cat-desc">{cat.desc}</p>
                                </div>
                                <span className="prac-soon-badge">Coming Soon</span>
                            </div>
                        );
                    })}
                </div>
            </div>
        </div>
    );
}
