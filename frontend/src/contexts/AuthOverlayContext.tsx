import { createContext, useContext, useState, useCallback } from 'react';
import type { ReactNode } from 'react';

export type OverlayType  = 'welcome' | 'goodbye';
export type OverlayPhase = 'idle' | 'visible' | 'exiting';

export interface OverlayState {
    type:  OverlayType | null;
    name:  string;
    phase: OverlayPhase;
}

interface AuthOverlayCtx {
    overlay:     OverlayState;
    showWelcome: (name: string, onDone: () => void) => void;
    showGoodbye: (name: string, onDone: () => void) => void;
}

const AuthOverlayContext = createContext<AuthOverlayCtx | null>(null);

// Time the overlay stays on screen before the exit animation starts.
// "welcome" is slightly longer so the user can read it.
const WELCOME_HOLD_MS = 1600;
const GOODBYE_HOLD_MS = 1200;
const EXIT_MS         = 400;

export function AuthOverlayProvider({ children }: { children: ReactNode }) {
    const [overlay, setOverlay] = useState<OverlayState>({ type: null, name: '', phase: 'idle' });

    const show = useCallback((type: OverlayType, name: string, onDone: () => void) => {
        const holdMs = type === 'welcome' ? WELCOME_HOLD_MS : GOODBYE_HOLD_MS;

        setOverlay({ type, name, phase: 'visible' });

        const t1 = setTimeout(() => setOverlay(o => ({ ...o, phase: 'exiting' })), holdMs);
        const t2 = setTimeout(() => {
            setOverlay({ type: null, name: '', phase: 'idle' });
            onDone();
        }, holdMs + EXIT_MS);

        return () => { clearTimeout(t1); clearTimeout(t2); };
    }, []);

    const showWelcome = useCallback((name: string, onDone: () => void) => show('welcome', name, onDone), [show]);
    const showGoodbye = useCallback((name: string, onDone: () => void) => show('goodbye', name, onDone), [show]);

    return (
        <AuthOverlayContext.Provider value={{ overlay, showWelcome, showGoodbye }}>
            {children}
        </AuthOverlayContext.Provider>
    );
}

export function useAuthOverlay() {
    const ctx = useContext(AuthOverlayContext);
    if (!ctx) throw new Error('useAuthOverlay must be used within AuthOverlayProvider');
    return ctx;
}
