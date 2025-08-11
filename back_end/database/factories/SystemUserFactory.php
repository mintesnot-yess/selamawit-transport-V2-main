<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SystemUser>
 */
class SystemUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $num = $this->faker->numberBetween(1, 10);
        return [
            'id' => $num,
            'user_name' => $this->faker->userName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'), // or Hash::make('password')
            'created_by' => $num,
            'updated_by' => $num,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
