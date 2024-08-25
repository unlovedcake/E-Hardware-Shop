<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

class BrandFactory extends Factory
{
    protected $model = Brand::class;

    public function definition()
    {
        return [
            'id' => $this->faker->numberBetween(1, 10),
            'name' => $this->faker->company,
        ];
    }
}
