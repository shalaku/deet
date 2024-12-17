<script setup>
import NotificationBell from '../misc/NotificationBell.vue';
const props = defineProps(['toggleSidebar']); // Step 3: Accept toggle function as prop

const appStore = useAppStore();

useHead({
	link: [
    {
      rel: 'manifest',
      href: '/cast/manifest.json' // Userアプリのマニフェスト
    },
	{
		rel: 'apple-touch-icon',
		type: 'image/x-icon',
		href: '/cast/icons/apple-touch-icon.png',
	}
	]
});
</script>

<template>
	<header class="">
		<div class="header-container">
			<div class="logo-parent d-flex">
				<a class="logo d-inline-flex" href="/cast">
					<img src="/deet_logo_h_white.svg" alt="" />
				</a>
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
