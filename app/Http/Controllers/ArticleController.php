<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class ArticleController extends Controller
{
    public function index(Request $request): Response
    {
        $category = $request->input('category');
        $sort = $request->input('sort', 'latest');

        $cacheKey = 'articles.' . md5(json_encode(['category' => $category, 'sort' => $sort, 'page' => $request->input('page', 1)]));

        $articles = Cache::remember($cacheKey, 60, function () use ($category, $sort) {
            $query = Article::published()
                ->with('user')
                ->withCount('comments');

            if ($category) {
                $query->byCategory($category);
            }

            return $query->ordered($sort)->paginate(10)->withQueryString();
        });

        $categoryCounts = Cache::remember('category_counts', 60, function () {
            return Article::published()
                ->selectRaw('category, count(*) as count')
                ->groupBy('category')
                ->pluck('count', 'category');
        });

        return Inertia::render('Articles/Index', [
            'articles'       => $articles,
            'filters'        => ['category' => $category, 'sort' => $sort],
            'categoryCounts' => $categoryCounts,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Articles/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'    => 'required|string|max:255',
            'excerpt'  => 'required|string|max:500',
            'body'     => 'required|string',
            'category' => 'required|in:proxy,vpn,security,tools,other',
            'publish'  => 'sometimes|boolean',
        ]);

        $article = $request->user()->articles()->create([
            'title'    => $validated['title'],
            'excerpt'  => $validated['excerpt'],
            'body'     => $validated['body'],
            'category' => $validated['category'],
            'status'   => isset($validated['publish']) && $validated['publish'] ? 'published' : 'draft',
        ]);

        if ($article->status === 'published') {
            $this->clearArticleCache();
        }

        return redirect()->route('articles.show', $article->slug);
    }

    public function show(string $slug): Response
    {
        $article = Article::where('slug', $slug)
            ->with([
                'user',
                'comments' => fn($q) => $q->whereNull('parent_id')->with(['user', 'replies.user']),
            ])
            ->firstOrFail();

        $article->increment('views_count');

        $related = Article::published()
            ->where('category', $article->category)
            ->where('id', '!=', $article->id)
            ->with('user')
            ->latest()
            ->limit(3)
            ->get();

        $userVote = null;
        if (auth()->check()) {
            $vote = $article->votes()->where('user_id', auth()->id())->first();
            $userVote = $vote?->type;
        }

        return Inertia::render('Articles/Show', [
            'article'  => $article,
            'related'  => $related,
            'userVote' => $userVote,
        ]);
    }

    public function edit(Article $article): Response
    {
        $this->authorize('update', $article);

        return Inertia::render('Articles/Edit', [
            'article' => $article,
        ]);
    }

    public function update(Request $request, Article $article)
    {
        $this->authorize('update', $article);

        $validated = $request->validate([
            'title'    => 'required|string|max:255',
            'excerpt'  => 'required|string|max:500',
            'body'     => 'required|string',
            'category' => 'required|in:proxy,vpn,security,tools,other',
        ]);

        $article->update($validated);
        $this->clearArticleCache();

        return redirect()->route('articles.show', $article->slug);
    }

    public function destroy(Article $article)
    {
        $this->authorize('delete', $article);

        $article->delete();
        $this->clearArticleCache();

        return redirect()->route('articles.index');
    }

    public function publish(Article $article)
    {
        $this->authorize('update', $article);

        $article->update(['status' => 'published']);
        $this->clearArticleCache();

        return redirect()->route('articles.show', $article->slug);
    }

    private function clearArticleCache(): void
    {
        Cache::flush();
    }
}
