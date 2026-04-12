<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class ArticleController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $articles = Article::with('user:id,name,username')
            ->withCount('comments')
            ->when($search, fn($q) => $q->where('title', 'ilike', "%{$search}%"))
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('Admin/Articles', [
            'articles' => $articles,
            'filters'  => ['search' => $search, 'status' => $status],
        ]);
    }

    public function toggleStatus(Article $article)
    {
        $article->update([
            'status' => $article->status === 'published' ? 'draft' : 'published',
        ]);
        Cache::flush();

        return back()->with('success', "Статус статьи изменён.");
    }

    public function destroy(Article $article)
    {
        $article->delete();
        Cache::flush();

        return back()->with('success', "Статья удалена.");
    }
}
