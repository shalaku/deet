<?php

namespace App\DTOs;
class CustomNotificationDTO
{
    public string $title;
    public string $message;
    public ?string $icon = null;


    public function __construct(
        string $title,
        string $message,
        ?string $icon = null
    ) {
        $this->title = $title;
        $this->message = $message;
        $this->icon = $icon;
    }
}
