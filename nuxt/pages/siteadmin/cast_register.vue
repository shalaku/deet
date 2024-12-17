<script setup>
import CastRegister from '../../components/siteadmin/CastRegister.vue';

definePageMeta({
	layout: 'siteadmin',
	middleware: ['auth'],
});
</script>

<template>
	<CastRegister />
</template>
