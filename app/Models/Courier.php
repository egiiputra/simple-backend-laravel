<?php

namespace App\Models;

use App\Enums\CourierLevel;
use App\Enums\VehicleType;
use Database\Factories\CourierFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'email',
    'phone',
    'level',
    'vehicle_type',
    'vehicle_plate',
    'address',
    'city',
    'is_active',
    'registered_at',
])]
class Courier extends Model
{
    /** @use HasFactory<CourierFactory> */
    use HasFactory;

    /**
     * The model's default attribute values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_active' => true,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'level' => CourierLevel::class,
            'vehicle_type' => VehicleType::class,
            'is_active' => 'boolean',
            'registered_at' => 'datetime',
        ];
    }

    /**
     * Filter couriers whose name contains every word of the given term.
     */
    #[Scope]
    protected function search(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        foreach (preg_split('/\s+/', $term) as $word) {
            $query->where('name', 'like', '%'.addcslashes($word, '\\%_').'%');
        }

        return $query;
    }

    /**
     * Filter couriers to the given levels.
     *
     * @param  array<int, int|string>  $levels
     */
    #[Scope]
    protected function ofLevel(Builder $query, array $levels): Builder
    {
        return $query->whereIn('level', array_map('intval', $levels));
    }
}
