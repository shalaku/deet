<script setup>
import '~/assets/css/customer/login.css';

useHead({
	title: 'User Register - AisekiSystem',
});

const user = ref({
	email: '',
	password: '',
});

const authStore = useAuthStore();
const router = useRouter();

const handleFormSubmit = async () => {
	try {
		const isSignedUp = await authStore.register({
			...user.value,
			name_ja: user.value.email,
			account_type: 'customer',
			user_type: 'admin',
		});

		if (isSignedUp) router.push('/user');
	} catch (error) {
		console.error(error);
	}
};

const goToLogin = () => {
	router.push('/user/login');
};
</script>

<template>
	<div class="page-user min-vh-100 d-flex justify-content-center align-items-center">
		<div class="container">
			<div class="row justify-content-center">
				<!-- Temporary placement of logo and background -->
				<div class="logo-area text-center">
					<img src="/deet_logo_h_gold.svg" alt="" />
				</div>

				<!-- Like a login, new registration switching tab -->
				<div class="tab-container-wrapper">
					<div class="tab-container  login-title text-center">
						<button class="" @click="goToLogin">ログイン</button>
						<button class="active">新規登録</button>
					</div>
				</div>


				<!-- Registration -->
				<div>
					<form @submit.prevent="handleFormSubmit" class="submit-form">
						<div class="input-group">
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
						<div class="input-group">
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
						<button class="btn submit-btn" type="submit">新規登録</button>
					</form>
				</div>

				<!-- External Service Registration -->
				<!-- <div class="login-option-links"> -->
					<!-- <button class="btn tel-login-btn">電話番号で登録</button> -->
					<!-- <button class="btn google-login-btn">Googleで登録</button> -->
					<!-- <button class="btn line-login-btn">LINEで登録</button> -->
				<!-- </div> -->
				<div class="terms">
					本サービスは20歳以上が利用可能です。<br>
					新規登録にあたって、<a href="#">利用規約</a>
					と <a href="#">プライバシーポリシー</a> に同意することとします。
				</div>
				<footer class="footer">
					<hr />
					<div class="d-flex justify-content-evenly text-center">
						<div><a href="" class="text-12px">会社概要</a></div>
						<div><a href="" class="text-12px">利用規約</a></div>
						<div><a href="" class="text-12px">プライバシーポリシー</a></div>
					</div>
					<hr />

					<div class="text-12px text-center law-cert">
						<span>インターネット異性紹介事業届出</span><br />
						<span>東京都公安委員会 (受理番号:xxxxxxxxxx)</span><br />
						<br />
						<span>電気通信事業者</span><br />
						<span>総務省(届出番号:xxxxxxxxxx)</span>
					</div>

					<hr />

					<div style="width: 100%" class="text-center">
						<small>&copy; {{ new Date().getFullYear() }} Deet All rights reserved.</small>
					</div>
				</footer>	
			</div>
		</div>
	</div>
</template>

<style scoped>
@font-face {
	font-family: Meiryo UI;
	src: url('/fonts/MeiryoUI.ttf');
}

body {
	/* background: rgb(29 26 22); */
	/* color: #ffffff; */
	text-align: center;
}

.container {
	max-width: 1384px;
	width: 100%;
}

/* 仮配置 */
.logo-area {
	/* background: #ffffff; */
	color: #8b8b8b;
	margin-bottom: 40px;
	margin-top: 40px;
	max-width: 80%;
	/* height: 288px; */
	align-items: center;
	img {
		width: 200px;
		margin-right: auto;
        	margin-left: auto;
		/* position: absolute; */
		/* top: 30px; */
		/* left: 30px; */
	}
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

.submit-form {
	margin-bottom: 24px;
	display: flex;
	flex-direction: column;
	align-items: stretch;
	padding: 0 52px;
	> div {
		/* text-align: left; */
	}
}

.input-group {
	max-width: 328px;
	margin-bottom: 15px;
}

.form-control:focus {
	border: 3px solid #a6cbf3;
	box-shadow: none;
}

.btn {
	font-weight: 400;
	color: #ffffff;
	border-radius: 5px;
	border: none;
}

.submit-btn {
	font-size: 18px;
	line-height: 24px;
	font-weight: bold !important;
	color: rgb(29, 26, 22);
	background: rgb(193 160 108);
	padding: 9px 0;
	margin-top: 15px;
	margin-bottom: 15px;
	max-width: 328px;
	margin-left: 1em;
	margin-right: 1em;
	white-space: nowrap;
}

.submit-btn:hover {
	color: rgb(29, 26, 22);
	background: rgb(193 160 108);
}

.login-option-links {
	display: flex;
	justify-content: center;
	margin-bottom: 82px;
}

.tel-login-btn,
.google-login-btn,
.line-login-btn {
	padding: 10px 70px;
	margin: 0 10px;
	white-space: nowrap;
}

.tel-login-btn {
	background: #aeaeae;
}
.tel-login-btn:hover {
	background: #aeaeae;
}

.google-login-btn {
	background: #ff4600;
}
.google-login-btn:hover {
	background: #ff4600;
}

.line-login-btn {
	background: #21d200;
}
.line-login-btn:hover {
	background: #21d200;
}

.terms {
	/* font-weight: 700; */
	font-size: 12px;
	color: #ffffff;
	max-width: 80%;
	/* text-align: left; */
	/* margin: 0 auto 150px auto; */
}

.terms a {
	color: #ffffff;
	text-decoration: none;
}

@media screen and (max-width: 768px) {
	.login-option-links {
		flex-direction: column;
		align-items: center;
	}

	.tel-login-btn,
	.google-login-btn,
	.line-login-btn {
		width: 100%;
		max-width: 328px;
		margin: 10px 0;
	}
}

.law-cert {
	width: 100%;
	line-height: 1.4;
}


.login-title {
	border-bottom: 1px solid #736b60;
    position: relative;
    margin-left: 0;
    margin-right: 0;
	button {
		background-color: transparent;
		border: none;
		text-decoration: none;
		font-weight: normal;
		color: #b9b9b9;
		padding: 0 20px 10px 20px;
		&.active{
			color: #FFF;
		}
	}
}
.tab-container-wrapper {
	padding: 0 52px;
    width: 100%;
}
</style>
