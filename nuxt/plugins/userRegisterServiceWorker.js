

export default defineNuxtPlugin(() => {
  if (process.client && 'serviceWorker' in navigator) {
    navigator.serviceWorker.register('/user/service-worker.js',{
      scope: '/user/' // Register service worker for /cast/* URLs only
    })
      .then((registration) => {
        console.log('User Service Worker registered with scope:', registration.scope);
      })
      .catch((error) => {
        console.error('User Service Worker registration failed:', error);
      });
  }
});
