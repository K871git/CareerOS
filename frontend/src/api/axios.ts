import axios from 'axios';
import toast from 'react-hot-toast';

const api = axios.create({
    baseURL: 'http://localhost:8000/api',
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

api.interceptors.response.use(
    (response) => response,
    (error) => {
        const status = error.response?.status as number | undefined;

        if (status === 401) {
            localStorage.removeItem('careeros_token');
            localStorage.removeItem('careeros_user');
            window.location.href = '/auth/login';
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
