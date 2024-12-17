<script setup lang="ts">
import '~/assets/css/siteadmin/layout.css';
</script>

<template lang="html">
	<div class="page-siteadmin site-admin-main-layout">
		<div class="site-admin-layout">
			<SiteadminHeader />
			<SiteadminSidebar />
			<div
				class="wrapper d-flex flex-column min-vh-100 justify-content-between flex-grow-1"
			>
				<slot />
				<SiteadminFooter />
			</div>
		</div>
		<!-- <VueQueryDevtools v-if="config.dev" /> -->
	</div>
</template>
