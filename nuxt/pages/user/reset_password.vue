<script setup lang="ts">
import { useMutation } from '@tanstack/vue-query';
import Swal from 'sweetalert2';
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { authAPI } from '../../libs/api';

const router = useRouter();
const route = useRoute();
const resetPwdForm = ref({
	email: '',
	verificationCode: '',
	newPassword: '',
	confirmPassword: '',
});

const { mutate: handleFormSubmit } = useMutation({
	mutationFn: async () => {
		if (resetPwdForm.value.newPassword !== resetPwdForm.value.confirmPassword) {
			throw new Error('Password Mismatch');
		}
		const payload = {
			email: resetPwdForm.value.email,
			token: parseInt(resetPwdForm.value.verificationCode),
			password: resetPwdForm.value.newPassword,
		};
		return await authAPI.resetPassword(payload);
	},
	onSuccess: (response) => {
		if (response.status_code === 200) {
			router.push('login');
			Swal.fire({
				icon: 'success',
				title: 'Reset Password Success',
				text: 'Password Reset Successfully',
			});
		} else {
			Swal.fire({
				icon: 'error',
				title: 'Reset Password Failed',
				text: 'Password Reset Failed',
			});
		}
	},
	onError: (error) => {
		if (error.message === 'Password Mismatch') {
			Swal.fire({
				icon: 'error',
				title: 'Password Mismatch',
				text: 'New password and confirm password do not match.',
			});
		} else {
			console.error('Error resetting password:', error);
		}
	}
});

// 	try {
// 		  // Check if newPassword and confirmPassword match
// 		  if (resetPwdForm.value.newPassword !== resetPwdForm.value.confirmPassword) {
// 				Swal.fire({
// 					icon: 'error',
// 					title: 'Password Mismatch',
// 					text: 'New password and confirm password do not match.',
// 				});
// 				return; // Exit the function early if passwords do not match
// 			}
// 		const payload = {
// 			email: resetPwdForm.value.email,
// 			token: parseInt(resetPwdForm.value.verificationCode),
// 			password: resetPwdForm.value.newPassword,
// 		};
// 		const response = await authAPI.resetPassword(payload);
// 		if(response.status_code === 200) {
// 			router.push('login');
// 			Swal.fire({
// 						icon: 'success',
// 						title: 'Reset Password Success',
// 						text: 'Password Reset Successfully',
// 					});
// 		}else{
// 			Swal.fire({
// 						icon: 'error',
// 						title: 'Reset Password Failed',
// 						text: 'Password Reset Failed',
// 					});
// 		}
// 		console.log('Password reset successfully:', response);
// 	} catch (error) {
// 		console.error('Error resetting password:', error);
// 	}
// };
// Set the email from the query parameter
onMounted(() => {
    resetPwdForm.value.email = route.query.email as string || '';
	console.log('email', resetPwdForm.value.email);
});
</script>

<template lang="html">
	<section
		class="min-vh-100 d-flex align-items-center justify-content-center"
	>
		<div class="card text-center ms-3 me-3">
			<!-- <h1 class="card-header">Deet 管理ページ</h1> -->
			<div class="card-body">
				<h2 class="fs-1">パスワード再設定</h2>
				<p class="fs-4">メールで受け取った認証コードと新しいパスワードを入力してください。</p>				
				<form class="mt-5" @submit.prevent=" () => handleFormSubmit()">
					<div class="input-group mb-5">
						<input
							v-model="resetPwdForm.verificationCode"
							class="form-control"
							name="verificationCode"
							type="text"
							placeholder="認証コード"
						/>
					</div>
					<div class="input-group mb-4">
						<input
							v-model="resetPwdForm.newPassword"
							class="form-control"
							name="newPassword"
							type="password"
							placeholder="新パスワード"
						/>
					</div>
					<div class="input-group mb-4">
						<input
							v-model="resetPwdForm.confirmPassword"
							class="form-control"
							name="confirmPassword"
							type="password"
							placeholder="新パスワードを再入力"
						/>
					</div>
					<div class="row align-items-end">
						<div class="col-12">
							<button type="submit" class="btn deet-btn submit-btn px-4 fs-4">
								パスワード変更
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
