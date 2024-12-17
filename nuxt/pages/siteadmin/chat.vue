<script setup>
import Chat from '../../components/siteadmin/Chat.vue';

definePageMeta({
	layout: 'siteadmin',
	middleware: ['auth'],
});
</script>

<template>
	<Chat />
</template>
