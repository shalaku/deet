<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { castAPI } from '~/libs/api';

// Direct component imports
import { TUICallKit } from '@tencentcloud/call-uikit-vue';
import { TUIKit } from './../TUIKit';

const router = useRouter();
const SDKAppID = ref(null);
const userID = ref('');
const userSig = ref('');
const conversationID = ref('');

// Fetch data and handle errors
const fetchCastProfile = async () => {
  try {
    console.log('Fetching profile...');
    const response = await castAPI.profile();
    console.log('API response:', response);
    if(localStorage.getItem('ConversationID')){
       conversationID.value=localStorage.getItem('ConversationID');
     }else{
       conversationID.value=ref('');
  }

    if (response.data && response.data.cast) {
      SDKAppID.value = Number(response.data.cast.sdkAppId);
      userID.value = response.data.cast.user_id;
      userSig.value = response.data.cast.user_sig;
      console.log('User data set:', { SDKAppID: SDKAppID.value, userID: userID.value, userSig: userSig.value });
    } else {
      console.error('Invalid response structure:', response);
    }
  } catch (error) {
    console.error('Error fetching customer data:', error);
  }
};

// Trigger data fetch on component mount
onMounted(async () => {
  console.log('Component mounted');
  await fetchCastProfile();
  
});
</script>

<template>
  <div id="app" class="chat-app">
    <client-only>
      <p v-if="!SDKAppID || !userID || !userSig">Loading...</p>

      <TUIKit
        v-if="SDKAppID && userID && userSig"
        :SDKAppID="SDKAppID"
        :userID="userID"
        :userSig="userSig"
        :conversationID="conversationID" 
      />

      <TUICallKit
        v-if="SDKAppID && userID && userSig"
        class="callkit-container"
        :allowedMinimized="true"
        :allowedFullScreen="false"
      />
    </client-only>
  </div>
</template>

<style lang="scss">


</style>