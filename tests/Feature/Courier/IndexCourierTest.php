<?php

namespace Tests\Feature\Courier;

use App\Enums\CourierLevel;
use App\Models\Courier;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class IndexCourierTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_returns_couriers_ordered_by_name_by_default(): void
    {
        Courier::factory()->create(['name' => 'Zulkifli Ramadhan']);
        Courier::factory()->create(['name' => 'Budi Agung']);
        Courier::factory()->create(['name' => 'Agus Salim']);

        $this->getJson('/api/couriers')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.0.name', 'Agus Salim')
            ->assertJsonPath('data.1.name', 'Budi Agung')
            ->assertJsonPath('data.2.name', 'Zulkifli Ramadhan')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'name', 'email', 'phone', 'level', 'vehicle_type',
                        'vehicle_plate', 'address', 'city', 'is_active',
                        'registered_at', 'created_at', 'updated_at',
                    ],
                ],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => ['current_page', 'per_page', 'total', 'last_page'],
            ]);
    }

    public function test_paginates_results_with_the_reported_meta(): void
    {
        Courier::factory()->count(20)->create();

        $this->getJson('/api/couriers')
            ->assertOk()
            ->assertJsonCount(15, 'data')
            ->assertJsonPath('meta.total', 20)
            ->assertJsonPath('meta.per_page', 15)
            ->assertJsonPath('meta.last_page', 2);
    }

    public function test_respects_the_per_page_parameter(): void
    {
        Courier::factory()->count(10)->create();

        $this->getJson('/api/couriers?per_page=5')
            ->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.per_page', 5);
    }

    public function test_orders_by_registration_date_when_requested(): void
    {
        $oldest = Courier::factory()->registeredAt(now()->subYear())->create();
        $middle = Courier::factory()->registeredAt(now()->subMonth())->create();
        $newest = Courier::factory()->registeredAt(now()->subDay())->create();

        $this->getJson('/api/couriers?sort=registered_at')
            ->assertOk()
            ->assertJsonPath('data.0.id', $oldest->id)
            ->assertJsonPath('data.1.id', $middle->id)
            ->assertJsonPath('data.2.id', $newest->id);
    }

    public function test_orders_descending_when_the_direction_is_desc(): void
    {
        $older = Courier::factory()->registeredAt('2024-01-01 00:00:00')->create();
        $newer = Courier::factory()->registeredAt('2025-01-01 00:00:00')->create();

        $this->getJson('/api/couriers?sort=registered_at&direction=desc')
            ->assertOk()
            ->assertJsonPath('data.0.id', $newer->id)
            ->assertJsonPath('data.1.id', $older->id);
    }

    public function test_search_matches_a_name_across_multiple_words(): void
    {
        $match = Courier::factory()->create(['name' => 'Budiono Hadi Agung']);
        Courier::factory()->create(['name' => 'Siti Aminah']);

        $this->getJson('/api/couriers?search=budi+agung')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $match->id);
    }

    public function test_search_returns_no_results_when_nothing_matches(): void
    {
        Courier::factory()->create(['name' => 'Siti Aminah']);

        $this->getJson('/api/couriers?search=budi')
            ->assertOk()
            ->assertJsonCount(0, 'data')
            ->assertJsonPath('meta.total', 0);
    }

    public function test_filters_by_the_requested_levels(): void
    {
        $levelTwo = Courier::factory()->ofLevel(CourierLevel::Two)->create();
        $levelThree = Courier::factory()->ofLevel(CourierLevel::Three)->create();
        Courier::factory()->ofLevel(CourierLevel::One)->create();
        Courier::factory()->ofLevel(CourierLevel::Four)->create();

        $response = $this->getJson('/api/couriers?level=2,3');

        $response->assertOk()->assertJsonCount(2, 'data');

        $this->assertEqualsCanonicalizing(
            [$levelTwo->id, $levelThree->id],
            collect($response->json('data'))->pluck('id')->all(),
        );
    }

    public function test_rejects_an_unsupported_sort_column(): void
    {
        Courier::factory()->create();

        $this->getJson('/api/couriers?sort=password')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['sort']);
    }

    public function test_rejects_a_level_outside_the_allowed_range(): void
    {
        $this->getJson('/api/couriers?level=6')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['level.0']);
    }

    public function test_rejects_an_unsupported_direction(): void
    {
        $this->getJson('/api/couriers?direction=sideways')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['direction']);
    }
}
