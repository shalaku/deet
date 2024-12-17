<script setup>
import { formatDateJpShort, formatNumber, getOrderType } from '@/libs/utilities';
import { CButton, CModal, CModalBody, CModalHeader } from '@coreui/vue';
import Swal from 'sweetalert2';
import { onMounted, ref } from 'vue';
import { pointAPI } from '~/libs/api';

const authStore = useAuthStore();
console.log(authStore);
// Fetch orders with status 600 ordered
const pointHistory = ref([]);
const isLoading = ref(true);
const isModalVisible = ref(false);
const shownOrderData = ref([]);
const error = ref(null);

const user_type = ref('');
const user_id = ref(authStore.user.id);

const user_type_select = ref('');
const name_ja_input = ref('');
const nickname_input = ref('');
const min_point_input = ref('');
const max_point_input = ref('');

const currentPage = ref(1);
const totalPages = ref(0);

const initSubmit = () => {
  const params = {
    user_type: user_type.value,
    user_id: user_id.value,
  };
  currentPage.value = 1;
  totalPages.value = 0;

  console.log('handleSubmit', params);
  fetchPointHistory(params);
};


const handleSubmit = () => {
  const params = {
    user_type: user_type_select.value,
    name_ja: name_ja_input.value,
    nickname: nickname_input.value,
    min_point: min_point_input.value,
    max_point: max_point_input.value,    
  };
  console.log('handleSubmit', params);
  fetchPointHistory(params);
};

// 非同期関数でデータを取得
const fetchPointHistory = async (params = {}) => {
  try {
    params.page = currentPage.value; // Add page parameter
    params.limit = 20;
    const response = await pointAPI.getAll(params);
    pointHistory.value = response.data.points.data; // データをセット
    totalPages.value = response.data.pages; // Set total pages
  } catch (err) {
    error.value = err; // エラーをセット
    console.error('Error fetching point history:', err);
    Swal.fire({
      icon: 'error',
      title: 'エラー',
      text: 'ポイント履歴の取得に失敗しました。',
    });
  } finally {
    isLoading.value = false; // ローディング状態を更新
  }
};

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
    const params = {
        user_type: user_type.value,
        user_id: user_id.value,
    };
    fetchPointHistory(params);
  }
};

const reloadPage = () => {
  window.location.reload();
};


const showModal = (order) => {
    shownOrderData.value = order;
    isModalVisible.value = true; // モーダルを表示
};

// コンポーネントがマウントされたときにデータを取得
onMounted(() => {
  console.log('onMounted');
  initSubmit();
});
</script>

<template>
      <!-- search results section -->
      <div class="container mt-4">
        <div class="card card-dark">
          <div class="card-body fs-4">
            <div class="row">
                <div class="col-3">ID</div>
                <div class="col-2">案件</div>
                <div class="col-2">ポイント数</div>
                <div class="col-3">日時</div>
                <div class="col-2">詳細</div>
            </div>
            <div class="row" v-for="point in pointHistory" :key="point.id">
                <!-- {{point}} -->
                <div class="col-3">{{ point.matching_order?.id }} <br> {{ point.matching_order?.shown_id }} </div>
                <div class="col-2" v-if="point.matching_order">{{ getOrderType(point.matching_order.order_type) }}</div>
                <div class="col-2" v-else>-</div>
                <div class="col-2">{{ formatNumber(point.point_amount) }}P</div>                
                <div class="col-3">{{ formatDateJpShort(point.created_at) }}</div>
                <div class="col-2">
                    <div v-if="point.matching_order">
                        <CButton color="primary" @click="showModal(point.matching_order)">詳細</CButton>
                    </div>
                    <div v-else>--</div>
                </div>
            </div>

            <div v-if="isLoading" class="text-center">Loading...</div>

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

  <!-- モーダル -->
  <CModal title="案件情報" :visible="isModalVisible" v-on:close="
			() => {
				isModalVisible = false;
			}
		">
    <CModalHeader>
      <h5 class="modal-title">案件情報</h5>
    </CModalHeader>
    <CModalBody>
        {{ shownOrderData.id }}
        {{ shownOrderData.shown_id }}
        {{ shownOrderData.place_street }}
        {{ shownOrderData.shown_id }}
        {{ shownOrderData.shown_id }}
        {{ shownOrderData.shown_id }}
        {{ shownOrderData.shown_id }}
        {{ shownOrderData.shown_id }}
    
    </CModalBody>
  </CModal>



</template>

<style scoped>
.card {
    background-color: var(--deet-color-cotent-base);
    border: none;
    border-radius: 4px;
    .card-body {
        background-color: var(--deet-color-cotent-base);
        color: var(--deet-color-font);
        border-radius: 4px;
    }
}

</style>



