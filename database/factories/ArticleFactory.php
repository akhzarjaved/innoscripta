<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'author_id' => Author::factory(),
            'source_id' => Source::factory(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'url' => $this->faker->url(),
            'published_at' => $this->faker->dateTimeBetween('-7 day', 'now'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
