export class NotificationService {
    private static notificationTimer: number | null = null;
  
    public static async requestPermission(): Promise<boolean> {
      if (!('Notification' in window)) {
        console.error('This browser does not support notifications.');
        return false;
      }
  
      if (Notification.permission === 'granted') {
        return true;
      }
  
      try {
        const permission = await Notification.requestPermission();
        return permission === 'granted';
      } catch (error) {
        console.error('Notification permission request failed:', error);
        return false;
      }
    }
  
    public static showNotification(title: string, options?: NotificationOptions): void {
      if (Notification.permission === 'granted') {
        new Notification(title, options);
      } else {
        console.warn('Notifications are not enabled.');
      }
    }
  
    public static startRandomNotifications(interval: number = 300000): void {
      const randomNotification = () => {
        const notifTitle = "hello";
        const notifBody = `Created by naib.`;
        const notifImg = `/apple-touch-icon.png`;
        const options = {
          body: notifBody,
          icon: notifImg,
        };
  
        new Notification(notifTitle, options);
  
        this.notificationTimer = window.setTimeout(randomNotification, interval);
      };
  
      // Start the notifications
      randomNotification();
    }
  
    public static stopRandomNotifications(): void {
      if (this.notificationTimer !== null) {
        clearTimeout(this.notificationTimer); // Clear the timer
        this.notificationTimer = null; // Reset the timer reference
        console.log('Random notifications stopped.');
      }
    }
   
    

  }
  