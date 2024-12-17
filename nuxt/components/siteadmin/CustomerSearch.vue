<script setup>
import { formatNumber, getStatus } from '@/libs/utilities';
import { CModal, CModalBody, CModalHeader } from '@coreui/vue';
import Swal from 'sweetalert2';
import { onMounted, ref } from 'vue';
import { pointAPI } from '~/libs/api';
import { categoryAPI } from '../../libs/api/category-api';
import { customerAPI } from '../../libs/api/customer-api';


const isPointModalVisible = ref(false); // Pointモーダルの表示状態
const name_ja = ref('');
const name_kana = ref('');
const nickname = ref('');
const introducer = ref('');
const introducerOptions = ref([
	{ label: "紹介者1", value: "1" },
	{ label: "紹介者2", value: "2" },
	{ label: "紹介者3", value: "3" }
]);
const person_in_charge = ref('');
const personInChargeOptions = ref([
	{ label: "担当1", value: "1" },
	{ label: "担当2", value: "2" },
	{ label: "担当3", value: "3" }
]);
const statusOptions = ref([
	{ label: "正会員", value: "201" },
	{ label: "凍結", value: "202" },
	{ label: "退会済み", value: "203" }
]);
statusOptions
 const registered_date = ref('');

const category = ref('');
const categoryOptions = ref([]);


const tag_of_preference = ref([]);
const customers = ref([]);
const birthday = ref('');
const featureTags = ref([]);
const preferences = ref([]);

const edit_name_ja = ref('');
const edit_name_kana = ref('');
const edit_nickname = ref('');
const edit_birthday = ref('');
const edit_introducer_id = ref('');
const edit_person_in_charge_id = ref('');
const edit_category_id = ref('');
const edit_status_id = ref('');
const edit_registered_date = ref('');
const edit_email = ref('');
const edit_phone_number = ref('');

const edit_customerId = ref();

const originalData = ref({});
const currentPage = ref(1);
const totalPages = ref(0);
const isEditModalVisible = ref(false);


// 特徴タグの仮APIデータ
const allFeatureTags = [
	'童顔',
	'ギャル',
	'清楚',
	'モデル体型',
	'小柄',
	'長身',
	'スレンダー',
	'ナチュラル',
	'癒し系',
	'クール',
];

// 特徴タグの仮APIデータ
const allPreferences = [
	'童顔',
	'ギャル',
	'清楚',
	'モデル体型',
	'小柄',
	'長身',
	'スレンダー',
	'ナチュラル',
	'癒し系',
	'クール',
];


// const editCustomer = (id) => {
	// console.log(`Editing cast with id: ${id}`);
	// 編集ロジックをここに実装
// };

const editCustomer = async (id) => {
  console.log(`Editing cast with id: ${id}`);

  try {
    const response = await customerAPI.getCustomerById(id);
    const data = response.data;
    console.log(data);
    const fetchCustomerCategoryName = async (categoryId) => {
      const categoryResponse = await categoryAPI.getCustomerCategoryName(categoryId);
      category.value = categoryResponse.data.category_name;
      console.log(category.value);
    };

    // Populate form fields with fetched data
	edit_customerId.value = data.id;
	await fetchCustomerCategoryName(data.category_id);
	edit_name_ja.value = data.name_ja;
	edit_name_kana.value = data.name_kana;
	edit_nickname.value = data.nickname;
	edit_birthday.value = data.birthday ? data.birthday.split(" ")[0] : "";
	edit_introducer_id.value = data.introducer_id;
	edit_person_in_charge_id.value = data.person_in_charge_id;
	edit_category_id.value = data.category_id;
	edit_status_id.value = data.status_id;
	edit_registered_date.value = data.registered_date ? data.registered_date.split(" ")[0] : "";
	edit_email.value = data.email;
	edit_phone_number.value = data.phone_number;
	isEditModalVisible.value = true;

  } catch (error) {
    console.error('Error fetching cast data:', error);
    Swal.fire('Error!', 'Failed to load cast details for editing.', 'error');
  }
  // 編集ロジックをここに実装
};


const selectedCustomer = ref({});
const showPointModal = (customer) => {
	selectedCustomer.value = customer; // 選択された女性会員の情報をセット
  isPointModalVisible.value = true; // モーダルを表示
};

const savePoint = async (data,pointValue) => {
  // Define a mapping of form fields to original data keys
  const sendData = {
    user_id: data.users_table_id, // bigint(20) unsigned
    point_amount: pointValue, // int(11)
  };

  try {
    await pointAPI.createWithHoldedPoint(sendData);
    isPointModalVisible.value = false;
    Swal.fire('Success!', 'ポイント処理が成功しました', 'success');

  } catch (error) {
    console.error('Error updating cast data:', error);
    Swal.fire('Error!', 'ポイント処理が失敗しました', 'error');
  }
};

const deleteCustomer = async (id) => {
  try {
    await customerAPI.deleteCustomer(id);
    console.log(`Customer with ID ${id} deleted successfully.`);
    // Optionally, refresh the customer list after deletion
    await fetchCustomers();
  } catch (error) {
    console.error('Error deleting customer:', error);
  }
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('ja-JP', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit'
  }).format(date);
};
// Reset form fields
const resetFields = () => {
  name_ja.value = '';
  name_kana.value = '';
  nickname.value = '';	
  introducer.value = '';
  person_in_charge.value = '';
  registered_date.value = '';
  category.value = '';
  birthday.value = '';
  preferences.value = [];
};
const handleSubmit = () => {
  const params = {
    name_ja: name_ja.value,
    name_kana: name_kana.value,
    nickname: nickname.value,
    introducer_id: introducer.value,
    person_in_charge: person_in_charge.value,
    registered_date: registered_date.value,
    category: category.value,
    tag_of_preference: featureTags.value
    // Add other form fields here
  };
  console.log('handleSubmit', params);
  fetchCustomers(params);
};
const fetchCustomers = async (params) => {
  try {
	params.page = currentPage.value;
    const response = await customerAPI.getAll(params);
    customers.value = response.data.customers;
	totalPages.value = response.data.pages; // Set total pages
  } catch (error) {
    console.log('Error fetching customers:', error);
  }
};



const goToPage = (page) => {
	if (page >= 1 && page <= totalPages.value) {
		currentPage.value = page;
		fetchCustomers({});
	}
};
const fetchCategories = async () => {
  try {
    const response = await categoryAPI.getCustomerAll();
	console.log('response', response);
    categoryOptions.value = response.data;
	
  } catch (error) {
    console.log('Error fetching categories:', error);
  }
};


const saveCustomerChanges = async (id) => {
  console.log("customerId", id);
  const updatedFields = {};

  // Define a mapping of form fields to original data keys
  const fieldMapping = {
    edit_name_ja: 'name_ja',
    edit_name_kana: 'name_kana',
    edit_nickname: 'nickname',
    edit_birthday: 'birthday',
    edit_introducer_id: 'introducer_id',
    edit_person_in_charge_id: 'person_in_charge_id',
    edit_category_id: 'category_id',
    edit_status_id: 'status_id',
    edit_registered_date: 'registered_date',
    edit_email: 'email',
	edit_phone_number: 'phone_number'
  };

  Object.entries(fieldMapping).forEach(([fieldRef, originalKey]) => {
    const currentValue = eval(fieldRef).value; // Using eval to access ref values
    const originalValue = originalData.value[originalKey];
    if (currentValue !== originalValue) {
      updatedFields[originalKey] = currentValue;
    }
  });

  try {
    await customerAPI.customerUpdate(id, updatedFields);
    isEditModalVisible.value = false;
    Swal.fire('Success!', 'Customer details updated successfully!', 'success');

  } catch (error) {
    console.error('Error updating Customer data:', error);
    Swal.fire('Error!', 'Failed to update Customer details.', 'error');
  }
};

onMounted(() => {
  console.log('onMounted');
  fetchCustomers({});
  fetchCategories();
});

</script>

<template>
	<div>
		<div class="d-flex justify-content-between align-items-center content-header px-5 py-3">
			<h4 class="fs-3">男性会員検索</h4>
		</div>
		<!-- search menu section -->
		<div class="container mt-4">
			<div class="card card-dark">
				<div class="card-header">
					<h4 class="fs-3 px-3 mx-2 my-3">検索メニュー</h4>
				</div>
				<div class="card-body">
					<div class="register-area">
						<form @submit.prevent="handleSubmit">
							<div class="d-flex search-area">
								<div class="main-area">
									<div class="d-flex justify-content-between align-items-end search-input">
										<div>
											<label for="name_ja">名前</label>
											<input type="text" v-model="name_ja" id="name_ja" class="form-control"
												placeholder="名前を入力" />
										</div>
										<div>
											<label for="name_kana">ふりがな</label>
											<input type="text" v-model="name_kana" id="name_kana" class="form-control"
												placeholder="ふりがなを入力" />
										</div>
										<div>
											<label for="nickname">ニックネーム</label>
											<input type="text" v-model="nickname" id="nickname" class="form-control"
												placeholder="ニックネームを入力" />
										</div>
										<div>
											<label for="dateOfBirth">生年月日</label>
											<input type="date" v-model="birthday" id="dateOfBirth"
												class="form-control" />
										</div>
									</div>
									<div class="d-flex justify-content-between align-items-end search-input">																				
										<div class="select-wrapper">
											<label for="introducer">紹介元</label>
											<select v-model="introducer" id="introducer"
												class="form-select introducer-input">
												<option disabled value="">紹介者選択</option>
												<option v-for="option in introducerOptions" :key="option.value"
													:value="option.id">
													{{ option.label }}
												</option>
											</select>
										</div>
										<div class="select-wrapper">
											<label for="person_in_charge">担当</label>
											<select v-model="person_in_charge" id="person_in_charge"
												class="form-select person-in-charge-input">
												<option disabled value="">担当選択</option>
												<option v-for="option in personInChargeOptions" :key="option.value"
													:value="option.id">
													{{ option.label }}
												</option>
											</select>
										</div>
										<div>
											<label for="registered_date">登録日</label>
											<input type="date" v-model="registered_date" id="registered_date"
												class="form-control" />
										</div>
										<div>
											<label for="category">カテゴリ</label>
											<select v-model="category" name="" id="category"
												class="form-select register-input category-input">
												<option disabled value="">カテゴリ選択</option>
												<option v-for="option in categoryOptions" :key="option.category_name"
													:value="option.id">
													{{ option.category_name }}
												</option>
											</select>
										</div>
									</div>
								</div>
							</div>
							<SiteadminTagSelector v-model:selectedTags="preferences" :allTags="allPreferences"
								label="特徴タグ (AND検索)" />
							<div class="button-container">
								<button type="submit" class="reset-btn btn btn-secondary" @click="resetFields">
									条件リセット
								</button>
								<button type="submit" class="btn deet-btn">
									検索
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<!-- search results section -->
		<div class="container mt-4">
			<div class="card card-dark">
				<div class="card-header">
					<h4 class="fs-3 px-3 mx-2 my-3">検索結果</h4>
				</div>
				<div class="card-body table-card">
					<table class="table table-hover fs-5">
						<thead>
							<tr>
								<th scope="col" class="text-center">ID</th>
								<th scope="col" class="text-center">ステータス</th>
								<th scope="col" class="text-center" style="width:18em;">男性会員名/ニックネーム</th>
								<th scope="col" class="text-center">誕生日</th>
								<th scope="col" class="text-center">カテゴリ</th>
								<th scope="col" class="text-center">保有ポイント</th>
								<th scope="col" class="text-center button-row">編集</th>
								<th scope="col" class="text-center button-row">Point調整</th>
								<th scope="col" class="text-center button-row">削除</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="customer in customers" :key="customer.id">
								<td class="text-center">{{ customer.id }}</td>
								<td class="text-center">{{ getStatus(customer.status) }}</td>
								<td class="text-center">{{customer.name_ja}} {{customer.nickname}}</td>
								<td class="text-center fw-normal">{{ formatDate(customer.birthday) }}</td>
								<td class="text-center">{{ customer.category_id }}</td>
								<td class="text-center fw-normal">{{ formatNumber(customer.points_holded) }}</td>     
								<td class="text-center">
									<button class="btn btn-deet-transparent fs-5" @click="editCustomer(customer.id)">
										編集
									</button>
								</td>
								<td class="text-center">
									<button class="btn btn-deet-transparent fs-5" @click="showPointModal(customer)">
										Point調整
									</button>
								</td>
								<td class="text-center">
									<button class="table-btn btn btn-danger fs-5" @click="deleteCustomer(customer.id)">
										削除
									</button>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<!-- ページネーションのサンプル置いただけ、後から実装する -->
				<nav aria-label="Page navigation example">
					<ul class="pagination justify-content-center pagination-sm custom-pagination">
						<li class="page-item" :class="{ disabled: currentPage === 1 }">
							<a class="page-link" href="#" @click="goToPage(currentPage - 1)">
								<span aria-hidden="true">&laquo;</span>
							</a>
						</li>
						<li class="page-item" v-for="page in Array.from({ length: totalPages }, (_, i) => i + 1)"
							:key="page" :class="{ active: currentPage === page }">
							<a class="page-link" href="#" @click="goToPage(page)">{{ page }}</a>
						</li>
						<li class="page-item" :class="{ disabled: currentPage === totalPages }">
							<a class="page-link" href="#" @click="goToPage(currentPage + 1)">
								<span aria-hidden="true">&raquo;</span>
							</a>
						</li>
					</ul>
				</nav>
			</div>
		</div>
	</div>

<CModal title="情報" 
	size="xl"    
	:visible="isEditModalVisible"
    v-on:close="() => { isEditModalVisible = false; }"
>
  <CModalHeader>
    <h5 class="modal-title fs-4">男性会員情報編集</h5>
  </CModalHeader>
  <CModalBody>
	<form @submit.prevent="submitForm">
						<div class="row mb-3">
							<!-- Name (Japanese) -->
							<div class="col-md-3">
								<label for="name_ja">名前 (日本語)</label>
								<input type="text" v-model="edit_name_ja" id="name_ja" required
									placeholder="名前を入力してください" class="form-control" />
							</div>

							<!-- Name (Kana) -->
							<div class="col-md-3">
								<label for="name_kana">名前 (カナ)</label>
								<input type="text" v-model="edit_name_kana" id="name_kana" required
									placeholder="カナを入力してください" class="form-control" />
							</div>

							<!-- Name (Kana) -->
							<div class="col-md-3">
								<label for="nickname">ニックネーム</label>
								<input type="text" v-model="edit_nickname" id="nickname" required
									placeholder="ニックネームを入力してください" class="form-control" />
							</div>

						</div>
						<div class="row mb-3">
							<!-- Birthday -->
							<div class="col-md-3">
								<label for="birthday">生年月日</label>
								<input type="date" v-model="edit_birthday" id="birthday" required
									class="form-control" />
							</div>

							<!-- Introducer ID (Number) -->
							<!-- <div class="col-md-3">
								<label for="introducer_id">紹介者ID</label>
								<input type="number" v-model="edit_introducer_id" id="introducer_id" required
									placeholder="紹介者IDを入力してください" class="form-control" />
							</div> -->
							<div class="col-md-3">
								<label for="introducer_id">紹介者ID</label>
								<select v-model="edit_introducer_id" name="" id="introducer_id"
									class="form-select footwork-input fs-4">
									<option v-for="option in introducerOptions" :key="option.label" :value="option.value" required>
										{{ option.label }}
									</option>
								</select>
								<!-- {{ personInChargeOptions }} -->
							</div>


							<!-- Person in Charge ID (Number) -->
							<div class="col-md-3">
								<label for="person_in_charge_id">担当者ID</label>
								<select v-model="edit_person_in_charge_id" name="" id="person_in_charge_id"
									class="form-select footwork-input fs-4">
									<option v-for="option in personInChargeOptions" :key="option.label" :value="option.value" required>
										{{ option.label }}
									</option>
								</select>
								<!-- {{ personInChargeOptions }} -->
							</div>

						</div>
						<div class="row mb-3">
							<!-- Category ID (Number) -->

							<div class="col-md-3">
								<label for="category_id">カテゴリ</label>
								<select v-model="edit_category_id" name="" id="category_id"
									class="form-select footwork-input fs-4">
									<option v-for="option in categoryOptions" :key="option.category_name" :value="option.id" required>
										{{ option.category_name }}
									</option>
								</select>
							</div>
							<!-- Status ID (Number) -->							
							<div class="col-md-3">
								<label for="status_id">ステータス</label>
								<select v-model="edit_status_id" name="" id="status_id"
									class="form-select footwork-input fs-4">
									<option v-for="option in statusOptions" :key="option.label" :value="option.value" required>
										{{ option.label }}
									</option>
								</select>

							</div>

							<!-- Registered Date -->
							<div class="col-md-3">
								<label for="registered_date">登録日</label>
								<input type="date" v-model="edit_registered_date" id="registered_date" required
									class="form-control" />
							</div>
						</div>
						<div class="row mb-3">
							<!--email-->
							<div class="col-md-3">
								<label for="email">email</label>
								<input type="text" v-model="edit_email" id="email" required placeholder="Default input"
									class="form-control" />
							</div>
							
							<div class="col-md-3">
								<label for="phone_number">電話番号</label>
								<input type="text" v-model="edit_phone_number" id="phone_number" required placeholder="Default input"
									class="form-control" />
							</div>
						</div>

					</form>

    <div class="text-end">
	  <button @click="saveCustomerChanges(edit_customerId)" type="submit" class="btn deet-btn btn-sm">保存</button>
    </div>
  </CModalBody>
</CModal>



<CModal title="情報" 
    :visible="isPointModalVisible"
    v-on:close="() => { isPointModalVisible = false; }"
>
  <CModalHeader>
    <h5 class="modal-title fs-4">Point調整</h5>
  </CModalHeader>
  <CModalBody>
    <p class="fs-5"><strong>ID:</strong> {{ selectedCustomer.id }}</p>
    <p class="fs-5"><strong>名前:</strong> {{ selectedCustomer.name_ja }}</p>
    <p class="fs-5"><strong>ニックネーム:</strong> {{ selectedCustomer.nickname }}</p>
    
    <label>ポイント増減</label>
    <input class="edit-point-input form-control" type="number" v-model="pointValue" step="1000">

    <div class="text-end">
      <button class="btn deet-btn btn-sm" @click="savePoint(selectedCustomer, pointValue)">確定</button>
    </div>
  </CModalBody>
</CModal>


</template>

<style scoped>
.pagination {
	a {
		font-size: 1.5rem !important;
		padding: 3px 10px;
	}
}
.btn-deet-transparent {
  border-color: transparent; 
  color: #FFF;
}
.btn-danger {
  border-color: transparent;  
  background-color: var(--deet-color-status-out-of-front);
  color: #FFF;
  padding: 0.3rem 1.2rem
}
.deet-btn {
  font-weight: normal !important;
  .btn-sm {
    padding: 5px 40px;
  }
}
.reset-btn {
    font-size: 16px;
    line-height: 24px;
    font-weight: normal !important;
    /* color: rgb(29, 26, 22) !important; */
    /* background: rgb(193 160 108); */
    padding: 5px 40px;
    margin-top: 15px;
    max-width: 328px;
    margin-left: 1em;
    white-space: nowrap;
    color: #FFF !important;
	margin-right: 12px;
}
/* Header styling */
.content-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 1rem;
  border-bottom: 1px solid #ddd;
}
.content-header h4 {
  /* font: normal normal normal 16px/20px Meiryo UI; */
  margin: 0;
  color: #3c4b64;
}

label,
.form-select  {
  font-size: 1.25rem;
}
.form-control,
.form-select {
  height: 43px;  
}

.card-dark .card-header {
	background-color: #2f353a;
	color: white;
}
.container {
	max-width: 1570px;
	padding: 0 60px;
}

.search-input {
	margin-bottom: 20px;
}

.search-input > div {
	margin-right: 20px;
}

.search-input > div:last-child {
	margin-right: 0;
}

.introducer-input {
	min-width: 130px;
}

.person-in-charge-input {
	min-width: 114px;
}

.rank-input {
	min-width: 114px;
}

.footwork-input {
	min-width: 82px;
}

.alcohol-input {
	min-width: 82px;
}

.category-input {
	min-width: 128px;
}

.button-container {
	display: flex;
	justify-content: flex-end;
	margin-top: 20px;
	padding-right: 20px;
}

.reset-btn {
	/* font: normal normal normal 12px/16px Meiryo UI; */
	color: #3c4b64;
	/* margin-right: 24px; */
}

.search-btn {
	/* font: normal normal normal 12px/16px Meiryo UI; */
	/* color: #ffffff; */
}

.search-btn:hover {
	color: #ffffff;
}

.status-area {
	margin-left: 40px;
}

.other-features-input {
	max-width: 1070px;
}

/* 夜職フラグ微調整 */
.night-job-flag-container {
	display: flex;
	align-items: center;
	height: 100%;
	margin-right: 20px;
}

.night-job-flag {
	height: 100%;
	min-height: 60px;
	width: 100px;
}

.night-job-flag .form-check-input {
	margin-left: 0;
}

.form-switch {
	padding-left: 0;
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
}

.form-check {
	padding-left: 0;
	margin-bottom: 0;
}

/* 以下テーブル幅微調整 */
.table {
	table-layout: fixed;
	width: 100%;
}



.table td {
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
}

/* 以下置いただけページネーションの微調整 */
.custom-pagination .page-link {
	color: #b2b2b2;
	background-color: #fff;
	border-color: #b2b2b2;
	margin-top: 40px;
}

.custom-pagination .page-item.active .page-link {
	color: #fff;
	background-color: #b2b2b2;
	border-color: #b2b2b2;
}

.custom-pagination .page-link:focus,
.custom-pagination .page-link:hover {
	color: #fff;
	background-color: #b2b2b2;
	border-color: #b2b2b2;
}

@media (max-width: 768px) {
	.container {
		padding: 20px;
	}

	.search-area {
		flex-direction: column;
	}

	.main-area,
	.status-area {
		width: 100%;
		max-width: 100%;
		margin-left: 0;
	}

	.search-input {
		flex-direction: column;
	}

	.search-input > div {
		width: 100%;
		margin-right: 0;
		margin-bottom: 15px;
	}

	.select-wrapper,
	.form-control,
	.form-select {
		width: 100%;
	}

	.button-container {
		flex-direction: column;
	}

	.button-container button {
		margin-bottom: 20px;
	}

	.reset-btn {
		margin-right: 0;
	}

	.table {
		table-layout: auto;
	}

	.table th,
	.table td {
		width: auto;
	}

	.table-responsive {
		overflow-x: auto;
	}

	.table th,
	.table td {
		white-space: nowrap;
	}
	

}
</style>
