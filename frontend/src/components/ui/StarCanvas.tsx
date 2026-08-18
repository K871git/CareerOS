import { useRef, useEffect } from 'react';

interface Star {
    x: number; y: number;
    r: number;
    baseA: number; alpha: number;
    phase: number; speed: number;
    vx: number; vy: number;
}

export default function StarCanvas({ count = 110 }: { count?: number }) {
    const ref = useRef<HTMLCanvasElement>(null);

    useEffect(() => {
        const canvas = ref.current;
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        if (!ctx) return;

        let w = 0, h = 0;
        let stars: Star[] = [];
        let raf = 0;
        const MAX_D = 130;
        const MAX_D2 = MAX_D * MAX_D;

        function init(width: number, height: number) {
            w = width;
            h = height;
            stars = Array.from({ length: count }, () => ({
                x: Math.random() * w,
                y: Math.random() * h,
                r: Math.random() * 1.5 + 0.35,
                baseA: Math.random() * 0.5 + 0.18,
                alpha: 0,
                phase: Math.random() * Math.PI * 2,
                speed: Math.random() * 0.016 + 0.005,
                vx: (Math.random() - 0.5) * 0.13,
                vy: (Math.random() - 0.5) * 0.08,
            }));
        }

        function tick() {
            ctx.clearRect(0, 0, w, h);

            for (const s of stars) {
                s.phase += s.speed;
                s.alpha = s.baseA * (0.5 + 0.5 * Math.sin(s.phase));
                s.x = (s.x + s.vx + w) % w;
                s.y = (s.y + s.vy + h) % h;
            }

            // Constellation lines
            for (let i = 0; i < stars.length - 1; i++) {
                const si = stars[i];
                for (let j = i + 1; j < stars.length; j++) {
                    const sj = stars[j];
                    const dx = si.x - sj.x;
                    const dy = si.y - sj.y;
                    const d2 = dx * dx + dy * dy;
                    if (d2 < MAX_D2) {
                        const d = Math.sqrt(d2);
                        const a = (1 - d / MAX_D) * 0.13 * ((si.alpha + sj.alpha) * 0.5);
                        ctx.beginPath();
                        ctx.moveTo(si.x, si.y);
                        ctx.lineTo(sj.x, sj.y);
                        ctx.strokeStyle = `rgba(165,180,252,${a})`;
                        ctx.lineWidth = 0.55;
                        ctx.stroke();
                    }
                }
            }

            // Stars + soft glow for larger ones
            for (const s of stars) {
                if (s.r > 1.1) {
                    const g = ctx.createRadialGradient(s.x, s.y, 0, s.x, s.y, s.r * 4.5);
                    g.addColorStop(0, `rgba(196,210,255,${s.alpha * 0.38})`);
                    g.addColorStop(1, 'rgba(196,210,255,0)');
                    ctx.beginPath();
                    ctx.arc(s.x, s.y, s.r * 4.5, 0, Math.PI * 2);
                    ctx.fillStyle = g;
                    ctx.fill();
                }
                ctx.beginPath();
                ctx.arc(s.x, s.y, s.r, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(210,220,255,${s.alpha})`;
                ctx.fill();
            }

            raf = requestAnimationFrame(tick);
        }

        const ro = new ResizeObserver((entries) => {
            const rect = entries[0].contentRect;
            canvas.width  = rect.width;
            canvas.height = rect.height;
            init(rect.width, rect.height);
        });

        const parent = canvas.parentElement;
        if (parent) {
            ro.observe(parent);
            canvas.width  = parent.offsetWidth;
            canvas.height = parent.offsetHeight;
            init(parent.offsetWidth, parent.offsetHeight);
        }

        raf = requestAnimationFrame(tick);
        return () => { cancelAnimationFrame(raf); ro.disconnect(); };
    }, [count]);

    return <canvas ref={ref} className="star-canvas" />;
}
