<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use App\Notifications\NewCommentOnArticle;
use App\Notifications\NewReplyToComment;
use App\Services\HtmlPurifierService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommentController extends Controller
{
    public function store(Request $request, Article $article)
    {
        $validated = $request->validate([
            // Added min:3 to prevent single-character or empty-string comments
            'body'      => 'required|string|min:3|max:2000',
            'parent_id' => [
                'nullable',
                Rule::exists('comments', 'id')->where('article_id', $article->id),
            ],
        ]);

        $purifier = app(HtmlPurifierService::class);

        $comment = $article->comments()->create([
            'user_id'   => $request->user()->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'body'      => $purifier->clean($validated['body']),
        ]);

        $comment->load(['user', 'article']);

        // Notify article author when someone comments (skip if the commenter is the author)
        if ($article->user_id !== $request->user()->id) {
            $article->user->notify(new NewCommentOnArticle($comment));
        }

        // Notify parent comment author on reply (skip if the commenter is the parent's author)
        if ($comment->parent_id) {
            $parent = $comment->parent()->with('user')->first();
            if ($parent && $parent->user_id !== $request->user()->id) {
                $parent->user->notify(new NewReplyToComment($comment));
            }
        }

        return back()->with('success', 'Комментарий добавлен');
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return back()->with('success', 'Комментарий удалён');
    }
}
