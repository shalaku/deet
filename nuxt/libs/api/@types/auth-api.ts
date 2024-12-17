export interface UserLoginPayload {
	email: string;
	password: string;
}

export interface User {
    id:number;
	name_ja: string;
	email: string;
	account_type: 'admin' | 'store' | 'cast' | 'customer';
	user_type?: 'admin' | 'staff';
    user_id:string,
    user_sig:string,
    sdkAppId:string,
    stripe_customer_id:string,
}

export interface UserLoginResponse {
	access_token: string;
	token_type: string;
	expires_in: number;
	refresh_token: string;
	user: User;
}
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
export interface ResetPasswordRequest {
    email: string;
    token: number;
    password: string;
}

export interface ResetPasswordResponse {
    status_code: number;
    message: string;
}

export type UserRegisterPayload = User;
