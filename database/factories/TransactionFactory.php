<?php

namespace Database\Factories;

use App\Models\Cashbook;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cashbook_id' => Cashbook::factory(),
            'party_name' => fake()->name(),
            'amount' => fake()->randomFloat(2, 10, 10000),
            'type' => fake()->randomElement(['in', 'out']),
            'transaction_datetime' => fake()->dateTime(),
            'description' => fake()->sentence(),
            'remark' => fake()->sentence(),
            'status' => fake()->randomElement(['active', 'inactive', 'pending', 'suspended']),
            'created_by' => User::factory(),
        ];
    }
}

