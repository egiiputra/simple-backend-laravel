<?php

namespace Tests\Feature;

use Tests\TestCase;

class OpenApiTest extends TestCase
{
    public function test_swagger_ui_is_reachable(): void
    {
        $this->get('/api/documentation')
            ->assertOk()
            ->assertSee('Courier Service API');
    }

    public function test_openapi_spec_documents_every_courier_endpoint(): void
    {
        $this->getJson('/docs')
            ->assertOk()
            ->assertJsonPath('openapi', '3.0.0')
            ->assertJsonPath('info.title', 'Courier Service API')
            ->assertJsonPath('servers.0.url', '/api')
            ->assertJsonStructure([
                'paths' => [
                    '/couriers' => ['get', 'post'],
                    '/couriers/{courier}' => ['get', 'put', 'patch', 'delete'],
                ],
                'components' => [
                    'schemas' => [
                        'Courier',
                        'CourierPayload',
                        'PaginationLinks',
                        'PaginationMeta',
                        'ValidationError',
                        'MessageResponse',
                    ],
                ],
            ]);
    }
}
