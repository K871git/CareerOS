import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { ArrowLeft, CheckCircle } from 'lucide-react';
import '../consent.css';

const STORAGE_KEY = 'careeros_terms_read';

export default function TermsPage() {
    const navigate = useNavigate();
    const [scrollPct,  setScrollPct]  = useState(0);
    const [accepted,   setAccepted]   = useState(() => !!localStorage.getItem(STORAGE_KEY));

    useEffect(() => {
        const onScroll = () => {
            const el = document.documentElement;
            const pct = Math.min(1, (el.scrollTop + window.innerHeight) / el.scrollHeight);
            setScrollPct(pct);
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
        return () => window.removeEventListener('scroll', onScroll);
    }, []);

    function handleAccept() {
        localStorage.setItem(STORAGE_KEY, '1');
        setAccepted(true);
        window.close();
    }

    const barVisible = scrollPct >= 0.88 || accepted;

    return (
        <div className="legal-page">
            {/* Top bar */}
            <nav className="legal-topbar">
                <div className="legal-topbar-brand">
                    <div className="legal-topbar-logo">CO</div>
                    <span className="legal-topbar-name">CareerOS</span>
                </div>
                <button className="legal-topbar-back" onClick={() => navigate(-1)}>
                    <ArrowLeft size={14} /> Back
                </button>
            </nav>

            {/* Hero */}
            <div className="legal-hero">
                <div className="legal-hero-tag">Legal Document</div>
                <h1 className="legal-hero-title">Terms &amp; Conditions</h1>
                <p className="legal-hero-sub">Please read these terms carefully before using CareerOS</p>
            </div>

            {/* Meta */}
            <div className="legal-meta-bar">
                <span className="legal-meta-item"><strong>Version:</strong> 1.0</span>
                <span className="legal-meta-item"><strong>Effective Date:</strong> 17 September 2026</span>
                <span className="legal-meta-item"><strong>Governing Law:</strong> Indian Law (DPDP Act, 2023 &amp; IT Act, 2000)</span>
            </div>

            {/* Content */}
            <div className="legal-content">

                {/* Table of contents */}
                <div className="legal-toc">
                    <p className="legal-toc-title">Table of Contents</p>
                    <ol className="legal-toc-list">
                        <li><a href="#definitions">Definitions</a></li>
                        <li><a href="#acceptance">Acceptance of Terms</a></li>
                        <li><a href="#eligibility">Eligibility &amp; Account</a></li>
                        <li><a href="#services">Services Provided</a></li>
                        <li><a href="#user-content">User Content &amp; Conduct</a></li>
                        <li><a href="#ip">Intellectual Property</a></li>
                        <li><a href="#data">Personal Data &amp; Privacy</a></li>
                        <li><a href="#ai">AI-Generated Content</a></li>
                        <li><a href="#disclaimers">Disclaimers &amp; Limitations</a></li>
                        <li><a href="#termination">Termination</a></li>
                        <li><a href="#dispute">Dispute Resolution</a></li>
                        <li><a href="#changes">Changes to Terms</a></li>
                        <li><a href="#contact">Contact &amp; Grievance</a></li>
                    </ol>
                </div>

                {/* Introduction */}
                <div className="legal-highlight-box" style={{ marginBottom: '2rem' }}>
                    <p>
                        These Terms &amp; Conditions ("Terms") constitute a legally binding agreement between you ("User")
                        and CareerOS ("Platform", "we", "us", or "our"). By accessing or using CareerOS, you confirm that you have
                        read, understood, and agree to be bound by these Terms. If you do not agree, you must not use the Platform.
                    </p>
                </div>

                {/* §1 Definitions */}
                <section className="legal-section" id="definitions">
                    <p className="legal-section-num">Section 1</p>
                    <h2>Definitions</h2>
                    <p>In these Terms, the following expressions have the meanings assigned to them:</p>
                    <ul>
                        <li><strong>"CareerOS"</strong> means the web application, APIs, and related services accessible through this platform.</li>
                        <li><strong>"User"</strong> means any individual who registers for or accesses CareerOS.</li>
                        <li><strong>"Personal Data"</strong> has the meaning assigned under the Digital Personal Data Protection Act, 2023 (DPDP Act).</li>
                        <li><strong>"User Content"</strong> means any data, text, assessments, or information submitted by a User on the Platform.</li>
                        <li><strong>"AI Content"</strong> means explanations, recommendations, and analysis generated by artificial intelligence models integrated within CareerOS.</li>
                        <li><strong>"Consent"</strong> means free, specific, informed, unconditional, and unambiguous indication of agreement as defined under §6 of the DPDP Act, 2023.</li>
                        <li><strong>"Data Fiduciary"</strong> means CareerOS, which determines the purpose and means of processing personal data.</li>
                    </ul>
                </section>

                {/* §2 Acceptance */}
                <section className="legal-section" id="acceptance">
                    <p className="legal-section-num">Section 2</p>
                    <h2>Acceptance of Terms</h2>
                    <p>
                        By clicking "I Accept &amp; Continue" on the consent gate, by creating an account, or by otherwise using CareerOS,
                        you acknowledge that you have read these Terms and agree to be bound by them.
                    </p>
                    <p>
                        Your consent is recorded with a timestamp, IP address, and browser identifier in accordance with the
                        Digital Personal Data Protection Act, 2023. This record constitutes evidence of your consent
                        as required by §6(3) of the Act.
                    </p>
                    <div className="legal-highlight-box">
                        <p>
                            <strong>DPDP Act §6(1) Compliance:</strong> Your consent to these Terms is freely given,
                            specific to the stated purposes, fully informed, and unambiguous. You may withdraw consent
                            at any time from your account settings.
                        </p>
                    </div>
                </section>

                {/* §3 Eligibility */}
                <section className="legal-section" id="eligibility">
                    <p className="legal-section-num">Section 3</p>
                    <h2>Eligibility &amp; Account Registration</h2>
                    <p>
                        CareerOS is intended for individuals who are <strong>18 years of age or older</strong>. By using CareerOS,
                        you represent and warrant that you meet this requirement. If you are found to be under 18 years of age,
                        we will promptly delete your account and all associated personal data.
                    </p>
                    <p>When registering an account, you agree to:</p>
                    <ul>
                        <li>Provide accurate, truthful, and complete registration information.</li>
                        <li>Maintain the security of your account credentials and not share them with others.</li>
                        <li>Promptly notify us of any unauthorised use of your account.</li>
                        <li>Be responsible for all activities that occur under your account.</li>
                    </ul>
                    <p>
                        We reserve the right to suspend or terminate accounts found to contain false information
                        or that violate these Terms.
                    </p>
                </section>

                {/* §4 Services */}
                <section className="legal-section" id="services">
                    <p className="legal-section-num">Section 4</p>
                    <h2>Services Provided</h2>
                    <p>CareerOS provides the following services:</p>
                    <ul>
                        <li><strong>Career Assessment</strong> — Personalised evaluation of your technical skills, knowledge areas, and career readiness.</li>
                        <li><strong>Learning Tracks</strong> — Structured educational content organised by subject, level, and topic.</li>
                        <li><strong>Practice Module</strong> — Multiple-choice assessments, timed quizzes, and skill-building exercises.</li>
                        <li><strong>AI Explanations</strong> — AI-powered explanations for assessment answers and learning concepts.</li>
                        <li><strong>SQL Playground</strong> — Interactive SQL execution environment for skill development.</li>
                        <li><strong>Progress Tracking</strong> — Dashboard and analytics tracking your learning journey.</li>
                    </ul>
                    <p>
                        We reserve the right to modify, suspend, or discontinue any aspect of the Services at any time with reasonable
                        prior notice where practicable.
                    </p>
                </section>

                {/* §5 User Content */}
                <section className="legal-section" id="user-content">
                    <p className="legal-section-num">Section 5</p>
                    <h2>User Content &amp; Acceptable Conduct</h2>
                    <p>You agree that you will not use CareerOS to:</p>
                    <ul>
                        <li>Submit false, misleading, or fraudulent information.</li>
                        <li>Violate any applicable law, regulation, or third-party rights.</li>
                        <li>Attempt to gain unauthorised access to any system or data.</li>
                        <li>Reverse engineer, decompile, or attempt to extract source code.</li>
                        <li>Use automated scripts, bots, or crawlers without our prior written consent.</li>
                        <li>Harass, abuse, or harm any other user or staff member.</li>
                        <li>Upload content that is defamatory, obscene, or illegal.</li>
                        <li>Circumvent any security feature or access control mechanism.</li>
                    </ul>
                    <p>
                        By submitting User Content, you grant CareerOS a non-exclusive, royalty-free licence to use such content
                        solely for the purpose of providing and improving the Services.
                    </p>
                </section>

                {/* §6 IP */}
                <section className="legal-section" id="ip">
                    <p className="legal-section-num">Section 6</p>
                    <h2>Intellectual Property</h2>
                    <p>
                        All content on CareerOS, including but not limited to text, graphics, logos, question banks,
                        lesson content, software code, and the CareerOS brand, is the exclusive property of CareerOS
                        or its licensors and is protected under the Copyright Act, 1957 and other applicable Indian
                        intellectual property laws.
                    </p>
                    <p>
                        You are granted a limited, non-exclusive, non-transferable licence to access and use CareerOS
                        solely for your personal, non-commercial educational purposes. You may not reproduce, distribute,
                        modify, or create derivative works from any CareerOS content without express written permission.
                    </p>
                </section>

                {/* §7 Personal Data */}
                <section className="legal-section" id="data">
                    <p className="legal-section-num">Section 7</p>
                    <h2>Personal Data &amp; Privacy</h2>
                    <p>
                        CareerOS processes personal data in accordance with the <strong>Digital Personal Data Protection Act, 2023</strong> (DPDP Act)
                        and the <strong>Information Technology (Reasonable Security Practices and Procedures and Sensitive Personal Data or
                        Information) Rules, 2011</strong>.
                    </p>
                    <p>The types of data we process and the lawful basis are described in full in our Privacy Policy.</p>
                    <div className="legal-highlight-box">
                        <p>
                            <strong>Your rights under DPDP Act §12–16:</strong> You have the right to access your data,
                            correct inaccuracies, erase your account and data, and withdraw consent at any time without
                            affecting the legality of processing before withdrawal. To exercise these rights, visit Settings
                            or contact our Grievance Officer.
                        </p>
                    </div>
                    <p>
                        We implement appropriate technical and organisational security measures to protect your personal data
                        against unauthorised access, loss, or disclosure, as required under §8(4) of the DPDP Act.
                    </p>
                </section>

                {/* §8 AI Content */}
                <section className="legal-section" id="ai">
                    <p className="legal-section-num">Section 8</p>
                    <h2>AI-Generated Content</h2>
                    <p>
                        CareerOS uses AI models (including locally-hosted models) to generate explanations, recommendations,
                        and feedback. You acknowledge and agree that:
                    </p>
                    <ul>
                        <li>AI-generated content is provided for <strong>educational purposes only</strong> and does not constitute professional career, legal, or financial advice.</li>
                        <li>AI explanations may occasionally contain inaccuracies or errors. You should critically evaluate AI outputs.</li>
                        <li>CareerOS does not guarantee the accuracy, completeness, or suitability of AI-generated content.</li>
                        <li>Your queries submitted for AI processing may be used to improve our services, subject to the Privacy Policy.</li>
                    </ul>
                    <div className="legal-warn-box">
                        <p>
                            <strong>Important:</strong> Do not rely solely on AI-generated explanations for high-stakes decisions.
                            Always consult qualified professionals for career-critical matters.
                        </p>
                    </div>
                </section>

                {/* §9 Disclaimers */}
                <section className="legal-section" id="disclaimers">
                    <p className="legal-section-num">Section 9</p>
                    <h2>Disclaimers &amp; Limitation of Liability</h2>
                    <p>
                        CareerOS is provided on an "as is" and "as available" basis without warranties of any kind,
                        either express or implied, including but not limited to warranties of merchantability,
                        fitness for a particular purpose, or non-infringement.
                    </p>
                    <p>We do not warrant that:</p>
                    <ul>
                        <li>The Platform will be available without interruption or error.</li>
                        <li>Assessment results or career recommendations will lead to any specific employment outcome.</li>
                        <li>The Platform will be free from viruses, malware, or other harmful components (though we take all reasonable precautions).</li>
                    </ul>
                    <p>
                        To the maximum extent permitted under applicable Indian law, CareerOS's total liability for any
                        claim arising out of or relating to your use of the Services shall not exceed the amount you paid
                        to CareerOS in the three (3) months preceding the claim, or ₹500, whichever is greater.
                    </p>
                    <p>
                        Nothing in these Terms excludes or limits our liability for death or personal injury arising from
                        our negligence, fraud or fraudulent misrepresentation, or any liability that cannot be excluded
                        under applicable law.
                    </p>
                </section>

                {/* §10 Termination */}
                <section className="legal-section" id="termination">
                    <p className="legal-section-num">Section 10</p>
                    <h2>Termination</h2>
                    <p>
                        We may suspend or terminate your access to CareerOS with immediate effect and without prior notice
                        if we reasonably believe you have breached these Terms, engaged in fraudulent activity, or if required
                        to do so by applicable law.
                    </p>
                    <p>
                        You may terminate your account at any time by contacting us or using the account deletion feature in Settings.
                        Upon termination, we will delete or anonymise your personal data as required by the DPDP Act and as
                        described in our Privacy Policy.
                    </p>
                    <p>
                        Sections 6 (Intellectual Property), 9 (Disclaimers), 11 (Dispute Resolution), and 13 (Contact) survive termination.
                    </p>
                </section>

                {/* §11 Dispute Resolution */}
                <section className="legal-section" id="dispute">
                    <p className="legal-section-num">Section 11</p>
                    <h2>Dispute Resolution</h2>
                    <p>
                        In the event of any dispute, claim, or controversy arising out of or relating to these Terms or the use of CareerOS,
                        the parties shall first attempt to resolve the matter through good-faith negotiation for a period of thirty (30) days.
                    </p>
                    <p>
                        If the dispute is not resolved by negotiation, it shall be referred to and finally resolved by arbitration
                        in accordance with the <strong>Arbitration and Conciliation Act, 1996</strong> (as amended). The seat of arbitration
                        shall be in India, and proceedings shall be conducted in English.
                    </p>
                    <p>
                        These Terms shall be governed by and construed in accordance with the laws of India.
                        Subject to the arbitration clause, the courts at [City], India shall have exclusive jurisdiction.
                    </p>
                </section>

                {/* §12 Changes */}
                <section className="legal-section" id="changes">
                    <p className="legal-section-num">Section 12</p>
                    <h2>Changes to Terms</h2>
                    <p>
                        We may update these Terms from time to time to reflect changes in law, our services, or business practices.
                        Material changes will be communicated to you by email or by prominent notice on the Platform at least
                        <strong> 30 days before</strong> the changes take effect.
                    </p>
                    <p>
                        As required by §6(2) of the DPDP Act, we will seek fresh consent for any new or materially different
                        processing of your personal data. Continued use of CareerOS after the effective date of revised Terms
                        constitutes acceptance of the changes.
                    </p>
                </section>

                {/* §13 Contact */}
                <section className="legal-section" id="contact">
                    <p className="legal-section-num">Section 13</p>
                    <h2>Contact &amp; Grievance Officer</h2>
                    <p>
                        As required by the <strong>DPDP Act, 2023 §13</strong> and the <strong>IT Act Intermediary Guidelines, 2021</strong>,
                        we have designated a Grievance Officer to address your concerns about personal data and Terms compliance.
                    </p>
                    <div className="legal-contact-card">
                        <strong>Grievance Officer — CareerOS</strong>
                        <p>Email: <a href="mailto:grievance@careeros.in">grievance@careeros.in</a></p>
                        <p>Response time: Within 48 hours (DPDP Act mandated 72-hour acknowledgement for data breach)</p>
                        <p>Escalation: If not resolved within 30 days, you may approach the Data Protection Board of India under §25 of the DPDP Act.</p>
                    </div>
                </section>

            </div>

            {/* Footer — extra bottom padding so content isn't hidden behind the bar */}
            <div className="legal-footer" style={{ paddingBottom: barVisible ? '5rem' : undefined }}>
                <p>
                    © 2026 CareerOS. All rights reserved. &nbsp;|&nbsp;
                    <a href="/privacy">Privacy Policy</a> &nbsp;|&nbsp;
                    Governed by the Digital Personal Data Protection Act, 2023 (India)
                </p>
            </div>

            {/* Scroll progress indicator */}
            <div className="legal-scroll-progress" style={{ width: `${scrollPct * 100}%` }} />

            {/* Scroll-to-accept floating bar */}
            <div className={`legal-accept-bar ${barVisible ? 'visible' : ''}`}>
                <div className="legal-accept-bar-left">
                    <span className="legal-accept-bar-title">
                        {accepted ? 'Terms & Conditions accepted ✓' : "You've read the Terms & Conditions"}
                    </span>
                    <span className="legal-accept-bar-sub">
                        {accepted
                            ? 'You may close this tab and read the Privacy Policy next.'
                            : 'Scroll through the full document before accepting.'}
                    </span>
                </div>
                {accepted ? (
                    <div className="legal-accept-bar-done">
                        <CheckCircle size={16} /> Accepted
                    </div>
                ) : (
                    <button className="legal-accept-bar-btn" onClick={handleAccept}>
                        ✓ I Accept — Close Tab
                    </button>
                )}
            </div>
        </div>
    );
}
