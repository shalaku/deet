<template lang="html">
	<div class="modal-overlay">
		<div class="modal-content">
			<!-- Cross button for closing the modal -->
			<button type="button" class="close-button" @click="closeModal">×</button>
			<!-- Success Message -->
			<div v-if="showSuccessMessage" class="success-message">
				<div class="success-icon">
					<svg
						xmlns="http://www.w3.org/2000/svg"
						viewBox="0 0 24 24"
						fill="none"
						stroke="currentColor"
						stroke-width="2"
						stroke-linecap="round"
						stroke-linejoin="round"
						class="icon-check"
					>
						<path d="M9 12l2 2l4-4" />
						<circle cx="12" cy="12" r="10" />
					</svg>
				</div>
				<h2>購入が完了しました</h2>
				<p>					
					<strong>{{ props.selectedPoints }} ポイント</strong> の購入が完了しました。					
				</p>
			</div>

			<form
				id="payment-form"
				v-if="!showSuccessMessage"
				@submit.prevent="handleSubmit"
			>
				<div id="link-authentication-element"></div>
				<div id="payment-element"></div>
				<button id="pay-now-button" :disabled="isLoading">支払う</button>
			</form>
		</div>
	</div>
</template>

<script setup lang="ts">
import {
	loadStripe,
	type Stripe,
	type StripeElements,
} from '@stripe/stripe-js';
import { defineEmits, defineProps } from 'vue';
import { useRouter } from 'vue-router';
import { customerAPI } from '~/libs/api';
const router = useRouter();
const props = defineProps({
	selectedPoints: {
		type: Number,
		required: true,
	},
	returnUrl:{
		type: String,
		required: true,
	}
});
const authStore = useAuthStore();
const emit = defineEmits(['close', 'save']);

const closeModal = () => {
	emit('close');
};
const isLoading = ref(true);
const showSuccessMessage = ref(false);
let stripe = null as unknown as Stripe;
let elements = null as unknown as StripeElements;

onMounted(async () => {
	const publishableKey = import.meta.env.VITE_STRIPE_PUBLISHERKEY;
	stripe = (await loadStripe(publishableKey)) as Stripe;

	const {
		data: { clientSecret },
	} = await customerAPI.createPaymentIntent({
		points: props.selectedPoints,
		user_id: authStore.user?.id || 0,
		user_type: 'customer',
		currency: 'JPY',
		email: authStore.user?.email || '',
	});

	elements = stripe.elements({ clientSecret });

	const paymentElement = elements.create('payment');
	paymentElement.mount('#payment-element');
	const linkAuthenticationElement = elements.create('linkAuthentication');
	linkAuthenticationElement.mount('#link-authentication-element');
	isLoading.value = false;
});

const handleSubmit = async () => {
	if (isLoading.value) {
		return;
	}

	isLoading.value = true;

	const { error } = await stripe.confirmPayment({
		elements,
		confirmParams: {
			return_url: props.returnUrl,
		},
		redirect: 'if_required',
	});

	isLoading.value = false;
	if (error) {
		alert(`Payment failed: ${error.message}`);
	} else {
		showSuccessMessage.value = true;
		setTimeout(() => {			
			closeModal();
			router.push('/user/mypage');
		}, 3000);
	}
};
</script>

<style scoped lang="css">
.modal-overlay {
	position: fixed;
	top: var(--header-height);
	padding-top: 3rem;
	left: 0;
	width: 100%;
	height: calc(100dvh - var(--header-height));
	background-color: rgba(0, 0, 0, 0.5);
	display: flex;
	align-items: flex-start;
	justify-content: center;
}

.modal-content {
	position: relative;
	background-color: #fff;
	padding: 20px;
	border-radius: 10px;
	width: 400px;
	box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.close-button {
	position: absolute;
	top: 5px;
	right: 10px;
	background: none;
	border: none;
	font-size: 24px;
	cursor: pointer;
	color: #333;
	transition: color 0.3s ease;
	z-index: 1;
}

.close-button:hover {
	/* color: #ff5a00; */
}

#pay-now-button {
	display: block;
	width: 100%;
	margin-top: 40px;
	padding: 12px 20px;
	background: var(--deet-color-status-online);
	color: white;
	font-size: 18px;
	/* font-weight: bold; */
	text-align: center;
	border: none;
	border-radius: 8px;
	cursor: pointer;
	/* box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); */
	transition: all 0.3s ease;
}

#pay-now-button:hover {
	background: linear-gradient(90deg, #ff9966, #ff5e62);
	transform: translateY(-3px);
	box-shadow: 0 6px 10px rgba(0, 0, 0, 0.3);
}

#pay-now-button:active {
	transform: translateY(1px);
	box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

#pay-now-button:disabled {
	background: #ccc;
	cursor: not-allowed;
	box-shadow: none;
}

#pay-now-button:focus {
	/* outline: 3px solid rgba(255, 94, 98, 0.5); */
}

.success-message {
	text-align: center;
}

.success-icon {
	color: #4caf50;
	margin-bottom: 10px;
}

.icon-check {
	width: 50px;
	height: 50px;
	stroke-width: 3;
}

h2 {
	color: #4caf50;
	font-size: 22px;
	font-weight: bold;
}

p {
	color: #4caf50;
	margin: 10px 0;
	line-height: 1.5;
	font-size: 12px;
}
</style>
