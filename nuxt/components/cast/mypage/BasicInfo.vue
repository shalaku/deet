<script setup lang="ts">
import { formatDateTimeToDate } from '@/libs/utilities';
import { CModal, CModalBody, CModalHeader } from '@coreui/vue';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import Swal from 'sweetalert2';
import { computed, ref } from 'vue';
import { castAPI } from '~/libs/api';
import { VueQuery } from '~/libs/enums';

const { data } = defineProps<{
	data: API.Cast;
}>();

const getFormattedDOB = (date: Date) => {
	return new Date(date).toLocaleDateString('ja-JP', {
		year: 'numeric',
		month: 'long',
		day: 'numeric',
	});
};

const isModalVisible = ref(false);
const basicInfoForm = ref({
	birthday: data.birthday,
	height: data.height,
	hair_color: data.hair_color,
	hair_style: data.hair_style,
	day_work: data.day_work,
	final_academic_background: data.final_academic_background,
	alcohol: data.alcohol,
	smoke: data.smoke,
	post_code: data.post_code,
	prefecture: data.prefecture,
	city: data.city,
	municipalitie: data.municipalitie,
	street: data.street,
	building: data.building,
	siblings: data.siblings,
	cohabitation: data.cohabitation,
	three_size_b: data.three_size_b,
	three_size_w: data.three_size_w,
	three_size_h: data.three_size_h,
});

const formattedBirthday = computed({
	get() {
		// console.log(basicInfoForm.value.birthday);
		return formatDateTimeToDate(basicInfoForm.value.birthday);
		// return new Date(basicInfoForm.value.birthday).toISOString().split('T')[0];
	},
	set(value: string) {
		// console.log(value);
		basicInfoForm.value.birthday = value;
	},
});

const alcoholJpTxt = ref({
	good: "強い",
	average: "普通",
	bad: "弱い",
	not: "NG",
});
// Get values dynamically using computed()
const alcoholText = computed(() => {
  return alcoholJpTxt.value[data.alcohol as keyof typeof alcoholJpTxt.value] || '-';
});

const smokeJpTxt = ref({
	e_smoke: "吸う（電子）",
	smoke: "吸う（紙）",
	not: "吸わない",
});
// Get values dynamically using computed()
const smokeText = computed(() => {
  return smokeJpTxt.value[data.smoke as keyof typeof smokeJpTxt.value] || '-';
});

const sibandcohaJpTxt = ref({
	0: "いない",
	1: "いる",
});
// Get values dynamically using computed()
const siblingsText = computed(() => {
  return sibandcohaJpTxt.value[data.siblings as keyof typeof sibandcohaJpTxt.value] || '-';
});
// Get values dynamically using computed()
const cohabitationText = computed(() => {
  return sibandcohaJpTxt.value[data.cohabitation as keyof typeof sibandcohaJpTxt.value] || '-';
});


// Access QueryClient instance
const queryClient = useQueryClient();

// Update status mutation
const { mutate: handleFormSubmit } = useMutation({
	mutationFn: () => {
		return castAPI.updateProfile(basicInfoForm.value);
	},
	onSuccess: () => {
		queryClient.invalidateQueries({
			queryKey: [VueQuery.GET_CAST_PROFILE],
		});
		isModalVisible.value = false;
	},
	onError: (error) => {
		Swal.fire({
			icon: 'error',
			title: 'Failed to update profile',
			text: error?.message,
		});
	},
});

const fetchAddress = async () => {
	// console.log(basicInfoForm);
	if (basicInfoForm.value.post_code.length === 7) {
		try {
			const address = await castAPI.fetchAddressByPostalCode(basicInfoForm.value.post_code);
			if (address) {
				basicInfoForm.value.prefecture = address.prefecture;
				basicInfoForm.value.municipalitie = address.city;
				basicInfoForm.value.street = address.townName;
			} else {
				console.warn('住所が見つかりませんでした');
			}
		} catch (error) {
			console.error('住所情報の取得に失敗しました', error);
		}
	}
};

</script>

<template lang="html">
	<hr />
	<div class="content-body">
		<div class="d-flex justify-content-between mb-3">
			<h3 class="me-3 fs-3">基本情報</h3>
			<button
				class="btn btn-outline-secondary btn-deet-trans-dark basic-info-edit-btn btn-popup fs-4 mb-2"
				@click="
					() => {
						isModalVisible = true;
					}
				"
			>
				編集
			</button>
		</div>
		<div
			class="row justify-content-start align-items-start fs-4 center-align-on-mobile"
		>
			<div class="col-6 col-sm-6 col-lg-3 basic-info mb-0">
				<p class="mb-3">
					<span class="fw-bold">生年月日</span><br>
					<span class="fs-3">{{ getFormattedDOB(new Date(data.birthday)) }}</span>
				</p>
				<p class="mb-3">
					<span class="fw-bold">身長</span><br> 
					<span class="fs-3">{{ data.height || '-' }}cm</span></p>
				<p class="mb-3">
					<span class="fw-bold">居住地</span><br> 
					<span class="fs-3">{{ data.prefecture || '-' }}</span></p>
			</div>
			<div class="col-6 col-sm-6 col-lg-3 basic-info mb-0">
				<p class="mb-3">
					<span class="fw-bold">お仕事</span><br> 
					<span class="fs-3">{{ data.day_work || '-' }}</span></p>
				<p class="mb-3">
					<span class="fw-bold">髪の色</span><br> 
					<span class="fs-3">{{ data.hair_color || '-' }}</span></p>
				<p class="mb-3">
					<span class="fw-bold">髪型</span><br> 
					<span class="fs-3">{{ data.hair_style || '-' }}</span></p>
			</div>
			<div class="col-6 col-sm-6 col-lg-3 basic-info">
				<p class="mb-3">
					<span class="fw-bold">お酒</span><br> 
					<span class="fs-3">{{ alcoholText }}</span></p>
				<p class="mb-3">
					<span class="fw-bold">学歴</span><br> 
					<span class="fs-3">{{ data.final_academic_background || '-' }}</span></p>
				<p class="mb-3">
					<span class="fw-bold">タバコ</span><br> 
					<span class="fs-3">{{ smokeText }}</span></p>
			</div>
			<div class="col-6 col-sm-6 col-lg-3 basic-info">
				<p class="mb-3">
					<span class="fw-bold">兄弟姉妹</span><br> 
					<span class="fs-3">{{ siblingsText }}</span></p>
				<p class="mb-3">
					<span class="fw-bold">同居人</span><br>
					<span class="fs-3">{{ cohabitationText }}</span></p>
			</div>
		</div>
	</div>

	<CModal
		size="xl"
		backdrop="static"
		alignment="center"
		:visible="isModalVisible"
		aria-labelledby="BasicInfoUpdateLabel"
		content-class-name="rounded-0"
		v-on:close="
			() => {
				isModalVisible = false;
			}
		"
	>
		<CModalHeader class="border-0 pb-0" />
		<CModalBody>
			<form class="basic-info-form" @submit.prevent="() => handleFormSubmit()">
				<div class="d-flex flex-column flex-lg-row align-items-center gap-2 mb-3 fs-4">
					<div class="w-100 mb-3">
						<label for="birthday">生年月日</label>
						<input
							id="birthday"
							class="form-control"
							type="date"
							v-model="formattedBirthday"
						/>
					</div>
					<div class="w-100 mb-3">
						<label for="height">身長</label>
						<input
							id="height"
							class="form-control"
							type="text"
							v-model="basicInfoForm.height"
							placeholder="身長を入力"
						/>
					</div>
					<div class="w-100 mb-3">
						<label for="hair_color">髪の色</label>
						<input
							id="hair_color"
							class="form-control"
							type="text"
							v-model="basicInfoForm.hair_color"
							placeholder="髪色を入力"
						/>
					</div>
					<div class="w-100 mb-3">
						<label for="hair_type">髪型</label>
						<input
							id="hair_type"
							class="form-control"
							type="text"
							v-model="basicInfoForm.hair_style"
							placeholder="髪型を入力"
						/>
					</div>
					<div class="w-100 mb-3">
						<label for="day_work">昼職</label>
						<input
							id="day_work"
							class="form-control"
							type="text"
							v-model="basicInfoForm.day_work"
							placeholder="職業を入力"
						/>
					</div>
					<div class="w-100 mb-3">
						<label for="education">最終学歴</label>
						<input
							id="education"
							class="form-control"
							type="text"
							v-model="basicInfoForm.final_academic_background"
							placeholder="最終学歴を入力"
						/>
					</div>
					<div class="w-100 mb-3">
						<label for="alcohol">酒</label>
						<select
							id="alcohol"
							class="form-control"
							v-model="basicInfoForm.alcohol"
						>
							<option value="good">強い</option>
							<option value="average">普通</option>
							<option value="bad">弱い</option>
							<option value="not">NG</option>
						</select>
					</div>
					<div class="w-100 mb-3">
						<label for="smoke">タバコ</label>
						<select
							id="smoke"
							class="form-control"
							v-model="basicInfoForm.smoke"
						>
							<option value="e_smoke">吸う（電子）</option>
							<option value="smoke">吸う（紙）</option>
							<option value="not">吸わない</option>
						</select>
					</div>
				</div>

				<div class="d-flex align-items-between gap-2 mb-3 fs-4">
					<div style="width: 32%">
						<label for="post_code">郵便番号</label
						><input
							id="post_code"
							class="form-control"
							type="text"
							v-model="basicInfoForm.post_code"
							@blur="fetchAddress"
						/>
					</div>
					<div style="width: 32%">
						<label for="prefecture">都道府県</label>
						<select
							id="prefecture"
							class="form-control"
							v-model="basicInfoForm.prefecture"
						>
							<option value="北海道">北海道</option>
							<option value="青森県">青森県</option>
							<option value="岩手県">岩手県</option>
							<option value="宮城県">宮城県</option>
							<option value="秋田県">秋田県</option>
							<option value="山形県">山形県</option>
							<option value="福島県">福島県</option>
							<option value="茨城県">茨城県</option>
							<option value="栃木県">栃木県</option>
							<option value="群馬県">群馬県</option>
							<option value="埼玉県">埼玉県</option>
							<option value="千葉県">千葉県</option>
							<option value="東京都">東京都</option>
							<option value="神奈川県">神奈川県</option>
							<option value="新潟県">新潟県</option>
							<option value="富山県">富山県</option>
							<option value="石川県">石川県</option>
							<option value="福井県">福井県</option>
							<option value="山梨県">山梨県</option>
							<option value="長野県">長野県</option>
							<option value="岐阜県">岐阜県</option>
							<option value="静岡県">静岡県</option>
							<option value="愛知県">愛知県</option>
							<option value="三重県">三重県</option>
							<option value="滋賀県">滋賀県</option>
							<option value="京都府">京都府</option>
							<option value="大阪府">大阪府</option>
							<option value="兵庫県">兵庫県</option>
							<option value="奈良県">奈良県</option>
							<option value="和歌山県">和歌山県</option>
							<option value="鳥取県">鳥取県</option>
							<option value="島根県">島根県</option>
							<option value="岡山県">岡山県</option>
							<option value="広島県">広島県</option>
							<option value="山口県">山口県</option>
							<option value="徳島県">徳島県</option>
							<option value="香川県">香川県</option>
							<option value="愛媛県">愛媛県</option>
							<option value="高知県">高知県</option>
							<option value="福岡県">福岡県</option>
							<option value="佐賀県">佐賀県</option>
							<option value="長崎県">長崎県</option>
							<option value="熊本県">熊本県</option>
							<option value="大分県">大分県</option>
							<option value="宮崎県">宮崎県</option>
							<option value="鹿児島県">鹿児島県</option>
							<option value="沖縄県">沖縄県</option>
						</select>
					</div>
					<div style="width: 32%">
						<label for="municipalitie">市区町村</label
						><input
							type="text"
							id="municipalitie"
							class="form-control"
							placeholder="Default input"
							v-model="basicInfoForm.municipalitie"
						/>
					</div>
				</div>

				<div class="d-flex align-items-between gap-2 mb-3 fs-4">
					<div style="width: 32%">
						<label for="street">町名など</label>
						<input
							type="text"
							id="street"
							class="form-control"
							placeholder="Default input"
							v-model="basicInfoForm.street"
						/>
					</div>
					<div style="width: 66%">
						<label for="building">番地以降</label>
						<input
							type="text"
							id="street"
							class="form-control"
							placeholder="Default input"
							v-model="basicInfoForm.building"
						/>
					</div>
				</div>

				<div class="d-flex align-items-between gap-2 mb-3 fs-4">

					<div style="width: 49%">
						<label for="siblings">兄弟姉妹</label>
						<select
							id="siblings"
							class="form-control"
							v-model="basicInfoForm.siblings"
						>
							<option value="0">いない</option>
							<option value="1">いる</option>
						</select>
					</div>

					<div style="width: 49%">
						<label for="cohabitation">同居人</label>
						<select
							id="cohabitation"
							class="form-control"
							v-model="basicInfoForm.cohabitation"
						>
							<option value="0">いない</option>
							<option value="1">いる</option>
						</select>
					</div>					
				</div>

				<div class="d-flex align-items-center gap-2 mb-3 fs-4">
					<div style="width: 100%">
						<label for="bust">スリーサイズ</label>
						<!-- <div class="d-flex flex-column flex-lg-rowalign-items-center"> -->
						<div class="row text-center">
							<div class="col-4 pe-0">
							<input
								type="number"
								placeholder="B"
								id="bust"
								class="form-control"
								v-model="basicInfoForm.three_size_b"
							/>
							</div>
							<!-- <span class="mx-2"> - </span> -->
							<div class="col-4 pe-0">
							<input
								type="number"
								placeholder="W"
								class="form-control"
								v-model="basicInfoForm.three_size_w"
							/>
							</div>
							<!-- <span class="mx-2"> - </span> -->
							<div class="col-4">
							<input
								type="number"
								placeholder="H"
								class="form-control"
								v-model="basicInfoForm.three_size_h"
							/>
							</div>
						</div>
					</div>
				</div>

				<div class="d-flex justify-content-end">
					<button type="submit" class="btn deet-btn">保存</button>
				</div>
			</form>
		</CModalBody>
	</CModal>
</template>

<style scoped lang="css">
.basic-info-form {
	/* font-size: 14px; */
	padding: auto;
	/* color: #3c4b64; */
}
/* 
.basic-info-form input,
.basic-info-form select {
	width: 100%;
	font-size: 14px;
}
.basic-info-form label {
	color: #000;
} */
</style>
