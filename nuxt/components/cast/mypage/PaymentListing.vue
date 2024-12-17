<script setup lang="ts">
import { getBankAccountTypeTxt } from '@/libs/utilities';
import { CModal, CModalBody, CModalHeader } from '@coreui/vue';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import Swal from 'sweetalert2';
import { castAPI } from '~/libs/api';
import { VueQuery } from '~/libs/enums';

const { data } = defineProps<{
	data: API.CastBankDetail;
}>();

const isModalVisible = ref(false);
const bankDetailsForm = ref({
	bank_name: data?.bank_name || '',
	branch: data?.branch || '',
	account_number: data?.account_number || '',
	account_type: data?.account_type || '',
});

// Access QueryClient instance
const queryClient = useQueryClient();

// Update status mutation
const { mutate: handleFormSubmit, isPending } = useMutation({
	mutationFn: () => {
		return castAPI.updateProfile({
			bank_details: { ...bankDetailsForm.value },
		});
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
			title: 'Failed to update bank details',
			text: error?.message,
		});
	},
});
</script>

<template lang="html">
	<hr />
	<div class="content-body">
		<div class="d-flex justify-content-between mb-3">
			<h3 class="me-3 fs-3">銀行情報</h3>
			<button class="btn btn-deet-trans-dark payment-edit-btn btn-popup fs-4 mb-2" @click="
					() => {
						isModalVisible = true;
					}
				">
				編集
			</button>
		</div>
		<div class="row justify-content-start align-items-center mb-3 keep-flex">
			
			<!-- <div class="row justify-content-start align-items-center mb-3"> -->
				<div class="col-6">
					<div class="me-3 mb-3">
						<div class="fs-4 fw-bold">銀行名</div>
						<div class="fs-3">{{ data?.bank_name || '-' }}</div>
					</div>
					<div class="me-3 mb-3">
						<div class="fs-4 fw-bold">口座種類</div>
						<div class="fs-3">{{ getBankAccountTypeTxt(data?.account_type) }}</div>
					</div>
				</div>
				<div class="col-6">
					<div class="me-3 mb-3">
						<div class="fs-4 fw-bold">支店名</div>
						<div class="fs-3">{{ data?.branch || '-' }}</div>
					</div>
					<div class="me-3 mb-3">
						<div class="fs-4 fw-bold">口座番号</div>
						<div class="fs-3">{{ data?.account_number || '-' }}</div>
					</div>
				</div>
			<!-- </div> -->
		</div>
	</div>

	<CModal size="md" backdrop="static" alignment="center" :visible="isModalVisible"
		aria-labelledby="BankDetailsUpdateLabel" content-class-name="rounded-0" v-on:close="
			() => {
				isModalVisible = false;
			}
		">
		<CModalHeader class="border-0 pb-0" />
		<CModalBody>
			<form class="bank-details-form" @submit.prevent="() => handleFormSubmit()">
				<div class="d-flex align-items-center gap-2 mb-3">
					<div style="width: 50%">
						<label for="bank_name">銀行名</label>
						<input id="bank_name" class="form-control" type="text" v-model="bankDetailsForm.bank_name"
							placeholder="銀行名を入力" />
					</div>
					<div style="width: 50%">
						<label for="branch">支店名</label>
						<input id="branch" class="form-control" type="text" v-model="bankDetailsForm.branch"
							placeholder="支店名を入力" />
					</div>
				</div>
				<div class="d-flex align-items-center gap-2 mb-3">
					<div style="width: 50%">
						<label for="account_type">口座種類</label>
						<select id="account_type" class="form-control" v-model="bankDetailsForm.account_type">
							<option value="normal">普通</option>
							<option value="special">当座</option>
						</select>
					</div>
					<div style="width: 50%">
						<label for="account_number">口座番号</label>
						<input id="account_number" class="form-control" type="text"
							v-model="bankDetailsForm.account_number" placeholder="口座番号を入力" />
					</div>
				</div>

				<div class="d-flex justify-content-end">
					<button type="submit" class="btn deet-btn" :disabled="isPending">
						保存
					</button>
				</div>
			</form>
		</CModalBody>
	</CModal>
</template>

<style scoped lang="css">
.bank-details-form {
	font-size: 14px;
	/* color: #3c4b64; */
}

.bank-details-form input,
.bank-details-form select {
	width: 100%;
	font-size: 14px;
}

</style>
