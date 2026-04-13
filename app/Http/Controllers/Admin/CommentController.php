<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Services\AdminLogger;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CommentController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->input('search');

        $comments = Comment::with(['user:id,name,username', 'article:id,title,slug'])
            ->when($search, fn($q) => $q->where('body', 'ilike', "%{$search}%"))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return Inertia::render('Admin/Comments', [
            'comments' => $comments,
            'filters'  => ['search' => $search],
        ]);
    }

    public function destroy(Comment $comment)
    {
        $preview = mb_substr($comment->body, 0, 50);
        $comment->delete();

        AdminLogger::log('delete_comment', "Комментарий удалён: «{$preview}…»", 'comment', null);

        return back()->with('success', "Комментарий удалён.");
    }
}
