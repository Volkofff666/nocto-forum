<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function show(string $username): Response
    {
        $user = User::where('username', $username)->firstOrFail();

        $articles = $user->articles()
            ->published()
            ->withCount('comments')
            ->latest()
            ->paginate(10);

        $totalVotes = $user->articles()->sum('votes_count');

        return Inertia::render('Profile/Show', [
            'profileUser'  => $user,
            'articles'     => $articles,
            'totalVotes'   => $totalVotes,
        ]);
    }
}
