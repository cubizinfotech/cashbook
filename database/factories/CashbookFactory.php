<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Cashbook;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cashbook>
 */
class CashbookFactory extends Factory
{
    protected $model = Cashbook::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'title' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['active', 'inactive', 'pending', 'suspended']),
            'created_by' => User::factory(),
        ];
    }
}

