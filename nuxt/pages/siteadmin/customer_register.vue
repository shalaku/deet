<script setup>
import CustomerRegister from '../../components/siteadmin/CustomerRegister.vue';
definePageMeta({
	layout: 'siteadmin',
	middleware: ['auth'],
});
</script>

<template>
	<CustomerRegister />
</template>
