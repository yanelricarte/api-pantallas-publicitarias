<?php

namespace Database\Factories;

use App\Models\Display;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Display>
 */
class DisplayFactory extends Factory
{
    protected $model = Display::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'price_per_day' => fake()->randomFloat(2, 100, 5000),
            'resolution_height' => 1080,
            'resolution_width' => 1920,
            'type' => fake()->randomElement(['indoor', 'outdoor']),
            'user_id' => User::factory(),
        ];
    }
}
