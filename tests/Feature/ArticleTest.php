<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    // ── store ────────────────────────────────────────────────────────────────

    public function test_guest_cannot_create_article(): void
    {
        $response = $this->post(route('articles.store'), [
            'title'    => 'Hello World',
            'excerpt'  => 'Short excerpt here.',
            'body'     => '<p>Body content.</p>',
            'category' => 'tech',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_article(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('articles.store'), [
            'title'    => 'My First Article',
            'excerpt'  => 'This is the excerpt.',
            'body'     => '<p>This is the body.</p>',
            'category' => 'tech',
            'publish'  => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('articles', [
            'user_id'  => $user->id,
            'title'    => 'My First Article',
            'status'   => 'published',
        ]);
    }

    public function test_store_validation_rejects_empty_title(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('articles.store'), [
            'title'    => '',
            'excerpt'  => 'Some excerpt.',
            'body'     => '<p>Body text.</p>',
            'category' => 'tech',
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_store_validation_rejects_oversized_body(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('articles.store'), [
            'title'    => 'Valid Title',
            'excerpt'  => 'Valid excerpt.',
            'body'     => str_repeat('a', 200001),
            'category' => 'tech',
        ]);

        $response->assertSessionHasErrors('body');
    }

    // ── update ───────────────────────────────────────────────────────────────

    public function test_author_can_edit_own_article(): void
    {
        $user    = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->patch(route('articles.update', $article), [
            'title'    => 'Updated Title',
            'excerpt'  => 'Updated excerpt.',
            'body'     => '<p>Updated body.</p>',
            'category' => 'security',
        ]);

        $response->assertRedirect(route('articles.show', $article->slug));
        $this->assertDatabaseHas('articles', [
            'id'    => $article->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_other_user_cannot_edit_article(): void
    {
        $owner   = User::factory()->create();
        $other   = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($other)->patch(route('articles.update', $article), [
            'title'    => 'Hijacked Title',
            'excerpt'  => 'Nope.',
            'body'     => '<p>Nope.</p>',
            'category' => 'tech',
        ]);

        $response->assertForbidden();
    }

    // ── destroy ──────────────────────────────────────────────────────────────

    public function test_author_can_delete_own_article(): void
    {
        $user    = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('articles.destroy', $article));

        $response->assertRedirect(route('articles.index'));
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    // ── show / index ─────────────────────────────────────────────────────────

    public function test_published_article_is_visible_in_listing(): void
    {
        $article = Article::factory()->published()->create(['title' => 'Unique Visible Article Title']);

        $response = $this->get(route('articles.index'));

        $response->assertOk();
        $response->assertSee('Unique Visible Article Title');
    }

    public function test_draft_article_is_forbidden_for_other_user(): void
    {
        $owner   = User::factory()->create();
        $other   = User::factory()->create();
        $article = Article::factory()->draft()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($other)->get(route('articles.show', $article->slug));

        $response->assertForbidden();
    }
}
