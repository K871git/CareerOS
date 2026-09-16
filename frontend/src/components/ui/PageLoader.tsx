import './PageLoader.css';

// Sparks placed around the ring, avoiding the top zone where label text sits
// Each entry: [rotation-degrees, animation-delay-seconds]
const SPARKS: [number, number][] = [
    [68,  0.0],
    [132, 0.7],
    [195, 1.2],
    [255, 0.4],
    [312, 0.9],
];

interface PageLoaderProps {
    label: string;
    hint?: string;
}

export default function PageLoader({ label, hint }: PageLoaderProps) {
    return (
        <div className="pl-back" role="status" aria-live="polite" aria-atomic="true">
            <div className="pl-stage">

                {/* Static ring track */}
                <div className="pl-track" aria-hidden="true" />

                {/* Primary spinning comet — indigo/violet, 2.0 s */}
                <div className="pl-arc pl-arc--primary" aria-hidden="true" />

                {/* Secondary counter-clockwise comet — violet, 3.6 s */}
                <div className="pl-arc pl-arc--secondary" aria-hidden="true" />

                {/* Blinking spark particles */}
                {SPARKS.map(([deg, delay], i) => (
                    <div
                        key={i}
                        className="pl-spark"
                        style={{
                            transform: `rotate(${deg}deg) translateY(-138px)`,
                            animationDelay: `${delay}s`,
                        }}
                        aria-hidden="true"
                    />
                ))}

                {/* Ambient inner glow */}
                <div className="pl-glow" aria-hidden="true" />

                {/* Center: process name + hint + pulsing dots */}
                <div className="pl-center">
                    <p className="pl-label">{label}</p>
                    {hint && <p className="pl-hint">{hint}</p>}
                    <div className="pl-dots" aria-hidden="true">
                        <span /><span /><span />
                    </div>
                </div>

            </div>
        </div>
    );
}
