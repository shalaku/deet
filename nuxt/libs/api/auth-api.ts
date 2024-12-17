import { config } from '../config';
import {
	authService,
	HttpAuthService,
	HttpService,
} from '../utilities/services';
import type {
	User,
	UserLoginPayload,
	UserLoginResponse,
	UserRegisterPayload,
	ForgotPasswordEmailRequest,
	 ForgotPasswordEmailResponse,
	 ResetPasswordRequest, 
	 ResetPasswordResponse 
} from './@types';

class AuthAPI {
	constructor(
		private readonly httpService: HttpService,
		private readonly httpAuthService: HttpService,
	) {}

	login(payload: UserLoginPayload) {
		return this.httpService.post<UserLoginResponse>('login', payload);
	}

	logout() {
		return this.httpAuthService.post('logout', {});
	}

	register(payload: UserRegisterPayload) {
		return this.httpService.post<UserLoginResponse>('register', payload);
	}

	profile() {
		return this.httpAuthService.get<User>('profile');
	}

	sendForgotPasswordEmail(payload: ForgotPasswordEmailRequest) {
		return this.httpService.post<ForgotPasswordEmailResponse>('send-forgot-password-email', payload);
	}
	
	resetPassword(payload: ResetPasswordRequest): Promise<ResetPasswordResponse> {
        return this.httpService.post<ResetPasswordResponse>('reset-password', payload);
    }
}

const httpService = new HttpService(config.apiBaseURL);
const httpAuthService = new HttpAuthService(config.apiBaseURL, authService);
export const authAPI = new AuthAPI(httpService, httpAuthService);
