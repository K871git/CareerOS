import { Link } from 'react-router-dom';
import {
    Code2, Monitor, Server, Network, Cpu,
    Database, GitBranch, Layers,
} from 'lucide-react';
import '../learning.css';

const CATEGORIES = [
    {
        key: 'languages',
        title: 'Programming Languages',
        desc: 'Master core syntax, concepts, and best practices across programming languages.',
        icon: Code2,
        active: true,
    },
    {
        key: 'frontend',
        title: 'Frontend Development',
        desc: 'Build modern, responsive UIs with frameworks used in production.',
        icon: Monitor,
        active: true,
    },
    {
        key: 'backend',
        title: 'Backend Development',
        desc: 'Design server-side applications, REST APIs, and scalable systems.',
        icon: Server,
        active: true,
    },
    {
        key: 'databases',
        title: 'Databases',
        desc: 'SQL, NoSQL, query optimization, indexing, and schema design patterns.',
        icon: Database,
        active: true,
    },
    {
        key: 'networking',
        title: 'Networking',
        desc: 'Understand protocols, TCP/IP, DNS, HTTP/S and network architecture.',
        icon: Network,
        active: false,
    },
    {
        key: 'os',
        title: 'Operating Systems',
        desc: 'Process management, memory, file systems, concurrency and more.',
        icon: Cpu,
        active: false,
    },
    {
        key: 'dsa',
        title: 'Data Structures & Algorithms',
        desc: 'Core algorithms, complexity analysis, and problem-solving techniques.',
        icon: GitBranch,
        active: false,
    },
    {
        key: 'systemdesign',
        title: 'System Design',
        desc: 'Scalable distributed systems, microservices, and architecture patterns.',
        icon: Layers,
        active: false,
    },
] as const;

export default function LearningTracksPage() {
    return (
        <div className="learn-page">
            <div className="page-header">
                <div>
                    <h1 className="page-header-title">Learning</h1>
                    <p className="page-header-description">
                        Level-based structured paths to build deep engineering knowledge for interviews.
                    </p>
                </div>
            </div>

            <div className="learn-cat-grid">
                {CATEGORIES.map((cat) => {
                    const Icon = cat.icon;
                    if (cat.active) {
                        return (
                            <Link
                                key={cat.key}
                                to={`/learning/${cat.key}`}
                                className="learn-cat-card learn-cat-card--active"
                            >
                                <div className="learn-cat-icon">
                                    <Icon size={20} strokeWidth={1.75} />
                                </div>
                                <div className="learn-cat-body">
                                    <h3 className="learn-cat-title">{cat.title}</h3>
                                    <p className="learn-cat-desc">{cat.desc}</p>
                                </div>
                                <span className="learn-cat-arrow">→</span>
                            </Link>
                        );
                    }
                    return (
                        <div key={cat.key} className="learn-cat-card learn-cat-card--soon">
                            <div className="learn-cat-icon">
                                <Icon size={20} strokeWidth={1.75} />
                            </div>
                            <div className="learn-cat-body">
                                <h3 className="learn-cat-title">{cat.title}</h3>
                                <p className="learn-cat-desc">{cat.desc}</p>
                            </div>
                            <span className="learn-soon-badge">Soon</span>
                        </div>
                    );
                })}
            </div>
        </div>
    );
}
