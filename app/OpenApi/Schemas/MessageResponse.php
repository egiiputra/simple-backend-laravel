<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MessageResponse',
    title: 'Message response',
    description: 'A simple message payload.',
    required: ['message'],
)]
final class MessageResponse
{
    #[OA\Property(property: 'message', type: 'string', example: 'Courier deleted successfully.')]
    public string $message;
}
