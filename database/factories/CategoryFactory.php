<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition()
    {
        return [
            'id' => $this->faker->numberBetween(1, 10),
            'name' => $this->faker->word,
        ];
    }
}
