<script setup lang="ts">
import { useMutation } from '@tanstack/vue-query';
import { ref } from 'vue';

const appStore = useAppStore();

const expandedIndex = ref<number | null>(0);

const toggleSubItems = (index: number) => {
	expandedIndex.value = expandedIndex.value === index ? null : index;
};
const authStore = useAuthStore();
const router = useRouter();

// Define the logout functionality
const { mutate: handleSiteadmintLogout } = useMutation({
	mutationFn: (e: Event) => {
		e.preventDefault();
		return authStore.logout();
	},
	onSuccess: () => {
		router.push('/siteadmin/login');
	},
});

const sidebarItems = [
	// {
		// title: 'ダッシュボード',
		// icon: 'icon-speedometer',
		// link: '/siteadmin',
	// },
	{
		title: '女性会員情報',
		icon: 'icon-user-female',
		link: '/siteadmin',
		subItems: [
			{
				title: '女性会員登録',
				icon: 'icon-user-follow',
				link: '/siteadmin/cast_register',
			},
			{
				title: '女性会員検索',
				icon: 'icon-magnifying-glass',
				link: '/siteadmin/cast_search',
			},
			{
				title: '特徴タグ登録',
				icon: 'icon-plus',
				link: '/siteadmin/cast_tag_register',
			},
			{
				title: 'カテゴリ登録',
				icon: 'icon-plus',
				link: '/siteadmin/cast_category_register',
			},
		],
	},
	{
		title: '男性会員情報',
		icon: 'icon-user',
		link: '/siteadmin',
		subItems: [
			{
				title: '男性会員登録',
				icon: 'icon-user-follow',
				link: '/siteadmin/customer_register',
			},
			{
				title: '男性会員検索',
				icon: 'icon-magnifying-glass',
				link: '/siteadmin/customer_search',
			},
			{
				title: 'カテゴリ登録',
				icon: 'icon-plus',
				link: '/siteadmin/customer_category_register',
			},
		],
	},
	{
		title: '管理情報',
		icon: 'icon-list-rich',
		link: '/siteadmin',
		subItems: [
			{ title: '成立Deet', icon: 'icon-plus', link: '/siteadmin/history_deet' },
			{ title: '成立コール', icon: 'icon-plus', link: '/siteadmin/history_call' },
			{ title: 'コール募集案件', icon: 'icon-plus', link: '/siteadmin/history_matching_request_control' },
			{ title: 'コール履歴', icon: 'icon-plus', link: '/siteadmin/history_matching_request' },
			{ title: '成立総合履歴', icon: 'icon-plus', link: '/siteadmin/history_matching_order' },
			{ title: 'ポイント履歴', icon: 'icon-plus', link: '/siteadmin/point_history' },
		],
	},
	{
		title: '営業情報',
		icon: 'icon-home',
		link: '/siteadmin',
		subItems: [
			{ title: '日時売上', icon: 'icon-wc', link: '/siteadmin/sales_daily' },
			{ title: '週次売上', icon: 'icon-plus', link: '/siteadmin/sales_weekly' },
			{ title: '月次売上', icon: 'icon-list-rich', link: '/siteadmin/sales_monthly' },
			{ title: '振込リスト', icon: 'icon-plus', link: '/siteadmin/sales_withdraw' },
		],
	},
	// {
	// 	title: '営業情報',
	// 	icon: 'icon-home',
	// 	link: '/siteadmin',
	// 	subItems: [
	// 		{ title: 'マッチングツール', icon: 'icon-wc', link: '/siteadmin' },
	// 		{ title: '売上入力', icon: 'icon-plus', link: '/siteadmin' },
	// 		{ title: '売上検索', icon: 'icon-list-rich', link: '/siteadmin' },
	// 		{ title: '予約入力', icon: 'icon-plus', link: '/siteadmin' },
	// 		{ title: '予約確認', icon: 'icon-list-rich', link: '/siteadmin' },
	// 		{ title: 'スタッフ登録', icon: 'icon-user-follow', link: '/siteadmin' },
	// 		{
	// 			title: 'スタッフ検索',
	// 			icon: 'icon-magnifying-glass',
	// 			link: '/siteadmin',
	// 		},
	// 		{ title: 'カテゴリ登録', icon: 'icon-plus', link: '/siteadmin' },
	// 		{ title: '店舗登録', icon: 'icon-plus', link: '/siteadmin' },
	// 		{ title: 'フロア/ルーム登録', icon: 'icon-plus', link: '/siteadmin' },
	// 	],
	// },
	{
		title: 'アカウント・ユーザー',
		icon: 'icon-equalizer',
		link: '/siteadmin',
		subItems: [
			{
				title: 'アカウント設定',
				icon: 'icon-settings',
				link: '/siteadmin/account_settings',
			},
			{
				title: '新規アカウント登録',
				icon: 'icon-settings',
				link: '/siteadmin/account_register',
			},
			{
				title: 'ユーザー設定',
				icon: 'icon-settings',
				link: '/siteadmin/user_settings',
			},
			{
				title: 'ユーザー登録',
				icon: 'icon-settings',
				link: '/siteadmin/user_register',
			},
			{
				title: 'ユーザー一覧',
				icon: 'icon-settings',
				link: '/siteadmin/user_list',
			},
			{
				title: 'チャット',
				icon: 'icon-history',
				link: '/siteadmin/chat',
			},
		],
	},
	{
		title: 'ログアウト',
		icon: 'icon-account-logout',
		link: '#', // Use # or null if you want to avoid the link behavior
		action: handleSiteadmintLogout,
	},
];
</script>

<template>
	<aside class="siteadmin-sidebar" v-if="appStore.isMainSidebarOpen">
		<div class="sidebar-menu">
			<ul class="px-2">
				<li v-for="(item, index) in sidebarItems" :key="index" class="mb-4 pb-1">
					<div v-if="item.subItems" class="pb-1">
						<div
							class="text-white fs-3"
							@click.prevent="toggleSubItems(index)"
							style="cursor: pointer"
						>
							<p>
								<span :class="item.icon" class="mx-1 fs-5"></span>
								{{ item.title }}
								<span
									v-if="item.subItems"
									:class="[expandedIndex === index ? 'open' : 'close', 'mx-2']"
								>></span>
							</p>
						</div>
					</div>
					<div v-else class="pb-1">
						<a
							v-if="item.action"
							href="javascript:void(0)"
							@click.prevent="item.action"
							class="text-white fs-3"
							style="text-decoration: none"
						>
							<p class="logout">
								<span :class="item.icon" class="mx-1 fs-5"></span>
								{{ item.title }}
							</p>
						</a>
						<NuxtLink
							v-else
							class="text-white fs-3"
							style="text-decoration: none"
							:to="item.link"
						>
							<p class="ms-4 ps-4">
								<span :class="item.icon" class="mx-1 fs-5"></span>
								{{ item.title }}
							</p>
						</NuxtLink>
					</div>
					<div v-if="expandedIndex === index" class="mx-2 pb-2">
						<li v-for="(subItem, subIndex) in item.subItems" :key="subIndex" class="my-2 fs-3 mb-4">
						<NuxtLink class="text-white" 
							    style="text-decoration: none" 
							    :to="subItem.link">
								<p class="ms-4 ps-4 pb-1">
									<!-- <span :class="subItem.icon" class="mx-2 fs-5"></span> -->
									{{ subItem.title }}
								</p>
						</NuxtLink>
						</li>
					</div>
				</li>
			</ul>
		</div>
	</aside>
</template>

<style scoped>

.sidebar-menu {
	span.open {
		display: inline-block;
        transform: scale3d(1.7, .7, 1) rotate(-90deg);		
	} 
	span.close {
		transform: scale3d(1.7, .7, 1) rotate(90deg);
		display: inline-block;		
	} 
	.logout {
		span::before {
			transform: rotate(180deg);
			display: inline-block;				
		}
	}
}
</style>
