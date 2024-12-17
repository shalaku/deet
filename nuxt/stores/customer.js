import { defineStore } from 'pinia';
import axios from '~/customplugin/axios.js';
import Swal from 'sweetalert2';

export const useCustomerStore = defineStore('CastStore', {
	state: () => ({
		customers: [],
	}),

	actions: {
		async registerCustomer(formData) {
			const token = localStorage.getItem('token');

			if (!token) {
				Swal.fire({
					title: 'Error!',
					text: 'User is not authenticated.',
					icon: 'error',
				});
				return;
			}

			try {
				const response = await axios.post(
					'/customers',
					formData,
					{
						headers: {
							Authorization: `Bearer ${token}`,
						},
					},
				);

				Swal.fire({
					title: 'Success!',
					text: 'Customer registered successfully!',
					icon: 'success',
				});

				// Optionally add the new customer to the state (customers list)
				this.customers.push(response.data);
			} catch (error) {
				Swal.fire({
					title: 'Error!',
					text: error.response
						? error.response.data.message
						: 'Failed to register customer.',
					icon: 'error',
				});
				console.error('Error registering customer:', error);
			}
		},

		// Optionally, you can add a method to fetch all customers
		async fetchCustomers() {
			const token = localStorage.getItem('token');
			if (!token) {
				Swal.fire({
					title: 'Error!',
					text: 'User is not authenticated.',
					icon: 'error',
				});
				return;
			}

			try {
				const response = await axios.get('/siteadmin/customer_list/', {
					headers: {
						Authorization: `Bearer ${token}`,
					},
				});

				this.customers = response.data;
			} catch (error) {
				Swal.fire({
					title: 'Error!',
					text: error.response
						? error.response.data.message
						: 'Failed to fetch customers.',
					icon: 'error',
				});
				console.error('Error fetching customers:', error);
			}
		},
	},
});
