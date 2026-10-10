<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PaginationLinks',
    title: 'Pagination links',
    description: 'Navigation URLs for the paginated result set.',
)]
final class PaginationLinks
{
    #[OA\Property(property: 'first', type: 'string', nullable: true, example: 'http://localhost/api/couriers?page=1')]
    public ?string $first;

    #[OA\Property(property: 'last', type: 'string', nullable: true, example: 'http://localhost/api/couriers?page=2')]
    public ?string $last;

    #[OA\Property(property: 'prev', type: 'string', nullable: true, example: null)]
    public ?string $prev;

    #[OA\Property(property: 'next', type: 'string', nullable: true, example: 'http://localhost/api/couriers?page=2')]
    public ?string $next;
}
