<template lang="html">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12 mt-4">
				<h1 class="fs-1 ps-3">ポイントを購入する</h1>
			</div>
			<div class="col-12">
				<div class="card p-3">
					<p class="fs-4 mb-0">保有ポイント</p>
					<div class="holded-point fw-bold ">
						{{ formatNumber(customerData.points_holded) }}P
					</div>
					<p class="text-end fs-5 mb-0">ポイントの有効期限は購入から90日です</p>
				</div>
			</div>
			<div class="col-12">
				<div class="list-container">
					<div class="list-item p-3" v-for="item in data?.data" :key="item.id">
						<div class="points">{{ item.points.toLocaleString() }} ポイント</div>
						<button class="price-button fs-3" @click="openStripePaymentModal(item.points)">
							¥{{ (item.points * 1.2).toLocaleString() || 'N/A' }}
						</button>
					</div>
				</div>

				<!-- Stripe payment modal -->
				<PaymentWithStripe v-if="showStripePaymentModal" :selectedPoints="purchasePoints" :returnUrl="returnUrl"
			@close="closeStripePaymentModal" />

			</div>
		</div>
	</div>
</template>

<script setup lang="ts">
import { formatNumber } from '@/libs/utilities';
import { useQuery } from '@tanstack/vue-query';
import { customerAPI } from '~/libs/api';
import { VueQuery } from '~/libs/enums';
import PaymentWithStripe from './modal/PaymentWithStripe.vue';

const customerData = ref([]);
const fetchCustomerProfile = async () => {
	try {
		const response = await customerAPI.getCustomerProfile(); // Adjust this line based on your API method
		if (response.status === 'SUCCESS') {
			customerData.value = response.data;
		}
	} catch (error) {
		console.error('Error fetching customer data:', error);
	}
};
await fetchCustomerProfile();

const { data } = useQuery({
	queryKey: [VueQuery.GET_CUSTOMER_ALL_POINTS],
	queryFn: () => customerAPI.pointList(),
});

const showStripePaymentModal = ref(false);
const purchasePoints = ref(0);

const openStripePaymentModal = (points: number) => {
	purchasePoints.value = points;
	showStripePaymentModal.value = true;
};

const closeStripePaymentModal = () => {
	showStripePaymentModal.value = false;
};
const returnUrl = computed(() => `${window.location.origin}/user/mypage`);
</script>

<style scoped lang="css">
.holded-point {
	font-size: 25px;
}

.card {
	background-color: var(--deet-color-cotent-base);
	color: var(--deet-color-font);
}

.list-container {
	display: flex;
	flex-direction: column;
	gap: 10px;
	margin-top: 20px;
}

.list-item {
	display: flex;
	justify-content: space-between;
	align-items: center;
	/* padding: 10px; */
	color: var(--deet-color-font);
	/* border: 1px solid #ddd; */
	border-radius: 5px;
	background-color: var(--deet-color-cotent-base);
}

.points {
	font-size: 16px;
	font-weight: bold;
	/* color: #333; */
}

.price-button {
	/* font-size: 14px; */
	color: white;
	background-color: var(--deet-color-gold);
	border: none;
	padding: 10px 16px 8px 16px;
	border-radius: 5px;
	cursor: pointer;
	transition: background-color 0.3s ease;
	width: 150px;
	height: 40px;
	display: inline-flex;
	justify-content: center;
	align-items: center;
	min-width: 150px;
	min-height: 40px;
}

.price-button:hover {
	/* background-color: #e04a00; */
}
</style>
