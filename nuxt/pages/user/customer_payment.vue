<script setup>
import CustomerPayment from '~/components/customer/CustomerPayment.vue';

definePageMeta({
	layout: 'customer',
	middleware: 'auth',
});
</script>

<template>
	<CustomerPayment />
</template>
