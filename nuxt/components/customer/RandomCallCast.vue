<script setup>
import { customerAPI } from '@/libs/api/customer-api';
import {
	formatDateJpShort,
	formatNumber,
	getRankPointValue,
} from '@/libs/utilities';
import { ref } from 'vue';
import PaymentWithStripe from './modal/PaymentWithStripe.vue';
import PurchasePointsModal from './modal/PurchasePointsModal.vue';

const route = useRoute();
const router = useRouter();
// 画像ダミーデータ
const dummyImageSrc =
	'data:image/gif;base64,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';

const callCastRequestInfo = ref({});
const callCastRequestInfoParams = ref({});

const submitBtnClicked = ref(false);

const castList = ref([]); // 女性会員リストを格納する変数

const showPurchasePointsModal = ref(false);
const showStripePaymentModal = ref(false);
const selectedPoints = ref(0);

const closePurchasePointsModal = () => {
	showPurchasePointsModal.value = false;
};

const closeStripePaymentModal = () => {
	showStripePaymentModal.value = false;
	selectedPoints.value = 0;
};

const setPointsAndOpenStripeModal = (points) => {
	showPurchasePointsModal.value = false;
	showStripePaymentModal.value = true;
	selectedPoints.value = points;
};

// Extract query parameters from the route
const {
	municipalities,
	area_name,
	number_of_people,
	requested_start_time,
	requested_matching_term,
	cast_age_min,
	cast_age_max,
	cast_height_min,
	cast_height_max,
	cast_rank,
	cast_tag,
	laterStartMeetingMinute,
	mid_night_fee,
	selected_cast_ids,
} = route.query;

// Set the reactive object with query parameters
callCastRequestInfo.value = {
	prefecture: municipalities, // Adjust this based on your data
	areaName: area_name,
	numberOfPeople: number_of_people,
	requested_start_time: requested_start_time, // Assuming this holds the duration
	minAge: cast_age_min,
	maxAge: cast_age_max,
	minHeight: cast_height_min,
	maxHeight: cast_height_max,
	castRank: cast_rank ? cast_rank.split(',') : [], // Split by comma if it's a string
	Tags: cast_tag ? cast_tag.split(',') : [], // Split by comma if it's a string
	requested_matching_term: requested_matching_term, // Adjust accordingly if needed
	mid_night_fee: mid_night_fee,
	laterStartMeetingMinute: laterStartMeetingMinute,
	selected_cast_ids: selected_cast_ids,
};

// const rankPrices = {
//     'SS': 12500,
//     'S': 7500,
//     'A': 5000,
// };
const basicPrice = computed(() => {
	let basicPoint = 10000;

	// console.log(callCastRequestInfo.value.castRank);
	if (callCastRequestInfo.value.castRank.length === 0) {
		basicPoint = 6000;
	} else {
		callCastRequestInfo.value.castRank.forEach((rank) => {
			if (parseInt(getRankPointValue(rank)) < basicPoint) {
				basicPoint = getRankPointValue(rank);
			}
		});
	}

	return basicPoint;
});

function calculateRankSubtotals(castList) {
	const rankSubtotals = {};
	let totalPoints = 0;

	castList.forEach((castItem) => {
		const rank = castItem.cast.rank;
		const points = parseInt(getRankPointValue(rank));
		totalPoints += parseInt(points);

		if (rankSubtotals[rank]) {
			rankSubtotals[rank].points += points;
			rankSubtotals[rank].count += 1;
		} else {
			rankSubtotals[rank] = { points: points, count: 1 };
		}
		rankSubtotals[rank].firstletter = rank.charAt(0); // rankの最初の文字をセット
	});

	return { rankSubtotals, totalPoints };
}

// console.log(basicPrice.value);
const displayHour = computed(() => {


	if (callCastRequestInfo.value.requested_matching_term) {
		const hour =
			callCastRequestInfo.value.requested_matching_term.split(':')[0]; // This will get the hour part
		return parseInt(hour, 10); // Convert to integer to remove leading zero
	}
	return 0; // Default value if duaringTime is not set
});

// console.log("call list form",callCastRequestInfo.value)

// お気に入りの状態を管理するref
const isFavorite = ref(false);

const navigateToCompleteRandomRequest = async () => {
	const response = await customerAPI.getCustomerProfile();
	const result = calculateTotal();
	if (response.data.points_holded < result) {
		showPurchasePointsModal.value = true;
		return;
	}

	submitBtnClicked.value = true;


	const customerId = response.data.id;
	callCastRequestInfoParams.value = {
		customer_id: customerId,
		status: 501,
		municipalities: municipalities, // Adjust this based on your data
		area_name: area_name,
		number_of_people: number_of_people,
		requested_start_time: requested_start_time, // Assuming this holds the duration
		cast_age_min: cast_age_min,
		cast_age_max: cast_age_max,
		cast_height_min: cast_height_min,
		cast_height_max: cast_height_max,
		cast_rank: cast_rank ? cast_rank.split(',') : [], // Split by comma if it's a string
		cast_tag: cast_tag ? cast_tag.split(',') : [], // Split by comma if it's a string
		requested_matching_term: requested_matching_term, // Adjust accordingly if needed
		laterStartMeetingMinute: laterStartMeetingMinute,
		mid_night_fee: mid_night_fee,
		selected_cast_ids: selected_cast_ids,
	};
	try {
		// console.log(callCastRequestInfoParams.value);
		const response = await customerAPI.requestMatching(
			callCastRequestInfoParams.value,
		);
		// console.log('Order placed successfully:', response.data);
		router.push('/user/complete_call_cast');
	} catch (error) {
		console.error(
			'Error placing order:',
			error.response ? error.response.data : error.message,
		);
	}
};
// const queryParams=route.query;
// const returnUrl = computed(() => `${window.location.origin}/user/random_call_cast?order_type=request&${queryParams}`);
const returnUrl = computed(() => '');
onMounted(() => {
	// console.log('onMounted');
	const query = route.query;
	fetchCasts();

	// Parse `cast_tag` from query string and set Tags array
	if (query.cast_rank) {
		callCastRequestInfo.value.castRank = query.cast_rank.split(','); // Split tags by commas
	}
});
const calculateTotal = () => {
	const displayHourVal = parseFloat(displayHour.value);
	const numberOfPeople = parseInt(callCastRequestInfo.value.numberOfPeople);
	const castLength = parseInt(castList.value.length);
	const basicPriceVal = parseFloat(basicPrice.value);
	const midnightFee = parseFloat(callCastRequestInfo.value.mid_night_fee);

	// Calculate rank subtotals
	const rankSubtotals = calculateRankSubtotals(castList.value);
	const totalPoints = parseFloat(rankSubtotals.totalPoints);
	const result = (displayHourVal *
		(numberOfPeople - castLength) *
		basicPriceVal * 2) +
		(displayHourVal * totalPoints * 2) +
		(midnightFee * numberOfPeople) +
		(castLength * 1500 * displayHourVal * 2);

	return result;
};
// 検索ボタンのクリックハンドラ
const submitSearch = async () => { };

// 非同期処理を行う関数
const fetchCasts = async () => {
	try {
		if (route.query.selected_cast_ids !== '') {
			const params = {
				selected_cast_ids: route.query.selected_cast_ids,
			};
			const response = await customerAPI.getCast(params);
			castList.value = response.data.casts;
			// console.log('API response:', castList.value);
		}
	} catch (error) {
		console.error(
			'Error fetching casts:',
			error.response ? error.response.data : error.message,
		);
	}
};
</script>

<template>
	<div class="container-fluid p-0" :class="route.query.order_type">
		<!-- <div> -->
		<!-- 以下、呼ぶからのオーダーの場合 -->
		<!-- </div> -->

		<!-- from call cast -->
		<div class="content request-order p-4">
			<div class="row g-4 m-0">
				<div class="col-12 mb-3">
					<h2 class="section-title text-center">
						マッチングリクエスト内容のご確認
					</h2>
				</div>
				<hr />
				<div class="col-12 fs-4">
					<div class="d-flex justify-content-between align-items-center mb-2">
						<span>合流エリア</span>
						<span class="fw-bold fs-3">{{ callCastRequestInfo.prefecture }} /
							{{ callCastRequestInfo.areaName }}</span>
					</div>
					<div class="d-flex justify-content-between mb-2">
						<span>女性会員人数</span>
						<div>
							<span v-for="tag in callCastRequestInfo.castRank" :key="tag" class="tag me-0 ms-3">
								{{ tag.charAt(0) }}
							</span>
							<span class="fw-bold fs-3 ms-3">{{ callCastRequestInfo.numberOfPeople }}人</span>
						</div>
					</div>
					<div class="d-flex justify-content-between mb-2">
						<span>合流予定</span>
						<span v-if="callCastRequestInfo.laterStartMeetingMinute > 0" class="fw-bold fs-3">{{
							callCastRequestInfo.laterStartMeetingMinute }}分後</span>
						<span v-else class="fw-bold fs-3">{{
							formatDateJpShort(callCastRequestInfo.requested_start_time)
						}}</span>
					</div>
					<div class="d-flex justify-content-between mb-2">
						<span>年齢</span>
						<span class="fw-bold fs-3">{{ callCastRequestInfo.minAge }}～{{
							callCastRequestInfo.maxAge
						}}歳</span>
					</div>
					<div class="d-flex justify-content-between mb-2">
						<span>身長</span>
						<span class="fw-bold fs-3">{{ callCastRequestInfo.minHeight }}～{{
							callCastRequestInfo.maxHeight
						}}cm</span>
					</div>

					<div class="d-flex justify-content-between mb-2">
						<span>タイプ</span>
						<div v-if="callCastRequestInfo.Tags && callCastRequestInfo.Tags.length">
							<span v-for="tag in callCastRequestInfo.Tags" :key="tag" class="tag me-0 ms-3">
								{{ tag }}
							</span>
						</div>
						<div v-else>
							<span class="fw-bold fs-3">未指定</span>
						</div>
					</div>

					<div class="d-flex justify-content-between mb-2"></div>

					<div class="d-flex justify-content-between mt-3 mb-3">
						<span class="">設定時間</span>
						<span class="fw-bold">{{ displayHour }}時間</span>
					</div>

					<div v-if="castList" class="d-flex flex-wrap justify-content-around">
						<div v-for="cast in castList" :key="cast.cast.id" class="cast-card mb-4">
							<div class="cast-image-container">
								<img :src="cast?.images[0]?.image_url || dummyImageSrc" alt="cast image"
									class="uploaded-image" />
							</div>
							<div class="cast-info">
								<div class="cast-details">
									<div class="d-flex justify-content-between">
										<span class="cast-age fs-4">{{
											new Date().getFullYear() -
											new Date(cast?.cast.birthday).getFullYear()
										}}歳</span>
										<span class="cast-area fs-4 text-end">{{
											cast.cast.prefecture
										}}</span>
									</div>
									<div class="cast-name fs-4">{{ cast.cast.nickname }}</div>
								</div>
								<div class="cast-comment">{{ cast?.cast.my_comment }}</div>
								<div class="cast-price fs-4 text-end fw-normal">
									{{
										(
											parseInt(cast.cast.basic_point_price, '10') || 0
										).toLocaleString('ja-JP', {
											maximumFractionDigits: 0,
										})
									}}P / 30min
								</div>
							</div>
						</div>
					</div>
				</div>
				<hr />

				<!-- price -->
				<div class="col-md-2 justify-content-end align-self-center price-summary fs-4">
					<div class="text-end fw-normal">
						<div class="mb-3 mt-0">
							<div class="mb-2 d-flex justify-content-between align-items-center">
								<div class="">時間：</div>
								<div>{{ displayHour }}時間</div>
							</div>
						</div>

						<div class="mb-2 d-flex justify-content-between align-items-center">
							<div>
								フリー
								<span v-for="tag in callCastRequestInfo.castRank" :key="tag" class="tag mx-1 mb-0">
									{{ tag.charAt(0) }}
								</span>
								：
							</div>
							<div style="font-size: 1.2rem">
								<span class="fs-3 fw-bold">{{
									formatNumber(
										basicPrice *
										2 *
										(callCastRequestInfo.numberOfPeople - castList.length) *
										parseInt(displayHour),
									)
								}}P～</span>
								<br />
								（
								{{ formatNumber(basicPrice) }}P～ / 30分 x
								{{ callCastRequestInfo.numberOfPeople - castList.length }}人 ）
							</div>
						</div>
						<div v-if="castList.length" class="mb-2">
							<div v-for="(subtotal, rank) in calculateRankSubtotals(castList)
								.rankSubtotals" :key="rank" class="mb-2 d-flex justify-content-between align-items-center">
								<div>
									指名
									<span class="tag mx-1 mb-0">
										{{ subtotal.firstletter }}
									</span>
									：
								</div>
								<div style="font-size: 1.2rem">
									<span class="fs-3 fw-bold">{{
										formatNumber(subtotal.points * 2 * parseInt(displayHour))
									}}P</span>
									<br />
									（
									{{ formatNumber(subtotal.points / subtotal.count) }}P / 30分 x
									{{ subtotal.count }}人 ）
								</div>
							</div>
						</div>

						<!-- {{castList.length}} -->
						<div class="mb-2">
							<div class="mb-2 d-flex justify-content-between align-items-center">
								<div v-if="callCastRequestInfo.mid_night_fee > 0">深夜手当</div>
								<div v-if="callCastRequestInfo.mid_night_fee > 0">
									{{
										formatNumber(parseInt(callCastRequestInfo.mid_night_fee))
									}}P / 1回 x {{ callCastRequestInfo.numberOfPeople }}人
								</div>
							</div>
						</div>
						<div class="mb-5" v-if="castList.length > 0">
							<!-- {{ callCastRequestInfo.selected_cast_ids.length }} -->
							<div class="mb-2 d-flex justify-content-between align-items-center">
								<div>指名料金：</div>
								<div style="font-size: 1.2rem">
									<span class="fs-3 fw-bold">{{
										formatNumber(
											1500 * 2 * castList.length * parseInt(displayHour),
										)
									}}P</span>
									<br />
									（ 1,500P / 30分 x {{ castList.length }}人 ）
								</div>
							</div>
						</div>

						<hr />
						<div class="d-flex justify-content-between align-items-center fw-normal mt-3 mb-45 fs-3">
							<div>合計ポイント</div>
							<div class="fw-bold">
								{{
									formatNumber(
										parseFloat(displayHour) *
										(parseInt(callCastRequestInfo.numberOfPeople) -
											parseInt(castList.length)) *
										parseFloat(basicPrice) *
										2 +
										parseFloat(displayHour) *
										parseFloat(
											calculateRankSubtotals(castList).totalPoints,
										) *
										2 +
										parseFloat(callCastRequestInfo.mid_night_fee) *
										parseInt(callCastRequestInfo.numberOfPeople) +
										parseInt(castList.length) *
										1500 *
										parseFloat(displayHour) *
										2,
									)
								}}P～
							</div>
						</div>
						<p class="mt-2 fs-6 text-start">
							※鍵付きの個室やレンタルスペース・マンションやホテル等の個室利用、または合流場所が「合流エリア」と異なる場合、女性会員とは合流できず100%有償でのキャンセルとなります。
						</p>
						<p class="mt-2 fs-6 text-start">
							※消費ポイントは概算です。実際のご利用時間・集まった女性会員に応じて変動いたしますのであらかじめご承知おきください。
						</p>
					</div>
					<div class="d-flex justify-content-between align-items-center fs-6 fw-bold"></div>
				</div>

				<!-- complete btn -->
				<div class="col-md-auto justify-content-center align-self-center text-center ms-4 mb-3">
					<button type="button" class="btn deet-btn full-btn m-0" @click="navigateToCompleteRandomRequest"
						:disabled="submitBtnClicked">
						この内容で確定する
					</button>
				</div>

				<!-- Purchase points modal -->
				<PurchasePointsModal v-if="showPurchasePointsModal" @close="closePurchasePointsModal"
					@onSelectPoints="setPointsAndOpenStripeModal" />

				<!-- Stripe payment modal -->
				<PaymentWithStripe v-if="showStripePaymentModal" :selectedPoints="selectedPoints" :returnUrl="returnUrl"
					@close="closeStripePaymentModal" />
			</div>
		</div>
	</div>
</template>

<style scoped>
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
