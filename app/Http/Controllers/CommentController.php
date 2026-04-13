<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Article $article)
    {
        $validated = $request->validate([
            // Added min:3 to prevent single-character or empty-string comments
            'body'      => 'required|string|min:3|max:2000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $article->comments()->create([
            'user_id'   => $request->user()->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'body'      => $validated['body'],
        ]);

        return back()->with('success', 'Комментарий добавлен');
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return back()->with('success', 'Комментарий удалён');
    }
}
