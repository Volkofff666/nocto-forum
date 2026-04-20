<?php

namespace App\Http\Controllers;

use App\Enums\ArticleCategory;
use App\Models\Article;
use App\Services\HtmlPurifierService;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class ArticleController extends Controller
{
    public function index(Request $request): Response
    {
        $category = $request->input('category');
        $sort     = $request->input('sort', 'latest');
        $tag      = $request->input('tag');

        $cacheKey = 'articles.' . md5(json_encode([
            'category' => $category, 'sort' => $sort,
            'tag' => $tag, 'page' => $request->input('page', 1),
        ]));

        $articles = Cache::remember($cacheKey, 60, function () use ($category, $sort, $tag) {
            $query = Article::published()
                ->with('user')
                ->withCount('comments');

            if ($category) $query->byCategory($category);
            if ($tag)      $query->whereJsonContains('tags', $tag);

            return $query->ordered($sort)->paginate(10)->withQueryString();
        });

        $categoryCounts = Cache::remember('category_counts', 60, function () {
            return Article::published()
                ->selectRaw('category, count(*) as count')
                ->groupBy('category')
                ->pluck('count', 'category');
        });

        $bookmarkedIds = auth()->check()
            ? auth()->user()->bookmarks()->pluck('article_id')->toArray()
            : [];

        $tgSubscribers = app(TelegramService::class)->getSubscriberCount();

        return Inertia::render('Articles/Index', [
            'articles'       => $articles,
            'filters'        => ['category' => $category, 'sort' => $sort, 'tag' => $tag],
            'categoryCounts' => $categoryCounts,
            'bookmarkedIds'  => $bookmarkedIds,
            'tgSubscribers'  => $tgSubscribers,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Articles/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'     => 'required|string|max:255',
            'excerpt'   => 'required|string|max:500',
            'body'      => 'required|string|max:200000',
            'category'  => 'required|in:' . implode(',', ArticleCategory::values()),
            'cover_url' => 'nullable|url|max:500',
            'tags'      => 'nullable|array|max:7',
            'tags.*'    => 'string|max:40',
            'publish'   => 'sometimes|boolean',
        ]);

        $purifier = app(HtmlPurifierService::class);

        $article = $request->user()->articles()->create([
            'title'     => $validated['title'],
            'excerpt'   => $validated['excerpt'],
            'body'      => $purifier->clean($validated['body']),
            'category'  => $validated['category'],
            'cover_url' => $validated['cover_url'] ?? null,
            'tags'      => $validated['tags'] ?? [],
            'status'    => isset($validated['publish']) && $validated['publish'] ? 'published' : 'draft',
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

        // Security: prevent non-authors from accessing draft articles directly by slug
        if ($article->status === 'draft' && auth()->id() !== $article->user_id) {
            abort(403);
        }

        $sessionKey = 'viewed.' . $article->id;
        if (!session()->has($sessionKey)) {
            $article->increment('views_count');
            session()->put($sessionKey, true);
        }

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

        $isBookmarked = auth()->check()
            ? auth()->user()->bookmarks()->where('article_id', $article->id)->exists()
            : false;

        return Inertia::render('Articles/Show', [
            'article'      => $article,
            'related'      => $related,
            'userVote'     => $userVote,
            'isBookmarked' => $isBookmarked,
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
            'title'     => 'required|string|max:255',
            'excerpt'   => 'required|string|max:500',
            'body'      => 'required|string|max:200000',
            'category'  => 'required|in:' . implode(',', ArticleCategory::values()),
            'cover_url' => 'nullable|url|max:500',
            'tags'      => 'nullable|array|max:7',
            'tags.*'    => 'string|max:40',
        ]);

        $purifier = app(HtmlPurifierService::class);
        $validated['body'] = $purifier->clean($validated['body']);

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

    public function drafts(Request $request): Response
    {
        $drafts = $request->user()
            ->articles()
            ->where('status', 'draft')
            ->latest()
            ->paginate(15);

        return Inertia::render('Articles/Drafts', [
            'drafts' => $drafts,
        ]);
    }

    private function clearArticleCache(): void
    {
        // Replaced Cache::flush() (wiped ALL cache including sessions/rate-limits)
        // with targeted invalidation of the known article-related cache keys.
        Cache::forget('category_counts');
        Cache::forget('articles_latest');
    }
}
