<script setup lang="ts">
import { VueQueryDevtools } from '@tanstack/vue-query-devtools';
import { ref } from 'vue';
import '~/assets/css/customer/common.css';
import '~/assets/css/customer/layout.css';
import { config } from '~/libs/config';

const isSidebarVisible = ref(false);

const toggleSidebar = () => {
	isSidebarVisible.value = !isSidebarVisible.value;
};
const props = defineProps(['isSidebarVisible']);
</script>

<template lang="html">
	<div :class="['page-misc page-misc2', { 'without-sidebar': !isSidebarVisible, 'with-sidebar': isSidebarVisible }]">
		<CustomerHeader :toggleSidebar="toggleSidebar" />
			<CustomerMainContent :isVisible="isSidebarVisible" >
				<slot />
			</CustomerMainContent>
		<VueQueryDevtools v-if="config.dev" />
	</div>
</template>
