<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BookmarkController extends Controller
{
    public function toggle(Request $request, Article $article)
    {
        $user = $request->user();
        $exists = $user->bookmarks()->where('article_id', $article->id)->exists();

        if ($exists) {
            $user->bookmarks()->where('article_id', $article->id)->delete();
        } else {
            $user->bookmarks()->create(['article_id' => $article->id]);
        }

        return back();
    }

    public function index(Request $request): Response
    {
        $articles = Article::published()
            ->join('bookmarks', 'articles.id', '=', 'bookmarks.article_id')
            ->where('bookmarks.user_id', $request->user()->id)
            ->with('user')
            ->withCount('comments')
            ->select('articles.*', 'bookmarks.created_at as bookmarked_at')
            ->orderByDesc('bookmarks.created_at')
            ->paginate(15);

        return Inertia::render('Articles/Bookmarks', [
            'articles' => $articles,
        ]);
    }
}
