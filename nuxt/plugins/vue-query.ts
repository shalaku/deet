import { useState } from '#app';
import type {
	DehydratedState,
	VueQueryPluginOptions,
} from '@tanstack/vue-query';
import {
	QueryClient,
	VueQueryPlugin,
	dehydrate,
	hydrate,
} from '@tanstack/vue-query';

export default defineNuxtPlugin((nuxtApp) => {
	const vueQueryState = useState<DehydratedState | null>('vue-query');

	const queryClient = new QueryClient({
		defaultOptions: {
			queries: {
				retry: false,
				staleTime: 5000,
				refetchOnWindowFocus: false,
			},
		},
	});

	const options: VueQueryPluginOptions = {
		queryClient,
	};

	nuxtApp.vueApp.use(VueQueryPlugin, options);

	if (import.meta.server) {
		nuxtApp.hooks.hook('app:rendered', () => {
			vueQueryState.value = dehydrate(queryClient);
		});
	}

	if (import.meta.client) {
		nuxtApp.hooks.hook('app:created', () => {
			hydrate(queryClient, vueQueryState.value);
		});
	}
});
