<script setup lang="ts">
import type { PaymentMethod, Stripe, StripeError } from '@stripe/stripe-js';
import { loadStripe } from '@stripe/stripe-js';
import Swal from 'sweetalert2';
import { onMounted, ref } from 'vue';
import { customerAPI } from '~/libs/api';

const stripePromise = loadStripe('pk_test_51QPoH3QAPkkuHM1C1OnmViNeQ0kHJHzQ8suoOD6iACvms4wDKFe08gsM2G78wds5OUufoSP27nvzlgDHkTmaaMlB00Ikklq5Wd'); // Replace with your Stripe publishable key

// State management for the form
const cardElement = ref<any>(null);
const stripeInstance = ref<Stripe | null>(null);
const cardError = ref('');
const loading = ref(false);
const authStore = useAuthStore();
const emit = defineEmits(['close', 'save']);
const paymentInfo = ref({
	name: '',
});

// Modal visibility
const showModal = ref(true);
const closeModal = () => {
	emit('close');
};
// Setup Stripe Elements
onMounted(async () => {
	stripeInstance.value = await stripePromise;
	if (stripeInstance.value) {
		const elements = stripeInstance.value.elements();
		cardElement.value = elements.create('card', {
			style: {
				base: {
					color: '#fff',
					// fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
					fontSmoothing: 'antialiased',
					fontSize: '16px',
					'::placeholder': {
						color: '#736b60'
					},
					borderColor: '#736b60',
          			borderRadius: '4px',
          			backgroundColor: '#1d1a16',
				},
				invalid: {
					color: '#fa755a',
					iconColor: '#fa755a'
				}
			}
		});
		cardElement.value.mount('#card-element');
	}
});

const savePaymentInfo = async () => {
	if (!stripeInstance.value) return;

	cardError.value = '';
	loading.value = true;

	try {
		const { paymentMethod, error }: { paymentMethod?: PaymentMethod; error?: StripeError | null } =
			await stripeInstance.value.createPaymentMethod({
				type: 'card',
				card: cardElement.value,
				billing_details: {
					name: paymentInfo.value.name,
				},
			});

		if (error) {
			cardError.value = error.message || 'An error occurred while processing the card';
		} else if (paymentMethod) {
			const payload = {
				paymentMethodId: paymentMethod.id,
				userId: authStore.user?.id || 0,
			};

			const response = await customerAPI.savePaymentMethodForCard(payload);

			if (response.status === 'SUCCESS' && response.status_code === 201) {
				await Swal.fire(
					'保存完了',
					'カード情報を登録しました。',
					'success'
				);
				closeModal();
			} else {
				throw new Error(response.message || 'Failed to save card');
			}
			closeModal();
		}
	} catch (err) {
		cardError.value = 'An unexpected error occurred.';
	} finally {
		loading.value = false;
	}
};
</script>

<template>
	<div v-if="showModal" class="modal-overlay">
		<div class="modal-content">
			<button class="close-button" @click="closeModal">×</button>
			<h2 class="modal-title">クレジットカード登録</h2>
			<form @submit.prevent="savePaymentInfo">
				<div class="form-group">
					<label for="name" class="form-label fs-5">カード名義</label>
					<input id="name" v-model="paymentInfo.name" type="text" class="form-control"
						placeholder="カード所有者の名前を入力" required />
				</div>

				<div class="form-group mt-3">
					<label for="card-element" class="form-label fs-5">カード番号</label>
					<div id="card-element" class="form-control"></div>
					<small v-if="cardError" class="text-danger">{{ cardError }}</small>
				</div>

				<div class="mt-4 text-center">
					<button type="submit" class="btn btn-deet-gold fs-4" :disabled="loading">
						{{ loading ? '保存中...' : 'クレジットカードを追加' }}
					</button>
				</div>
			</form>
		</div>
	</div>
</template>

<style scoped>
.modal-overlay {
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background-color: rgba(0, 0, 0, 0.5);
	display: flex;
	justify-content: center;
	align-items: center;
	z-index: 1000;
}

.modal-content {
	background: var(--deet-color-base);
	padding: 30px;
	border-radius: 8px;
	max-width: 500px;
	width: 90%;
	box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
	position: relative;
	animation: fadeIn 0.3s ease-in-out;
}

.close-button {
	position: absolute;
	top: 15px;
	right: 15px;
	background: transparent;
	border: none;
	font-size: 20px;
	font-weight: bold;
	cursor: pointer;
	color: #FFF;
}

.close-button:hover {
	color: #ff0000;
}

/* Modal Title */
.modal-title {
	margin-bottom: 20px;
	font-size: 1.5rem;
	font-weight: bold;
	text-align: center;
	padding-top: 10px;
}

/* Form Controls */
.form-control {
	border: 1px solid #ced4da;
	border-radius: 4px;
	padding: 10px;
}

.form-group {
	margin-bottom: 20px;
}

/* Buttons */
.btn {
	padding: 10px 20px;
	font-size: 1rem;
	border-radius: 4px;
	cursor: pointer;
}

.btn-primary {
	background-color: #007bff;
	color: #fff;
	border: none;
}

.btn-primary:hover {
	background-color: #0056b3;
}

.btn-secondary {
	background-color: #6c757d;
	color: #fff;
	border: none;
}

.btn-secondary:hover {
	background-color: #5a6268;
}

#card-element {
	background-color: var(--deet-color-base);
	border-color: #736b60;
}

/* Animations */
@keyframes fadeIn {
	from {
		opacity: 0;
		transform: translateY(-20px);
	}

	to {
		opacity: 1;
		transform: translateY(0);
	}
}
</style>
