<script setup lang="ts">
// import '~/assets/css/siteadmin/login.css';

useHead({
	title: 'Deet 管理ページ',
	link: [
    {
      rel: 'manifest',
      href: '/siteadmin/manifest.json' // Userアプリのマニフェスト
    },
	{
		rel: 'apple-touch-icon',
		type: 'image/x-icon',
		href: '/siteadmin/icons/apple-touch-icon.png',
	}
	]
});
const user = ref({
	email: '',
	password: '',
});

const authStore = useAuthStore();
const router = useRouter();

const handleFormSubmit = async () => {
	try {
		const isLoggedIn = await authStore.login({
			...user.value,
			loginAs: 'admin',
		});
		if (isLoggedIn) router.push('/siteadmin');
	} catch (error) {
		console.error(error);
	}
};
</script>

<template lang="html">
	<section
		class="bg-body-tertiary min-vh-100 d-flex align-items-center justify-content-center"
	>
		<div class="card text-center">
			<h1 class="card-header">Deet 管理ページ</h1>
			<div class="card-body">
				<h2>Login</h2>
				<p class="text-body-secondary">Sign In to your account</p>
				<form @submit.prevent="handleFormSubmit">
					<div class="input-group mb-3">
						<!-- <span class="input-group-text"> -->
						<!-- <i class="icon-at"></i> -->
						<!-- </span> -->
						<input
							v-model="user.email"
							class="form-control"
							name="email"
							type="email"
							placeholder="メールアドレス"
						/>
					</div>
					<div class="input-group mb-4">
						<!-- <span class="input-group-text"> -->
						<!-- <i class="icon-lock-locked"></i> -->
						<!-- </span> -->
						<input
							v-model="user.password"
							class="form-control"
							name="password"
							type="password"
							placeholder="パスワード"
						/>
					</div>
					<div class="row align-items-end">
						<div class="col-6">
							<button type="submit" class="btn deet-btn submit-btn px-4">
								ログイン
							</button>
						</div>
						<div class="col-6 text-end">
							<NuxtLink
								to="/siteadmin/forgot_password"
								class="deet-link-text forgot-password"
							>
								パスワードを忘れた場合
							</NuxtLink>
						</div>
					</div>
				</form>
			</div>
		</div>
	</section>
</template>

<style scoped lang="css">
@font-face {
	font-family: Meiryo UI;
	src: url('/fonts/MeiryoUI.ttf');
}

h2 {
	font-size: 26px;
	font-weight: 700;
	border-bottom: 1px solid rgb(115, 107, 96);
	display: inline-block;
	padding-bottom: 10px;
	margin-bottom: 30px;
	width: 180px;
	text-align: center;
}

.card {
	background: #ffffff 0% 0% no-repeat padding-box;
	border: 1px solid #d8dbe0;
	border-radius: 4px;
}
.card-container {
	padding: 0 40px;
}
.card-header {
	background: #2f353a 0% 0% no-repeat padding-box;
	color: #fff;
	font: normal normal bold 16px/20px Meiryo UI;
	padding: 20px;
}

.card-body {
	padding: 50px 116px;
	color: #707070;
}

.card-body p {
	font: normal normal normal 20px/25px Meiryo UI;
	margin-bottom: 50px;
}
.submit-btn {
	/* background: #20a8d8 0% 0% no-repeat padding-box; */
	/* border: none; */
	/* border-radius: 5px; */
	/* font: normal normal normal 16px/20px Meiryo UI; */
	/* letter-spacing: 0px; */
	/* color: #ffffff; */
	/* padding: 10px 16px; */
}
.submit-btn:hover {
	/* background: #20a8d8 0% 0% no-repeat padding-box; */
}
.forgot-password {
	/* font: normal normal normal 15px/19px Meiryo UI; */
	/* letter-spacing: 0px; */
	/* color: #20a8d8; */
	/* text-decoration: none; */
}
.form-control:focus {
	border: 3px solid #a6cbf3;
	box-shadow: none;
}
@media screen and (max-width: 768px) {
	.card-body {
		padding: 2rem;
	}
}
</style>
