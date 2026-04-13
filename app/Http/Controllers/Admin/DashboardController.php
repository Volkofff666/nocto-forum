<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Report;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $stats = [
            'users'          => User::count(),
            'banned'         => User::whereNotNull('banned_at')->count(),
            'published'      => Article::published()->count(),
            'drafts'         => Article::where('status', 'draft')->count(),
            'comments'       => Comment::count(),
            'votes'          => Vote::count(),
            'reports'        => Report::where('status', 'pending')->count(),
            'users_today'    => User::whereDate('created_at', today())->count(),
            'articles_today' => Article::whereDate('created_at', today())->count(),
        ];

        // Sparkline: registrations last 7 days
        $userChart = $this->dailyCounts('users', 7);
        // Sparkline: publications last 7 days
        $articleChart = $this->dailyCounts('articles', 7, "status = 'published'");

        return Inertia::render('Admin/Dashboard', [
            'stats'          => $stats,
            'userChart'      => $userChart,
            'articleChart'   => $articleChart,
            'recentUsers'    => User::latest()->limit(5)->get(['id', 'name', 'username', 'role', 'banned_at', 'created_at']),
            'recentArticles' => Article::with('user:id,name,username')
                ->latest()->limit(5)
                ->get(['id', 'title', 'slug', 'status', 'category', 'user_id', 'created_at']),
            'recentLogs'     => AdminLog::with('admin:id,name,username')->latest()->limit(8)->get(),
        ]);
    }

    private function dailyCounts(string $table, int $days, string $where = ''): array
    {
        $rows = DB::select("
            SELECT
                TO_CHAR(d.day, 'DD.MM') AS label,
                COUNT({$table}.id) AS count
            FROM generate_series(
                NOW()::date - INTERVAL '{$days} days',
                NOW()::date,
                '1 day'
            ) AS d(day)
            LEFT JOIN {$table}
                ON DATE({$table}.created_at) = d.day
                " . ($where ? "AND ({$where})" : '') . "
            GROUP BY d.day
            ORDER BY d.day
        ");

        return collect($rows)->map(fn($r) => [
            'label' => $r->label,
            'count' => (int) $r->count,
        ])->values()->all();
    }
}
