<?php

namespace Tests\Unit\Enums;

use App\Enums\CourierLevel;
use PHPUnit\Framework\TestCase;

class CourierLevelTest extends TestCase
{
    public function test_exposes_every_level_value(): void
    {
        $this->assertSame([1, 2, 3, 4, 5], CourierLevel::values());
    }

    public function test_defines_five_levels(): void
    {
        $this->assertCount(5, CourierLevel::cases());
    }
}
