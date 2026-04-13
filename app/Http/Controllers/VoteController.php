<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // Atomic update to avoid race condition when concurrent votes arrive.
        // Previously used two separate SELECT COUNT queries then UPDATE — a TOCTOU window.
        $article->update([
            'votes_count' => DB::raw(
                "(SELECT COALESCE(SUM(CASE WHEN type = 'up' THEN 1 ELSE -1 END), 0) FROM votes WHERE article_id = {$article->id})"
            ),
        ]);

        return back()->with('success', 'Голос учтён');
    }
}
