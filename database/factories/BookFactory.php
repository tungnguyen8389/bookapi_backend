<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'slug' => $this->faker->unique()->slug,
            'price' => $this->faker->randomFloat(2, 5, 200),
            'discount' => $this->faker->optional()->randomFloat(2, 0, 50),
            'stock' => $this->faker->numberBetween(0, 100),
            'status' => $this->faker->randomElement(['available', 'out_of_stock']),
            'description' => $this->faker->paragraph,
            'image_url' => $this->faker->imageUrl(200, 300, 'books', true),
            'category_id' => Category::factory(),
            'author_id' => Author::factory(),
            'published_year' => $this->faker->year,
            'publisher' => $this->faker->company,
        ];
    }
}
