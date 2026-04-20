<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Tests\TestCase;

class VoteTest extends TestCase
{
    // ── guest ─────────────────────────────────────────────────────────────────

    public function test_guest_cannot_vote(): void
    {
        $article = Article::factory()->published()->create();

        $response = $this->post(route('votes.store', $article), ['type' => 'up']);

        $response->assertRedirect(route('login'));
    }

    // ── upvote ────────────────────────────────────────────────────────────────

    public function test_user_can_cast_upvote_and_votes_count_updates(): void
    {
        $user    = User::factory()->create();
        $article = Article::factory()->published()->create(['votes_count' => 0]);

        $this->actingAs($user)->post(route('votes.store', $article), ['type' => 'up']);

        $this->assertDatabaseHas('votes', [
            'user_id'    => $user->id,
            'article_id' => $article->id,
            'type'       => 'up',
        ]);

        $article->refresh();
        $this->assertEquals(1, $article->votes_count);
    }

    // ── toggle (same type) ────────────────────────────────────────────────────

    public function test_repeated_upvote_removes_vote(): void
    {
        $user    = User::factory()->create();
        $article = Article::factory()->published()->create(['votes_count' => 0]);

        // First upvote
        $this->actingAs($user)->post(route('votes.store', $article), ['type' => 'up']);
        // Second upvote — should toggle off
        $this->actingAs($user)->post(route('votes.store', $article), ['type' => 'up']);

        $this->assertDatabaseMissing('votes', [
            'user_id'    => $user->id,
            'article_id' => $article->id,
        ]);

        $article->refresh();
        $this->assertEquals(0, $article->votes_count);
    }

    // ── change vote type ──────────────────────────────────────────────────────

    public function test_switching_upvote_to_downvote_changes_votes_count(): void
    {
        $user    = User::factory()->create();
        $article = Article::factory()->published()->create(['votes_count' => 0]);

        // Cast an upvote (+1)
        $this->actingAs($user)->post(route('votes.store', $article), ['type' => 'up']);
        $article->refresh();
        $this->assertEquals(1, $article->votes_count);

        // Change to downvote (should now be -1)
        $this->actingAs($user)->post(route('votes.store', $article), ['type' => 'down']);
        $article->refresh();
        $this->assertEquals(-1, $article->votes_count);

        $this->assertDatabaseHas('votes', [
            'user_id'    => $user->id,
            'article_id' => $article->id,
            'type'       => 'down',
        ]);
    }
}
