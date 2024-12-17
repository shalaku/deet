import { authService } from '~/libs/utilities/services';

export default defineNuxtRouteMiddleware(async (to, from) => {
	const authStore = useAuthStore();
	const token = authService.getToken();
	const toPathArr = to.path.split('/').filter((x) => x);
	const fromPathArr = from.path.split('/').filter((x) => x);

	try {
		if (!token) {
			throw new Error('Auth token not found');
		}

		await authStore.checkAuth();
		const isAuthenticated = authStore.isAuthenticated;
		const userRole = authStore.user?.account_type;

		if (!isAuthenticated) {
			throw new Error('Unauthorized');
		}

		if (!userRole) {
			throw new Error('User role not found');
		}

		if (toPathArr[0] === 'siteadmin' && !['admin'].includes(userRole)) {
			return navigateTo('/siteadmin/login');
		}

		if (toPathArr[0] === 'cast' && !['cast'].includes(userRole)) {
			return navigateTo('/cast/login');
		}

		if (toPathArr[0] === 'user' && !['customer'].includes(userRole)) {
			return navigateTo('/user/login');
		}
	} catch (error) {
		if (fromPathArr[0] === 'siteadmin') {
			return navigateTo('/siteadmin/login');
		}

		if (fromPathArr[0] === 'cast') {
			return navigateTo('/cast/login');
		}

		return navigateTo('/user/login');
	}
});
