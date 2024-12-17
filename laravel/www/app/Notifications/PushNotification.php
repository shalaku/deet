<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class PushNotification extends Notification
{
    use Queueable;

    private string $title;
    private string $body;
    private string $icon;

    /**
     * Create a new notification instance.
     */
    public function __construct($title, $body, $icon = null)
    {
        $this->title = $title;
        $this->body = $body;
        $this->icon = $icon;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }


    public function toWebPush($notifiable)
    {
        return (new WebPushMessage())
            ->title($this->title)
            ->body($this->body);
            //->icon($this->icon ?? url('/default-icon.png'));
             // Send to the 'notifications' queue
            //->delay(now()->addSeconds(30));  // Delay by 30 seconds
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
