<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "customer_id" => 1,
            "employee_id" => 2,
            "vehicle_id" => 1,
            "loading_place" => 1,
            "destination" => 1,
            "load_type_id" => 1,
            "quintal" => 1,
            "given_tariff" => 122,
            "sub_tariff" => 2112,
            "order_date" => now(),
            "arrival_at_loading_site" => "2025-05-21",
            "loading_date" => "2025-05-15",
            "current_condition" => "LOADED",
            "payment_collected" => 0,
            "status" => "PENDING",
            "created_by" => 1,
            "updated_by" => 1,
        ];
    }
}
