import { defineStore } from 'pinia';
import axios from '~/customplugin/axios.js';
import Swal from 'sweetalert2';

export const useUserStore = defineStore('UserStore', {
	state: () => ({
		user: null,
		token: null,
		isAuthenticated: false,
		refreshTimer: null,
	}),
	actions: {
		async login(email, password) {
			try {
				const response = await axios.post('/login', { email, password });
				if (response.data.status === 'SUCCESS') {
					this.token = response.data.data.access_token;
					this.saveUser(response.data.data.user);
					this.saveToken(this.token, response.data.data.expires_in);
					Swal.fire({
						title: 'Success!',
						text: 'You have successfully logged in!',
						icon: 'success',
					});
					this.scheduleTokenRefresh(response.data.data.expires_in);
					return {
						success: true,
						message: response.data.message,
					};
				}
			} catch (error) {
				if (error.response?.data.message) {
					Swal.fire({
						icon: 'error',
						title: 'Login Failed',
						text: error.response.data.message,
					});
				} else {
					Swal.fire({
						icon: 'error',
						title: 'Login Failed',
						text: 'Invalid credentials',
					});
				}
			}
		},

		async checkAuth() {
			// eslint-disable-next-line nuxt/prefer-import-meta
			if (!process.client) return false;

			const token = localStorage.getItem('token');
			const isLoggedInCookie = this.getCookie('isLoggedIn');
			const expiresAt = localStorage.getItem('expiresAt');

			if (!token || !isLoggedInCookie || !expiresAt) return false;

			const expiresIn = (expiresAt - Date.now()) / 1000;

			if (expiresIn <= 0) {
				return false;
			}

			try {
				const response = await axios.get('/profile', {
					headers: { Authorization: `Bearer ${token}` },
				});

				if (response.data.status === 'SUCCESS') {
					this.user = response.data.data;
					this.isAuthenticated = true;
					this.scheduleTokenRefresh(expiresIn);
				}

				return true;
			} catch (error) {
				console.error(error);
			}

			return false;
		},

		async fetchUser() {
			try {
				const token = localStorage.getItem('token');
				const response = await axios.get('/profile', {
					headers: { Authorization: `Bearer ${token}` },
				});

				if (response.data.status === 'SUCCESS') {
					this.user = response.data.data;
					this.isAuthenticated = true;
				}
			} catch (error) {
				console.log(error);
			}
		},

		scheduleTokenRefresh(expiresIn) {
			if (this.refreshTimer) {
				clearTimeout(this.refreshTimer);
			}
			let refreshTimer = (expiresIn - 300) * 1000;
			this.refreshTimer = setTimeout(async () => {
				await this.refreshToken();
			}, refreshTimer);
		},

		async refreshToken() {
			try {
				const token = localStorage.getItem('token');
				const response = await axios.post(
					'/refresh',
					{},
					{
						headers: { Authorization: `Bearer ${token}` },
					},
				);
				if (response.data.status === 'SUCCESS') {
					this.updateToken(
						response.data.data.access_token,
						response.data.data.expires_in,
					);
				}
			} catch (error) {
				console.error('Error refreshing token:', error);
			}
		},

		updateToken(token, expiresIn) {
			this.token = token;
			localStorage.setItem('token', token);
			const expiresAt = Date.now() + expiresIn * 1000;
			localStorage.setItem('expiresAt', expiresAt);
			this.saveCookie('isLoggedIn', true, expiresIn);
			this.scheduleTokenRefresh(expiresIn);
		},

		saveToken(token, expiresIn) {
			this.token = token;
			localStorage.setItem('token', token);
			const expiresAt = Date.now() + expiresIn * 1000;
			localStorage.setItem('expiresAt', expiresAt);
			this.saveCookie('isLoggedIn', true, expiresIn);
		},

		saveUser(user) {
			this.user = user;
		},

		saveCookie(name, value, maxAge) {
			document.cookie = `${name}=${value}; max-age=${maxAge}; path=/;`;
		},

		getCookie(name) {
			const cookies = document.cookie.split(';').reduce((acc, cookie) => {
				const [key, value] = cookie.trim().split('=');
				acc[key] = value;
				return acc;
			}, {});
			return cookies[name];
		},
	},
	getters: {
		getUser: (state) => state.user,
		getAuth: (state) => state.isAuthenticated,
	},
});
