import { Link } from 'react-router-dom';
import '../../layouts/layout.css';

export default function Footer() {
    return (
        <footer className="footer">
            <div className="footer-inner">
                <div className="footer-col footer-col--brand">
                    <span className="footer-brand">CareerOS</span>
                    <p className="footer-tagline">
                        A focused career growth platform for software engineers at every level — from placement prep to senior interviews.
                    </p>
                </div>

                <div className="footer-col">
                    <p className="footer-col-title">Platform</p>
                    <div className="footer-links">
                        <Link to="/?modal=register">Get started free</Link>
                        <Link to="/?modal=login">Sign in</Link>
                    </div>
                </div>

                <div className="footer-col">
                    <p className="footer-col-title">Company</p>
                    <div className="footer-links">
                        <a href="#">Privacy Policy</a>
                        <a href="#">Terms of Use</a>
                    </div>
                </div>
            </div>

            <div className="footer-bottom">
                <span className="footer-copy">© 2026 CareerOS · Built for engineers, by engineers.</span>
            </div>
        </footer>
    );
}
