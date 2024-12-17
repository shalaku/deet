import { defineNuxtPlugin } from '#app';
import { requestAPI } from '~/libs/api';

function urlBase64ToUint8Array(base64String) {
  var padding = "=".repeat((4 - (base64String.length % 4)) % 4);
  var base64 = (base64String + padding).replace(/\-/g, "+").replace(/_/g, "/");

  var rawData = window.atob(base64);
  var outputArray = new Uint8Array(rawData.length);

  for (var i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i);
  }

  return outputArray;
}

function subscribe(sub) {
  const key = sub.getKey('p256dh');
  const token = sub.getKey('auth');
  const contentEncoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0];

  const data = {
    endpoint: sub.endpoint,
    public_key: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
    auth_token: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null,
    encoding: contentEncoding,
  };

  const response = requestAPI.getPushNotification(data);
  console.log(response);
}

const VAPID_PUBLIC_KEY = import.meta.env.VITE_VAPID_PUBLIC_KEY;

function enablePushNotifications() {
  navigator.serviceWorker.ready.then(registration => {
    registration.pushManager.getSubscription().then(subscription => {
      if (subscription) {
        return subscription;
      }

      const serverKey = urlBase64ToUint8Array(VAPID_PUBLIC_KEY);

      return registration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: serverKey
      });
    }).then(subscription => {
      if (!subscription) {
        alert('Error occurred while subscribing');
        return;
      }
      subscribe(subscription);
    });
  });
}

function disablePushNotifications() {
  navigator.serviceWorker.ready.then(registration => {
    registration.pushManager.getSubscription().then(subscription => {
      if (!subscription) {
        return;
      }

      subscription.unsubscribe().then(() => {
        const data = {
          endpoint: subscription.endpoint,
        };

        requestAPI.getPushNotificationunsubscribe(data)
          .then(response => {
            console.log('Success:', response);
          })
          .catch(error => {
            console.error('Error:', error);
          });
      });
    });
  });
}

function checkSubscription() {
  return navigator.serviceWorker.ready.then(registration => {
    return registration.pushManager.getSubscription().then(subscription => {
      return !!subscription;
    });
  });
}

export default defineNuxtPlugin(() => {
  return {
    provide: {
      enablePushNotifications,
      disablePushNotifications,
      checkSubscription
    }
  };
});