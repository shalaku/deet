<script setup>
const props = defineProps({
	allTags: Array,
	selectedTags: Array,
	label: String,
});

const emit = defineEmits(['update:selectedTags']);

const tagInput = ref('');
const isSelectingTag = ref(false);

const filteredTags = computed(() => {
	const inputValue = tagInput.value.toLowerCase();
	return props.allTags.filter(
		(tag) =>
			tag.toLowerCase().includes(inputValue) &&
			!props.selectedTags.includes(tag),
	);
});

const addTag = (tag) => {
	if (tag && !props.selectedTags.includes(tag)) {
		emit('update:selectedTags', [...props.selectedTags, tag]);
		tagInput.value = '';
	}
};

const removeTag = (tag) => {
	emit(
		'update:selectedTags',
		props.selectedTags.filter((t) => t !== tag),
	);
};

const openTagSelection = () => {
	isSelectingTag.value = true;
};

const closeTagSelection = () => {
	isSelectingTag.value = false;
};
</script>

<template lang="html">
	<div class="register-input tag-input">
		<label class="mb-1 fs-5">{{ label }}</label>
		<div class="form-control tag-display">
			<span v-for="tag in selectedTags" :key="tag" class="tag mb-2 me-2 fs-4 fw-normal">
				{{ tag }}
			</span>
		</div>
		<div>
			<span @click="openTagSelection" class="btn btn-outline-secondary btn-deet-trans-dark basic-info-edit-btn btn-popup fs-4 mt-2 mb-2 ps-4 pe-4"
				>タグを選択</span
			>
		</div>
	</div>

	<!-- modal -->
	<div v-if="isSelectingTag" class="tag-selection-overlay">
		<div class="tag-selection-content">
			<div class="mb-3">
				<input
					type="text"
					class="form-control tag-search-input"
					v-model="tagInput"
					:placeholder="`${label}を検索`"
					@keydown.esc="closeTagSelection"
				/>
			</div>
			<div class="tag-list">
				<button
					v-for="tag in filteredTags"
					:key="tag"
					class="btn btn-outline-deet-dark tag fs-4 me-2 mb-2"
					@click="addTag(tag)"
				>
					{{ tag }}
				</button>
			</div>
			<div class="selected-tags mt-3 d-flex flex-wrap gap-2 align-items-center">
				<div
					v-for="tag in selectedTags"
					:key="tag"
					class="tag d-flex align-items-center fs-4 fw-normal"
				>
					{{ tag }}
					<i
						@click.stop="removeTag(tag)"
						class="icon-x d-block ms-3"
						aria-label="Close"
					></i>
				</div>
			</div>
			<div class="d-grid gap-2 col-8 mx-auto fs-4">
					<button type="submit" class="btn deet-btn" @click="closeTagSelection">閉じる</button>
			</div>
			<!-- <button class="btn btn-primary mt-3" @click="closeTagSelection"> -->
				<!-- 完了 -->
			<!-- </button> -->
		</div>
	</div>
</template>

<style scoped>
.btn-close {
	width: 5px;
	height: 5px;
}
.register-input {
	margin-bottom: 20px;
}

.tag-input {
	max-width: 1070px;
}

.tag-display {
	/* min-height: 38px; */
	cursor: pointer;
	border: none;
    background: transparent;
    padding: 0;
}


.tag-selection-overlay {
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background-color: rgba(0, 0, 0, 0.5);
	display: flex;
	justify-content: center;
	align-items: center;
	z-index: 1000;
}

.tag-selection-content {
	background-color: var(--deet-color-cotent-base);
	padding: 20px;
	border-radius: 3px;
	width: 90%;
	max-width: 500px;
	max-height: 80vh;
	overflow-y: auto;
}

.tag-list {
	max-height: 200px;
	overflow-y: auto;
}

.tag {
	/* display: inline-block;
	padding: 4px 10px 2px 10px;
	font-weight: 400;
	font-size: 75%;
	text-align: center;
	vertical-align: baseline;
	border-radius: 3px;
	color: #fff;
	background-color: #707070;
	margin-right: 0.25rem; */
}
.tag {
	padding: 13px 15px;
}
.tag-text {
	padding-top: 2px;
}
</style>
