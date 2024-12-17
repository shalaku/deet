<script setup lang="ts">
import { useMutation } from '@tanstack/vue-query';
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { authAPI } from '../../libs/api/auth-api';
const router = useRouter();

const emailAddress = ref('');

// 	try {
// 		const payload = { email: emailAddress.value };
// 		const response = await authAPI.sendForgotPasswordEmail(payload);
// 		console.log('Email sent successfully:', response);
// 		if (response.status_code === 200) {
// 			// router.push('reset_password');
//             // Pass the email as a query parameter
//             // router.push({ name: 'reset_password', query: { email: emailAddress.value } });
// 			router.push({ path: 'reset_password', query: { email: emailAddress.value } });
// 		}
// 	} catch (error) {
// 		console.error('Error sending forgot password email:', error);
// 	}
// };

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
		class="min-vh-100 d-flex align-items-center justify-content-center"
	>
		<div class="card text-center ms-3 me-3">
			<!-- <h1 class="card-header">Deet</h1> -->
			<div class="card-body">
				<h2 class="fs-1">パスワードリセット</h2>
				<p class="fs-4">
					メールアドレスをご入力ください。<br>
					ご入力いただいたアドレスにパスワードリセット用の認証コードが送信されます。
				</p>
				<p class="fs-6">
					*メールアドレスが間違っている場合はメールが届きませんのでご注意ください。
				</p>
				<form class="mt-5" @submit.prevent=" () => handleFormSubmit()">
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
							<button type="submit" class="btn deet-btn submit-btn px-4 fs-4">
								認証コードを送信
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
	background: var(--deet-color-cotent-base) 0% 0% no-repeat padding-box;
	border: none;
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
	color: #ffffff;
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
