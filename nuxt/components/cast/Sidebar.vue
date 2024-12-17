<script setup lang="ts">
import { useMutation } from '@tanstack/vue-query';

const appStore = useAppStore();

const props = defineProps(['isVisible','toggleSidebar']);

const items = ref([
	{
		title: '注文',
		icon: 'icon-list',
		content: [
			{
				text: '募集案件',
				route: '/cast/order_history?tab=orderAndRequest',
				icon: 'icon-list',
			},
			{
				text: '確定案件',
				route: '/cast/order_history?tab=confirmed',
				icon: 'icon-list',
			},
			{
				text: '案件履歴',
				route: '/cast/order_history?tab=history',
				icon: 'icon-history',
			},
			{
				text: 'チャット',
				route: '/cast/chat',
				icon: 'icon-history',
			},
			{
				text: 'ポイント履歴',
				route: '/cast/point_history',
				icon: 'icon-list',
			},
			// {
				// text: 'ランキング',
				// route: '/cast/ranking',
				// icon: 'icon-list',
			// },
		],
		isOpen: true,
	},
]);

const toggleItem = (index: number) => {
	items.value[index].isOpen = !items.value[index].isOpen;
};

const authStore = useAuthStore();
const router = useRouter();

const { mutate: handleCastLogout } = useMutation({
	mutationFn: (e: Event) => {
		e.preventDefault();
		return authStore.logout();
	},
	onSuccess: () => {
		router.push('/cast/login');
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
				<NuxtLink class="sidebar-link" to="/cast/mypage" @click="toggleSidebar">
					<!-- <span class="icon-home icon"></span> -->
					<strong>マイページ</strong>
				</NuxtLink>
			</li>
			<li
				v-for="(item, index) in items"
				:key="index"
				class="nav-item nav-item-dark"
			>
				<div class="accordion-item">
					<div
						class="accordion-collapse collapse"
						:class="{ show: item.isOpen }"
					>
						<div class="accordion-body">
							<NuxtLink
								v-for="(content, contentIndex) in item.content"
								:key="contentIndex"
								:to="content.route"
								class="content-item"
								@click="toggleSidebar"
							>
								{{ content.text }}
							</NuxtLink>
						</div>
					</div>
				</div>
			</li>
			<li class="nav-item">
				<NuxtLink class="sidebar-link" to="#" @click="handleCastLogout">
					<!-- <span class="icon-comment-bubble icon"></span> -->
					ログアウト
				</NuxtLink>
			</li>
			<li class="nav-item">
				<NuxtLink class="sidebar-link" to="/cast/faq" @click="toggleSidebar">
					<span class="icon-comment-bubble icon"></span>
					初めての方へ
				</NuxtLink>
			</li>
		</ul>
	</div>
</template>
