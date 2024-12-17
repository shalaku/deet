import { defineStore } from 'pinia';
import axios from '~/customplugin/axios.js';

export const useAccountStore = defineStore('AccountStore', {
	state: () => ({
		account: null,
	}),
	actions: {
		async getAccounts() {
			const token = localStorage.getItem('token');
			try {
				const response = await axios.get('/siteadmin/user_register', {
					headers: { Authorization: `Bearer ${token}` },
				});
				if (response.data.success) {
					this.account = response.data.data;

					console.log(this.account);
				}
			} catch (error) {
				console.log(error);
			}
		},
	},
	getters: {
		getAccount: (state) => state.account,
	},
});
