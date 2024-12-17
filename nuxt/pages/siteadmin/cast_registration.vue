<script setup>
import CastRegister from '../../components/cast/registration.vue';

definePageMeta({
	layout: 'siteadmin',
	middleware: ['auth'],
});
</script>

<template>
	<CastRegister />
</template>
