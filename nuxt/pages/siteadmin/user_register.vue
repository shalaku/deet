<template>
	<SiteadminUserRegister />
</template>
<script>
definePageMeta({
	layout: 'siteadmin',
	middleware: ['auth'],
});
</script>
