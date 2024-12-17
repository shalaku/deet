<script setup>
import { formatNumber } from '@/libs/utilities';
import { useQuery } from '@tanstack/vue-query';
import Swal from 'sweetalert2';
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import NotificationBell from '~/components/misc/NotificationBell.vue';
import { castAPI } from '~/libs/api';
import { VueQuery } from '~/libs/enums';
import { customerAPI } from '../../libs/api/customer-api';
// const router = useRouter();
const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
// ダミーデータ
const dummyImageSrc =
	'data:image/gif;base64,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';

// 基本情報のダミーデータ
const basicInfo = ref({
	birthday: '1984年3月6日',
	height: '156',
	address: '東京都江東区',
	job: 'システムエンジニア',
	hairColor: '黒',
	hairstyle: 'ショート',
	alcohol: '飲む',
	education: '専門学校',
	smoking: '吸う（電子タバコ）',
	siblings: '長男',
	livingWith: '1人暮らし',
});
const prefectureOptions = ref([
	'北海道',
	'青森県',
	'岩手県',
	'宮城県',
	'秋田県',
	'山形県',
	'福島県',
	'茨城県',
	'栃木県',
	'群馬県',
	'埼玉県',
	'千葉県',
	'東京都',
	'神奈川県',
	'新潟県',
	'富山県',
	'石川県',
	'福井県',
	'山梨県',
	'長野県',
	'岐阜県',
	'静岡県',
	'愛知県',
	'三重県',
	'滋賀県',
	'京都府',
	'大阪府',
	'兵庫県',
	'奈良県',
	'和歌山県',
	'鳥取県',
	'島根県',
	'岡山県',
	'広島県',
	'山口県',
	'徳島県',
	'香川県',
	'愛媛県',
	'高知県',
	'福岡県',
	'佐賀県',
	'長崎県',
	'熊本県',
	'大分県',
	'宮崎県',
	'鹿児島県',
	'沖縄県',
]);

// 支払い情報編集モーダルの機能
const showPaymentModal = ref(false);
const showSubscribeModal=ref(false);
const customerData = ref(null);
const imageInput = ref(null);
const formattedBirthday = ref('');
const isEditModalVisible = ref(false);
const isEditModalVisiblePersonal = ref(false);
const EditformattedBirthday = ref('');
const postCode = ref('');
const municipalitie = ref('');
const street = ref('');
const prefecture = ref('');
const building = ref('');
const name = ref('');
const nickname = ref('');
const tagsList = ref([]);
const notice = ref('');
const newTag = ref('');
const avatar = ref('');

const { data: customerCardInfo, refetch } = useQuery({
	queryKey: [VueQuery.GET_CUSTOMER_CARD_INFO],
	queryFn: () => customerAPI.getCustomerCardInfo(authStore.user?.id || 0),
});
const isDefaultCardExist = computed(() => !!customerCardInfo.value?.data?.default_card?.id);
// モーダルを開く関数
const openPaymentModal = () => {
	showPaymentModal.value = true;
};

// モーダルを閉じる関数
const closePaymentModal = () => {
	showPaymentModal.value = false;
	refetch();

};

// 支払い情報を保存する関数
const savePaymentInfo = (newPaymentInfo) => {
	// console.log('親コンポーネント: 新しい支払い情報を受信', newPaymentInfo);
	// // ここに保存ロジックを追加する
	closePaymentModal();
};
const openSubscribeModal= ()=>{
	showSubscribeModal.value=true;
};

const closeSubscribeModal=()=>{
	refetch();
	showSubscribeModal.value = false;
}

const saveSubscribeInfo = (newPaymentInfo) => {
	closeSubscribeModal();
};

const navigateTocustomerpaymet = () => {
	router.push('/user/customer_payment');
};

const deleteCard = async (paymentMethodId) => {
	try {
		const result = await Swal.fire({
			title: '本気ですか?',
			text: '本当にこのカードを削除してもよろしいですか?この操作は元に戻すことができません。',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, delete it!',
		});

		if (result.isConfirmed) {
			const response = await customerAPI.deleteCustomerCard(
				paymentMethodId,
				authStore?.user?.id || 0
			);

			if (response.status === 'SUCCESS') {
				await Swal.fire(
					'Deleted!',
					'Your card has been successfully deleted.',
					'success'
				);
				refetch();
			} else {
				await Swal.fire(
					'Error!',
					'There was an issue deleting the card. Please try again.',
					'error'
				);
			}
		}
	} catch (error) {
		await Swal.fire(
			'Error!',
			'Something went wrong. Please try again later.',
			'error'
		);
	}
};

const fetchCustomerProfile = async () => {
	try {
		const response = await customerAPI.getCustomerProfile(); // Adjust this line based on your API method
		if (response.status === 'SUCCESS') {
			customerData.value = response.data;
			formattedBirthday.value = formatDate(customerData.value.birthday);
			const avatarData = customerData.value.images?.find(
				(image) => image.is_profile_picture === 1,
			);
			if (avatarData) {
				avatar.value = avatarData.image_url; // Store the image URL in the avatar variable
			} else {
				avatar.value = ''; // Set a default or fallback if no profile picture is found
			}

		}
	} catch (error) {
		console.error('Error fetching customer data:', error);
	}
};
const editCustomerpersonalinfo = (id) => {
	name.value = customerData.value.name_ja;
	nickname.value = customerData.value.nickname;
	notice.value = customerData.value.notices;
	tagsList.value = customerData.value.tag_of_preference;
	isEditModalVisiblePersonal.value = true;
};

const editCustomerbasicinfo = (id) => {
	EditformattedBirthday.value = customerData.value?.birthday
		? customerData.value.birthday.split(' ')[0]
		: 'N/A';
	postCode.value = customerData.value?.post_code;
	municipalitie.value = customerData.value?.municipalitie;
	street.value = customerData.value?.street;
	prefecture.value = customerData.value?.prefecture;
	building.value = customerData.value?.building;
	isEditModalVisible.value = true;
};

const saveCustomerBasicInfoChanges = async (id) => {
	try {
		const updatedData = {
			id: customerData.value.id,
			birthday: EditformattedBirthday.value,
			post_code: postCode.value,
			municipalitie: municipalitie.value,
			street: street.value,
			building: building.value,
			prefecture: prefecture.value,
		};

		const response = await customerAPI.customerUpdate(id, updatedData); // Update API method
		await fetchCustomerProfile();
		isEditModalVisible.value = false;
		Swal.fire('Success!', 'Cast details updated successfully!', 'success');
	} catch (error) {
		console.error('Error updating cast data:', error);
		Swal.fire('Error!', 'Failed to update cast details.', 'error');
	}
};

const saveCustomerPersonalInfoChanges = async (id) => {
	try {
		const updatedData = {
			id: customerData.value.id,
			name_ja: name.value,
			nickname: nickname.value,
			tag_of_preference: JSON.stringify(tagsList.value),
			notices: notice.value,
		};

		const response = await customerAPI.customerUpdate(id, updatedData); // Update API method
		const updateNicknameResponse = await customerAPI.updateUserNameChat(
			id,
			nickname.value,
		);
		await fetchCustomerProfile();
		isEditModalVisiblePersonal.value = false;
		Swal.fire('Success!', 'Cast details updated successfully!', 'success');
	} catch (error) {
		console.error('Error updating cast data:', error);
		Swal.fire('Error!', 'Failed to update cast details.', 'error');
	}
};

const openImageUpload = () => {
	imageInput.value.click();
};

const handleImageUpload = async (event) => {
	const file = event.target.files[0];
	if (!file) return;

	try {
		const customerId = customerData.value.id; // Get the customer ID from your data
		const response = await customerAPI.uploadImage(
			customerId,
			file,
			'customer',
		);
		if (response.status === 'SUCCESS') {
			Swal.fire({
				icon: 'success',
				title: 'プロフィール画像の変更が完了しました',
			});
			await fetchCustomerProfile();
			const updateImageResponse = await customerAPI.updateAvatarChat(
				customerId,
				avatar.value,
			);

		} else {
			console.error('Image upload failed:', response.message);
		}
	} catch (error) {
		console.error('Error uploading image:', error);
	}
};

// こちらからご確認くださいボタンをクリックしたとき
const navigateToHistoryOrder = () => {
	router.push('/user/order_history');
};

function formatDate(dateString) {
	const date = new Date(dateString);
	const options = { year: 'numeric', month: 'long', day: 'numeric' };
	return date.toLocaleDateString('ja-JP', options);
}

const fetchAddress = async () => {
	if (postCode.value.length === 7) {
		try {
			const address = await castAPI.fetchAddressByPostalCode(postCode.value);
			if (address) {
				prefecture.value = address.prefecture;
				municipalitie.value = address.city;
			} else {
				console.warn('住所が見つかりませんでした');
			}
		} catch (error) {
			console.error('住所情報の取得に失敗しました', error);
		}
	}
};

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

onMounted(async () => {
	await fetchCustomerProfile();
});
</script>

<template>
	<div class="container-fluid">
		<div class="content ps-3 pe-3 pb-4">
			<!-- profile -->
			<div class="content-body mt-3">
				<div style="position: relative;"
					class="d-flex flex-column flex-lg-row justify-content-between align-items-center center-align-on-mobile pt-3">
					<div class="profile-image-wrapper">
						<div class="profile-image-area">
							<img :src="customerData?.images[0]?.image_url || dummyImageSrc" alt="profile image"
								class="profile-image" />
						</div>
						<button type="button" class="profile-image-edit-button" @click="openImageUpload">
							<i class="icon-pencil profile-image-edit-icon"></i>
						</button>
						<input type="file" ref="imageInput" style="display: none" @change="handleImageUpload" />
					</div>

					<div class="notification mt-5">
						<NotificationBell />
					</div>

					<div class="container-fluid p-0 pt-4">
						<div class="row align-items-start justify-content-between mb-2 center-align-on-mobile gap-2">
							<div class="col-8 d-flex align-items-end">
								<div>
									<div>
										<div class="fs-3 fw-bold mb-2">姓名</div>
										<div class="fs-2">{{ customerData?.name_ja || 'N/A' }}</div>
									</div>
									<div>
										<div class="fs-3 fw-bold mb-2 mt-4">ニックネーム</div>
										<div class="fs-2">
											{{ customerData?.nickname || 'N/A' }}
										</div>
									</div>
								</div>
								<div class="d-flex fs-4 ms-3 pb-1">
									<span class="me-1">{{
										new Date().getFullYear() -
										new Date(customerData?.birthday).getFullYear()
										}}歳</span>
									<!-- <span>{{ customerData?.prefecture || 'N/A' }}</span> -->
								</div>
							</div>
							<div class="col-3 text-end">
								<button
									class="btn btn-outline-secondary btn-deet-trans-dark basic-info-edit-btn btn-popup fs-4 mb-2 ps-4 pe-4"
									@click="editCustomerpersonalinfo(customerData?.id)">
									編集
								</button>
							</div>

							<div class="mt-3 fs-3 fw-bold">特徴タグ</div>
							<div class="d-flex flex-wrap mb-3">
								<div v-for="tag in customerData?.tag_of_preference" :key="tag"
									class="tag me-1 mb-1 p-3 ps-4 pe-4">
									{{ tag }}
								</div>
							</div>
						</div>

						<div class="col-7">
							<div class="fs-3 fw-bold mb-3">自己紹介</div>
							<div class="fs-4 mb-3">
								{{ customerData?.notices || 'N/A' }}
							</div>
						</div>
					</div>
				</div>
			</div>

			<hr style="margin-bottom: 25px" />

			<!-- basic information あとで基本情報編集モーダルつくる -->
			<div class="content-body">
				<div class="d-flex justify-content-between mb-3">
					<h3 class="fs-3 fw-bold me-3">基本情報</h3>
					<button
						class="text-end btn btn-outline-secondary btn-deet-trans-dark basic-info-edit-btn btn-popup fs-4 mb-2 ps-4 pe-4"
						@click="editCustomerbasicinfo(customerData?.id)">
						編集
					</button>
				</div>
				<div class="row fs-4">
					<div class="col-6">
						<p class="mb-2">
							<span class="fw-bold">生年月日</span><br />
							<span class="fs-3">{{ formattedBirthday }}</span>
						</p>
						<p class="mb-2">
							<span class="fw-bold">郵便番号</span><br />
							<span class="fs-3">{{
								customerData?.post_code ? customerData.post_code : 'N/A'
								}}</span>
						</p>
						<p class="mb-2">
							<span class="fw-bold">居住地</span><br />
							<span class="fs-3">{{
								customerData?.prefecture ? customerData.prefecture : 'N/A'
								}}</span>
						</p>
					</div>
					<div class="col-6">
						<p class="mb-2">
							<span class="fw-bold">市区町村</span><br />
							<span class="fs-3">{{
								customerData?.municipalitie ? customerData.municipalitie : 'N/A'
								}}</span>
						</p>
						<p class="mb-2">
							<span class="fw-bold">通り</span><br />
							<span class="fs-3">{{
								customerData?.street ? customerData.street : 'N/A'
								}}</span>
						</p>
						<p class="mb-2">
							<span class="fw-bold">建物</span><br />
							<span class="fs-3">{{
								customerData?.building ? customerData.building : 'N/A'
								}}</span>
						</p>
					</div>
				</div>
			</div>

			<hr />

			<!-- payment -->
			<div class="content-body">
				<div class="row g-4">
					<div class="col-md-6 mt-0">
						<div class="d-flex justify-content-between align-items-start mb-4">
							<div class="d-flex flex-column align-items-start mb-4">
								<h3 class="fs-3 fw-bold mb-3">お支払い</h3>

							</div>
							<button
									class="btn btn-outline-secondary btn-deet-trans-dark basic-info-edit-btn btn-popup fs-4 ps-4 pe-4"
									@click="openPaymentModal">
									新しいカードを追加
							</button>
<!--							<button-->
<!--								class="text-end btn btn-outline-secondary btn-deet-trans-dark basic-info-edit-btn btn-popup fs-4 mb-2 ps-4 pe-4"-->
<!--								@click="openSubscribeModal">-->
<!--								サブスクリプション-->
<!--							</button>-->
						</div>

						<div v-for="card in customerCardInfo?.data?.payment_methods" :key="card.id" class="mb-4">
							<div class="d-flex align-items-center fs-4 mb-2">
								<div class="me-3">
									{{ card.card.brand.toUpperCase() }}
								</div>
								<div class="me-3">
									xxxx-xxxx-xxxx-{{ card.card.last4 }}
								</div>
								<div v-if="customerCardInfo?.data?.default_card?.id === card.id"
									class="me-3 text-primary">
									<span class="btn btn-success">デフォルト</span>
								</div>
								<button class="btn btn-danger" @click="deleteCard(card.id)">
									削除
								</button>
							</div>
							<div class="me-3 fs-4 mb-2">
								有効期限：{{ card.card.exp_year }}年{{ card.card.exp_month }}月
							</div>
						</div>
					</div>
				</div>
			</div>
			<hr />

			<div class="content-body">
				<div class="row g-4">
					<div class="col-md-6 mt-0">
						<div class="d-flex justify-content-between align-items-start mb-4">
							<div class="d-flex flex-column align-items-start mb-4">
								<div class="fs-3 fw-bold mb-3">VIPメンバー登録</div>				
							</div>
							<button @click="openSubscribeModal" class="text-end btn btn-deet-gold fs-4 mb-2 ps-4 pe-4">
								登録に進む
							</button>
						</div>
					</div>
				</div>
			</div>

			<hr />

			<div class="content-body">
				<div class="row g-4">
					<div class="col-md-6 mt-0">
						<div class="d-flex justify-content-between align-items-start mb-4">
							<div class="d-flex flex-column align-items-start mb-4">
								<div class="fs-3 fw-bold mb-3">保有ポイント</div>
								<div class="point-count">
									{{ formatNumber(customerData?.points_holded) }} <span> ポイント</span>
								</div>					
							</div>
							<button @click="navigateTocustomerpaymet()" class="text-end btn btn-deet-gold fs-4 mb-2 ps-4 pe-4">
								ポイント購入
							</button>
						</div>
					</div>
				</div>
			</div>

			<!-- <div class="point-row">
				<div class="point-title">保有ポイント</div>
				<div class="point-count">
					{{ customerData?.points_holded }} <span> ポイント</span>
				</div>
				<button @click="navigateTocustomerpaymet()" class="point-button">
					支払い
				</button>
			</div> -->
			<hr />
			<!-- <hr /> -->

			<!-- <div class="content-body">
				<div class="row g-4">
					<div class="col-md-6 mt-2 mb-5">
						<h3 class="fs-3 fw-bold mb-5">ご注文履歴</h3>
						<NuxtLink to="/user/order_history" class="btn btn-deet-transparent fs-4 pt-2 pb-2">
							こちらからご確認ください
						</NuxtLink>
					</div>
				</div>
			</div> -->

		</div>

		<!-- CustomerPaymentEditModal コンポーネントに v-model:show を使用して双方向バインディング -->
		<CustomerModalPaymentEditModal v-if="showPaymentModal" @close="closePaymentModal" @save="savePaymentInfo" />
		<CustomerModalSubscribeWithStripe v-if="showSubscribeModal" @close="closeSubscribeModal"
			@save="saveSubscribeInfo" :isExist="isDefaultCardExist"/>


		<div v-if="isEditModalVisible" class="modal-overlay">
			<!-- Modal Content -->
			<div class="modal-content">
				<form @submit.prevent="handleSubmit">
					<div class="container">
						<div class="card">
							<div class="card-header p-0 pt-3 pb-3">
								<i class="icon-x d-block text-end" @click="isEditModalVisible = false"></i>
							</div>

							<div class="card-body">
								<div class="register-area">
									<!-- Upper part from hr -->
									<div class="upper-area">
										<div class="main-area">
											<!-- 1st row -->
											<div
												class="d-flex justify-content-between align-items-start register-input fs-4">
												<div>
													<label for="dateOfBirth">生年月日</label>
													<input type="date" v-model="EditformattedBirthday" id="dateOfBirth"
														class="form-control" />
												</div>
												<div>
													<label for="postalCode">郵便番号</label>
													<input type="text" v-model="postCode" id="postalCode"
														class="form-control" placeholder="" @blur="fetchAddress" />
												</div>
												<div>
													<label for="prefecture">都道府県</label>
													<select v-model="prefecture" id="prefecture"
														class="form-select prefecture-input fs-4">
														<option disabled value="">選択</option>
														<option v-for="option in prefectureOptions" :key="option"
															:value="option">
															{{ option }}
														</option>
													</select>
												</div>
											</div>

											<!-- 2nd row -->
											<div
												class="d-flex justify-content-between align-items-start register-input second-row fs-4">
												<div>
													<label for="municipalitie">市区町村</label>
													<input type="text" v-model="municipalitie" id="city"
														class="form-control" placeholder="" />
												</div>
												<div>
													<label for="street">番地以降</label>
													<input type="text" v-model="street" id="street" class="form-control"
														placeholder="" />
												</div>
												<div>
													<label for="building">建物</label>
													<input type="text" v-model="building" id="building"
														class="form-control" placeholder="" />
												</div>
											</div>
										</div>
									</div>
									<div class="d-flex justify-content-end align-items-center">
										<button @click="saveCustomerBasicInfoChanges(customerData.id)" type="submit"
											class="btn deet-btn">
											保存
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div v-if="isEditModalVisiblePersonal" class="modal-overlay">
			<!-- Modal Content -->
			<div class="modal-content">
				<form @submit.prevent="handleSubmit">
					<div class="container">
						<div class="card">
							<div class="card-header p-0 pt-3 pb-3">
								<i class="icon-x d-block text-end" @click="isEditModalVisiblePersonal = false"></i>
							</div>

							<div class="card-body">
								<div class="register-area">
									<!-- Upper part from hr -->
									<div class="upper-area">
										<div class="main-area fs-4">
											<!-- 1st row -->
											<div
												class="d-flex justify-content-between align-items-start register-input">
												<div>
													<label for="dateOfBirth">姓名</label>
													<input type="text" v-model="name" id="name" class="form-control" />
												</div>
												<div>
													<label for="dateOfBirth">ニックネーム</label>
													<input type="text" v-model="nickname" id="nickname"
														class="form-control" />
												</div>
											</div>

											<!-- 2nd row -->
											<div
												class="d-flex justify-content-between align-items-start register-input second-row">
												<SiteadminTagSelector v-model:selectedTags="tagsList"
													:allTags="allFeatureTags" label="特徴" />
											</div>

											<!-- 3rd row -->
											<div
												class="d-flex justify-content-between align-items-start register-input second-row">
												<div>
													<label for="notice">自己紹介</label>
													<input type="text" v-model="notice" id="city" class="form-control"
														placeholder="" />
												</div>
											</div>
										</div>
									</div>
									<div class="d-flex justify-content-end align-items-end">
										<button @click="saveCustomerPersonalInfoChanges(customerData.id)" type="submit"
											class="btn deet-btn">
											保存
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</template>

<style scoped>
.notification {
	position: absolute;
    top: 1rem;
    right: 0px;
}
.content-body {
	/* padding: 20px 40px; */
}

.text-10px {
	font-size: 10px;
}

.text-12px {
	font-size: 12px;
}

/* profile image */
.profile-image-wrapper {
	position: relative;
	width: 150px;
	height: 150px;
}

.profile-image-area {
	width: 100%;
	height: 100%;
	border-radius: 50%;
	overflow: hidden;
}

.profile-image {
	width: 100%;
	height: 100%;
	object-fit: cover;
	object-position: center;
}

/* name ~ feature tag area */
.name-area {
	max-width: 250px;
}

.feature-tag {
	display: inline-block;
	background: #ffffff;
	border: #707070 1px solid;
	border-radius: 3px;
	font-size: 12px;
	font-weight: 700;
	color: #545454;
	padding: 6px 12px 4px 12px;
}

/* General modal overlay */
.modal-overlay {
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: rgba(0, 0, 0, 0.7);
	display: flex;
	align-items: center;
	justify-content: center;
	z-index: 1000;
	overflow-y: auto;
	/* Allows scrolling if modal content exceeds screen height */
	padding: 10px;
	/* Adds padding for smaller screens */
}

/* Modal content container */
.icon-x:before {
	color: var(--deet-color-font);
	font-size: 2rem;
	opacity: 1;
}

.modal-content {
	width: 100%;
	max-width: 800px;
	background: var(--deet-color-cotent-base);
	border-radius: 8px;
	overflow: hidden;
	box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3);
}

/* Header styling */
.content-header {
	padding: 11px 24px;
	background: #ffffff;
	display: flex;
	justify-content: space-between;
	align-items: center;
	border-bottom: 1px solid #ddd;
}

.content-header h4 {
	font: normal normal normal 16px/20px Meiryo UI;
	color: #4f5d73;
	margin: 0;
}

/* Close button */
.close-btn {
	background: transparent;
	border: none;
	font-size: 24px;
	color: #333;
	cursor: pointer;
	font-weight: bold;
}

.close-btn:hover {
	color: #ff0000;
}

/* Card styles */
.card {
	border: none;
	border-radius: 4px;
}

.card-header {
	background: var(--deet-color-cotent-base);
	padding: 14px 22px;
}

.card-body {
	background: var(--deet-color-cotent-base);
	padding: 20px;
	font-weight: 400;
	color: #fff;
	font-family: Meiryo UI;
}

.card-header h4 {
	font: normal normal bold 14px/18px Meiryo UI;
	color: #3c4b64;
	margin: 0;
}

/* Button styling */
.register-btn {
	background: #20a8d8;
	border: none;
	font: normal normal normal 12px/16px Meiryo UI;
	color: #ffffff;
	padding: 8px 16px;
	cursor: pointer;
	border-radius: 4px;
}

.register-btn:hover {
	background: #1a8ab3;
}

/* Input styling */
.register-input {
	margin-bottom: 16px;
	display: flex;
	flex-wrap: wrap;
	/* Allows items to wrap on smaller screens */
	gap: 16px;
}

.register-input>div {
	flex: 1;
	width: 100%;
	/* min-width: 200px; Minimum width for better responsiveness */
}

.register-input input {
	/* color: white; */
	/* background-color: #333; */
	/* border: 1px solid #fff; */
}

.register-input input::placeholder {
	color: rgba(255, 255, 255, 0.6);
}

.prefecture-input {
	min-width: 82px;
}

.status-area {
	margin-left: 60px;
	max-width: 163px;
	width: 100%;
}

hr {
	margin-bottom: 40px;
}

.point-row {
	display: flex;
	align-items: center;
	gap: 20px;
	/* Adds spacing between items */
}

/* .point-title { */
	/* font-size: 18px; */
	/* font-weight: bold; */
/* } */

.point-count {
	font-size: 16px;
}

.point-button {
	padding: 8px 16px;
	font-size: 14px;
	background-color: transparent;
	color: white;
	border: 2px solid white;
	border-radius: 4px;
	cursor: pointer;
}

.point-button:hover {
	background-color: #0056b3;
}

@media (max-width: 768px) {
	.profile-image-wrapper {
		margin: 0 auto;
	}

	.name-area {
		max-width: 100%;
	}

	.card-body {
		padding: 0 0 16px 0;
	}

	.register-input {
		flex-direction: column;
	}

	.content-header {
		padding: 10px 16px;
	}

	.content-header h4 {
		font-size: 18px;
	}

	.close-btn {
		font-size: 20px;
	}
}
</style>
