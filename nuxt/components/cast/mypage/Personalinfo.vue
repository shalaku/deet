<script setup lang="ts">
import { convertToHalfWidth } from '@/libs/utilities';
import { CModal, CModalBody, CModalHeader } from '@coreui/vue';
import { useMutation, useQuery, useQueryClient } from '@tanstack/vue-query';
import Swal from 'sweetalert2';
import { computed, onMounted, ref } from 'vue';
import NotificationBell from '~/components/misc/NotificationBell.vue';
import { castAPI, statusAPI } from '~/libs/api';
import { VueQuery } from '~/libs/enums';

const { data, images, ownerId } = defineProps<{
	ownerId: string;
	data: API.Cast;
	images: API.CastImage[];
}>();
const avatarURL = computed(() => {
	const profileImage = images.find((image) => image.is_profile_picture === 1);
	return profileImage ? profileImage.image_url : 'data:image/gif;base64,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';
});

// Trigger file input
const fileInput = ref<HTMLInputElement | null>(null);
const handleTriggerFileInput = () => {
	if (!fileInput.value) return;
	fileInput.value.click();
};

// Add photograph mutation
const { mutate: handleUploadPhotos, isPendingImg } = useMutation({
	mutationFn: (event: Event) => {
		const files = (event.target as HTMLInputElement).files;
		if (!files) throw new Error('No files selected');

		const images = Array.from(files);
		if (!ownerId) throw new Error('Owner ID not found');

		if (event.target) {
			(event.target as HTMLInputElement).value = '';
		}

		return castAPI.uploadPhotos(images, ownerId);
	},
	onSuccess: () => {
		queryClient.invalidateQueries({
			queryKey: [VueQuery.GET_CAST_PROFILE],
		});
	},
	onError: (error) => {
		Swal.fire({
			icon: 'error',
			title: 'Failed to upload photos',
			text: error?.message,
		});
	},
});

const selectedStatus = ref<number | null>(null);
onMounted(() => {
    selectedStatus.value = data.live_status;
	console.log(data);
});
const calculateAge = (dob: Date) => {
	const diff = Date.now() - dob.getTime();
	const ageDate = new Date(diff);
	return Math.abs(ageDate.getUTCFullYear() - 1970);
};

const { data: statusByCategory } = useQuery({
	queryKey: [VueQuery.GET_STATUS_BY_CATEGORY],
	queryFn: () => statusAPI.getStatusesByCategory('cast_live')
	.then(response => {
		// console.log(response);
		return response.data.cast_live;
	}),
});

// Get values dynamically using computed()
const statusCssClass = computed(() => {
  return "stat_"+data.live_status;
});

// Access QueryClient instance
const queryClient = useQueryClient();

// Update status mutation
const { mutate: handleUpdateStatus } = useMutation({
	mutationFn: (event: Event) => {
		//const status = (event.target as HTMLSelectElement).value;
		if (!selectedStatus.value) throw new Error('No status selected');
		return castAPI.updateProfile({
			live_status: Number(selectedStatus.value),
		});
	},
	onSuccess: () => {
		queryClient.invalidateQueries({
			queryKey: [VueQuery.GET_CAST_PROFILE],
		});
	},
	onError: (error) => {
		Swal.fire({
			icon: 'error',
			title: 'Failed to update status',
			text: error?.message,
		});
	},
});
const { mutate: makeProfilePicture } = useMutation({
	mutationFn: ({ ownerId, imageId }: { ownerId: number; imageId: number }) =>
		castAPI.makeProfilePicture(ownerId, imageId),
	onSuccess: () => {
		queryClient.invalidateQueries({
			queryKey: [VueQuery.GET_CAST_PROFILE],
		});
		// Invalidate queries to refresh data
		queryClient.invalidateQueries({ queryKey: [VueQuery.GET_CAST_PROFILE] });
			Swal.fire({
				icon: 'success',
				title: 'プロフィール画像の変更が完了しました',
			});
		},
		onError: (error) => {
			Swal.fire({
				icon: 'error',
				title: 'プロフィール画像の変更に失敗しました',
				text: error?.message,
			});
		},
	}
);

// Update profile picture mutation
const avatar=ref('');
const isProfilePictureModalVisible = ref(false);
const handleImageClick = async (imageId: number) => {
  isProfilePictureModalVisible.value = false;

  try {
     // Await the mutation to ensure it completes before proceeding
	 await new Promise((resolve, reject) => {
      makeProfilePicture(
        { ownerId: data.id, imageId },
        {
          onSuccess: () => {
            resolve(true);
          },
          onError: (error) => {
            reject(error);
          },
        }
      );
    });

    const updatedProfileData = await castAPI.profile();
    const updatedProfileImage = updatedProfileData.data.images.find(
      (image: { is_profile_picture: number }) => image.is_profile_picture === 1
    );
    if (updatedProfileImage) {

      const response=await castAPI.updateAvatarChat(data.id,updatedProfileImage.image_url );   
    } 
  } catch (error: any) {
    Swal.fire({
      icon: 'error',
      title: 'プロフィール画像の変更に失敗しました', // "Failed to update profile image"
      text: error.message || error?.response?.data?.message || 'An error occurred',
    });
  }
};



// Update personal info mutation
const isModalVisible = ref(false);
const allFeatureTags = [
	'スレンダー',
	'グラマー',
	'高身長',
	'小柄',
	'童顔',
	'ナチュラル',
	'可愛い系',
	'綺麗系',	
	'ギャル',
	'清楚',
	'モデル系',
	'アイドル系',
	'ハーフ',	
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
	'盛り上げ上手',
	'歌上手',
	'お酒好き',
	'英語が話せる',
	'中国語が話せる',
	'韓国語が話せる',		
];

const personalInfoForm = ref({
    nickname: data.nickname,
    my_comment: data.my_comment,
    tag_of_spec: data.tag_of_spec ? data.tag_of_spec : ['お酒好き'],
    notices: data.notices,
    basic_point_price: parseInt(data.basic_point_price, 10), // 初期値を設定
    rank: data.rank,
});


const { mutate: handleFormSubmit, isPending } = useMutation({
    mutationFn: () => {
        // 入力値を全角から半角に変換
        personalInfoForm.value.basic_point_price = convertToHalfWidth(personalInfoForm.value.basic_point_price);
        
        // basic_point_priceが4000未満の場合は4000に設定
        personalInfoForm.value.basic_point_price = Math.max(parseInt(personalInfoForm.value.basic_point_price, 10), 4000);
        console.log(personalInfoForm.value);
        return castAPI.updateProfile(personalInfoForm.value);	
    },
    onSuccess: () => {
        queryClient.invalidateQueries({
            queryKey: [VueQuery.GET_CAST_PROFILE],
        });
		const nicknameResponse = castAPI.updateUserNameChat(data.id, personalInfoForm.value.nickname);
        isModalVisible.value = false;
    },
    onError: (error) => {
        Swal.fire({
            icon: 'error',
            title: 'Failed to update profile',
            text: error?.message,
        });
    },
});


</script>

<template lang="html">
	<div class="content-body">
		<div class="d-flex flex-column flex-lg-row justify-content-between align-items-center center-align-on-mobile">
			<div class="profile-image-wrapper">
				<div class="profile-image-area">
					<img :src="avatarURL" alt="profile image" class="profile-image" />
				</div>
				<button class="profile-image-edit-button" @click="
						() => {
							isProfilePictureModalVisible = true;
						}
					">
					<span class="profile-image-edit-icon"><i class="icon-pencil"></i></span>
				</button>
			</div>

			<div class="" style="width:100%;">
				<div class="d-flex justify-content-between">
				<div class="status-tag mb-2 center-align-on-mobile fs-4" :class="statusCssClass">
							{{ statusByCategory?.find((status) => status.id === data.live_status)
							?.status_name
							}}
				</div>
				<NotificationBell />
				</div>
				<div class="row align-items-end justify-content-between mb-2 center-align-on-mobile gap-2">
					<div class="col-8 d-flex align-items-end">
						<div>
							<div class="">
								<span class="rank-tag mb-3 fs-4"> {{data.rank}}</span><br class="mb-1" />
							</div>
							<div class="fs-3 fw-bold">姓名</div>
							<div class="fs-2 mb-3">
								{{ data.name_ja }}
							</div>
							<div class="fs-3 fw-bold">ニックネーム</div>
							<div class="fs-2">
								{{ data.nickname }}
							</div>
						</div>
						<div class="d-flex fs-4 ms-2">
							<div class="me-1" style="line-height: 1.7;">
								{{ calculateAge(new Date(data.birthday)) }}
								歳
							</div>
						</div>
					</div>
					<div class="col-3" style="top: -40px; position: relative;">
					<div class="d-flex justify-content-end align-items-end mt-3 mb-3">
					<button class="btn btn-outline-secondary btn-deet-trans-dark basic-info-edit-btn btn-popup fs-4 mb-2" 
						@click="
							() => {
								isModalVisible = true;
							}
						">
						編集
					</button>
					</div>
					</div>
				</div>

				<div class="mt-4 fs-3 fw-bold">現保有ポイント</div>
				<div class="fs-3 mt-0">
					{{ parseInt( data.points_holded, "10").toLocaleString("ja-JP",{
								maximumFractionDigits: 0
							}) }}P
				</div>

				<div class="mt-4 fs-3 fw-bold">Deet報酬ポイント（30分）</div>
				<div class="fs-3 mt-0">
					{{ parseInt( data.basic_point_price, "10").toLocaleString("ja-JP",{
								maximumFractionDigits: 0
							}) }}P
				</div>

				<div class="mb-4 mt-4 fs-4">
					<div class="mb-2 fs-3 fw-bold">今日の一言</div>
					{{ data.my_comment }}
				</div>
				
				<div class="mb-2 fs-3 fw-bold">特徴タグ</div>
				<div
					class="d-flex justify-content-lg-start justify-content-md-center align-items-start mb-3 flex-wrap fs-4">
					<div v-for="tag in data.tag_of_spec" :key="tag" class="tag me-3 mb-3 fs-4 ">
						{{ tag }}
					</div>
				</div>

				<div class="d-flex flex-column justify-content-start mb-3" style="max-width: 200px;">
					<label class="mb-1 fs-3 fw-bold" for="status">ステータス</label>
					<select id="status" class="form-select form-select-sm fs-4" v-model="selectedStatus"
						@change="handleUpdateStatus">
						<option v-for="status in statusByCategory" :key="status.id" :value="status.id">
							{{ status.status_name }}
						</option>
					</select>
				</div>
				<div class="self-introduction mb-3">
					<div class="mb-2 fs-3 fw-bold">自己紹介</div>
					<div class="fs-4">
						{{ data.notices }}
					</div>
				</div>

			</div>
		</div>
	</div>

	<CModal size="lg" backdrop="static" alignment="center" :visible="isProfilePictureModalVisible"
		aria-labelledby="ProfilePictureUpdateLabel" content-class-name="rounded-0" v-on:close="
			() => {
				isProfilePictureModalVisible = false;
			}
		">
		<CModalHeader class="border-0 pb-0" />
		<CModalBody>
			<div class="d-flex flex-wrap justify-content-around" v-if="images?.length > 0">
				<div v-for="(image, index) in images" :key="index" class="image-container position-relative"
					style="cursor: pointer" @click="handleImageClick(image.id)">
					<img :src="image.image_url" alt="Dummy image" class="uploaded-image" />
				</div>
			</div>
			<input
				type="file"
				ref="fileInput"
				class="file-input"
				@change="handleUploadPhotos"
				accept="image/*"
				multiple
			/>
			<button
				class="btn btn-deet-trans-dark btn-deet-trans-dark image-upload-btn btn-popup fs-4 mb-4"
				@click="handleTriggerFileInput"
				type="button"
				:disabled="isPendingImg"
			>
				アップロード
			</button>
		</CModalBody>
	</CModal>

	<CModal size="lg" backdrop="static" alignment="center" :visible="isModalVisible"
		aria-labelledby="PersonalInfoUpdateLabel" content-class-name="rounded-0" v-on:close="
			() => {
				isModalVisible = false;
			}
		">
		<CModalHeader class="border-0 pb-0" />
		<CModalBody>
			<form class="personal-info-form" @submit.prevent="() => handleFormSubmit()">
				<div class="d-flex align-items-center gap-2 mb-3">
					<div class="fs-4">
						<label for="nickname">ニックネーム</label>
						<input id="nickname" class="form-control mt-1" type="text" v-model="personalInfoForm.nickname"
							placeholder="ニックネームを入力" />
					</div>
				</div>

				<div class="d-flex align-items-center gap-2 mb-3">
					<div class="fs-4">
						<label for="basic_point_price">Deet報酬ポイント（30分）</label>
						<input id="basic_point_price" class="form-control mt-1" type="text" inputmode="numeric" min="4000" v-model="personalInfoForm.basic_point_price"
							placeholder="Deet報酬ポイントを設定する" />
					</div>
				</div>

				<div class="d-flex align-items-center gap-2 mb-3">
					<div class="fs-4">
						<label for="my_comment">今日の一言</label>
						<input id="my_comment" class="form-control mt-1" type="text"
							v-model="personalInfoForm.my_comment" placeholder="今日の一言を入力" />
					</div>
				</div>

				<div class="d-flex align-items-center gap-2">
					<div class="w-100 fs-4">
						<SiteadminTagSelector label="特徴タグ" :allTags="allFeatureTags"
							v-model:selectedTags="personalInfoForm.tag_of_spec" />
					</div>
				</div>

				<div class="d-flex align-items-center gap-2 mb-3 fs-4">
					<div class="w-100">
						<label for="notices">自己紹介</label>
						<textarea class="form-control mt-1 fs-4" rows="5" v-model="personalInfoForm.notices"
							placeholder="自己紹介を入力" />
					</div>
				</div>

				<div class="d-grid gap-2 col-8 mx-auto fs-4">
					<button type="d" class="btn deet-btn" :disabled="isPending">保存</button>
				</div>

				<!-- <div class="d-flex justify-content-end fs-4"> -->
					<!-- <button type="submit" class="btn deet-btn" :disabled="isPending"> -->
						<!-- 保存 -->
					<!-- </button> -->
				<!-- </div> -->
			</form>
		</CModalBody>
	</CModal>
</template>

<style scoped lang="css">
/* .personal-info-form { */

/* } */

.personal-info-form input,
.personal-info-form select,
.personal-info-form textarea {
	width: 100%;
	/* font-size: 14px; */
}

.status-tag {
	&.stat_120 {
		background: var(--deet-color-status-online);
	}
	&.stat_121 {
		background: var(--deet-color-status-out-of-front);
	}
	&.stat_122 {
		background: var(--deet-color-status-offline);
	}
}
.tag {
	padding: 13px 15px;
}
.hide {
	display: none!important;
}

</style>
