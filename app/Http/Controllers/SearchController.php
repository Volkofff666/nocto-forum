<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    public function index(Request $request): Response
    {
        $q = trim($request->input('q', ''));

        if (strlen($q) < 2) {
            return Inertia::render('Search', [
                'q'        => $q,
                'articles' => [],
                'users'    => [],
                'total'    => 0,
            ]);
        }

        $articles = Article::published()
            ->with('user:id,name,username,avatar_url')
            ->withCount('comments')
            ->where(fn($query) => $query
                ->where('title',   'ilike', "%{$q}%")
                ->orWhere('excerpt', 'ilike', "%{$q}%")
            )
            ->latest()
            ->limit(10)
            ->get();

        $users = User::where('name',     'ilike', "%{$q}%")
            ->orWhere('username', 'ilike', "%{$q}%")
            ->withCount('articles')
            ->limit(5)
            ->get(['id', 'name', 'username', 'avatar_url', 'bio', 'role']);

        return Inertia::render('Search', [
            'q'        => $q,
            'articles' => $articles,
            'users'    => $users,
            'total'    => $articles->count() + $users->count(),
        ]);
    }
}
