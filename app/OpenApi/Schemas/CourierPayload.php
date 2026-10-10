<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CourierPayload',
    title: 'Courier payload',
    description: 'Payload used to create or update a courier. Required fields apply to create; on update (PATCH) every field is optional and only the provided attributes are changed.',
    required: ['name', 'email', 'phone', 'level', 'vehicle_type', 'registered_at'],
)]
final class CourierPayload
{
    #[OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Budi Agung')]
    public string $name;

    #[OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255, example: 'budi.agung@example.com')]
    public string $email;

    #[OA\Property(property: 'phone', type: 'string', maxLength: 20, example: '081234567890')]
    public string $phone;

    #[OA\Property(property: 'level', type: 'integer', enum: [1, 2, 3, 4, 5], example: 3)]
    public int $level;

    #[OA\Property(
        property: 'vehicle_type',
        type: 'string',
        enum: ['motorcycle', 'car', 'van', 'truck', 'bicycle'],
        example: 'motorcycle',
    )]
    public string $vehicleType;

    #[OA\Property(property: 'vehicle_plate', type: 'string', maxLength: 20, nullable: true, example: 'B 1234 XYZ')]
    public ?string $vehiclePlate;

    #[OA\Property(property: 'address', type: 'string', maxLength: 1000, nullable: true, example: 'Jl. Merdeka No. 1')]
    public ?string $address;

    #[OA\Property(property: 'city', type: 'string', maxLength: 100, nullable: true, example: 'Jakarta')]
    public ?string $city;

    #[OA\Property(property: 'is_active', type: 'boolean', default: true, example: true)]
    public bool $isActive;

    #[OA\Property(property: 'registered_at', type: 'string', format: 'date-time', example: '2025-02-01 08:00:00')]
    public string $registeredAt;
}
