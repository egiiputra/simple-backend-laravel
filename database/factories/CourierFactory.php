<?php

namespace Database\Factories;

use App\Enums\CourierLevel;
use App\Enums\VehicleType;
use App\Models\Courier;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Courier>
 */
class CourierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->numerify('08##########'),
            'level' => fake()->randomElement(CourierLevel::cases()),
            'vehicle_type' => fake()->randomElement(VehicleType::cases()),
            'vehicle_plate' => fake()->unique()->bothify('? #### ??'),
            'address' => fake()->address(),
            'city' => fake()->city(),
            'is_active' => true,
            'registered_at' => fake()->dateTimeBetween('-2 years'),
        ];
    }

    /**
     * Indicate that the courier is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate the courier's level.
     */
    public function ofLevel(CourierLevel $level): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => $level,
        ]);
    }

    /**
     * Indicate the courier's registration date.
     */
    public function registeredAt(DateTimeInterface|string $date): static
    {
        return $this->state(fn (array $attributes) => [
            'registered_at' => $date,
        ]);
    }
}
