import { defineStore } from 'pinia';
import Swal from 'sweetalert2';
import { authAPI } from '~/libs/api';
import { authService } from '~/libs/utilities/services';

interface AuthStoreState {
	user: API.User | null;
	isAuthenticated: boolean;
}

const initialState: AuthStoreState = {
	user: null,
	isAuthenticated: false,
};

export const useAuthStore = defineStore('AuthStore', {
	state: () => initialState,
	actions: {
		async login({
			email,
			password,
			loginAs,
		}: API.UserLoginPayload & { loginAs: API.User['account_type'] }) {
			try {
				const { status, message, data } = await authAPI.login({
					email,
					password,
				});

				if (status !== 'SUCCESS') {
					throw new Error(message);
				}

				if (loginAs !== data.user.account_type) {
					throw new Error('メールアドレスもしくはパスワードが間違っています');
				}

				this.user = data.user;
				this.isAuthenticated = true;
				authService.setToken(data.access_token);
				authService.setRefreshToken(data.refresh_token);

				return true;
			} catch (error: any) {
				Swal.fire({					
					icon: 'error',
					title: 'ログインできません',
					text: error?.message.error || 'メールアドレスもしくはパスワードが間違っています',
				});

				return false;
			}
		},

		async register(payload: API.UserRegisterPayload) {
			try {
				const { status, message, data } = await authAPI.register(payload);

				if (status !== 'SUCCESS') {
					throw new Error(message);
				}

				this.user = data.user;
				this.isAuthenticated = true;
				authService.setToken(data.access_token);
				authService.setRefreshToken(data.refresh_token);

				Swal.fire({
					icon: 'success',
					title: 'Success!',
					text: 'You have successfully logged in!',
				});

				return true;
			} catch (error: any) {
				Swal.fire({
					icon: 'error',
					title: 'Login Failed',
					text: error?.message || 'Invalid credentials',
				});

				return false;
			}
		},

		async checkAuth() {
			try {
				const { status, message, data } = await authAPI.profile();

				if (status !== 'SUCCESS') {
					throw new Error(message);
				}

				this.user = data;
				this.isAuthenticated = true;
			} catch (_error) {
				this.user = null;
				this.isAuthenticated = false;
			}
		},

		async logout() {
			try {
				await authAPI.logout();
			} catch (error: any) {
				Swal.fire({
					icon: 'error',
					title: 'Logout Failed',
					text: error?.message || 'An error occurred while logging out',
				});
			} finally {
				this.user = null;
				this.isAuthenticated = false;
				authService.revokeAll();
			}
		},
	},
});
