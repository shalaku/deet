<script setup lang="ts">
import { useQuery } from '@tanstack/vue-query';
import { castAPI } from '~/libs/api';
import { VueQuery } from '~/libs/enums';

definePageMeta({
	layout: 'cast',
	middleware: 'auth',
});

useHead({
	title: 'My Page - Cast | Deet',
});

const { data: profileData } = useQuery({
	queryKey: [VueQuery.GET_CAST_PROFILE],
	queryFn: () => castAPI.profile(),
});
</script>

<template lang="html">
	<div class="container-fluid main-area mt-3" v-if="profileData?.data">
		<div class="content p-4">
			<!-- プロフィール -->
			<CastMypagePersonalinfo
				:owner-id="profileData.data.cast.id.toString()"	
				:data="profileData.data.cast"
				:images="profileData.data.images"
			/>

			<!-- 基本情報 -->
			<CastMypageBasicInfo :data="profileData.data.cast" />

			<!-- 写真 -->
			<CastMypagePhotographs
				:owner-id="profileData.data.cast.id.toString()"
				:data="profileData.data.images"
			/>

			<!-- 対応可能地域 -->
			<CastMypageAvailableAreas :data="profileData.data.compatible_areas" />

			<!-- お支払い -->
			<CastMypagePaymentListing :data="profileData.data.bank_details" />
		</div>
	</div>
</template>

<style scoped>
.content-body {
	padding: 40px;
}
@media (max-width: 768px) {
	.content-body {
		padding: 20px;
	}
}

</style>

<style lang="css" >


h3 {
	font-size: 14px;
	font-weight: 700;
}

.text-10px {
	font-size: 10px;
}
.text-12px {
	font-size: 12px;
}

.status-tag {
	display: inline-block;
	background: #23b700;
	border: none;
	border-radius: 3px;
	color: #ffffff;
	padding: 5px 8px 4px 8px;
}

.feature-tag {
	display: inline-block;
	background: #ffffff;
	border: #707070 1px solid;
	border-radius: 3px;
	color: #545454;
	padding: 6px 12px 4px 12px;
}

.self-introduction {
	max-width: 400px;
}

.basic-info {
	margin-right: 30px;
}

.basic-info-edit-btn {
	padding: 4px 14px 3px 14px;
	border-radius: 3px;
}

.region-item {
	padding: 6px 14px 4px 14px;
	border-radius: 4px;
	font-size: 14px;
	font-weight: 700;
}

.available-item {
	color: #ffffff;
	background-color: #545454;
	border: none;
}

.payment-edit-btn {
	padding: 4px 14px 3px 14px;
	border-radius: 3px;
}

.icon-bank:before {
	font-size: 24px;
}

/* file upload */
.file-input {
	display: none;
}

.image-upload-btn {
	padding: 6px 14px;
	border-radius: 3px;
}

.image-upload-btn:hover {
	background: #44494e;
	color: #ffffff;
}

.image-container {
	width: 172px;
	height: 130px;
	margin-right: 30px;
	margin-bottom: 30px;
}

.uploaded-image {
	width: 100%;
	height: 100%;
	object-fit: cover;
}

.delete-button {
	position: absolute;
	top: -10px;
	right: -10px;
	width: 23px;
	height: 23px;
	border-radius: 50%;
	background-color: white;
	border: 2px solid #707070;
	display: flex;
	align-items: center;
	justify-content: center;
	padding: 0;
	cursor: pointer;
}

.delete-icon {
	color: #707070;
	font-size: 18px;
	font-weight: bold;
	line-height: 1;
}

.delete-button:hover {
	background-color: #f8f9fa;
}

.profile-image-wrapper {
	position: relative;
	width: 150px;
	height: 150px;
}

.profile-image-area {
	width: 100%;
	height: 100%;
	border-radius: 50%;
	overflow: hidden;
	background-color: #f0f0f0;
}

.profile-image {
	width: 100%;
	height: 100%;
	object-fit: cover;
	object-position: center;
	aspect-ratio: 1;
	inline-size: max-content;
}



.image-container {
	width: 162px;
	height: 130px;
	margin-right: 30px;
	margin-bottom: 30px;
}

@media (max-width: 768px) {
	.content-body {
		/* padding: 20px; */
	}

	.d-flex:not(.keep-flex) {
		/* flex-direction: column; */
	}

	.keep-flex {
		display: flex !important;
		flex-direction: row !important;
		justify-content: center !important;
		width: 100%;
	}

	.center-align-on-mobile {
		/* align-items: center !important; */
		/* text-align: center; */
	}

	.profile-image-wrapper {
		margin-bottom: 20px;
	}

	.basic-info {
		margin-right: 0;
		margin-bottom: 20px;
	}

	.region-list > div {
		flex-wrap: wrap;
	}

	.image-container {
		width: 100%;
		height: auto;
		margin-right: 0;
		max-width: 35vw;
	}
	.modal-content {
 		background-color: var(--deet-color-cotent-base) !important;	   
		.tag-selection-content {
			background-color: var(--deet-color-cotent-base) !important;	
		}
	}
}
</style>
