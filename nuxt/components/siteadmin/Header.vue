<script setup lang="ts">

const appStore = useAppStore();
const authStore = useAuthStore();

useHead({
	link: [
    {
      rel: 'manifest',
      href: '/siteadmin/manifest.json' // Userアプリのマニフェスト
    },
	{
		rel: 'apple-touch-icon',
		type: 'image/x-icon',
		href: '/siteadmin/icons/apple-touch-icon.png',
	}
	]
});
</script>

<template>
	<header class="header header-sticky">
		<div class="flex">
			<NuxtLink class="logo" to="/siteadmin">
				<img src="/deet_logo_h_black.svg" style="width: 100px" alt="" class="ms-3"/>
			</NuxtLink>
			<!-- <CButton variant="ghost" size="lg" @click="appStore.toggleMainSidebar()"> -->
				<!-- <i class="icon-menu"></i> -->
			<!-- </CButton> -->
		</div>
		<div class="d-flex align-items-center">
			<div
				v-if="authStore.user?.name_ja"
				class="bg-dark text-light py-1 px-2 fs-6"
			>
				{{ authStore.user.name_ja }}
			</div>
			<div class="avatar">
				<img class="avatar-img" />
			</div>
		</div>
	</header>
</template>
