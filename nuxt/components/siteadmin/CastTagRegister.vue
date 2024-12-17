<template>
	<div>
		<div
			class="d-flex justify-content-between align-items-center content-header"
		>
			<h4>女性会員特徴タグ登録</h4>
		</div>
		<div class="container">
			<div class="card">
				<div class="card-header">
					<h4>登録・編集</h4>
				</div>
				<div class="card-body">
					<div class="register-area">
						<form @submit.prevent="handleSubmit">
							<div class="d-flex justify-content-between align-items-end">
								<div>
									<label>タグ名</label>
									<input
										type="text"
										v-model="tag_name"
										class="form-control register-input"
										:placeholder="dynamicPlaceholder"
									/>
								</div>
								<div>
									<button type="submit" class="register-btn btn btn-success">
										登録・編集
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="card card-dark">
				<div class="card-header">
					<h4>検索結果</h4>
				</div>
				<div class="card-body table-card">
					<table class="table table-striped">
						<thead>
							<tr>
								<th scope="col">タグ名</th>
								<th scope="col" class="button-row">編集</th>
								<th scope="col" class="button-row">削除</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="tag in tags" :key="tag.id">
								<td>{{ tag.spec_tag_name }}</td>
								<td>
									<button
										class="table-btn btn btn-success"
										@click="editTag(tag.id, tag.spec_tag_name)"
									>
										編集
									</button>
								</td>
								<td>
									<button
										class="table-btn btn btn-danger"
										@click="deleteTag(tag.id)"
									>
										削除
									</button>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
import { useCastStore } from '~/stores/casts';

export default {
	data() {
		return {
			tags: null,
			tagId: null,
			tag_name: '',
		};
	},
	methods: {
		async fetchTags() {
			const store = useCastStore();
			await store.getTags();
			this.tags = store.getCastTags;
			console.log(this.tags);
		},
		async handleSubmit() {
			const store = useCastStore();
			if (this.tagId) {
				await store.updateTag(this.tagId, this.tag_name);
			} else {
				await store.registerTag(this.tag_name);
			}
			this.tagId = null;
			this.tag_name = '';
			await this.fetchTags();
		},
		editTag(id, name) {
			this.tagId = id;
			this.tag_name = name;
		},
		async deleteTag(id) {
			const store = useCastStore();
			await store.deleteTag(id);
			await this.fetchTags();
		},
	},
	created() {
		this.fetchTags();
	},
	computed: {
		dynamicPlaceholder() {
			return this.tag_name ? this.tag_name : '名称';
		},
	},
};
</script>
<style scoped>
.container {
	margin-top: 44px;
	max-width: 720px;
}
.content-header {
	padding: 11px 24px;
	background: #ffffff 0% 0% no-repeat padding-box;
}

.content-header h4 {
	font: normal normal normal 16px/20px Meiryo UI;
	margin: 0;
	color: #4f5d73;
}
.card {
	border: 1px solid #e0e0e0;
	border-radius: 4px;
}

.card-header {
	background: #f7f7f7 0% 0% no-repeat padding-box;
	padding: 14px 22px;
}
.card-body {
	padding: 27px 28px 27px 35px;
}
.card-header h4 {
	font: normal normal bold 14px/18px Meiryo UI;
	color: #3c4b64;
	margin: 0;
}
.register-input {
	margin-bottom: 14px;
}
.register-btn {
	font: normal normal normal 12px/16px Meiryo UI;
	color: #ffffff;
}
.register-btn:hover {
	color: #ffffff;
}
.card-dark .card-header {
	background: #2f353a 0% 0% no-repeat padding-box;
}
.card-dark .card-header h4 {
	color: #ffffff;
}
.table-card {
	padding: 0;
}
.table-btn {
	font: normal normal normal 12px/16px Meiryo UI;
	color: #ffffff;
	text-align: center;
}
.table-btn:hover {
	color: #ffffff;
}
.button-row {
	width: 10%;
	text-align: center;
}
thead {
	font: normal normal bold 12px/16px Meiryo UI;
	color: #3c4b64;
}
th {
	padding: 15px 0 !important;
}
th:first-child {
	padding-left: 30px !important;
}
td:first-child {
	padding-left: 30px !important;
}
</style>
