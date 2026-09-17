import { useState } from 'react';
import type { ReactNode } from 'react';
import ConsentGatePage from '../features/consent/pages/ConsentGatePage';

const CONSENT_KEY     = 'careeros_consent';
const CONSENT_VERSION = '1.0';

function hasValidConsent(): boolean {
    try {
        const stored = localStorage.getItem(CONSENT_KEY);
        if (!stored) return false;
        const parsed = JSON.parse(stored) as { version: string };
        return parsed.version === CONSENT_VERSION;
    } catch {
        return false;
    }
}

function recordConsent(): void {
    localStorage.setItem(
        CONSENT_KEY,
        JSON.stringify({ version: CONSENT_VERSION, acceptedAt: new Date().toISOString() })
    );
}

interface Props {
    children: ReactNode;
}

export default function ConsentGate({ children }: Props) {
    const [consented, setConsented] = useState<boolean>(hasValidConsent);

    if (!consented) {
        return (
            <ConsentGatePage
                onAccept={() => {
                    recordConsent();
                    setConsented(true);
                }}
            />
        );
    }

    return <>{children}</>;
}
