import { useEffect } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Save, AlertCircle, Settings2 } from 'lucide-react';
import { profileSchema, type ProfileFormData } from '../schemas';
import { useUpdateProfile } from '../hooks/useProfile';
import { EXPERIENCE_LEVELS } from '../types';
import type { UserProfile } from '../../../types/api';

interface ProfileFormProps {
    profile: UserProfile | null;
}

export default function ProfileForm({ profile }: ProfileFormProps) {
    const { mutate: updateProfile, isPending } = useUpdateProfile();

    const {
        register,
        handleSubmit,
        reset,
        formState: { errors },
    } = useForm<ProfileFormData>({
        resolver: zodResolver(profileSchema),
        defaultValues: {
            current_role:     '',
            experience_level: 'junior',
            target_role:      '',
            career_goal:      '',
        },
    });

    useEffect(() => {
        if (profile) {
            reset({
                current_role:     profile.current_role ?? '',
                experience_level: profile.experience_level,
                target_role:      profile.target_role,
                career_goal:      profile.career_goal ?? '',
            });
        }
    }, [profile, reset]);

    return (
        <div className="profile-form-card">
            <div className="profile-form-header">
                <div className="profile-form-title-row">
                    <div className="profile-form-title-icon">
                        <Settings2 size={18} />
                    </div>
                    <div>
                        <h2 className="profile-form-title">Edit Profile</h2>
                        <p className="profile-form-sub">
                            Keep your career profile up to date to get personalised learning recommendations.
                        </p>
                    </div>
                </div>
            </div>

            <form onSubmit={handleSubmit((data) => updateProfile(data))} noValidate>
                <div className="profile-fields-row">
                    <div className="profile-field">
                        <label className="profile-label">
                            Current Role
                            <span className="profile-label-optional">optional</span>
                        </label>
                        <input
                            type="text"
                            placeholder="e.g. Junior Developer"
                            className={`profile-input${errors.current_role ? ' has-error' : ''}`}
                            {...register('current_role')}
                        />
                        {errors.current_role && (
                            <p className="profile-error-msg">
                                <AlertCircle size={12} />
                                {errors.current_role.message}
                            </p>
                        )}
                    </div>

                    <div className="profile-field">
                        <label className="profile-label">Experience Level</label>
                        <select
                            className={`profile-select${errors.experience_level ? ' has-error' : ''}`}
                            {...register('experience_level')}
                        >
                            {EXPERIENCE_LEVELS.map((opt) => (
                                <option key={opt.value} value={opt.value}>
                                    {opt.label}
                                </option>
                            ))}
                        </select>
                        {errors.experience_level && (
                            <p className="profile-error-msg">
                                <AlertCircle size={12} />
                                {errors.experience_level.message}
                            </p>
                        )}
                    </div>
                </div>

                <div className="profile-field">
                    <label className="profile-label">Target Role</label>
                    <input
                        type="text"
                        placeholder="e.g. Senior Full-Stack Engineer"
                        className={`profile-input${errors.target_role ? ' has-error' : ''}`}
                        {...register('target_role')}
                    />
                    {errors.target_role && (
                        <p className="profile-error-msg">
                            <AlertCircle size={12} />
                            {errors.target_role.message}
                        </p>
                    )}
                </div>

                <div className="profile-field">
                    <label className="profile-label">
                        Career Goal
                        <span className="profile-label-optional">optional</span>
                    </label>
                    <textarea
                        placeholder="Describe your career aspirations and what you want to achieve..."
                        rows={4}
                        className={`profile-textarea${errors.career_goal ? ' has-error' : ''}`}
                        {...register('career_goal')}
                    />
                    {errors.career_goal && (
                        <p className="profile-error-msg">
                            <AlertCircle size={12} />
                            {errors.career_goal.message}
                        </p>
                    )}
                </div>

                <div className="profile-form-actions">
                    <button type="submit" disabled={isPending} className="profile-save-btn">
                        <Save size={15} />
                        {isPending ? 'Saving…' : 'Save Changes'}
                    </button>
                    <span className="profile-save-hint">
                        {isPending ? 'Updating your profile…' : 'Changes are saved immediately.'}
                    </span>
                </div>
            </form>
        </div>
    );
}
