<script setup lang="ts">
import { calculateAge, formatDateJpShort, formatDateTimeJp, formatMeetingTime, formatNumber, getStatusCast } from '@/libs/utilities';
import { CModal, CModalBody, CModalFooter, CModalHeader } from '@coreui/vue';
import Swal from 'sweetalert2';
import { ref, watchEffect } from 'vue';
import '~/assets/css/cast/components/order_details.css';
import { castAPI, customerAPI, orderAPI } from '~/libs/api';

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
  orderData: {
    type: Array,
    required: false,
  },
  profileData: {
    type: Object,
    required: false,
  },
  progress: {
    type: String,
    default: '',
  },
  progressOrderAmount: {
    type: Number,
    default: 0,
  },
  designatedMatching: {
    type: Object,
    required: true,
  },
  refetchAggregate: Function,
	refetchProgress: Function,
	refetchComfirmed: Function,
	refetchDeet: Function,
	refetchRequest: Function,
});

const isMainButtonClicked = ref(false);
const isRejectButtonClicked = ref(false);

const isloading = ref(true);

const selectedOrder = ref({});
const editableCastMemo = ref('');
const isMemoModalVisible = ref(false); // モーダルの表示状態
const showOrderMemos = (order) => {
  selectedOrder.value = order; // 選択された女性会員の情報をセット
  editableCastMemo.value = order.details[0].cast_memo;
  isMemoModalVisible.value = true; // モーダルを表示
};
// if (props.orderData?.length > 0){
//   if(props.orderData[0].status == 601 || (props.orderData[0].status == 602 && props.orderData[0].details[0].start_date_time == null)) {
//     if(props.orderData[0].details[0].work_status == 111) {
//       isMainButtonClicked.value = true;
//     }
//     // console.log(props.orderData);
//     // isMainButtonClicked.value = true;
//     // buttonClickedText.value = "別案件進行中";
//   }
// }
const saveCastMemo = async (id,value) => {
  // Define a mapping of form fields to original data keys
  const sendData = {
    detail_id: id, 
    cast_memo: value, 
  };
    // console.log(selectedOrder);

  try {
    await orderAPI.saveCastMemo(sendData);
    isMemoModalVisible.value = false;    
    Swal.fire('処理成功', '処理が成功しました', 'success');
    selectedOrder.value.details[0].cast_memo = sendData.cast_memo;

  } catch (error) {
    console.error('Error updating order data:', error);
    Swal.fire('処理失敗', '処理が失敗しました', 'error');
  }
};


const emit = defineEmits(['buttonClicked', 'rejectButtonClicked']);

const isConditionStart = (plannedMeetingDateTime: string) => {
    if(props.progress === 'confirmed'){
      const currentTime = new Date();
      const meetingTime = new Date(plannedMeetingDateTime);
      return (meetingTime - currentTime) < 30 * 60 * 1000;
    } else {
      return true;
    }
};

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
  // console.log('avatarURL: ', avatarURL);
  return avatarURL ? avatarURL : 'data:image/gif;base64,iVBORw0KGgoAAAANSUhEUgAAALcAAACICAYAAAClQnrnAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAAEnQAABJ0Ad5mH3gAAABhaVRYdFNuaXBNZXRhZGF0YQAAAAAAeyJjbGlwUG9pbnRzIjpbeyJ4IjowLCJ5IjowfSx7IngiOjE4NCwieSI6MH0seyJ4IjoxODQsInkiOjEzNn0seyJ4IjowLCJ5IjoxMzZ9XX3tvd09AAAHaklEQVR4Xu3aZ4sUWxfF8T3mnHPOOWCO6AtB3/mZ7veYTyIoqIgKKkbMUXMWc57LOre299y50zOj4OV51vx/UNhTXd1dZa+za9epbmtvb+8IwFC/5l/ADuGGLcINW4Qbtgg3bBFu2CLcsEW4YYtwwxbhhi3CDVuEG7YIN2wRbtgi3LBFuGGLc...';
};


const dummy_img = 'data:image/gif;base64,iVBORw0KGgoAAAANSUhEUgAAALcAAACICAYAAAClQnrnAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAAEnQAABJ0Ad5mH3gAAABhaVRYdFNuaXBNZXRhZGF0YQAAAAAAeyJjbGlwUG9pbnRzIjpbeyJ4IjowLCJ5IjowfSx7IngiOjE4NCwieSI6MH0seyJ4IjoxODQsInkiOjEzNn0seyJ4IjowLCJ5IjoxMzZ9XX3tvd09AAAHaklEQVR4Xu3aZ4sUWxfF8T3mnHPOOWCO6AtB3/mZ7veYTyIoqIgKKkbMEXMWc57LOre299y50zOj4OV51vx/UNhTXd1dZa+za9epbmtvb+8IwFC/5l/ADuGGLcINW4Qbtgg3bBFu2CLcsEW4YYtwwxbhhi3CDVuEG7YIN2wRbtgi3LBFuGGLcMMW4YYtwg1bhBu2CDdsEW7YItywRbhhi3DDFuGGLcINW4Qbtgg3bBFu2CLcsEW4YYtwwxbhhi3CDVuEG7YIN2wRbtgi3LBFuGGLcMMW4YYtwg1bhBu2CDdsEW7YItywRbhhi3DDFuGGLcINW4Qbtgg3bBFu2CLcsEW4YYtwwxbhhi3CDVuEG7YIN2wRbgP379//sXz69KlZi7b29vaO5rGlN2/exN27d+PFixfx8ePH+PbtWwwZMiRGjhwZEyZMiClTpsSgQYOarX+vO3fuxJcvX8rj2bNnx8CBA8vj9OzZs7h161bz19/69etX9lH7PGnSpBg2bFjzzF/27dvXPIrYsGFDjBs3rvmrb7Ot3B0dHXH16tU4evRoCfe7d+9KsEUhf/r0aVy6dCkOHTr0n1U7hfvGjRtl+fr1a7P2b9oPDcLOi0L/4MGDuHLlShw5ciSuXbvWvALdsQ23glBXwdGjR8fSpUtj5cqVsXjx4vK3fP/+vSz/a/r37x/Tpk0ry8SJE2PAgAFlvQbtzZs3y4BNqta5qLrjL/337t37R/PYhqrdxYsXm78iZs2aFWvWrCmB1pc/ZsyYmDFjRnn8/PnzmDlzZmkRVDlfvnwZT548icePH8fDhw9L1VTVl6FDh5Z/O/v8+XPZVj3vo0ePyue/f/++BFLthN739evX5bk8e2gf9LoPHz6UwaXt1ELps0Wt08aNG2Py5MkxderUso/a1zzL6OyjdaJ1Cr2oZWlrayuPkz4zj0dnAB2jPlufoUHkyrLnPnv2bAmSDB8+PLZt2/avLzwpGAq2+trz58+XL7+V8ePHl0FSB+L27dulTWhV/VetWlWeu3DhQrPm31SddUbR4MjtNJB27NhRHidV63rQ7tmzp/x7+PDhMkhEr6kHod7z8uXLXbZBOmZtP3jw4GaNF8u2RNU2qeq1Crboi9WXnPS3wrZkyZKyqDpmmFU5r1+/Xh5L9sEKrwbI3LlzY8WKFTFv3rwYNWpU2UbPqZrqPetBoYqcbcfYsWObtb3Xm4p77969Mlgy2LrQ1L7p/0RnFe1bq0HpwC7cqmB1lcreujcWLFgQO3fuLFVUsxlali1bFqtXr262iHIhmnSaT8uXL49FixbF9OnTY+HChbFly5ZYt25dOfUrvHrPelZGfb/WaVGL1BO1HWotUg6eVjQro4GX9HnqybVvOpuoYmumyJlduLP3THVV7olO53WVV6+qwVKvqytd9s+iKqmeuaapRrUyv0IDNOeudbY4duxYOXMkVeDuaBDmINeZY86cOeVx0plGg7bVdYQDu3B314L0JKvjmTNn4uDBg7F///7Sz546darZ4p/q+WS1Qpp2PHDgQNleMzV58fcrVHnVUmjR1GEOHB2fZn00cLqTF8HSV+e97cKtU38dcM1a9JYuRBVsBVxVTwFSxWtVJdVjdw6ZXqega45d1fbt27fNM79Ox6MLY7UvmzdvLrM/PdFsSqrbob7ELty60Kr70bpP7U5O/4mCpJ5UPbN61VY9sT5L22zatKkMALUg9YWeKnd9Afoz1C5oNkTL7t27Y/v27aWv76nXTnW78TMD3IlduEUzEElVtL7wq+nUf/r06dJXax46aSajN9Uu2w7NWetCbf369bFr165/VPq6Pajlbfjfpb6Q1rx7fX2QNNfd1XoXluHOGzRJ89eqoFnBFDjNGatHzpsmdStTB11yzryzEydOlPetg6r30QxJ0lkg5V1Gqc8ov2M6Tu1S/gZFIT537tyP/dS1hY77+PHj5TlXtj+cUjXWhV2ryllTC6IvXl92zrbotK4lf2yVVVrrtL3kzRMFWpVS4VVYcnCoRVHLkgNNN3t06zwp+HqtWo3e3MRppdVNnFevXsXJkyd/VOcceLouyKDX27uxrNyiL0xzzfPnz++yxdAUoX5ht3bt2rKtAqZZiJw6VFh0m1oVUO1GV9S+KNAaEAqSWqAMtvpv3T6vzyBqVzS3nGcJDTxdcP7MdOXPULu0devWcpz6TO2njkvB1mfWv1lxZP+T16SWJGcQ9IWOGDGiy1Dpi89pNwWz889Su6KQZmVXiFSRu+vZ9RnZIumOaN3G/C6q3joutUA6bh1bb+5y/j/rM+FG32PblgCEG7YIN2wRbtgi3LBFuGGLcMMW4YYtwg1bhBu2CDdsEW7YItywRbhhi3DDFuGGLcINW4Qbtgg3bBFu2CLcsEW4YYtwwxbhhi3CDVuEG7YIN2wRbtgi3LBFuGGLcMMW4YYtwg1bhBu2CDdsEW7YItywRbhhi3DDFuGGLcINW4Qbtgg3bBFu2CLcsEW4YYtwwxbhhi3CDVuEG7YIN2wRbtgi3LBFuGGLcMMW4YapiD8BUrgTYuWfYhIAAAAASUVORK5CYII=';

const ordersWithCustomerData = ref([]);

const fetchCustomerDataForOrders = async () => {
  if (!Array.isArray(props.orderData)) {
    console.error('orderData is not an array');
    return;
  }

  try {
    const orders = await Promise.all(
      props.orderData.map(async (order) => {
        try {
          const response = await customerAPI.getCustomerById(order.customer_id);
          const castDataList = await Promise.all(
            order.details.map(async (detail) => {
              try {
                const castData = await castAPI.getCastInfo(detail.cast_id);
                return castData.data;
              } catch (error) {
                console.error(`Failed to fetch cast data for cast ID ${detail.cast_id}:`, error);
                return null;
              }
            })
          );
          return {
            ...order,
            customerData: response.data,
            casts: castDataList.filter(cast => cast !== null),
          };
        } catch (error) {
          console.error(`Failed to fetch customer data for order ${order.id}:`, error);
          return {
            ...order,
            customerData: null,
            casts: [],
          };
        }
      })
    );
    // console.log('orders: ============', orders);
    ordersWithCustomerData.value = orders;
    isloading.value = false;
  } catch (error) {
    console.error('Error processing orders:', error);
  }
};


const submitBtn = async (orderData,profileData) => {

// console.log(orderData.details[0]);
// console.log(profileData);

// progress
if(orderData.status === 602 && orderData.details[0].start_date_time !== null){ 

  // orderData.order_type == 'direct' && 
  Swal.fire({
    title: "解散しますか？",
    icon: "warning",
    showCancelButton: true,
    reverseButtons: true,
    confirmButtonText: "はい",
    cancelButtonText: `いいえ`,
}).then(async (result) => {
    /* Read more about isConfirmed, isDenied below */
    if (result.isConfirmed) {
        const params = {
            order_id: orderData.id,
            order_type: orderData.order_type,
            cast_id: profileData.id,
        };
        try {
            const response = await orderAPI.leaveOrder(params);
            Swal.fire({
                icon: 'success',
                title: '解散しました',
            }).then(async () => {
                        // Reload the page after the success alert is closed
                        // location.reload();
                        await props.refetchProgress();
                        await props.refetchAggregate();
                    });
        } catch (error) {
            console.error('Error fetching cast data:', error);
        }
    } else if (result.isDenied) {
        // Swal.fire("Changes are not saved", "", "info");
    }
});


// todays order
} else if(orderData.status == 601 || (orderData.status == 602 && orderData.details[0].start_date_time == null)) {

  Swal.fire({
    title: "この案件を開始しますか？",
    icon: "warning",
    showCancelButton: true,
    reverseButtons: true,
    confirmButtonText: "はい",
    cancelButtonText: `いいえ`,
}).then(async (result) => {
    /* Read more about isConfirmed, isDenied below */
    if (result.isConfirmed) {
        const params = {
            order_id: orderData.id,
            order_type: orderData.order_type,
            cast_id: profileData.id,
        };
        try {
            const response = await orderAPI.startOrder(params);
            Swal.fire({
                icon: 'success',
                title: '案件を開始しました',
            }).then(async () => {
                        // Reload the page after the success alert is closed
                        // location.reload();
                        await props.refetchComfirmed();
                        await props.refetchProgress();
                    });
        } catch (error) {
            console.error('Error fetching cast data:', error);
        }
    } else if (result.isDenied) {
        // Swal.fire("Changes are not saved", "", "info");
    }
});

} else {

  Swal.fire({
    title: "この案件をお受けしますか？",
    icon: "warning",
    showCancelButton: true,
    reverseButtons: true,
    confirmButtonText: "はい",
    cancelButtonText: `いいえ`,
}).then(async (result) => {
    /* Read more about isConfirmed, isDenied below */
    if (result.isConfirmed) {
        const params = {
            order_id: orderData.id,
        };
        try {
            const response = await orderAPI.acceptDeet(params);
            Swal.fire({
                icon: 'success',
                title: '案件をお受けしました',
            }).then(async () => {
                        // Reload the page after the success alert is closed
                        // location.reload();
                        await props.refetchDeet();
                        await props.refetchComfirmed();
                        await props.refetchAggregate();
                    });
        } catch (error) {
            console.error('Error fetching cast data:', error);
        }
    } else if (result.isDenied) {
        // Swal.fire("Changes are not saved", "", "info");
    }
});

}


};

const rejectBtn = async (orderData,profileData) => {

Swal.fire({
    title: "この案件を辞退しますか？",
    icon: "warning",
    showCancelButton: true,
    reverseButtons: true,
    confirmButtonText: "はい",
    cancelButtonText: `いいえ`,
}).then(async (result) => {
    /* Read more about isConfirmed, isDenied below */
    if (result.isConfirmed) {
        const params = {
            order_id: orderData.id,
        };

        try {
            const response = await orderAPI.rejectDeet(params);
            // console.log(response);

            Swal.fire({
                icon: 'success',
                title: '辞退が完了しました',
            }).then(async () => {
                        // Reload the page after the success alert is closed
                        // location.reload();
                        await props.refetchDeet();
                    });
        } catch (error) {
            console.error('Error fetching cast data:', error);
        }

    } else if (result.isDenied) {
        // Swal.fire("Changes are not saved", "", "info");
    }
});
};

// 詳細表示ボタンを押したときの機能
const toggleDetails = (order) => {
	order.showDetails = !order.showDetails;
};

const getTimeDifference = (plannedMeetingDateTime) => {
  const currentTime = new Date();
  const meetingTime = new Date(plannedMeetingDateTime);
  const timeDifference = (meetingTime - currentTime) / (1000 * 60 * 60); // Difference in hours
  console.log("timeDifference:"+timeDifference);
  return timeDifference;  
};

watchEffect(() => {
  // console.log('props.orderData: ', props.orderData);
  // console.log('props.profileData: ', props.profileData);
  fetchCustomerDataForOrders();
});
</script>

<template>
  <div class="wrapper order-details">
    <div v-if="!hideHeader && ordersWithCustomerData && ordersWithCustomerData.length">
      <div class="row inner-card p-2 pb-4 pt-4 m-0 mb-3" v-for="order in ordersWithCustomerData" :key="order.id">
        <!-- {{ order }} -->
        <div class="col-9 d-flex">
          <div class="profile-image-wrapper mb-3">
            <div class="profile-image-area">
              <img :src="order.customerData.images[0]?.image_url || dummy_img" class="profile-image">
            </div>
            <!-- <datalist>{{ order.customerData }}</datalist> -->
          </div>
          <div class="fs-4 ms-4">
            <!-- <span class="fw-bold">お客様</span><br> -->
            <span class="fs-3">{{ order.customerData.nickname }}</span>
            <span class="ms-3">{{ calculateAge(new Date(order.customerData.birthday)) }}歳</span>
            <span class="ms-3">{{ order.customerData.work }}</span>
          </div>
        </div>
        <div class="col-3 text-end order-badge" :class="order.order_type">
          <span class="rank-tag fs-4 me-0" :class="order.order_type" v-if="order.order_type === 'direct'">
            Deet
          </span>


        </div>

        <div class="col-12 mt-3">
          <div class="mb-2 d-flex justify-content-between">
            <div>
              <span class="fs-4 me-2 status-label" :class="`label-${order.status}`">{{
                getStatusCast(order.status) }}</span>
            </div>
            <div class="">
              <span v-if="!order.shown_id" class="fs-5 me-2">ID:{{ order.id }}</span>
              <span v-else class="fs-5 me-2">ID:{{ order.shown_id }}</span>              
              <span class="fs-5 me-2 text-end">{{ formatDateTimeJp(order.created_at) }}</span>
            </div>
          </div>
        </div>
        <!-- <div class="col-12 mb-2">
          <span class="fs-5 me-2">ID:{{ order.id }}</span>
          <span class="fs-5 me-2 status-label" :class="`label-${order.status}`">{{
            getStatusCast(order.status) }}</span>
          <span class="designated rank-tag fs-4 me-0" v-if="order.details[0].designated_point > 0">指名案件</span>
        </div> -->
        <div class="col-6">
          <div class="fs-4 fw-bold"><span class="icon-periscope d-inline-block me-2"></span>合流場所</div>
          <div class="fs-3">{{ order.place_street }}</div>
        </div>
        <div class="col-6">
          <div class="fs-4 fw-bold"><span class="icon-alarm d-inline-block me-2"></span>合流予定</div>
          <div class="fs-3">{{ formatDateJpShort(order.planned_meeting_date_time) }}</div>
        </div>

        <div class="col-6 mt-3">
          <div class="fs-4 fw-bold"><span class="icon-history d-inline-block me-2"></span>リクエスト時間</div>
          <div class="fs-3">{{ formatMeetingTime(order.planned_meeting_time) }}</div>
        </div>
        <!-- {{ order }} -->
        <div class="col-6 mt-3">
          <div class="fs-4 fw-bold"><span class="icon-user d-inline-block me-2"></span>人数</div>
          <div class="fs-3">
            <span v-if="order.order_type === 'direct'">{{ order.details.length }}人</span>
            <span v-else>{{ order.number_of_people }}人</span>
          </div>
        </div>

        <div v-if="order.status !== 604" class="col-6 mt-3">
          <div class="fs-4 fw-bold">基本報酬</div>
          <div class="fs-3">{{ formatNumber((order.details[0].applied_point * 2) *
            order.planned_meeting_time?.split(':')[0]) }}P～
          </div>
        </div>
        <div class="col-6 mt-3" v-if="order.details[0].mid_night_fee > 0 && order.status !== 604">
          <div class="fs-4 fw-bold">深夜手当</div>
          <div class="fs-3">{{ formatNumber(order.details[0].mid_night_fee) }}P</div>
        </div>
        <!-- {{ order.details[0] }} -->
        <div class="col-6 mt-3" v-if="order.details[0].designated_point > 0 && order.status !== 604">
          <div class="fs-4 fw-bold">指名手当</div>
          <div class="fs-3">{{ formatNumber(order.details[0].designated_point) }}P</div>
        </div>

        <div class="col-7 mt-4 d-flex justify-content-start">
          <div class="fs-3 fw-bold"><span v-if="order.status !== 604">合計</span><span v-else>獲得ポイント</span>
            {{ formatNumber(((order.details[0].applied_point*2) * order.planned_meeting_time?.split(':')[0]) +
            order.details[0].mid_night_fee + Number(order.details[0].designated_point)) }}
            <!-- {{ formatNumber((currentCastInfo.castBasePoint * 2) * order.duration + order.lateNightPoint) }} -->
            P<span v-if="order.status !== 604">～</span>
          </div>
        </div>

        <!-- {{ order }} -->
        <div v-if="order.status === 604" class="row">
          <!-- <div class="col-6 mt-3">
            <div class="fs-4 fw-bold">開始時間</div>
            <div class="fs-3">{{ formatDateJpShort(order.start_date_time) }}</div>
          </div>
          <div class="col-6 mt-3">
            <div class="fs-4 fw-bold">解散時間</div>
            <div class="fs-3">{{ formatDateJpShort(order.end_date_time) }}</div>
          </div> -->
          <!-- {{ order }}  -->
          <!-- <div class="col-6 mt-3">
            <div class="fs-4 fw-bold">基本報酬</div>
            <div class="fs-3">{{ formatNumber((order.details[0].applied_point * 2) *
              order.planned_meeting_time?.split(':')[0]) }}P</div>
          </div>
          <div class="col-6 mt-3">
            <div class="fs-4 fw-bold">延長報酬</div>
            <div class="fs-3">{{ formatNumber(order.details[0].extra_charge) }}P</div>
          </div>
          <div class="col-6 mt-3">
            <div class="fs-4 fw-bold">深夜報酬</div>
            <div class="fs-3">{{ formatNumber(order.details[0].mid_night_fee) }}P</div>
          </div>
          <div class="col-6 mt-3">
            <div class="fs-4 fw-bold">指名報酬</div>
            <div class="fs-3">{{ formatNumber(order.details[0].designated_point) }}P</div>
          </div> -->
        </div>

        <section class="mb-3">
          <div class="row">
            <div class="col-12 mt-3">
              <div class="fs-4 fw-bold">メモ</div>
              <div class="fs-3">{{ (order.details[0].cast_memo) }}</div>
              <div class="text-end">

              </div>
            </div>
            <div class="col-12 d-flex justify-content-end">
              <button class="btn btn-outline-secondary btn-deet-trans-dark basic-info-edit-btn btn-popup fs-4 mb-2"
                @click="showOrderMemos(order)">
                メモを書く
              </button>
            </div>
          </div>
        </section>

        <section v-if="order.status === 604">
          <div class="row">
            <div class="col-12 d-flex justify-content-end">
              <button class="btn btn-deet-transparent fs-4" @click="toggleDetails(order)">
                {{ order.showDetails ? '閉じる' : '詳細' }}
              </button>
            </div>
          </div>
          <div class="row" v-if="order.showDetails">
            <div class="col-6 mt-3">
              <div class="fs-4 fw-bold">開始時間</div>
              <div class="fs-3">{{ formatDateJpShort(order.start_date_time) }}</div>
            </div>
            <div class="col-6 mt-3">
              <div class="fs-4 fw-bold">解散時間</div>
              <div class="fs-3">{{ formatDateJpShort(order.end_date_time) }}</div>
            </div>
            <!-- {{ order }}  -->
            <div class="col-6 mt-3">
              <div class="fs-4 fw-bold">基本報酬</div>
              <div class="fs-3">{{ formatNumber((order.details[0].applied_point * 2) *
                order.planned_meeting_time?.split(':')[0]) }}P</div>
            </div>
            <div class="col-6 mt-3">
              <div class="fs-4 fw-bold">延長報酬</div>
              <div class="fs-3">{{ formatNumber(order.details[0].extra_charge) }}P</div>
            </div>
            <div class="col-6 mt-3">
              <div class="fs-4 fw-bold">深夜報酬</div>
              <div class="fs-3">{{ formatNumber(order.details[0].mid_night_fee) }}P</div>
            </div>
            <div class="col-6 mt-3">
              <div class="fs-4 fw-bold">指名報酬</div>
              <div class="fs-3">{{ formatNumber(order.details[0].designated_point) }}P</div>
            </div>
          </div>
        </section>


        <div class="col-12 d-flex justify-content-end align-items-end mt-4" v-if="showButton">
          <button v-if="showRejectButton" @click="(event) => { rejectBtn(order, profileData); }"
            class="btn small-btn btn-secondary order-type fs-4 me-2 pt-1 pb-1 ps-5 pe-5" :class="orderType"
            :disabled="isMainButtonClicked || isRejectButtonClicked">
            {{
            isRejectButtonClicked
            ? rejectButtonClickedText
            : rejectButtonText
            }}
          </button>

          <section v-if="progress == 'confirmed'">
            <!-- {{isConditionStart(order.planned_meeting_date_time)}}x -->
            <button
              v-if="progressOrderAmount > 0 && getTimeDifference(order.planned_meeting_date_time) <= 12"
              class="btn entry-btn deet-btn-clickable fs-4 fw-bold mt-2 ms-1 pt-3 pb-3 ps-5 pe-5 btn-secondary"
              disabled>
              参加不可
            </button>
            <button v-else-if="!isConditionStart(order.planned_meeting_date_time)"
              class="btn entry-btn deet-btn-clickable fs-4 fw-bold mt-2 ms-1 pt-3 pb-3 ps-5 pe-5 btn-secondary"
              disabled>
              案件開始前です
            </button>
            <button v-else
              @click="(event) => { submitBtn(order, profileData); }"
              class="btn entry-btn deet-btn-clickable fs-4 fw-bold mt-2 ms-1 pt-3 pb-3 ps-5 pe-5"
              :class="[buttonClass, { 'btn-secondary': isMainButtonClicked }]"
              :disabled="isMainButtonClicked || isRejectButtonClicked">
              {{ isMainButtonClicked ? buttonClickedText : buttonText }}
            </button>
          </section>
          <section v-else>
            <button
              v-if="isConditionStart(order.planned_meeting_date_time)"
              @click="(event) => { submitBtn(order, profileData); }"
              class="btn entry-btn deet-btn-clickable fs-4 fw-bold mt-2 ms-1 pt-3 pb-3 ps-5 pe-5"
              :class="[buttonClass, { 'btn-secondary': isMainButtonClicked }]"
              :disabled="isMainButtonClicked || isRejectButtonClicked">
              {{ isMainButtonClicked ? buttonClickedText : buttonText }}
            </button>
            <button v-else
              class="btn entry-btn deet-btn-clickable fs-4 fw-bold mt-2 ms-1 pt-3 pb-3 ps-5 pe-5 btn-secondary"
              disabled>
              案件開始前です
            </button>
          </section>


        </div>

        <div class="row">

          <div class="col-12">
            <div v-if="showMeetingTime" class="mt-3">
              <div class="mb-1 text-14px fw-bold">合流時刻</div>
              <div class="mb-2 text-16px">2021-06-15 23:55</div>
              <div class="mb-1 text-14px fw-bold">集合まであと</div>
              <div class="text-16px">xx分</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-if="isloading" class="text-center my-5 py-5">
      <LoadingLine></LoadingLine>
    </div>
    <div v-if="!hideHeader && ordersWithCustomerData && ordersWithCustomerData.length == 0 && !isloading"
      class="text-center no-result">
      <p class="h4 mt-5 pt-1">案件はありません</p>
    </div>
  </div>

  <!-- モーダル -->
  <CModal title="メモ情報" :visible="isMemoModalVisible" v-on:close="() => { isMemoModalVisible = false; }">
    <CModalHeader>
      <h5 class="modal-title fs-4">メモ情報</h5>
    </CModalHeader>
    <CModalBody>
      <div class="row fs-4">
        <div class="col-12">
          <p><strong>メモ</strong></p>
          <textarea v-model="editableCastMemo" class="form-control" rows="4">
          </textarea>
        </div>
      </div>
    </CModalBody>
    <CModalFooter>
      <div class="container mb-3 fs-4">
        <div class="row">
          <div class="col-12 text-end">
            <button class="btn deet-btn fs-4" @click="saveCastMemo(selectedOrder.details[0].id, editableCastMemo)">
              保存
            </button>
          </div>
        </div>
      </div>
    </CModalFooter>
  </CModal>


</template>

<style scoped>
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
