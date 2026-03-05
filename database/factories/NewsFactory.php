<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories.Factory<\App\Models\News>
 */
class NewsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(6);

        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(5),
            'summary' => $this->faker->paragraph(2),
            'content' => $this->faker->paragraphs(5, true),
            'thumbnail' => $this->faker->imageUrl(800, 600, 'news', true),
            'category_id' => null,
            'author_id' => null,
            'status' => $this->faker->randomElement([0, 1]),
            'published_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            'views' => $this->faker->numberBetween(0, 1000),
        ];
    }
}

