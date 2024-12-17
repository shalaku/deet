<script setup>
import { customerAPI } from '@/libs/api/customer-api';
import { formatDateJpShort } from '@/libs/utilities';
import { computed, ref, watch } from 'vue';

const route = useRoute();
const router = useRouter();
// 画像ダミーデータ
const dummyImageSrc =
    'data:image/gif;base64,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';
// 女性会員基本情報のダミーデータ
const basicInfo = ref({
  rank: 'A',
  name: '名前名前',
  age: '24',
  area: 'xx',
  needPoint: 10000,
  arrivalTime: 30,
  duringTime: 1,
  isOnline: true, // 仮に女性会員をオンライン状態に設定
});


// 確定ボタンのクリックハンドラ

// お気に入りの状態を管理するref
const isFavorite = ref(false);
// お気に入りボタンのクリックハンドラ
const toggleFavorite = () => {
  isFavorite.value = !isFavorite.value;
};
// チャットボタンのクリックハンドラ
const navigateToChat = () => {
  router.push('/user/chat');
};

const submitBtnClicked = ref(false);
const castData = ref([]);
const customerData = ref([]);
const arivalTime = ref("-");
const duringTime = ref(1);
const midNightFee = ref(0);
const totalPoint = ref(0);
const selectedDateTime = ref(null);
const selectedMinutes = ref('30');
const selectedDuration = ref('1'); 
const bookingDetails = ref({
  customer_id: 7,
  order_type: '',
  planned_meeting_date_time: '',
  planned_meeting_time: '1:00',
  // place_post_code: '',
  place_prefecture: '',
  place_municipalitie: '',
  place_street: 'Bar V（六本木）',
  place_building: '',
  place_desc: '',
  requested_start_time: '',
  laterStartMeetingMinute: '',
  mid_night_fee: 0,
  number_of_people: 1,
//   start_date_time: '',
//   end_date_time: '',
//   casts: [],
  cast_id: 0,
});

const formatDateTime = (date) => {
	const pad = (num) => String(num).padStart(2, '0');
	return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}:${pad(date.getSeconds())}`;
};

const getCurrentDateTime = () => {
  const now = new Date();

  const formatDateTime = (date) => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const seconds = String(date.getSeconds()).padStart(2, '0');

    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
  };

  const startDateTime = formatDateTime(new Date(now.getTime() + 30 * 60 * 1000)); // +30 minutes
  const endDateTime = formatDateTime(new Date(now.getTime() + 90 * 60 * 1000)); // +1 hour 30 minutes

  return {startDateTime, endDateTime};
};

const fetchCasts = async (params = {}) => {
  // route.query.order_type
  if (route.query.order_type == "direct") {

    const params = {
      cast_id: route.query.cast_id,
    };

    try {
      // Call the API and await the response
      const response = await customerAPI.getCast(params);
      castData.value = response.data.casts;
      console.log('API response:', castData.value[0]);
      const isMidNightFeeApplicable = new Date(new Date().getTime() + 30 * 60 * 1000).getHours() >= 0 && new Date(new Date().getTime() + 30 * 60 * 1000).getHours() < 5;
      console.log("detailsCast", castData.value[0].cast.basic_point_price);
      //   const isMidNightFeeApplicable = new Date(now.getTime() + 30 * 60 * 1000).getHours() >= 0 && new Date().getHours() < 5;
      midNightFee.value = isMidNightFeeApplicable ? 5000 : 0;
      totalPoint.value = castData.value[0].cast.basic_point_price || 0;
      bookingDetails.value.cast_id = castData.value[0].cast.id;
    } catch (error) {
      console.error('Error fetching cast data:', error);
    }
  }
};

const fetchCustomer = async () => {
  try {
    const response = await customerAPI.getCustomerProfile();
    customerData.value = response.data;
    console.log(customerData.value);
    bookingDetails.value.customer_id = customerData.value.id || null;
    bookingDetails.value.order_type = route.query.order_type || '';
    const {startDateTime, endDateTime} = getCurrentDateTime();
    // bookingDetails.value.planned_meeting_date_time = startDateTime;
    // bookingDetails.value.planned_meeting_time = "1:00"; // it is static for search_cast->call_now
    // bookingDetails.value.place_post_code = customerData.value.post_code || '56788';
    bookingDetails.value.place_prefecture = customerData.value.prefecture || '';
    bookingDetails.value.place_municipalitie = customerData.value.municipalitie || '';
    bookingDetails.value.place_building = customerData.value.building || '';

    console.log('Final booking details:', bookingDetails.value);

  } catch (error) {
    console.error('Error fetching cast data:', error);
  }
}


const navigateToComplete = async () => {
  submitBtnClicked.value = true;
  // Wait for fetchCasts and fetchCustomer to finish
  await fetchCasts();
  await fetchCustomer();

  // Check if all required fields are populated
  const requiredFields = [
    'customer_id', 'order_type', 'planned_meeting_date_time',
    'planned_meeting_time'
  ];
  const isComplete = requiredFields.every(field => bookingDetails.value[field]);

  if (!isComplete) {
    console.error('Missing required fields in booking details:', bookingDetails.value);
    return;
  }
  try {
    const response = await customerAPI.makeOrder(bookingDetails.value);
    console.log('Order placed successfully:', response.data);
    router.push('/user/complete_call_cast');
  } catch (error) {
    console.error('Error placing order:', error.response ? error.response.data : error.message);
  }
};

// Computed property to format the date-time for the input
const formattedDateTime = computed({
	get: () => {
		return selectedDateTime.value ? selectedDateTime.value.slice(0, 16) : ''; // Format to YYYY-MM-DDTHH:MM
	},
	set: (value) => {
		selectedDateTime.value = value; 
		updateTotalDateTime(); 
	},
});

// Function to update total date-time
const updateTotalDateTime = () => {
	let dateTime;

	if (selectedDateTime.value) {
		// User has chosen a specific date-time in datetime-local input
		dateTime = new Date(selectedDateTime.value);
		bookingDetails.value.requested_start_time = formatDateTime(dateTime);
		bookingDetails.value.planned_meeting_date_time = formatDateTime(dateTime);    
		bookingDetails.value.laterStartMeetingMinute = 0;
    arivalTime.value = formatDateJpShort(dateTime);
    console.log("ii");
	} else if (selectedMinutes.value) {
		// User has selected a delay in minutes
		dateTime = new Date(); 
		dateTime.setMinutes(dateTime.getMinutes() + parseInt(selectedMinutes.value));
		bookingDetails.value.requested_start_time = formatDateTime(dateTime);
    bookingDetails.value.planned_meeting_date_time = formatDateTime(dateTime);  
		bookingDetails.value.laterStartMeetingMinute = selectedMinutes.value;
    arivalTime.value = selectedMinutes.value+"分後";
	}

	// Check for the midnight fee based on calculated hour
	const hour = dateTime.getHours();
	console.log(hour);
  bookingDetails.value.mid_night_fee = (hour >= 0 && hour < 5) ? 5000 : 0;
};

// Watch for changes to the selected duration
watch(selectedDuration, (newDuration) => {
	if (newDuration) {
		const hours = String(newDuration).padStart(2, '0');
		const formattedDuration = `${hours}:00`;
		bookingDetails.value.requested_matching_term = formattedDuration;
    bookingDetails.value.planned_meeting_time = formattedDuration;
    duringTime.value = Number(hours);
		console.log(`Updated bookingDetails: requested_matching_term: ${formattedDuration}`);
	} else {
		console.log('No duration selected');
	}
});

watch([selectedDateTime, selectedMinutes], updateTotalDateTime);

onMounted(() => {
  console.log('onMounted');
  fetchCasts();
  fetchCustomer();
  updateTotalDateTime();
});
// 検索ボタンのクリックハンドラ
const submitSearch = async () => {

};


</script>

<template>
  <div class="container-fluid p-0" :class=route.query.order_type>

    <!-- from search_cast -->
    <div class="content direct-order p-4" v-for="cast in castData" :key="cast.cast.id">

      <div class="row g-4 m-0">
        <div class="col-12 mt-2 mb-4">
          <h2 class="section-title text-center fs-3 fw-bold">Deet内容のご確認</h2>
        </div>
        <!-- cast image -->
        <div class="col-6 d-flex justify-content-center cast-image-container">
          <img :src="cast?.images[0]?.image_url || dummyImageSrc" alt="cast image" class="cast-image"/>
          <!-- <img :src="dummyImageSrc" alt="cast info image" class="cast-image" /> -->
          <div v-if="basicInfo.isOnline" class="online-status"></div>
        </div>
        <!-- cast infomation -->
        <div class="col-6 d-flex flex-column">
          <div class="f-flex flex-wrap mb-2">
            <span class="rank-tag fs-4">{{ cast.cast.rank }}</span>
          </div>
          <div class="f-flex flex-wrap mb-3">
						<span class="age fs-4">{{
                new Date().getFullYear() - new
                Date(cast?.cast.birthday).getFullYear()
              }}歳</span>
            <span class="place fs-4">{{ cast.cast.prefecture }}</span>
          </div>
          <div class="info-name fs-1 fw-normal">{{ cast.cast.nickname }}</div>


          <div class="info-price fs-4">
            {{
              (parseInt(cast.cast.basic_point_price, "10") || 0).toLocaleString("ja-JP", {
                maximumFractionDigits: 0
              })
            }}P / 30min
          </div>

          <button type="button" class="chat-btn mb-3 fs-4" @click="navigateToChat">
            チャット
          </button>
          <button type="button" class="favorite-btn fs-4" @click="toggleFavorite">
						<span class="favorite-btn-content">
							<i class="icon-star" :class="{ 'is-favorite': isFavorite }"></i>
							<span class="favorite-text">お気に入りに追加</span>
						</span>
          </button>
        </div>
        <!-- <div class="col-12 d-flex flex-column"> -->
        <!-- <div class="d-flex flex-wrap mb-3"> -->
        <!-- <span class="tag">特徴タグ1</span> -->
        <!-- <span class="tag">特徴タグ2</span> -->
        <!-- <span class="tag">特徴タグ3</span> -->
        <!-- <span class="tag">特徴タグ4</span> -->
        <!-- </div> -->
        <!-- <p class="info-comment fs-4"> -->
        <!-- 一言コメント一言コメント一言コメント一言コメント一言コメント一言コメント一言コメント一言コメント一言コメント一言コメント一言コメント一言コメント一言コメント一言コメント -->
        <!-- </p> -->

        <!-- </div> -->

        <!-- price -->
        <div
            class="col-12 justify-content-end align-self-center price-summary fs-4 mt-4"
        >
          <div class="text-end fw-normal">
            <div class="d-flex justify-content-end align-items-center fw-normal mb-2">
              <div>
                合流予定時間：<span>{{ arivalTime }}</span>
              </div>
            </div>
            <div class="d-flex justify-content-end align-items-center fw-normal mb-2">
              <div>
                設定時間：<span>{{ duringTime }}</span>時間
              </div>
            </div>
            <hr/>
            <div class="d-flex justify-content-between align-items-center fw-normal mb-2 fs-3">
              <div>合計ポイント</div>
              <div>{{ ((totalPoint * duringTime * 2) + bookingDetails.mid_night_fee).toLocaleString() }}P</div>
            </div>
          </div>

        </div>



					<!-- call time -->
					<div class="col-12">
						<label for="call-time-group" class="fs-4">何分後に合流しますか？</label>
						<div id="call-time-group">
							<div class="d-flex gap-2 align-items-stretch">
								<input type="radio" class="btn-check" name="call_time" id="call-time-30" value="30"
									v-model="selectedMinutes" @change="selectedDateTime = null" />
								<label
									class="btn btn-outline-deet-dark fs-4 d-flex align-items-center justify-content-center px-4 py-3"
									:class="{ 'checked': selectedMinutes === '30' }" for="call-time-30">30分後</label>

								<input type="radio" class="btn-check" name="call_time" id="call-time-60" value="60"
									v-model="selectedMinutes" @change="selectedDateTime = null" />
								<label
									class="btn btn-outline-deet-dark fs-4 d-flex align-items-center justify-content-center px-4 py-3"
									:class="{ 'checked': selectedMinutes === '60' }" for="call-time-60">60分後</label>

								<input type="radio" class="btn-check" name="call_time" id="call-time-90" value="90"
									v-model="selectedMinutes" @change="selectedDateTime = null" />
								<label
									class="btn btn-outline-deet-dark fs-4 d-flex align-items-center justify-content-center px-4 py-3"
									:class="{ 'checked': selectedMinutes === '90' }" for="call-time-90">90分後</label>

								<input type="radio" class="btn-check" name="call_time" id="call-time-other"
									value="other" v-model="selectedMinutes" @change="selectedDateTime = null" />
								<label
									class="btn btn-outline-deet-dark fs-4 d-flex align-items-center justify-content-center px-4 py-3"
									:class="{ 'checked': selectedMinutes === 'other' }"
									for="call-time-other">それ以外</label>
							</div>

							<div class="mt-3 fs-4" v-if="formattedDateTime || selectedMinutes === 'other'">
								<div class="d-flex align-items-stretch">
									<input type="datetime-local" id="datePicker" class="form-control"
										v-model="formattedDateTime" @change="selectedMinutes = null" 
									:class="{'picked':formattedDateTime}"
										/>
								</div>
							</div>
						</div>

					</div>


					<!-- call duration -->
					<div class="col-12">
						<label for="call-duration" class="fs-4">何時間ご利用しますか？</label>
						<div class="call-duration-group">
							<div class="d-flex gap-2" id="call-duration">
								<input type="radio" class="btn-check" name="call_duration" id="call-duration-1"
									value="1" v-model="selectedDuration" />
								<label class="btn btn-outline-deet-dark fs-4 px-4 py-3"
									:class="{ 'checked': selectedDuration === '1' }" for="call-duration-1">1時間</label>

								<input type="radio" class="btn-check" name="call_duration" id="call-duration-2"
									value="2" v-model="selectedDuration" />
								<label class="btn btn-outline-deet-dark fs-4 px-4 py-3"
									:class="{ 'checked': selectedDuration === '2' }" for="call-duration-2">2時間</label>

								<input type="radio" class="btn-check" name="call_duration" id="call-duration-3"
									value="3" v-model="selectedDuration" />
								<label class="btn btn-outline-deet-dark fs-4 px-4 py-3"
									:class="{ 'checked': selectedDuration === '3' }" for="call-duration-3">3時間</label>

								<select @change="clearRadioButtons" v-model="selectedDuration"
									class="form-select select-duration-time fs-4"
									:class="{ 'checked': ['4', '5', '6', '7', '8'].includes(selectedDuration) }">
									<option value="" disabled selected>4時間以上</option>
									<option value="4">4時間</option>
									<option value="5">5時間</option>
									<option value="6">6時間</option>
									<option value="7">7時間</option>
									<option value="8">8時間</option>
								</select>
							</div>
						</div>
					</div>




        <div class="col-12 mt-3">
          <label for="" class="fs-4 mb-2">合流場所</label>
          <input type="text" class="form-control" placeholder="店名または位置情報のURLを入力してください"
                 v-model="bookingDetails.place_street">
          <p class="mt-2">
            ※鍵付きの個室やレンタルスペース・マンションやホテル等の個室利用、または「合流場所」が「合流エリア」と異なる場合、女性会員とは合流できず100%有償でのキャンセルとなります。
          </p>
        </div>

        <div class="col-12 mt-2 mb-4">
          <label for="" class="fs-4 mb-2">予約名</label>
          <input type="text" class="form-control" placeholder="予約名を入力してください" v-model="bookingDetails.place_desc">
        </div>

        <!-- complete btn -->
        <div
            class="col-md-auto justify-content-center align-self-center text-center ms-4 mb-3"
        >
          <button type="button" class="btn deet-btn full-btn m-0" @click="navigateToComplete" :disabled="submitBtnClicked">
            この内容で確定する
          </button>
        </div>
      </div>
    </div>


    <!-- <div> -->
    <!-- 以下、呼ぶからのオーダーの場合 -->
    <!-- </div> -->
  </div>


</template>

<style scoped>
input[type=datetime-local]::-webkit-calendar-picker-indicator {
  position: absolute;
  width: 100%;
  height: 100%;
  opacity: 0;
}
input[type="datetime-local"]::-webkit-inner-spin-button{
  -webkit-appearance: none;
}
input[type="datetime-local"]::-webkit-clear-button{
  -webkit-appearance: none;
}
input[type="datetime-local"]{
  position: relative;
}
input[type="datetime-local"]::before {
	content: "日付を選択してください";
	position: absolute;
    top: .7rem;
    left: 1.2rem;
	background-color: var(--deet-color-base);
}
input[type="datetime-local"] {
	&.picked::before {
		display: none;
	}
}

.select-duration-time {
  width: auto;
}

label {
		&.checked {
			background-color: var(--deet-color-base) !important;
			border-color: var(--deet-color-gold) !important;
    		color: var(--deet-color-gold) !important;
		}
  }

.direct-order,
.request-order {
  display: none;
}

.direct {

.direct-order {
  display: block;
}

.request-order {
  display: none;
}

}
.request {

.direct-order {
  display: none;
}

.request-order {
  display: block;
}

}


.age,
.place {
  color: #c1a06c;
}

.container {
  /* max-width: 1620px; */
}

.content {
  /* max-width: 1620px; */
  /* width: 100%; */
}

.section-title {
  /* color: #545454; */
  /* font-size: 22px; */
  /* font-weight: 700; */
  /* margin-bottom: 0px; */
}

hr {
  margin: 6px 0px;
}

/* cast info section */
.cast-image-container {
  /* position: relative; */
  /* width: 262px; */
  /* height: 212px; */
  /* overflow: hidden; */
  /* border-radius: 4px; */
}

.cast-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.online-status {
  position: absolute;
  top: 10px;
  right: 22px;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background-color: #38ff60;
}

.info-name {
  font-size: 15px;
  font-weight: 700;
  margin-bottom: 14px;
}

.info-price {
  font-size: 12px;
  margin-bottom: 10px;
}

.rank-tag {
  /* font-size: 10px; */
  /* font-weight: 700; */
  /* background: #ffb300; */
  /* border: none; */
  /* border-radius: 3px; */
  /* color: #ffffff; */
  /* padding: 4px 13px; */
  /* margin-right: 13px; */
}

.tag {
  /* font-size: 10px; */
  /* font-weight: 700; */
  /* background: #ffffff; */
  /* border: #707070 solid 1px; */
  /* border-radius: 3px; */
  /* color: #545454; */
  /* padding: 3px 10px; */
  /* margin-right: 8px; */
}

.info-comment {
  /* font-size: 12px; */
  /* color: #545454; */
  /* margin-bottom: 30px; */
}

.basic-info p {
  /* font-size: 12px; */
}

/* buttons */
.call-btn {
  /* padding: 10px 70px; */
  /* background-color: #ff4600; */
  /* color: #ffffff; */
  /* border: none; */
  /* border-radius: 5px; */
  /* cursor: pointer; */
}

.call-btn:hover {
  /* opacity: 0.8; */
}

.favorite-btn {
  padding: 6px 11px 4px 11px;
  background-color: #ffffff;
  color: #545454;
  font-size: 12px;
  border: 1px solid #707070;
  border-radius: 14px;
  cursor: pointer;
  align-items: center;
}

.favorite-btn:hover {
  opacity: 0.8;
}

.favorite-btn-content {
  display: flex;
  align-items: center;
  justify-content: center;
}

.icon-star {
  margin-right: 5px;
  font-size: 14px;
  color: #000000;
  transition: color 0.3s ease;
}

.icon-star.is-favorite {
  color: #ffd700;
}

.favorite-text {
  line-height: 1;
}

.chat-btn {
  padding: 10px 40px;
  background-color: #717171;
  color: #ffffff;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.chat-btn:hover {
  /* opacity: 0.8; */
}

/* price */
.price-summary {
  /* max-width: 180px; */
  /* font-size: 10px; */
}

@media (max-width: 768px) {
  .row {
    /* flex-direction: column; */
  }

  .cast-image-container {
    /* width: 100%; */
    /* max-width: 262px; */
    /* height: auto; */
    /* aspect-ratio: 262 / 212; */
    /* margin: 0 auto; */
  }

  .call-btn,
  .favorite-btn,
  .chat-btn {
    width: 100%;
  }

  .ms-4 {
    margin-left: 0 !important;
  }

  .price-summary {
    max-width: 100%;
  }
}
</style>
