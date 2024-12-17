<script setup lang="ts">
import { getCurrentLocation } from '@/libs/utilities';
import { ref } from 'vue';
import '~/assets/css/cast/common.css';
import '~/assets/css/cast/layout.css';

getCurrentLocation();

const isSidebarVisible = ref(false);

const toggleSidebar = () => {
	isSidebarVisible.value = !isSidebarVisible.value;
};
const props = defineProps(['isSidebarVisible']);
</script>

<template lang="html">
	<div
		:class="[
			'page-cast',
			{
				'without-sidebar': !isSidebarVisible,
				'with-sidebar': isSidebarVisible,
			},
		]"
	>
		<CastHeader :toggleSidebar="toggleSidebar" />
		<div class="d-flex w-100">
			<CastSidebar :isVisible="isSidebarVisible" :toggleSidebar="toggleSidebar"/>
			<CastMainContent :isSidebarVisible="isSidebarVisible">
				<slot />
			</CastMainContent>
		</div>
		<CastFooter />
		<CastFooterMenu />
		<!-- <VueQueryDevtools v-if="config.dev" /> -->
	</div>
</template>
