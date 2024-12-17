<template>
	<div>
		<div class="d-flex justify-content-between align-items-center form-header">
			<h4>アカウント・ユーザー新規登録</h4>
			<button class="submit-btn" @click="submitForm" id="form-submit">
				ログアウト
			</button>
		</div>
		<div class="container user-info">
			<div class="card">
				<div class="card-header">
					<h4>初期ユーザー情報</h4>
				</div>
				<div class="card-body">
					<form id="user-info-form" action="">
						<div class="row">
							<div class="col-lg-2">
								<span class="placeholder col-12 placeholder-lg"></span>
								<p class="text-img">アイコン</p>
								<label class="custom-upload" for="custom-image-user"
									>ファイルを選択</label
								>
								<input type="file" id="custom-image-user" />
							</div>
							<div class="col-lg-10">
								<div class="row form-group">
									<div class="col-lg-2">
										<div>
											<label>アカウント</label>
											<select class="form-select" v-model="account_id" required>
												<option
													v-if="account"
													:value="account.id"
													:selected="account_id"
												>
													{{ account.name_ja }}
												</option>
											</select>
										</div>
									</div>

									<div class="col-lg-2">
										<div>
											<label>アカウントタイプ</label>
											<select
												class="form-select"
												v-model="account_type"
												required
											>
												<option
													v-if="user"
													:value="user.account_type"
													:selected="account_type"
												>
													{{ user.account_type }}
												</option>
											</select>
										</div>
									</div>
									<div class="col-lg-2">
										<div>
											<label>ユーザータイプ</label>
											<select class="form-select" v-model="user_type" required>
												<option :value="'-'">-</option>
												<option
													v-if="user"
													:disabled="user.account_type != 'store'"
													:value="'admin'"
												>
													Admin
												</option>
												<option
													v-if="user"
													:disabled="user.account_type != 'store'"
													:value="'staff'"
												>
													Staff
												</option>
											</select>
										</div>
									</div>
									<div class="col-lg-2">
										<div>
											<label>ステータス</label>
											<select class="form-select" v-model="status">
												<option :value="'active'">Active</option>
												<option :value="'freeze'">Freeze</option>
											</select>
										</div>
									</div>
								</div>
								<div class="row form-group">
									<div class="col-lg-5">
										<div>
											<label>ユーザー名</label>
											<input
												class="form-control"
												v-model="name_ja"
												type="text"
												placeholder="Default input"
											/>
										</div>
									</div>
									<div class="col-lg-5">
										<div>
											<label>パスワード</label>
											<input
												class="form-control"
												v-model="password"
												type="password"
												placeholder="Default input"
											/>
										</div>
									</div>
								</div>
								<div class="row form-group">
									<div class="col-lg-6">
										<div>
											<label>メールアドレス</label>
											<input
												class="form-control"
												v-model="email"
												type="email"
												placeholder="Default input"
											/>
										</div>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
import axios from '~/customplugin/axios.js';
import { useAccountStore } from '~/stores/accounts';
import { useUserStore } from '~/stores/users';
import Swal from 'sweetalert2';
export default {
	data() {
		return {
			user: null,
			account: null,
			account_id: '',
			account_type: '',
			user_type: '',
			status: '',
			name_ja: '',
			email: '',
			password: '',
		};
	},
	methods: {
		async fetchUser() {
			const userStore = useUserStore();
			await userStore.fetchUser();
			this.user = userStore.getUser;
		},
		async fetchAccount() {
			const accountStore = useAccountStore();
			await accountStore.getAccounts();
			this.account = accountStore.getAccount;
		},
		submitForm() {
			const formData = new FormData();
			formData.append('account_id', this.account_id);
			formData.append('account_type', this.account_type);
			formData.append('user_type', this.user_type);
			formData.append('status', this.status);
			formData.append('name_ja', this.name_ja);
			formData.append('email', this.email);
			formData.append('password', this.password);
			const token = localStorage.getItem('token');
			console.log(formData);
			try {
				axios
					.post('/siteadmin/user_register/action', formData, {
						headers: {
							Authorization: `Bearer ${token}`,
						},
					})
					.then((response) => {
						Swal.fire({
							title: 'Success!',
							text: response.data.message,
							icon: 'success',
						});
						console.log(response.data);
					})
					.catch((error) => {
						if (
							error.response &&
							error.response.data &&
							error.response.data.errors
						) {
							const errors = error.response.data.errors;
							const getErrorMessages = (errors) => {
								let messages = [];
								for (const key in errors) {
									if (Object.prototype.hasOwnProperty.call(errors, key)) {
										messages = messages.concat(errors[key]);
									}
								}
								return messages;
							};
							const errorMessages = getErrorMessages(errors);

							Swal.fire({
								title: 'Error!',
								html: `<ul>${errorMessages.map((message) => `<li>${message}</li>`).join('')}</ul>`,
								icon: 'error',
							});
							console.log(errorMessages);
						} else {
							Swal.fire({
								title: 'Error!',
								text: 'Oops, something went wrong',
								icon: 'error',
							});
							console.log(error);
						}
					});
			} catch (error) {
				Swal.fire({
					title: 'Error!',
					text: 'Oops, something went wrong',
					icon: 'error',
				});
				console.log(error);
			}
		},
	},
	created() {
		this.fetchAccount();
		this.fetchUser();
	},
};
</script>
<style scoped>
.form-header {
	padding: 11px 24px;
	background: #ffffff 0% 0% no-repeat padding-box;
}

.form-header h4 {
	font: normal normal normal 16px/20px Meiryo UI;
	margin: 0;
	color: #4f5d73;
}

.submit-btn {
	font: normal normal normal 12px/16px Meiryo UI;
	color: #ffffff;
	padding: 6px 14px;
	background: #20a8d8 0% 0% no-repeat padding-box;
	border-radius: 3px;
	border: none;
}

.user-info {
	margin-top: 40px;
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

.text-img {
	margin: 0;
}
</style>
