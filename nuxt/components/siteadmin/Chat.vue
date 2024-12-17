<script setup>
import { TUICallKit } from '@tencentcloud/call-uikit-vue';
import { useRouter } from 'vue-router';
import { TUIKit } from './../TUIKit';

const authStore = useAuthStore();
const router = useRouter();
const SDKAppID = authStore.user?.sdkAppId;
const userID = authStore.user?.user_id;
const userSig = authStore.user?.user_sig;
</script>

<template>
	<div id="app" class="chat-app">
		<client-only>
			<p v-if="!SDKAppID || !userID || !userSig">Loading...</p>

			<TUIKit v-if="SDKAppID && userID && userSig" :SDKAppID="SDKAppID" :userID="userID" :userSig="userSig" />

			<TUICallKit v-if="SDKAppID && userID && userSig" class="callkit-container" :allowedMinimized="true"
				:allowedFullScreen="false" />
		</client-only>
	</div>
</template>

<style lang="scss"></style>