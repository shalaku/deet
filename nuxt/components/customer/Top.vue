<script setup>
import { calculateAge, formatDateJpShort, formatDateTimeJp, formatDateTimeWithYear, formatMeetingTime, formatNumber, getStatusCustomer } from '@/libs/utilities';
import { onMounted, ref } from 'vue';
import { castAPI, customerAPI, orderAPI } from '~/libs/api';
// タブ切り替え機能
const tabs = [
	// { label: '人気女性会員', value: 'popular' },
	// { label: '高満足度', value: 'highSatisfaction' },
	// { label: '人気急上昇', value: 'trending' },
	{ label: '20-25歳', value: 'age20to25' },
	{ label: '25-30歳', value: 'age25to30' },
];
const activeTab = ref('age20to25');
const setActiveTab = (tabValue) => {
	activeTab.value = tabValue;
};
const currentPage20to25 = ref(1); // Track page for age group 20-25
const currentPage25to30 = ref(1); // Track page for age group 25-30
const isLoading20to25 = ref(false); // Loading state
const isLoading25to30 = ref(false); // Loading state
const hasMoreCasts20to25 = ref(true); // Track if there are more casts for age 20-25
const hasMoreCasts25to30 = ref(true); // Track if there are more casts for age 25-30
const filteredCasts20to25 = ref([]);
const filteredCasts25to30 = ref([]);
const newCastList=ref([]);
const currentPage = ref(1); // Start at page 1
const isLoading = ref(false); // Loading state
const hasMoreCasts = ref(true); // Tracks if more data is available
const currentPageAvailableCast = ref(1); // Start at page 1
const isLoadingAvailableCast = ref(false); // Loading state
const hasMoreCastsAvailable= ref(true); // Tracks if more data is available
const availableCast=ref([]);
// cast list をクリックしたときpopupを出す機能
const showPopup = ref(false);
const selectedCast = ref(null);
const recommendationList = ref([]);

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
// const calcTotalPoint = (order) => {
//     let totalPoint = 0;
//     if (order && order.details && order.details.length > 0) {
//         order.details.forEach(detail => {
//             // console.log((parseFloat(detail.applied_point) * 2));
//             // console.log(parseFloat(order.duration.replace(/[^0-9]/g, '')));
//             // console.log(parseFloat(detail.mid_night_fee));
//             totalPoint += (parseFloat(detail.applied_point) * 2) * parseFloat(order.duration.replace(/[^0-9]/g, '')) + parseFloat(detail.mid_night_fee);
//         });
//     } else {
//         console.log("No details available for this order.");
//     }
//     return totalPoint;
// };
const toggleDetails = (order) => {
	order.showDetails = !order.showDetails;
};

const openPopupNewCast = (cast, castId) => {
    selectedCast.value = cast;
    let filteredRecommendations = newCastList.value;
    recommendationList.value = filteredRecommendations.filter(cast => cast.cast.id !== castId);
    showPopup.value = true;
};
const openPopupAvailableCast = (cast, castId) => {
    selectedCast.value = cast;
    let filteredRecommendations = availableCast.value;
    recommendationList.value = filteredRecommendations.filter(cast => cast.cast.id !== castId);
    showPopup.value = true;
};
const openPopup = (cast, castId) => {
    selectedCast.value = cast;
    let filteredRecommendations = [];
   if (activeTab.value === 'age20to25') {
        filteredRecommendations = filteredCasts20to25.value;
    } else if (activeTab.value === 'age25to30') {
        filteredRecommendations = filteredCasts25to30.value;
    }
    recommendationList.value = filteredRecommendations.filter(cast => cast.cast.id !== castId);
    showPopup.value = true;
};
const closePopup =async () => {
	showPopup.value = false;
	await fetchFavoriteStatus();
	// fetchNewCasts();
	// fetchCastsByAge(activeTab.value === 'age20to25' ? '20-25' : '25-30');
	// fetchAvailableCasts();
};




// 星のアイコン表示切替
const toggleFavorite = async (cast) => {
  try {

    if (cast.isFavorite) {
      await customerAPI.removeFavCast(cast.cast.id);
      cast.isFavorite = false;
    } else {
      await customerAPI.addFavCast(cast.cast.id);
      cast.isFavorite = true; 
    }
  } catch (error) {
    console.error('Error toggling favorite:', error);
  }
};



// 画像ダミーデータ
const dummyImageSrc =
	'data:image/gif;base64,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';

// ダミーデータの生成
const generateDummyCasts = (count) => {
	return Array.from({ length: count }, (_, index) => ({
		id: index + 1,
		name: `女性会員${index + 1}`,
		age: 'xx',
		area: '東京',
		comment:
			'一言コメント一言コメント一言コメント一言コメント',
		price: 5000,
	}));
};
const casts1 = ref(generateDummyCasts(10));

const fetchNewCasts = async () => {
	if (isLoading.value || !hasMoreCasts.value) return;
	try {
        isLoading.value = true;

        const response = await castAPI.getNewCasts({ page: currentPage.value });
        const fetchedCasts = response.data.new_cast;
        newCastList.value = [...newCastList.value, ...fetchedCasts];
        if (fetchedCasts.length < 10) {
          hasMoreCasts.value = false; 
        } else {
          currentPage.value += 1;
        }
      } catch (error) {
        console.log('Error fetching casts:', error);
      } finally {
        isLoading.value = false;
      }
};

const fetchAvailableCasts = async () => {
	if (isLoadingAvailableCast.value || !hasMoreCastsAvailable.value) return;
 
  try {
    isLoadingAvailableCast.value = true;
    const response = await castAPI.getAvailableCasts({ page: currentPageAvailableCast.value, limit: 10 });
    const fetchedCasts = response.data.available_cast;

    // Append fetched casts to the existing list
    availableCast.value = [...availableCast.value, ...fetchedCasts];

    // Check if fewer than 10 items are returned, indicating no more data
    if (fetchedCasts.length < 10) {
		hasMoreCastsAvailable.value = false;
    } else {
		currentPageAvailableCast.value += 1; // Increment the page number for the next call
    }

    // Fetch favorite status for the loaded casts
    await fetchFavoriteStatus();
  } catch (error) {
    console.error('Error fetching available casts:', error);
  } finally {
    isLoadingAvailableCast.value = false;
  }
};

const fetchCastsByAge = async (ageLimit) => {
    // Separate loading states for each age group
    if (ageLimit === '20-25' && isLoading20to25.value) return;
    if (ageLimit === '25-30' && isLoading25to30.value) return;

    // Set loading state for the appropriate age group
    if (ageLimit === '20-25') isLoading20to25.value = true;
    if (ageLimit === '25-30') isLoading25to30.value = true;

    try {
        let response;
        if (ageLimit === '20-25') {
            response = await castAPI.getNewCastsAge({ age: ageLimit, page: currentPage20to25.value, limit: 10 });

            filteredCasts20to25.value = [...filteredCasts20to25.value, ...response.data.age];
            if (response.data.age.length < 10) {
                hasMoreCasts20to25.value = false;
            } else {
                currentPage20to25.value += 1;
            }
        } else if (ageLimit === '25-30') {
            response = await castAPI.getNewCastsAge({ age: ageLimit, page: currentPage25to30.value, limit: 10 });

            // Append data to filtered list
            filteredCasts25to30.value = [...filteredCasts25to30.value, ...response.data.age];

            // Determine if more casts are available
            if (response.data.age.length < 10) {
                hasMoreCasts25to30.value = false;
            } else {
                currentPage25to30.value += 1;
            }
        }

        await fetchFavoriteStatus();
    } catch (error) {
        console.error('Error fetching casts by age:', error);
    } finally {
        if (ageLimit === '20-25') isLoading20to25.value = false;
        if (ageLimit === '25-30') isLoading25to30.value = false;
    }
};


const fetchFavoriteStatus = async () => {
  try {
    const response = await customerAPI.getFavCasts();
    const favoriteCastIds = response.data.casts.map(cast => cast.cast.id);
	// console.log(favoriteCastIds); 
    filteredCasts20to25.value.forEach(cast => {
      cast.isFavorite = favoriteCastIds.includes(cast.cast.id);
	//   console.log(cast.isFavorite);
    });

    filteredCasts25to30.value.forEach(cast => {
      cast.isFavorite = favoriteCastIds.includes(cast.cast.id);
    });

	availableCast.value.forEach(cast => {
      cast.isFavorite = favoriteCastIds.includes(cast.cast.id);
    });

  } catch (error) {
    console.error('Error fetching favorite status:', error);
  }
};


const progressOrders = ref([]);

const fetchhistoryOrders = async (status) => {
  try {
    const historyresponse = await orderAPI.getOrdersByStatus(status);
    const historyOrders = historyresponse.data.orders || [];
    for (const historyorder of historyOrders) {
      historyorder.duration = formatMeetingTime(historyorder.planned_meeting_time); 
	  historyorder.castData = await Promise.all(historyorder.details.map(async (detail) => {
      const historycastInfo = await castAPI.getCastInfo(detail.cast_id);
	 
        return {
          id: detail.id,
          castId: detail.cast_id,
          appliedPoint: detail.applied_point,
          ...historycastInfo, 
        };
      }));
   
	historyorder.meetingTime = formatDateTimeWithYear(historyorder.planned_meeting_date_time);
	historyorder.location = `${historyorder.place_street}`;
	  
    }
    
	if(status == 602){
		progressOrders.value = historyOrders;
	}
  } catch (error) {
    console.error('Failed to fetch confirmed orders:', error);
  }
};





watch(activeTab, (newTab) => {
    if (newTab === 'age20to25' && filteredCasts20to25.value.length === 0) {
        fetchCastsByAge('20-25');
    } else if (newTab === 'age25to30' && filteredCasts25to30.value.length === 0) {
        fetchCastsByAge('25-30');
    }
});
onMounted(() => {
	// console.log('onMounted');
	fetchNewCasts();
	fetchCastsByAge(activeTab.value === 'age20to25' ? '20-25' : '25-30');
	fetchAvailableCasts();
	fetchhistoryOrders(602);
});


</script>

<template>
	<div class="container-fluid p-0 main-area top">

		<template v-if="progressOrders.length > 0">
			<!-- 進行中 -->
			<div class="status-tab-bg mt-4">
				<div class="status-tab">進行中</div>
			</div>
			<div class="content pt-3 pb-3">
				<div class="wrapper order-details p-3">

					<div class="row inner-card p-2 pb-4 pt-4 m-0 mb-3" v-for="order in progressOrders" :key="order.id">
						<!-- {{ order }} -->
						<div class="col-9 d-flex" v-if="order.order_type === 'direct'">
							<div v-if="order.order_type === 'direct'" class="profile-image-wrapper">
								<div class="profile-image-area">
									<img :src="order.castData[0]?.data.images[0]?.image_url || dummyImageSrc"
										class="profile-image">
								</div>
								<!-- <datalist>{{ order.castData[0]?.data }}</datalist> -->
							</div>
							<div class="fs-4 ms-4">
								<!-- <span v-if="order.order_type === 'direct'" class="fw-bold">女性会員</span><br> -->


								<span class="fs-3">{{
									order.castData[0].data.cast.name_ja }}</span>
								<span class="ms-3">{{ calculateAge(new
									Date(order.castData[0].data.cast.birthday)) }}歳</span><br>
								<span>{{ order.castData[0]?.data.cast.my_comment }}</span>
								<!-- <div> -->
								<!-- <span>ID:{{ order.id }}</span> -->
								<!-- <span class="fs-5 me-2 status-label" :class="`label-${order.status}`">{{ -->
								<!-- getStatus(order.status) }}</span> -->
								<!-- </div> -->
							</div>
						</div>


						<div class="col-3 text-end order-badge" :class="order.order_type">
							<span class="rank-tag fs-4 me-0" :class="order.order_type"
								v-if="order.order_type === 'direct'">
								Deet
							</span>
						</div>

						<div class="col-12 mt-0 mb-3">
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

						<div class="col-6 mt-3">
							<div class="fs-4 fw-bold">合計消費ポイント</div>
							<div class="fs-3">{{ formatNumber(calcTotalPoint(order)) }}P～
							</div>
							<!-- <div class="fs-3">{{ formatNumber((order.details[0].applied_point * 2) * -->
							<!-- order.planned_meeting_time?.split(':')[0]) }}P～</div> -->
						</div>
						<div class="col-6 mt-3" v-if="order.details.mid_night_fee > 0">
							<div class="fs-4 fw-bold">深夜手当</div>
							<div class="fs-3">{{ order.details.mid_night_fee }}P</div>
						</div>
						<div class="col-6 mt-3" v-if="calcTotalDesignatedPoint(order) > 0">
							<div class="fs-4 fw-bold">指名ポイント</div>
							<div class="fs-3">{{ calcTotalDesignatedPoint(order) }}P</div>
						</div>

						<div class="col-7 mt-4 d-flex justify-content-start">
							<div v-if="order.details[0]" class="fs-3 fw-bold">合計
								{{ formatNumber(calcTotalPoint(order)) }}P～
							</div>
						</div>
						<div class="col-5 d-flex justify-content-end">
							<button class="btn btn-deet-transparent fs-4" @click="toggleDetails(order)">
								{{ order.showDetails ? '閉じる' : '詳細' }}
							</button>
						</div>
						<div class="col-12" v-if="order.showDetails">
							<div class="order-details">
								<CustomerOrderDetails2 :castData="order.castData" :orderData="order" :showBadges="true"
									@open-cast-info="openCastInfoPopup" />
							</div>
						</div>
					</div>

				</div>
			</div>
		</template>

		<!-- New女性会員 -->
		<div class="status-tab-bg mt-4">
			<div class="status-tab">New</div>
		</div>
		<div class="content pt-3 pb-3">
			<!-- <div v-if="newCastList.length == 0" class="text-center my-5 py-5"> -->
						<!-- <LoadingLine></LoadingLine> -->
			<!-- </div>			 -->
			<div class="card-grid">

				<div v-for="cast in newCastList" :key="cast.cast.id" class="cast-card"
					@click="openPopupNewCast(cast,cast.cast.id)">
					<!-- <div v-for="cast in casts" :key="cast.id" class="cast-card"> -->
					<div class="cast-image-container">
						<img :src="cast.images.find(image => image.is_profile_picture === 1)?.image_url || cast.images[0]?.image_url || dummyImageSrc"
							alt="cast image" class="uploaded-image" />
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
			<!-- もっと見る Button -->
			<div class="text-center mt-3">
				<button class="btn btn-primary load-more-btn" @click="fetchNewCasts"
					:disabled="isLoading || !hasMoreCasts">
					{{ isLoading ? "読み込み中..." : hasMoreCasts ? "もっと見る" : "No More Casts" }}
				</button>
			</div>
		</div>

		<!-- 人気女性会員 ～ 25-30歳 共通切り替えタブ あとでレスポンシブ対応する -->
		<div class="change-tab-bg status-tab-bg mt-4">
			<div v-for="tab in tabs" :key="tab.value"
				:class="['change-tab status-tab', { active: activeTab === tab.value }]"
				@click="setActiveTab(tab.value)">
				{{ tab.label }}
			</div>
		</div>
		<div class="content  pt-3 pb-3">

			<div v-if="activeTab === 'age20to25'">
				<div v-if="filteredCasts20to25.length == 0" class="text-center my-5 py-5">
						<LoadingLine></LoadingLine>
			</div>	
				<div class="card-grid">
					<div v-for="cast in filteredCasts20to25" :key="cast.cast.id" class="cast-card"
						@click="openPopup(cast,cast.cast.id)">
						<div class="cast-image-container">
							<img :src="cast.images.find(image => image.is_profile_picture === 1)?.image_url || cast.images[0]?.image_url || dummyImageSrc"
								alt="cast image" class="uploaded-image" />
							<div class="favorite-icon" @click.stop="toggleFavorite(cast)">
								<i :class="['icon-star', { favorite: cast.isFavorite }]"></i>
							</div>
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
				<div class="text-center mt-3">
					<button class="btn btn-primary load-more-btn" @click="fetchCastsByAge('20-25')"
						:disabled="isLoading20to25 || !hasMoreCasts20to25">
						{{ isLoading20to25 ? "読み込み中" : hasMoreCasts20to25 ? "もっと見る" : "No More Casts" }}
					</button>
				</div>
			</div>

			<div v-if="activeTab === 'age25to30'">
				<div class="card-grid">
					<div v-for="cast in filteredCasts25to30" :key="cast.cast.id" class="cast-card"
						@click="openPopup(cast,cast.cast.id)">
						<div class="cast-image-container">
							<img :src="cast.images.find(image => image.is_profile_picture === 1)?.image_url || cast.images[0]?.image_url || dummyImageSrc"
								alt="cast image" class="uploaded-image" />
							<div class="favorite-icon" @click="toggleFavorite(cast)">
								<i :class="['icon-star', { favorite: cast.isFavorite }]"></i>
							</div>
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
								{{ (parseInt(cast.cast.basic_point_price,"10") || 0).toLocaleString("ja-JP",{
								maximumFractionDigits: 0
								}) }}P / 30min
							</div>
						</div>
					</div>
				</div>
				<div class="text-center mt-3">
					<button class="btn btn-primary load-more-btn" @click="fetchCastsByAge('25-30')"
						:disabled="isLoading25to30 || !hasMoreCasts25to30">
						{{ isLoading25to30 ? "読み込み中" : hasMoreCasts25to30 ? "もっと見る" : "No More Casts" }}
					</button>
				</div>
			</div>

			<!-- 人気急上昇 -->
			<div v-if="activeTab === 'trending'">
				<div class="card-grid">
					<!-- 人気急上昇の女性会員一覧を追加する -->
				</div>
			</div>

			<!-- 20-25歳 -->
			<!-- <div v-if="activeTab === 'age20to25'"> -->
			<!-- <div class="card-grid"> -->
			<!-- 20-25歳の女性会員一覧を追加する -->
			<!-- </div> -->
			<!-- </div> -->

			<!-- 25-30歳 -->
			<!-- <div v-if="activeTab === 'age25to30'"> -->
			<!-- <div class="card-grid"> -->
			<!-- 25-30歳の女性会員一覧を追加する -->
			<!-- </div> -->
			<!-- </div> -->
		</div>

		<!-- すぐ呼べる -->
		<div class="status-tab-bg mt-4">
			<div class="status-tab">すぐ呼べる</div>
		</div>
		<div class="content pt-3 pb-3">
			<div class="card-grid">

				<div v-for="cast in availableCast" :key="cast.cast.id" class="cast-card"
					@click="openPopupAvailableCast(cast,cast.cast.id)">
					<div class="cast-image-container">
						<img :src="cast.images.find(image => image.is_profile_picture === 1)?.image_url || cast.images[0]?.image_url || dummyImageSrc"
							alt="cast image" class="uploaded-image" />
						<div class="favorite-icon" @click.stop="toggleFavorite(cast)">
							<i :class="['icon-star', { favorite: cast.isFavorite }]"></i>
						</div>
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
							{{ (parseInt(cast.cast.basic_point_price,"10") || 0).toLocaleString("ja-JP",{
							maximumFractionDigits: 0
							}) }}P / 30min
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- もっと見る Button -->
		<div class="text-center mt-3">
			<button class="btn btn-primary load-more-btn" @click="fetchAvailableCasts"
				:disabled="isLoadingAvailableCast || !hasMoreCastsAvailable">
				{{ isLoadingAvailableCast ? "読み込み中" : hasMoreCastsAvailable ? "もっと見る" : "No More Casts" }}
			</button>
		</div>
	</div>

	<!-- popup -->
	<CustomerCastInfoPopup v-if="showPopup" :cast="selectedCast" :recommendation-list="recommendationList"
		@close="closePopup" />


</template>

<style scoped>
.load-more-btn:disabled {
	display: none;
}
.section-title {
	color: #545454;
	font-size: 22px;
	font-weight: 700;
	margin-bottom: 0px;
}
.profile-image-wrapper {
    height: 45px;
    width: 45px
}
/* fixed tab */
.fixed-tab {
	display: inline-block;
	padding: 12px 54px 7px 54px;
	background-color: #ffffff;
	color: #545454;
	font-weight: 700;
}

/* change tab */
.change-tab {
	display: inline-block;
	padding: 8px 54px 8px 54px !important;
	/* background-color: #ffffff; */
	/* color: #ebedef; */
	border-top-left-radius: 2px;
	border-top-right-radius: 2px;
	font-weight: 700;
	cursor: pointer;
	position: relative;
	font-size: 14px;
	transition: all 0.3s ease;
	white-space: nowrap;
}

.change-tab-bg {
	/* background: #ebedef; */
	display: flex;
	justify-content: flex-start;
	align-items: flex-end;
	max-width: fit-content;
    margin-left: 0;
    overflow-x: scroll;	
}

.change-tab::after {
	content: '';
	position: absolute;
	bottom: 0px;
	left: 0;
	width: 100%;
	height: 1.5px;
}

.change-tab:hover {
	background-color: #e0e0e0;
}

.change-tab.active {
	color: #ffffff;
	background-color: #342f28;
	font-size: 16px;
	/* padding-top: 10px; */
	/* padding-bottom: 10px; */
	padding: 10px 54px 10px 54px !important;
}

.change-tab.active::after {
	background-color: var(--deet-color-gold) !important;
}

/* cast card */

.cast-info {
    padding: 8px;
}

/* .cast-card { */
	/* width: 100%; */
	/* border-radius: 4px; */
	/* box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); */
	/* overflow: hidden; */
/* } */

.cast-image-container {
	position: relative;
	/* width: 100%; */
	/* height: 180px; */
}

/* .uploaded-image { */
	/* width: 100%; */
	/* height: 100%; */
	/* object-fit: cover; */
/* } */

.favorite-icon {
	position: absolute;
	top: 6px;
	right: 8px;
	cursor: pointer;
	font-size: 20px;
	z-index: 10;
}
.icon-star.favorite {
	color: yellow;
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
/*
.icon-star {
	font-size: 24px;
	color: white;
	text-shadow: 0 0 3px rgba(0, 0, 0, 0.5);
}



.cast-info {
	padding: 13px 10px;
}

.cast-details {
	display: flex;
	align-items: baseline;
	margin-bottom: 8px;
	line-height: 1.2;
}

.cast-age,
.cast-area {
	font-size: 8px;
	font-weight: 700;
	margin-right: 4px;
}

.cast-name {
	font-size: 10px;
	font-weight: 700;
	margin-right: 4px;
}

.cast-comment {
	font-size: 9px;
	margin-bottom: 3px;
	line-height: 1.2;
}

.cast-price {
	font-size: 8px;
	text-align: right;
} */
</style>
