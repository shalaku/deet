<script setup>
definePageMeta({
	layout: 'customer',
	middleware: 'auth',
});
</script>

<template>
	<CustomerChat />
</template>
