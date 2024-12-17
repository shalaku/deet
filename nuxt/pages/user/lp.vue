<script setup lang="ts">
import { useMutation } from '@tanstack/vue-query';
import '~/assets/css/cast/common.css';
import '~/assets/css/cast/layout.css';
// import '~/assets/css/cast/login.css';

useHead({
	title: 'to discover the Deet - Deet.jp',
	link: [
    {
      rel: 'manifest',
      href: '/user/manifest.json' // Userアプリのマニフェスト
    },
	{
		rel: 'apple-touch-icon',
		type: 'image/x-icon',
		href: '/user/icons/apple-touch-icon.png',
	}
	]
});

const showInstallButton = ref(false);
let deferredPrompt: any;

onMounted(() => {
  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    showInstallButton.value = true; // インストールボタンを表示
  });
});

const installPWA = async () => {
  if (deferredPrompt) {
    deferredPrompt.prompt(); // プロンプトを表示
    const { outcome } = await deferredPrompt.userChoice;
    if (outcome === 'accepted') {
      console.log('ユーザーがインストールプロンプトを承認しました。');
    } else {
      console.log('ユーザーがインストールプロンプトを拒否しました。');
    }
    deferredPrompt = null; // イベントをリセット
    showInstallButton.value = false; // ボタンを非表示
  }
};

</script>

<template>
	<div class="page-cast page-lp login-template min-vh-100 d-flex justify-content-center align-items-center">
		<div class="container">
			<div class="row justify-content-center">
				<div class="logo-area text-center col-12">
					<img src="/deet_logo_h_gold.svg" alt="" />
				</div>

				<div class="col-12 text-center">
                    <p>is pleased to invite</p>
					<img class="dear-sig pt-4 pb-4" src="/dear_gentlemen.svg" alt="" />
                    <p class="mt-4">to discover the</p>
				</div>

				<div class="col-12 text-center fs-5 mt-5">
                    <p class="">-invitation only-</p>
                    <p class="mt-4 mb-0">選ばれたメンバーだけがアクセスできる</p>
                    <p class="mt-2 pt-0 mb-5">エグゼクティブ専用ラグジュアリーアプリ</p>
                    <p class="">「 上質な安心に包まれた、唯一無二の体験。」</p>
				</div>

				<div class="col-12 text-center mt-5">
                    <p class="pb-2">
						<div v-if="showInstallButton" @click="installPWA">
							<span class="discover pt-2 pb-2 ps-4 pe-4">
								DISCOVER
							</span>
						</div>
						<div v-else>
							<NuxtLink to="/user/login">
       	                    	<span class="discover pt-2 pb-2 ps-4 pe-4">
									DISCOVER
								</span>
                        	</NuxtLink>
						</div>
                    </p>
                    <p class="fs-4">on Deet.jp</p>
				</div>                

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

.page-lp {
    /* background: #fff; */
    /* color: #000; */
}

.container {
	max-width: 1384px;
	width: 100%;
}
.dear-sig{
    max-width: 250px;
}
.discover {
    background-color: var(--deet-color-gold);
    color: #fff;
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
		width: 150px;
		margin-right: auto;
		margin-left: auto;
		/* position: absolute; */
		/* top: 30px; */
		/* left: 30px; */
	}
}
a {
    text-decoration: none;
}
h2 {
	font-size: 19px;
	font-weight: 700;
	border-bottom: 1px solid rgb(115, 107, 96);
	display: inline-block;
	padding-bottom: 10px;
	margin-bottom: 30px;
	/* width: 180px; */
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
	font-weight: normal !important;
	color: rgb(255, 255, 255);
	background: #897150;
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

</style>
