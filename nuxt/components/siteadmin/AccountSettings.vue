<script>
import axios from '~/customplugin/axios.js';
import Swal from 'sweetalert2';

export default {
	data() {
		return {
			name_ja: '',
			chef_admin: '',
			post_code_1: '',
			post_code_2: '',
			prefectures: '',
			municipalities: '',
			address_1: '',
			address_2: '',
			phone: '',
			email: '',
		};
	},
	methods: {
		submitForm() {
			const formData = new FormData();
			formData.append('name_ja', this.name_ja);
			formData.append('chef_admin', this.chef_admin);
			formData.append('post_code', `${this.post_code_1}-${this.post_code_2}`);
			formData.append('prefectures', this.prefectures);
			formData.append('municipalities', this.municipalities);
			formData.append('address_1', this.address_1);
			formData.append('address_2', this.address_2);
			formData.append('phone', this.phone);
			formData.append('email', this.email);

			const token = localStorage.getItem('token');
			try {
				axios
					.post('/siteadmin/account_settings/update', formData, {
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
};
</script>

<template>
	<div>
		<div class="d-flex justify-content-between align-items-center form-header">
			<h4>アカウント設定</h4>
			<button class="submit-btn" @click="submitForm" id="form-submit">
				更新
			</button>
		</div>
		<div class="container account-info">
			<div class="card">
				<div class="card-header">
					<h4>アカウント基本情報</h4>
				</div>
				<div class="card-body">
					<form id="account-info-form" action="">
						<div class="row">
							<div class="col-lg-2">
								<span class="placeholder col-12 placeholder-lg"></span>
								<p class="text-img">アイコン</p>
								<label class="custom-upload" for="custom-image-acc"
									>ファイルを選択</label
								>
								<input type="file" id="custom-image-acc" />
							</div>
							<div class="col-lg-10">
								<div class="row form-group">
									<div class="col-lg-4">
										<div>
											<label>アカウント名（法人名）</label>
											<input
												class="form-control"
												v-model="name_ja"
												type="text"
												placeholder="Default input"
											/>
										</div>
									</div>
									<div class="col-lg-4">
										<div>
											<label>管理責任者</label>
											<input
												class="form-control"
												v-model="chef_admin"
												type="text"
												placeholder="Default input"
											/>
										</div>
									</div>
								</div>
								<div class="row form-group">
									<div class="col-lg-3">
										<div>
											<label>郵便番号</label>
											<div class="input-group">
												<input
													type="text"
													v-model="post_code_1"
													class="form-control"
												/>
												<span class="input-group-text">-</span>
												<input
													type="text"
													v-model="post_code_2"
													class="form-control"
												/>
											</div>
										</div>
									</div>
									<div class="col-lg-2">
										<div>
											<label>都道府県</label>
											<select class="form-select" v-model="prefectures">
												<option>Fukuoka</option>
											</select>
										</div>
									</div>
									<div class="col-lg-3">
										<div>
											<label>市区町村</label>
											<input
												class="form-control"
												v-model="municipalities"
												type="text"
												placeholder="Default input"
											/>
										</div>
									</div>
									<div class="col-lg-4">
										<div>
											<label>町名など</label>
											<input
												class="form-control"
												v-model="address_1"
												type="text"
												placeholder="Default input"
											/>
										</div>
									</div>
								</div>
								<div class="row form-group">
									<div class="col-lg-5">
										<div>
											<label>番地以降</label>
											<input
												class="form-control"
												v-model="address_2"
												type="text"
												placeholder="Default input"
											/>
										</div>
									</div>
									<div class="col-lg-4">
										<div>
											<label>電話番号</label>
											<input
												class="form-control"
												v-model="phone"
												type="text"
												placeholder="Default input"
											/>
										</div>
									</div>
									<div class="col-lg-3">
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
