<?php

namespace Tests\Feature\Courier;

use App\Enums\CourierLevel;
use App\Enums\VehicleType;
use App\Models\Courier;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class StoreCourierTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_valid_payload_creates_the_courier_and_returns_201(): void
    {
        $response = $this->postJson('/api/couriers', $this->validPayload());

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Budi Agung')
            ->assertJsonPath('data.email', 'budi@example.com')
            ->assertJsonPath('data.level', 3)
            ->assertJsonPath('data.vehicle_type', 'motorcycle')
            ->assertJsonPath('data.vehicle_plate', 'B 1234 XYZ')
            ->assertJsonPath('data.is_active', true);

        $this->assertDatabaseHas('couriers', [
            'name' => 'Budi Agung',
            'email' => 'budi@example.com',
            'phone' => '081234567890',
            'level' => 3,
            'vehicle_type' => 'motorcycle',
            'vehicle_plate' => 'B 1234 XYZ',
            'is_active' => true,
        ]);
        $this->assertDatabaseCount('couriers', 1);
    }

    public function test_defaults_active_to_true_when_the_flag_is_omitted(): void
    {
        $payload = $this->validPayload();
        unset($payload['is_active']);

        $this->postJson('/api/couriers', $payload)
            ->assertCreated()
            ->assertJsonPath('data.is_active', true);

        $this->assertDatabaseHas('couriers', ['email' => 'budi@example.com', 'is_active' => true]);
    }

    public function test_rejects_an_empty_payload(): void
    {
        $this->postJson('/api/couriers', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'phone', 'level', 'vehicle_type', 'registered_at'])
            ->assertJsonPath('errors.name.0', 'The name field is required.');

        $this->assertDatabaseCount('couriers', 0);
    }

    #[TestWith([1])]
    #[TestWith([5])]
    public function test_accepts_the_boundary_level_values(int $level): void
    {
        $this->postJson('/api/couriers', $this->validPayload(['level' => $level]))
            ->assertCreated()
            ->assertJsonPath('data.level', $level);

        $this->assertDatabaseHas('couriers', ['level' => $level]);
    }

    public function test_accepts_a_level_sent_as_a_numeric_string(): void
    {
        $this->postJson('/api/couriers', $this->validPayload(['level' => '4']))
            ->assertCreated()
            ->assertJsonPath('data.level', 4);
    }

    #[TestWith([0])]
    #[TestWith([6])]
    #[TestWith(['abc'])]
    public function test_rejects_an_invalid_level(mixed $level): void
    {
        $this->postJson('/api/couriers', $this->validPayload(['level' => $level]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['level']);

        $this->assertDatabaseCount('couriers', 0);
    }

    #[TestWith(['spaceship'])]
    #[TestWith([123])]
    public function test_rejects_an_unknown_vehicle_type(mixed $vehicleType): void
    {
        $this->postJson('/api/couriers', $this->validPayload(['vehicle_type' => $vehicleType]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['vehicle_type']);

        $this->assertDatabaseCount('couriers', 0);
    }

    #[DataProvider('vehicleTypes')]
    public function test_accepts_every_known_vehicle_type(string $vehicleType): void
    {
        $this->postJson('/api/couriers', $this->validPayload(['vehicle_type' => $vehicleType]))
            ->assertCreated()
            ->assertJsonPath('data.vehicle_type', $vehicleType);
    }

    public function test_rejects_a_duplicate_email(): void
    {
        Courier::factory()->create(['email' => 'budi@example.com']);

        $this->postJson('/api/couriers', $this->validPayload(['email' => 'budi@example.com']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);

        $this->assertDatabaseCount('couriers', 1);
    }

    public function test_rejects_a_duplicate_phone(): void
    {
        Courier::factory()->create(['phone' => '081234567890']);

        $this->postJson('/api/couriers', $this->validPayload(['phone' => '081234567890']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['phone']);

        $this->assertDatabaseCount('couriers', 1);
    }

    public function test_rejects_a_duplicate_vehicle_plate(): void
    {
        Courier::factory()->create(['vehicle_plate' => 'B 1234 XYZ']);

        $this->postJson('/api/couriers', $this->validPayload(['vehicle_plate' => 'B 1234 XYZ']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['vehicle_plate']);

        $this->assertDatabaseCount('couriers', 1);
    }

    public function test_rejects_a_malformed_email(): void
    {
        $this->postJson('/api/couriers', $this->validPayload(['email' => 'not-an-email']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);

        $this->assertDatabaseCount('couriers', 0);
    }

    /**
     * Provide every known vehicle type keyed by its value.
     *
     * @return array<string, array{string}>
     */
    public static function vehicleTypes(): array
    {
        return collect(VehicleType::cases())
            ->mapWithKeys(fn (VehicleType $type): array => [$type->value => [$type->value]])
            ->all();
    }

    /**
     * Build a valid create payload.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Budi Agung',
            'email' => 'budi@example.com',
            'phone' => '081234567890',
            'level' => CourierLevel::Three->value,
            'vehicle_type' => VehicleType::Motorcycle->value,
            'vehicle_plate' => 'B 1234 XYZ',
            'address' => 'Jl. Merdeka No. 1',
            'city' => 'Jakarta',
            'is_active' => true,
            'registered_at' => '2025-02-01 08:00:00',
        ], $overrides);
    }
}
