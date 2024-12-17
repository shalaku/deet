<script setup>
import RandomCallCast from '~/components/customer/RandomCallCast.vue';

definePageMeta({
	layout: 'customer',
	middleware: 'auth',
});
</script>

<template>
	<RandomCallCast />
</template>