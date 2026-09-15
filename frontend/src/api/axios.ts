import axios from 'axios';
import toast from 'react-hot-toast';

const api = axios.create({
    baseURL: import.meta.env.VITE_API_URL ?? 'http://localhost:8000/api',
    headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
    },
});

api.interceptors.request.use((config) => {
    const token = localStorage.getItem('careeros_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Guard against multiple concurrent 401s all triggering simultaneous redirects.
// Reset on each navigation (popstate) so re-login in the same session works.
let isLoggingOut = false;
window.addEventListener('popstate', () => { isLoggingOut = false; });

api.interceptors.response.use(
    (response) => response,
    (error) => {
        const status = error.response?.status as number | undefined;

        if (status === 401) {
            // Don't redirect when the login/register/oauth endpoints themselves return 401
            // (wrong credentials / bad code) — let the caller handle the error inline.
            const url: string = error.config?.url ?? '';
            const isAuthEndpoint =
                url.includes('/auth/login') ||
                url.includes('/auth/register') ||
                url.includes('/auth/oauth/exchange');
            if (!isAuthEndpoint && !isLoggingOut) {
                isLoggingOut = true;
                localStorage.removeItem('careeros_token');
                localStorage.removeItem('careeros_user');
                // Reset flag after navigation completes so re-login works.
                window.location.href = '/?modal=login';
            }
            return Promise.reject(error);
        }

        if (status === 403) {
            toast.error("You don't have permission to perform this action.");
            return Promise.reject(error);
        }

        if (status !== undefined && status >= 500) {
            toast.error('Something went wrong on the server. Please try again.');
            return Promise.reject(error);
        }

        if (!error.response) {
            toast.error('Network connection failed. Check your internet connection.');
        }

        return Promise.reject(error);
    },
);

export default api;
