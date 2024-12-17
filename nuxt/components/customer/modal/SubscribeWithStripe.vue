<script setup lang="ts">
import { formatNumber } from '@/libs/utilities';
import { useQuery } from '@tanstack/vue-query';
import Swal from 'sweetalert2';
import { defineProps, ref } from 'vue';
import { customerAPI } from '~/libs/api';
import { VueQuery } from '~/libs/enums';

const authStore = useAuthStore();
const loading = ref(false);
const showPaymentModal = ref(false);
const props = defineProps({
  isExist: {
    type: Boolean,
    required: true,
  },
});
const isCardExist = ref(props.isExist);
const savePaymentInfo = () => {
  closePaymentModal();
};

const openPaymentModal = () => {
  showPaymentModal.value = true;
};

const closePaymentModal = () => {
  showPaymentModal.value = false;
  isCardExist.value = true;
};

const emit = defineEmits(['close', 'save']);
const showModal = ref(true);
const closeModal = () => {
  emit('close');
};

const { data: subscriptionInfo, isLoading } = useQuery({
  queryKey: [VueQuery.GET_CUSTOMER_SUBSCRIPTION_INFO],
  queryFn: () => customerAPI.getSubscriptionList(),
});

const saveSubscription = async (priceId: string, unitAmount: number) => {
  if (!isCardExist.value) {
    openPaymentModal();
    return;
  }
  loading.value = true;
  try {
    const payload = {
      user_id: authStore.user?.id || 0,
      plan_id: priceId,
      stripe_customer_id: authStore.user?.stripe_customer_id || '',
      points: (unitAmount / 1.2),
      user_type: authStore.user?.account_type,
      currency: "JPY",
      email: authStore.user?.email || '',
      payment_for: "subscription",

    };

    const response = await customerAPI.createSubscription(payload);
    console.log(response);
    if (response.status === 'SUCCESS' && response.status_code === 200) {
      Swal.fire({
        icon: 'success',
        title: '登録が完了しました',
        text: 'プランの登録が完了しました。ありがとうございました。',
        confirmButtonText: 'OK',
      });
      closeModal();
    } else {
      throw new Error(response.message || 'Failed to subscribe. Please try again.');
    }
    closeModal()

  } catch (error: any) {
    const errors = JSON.parse(error.message);
    Swal.fire(
      'Error!',
      errors.error || 'An unexpected error occurred. Please try again later.',
      'error'
    )
  } finally {
    loading.value = false;
  }
  closeModal();
};
</script>

<template>
  <div v-if="showModal" class="modal-overlay">
    <div class="modal-content p-4">
      <button class="close-button" @click="closeModal">&times;</button>
      <h2 class="modal-title fs-3 mb-4">プランを選択してください</h2>
      <div v-if="isLoading" class="loading">Loading...</div>
      <div v-else-if="Array.isArray(subscriptionInfo?.data) && subscriptionInfo.data.length" class="card-container">
        <div class="" v-for="(subscription, index) in subscriptionInfo.data" :key="index">
          <div class="card" v-if="subscription.name === '月額プラン'">
          <div class="card-header">
            <h3 class="p-2 fs-5">{{ subscription.name }}</h3>
          </div>
          <div class="card-body">
            <p class="card-price fs-2">
              <span class="fw-bold price" >{{ formatNumber(subscription.prices[0].unit_amount) }}</span><span class="fs-4"> 円/月(税込)</span>
              <!-- {{ subscription.prices[0].interval }} -->
            </p>
            <p class="card-description fs-5">{{ subscription.description }}</p>

            <button :disabled="loading"
              @click="saveSubscription(subscription.prices[0].price_id, subscription.prices[0].unit_amount)"
              class="fs-3 btn btn-deet-gold">
              月額プランで契約
            </button>
          </div>
          </div>
        </div>
      </div>
      <div v-else class="error-message">No subscriptions available.</div>
      <CustomerModalPaymentEditModal v-if="showPaymentModal" @close="closePaymentModal" @save="savePaymentInfo" />
    </div>
  </div>
</template>

<style scoped lang="css">
/* Overlay */
.deet-btn {
  /* max-width: 9rem; */
}
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  /* background-color: rgba(0, 0, 0, 0.7); */
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

/* Modal Content */
.modal-content {
  background-color: var(--deet-color-base);
  border-radius: 10px;
  padding: 20px;
  width: 95%;
  /* max-width: 800px; */
  position: relative;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
  animation: fadeIn 0.3s ease-in-out;
}

/* Close Button */
.close-button {
  position: absolute;
  top: 10px;
  right: 10px;
  background: transparent;
  border: none;
  font-size: 20px;
  font-weight: bold;
  cursor: pointer;
  color: white;
}

.close-button:hover {
  color: #ff0000;
}

/* Modal Title */
.modal-title {
  text-align: center;
  /* font-size: 1.8rem; */
  /* font-weight: bold; */
  /* margin-bottom: 20px; */
}

/* Card Container */
.card-container {
  display: flex;
  justify-content: space-between;
  gap: 15px;
  flex-wrap: wrap;
}

/* Card */
.card {
  background: var(--deet-color-cotent-base);
  color: var(--deet-color-font);
  /* border: 1px solid #ddd; */
  border-radius: 6px;
  padding: 20px;
  text-align: center;
  flex: 1;
  /* max-width: 30%; */
  /* box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1); */
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Card Header */
.card-header {
  padding-left: 35%;
  padding-right: 35%;
  border: none;
  h3 {
    background-color: var(--deet-color-status-online);    
  }
  /* margin-bottom: 15px; */
}

.card-title {
  /* font-size: 1.25rem; */
  /* font-weight: bold; */
}

/* Card Body */
.card-price {
  .price {
    font-size: 3.3rem;
  }
  /* font-size: 1.5rem; */
  /* color: var(--deet-color-font); */
  /* margin-bottom: 15px; */
}

.btn {
  padding: 10px 20px;
  font-size: 1rem;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.btn-primary {
  background-color: #007bff;
  color: white;
  border: none;
}

.btn-primary:hover {
  background-color: #0056b3;
}



.loading {
  text-align: center;
  font-size: 1.5rem;
  color: white;
}

.error-message {
  text-align: center;
  color: red;
  font-size: 1.2rem;
}

/* Animation */
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
