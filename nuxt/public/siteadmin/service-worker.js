self.addEventListener('push', (event) => {
  if (!(self.Notification && self.Notification.permission === 'granted')) {
    return;
}
const data = event.data ? event.data.json() : { title: 'Default', body: 'No data' };
    const options = {
      body: data.body,
      icon: data.icon || '/user-default-icon.png',
      badge: data.badge || '/user-default-badge.png',
      data: { url: data.url || '/user' },
    };
    event.waitUntil(
      self.registration.showNotification(data.title, options)
    );
  });
  
  self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    event.waitUntil(clients.openWindow(event.notification.data.url));
  });
  