<script setup>
import { calculateAge, formatDateJpShort, formatMeetingTime, formatNumber, getAlcoholTxt, getFootworkTxt, getOrderType, getStatus, getVipTxt } from '@/libs/utilities';
import { CBadge, CButton, CModal, CModalBody, CModalHeader } from '@coreui/vue';
import Swal from 'sweetalert2';
import { onMounted, ref } from 'vue';
import { orderAPI } from '~/libs/api';

const orderHistory = ref([]);
const isLoading = ref(true);
const error = ref(null);
const selectedCast = ref({});
const selectedCase = ref({});
const isModalVisible = ref(false); // モーダルの表示状態
const isCaseModalVisible = ref(false); // モーダルの表示状態
// 変数の定義
const status_select = ref('');
const cast_status_select = ref('');
const working_status_select = ref('');
const live_status_select = ref('');
const location_select = ref('');
const planned_start_time = ref('');
const planned_end_time = ref('');
const currentPage = ref(1);
const totalPages = ref(0);


    // 基本ポイントを計算
    const calculateBasePoints = (details) => {
      return calculateTotalPoints(details) - calculateMidnightFee(details) - calculateExtraCharge(details) - calculateDesignatedPoint(details);
    };
    // 深夜料金を計算
    const calculateMidnightFee = (details) => {
    // calculateMidnightFee(details) {
      return details.reduce((sum, detail) => sum + (parseFloat(detail.mid_night_fee) || 0), 0);
    };
    // 超過料金を計算
    const calculateExtraCharge = (details) => {
    // calculateExtraCharge(details) {
      return details.reduce((sum, detail) => sum + (parseFloat(detail.extra_charge) || 0), 0);
    };
    // 指名ポイントを計算
    const calculateDesignatedPoint = (details) => {
    // calculateDesignatedPoint(details) {
      return details.reduce((sum, detail) => sum + (parseFloat(detail.designated_point) || 0), 0);
    };
    const calculateTotalPoints = (details) => {
      return details.reduce((sum, detail) => sum + (parseFloat(detail.total_point) || 0), 0);
    }


const fetchOrderHistory = async (params = {}) => {
  try {
    params.page = currentPage.value; // Add page parameter
    const response = await orderAPI.getAllOrders(params);
    orderHistory.value = response.data;
    totalPages.value = response.data.pages; // Set total pages

  } catch (err) {
    error.value = err;
    console.error('Error fetching order history:', err);
    Swal.fire({
      icon: 'error',
      title: 'エラー',
      text: '履歴の取得に失敗しました。',
    });
  } finally {
    isLoading.value = false;
  }
};

const showCaseDetails = (data) => {
  console.log(data);
  selectedCase.value = data; // 選択された女性会員の情報をセット
  isCaseModalVisible.value = true; // モーダルを表示
};

const showCastDetails = (cast) => {
  selectedCast.value = cast; // 選択された女性会員の情報をセット
  isModalVisible.value = true; // モーダルを表示
};

const handleSubmit = () => {
  const params = {
    status: status_select.value,
    cast_status: cast_status_select.value,
    cast_work_status: working_status_select.value,
    cast_live_status: live_status_select.value,
    location: location_select.value,
    planned_start_datetime: planned_start_time.value,
    planned_end_datetime: planned_end_time.value,
    order_type: 'direct',
    // Add other form fields here
  };
  fetchOrderHistory(params);
};

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
    const params = {
    status: status_select.value,
    cast_status: cast_status_select.value,
    cast_work_status: working_status_select.value,
    cast_live_status: live_status_select.value,
    location: location_select.value,
    planned_start_datetime: planned_start_time.value,
    planned_end_datetime: planned_end_time.value,
    // Add other form fields here
  };
  fetchOrderHistory(params);
  }
};

const reloadPage = () => {
  status_select.value = '';
  cast_status_select.value = '';
  working_status_select.value = '';
  live_status_select.value = '';
  location_select.value = '';
  planned_start_time.value = '';
  status_select.value = '';
  planned_end_time.value = '';
  handleSubmit();
};

onMounted(() => {
  handleSubmit();
  const intervalId = setInterval(() => {
    handleSubmit();
      }, 60000); // 60000ミリ秒 = 1分

      // コンポーネントがアンマウントされたときにインターバルをクリア
      onUnmounted(() => {
        clearInterval(intervalId);
      });
    });
</script>


<template>
  <div>
    <div>
      <div class="d-flex justify-content-between align-items-center content-header">
        <h4 class="fw-bold fs-3 px-3 py-3">Deet履歴</h4>
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
                  <div class="mb-3 col-3">
                    <label for="statusSelect" class="form-label">ステータス</label>
                    <select v-model="status_select" id="statusSelect" class="form-select">
                      <option value="">選択してください</option>
                      <option value="600">確定待ち</option>
                      <option value="601">確定</option>
                      <option value="602">進行中</option>
                      <option value="603">キャンセル</option>
                      <option value="604">終了</option>
                      <option value="605">拒否</option>
                    </select>
                  </div>
                  <div class="mb-3 col-3">
                    <label for="statusSelect" class="form-label">女性会員ステータス</label>
                    <select v-model="cast_status_select" id="castStatusSelect" class="form-select">
                      <option value="">選択してください</option>
                      <option value="100">稼働</option>
                      <option value="101">一時停止</option>
                      <option value="102">強制停止</option>
                      <option value="103">削除済み</option>
                    </select>
                  </div>                  
                  <div class="mb-3 col-3">
                    <label for="workingStatusSelect" class="form-label">稼働ステータス</label>
                    <select v-model="working_status_select" id="workingStatusSelect" class="form-select">
                      <option value="">選択してください</option>
                      <option value="110">待機</option>
                      <option value="111">合流中</option>
                      <option value="112">業務外</option>
                    </select>
                  </div>
                  <div class="mb-3 col-3">
                    <label for="liveStatusSelect" class="form-label">Liveステータス</label>
                    <select v-model="live_status_select" id="liveStatusSelect" class="form-select">
                      <option value="">選択してください</option>
                      <option value="120">オンライン</option>
                      <option value="121">離席</option>
                      <option value="122">オフライン</option>
                    </select>
                  </div>
                  <div class="mb-3 col-3">
                    <label for="locationSelect" class="form-label">案件の場所</label>
                    <select v-model="location_select" id="locationSelect" class="form-select">
                      <option value="">選択してください</option>
                      <option value="Bar V（六本木）">Bar V（六本木）</option>
                    </select>
                  </div>
                  <div class="mb-3 col-3">
                    <label for="startTime" class="form-label">開示時刻</label>
                    <input v-model="planned_start_time" type="datetime-local" id="startTime" class="form-control">
                  </div>
                  <div class="mb-3 col-3">
                    <label for="endTime" class="form-label">終了時刻</label>
                    <input v-model="planned_end_time" type="datetime-local" id="endTime" class="form-control">
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
                  <th scope="col" class="text-center">男性会員</th>
                  <th scope="col" class="text-center">種類</th>
                  <th scope="col" class="text-center">ステータス</th>
                  <th scope="col" class="text-center">人数</th>
                  <th scope="col" class="text-center">発生日時</th>                
                  <th scope="col" class="text-center">予定時刻</th>
                  <th scope="col" class="text-center">場所</th>
                  <th scope="col" class="text-center">開始/終了時刻</th>
                  <th scope="col" class="text-center">ポイント</th>
                  <th scope="col" class="text-center">詳細</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="order in orderHistory.orders" :key="order.id">
                  <td class="text-center">{{ order.id }}<br>{{ order.shown_id }}</td>
                  <td class="text-center">{{ order.customer.name_ja }}<br>（{{ order.customer.nickname }}）</td>
                  <td class="text-center">{{ getOrderType(order.order_type) }}</td>
                  <td class="text-center">
                    <CBadge class="fs-5" :class="'status-'+order.status">{{ getStatus(order.status) }}</CBadge>
                  </td>
                  <td class="text-center">{{ order.details.length }}/{{ order.number_of_people }}</td>
                  <td class="text-center">{{ formatDateJpShort(order.created_at) }}</td>
                  <td class="text-center">{{ formatDateJpShort(order.planned_meeting_date_time) }}<br>{{
                    formatMeetingTime(order.planned_meeting_time) }}</td>
                  <td class="text-center">{{ order.place_street }}</td>
                  <td class="text-center">開始：{{ formatDateJpShort(order.start_date_time) }}<br>終了：{{
                    formatDateJpShort(order.end_date_time) }}</td>
                  <td class="points">
                    <p>{{ formatNumber(calculateTotalPoints(order.details)) }}</p>
                  </td>
                  <td class="text-start">
                    <div v-for="detail in order.details" :key="detail.id">
                      <CBadge color="danger" class="mb-1" v-if="detail.start_date_time == null && detail.end_date_time == null">未合流</CBadge>
                      <CBadge color="success" class="mb-1" v-if="detail.start_date_time !== null && detail.end_date_time == null">合流中</CBadge>
                      <CBadge color="secondary" class="mb-1" v-if="detail.start_date_time !== null && detail.end_date_time !== null">解散済</CBadge>
                      <span class="mb-0 cast_name" @click="showCastDetails(detail)" style="cursor: pointer;">{{
                        detail.name_ja }}</span>
                    </div>
                    <CButton color="warning" class="fs-5" @click="showCaseDetails(order)">詳細</CButton>
                  </td>


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


  <!-- <CModal size="lg" backdrop="static" alignment="center" :visible="isModalVisible"
		aria-labelledby="ProfilePictureUpdateLabel" content-class-name="rounded-0" v-on:close="
			() => {
				isProfilePictureModalVisible = false;
			}
		">
		<CModalHeader class="border-0 pb-0" />
		<CModalBody>
			<div class="d-flex flex-wrap justify-content-around" v-if="images?.length > 0">
				<div v-for="(image, index) in images" :key="index" class="image-container position-relative"
					style="cursor: pointer" @click="handleImageClick(image.id)">
					<img :src="image.image_url" alt="Dummy image" class="uploaded-image" />
				</div>
			</div>
		</CModalBody>
	</CModal> -->

  <!-- 詳細モーダル -->
  <CModal title="コール詳細" size="xl" :visible="isCaseModalVisible" v-on:close="
			() => {
				isCaseModalVisible = false;
			}
		">
    <CModalHeader>
      <h5 class="modal-title">コール詳細</h5>
    </CModalHeader>
    <CModalBody>

      <div class="container-fluid">
        <div class="row">
          <div class="col-6">
            <div>
              <CBadge class="fs-5 mb-3" :class="'status-'+selectedCase.status">{{ getStatus(selectedCase.status) }}</CBadge>
              <span class="fs-5 ms-3">ID : {{ selectedCase.id }}</span>
            </div>
            <p class="fs-5">男性会員：{{ selectedCase.customer.name_ja }}（{{ selectedCase.customer.nickname }}）</p>
            <p class="fs-5">リクエスト場所：{{ selectedCase.place_street }}</p>
            <p class="fs-5">リクエスト時刻・時間：{{ formatDateJpShort(selectedCase.planned_meeting_date_time) }} / {{
              formatMeetingTime(selectedCase.planned_meeting_time) }}</p>
            <p class="fs-5">人数：{{ selectedCase.number_of_people }}</p>
            <p class="fs-5">開始時刻：{{ formatDateJpShort(selectedCase.start_date_time) }}</p>
            <p class="fs-5">終了時刻：{{ formatDateJpShort(selectedCase.end_date_time) }}</p>
          </div>
          <div class="col-6">
            <p class="fs-5">基本：{{ formatNumber(calculateBasePoints(selectedCase.details)) }}</p>
            <p class="fs-5">深夜：{{ formatNumber(calculateMidnightFee(selectedCase.details)) }}</p>
            <p class="fs-5">超過：{{ formatNumber(calculateExtraCharge(selectedCase.details)) }}</p>
            <p class="fs-5">指名：{{ formatNumber(calculateDesignatedPoint(selectedCase.details)) }}</p>
            <p class="fs-5">合計：{{ formatNumber(calculateTotalPoints(selectedCase.details)) }}</p>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-4 fs-5" v-for="detail in selectedCase.details" :key="detail.id">
            <div class="row">
              <div class="col-12">
                <CBadge color="danger" class="mb-1 fs-5"
                  v-if="detail.start_date_time == null && detail.end_date_time == null">未合流</CBadge>
                <CBadge color="success" class="mb-1 fs-5"
                  v-if="detail.start_date_time !== null && detail.end_date_time == null">合流中</CBadge>
                <CBadge color="secondary" class="mb-1 fs-5"
                  v-if="detail.start_date_time !== null && detail.end_date_time !== null">解散済</CBadge>
                <span class="ms-3">ID: {{ detail.id }}</span>
                <p class="mt-3"><strong>名前:</strong> {{ detail.name_ja }}</p>
                <p><strong>ニックネーム:</strong> {{ detail.nickname }}</p>
                <p><strong>年齢:</strong> {{ calculateAge(new Date(detail.birthday)) }}</p>
                <p><strong>クラス:</strong> {{ detail.rank }}</p>
                <p><strong>フットワーク:</strong> {{ getFootworkTxt(detail.footwork) }}</p>
                <p><strong>アルコール:</strong> {{ getAlcoholTxt(detail.alcohol) }}</p>
                <p><strong>VIP対応:</strong> {{ getVipTxt(detail.vip_status) }}</p>
              </div>
              <div class="col-6">
                <p class="fs-5">開始時刻：{{ formatDateJpShort((detail.start_date_time)) }}</p>
              </div>
              <div class="col-6">
                <p class="fs-5">終了時刻：{{ formatDateJpShort((detail.end_date_time)) }}</p>
              </div>
              <div class="col-6">
                <p class="fs-5">基本：{{ formatNumber((detail.applied_point)) }}</p>

                <p class="fs-5">超過：{{ formatNumber((detail.extra_charge)) }}</p>
              </div>
              <div class="col-6">
                <p class="fs-5">深夜：{{ formatNumber((detail.mid_night_fee)) }}</p>
                <p class="fs-5">指名：{{ formatNumber((detail.designated_point)) }}</p>
              </div>

              <p class="fs-5">合計：{{ formatNumber((detail.total_point)) }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- {{ selectedCase }} -->


      <!-- 他の女性会員情報をここに追加 -->
    </CModalBody>
  </CModal>




  <!-- モーダル -->
  <CModal title="女性会員情報" 
    :visible="isModalVisible"
    v-on:close="
			() => {
				isModalVisible = false;
			}
		"
  >
    <CModalHeader>
      <h5 class="modal-title">女性会員情報</h5>
    </CModalHeader>
    <CModalBody>
      <p><strong>ID:</strong> {{ selectedCast.id }}</p>
      <p><strong>名前:</strong> {{ selectedCast.name_ja }}</p>
      <p><strong>ニックネーム:</strong> {{ selectedCast.nickname }}</p>
      <p><strong>年齢:</strong> {{ calculateAge(new Date(selectedCast.birthday)) }}</p>
      <p><strong>クラス:</strong> {{ selectedCast.rank }}</p>
      <p><strong>フットワーク:</strong> {{ getFootworkTxt(selectedCast.footwork) }}</p>
      <p><strong>アルコール:</strong> {{ getAlcoholTxt(selectedCast.alcohol) }}</p>
      <p><strong>VIP対応:</strong> {{ getVipTxt(selectedCast.vip_status) }}</p>

      <!-- 他の女性会員情報をここに追加 -->
    </CModalBody>
  </CModal>

</template>

<style scoped>
.cast_name {
  cursor: pointer; /* ポインタを表示 */
}
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
.modal-header {
  background-color: #2f353a;
  color: #FFF;
}
</style>
