<script setup>
import { calculateAge, formatDateJpShort, formatMeetingTime, getAlcoholTxt, getFootworkTxt, getStatus, getVipTxt } from '@/libs/utilities';
import { CBadge, CButton, CModal, CModalBody, CModalHeader, CTable, CTableBody, CTableDataCell, CTableHead, CTableHeaderCell, CTableRow } from '@coreui/vue';
import Swal from 'sweetalert2';
import { onMounted, ref } from 'vue';
import { requestAPI } from '~/libs/api';

// Fetch orders with status 600 ordered
const requestHistory = ref({ requests: [] }); // 初期化
const isLoading = ref(true);
const error = ref(null);
const selectedCast = ref({});
const selectedRequest = ref({});
const isModalVisible = ref(false); // モーダルの表示状態
const isEditModalVisible = ref(false); // モーダルの表示状態
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
const castList=ref([]);

const editing_details = ref([]);

const getPeopleClass = (detailsLength, numberOfPeople) => {
    if (detailsLength > numberOfPeople) {
      return 'over';
    } else if (detailsLength === numberOfPeople) {
      return 'just';
    } else {
      return 'less';
    }
}
const getPassedTime = (createdAt) => {
      const createdTime = new Date(createdAt).getTime();
      const currentTime = new Date().getTime();
      const diffMinutes = Math.floor((currentTime - createdTime) / 60000);

      if (diffMinutes > 60) {
        return '-';
      }
      return `${diffMinutes}分`;
}
const getTimeClass = (createdAt) => {
      const createdTime = new Date(createdAt).getTime();
      const currentTime = new Date().getTime();
      const diffMinutes = Math.floor((currentTime - createdTime) / 60000);

      if (diffMinutes >= 5 && diffMinutes < 10) {
        return 'time-warning';
      } else if (diffMinutes >= 10 && diffMinutes < 15) {
        return 'time-danger';
      } else if (diffMinutes >= 15 && diffMinutes <= 60) {
        return 'time-passed';
      }
      return '';
}

// 非同期関数でデータを取得
const fetchRequestHistory = async (params = {}) => {
  try {
    params.page = currentPage.value; // Add page parameter
    params.status = "501,504"; // Add page parameter
    const response = await requestAPI.getAllRequests(params);
    requestHistory.value = response.data;
    totalPages.value = response.data.pages; // Set total pages
  } catch (err) {
    error.value = err; // エラーをセット
    console.error('Error fetching order history:', err);
    Swal.fire({
      icon: 'error',
      title: 'エラー',
      text: '履歴の取得に失敗しました。',
    });
  } finally {
    isLoading.value = false; // ローディング状態を更新
  }
};

const showCastDetails = (cast) => {
  selectedCast.value = cast; // 選択された女性会員の情報をセット
  isModalVisible.value = true; // モーダルを表示
};


const showRequestEdit = (request) => {
  console.log(request.details);
  editing_details.value = [ ...request.details ]; 
  selectedRequest.value = request; // 選択された女性会員の情報をセット
  selectedRequest.value.request_matching_id = request.id;
  isEditModalVisible.value = true; // モーダルを表示
  onSearch();
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
    // order_type: "direct",
  };
  fetchRequestHistory(params);
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
    // order_type: "direct",
  };
  fetchRequestHistory(params);
  }
};

 // Search handler
 const onSearch = async () => {
  const params = {
    status: status_select.value,
    status: cast_status_select.value,
    work_status: working_status_select.value,
    live_status: live_status_select.value,
   
  };

      const response=await requestAPI.fetchCastData(params); // Call fetchData with the selected parameters
      castList.value=response.data.casts;
      console.log(castList.value);
    };

    const cancelRequest=async (requestId)=>{
      try {
    const response = await requestAPI.requestCancel(requestId);

    // Check if the response status and status_code indicate success
    if (response.status === "SUCCESS" && response.status_code === 200) {
      Swal.fire({
        icon: "success",
        title: "処理成功",
        text: response.message, // Display the success message from the response
        
      });
      fetchRequestHistory();
    } else {
      Swal.fire({
        icon: "error",
        title: "エラー",
        text: "リクエストのキャンセルに失敗しました",
      });
    }
  } catch (err) {
    console.error("Error cancelling request:", err);
    Swal.fire({
      icon: "error",
      title: "エラー",
      text: "エラーが発生しました。再ログイン後、再実行してください",
    });
  }
    }

    // Function to add a cast to selectedRequest.details
const addToSelectedRequest = (cast) => {
  if (!editing_details) {
    editing_details = [];
  }
  
  // Check if the cast is already added
  const exists = editing_details.value.some(
    (detail) => detail.cast_id === cast.cast.id
  );
  if (!exists) {
    editing_details.value.push({
      cast_id: cast.cast.id,
      status: 510,
      cast_work_status: cast.cast.work_status,
      rank: cast.cast.rank,
      name_ja: cast.cast.name_ja,
      nickname: cast.cast.nickname,
      birthday: cast.cast.birthday,
    });
  } else {
    Swal.fire({
      icon: "info",
      title: "追加済み",
      text: "既にリストに存在します。",
      willClose: () => {
        isEditModalVisible.value = true; // Swalが閉じられた後にモーダルを再度開く
      }         
    });
  }
  console.log(editing_details);
};

// Function to remove a cast from selectedRequest.details
const removeFromSelectedRequest = (castId) => {
  if (editing_details && Array.isArray(editing_details.value)) {
    const index = editing_details.value.findIndex(detail => detail.cast_id === castId);
    if (index !== -1) {
      editing_details.value.splice(index, 1);
    }
  } else {
    console.error('editing_details.value is not an array:', editing_details);
  }
};

const saveChanges = async () => {
  // console.log(selectedRequest);
  if (!editing_details.value || editing_details.value.length !== selectedRequest.value.number_of_people) {
    Swal.fire({
      icon: "warning",
      title: "無効な選択です",
      text: "人数が不適切です："+selectedRequest.value.number_of_people+"人ちょうどになるようにしてください",
      willClose: () => {
        isEditModalVisible.value = true; // Swalが閉じられた後にモーダルを再度開く
      }      
    });    
    // console.log(first_details.value);
    return;
  }

  // console.log(editing_details);
  const requestData = {
    request_matching_id: selectedRequest.value.request_matching_id, // Ensure this ID is set in your request data
    cast_ids: editing_details.value.map((detail) => detail.cast_id),
  };

  // console.log(requestData);

  try {
    const response = await requestAPI.castConfirm(requestData);

    if (response.status === "SUCCESS" && response.status_code === 200) {
      Swal.fire({
        icon: "success",
        title: "保存・更新されました",
        text: response.message,
        willClose: () => {
          selectedRequest.value.details = [...editing_details.value];
          selectedRequest.value.status = 502;
        }     
      });
    } else {
      Swal.fire({
        icon: "error",
        title: "エラー",
        text: response.message || "保存・更新に失敗しました",
      });
    }
  } catch (error) {
    console.error("Error saving changes:", error);
    Swal.fire({
      icon: "error",
      title: "エラー",
      text: "エラーが発生しました。再ログイン後、再実行してください",
    });
  }
  fetchRequestHistory();
};

const ForceCloseWithSuccess=async ()=>{
      try {
    const requestId=selectedRequest.value.request_matching_id;
    const response = await requestAPI.forceclosewithsuccess(requestId);

    // Check if the response status and status_code indicate success
    if (response.status === "SUCCESS" && response.status_code === 200) {
      Swal.fire({
        icon: "success",
        title: "処理成功",
        text: response.message, // Display the success message from the response
        
      });
      fetchRequestHistory();
    } else {
      Swal.fire({
        icon: "error",
        title: "エラー",
        text: "保存・更新に失敗しました",
      });
    }
  } catch (err) {
    console.error("Error confirme request:", err);
    Swal.fire({
      icon: "error",
      title: "エラー",
      text: "エラーが発生しました。再ログイン後、再実行してください",
    });
  }
    }

const reloadPage = () => {
  status_select.value = '';
  cast_status_select.value = '';
  working_status_select.value = '';
  live_status_select.value = '';
  location_select.value = '';
  planned_start_time.value = '';
  status_select.value = '';
  planned_end_time.value = '';
  order_type_select.value = '';
  fetchRequestHistory();
};

watch(isEditModalVisible, (newValue, oldValue) => {
  // if (!newValue && first_details.details) {
    // selectedRequest.value.details = [ ...first_details.details ]; 
  // }
});


// コンポーネントがマウントされたときにデータを取得
onMounted(() => {
      console.log('onMounted');
      fetchRequestHistory();
      
      const intervalId = setInterval(() => {
        fetchRequestHistory();
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
        <h4 class="fw-bold fs-3 px-3 py-3">コール検索</h4>
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
                  <!-- <div class="mb-3 col-3">
                    <label for="statusSelect" class="form-label">コールステータス</label>
                    <select v-model="status_select" id="statusSelect" class="form-select">
                      <option value="">選択してください</option>
                      <option value="501">募集中</option>
                      <option value="502">募集終了</option>
                      <option value="503">キャンセル</option>
                      <option value="504">要調整</option>
                    </select>
                  </div> -->
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
          <div v-if="!isLoading" class="card-body table-card">
            <table class="table table-hover fs-4">
              <thead>
                <tr>
                  <th scope="col" class="text-center">ID</th>
                  <th scope="col" class="text-center">男性会員</th>
                  <th scope="col" class="text-center">ステータス</th>
                  <th scope="col" class="text-center">人数</th>
                  <th scope="col" class="text-center">発生日時</th>
                  <th scope="col" class="text-center">経過時間</th>
                  <th scope="col" class="text-center">場所</th>
                  <th scope="col" class="text-center">予定時刻</th>
                  <th scope="col" class="text-center">希望特徴</th>
                  <th scope="col" class="text-center">希望クラス</th>
                  <th scope="col" class="text-center">詳細</th>
                  <th scope="col" class="text-center">編集</th>
                  <!-- <th scope="col" class="text-center">cancel</th> -->
                </tr>
              </thead>
              <tbody>
                <tr v-for="request in requestHistory.requests" :key="request.id" :class="'status-'+request.status">
                  <td class="text-center">{{ request.id }}<br>{{ request.shown_id }}</td>
                  <td class="text-center">{{ request.customer.name_ja }}<br>（{{ request.customer.nickname }}）</td>
                  <td class="text-center"><CBadge class="fs-5" :class="'status-'+request.status">{{ getStatus(request.status) }}</CBadge></td>
                  <td 
                    v-if="request.status==502"
                    :class="getPeopleClass(request.details.filter(detail => detail.status === 512).length, request.number_of_people)" 
                    class="text-center number_of_people"
                  >
                    {{ request.details.filter(detail => detail.status === 512).length }} / {{ request.number_of_people }}
                  </td>
                  <td 
                    v-else
                    :class="getPeopleClass(request.details.filter(detail => detail.status === 510).length, request.number_of_people)" 
                    class="text-center number_of_people"
                  >
                    {{ request.details.filter(detail => detail.status === 510).length }} / {{ request.number_of_people }}
                  </td>
                  <td class="text-center">{{ formatDateJpShort(request.created_at) }}</td>
                  <td :class="getTimeClass(request.created_at)" class="text-center">{{ getPassedTime(request.created_at) }}</td>
                  <td class="text-center">{{ request.area_name }}</td>
                  <td class="text-center">{{ formatDateJpShort(request.requested_start_time) }}<br>{{
                    formatMeetingTime(request.requested_matching_term) }}</td>
                  <td class="text-center">
                    <span v-for="(tag, index) in request.cast_tag" :key="index">
                      <CBadge color="secondary" class="me-1">{{ tag }}</CBadge>
                    </span>
                  </td>
                  <td class="text-center">
                    <span v-for="(rank, index) in request.cast_rank" :key="index">
                      <CBadge color="secondary" class="me-1">{{ rank }}</CBadge>
                    </span>
                  </td>
                  <td class="text-start">
                    <div v-for="detail in request.details" :key="detail.id">
                      <p class="mb-0 cast_name" @click="showCastDetails(detail)" style="cursor: pointer;">
                        <CBadge class="mb-1" :class="'status-'+detail.status">{{ getStatus(detail.status) }}</CBadge>
                        {{ detail.name_ja }}（{{ detail.nickname }}）</p>
                      <p class="ps-3 mb-0"></p>
                      <!-- <p class="ps-3 mb-0">指名：{{ formatNumber(detail.designated_point) }}</p> -->
                    </div>
                  </td>
                  <td>
                    <div v-if="request.status===501 || request.status===504">
                      <CButton color="secondary" class="fs-5" @click="showRequestEdit(request)">編集</CButton>
                      <!-- <button class="btn btn-warning" @click="showRequestEdit(request)">編集</button> -->
                    </div>
                  </td>
                  <!-- <td> -->
                    <!-- <div v-if="request.status===501 || request.status===504"> -->
                      <!-- <button class="btn btn-danger" @click="cancelRequest(request.id)">Cancel</button> -->
                    <!-- </div> -->
                  <!-- </td> -->

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

  <!-- モーダル -->
  <CModal size="xl" :visible="isEditModalVisible" v-on:close="
			() => {
				isEditModalVisible = false;
			}
		">
    <CModalHeader>
      <h5 class="modal-title">コール内容</h5>
    </CModalHeader>
    <CModalBody>
      <div class="row fs-5">
        <div class="col-11">
        <div>ID : {{ selectedRequest.id }}</div>
        <div>男性会員：{{ selectedRequest.customer.name_ja }}（{{ selectedRequest.customer.nickname }}）</div>
        <div>{{ getStatus(selectedRequest.status) }}</div>
        <div>リクエスト場所：{{ selectedRequest.area_name }}</div>
        <div>リクエスト時刻・時間：{{ formatDateJpShort(selectedRequest.requested_start_time) }} / {{
          formatMeetingTime(selectedRequest.requested_matching_term) }}</div>
        <div>希望人数：{{ selectedRequest.number_of_people }}</div>
        <div>希望女性会員特徴：{{ selectedRequest.cast_tag }}</div>
        <div>希望女性会員クラス：{{ selectedRequest.cast_rank }}</div>
        </div>
        <div class="col-1">
          <div v-if="selectedRequest.status===501 || selectedRequest.status===504">
            <button class="btn btn-danger" @click="cancelRequest(selectedRequest.id)">案件キャンセル</button>
          </div>
        </div>
      </div>

      <div class="current_cast fs-5">
        <div>現在の応募
          <CTable hover>
            <CTableHead>
              <CTableRow>
                <CTableHeaderCell scope="col">ステータス</CTableHeaderCell>
                <CTableHeaderCell scope="col">クラス</CTableHeaderCell>
                <CTableHeaderCell scope="col">名前</CTableHeaderCell>
                <CTableHeaderCell scope="col">年齢</CTableHeaderCell>
                <CTableHeaderCell scope="col">削除</CTableHeaderCell>
              </CTableRow>
            </CTableHead>
            <CTableBody>
              <CTableRow v-for="detail in editing_details" :key="detail.id">
                <CTableHeaderCell scope="row">
                  <CBadge class="fs-5" :class="'status-'+detail.cast_work_status">{{ getStatus(detail.cast_work_status) }}</CBadge>
                  <!-- <CBadge color="secondary">{{ getStatus(detail.status) }}</CBadge> -->
                </CTableHeaderCell>
                <CTableDataCell>{{ detail.rank }}</CTableDataCell>
                <CTableDataCell>{{ detail.name_ja }}（{{ detail.nickname }}）</CTableDataCell>
                <CTableDataCell>{{ calculateAge(new Date(detail.birthday)) }}</CTableDataCell>
                <CTableDataCell>
                  <CButton color="danger" class="fs-5" @click="removeFromSelectedRequest(detail.cast_id)">削除</CButton>
                </CTableDataCell>
              </CTableRow>
            </CTableBody>
          </CTable>

        </div>

        <div class="text-end">
          <!-- <CButton></CButton> -->
          <CButton color="success" class="fs-5" @click="saveChanges">保存</CButton>
          <CButton color="warning" class="fs-5 ms-3" @click="ForceCloseWithSuccess">強制確定</CButton>
          <!-- <button class="btn btn-success me-2" @click="saveChanges">保存</button> -->
          <!-- <button class="btn btn-warning" @click="ForceCloseWithSuccess">ForceCloseWithSuccess</button> -->
        </div>

      </div>
      <hr>
      <div class="search_cast">
        <h4>検索</h4>
        <form @submit.prevent="onSearch">
          <div class="row">
            <div class="mb-3 col-3">
              <label for="statusSelect" class="form-label">コールステータス</label>
              <select v-model="status_select" id="statusSelect" class="form-select">
                <option value="">選択してください</option>
                <option value="501">募集中</option>
                <option value="502">募集終了</option>
                <option value="503">キャンセル</option>
                <option value="504">保留</option>
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

            <div class="col-12 text-end">
              <button type="submit" class="reset-btn btn btn-outline-dark fs-5">
                条件リセット
              </button>
              <button type="submit" class="search-btn btn btn-success fs-5">
                検索
              </button>
            </div>
          </div>
        </form>
      </div>
      <hr>
      <div class="search_result fs-5">
        <h4>検索結果</h4>
        <CTable hover>
          <CTableHead>
            <CTableRow>
              <CTableHeaderCell scope="col">WorkingStatus</CTableHeaderCell>
              <CTableHeaderCell scope="col">CastRank</CTableHeaderCell>
              <CTableHeaderCell scope="col">CastName</CTableHeaderCell>
              <CTableHeaderCell scope="col">CastAge</CTableHeaderCell>
              <CTableHeaderCell scope="col">footwork</CTableHeaderCell>
              <CTableHeaderCell scope="col">alcohol</CTableHeaderCell>
              <CTableHeaderCell scope="col">VIP</CTableHeaderCell>
              <CTableHeaderCell scope="col">Select</CTableHeaderCell>
            </CTableRow>
          </CTableHead>
          <CTableBody>
    <CTableRow v-for="(cast, index) in castList" :key="index">
      <CTableHeaderCell scope="row">
        <CBadge class="fs-5" :class="'status-'+cast.cast.work_status">{{ getStatus(cast.cast.work_status) }}</CBadge>
        <!-- {{ cast.cast }} -->
        <!-- <CBadge color="secondary">{{ getStatus(cast.cast.status) }}</CBadge> -->
      </CTableHeaderCell>
      <CTableDataCell>{{ cast.cast.rank }}</CTableDataCell>
      <CTableDataCell>{{ cast.cast.name_ja }} and {{ cast.cast.nickname }}</CTableDataCell>
      <CTableDataCell>{{ calculateAge(new Date(cast.cast.birthday)) }}</CTableDataCell>
      <CTableDataCell>{{ getFootworkTxt(cast.cast.footwork) }}</CTableDataCell>
      <CTableDataCell>{{ getAlcoholTxt(cast.cast.alcohol) }}</CTableDataCell>
      <CTableDataCell>{{ getVipTxt(cast.cast.vip_status) }}</CTableDataCell>
      <CTableDataCell><button class="btn btn-success fs-5" @click="addToSelectedRequest(cast)">Select</button></CTableDataCell>
    </CTableRow>
  </CTableBody>
        </CTable>
      </div>

    </CModalBody>
  </CModal>


  <!-- モーダル -->
  <CModal title="女性会員情報" :visible="isModalVisible" v-on:close="
			() => {
				isModalVisible = false;
			}
		">
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
.modal-header {
  background-color: #2f353a;
  color: #FFF;
}

.status-504, 
.status-501 {
.time-warning {
  background-color: orange;
  color: #FFF;
}

.time-danger {
  background-color: red;
  color: #FFF;
}

.time-passed {
  animation: blink-bg 1s infinite;
}
}

@keyframes blink-bg {
  0%, 50%, 100% {
    background-color: red;
    color: #FFF;
  }
  25%, 75% {
    background-color: transparent;
    color: #000;
  }
}

.just {
  background-color: green;
  color: #FFF;
}

.status-504, 
.status-501 {
.over {
  background-color: yellow;
  /* color: #FFF; */
}

.less {
  background-color: red;
  color: #FFF;
}
}

.number_of_people {
  min-width: 3em;
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
</style>
