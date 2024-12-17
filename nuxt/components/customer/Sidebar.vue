<script setup lang="ts">
import { useMutation } from '@tanstack/vue-query';
const appStore = useAppStore();
const props = defineProps(['isVisible','toggleSidebar']);


const items = ref([
	{
		title: 'メニュー',
		icon: 'icon-list',
		content: [
			{
				text: 'メッセージ',
				route: '/user/chat',
				icon: 'icon-list',
			},
			{
				text: 'Deetで探す',
				route: '/user/search_cast',
				icon: 'icon-list',
			},
			{
				text: 'コールで呼ぶ',
				route: '/user/call_cast',
				icon: 'icon-history',
			},
			// {
				// text: 'お気に入り',
				// route: '/user/fav_cast',
				// icon: 'icon-list',
			// },
			// {
				// text: 'ランキング',
				// route: '/user/ranking_cast',
				// icon: 'icon-list',
			// },
			{
				text: '注文履歴',
				route: '/user/order_history',
				icon: 'icon-list',
			},
			{
				text: 'ポイント履歴',
				route: '/user/point_history',
				icon: 'icon-list',
			},
		],
		isOpen: true,
	},
]);

const toggleItem = (index: number) => {
	items.value[index].isOpen = !items.value[index].isOpen;
};
const authStore = useAuthStore();
const router = useRouter();

const { mutate: handleUserLogout } = useMutation({
	mutationFn: (e: Event) => {
		e.preventDefault();
		return authStore.logout();
	},
	onSuccess: () => {
		router.push('/user/login');
	},
});
</script>

<template lang="html">
	<div class="shadow-screen d-block d-lg-none" @click="toggleSidebar"></div>
	<div
		v-if="appStore.isMainSidebarOpen"
		:class="['sidebar-frontend', { 'is-visible': isVisible }]"
	>
		<ul class="nav flex-column">
			<li class="nav-item">
				<NuxtLink class="sidebar-link" href="/user/mypage" @click="toggleSidebar">
					<!-- <span class="icon-comment-bubble icon"></span> -->
					<strong>マイページ</strong>
				</NuxtLink>
			</li>
			<li
				v-for="(item, index) in items"
				:key="index"
				class="nav-item nav-item-dark"
			>
				<div class="accordion-item">
					<!-- <h2 class="accordion-header"> -->
					<!-- <button -->
					<!-- class="accordion-button custom-accordion-button" -->
					<!-- :class="{ collapsed: !item.isOpen }" -->
					<!-- @click="toggleItem(index)" -->
					<!-- > -->
					<!-- <span :class="item.icon" class="icon"></span> -->
					<!-- <span class="accordion-title">{{ item.title }}</span> -->
					<!-- <span -->
					<!-- class="icon-chevron-top chevron-icon" -->
					<!-- :class="{ 'chevron-rotated': !item.isOpen }" -->
					<!-- ></span> -->
					<!-- </button> -->
					<!-- </h2> -->
					<div
						class="accordion-collapse collapse"
						:class="{ show: item.isOpen }"
					>
						<div class="accordion-body">
							<NuxtLink
								v-for="(content, contentIndex) in item.content"
								:key="contentIndex"
								:href="content.route"
								@click="toggleSidebar"
								class="content-item"
							>
								<!-- <span :class="content.icon" class="icon"></span> -->
								{{ content.text }}
							</NuxtLink>
						</div>
					</div>
				</div>
			</li>
			<li class="nav-item">
				<NuxtLink class="sidebar-link" href="#" @click="handleUserLogout">
					<!-- <span class="icon-comment-bubble icon"></span> -->
					ログアウト
				</NuxtLink>
			</li>
			<li class="nav-item">
				<NuxtLink class="sidebar-link" href="/user/faq" @click="toggleSidebar">
					<!-- <span class="icon-comment-bubble icon"></span> -->
					初めての方へ
				</NuxtLink>
			</li>
		</ul>
	</div>
</template>
