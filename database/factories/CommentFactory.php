<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'user_id'    => User::factory(),
            'article_id' => Article::factory(),
            'parent_id'  => null,
            'body'       => fake()->paragraph(),
        ];
    }

    public function reply(Comment $parent): static
    {
        return $this->state([
            'parent_id'  => $parent->id,
            'article_id' => $parent->article_id,
        ]);
    }
}
