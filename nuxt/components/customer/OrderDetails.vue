<script setup>
const props = defineProps({
	castData: {
		type: Array,
		required: true,
	},
	orderData: {
		type: Array,
		required: true,
	},
	showBadges: {
		type: Boolean,
		default: true,
	},
});
const dummyImageSrc =
	'data:image/gif;base64,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';
const emit = defineEmits(['open-cast-info']);

const getBadgeClass = (status) => {
	return status === '承諾' ? 'badge-accepted' : 'badge-pending';
};
const formatNumber = (value) => {
	return Number(value).toLocaleString();
};
const openCastInfo = (cast) => {
	emit('open-cast-info', cast);
};

const rankPoint = {
    'BLACK': 10000,
    'PLATINUM': 8000,
    'GOLD': 6000,
};
const rankPointCalc = {
    'BLACK': 0,
    'PLATINUM': 0,
    'GOLD': 0,
};
const rankPersonCalc = {
    'BLACK': 0,
    'PLATINUM': 0,
    'GOLD': 0,
};
let subTotalPerHour = 0;
let midNightFeeTotal = 0;

props.castData.forEach(cast => {
	subTotalPerHour += parseFloat(cast.appliedPoint)*2;
	rankPointCalc[cast.data.cast.rank] += parseFloat(cast.appliedPoint);
	rankPersonCalc[cast.data.cast.rank]++;
});

if(props.orderData.details){
	props.orderData.details.forEach(detail => {
		midNightFeeTotal += parseFloat(detail.mid_night_fee);
	});
}




</script>

<template>
	<div class="wrapper">
		<div class="d-flex justify-content-between align-items-end">
			<div class="row align-items-end justify-content-around">
				<!-- cast card -->
				<div class="cast-card col-6 p-0 mb-3" v-for="cast in castData" :key="cast.id" @click="openCastInfo(cast)">
					<div class="cast-image">
						<img :src="cast.data?.images[0]?.image_url || dummyImageSrc" alt="cast image" class="uploaded-image" />
						<!-- <span v-if="showBadges" :class="['cast-pic-badge', getBadgeClass(cast.status)]" class="fs-6">
							{{ cast.status }}
						</span> -->
					</div>
					<div class="cast-info">
						<datalist>{{ cast.data?.images[0]?.image_url }}</datalist>
						<div class="d-flex justify-content-between">
							<span class="fs-5">{{ new Date().getFullYear() - new Date(cast.data.cast?.birthday).getFullYear() }}歳</span>
							<span class="fs-5">{{ cast.data.cast.nickname }}</span>
						</div>
						<div class="fs-5 text-end">{{cast.data.cast.city}}</div>
						<div class="fs-5 text-end fw-normal">{{ cast.data.cast?.basic_point_price ? cast.data.cast.basic_point_price.toLocaleString() : "N/A" }}P / 30min</div>
					</div>
				</div>

				<!-- meeting place -->
				<div class="meeting-place mt-4">
					<!-- <div class="mb-3"> -->
						<!-- <div class="fw-bold fs-4 mb-1">ID</div> -->
						<!-- <div class="fw-normal fs-3">{{ orderData.id }}</div> -->
					<!-- </div> -->
					<!-- <div class="mb-3"> -->
						<!-- <div class="fw-bold fs-4 mb-1">集合住所</div> -->
						<!-- <div class="fw-normal fs-3">{{ orderData.place_prefecture }}{{ orderData.place_municipalitie }}{{ orderData.place_street }}</div> -->
					<!-- </div> -->
					<div>
						<div class="fw-bold fs-4 mb-1">住所</div>
						<div class="fw-normal fs-3 mt-2">
							<span class="" style="line-height: 1.8;">東京都港区六本木4-12-12穂高ビル2階</span><br>
							{{ orderData.place_desc }}
						</div>
					</div>
				</div>

				<!-- price -->
				<div class="price-summary mt-4 fs-4">
					<div class="text-end fw-normal">
						<div v-if="orderData.order_type === 'request'">
							<div v-if="rankPointCalc['GOLD'] != 0">GOLDクラス {{ formatNumber(rankPoint['GOLD']) }}P / 30分 x {{ rankPersonCalc['GOLD'] }}人</div>
							<div v-if="rankPointCalc['PLATINUM'] != 0">PLATINUMクラス {{ formatNumber(rankPoint['PLATINUM']) }}P / 30分 x {{ rankPersonCalc['PLATINUM'] }}人</div>
							<div v-if="rankPointCalc['BLACK'] != 0">BLACKクラス {{ formatNumber(rankPoint['BLACK']) }}P / 30分 x {{ rankPersonCalc['BLACK'] }}人</div>
						</div>
						<div v-else>
							{{ castData[0].appliedPoint ? formatNumber(castData[0].appliedPoint.toLocaleString()) : "N/A" }}P/30分
						</div>
						<hr />
						<div class="d-flex justify-content-end align-items-center fw-normal mb-2">
							<div class="me-30px">時間</div>
							<div>{{ orderData.duration }}</div>
						</div>
						<hr />
						<div class="d-flex justify-content-between align-items-center fw-normal mb-2">
							<div>小計</div>
							<div>{{ formatNumber(subTotalPerHour) }} x {{ orderData.duration }}</div>
						</div>
					</div>
					<div class="d-flex justify-content-between align-items-center fw-bold">
						<div>合計</div>
						<div>{{ formatNumber(
							subTotalPerHour * parseFloat(orderData.duration.replace(/[^0-9]/g, '')) + midNightFeeTotal
						) }}P</div>
					</div>
				</div>


			</div>


		</div>
	</div>
</template>

<style scoped>
.wrapper {
	/* color: #545454; */
	padding: 25px 0 10px 0;
}

.mb-30px {
	margin-bottom: 30px;
}

.mb-40px {
	margin-bottom: 40px;
}

.me-30px {
	margin-right: 30px;
}

.text-5px {
	font-size: 5px;
}

.text-7px {
	font-size: 7px;
}

.text-9px {
	font-size: 9px;
}

.text-10px {
	font-size: 10px;
}

hr {
	margin: 8px 0;
}

/* cast card */
.cast-card {
	/* width: 120px; */
	border-radius: 3px;
	box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
	overflow: hidden;
	cursor: pointer;
	transition: all 0.3s ease;
}
.cast-card:hover {
	transform: translateY(-5px);
}

.cast-image {
	position: relative;
	width: 100%;
	/* height: 83px; */
}

.uploaded-image {
	width: 100%;
	height: 100%;
	object-fit: cover;
}

.cast-info {
	padding: 5px;
}

.cast-pic-badge {
		position: absolute;
		top: 0px;
		left: 0px;
		font-weight: normal;
		width: 4rem;
		height: 2.5rem;
		border-radius: 3px 0 0 0;
		z-index: 1;
		display: flex;
		justify-content: center;
		align-items: center;
	}

.badge-accepted {
	background-color: #0090ff;
	/* color: #ffffff; */
}

.badge-pending {
	background-color: #545454;
	/* color: #ffffff; */
}

/* meeting place */
.meeting-place {
	font: normal normal normal 12px/16px Meiryo UI;
	/* color: #707070; */
	/* margin: 0px 30px; */
}
</style>
