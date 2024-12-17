<script setup>
// タブ切り替え機能
const tabs = [
	{ label: 'お気に入り女性会員', value: 'favorite' },
	{ label: '高満足度', value: 'highSatisfaction' },
	{ label: '人気急上昇', value: 'trending' },
	{ label: '20-25歳', value: 'age20to25' },
	{ label: '25-30歳', value: 'age25to30' },
];
const activeTab = ref('favorite');
const setActiveTab = (tabValue) => {
	activeTab.value = tabValue;
};

// 星のアイコン表示切替、アイコンクリック時cast_cardのページ遷移イベントが発火しないようにする
const toggleFavorite = (event, cast) => {
	event.stopPropagation();
	cast.isFavorite = !cast.isFavorite;
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
			'一言コメント一言コメント一言コメント一言コメント一言コメント一言コメント一言コメント一言コメント',
		price: 5000,
		isOnline: true, // 仮にすべての女性会員をオンライン状態に設定
	}));
};
const casts = ref(generateDummyCasts(15));

// cast list をクリックしたときpopupを出す機能
const showPopup = ref(false);
const selectedCast = ref(null);

const openPopup = (cast) => {
	selectedCast.value = cast;
	showPopup.value = true;
};
const closePopup = () => {
	showPopup.value = false;
};
</script>

<template>
	<div class="container">
		<!-- 共通切り替えタブ Common switching tabs あとでレスポンシブ対応する -->
		<div class="change-tab-bg">
			<div
				v-for="tab in tabs"
				:key="tab.value"
				:class="['change-tab', { active: activeTab === tab.value }]"
				@click="setActiveTab(tab.value)"
			>
				{{ tab.label }}
			</div>
		</div>
		<div class="content">
			<!-- お気に入り女性会員 Favorite cast -->
			<div v-if="activeTab === 'favorite'">
				<div class="card-grid">
					<div
						v-for="cast in casts"
						:key="cast.id"
						class="cast-card"
						@click="openPopup(cast)"
					>
						<div class="cast-image-container">
							<img
								:src="dummyImageSrc"
								alt="cast image"
								class="uploaded-image"
							/>
							<div
								class="favorite-icon"
								@click.stop="toggleFavorite($event, cast)"
							>
								<i :class="['icon-star', { favorite: cast.isFavorite }]"></i>
							</div>
							<div v-if="cast.isOnline" class="online-status"></div>
						</div>
						<div class="cast-info">
							<div class="cast-details">
								<span class="cast-age">{{ cast.age }}歳</span>
								<span class="cast-name">{{ cast.name }}</span>
								<span class="cast-area">{{ cast.area }}</span>
							</div>
							<div class="cast-comment">{{ cast.comment }}</div>
							<div class="cast-price">
								{{ cast.price.toLocaleString() }}P / 30min
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- 高満足度 High Satisfaction Level -->
			<div v-if="activeTab === 'highSatisfaction'">
				<div class="card-grid">
					<!-- 高満足度の女性会員一覧を追加する -->
				</div>
			</div>

			<!-- 人気急上昇 rapidly rising in popularity -->
			<div v-if="activeTab === 'trending'">
				<div class="card-grid">
					<!-- 人気急上昇の女性会員一覧を追加する -->
				</div>
			</div>

			<!-- Ages 20-25 -->
			<div v-if="activeTab === 'age20to25'">
				<div class="card-grid">
					<!-- 20-25歳の女性会員一覧を追加する -->
				</div>
			</div>

			<!-- Ages 25-30 -->
			<div v-if="activeTab === 'age25to30'">
				<div class="card-grid">
					<!-- 25-30歳の女性会員一覧を追加する -->
				</div>
			</div>
		</div>

		<!-- cast info popup -->
		<CustomerCastInfoPopup
			v-if="showPopup"
			:cast="selectedCast"
			@close="closePopup"
		/>
	</div>
</template>

<style scoped>
.section-title {
	color: #545454;
	font-size: 17px;
	font-weight: 700;
	margin-bottom: 0px;
}

/* change tab */
.change-tab {
	display: inline-block;
	padding: 12px 54px 7px 54px;
	background-color: #ffffff;
	color: #ebedef;
	border-top-left-radius: 2px;
	border-top-right-radius: 2px;
	font-weight: bold;
	cursor: pointer;
	position: relative;
	font-size: 15px;
	transition: all 0.3s ease;
}

.change-tab-bg {
	background: #ebedef;
	display: flex;
	justify-content: flex-start;
	align-items: flex-end;
}

.change-tab::after {
	content: '';
	position: absolute;
	bottom: -1px;
	left: 0;
	width: 100%;
	height: 2px;
}

.change-tab:hover {
	background-color: #e0e0e0;
}

.change-tab.active {
	color: #545454;
	background-color: #ffffff;
	font-size: 17px;
	padding-top: 10px;
	padding-bottom: 10px;
}

.change-tab.active::after {
	background-color: #545454;
}

/* cast card */
.card-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
	gap: 30px;
	justify-content: start;
}

.cast-card {
	width: 100%;
	border-radius: 4px;
	box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
	overflow: hidden;
}

.cast-image-container {
	position: relative;
	width: 100%;
	height: 180px;
}

.uploaded-image {
	width: 100%;
	height: 100%;
	object-fit: cover;
}

.favorite-icon {
	position: absolute;
	top: 10px;
	left: 10px;
	cursor: pointer;
	z-index: 10;
}

.icon-star {
	font-size: 24px;
	color: #000;
}

.icon-star.favorite {
	color: yellow;
}

.online-status {
	position: absolute;
	top: 10px;
	right: 10px;
	width: 12px;
	height: 12px;
	border-radius: 50%;
	background-color: #38ff60;
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
	font-weight: bold;
	margin-right: 4px;
}

.cast-name {
	font-size: 10px;
	font-weight: bold;
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
}
</style>
