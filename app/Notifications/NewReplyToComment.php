<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewReplyToComment extends Notification
{
    use Queueable;

    public function __construct(private readonly Comment $reply)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'            => 'new_reply',
            'article_id'      => $this->reply->article_id,
            'article_slug'    => $this->reply->article->slug,
            'article_title'   => $this->reply->article->title,
            'actor_name'      => $this->reply->user->name,
            'actor_username'  => $this->reply->user->username,
            'comment_preview' => mb_substr(strip_tags($this->reply->body), 0, 100),
        ];
    }
}
