import { Link } from 'react-router-dom';
import { BookOpen, Target, TrendingUp, ArrowRight, Zap } from 'lucide-react';
import './home.css';

const features = [
    {
        icon: <BookOpen size={22} />,
        title: 'Structured Learning Tracks',
        description:
            'Follow curated paths from fundamentals to advanced topics. Each track is broken into subjects, topics, and bite-sized lessons.',
    },
    {
        icon: <Target size={22} />,
        title: 'Real Interview Practice',
        description:
            'Practice MCQ and theory questions modeled after actual interviews at top tech companies. Instant scoring and detailed feedback.',
    },
    {
        icon: <TrendingUp size={22} />,
        title: 'Progress Tracking',
        description:
            'See exactly where you stand across every track, subject, and topic. Know your weak areas and fix them systematically.',
    },
];

const stats = [
    { value: '500+', label: 'Practice Questions' },
    { value: '10+', label: 'Learning Tracks' },
    { value: '3', label: 'Engineering Levels' },
];

export default function HomePage() {
    return (
        <>
            {/* Hero */}
            <section className="hero">
                <div className="hero-badge">
                    <span className="hero-badge-dot" />
                    Now in Beta — Free for early users
                </div>

                <h1 className="hero-title">
                    Accelerate Your{' '}
                    <span className="hero-title-gradient">Software Engineering</span>
                    <br />Career
                </h1>

                <p className="hero-description">
                    Master interviews, learn real-world engineering concepts, and track your growth — all in one focused platform built for developers.
                </p>

                <div className="hero-actions">
                    <Link to="/auth/register" className="btn-primary">
                        Get started free <ArrowRight size={16} />
                    </Link>
                    <Link to="/auth/login" className="btn-ghost">
                        Sign in
                    </Link>
                </div>

                <div className="hero-stats">
                    {stats.map((s) => (
                        <div key={s.label}>
                            <div className="hero-stat-value">{s.value}</div>
                            <div className="hero-stat-label">{s.label}</div>
                        </div>
                    ))}
                </div>
            </section>

            {/* Features */}
            <section className="features">
                <div className="section-header">
                    <span className="section-label">Why CareerOS</span>
                    <h2 className="section-title">Everything you need to land the job</h2>
                    <p className="section-description">
                        Built specifically for software engineers — not a generic quiz platform, but a focused career growth system.
                    </p>
                </div>

                <div className="features-grid">
                    {features.map((f) => (
                        <div className="feature-card" key={f.title}>
                            <div className="feature-icon">{f.icon}</div>
                            <h3 className="feature-title">{f.title}</h3>
                            <p className="feature-description">{f.description}</p>
                        </div>
                    ))}
                </div>
            </section>

            {/* CTA */}
            <section className="cta-section">
                <div className="cta-card">
                    <div className="feature-icon" style={{ margin: '0 auto 1.5rem' }}>
                        <Zap size={22} />
                    </div>
                    <h2 className="cta-title">Ready to level up your career?</h2>
                    <p className="cta-description">
                        Join engineers who are preparing smarter — not just harder. Create your free account and start your first track today.
                    </p>
                    <Link to="/auth/register" className="btn-primary">
                        Create free account <ArrowRight size={16} />
                    </Link>
                </div>
            </section>
        </>
    );
}
