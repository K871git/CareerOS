import { useOutletContext } from 'react-router-dom';
import { BookOpen, Target, TrendingUp, ArrowRight, Zap, CheckCircle, Users, Code2, BarChart2 } from 'lucide-react';
import type { GuestOutletContext } from '../layouts/GuestLayout';
import './home.css';

const features = [
    {
        icon: <BookOpen size={22} />,
        title: 'A Learning Path That Makes Sense',
        description:
            'No more random tutorials or scattered blog posts. Follow curated tracks — subjects, topics, and bite-sized lessons in the exact order that builds real understanding.',
    },
    {
        icon: <Target size={22} />,
        title: 'Practice What Interviewers Actually Ask',
        description:
            'MCQ and theory questions built around real interview patterns. Instant scoring and detailed feedback — every session makes you sharper, not just busier.',
    },
    {
        icon: <TrendingUp size={22} />,
        title: 'A Mirror for Your Weak Areas',
        description:
            'See exactly where you stand across every subject and topic. Stop wasting time on what you already know — fix what is actually holding you back.',
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
            'Take a quick career assessment to pinpoint where you stand — your skills, experience, and target role captured in one place.',
    },
    {
        number: '02',
        title: 'Follow Your Track',
        description:
            'Get a structured path tailored to your level. No more wondering what to study — the track tells you exactly what to do next.',
    },
    {
        number: '03',
        title: 'Practice & Track Growth',
        description:
            'Solve real interview questions, see scores instantly, and watch your weak areas shrink — systematically, not randomly.',
    },
];

const audiences = [
    {
        icon: <Users size={20} />,
        label: 'Placement Students',
        badge: 'Freshers',
        description:
            'Crack campus placements at top product companies. Cover every topic that shows up — from core CS to full-stack development — with a track built for freshers.',
        perks: ['Campus-focused question bank', 'Fundamentals to advanced', 'Track your readiness'],
    },
    {
        icon: <Code2 size={20} />,
        label: 'Junior Engineers',
        badge: '0–2 years',
        description:
            'Fill the gaps that experience has not covered yet. Sharpen your fundamentals and walk into your first mid-level interview with real confidence — not hope.',
        perks: ['Gap analysis across topics', 'Real-world engineering concepts', 'Interview-style practice'],
    },
    {
        icon: <BarChart2 size={20} />,
        label: 'Mid-level Engineers',
        badge: '2–5 years',
        description:
            'Prove your seniority. Tackle advanced questions, system concepts, and benchmark exactly where you stand before your next role switch.',
        perks: ['Advanced & hard-level questions', 'System design coverage', 'Weak area targeting'],
    },
];

const techs = [
    'JavaScript', 'TypeScript', 'React', 'PHP', 'Laravel', 'Python',
    'SQL', 'Data Structures', 'Algorithms', 'System Design', 'REST APIs', 'OOP',
];

const valueProps = [
    { icon: CheckCircle, label: 'Structured learning tracks' },
    { icon: Target, label: 'Real interview questions' },
    { icon: TrendingUp, label: 'Measurable progress' },
];

export default function HomePage() {
    const { openModal } = useOutletContext<GuestOutletContext>();

    return (
        <>
            {/* Hero */}
            <section className="hero">
                <div className="hero-blob hero-blob-1" />
                <div className="hero-blob hero-blob-2" />
                <div className="hero-blob hero-blob-3" />
                <div className="hero-inner">
                    <div className="hero-badge">
                        <span className="hero-badge-dot" />
                        Now in Beta — Free for early users
                    </div>

                    <h1 className="hero-title">
                        The Smarter Way to Build{' '}
                        <span className="hero-title-gradient">Interview Confidence</span>
                    </h1>

                    <p className="hero-description">
                        Preparation without a system is just noise. CareerOS gives you a structured path — learn the right concepts, practice real questions, and track your progress until the interview feels like a formality.
                    </p>

                    <div className="hero-value-row">
                        {valueProps.map(({ icon: Icon, label }) => (
                            <span key={label} className="hero-value-item">
                                <Icon size={15} className="hero-value-icon" />
                                {label}
                            </span>
                        ))}
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
                        <h2 className="section-title section-title--light">From scattered to structured — in three steps</h2>
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
                        <span className="section-label">What you get</span>
                        <h2 className="section-title">Everything you need. Nothing you don't.</h2>
                        <p className="section-description">
                            Built specifically for software engineers — not a generic quiz app, but a focused system that respects your time and rewards your effort.
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
                        <h2 className="section-title">For developers at every stage</h2>
                        <p className="section-description">
                            Whether you are preparing for your first job or your next senior role, CareerOS meets you exactly where you are.
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
                    <p className="tech-label">Technologies &amp; topics covered</p>
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
                    <h2 className="cta-title">Your time is worth the investment.</h2>
                    <p className="cta-description">
                        Every hour on CareerOS builds real confidence — not just completed checkboxes. Start your first track for free and see what a system actually feels like.
                    </p>
                    <button className="btn-primary" onClick={() => openModal('register')}>
                        Create free account <ArrowRight size={16} />
                    </button>
                </div>
            </section>
        </>
    );
}
