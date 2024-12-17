<script setup>
definePageMeta({
	layout: 'siteadmin',
	middleware: ['auth'],
});
</script>

<template>
	<SiteadminAccountSettings />
</template>

<style scoped>
.account-info {
	margin-top: 50px;
}

.card {
	border: 1px solid #e0e0e0;
	border-radius: 4px;
}

.card-header {
	background: #f7f7f7 0% 0% no-repeat padding-box;
	padding: 14px 22px;
}

.card-header h4 {
	font: normal normal bold 14px/18px Meiryo UI;
	color: #3c4b64;
	margin: 0;
}

label {
	font: normal normal normal 12px/24px Meiryo UI;
	color: #3c4b64;
}

.form-group {
	margin-bottom: 9px;
}

.user-info {
	margin-top: 40px;
}

input[type='file'] {
	display: none;
}

input {
	border-radius: 5px !important;
}

.form-control:focus {
	border: 1px solid #a6cbf3;
	box-shadow: none;
}

select:focus {
	border: 1px solid #a6cbf3;
	box-shadow: none;
}

.custom-upload {
	display: inline-block;
	cursor: pointer;
	background: #2f353a 0% 0% no-repeat padding-box;
	border-radius: 3px;
	font: normal normal normal 12px/16px Meiryo UI;
	letter-spacing: 0px;
	color: #ffffff;
	padding: 6px 18px;
}

.input-group-text {
	background: #ffffff 0% 0% no-repeat padding-box;
	border: none;
	padding: 0 4px;
}

.form-header {
	padding: 11px 24px;
	background: #ffffff 0% 0% no-repeat padding-box;
}

.form-header h4 {
	font: normal normal normal 16px/20px Meiryo UI;
	margin: 0;
	color: #4f5d73;
}

.text-img {
	margin: 0;
}

.submit-btn {
	font: normal normal normal 12px/16px Meiryo UI;
	color: #ffffff;
	padding: 6px 14px;
	background: #20a8d8 0% 0% no-repeat padding-box;
	border-radius: 3px;
	border: none;
}
</style>
