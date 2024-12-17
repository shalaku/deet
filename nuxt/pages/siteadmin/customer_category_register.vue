<script setup>
definePageMeta({
	layout: 'siteadmin',
	middleware: ['auth'],
});
</script>

<template>
	<div>
		<HeadingsPageHeading title="登録と編集" />
		<!-- Register Section -->
		<FormsRegistrationForm cardTitle="登録と編集" inputLabel="カテゴリ名" />
		<!-- DATA TABLE -->
		<TablesDataTable />
	</div>
</template>
