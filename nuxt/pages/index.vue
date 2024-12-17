<script setup lang="ts">
// import '~/assets/css/customer/common.css';
// import '~/assets/css/customer/login.css';
// import '~/assets/css/customer/layout.css';

// useHead({
// title: 'User Login - AisekiSystem',
// });
if (import.meta.env.MODE === 'production') {
	console.log('Production mode is enabled');
} else {
	console.log('Development mode is enabled');
}
</script>

<template>
	<link rel="preconnect" href="https://fonts.googleapis.com" />
	<link
		rel="preconnect"
		href="https://fonts.gstatic.com"
		crossorigin="anonymous"
	/>
	<link
		href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&display=swap"
		rel="stylesheet"
	/>
	<div class="lp-wrap">
		<header>
			<div class="d-flex align-items-center">
				<img src="/deet_logo_h_white.svg" alt="" />
			</div>
		</header>
		<section class="carousel">
			<div class="container-fluid p-0 m-0">
				<div class="row p-0 m-0">
					<div class="col-6 d-flex align-items-center justify-content-center">
						<div class="text-start">
							<h1 class="eb-garamond">Create the future</h1>
							<h1 class="eb-garamond">with the Deet platform</h1>
							<p class="fs-2 mt-5">
								ヒトのつながりで飲食業界の明日を変えていく
							</p>
							<p class="fs-2">飲食業界特化型マッチングプラットフォーム</p>
						</div>
					</div>
					<div class="col-6 d-flex justify-content-center">
						<img src="/lp-main.jpeg" />
					</div>
				</div>
			</div>
		</section>

		<section class="section-1 d-flex align-items-center justify-content-center">
			<div class="text-center mt-5 mb-5 pt-4 pm-5">
				<h1 class="eb-garamond mb-5 fw-bold">What is Deet ?</h1>
				<p class="fs-3 pm-3">
					Deetは飲食店オーナー様と飲食業界で求人を探している求職者様向けの採用マッチングプラットフォームです。
				</p>
				<p class="fs-3">
					飲食業界に特化した内容となっており、双方のニーズが一目で判断できるのが特徴です。
				</p>
			</div>
		</section>

		<section class="section-2">
			<div class="container-fluid">
				<div class="row">
					<div class="col-4">
						<div class="card">
							<img src="/both.jpg" class="card-img-top" alt="..." />
							<div class="card-body">
								<h3 class="card-title eb-garamond pt-4">Matching Platform</h3>
								<p class="fs-5 pb-4">マッチングプラットフォーム</p>
								<p class="card-text fs-4">
									飲食店オーナー様と求職者様が直接やり取りができるシステムです。
								</p>
								<p class="card-text fs-4">
									飲食業界に特化した内容なので、スキルや職場環境などの双方が求める解像度の高い情報を検索できるのが大きな特徴です。
								</p>
								<p class="card-text fs-4 pb-5 mb-5">
									また、面倒な日程調整もサービス上で完結するので、効率よくコミュニケーションができる設計となっています。
								</p>
							</div>
						</div>
					</div>
					<div class="col-4">
						<div class="card">
							<img src="/cheff.jpg" class="card-img-top" alt="..." />
							<div class="card-body">
								<h3 class="card-title eb-garamond pt-4">Career Consulting</h3>
								<p class="fs-5 pb-4">キャリアコンサルティング</p>
								<p class="card-text fs-4">
									ご希望される求職者様には、弊社のキャリアコンサルタントが専属でサポートします。
								</p>
								<p class="card-text fs-4">
									タイムリーな採用市場の情報共有や、ご本人のスキルの言語化などのサポートはもちろん、より精度の高い求人を直接ご提案いたします。
								</p>
								<p class="card-text fs-4 pb-4">
									飲食業界に特化しており、オーナー様や採用ご担当者様と密にコミュニケーションを取っているため、細やかな情報を網羅して提供できるのが強みです。
								</p>
							</div>
						</div>
					</div>
					<div class="col-4">
						<div class="card">
							<img src="/staff.jpg" class="card-img-top" alt="..." />
							<div class="card-body">
								<h3 class="card-title eb-garamond pt-4">
									Recruitment Consulting
								</h3>
								<p class="fs-5 pb-4">採用コンサルティング</p>
								<p class="card-text fs-4">
									飲食業に特化した採用コンサルティングサービスです。
								</p>
								<p class="card-text fs-4">
									採用分析はもちろん、飲食業界の採用マーケットのご説明、最適な採用媒体のご提案など、採用戦略立案から運用まで、ワンストップでサポートが可能です。
								</p>
								<p class="card-text fs-4 pb-5 mb-5">
									事業規模に合わせた最適な採用戦略のご提案をいたします。
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<section class="section-3 d-flex align-items-center justify-content-center">
			<div class="text-center mt-5 mb-5 pt-4 pm-5">
				<h1 class="eb-garamond fw-bold">Deet Features</h1>
				<p class="fs-4 mb-5">Deetの特徴</p>
				<p class="fs-3 pm-3">
					飲食店経営の経験があるメンバーや、人材事業出身のメンバーが在籍しています。
				</p>
				<p class="fs-3">
					飲食業界に対しての深い知見があり、様々な分野でサポートが可能です。
				</p>
				<p class="fs-3">
					他社様では公開していない独自の求人を抱えていたり、人材紹介会社では出会えないような求職者様をご紹介できることがDeetの強みです。
				</p>
			</div>
		</section>

		<section class="section-4">
			<div class="bg-image"></div>
			<div class="container">
				<div class="d-flex align-items-center justify-content-center">
					<div class="text-center">
						<h1 class="eb-garamond fw-bold">Registration Process</h1>
						<p class="fs-4 mb-5">登録までの流れ</p>

						<p class="fs-3">Step1. 資料請求</p>
						<p class="fs-4 mb-5">フォームよりお申し込みください。</p>

						<p class="fs-3">Step2. お打ち合わせ</p>
						<p class="fs-4 mb-5">
							担当者よりご連絡を差し上げますので、サービス説明とヒアリングをさせていただきます。
						</p>

						<p class="fs-3">Step3. 基本契約書の締結</p>
						<p class="fs-4 mb-5">契約締結後にサービスが利用開始できます。</p>
					</div>
				</div>
			</div>
		</section>

		<section class="section-5">
			<div class="container">
				<div
					class="d-flex align-items-center justify-content-center pt-5 mb-5 pb-5"
				>
					<div class="text-center">
						<h1 class="fw-bold eb-garamond">Information Request Form</h1>
						<p class="fs-4 mb-5">資料請求フォーム</p>

						<p class="fs-4">
							以下のフォームの必要事項をご入力ください。
							ご登録頂いたメールアドレスに資料をお送りいたします。
						</p>

						<div class="row align-items-center mt-5 pt-5">
							<div class="col-4 text-end">
								<label for="" class="col-form-label">企業/個人事業主様名</label>
							</div>
							<div class="col-8">
								<input
									type="text"
									id=""
									class="form-control"
									aria-labelledby=""
								/>
							</div>
						</div>

						<div class="row align-items-center">
							<div class="col-4 text-end">
								<label for="" class="col-form-label"
									>都道府県（本社所在地）</label
								>
							</div>
							<div class="col-8">
								<input
									type="text"
									id=""
									class="form-control"
									aria-labelledby=""
								/>
							</div>
						</div>

						<div class="row align-items-center">
							<div class="col-4 text-end">
								<label for="" class="col-form-label">姓</label>
							</div>
							<div class="col-3 col-3-alt">
								<input
									type="text"
									id=""
									class="form-control"
									aria-labelledby=""
								/>
							</div>
							<div class="col-1 text-end">
								<label for="" class="col-form-label">名</label>
							</div>
							<div class="col-3 col-3-alt">
								<input
									type="text"
									id=""
									class="form-control"
									aria-labelledby=""
								/>
							</div>
						</div>

						<div class="row align-items-center">
							<div class="col-4 text-end">
								<label for="" class="col-form-label">メールアドレス</label>
							</div>
							<div class="col-8">
								<input
									type="text"
									id=""
									class="form-control"
									aria-labelledby=""
								/>
							</div>
						</div>

						<div class="row align-items-center">
							<div class="col-4 text-end">
								<label for="" class="col-form-label">電話番号</label>
							</div>
							<div class="col-8">
								<input
									type="text"
									id=""
									class="form-control"
									aria-labelledby=""
								/>
							</div>
						</div>

						<button class="btn deet-btn">送信</button>
					</div>
				</div>
			</div>
		</section>

		<footer class="footer">
			<hr />
			<div class="d-flex justify-content-evenly text-center">
				<div>
					<a href="https://value-create.co.jp/" class="text-12px fs-4 mx-4"
						>会社概要</a
					>
				</div>
				<div>
					<a href="/privacy-policy2" class="text-12px fs-4 mx-4"
						>プライバシーポリシー</a
					>
				</div>
				<div>
					<a href="/law" class="text-12px fs-4 mx-4">特定商法に基づく表示</a>
				</div>
			</div>
			<hr />

			<div style="width: 100%" class="text-center fs-4">
				<span
					>&copy; {{ new Date().getFullYear() }} Deet All rights reserved.</span
				>
			</div>
		</footer>
	</div>
</template>

<style scoped>
.eb-garamond {
	font-family: 'EB Garamond', serif;
	font-optical-sizing: auto;
	font-weight: normal;
	font-style: normal;
}

.bg-image,
header,
footer,
.lp-wrap {
	min-width: 1200px;
}

header {
	position: fixed;
	top: 0;
	left: 0;
	width: 100vw;
	height: 72px;
	img {
		width: auto;
		height: 40px;
		margin-left: 110px;
	}
	> div {
		height: 72px;
	}
}
section {
	color: #1d1a16;
	&.carousel {
		margin-top: 72px;
		color: #fff;
		h1 {
			font-size: 6rem;
		}
		img {
			width: 100%;
			margin-top: 5em;
			margin-bottom: 5em;
		}
	}
	&.section-1 {
		padding: 17rem 0;
		h1 {
			font-size: 4rem;
		}
	}
	&.section-1,
	&.section-3,
	&.section-5 {
		background-color: #f2f2f2;
	}
	&.section-2 {
		padding: 8rem 3rem;
		.card {
			color: #fff;
			background-color: #342f28;
			.card-body {
				h3 {
					font-size: 4.5rem;
				}
				padding: 2rem 4rem;
			}
		}
	}
	&.section-3 {
		padding: 17rem 0;
		h1 {
			font-size: 4rem;
		}
	}
	&.section-4 {
		position: relative;
		h1 {
			font-size: 4rem;
		}
		.bg-image {
			background-image: url(/lp-back.jpg);
			filter: grayscale(1);
			background-position: 0px -225px;
			width: 100vw;
			position: absolute;
			top: 0;
			left: 0;
			height: 100%;
			z-index: 10;
			opacity: 0.2;
		}
		.container {
			z-index: 99;
			position: relative;
		}
		padding: 19rem 0;
		color: #fff;
	}
	&.section-5 {
		padding: 17rem 0;
		h1 {
			font-size: 4rem;
		}
		label,
		input {
			font-size: 1.6rem;
			margin-bottom: 4rem;
		}
		input {
			border: none;
			background: transparent;
			/* color: #1d1a16; */
			border-bottom: 1px solid #1d1a16;
			border-radius: 0;
			&:focus {
				box-shadow: none;
			}
		}
		.col-3-alt {
			width: 29%;
		}
	}
}
.deet-btn {
	background-color: #1d1a16 !important;
	color: #fff !important;
}
footer {
	a {
		color: #fff;
		text-decoration: none;
	}
	color: #fff;
	margin-left: 0 !important;
	width: 100vw !important;
	background-color: #1d1a16;
	height: 18rem;
}
</style>
