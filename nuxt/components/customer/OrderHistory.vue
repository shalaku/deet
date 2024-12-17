<script setup>
import { calculateAge, formatDateJpShort, formatDateTimeJp, formatDateTimeWithYear, formatMeetingTime, formatNumber, getRankPointValue, getStatusCustomer } from '@/libs/utilities';
import { ref } from 'vue';
import { castAPI, orderAPI, requestAPI } from '~/libs/api';
const route = useRoute();
const router = useRouter();

// タブ切り替え機能
const tabs = [
	{ label: 'リクエスト案件', value: 'inProgress' },
	{ label: '確定案件', value: 'confirmed' },
	{ label: '案件履歴', value: 'history' },
];
const activeTab = ref('inProgress');
const setActiveTab = (tab) => {
	activeTab.value = tab;
};

// cast card をクリックしたときにCastInfoPopupを出す機能
const showCastInfoPopup = ref(false);
const selectedCast = ref();

const isLodingIndicatorDeet = ref(true);
const isLoadingIndicatorCall = ref(true);
const isLoadingIndicatorConfirmed = ref(true);


const openCastInfoPopup = (cast) => {
	selectedCast.value = cast;
	showCastInfoPopup.value = true;
};

const closeCastInfoPopup = () => {
	showCastInfoPopup.value = false;
};

// 詳細表示ボタンを押したときの機能
const toggleDetails = (order) => {
	order.showDetails = !order.showDetails;
};

const rankPrices = {
    'BLACK': 10000,
    'PLATINUM': 8000,
    'GOLD': 6000,
};

const basicPrice = (requestedRank) => {
  let basicPoint = 10000;

    // console.log(callCastRequestInfo.value.castRank);
  if (requestedRank.length === 0) {
    basicPoint = 6000;
  } else {
    requestedRank.forEach(rank => {
      if (parseInt(getRankPointValue(rank)) < basicPoint) {
        basicPoint = getRankPointValue(rank);
      }
    });
  }

  return basicPoint;
};

// const basicPrice = (requestedRank) => {
//     let total = 0;
//     requestedRank.forEach(rank => {
//         if (rankPrices[rank] && (total === 0 || total > rankPrices[rank])) {
//             total = rankPrices[rank];      
//         }
//     });
//     return total === 0 ? 5000 : total;
// };


onMounted(async () => {
	await fetchhistoryOrders(600);
	await fetchhistoryRequests('501,504');
	// await fetchconfirmedOrders(601);
	await fetchhistoryOrders(601);
	await fetchAllhistoryOrders();

});
const detailshistoryOrders=ref([]);
const detailshistoryRequests=ref([]);
const allDetailsHistoryRequests=ref([]);
const confirmedOrders = ref([]);
const alldetailshistoryOrders=ref([]);
const currentPageHistory = ref(1);
const currentPageConfirmed = ref(1);
const hasMoreHistoryOrders = ref(true);
const hasMoreConfirmedOrders = ref(true);
const isLoadingHistory = ref(false);
const isLoadingConfirmed = ref(false);
const currentPageHistoryRequests = ref(1);
const isLoadingHistoryRequests = ref(false);
const hasMoreHistoryRequests = ref(true);
const currentPageAllHistoryOrders = ref(1);
const isLoadingAllHistoryOrders = ref(false);
const hasMoreAllHistoryOrders = ref(true);
const fetchconfirmedOrders = async (status) => {
  try {
    const response = await orderAPI.getOrdersByStatus(status);
    const orders = response.data.orders || [];

    for (const order of orders) {

      const startTime = new Date(order.start_date_time);
      const endTime = new Date(order.end_date_time);

      const durationInMs = endTime - startTime;
      order.duration = Math.floor(durationInMs / (1000 * 60 * 60));
	  order.castData = await Promise.all(order.details.map(async (detail) => {
      const castInfo = await castAPI.getCastInfo(detail.cast_id); // Assume fetchCastDetails is defined
	 
        return {
          id: detail.id,
          castId: detail.cast_id,
          appliedPoint: detail.applied_point,
          ...castInfo, // Spread cast information into the object
        };
      }));
      order.meetingTime = order.planned_meeting_time;
      order.location = `${order.place_street}`;
	  
    }
    
    confirmedOrders.value = orders;
  } catch (error) {
    console.error('Failed to fetch confirmed orders:', error);
  }
};

function calculateRankSubtotals(castList) {
  const rankSubtotals = {};
  let totalPoints = 0;

  castList.forEach(castItem => {
    const rank = castItem.rank;
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


const fetchhistoryOrders = async (status) => {
    if (
        (status === 600 && (isLoadingHistory.value || !hasMoreHistoryOrders.value)) ||
        (status === 601 && (isLoadingConfirmed.value || !hasMoreConfirmedOrders.value))
    ) {
        return; 
    }

    // Set loading state
    if (status === 600) isLoadingHistory.value = true;
    if (status === 601) isLoadingConfirmed.value = true;

    try {
        const currentPage = status === 600 ? currentPageHistory.value : currentPageConfirmed.value;

        // Fetch orders with pagination
        const response = await orderAPI.getOrdersByStatusPagar(
            status,
            currentPage,
        );

        const fetchedOrders = response.data.orders || [];
        for (const order of fetchedOrders) {
            order.duration = formatMeetingTime(order.planned_meeting_time);
            order.castData = await Promise.all(
                order.details.map(async (detail) => {
                    const castInfo = await castAPI.getCastInfo(detail.cast_id);
                    return {
                        id: detail.id,
                        castId: detail.cast_id,
                        appliedPoint: detail.applied_point,
                        ...castInfo,
                    };
                })
            );

            order.meetingTime = formatDateTimeWithYear(order.planned_meeting_date_time);
            order.location = `${order.place_street}`;
        }

        // Append data to the appropriate list
        if (status === 600) {
            detailshistoryOrders.value = [...detailshistoryOrders.value, ...fetchedOrders];
			isLodingIndicatorDeet.value = false;         
			if (fetchedOrders.length <10) hasMoreHistoryOrders.value = false; // No more data
            else currentPageHistory.value += 1; // Increment page
        } else if (status === 601) {
            confirmedOrders.value = [...confirmedOrders.value, ...fetchedOrders];
			isLoadingIndicatorConfirmed.value = false;         
			if (fetchedOrders.length < 10) hasMoreConfirmedOrders.value = false; // No more data
            else currentPageConfirmed.value += 1; // Increment page
        }
    } catch (error) {
        console.error("Failed to fetch orders:", error);
    } finally {
        // Reset loading state
        if (status === 600) isLoadingHistory.value = false;
        if (status === 601) isLoadingConfirmed.value = false;
    }
};

const fetchhistoryRequests = async (status, page = currentPageHistoryRequests.value) => {
  if (isLoadingHistoryRequests.value || !hasMoreHistoryRequests.value) return;

  try {
    isLoadingHistoryRequests.value = true;
    const historyResponse = await requestAPI.getRequestsByStatusPagar(status, { page });
    const historyRequests = historyResponse.data.requests || [];

    for (const historyRequest of historyRequests) {
      historyRequest.duration = formatMeetingTime(historyRequest.requested_matching_term);


      historyRequest.castData = await Promise.all((historyRequest.details || []).map(async (detail) => {
        try {
          const historyCastInfo = await castAPI.getCastInfo(detail.cast_id);
          return {
            id: detail.id,
            castId: detail.cast_id,
            appliedPoint: detail.applied_point,
            ...historyCastInfo,
          };
        } catch (error) {
          console.warn(`Failed to fetch cast info for cast_id ${detail.cast_id}:`, error);
          return {
            id: detail.id,
            castId: detail.cast_id,
            appliedPoint: detail.applied_point,
            castInfo: null,
          };
        }
      }));

      historyRequest.meetingTime = formatDateTimeWithYear(historyRequest.requested_start_time);
      historyRequest.location = `${historyRequest.area_name}`;
    }

    const statusArray = status.split(',').map(Number); // カンマ区切りのステータスを配列に変換

    if (statusArray.includes(501) || statusArray.includes(504)) {
      detailshistoryRequests.value = [...detailshistoryRequests.value, ...historyRequests];
	  isLoadingIndicatorCall.value = false;
      if (historyRequests.length < 10) {
        hasMoreHistoryRequests.value = false; 
      } else {
        currentPageHistoryRequests.value += 1; 
      }
    }
  } catch (error) {
    console.error('Failed to fetch history requests:', error);
  } finally {
    isLoadingHistoryRequests.value = false;
  }
};

const fetchAllhistoryOrders = async () => {
  // Prevent fetching if already loading or no more data
  if (isLoadingAllHistoryOrders.value || !hasMoreAllHistoryOrders.value) return;

  try {
    isLoadingAllHistoryOrders.value = true;
    const limit = 10;
	const multi_status=[600,601,602,603,604];
    const allHistoryResponse = await orderAPI.getAllOrders({ page: currentPageAllHistoryOrders.value, limit, multi_status });
    const allHistoryOrders = allHistoryResponse.data.orders || [];

    for (const allHistoryOrder of allHistoryOrders) {
      allHistoryOrder.duration = formatMeetingTime(allHistoryOrder.planned_meeting_time);

      allHistoryOrder.castData = await Promise.all((allHistoryOrder.details || []).map(async (detail) => {
        try {
          const allHistoryCastInfo = await castAPI.getCastInfo(detail.cast_id);
          return {
            id: detail.id,
            castId: detail.cast_id,
            appliedPoint: detail.applied_point,
            ...allHistoryCastInfo,
          };
        } catch (error) {
          console.warn(`Failed to fetch cast info for cast_id ${detail.cast_id}:`, error);
          return {
            id: detail.id,
            castId: detail.cast_id,
            appliedPoint: detail.applied_point,
            castInfo: null,
          };
        }
      }));

      allHistoryOrder.meetingTime = formatDateTimeWithYear(allHistoryOrder.planned_meeting_date_time);
      allHistoryOrder.location = `${allHistoryOrder.place_street}`;
    }

    alldetailshistoryOrders.value = [...alldetailshistoryOrders.value, ...allHistoryOrders];

    if (allHistoryOrders.length < limit) {
      hasMoreAllHistoryOrders.value = false;
	  console.log(hasMoreAllHistoryOrders.value); 
    } else {
      currentPageAllHistoryOrders.value += 1;
    }
  } catch (error) {
    console.error('Failed to fetch all history orders:', error);
  } finally {
    isLoadingAllHistoryOrders.value = false;
  }
};

    // 指名ポイントを計算
    const calculateDesignatedPoint = (details) => {
    // calculateDesignatedPoint(details) {
      return details.reduce((sum, detail) => sum + (parseFloat(detail.designated_point) || 0), 0);
    };

const calcTotalPoint = (order) => {
    let totalPoint = 0;
    if (order && order.details && order.details.length > 0) {
        order.details.forEach(detail => {
            // console.log((parseFloat(detail.applied_point) * 2));
            // console.log(parseFloat(order.duration.replace(/[^0-9]/g, '')));
            // console.log(parseFloat(detail.mid_night_fee));
            totalPoint += 
				(parseFloat(detail.applied_point) * 2) * parseFloat(order.duration.replace(/[^0-9]/g, '')) + 
				(parseFloat(detail.designated_point)) + 
				parseFloat(detail.mid_night_fee)
			;
        });
    } else {
        console.log("No details available for this order.");
    }
    return totalPoint;
};

const calcTotalDesignatedPoint = (order) => {
    let totalPoint = 0;
    if (order && order.details && order.details.length > 0) {
        order.details.forEach(detail => {
            totalPoint += (parseFloat(detail.designated_point));
        });
    } else {
        console.log("No details available for this order.");
    }
    return totalPoint;
};
const calcTotalMidNightFee = (order) => {
    let totalPoint = 0;
    if (order && order.details && order.details.length > 0) {
        order.details.forEach(detail => {
            totalPoint += (parseFloat(detail.mid_night_fee));
        });
    } else {
        console.log("No details available for this order.");
    }
    return totalPoint;
};

// const formatNumber = (value) => {
// 	return value.toLocaleString();
// };

const loadMoreConfirmed = async () => {
      await fetchhistoryOrders(601);
    };

// テーブルのステータスに応じたクラスを返す関数(ステータスによって色を変更するため)
const getStatusCustomerClass = (status) => {
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


// 画像ダミーデータ
const dummyImageSrc =
	'data:image/gif;base64,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';

</script>

<template>
	<div class="container-fluid p-0">
		<div class="status-tab-bg fs-3">
			<div v-for="tab in tabs" :key="tab.value" :class="['status-tab', { active: activeTab === tab.value }]"
				@click="setActiveTab(tab.value)">
				{{ tab.label }}
			</div>
		</div>
		<div class="content">
			<div class="content-body">
				<!-- 注文中タブ Orders in progress tab -->
				<template v-if="activeTab === 'inProgress'">

					<div class="wrapper order-details p-3">

						<p class="fs-3 fw-bold">Deet案件</p>
						
						<div v-if="isLodingIndicatorDeet" class="text-center my-5 py-5">
							<LoadingLine></LoadingLine>
						</div>
						<div v-if="detailshistoryOrders && detailshistoryOrders.length == 0 && !isLodingIndicatorDeet"
							class="text-center no-result">
							<p class="h4 mt-5 pt-1">案件はありません</p>
						</div>
						
						<div class="row inner-card p-2 pb-4 pt-4 m-0 mb-3" v-for="order in detailshistoryOrders"
							:key="order.id">
							<div class="col-9 d-flex">
								<div class="profile-image-wrapper mb-3">
									<div class="profile-image-area">
										<!-- <img :src="order.castData[0].data.cast.images.find(image => image.is_profile_picture === 1)?.image_url || order.castData[0].data.cast.images[0]?.image_url || dummyImageSrc" alt="cast image" class="profile-image" /> -->
										<img :src="order.castData[0]?.data.images[0]?.image_url || dummyImageSrc"
											class="profile-image">
										<!-- <img :src="dummyImageSrc" alt="profile image" class="profile-image"> -->
									</div>
									<!-- <datalist>{{ order.castData[0]?.data }}</datalist> -->
								</div>
								<div class="fs-4 ms-4">
									<!-- <span class="fw-bold">女性会員</span><br> -->
									<span class="fs-3">{{ order.castData[0]?.data.cast.nickname }}</span>
									<span class="ms-3">{{ calculateAge(new Date(order.castData[0]?.data.cast.birthday))
										}}歳</span><br>
									{{ order.castData[0]?.data.cast.my_comment }}
								</div>

							</div>
							<div class="col-3 text-end order-badge" :class="order.order_type">
								<span class="rank-tag fs-4 me-0">
									Deet
								</span>
							</div>

							<div class="col-12 mt-3">
								<div class="mb-2 d-flex justify-content-between">
									<div>
										<span class="fs-4 me-2 status-label" :class="`label-${order.status}`">{{
											getStatusCustomer(order.status) }}</span>
									</div>
									<div class="">
										<span v-if="!order.shown_id" class="fs-5 me-2">ID:{{ order.id }}</span>
										<span v-else class="fs-5 me-2">ID:{{ order.shown_id }}</span>
										<span class="fs-5 me-2 text-end">{{ formatDateTimeJp(order.created_at) }}</span>
									</div>
								</div>
							</div>
							<div class="col-6 mt-3">
								<div class="fs-4 fw-bold"><span class="icon-periscope d-inline-block me-2"></span>合流場所
								</div>
								<div class="fs-3">{{ order.location }}</div>
							</div>
							<div class="col-6 mt-3">
								<div class="fs-4 fw-bold"><span class="icon-alarm d-inline-block me-2"></span>合流予定</div>
								<div class="fs-3">{{ formatDateJpShort(order.planned_meeting_date_time) }}</div>
							</div>

							<div class="col-6 mt-3">
								<div class="fs-4 fw-bold"><span class="icon-history d-inline-block me-2"></span>リクエスト時間
								</div>
								<div class="fs-3">{{ Number(order.planned_meeting_time?.split(':')[0]) }}時間</div>
							</div>
							<div class="col-6 mt-3">
								<div class="fs-4 fw-bold"><span class="icon-user d-inline-block me-2"></span>人数</div>
								<div class="fs-3">{{ order.details.length }}人</div>
							</div>


							<!-- <div class="col-6 mt-3"> -->
							<!-- <div class="fs-4 fw-bold">消費予定ポイント</div> -->
							<!-- <div class="fs-3">{{ formatNumber(calcTotalPoint(order)) }}P～</div> -->
							<!-- </div> -->

							<!-- <div class="col-6 mt-3" v-if="parseInt(order.details[0].mid_night_fee) > 0"> -->
							<!-- <div class="fs-4 fw-bold">深夜手当</div> -->
							<!-- <div class="fs-3">{{ formatNumber(parseInt(order.details[0].mid_night_fee)) }}P</div> -->
							<!-- </div> -->

							<div class="col-7 mt-4 d-flex justify-content-start">
								<div class="fs-3 fw-bold">合計
									{{ formatNumber(calcTotalPoint(order)) }}P～
								</div>
							</div>
							<div class="col-5 d-flex justify-content-end">
								<button class="btn btn-deet-transparent btn-detail-toglle fs-4" @click="toggleDetails(order)">
									{{ order.showDetails ? '閉じる' : '詳細' }}
								</button>
							</div>
							<div class="col-12" v-if="order.showDetails">
								<div class="order-details">
									<CustomerOrderDetails2 :castData="order.castData" :orderData="order"
										:showBadges="true" @open-cast-info="openCastInfoPopup" />
								</div>
							</div>
						</div>

						<!-- コール -->
						<!-- <hr> -->
						<p class="fs-3 fw-bold mt-4">コール案件</p>

						<div v-if="isLoadingIndicatorCall" class="text-center my-5 py-5">
							<LoadingLine></LoadingLine>
						</div>
						<div v-if="detailshistoryRequests && detailshistoryRequests.length == 0 && !isLoadingIndicatorCall"
							class="text-center no-result">
							<p class="h4 mt-5 pt-1">案件はありません</p>
						</div>

						<div class="row inner-card p-2 pb-4 pt-4 m-0 mb-3" v-for="request in detailshistoryRequests"
							:key="request.id">
							<div class="col-12 mt-3 mb-3">
								<div class="mb-2 d-flex justify-content-between">
									<div>
										<span class="fs-4 me-2 status-label" :class="`label-${request.status}`">{{
											getStatusCustomer(request.status) }}</span>
									</div>
									<div class="">
										<span v-if="!request.shown_id" class="fs-5 me-2">ID:{{ request.id }}</span>
										<span v-else class="fs-5 me-2">ID:{{ request.shown_id }}</span>
										<span class="fs-5 me-2 text-end">{{ formatDateTimeJp(request.created_at)
											}}</span>
									</div>
								</div>
							</div>

							<div class="col-6">
								<div class="fs-4 fw-bold"><span class="icon-periscope d-inline-block me-2"></span>合流場所
								</div>
								<div class="fs-3">{{ request.location }}</div>
							</div>
							<div class="col-6">
								<div class="fs-4 fw-bold"><span class="icon-alarm d-inline-block me-2"></span>合流予定</div>
								<div class="fs-3">{{ formatDateJpShort(request.requested_start_time) }}</div>
							</div>

							<div class="col-6 mt-3">
								<div class="fs-4 fw-bold"><span class="icon-history d-inline-block me-2"></span>リクエスト時間
								</div>
								<div class="fs-3">{{ Number(request.requested_matching_term?.split(':')[0]) }}時間</div>
							</div>

							<div class="col-6 mt-3">
								<div class="fs-4 fw-bold"><span class="icon-user d-inline-block me-2"></span>人数</div>
								<div class="fs-3">{{ request.number_of_people }}人</div>
							</div>

							<div class="col-12 mt-3" v-if="request.cast_rank.length > 0">
								<div class="fs-4 fw-bold">希望クラス</div>
								<div class="fs-3">
									<span v-for="rank in request.cast_rank" :key="rank"
										class="rank-tag fs-4 me-3 mt-1">{{ rank }}</span>
								</div>
							</div>

							<div class="col-7 mt-4 d-flex justify-content-start">
								<div class="fs-3 fw-bold">消費予定ポイント<br>
									{{ formatNumber(
									(parseInt(request.number_of_people) - parseInt(request.details.length)) *
									parseInt(basicPrice(request.cast_rank)*2)*
									parseInt(request.requested_matching_term?.split(':')[0]) +
									parseInt(request.mid_night_fee) * parseInt(request.number_of_people) +
									(parseInt(calculateDesignatedPoint(request.details)*2)*parseInt(request.requested_matching_term?.split(':')[0]))
									+
									parseInt(calculateRankSubtotals(request.details).totalPoints)*2*parseInt(request.requested_matching_term?.split(':')[0])
									)
									}}
									<!-- {{ formatNumber(
									(Number(request.number_of_people) - Number(request.details)) * (basicPrice(request.cast_rank)*2) *
									Number(request.requested_matching_term?.split(':')[0]) +
									Number(request.mid_night_fee) * Number(request.number_of_people) +
									(Number(calculateDesignatedPoint(request.details)*2)*Number(request.requested_matching_term?.split(':')[0]))
									) }} -->
									P～
								</div>
							</div>
							<div class="col-5 d-flex justify-content-end">
								<button class="btn btn-deet-transparent btn-detail-toglle fs-4" @click="toggleDetails(request)">
									{{ request.showDetails ? '閉じる' : '詳細' }}
								</button>
							</div>
							<div class="col-12" v-if="request.showDetails">
								<div class="order-details">
									<CustomerRequestDetails :castData="request.castData" :requestData="request"
										:showBadges="true" @open-cast-info="openCastInfoPopup" />
								</div>
							</div>
						</div>
						<div class="text-center mt-3" v-if="hasMoreHistoryRequests">
							<button class="btn btn-primary" @click="() => fetchhistoryRequests('501,504')"
								:disabled="isLoadingHistoryRequests || !hasMoreHistoryRequests">
								{{
								isLoadingHistoryRequests
								? "Loading..."
								: hasMoreHistoryRequests
								? "もっと見る"
								: "No More Requests"
								}}
							</button>
						</div>
						<!-- <div v-else class="text-center mt-3"> -->
							<!-- 案件はありません -->
						<!-- </div> -->
					</div>


				</template>

				<!-- 確定注文タブ Confirmed orders tab -->
				<template v-else-if="activeTab === 'confirmed'">

					<div class="wrapper order-details p-3">

						<div v-if="isLoadingIndicatorConfirmed" class="text-center my-5 py-5">
							<LoadingLine></LoadingLine>
						</div>
						<div v-if="confirmedOrders && confirmedOrders.length == 0 && !isLoadingIndicatorConfirmed"
							class="text-center no-result">
							<p class="h4 mt-5 pt-1">案件はありません</p>
						</div>

						<div class="row inner-card p-2 pb-4 pt-4 m-0 mb-3" v-for="order in confirmedOrders"
							:key="order.id">
							<!-- {{ order }} -->
							<div class="col-9 d-flex">
								<div v-if="order.order_type === 'direct'" class="profile-image-wrapper">
									<div class="profile-image-area">
										<img :src="order.castData[0]?.data.images[0]?.image_url || dummyImageSrc"
											class="profile-image">
									</div>
									<!-- <datalist>{{ order.castData[0]?.data }}</datalist> -->
								</div>
								<div class="fs-4 ms-4">
									<span v-if="order.order_type === 'direct'" class="fs-3">{{
										order.castData[0].data.cast.name_ja }}さん</span>
									<span v-if="order.order_type === 'direct'" class="ms-3">{{ calculateAge(new
										Date(order.castData[0].data.cast.birthday))}}歳</span>
									<div v-if="order.order_type === 'direct'">
										<span>{{ order.castData[0].data.cast.my_comment }}</span>
									</div>
								</div>
							</div>
							<div class="col-3 text-end order-badge" :class="order.order_type">
								<span class="rank-tag fs-4 me-0" :class="order.order_type"
									v-if="order.order_type === 'direct'">
									Deet
								</span>
							</div>

							<div class="col-12 mt-1 mb-3">
								<div class="mb-2 d-flex justify-content-between">
									<div>
										<span class="fs-4 me-2 status-label" :class="`label-${order.status}`">{{
											getStatusCustomer(order.status) }}</span>
									</div>
									<div class="">
										<span class="fs-5 me-2">ID:{{ order.id }}</span>
										<span class="fs-5 me-2 text-end">{{ formatDateTimeJp(order.created_at) }}</span>
									</div>
								</div>
							</div>

							<div class="col-6">
								<div class="fs-4 fw-bold"><span class="icon-periscope d-inline-block me-2"></span>合流場所
								</div>
								<div class="fs-3">{{ order.location }}</div>
							</div>
							<div class="col-6">
								<div class="fs-4 fw-bold"><span class="icon-alarm d-inline-block me-2"></span>合流予定</div>
								<div class="fs-3">{{ formatDateJpShort(order.planned_meeting_date_time) }}</div>
							</div>

							<div class="col-6 mt-3">
								<div class="fs-4 fw-bold"><span class="icon-history d-inline-block me-2"></span>リクエスト時間
								</div>
								<div class="fs-3">{{ Number(order.planned_meeting_time?.split(':')[0]) }}時間</div>
							</div>

							<div class="col-6 mt-3">
								<div class="fs-4 fw-bold"><span class="icon-user d-inline-block me-2"></span>人数</div>
								<div class="fs-3">{{ order.details.length }}人</div>
							</div>

							<!-- <div class="col-6 mt-3"> -->
							<!-- <div class="fs-4 fw-bold">合計消費ポイント</div> -->
							<!-- <div class="fs-3">{{ formatNumber(calcTotalPoint(order)) }}P～ -->
							<!-- </div> -->
							<!-- <div class="fs-3">{{ formatNumber((order.details[0].applied_point * 2) * -->
							<!-- order.planned_meeting_time?.split(':')[0]) }}P～</div> -->
							<!-- </div> -->
							<!-- <div class="col-6 mt-3" v-if="calcTotalMidNightFee(order) > 0"> -->
							<!-- <div class="fs-4 fw-bold">深夜手当</div> -->
							<!-- <div class="fs-3">{{ formatNumber(calcTotalMidNightFee(order)) }}P</div> -->
							<!-- </div> -->
							<!-- <div class="col-6 mt-3" v-if="calcTotalDesignatedPoint(order) > 0"> -->
							<!-- <div class="fs-4 fw-bold">指名ポイント</div> -->
							<!-- <div class="fs-3">{{ formatNumber(calcTotalDesignatedPoint(order)) }}P</div> -->
							<!-- </div> -->

							<div class="col-7 mt-4 d-flex justify-content-start">
								<div v-if="order.details[0]" class="fs-3 fw-bold">合計
									{{ formatNumber(calcTotalPoint(order)) }}P～
								</div>
							</div>
							<div class="col-5 d-flex justify-content-end">
								<button class="btn btn-deet-transparent btn-detail-toglle fs-4" @click="toggleDetails(order)">
									{{ order.showDetails ? '閉じる' : '詳細' }}
								</button>
							</div>
							<div class="col-12" v-if="order.showDetails">
								<div class="order-details">
									<CustomerOrderDetails2 :castData="order.castData" :orderData="order"
										:showBadges="true" @open-cast-info="openCastInfoPopup" />
								</div>
							</div>


						</div>
						<!-- もっと見る Button -->
						<div class="text-center mt-3" v-if="hasMoreConfirmedOrders">
							<button class="btn btn-primary" @click="loadMoreConfirmed"
								:disabled="isLoadingConfirmed || !hasMoreConfirmedOrders">
								{{
								isLoadingConfirmed
								? "Loading..."
								: hasMoreConfirmedOrders
								? "もっと見る"
								: "No More Orders"
								}}
							</button>
						</div>
						<!-- <div v-else class="text-center mt-3"> -->
							<!-- 案件はありません -->
						<!-- </div> -->
					</div>



				</template>






				<!-- 履歴タブ history tab  -->
				<template v-else-if="activeTab === 'history'">


					<div class="wrapper order-details p-3">

						<p class="fs-3 fw-bold mt-2">案件履歴</p>
						<div class="inner-card p-2 pb-4 pt-4 m-0 mb-3" v-for="order in alldetailshistoryOrders"
							:key="order.id">

							<div class="row p-2 pb-4 pt-4">
								<div v-if="order.order_type == 'direct'" class="col-9 d-flex">
									<div class="profile-image-wrapper mb-3">
										<div class="profile-image-area">
											<img :src="order.castData[0]?.data.images[0]?.image_url || dummyImageSrc"
												class="profile-image">
											<!-- <img :src="order.castData[0].data.cast.images.find(image => image.is_profile_picture === 1)?.image_url || order.castData[0].data.cast.images[0]?.image_url || dummyImageSrc" alt="cast image" class="profile-image" /> -->
											<!-- <img :src="dummyImageSrc" alt="profile image" class="profile-image"> -->
										</div>
										<!-- <datalist>{{ order.castData[0].data.images[0].image_url }}</datalist> -->
									</div>
									<div class="fs-4 ms-4">
										<!-- <span class="fw-bold">女性会員</span><br> -->
										<span class="fs-3">{{ order.castData[0]?.data.cast.nickname }}</span>
										<span class="ms-3">{{ calculateAge(new
											Date(order.castData[0]?.data.cast.birthday))
											}}歳</span>
										<br>
										<span class="fs-3">{{ order.castData[0]?.data.cast.my_comment }}</span>
									</div>

								</div>
								<div v-if="order.order_type == 'direct'" class="col-3 text-end order-badge"
									:class="order.order_type">
									<span class="rank-tag fs-4 me-0">
										Deet
									</span>
								</div>

								<div class="col-12 mt-3 mb-3">
									<div class="mb-2 d-flex justify-content-between">
										<div>
											<span class="fs-4 me-2 status-label" :class="`label-${order.status}`">{{
												getStatusCustomer(order.status) }}</span>
										</div>
										<div class="">
											<span class="fs-5 me-2">ID:{{ order.id }}</span>
											<span class="fs-5 me-2 text-end">{{ formatDateTimeJp(order.created_at)
												}}</span>
										</div>
									</div>
								</div>
								<div class="col-6">
									<!-- <div class="mb-2">
										<span class="fs-5 me-2">ID:{{ order.id }}</span>
										<span class="fs-5 me-2 status-label" :class="`label-${order.status}`">{{
											getStatusCustomer(order.status) }}</span>
									</div> -->
									<div class="fs-4 fw-bold"><span
											class="icon-periscope d-inline-block me-2"></span>合流場所
									</div>
									<div class="fs-3">{{ order.location }}</div>
								</div>
								<div class="col-6">
									<div class="fs-4 fw-bold pt-0 mt-0" style="margin-top: 8px;"><span
											class="icon-alarm d-inline-block me-2"></span>合流予定</div>
									<div class="fs-3">{{ formatDateJpShort(order.planned_meeting_date_time) }}</div>
								</div>

								<div class="col-6 mt-3">
									<div class="fs-4 fw-bold"><span
											class="icon-history d-inline-block me-2"></span>リクエスト時間
									</div>
									<div class="fs-3">{{ Number(order.planned_meeting_time?.split(':')[0]) }}時間</div>
								</div>
								<div class="col-6 mt-3">
									<div class="fs-4 fw-bold"><span class="icon-user d-inline-block me-2"></span>人数
									</div>
									<div class="fs-3">{{ order.details.length }}人</div>
								</div>


								<div class="col-6 mt-3" v-if="order.details.mid_night_fee > 0">
									<div class="fs-4 fw-bold">深夜手当</div>
									<div class="fs-3">{{ formatNumber(order.details.mid_night_fee) }}P</div>
								</div>

								<div class="col-12 mt-4 d-flex justify-content-start">
									<div class="fs-3 fw-bold">消費ポイント
										{{ formatNumber(calcTotalPoint(order)) }}P
									</div>
								</div>
								<!-- {{ order }} -->
								<!-- <div class="col-6 mt-3" v-if="order.start_date_time">
									<div class="fs-4 fw-bold">開始時間</div>
									<div class="fs-3">{{ formatDateJpShort(order.start_date_time) }}</div>
								</div>
								<div class="col-6 mt-3" v-if="order.end_date_time">
									<div class="fs-4 fw-bold">解散時間</div>
									<div class="fs-3">{{ formatDateJpShort(order.end_date_time) }}</div>
								</div> -->

								<div class="col-12 d-flex justify-content-end mt-4">
									<button class="btn btn-deet-transparent btn-detail-toglle fs-4" @click="toggleDetails(order)">
										{{ order.showDetails ? '閉じる' : '詳細' }}
									</button>
								</div>
								<div class="col-12" v-if="order.showDetails">
									<div class="order-details">
										<CustomerOrderDetails2 :castData="order.castData" :orderData="order"
											:showBadges="true" @open-cast-info="openCastInfoPopup" />
									</div>
								</div>
							</div>

						</div>
						<div class="text-center mt-3" v-if="hasMoreAllHistoryOrders">
							<button class="btn btn-primary" @click="fetchAllhistoryOrders"
								:disabled="isLoadingAllHistoryOrders || !hasMoreAllHistoryOrders">
								{{
								isLoadingAllHistoryOrders
								? "Loading..."
								: hasMoreAllHistoryOrders
								? "もっと見る"
								: "No More Orders"
								}}
							</button>
						</div>
						<div v-else class="text-center mt-3">
							案件はありません
						</div>


						<p class="fs-3 fw-bold mt-4">リクエスト履歴</p>
						<div class="row inner-card p-2 pb-4 pt-4 m-0 mb-3" v-for="request in detailshistoryRequests"
							:key="request.id">
							<div class="col-12 mt-3 mb-3">
								<div class="mb-2 d-flex justify-content-between">
									<div>
										<span class="fs-4 me-2 status-label" :class="`label-${request.status}`">{{
											getStatusCustomer(request.status) }}</span>
									</div>
									<div class="">
										<span class="fs-5 me-2">ID:{{ request.id }}</span>
										<span class="fs-5 me-2 text-end">{{ formatDateTimeJp(request.created_at)
											}}</span>
									</div>
								</div>
							</div>

							<div class="col-6">
								<div class="fs-4 fw-bold"><span class="icon-periscope d-inline-block me-2"></span>合流場所
								</div>
								<div class="fs-3">{{ request.location }}</div>
							</div>
							<div class="col-6">
								<div class="fs-4 fw-bold"><span class="icon-alarm d-inline-block me-2"></span>合流予定</div>
								<div class="fs-3">{{ formatDateJpShort(request.requested_start_time) }}</div>
							</div>

							<div class="col-6 mt-3">
								<div class="fs-4 fw-bold"><span class="icon-history d-inline-block me-2"></span>リクエスト時間
								</div>
								<div class="fs-3">{{ Number(request.requested_matching_term?.split(':')[0]) }}時間</div>
							</div>

							<div class="col-6 mt-3">
								<div class="fs-4 fw-bold"><span class="icon-user d-inline-block me-2"></span>人数</div>
								<div class="fs-3">{{ request.number_of_people }}人</div>
							</div>

							<div class="col-12 mt-3" v-if="request.cast_rank.length > 0">
								<div class="fs-4 fw-bold">希望クラス</div>
								<div class="fs-3">
									<span v-for="rank in request.cast_rank" :key="rank"
										class="rank-tag fs-4 me-3 mt-1">{{ rank }}</span>
								</div>
							</div>

							<div class="col-7 mt-4 d-flex justify-content-start">
								<div class="fs-3 fw-bold">消費予定ポイント
									{{ formatNumber(
									Number(request.number_of_people) * (basicPrice(request.cast_rank)*2) *
									Number(request.requested_matching_term?.split(':')[0]) +
									Number(request.mid_night_fee) * Number(request.number_of_people) +
									(Number(calculateDesignatedPoint(request.details)*2)*Number(request.requested_matching_term?.split(':')[0]))
									) }}
									P～
								</div>
							</div>
							<div class="col-5 d-flex justify-content-end">
								<button class="btn btn-deet-transparent btn-detail-toglle fs-4" @click="toggleDetails(request)">
									{{ request.showDetails ? '閉じる' : '詳細' }}
								</button>
							</div>
							<div class="col-12" v-if="request.showDetails">
								<div class="order-details">
									<CustomerRequestDetails :castData="request.castData" :requestData="request"
										:showBadges="true" @open-cast-info="openCastInfoPopup" />
								</div>
							</div>
						</div>
						<div class="text-center mt-3" v-if="hasMoreHistoryRequests">
							<button class="btn btn-primary" @click="() => fetchhistoryRequests(501)"
								:disabled="isLoadingHistoryRequests || !hasMoreHistoryRequests">
								{{
								isLoadingHistoryRequests
								? "Loading..."
								: hasMoreHistoryRequests
								? "もっと見る"
								: "No More Requests"
								}}
							</button>
						</div>
						<div v-else class="text-center mt-3">
							案件はありません
						</div>
					</div>



					<!-- </div> -->





				</template>
			</div>
		</div>

		<!-- cast info popup -->
		<CustomerCastInfoPopup v-if="showCastInfoPopup" :cast="selectedCast" @close="closeCastInfoPopup" />
	</div>
</template>

<style scoped>
.btn-detail-toglle {
	height: 2.8em;
}
.text-10px {
	font-size: 10px;
}

/* tab */
.status-tab-bg {
	/* background: #ebedef; */
	/* display: flex; */
}

.status-tab {
	padding: 10px 38px;
	/* display: inline-block; */
	/* padding: 10px 64px; */
	/* background-color: #ffffff; */
	/* color: #707070; */
	/* border-top-left-radius: 2px; */
	/* border-top-right-radius: 2px; */
	/* font-weight: 700; */
	/* cursor: pointer; */
}

.status-tab-bg {
	/* background: #ebedef; */
	/* display: flex; */
	/* justify-content: flex-start; */
}

.status-tab {
	/* padding: 10px 64px; */
	/* color: #bebebe; */
	/* font-weight: 700; */
	/* cursor: pointer; */
	/* position: relative; */
}

.status-tab::after {
	/* content: ''; */
	/* position: absolute; */
	/* bottom: -1px; */
	/* left: 0; */
	/* width: 100%; */
	/* height: 2px; */
}

.status-tab:hover {
	/* background-color: #e0e0e0; */
}

.status-tab.active {
	/* color: #707070; */
	/* background-color: #ffffff; */
}

.status-tab.active::after {
	/* background-color: #707070; */
}

/* table */
.order-details {
	/* padding: 20px; */
	/* background-color: #f8f9fa; */
}

.table th,
.table td {
	background-color: var(--deet-color-cotent-base);
	color: var(--deet-color-font);
	/* vertical-align: middle; */
	/* color: #707070; */
	/* font: normal normal bold 12px Meiryo UI; */
}

.table td {
	/* font-weight: normal; */
}

.table>:not(caption)>*>* {
    padding: 1.5rem .3rem;
}

.status-badge {
	display: flex;
	justify-content: center;
	align-items: center;
	width: 55px;
	height: 18px;
	font: normal normal normal 8px/10px Meiryo UI;
	border-radius: 4px;
	margin: 0 auto;
}

.status-pending {
	background-color: #d87c20;
	color: #ffffff;
}

.status-confirmed {
	background-color: #0dbe00;
	color: #ffffff;
}

.status-completed {
	background-color: #005fbe;
	color: #ffffff;
}

.status-cancelled {
	background-color: #7f7f7f;
	color: #ffffff;
}

.status-default {
	background-color: #7f7f7f;
	color: #ffffff;
}


.status-tab {
	padding: 10px 28px;
	color: #bebebe;
}
.status-tab.active,
.status-tab.hover {
	color: #bebebe;
	background-color: #342f28;
}

.status-badge {
	display: inline;
}

.order-type {
	&.request {
		display: none;
	}
}
.table {
	--cui-table-border-color: #67635e;
}
.status-tab.active::after {
    background-color: #67635e;
}

.profile-image-wrapper {
    width: 45px;
    height: 45px;
}
button.btn-primary {
  background-color: #342f28; 
  color: #ffffff;
  border: 1px solid #342f28; 
  padding: 10px 20px; 
  border-radius: 5px;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease; 
}


button.btn-primary:hover {
  background-color: #4a423b; 
  transform: scale(1.05); 
}


button.btn-primary:disabled {
  background-color: #b0a9a1; 
  border-color: #b0a9a1; 
  cursor: not-allowed; 
  opacity: 0.7; 
}
</style>
