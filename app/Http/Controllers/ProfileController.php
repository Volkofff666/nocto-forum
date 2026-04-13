<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function show(string $username): Response
    {
        $user = User::where('username', $username)->firstOrFail();

        $articles = $user->articles()
            ->published()
            ->with('user')
            ->withCount('comments')
            ->latest()
            ->paginate(10);

        // Replaced 3 separate queries with 2 — combines article aggregates into one DB round-trip
        $articleStats = DB::table('articles')
            ->where('user_id', $user->id)
            ->selectRaw('COALESCE(SUM(votes_count), 0) as total_votes, COALESCE(SUM(views_count), 0) as total_views')
            ->first();

        $totalVotes    = (int) $articleStats->total_votes;
        $totalViews    = (int) $articleStats->total_views;
        $totalComments = (int) $user->comments()->count();

        return Inertia::render('Profile/Show', [
            'profileUser'   => $user,
            'articles'      => $articles,
            'totalVotes'    => $totalVotes,
            'totalViews'    => $totalViews,
            'totalComments' => $totalComments,
        ]);
    }
}
