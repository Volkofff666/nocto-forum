<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\AdminLogger;
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
        $newStatus = $article->status === 'published' ? 'draft' : 'published';
        $article->update(['status' => $newStatus]);
        Cache::flush();

        AdminLogger::log('toggle_article', "Статья «{$article->title}» → {$newStatus}", 'article', $article->id);

        return back()->with('success', "Статус статьи изменён.");
    }

    public function destroy(Article $article)
    {
        $title = $article->title;
        $article->delete();
        Cache::flush();

        AdminLogger::log('delete_article', "Статья «{$title}» удалена", 'article', null);

        return back()->with('success', "Статья удалена.");
    }
}
