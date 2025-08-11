<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    public function run()
    {
        Bank::create([
            'name' => 'Commercial Bank of Ethiopia',
            'created_by' => 10,
            'updated_by' => 10
        ]);

        // Add more banks if needed
        Bank::factory()->count(5)->create();
    }
}