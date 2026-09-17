import { createContext, useContext, useEffect, useReducer } from 'react';
import type { ReactNode } from 'react';
import toast from 'react-hot-toast';
import api from '../api/axios';

const IDLE_MS = 2 * 60 * 60 * 1000; // 2 hours
const WARN_MS = 60 * 1000;           // warn 60 s before logout

export interface AuthUser {
    id: number;
    name: string;
    email: string;
    consent_version?: string;
}

interface AuthState {
    user: AuthUser | null;
    token: string | null;
    isAuthenticated: boolean;
    isValidating: boolean;
}

type AuthAction =
    | { type: 'LOGIN'; payload: { user: AuthUser; token: string } }
    | { type: 'LOGOUT' }
    | { type: 'VALIDATED'; payload: { user: AuthUser } };

const TOKEN_KEY = 'careeros_token';
const USER_KEY  = 'careeros_user';

function loadInitialState(): AuthState {
    const token = localStorage.getItem(TOKEN_KEY);
    try {
        const user = JSON.parse(localStorage.getItem(USER_KEY) ?? 'null') as AuthUser | null;
        return { user, token, isAuthenticated: !!token, isValidating: !!token };
    } catch {
        return { user: null, token: null, isAuthenticated: false, isValidating: false };
    }
}

function authReducer(state: AuthState, action: AuthAction): AuthState {
    switch (action.type) {
        case 'LOGIN':
            localStorage.setItem(TOKEN_KEY, action.payload.token);
            localStorage.setItem(USER_KEY, JSON.stringify(action.payload.user));
            return { user: action.payload.user, token: action.payload.token, isAuthenticated: true, isValidating: false };
        case 'LOGOUT':
            localStorage.removeItem(TOKEN_KEY);
            localStorage.removeItem(USER_KEY);
            return { user: null, token: null, isAuthenticated: false, isValidating: false };
        case 'VALIDATED':
            localStorage.setItem(USER_KEY, JSON.stringify(action.payload.user));
            return { ...state, user: action.payload.user, isAuthenticated: true, isValidating: false };
        default:
            return state;
    }
}

interface AuthContextValue {
    state: AuthState;
    login: (user: AuthUser, token: string) => void;
    logout: () => void;
}

const AuthContext = createContext<AuthContextValue | null>(null);

export function AuthProvider({ children }: { children: ReactNode }) {
    const [state, dispatch] = useReducer(authReducer, undefined, loadInitialState);

    const login = (user: AuthUser, token: string) =>
        dispatch({ type: 'LOGIN', payload: { user, token } });

    const logout = () => dispatch({ type: 'LOGOUT' });

    // On mount: validate stored token against the server.
    // If 401, the axios interceptor clears localStorage and redirects.
    // If success, refresh the user object in case it changed.
    useEffect(() => {
        if (!state.token) return;

        api.get<{ data: AuthUser }>('/v1/auth/me')
            .then(res => dispatch({ type: 'VALIDATED', payload: { user: res.data.data } }))
            .catch(() => dispatch({ type: 'LOGOUT' }));
    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    // Idle timeout: logout after 2 h of no interaction, warn 60 s before.
    useEffect(() => {
        if (!state.isAuthenticated) return;

        let logoutTimer: ReturnType<typeof setTimeout>;
        let warnTimer: ReturnType<typeof setTimeout>;

        const reset = () => {
            clearTimeout(logoutTimer);
            clearTimeout(warnTimer);
            toast.dismiss('idle-warning');

            warnTimer = setTimeout(() => {
                toast('You will be logged out in 60 seconds due to inactivity.', {
                    id:       'idle-warning',
                    duration: WARN_MS + 2000,
                    icon:     '⏰',
                });
            }, IDLE_MS - WARN_MS);

            logoutTimer = setTimeout(() => {
                dispatch({ type: 'LOGOUT' });
                window.location.href = '/?modal=login';
            }, IDLE_MS);
        };

        const events: string[] = ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'];
        events.forEach(e => window.addEventListener(e, reset, { passive: true }));
        reset();

        return () => {
            clearTimeout(logoutTimer);
            clearTimeout(warnTimer);
            toast.dismiss('idle-warning');
            events.forEach(e => window.removeEventListener(e, reset));
        };
    }, [state.isAuthenticated]);

    return (
        <AuthContext.Provider value={{ state, login, logout }}>
            {children}
        </AuthContext.Provider>
    );
}

export function useAuth() {
    const ctx = useContext(AuthContext);
    if (!ctx) throw new Error('useAuth must be used within AuthProvider');
    return ctx;
}
