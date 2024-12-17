<script setup>
definePageMeta({
	layout: 'customer',
	middleware: ['auth'],
	// middleware: 'userauth',
});
</script>

<template>
	<CustomerTop />
</template>
