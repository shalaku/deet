<script setup>
import { defineProps } from 'vue';
const props = defineProps({
	cardTitle: String,
	inputLabel: String,
});
</script>

<template>
	<section class="container mt-4">
		<div class="card mb-4 shadow-sm">
			<div class="card-header bg-light">
				<h5 class="font-weight-bold">{{ cardTitle }}</h5>
			</div>
			<div class="card-body">
				<form class="d-flex flex-wrap justify-content-between">
					<div class="col-md-9 mb-3">
						<label for="name_ja" class="form-label">{{ inputLabel }}</label>
						<input
							type="text"
							required
							:placeholder="inputLabel"
							class="form-control"
						/>
					</div>
					<div class="align-self-center mt-3">
						<button
							type="submit"
							class="btn btn-success text-light font-weight-bold"
						>
							登録と編集
						</button>
					</div>
				</form>
			</div>
		</div>
	</section>
</template>
