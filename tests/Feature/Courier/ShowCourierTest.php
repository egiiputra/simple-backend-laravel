<?php

namespace Tests\Feature\Courier;

use App\Enums\CourierLevel;
use App\Enums\VehicleType;
use App\Models\Courier;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ShowCourierTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_returns_every_attribute_of_the_courier(): void
    {
        $courier = Courier::factory()->ofLevel(CourierLevel::Three)->create([
            'name' => 'Budi Agung',
            'email' => 'budi@example.com',
            'phone' => '081234567890',
            'vehicle_type' => VehicleType::Motorcycle,
            'vehicle_plate' => 'B 1234 XYZ',
            'address' => 'Jl. Merdeka No. 1',
            'city' => 'Jakarta',
            'is_active' => true,
            'registered_at' => '2025-02-01 08:00:00',
        ]);

        $this->getJson("/api/couriers/{$courier->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $courier->id)
            ->assertJsonPath('data.name', 'Budi Agung')
            ->assertJsonPath('data.email', 'budi@example.com')
            ->assertJsonPath('data.phone', '081234567890')
            ->assertJsonPath('data.level', 3)
            ->assertJsonPath('data.vehicle_type', 'motorcycle')
            ->assertJsonPath('data.vehicle_plate', 'B 1234 XYZ')
            ->assertJsonPath('data.address', 'Jl. Merdeka No. 1')
            ->assertJsonPath('data.city', 'Jakarta')
            ->assertJsonPath('data.is_active', true)
            ->assertJsonStructure([
                'data' => [
                    'id', 'name', 'email', 'phone', 'level', 'vehicle_type',
                    'vehicle_plate', 'address', 'city', 'is_active',
                    'registered_at', 'created_at', 'updated_at',
                ],
            ]);
    }

    public function test_returns_404_when_the_courier_does_not_exist(): void
    {
        $this->getJson('/api/couriers/999999')->assertNotFound();
    }
}
