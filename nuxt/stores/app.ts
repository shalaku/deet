import { defineStore } from 'pinia';

interface AppStoreState {
	isMainSidebarOpen: boolean;
}

const initialState: AppStoreState = {
	isMainSidebarOpen: true,
};

export const useAppStore = defineStore('AppStore', {
	state: () => initialState,
	actions: {
		toggleMainSidebar() {
			this.isMainSidebarOpen = !this.isMainSidebarOpen;
		},
	},
});
