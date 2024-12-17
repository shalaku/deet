// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
	compatibilityDate: '2024-04-03',
	devtools: { enabled: process.env.NUXT_DEVTOOLS_ENABLED === 'true' },
	app: {
		head: {
			title: 'Deet',
			meta: [
				{ charset: 'utf-8' },
				{ name: 'viewport', content: 'width=device-width, initial-scale=1' },
				{ name: 'format-detection', content: 'telephone=no' },
				{
					hid: 'description',
					name: 'description',
					content: 'Nuxt.js TypeScript project',
				},
			],
			link: [
				{ rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
			],
		},
	},
	build: {
		transpile: ['@coreui/vue'],
	},
	css: ['~/assets/css/main.css'],
	modules: ['@pinia/nuxt'],
	imports: {
		dirs: ['./stores'],
	},
	plugins: [
		'~/plugins/castRegisterServiceWorker.js',  // Register Cast service worker plugin
		'~/plugins/userRegisterServiceWorker.js',  // Register User service worker plugin
		'~/plugins/siteadminRegisterServiceWorker.js',  // Register Siteadmin service worker plugin
	],
	vite: {
		server: {
		  watch: {
			usePolling: process.env.NUXT_VITE_POLLING === 'true', // 環境変数で制御
		  },
		  hmr: {
			protocol: 'wss',
			clientPort: 443,
			port: 24678,
			path: 'hmr/'
		  }
		}
	  }
});
