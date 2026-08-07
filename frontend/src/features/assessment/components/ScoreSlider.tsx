interface Props {
    value: number;
    onChange: (score: number) => void;
}

export default function ScoreSlider({ value, onChange }: Props) {
    return (
        <div className="score-slider-wrap">
            <div className="score-slider-row">
                <span className="score-slider-label">Confidence</span>
                <span className="score-slider-value">
                    {value}<span className="score-slider-pct"> / 100</span>
                </span>
            </div>
            <input
                type="range"
                min={0}
                max={100}
                step={5}
                value={value}
                onChange={(e) => onChange(Number(e.target.value))}
                className="score-slider"
            />
        </div>
    );
}
