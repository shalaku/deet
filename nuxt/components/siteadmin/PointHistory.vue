<script setup>
import { formatDateJpShort, formatNumber } from '@/libs/utilities';
import Swal from 'sweetalert2';
import { onMounted, ref } from 'vue';
import { pointAPI } from '~/libs/api';

// Fetch orders with status 600 ordered
const pointHistory = ref([]);
const isLoading = ref(true);
const error = ref(null);

const user_type_select = ref('');
const name_ja_input = ref('');
const nickname_input = ref('');
const min_point_input = ref('');
const max_point_input = ref('');

const currentPage = ref(1);
const totalPages = ref(0);

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
      user_type: user_type_select.value,
      name_ja: name_ja_input.value,
      nickname: nickname_input.value,
      min_point: min_point_input.value,
      max_point: max_point_input.value,
    };
    fetchPointHistory(params);
  }
};

const reloadPage = () => {
  window.location.reload();
};

// コンポーネントがマウントされたときにデータを取得
onMounted(() => {
  console.log('onMounted');
  fetchPointHistory();
});
</script>

<template>
  <div>
    <div>
      <div class="d-flex justify-content-between align-items-center content-header">
        <h4 class="fw-bold fs-3 px-3 py-3">ポイント履歴検索</h4>
      </div>
      <!-- search menu section -->
      <div class="container">
        <div class="card card-dark">
          <div class="card-header">
            <h4 class="fs-3 px-3 pt-3 pb-2">検索メニュー</h4>
          </div>
          <div class="card-body fs-5">
            <div class="register-area">
              <form @submit.prevent="handleSubmit">
                <div class="row">
                  
                  <div class="mb-3 col-2">
                    <label for="userTypeSelect" class="form-label">ユーザータイプ</label>
                    <select v-model="user_type_select" id="userTypeSelect" class="form-select">
                      <option value="">選択してください</option>
                      <option value="cast">女性会員</option>
                      <option value="customer">男性会員</option>
                    </select>
                  </div>
                  
                  <div class="mb-3 col-3">
                    <label for="nameJaInput">ユーザーネーム</label>
                    <input type="text" v-model="name_ja_input" id="nameJaInput" class="form-control" placeholder="姓名を入力" />
                  </div>

                  <div class="mb-3 col-3">
                    <label for="nicknameInput">ニックネーム</label>
                    <input type="text" v-model="nickname_input" id="nicknameInput" class="form-control" placeholder="ニックネームを入力" />
                  </div>                  


                  <div class="mb-3 col-2">
                    <label for="minPointInput" class="form-label">ポイント範囲（min）</label>
                    <input v-model="min_point_input" type="number" id="minPointInput" step="1000" class="form-control">
                  </div>
                  <div class="mb-3 col-2">
                    <label for="maxPointInput" class="form-label">ポイント範囲（max）</label>
                    <input v-model="max_point_input" type="number" id="maxPointInput" step="1000" class="form-control">
                  </div>
                  <div class="col-12 text-end">
                    <button type="submit" class="reset-btn btn btn-secondary" @click="reloadPage">
                      条件リセット
                    </button>
                    <button type="submit" class="btn deet-btn">
                      検索
                    </button>
                  </div>
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
            <h4 class="fs-3 px-3 pt-3 pb-2">検索結果</h4>
          </div>
          <div class="card-body table-card">
            <table class="table table-hover fs-4">
              <thead>
                <tr>
                  <th scope="col" class="text-center">ID</th>
                  <th scope="col" class="text-center">type</th>
                  <th scope="col" class="text-center">name</th>
                  <th scope="col" class="text-center">order_id</th>
                  <th scope="col" class="text-center">point_amount</th>
                  <th scope="col" class="text-center">pay_amount</th>
                  <th scope="col" class="text-center">payment_status</th>
                  <th scope="col" class="text-center">sell_date_time</th>
                  <th scope="col" class="text-center">created_at</th>
                  <th scope="col" class="text-center">updated_at</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="point in pointHistory" :key="point.id">
                  <td class="text-center">{{ point.id }}</td>
                  <td class="text-center">
                    <span v-if="point.customer">男性会員</span>
                    <span v-else-if="point.cast">女性会員</span>
                  </td>
                  <td class="text-center">
                    <span v-if="point.customer">{{ point.customer.name_ja }}<br>({{ point.customer.nickname }})</span>
                    <span v-else-if="point.cast">{{ point.cast.name_ja }}<br>({{ point.cast.nickname }})</span>
                  </td>
                  <td class="text-center">{{ point.order_id }}</td>
                  <td class="text-center">{{ formatNumber(point.point_amount) }}</td>
                  <td class="text-center">{{ point.pay_amount }}</td>
                  <td class="text-center">{{ point.payment_status }}</td>
                  <td class="text-center">{{ point.sell_date_time }}</td>
                  <td class="text-center">{{ formatDateJpShort(point.created_at) }}</td>
                  <td class="text-center">{{ formatDateJpShort(point.updated_at) }}</td>
                </tr>
              </tbody>
            </table>
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
    </div>
  </div>
</template>

<style scoped>
.card-dark .card-header {
  background: #2f353a 0% 0% no-repeat padding-box;
}

.card-dark .card-header h4 {
  color: #ffffff;
}
label {
  font-size: 1.25rem;
}
.form-control,
.form-select {
  height: 43px;  
}
.form-select {
	font-size: 1.6rem;
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
    padding: 9px 40px;
    margin-top: 15px;
    max-width: 328px;
    margin-left: 1em;
    white-space: nowrap;
    color: #FFF;
    margin-right: 12px;
}
.pagination {
	a {
		font-size: 1.5rem !important;
		padding: 3px 10px;
	}
}
</style>
