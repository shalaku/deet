<script setup>
import { customerAPI } from '@/libs/api/customer-api';
import { CModal, CModalBody, CModalHeader } from '@coreui/vue';
import Swal from 'sweetalert2';
import { computed, ref, watch } from 'vue';
const router = useRouter();
// Form data object
const formData = ref({
    municipalities: '',
    area_name: '',
    number_of_people: null,
    requested_start_time: '',
    requested_matching_term: '',
    cast_age_min: null,
    cast_age_max: null,
    cast_height_min: null,
    cast_height_max: null,
    cast_rank: [],
    cast_tag: [],
    mid_night_fee: 0,
    laterStartMeetingMinute: '',
});

// フィールド名と日本語ラベルのマッピング
const fieldLabels = {
    municipalities: 'エリア',
    area_name: 'エリア詳細',
    number_of_people: '人数',
    requested_start_time: '開始時間（何分後に合流しますか？）',
    requested_matching_term: 'マッチング時間（何時間ご利用しますか？）',
    cast_age_min: '女性会員年齢（最小）',
    cast_age_max: '女性会員年齢（最大）',
    cast_height_min: '女性会員身長（最小）',
    cast_height_max: '女性会員身長（最大）',
    cast_rank: '女性会員クラス',
    cast_tag: '女性会員特徴（詳細条件）',
    mid_night_fee: '深夜手当',
    laterStartMeetingMinute: '後開始ミーティング分',

};

// 画像ダミーデータ
const dummyImageSrc =
	'data:image/gif;base64,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';


// 呼ぶボタンのクリックハンドラ
const submitCall = () => {
    console.log('呼ぶボタンがクリックされました');
    const optionalFields = ['cast_tag', 'cast_rank']; // 必須でないフィールド名

    const missingFields = Object.entries(formData.value)
        .filter(([key, value]) => 
            !optionalFields.includes(key) && (value === null || value === '' || (Array.isArray(value) && value.length === 0))
        )
        .map(([key]) => fieldLabels[key] || key); // 日本語ラベルに変換

    if (missingFields.length > 0) {
        Swal.fire({
            icon: 'warning',
            title: '必須項目が未入力です', 
            text: `次の項目を入力してください: ${missingFields.join(', ')}`,
        });
    } else {
		updateTotalDateTime();
        console.log('呼ぶボタンがクリックされました');
        console.log(formData);
        const queryString = new URLSearchParams({
            ...formData.value,
            selected_cast_ids: selectedCastIds.value.join(',') // 選択された女性会員のIDをカンマ区切りで追加
        }).toString();
        router.push(`/user/random_call_cast?order_type=request&${queryString}`);
    }
};


const tagPrices = {
    'BLACK': 10000,
    'PLATINUM': 8000,
    'GOLD': 6000,
};
const selectedMinutes = ref(null);
const selectedDateTime = ref(null);
const selectedDuration = ref(''); 

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

// Helper function to format date as 'YYYY-MM-DD HH:MM:SS'
const formatDateTime = (date) => {
	const pad = (num) => String(num).padStart(2, '0');
	return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}:${pad(date.getSeconds())}`;
};

// Function to update total date-time
const updateTotalDateTime = () => {
	let dateTime;

	console.log(selectedDateTime.value);
	console.log(selectedMinutes.value);

	if (selectedDateTime.value) {
		// User has chosen a specific date-time in datetime-local input
		dateTime = new Date(selectedDateTime.value);
		formData.value.requested_start_time = formatDateTime(dateTime);
		formData.value.laterStartMeetingMinute = 0;
	} else if (selectedMinutes.value) {
		// User has selected a delay in minutes
		dateTime = new Date(); 
		dateTime.setMinutes(dateTime.getMinutes() + parseInt(selectedMinutes.value));
		formData.value.requested_start_time = formatDateTime(dateTime);
		formData.value.laterStartMeetingMinute = selectedMinutes.value;
	}
	console.log(formData.value.requested_matching_term);
	// Check for the midnight fee based on calculated hour
	const start_hour = dateTime.getHours();

	// Check if requested_matching_term exists, if not set to '0:00'
	const requestedMatchingTerm = formData.value.requested_matching_term ? formData.value.requested_matching_term : '0:00';
	const [hours, minutes] = requestedMatchingTerm.split(':');
    // 先頭の0を削除して整数に変換
    const formattedHours = parseInt(hours, 10);

	dateTime.setHours(dateTime.getHours() + formattedHours);
	const end_hour = dateTime.getHours();

	// Set mid_night_fee to 5000 if either start_hour or end_hour is between 0 and 5
	formData.value.mid_night_fee = (start_hour >= 0 && start_hour < 5) || (end_hour >= 0 && end_hour < 5) ? 5000 : 0;

	console.log(end_hour);
};

        

// Watch for changes to the selected duration
watch(selectedDuration, (newDuration) => {
	if (newDuration) {
		const hours = String(newDuration).padStart(2, '0');
		const formattedDuration = `${hours}:00`;
		formData.value.requested_matching_term = formattedDuration;
		console.log(`Updated FormData: requested_matching_term: ${formattedDuration}`);
	} else {
		console.log('No duration selected');
	}
});


watch([selectedDateTime, selectedMinutes], updateTotalDateTime);
		
//女性会員特徴の特徴選択ボタンを押したときの機能、必要なければ削除
// スタイル
const dummyTags = ref([
	'スレンダー',
	'グラマー',
	'高身長',
	'小柄',
]);

// 顔立ち
const dummyTags2 = ref([
	'童顔',
	'ナチュラル',
	'可愛い系',
	'綺麗系',
]);

// 系統
const dummyTags3 = ref([
	'ギャル',
	'清楚',
	'モデル系',
	'アイドル系',
	'ハーフ',
]);

// シチュエーション
const dummyTags4 = ref([
	'プライベート',
	'接待',
	'わいわい',
	'しっとり',
	'カラオケ',
	'タバコNG',
	'マナー重視',
	'誕生日会',
	'ゴルフ',
	'ご飯',
	'旅行',
]);

// 女性会員スキル
const dummyTags5 = ref([
	'盛り上げ上手',
	'歌上手',
	'お酒好き',
	'英語が話せる',
	'中国語が話せる',
	'韓国語が話せる',
]);


const selectedTags = ref([]);
const isModalOpen = ref(false);

const toggleTag = (tag) => {
	const index = selectedTags.value.indexOf(tag);
	if (index === -1) {
		selectedTags.value.push(tag);
		formData.value.cast_tag.push(tag);
	} else {
		selectedTags.value.splice(index, 1);
		formData.value.cast_tag.splice(index, 1);

	}
};
const removeTag = (tag) => {
	const index = selectedTags.value.indexOf(tag);
	if (index !== -1) {
		selectedTags.value.splice(index, 1);
	}
};

const ageRange = Array.from({ length: 21 }, (_, i) => i + 20);
const heightRange = Array.from({ length: 9 }, (_, i) => i*5 + 140);

const minAge = ref(ageRange[0]);
const maxAge = ref(ageRange[10]);
const minHeight = ref(heightRange[0]);
const maxHeight = ref(heightRange[4]);

// Set initial values in formData on component load
formData.value.municipalities = "東京都";
formData.value.area_name = "Bar V（六本木）";
formData.value.number_of_people = "";
// formData.value.selectedDuration = "";
formData.value.cast_age_min = minAge.value;
formData.value.cast_age_max = maxAge.value;
formData.value.cast_height_min = minHeight.value;
formData.value.cast_height_max = maxHeight.value;


const maxAgeOptions = computed(() => {
	return minAge.value ? ageRange.filter((age) => age > minAge.value) : ageRange;
});
const maxHeightOptions = computed(() => {
	return minHeight.value ? heightRange.filter((height) => height > minHeight.value) : heightRange;
});
watch(minAge, (newMinAge) => {
	formData.value.cast_age_min = newMinAge;
});

watch(maxAge, (newMaxAge) => {
	formData.value.cast_age_max = newMaxAge;
});

// Watchers for height
watch(minHeight, (newMinHeight) => {
	formData.value.cast_height_min = newMinHeight;
});

watch(maxHeight, (newMaxHeight) => {
	formData.value.cast_height_max = newMaxHeight;
});

const handleMinAgeChange = () => {
	if (maxAge.value && maxAge.value <= minAge.value) {
		maxAge.value = null;
	}
};

const handleMinHeightChange = () => {
	if (maxHeight.value && maxHeight.value <= minHeight.value) {
		maxHeight.value = null;
	}
};



const clearRadioButtons = () => {
    if (event.target.value !== "") {
    	const radioButtons = document.querySelectorAll('input[name="call_duration"]');
    	radioButtons.forEach(radio => {
        	radio.checked = false;
        });
    }
};

const castList = ref([]); // APIから取得したデータを格納する変数
const selectedCastIds = ref([]); // 選択された女性会員のIDを格納する配列
// Update profile picture mutation
const submitRecommendedSearch = async () => {
	castList.value = [];
	// console.log('検索ボタンがクリックされました');
	// console.log(formData.value.cast_rank);
    const optionalFields = ['cast_tag', 'cast_rank']; // 必須でないフィールド名

    const missingFields = Object.entries(formData.value)
        .filter(([key, value]) => 
            !optionalFields.includes(key) && (value === null || value === '' || (Array.isArray(value) && value.length === 0))
        )
        .map(([key]) => fieldLabels[key] || key); // 日本語ラベルに変換

    if (missingFields.length > 0) {
		isRecommendModalVisible.value = false;      
		Swal.fire({
            icon: 'warning',
            title: '必須項目が未入力です', 
            text: `次の項目を入力してください: ${missingFields.join(', ')}`,
        });
    } 

	const params = {
        // city: selectedLocation.value,
        // prefecture: "東京都",
        height: minHeight.value + '-' + maxHeight.value,
        age: minAge.value + '-' + maxAge.value,
		tag_of_spec: selectedTags.value.join(','),
		rank: formData.value.cast_rank.join(','), // 配列をカンマ区切りの文字列に変換
		// ranks: selectedRtags.value,
		// status_id: 1,	
    };

	try {
		// console.log(params);
		// Call the API and await the response		
		const response = await customerAPI.getCast(params);
		castList.value = response.data.casts; 
		// console.log('API response:', castList.value);
		
		// Handle the response, e.g., update data in the component
		// this.results = response.data; // Uncomment if you want to update a data property with the response
	} catch (error) {
		console.error('Error fetching cast data:', error);
	}
};

// 女性会員の選択状態をトグルする関数
const toggleSelect = (castId) => {
    if (selectedCastIds.value.includes(castId)) {
        selectedCastIds.value = selectedCastIds.value.filter(id => id !== castId); // 削除
    } else {
        selectedCastIds.value.push(castId); // 追加
    }
};

const isRecommendModalVisible = ref(false);
// const handleImageClick = (imageId: number) => {
	// isRecommendModalVisible.value = false;
	// makeProfilePicture({ ownerId: data.id, imageId });
// };


</script>

<template>
	<div class="container-flund">
		<div class="content p-4">
			<h2 class="section-title mb-4 pt-3">おまかせで呼ぶ</h2>
			<form action="">
				<div class="row g-4 fs-4">
					<!-- area -->
					<div class="col-md-2">
						<div>
							<label for="area">エリア</label>
							<select v-model="formData.municipalities" id="area" class="form-select fs-4">
								<option disabled value="">選択ください</option>
								<option>東京都</option>
							</select>
						</div>
					</div>
					<!-- area detail -->
					<div class="col-md-2">
						<div>
							<label for="subArea">エリア詳細</label>
							<select v-model="formData.area_name" id="subArea" class="form-select fs-4">
								<option disabled value="">選択ください</option>
								<option>Bar V（六本木）</option>
							</select>
						</div>
					</div>
					<!-- cast count -->
					<div class="col-md-1">
						<div>
							<label for="cast-count">人数</label>
							<select v-model="formData.number_of_people" id="cast-count" class="form-select fs-4">
								<option disabled value="">選択ください</option>
								<option v-for="n in 9" :key="n + 1" :value="n + 1">{{ n + 1 }}</option>
							</select>
						</div>
					</div>
					<!-- call time -->
					<div class="col-md-4">
						<label for="call-time-group">何分後に合流しますか？</label>
						<div id="call-time-group">
							<div class="d-flex gap-2 align-items-stretch">
								<input type="radio" class="btn-check" name="call_time" id="call-time-30" value="30"
									v-model="selectedMinutes" @change="selectedDateTime = null" />
								<label
									class="btn btn-outline-deet-dark fs-4 d-flex align-items-center justify-content-center"
									:class="{ 'checked': selectedMinutes === '30' }" for="call-time-30">30分後</label>

								<input type="radio" class="btn-check" name="call_time" id="call-time-60" value="60"
									v-model="selectedMinutes" @change="selectedDateTime = null" />
								<label
									class="btn btn-outline-deet-dark fs-4 d-flex align-items-center justify-content-center"
									:class="{ 'checked': selectedMinutes === '60' }" for="call-time-60">60分後</label>

								<input type="radio" class="btn-check" name="call_time" id="call-time-90" value="90"
									v-model="selectedMinutes" @change="selectedDateTime = null" />
								<label
									class="btn btn-outline-deet-dark fs-4 d-flex align-items-center justify-content-center"
									:class="{ 'checked': selectedMinutes === '90' }" for="call-time-90">90分後</label>

								<input type="radio" class="btn-check" name="call_time" id="call-time-other"
									value="other" v-model="selectedMinutes" @change="selectedDateTime = null" />
								<label
									class="btn btn-outline-deet-dark fs-4 d-flex align-items-center justify-content-center"
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
					<div class="col-md-3">
						<label for="call-duration">何時間ご利用しますか？</label>
						<div class="call-duration-group">
							<div class="d-flex gap-2" id="call-duration">
								<input type="radio" class="btn-check" name="call_duration" id="call-duration-1"
									value="1" v-model="selectedDuration" />
								<label class="btn btn-outline-deet-dark fs-4"
									:class="{ 'checked': selectedDuration === '1' }" for="call-duration-1">1時間</label>

								<input type="radio" class="btn-check" name="call_duration" id="call-duration-2"
									value="2" v-model="selectedDuration" />
								<label class="btn btn-outline-deet-dark fs-4"
									:class="{ 'checked': selectedDuration === '2' }" for="call-duration-2">2時間</label>

								<input type="radio" class="btn-check" name="call_duration" id="call-duration-3"
									value="3" v-model="selectedDuration" />
								<label class="btn btn-outline-deet-dark fs-4"
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

					<div class="col-md-2 d-flex flex-column gap-3">
						<!-- residence -->
						<!-- <div> -->
						<!-- <label for="residence">居住地</label> -->
						<!-- <select id="residence" name="residence" class="form-select"> -->
						<!-- <option disabled value="">選択ください</option> -->
						<!-- <option></option> -->
						<!-- </select> -->
						<!-- </div> -->
						<!-- age-range -->
						<div>
							<label for="age-range">年齢</label>
							<div class="d-flex align-items-center gap-2" id="age-range">
								<select v-model="minAge" @change="handleMinAgeChange" class="form-select fs-4">
									<option disabled value="">選択ください</option>
									<option v-for="age in ageRange" :key="age" :value="age">{{ age }}</option>
								</select>
								<span>-</span>
								<select v-model="maxAge" class="form-select fs-4">
									<option disabled value="">選択ください</option>
									<option v-for="age in maxAgeOptions" :key="age" :value="age">{{ age }}</option>
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-2 d-flex flex-column gap-3">
						<!-- birthplace -->
						<!-- <div> -->
						<!-- <label for="birthplace">出身地</label> -->
						<!-- <select id="birthplace" name="birthplace" class="form-select"> -->
						<!-- <option disabled value="">選択ください</option> -->
						<!-- <option></option> -->
						<!-- </select> -->
						<!-- </div> -->
						<!-- height-range -->
						<div>
							<label for="height-range">身長</label>
							<div class="d-flex align-items-center gap-2" id="height-range">
								<select v-model="minHeight" @change="handleMinHeightChange" class="form-select fs-4">
									<option disabled value="">選択ください</option>
									<option v-for="height in heightRange" :key="height" :value="height">{{ height }}
									</option>
								</select>
								<span>-</span>
								<select v-model="maxHeight" class="form-select fs-4">
									<option disabled value="">選択ください</option>
									<option v-for="height in maxHeightOptions" :key="height" :value="height">{{
										height }}</option>
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="d-flex flex-column gap-3">
							<div class="d-flex flex-column">
								<label class="" for="">詳細条件</label>
								<button type="button" class="btn btn-outline-deet-dark fs-4"
									@click="isModalOpen = true">
									選択する
								</button>
							</div>
							<div class="d-flex flex-wrap gap-2 align-items-center">
								<div v-for="tag in selectedTags" :key="tag"
									class="badge badge-deet-dark bg-secondary d-flex align-items-center fs-4 fw-normal">
									{{ tag }}
									<!-- <i class="icon-x d-block ms-2" @click="removeTag(tag)"></i> -->
								</div>
							</div>
						</div>

						<!--  cast feature tag select Modal (必要なかったら削除) -->
						<div v-if="isModalOpen" class="modal" tabindex="-1" role="dialog">
							<div class="modal-dialog" role="document">
								<div class="modal-content">
									<div class="modal-header justify-content-between">
										<h5 class="modal-title fs-3">詳細条件</h5>
										<i type="button" class="icon-x d-block fs-3" @click="isModalOpen = false"></i>
									</div>
									<div class="modal-body">
										<div class="d-flex flex-wrap gap-2">
											<button type="button" v-for="tag in dummyTags" :key="tag"
												class="btn btn-outline-deet-dark fs-4"
												:class="{ active: selectedTags.includes(tag) }" @click="toggleTag(tag)">
												{{ tag }}
											</button>
											<button type="button" v-for="tag in dummyTags2" :key="tag"
												class="btn btn-outline-deet-dark fs-4"
												:class="{ active: selectedTags.includes(tag) }" @click="toggleTag(tag)">
												{{ tag }}
											</button>
											<button type="button" v-for="tag in dummyTags3" :key="tag"
												class="btn btn-outline-deet-dark fs-4"
												:class="{ active: selectedTags.includes(tag) }" @click="toggleTag(tag)">
												{{ tag }}
											</button>
											<button type="button" v-for="tag in dummyTags4" :key="tag"
												class="btn btn-outline-deet-dark fs-4"
												:class="{ active: selectedTags.includes(tag) }" @click="toggleTag(tag)">
												{{ tag }}
											</button>
											<button type="button" v-for="tag in dummyTags5" :key="tag"
												class="btn btn-outline-deet-dark fs-4"
												:class="{ active: selectedTags.includes(tag) }" @click="toggleTag(tag)">
												{{ tag }}
											</button>
										</div>
									</div>
									<div class="modal-footer">
										<button type="button"
											class="btn btn-deet-transparent deet-btn-clickable fs-5 fw-bold"
											@click="isModalOpen = false">
											閉じる
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-3 d-flex flex-column gap-3">
						<!-- cast rank -->
						<div class="col-md-4 cast-rank-group">
							<label for="cast-rank">希望クラス</label>
							<div class="d-flex gap-2" id="cast-rank">
								<input type="checkbox" class="btn-check" id="cast-rank-c" value="GOLD"
									v-model="formData.cast_rank" />
								<label class="btn btn-outline-deet-dark fs-4"
									:class="{ 'checked': formData.cast_rank.includes('GOLD') }"
									for="cast-rank-c">GOLD</label>

								<input type="checkbox" class="btn-check" id="cast-rank-b" value="PLATINUM"
									v-model="formData.cast_rank" />
								<label class="btn btn-outline-deet-dark fs-4"
									:class="{ 'checked': formData.cast_rank.includes('PLATINUM') }"
									for="cast-rank-b">PLATINUM</label>
								<input type="checkbox" class="btn-check" id="cast-rank-a" value="BLACK"
									v-model="formData.cast_rank" />
								<label class="btn btn-outline-deet-dark fs-4"
									:class="{ 'checked': formData.cast_rank.includes('BLACK') }"
									for="cast-rank-a">BLACK</label>

							</div>
						</div>

						<!-- vacant schedule -->
						<!-- <div> -->
						<!-- <label for="vacant-schedule">空き日程</label> -->
						<!-- <div class="d-flex align-items-center gap-1" id="vacant-schedule"> -->
						<!-- <input type="date" class="form-control" /> -->
						<!-- <span>-</span> -->
						<!-- <input type="date" class="form-control" /> -->
						<!-- </div> -->
						<!-- </div> -->
					</div>

					<!-- search button -->
					<div class="col-12 text-end">
						<p type="submit" class="btn search-btn deet-btn"
							@click="() => { isRecommendModalVisible = true; submitRecommendedSearch(); }">次へ進む</p>
					</div>


					<!-- <div class="col-md-2 d-flex align-items-end text-end"> -->
					<!-- <button type="submit" class="btn deet-btn" @click.prevent="submitCall"> -->
					<!-- この条件で呼ぶ -->
					<!-- </button> -->
					<!-- </div> -->
				</div>
			</form>
		</div>
	</div>

	<CModal size="lg" backdrop="static" alignment="center" :visible="isRecommendModalVisible"
		aria-labelledby="ProfilePictureUpdateLabel" content-class-name="rounded-0" v-on:close="
			() => {
				isRecommendModalVisible = false;
			}
		">
		<CModalHeader class="border-0 pb-0 text-center">
			<span class="fs-3 p-2 mt-2">優先マッチング</span>
		</CModalHeader>
		<CModalBody>
			<div class="d-flex flex-wrap justify-content-around ps-4 pe-4 fs-5"
				style="background-color: var(--deet-color-base); border-radius: 6px; padding: 14px 10px 0px 10px;">
				<p>優先マッチング希望をすると、女性会員に合流したい気持ちが届きますが、マッチングできない場合もあります。</p>
				<p>優先マッチング希望の成立時には1人当たり別途30分毎1,500P発生いたします。 </p>
			</div>
			<p class="ms-3 mt-3 fs-4">優先マッチングできる女性会員から選ぶ</p>
			<div class="d-flex flex-wrap justify-content-around cast-list-wappeer">
				
				<div v-if="castList.length == 0" class="text-center py-5 my-5">
					<LoadingLine></LoadingLine>
				</div>
				
				<div v-for="cast in castList" :key="cast.cast.id"
					:class="['cast-card mb-4', { selected: selectedCastIds.includes(cast.cast.id) }]"
					@click="toggleSelect(cast.cast.id)">
					<div class="cast-image-container">
						<img :src="cast?.images[0]?.image_url || dummyImageSrc" alt="cast image"
							class="uploaded-image" />
						<!-- <div class="favorite-icon" @click.stop="toggleFavorite(cast)"> -->
						<!-- <i :class="['icon-star', { favorite: cast.isFavorite }]"></i> -->
						<!-- </div> -->
					</div>

					<div class="cast-info">
						<div class="cast-details">
							<div class="d-flex justify-content-between">
								<span class="cast-age fs-4">{{ new Date().getFullYear() - new
									Date(cast?.cast.birthday).getFullYear() }}歳</span>
								<span class="cast-area fs-4 text-end">{{ cast.cast.prefecture }}</span>
							</div>
							<div class="cast-name fs-4">{{ cast.cast.nickname }}</div>
						</div>
						<div class="cast-comment">{{ cast?.cast.my_comment }}</div>
						<div class="cast-price fs-4 text-end fw-normal">
							<!-- {{ cast.price.toLocaleString() }}P / 30min -->
							{{ (parseInt(cast.cast.basic_point_price,"10") || 0).toLocaleString("ja-JP",{
							maximumFractionDigits: 0
							}) }}P / 30min
						</div>
					</div>
				</div>
			</div>
			<div class="d-flex flex-wrap justify-content-around">
				<button type="submit" class="btn search-btn deet-btn" @click.prevent="submitCall">次に進む</button>
			</div>
		</CModalBody>
	</CModal>


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

#call-time-group,
#call-duration {
	.btn {
		padding: 9.5px 18.7px;
	}
}
.cast-list-wappeer {
	/* min-height: calc(90vh - var(--header-height)*2 - var(--footer-menu-height)); */
	max-height: calc(85vh - 10rem - var(--header-height)*2 - var(--footer-menu-height));
	overflow-y: auto;
}
.cast-card {
	border: 2px solid transparent;
	&.selected {
		border-color: var(--deet-color-gold);
	}
}

#call-time-group,
.call-duration-group,
.cast-rank-group {
	label {
		background-color: var(--deet-color-cotent-base) !important;
    	border-color: var(--deet-color-gold-dark) !important;
		&.checked {
			background-color: var(--deet-color-base) !important;
			border-color: var(--deet-color-gold) !important;
    		color: var(--deet-color-gold) !important;
		}
		&.wraplabel {
			color: #FFF !important;
			position: absolute;
			display: block;
			background-color: var(--deet-color-base) !important;
			border-radius: 6px;
			padding: 10px 10px;
			margin: 1px;
		}
	}
	select {
		&.checked {
			box-shadow: none !important;
			background-color: var(--deet-color-base) !important;
            border-color: var(--deet-color-gold) !important;
            color: var(--deet-color-gold) !important;
		}
	}	
}



.container {
	max-width: 1620px;
}

.content {
	max-width: 1620px;
	width: 100%;
	/* padding: 60px; */
}

.section-title {
	/* color: #545454; */
	font-size: 22px;
	font-weight: 700;
	margin-bottom: 0px;
}

.font-12px {
	font-size: 12px;
}

/* cast feature tag select Modal */
.modal {
	display: block;
	background-color: rgba(0, 0, 0, 0.5);
}

.badge {
	font-size: 9px;
	/* padding: 3px 10px; */
}

.feature-select-btn {
	width: auto;
	align-self: flex-start;
}

.btn-close {
	font-size: 8px;
	padding: 4px;
}

/* call btn */
.call-btn {
	color: #ffffff;
	background-color: #ff4600;
	border: none;
	border-radius: 5px;
	padding: 15px 40px;
	margin-left: auto;
}

.call-btn:hover {
	background-color: #ff6a33;
}

.call-btn:active {
	background-color: #e63d00;
}

.btn-deet-transparent,
.btn-outline-deet,
.btn-outline-deet-dark {
	padding: 9.5px 20px;
}

.select-duration-time {
	width: 12rem;
	background-color: var(--deet-color-cotent-base);
    border-color: var(--deet-color-gold-dark);
    color: var(--deet-color-gold);
	text-align: center;
}

@media (max-width: 767px) {
	.row > .col-md-6 {
		margin-top: 1rem;
	}
}
</style>
