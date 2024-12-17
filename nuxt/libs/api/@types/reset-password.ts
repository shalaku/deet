export interface ResetPasswordRequest {
    email: string;
    token: number;
    password: string;
}

export interface ResetPasswordResponse {
    status_code: number;
    message: string;
}