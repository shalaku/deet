<script setup lang="ts">
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import Swal from 'sweetalert2';
import { castAPI } from '~/libs/api';
import { VueQuery } from '~/libs/enums';

const { ownerId, data } = defineProps<{
	ownerId: string;
	data: API.CastImage[];
}>();

// Trigger file input
const fileInput = ref<HTMLInputElement | null>(null);
const handleTriggerFileInput = () => {
	if (!fileInput.value) return;
	fileInput.value.click();
};

// Access QueryClient instance
const queryClient = useQueryClient();

// Add photograph mutation
const { mutate: handleUploadPhotos, isPending } = useMutation({
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

// Remove photograph mutation
const { mutate: handleDeletePhoto } = useMutation({
	mutationFn: (id: number) => castAPI.deletePhoto(id),
	onSuccess: () => {
		queryClient.invalidateQueries({
			queryKey: [VueQuery.GET_CAST_PROFILE],
		});
	},
	onError: (error) => {
		Swal.fire({
			icon: 'error',
			title: 'Failed to delete photos',
			text: error?.message,
		});
	},
});
</script>

<template lang="html">
	<hr />
	<div class="content-body register-area">
		<div class="row">
			<div class="col-6">
				<h3 class="mt-2 mb-2 center-align-on-mobile text-start fs-3">プロフィール写真</h3>
			</div>
			<div class="col-6">
			<div class="text-end">
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
				:disabled="isPending"
			>
				アップロード
			</button>
			</div>
		</div>
		</div>
		<div class="d-flex flex-wrap justify-content-around" v-if="data?.length > 0">
			<div
				v-for="(image, index) in data"
				:key="index"
				class="image-container position-relative"
			>
				<img :src="image.image_url" alt="Dummy image" class="uploaded-image" />
				<button
					@click="handleDeletePhoto(image.id)"
					class="delete-button"
					aria-label="画像を削除"
				>
					<span class="delete-icon">×</span>
				</button>
			</div>
		</div>
	</div>
</template>
