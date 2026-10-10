<?php

namespace Tests\Feature\Courier;

use App\Enums\VehicleType;
use App\Models\Courier;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class UpdateCourierTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_valid_payload_updates_the_courier_and_persists_the_changes(): void
    {
        $courier = Courier::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $response = $this->putJson("/api/couriers/{$courier->id}", [
            'name' => 'New Name',
            'email' => 'new@example.com',
            'level' => 5,
            'vehicle_type' => VehicleType::Truck->value,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'New Name')
            ->assertJsonPath('data.email', 'new@example.com')
            ->assertJsonPath('data.level', 5)
            ->assertJsonPath('data.vehicle_type', 'truck');

        $this->assertDatabaseHas('couriers', [
            'id' => $courier->id,
            'name' => 'New Name',
            'email' => 'new@example.com',
            'level' => 5,
            'vehicle_type' => 'truck',
        ]);
    }

    public function test_keeps_the_unique_attributes_that_are_unchanged(): void
    {
        $courier = Courier::factory()->create([
            'email' => 'budi@example.com',
            'phone' => '081234567890',
        ]);

        $this->putJson("/api/couriers/{$courier->id}", ['name' => 'Budi Agung'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Budi Agung');

        $this->assertDatabaseHas('couriers', [
            'id' => $courier->id,
            'name' => 'Budi Agung',
            'email' => 'budi@example.com',
            'phone' => '081234567890',
        ]);
    }

    public function test_patch_updates_only_the_provided_attributes(): void
    {
        $courier = Courier::factory()->create(['name' => 'Budi Agung', 'city' => 'Bandung']);

        $this->patchJson("/api/couriers/{$courier->id}", ['city' => 'Surabaya'])
            ->assertOk()
            ->assertJsonPath('data.city', 'Surabaya')
            ->assertJsonPath('data.name', 'Budi Agung');

        $this->assertDatabaseHas('couriers', [
            'id' => $courier->id,
            'name' => 'Budi Agung',
            'city' => 'Surabaya',
        ]);
    }

    public function test_rejects_an_email_already_used_by_another_courier(): void
    {
        Courier::factory()->create(['email' => 'taken@example.com']);
        $courier = Courier::factory()->create(['email' => 'mine@example.com']);

        $this->putJson("/api/couriers/{$courier->id}", ['email' => 'taken@example.com'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);

        $this->assertDatabaseHas('couriers', ['id' => $courier->id, 'email' => 'mine@example.com']);
    }

    #[TestWith([0])]
    #[TestWith([6])]
    #[TestWith(['abc'])]
    public function test_rejects_an_invalid_level(mixed $level): void
    {
        $courier = Courier::factory()->create(['level' => 2]);

        $this->putJson("/api/couriers/{$courier->id}", ['level' => $level])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['level']);

        $this->assertDatabaseHas('couriers', ['id' => $courier->id, 'level' => 2]);
    }

    public function test_returns_404_when_the_courier_does_not_exist(): void
    {
        $this->putJson('/api/couriers/999999', ['name' => 'New Name'])->assertNotFound();
    }
}
