<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'users'     => User::count(),
                'published' => Article::published()->count(),
                'drafts'    => Article::where('status', 'draft')->count(),
                'comments'  => Comment::count(),
            ],
            'recentUsers' => User::latest()->limit(5)->get(['id', 'name', 'username', 'role', 'created_at']),
            'recentArticles' => Article::with('user:id,name,username')
                ->latest()->limit(5)
                ->get(['id', 'title', 'slug', 'status', 'category', 'user_id', 'created_at']),
        ]);
    }
}
