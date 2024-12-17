<script setup lang="ts">
import { calculateAge, formatNumber } from '@/libs/utilities';
import { useQuery } from '@tanstack/vue-query';
import { computed, reactive, ref, watchEffect } from 'vue';
import { castAPI, orderAPI, requestAPI } from '~/libs/api';
import { VueQuery } from '~/libs/enums';

const { data: profileData } = useQuery({
	queryKey: [VueQuery.GET_CAST_LANDING_PAGE],
	queryFn: () => castAPI.getLandingPage(),
});
const avatarURL = computed(() => {
	// console.log('profileData', profileData.value?.data.profile_data.images)
	const profileImage = profileData.value?.data.profile_data.images.find((image) => image.is_profile_picture === 1);
	return profileImage ? profileImage.image_url : 'data:image/gif;base64,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';
});

// Fetch profile data

// aggregateOrdersMonthly
const { data: aggregateOrdersMonthly, isLoading: isLoadingaggregateOrdersMonthly,refetch: refetchAggregateOrdersMonthly } = useQuery({
  queryKey: [VueQuery.AGGREGATE_ORDERS_MONTHLY],
  queryFn: () => castAPI.aggregateOrdersMonthly()
});

// Fetch orders with status 600 orderd
const { data: ordersDataOrderd, isLoading: isLoadingOrders, refetch: refetchOrdersDataOrderd } = useQuery({
  queryKey: [VueQuery.GET_ORDER_BY_STATUS, 600],
  queryFn: () => orderAPI.getOrdersByStatus(600)
});
// Fetch orders with status 602 in_progress
const { data: ordersDataInProgress, isLoading: isLoadingProgressOrders,refetch: refetchOrdersDataInProgress } = useQuery({
  queryKey: [VueQuery.GET_PROGRESS_ORDERS_IN_CASTSIDE],
  queryFn: () => orderAPI.getProgressOrdersInCastSide()
});

// Fetch orders with status 601 confirmed
const { data: ordersDataConfirmed, isLoading: isLoadingConfirmedOrders,refetch: refetchOrdersDataConfirmed } = useQuery({
  queryKey: [VueQuery.GET_CONFIRMED_ORDERS_IN_CASTSIDE,],
  queryFn: () => orderAPI.getConfirmedOrdersInCastSide(),
  onSuccess: () => {
    updateOrdersDataAsap();
  }
});

const ordersDataAsap = ref({ data: { orders: [] } });

const updateOrdersDataAsap = () => {
  if (!isLoadingConfirmedOrders.value && ordersDataConfirmed.value) {
    const ordersArray = ordersDataConfirmed.value.data.orders;
    const currentTime = new Date();
    const filteredOrders = ordersArray.filter(order => {
      const meetingTime = new Date(order.planned_meeting_date_time);
      const timeDifference = (meetingTime - currentTime) / (1000 * 60); // Difference in minutes
      return timeDifference <= 30;
    });
    ordersDataAsap.value.data.orders = [...filteredOrders]; // 新しい配列を代入
    console.log(ordersDataAsap);
    console.log(ordersDataConfirmed);
  }
};

watchEffect(() => {
  updateOrdersDataAsap();
});
	
// Fetch request data
const { data: requestMatchingData, isLoading: isLoadingRequestMatching,refetch: refetchRequestMatchingData } = useQuery({
  queryKey: [VueQuery.GET_ORDER_BY_STATUS],
  queryFn: () => requestAPI.getRequestsFilteredCastRank()
});
// console.log(requestMatchingData);

// Fetch request data
const { data: requestDesignatedMatchingData,refetch: refetchRequestDesignatedMatchingData } = useQuery({
  queryKey: [VueQuery.GET_ORDER_BY_STATUS],
  queryFn: () => requestAPI.getDesignatedRequest()
});
console.log(requestDesignatedMatchingData);


// ボタンを押したときの機能
const handleOngoingButtonClick = () => {
	// console.log('進行中の予約が解散されました');
};

const handleScheduledButtonClick = () => {
	// console.log('予定の予約が開始されました');
};

const handleCallRequestApprove = (order) => {
	// console.log(order);
	// console.log('呼び出し依頼が了承されました');
};

const handleCallRequestReject = (order) => {
	// console.log(order);
	// console.log('呼び出し依頼が拒否されました');
};

const handleCallRequestSend = (order) => {
	// console.log(order);
	// console.log('募集に参加申請しました');
};

// テーブルの詳細表示の開閉を管理する、初期状態ではすべて閉じている
const detailsState = reactive({});
const toggleDetails = (orderId) => {
	detailsState[orderId] = !detailsState[orderId];
};
const isDetailShown = (orderId) => {
	return detailsState[orderId] === true;
};

// 呼び出し依頼テーブルのステータスに応じたクラスを返す関数(ステータスによって色を変更するため)
const getStatusClass = (status) => {
	switch (status) {
		case '確定待ち':
			return 'status-pending';
		case '確定':
			return 'status-confirmed';
		case '完了':
			return 'status-completed';
		case 'キャンセル':
			return 'status-cancelled';
		default:
			return 'status-default';
	}
};



// 詳細用（バッジ）仮データ
const castData = ref([]);

// current cast info
const currentCastInfo = ref({
	castRank: 'A',
	castRankPoint: 5000,
	castBasePoint: 10000,
	totalOrderAmount: '--',
	totalOrderPointAmount: '--',
});



</script>

<template>
	<div class="container-fluid main-area top pt-3">

		<!-- 女性会員プロフィール -->
		<div class="content p-4">
			<div class="row justify-content-between align-items-center">
				<div class="col-6">
					<h5 class="mb-3 text-20px">{{profileData?.data.profile_data.cast.nickname}}</h5>

				</div>
				<div class="col-6 text-start">
					<span class="rank-tag mb-2 fs-4"> {{profileData?.data.profile_data.cast.rank}}</span><br
						class="mb-1" />

				</div>


				<div class="col-12 mt-3">
					<div class="row">
						<div class="col-6 col-xl-12 d-flex flex-column justify-content-flex-start">
							<div class="fs-4">今月の終了件数</div>
							<div class="fw-bold mb-2" style="font-size:25px;">{{ formatNumber(aggregateOrdersMonthly?.data.orders_finished.order_count) }}件
							</div>
						</div>
						<div class="col-6 col-xl-12 d-flex flex-column justify-content-flex-start">
							<div class="fs-4">今月の総獲得ポイント</div>
							<div class="fw-bold" style="font-size:25px;">{{ formatNumber(aggregateOrdersMonthly?.data.orders_finished.total_sales) }}P
							</div>
						</div>
						<div class="col-6 col-xl-12 d-flex flex-column justify-content-flex-start">
							<div class="fs-4">今月の予定件数</div>
							<div class="fw-bold mb-2" style="font-size:25px;">{{ formatNumber(aggregateOrdersMonthly?.data.orders_planned.order_count) }}件
							</div>
						</div>
						<div class="col-6 col-xl-12 d-flex flex-column justify-content-flex-start">
							<div class="fs-4">今月の予定獲得ポイント</div>
							<div class="fw-bold" style="font-size:25px;">{{ formatNumber(aggregateOrdersMonthly?.data.orders_planned.total_sales) }}P
							</div>
						</div>
					</div>

				</div>

			</div>
		</div>










		<!-- 女性会員プロフィール -->
		<div class="content p-3 hide">
			<div class="row justify-content-between align-items-center">
				<div class="col-xs-12 col-xl-3">
					<div class="row justify-content-center">
						<div class="col-6 col-md-12 col-xl-12 d-flex justify-content-md-center">
							<div class="profile-pic">
								<!-- <img :src="dummyImageSrc" alt="Dummy image" class="uploaded-image" /> -->
								<img :src="avatarURL" alt="Dummy image" class="uploaded-image" />
							</div>
						</div>
						<div class="col-6 mt-1">
							<div class="mb-3 profile-info">
								<span class="rank-tag mb-2 fs-4">
									{{profileData?.data.profile_data.cast.rank}}クラス</span><br class="mb-1" />
								<span class="age fs-4">{{calculateAge(new
									Date(profileData?.data.profile_data.cast.birthday.split(' ')[0]))}}歳</span>
								<span class="place fs-4">{{profileData?.data.profile_data.cast.prefecture}}地区</span>
							</div>
							<h5 class="mb-3 text-20px">{{profileData?.data.profile_data.cast.nickname}}</h5>

							<div class="d-md-none">
								<div class="text-16px fw-bold">現在の確定予約</div>
								<div class="text-20px fw-bold">15件</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-xl-6 profile-info flex-grow-1 mt-3 mt-lg-0">
					<div class="d-flex justify-content-between align-items-start mb-4">
						<div>
							<div class="grid">
								<span v-for="(tag, index) in profileData?.data.profile_data.cast.tag_of_spec"
									:key="index" class="g-col-4 tag">
									{{ tag }}
								</span>
							</div>
						</div>
						<div class="text-end d-none d-md-block">
							<div class="text-18px fw-bold">現在の確定予約</div>
							<div class="text-28px fw-bold">15件</div>
						</div>
					</div>
					<p class="card-text text-14px">
						{{ profileData?.data.profile_data.cast.notices }}
					</p>
				</div>
				<hr class="mt-3" />
				<div class="col-xs-12 col-xl-3 mt-3 mt-lg-0 sales-info">
					<div class="row">
						<div class="col-12 d-flex justify-content-flex-start align-items-center">
							<div class="text-16px fw-bold mb-2">今月の売上</div>
						</div>
						<div class="col-6 col-xl-12 d-flex flex-column justify-content-flex-start">
							<div class="text-12px">受注件数</div>
							<div class="text-20px fw-bold mb-2">15件</div>
						</div>
						<div class="col-6 col-xl-12 d-flex flex-column justify-content-flex-start">
							<div class="text-12px">売上金額合計</div>
							<div class="text-20px fw-bold">2,000,000P</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- 進行中 -->
		<div class="status-tab-bg mt-4">
			<div class="status-tab">進行中</div>
		</div>
		<div class="content content-with-tab p-3">
			<div class="content-body">
			
				<CastOrderDetails button-text="解散する" button-clicked-text="解散済み" button-class="deet-btn full-btn"
					badge-text="Deet" @button-clicked="handleOngoingButtonClick"
					:cast-data="castData" :order-data="ordersDataInProgress?.data?.orders"
					:profile-data="profileData?.data.profile_data.cast" 
					:refetch-aggregate="refetchAggregateOrdersMonthly"
					:refetch-progress="refetchOrdersDataInProgress"
					:refetch-comfirmed="refetchOrdersDataConfirmed"
					:refetch-deet="refetchOrdersDataOrderd"
					:refetch-request="refetchRequestMatchingData"
				/>
			</div>
		</div>


		<!-- 予定 -->
		<section v-if="ordersDataAsap?.data?.orders.length > 0">
		<div class="status-tab-bg mt-4">
			<div class="status-tab">急ぎ合流案件</div>
		</div>
		<div class="content content-with-tab p-3">
			<div class="content-body">

				<CastOrderDetails button-text="開始する" button-clicked-text="開始済み" button-class="deet-btn full-btn"
					badge-text="Deet" @button-clicked="handleScheduledButtonClick(orderData)"
					:progress="'confirmed'"
					:progress-order-amount="ordersDataInProgress?.data?.orders?.length"
					:cast-data="castData" :order-data="ordersDataAsap?.data?.orders"
					:profile-data="profileData?.data.profile_data.cast" 
					:refetch-aggregate="refetchAggregateOrdersMonthly"
					:refetch-progress="refetchOrdersDataInProgress"
					:refetch-comfirmed="refetchOrdersDataConfirmed"
					:refetch-deet="refetchOrdersDataOrderd"
					:refetch-request="refetchRequestMatchingData"
				/>
			</div>
		</div>
		</section>





		<!-- 予定 -->
		<div class="status-tab-bg mt-4">
			<div class="status-tab">本日の確定案件</div>
		</div>
		<div class="content content-with-tab p-3">
			<div class="content-body">

				<CastOrderDetails button-text="開始する" button-clicked-text="開始済み" button-class="deet-btn full-btn"
					badge-text="Deet" @button-clicked="handleScheduledButtonClick(orderData)"
					:progress="'confirmed'"
					:progress-order-amount="ordersDataInProgress?.data?.orders?.length"
					:cast-data="castData" :order-data="ordersDataConfirmed?.data?.orders"
					:profile-data="profileData?.data.profile_data.cast" 
					:refetch-aggregate="refetchAggregateOrdersMonthly"
					:refetch-progress="refetchOrdersDataInProgress"
					:refetch-comfirmed="refetchOrdersDataConfirmed"
					:refetch-deet="refetchOrdersDataOrderd"
					:refetch-request="refetchRequestMatchingData"
				/>
			</div>
		</div>




		<!-- direct order-->
		<div class="status-tab-bg mt-4">
			<div class="status-tab">Deet案件</div>
		</div>
		<div class="content content-with-tab p-3">
			<div class="content-body">

				<CastOrderDetails button-text="お受けする" button-clicked-text="お受け済み" button-class="deet-btn"
					badge-text="Deet" @button-clicked="handleCallRequestApprove" 
					:progress="'before_accept'"
					:cast-data="castData" :showRejectButton="true" :order-data="ordersDataOrderd?.data?.orders"
					:profile-data="profileData?.data.profile_data.cast" 
					:refetch-aggregate="refetchAggregateOrdersMonthly"
					:refetch-progress="refetchOrdersDataInProgress"
					:refetch-comfirmed="refetchOrdersDataConfirmed"
					:refetch-deet="refetchOrdersDataOrderd"
					:refetch-request="refetchRequestMatchingData"
				/>
			</div>

		</div>




		<!-- コール案件 matching request -->
		<div class="status-tab-bg mt-4">
			<div class="status-tab">コール案件</div>
		</div>
		<div class="content content-with-tab ps-3 pe-3 pb-0">
			<div class="content-body matching-request pt-3 p-2 pb-1">

				<CastRequestDetails button-text="応募する" button-clicked-text="応募済み" button-class="deet-btn"
					badge-text="募集案件" :cast-data="castData" :showRejectButton="true"
					:request-data="requestMatchingData?.data?.requests"
					:designated-matching="requestDesignatedMatchingData"
					:profile-data="profileData?.data.profile_data" 
					:refetch-aggregate="refetchAggregateOrdersMonthly"
					:refetch-progress="refetchOrdersDataInProgress"
					:refetch-comfirmed="refetchOrdersDataConfirmed"
					:refetch-deet="refetchOrdersDataOrderd"
					:refetch-request="refetchRequestMatchingData"
				/>

			</div>
		</div>




	</div>
</template>

<style scoped>
.hide {
	display: none;
}

.profile-image-wrapper {
    width: 40px;
    height: 40px;
}
.matching-request {
	.entry-btn{
		padding: 5px 40px
	}
}
.entry-btn {
	height: 4rem;
}
.content-body {
	/* min-height: 5rem; */
}
.no-result {
	color: #949494;
}
</style>
