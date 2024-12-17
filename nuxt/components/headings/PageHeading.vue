<script setup>
import { defineProps } from 'vue';
const props = defineProps({
	title: String,
});
</script>

<template>
	<div class="p-2 bg-light text-sm border-bottom">
		<h4 class="text-secondary font-weight-bold">{{ props.title }}</h4>
	</div>
</template>
