<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ValidationError',
    title: 'Validation error',
    description: 'Returned with a 422 response when the request fails validation.',
    required: ['message', 'errors'],
)]
final class ValidationError
{
    #[OA\Property(property: 'message', type: 'string', example: 'The name field is required. (and 2 more errors)')]
    public string $message;

    #[OA\Property(
        property: 'errors',
        type: 'object',
        additionalProperties: new OA\AdditionalProperties(
            type: 'array',
            items: new OA\Items(type: 'string'),
        ),
        example: ['name' => ['The name field is required.']],
    )]
    public array $errors;
}
