import { useEffect } from 'react';
import { useOutletContext } from 'react-router-dom';
import { ArrowRight, CheckCircle, Users, Code2, BarChart2 } from 'lucide-react';
import type { GuestOutletContext } from '../layouts/GuestLayout';
import StarCanvas from '../components/ui/StarCanvas';
import './home.css';

/* Scroll animation — adds .is-visible to every [data-animate] element
   as it enters the viewport. CSS handles the actual transition.          */
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

/* ── Data ─────────────────────────────────────────────────────────────── */

const painPoints = [
    {
        emoji: '📚',
        title: 'Endless tutorials, zero structure',
        desc: "You watch videos, read blogs, switch courses. But nothing tells you what to study next or whether you're covering the right topics.",
    },
    {
        emoji: '🎯',
        title: "You don't know what interviewers ask",
        desc: "Generic study content rarely matches what actually gets asked. You're guessing what matters and hoping for the best.",
    },
    {
        emoji: '📉',
        title: 'No way to measure readiness',
        desc: "You practice, but have no feedback loop. No one tells you where you're weak or how close you are to being interview-ready.",
    },
];

const featureRows = [
    {
        num: '01',
        title: 'A Learning Path That Makes Sense',
        desc: 'No more random tutorials or scattered blog posts. Follow curated tracks — subjects, topics, and bite-sized lessons in the exact order that builds real understanding.',
        visual: 'track' as const,
    },
    {
        num: '02',
        title: 'Practice What Interviewers Actually Ask',
        desc: 'MCQ and theory questions built around real interview patterns. Instant scoring and detailed feedback — every session makes you sharper, not just busier.',
        visual: 'question' as const,
    },
    {
        num: '03',
        title: 'A Mirror for Your Weak Areas',
        desc: 'See exactly where you stand across every subject and topic. Stop wasting time on what you already know — fix what is actually holding you back.',
        visual: 'analytics' as const,
    },
];

const steps = [
    { number: '01', title: 'Assess Your Level', description: 'Take a quick career assessment to pinpoint where you stand — your skills, experience, and target role captured in one place.' },
    { number: '02', title: 'Follow Your Track', description: 'Get a structured path tailored to your level. No more wondering what to study — the track tells you exactly what to do next.' },
    { number: '03', title: 'Practice & Track Growth', description: 'Solve real interview questions, see scores instantly, and watch your weak areas shrink — systematically, not randomly.' },
];

const audiences = [
    {
        icon: <Users size={20} />,
        label: 'Placement Students',
        badge: 'Freshers',
        color: 'green',
        description: 'Crack campus placements at top product companies. Cover every topic that shows up — from core CS to full-stack development — with a track built for freshers.',
        perks: ['Campus-focused question bank', 'Fundamentals to advanced', 'Track your readiness'],
    },
    {
        icon: <Code2 size={20} />,
        label: 'Junior Engineers',
        badge: '0–2 years',
        color: 'blue',
        description: 'Fill the gaps that experience has not covered yet. Sharpen your fundamentals and walk into your first mid-level interview with real confidence — not hope.',
        perks: ['Gap analysis across topics', 'Real-world engineering concepts', 'Interview-style practice'],
    },
    {
        icon: <BarChart2 size={20} />,
        label: 'Mid-level Engineers',
        badge: '2–5 years',
        color: 'purple',
        description: 'Prove your seniority. Tackle advanced questions, system concepts, and benchmark exactly where you stand before your next role switch.',
        perks: ['Advanced & hard-level questions', 'System design coverage', 'Weak area targeting'],
    },
];

const techGroups = [
    { label: 'Languages', items: ['JavaScript', 'TypeScript', 'Python', 'PHP'] },
    { label: 'Frameworks & Tools', items: ['React', 'Laravel', 'REST APIs'] },
    { label: 'CS Concepts', items: ['Data Structures', 'Algorithms', 'System Design', 'OOP', 'SQL'] },
];

const previewStats = [
    { val: '24', lbl: 'Quizzes', color: '#4f46e5' },
    { val: '78%', lbl: 'Avg Score', color: '#16a34a' },
    { val: '8', lbl: 'Topics', color: '#d97706' },
    { val: '5🔥', lbl: 'Streak', color: '#dc2626' },
];

const previewSubjects = [
    { label: 'JavaScript', pct: 82 },
    { label: 'React', pct: 71 },
    { label: 'Data Structures', pct: 58 },
];

/* ── Feature visual mini-mockups ─────────────────────────────────────── */

function FeatureVisualTrack() {
    return (
        <div className="fv-track">
            <div className="fv-track-header">
                <span className="fv-track-name">JavaScript Track</span>
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
                    <span className="fv-lesson-dot">○</span>DOM &amp; Events
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
                What does <code className="fv-code">typeof null</code> return?
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
        { label: 'JavaScript',      pct: 82, color: '#4f46e5' },
        { label: 'React',           pct: 61, color: '#7c3aed' },
        { label: 'Data Structures', pct: 38, color: '#a855f7' },
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
                Focus next: DS Trees &amp; Graphs
            </div>
        </div>
    );
}

const featureVisuals = {
    track:     <FeatureVisualTrack />,
    question:  <FeatureVisualQuestion />,
    analytics: <FeatureVisualAnalytics />,
};

/* ── Page ─────────────────────────────────────────────────────────────── */

export default function HomePage() {
    const { openModal } = useOutletContext<GuestOutletContext>();
    useScrollAnimation();

    return (
        <>
            {/* ── Hero ───────────────────────────────────────────────── */}
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

                {/* Split: text left / mockup right */}
                <div className="hero-split">
                    <div className="hero-text">
                        <div className="hero-badge">
                            <span className="hero-badge-dot" />
                            Now in Beta — Free for early users
                        </div>
                        <h1 className="hero-title">
                            Stop guessing.<br />
                            Start{' '}<span className="hero-title-gradient">getting hired.</span>
                        </h1>
                        <p className="hero-description">
                            CareerOS gives you a structured prep system — curated learning tracks, real interview questions, and a weak-area tracker that shows exactly what to fix next.
                        </p>
                        <div className="hero-actions">
                            <button className="btn-primary" onClick={() => openModal('register')}>
                                Get started free <ArrowRight size={16} />
                            </button>
                            <a href="#how-it-works" className="hero-ghost-link">See how it works</a>
                        </div>
                        <div className="hero-value-row">
                            <span className="hero-value-item">
                                <CheckCircle size={14} className="hero-value-icon" /> Structured tracks
                            </span>
                            <span className="hero-value-item">
                                <CheckCircle size={14} className="hero-value-icon" /> Real interview questions
                            </span>
                            <span className="hero-value-item">
                                <CheckCircle size={14} className="hero-value-icon" /> Measurable progress
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
                                            <div className="preview-welcome-sub">5-day streak — keep it up.</div>
                                        </div>
                                        <div className="preview-cta-pill">Start Practice →</div>
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
                                            {['JS Closures & Scope', 'Promise chaining', 'DB Indexing'].map(w => (
                                                <div className="preview-weak-row" key={w}>
                                                    <span className="preview-weak-dot" />
                                                    <span>{w}</span>
                                                </div>
                                            ))}
                                            <div className="preview-panel-title" style={{ marginTop: '0.625rem' }}>Next Up</div>
                                            <div className="preview-rec-row">
                                                <div className="preview-rec-icon-box" />
                                                <span>Advanced JS Closures → 12 questions</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Stats strip */}
                <div className="hero-stats-strip">
                    <div className="hero-stat-item">
                        <span className="hero-stat-value">500+</span>
                        <span className="hero-stat-label">Practice Questions</span>
                    </div>
                    <div className="hero-stat-sep" />
                    <div className="hero-stat-item">
                        <span className="hero-stat-value">10+</span>
                        <span className="hero-stat-label">Learning Tracks</span>
                    </div>
                    <div className="hero-stat-sep" />
                    <div className="hero-stat-item">
                        <span className="hero-stat-value">3</span>
                        <span className="hero-stat-label">Engineering Levels</span>
                    </div>
                </div>
            </section>

            {/* ── Pain Points ────────────────────────────────────────── */}
            <section className="pain-section">
                <StarCanvas count={50} />
                <div className="pain-inner">
                    <div className="pain-header" data-animate>
                        <span className="pain-eyebrow">Sound familiar?</span>
                        <h2 className="pain-title">Interview prep is broken.</h2>
                        <p className="pain-sub">You're putting in the hours. But without structure, effort doesn't compound.</p>
                    </div>
                    <div className="pain-grid">
                        {painPoints.map((p, i) => (
                            <div className="pain-card" key={p.title} data-animate data-delay={String(i + 1)}>
                                <div className="pain-emoji">{p.emoji}</div>
                                <h3 className="pain-card-title">{p.title}</h3>
                                <p className="pain-card-desc">{p.desc}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* ── How it Works ───────────────────────────────────────── */}
            <section className="how-it-works" id="how-it-works">
                <StarCanvas count={120} />
                <div className="section-inner">
                    <div className="section-header" data-animate>
                        <span className="section-label section-label--light">How it works</span>
                        <h2 className="section-title section-title--light">From scattered to structured — in three steps</h2>
                        <p className="section-description section-description--light">
                            No fluff, no confusion. A clear system that takes you from where you are to where you need to be.
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

            {/* ── Features — alternating rows ────────────────────────── */}
            <section className="features" id="features">
                <StarCanvas count={42} />
                <div className="features-inner">
                    <div className="section-header" data-animate>
                        <span className="section-label">What you get</span>
                        <h2 className="section-title">Everything you need.<br />Nothing you don't.</h2>
                        <p className="section-description">
                            Built specifically for software engineers — not a generic quiz app, but a focused system that respects your time and rewards your effort.
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

            {/* ── Audience ───────────────────────────────────────────── */}
            <section className="audience-section" id="for-who">
                <StarCanvas count={42} />
                <div className="section-inner">
                    <div className="section-header" data-animate>
                        <span className="section-label">Built for you</span>
                        <h2 className="section-title">For developers at every stage</h2>
                        <p className="section-description">
                            Whether you are preparing for your first job or your next senior role, CareerOS meets you exactly where you are.
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

            {/* ── Tech strip — grouped ───────────────────────────────── */}
            <section className="tech-section">
                <StarCanvas count={70} />
                <div className="tech-section-inner" data-animate>
                    <p className="tech-label">Technologies &amp; topics covered</p>
                    <div className="tech-groups">
                        {techGroups.map(group => (
                            <div className="tech-group" key={group.label}>
                                <span className="tech-group-name">{group.label}</span>
                                <div className="tech-group-tags">
                                    {group.items.map(t => (
                                        <span className="tech-tag" key={t}>{t}</span>
                                    ))}
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* ── CTA ────────────────────────────────────────────────── */}
            <section className="cta-section">
                <StarCanvas count={90} />
                <div className="cta-inner" data-animate>
                    <h2 className="cta-title">Your next interview<br />starts today.</h2>
                    <p className="cta-description">
                        Every hour on CareerOS builds real confidence — not just completed checkboxes. Start your first track free and see what a system actually feels like.
                    </p>
                    <button className="btn-primary btn-primary--lg" onClick={() => openModal('register')}>
                        Create free account <ArrowRight size={18} />
                    </button>
                    <a href="#features" className="cta-secondary-link">Explore features instead ↓</a>
                    <p className="cta-note">No credit card. No fluff. Just structure.</p>
                </div>
            </section>
        </>
    );
}
