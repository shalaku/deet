<template>
  <div class="toggleWapper">
    <label for="toggle" class="toggleLabel fs-4 me-3">通知</label>
    <button class="toggleButton" :class="{ active: notificationsEnabled }" id="toggle" @click="toggleNotifications"></button>
  </div>
</template>
  
<script>
  import { useNuxtApp } from '#app';
import { onMounted, ref } from 'vue';
  
  export default {
    setup() {
      const { $enablePushNotifications, $disablePushNotifications, $checkSubscription } = useNuxtApp();
      const notificationsEnabled = ref(false);
  
      const checkSubscriptionStatus = async () => {
        notificationsEnabled.value = await $checkSubscription();
        updateToggleButton();
      };
  
      const updateToggleButton = () => {
        const toggleButton = document.getElementById('toggle');
        if (notificationsEnabled.value) {
          toggleButton.classList.add('darkmode');
        } else {
          toggleButton.classList.remove('darkmode');
        }
      };
  
      onMounted(() => {
        checkSubscriptionStatus();
      });
  
      const toggleNotifications = async () => {
        if (notificationsEnabled.value) {
          await $disablePushNotifications();
        } else {
          await $enablePushNotifications();
        }
        notificationsEnabled.value = !notificationsEnabled.value;
        updateToggleButton();
      };
  
      return {
        notificationsEnabled,
        toggleNotifications
      };
    }
  };
</script>

<style scoped>

.toggleWapper {
  display: flex;
  align-items: center;
  justify-content: center;
}

.toggleButton {
  width: 46px;
  height: 24px;
  position: relative;
  border: none;
  border-radius: 12px;
  background-color: #BCBDBD;
  cursor: pointer;
  &::after {
    content: "";
    position: absolute;
    width: 20px;
    height: 20px;
    border-radius: 100%;
    left: 2px;
    top: 2px;
    background: #FFFFFF;
    border: 1px solid #BCBDBD;
    transition: all 0.1s ease 0s;
  }
  &.active {
    background-color: #c1a06c;
    &::after {
      left: 23px;
      right: 2px;
    }
  }
}

.toggleLabel {
  color: #FFF;
  cursor: pointer;
}
</style>