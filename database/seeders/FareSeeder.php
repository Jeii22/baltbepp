<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fare;

class FareSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['passenger_type' => 'Regular', 'price' => 900.00],
            ['passenger_type' => 'Student', 'price' => 765.00],
            ['passenger_type' => 'Senior Citizen / PWD', 'price' => 720.00],
            ['passenger_type' => 'Child (2-11)', 'price' => 450.00],
            ['passenger_type' => 'Infant', 'price' => 0.00],
        ];

        foreach ($rows as $row) {
            Fare::updateOrCreate(
                ['passenger_type' => $row['passenger_type']],
                ['price' => $row['price'], 'active' => true]
            );
        }
    }
}