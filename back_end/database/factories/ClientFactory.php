<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            "name" => $this->faker->company,
            "contact_person" => $this->faker->name,
            "phone" => $this->faker->phoneNumber,
            "address" => $this->faker->address,
            "created_by" => 1, // Assuming user with ID 1 exists
            "updated_by" => 1, // Assuming user with ID 1 exists
        ];
    }
}
