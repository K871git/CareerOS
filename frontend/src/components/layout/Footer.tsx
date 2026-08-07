import '../../layouts/layout.css';

export default function Footer() {
    return (
        <footer className="footer">
            <div className="footer-inner">
                <span className="footer-brand">CareerOS</span>
                <span className="footer-copy">© 2026 · Built for engineers, by engineers.</span>
                <div className="footer-links">
                    <a href="#">Privacy</a>
                    <a href="#">Terms</a>
                </div>
            </div>
        </footer>
    );
}
