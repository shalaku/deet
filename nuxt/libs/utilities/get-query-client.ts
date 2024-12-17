import { QueryClient } from '@tanstack/vue-query';

export const getQueryClient = () => {
	return new QueryClient({
		defaultOptions: {
			queries: {
				retry: false,
				staleTime: 5000,
				refetchOnWindowFocus: false,
			},
		},
	});
};
