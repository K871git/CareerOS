import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { ArrowLeft, CheckCircle } from 'lucide-react';
import '../consent.css';

const STORAGE_KEY = 'careeros_privacy_read';

export default function PrivacyPage() {
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
            <div className="legal-hero" style={{ background: 'linear-gradient(135deg, #059669 0%, #0d9488 100%)' }}>
                <div className="legal-hero-tag">Legal Document</div>
                <h1 className="legal-hero-title">Privacy Policy</h1>
                <p className="legal-hero-sub">How CareerOS collects, uses, and protects your personal data</p>
            </div>

            {/* Meta */}
            <div className="legal-meta-bar">
                <span className="legal-meta-item"><strong>Version:</strong> 1.0</span>
                <span className="legal-meta-item"><strong>Effective Date:</strong> 17 September 2026</span>
                <span className="legal-meta-item"><strong>Governing Law:</strong> DPDP Act, 2023 &amp; IT Act, 2000</span>
            </div>

            {/* Content */}
            <div className="legal-content">

                {/* TOC */}
                <div className="legal-toc">
                    <p className="legal-toc-title">Table of Contents</p>
                    <ol className="legal-toc-list">
                        <li><a href="#p-who">Who We Are</a></li>
                        <li><a href="#p-collect">Data We Collect</a></li>
                        <li><a href="#p-purpose">Purpose &amp; Legal Basis</a></li>
                        <li><a href="#p-storage">Data Storage &amp; Retention</a></li>
                        <li><a href="#p-sharing">Sharing &amp; Disclosure</a></li>
                        <li><a href="#p-rights">Your Rights (DPDP Act)</a></li>
                        <li><a href="#p-consent">Consent &amp; Withdrawal</a></li>
                        <li><a href="#p-security">Security Measures</a></li>
                        <li><a href="#p-minors">Children's Privacy</a></li>
                        <li><a href="#p-cookies">Cookies &amp; Local Storage</a></li>
                        <li><a href="#p-changes">Policy Changes</a></li>
                        <li><a href="#p-contact">Contact &amp; Grievance</a></li>
                    </ol>
                </div>

                {/* Intro */}
                <div className="legal-highlight-box" style={{ marginBottom: '2rem' }}>
                    <p>
                        This Privacy Policy explains how CareerOS ("we", "us", "Data Fiduciary") collects, processes, stores, and
                        protects your personal data. It is drafted in compliance with the
                        <strong> Digital Personal Data Protection Act, 2023 (DPDP Act)</strong> and the
                        <strong> Information Technology (Reasonable Security Practices) Rules, 2011</strong>.
                    </p>
                </div>

                {/* §1 */}
                <section className="legal-section" id="p-who">
                    <p className="legal-section-num">Section 1</p>
                    <h2>Who We Are</h2>
                    <p>
                        CareerOS is an educational technology platform designed to help individuals assess, build, and
                        demonstrate their professional skills. We act as the <strong>Data Fiduciary</strong> for all personal data
                        you provide to us, as defined under §2(i) of the DPDP Act, 2023.
                    </p>
                    <p>
                        Our registered contact address and Grievance Officer details are listed in Section 12 of this Policy.
                    </p>
                </section>

                {/* §2 */}
                <section className="legal-section" id="p-collect">
                    <p className="legal-section-num">Section 2</p>
                    <h2>Personal Data We Collect</h2>
                    <p>We collect the following categories of personal data:</p>

                    <p><strong>2.1 Account &amp; Registration Data</strong></p>
                    <ul>
                        <li>Full name, email address, and mobile number (required for account creation)</li>
                        <li>Password (stored as a cryptographic hash — we never store plaintext passwords)</li>
                        <li>Profile photo (if provided via Google OAuth)</li>
                        <li>Google Account identifier (only if you sign in via Google)</li>
                    </ul>

                    <p><strong>2.2 Learning &amp; Assessment Data</strong></p>
                    <ul>
                        <li>Career assessment responses and results</li>
                        <li>Quiz attempts, selected answers, scores, and timestamps</li>
                        <li>Lesson completion records and progress</li>
                        <li>SQL Playground queries and execution logs</li>
                    </ul>

                    <p><strong>2.3 Technical &amp; Usage Data</strong></p>
                    <ul>
                        <li>IP address (collected at registration and consent for legal compliance)</li>
                        <li>Browser type and version (User-Agent string)</li>
                        <li>Session logs and feature interaction timestamps</li>
                        <li>Device type and operating system (inferred from User-Agent)</li>
                    </ul>

                    <p><strong>2.4 Consent Records</strong></p>
                    <ul>
                        <li>Consent version accepted, timestamp of consent, and IP address at time of consent — stored as an immutable audit trail under DPDP Act §6(3)</li>
                    </ul>

                    <p><strong>2.5 Communications Data</strong></p>
                    <ul>
                        <li>OTP codes (hashed and stored temporarily — deleted after use or 5-minute expiry)</li>
                        <li>Support correspondence, if you contact us</li>
                    </ul>
                </section>

                {/* §3 */}
                <section className="legal-section" id="p-purpose">
                    <p className="legal-section-num">Section 3</p>
                    <h2>Purpose &amp; Legal Basis for Processing</h2>
                    <p>
                        Under the DPDP Act, 2023, we process your personal data only for specified lawful purposes.
                        The table below sets out each processing purpose and the corresponding lawful basis.
                    </p>
                    <ul>
                        <li>
                            <strong>Account creation and authentication</strong> — Necessary for performance of the service agreement (§7 DPDP Act)
                        </li>
                        <li>
                            <strong>Providing learning tracks, assessments, and progress tracking</strong> — Necessary for performance of the service agreement
                        </li>
                        <li>
                            <strong>AI-powered explanations and recommendations</strong> — Based on your <em>consent</em> given at registration (§6 DPDP Act)
                        </li>
                        <li>
                            <strong>Security, fraud prevention, and access control</strong> — Legitimate interest and legal obligation
                        </li>
                        <li>
                            <strong>Sending OTP codes and account-related emails</strong> — Necessary for performance of the service and security
                        </li>
                        <li>
                            <strong>Maintaining consent audit records</strong> — Legal obligation under DPDP Act §6(3)
                        </li>
                        <li>
                            <strong>Platform improvement and analytics</strong> — Legitimate interest (anonymised/aggregated where possible)
                        </li>
                    </ul>
                    <div className="legal-highlight-box">
                        <p>
                            <strong>DPDP Act §5 Compliance:</strong> We collect only the minimum personal data necessary
                            for the stated purposes (data minimisation principle). We do not collect sensitive personal
                            data (financial, health, biometric) except what you voluntarily provide.
                        </p>
                    </div>
                </section>

                {/* §4 */}
                <section className="legal-section" id="p-storage">
                    <p className="legal-section-num">Section 4</p>
                    <h2>Data Storage &amp; Retention</h2>
                    <p>
                        Your personal data is stored on servers located in India / within the jurisdiction permitted under
                        applicable law. We retain your personal data for as long as your account is active or as necessary
                        to provide services.
                    </p>
                    <p>Specific retention periods:</p>
                    <ul>
                        <li><strong>Account data:</strong> Retained for the lifetime of your account, plus 30 days post-deletion grace period.</li>
                        <li><strong>Assessment &amp; learning data:</strong> Retained for the lifetime of your account. Deleted upon account closure.</li>
                        <li><strong>OTP tokens:</strong> Automatically deleted after use or after 5 minutes, whichever is sooner.</li>
                        <li><strong>Consent audit records:</strong> Retained for 5 years as required by applicable law and DPDP compliance obligations, even after account deletion.</li>
                        <li><strong>Technical/usage logs:</strong> Retained for up to 90 days for security and debugging, then deleted.</li>
                    </ul>
                    <p>
                        Upon account deletion, we delete or permanently anonymise all personal data within 30 days, except
                        for data we are legally required to retain.
                    </p>
                </section>

                {/* §5 */}
                <section className="legal-section" id="p-sharing">
                    <p className="legal-section-num">Section 5</p>
                    <h2>Sharing &amp; Disclosure of Personal Data</h2>
                    <p>
                        We do not sell, rent, or trade your personal data. We share your data only in the following limited circumstances:
                    </p>
                    <ul>
                        <li>
                            <strong>Service providers (Data Processors):</strong> We engage trusted third-party service providers
                            (e.g., email delivery, hosting) who process data on our behalf under strict data processing agreements.
                            These providers are contractually prohibited from using your data for their own purposes.
                        </li>
                        <li>
                            <strong>Google OAuth:</strong> If you choose to sign in with Google, your Google Account information
                            (name, email, profile photo) is shared with us by Google under Google's Privacy Policy.
                        </li>
                        <li>
                            <strong>Legal obligations:</strong> We may disclose personal data to law enforcement or government
                            authorities if required by law, court order, or other legal process, or when necessary to protect
                            the rights, property, or safety of CareerOS, its users, or the public.
                        </li>
                        <li>
                            <strong>Business transfer:</strong> In the event of a merger, acquisition, or sale of assets,
                            personal data may be transferred, subject to the same privacy protections, with prior notice to you.
                        </li>
                    </ul>
                    <div className="legal-highlight-box">
                        <p>
                            <strong>No cross-border transfers to non-compliant countries.</strong> We do not transfer personal
                            data to countries not listed in the DPDP Act Approved Countries Schedule without your explicit
                            separate consent.
                        </p>
                    </div>
                </section>

                {/* §6 */}
                <section className="legal-section" id="p-rights">
                    <p className="legal-section-num">Section 6</p>
                    <h2>Your Rights under the DPDP Act, 2023</h2>
                    <p>
                        The Digital Personal Data Protection Act, 2023 grants you the following rights as a Data Principal.
                        You can exercise these rights through your account settings or by contacting our Grievance Officer.
                    </p>
                    <ul>
                        <li>
                            <strong>Right to Access (§12):</strong> You may request a summary of your personal data that we process
                            and the identity of all Data Processors to whom your data has been disclosed.
                        </li>
                        <li>
                            <strong>Right to Correction (§13):</strong> You may request correction of inaccurate or incomplete personal data.
                        </li>
                        <li>
                            <strong>Right to Erasure (§13):</strong> You may request deletion of your personal data when it is no
                            longer necessary for the purposes for which it was collected, subject to legal retention requirements.
                        </li>
                        <li>
                            <strong>Right to Withdraw Consent (§6(4)):</strong> You may withdraw your consent at any time.
                            Withdrawal does not affect the lawfulness of processing based on consent before its withdrawal.
                            We will stop processing your data within 7 days of withdrawal, except where legally required.
                        </li>
                        <li>
                            <strong>Right to Grievance Redressal (§13):</strong> You have the right to have your grievances
                            addressed within the prescribed time limit.
                        </li>
                        <li>
                            <strong>Right to Nominate (§14):</strong> You may nominate another individual to exercise your
                            rights in the event of your death or incapacity.
                        </li>
                    </ul>
                    <div className="legal-warn-box">
                        <p>
                            <strong>How to exercise your rights:</strong> Visit your account Settings → Privacy, or email
                            our Grievance Officer at <strong>grievance@careeros.in</strong>. We will respond within 30 days
                            (or 72 hours for data breach notifications, as required by the Act).
                        </p>
                    </div>
                </section>

                {/* §7 */}
                <section className="legal-section" id="p-consent">
                    <p className="legal-section-num">Section 7</p>
                    <h2>Consent &amp; Withdrawal</h2>
                    <p>
                        Your consent to this Privacy Policy is collected in compliance with DPDP Act §6, which requires consent to be:
                    </p>
                    <ul>
                        <li><strong>Free</strong> — not coerced or conditional upon service (except for core functionality)</li>
                        <li><strong>Specific</strong> — for identified, stated purposes only</li>
                        <li><strong>Informed</strong> — you are shown a clear notice of what data is collected and why</li>
                        <li><strong>Unconditional</strong> — no bundled consents hidden in lengthy terms</li>
                        <li><strong>Unambiguous</strong> — requires a clear affirmative action (ticking a checkbox)</li>
                    </ul>
                    <p>
                        You may withdraw consent at any time from <strong>Settings → Privacy → Withdraw Consent</strong>.
                        Upon withdrawal, we will cease processing your data for consent-based purposes. Note that
                        withdrawal may affect your ability to use certain features.
                    </p>
                </section>

                {/* §8 */}
                <section className="legal-section" id="p-security">
                    <p className="legal-section-num">Section 8</p>
                    <h2>Security Measures</h2>
                    <p>
                        We implement appropriate technical and organisational security measures as required by
                        DPDP Act §8(4) and the IT (Reasonable Security Practices) Rules, 2011, including:
                    </p>
                    <ul>
                        <li>All passwords are hashed using bcrypt — plaintext passwords are never stored.</li>
                        <li>Authentication tokens use Laravel Sanctum and are stored server-side.</li>
                        <li>HTTPS/TLS encryption for all data in transit.</li>
                        <li>Database access controls and principle of least privilege for staff.</li>
                        <li>OTP codes are cryptographically hashed and expire within 5 minutes.</li>
                        <li>Input validation and parameterised queries to prevent SQL injection.</li>
                        <li>Rate limiting on authentication endpoints to prevent brute-force attacks.</li>
                    </ul>
                    <p>
                        In the event of a personal data breach, we will notify affected users and the Data Protection Board
                        of India within 72 hours of becoming aware, as required by the DPDP Act.
                    </p>
                </section>

                {/* §9 */}
                <section className="legal-section" id="p-minors">
                    <p className="legal-section-num">Section 9</p>
                    <h2>Children's Privacy</h2>
                    <p>
                        CareerOS is not directed at or intended for use by children under 18 years of age.
                        We do not knowingly collect personal data from children. If we become aware that a user
                        is under 18, we will immediately delete their account and all associated personal data,
                        in accordance with DPDP Act §9 (processing of personal data of children).
                    </p>
                    <p>
                        If you believe a child has provided personal data to us, please contact our Grievance Officer
                        immediately at <a href="mailto:grievance@careeros.in">grievance@careeros.in</a>.
                    </p>
                </section>

                {/* §10 */}
                <section className="legal-section" id="p-cookies">
                    <p className="legal-section-num">Section 10</p>
                    <h2>Cookies &amp; Local Storage</h2>
                    <p>CareerOS uses browser-based storage mechanisms as follows:</p>
                    <ul>
                        <li>
                            <strong>localStorage (essential):</strong> Stores your authentication token and user profile
                            to keep you logged in across browser sessions. Stored under keys{' '}
                            <code>careeros_token</code> and <code>careeros_user</code>.
                        </li>
                        <li>
                            <strong>localStorage (consent):</strong> Stores the consent version you accepted under key{' '}
                            <code>careeros_consent</code> to avoid repeating the consent gate on every visit.
                        </li>
                        <li>
                            <strong>sessionStorage (functional):</strong> Temporarily stores quiz progress
                            (active session only, cleared when you close the tab or submit).
                        </li>
                    </ul>
                    <p>
                        We do not use advertising cookies, third-party tracking cookies, or analytics cookies
                        that profile your behaviour across other websites.
                    </p>
                </section>

                {/* §11 */}
                <section className="legal-section" id="p-changes">
                    <p className="legal-section-num">Section 11</p>
                    <h2>Changes to This Policy</h2>
                    <p>
                        We may update this Privacy Policy periodically. When we make material changes, we will:
                    </p>
                    <ul>
                        <li>Notify you by email at least 30 days before the changes take effect.</li>
                        <li>Display a prominent notice on the Platform.</li>
                        <li>For changes that require fresh consent under the DPDP Act, present a new consent gate.</li>
                    </ul>
                    <p>
                        The "Effective Date" at the top of this Policy indicates when it was last updated.
                    </p>
                </section>

                {/* §12 */}
                <section className="legal-section" id="p-contact">
                    <p className="legal-section-num">Section 12</p>
                    <h2>Contact &amp; Grievance Officer</h2>
                    <p>
                        For any privacy-related queries, data requests, or grievances, please contact our
                        designated Grievance Officer as required by DPDP Act §13 and the IT Intermediary Guidelines, 2021:
                    </p>
                    <div className="legal-contact-card">
                        <strong>Grievance Officer — CareerOS</strong>
                        <p>Email: <a href="mailto:grievance@careeros.in">grievance@careeros.in</a></p>
                        <p>Acknowledgement: Within 48 hours</p>
                        <p>Resolution: Within 30 days of receipt of grievance</p>
                        <p>
                            <strong>Escalation:</strong> If your grievance is not resolved satisfactorily,
                            you may approach the <strong>Data Protection Board of India</strong> established
                            under §18 of the DPDP Act, 2023.
                        </p>
                    </div>
                </section>

            </div>

            {/* Footer */}
            <div className="legal-footer" style={{ paddingBottom: barVisible ? '5rem' : undefined }}>
                <p>
                    © 2026 CareerOS. All rights reserved. &nbsp;|&nbsp;
                    <a href="/terms">Terms &amp; Conditions</a> &nbsp;|&nbsp;
                    Governed by the Digital Personal Data Protection Act, 2023 (India)
                </p>
            </div>

            {/* Scroll progress indicator */}
            <div className="legal-scroll-progress" style={{ width: `${scrollPct * 100}%` }} />

            {/* Scroll-to-accept floating bar */}
            <div className={`legal-accept-bar ${barVisible ? 'visible' : ''}`}>
                <div className="legal-accept-bar-left">
                    <span className="legal-accept-bar-title">
                        {accepted ? 'Privacy Policy accepted ✓' : "You've read the Privacy Policy"}
                    </span>
                    <span className="legal-accept-bar-sub">
                        {accepted
                            ? 'Both documents accepted. Return to registration to complete your account.'
                            : 'Scroll through the full policy before accepting.'}
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
