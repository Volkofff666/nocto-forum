<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        $title = fake()->sentence(6);

        return [
            'user_id'     => User::factory(),
            'title'       => $title,
            'slug'        => Str::slug($title) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'excerpt'     => fake()->paragraph(2),
            'body'        => implode("\n\n", fake()->paragraphs(4)),
            'category'    => fake()->randomElement(['tech', 'security', 'guides', 'news', 'other']),
            'status'      => 'published',
            'votes_count' => 0,
            'views_count' => 0,
            'tags'        => [],
            'cover_url'   => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(['status' => 'draft']);
    }

    public function published(): static
    {
        return $this->state(['status' => 'published']);
    }
}
