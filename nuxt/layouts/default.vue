<script setup lang="ts">
import { VueQueryDevtools } from '@tanstack/vue-query-devtools';
import { config } from '~/libs/config';
</script>

<template lang="html">
	<div>
		<NuxtPage />
		<VueQueryDevtools v-if="config.dev" />
	</div>
</template>
