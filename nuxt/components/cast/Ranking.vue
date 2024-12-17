<script setup>
// 画像ダミーデータ
const dummyImageSrc =
	'data:image/gif;base64,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';

// cast_ranking仮データ
const castRankingData = ref([
	{ id: 1, name: '名前1', age: 25, area: '地区A', sales: 2000000, price: 5000 },
	{ id: 2, name: '名前2', age: 28, area: '地区B', sales: 1800000, price: 5500 },
	{ id: 3, name: '名前3', age: 23, area: '地区C', sales: 1000000, price: 4500 },
	{ id: 4, name: '名前4', age: 26, area: '地区D', sales: 950000, price: 5000 },
	{ id: 5, name: '名前5', age: 24, area: '地区E', sales: 900000, price: 4800 },
	{ id: 6, name: '名前6', age: 27, area: '地区F', sales: 850000, price: 5200 },
	{ id: 7, name: '名前7', age: 29, area: '地区G', sales: 800000, price: 5300 },
	{ id: 8, name: '名前8', age: 22, area: '地区H', sales: 750000, price: 4700 },
	{ id: 9, name: '名前9', age: 25, area: '地区I', sales: 700000, price: 4900 },
	{
		id: 10,
		name: '名前10',
		age: 26,
		area: '地区J',
		sales: 650000,
		price: 5100,
	},
	{
		id: 11,
		name: '名前11',
		age: 24,
		area: '地区K',
		sales: 600000,
		price: 4600,
	},
	{
		id: 12,
		name: '名前12',
		age: 28,
		area: '地区L',
		sales: 550000,
		price: 5400,
	},
	{
		id: 13,
		name: '名前13',
		age: 23,
		area: '地区M',
		sales: 500000,
		price: 4800,
	},
	{
		id: 14,
		name: '名前14',
		age: 24,
		area: '地区O',
		sales: 450000,
		price: 4200,
	},
	{
		id: 15,
		name: '名前15',
		age: 21,
		area: '地区P',
		sales: 400000,
		price: 4300,
	},
	{
		id: 16,
		name: '名前16',
		age: 27,
		area: '地区Q',
		sales: 350000,
		price: 4600,
	},
]);

// バッジの色を決定する関数
const getBadgeColor = (index) => {
	switch (index) {
		case 0:
			return 'ranking-badge-gold';
		case 1:
			return 'ranking-badge-silver';
		case 2:
			return 'ranking-badge-bronze';
		default:
			return '';
	}
};

// 数値の3桁ごとにカンマを入れる関数
const formatNumber = (value) => {
	return value.toLocaleString();
};
</script>

<template>
	<div class="container">
		<div class="content">
			<h2 class="ranking__title">2024年7月 売上ランキング</h2>
			<!-- Top 3 ranking -->
			<div class="d-flex justify-content-center align-items-end ranking__top">
				<!-- 1st -->
				<div class="ranking__card ranking__card-first">
					<div
						class="ranking__card-image__container ranking__card-image__container-first"
					>
						<img
							:src="dummyImageSrc"
							alt="cast image"
							class="ranking__card-img"
						/>
						<span class="ranking__badge ranking-badge-gold ranking__badge-first"
							>1</span
						>
					</div>
					<div class="ranking__card-info">
						<div
							class="d-flex justify-content-start align-items-end fw-bold mb-2"
						>
							<span class="me-2">{{ castRankingData[0].name }}</span>
							<div class="d-flex mb-1 text-8px">
								<span class="me-1">{{ castRankingData[0].age }}歳</span>
								<span class="">{{ castRankingData[0].area }}</span>
							</div>
						</div>
						<div
							class="d-flex justify-content-end align-items-end fw-bold mb-1"
						>
							<span class="mb-1 me-2 text-8px">売上金額</span>
							<span class="text-20px"
								>{{ formatNumber(castRankingData[0].sales) }}P</span
							>
						</div>
						<div class="text-end text-8px">
							{{ formatNumber(castRankingData[0].price) }}P / 30min
						</div>
					</div>
				</div>

				<!-- 2nd and 3rd -->
				<div
					class="ranking__card"
					v-for="(cast, index) in castRankingData.slice(1, 3)"
					:key="cast.id"
				>
					<div class="ranking__card-image__container">
						<img
							:src="dummyImageSrc"
							:alt="cast.name"
							class="ranking__card-img"
						/>
						<span :class="['ranking__badge', getBadgeColor(index + 1)]">
							{{ index + 2 }}
						</span>
					</div>
					<div class="ranking__card-info">
						<div
							class="d-flex justify-content-start align-items-end fw-bold mb-2"
						>
							<span class="me-2 text-12px">{{ cast.name }}</span>
							<div class="d-flex mb-2px text-8px">
								<span class="me-1">{{ cast.age }}歳</span>
								<span class="">{{ cast.area }}</span>
							</div>
						</div>
						<div
							class="d-flex justify-content-end align-items-end fw-bold mb-1"
						>
							<span class="mb-1 me-2 text-8px">売上金額</span>
							<span class="fs-4">{{ formatNumber(cast.sales) }}P</span>
						</div>
						<div class="text-end text-8px">
							{{ formatNumber(cast.price) }} / 30min
						</div>
					</div>
				</div>
			</div>

			<!-- Ranking list for 4th~ -->
			<div class="ranking__list">
				<div
					v-for="(cast, index) in castRankingData.slice(3)"
					:key="cast.id"
					class="d-flex justify-content-center align-items-center ranking__list-item"
				>
					<img
						:src="dummyImageSrc"
						:alt="cast.name"
						class="ranking__list-img"
					/>
					<div class="fw-bold fs-4 me-3">{{ cast.name }}</div>
					<div class="fw-bold text-8px me-4">
						<span class="me-1">{{ cast.age }}歳</span>
						<span class="">{{ cast.area }}</span>
					</div>
					<div class="fw-bold text-12px me-4">
						{{ formatNumber(cast.sales) }}P
					</div>
					<div class="text-8px">{{ formatNumber(cast.price) }}P / 30min</div>
				</div>
			</div>
		</div>
	</div>
</template>
<style scoped>
.text-8px {
	font-size: 8px;
}

.text-10px {
	font-size: 10px;
}

.text-12px {
	font-size: 12px;
}


.text-20px {
	font-size: 20px;
}

.mb-2px {
	margin-bottom: 2px;
}

.ranking__title {
	font-size: 44px;
	font-weight: 700;
	margin: 40px 0 60px 0;
	text-align: center;
}

.ranking__top {
	max-width: 900px;
	gap: 80px;
	margin: 0 auto 80px auto;
}

/* 共通のカードスタイル */
.ranking__card {
	width: 180px;
	border-radius: 4px;
	box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.ranking__card-image__container {
	position: relative;
	width: 100%;
	height: 180px;
}

.ranking__card-img {
	width: 100%;
	height: 100%;
	object-fit: cover;
	border-radius: 4px 4px 0 0;
}

.ranking__card-info {
	padding: 10px;
}

.ranking__badge {
	position: absolute;
	top: -18px;
	left: -18px;
	width: 48px;
	height: 48px;
	border-radius: 50%;
	display: flex;
	justify-content: center;
	align-items: center;
	font-size: 32px;
	font-weight: bold;
	color: white;
	line-height: 1;
	padding-top: 4px;
}

.ranking-badge-gold {
	background-color: #f6cd00;
}

.ranking-badge-silver {
	background-color: #98a2af;
}

.ranking-badge-bronze {
	background-color: #8b6600;
}

/* 1位のスタイル */
.ranking__card-first {
	width: 240px;
}

.ranking__card-image__container-first {
	position: relative;
	width: 100%;
	height: 200px;
}

.ranking__badge-first {
	width: 60px;
	height: 60px;
	font-size: 44px;
	padding-top: 6px;
}

/* 4位以降のリストスタイル */
.ranking__list {
	max-width: 500px;
	margin: 0 auto;
}

.ranking__list-item {
	padding: 10px 15px;
}

.ranking__list-img {
	width: 100px;
	height: 70px;
	object-fit: cover;
	margin-right: 30px;
	border-radius: 4px;
}

@media (max-width: 768px) {
	.ranking__top {
		flex-direction: column;
		align-items: center !important;
		gap: 60px;
	}

	.ranking__list {
		width: 90%;
		max-width: 500px;
	}

	.ranking__list-item {
		flex-direction: column;
		padding: 15px 10px;
		margin-right: 0 !important;
	}

	.ranking__list-img {
		width: 100px;
		height: 80px;
		margin-right: 0 !important;
		margin-bottom: 10px;
	}

	.me-3 {
		margin-right: 0 !important;
	}

	.me-4 {
		margin-right: 0 !important;
	}

	.ranking__title {
		font-size: 32px;
		margin: 40px 0 60px 0;
	}
}
</style>
