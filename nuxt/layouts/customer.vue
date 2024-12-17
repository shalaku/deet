<script setup lang="ts">
import { getCurrentLocation } from '@/libs/utilities';
import { VueQueryDevtools } from '@tanstack/vue-query-devtools';
import { ref } from 'vue';
import '~/assets/css/customer/common.css';
import '~/assets/css/customer/layout.css';
import { config } from '~/libs/config';

getCurrentLocation();

const isSidebarVisible = ref(false);

const toggleSidebar = () => {
	isSidebarVisible.value = !isSidebarVisible.value;
};
const props = defineProps(['isSidebarVisible']);
</script>

<template lang="html">
	<div :class="['page-user', { 'without-sidebar': !isSidebarVisible, 'with-sidebar': isSidebarVisible }]">
		<CustomerHeader :toggleSidebar="toggleSidebar" />
		<div class="d-flex w-100">
			<CustomerSidebar :isVisible="isSidebarVisible" :toggleSidebar="toggleSidebar" />
			<CustomerMainContent :isVisible="isSidebarVisible" >
				<slot />
			</CustomerMainContent>
		</div>
		<CustomerFooter />
		<CustomerFooterMenu />
		<VueQueryDevtools v-if="config.dev" />
	</div>
</template>
