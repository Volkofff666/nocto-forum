<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Tests\TestCase;

class AdminTest extends TestCase
{
    // ── access control ────────────────────────────────────────────────────────

    public function test_regular_user_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create(); // role = 'user'

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_moderator_can_access_admin_panel(): void
    {
        $moderator = User::factory()->moderator()->create();

        $response = $this->actingAs($moderator)->get(route('admin.dashboard'));

        $response->assertOk();
    }

    // ── role management ───────────────────────────────────────────────────────

    public function test_only_admin_can_change_user_role(): void
    {
        $moderator = User::factory()->moderator()->create();
        $target    = User::factory()->create();

        // Moderator does not have admin-level access to role changes
        $response = $this->actingAs($moderator)->patch(
            route('admin.users.role', $target),
            ['role' => 'moderator']
        );

        $response->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $target->id, 'role' => 'user']);
    }

    public function test_admin_can_change_user_role(): void
    {
        $admin  = User::factory()->admin()->create();
        $target = User::factory()->create();

        $response = $this->actingAs($admin)->patch(
            route('admin.users.role', $target),
            ['role' => 'moderator']
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $target->id, 'role' => 'moderator']);
    }

    // ── ban ───────────────────────────────────────────────────────────────────

    public function test_admin_can_ban_user(): void
    {
        $admin  = User::factory()->admin()->create();
        $target = User::factory()->create();

        $response = $this->actingAs($admin)->post(
            route('admin.users.ban', $target),
            ['reason' => 'Spam']
        );

        $response->assertRedirect();
        $target->refresh();
        $this->assertNotNull($target->banned_at);
        $this->assertEquals('Spam', $target->ban_reason);
    }

    // ── article deletion ──────────────────────────────────────────────────────

    public function test_admin_can_delete_article(): void
    {
        $admin   = User::factory()->admin()->create();
        $article = Article::factory()->published()->create();

        $response = $this->actingAs($admin)->delete(
            route('admin.articles.destroy', $article)
        );

        $response->assertRedirect();
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }
}
