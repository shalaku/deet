<template>
	<div :class="['main-content wrapper', { 'full-width': !isSidebarVisible, 'with-sidebar': isSidebarVisible }]">
		<div class="flex-grow-1">
			<slot />
		</div>
	</div>
</template>

<script setup>
const props = defineProps(['isSidebarVisible']);
</script>

<style scoped>

</style>
