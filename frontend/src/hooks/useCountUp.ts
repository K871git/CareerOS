import { useEffect, useRef, useState } from 'react';

export function useCountUp(target: number, duration = 900): number {
    const [value, setValue] = useState(0);
    const raf = useRef<number>(0);

    useEffect(() => {
        if (target === 0) { setValue(0); return; }
        const start = performance.now();
        const tick = (now: number) => {
            const t = Math.min((now - start) / duration, 1);
            // ease-out cubic
            setValue(Math.round(target * (1 - Math.pow(1 - t, 3))));
            if (t < 1) raf.current = requestAnimationFrame(tick);
        };
        raf.current = requestAnimationFrame(tick);
        return () => cancelAnimationFrame(raf.current);
    }, [target, duration]);

    return value;
}
