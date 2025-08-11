<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BankAccount>
 */
class BankAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "account_number" => fake()->bankAccountNumber(),
            "bank_id" => 70, // Assuming you have a Bank factory
            "created_by" => 10,
            "updated_by" => 10,
        ];
    }
}
