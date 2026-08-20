import { useAuthOverlay } from '../../contexts/AuthOverlayContext';
import './AuthOverlay.css';

// Top arc for SVG textPath — centered at (150,150), radius 122
// Goes from left (28,150) to right (272,150) via the 12-o'clock position
const TOP_ARC_PATH = 'M 28,150 A 122,122 0 0,1 272,150';

// Sparks positioned on the ring, avoiding the top-arc text zone (≈ 315°–45°).
// Each entry: [rotation-degrees, animation-delay-seconds]
const SPARKS: [number, number][] = [
    [68,  0.0],
    [135, 0.6],
    [195, 1.1],
    [252, 0.3],
    [308, 0.9],
];

export default function AuthOverlay() {
    const { overlay } = useAuthOverlay();

    if (overlay.phase === 'idle') return null;

    const isExiting = overlay.phase === 'exiting';
    const isWelcome = overlay.type  === 'welcome';
    const firstName = overlay.name.split(' ')[0] || overlay.name;

    return (
        <div
            className={`ao-back${isExiting ? ' ao-back--out' : ''}`}
            role="status"
            aria-live="polite"
            aria-atomic="true"
        >
            <div className={`ao-stage${isExiting ? ' ao-stage--out' : ''}`}>

                {/* Static ring track */}
                <div className="ao-track" aria-hidden="true" />

                {/* Primary rotating comet — fast, indigo/violet */}
                <div className="ao-comet ao-comet--primary" aria-hidden="true" />

                {/* Secondary rotating comet — slow, opposite direction, violet */}
                <div className="ao-comet ao-comet--secondary" aria-hidden="true" />

                {/* Blinking spark particles on the ring */}
                {SPARKS.map(([deg, delay], i) => (
                    <div
                        key={i}
                        className="ao-spark"
                        style={{
                            transform: `rotate(${deg}deg) translateY(-148px)`,
                            animationDelay: `${delay}s`,
                        }}
                        aria-hidden="true"
                    />
                ))}

                {/* Ambient inner glow */}
                <div className="ao-glow" aria-hidden="true" />

                {/* Curved greeting text via SVG textPath */}
                <svg
                    className="ao-arc-svg"
                    viewBox="0 0 300 300"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true"
                >
                    <defs>
                        <path id="ao-top-arc" d={TOP_ARC_PATH} />
                    </defs>
                    <text className="ao-arc-text">
                        <textPath
                            href="#ao-top-arc"
                            startOffset="50%"
                            textAnchor="middle"
                        >
                            {isWelcome ? 'WELCOME BACK' : 'SEE YOU SOON'}
                        </textPath>
                    </text>
                </svg>

                {/* Center: name + hint + pulsing dots */}
                <div className="ao-center">
                    <h2 className="ao-name">{firstName}</h2>
                    <p className="ao-hint">
                        {isWelcome ? 'Loading your workspace…' : 'Logging out securely…'}
                    </p>
                    <div className="ao-dots" aria-hidden="true">
                        <span /><span /><span />
                    </div>
                </div>

            </div>
        </div>
    );
}
