import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import toast from 'react-hot-toast';
import { profileService } from '../services/profileService';
import type { UserProfile } from '../../../types/api';
import type { ProfileFormData } from '../schemas';

export function useProfile() {
    return useQuery<UserProfile | null>({
        queryKey: ['profile'],
        queryFn: async () => {
            try {
                const res = await profileService.get();
                return res.data.data ?? null;
            } catch {
                return null;
            }
        },
        retry: false,
    });
}

export function useUpdateProfile() {
    const queryClient = useQueryClient();

    return useMutation({
        mutationFn: (data: ProfileFormData) => profileService.update(data),
        onSuccess: ({ data: res }) => {
            queryClient.setQueryData(['profile'], res.data);
            toast.success('Profile updated successfully!');
        },
        onError: (error: any) => {
            const message = error.response?.data?.message ?? 'Failed to update profile. Please try again.';
            toast.error(message);
        },
    });
}
