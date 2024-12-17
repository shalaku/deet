<script setup>
import { customerAPI } from '@/libs/api/customer-api';
import { computed, ref } from 'vue';
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
		rank: 'A',
	}));
};
const casts = ref(generateDummyCasts(18));

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

const dummyRanks = ref([
	{label:'GOLD',value:'GOLD'},
	{label:'PLATINUM',value:'PLATINUM'},
	{label:'BLACK',value:'BLACK'},
]);
const selectedTags = ref([]);
const selectedRtags = ref([]);
const isModalOpen = ref(false);

const toggleTag = (tag) => {
	const index = selectedTags.value.indexOf(tag);
	if (index === -1) {
		selectedTags.value.push(tag);
	} else {
		selectedTags.value.splice(index, 1);
	}
};
const toggleRtag = (tag) => {
	const index = selectedRtags.value.indexOf(tag);
	if (index === -1) {
		selectedRtags.value = [];
		selectedRtags.value.push(tag);
	} else {
		selectedRtags.value.splice(index, 1);
	}
};
const removeTag = (tag) => {
	const index = selectedTags.value.indexOf(tag);
	if (index !== -1) {
		selectedTags.value.splice(index, 1);
	}
};

// cast list をクリックしたときpopupを出す機能
const showPopup = ref(false);
const selectedCast = ref(null);
const recommendationList = ref([]);

const openPopup = (cast,castId) => {
	selectedCast.value = cast;
	recommendationList.value = castList.value.filter(cast => cast.cast.id !== castId);
	console.log("reco",recommendationList.value);
	showPopup.value = true;
};
const closePopup = async () => {
	showPopup.value = false;
	await fetchFavoriteStatus();
};

const ageRange = Array.from({ length: 21 }, (_, i) => i + 20);
const heightRange = Array.from({ length: 9 }, (_, i) => i*5 + 140);

const minAge = ref(ageRange[0]);
const maxAge = ref(ageRange[10]);
const minHeight = ref(heightRange[0]);
const maxHeight = ref(heightRange[4]);
const maxAgeOptions = computed(() => {
	return minAge.value ? ageRange.filter((age) => age > minAge.value) : ageRange;
});
const maxHeightOptions = computed(() => {
	return minHeight.value ? heightRange.filter((height) => height > minHeight.value) : heightRange;
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
const selectedLocation= ref('');
const city=ref('');
const castList=ref([]);
const currentPage = ref(1); 
const hasMoreCasts = ref(true); 
const isLoading = ref(false); 

// const filters = ref({
// 	  city: '',
// 	  age:'',
//       minAge: '',
//       maxAge: '',
// 	  height:'',
//       minHeight: '',
//       maxHeight: '',
// 	  tags:[],
// 	  ranks:[],

//     });
const fetchFavoriteStatus = async () => {
  try {
    const response = await customerAPI.getFavCasts();
    const favoriteCastIds = response.data.casts.map(cast => cast.cast.id);
	console.log(favoriteCastIds); 
    
    // Check each cast and update its 'isFavorite' status
    castList.value.forEach(cast => {
      cast.isFavorite = favoriteCastIds.includes(cast.cast.id);
	  console.log(cast.isFavorite);
    });

  

  } catch (error) {
    console.error('Error fetching favorite status:', error);
  }
};

// 検索ボタンのクリックハンドラ
const submitSearch = async (param) => {
	// console.log(param);
    
	if (isLoading.value || !hasMoreCasts.value) {
		if(param !== 'reset_search'){
			return;
		}
	}
	if(param == 'reset_search'){
		castList.value = [];
		currentPage.value = 1;
		hasMoreCasts.value = true;
	}

    const params = {
        
        page: currentPage.value, 
        prefecture: selectedLocation.value, 
        height: `${minHeight.value}-${maxHeight.value}`,
        age: `${minAge.value}-${maxAge.value}`,
        tag_of_spec: selectedTags.value,
        rank: selectedRtags.value,
    };

    try {
        isLoading.value = true;
        const response = await customerAPI.getCast(params); 
        const newCasts = response.data.casts;
		

        castList.value = [...castList.value, ...newCasts]; 
        if (newCasts.length < 10) {
            hasMoreCasts.value = false; 
        } else {
            currentPage.value += 1; 
        }
		await fetchFavoriteStatus();
    } catch (error) {
        console.error("Error during search:", error);
    } finally {
        isLoading.value = false; 
    }
};

</script>

<template>
	<div class="container-fluid p-0">
		<div class="content p-3 pb-4 mb-4">
			<!-- Casting Search Criteria section -->
			<h2 class="section-title mt-4 mb-5 pt-0">Deetで探す</h2>
			<form @submit.prevent="submitSearch('reset_search')">
				<div class="row g-4">
					<!-- Residence Selection -->
					<div class="col-md-2 d-flex flex-column gap-3">
						<label class="fs-4">居住地</label>
						<select class="form-select fs-4" v-model="selectedLocation">
							<option disabled value="">選択ください</option>
							<option value="東京都">東京都</option>
							<option value="神奈川県">神奈川県</option>
							<option value="千葉県">千葉県</option>
							<option value="埼玉県">埼玉県</option>
							<option value="茨城県">茨城県</option>
						</select>
					</div>

					<!-- Age Selection -->
					<div class="col-md-2 d-flex flex-column gap-3">
						<label class="fs-4">年齢</label>
						<div class="d-flex align-items-center gap-2">
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

					<!-- Height Selection -->
					<div class="col-md-2 d-flex flex-column gap-3">
						<label class="fs-4">身長</label>
						<div class="d-flex align-items-center gap-2">
							<select v-model="minHeight" @change="handleMinHeightChange" class="form-select fs-4">
								<option disabled value="">選択ください</option>
								<option v-for="height in heightRange" :key="height" :value="height">{{ height }}
								</option>
							</select>
							<span>-</span>
							<select v-model="maxHeight" class="form-select fs-4">
								<option disabled value="">選択ください</option>
								<option v-for="height in maxHeightOptions" :key="height" :value="height">{{ height }}
								</option>
							</select>
						</div>
					</div>

					<!-- Cast Features Selection -->
					<div class="col-md-3 d-flex flex-column gap-3">
						<label class="fs-4">詳細条件</label>
						<button class="btn btn-deet-transparent fs-4" @click.prevent="isModalOpen = true">選択する</button>
						<div class="d-flex flex-wrap gap-2 align-items-center">
							<div v-for="tag in selectedTags" :key="tag"
								class="badge badge-deet-dark bg-secondary d-flex align-items-center fs-4">
								{{ tag }}
								<i class="icon-x d-block ms-2" @click="removeTag(tag)"></i>
							</div>
						</div>
					</div>

					<!-- Cast Feature Tag Modal -->
					<div v-if="isModalOpen" class="modal" tabindex="-1" role="dialog">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header justify-content-between">
									<h5 class="modal-title fs-3">詳細条件</h5>
									<i type="button" class="icon-x d-block fs-3" @click="isModalOpen = false"></i>
								</div>
								<div class="modal-body">
									<div class="d-flex flex-wrap gap-2">
										<button v-for="tag in dummyTags" :key="tag"
											class="btn btn-outline-deet-dark fs-4"
											:class="{ active: selectedTags.includes(tag) }"
											@click.prevent="toggleTag(tag)">
											{{ tag }}
										</button>
										<button v-for="tag in dummyTags2" :key="tag"
											class="btn btn-outline-deet-dark fs-4"
											:class="{ active: selectedTags.includes(tag) }"
											@click.prevent="toggleTag(tag)">
											{{ tag }}
										</button>
										<button v-for="tag in dummyTags3" :key="tag"
											class="btn btn-outline-deet-dark fs-4"
											:class="{ active: selectedTags.includes(tag) }"
											@click.prevent="toggleTag(tag)">
											{{ tag }}
										</button>
										<button v-for="tag in dummyTags4" :key="tag"
											class="btn btn-outline-deet-dark fs-4"
											:class="{ active: selectedTags.includes(tag) }"
											@click.prevent="toggleTag(tag)">
											{{ tag }}
										</button>
										<button v-for="tag in dummyTags5" :key="tag"
											class="btn btn-outline-deet-dark fs-4"
											:class="{ active: selectedTags.includes(tag) }"
											@click.prevent="toggleTag(tag)">
											{{ tag }}
										</button>
										<!-- Add more tag groups as needed -->
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

					<!-- Cast Rank -->
					<div class="col-md-3 d-flex flex-column gap-3">
						<label class="fs-4">希望クラス</label>
						<div class="d-flex gap-2">
							<button v-for="rtag in dummyRanks" :key="rtag.value" class="btn btn-outline-deet-dark fs-4"
								:class="{ active: selectedRtags.includes(rtag.value) }" @click.prevent="toggleRtag(rtag.value)">
								{{ rtag.label }}
							</button>
						</div>
					</div>

					<!-- Search Button -->
					<div class="col-12 text-end">
						<button type="submit" class="btn search-btn deet-btn">この条件で検索</button>
					</div>
				</div>
			</form>
		</div>

		<!-- cast list section -->
		<!-- <div class="fixed-tab"> -->
			<!-- <span class="section-title fs-2"></span> -->
		<!-- </div> -->
		<div class="content pt-4 pb-4">
			<div class="card-grid">

				<div v-for="cast in castList" :key="cast.cast.id" class="cast-card" @click="openPopup(cast,cast.cast.id)">
					<div class="cast-image-container">
						<img :src="cast?.images[0]?.image_url || dummyImageSrc" alt="cast image" class="uploaded-image" />
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
			<!-- Load More Button -->
		<div class="text-center mt-3" v-if="hasMoreCasts">
			<button class="btn btn-primary" @click="submitSearch"
				:disabled="isLoading || !hasMoreCasts">
				{{ isLoading ? "Loading..." : hasMoreCasts ? "もっと見る" : "No More Casts" }}
			</button>
		</div>
		</div>

		<!-- popup -->
		<CustomerCastInfoPopup v-if="showPopup" :cast="selectedCast" :recommendation-list="recommendationList" @close="closePopup" />
	</div>
</template>

<style scoped>
.container {
	/* max-width: 1620px; */
}

.content {
	/* max-width: 1620px; */
	/* width: 100%; */
	/* padding: 60px; */
}

.section-title {
	/* color: #545454; */
	font-size: 22px;
	/* font-weight: 700; */
	/* margin-bottom: 0px; */
}

/* Casting Search Criteria section */
.modal {
	display: block;
	background-color: rgba(0, 0, 0, 0.5);
}

.badge {
	font-weight: normal;
	/* font-size: 9px; */
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

.search-btn {
	/* color: #ffffff; */
	/* background-color: #ff4600; */
	/* border: none; */
	/* border-radius: 5px; */
	/* padding: 15px 40px; */
}

.search-btn:hover {
	/* background-color: #ff6a33; */
}

.search-btn:active {
	/* background-color: #e63d00; */
}

/* cast list section */
.fixed-tab {
	/* display: inline-block;
	padding: 12px 54px 7px 54px;
	background-color: #ffffff;
	color: #545454;
	font-weight: 700; */
}

.card-grid {
	/* display: grid;
	grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
	gap: 50px;
	justify-content: start; */
}

.cast-card {
	/* width: 100%;
	border-radius: 4px;
	box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
	overflow: hidden; */
}

.cast-image-container {
	position: relative;
	width: 100%;
	/* height: 180px; */
}

.uploaded-image {
	/* aspect-ratio: 3 / 4; */
	/* width: 100%; */
	/* height: 100%; */
	/* object-fit: cover; */
}

.favorite-icon {
	position: absolute;
	top: 10px;
	right: 10px;
	cursor: pointer;
	z-index: 10;
}

.icon-star {
	font-size: 24px;
	color: white;
	text-shadow: 0 0 3px rgba(0, 0, 0, 0.5);
}

.icon-star.favorite {
	color: yellow;
}

.cast-info {
	padding: 13px 10px;
}

.cast-details {
	/* display: flex; */
	/* align-items: baseline; */
	/* margin-bottom: 8px; */
	/* line-height: 1.2; */
}

.cast-age,
.cast-area {
	/* font-size: 8px; */
	/* font-weight: 700; */
	/* margin-right: 4px; */
}

.cast-name {
	/* font-size: 10px; */
	/* font-weight: 700; */
	/* margin-right: 4px; */
}

.cast-comment {
	/* font-size: 9px; */
	/* margin-bottom: 3px; */
	/* line-height: 1.2; */
}

.cast-price {
	/* font-size: 8px; */
	/* text-align: right; */
}

.btn-deet-transparent,
.btn-outline-deet,
.btn-outline-deet-dark {
	padding: 9.5px 20px;
}


@media (max-width: 767px) {
	.row > .col-md-6 {
		margin-top: 1rem;
	}

}
</style>
