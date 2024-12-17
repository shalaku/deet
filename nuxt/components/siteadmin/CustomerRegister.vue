<template>
	<div>
		<div class="pb-0 pt-0">
			<div
				class="d-flex justify-content-between align-items-center content-header ps-4 pe-0"
			>
				<h4>男性会員登録</h4>
				<button
					type="submit"
					class="submit-btn btn deet-btn btn-sm fs-4 mt-0 me-4 px-5 py-2 fw-normal"
					@click="submitForm"
				>
					保存
				</button>
			</div>
		</div>

		<!-- Basic Information Section -->
		<div class="container mt-4">
			<div class="card card-dark mb-4">
				<div class="card-header">
					<h5 class="fs-3 px-3 mx-2 my-3">基本情報</h5>
				</div>
				<div class="card-body fs-5">
					<form @submit.prevent="submitForm">
						<div class="row mb-3">
							<!-- Name (Japanese) -->
							<div class="col-md-3">
								<label for="name_ja" class="required">*名前 (日本語)</label>
								<input
									type="text"
									v-model="form.name_ja"
									id="name_ja"
									required
									placeholder="名前を入力してください"
									class="form-control"
								/>
							</div>

							<!-- Name (Kana) -->
							<div class="col-md-3">
								<label for="name_kana">名前 (カナ)</label>
								<input
									type="text"
									v-model="form.name_kana"
									id="name_kana"
									required
									placeholder="カナを入力してください"
									class="form-control"
								/>
							</div>

							<!-- Name (Kana) -->
							<div class="col-md-3">
								<label for="nickname" class="required">*ニックネーム</label>
								<input
									type="text"
									v-model="form.nickname"
									id="nickname"
									required
									placeholder="ニックネームを入力してください"
									class="form-control"
								/>
							</div>
						</div>
						<div class="row mb-3">
							<!-- Birthday -->
							<div class="col-md-3">
								<label for="birthday">生年月日</label>
								<input
									type="date"
									v-model="form.birthday"
									id="birthday"
									required
									class="form-control"
								/>
							</div>

							<!-- Introducer ID (Number) -->
							<div class="col-md-3">
								<label for="introducer_id">紹介者ID</label>
								<input
									type="number"
									v-model="form.introducer_id"
									id="introducer_id"
									required
									placeholder="紹介者IDを入力してください"
									class="form-control"
								/>
							</div>

							<!-- Person in Charge ID (Number) -->
							<div class="col-md-3">
								<label for="person_in_charge_id">担当者ID</label>
								<select
									v-model="form.person_in_charge_id"
									name=""
									id="person_in_charge_id"
									class="form-select footwork-input fs-4"
								>
									<option
										v-for="option in personInChargeOptions"
										:key="option.label"
										:value="option.value"
										required
									>
										{{ option.label }}
									</option>
								</select>
							</div>
						</div>
						<div class="row mb-3">
							<!-- Category ID (Number) -->

							<div class="col-md-3">
								<label for="category_id">カテゴリ</label>
								<select
									v-model="form.category_id"
									name=""
									id="category_id"
									class="form-select footwork-input fs-4"
								>
									<option
										v-for="option in categoryOptions"
										:key="option.label"
										:value="option.value"
										required
									>
										{{ option.label }}
									</option>
								</select>
							</div>
							<!-- Status ID (Number) -->
							<div class="col-md-3">
								<label for="status_id">ステータス</label>
								<select
									v-model="form.status_id"
									name=""
									id="status_id"
									class="form-select footwork-input fs-4"
								>
									<option
										v-for="option in statusOptions"
										:key="option.label"
										:value="option.value"
										required
									>
										{{ option.label }}
									</option>
								</select>
							</div>

							<!-- Registered Date -->
							<div class="col-md-3">
								<label for="registered_date" class="required">*登録日</label>
								<input
									type="date"
									v-model="form.registered_date"
									id="registered_date"
									required
									class="form-control"
								/>
							</div>
						</div>
						<div class="row mb-3">
							<!--phone_number-->
							<div class="col-md-3">
								<label for="phone_number" class="required">*電話番号</label>
								<input
									type="tel"
									v-model="form.phone_number"
									id="phone_number"
									placeholder="電話番号を入力"
									class="form-control"
								/>
							</div>

							<!--email-->
							<div class="col-md-3">
								<label for="email" class="required">*email</label>
								<input
									type="text"
									v-model="form.email"
									id="email"
									required
									placeholder="メールアドレスを入力"
									class="form-control"
								/>
							</div>
							<!--password-->
							<div class="col-md-3">
								<label for="password" class="required">*password</label>
								<input
									type="password"
									v-model="form.password"
									id="password"
									required
									placeholder="パスワードを入力"
									class="form-control"
								/>
							</div>
						</div>
					</form>
				</div>
			</div>

			<!-- Tags of Preference Section -->
			<div class="card card-dark mb-4">
				<div class="card-header">
					<h5 class="fs-3 px-3 mx-2 my-3">好みのタグ</h5>
				</div>
				<div class="card-body">
					<div class="form-group">
						<!-- Input field for adding tags -->
						<input
							type="text"
							v-model="newTag"
							@keyup.enter="addTag"
							placeholder="タグを入力してエンターを押してください"
							class="form-control"
						/>
					</div>

					<!-- Display added tags as badges -->
					<div class="mt-2">
						<span
							v-for="(tag, index) in form.tag_of_preference"
							:key="tag + index"
							class="badge badge-info mr-1"
						>
							{{ tag }}&nbsp;
							<button
								@click="removeTag(index)"
								class="btn btn-link p-0"
								style="color: white"
							>
								&times;
							</button>
						</span>
					</div>
				</div>
			</div>

			<!-- Notices Section -->
			<div class="card card-dark mb-4">
				<div class="card-header">
					<h5 class="fs-3 px-3 mx-2 my-3">通知</h5>
				</div>
				<div class="card-body">
					<div class="form-group">
						<textarea
							v-model="form.notices"
							id="notices"
							placeholder="通知を入力してください"
							class="form-control"
							rows="4"
						></textarea>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import Swal from 'sweetalert2';
import { ref } from 'vue';
import { customerAPI } from '~/libs/api';
import { useCustomerStore } from '../../stores/customer.js';
const customerStore = useCustomerStore();

const form = ref({
	name_ja: '',
	name_kana: '',
	nickname: '新規会員',
	birthday: '',
	introducer_id: 1,
	person_in_charge_id: 1,
	category_id: 1,
	status_id: 201,
	registered_date: '',
	tag_of_preference: [],
	notices: '',
	phone_number: '',
	email: '',
	password: '',
});

const categoryOptions = ref([{ label: '一般男性', value: '1' }]);

const statusOptions = ref([
	{ label: '正会員', value: '201' },
	{ label: '凍結', value: '202' },
	{ label: '退会済み', value: '203' },
]);

const personInChargeOptions = ref([{ label: '紹介者１', value: '1' }]);

const newTag = ref('');

const addTag = () => {
	if (newTag.value.trim() !== '') {
		form.value.tag_of_preference.push(newTag.value.trim());
		newTag.value = '';
	}
};

const removeTag = (index) => {
	const updatedTags = [...form.value.tag_of_preference];
	updatedTags.splice(index, 1);
	form.value.tag_of_preference = updatedTags;
};

const resetForm = () => {
	form.value = {
		name_ja: '',
		name_kana: '',
		nickname: '新規会員',
		birthday: '',
		introducer_id: 1,
		person_in_charge_id: 1,
		category_id: 1,
		status_id: 201,
		registered_date: new Date().toISOString().split('T')[0],
		tag_of_preference: [],
		notices: '',
		phone_number: '',
		email: '',
		password: '',
	};
};

const submitForm = async () => {
	const formData = {
		...form.value,
		tag_of_preference: JSON.stringify(form.value.tag_of_preference),
	};
	try {
		await customerAPI.addCustomer(formData);
		Swal.fire({
			icon: 'success',
			title: '登録成功',
			text: 'お客様の登録が成功しました！',
			confirmButtonText: 'OK',
		});
		resetForm(); // フォームをリセット
	} catch (error) {
		Swal.fire({
			icon: 'error',
			title: '登録失敗',
			text: 'エラーが発生しました。詳細はコンソールをご確認ください。',
			footer: String(error), // Display the error details in the footer
			confirmButtonText: 'OK',
		});
	}
};

onMounted(async () => {
	resetForm();
});

</script>

<style scoped>
.required {
	color: red;
}
.card-dark .card-header {
	background-color: #2f353a;
	color: white;
}
.deet-btn {
	font-weight: normal !important;
}

/* Main content header */
.content-header {
	padding: 16px;
	background-color: #ffffff;
	border-bottom: 1px solid #e0e0e0;
}

.content-header h4 {
	margin: 0;
	color: #4f5d73;
	font-weight: bold;
	font-size: 18px;
}

/* Form styling */
.form-group {
	margin-bottom: 20px;
}

.form-group label {
	font-size: 14px;
	font-weight: 500;
	color: #4f5d73;
}

.form-control {
	width: 100%;
	padding: 8px;
	border: 1px solid #ced4da;
	border-radius: 4px;
	box-shadow: none;
}
.form-select {
	padding: 9px;
}

/* Card styling */
.card {
	border-radius: 6px;
	box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.card-body {
	padding: 20px;
}

.card-header {
	background-color: #f7f7f7;
	/* padding: 10px 20px; */
	font-weight: bold;
}

/* Badge styling for tags */
.badge {
	padding: 10px;
	font-size: 14px;
	margin-right: 10px;
}

.badge-info {
	background-color: #17a2b8;
	color: white;
}

/* Button styling */
.btn-success {
	padding: 10px 20px;
	background-color: #28a745;
	border-color: #28a745;
	color: white;
}

h5 {
	/* color: black; */
}

th,
td {
	font-weight: normal;
}
</style>
