<template>
	<div id="app" class="chat-app">
	  <client-only>
		<TUIKit 
		  v-if="SDKAppID && userID && userSig" 
		  :SDKAppID="SDKAppID" 
		  :userID="userID" 
		  :userSig="userSig" 
		  :conversationID="conversationID" 
		/>
		<TUICallKit class="callkit-container" :allowedMinimized="true" :allowedFullScreen="true" />
	  </client-only>
	</div>
  </template>
  
  <script setup>
  import { TUICallKit } from '@tencentcloud/call-uikit-vue';
  import { onMounted, ref } from 'vue';
  import { useRouter } from 'vue-router';
  import { customerAPI } from '../../libs/api/customer-api';
  import { TUIKit } from './../TUIKit';
  
  const router = useRouter();
  
  const SDKAppID = ref(null);
  const userID = ref('');
  const userSig = ref('');
  const conversationID = ref('');
  
  // Fetch user profile data and conversationID
  const fetchCustomerProfile = async () => {
	try {
	  const storedUserId = localStorage.getItem('user_id');
  
	  if (!storedUserId) {
		console.error('No user_id found in localStorage');
		return;
	  }
  
	  // Fetching profile and setting SDKAppID, userID, and userSig
	  const response = await customerAPI.getCustomerProfile();
	  if (response && response.data) {
		SDKAppID.value = Number(response.data.sdkAppId);
		userID.value = response.data.user_id;
		userSig.value = response.data.user_sig;
  
		// Fetch conversationID directly based on userId
		conversationID.value = `C2C${storedUserId}`;
		console.log('Customer Data:', userID.value, userSig.value, conversationID.value);
	  }
	} catch (error) {
	  console.error('Error fetching customer data:', error);
	}
  };
  
  // Initialize chat on component mount
  onMounted(async () => {
	await fetchCustomerProfile();
  });
 

  

	
  
	// const userID = 'habib';
	// const userSig = 'eJwtzFELgjAUhuH-cq5DzubmQugiw4JRVw3C7jRnHVoxVEqJ-numXn7PB*8HzP4YvGwNMfAAYTFuKu2zpYpGvuUFFfPRlPfceyoh5ogsVEJMbjtPtYWYSSmHBydt6fE3FTIRLTFSc4OuQ7XHzF36dHs660oIs9Ecd2Fn1tnbSM2dSw6ik6lR2OAKvj*3uS*3';
	// const conversationID = 'C2Ccus3'; // Assuming 'C2C' prefix is used for one-on-one chats


  </script>



  
<style lang="scss">

  
</style>
  