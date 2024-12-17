<script setup>
import NotificationBell from '../misc/NotificationBell.vue';
const props = defineProps(['toggleSidebar']); // Step 3: Accept toggle function as prop
const route = useRoute();
const appStore = useAppStore();

// 現在のページに応じてナビゲーションリンクをハイライトするための関数
const isActive = computed(() => (path) => {
	return route.path.startsWith(path);
});
useHead({
	link: [
    {
      rel: 'manifest',
      href: '/user/manifest.json' // Userアプリのマニフェスト
    },
	{
		rel: 'apple-touch-icon',
		type: 'image/x-icon',
		href: '/user/icons/apple-touch-icon.png',
	}
	]
});
</script>

<template>
	<header class="">
		<div class="header-container">
			<div class="logo-parent d-flex">
				<NuxtLink class="logo d-inline-flex" to="/user">
					<img src="/deet_logo_h_gold.svg" alt="" />
				</NuxtLink>
				<!-- <CButton -->
					<!-- variant="ghost" -->
					<!-- size="lg" -->
					<!-- @click="appStore.toggleMainSidebar()" -->
				<!-- > -->
					<!-- <i class="icon-menu"></i> -->
				<!-- </CButton> -->
			</div>
			<div class="d-block d-lg-none">
				<i class="icon-menu" @click="toggleSidebar"></i>
				<!-- Step 3: Use toggle function -->
				<i class="icon-x" @click="toggleSidebar"></i>
				<!-- Step 3: Use toggle function -->
			</div>
			<div class="notification-bell-container">
				<NotificationBell />
			</div>
		</div>
	</header>
</template>

<style scoped>
</style>
