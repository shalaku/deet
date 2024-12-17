export interface ForgotPasswordEmailRequest {
    email: string;
}

export interface ForgotPasswordEmailResponse {
    message: string;
    status: string;
    status_code: number;
    errors?: {
        error: string;
    };
}