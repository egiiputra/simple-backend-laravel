<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Courier Service API',
    description: 'REST API for managing courier master data (create, read, update, delete).',
)]
#[OA\Server(
    url: '/api',
    description: 'API base path',
)]
#[OA\Tag(
    name: 'Couriers',
    description: 'Courier master data endpoints',
)]
final class OpenApiSpec {}
