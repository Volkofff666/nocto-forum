<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewCommentOnArticle extends Notification
{
    use Queueable;

    public function __construct(private readonly Comment $comment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'            => 'new_comment',
            'article_id'      => $this->comment->article_id,
            'article_slug'    => $this->comment->article->slug,
            'article_title'   => $this->comment->article->title,
            'actor_name'      => $this->comment->user->name,
            'actor_username'  => $this->comment->user->username,
            'comment_preview' => mb_substr(strip_tags($this->comment->body), 0, 100),
        ];
    }
}
