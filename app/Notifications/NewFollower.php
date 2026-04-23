<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewFollower extends Notification
{
    use Queueable;

    public function __construct(
        private readonly User $follower
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'             => 'new_follower',
            'actor_name'       => $this->follower->name,
            'actor_username'   => $this->follower->username,
            'actor_avatar_url' => $this->follower->avatar_url,
        ];
    }
}
