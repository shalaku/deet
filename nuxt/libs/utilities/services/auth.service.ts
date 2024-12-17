export class AuthService {
	getToken() {
		// return localStorage.getItem('token');
		return useCookie('token', { maxAge: 60 * 60 * 24 * 7 }).value;
	}

	getRefreshToken() {
		// return localStorage.getItem('refreshToken');
		return useCookie('refreshToken', { maxAge: 60 * 60 * 24 * 7 }).value;
	}

	setToken(token: string) {
		// localStorage.setItem('token', token);
		useCookie('token', { maxAge: 60 * 60 * 24 * 7 }).value = token;
	}

	setRefreshToken(refreshToken: string) {
		// localStorage.setItem('refreshToken', refreshToken);
		useCookie('refreshToken', { maxAge: 60 * 60 * 24 * 7 }).value = refreshToken;
	}

	revokeAll() {
		// localStorage.removeItem('token');
		// localStorage.removeItem('refreshToken');
		useCookie('token').value = null;
		useCookie('refreshToken').value = null;
	}
}

export const authService = new AuthService();
