import { useEffect } from 'react';
import { Link, useOutletContext } from 'react-router-dom';
import {
    ArrowRight, CheckCircle, Users, Code2, BarChart2,
    BookOpen, GitMerge, Cpu, Shield, TrendingUp, Zap,
} from 'lucide-react';
import type { GuestOutletContext } from '../layouts/GuestLayout';
import StarCanvas from '../components/ui/StarCanvas';
import './home.css';

function useScrollAnimation() {
    useEffect(() => {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        (entry.target as HTMLElement).classList.add('is-visible');
                    }
                });
            },
            { threshold: 0.1, rootMargin: '0px 0px -40px 0px' }
        );
        document.querySelectorAll('[data-animate]').forEach(el => observer.observe(el));
        return () => observer.disconnect();
    }, []);
}

/* ── Data ─────────────────────────────────────────────────────── */

const engineerTraits = [
    'Understand programming and software engineering fundamentals',
    'Review and verify AI-generated code critically',
    'Identify bugs, security risks, and performance problems',
    'Make informed technical and architectural decisions',
    'Use AI tools effectively — without blindly trusting them',
    'Test, validate, and take ownership of software behavior',
];

const capabilities = [
    {
        icon: <BookOpen size={20} />,
        title: 'Strong Fundamentals',
        desc: 'Deep understanding of programming, system design, databases, APIs, and the principles that hold real software together.',
    },
    {
        icon: <Code2 size={20} />,
        title: 'Practical Problem-Solving',
        desc: 'Apply concepts to real engineering scenarios — not just theoretical exercises. Build the ability to reason through actual problems.',
    },
    {
        icon: <GitMerge size={20} />,
        title: 'Code Review & Debugging',
        desc: 'Read code critically. Identify issues, performance problems, and security risks the way experienced engineers do.',
    },
    {
        icon: <Cpu size={20} />,
        title: 'AI-Assisted Engineering',
        desc: 'Work effectively with AI tools — using them as force multipliers while retaining full engineering understanding and judgment.',
    },
    {
        icon: <Shield size={20} />,
        title: 'Testing & Technical Judgment',
        desc: 'Know what good software looks like. Validate behavior, understand trade-offs, and make decisions you can defend and own.',
    },
    {
        icon: <TrendingUp size={20} />,
        title: 'Continuous Growth',
        desc: 'Engineering practices evolve. Build the habits and mindset to stay capable as technologies and workflows change around you.',
    },
];

const pillars = [
    {
        key: 'understand',
        num: '01',
        label: 'Understand',
        color: 'indigo',
        desc: 'Every concept explained deeply — what it is, why it exists, how it works, when to use it, and the real-world trade-offs.',
    },
    {
        key: 'practice',
        num: '02',
        label: 'Practice',
        color: 'violet',
        desc: 'MCQs, scenario-based questions, debugging problems, and practical engineering challenges that build real ability.',
    },
    {
        key: 'measure',
        num: '03',
        label: 'Measure',
        color: 'blue',
        desc: 'Track your performance by topic, subject, and difficulty level. See exactly where you stand and what needs improvement.',
    },
    {
        key: 'evolve',
        num: '04',
        label: 'Evolve',
        color: 'emerald',
        desc: 'Understand AI-assisted development, modern engineering practices, and the changing responsibilities of engineers.',
    },
];

const steps = [
    {
        number: '01',
        title: 'Assess Your Foundations',
        description: 'Complete a quick engineering assessment to understand your current skills, experience level, and target role — so your path starts in the right place.',
    },
    {
        number: '02',
        title: 'Follow Structured Paths',
        description: 'Work through engineering tracks built around real concepts, not random tutorials. Know exactly what to study and why it builds toward something meaningful.',
    },
    {
        number: '03',
        title: 'Practice, Measure & Grow',
        description: 'Solve real engineering problems, track your performance over time, identify weak areas, and systematically close the gaps that matter.',
    },
];

const featureRows = [
    {
        num: '01',
        title: 'Structured Engineering Paths',
        desc: 'Follow curated tracks across subjects, topics, and bite-sized lessons — ordered to build real engineering understanding, not surface-level familiarity with definitions.',
        visual: 'track' as const,
    },
    {
        num: '02',
        title: 'Real Engineering Practice',
        desc: 'MCQs, scenario questions, and conceptual problems built around real engineering challenges. Instant feedback so you know what you got right — and more importantly, why.',
        visual: 'question' as const,
    },
    {
        num: '03',
        title: 'Know Exactly Where You Stand',
        desc: 'Performance tracked by subject, topic, and difficulty level. Stop spending time on what you already know — focus on what actually needs work.',
        visual: 'analytics' as const,
    },
];

const audiences = [
    {
        icon: <Users size={20} />,
        label: 'Students & Beginners',
        badge: 'Getting Started',
        color: 'green',
        description: 'Build strong programming fundamentals, understand how real software systems work, and develop the engineering mindset required for the modern industry.',
        perks: ['Programming fundamentals to advanced concepts', 'Structured step-by-step learning paths', 'Measurable progress at every stage'],
    },
    {
        icon: <Code2 size={20} />,
        label: 'Junior Engineers',
        badge: '0–2 years',
        color: 'blue',
        description: 'Identify and close the gaps experience has not yet covered. Strengthen your technical foundations and develop the engineering judgment that makes you reliable.',
        perks: ['Gap analysis across core engineering topics', 'Real-world concepts beyond the basics', 'Multi-discipline practice and assessment'],
    },
    {
        icon: <BarChart2 size={20} />,
        label: 'Working Engineers',
        badge: '2–5 years',
        color: 'purple',
        description: 'Stay capable as engineering practices evolve. Deepen your understanding of systems, architecture, AI-assisted development, and the judgment that defines strong senior engineers.',
        perks: ['Advanced topics and system design', 'AI-assisted development content', 'Targeted weak area improvement'],
    },
];

const techGroups = [
    {
        label: 'Languages & Frameworks',
        icon: <Code2 size={20} />,
        color: 'indigo',
        desc: 'Core programming languages and the frameworks built around them — the raw material of modern software.',
        items: ['JavaScript', 'TypeScript', 'Python', 'PHP', 'React', 'Laravel'],
    },
    {
        label: 'Engineering Concepts',
        icon: <Cpu size={20} />,
        color: 'violet',
        desc: 'Foundational CS and software engineering principles every professional engineer must understand deeply.',
        items: ['Data Structures', 'System Design', 'Databases', 'APIs', 'OOP', 'Networking'],
    },
    {
        label: 'Modern Engineering',
        icon: <Zap size={20} />,
        color: 'emerald',
        desc: 'Practices and mindsets that define engineering today — including how to work alongside AI effectively.',
        items: ['AI-Assisted Dev', 'Code Review', 'Testing', 'Security', 'SDLC'],
    },
];

const aiFeatures = [
    {
        icon: '💡',
        title: 'Explain why answers are correct',
        desc: 'Understand the reasoning behind every answer, not just which option to pick.',
    },
    {
        icon: '🔍',
        title: 'Simplify difficult concepts',
        desc: 'Get clearer, plain-language explanations for complex engineering topics on demand.',
    },
    {
        icon: '🎯',
        title: 'Hints when you are stuck',
        desc: 'Guided thinking that points you in the right direction without giving away the answer.',
    },
    {
        icon: '✅',
        title: 'Assessment stays objective',
        desc: 'AI assists your learning. Your scores are always based on real, verified answers.',
    },
];

const previewStats = [
    { val: '12', lbl: 'Lessons', color: '#4f46e5' },
    { val: '84%', lbl: 'Accuracy', color: '#16a34a' },
    { val: 'L2',  lbl: 'JS Level', color: '#7c3aed' },
    { val: '6',   lbl: 'Topics',   color: '#0ea5e9' },
];

const previewSubjects = [
    { label: 'JavaScript',    pct: 82 },
    { label: 'System Design', pct: 64 },
    { label: 'Data Structures', pct: 48 },
];

/* ── Feature mini-mockups ─────────────────────────────────────── */

function FeatureVisualTrack() {
    return (
        <div className="fv-track">
            <div className="fv-track-header">
                <span className="fv-track-name">JavaScript — Level 2</span>
                <span className="fv-track-pct">64%</span>
            </div>
            <div className="fv-track-progress-bar">
                <div className="fv-track-progress-fill" style={{ width: '64%' }} />
            </div>
            <div className="fv-lessons">
                <div className="fv-lesson fv-lesson--done">
                    <span className="fv-lesson-check">✓</span>Core Concepts
                </div>
                <div className="fv-lesson fv-lesson--done">
                    <span className="fv-lesson-check">✓</span>Functions &amp; Scope
                </div>
                <div className="fv-lesson fv-lesson--active">
                    <span className="fv-lesson-arrow">→</span>Async &amp; Promises
                </div>
                <div className="fv-lesson">
                    <span className="fv-lesson-dot">○</span>Event Loop &amp; Runtime
                </div>
            </div>
        </div>
    );
}

function FeatureVisualQuestion() {
    return (
        <div className="fv-question">
            <div className="fv-q-meta">JavaScript · Medium</div>
            <div className="fv-q-text">
                What does <code className="fv-code">typeof null</code> return in JavaScript?
            </div>
            <div className="fv-options">
                <div className="fv-option">"null"</div>
                <div className="fv-option fv-option--correct">
                    <span className="fv-option-check">✓</span>"object"
                </div>
                <div className="fv-option">"undefined"</div>
                <div className="fv-option">"boolean"</div>
            </div>
        </div>
    );
}

function FeatureVisualAnalytics() {
    const bars = [
        { label: 'JavaScript',    pct: 82, color: '#4f46e5' },
        { label: 'System Design', pct: 54, color: '#7c3aed' },
        { label: 'Networking',    pct: 31, color: '#a855f7' },
    ];
    return (
        <div className="fv-analytics">
            {bars.map(b => (
                <div className="fv-bar-row" key={b.label}>
                    <span className="fv-bar-label">{b.label}</span>
                    <div className="fv-bar-track">
                        <div className="fv-bar-fill" style={{ width: `${b.pct}%`, background: b.color }} />
                    </div>
                    <span className="fv-bar-pct">{b.pct}%</span>
                </div>
            ))}
            <div className="fv-weak-box">
                <span className="fv-weak-dot" />
                Focus next: Networking fundamentals
            </div>
        </div>
    );
}

const featureVisuals = {
    track:     <FeatureVisualTrack />,
    question:  <FeatureVisualQuestion />,
    analytics: <FeatureVisualAnalytics />,
};

/* ── Page ─────────────────────────────────────────────────────── */

export default function HomePage() {
    const { openModal } = useOutletContext<GuestOutletContext>();
    useScrollAnimation();

    return (
        <>
            {/* ══ 1. HERO ══════════════════════════════════════════ */}
            <section className="hero">
                <div className="hero-blob hero-blob-1" />
                <div className="hero-blob hero-blob-2" />
                <div className="hero-blob hero-blob-3" />
                <StarCanvas count={60} />

                <div className="hero-deco" aria-hidden="true">
                    <svg className="hero-deco-shape shape-1"  viewBox="0 0 80 80"><circle cx="40" cy="40" r="34" fill="none" stroke="currentColor" strokeWidth="1.5" strokeDasharray="6 4"/></svg>
                    <svg className="hero-deco-shape shape-2"  viewBox="0 0 52 52"><polygon points="26,3 49,26 26,49 3,26" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinejoin="round"/></svg>
                    <svg className="hero-deco-shape shape-3"  viewBox="0 0 60 56"><polygon points="30,4 58,52 2,52" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinejoin="round"/></svg>
                    <svg className="hero-deco-shape shape-4"  viewBox="0 0 40 40"><circle cx="20" cy="20" r="16" fill="none" stroke="currentColor" strokeWidth="1.5" strokeDasharray="3 3"/></svg>
                    <svg className="hero-deco-shape shape-5"  viewBox="0 0 30 30"><line x1="15" y1="3" x2="15" y2="27" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/><line x1="3" y1="15" x2="27" y2="15" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/></svg>
                    <svg className="hero-deco-shape shape-6"  viewBox="0 0 56 56"><polygon points="28,4 52,18 52,38 28,52 4,38 4,18" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinejoin="round"/></svg>
                    <svg className="hero-deco-shape shape-7"  viewBox="0 0 38 38"><polygon points="19,2 36,19 19,36 2,19" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinejoin="round" strokeDasharray="3 2"/></svg>
                    <svg className="hero-deco-shape shape-8"  viewBox="0 0 50 50"><path d="M25,25 m-16,0 a16,16 0 1,1 32,0 a12,12 0 1,0 -24,0 a8,8 0 1,1 16,0" fill="none" stroke="currentColor" strokeWidth="1.2" strokeLinecap="round"/></svg>
                    <svg className="hero-deco-shape shape-9"  viewBox="0 0 48 48"><circle cx="24" cy="24" r="20" fill="none" stroke="currentColor" strokeWidth="1.5" strokeDasharray="5 3"/><circle cx="24" cy="24" r="10" fill="none" stroke="currentColor" strokeWidth="1.2"/><circle cx="24" cy="24" r="3" fill="currentColor" opacity="0.45"/></svg>
                    <svg className="hero-deco-shape shape-10" viewBox="0 0 28 28"><line x1="4" y1="4" x2="24" y2="24" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/><line x1="24" y1="4" x2="4" y2="24" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/></svg>
                    <svg className="hero-deco-shape shape-11" viewBox="0 0 44 44"><rect x="6" y="6" width="32" height="32" rx="8" fill="none" stroke="currentColor" strokeWidth="1.5" strokeDasharray="4 3"/></svg>
                    <svg className="hero-deco-shape shape-12" viewBox="0 0 32 30"><polygon points="16,3 30,27 2,27" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinejoin="round"/></svg>
                </div>

                <div className="hero-split">
                    <div className="hero-text">
                        <div className="hero-badge">
                            <span className="hero-badge-dot" />
                            CareerOS · Engineering Growth Platform
                        </div>
                        <h1 className="hero-title">
                            The Path for<br />
                            <span className="hero-title-gradient">NextGen Engineers.</span>
                        </h1>
                        <p className="hero-description">
                            Build strong fundamentals. Practice real engineering problems. Understand AI-assisted development. Grow into the engineer the future demands.
                        </p>
                        <div className="hero-actions">
                            <button className="btn-primary" onClick={() => openModal('register')}>
                                Start Your Engineering Journey <ArrowRight size={16} />
                            </button>
                            <a href="#features" className="hero-ghost-link">
                                See How It Works
                            </a>
                        </div>
                        <div className="hero-value-row">
                            <span className="hero-value-item">
                                <CheckCircle size={14} className="hero-value-icon" /> Engineering fundamentals
                            </span>
                            <span className="hero-value-item">
                                <CheckCircle size={14} className="hero-value-icon" /> AI-assisted development
                            </span>
                            <span className="hero-value-item">
                                <CheckCircle size={14} className="hero-value-icon" /> Measurable growth
                            </span>
                        </div>
                    </div>

                    {/* Browser mockup */}
                    <div className="hero-preview" aria-hidden="true">
                        <div className="preview-browser">
                            <div className="preview-chrome">
                                <div className="preview-chrome-dots"><span /><span /><span /></div>
                                <div className="preview-chrome-url">careeros.app/dashboard</div>
                            </div>
                            <div className="preview-ui">
                                <div className="preview-ui-nav">
                                    <span className="preview-logo">CareerOS</span>
                                    <div className="preview-nav-links">
                                        <span className="pnl pnl--active">Dashboard</span>
                                        <span className="pnl">Practice</span>
                                        <span className="pnl">Learning</span>
                                    </div>
                                </div>
                                <div className="preview-ui-body">
                                    <div className="preview-welcome-row">
                                        <div>
                                            <div className="preview-welcome-title">Good morning, Rahul 👋</div>
                                            <div className="preview-welcome-sub">JavaScript — Level 2 active.</div>
                                        </div>
                                        <div className="preview-cta-pill">Continue →</div>
                                    </div>
                                    <div className="preview-stats-row">
                                        {previewStats.map(s => (
                                            <div className="preview-stat" key={s.lbl}>
                                                <div className="preview-stat-val" style={{ color: s.color }}>{s.val}</div>
                                                <div className="preview-stat-lbl">{s.lbl}</div>
                                            </div>
                                        ))}
                                    </div>
                                    <div className="preview-panels">
                                        <div className="preview-panel">
                                            <div className="preview-panel-title">Performance by Subject</div>
                                            {previewSubjects.map(s => (
                                                <div className="preview-bar-row" key={s.label}>
                                                    <span className="preview-bar-lbl">{s.label}</span>
                                                    <div className="preview-bar-track">
                                                        <div className="preview-bar-fill" style={{ width: `${s.pct}%` }} />
                                                    </div>
                                                    <span className="preview-bar-pct">{s.pct}%</span>
                                                </div>
                                            ))}
                                        </div>
                                        <div className="preview-panel">
                                            <div className="preview-panel-title">Weak Areas</div>
                                            {['Networking basics', 'Promise chaining', 'DB Indexing'].map(w => (
                                                <div className="preview-weak-row" key={w}>
                                                    <span className="preview-weak-dot" />
                                                    <span>{w}</span>
                                                </div>
                                            ))}
                                            <div className="preview-panel-title" style={{ marginTop: '0.625rem' }}>Recommended</div>
                                            <div className="preview-rec-row">
                                                <div className="preview-rec-icon-box" />
                                                <span>Networking Fundamentals → 10 Q</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="hero-stats-strip">
                    <div className="hero-stat-item">
                        <span className="hero-stat-value">500+</span>
                        <span className="hero-stat-label">Practice Questions</span>
                    </div>
                    <div className="hero-stat-sep" />
                    <div className="hero-stat-item">
                        <span className="hero-stat-value">10+</span>
                        <span className="hero-stat-label">Engineering Tracks</span>
                    </div>
                    <div className="hero-stat-sep" />
                    <div className="hero-stat-item">
                        <span className="hero-stat-value">8</span>
                        <span className="hero-stat-label">Theory Areas</span>
                    </div>
                </div>
            </section>

            {/* ══ 2. WHY CAREEROS ══════════════════════════════════ */}
            <section className="why-section">
                <StarCanvas count={80} />
                <div className="why-inner">
                    <div className="why-left" data-animate>
                        <span className="section-label section-label--light">Why CareerOS?</span>
                        <h2 className="why-title">
                            Engineering is evolving.<br />Are you growing with it?
                        </h2>
                        <p className="why-desc">
                            AI is becoming part of how software is built — from generating code to assisting with debugging, testing, and implementation. The role of the engineer is changing.
                        </p>
                        <p className="why-desc">
                            But engineers are not becoming obsolete. The engineers who thrive are those who understand technology deeply, can verify what AI produces, and take real ownership of the software they build.
                        </p>
                        <blockquote className="why-belief">
                            AI can assist with implementation. Engineering understanding, verification, responsibility, and judgment remain essential.
                        </blockquote>
                    </div>
                    <div className="why-right" data-animate data-delay="1">
                        <div className="why-traits-label">A NextGen Engineer can:</div>
                        <ul className="why-traits-list">
                            {engineerTraits.map(trait => (
                                <li key={trait} className="why-trait-item">
                                    <CheckCircle size={15} className="why-trait-check" />
                                    {trait}
                                </li>
                            ))}
                        </ul>
                        <div className="why-platform-note">
                            CareerOS is built to help you develop all of these capabilities.
                        </div>
                    </div>
                </div>
            </section>

            {/* ══ 3. CAPABILITIES ══════════════════════════════════ */}
            <section className="capabilities-section">
                <StarCanvas count={40} />
                <div className="cap-inner">
                    <div className="section-header" data-animate>
                        <span className="section-label">What you will build</span>
                        <h2 className="section-title">Capabilities that define<br />a strong engineer.</h2>
                        <p className="section-description">
                            CareerOS focuses on the real skills that matter in the modern engineering world — not just what shows up on a topic checklist.
                        </p>
                    </div>
                    <div className="cap-grid">
                        {capabilities.map((c, i) => (
                            <div className="cap-card" key={c.title} data-animate data-delay={String((i % 3) + 1)}>
                                <div className="cap-icon">{c.icon}</div>
                                <h3 className="cap-title">{c.title}</h3>
                                <p className="cap-desc">{c.desc}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* ══ 4. PILLARS ════════════════════════════════════════ */}
            <section className="pillars-section">
                <div className="section-inner">
                    <div className="section-header" data-animate>
                        <span className="section-label">How it is structured</span>
                        <h2 className="section-title">Learn. Practice. Measure. Evolve.</h2>
                        <p className="section-description">
                            Four pillars that work together to build real engineering capability — not just isolated knowledge.
                        </p>
                    </div>
                    <div className="pillars-grid">
                        {pillars.map((p, i) => (
                            <div
                                className={`pillar-card pillar-card--${p.color}`}
                                key={p.key}
                                data-animate
                                data-delay={String(i + 1)}
                            >
                                <div className="pillar-num">{p.num}</div>
                                <h3 className="pillar-label">{p.label}</h3>
                                <p className="pillar-desc">{p.desc}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* ══ 5. HOW IT WORKS ══════════════════════════════════ */}
            <section className="how-it-works" id="how-it-works">
                <StarCanvas count={120} />
                <div className="section-inner">
                    <div className="section-header" data-animate>
                        <span className="section-label section-label--light">The journey</span>
                        <h2 className="section-title section-title--light">
                            A clear path from where you are<br />to where you want to be.
                        </h2>
                        <p className="section-description section-description--light">
                            No random tutorials. No guessing. A structured system that builds engineering capability step by step.
                        </p>
                    </div>
                    <div className="steps-grid">
                        {steps.map((step, i) => (
                            <div className="step-card" key={step.number} data-animate data-delay={String(i + 1)}>
                                <div className="step-number">{step.number}</div>
                                <h3 className="step-title">{step.title}</h3>
                                <p className="step-description">{step.description}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* ══ 6. FEATURES ══════════════════════════════════════ */}
            <section className="features" id="features">
                <StarCanvas count={42} />
                <div className="features-inner">
                    <div className="section-header" data-animate>
                        <span className="section-label">What you get</span>
                        <h2 className="section-title">Everything you need.<br />Nothing you don't.</h2>
                        <p className="section-description">
                            Built specifically for engineers — a focused system that respects your time and actually builds understanding, not just familiarity.
                        </p>
                    </div>
                    {featureRows.map((f, i) => (
                        <div
                            className={`feature-row${i % 2 !== 0 ? ' feature-row--flip' : ''}`}
                            key={f.title}
                            data-animate
                            data-delay={String((i % 2) + 1)}
                        >
                            <div className="feature-row-visual">
                                <span className="feature-row-num">{f.num}</span>
                                {featureVisuals[f.visual]}
                            </div>
                            <div className="feature-row-text">
                                <h3 className="feature-row-title">{f.title}</h3>
                                <p className="feature-row-desc">{f.desc}</p>
                            </div>
                        </div>
                    ))}
                </div>
            </section>

            {/* ══ 7. WHO IT'S FOR ══════════════════════════════════ */}
            <section className="audience-section" id="for-who">
                <StarCanvas count={42} />
                <div className="section-inner">
                    <div className="section-header" data-animate>
                        <span className="section-label">Who it is for</span>
                        <h2 className="section-title">For engineers at every stage.</h2>
                        <p className="section-description">
                            Whether you are just starting out or a working engineer looking to stay sharp, CareerOS meets you where you are and grows with you.
                        </p>
                    </div>
                    <div className="audience-grid">
                        {audiences.map((a, i) => (
                            <div
                                className={`audience-card audience-card--${a.color}`}
                                key={a.label}
                                data-animate
                                data-delay={String(i + 1)}
                            >
                                <div className="audience-card-header">
                                    <div className="audience-icon">{a.icon}</div>
                                    <span className="audience-badge">{a.badge}</span>
                                </div>
                                <div className="audience-card-body">
                                    <h3 className="audience-title">{a.label}</h3>
                                    <p className="audience-desc">{a.description}</p>
                                    <ul className="audience-perks">
                                        {a.perks.map(perk => (
                                            <li key={perk} className="audience-perk-item">
                                                <CheckCircle size={13} className="perk-check" />
                                                {perk}
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* ══ 8. AI LEARNING COMPANION ═════════════════════════ */}
            <section className="ai-section">
                <StarCanvas count={50} />
                <div className="ai-inner">
                    <div className="ai-header" data-animate>
                        <span className="section-label section-label--light">AI Learning Companion</span>
                        <h2 className="section-title section-title--light">
                            AI that explains.<br />Not one that replaces.
                        </h2>
                        <p className="section-description section-description--light">
                            CareerOS uses an AI learning assistant powered by Ollama to help you understand — not to hand you answers or replace real, objective assessment.
                        </p>
                    </div>
                    <div className="ai-features-grid" data-animate data-delay="1">
                        {aiFeatures.map(f => (
                            <div className="ai-feature" key={f.title}>
                                <div className="ai-feature-icon">{f.icon}</div>
                                <div>
                                    <div className="ai-feature-title">{f.title}</div>
                                    <div className="ai-feature-desc">{f.desc}</div>
                                </div>
                            </div>
                        ))}
                    </div>
                    <p className="ai-note" data-animate data-delay="2">
                        Every time you get something wrong, you get a full explanation of why — not just the correct answer, but the reasoning behind it. So every mistake actually teaches you something.
                    </p>
                </div>
            </section>

            {/* ══ 9. LEARNING PATHS ════════════════════════════════ */}
            <section className="tech-section">
                <StarCanvas count={70} />
                <div className="tech-section-inner">
                    <div className="section-header" data-animate>
                        <span className="section-label section-label--light">Curriculum</span>
                        <h2 className="section-title section-title--light">What we cover.</h2>
                        <p className="section-description section-description--light">
                            A growing library of engineering topics — from core fundamentals to the modern practices that matter right now.
                        </p>
                    </div>
                    <div className="tech-cards-grid">
                        {techGroups.map((group, i) => (
                            <div
                                className={`tech-card tech-card--${group.color}`}
                                key={group.label}
                                data-animate
                                data-delay={String(i + 1)}
                            >
                                <div className="tech-card-header">
                                    <div className={`tech-card-icon tech-card-icon--${group.color}`}>
                                        {group.icon}
                                    </div>
                                    <h3 className="tech-card-title">{group.label}</h3>
                                </div>
                                <p className="tech-card-desc">{group.desc}</p>
                                <div className="tech-card-tags">
                                    {group.items.map(t => (
                                        <span className="tech-tag" key={t}>{t}</span>
                                    ))}
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* ══ 10. FINAL CTA ════════════════════════════════════ */}
            <section className="cta-section">
                <StarCanvas count={90} />
                <div className="cta-inner" data-animate>
                    <h2 className="cta-title">Your engineering journey<br />starts today.</h2>
                    <p className="cta-description">
                        The future of software engineering belongs to people who understand technology deeply, use AI intelligently, and take full responsibility for the software they build. Start building that foundation now.
                    </p>
                    <button className="btn-primary btn-primary--lg" onClick={() => openModal('register')}>
                        Start Your Engineering Journey <ArrowRight size={18} />
                    </button>
                    <a href="#features" className="cta-secondary-link">See how it works first →</a>
                    <p className="cta-note">Free to start. No credit card required.</p>
                </div>
            </section>
        </>
    );
}
