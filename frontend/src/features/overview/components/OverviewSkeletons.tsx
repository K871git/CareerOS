export function SkeletonStats() {
    return (
        <div className="dash-stats">
            {[0, 1, 2, 3].map(i => <div key={i} className="skeleton skeleton-stat" />)}
        </div>
    );
}

export function SkeletonSections() {
    return (
        <div className="dash-section-cards">
            {[0, 1].map(i => <div key={i} className="skeleton" style={{ height: 82, borderRadius: 16 }} />)}
        </div>
    );
}

export function SkeletonCard({ rows = 3 }: { rows?: number }) {
    return (
        <div>
            {Array.from({ length: rows }).map((_, i) => (
                <div key={i} className="skeleton skeleton-block"
                    style={{ height: 48, marginBottom: 10, width: i === rows - 1 ? '70%' : '100%' }} />
            ))}
        </div>
    );
}
