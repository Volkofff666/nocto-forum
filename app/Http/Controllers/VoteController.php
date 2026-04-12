<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function store(Request $request, Article $article)
    {
        $validated = $request->validate([
            'type' => 'required|in:up,down',
        ]);

        $type = $validated['type'];
        $userId = $request->user()->id;

        $existingVote = $article->votes()->where('user_id', $userId)->first();

        if ($existingVote) {
            if ($existingVote->type === $type) {
                $existingVote->delete();
            } else {
                $existingVote->update(['type' => $type]);
            }
        } else {
            $article->votes()->create([
                'user_id' => $userId,
                'type'    => $type,
            ]);
        }

        $votesCount = $article->votes()->where('type', 'up')->count()
            - $article->votes()->where('type', 'down')->count();

        $article->update(['votes_count' => $votesCount]);

        return back();
    }
}
