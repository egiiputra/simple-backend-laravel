<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Courier',
    title: 'Courier',
    description: 'A single courier master data record.',
    required: [
        'id', 'name', 'email', 'phone', 'level', 'vehicle_type',
        'is_active', 'registered_at', 'created_at', 'updated_at',
    ],
)]
final class Courier
{
    #[OA\Property(property: 'id', type: 'integer', example: 1)]
    public int $id;

    #[OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Budiono Hadi Agung')]
    public string $name;

    #[OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255, example: 'budiono.agung@example.com')]
    public string $email;

    #[OA\Property(property: 'phone', type: 'string', maxLength: 20, example: '081200000001')]
    public string $phone;

    #[OA\Property(property: 'level', type: 'integer', enum: [1, 2, 3, 4, 5], example: 2)]
    public int $level;

    #[OA\Property(
        property: 'vehicle_type',
        type: 'string',
        enum: ['motorcycle', 'car', 'van', 'truck', 'bicycle'],
        example: 'motorcycle',
    )]
    public string $vehicleType;

    #[OA\Property(property: 'vehicle_plate', type: 'string', maxLength: 20, nullable: true, example: 'B 1001 AAA')]
    public ?string $vehiclePlate;

    #[OA\Property(property: 'address', type: 'string', nullable: true, example: 'Jl. Merdeka No. 1')]
    public ?string $address;

    #[OA\Property(property: 'city', type: 'string', maxLength: 100, nullable: true, example: 'Jakarta')]
    public ?string $city;

    #[OA\Property(property: 'is_active', type: 'boolean', example: true)]
    public bool $isActive;

    #[OA\Property(property: 'registered_at', type: 'string', format: 'date-time', example: '2025-02-01T08:00:00.000000Z')]
    public string $registeredAt;

    #[OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2025-02-01T08:00:00.000000Z')]
    public string $createdAt;

    #[OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2025-02-01T08:00:00.000000Z')]
    public string $updatedAt;
}
