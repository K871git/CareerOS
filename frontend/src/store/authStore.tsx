import { createContext, useContext, useReducer } from 'react';
import type { ReactNode } from 'react';

export interface AuthUser {
    id: number;
    name: string;
    email: string;
}

interface AuthState {
    user: AuthUser | null;
    token: string | null;
    isAuthenticated: boolean;
}

type AuthAction =
    | { type: 'LOGIN'; payload: { user: AuthUser; token: string } }
    | { type: 'LOGOUT' };

const TOKEN_KEY = 'careeros_token';
const USER_KEY  = 'careeros_user';

function loadInitialState(): AuthState {
    const token = localStorage.getItem(TOKEN_KEY);
    try {
        const user = JSON.parse(localStorage.getItem(USER_KEY) ?? 'null') as AuthUser | null;
        return { user, token, isAuthenticated: !!token };
    } catch {
        return { user: null, token: null, isAuthenticated: false };
    }
}

function authReducer(state: AuthState, action: AuthAction): AuthState {
    switch (action.type) {
        case 'LOGIN':
            localStorage.setItem(TOKEN_KEY, action.payload.token);
            localStorage.setItem(USER_KEY, JSON.stringify(action.payload.user));
            return { user: action.payload.user, token: action.payload.token, isAuthenticated: true };
        case 'LOGOUT':
            localStorage.removeItem(TOKEN_KEY);
            localStorage.removeItem(USER_KEY);
            return { user: null, token: null, isAuthenticated: false };
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
