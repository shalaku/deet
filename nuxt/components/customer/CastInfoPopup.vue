<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { customerAPI } from '~/libs/api';


// 画像ダミーデータ
const dummyImageSrc =
	'data:image/gif;base64,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';


const props = defineProps({
	cast: {
		type: Object,
		required: true,
	},
	recommendationList: {
    	type: Array,
    	required: true,
  	},
});

const castData = ref({ ...props.cast });
const recommendationList = ref([...props.recommendationList]);
const emit = defineEmits(['close']);
const router = useRouter();
const favCastId = computed(() => castData.value.cast.id);

const handleClose = () => {
	emit('close');
};
const selectedImageIndex = ref(0);
const popupContainer = ref(0);
const profilePicture = computed(() => {
    return galleryImages.value[selectedImageIndex.value] || dummyImageSrc;
});

// Filter gallery images to exclude the profile image
const galleryImages = computed(() =>
    (castData.value.images || []).map(image => image.image_url)
);
const findImage = (index) => {
	console.log("image___1");
    selectedImageIndex.value = index;
};
const generateDummyCasts = (count) => {
	return Array.from({ length: count }, (_, index) => ({
		id: index + 1,
		name: `女性会員${index + 1}`,
		age: 'xx',
		area: '東京',
		comment:
			'一言コメント一言コメント一言コメント一言コメント一言コメント一言コメント一言コメント',
		price: 5000,
	}));
};
const RecommendedCasts = ref(generateDummyCasts(6));
const navigateToConfirmCall = (cast_id) => {
	router.push('/user/confirm_call_cast?order_type=direct&cast_id='+cast_id);
};

const isFavorite = ref(false);
const fetchFavoriteStatus = async () => {
  try {
    const response = await customerAPI.getFavCasts();
	
    const favoriteCastIds = response.data.casts.map(cast => cast.cast.id);
    isFavorite.value = favoriteCastIds.includes(favCastId.value);
  } catch (error) {
    console.error('Error fetching favorite status:', error);
  }
};

const toggleFavorite = async () => {
  try {
    if (isFavorite.value) {
      await customerAPI.removeFavCast( favCastId.value );
      isFavorite.value = false;
    
    } else {
      await customerAPI.addFavCast(favCastId.value);
      isFavorite.value = true;
    
    }
  } catch (error) {
    console.error('Error toggling favorite:', error);
  }
};

const navigateToChat = (user_id) => {
	localStorage.setItem('user_id', user_id);

// Navigate to the chat page
router.push('/user/chat');
};

const navigateToCast = (castId) => {
	router.push(`/user/cast_info/${castId}`);
};


const handleRecommendationClick = (selectedCast) => {
  const previousCast = { ...castData.value };
  castData.value = { ...selectedCast };
  recommendationList.value = recommendationList.value.map((rec) =>
    rec.cast.id === selectedCast.cast.id ? previousCast : rec
  );

  if (popupContainer.value) {
    popupContainer.value.scrollTo({ top: 0 });
  }
};


const alcoholJpTxt = ref({
	good: "強い",
	average: "普通",
	bad: "弱い",
	not: "NG",
});

// Get values dynamically using computed()
const alcoholText = computed(() => {
  return alcoholJpTxt.value[castData.alcohol] || '-';
});



const smokeJpTxt = ref({
	e_smoke: "吸う（電子）",
	smoke: "吸う（紙）",
	not: "吸わない",
});
// Get values dynamically using computed()
const smokeText = computed(() => {
//   return smokeJpTxt.value[castData.smoke as keyof typeof smokeJpTxt.value] || '-';
});

const sibandcohaJpTxt = ref({
	0: "いない",
	1: "いる",
});
// Get values dynamically using computed()
const siblingsText = computed(() => {
//   return sibandcohaJpTxt.value[castData.siblings as keyof typeof sibandcohaJpTxt.value] || '-';
});
// Get values dynamically using computed()
const cohabitationText = computed(() => {
//   return sibandcohaJpTxt.value[castData.cohabitation as keyof typeof sibandcohaJpTxt.value] || '-';
});

watch(favCastId, () => {
  fetchFavoriteStatus();
});

onMounted(() => {
  fetchFavoriteStatus();
});
</script>



<template>
	<div  class="popup-overlay" @click="handleClose">
		<div ref="popupContainer" class="popup-content" @click.stop>
			<i class="icon-x d-block fs-1" style="right: 25px;
    position: absolute;
    top: 18px;
    font-size: 2.8rem !important;" @click="handleClose"></i>
			<div class="container p-0">
				<div class="content p-4">
					<!-- cast info section -->
					<div class="row g-3">
						<!-- Main Image Display -->
						<div class="col-md-5 info-image-gallery">
							<div class="info-top-image-container">
								<img :src="profilePicture || dummyImageSrc" alt="Profile picture" class="info-top-image" />
							</div>
							<div class="gallery-images-container">
								<img v-for="(image, index) in galleryImages" :key="index" :src="image"
									:alt="`Gallery image ${index + 1}`" class="gallery-image"
									:class="{ selected: index === selectedImageIndex }" @click="findImage(index)" />
							</div>
						</div>

						<!-- Cast Details -->
						<div class="col-md-5 info-details">
							<div class="tags">
								<span class="rank-tag fs-4">{{ castData.cast.rank }}</span>
								<span class="age fs-4">{{ new Date().getFullYear() - new
									Date(castData?.cast.birthday).getFullYear() }}歳</span>
								<span class="place fs-4 ms-2">{{ castData.cast.prefecture }}</span>
							</div>
							<div class="info-name fs-2">{{ castData.cast.nickname }}</div>
							<div class="info-price fs-4">
								{{ (parseInt(castData.cast.basic_point_price,"10") || 0).toLocaleString("ja-JP",{
								maximumFractionDigits: 0
								}) }}P / 30min
							</div>
							<div class="feature-tags mb-4  fs-4">
								<span v-for="(tag, index) in castData.cast.tag_of_spec" :key="index"
									class="tag fs-5 mb-2">
									{{ tag }}
								</span>
							</div>
							<p class="info-comment fs-4">
								{{ castData.cast.notices }}
							</p>
							<div class="row g-2 fs-4">
								<div class="col-6 basic-info mt-0">
									<p class="mb-2">身長: {{ castData.cast.height }}cm</p>
									<p class="mb-2">居住地: {{ castData.cast.prefecture }}</p>
									<p class="mb-2">お仕事: {{ castData.cast.day_work }}</p>
								</div>
								<div class="col-6 basic-info mt-0">
									<p class="mb-2">お酒: {{ castData.cast.alcohol?alcoholJpTxt[castData.cast.alcohol]:"未回答" }}</p>
									<p class="mb-2">学歴: {{ castData.cast.final_academic_background }}卒</p>
									<p class="mb-2">タバコ: {{ castData.cast.smoke?smokeJpTxt[castData.cast.smoke]:"未回答" }}</p>
								</div>
								<div class="col-6 basic-info mt-0">
									<p class="mb-2">兄弟姉妹: {{ sibandcohaJpTxt[castData.cast.siblings] }}</p>
									<p class="mb-2">同居人: {{ sibandcohaJpTxt[castData.cast.cohabitation] }}</p>
									<p class="mb-2">髪型: {{ castData.cast.hair_style }}</p>
								</div>
								<div class="col-6 basic-info mt-0">
									<p class="mb-2">髪の色: {{ castData.cast.hair_color }}</p>
								</div>
							</div>
						</div>
						<div class="col-md-2 action-buttons">
							<button class="btn deet-btn ms-0" @click="navigateToConfirmCall(castData.cast.id)">
								Deetで呼ぶ
							</button>
							<button class="favorite-btn fs-4" @click="toggleFavorite">
								<span class="favorite-btn-content">
									<i class="icon-star" :class="{ 'is-favorite': isFavorite }"></i>
									<span class="favorite-text">お気に入りに追加</span>
								</span>
							</button>
							<button class="chat-btn fs-4" @click="navigateToChat(castData.cast.user_id)">チャット</button>
						</div>

					</div>

					<hr />

					<!-- recommended cast section -->
					<h3 class="recommended-title fs-3 fw-normal">おすすめの女性会員</h3>
          <div class="card-grid">
            <div
              v-for="recommendation in recommendationList"
              :key="recommendation.cast.id"
              class="recommended-cast-card"
              @click="handleRecommendationClick(recommendation)"
            >
              <div class="recommended-cast-image-container">
                <img :src="recommendation.images.find(image => image.is_profile_picture === 1)?.image_url || recommendation.images[0]?.image_url || dummyImageSrc" alt="cast image" class="recommended-uploaded-image" />
              </div>
              <div class="recommended-cast-info">
                <div class="cast-details">
                  <div class="d-flex justify-content-between">
                    <span class="cast-age fs-4">
                      {{ new Date().getFullYear() - new Date(recommendation.cast.birthday).getFullYear() }}歳
                    </span>
                    <span class="cast-area fs-4 text-end">{{ recommendation.cast.prefecture }}</span>
                  </div>
                  <div class="cast-name fs-4">{{ recommendation.cast.nickname }}</div>
                </div>
                <div class="recommended-cast-comment text-12px">{{ recommendation.cast.my_comment }}</div>
                <div class="recommended-cast-price fs-4">
                  {{ (parseInt(recommendation.cast.basic_point_price, "10") || 0).toLocaleString("ja-JP", { maximumFractionDigits: 0 }) }}P / 30min
                </div>
              </div>
            </div>
          </div>
				</div>
			</div>
		</div>
	</div>
</template>

<style scoped>
.content {
	max-width: 1253px;
	border: none;
	box-shadow: none;
}

/* popup */
.popup-overlay {
	position: fixed;
	top: calc(var(--header-height));
	left: 0;
	width: 100%;
	height: calc( 100vh - var(--header-height) - var(--footer-menu-height) );
	background-color: rgba(0, 0, 0, 0.5);
	display: flex;
	justify-content: center;
	align-items: center;
	z-index: 1000;
	.icon-x {
		/* position: fixed; */
		/* top: calc( var(--header-height) + 18px); */
		/* right: 25px;		 */
		/* font-size: 2.8rem !important; */
	}
}



@media (min-width: 992px) {
    .popup-overlay {
        margin-left: var(--sidebar-width);
		width: calc( 100vw - var(--sidebar-width));
	   

    }
}

.popup-content {
	/* background-color: white; */
	max-width: 100%;
	max-height: 100%;
	overflow-y: auto;
	position: relative;
}

.close-btn {
	position: absolute;
	top: 10px;
	right: 10px;
	background: none;
	border: none;
	font-size: 24px;
	cursor: pointer;
}

/* cast info section */
/* cast info image */
.gallery-image {
	width: 73px;
	height: 78px;
	object-fit: cover;
	border-radius: 4px;
	cursor: pointer;
	transition: border 0.3s ease;
}

.gallery-image.selected {
	border: 2px solid #ff9100;
}

.info-image-gallery {
	display: flex;
	flex-direction: column;
	gap: 10px;
}

.info-top-image-container {
	width: 262px;
	height: 212px;
	overflow: hidden;
	border-radius: 4px;
}

.info-top-image {
	width: 100%;
	height: 100%;
	object-fit: cover;
}

.gallery-images-container {
	display: flex;
	flex-wrap: wrap;
	gap: 5px;
}

.gallery-image {
	width: 73px;
	height: 78px;
	object-fit: cover;
	border-radius: 4px;
}

/* cast info details */
.info-details {
	display: flex;
	flex-direction: column;
}

.tags {
	display: flex;
	flex-wrap: wrap;
	margin-bottom: 14px;
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

.feature-tags {
	display: flex;
	flex-wrap: wrap;
	margin-bottom: 20px;
}

.age,
.place {
	color: rgb(193, 160, 108);
	padding-right: 0.5rem;
	line-height: 2.2;
}

.rank-tag {
	/* font-size: 10px;
	font-weight: 700;
	background: #ffb300;
	border: none;
	border-radius: 3px;
	color: #ffffff;
	padding: 4px 13px;
	margin-right: 13px; */
}

.tag {
	/* font-size: 10px;
	font-weight: 700;
	background: #ffffff;
	border: #707070 solid 1px;
	border-radius: 3px;
	color: #545454;
	padding: 3px 10px;
	margin-right: 8px; */
}

.info-comment {
	/* font-size: 12px; */
	/* color: #545454; */
	margin-bottom: 30px;
}

.basic-info p {
	/* font-size: 12px; */
}

/* cast info buttons */
.action-buttons {
	display: flex;
	flex-direction: column;
	align-self: center;
	gap: 10px;
}

.call-btn {
	padding: 10px 40px;
	background-color: #ff4600;
	color: #ffffff;
	border: none;
	border-radius: 5px;
	cursor: pointer;
}

.call-btn:hover {
	opacity: 0.8;
}

.favorite-btn {
	padding: 12.5px 11px 12.5px 11px;
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
.deet-btn {
	max-width: 100%;
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
	opacity: 0.8;
}

/* recommended Cast card */
.recommended-title {
	text-align: center;
	/* color: #707070; */
	font: normal normal bold 13px/17px Meiryo UI;
	margin: 60px 0 30px 0;
}

.card-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
	gap: 30px;
	justify-content: start;
	max-width: 964px;
	margin: 0 auto;
}

.recommended-cast-card {
	background-color: var(--deet-color-base);
	width: 100%;
	border-radius: 4px;
	box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
	overflow: hidden;
	cursor: pointer;
	transition: transform 0.2s ease-in-out;
}

.recommended-cast-card:hover {
	transform: translateY(-5px);
}

.recommended-cast-image-container {
	position: relative;
	/* width: 100%; */
	/* height: 114px; */
	aspect-ratio: 3 / 4;
}

.recommended-uploaded-image {
	width: 100%;
	height: 100%;
	object-fit: cover;
}

.recommended-cast-info {
	padding: 13px 10px;
}

.recommended-cast-details {
	display: flex;
	justify-content: space-between;
	align-items: baseline;
	margin-bottom: 8px;
	line-height: 1.2;
}

.recommended-cast-age,
.recommended-cast-area {
	/* font-size: 8px; */
	/* font-weight: 700; */
	/* margin-right: 4px; */
}

.recommended-cast-name {
	/* font-size: 10px; */
	/* font-weight: 700; */
	/* margin-right: 4px; */
}

.recommended-cast-comment {
	/* font-size: 9px; */
	/* margin-bottom: 3px; */
}

.recommended-cast-price {
	/* font-size: 8px; */
	text-align: right;
}

@media (max-width: 768px) {
	.info-grid {
		grid-template-columns: 1fr;
	}

	.info-image-gallery {
		display: flex;
		flex-direction: column;
		align-items: center;
	}

	.info-top-image-container {
		width: 100%;
		max-width: 400px;
		height: auto;
		aspect-ratio: 3 / 4;
	}

	.gallery-images-container {
		justify-content: center;
	}

	.action-buttons {
		grid-column: auto;
		flex-direction: column;
		width: 100%;
	}

	.call-btn,
	.favorite-btn,
	.chat-btn {
		width: 100%;

	}

	.basic-info-grid {
		grid-template-columns: 1fr;
	}
}
</style>
