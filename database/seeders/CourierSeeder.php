<?php

namespace Database\Seeders;

use App\Enums\CourierLevel;
use App\Enums\VehicleType;
use App\Models\Courier;
use Illuminate\Database\Seeder;

class CourierSeeder extends Seeder
{
    /**
     * Seed the couriers table.
     */
    public function run(): void
    {
        Courier::factory()->create([
            'name' => 'Budiono Hadi Agung',
            'email' => 'budiono.agung@example.com',
            'phone' => '081200000001',
            'level' => CourierLevel::Two,
            'vehicle_type' => VehicleType::Motorcycle,
            'vehicle_plate' => 'B 1001 AAA',
            'city' => 'Jakarta',
            'registered_at' => now()->subMonths(6),
        ]);

        Courier::factory()->count(20)->create();
    }
}
