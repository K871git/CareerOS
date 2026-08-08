import { useState, useEffect, useRef } from 'react';
import { Briefcase, Star, Save, Edit3, CheckCircle2, Target } from 'lucide-react';
import {
    useCareerAssessment,
    useCreateAssessment,
    useSkills,
    useUpdateAssessment,
} from '../hooks/useAssessment';
import type { CareerAssessment, Skill } from '../../../types/api';
import type { SelectedSkill, SkillLevel } from '../types';
import SkillSelector from '../components/SkillSelector';
import '../assessment.css';

function AssessmentSummaryView({
    assessment,
    skills,
    onEdit,
}: {
    assessment: CareerAssessment;
    skills: Skill[];
    onEdit: () => void;
}) {
    const enriched = assessment.skills.map((s) => ({
        ...s,
        category: skills.find((sk) => sk.id === s.skill_id)?.category ?? 'Other',
    }));

    const grouped = enriched.reduce<Record<string, typeof enriched>>((acc, s) => {
        (acc[s.category] ??= []).push(s);
        return acc;
    }, {});

    return (
        <div className="assessment-summary">
            <div className="assessment-card assessment-role-card">
                <div className="assessment-role-inner">
                    <div className="assessment-role-icon">
                        <Target size={20} />
                    </div>
                    <div>
                        <p className="assessment-role-label">Target Role</p>
                        <p className="assessment-role-value">{assessment.target_role ?? '—'}</p>
                    </div>
                </div>
                <div className="assessment-summary-meta">
                    <span className="assessment-summary-count">
                        <CheckCircle2 size={14} />
                        {assessment.skills.length} skill{assessment.skills.length !== 1 ? 's' : ''} assessed
                    </span>
                    <button type="button" className="assessment-edit-btn" onClick={onEdit}>
                        <Edit3 size={14} /> Edit Assessment
                    </button>
                </div>
            </div>

            {Object.entries(grouped).map(([category, catSkills]) => (
                <div key={category} className="assessment-card">
                    <h3 className="assessment-summary-category">{category}</h3>
                    <div className="summary-skill-list">
                        {catSkills.map((s) => (
                            <div key={s.id} className="summary-skill-item">
                                <span className="summary-skill-name">{s.skill_name}</span>
                                <span className={`summary-level-badge lvl-badge-${s.level}`}>
                                    {s.level}
                                </span>
                                <div className="summary-score-wrap">
                                    <div className="summary-score-bar">
                                        <div
                                            className="summary-score-fill"
                                            style={{ width: `${s.score}%` }}
                                        />
                                    </div>
                                    <span className="summary-score-num">{s.score}</span>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            ))}
        </div>
    );
}

function AssessmentSkeleton() {
    return (
        <div className="assessment-page">
            <div className="page-header">
                <div className="skeleton" style={{ width: 200, height: 28, borderRadius: 8 }} />
            </div>
            <div className="assessment-card">
                <div className="skeleton" style={{ height: 18, width: '35%', marginBottom: 16, borderRadius: 6 }} />
                <div className="skeleton" style={{ height: 44, borderRadius: 10 }} />
            </div>
            <div className="assessment-card">
                <div className="skeleton" style={{ height: 18, width: '28%', marginBottom: 16, borderRadius: 6 }} />
                <div className="skill-grid">
                    {[0, 1, 2, 3, 4, 5].map((i) => (
                        <div key={i} className="skeleton" style={{ height: 52, borderRadius: 10 }} />
                    ))}
                </div>
            </div>
        </div>
    );
}

export default function CareerAssessmentPage() {
    const { data: skills = [], isLoading: skillsLoading } = useSkills();
    const { data: assessment, isLoading: assessmentLoading } = useCareerAssessment();

    const [isEditing, setIsEditing] = useState(false);
    const [targetRole, setTargetRole] = useState('');
    const [targetRoleError, setTargetRoleError] = useState<string | null>(null);
    const [selectedSkills, setSelectedSkills] = useState<SelectedSkill[]>([]);
    const [skillsError, setSkillsError] = useState<string | null>(null);

    const createMutation = useCreateAssessment();
    const updateMutation = useUpdateAssessment();
    const isSaving = createMutation.isPending || updateMutation.isPending;

    const buildSelectedSkills = (src: CareerAssessment): SelectedSkill[] =>
        src.skills.map((s) => ({
            skill_id:   s.skill_id,
            skill_name: s.skill_name,
            category:   skills.find((x) => x.id === s.skill_id)?.category ?? '',
            level:      s.level as SkillLevel,
            score:      s.score,
        }));

    // Initialise form state once both data sources are ready
    const initializedRef = useRef(false);
    useEffect(() => {
        if (initializedRef.current) return;
        if (assessment !== undefined && skills.length > 0) {
            initializedRef.current = true;
            if (assessment) {
                setTargetRole(assessment.target_role ?? '');
                setSelectedSkills(buildSelectedSkills(assessment));
            }
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [assessment, skills]);

    const handleEdit = () => {
        if (assessment) {
            setTargetRole(assessment.target_role ?? '');
            setSelectedSkills(buildSelectedSkills(assessment));
            setTargetRoleError(null);
            setSkillsError(null);
        }
        setIsEditing(true);
    };

    const handleToggleSkill = (skill: Skill) => {
        setSkillsError(null);
        setSelectedSkills((prev) =>
            prev.some((s) => s.skill_id === skill.id)
                ? prev.filter((s) => s.skill_id !== skill.id)
                : [
                      ...prev,
                      {
                          skill_id:   skill.id,
                          skill_name: skill.name,
                          category:   skill.category,
                          level:      'intermediate',
                          score:      50,
                      },
                  ]
        );
    };

    const handleUpdateLevel = (skillId: number, level: SkillLevel) =>
        setSelectedSkills((prev) =>
            prev.map((s) => (s.skill_id === skillId ? { ...s, level } : s))
        );

    const handleUpdateScore = (skillId: number, score: number) =>
        setSelectedSkills((prev) =>
            prev.map((s) => (s.skill_id === skillId ? { ...s, score } : s))
        );

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        let valid = true;

        const trimmed = targetRole.trim();
        if (!trimmed) {
            setTargetRoleError('Target role is required');
            valid = false;
        } else if (trimmed.length > 100) {
            setTargetRoleError('Max 100 characters');
            valid = false;
        } else {
            setTargetRoleError(null);
        }

        if (selectedSkills.length === 0) {
            setSkillsError('Please select at least one skill');
            valid = false;
        } else {
            setSkillsError(null);
        }

        if (!valid) return;

        const payload = {
            target_role: trimmed,
            skills: selectedSkills.map(({ skill_id, level, score }) => ({ skill_id, level, score })),
        };

        if (assessment) {
            updateMutation.mutate(payload, { onSuccess: () => setIsEditing(false) });
        } else {
            createMutation.mutate(payload);
        }
    };

    if (assessmentLoading || skillsLoading) return <AssessmentSkeleton />;

    const showSummary = !!assessment && !isEditing;

    return (
        <div className="assessment-page">
            <div className="page-header">
                <div>
                    <h1 className="page-header-title">Career Assessment</h1>
                    <p className="page-header-description">
                        Rate your skills and define your target role to get personalised recommendations.
                    </p>
                </div>
            </div>

            {showSummary ? (
                <AssessmentSummaryView
                    assessment={assessment!}
                    skills={skills}
                    onEdit={handleEdit}
                />
            ) : (
                <form onSubmit={handleSubmit} noValidate>
                    {/* Target Role */}
                    <div className="assessment-card">
                        <div className="assessment-section-head">
                            <div className="assessment-section-icon">
                                <Briefcase size={16} />
                            </div>
                            <div>
                                <h2 className="assessment-section-title">Target Role</h2>
                                <p className="assessment-section-desc">
                                    What role are you preparing for?
                                </p>
                            </div>
                        </div>
                        <div className="assessment-field">
                            <label className="assessment-label" htmlFor="target_role">
                                Role title
                            </label>
                            <input
                                id="target_role"
                                type="text"
                                className={`assessment-input${targetRoleError ? ' input--error' : ''}`}
                                placeholder="e.g. Full Stack Developer, Backend Engineer"
                                value={targetRole}
                                onChange={(e) => {
                                    setTargetRole(e.target.value);
                                    setTargetRoleError(null);
                                }}
                            />
                            {targetRoleError && (
                                <p className="assessment-error">{targetRoleError}</p>
                            )}
                        </div>
                    </div>

                    {/* Skills */}
                    <div className="assessment-card">
                        <div className="assessment-section-head">
                            <div className="assessment-section-icon">
                                <Star size={16} />
                            </div>
                            <div>
                                <h2 className="assessment-section-title">Your Skills</h2>
                                <p className="assessment-section-desc">
                                    Click a skill to select it, then set your level and confidence score.
                                </p>
                            </div>
                        </div>
                        {skillsError && (
                            <p className="assessment-error assessment-skills-error">{skillsError}</p>
                        )}
                        <SkillSelector
                            skills={skills}
                            selectedSkills={selectedSkills}
                            onToggle={handleToggleSkill}
                            onUpdateLevel={handleUpdateLevel}
                            onUpdateScore={handleUpdateScore}
                        />
                    </div>

                    {/* Actions */}
                    <div className="assessment-actions">
                        {assessment && (
                            <button
                                type="button"
                                className="assessment-cancel-btn"
                                onClick={() => setIsEditing(false)}
                            >
                                Cancel
                            </button>
                        )}
                        <button type="submit" disabled={isSaving} className="assessment-save-btn">
                            {isSaving ? (
                                'Saving…'
                            ) : (
                                <>
                                    <Save size={15} />
                                    {assessment ? 'Update Assessment' : 'Save Assessment'}
                                </>
                            )}
                        </button>
                    </div>
                </form>
            )}
        </div>
    );
}
