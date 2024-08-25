<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {

        return [
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'price' => $this->faker->numberBetween(10, 1000),
            'quantity' => $this->faker->numberBetween(10, 1000),
            'image' => $this->faker->imageUrl(640, 480, 'products', true),
            'brand_id' => $this->faker->numberBetween(1, 10),
            'category_id' => $this->faker->numberBetween(1, 10),
        ];
    }
}
