<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Tests\TestCase;

class CommentTest extends TestCase
{
    private function publishedArticle(): Article
    {
        return Article::factory()->published()->create();
    }

    // ── store ────────────────────────────────────────────────────────────────

    public function test_guest_cannot_post_comment(): void
    {
        $article = $this->publishedArticle();

        $response = $this->post(route('comments.store', $article), [
            'body' => 'This is a guest comment.',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_post_comment(): void
    {
        $user    = User::factory()->create();
        $article = $this->publishedArticle();

        $response = $this->actingAs($user)->post(route('comments.store', $article), [
            'body' => 'This is a valid comment body.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'user_id'    => $user->id,
            'article_id' => $article->id,
            'body'       => 'This is a valid comment body.',
        ]);
    }

    public function test_comment_body_shorter_than_3_chars_fails_validation(): void
    {
        $user    = User::factory()->create();
        $article = $this->publishedArticle();

        $response = $this->actingAs($user)->post(route('comments.store', $article), [
            'body' => 'Hi',
        ]);

        $response->assertSessionHasErrors('body');
        $this->assertDatabaseCount('comments', 0);
    }

    // ── destroy ──────────────────────────────────────────────────────────────

    public function test_comment_author_can_delete_own_comment(): void
    {
        $user    = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('comments.destroy', $comment));

        $response->assertRedirect();
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_moderator_can_delete_others_comment(): void
    {
        $moderator = User::factory()->moderator()->create();
        $comment   = Comment::factory()->create();

        $response = $this->actingAs($moderator)->delete(route('comments.destroy', $comment));

        $response->assertRedirect();
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_regular_user_cannot_delete_others_comment(): void
    {
        $user    = User::factory()->create();
        $comment = Comment::factory()->create();

        $response = $this->actingAs($user)->delete(route('comments.destroy', $comment));

        $response->assertForbidden();
        $this->assertDatabaseHas('comments', ['id' => $comment->id]);
    }
}
