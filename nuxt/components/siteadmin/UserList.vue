<script setup>
import axios from '~/customplugin/axios.js';
import Swal from 'sweetalert2';
import { ref, onMounted } from 'vue';
import { useUserStore } from '~/stores/users';

const users = ref(null);
const isAdmin = ref(false);
const userStore = useUserStore();

const setAccountType = async () => {
	await userStore.fetchUser();
	const admin = userStore.getUser;
	if (admin.account_type === 'admin') {
		isAdmin.value = true;
	}
	console.log(isAdmin.value);
};

const fetchUsers = async () => {
	const token = localStorage.getItem('token');
	try {
		const response = await axios.get(`/siteadmin/user_list`, {
			headers: {
				Authorization: `Bearer ${token}`,
			},
		});
		users.value = response.data.data;
		console.log(users.value);
	} catch (error) {
		console.log(error.response ? error.response.data : error);
	}
};

const toggleUserStatus = async (userId, currentStatus) => {
	const token = localStorage.getItem('token');
	try {
		const newStatus = currentStatus === 'active' ? 'freeze' : 'active';
		const response = await axios.post(
			'/siteadmin/activate_user',
			{ id: userId, status: newStatus },
			{
				headers: {
					Authorization: `Bearer ${token}`,
				},
			},
		);

		Swal.fire({
			title: 'Success!',
			text: response.data.message,
			icon: 'success',
		});

		// Update the user status in the local state
		users.value = users.value.map((user) =>
			user.id === userId ? { ...user, status: newStatus } : user,
		);
	} catch (error) {
		const errorMessage = error.response
			? error.response.data
			: 'Oops, something went wrong';
		Swal.fire({
			title: 'Error!',
			text: errorMessage,
			icon: 'error',
		});
		console.error('Error response:', errorMessage);
	}
};

// Fetch data when the component is mounted
onMounted(async () => {
	await fetchUsers();
	await setAccountType();
});
</script>

<template>
	<div>
		<div class="table-title">ユーザー一覧</div>
		<table class="table table-striped">
			<thead>
				<tr>
					<th scope="col">ID</th>
					<th scope="col">ステータス</th>
					<th scope="col">ユーザー名</th>
					<th scope="col">メールアドレス</th>
					<th scope="col">タイプ</th>
					<th scope="col">最終ログイン</th>
					<th scope="col">action</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="user in users" :key="user.id">
					<td>{{ user.account_id }}</td>
					<td>
						<button
							:class="{ 'status-btn': true, active: user.status === 'active' }"
						>
							{{ user.status }}
						</button>
					</td>
					<td>{{ user.name_ja }}</td>
					<td>{{ user.email }}</td>
					<td>{{ user.user_type }}</td>
					<td>{{ user.updated_at }}</td>
					<td>
						<button
							:class="{ 'status-btn': true, active: user.status === 'freeze' }"
							@click="isAdmin && toggleUserStatus(user.id, user.status)"
						>
							{{
								user.status === 'active' ? 'アカウント停止' : 'アカウント再開'
							}}
						</button>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<style scoped>
.table-title {
	font: normal normal bold 14px/18px Meiryo UI;
	letter-spacing: 0px;
	color: #3c4b64;
	padding: 14px 22px;
	background: #f7f7f7 0% 0% no-repeat padding-box;
}

thead {
	border-bottom: 2px solid #d8dbe0;
	font: normal normal bold 12px/16px Meiryo UI;
	letter-spacing: 0px;
	color: #3c4b64;
}

th,
td {
	padding: 15px 14px !important;
}

.status-btn {
	background: #707070 0% 0% no-repeat padding-box;
	border-radius: 4px;
	font: normal normal normal 12px/16px Meiryo UI;
	letter-spacing: 0px;
	color: #ffffff;
	border: none;
	padding: 5px 11px;
}

.status-btn.active {
	background: #20a8d8 0% 0% no-repeat padding-box;
}
</style>
