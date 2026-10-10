<?php

namespace App\Enums;

enum VehicleType: string
{
    case Motorcycle = 'motorcycle';
    case Car = 'car';
    case Van = 'van';
    case Truck = 'truck';
    case Bicycle = 'bicycle';

    /**
     * Get every vehicle type value.
     *
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
