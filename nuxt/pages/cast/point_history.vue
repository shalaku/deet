<script setup>
import '~/assets/css/cast/pages/order_history.css';

definePageMeta({
	layout: 'cast',
	middleware: 'auth',
});
</script>

<template>
	<CastPointHistory />
</template>
