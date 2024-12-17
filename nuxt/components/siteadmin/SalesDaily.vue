<script setup>
import { calculateAge, formatDateJpShort, formatMeetingTime, formatNumber, getAlcoholTxt, getFootworkTxt, getOrderType, getStatus, getVipTxt } from '@/libs/utilities';
import { CBadge, CButton, CModal, CModalBody, CModalHeader } from '@coreui/vue';
import Swal from 'sweetalert2';
import { onMounted, ref } from 'vue';
import { orderAPI, pointAPI } from '~/libs/api';

const orderHistory = ref([]);
const pointHistory = ref([]);
const totalPointsIn = ref(0);
const totalPointsOut = ref(0);
const totalYenIn = ref(0);
const totalYenOut = ref(0);
const currentPage = ref(1);
const totalPages = ref(0);
const error = ref(null);
const isLoading = ref(true);

const start_date = ref('');
const end_date = ref('');

const totalCaseAmount = ref(0);
const totalDeetAmount = ref(0);
const totalCallAmount = ref(0);
const totalExtraChargeAmount = ref(0);

const selectedCase = ref({});
const isCaseModalVisible = ref(false);



const handleSubmit = () => {
  const paramsOrderHistory = {
    multi_status: '601,602,604',
    min_date: start_date.value,
    max_date: end_date.value,
    // cast_work_status: working_status_select.value,
    // cast_live_status: live_status_select.value,
    // location: location_select.value,
    // planned_start_datetime: planned_start_time.value,
    // planned_end_datetime: planned_end_time.value,
    // order_type: order_type_select.value,
    // Add other form fields here
  };
  const paramsPointHistory = {
    multi_status: '601,602,604',
    min_date: start_date.value,
    max_date: end_date.value,
    user_type: 'customer',
    // min_point: 0,
    // cast_work_status: working_status_select.value,
    // cast_live_status: live_status_select.value,
    // location: location_select.value,
    // planned_start_datetime: planned_start_time.value,
    // planned_end_datetime: planned_end_time.value,
    // order_type: order_type_select.value,
    // Add other form fields here
  };
  // console.log('handleSubmit', params);
  fetchOrderHistory(paramsOrderHistory);
  fetchPointHistory(paramsPointHistory);
};

const reloadPage = () => {
  start_date.value = '';
  end_date.value = '';
  handleSubmit();
};

const fetchOrderHistory = async (params = {}) => {
  try {
    params.page = currentPage.value; // Add page parameter
    // params.start_date_time
    // params.multi_status = '601,602,604'
    const response = await orderAPI.getAllOrders(params);
    orderHistory.value = response.data;
    totalPages.value = response.data.pages; // Set total pages
    totalCaseAmount.value = response.data.total;
    totalDeetAmount.value = response.data.order_type_points.direct;
    totalCallAmount.value = response.data.order_type_points.request;
    totalExtraChargeAmount.value = response.data.extra_charge_sum;
    calculateTotalPointsOut(orderHistory.value);
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

const fetchPointHistory = async (params = {}) => {
  try {
    params.page = currentPage.value; // Add page parameter
    const response = await pointAPI.getAll(params);
    pointHistory.value = response.data.points.data; // データをセット
    // console.log(response);
    totalPointsIn.value = response.data.total_points;
    totalYenIn.value = response.data.total_pay_amount;
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

const calculateTotalPointsOut = (orderHistory) => {
  // console.log(orderHistory);
  totalPointsOut.value = 0; // 初期化
  totalYenOut.value = 0;
  orderHistory.orders.forEach(order => {
    order.details.forEach(detail => {
      totalPointsOut.value += parseInt(detail.total_point);
      totalYenOut.value += parseInt(detail.total_point * (detail.point_rate/100))
      console.log(totalPointsOut.value);
    });
  });  
};


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

const showCaseDetails = (data) => {
  console.log(data);
  selectedCase.value = data; // 選択された女性会員の情報をセット
  isCaseModalVisible.value = true; // モーダルを表示
};

onMounted(() => {
  const currentDate = new Date().toISOString().slice(0, 10); // "YYYY-MM-DD"形式で現在の日時を取得
  start_date.value = currentDate;
  end_date.value = currentDate;
  handleSubmit();   
});



</script>

<template>

<div>
    <div>
      <div class="d-flex justify-content-between align-items-center content-header">
        <h4 class="fw-bold fs-3 px-3 py-3">日別売上</h4>
      </div>
      <!-- search menu section -->
      <div class="container-fluid">
        <div class="card card-dark">
          <div class="card-header">
            <h4 class="fs-3 px-3 pt-3 pb-2">検索メニュー</h4>
          </div>
          <div class="card-body fs-5">
            <div class="register-area">
              <form @submit.prevent="handleSubmit">
                <div class="row">
                  <div class="mb-3 col-3">
                    <label for="startDate" class="form-label">開始月</label>
                    <input v-model="start_date" type="date" id="startDate" class="form-control">
                  </div>
                  <div class="mb-3 col-3">
                    <label for="endDate" class="form-label">終了月</label>
                    <input v-model="end_date" type="date" id="endDate" class="form-control">
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

      <div class="container-fluid mt-4">
        <div class="card card-dark">
          <div class="card-body table-card">
            <div class="row">
              <div class="col-12">
                <div class="fs-3 fw-bold">案件数：{{ formatNumber(totalCaseAmount) }}件</div>
                <div class="fs-3 fw-bold">Deetポイント：{{ formatNumber(totalDeetAmount) }}P</div>
                <div class="fs-3 fw-bold">コールポイント：{{ formatNumber(totalCallAmount) }}P</div>
                <div class="fs-3 fw-bold">延長ポイント：{{ formatNumber(totalExtraChargeAmount) }}P</div>
                <div class="fs-3 fw-bold">プレゼントポイント：--P</div>
              </div>
                <hr>
            <div class="col-6">
              <div class="fs-3 fw-bold">合計消費ポイント：{{ formatNumber(totalPointsIn) }}P</div>
              <div class="fs-3 fw-bold">合計購入ポイント：{{ formatNumber(totalPointsIn) }}P</div>
              <div class="fs-3 fw-bold">合計購入金額：{{ formatNumber(totalYenIn) }}円</div>
            </div>
            <div class="col-6">         
              <div class="fs-3 fw-bold">合計取得ポイント：{{ formatNumber(totalPointsOut) }}P</div>  
              <div class="fs-3 fw-bold">合計支払い金額：{{ formatNumber(totalYenOut) }}円</div>  
            </div>
            <hr>
            <div class="col-12">
              <div class="fs-3 fw-bold">暫定利益額：{{ formatNumber(totalYenIn - totalYenOut) }}円</div>
              <div class="fs-3 fw-bold">暫定利益率：{{ Math.floor(((totalYenIn - totalYenOut) / totalYenIn) * 100 * 100) / 100 }}%</div>
            </div>
          </div>
            <div v-if="isLoading" class="text-center">Loading...</div>
          </div>
        </div>
      </div>

      <!-- search results section -->
      <div class="container-fluid mt-4">
        <div class="card card-dark">
          <div class="card-header">
            <h4 class="fs-3 px-3 pt-3 pb-2">検索結果</h4>
          </div>
          <div class="card-body table-card">
            <div class="row">
            <div class="col-6">
              <table class="table table-hover fs-4">
              <thead>
                <tr>
                  <th scope="col" class="text-center">ID</th>
                  <th scope="col" class="text-center">男性会員</th>
                  <th scope="col" class="text-center">種類</th>
                  <th scope="col" class="text-center">ポイント</th>
                  <th scope="col" class="text-center">金額</th>
                  <th scope="col" class="text-center">発生日時</th>
                  <th scope="col" class="text-center">詳細</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="point in pointHistory" :key="point.id">
                  <td class="text-center">{{ point.matching_order?.id }}</td>
                  <td class="text-center">{{ point.customer.name_ja }}</td>
                  <td class="text-center">
                    <span v-if="point.matching_order?.order_type">
                      {{ getOrderType(point.matching_order.order_type) }}
                    </span>                  
                  </td>
                  <!-- <td v-if="point.cast !== null" class="text-center">{{ point.cast.name_ja }}</td> -->
                  <!-- <td v-else class="text-center">{{ point.customer.name_ja }}</td> -->
                  <td class="text-center">{{ formatNumber(point.point_amount) }}P</td>
                  <td class="text-center">{{ formatNumber(point.pay_amount) }}円</td>
                  <td class="text-center">{{ formatDateJpShort(point.created_at) }}</td>
                  <td class="text-center">
                    <span v-if="point.matching_order">
                      <!-- {{ point }} -->
                      <CButton color="warning" class="fs-5" @click="showCaseDetails(point.matching_order)">詳細</CButton>
                    </span>                       
                  </td>



                </tr>
              </tbody>
            </table>
            </div>
            <div class="col-6">
              <table class="table table-hover fs-4">
              <thead>
                <tr>
                  <th scope="col" class="text-center">ID</th>
                  <th scope="col" class="text-center">男性会員</th>
                  <th scope="col" class="text-center">種類</th>
                  <th scope="col" class="text-center">ステータス</th>
                  <th scope="col" class="text-center">人数</th>
>
                  <th scope="col" class="text-center">場所</th>
                  <th scope="col" class="text-center">開始/終了時刻</th>
                  <th scope="col" class="text-center">ポイント</th>
                  <!-- <th scope="col" class="text-center">詳細</th> -->
                </tr>
              </thead>
              <tbody>
                <tr v-for="order in orderHistory.orders" :key="order.id">
                  <td class="text-center">{{ order.id }}</td>
                  <td class="text-center">{{ order.customer.name_ja }}<br>（{{ order.customer.nickname }}）</td>
                  <td class="text-center">{{ getOrderType(order.order_type) }}</td>
                  <td class="text-center">
                    <CBadge class="fs-5" :class="'status-'+order.status">{{ getStatus(order.status) }}</CBadge>
                  </td>
                  <td class="text-center">{{ order.details.length }}</td>

                  <td class="text-center">{{ order.place_street }}</td>
                  <td class="text-center">開始：{{ formatDateJpShort(order.start_date_time) }}<br>終了：{{
                    formatDateJpShort(order.end_date_time) }}</td>
                  <td class="points">
                    <p>{{ formatNumber(calculateTotalPoints(order.details)) }}</p>
                  </td>
                  <td class="text-start">
                    <div v-for="detail in order.details" :key="detail.id">
                      <!-- <CBadge color="danger" class="mb-1" -->
                        <!-- v-if="detail.start_date_time == null && detail.end_date_time == null">未合流</CBadge> -->
                      <!-- <CBadge color="success" class="mb-1" -->
                        <!-- v-if="detail.start_date_time !== null && detail.end_date_time == null">合流中</CBadge> -->
                      <!-- <CBadge color="secondary" class="mb-1" -->
                        <!-- v-if="detail.start_date_time !== null && detail.end_date_time !== null">解散済</CBadge> -->
                      <span class="mb-0 cast_name" @click="showCastDetails(detail)" style="cursor: pointer;">{{
                        detail.name_ja }}</span>
                      <span> {{ formatNumber(detail.total_point) }}({{ detail.total_point * (detail.point_rate/100) }})</span>
                    </div>
                    <CButton color="warning" class="fs-5" @click="showCaseDetails(order)">詳細</CButton>
                  </td>


                </tr>
              </tbody>
            </table>              
            </div>
          </div>
            <div v-if="isLoading" class="text-center">Loading...</div>

          <!-- ページネーションのサンプル置いただけ、後から実装する
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
          </nav> -->

          </div>
        </div>
      </div>
    </div>
  </div>

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