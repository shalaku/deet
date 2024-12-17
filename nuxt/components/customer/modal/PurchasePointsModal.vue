<template lang="html">
	<div class="modal-overlay">
		<div class="modal-content">
			<!-- Cross button for closing the modal -->
			<button type="button" class="close-button" @click="closeModal">×</button>

			<h1 style="color: #333">ポイントが不足しています。ポイントをご購入ください。</h1>

			<div class="list-container">
				<div class="list-item" v-for="item in data?.data" :key="item.id">
					<div class="points">{{ item.points.toLocaleString() }} ポイント</div>
					<button class="price-button" @click="onSelectPoints(item.points)">
						{{ item.points.toLocaleString() || 'N/A' }} 購入
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup lang="ts">
import { useQuery } from '@tanstack/vue-query';
import { defineEmits } from 'vue';
import { customerAPI } from '~/libs/api';
import { VueQuery } from '~/libs/enums';

const emit = defineEmits(['close', 'onSelectPoints']);

const closeModal = () => {
	emit('close');
};

const onSelectPoints = (points: number) => {
	emit('onSelectPoints', points);
};

const { data } = useQuery({
	queryKey: [VueQuery.GET_CUSTOMER_ALL_POINTS],
	queryFn: () => customerAPI.pointList(),
});
</script>

<style scoped lang="css">
.modal-overlay {
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background-color: rgba(0, 0, 0, 0.5);
	display: flex;
	align-items: center;
	justify-content: center;
}

.modal-content {
	position: relative;
	background-color: #fff;
	padding: 20px;
	border-radius: 10px;
	width: 400px;
	max-height: 60vh;
	overflow-y: auto;
	box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.close-button {
	position: absolute;
	top: 5px;
	right: 10px;
	background: none;
	border: none;
	font-size: 24px;
	cursor: pointer;
	color: #333;
	transition: color 0.3s ease;
	z-index: 1;
}

.close-button:hover {
	color: #ff5a00;
}

.list-container {
	display: flex;
	flex-direction: column;
	gap: 10px;
	margin-top: 20px;
}

.list-item {
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 10px;
	border: 1px solid #ddd;
	border-radius: 5px;
	background-color: #f9f9f9;
}

.points {
	font-size: 16px;
	font-weight: bold;
	color: #333;
}

.price-button {
	font-size: 14px;
	color: white;
	background-color: #ff5a00;
	border: none;
	padding: 8px 16px;
	border-radius: 5px;
	cursor: pointer;
	transition: background-color 0.3s ease;
	width: 150px;
	height: 40px;
	display: inline-flex;
	justify-content: center;
	align-items: center;
	min-width: 150px;
	min-height: 40px;
}

.price-button:hover {
	background-color: #e04a00;
}
</style>
