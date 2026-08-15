import { Link, useParams, useNavigate } from 'react-router-dom';
import {
    ArrowLeft, ArrowRight,
    Globe, Monitor, Server, Network, Layers,
    Terminal, Database, Cpu, GitBranch, Shield,
    Smartphone, Cloud, BookOpen, FileCode2, Code2,
    Palette, Package, Boxes,
} from 'lucide-react';
import { useTracks, useSubjects } from '../../learning/hooks/useLearning';
import type { Subject } from '../../../types/api';
import '../practice.css';

function getSubjectIcon(title: string) {
    const t = title.toLowerCase();
    // Languages
    if (t.includes('javascript') || t.includes('js'))            return FileCode2;
    if (t.includes('typescript') || t.includes('ts'))            return FileCode2;
    if (t.includes('python'))                                     return Code2;
    if (t.includes('php'))                                        return Code2;
    if (t.includes('java') && !t.includes('javascript'))         return Code2;
    if (t.includes('html'))                                       return Globe;
    if (t.includes('css'))                                        return Palette;
    // Frontend frameworks
    if (t.includes('react'))                                      return Layers;
    if (t.includes('angular'))                                    return Boxes;
    if (t.includes('vue'))                                        return Layers;
    if (t.includes('next'))                                       return Monitor;
    if (t.includes('svelte'))                                     return Monitor;
    // Backend frameworks
    if (t.includes('laravel'))                                    return Package;
    if (t.includes('node') || t.includes('node.js'))             return Server;
    if (t.includes('express'))                                    return Server;
    if (t.includes('django') || t.includes('flask'))             return Server;
    if (t.includes('spring'))                                     return Server;
    // Infrastructure
    if (t.includes('network'))                                    return Network;
    if (t.includes('database') || t.includes('sql') || t.includes('mysql') || t.includes('postgres')) return Database;
    if (t.includes('system design') || t.includes('architecture')) return Layers;
    if (t.includes('operating') || t.includes(' os'))            return Terminal;
    if (t.includes('iot') || t.includes('embedded'))             return Cpu;
    if (t.includes('algorithm') || t.includes('dsa'))            return GitBranch;
    if (t.includes('security'))                                   return Shield;
    if (t.includes('mobile') || t.includes('android') || t.includes('ios')) return Smartphone;
    if (t.includes('cloud') || t.includes('aws'))                return Cloud;
    return BookOpen;
}

const SUBJECT_COLORS = [
    'prac-sub-indigo',
    'prac-sub-blue',
    'prac-sub-purple',
    'prac-sub-teal',
    'prac-sub-amber',
    'prac-sub-rose',
];

function SubjectCard({ subject, trackTitle, trackId, index }: {
    subject: Subject;
    trackTitle: string;
    trackId: number;
    index: number;
}) {
    const Icon = getSubjectIcon(subject.title);
    const colorClass = SUBJECT_COLORS[index % SUBJECT_COLORS.length];

    return (
        <Link
            to={`/practice/subjects/${subject.id}`}
            state={{ subjectTitle: subject.title, trackTitle, trackId }}
            className={`prac-sub-card ${colorClass}`}
        >
            <div className="prac-sub-icon-col">
                <div className="prac-sub-icon-wrap">
                    <Icon size={20} strokeWidth={1.75} />
                </div>
            </div>
            <div className="prac-sub-content">
                <h3 className="prac-sub-title">{subject.title}</h3>
                <p className="prac-sub-desc">
                    {subject.description || 'Level-based interview questions from Junior to Senior.'}
                </p>
            </div>
            <div className="prac-sub-right">
                <span className="prac-sub-badge">3 Levels</span>
                <ArrowRight size={15} className="prac-sub-arrow" />
            </div>
        </Link>
    );
}

export default function PracticeTrackPage() {
    const { trackId } = useParams<{ trackId: string }>();
    const id = Number(trackId);
    const navigate = useNavigate();

    const { data: tracks = [], isLoading: tracksLoading } = useTracks();
    const { data: subjects = [], isLoading: subjectsLoading } = useSubjects(id);

    const track = tracks.find((t) => t.id === id);
    const practiceSubjects = subjects.filter((s) => s.mcq_question_count >= 10);
    const isLoading = tracksLoading || subjectsLoading;

    return (
        <div className="practice-page">
            <div className="practice-inner">
                {/* Back */}
                <button className="prac-back-btn" onClick={() => navigate('/practice')}>
                    <ArrowLeft size={15} /> Practice
                </button>

                {/* Track header */}
                <div className="prac-track-page-header">
                    <h1 className="prac-track-page-title">
                        {track?.title ?? 'Loading…'}
                    </h1>
                    {track?.description && (
                        <p className="prac-track-page-desc">{track.description}</p>
                    )}
                </div>

                {/* Subject list */}
                {isLoading ? (
                    <div className="prac-sub-list">
                        {[0, 1, 2, 3].map((i) => (
                            <div key={i} className="skeleton" style={{ height: 88, borderRadius: 14 }} />
                        ))}
                    </div>
                ) : practiceSubjects.length === 0 ? (
                    <div style={{ textAlign: 'center', padding: '3rem 1rem', color: 'var(--text-muted)' }}>
                        <p>No practice subjects available for this track yet.</p>
                        <button className="prac-back-btn" style={{ marginTop: '1rem' }} onClick={() => navigate('/practice')}>
                            ← Back to Practice
                        </button>
                    </div>
                ) : (
                    <div className="prac-sub-list">
                        {practiceSubjects.map((s, i) => (
                            <SubjectCard
                                key={s.id}
                                subject={s}
                                trackTitle={track?.title ?? ''}
                                trackId={id}
                                index={i}
                            />
                        ))}
                    </div>
                )}
            </div>
        </div>
    );
}
