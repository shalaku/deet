<script setup>
import '~/assets/css/cast/pages/top.css';
definePageMeta({
	layout: 'cast',
	middleware: ['auth'],
});
</script>

<template>
	<CastTop />
</template>
