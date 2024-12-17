

export default defineNuxtPlugin(() => {
  if (process.client && 'serviceWorker' in navigator) {
    navigator.serviceWorker.register('/siteadmin/service-worker.js',{
      scope: '/siteadmin/' 
    })
      .then((registration) => {
        console.log('Siteadmin Service Worker registered with scope:', registration.scope);
      })
      .catch((error) => {
        console.error('Siteadmin Service Worker registration failed:', error);
      });
  }
});
