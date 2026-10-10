<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PaginationMeta',
    title: 'Pagination meta',
    description: 'Pagination metadata for the result set.',
)]
final class PaginationMeta
{
    #[OA\Property(property: 'current_page', type: 'integer', example: 1)]
    public int $currentPage;

    #[OA\Property(property: 'from', type: 'integer', nullable: true, example: 1)]
    public ?int $from;

    #[OA\Property(property: 'last_page', type: 'integer', example: 2)]
    public int $lastPage;

    #[OA\Property(property: 'path', type: 'string', example: 'http://localhost/api/couriers')]
    public string $path;

    #[OA\Property(property: 'per_page', type: 'integer', example: 15)]
    public int $perPage;

    #[OA\Property(property: 'to', type: 'integer', nullable: true, example: 15)]
    public ?int $to;

    #[OA\Property(property: 'total', type: 'integer', example: 21)]
    public int $total;

    #[OA\Property(
        property: 'links',
        type: 'array',
        items: new OA\Items(
            type: 'object',
            properties: [
                new OA\Property(property: 'url', type: 'string', nullable: true),
                new OA\Property(property: 'label', type: 'string', example: '1'),
                new OA\Property(property: 'page', type: 'integer', nullable: true, example: 1),
                new OA\Property(property: 'active', type: 'boolean', example: true),
            ],
        ),
    )]
    public array $links;
}
