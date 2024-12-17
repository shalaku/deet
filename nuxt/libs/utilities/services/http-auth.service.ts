import type { AuthService } from './auth.service';
import { HttpService } from './http.service';

export class HttpAuthService extends HttpService {
	constructor(
		baseURL: string,
		private readonly authService: AuthService,
	) {
		super(baseURL, {
			getToken: () => this.authService.getToken(),
			getRefreshToken: () => this.authService.getRefreshToken(),
			onRetrieveTokens: ({ token, refreshToken }) => {
				this.authService.setToken(token);
				this.authService.setRefreshToken(refreshToken);
			},
			onUnauthorised: () => this.authService.revokeAll(),
		});
	}
}
