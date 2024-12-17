<script setup lang="ts">
import { VueQueryDevtools } from '@tanstack/vue-query-devtools';
// import '@coreui/coreui/dist/css/coreui.min.css'
import { config } from '~/libs/config';
</script>

<template lang="html">
	<Siteadmin2SubcomponentsHeader />
	<Siteadmin2SubcomponentsSidebar />
	<slot />
	<Siteadmin2SubcomponentsFooter />
</template>
