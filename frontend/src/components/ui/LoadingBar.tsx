import { useEffect, useRef, useState } from 'react';
import { useLocation } from 'react-router-dom';
import './LoadingBar.css';

type Phase = 'idle' | 'running' | 'done';

export default function LoadingBar() {
    const location  = useLocation();
    const [phase, setPhase] = useState<Phase>('idle');
    const isFirst   = useRef(true);

    useEffect(() => {
        // Skip the very first render so the bar doesn't flash on initial page load.
        if (isFirst.current) {
            isFirst.current = false;
            return;
        }

        setPhase('running');
        const t1 = setTimeout(() => setPhase('done'), 520);
        const t2 = setTimeout(() => setPhase('idle'), 880);
        return () => { clearTimeout(t1); clearTimeout(t2); };
    }, [location.key]);

    if (phase === 'idle') return null;

    return (
        <div className={`lb-track lb-track--${phase}`} aria-hidden="true">
            <div className="lb-bar" />
        </div>
    );
}
