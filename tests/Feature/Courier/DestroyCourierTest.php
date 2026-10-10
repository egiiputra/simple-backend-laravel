<?php

namespace Tests\Feature\Courier;

use App\Models\Courier;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DestroyCourierTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_deletes_the_courier_and_returns_a_confirmation(): void
    {
        $courier = Courier::factory()->create();

        $this->deleteJson("/api/couriers/{$courier->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Courier deleted successfully.');

        $this->assertDatabaseMissing('couriers', ['id' => $courier->id]);
        $this->assertDatabaseCount('couriers', 0);
    }

    public function test_only_deletes_the_requested_courier(): void
    {
        $courier = Courier::factory()->create();
        $other = Courier::factory()->create();

        $this->deleteJson("/api/couriers/{$courier->id}")->assertOk();

        $this->assertDatabaseMissing('couriers', ['id' => $courier->id]);
        $this->assertDatabaseHas('couriers', ['id' => $other->id]);
    }

    public function test_returns_404_when_the_courier_does_not_exist(): void
    {
        $this->deleteJson('/api/couriers/999999')->assertNotFound();
    }
}
