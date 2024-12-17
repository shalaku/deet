<script setup lang="ts">
import { calculateAge, formatDateJpShort, formatDateTimeJp, formatMeetingTime, getStatusCast } from '@/libs/utilities';
import Swal from 'sweetalert2';
import { ref, watchEffect } from 'vue';
import '~/assets/css/cast/components/order_details.css';
import { customerAPI, requestAPI } from '~/libs/api';


const props = defineProps({
  showButton: {
    type: Boolean,
    default: true,
  },
  buttonText: {
    type: String,
    default: '解散する',
  },
  buttonClickedText: {
    type: String,
    default: '解散済み',
  },
  buttonClass: {
    type: String,
    default: 'btn-green big-btn',
  },
  showRejectButton: {
    type: Boolean,
    default: false,
  },
  rejectButtonText: {
    type: String,
    default: '辞退する',
  },
  rejectButtonClickedText: {
    type: String,
    default: '辞退済み',
  },
  showMeetingTime: {
    type: Boolean,
    default: false,
  },
  hideHeader: {
    type: Boolean,
    default: false,
  },
  castData: {
    type: Array,
    required: true,
  },
  showBadge: {
    type: Boolean,
    default: true,
  },
  badgeText: {
    type: String,
    default: '',
  },  
  orderType: {
    type: String,
    default: '',
  },
  requestData: {
    type: Array,
    required: false,
  },
  designatedMatching: {
    type: Array,
    required: false,
  },
  profileData: {
    type: Object,
    required: false,
  },
  statusData: {
    type: Array,
    required: false,
  },
});

// リアクティブな変数を作成
const buttonClickedText = ref(props.buttonClickedText);

const baseRankPoint = ref({
    "BLACK": 10000,
    "PLATINUM": 8000,
    "GOLD": 6000,
});

const findDesignatedPoint = (requestData,cast_id) => {  
  // console.log(requestData);
  // console.log(cast_id);
  const matchingDetail = requestData.details.find(detail => detail.cast_id === cast_id);
  if (matchingDetail) {
    return matchingDetail.designated_point;
  } else {
    return 0;
  }
};


const isMainButtonClicked = ref(false);
const isRejectButtonClicked = ref(false);

const isloading = ref(true);

const castId = ref('');
const requestId = ref('');

const emit = defineEmits(['buttonClicked', 'rejectButtonClicked']);

const handleMainButtonClick = () => {
  isMainButtonClicked.value = true;
  emit('buttonClicked');
};

const handleRejectButtonClick = () => {
  isRejectButtonClicked.value = true;
  emit('rejectButtonClicked');
};

const getBadgeClass = (status) => {
  return status === '承諾' ? 'badge-accepted' : 'badge-pending';
};

// Create a function to fetch customer data
const getCastAvatarURL = (images: API.CastImage[]) => {
  const avatarURL = images.find((image) => image.is_profile_picture === 1)?.image_url;
  console.log('avatarURL: ', avatarURL);
  return avatarURL ? avatarURL : 'data:image/gif;base64,iVBORw0KGgoAAAANSUhEUgAAALcAAACICAYAAAClQnrnAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAAEnQAABJ0Ad5mH3gAAABhaVRYdFNuaXBNZXRhZGF0YQAAAAAAeyJjbGlwUG9pbnRzIjpbeyJ4IjowLCJ5IjowfSx7IngiOjE4NCwieSI6MH0seyJ4IjoxODQsInkiOjEzNn0seyJ4IjowLCJ5IjoxMzZ9XX3tvd09AAAHaklEQVR4Xu3aZ4sUWxfF8T3mnHPOOWCO6AtB3/mZ7veYTyIoqIgKKkbMUXMWc57LOre299y50zOj4OV51vx/UNhTXd1dZa+za9epbmtvb+8IwFC/5l/ADuGGLcINW4Qbtgg3bBFu2CLcsEW4YYtwwxbhhi3CDVuEG7YIN2wRbtgi3LBFuGGLc...';
};

const dummy_img = 'data:image/gif;base64,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';

const requestsWithCustomerData = ref([]);

const fetchCustomerDataForRequests = async () => {
  if (!Array.isArray(props.requestData)) {
    console.error('requestData is not an array');
    return;
  }
  try {
    const requests = await Promise.all(
      props.requestData.map(async (request) => {
        try {
          const response = await customerAPI.getCustomerById(request.customer_id);
          // console.log(request);
          const isMainButtonClicked = request.details.some(detail => detail.cast_id === props.profileData.cast.id && detail.status === 510);
          return {
            ...request,
            customerData: response.data,
            isMainButtonClicked: isMainButtonClicked,
            buttonText: isMainButtonClicked ? '応募済み' : props.buttonText,
          };
        } catch (error) {
          console.error(`Failed to fetch customer data for request ${request.id}:`, error);
          return {
            ...request,
            customerData: null,
            isMainButtonClicked: false,
            buttonText: props.buttonText,
          };
        }
      })
    );
    // console.log('requests: ============', requests);
    requestsWithCustomerData.value = requests;
    isloading.value = false;
  } catch (error) {
    console.error('Error processing requests:', error);
  }
};
const formatNumber = (value) => {
	return value.toLocaleString();
};

const submitRequestBtn = async (requestData, profileData, designated) => {
  if (findDesignatedPoint(requestData,props.profileData.cast.id) > 0) {
    designated = props.profileData.cast.id;
  }
  Swal.fire({
    title: "この案件に応募しますか？",
    icon: "warning",
    showCancelButton: true,
    reverseButtons: true,
    confirmButtonText: "はい",
    cancelButtonText: "いいえ",
  }).then(async (result) => {
    if (result.isConfirmed) {
      const params = {
        matching_id: requestData.id,
        cast_id: profileData.cast.id,
        status: 510,
        designated: designated
      };

      try {
        const response = await requestAPI.createDetail(params);
        console.log(response);

        Swal.fire({
          icon: 'success',
          title: '応募が完了しました',
        }).then(() => {
          // 応募完了後にボタンの状態を更新
          const request = requestsWithCustomerData.value.find(r => r.id === requestData.id);
          if (request) {
            request.isMainButtonClicked = true;
            request.buttonText = '応募が完了しました';
          }
        });
      } catch (error) {
        console.error('Error fetching cast data:', error);
      }
    } else if (result.isDenied) {
      // Swal.fire("Changes are not saved", "", "info");
    }
  });
};





watchEffect(() => {
//   console.log('props.requestData: ', props.requestData);
  console.log('props.profileData: ', props.profileData);
  fetchCustomerDataForRequests();
});
</script>

<template>
  <div class="wrapper order-details p-2">
    <div v-if="!hideHeader && requestsWithCustomerData && requestsWithCustomerData.length">
      <div class="row inner-card pb-4 pt-4 mb-3" v-for="requestData in requestsWithCustomerData" :key="requestData.id" :class="`req-${requestData.status}`">
        <div class="col-12 d-flex">
          <div class="profile-image-wrapper mb-3">
            <div class="profile-image-area">
              <img :src="requestData.customerData.images[0]?.image_url || dummy_img" alt="profile image"
                class="profile-image">
            </div>
            <!-- <datalist>{{ requestData.customerData }}</datalist> -->
          </div>
          <div class="fs-4 ms-4">
            <!-- <span class="fw-bold">お客様</span><br> -->
            <!-- <span class="fs-4">会社経営</span><br> -->
            <span class="fs-4">{{ requestData.customerData.work }}</span><br>
            <span class="fs-3">{{ requestData.customerData.nickname }}</span>            
            <span class="ms-3">{{ calculateAge(new Date(requestData.customerData.birthday)) }}歳</span>
          </div>
        </div>
        <div class="col-12 mt-3 mb-3">
          <div class="mb-2 d-flex justify-content-between">
            <div>
              <span class="fs-4 me-2 status-label" :class="`label-${requestData.status}`">{{
                getStatusCast(requestData.status) }}</span>
            </div>
            <div class="">
              <span v-if="!requestData.shown_id" class="fs-5 me-2">ID:{{ requestData.id }}</span>
              <span v-else class="fs-5 me-2">ID:{{ requestData.shown_id }}</span>  
              <span class="fs-5 me-2 text-end">{{ formatDateTimeJp(requestData.created_at) }}</span>
            </div>
          </div>
        </div>
        <div class="col-12 d-flex mb-2">
          <div class="fs-4">
            <!-- <span class="">ID:{{ requestData.id }}</span>
            <span class="fs-5 me-2 status-label" :class="`label-${requestData.status}`">{{ getStatus(requestData.status)
              }}</span> -->
            <span class="designated rank-tag fs-4 me-0"
              v-if="findDesignatedPoint(requestData, profileData.cast.id) > 0">指名案件</span>
          </div>
        </div>

        <div class="col-6">
          <div class="fs-4 fw-bold"><span class="icon-periscope d-inline-block me-2"></span>合流場所</div>
          <div class="fs-3">{{ requestData.area_name }}</div>
        </div>
        <div class="col-6">
          <div class="fs-4 fw-bold"><span class="icon-alarm d-inline-block me-2"></span>合流予定</div>
          <div class="fs-3">{{ formatDateJpShort(requestData.requested_start_time) }}</div>
        </div>

        <div class="col-6 mt-3">
          <div class="fs-4 fw-bold"><span class="icon-history d-inline-block me-2"></span>リクエスト時間</div>
          <div class="fs-3">{{ formatMeetingTime(requestData.requested_matching_term) }}</div>
        </div>
        <div class="col-6 mt-3">
          <div class="fs-4 fw-bold"><span class="icon-user d-inline-block me-2"></span>人数</div>
          <div class="fs-3">{{ requestData.number_of_people }}人</div>
        </div>

        <div class="col-6 mt-3">
          <div class="fs-4 fw-bold">獲得予定ポイント</div>
          <div class="fs-3">
            {{ formatNumber((baseRankPoint[profileData.cast.rank] * 2) *
            requestData.requested_matching_term?.split(':')[0])}}P～
          </div>
        </div>
        <div class="col-6 mt-3" v-if="requestData.mid_night_fee > 0">
          <div class="fs-4 fw-bold">深夜手当</div>
          <div class="fs-3">{{ formatNumber(requestData.mid_night_fee) }}P</div>
        </div>
        <div class="col-6 mt-3" v-if="findDesignatedPoint(requestData, profileData.cast.id) > 0">
          <div class="fs-4 fw-bold">指名ポイント</div>
          <div class="fs-3">{{ formatNumber((findDesignatedPoint(requestData, profileData.cast.id) * 2) *
            requestData.requested_matching_term?.split(':')[0])}}P～
            <!-- {{ profileData.cast.id }} -->
          </div>
        </div>


        <div class="col-12 mt-4 d-flex justify-content-start">
          <div class="fs-3 fw-bold">合計
            <!-- {{  }} -->
            {{ formatNumber(((Number(baseRankPoint[profileData.cast.rank]) + Number(findDesignatedPoint(requestData,
            profileData.cast.id))) * 2) *
            requestData.requested_matching_term?.split(':')[0] +
            requestData.mid_night_fee) }}
            P～
          </div>
        </div>
        <div class="col-12 d-flex justify-content-end align-items-end mt-4" v-if="showButton">
          <!-- {{ designatedMatching }} -->
          <button v-if="requestData.status == 501" type="submit" class="btn entry-btn deet-btn-clickable fs-4 fw-bold mt-2 ms-1 pt-3 pb-3 ps-5 pe-5"
            @click="(event) => {
              const designatedId = Array.isArray(designatedMatching) && designatedMatching.some(dm => dm.request.id === requestData.id)
                ? designatedMatching.find(dm => dm.request.id === requestData.id).request.id
                : '';
              submitRequestBtn(requestData, profileData, designatedId);
            }" :class="[buttonClass, { 'btn-secondary': isMainButtonClicked }]"
            :disabled="requestData.isMainButtonClicked || isRejectButtonClicked">
            {{ requestData.buttonText }}
          </button>
        </div>

        <div class="stamp-wraper d-flex justify-content-center align-items-center" v-if="requestData.status !== 501">
          <div class="stamp d-flex flex-column justify-content-center align-items-center">
            <span class="fw-bold title-1">募集を</span>
            <span class="fw-bold title-2">締め切りました</span>
          </div>
        </div>
      </div>
    </div>
    <div v-if="isloading" class="text-center my-5 py-5">
					<LoadingLine></LoadingLine>
		</div>
    <div v-if="!hideHeader && requestsWithCustomerData && requestsWithCustomerData.length == 0 && !isloading" class="text-center no-result">
					<p class="h4 mt-5 pt-1">案件はありません</p>
		</div>	
  </div>

</template>

<style scoped>
.stamp-wraper {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  transform: rotate(10%);
}
.stamp {
  z-index: 99;
  border: 3px solid #fff;
  /* border-radius: 50%; */
  width: 18rem;
  height: 8rem;
  position: absolute;
  color: #fff;
  .title-1 {
    font-size: 1.5rem;
  }
  .title-2 {
    font-size: 1.5rem;
  }
}
.inner-card {
  position: relative;
}
.req-502::before,
.req-504::before {
  content: "";
    width: 100%;
    height: 100%;
    background-color: #1f1c18e0;
    position: absolute;
    top: 0;
    border-radius: 6px;
    z-index: 99;
}
.req-502::after,
.req-504::after {
  /* content: "募集終了";
  font-size: 4rem;
  position: absolute;
  z-index: 100; */
}
.status-label {
  &.label-502,
  &.label-504,
  &.label-503 {
    display: none;
  }
}

.order-type {
  /* background-color: #f8f9fa; */
  /* color: #212529; */
}
.request {
  display: none;
}
.profile-image-wrapper {
    width: 45px;
    height: 45px;
}
.entry-btn,
.small-btn {
  height: 4.3rem;
}
.order-badge {
  display: none;
}
.order-badge.direct {
  display: block;
}
</style>
