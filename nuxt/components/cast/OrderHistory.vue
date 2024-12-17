<script setup>
import { calculateAge } from '@/libs/utilities';
import { useQuery } from '@tanstack/vue-query';
import { ref, watchEffect } from 'vue';
import { castAPI, customerAPI, orderAPI, requestAPI } from '~/libs/api';
import { VueQuery } from '~/libs/enums';
const route = useRoute();
const router = useRouter();

const castData = ref([]);
// タブ切り替え機能、sidebarとリンク紐づけ
const tabs = [
	{ label: '募集案件', value: 'orderAndRequest' },
	{ label: '確定案件', value: 'confirmed' },
	{ label: '案件履歴', value: 'history' },
];
const activeTab = ref('orderAndRequest');

const { data: profileData } = useQuery({
	queryKey: [VueQuery.GET_CAST_PROFILE],
	queryFn: () => castAPI.profile(),
});


const setActiveTab = (tab) => {
	activeTab.value = tab;
	router.push({ query: { ...route.query, tab } });
};
onMounted(async () => {
	if (route.query.tab) {
		activeTab.value = route.query.tab;
		
	}
	// await fetchconfirmedOrders(601);
	await fetchhistoryOrders();
	// console.log(profileData);
});
watch(
	() => route.query.tab,
	(newTab) => {
		if (newTab) {
			activeTab.value = newTab;
		}
	},
);



// Fetch orders with status 600 orderd
const { data: ordersDataOrderd } = useQuery({
  queryKey: [VueQuery.GET_ORDER_BY_STATUS, 600],
  queryFn: () => orderAPI.getOrdersByStatus(600)
});



// Fetch orders with status 601 confirmed
const currentPageConformedData = ref(1);  
const hasMoreConfirmedData = ref(true); 
    const { data:ordersDataConfirmed, isLoading: queryLoading, isError: queryError,refetch } = useQuery({
		queryKey:['GET_ORDER_BY_STATUS', 601, currentPageConformedData.value],
		queryFn:	() => orderAPI.getOrdersByStatusPagar(601, currentPageConformedData.value),
	
   } );

const checkDataLength = (data) => {
  if (data?.data?.orders?.length < 10) {
    hasMoreConfirmedData.value = false; // No more data
  } else {
    hasMoreConfirmedData.value = true; // More data available
  }
};

watch(
  () => ordersDataConfirmed?.value,
  (newData) => {
    if (newData) {
      console.log("Checking ordersDataConfirmed on update");
      checkDataLength(newData);
    }
  },
  { immediate: true }
);
const loadMoreConfirmedData = () => {
      if (hasMoreConfirmedData.value) {
        currentPageConformedData.value += 1; 
        refetch(); 
      }
    };

// Fetch orders with status 601 confirmed
const { data: ordersDataHistory } = useQuery({
  queryKey: [VueQuery.GET_ORDER_BY_STATUS],
  queryFn: () => orderAPI.getHistoryOrders()
});

// Fetch request data
// const { data: requestMatchingData } = useQuery({
//   queryKey: [VueQuery.GET_ORDER_BY_STATUS, 501],
//   queryFn: () => requestAPI.getRequestsByStatusPagar(501)
// });

const currentPagerequestMatchingData = ref(1);  
// const { data: requestMatchingData, isLoading: isLoadingRequestMatching,refetch: refetchRequestMatchingData } = useQuery({
//   queryKey: [VueQuery.GET_ORDER_BY_STATUS],
//   queryFn: () => requestAPI.getRequestsFilteredCastRank()
// });
const hasMorerequestMatchingData = ref(true); 
    const { data:requestMatchingData, isLoading: queryLoadingrequestMatchingData, isError: queryErrorrequestMatchingData,refetchorder } = useQuery({
		queryKey:['GET_ORDER_BY_STATUS', '', currentPagerequestMatchingData.value],
		queryFn:	() => requestAPI.getRequestsFilteredCastRank('', currentPagerequestMatchingData.value),
	
   } );

   const checkDataLengthrequestMatchingData = (data) => {
  if (!data?.data?.requests || data.data.requests.length === 0 || data.data.total === 0) {
    hasMorerequestMatchingData.value = false; // No more data
  } else if (data.data.requests.length < 10) {
    hasMorerequestMatchingData.value = false; // No more data if fewer than 10 items
  } else {
    hasMorerequestMatchingData.value = true; // More data available
  }
};
// Watch the query data
watch(
  () => requestMatchingData?.value,
  (newData) => {
    if (newData) {
      checkDataLengthrequestMatchingData(newData);
    }
  },
  { immediate: true }
);


    // Function to load more data
    const loadMorerequestMatchingData = () => {
      if (hasMorerequestMatchingData.value) {
        currentPagerequestMatchingData.value += 1; // Increment page
        refetchorder(); // Trigger refetch to load next page
      }
    };

// console.log(requestMatchingData);

// Fetch function to get orders by status
const confirmedOrders = ref([]);
const detailshistoryOrders = ref([]);
const fetchconfirmedOrders = async (status) => {
  try {
    const response = await orderAPI.getOrdersByStatus(status);
    const orders = response.data.orders || [];

    for (const order of orders) {
      const customerDetails = await customerAPI.getCustomerById(order.customer_id);
      
      if (customerDetails) {
        order.customerName = customerDetails.data.name_ja;
        order.customerAge = calculateAge(customerDetails.data.birthday); // Calculate age from birthday
        order.customerJob = customerDetails.data.work;
		order.basic_point=customerDetails.data.points_holded;
      }
      
      // Compute the duration in hours
      const startTime = new Date(order.start_date_time);
      const endTime = new Date(order.end_date_time);
	  order.late_point = ((startTime >= 0 && startTime < 5) || (endTime >= 0 && endTime < 5)) ? 5000 : 0;
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
      order.location = `${order.place_prefecture} ${order.place_municipalitie} ${order.place_street}`;
	  
      order.amount = order.details.reduce((sum, detail) => sum + detail.applied_point, 0);
    }
    
    confirmedOrders.value = orders;
  } catch (error) {
    console.error('Failed to fetch confirmed orders:', error);
  }
};

const fetchhistoryOrders = async (status) => {
  try {
    const historyresponse = await orderAPI.getAllOrders();
    const historyOrders = historyresponse.data.orders || [];

    for (const historyorder of historyOrders) {
      const historyCustomerDetails = await customerAPI.getCustomerById(historyorder.customer_id);
      
      if (historyCustomerDetails) {
        historyorder.customerName = historyCustomerDetails.data.name_ja;
        historyorder.customerAge = calculateAge(historyCustomerDetails.data.birthday); 
        historyorder.customerJob = historyCustomerDetails.data.work;
		historyorder.basic_point=historyCustomerDetails.data.points_holded;
      }
      
      // Compute the duration in hours
      const startTime = new Date(historyorder.start_date_time);
      const endTime = new Date(historyorder.end_date_time);
	  historyorder.late_point = ((startTime >= 0 && startTime < 5) || (endTime >= 0 && endTime < 5)) ? 5000 : 0;

      const durationInMs = endTime - startTime;
      historyorder.duration = Math.floor(durationInMs / (1000 * 60 * 60)); 
	  historyorder.castData = await Promise.all(historyorder.details.map(async (detail) => {
      const historycastInfo = await castAPI.getCastInfo(detail.cast_id);
	 
        return {
          id: detail.id,
          castId: detail.cast_id,
          appliedPoint: detail.applied_point,
          ...historycastInfo, 
        };
      }));
   
	historyorder.meetingTime = historyorder.planned_meeting_time;
	historyorder.location = `${historyorder.place_prefecture} ${historyorder.place_municipalitie} ${historyorder.place_street}`;
	  
	historyorder.amount = historyorder.details.reduce((sum, detail) => sum + detail.applied_point, 0);
    }
    
    detailshistoryOrders.value = historyOrders;
  } catch (error) {
    console.error('Failed to fetch confirmed orders:', error);
  }
};

// const calculateAge = (birthday) => {
//   const birthDate = new Date(birthday);
//   const today = new Date();
//   let age = today.getFullYear() - birthDate.getFullYear();
//   const monthDifference = today.getMonth() - birthDate.getMonth();
  
//   // Adjust age if the birthday hasn't occurred yet this year
//   if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthDate.getDate())) {
//     age--;
//   }

//   return age;
// };
watchEffect(() => {
  console.log('historyOrders updated:', detailshistoryOrders.value); // Logs changes in `historyOrders`
});

// ボタンを押したときの機能
const handleStartButtonClick = () => {
	console.log('開始されました');
};

const handleCallRequestApprove = (order) => {
	console.log('呼び出し依頼が了承されました', order.id);
};

const handleCallRequestReject = (order) => {
	console.log('呼び出し依頼が拒否されました', order.id);
};

const toggleDetails = (order) => {
	order.showDetails = !order.showDetails;
};




// 画像ダミーデータ
const dummyImageSrc =
	'data:image/gif;base64,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';

// // 詳細用仮データ
// const castData = ref([
// 	{ id: 1, status: '承諾', image: dummyImageSrc },
// 	{ id: 2, status: '承諾', image: dummyImageSrc },
// 	{ id: 3, status: '確認中', image: dummyImageSrc },
// 	{ id: 4, status: '確認中', image: dummyImageSrc },
// ]);
</script>

<template>
  <div class="container-fluid main-area order-history" :class=route.query.tab>
    <div class="status-tab-bg content mb-0 p-0 mt-3">
      <div v-for="tab in tabs" :key="tab.value" :class="['status-tab small', { active: activeTab === tab.value }]"
        @click="setActiveTab(tab.value)">
        {{ tab.label }}
      </div>
    </div>


    <div class="content order-data">
      <div class="content-body">

        <!-- 呼び出し依頼タブ -->
        <template v-if="activeTab === 'orderAndRequest'">
          <div class="content content-with-tab p-3 pb-1">
            <div class="content-body">
              <div v-if="ordersDataOrderd?.data.length > 0">
              <p class="fs-3 fw-bold">Deet案件</p>
              <CastOrderDetails button-text="お受けする" button-clicked-text="お受け済み" button-class="deet-btn"
                badge-text="Deet" @button-clicked="handleScheduledButtonClick" :cast-data="castData"
                :showRejectButton="true" :order-data="ordersDataOrderd?.data?.orders"
                :profile-data="profileData?.data.cast" />
              <hr>
              </div>
              <p class="fs-3 fw-bold">コール案件</p>
              <CastRequestDetails button-text="応募する" button-clicked-text="応募済み" button-class="deet-btn"
                badge-text="募集案件" :status-data="statusData" :cast-data="castData" :showRejectButton="true"
                :request-data="requestMatchingData?.data?.requests" :profile-data="profileData?.data" />
              <div v-if="hasMorerequestMatchingData" class="text-center">
                <button @click="loadMorerequestMatchingData" class="btn btn-primary">もっと見る</button>
              </div>
              <div v-else>
                <p>案件はありません</p>
              </div>

            </div>

          </div>
        </template>





        <!-- confirmed orders  -->
        <template v-if="activeTab === 'confirmed'">
          <div class="content content-with-tab p-3">
            <div class="content-body">
              <CastOrderDetails button-text="開始する" button-clicked-text="開始済み" button-class="deet-btn full-btn"
                badge-text="Deet" @button-clicked="handleScheduledButtonClick" :cast-data="castData"
                :order-data="ordersDataConfirmed?.data?.orders" :profile-data="profileData?.data.cast" />
            </div>
            <!-- Load More Button -->
            <div v-if="hasMoreConfirmedData">
              <button @click="loadMoreConfirmedData" class="btn btn-primary">もっと見る</button>
            </div>

            <!-- No More Data Message -->
            <div v-if="ordersDataConfirmed?.data.length == 0">
              <p>案件はありません</p>
            </div>

          </div>
        </template>


        <!-- 履歴タブ -->
        <template v-if="activeTab === 'history'">
          <div class="content content-with-tab p-3">
            <div class="content-body">
              <CastOrderDetails button-text="開始する" button-clicked-text="開始済み" button-class="deet-btn full-btn"
                badge-text="Deet" @button-clicked="handleScheduledButtonClick" :show-button=false :cast-data="castData"
                :order-data="ordersDataHistory?.data?.orders" :profile-data="profileData?.data.cast" />
            </div>
          </div>
        </template>




      </div>
    </div>
  </div>
</template>

<style scoped>
.main-area .order-history {
	display: none;
	
}

.status-tab {
	display: none;
	padding: 10px 28px;
	color: #fff;
	&.active {
		display: block
	}
}
.status-tab.active::after {
    background-color: transparent !important;
}
.status-tab.active,
.status-tab.hover {
	color: #fff;
	background-color: #342f28;
}
.status-tab-bg {
	background: transparent !important;
}

.order-data {
	/* border-radius: 0 6px 6px 6px; */
}

.status-badge {
	display: inline;
}

.order-type {
	&.request {
		display: none;
	}
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
