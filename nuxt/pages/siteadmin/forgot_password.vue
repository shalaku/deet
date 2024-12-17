<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { authAPI } from '../../libs/api';
import { useMutation } from '@tanstack/vue-query';

const router = useRouter();
const emailAddress = ref('');

const { isPending, mutate: handleFormSubmit } = useMutation({
	mutationFn: async () => {
		const payload = { email: emailAddress.value };
		return await authAPI.sendForgotPasswordEmail(payload);
	},
	onSuccess: (response) => {
		console.log('Email sent successfully:', response);
		if (response.status_code === 200) {
			router.push({ path: 'reset_password', query: { email: emailAddress.value } });
		}
	},
	onError: (error) => {
		console.error('Error sending forgot password email:', error);
	}
});

</script>

<template lang="html">
	<section
		class="bg-body-tertiary min-vh-100 d-flex align-items-center justify-content-center"
	>
		<div class="card text-center">
			<h1 class="card-header">Deet 管理ページ</h1>
			<div class="card-body">
				<h2>Password reset</h2>
				<p class="fs-5">
					An authentication code will be sent to the email address you entered.
				</p>
				<p style="font-size: 12px">
					*If the corresponding e-mail does not exist, no e-mail will be sent.
					*URLs are valid for one hour.
				</p>
				<form @submit.prevent=" () => handleFormSubmit()">
					<div class="input-group mb-3">
						<input
							v-model="emailAddress"
							class="form-control"
							name="emailAddress"
							type="email"
							placeholder="メールアドレス"
						/>
					</div>
					<div class="row align-items-end">
						<div class="col-12">
							<button type="submit" class="btn deet-btn submit-btn px-4 fs-6">
								Sent authentication code
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</section>
</template>

<style scoped lang="css">
@font-face {
	font-family: Meiryo UI;
	src: url('/fonts/MeiryoUI.ttf');
}

h2 {
	font-size: 26px;
	font-weight: 700;
	border-bottom: 1px solid rgb(115, 107, 96);
	display: inline-block;
	padding-bottom: 10px;
	margin-bottom: 30px;
	text-align: center;
}

.card {
	background: #ffffff 0% 0% no-repeat padding-box;
	border: 1px solid #d8dbe0;
	border-radius: 4px;
	max-width: 560px;
}
.card-container {
	padding: 0 40px;
}
.card-header {
	background: #2f353a 0% 0% no-repeat padding-box;
	color: #fff;
	font: normal normal bold 16px/20px Meiryo UI;
	padding: 20px;
}

.card-body {
	padding: 50px;
	color: #707070;
}
.form-control:focus {
	border: 3px solid #a6cbf3;
	box-shadow: none;
}
@media screen and (max-width: 768px) {
	.card-body {
		padding: 2rem;
	}
}
</style>
