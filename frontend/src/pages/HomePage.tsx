import { useOutletContext } from 'react-router-dom';
import { BookOpen, Target, TrendingUp, ArrowRight, Zap, CheckCircle, Users, Code2, BarChart2 } from 'lucide-react';
import type { GuestOutletContext } from '../layouts/GuestLayout';
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

const steps = [
    {
        number: '01',
        title: 'Assess Your Level',
        description:
            'Take a quick career assessment to identify where you stand — skills, experience, and goals all in one place.',
    },
    {
        number: '02',
        title: 'Follow Your Track',
        description:
            'Get a structured learning path with curated subjects, topics, and bite-sized lessons tailored to your level.',
    },
    {
        number: '03',
        title: 'Practice & Improve',
        description:
            'Solve MCQ and theory questions, see scores instantly, and track improvement across all weak areas systematically.',
    },
];

const audiences = [
    {
        icon: <Users size={20} />,
        label: 'Placement Students',
        badge: 'Freshers',
        description:
            'Crack campus placements at top product companies. Cover every topic that shows up — from core CS to full-stack web development.',
        perks: ['Campus-focused question bank', 'Fundamentals to advanced', 'Track your readiness'],
    },
    {
        icon: <Code2 size={20} />,
        label: 'Junior Engineers',
        badge: '0–2 years',
        description:
            'Fill knowledge gaps, sharpen fundamentals, and prepare confidently for your first mid-level interview.',
        perks: ['Gap analysis across topics', 'Real-world engineering concepts', 'Interview-style practice'],
    },
    {
        icon: <BarChart2 size={20} />,
        label: 'Mid-level Engineers',
        badge: '2–5 years',
        description:
            'Prove your seniority. Tackle hard questions, advanced system concepts, and benchmark your skills before switching roles.',
        perks: ['Advanced & hard-level questions', 'System design coverage', 'Weak area targeting'],
    },
];

const techs = [
    'JavaScript', 'TypeScript', 'React', 'PHP', 'Laravel', 'Python',
    'SQL', 'Data Structures', 'Algorithms', 'System Design', 'REST APIs', 'OOP',
];

export default function HomePage() {
    const { openModal } = useOutletContext<GuestOutletContext>();

    return (
        <>
            {/* Hero */}
            <section className="hero">
                <div className="hero-blob hero-blob-1" />
                <div className="hero-blob hero-blob-2" />
                <div className="hero-inner">
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
                        <button className="btn-primary" onClick={() => openModal('register')}>
                            Get started free <ArrowRight size={16} />
                        </button>
                        <button className="btn-ghost" onClick={() => openModal('login')}>
                            Sign in
                        </button>
                    </div>

                    <div className="hero-stats">
                        {stats.map((s) => (
                            <div key={s.label} className="hero-stat-item">
                                <div className="hero-stat-value">{s.value}</div>
                                <div className="hero-stat-label">{s.label}</div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* How it Works */}
            <section className="how-it-works">
                <div className="section-inner">
                    <div className="section-header">
                        <span className="section-label section-label--light">How it works</span>
                        <h2 className="section-title section-title--light">Three steps to interview-ready</h2>
                        <p className="section-description section-description--light">
                            No fluff, no confusion. A clear system that takes you from where you are to where you need to be.
                        </p>
                    </div>
                    <div className="steps-grid">
                        {steps.map((step) => (
                            <div className="step-card" key={step.number}>
                                <div className="step-number">{step.number}</div>
                                <h3 className="step-title">{step.title}</h3>
                                <p className="step-description">{step.description}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Features */}
            <section className="features">
                <div className="section-inner">
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
                </div>
            </section>

            {/* Who is it for */}
            <section className="audience-section">
                <div className="section-inner">
                    <div className="section-header">
                        <span className="section-label">Built for you</span>
                        <h2 className="section-title">For developers at every level</h2>
                        <p className="section-description">
                            Whether you're preparing for your first job or your next senior role, CareerOS meets you where you are.
                        </p>
                    </div>
                    <div className="audience-grid">
                        {audiences.map((a) => (
                            <div className="audience-card" key={a.label}>
                                <div className="audience-card-top">
                                    <div className="audience-icon">{a.icon}</div>
                                    <span className="audience-badge">{a.badge}</span>
                                </div>
                                <h3 className="audience-title">{a.label}</h3>
                                <p className="audience-desc">{a.description}</p>
                                <ul className="audience-perks">
                                    {a.perks.map((perk) => (
                                        <li key={perk} className="audience-perk-item">
                                            <CheckCircle size={13} className="perk-check" />
                                            {perk}
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Technologies */}
            <section className="tech-section">
                <div className="tech-section-inner">
                    <p className="tech-label">Technologies & topics covered</p>
                    <div className="tech-tags">
                        {techs.map((t) => (
                            <span className="tech-tag" key={t}>{t}</span>
                        ))}
                    </div>
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
                    <button className="btn-primary" onClick={() => openModal('register')}>
                        Create free account <ArrowRight size={16} />
                    </button>
                </div>
            </section>
        </>
    );
}
