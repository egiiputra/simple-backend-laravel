<?php

namespace Tests\Unit\Enums;

use App\Enums\VehicleType;
use PHPUnit\Framework\TestCase;

class VehicleTypeTest extends TestCase
{
    public function test_exposes_every_vehicle_type_value(): void
    {
        $this->assertSame(
            ['motorcycle', 'car', 'van', 'truck', 'bicycle'],
            VehicleType::values(),
        );
    }
}
