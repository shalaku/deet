import { defineStore } from 'pinia';
import axios from '~/customplugin/axios.js';
import Swal from 'sweetalert2';
export const useCastStore = defineStore('CastStore', {
	state: () => ({
		tags: null,
		categories: null,
		successMessage: '',
		errorMessage: '',
		isSubmitting: false,
	}),
	actions: {
		async registerCast(form) {
			const token = localStorage.getItem('token');
			if (!token) {
				this.errorMessage = 'User is not authenticated.';
				return;
			}

			this.isSubmitting = true;
			try {
				const response = await axios.post('/siteadmin/cast_register', form, {
					headers: {
						Authorization: `Bearer ${token}`,
					},
				});
				this.successMessage = 'Cast registered successfully!';
				Swal.fire({
					title: 'Success!',
					text: this.successMessage,
					icon: 'success',
				});
				this.errorMessage = '';
			} catch (error) {
				this.errorMessage = 'Failed to register cast. Please try again.';
				Swal.fire({
					title: 'Error!',
					text: this.errorMessage,
					icon: 'error',
				});
				console.error(error);
			} finally {
				this.isSubmitting = false;
			}
		},
		async getTags() {
			const token = localStorage.getItem('token');
			try {
				const response = await axios.get('/siteadmin/cast_spec_tags', {
					headers: { Authorization: `Bearer ${token}` },
				});
				if (response.data.success) {
					this.tags = response.data.data;
					console.log(this.tags);
				}
			} catch (error) {
				console.log(error);
			}
		},
		async registerTag(tagName) {
			const token = localStorage.getItem('token');

			try {
				const response = await axios.post(
					'siteadmin/cast_tag_register',
					{ spec_tag_name: tagName },
					{
						headers: {
							Authorization: `Bearer ${token}`,
						},
					},
				);
				Swal.fire({
					title: 'Success!',
					text: response.data.message,
					icon: 'success',
				});
				console.log(response.data);
				await this.getTags(); // Refresh the tags
			} catch (error) {
				if (
					error.response &&
					error.response.data &&
					error.response.data.errors
				) {
					const errorMessages = Object.values(error.response.data.errors)
						.flat()
						.join(', ');
					Swal.fire({
						title: 'Error!',
						text: errorMessages,
						icon: 'error',
					});
					console.log(errorMessages);
				} else {
					Swal.fire({
						title: 'Error!',
						text: 'An unexpected error occurred.',
						icon: 'error',
					});
					console.log('An unexpected error occurred.');
				}
			}
		},
		async updateTag(id, tagName) {
			const token = localStorage.getItem('token');
			try {
				const response = await axios.post(
					'siteadmin/cast_spec_tag/update',
					{ spec_tag_id: id, spec_tag_name: tagName },
					{
						headers: {
							Authorization: `Bearer ${token}`,
						},
					},
				);
				Swal.fire({
					title: 'Success!',
					text: response.data.message,
					icon: 'success',
				});
				console.log(response.data);
				await this.getTags(); // Refresh the tags
			} catch (error) {
				if (
					error.response &&
					error.response.data &&
					error.response.data.errors
				) {
					const errorMessages = Object.values(error.response.data.errors)
						.flat()
						.join(', ');
					Swal.fire({
						title: 'Error!',
						text: errorMessages,
						icon: 'error',
					});
					console.log(errorMessages);
				} else {
					Swal.fire({
						title: 'Error!',
						text: 'An unexpected error occurred.',
						icon: 'error',
					});
					console.log('An unexpected error occurred.');
				}
			}
		},
		async deleteTag(id) {
			const token = localStorage.getItem('token');
			const result = await Swal.fire({
				title: 'Are you sure?',
				text: "You won't be able to revert this!",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, delete it!',
			});

			if (result.isConfirmed) {
				try {
					const response = await axios.post(
						'siteadmin/cast_spec_tag/delete',
						{ spec_tag_id: id },
						{
							headers: {
								Authorization: `Bearer ${token}`,
							},
						},
					);
					console.log(response.data);
					await this.getTags(); // Refresh the tags
					Swal.fire('Deleted!', 'The tag has been deleted.', 'success');
				} catch (error) {
					if (
						error.response &&
						error.response.data &&
						error.response.data.errors
					) {
						const errorMessages = Object.values(error.response.data.errors)
							.flat()
							.join(', ');
						Swal.fire({
							title: 'Error!',
							text: errorMessages,
							icon: 'error',
						});
						console.log(errorMessages);
					} else {
						Swal.fire({
							title: 'Error!',
							text: 'An unexpected error occurred.',
							icon: 'error',
						});
						console.log('An unexpected error occurred.');
					}
				}
			}
		},

		async getCategories() {
			const token = localStorage.getItem('token');
			try {
				const response = await axios.get('/siteadmin/cast_categories', {
					headers: { Authorization: `Bearer ${token}` },
				});
				if (response.data.success) {
					this.categories = response.data.data;

					console.log(this.categories);
				}
			} catch (error) {
				console.log(error);
			}
		},
		async registerCategory(catregoryName) {
			const token = localStorage.getItem('token');
			try {
				const response = await axios.post(
					'siteadmin/cast_category_register',
					{ category_name: catregoryName },
					{
						headers: {
							Authorization: `Bearer ${token}`,
						},
					},
				);
				Swal.fire({
					title: 'Success!',
					text: response.data.message,
					icon: 'success',
				});
				console.log(response.data);
				await this.getCategories();
			} catch (error) {
				if (
					error.response &&
					error.response.data &&
					error.response.data.errors
				) {
					const errorMessages = Object.values(error.response.data.errors)
						.flat()
						.join(', ');
					Swal.fire({
						title: 'Error!',
						text: errorMessages,
						icon: 'error',
					});
					console.log(errorMessages);
				} else {
					Swal.fire({
						title: 'Error!',
						text: 'An unexpected error occurred.',
						icon: 'error',
					});
					console.log('An unexpected error occurred.');
				}
			}
		},
		async updateCategory(id, catName) {
			const token = localStorage.getItem('token');
			try {
				const response = await axios.post(
					'siteadmin/cast_category/update',
					{ category_id: id, category_name: catName },
					{
						headers: {
							Authorization: `Bearer ${token}`,
						},
					},
				);
				Swal.fire({
					title: 'Success!',
					text: response.data.message,
					icon: 'success',
				});
				console.log(response.data);
				await this.getCategories();
			} catch (error) {
				if (
					error.response &&
					error.response.data &&
					error.response.data.errors
				) {
					const errorMessages = Object.values(error.response.data.errors)
						.flat()
						.join(', ');
					Swal.fire({
						title: 'Error!',
						text: errorMessages,
						icon: 'error',
					});
					console.log(errorMessages);
				} else {
					Swal.fire({
						title: 'Error!',
						text: 'An unexpected error occurred.',
						icon: 'error',
					});
					console.log('An unexpected error occurred.');
				}
			}
		},
		async deleteCategory(id) {
			const token = localStorage.getItem('token');
			const result = await Swal.fire({
				title: 'Are you sure?',
				text: "You won't be able to revert this!",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, delete it!',
			});

			if (result.isConfirmed) {
				try {
					const response = await axios.post(
						'siteadmin/cast_category/delete',
						{ category_id: id },
						{
							headers: {
								Authorization: `Bearer ${token}`,
							},
						},
					);
					console.log(response.data);
					await this.getCategories(); // Refresh the categories
					Swal.fire('Deleted!', 'The category has been deleted.', 'success');
				} catch (error) {
					if (
						error.response &&
						error.response.data &&
						error.response.data.errors
					) {
						const errorMessages = Object.values(error.response.data.errors)
							.flat()
							.join(', ');
						Swal.fire({
							title: 'Error!',
							text: errorMessages,
							icon: 'error',
						});
						console.log(errorMessages);
					} else {
						Swal.fire({
							title: 'Error!',
							text: 'An unexpected error occurred.',
							icon: 'error',
						});
						console.log('An unexpected error occurred.');
					}
				}
			}
		},
	},
	getters: {
		getCastTags: (state) => state.tags,
		getCastCategories: (state) => state.categories,
	},
});
