import { useState } from 'react';
import { ShieldCheck } from 'lucide-react';
import '../consent.css';

interface Props {
    onAccept: () => void;
}

export default function ConsentGatePage({ onAccept }: Props) {
    const [adultCheck,   setAdultCheck]   = useState(false);
    const [termsCheck,   setTermsCheck]   = useState(false);
    const [declined,     setDeclined]     = useState(false);

    const canAccept = adultCheck && termsCheck;

    if (declined) {
        return (
            <div className="cgate-root">
                <div className="cgate-card">
                    <div className="cgate-declined">
                        <div className="cgate-declined-icon">🔒</div>
                        <h2 className="cgate-declined-title">Access Unavailable</h2>
                        <p className="cgate-declined-desc">
                            CareerOS requires your consent to the Terms &amp; Conditions and Privacy Policy to operate
                            in compliance with the <strong>Digital Personal Data Protection Act, 2023</strong>.
                            Without consent, we are unable to process your personal data or provide the service.
                        </p>
                        <p className="cgate-declined-desc" style={{ marginTop: '-0.5rem' }}>
                            You may return at any time and accept to begin using CareerOS.
                        </p>
                        <button className="cgate-declined-back" onClick={() => setDeclined(false)}>
                            ← Go back and review
                        </button>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="cgate-root">
            <div className="cgate-card">

                {/* Header */}
                <div className="cgate-header">
                    <div className="cgate-brand">
                        <div className="cgate-logo">CO</div>
                        <span className="cgate-brand-name">CareerOS</span>
                    </div>
                    <h1 className="cgate-heading">
                        Your data, your rights — please read before continuing
                    </h1>
                    <p className="cgate-subheading">
                        In compliance with the Digital Personal Data Protection Act, 2023 (DPDP Act),
                        we are required to inform you about how we collect and use your personal data.
                    </p>
                </div>

                {/* Body */}
                <div className="cgate-body">

                    {/* DPDP Notice — required by §5 of the Act */}
                    <div className="cgate-notice">
                        <p className="cgate-notice-title">What personal data we collect &amp; why</p>
                        <ul className="cgate-notice-list">
                            <li>
                                <strong>Account data</strong> (name, email, mobile) — to create and manage your account
                            </li>
                            <li>
                                <strong>Learning &amp; assessment data</strong> — to track your progress and personalise recommendations
                            </li>
                            <li>
                                <strong>Usage data</strong> (session logs, feature interactions) — to improve the platform
                            </li>
                            <li>
                                <strong>Device &amp; technical data</strong> (IP address, browser type) — for security and fraud prevention
                            </li>
                        </ul>
                    </div>

                    {/* Rights summary */}
                    <div className="cgate-notice" style={{ marginBottom: '1.25rem', background: 'rgba(16,185,129,0.04)', borderColor: 'rgba(16,185,129,0.2)' }}>
                        <p className="cgate-notice-title" style={{ color: '#059669' }}>Your rights under the DPDP Act 2023</p>
                        <ul className="cgate-notice-list">
                            <li>Right to access information about your personal data</li>
                            <li>Right to correct or erase your personal data</li>
                            <li>Right to withdraw consent at any time (via Settings)</li>
                            <li>Right to grievance redressal — contact our Grievance Officer</li>
                        </ul>
                    </div>

                    {/* Checkboxes */}
                    <div className="cgate-checks">
                        <label className="cgate-check-row">
                            <input
                                type="checkbox"
                                checked={adultCheck}
                                onChange={e => setAdultCheck(e.target.checked)}
                            />
                            <span className="cgate-check-label">
                                I confirm that I am <strong>18 years of age or older</strong>.
                                CareerOS is not designed for persons under 18 years of age.
                            </span>
                        </label>

                        <label className="cgate-check-row">
                            <input
                                type="checkbox"
                                checked={termsCheck}
                                onChange={e => setTermsCheck(e.target.checked)}
                            />
                            <span className="cgate-check-label">
                                I have read, understood, and agree to the{' '}
                                <a href="/terms" target="_blank" rel="noopener noreferrer">
                                    Terms &amp; Conditions
                                </a>{' '}
                                and{' '}
                                <a href="/privacy" target="_blank" rel="noopener noreferrer">
                                    Privacy Policy
                                </a>
                                . I give my <strong>free, specific, informed, and unambiguous consent</strong> to
                                CareerOS processing my personal data for the purposes described therein.
                            </span>
                        </label>
                    </div>

                    {/* Actions */}
                    <div className="cgate-actions">
                        <button
                            className="cgate-accept-btn"
                            disabled={!canAccept}
                            onClick={onAccept}
                        >
                            <ShieldCheck size={18} />
                            I Accept &amp; Continue
                        </button>
                        <button
                            className="cgate-decline-btn"
                            onClick={() => setDeclined(true)}
                        >
                            I decline — exit
                        </button>
                    </div>

                </div>
            </div>
        </div>
    );
}
