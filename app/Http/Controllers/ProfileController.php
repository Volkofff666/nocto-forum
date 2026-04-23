<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function show(Request $request, string $username): Response
    {
        $user     = User::where('username', $username)->firstOrFail();
        $authUser = $request->user();

        $articles = $user->articles()
            ->published()
            ->with('user')
            ->withCount('comments')
            ->latest()
            ->paginate(10);

        // Combines article aggregates into one DB round-trip
        $articleStats = DB::table('articles')
            ->where('user_id', $user->id)
            ->selectRaw('COALESCE(SUM(votes_count), 0) as total_votes, COALESCE(SUM(views_count), 0) as total_views')
            ->first();

        $totalVotes    = (int) $articleStats->total_votes;
        $totalViews    = (int) $articleStats->total_views;
        $totalComments = (int) $user->comments()->count();

        $followersCount = $user->followers()->count();
        $followingCount = $user->following()->count();

        $isFollowing = $authUser && $authUser->id !== $user->id
            ? $authUser->following()->where('following_id', $user->id)->exists()
            : false;

        return Inertia::render('Profile/Show', [
            'profileUser'    => $user,
            'articles'       => $articles,
            'totalVotes'     => $totalVotes,
            'totalViews'     => $totalViews,
            'totalComments'  => $totalComments,
            'followersCount' => $followersCount,
            'followingCount' => $followingCount,
            'isFollowing'    => $isFollowing,
        ]);
    }
}
