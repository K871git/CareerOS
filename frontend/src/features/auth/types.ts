export interface LoginCredentials {
    email: string;
    password: string;
}

export interface RegisterCredentials {
    name: string;
    email: string;
    mobile: string;
    password: string;
    password_confirmation: string;
}

export interface SendOtpPayload {
    email: string;
}

export interface VerifyOtpPayload {
    email: string;
    code: string;
}

export interface OtpSendData {
    message: string;
}
