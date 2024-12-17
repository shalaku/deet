

export default defineNuxtPlugin(() => {
  if (process.client && 'serviceWorker' in navigator) {
    navigator.serviceWorker.register('/cast/service-worker.js',{
      scope: '/cast/' 
    })
      .then((registration) => {
        console.log('Cast Service Worker registered with scope:', registration.scope);
      })
      .catch((error) => {
        console.error('Cast Service Worker registration failed:', error);
      });
  }
});
