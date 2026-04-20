<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    // ── registration ─────────────────────────────────────────────────────────

    public function test_valid_registration_creates_user(): void
    {
        $response = $this->post(route('register'), [
            'name'                  => 'Test User',
            'username'              => 'testuser',
            'email'                 => 'test@example.com',
            'password'              => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('users', [
            'email'    => 'test@example.com',
            'username' => 'testuser',
        ]);
    }

    // ── login ─────────────────────────────────────────────────────────────────

    public function test_correct_credentials_authenticate_user(): void
    {
        $user = User::factory()->create([
            'email'    => 'login@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->post(route('login'), [
            'email'    => 'login@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_wrong_password_returns_validation_error(): void
    {
        User::factory()->create([
            'email'    => 'wrong@example.com',
            'password' => bcrypt('correctpass'),
        ]);

        $response = $this->post(route('login'), [
            'email'    => 'wrong@example.com',
            'password' => 'wrongpass',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    // ── banned user ───────────────────────────────────────────────────────────

    public function test_banned_user_is_logged_out_on_next_request(): void
    {
        $user = User::factory()->create();

        $user->update(['banned_at' => now(), 'ban_reason' => 'Spam']);

        // GET: regular page load — should redirect to login
        $response = $this->actingAs($user)->get(route('articles.index'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_banned_user_receives_409_on_inertia_post(): void
    {
        $user = User::factory()->create();
        $user->update(['banned_at' => now(), 'ban_reason' => 'Spam']);

        // Inertia POST (XHR with X-Inertia header) — should get 409 JSON, not a silent redirect
        $response = $this->actingAs($user)
            ->withHeaders(['X-Inertia' => 'true'])
            ->post(route('articles.store'), [
                'title'    => 'Hello',
                'excerpt'  => 'World',
                'body'     => '<p>test</p>',
                'category' => 'tech',
            ]);

        $response->assertStatus(409);
        $response->assertJson(['message' => fn($msg) => str_contains($msg, 'заблокирован')]);
    }
}
