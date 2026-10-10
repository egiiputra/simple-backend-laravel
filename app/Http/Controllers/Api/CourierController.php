<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Courier\IndexCourierRequest;
use App\Http\Requests\Courier\StoreCourierRequest;
use App\Http\Requests\Courier\UpdateCourierRequest;
use App\Http\Resources\CourierResource;
use App\Models\Courier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

class CourierController extends Controller
{
    /**
     * Display a paginated list of couriers.
     */
    #[OA\Get(
        path: '/couriers',
        operationId: 'couriers.index',
        summary: 'List couriers',
        description: 'Returns a paginated list of couriers. Results are ordered by name by default; use the query parameters to change the ordering, search by name, or filter by level.',
        tags: ['Couriers'],
        parameters: [
            new OA\QueryParameter(name: 'search', description: 'Match couriers whose name contains every word of the term.', schema: new OA\Schema(type: 'string'), example: 'budi agung'),
            new OA\QueryParameter(name: 'level', description: 'Comma-separated list of levels to include (1-5).', schema: new OA\Schema(type: 'string'), example: '2,3'),
            new OA\QueryParameter(name: 'sort', description: 'Column to order by.', schema: new OA\Schema(type: 'string', enum: ['name', 'registered_at', 'level', 'created_at'], default: 'name')),
            new OA\QueryParameter(name: 'direction', description: 'Sort direction.', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'], default: 'asc')),
            new OA\QueryParameter(name: 'per_page', description: 'Number of items per page (1-100).', schema: new OA\Schema(type: 'integer', default: 15, minimum: 1, maximum: 100)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated list of couriers.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Courier')),
                        new OA\Property(property: 'links', ref: '#/components/schemas/PaginationLinks'),
                        new OA\Property(property: 'meta', ref: '#/components/schemas/PaginationMeta'),
                    ],
                ),
            ),
            new OA\Response(response: 422, description: 'Invalid query parameters.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ],
    )]
    public function index(IndexCourierRequest $request): AnonymousResourceCollection
    {
        $filters = $request->validated();

        $couriers = Courier::query()
            ->search($filters['search'] ?? null)
            ->when(
                ! empty($filters['level']),
                fn (Builder $query): Builder => $query->ofLevel($filters['level']),
            )
            ->orderBy($filters['sort'] ?? 'name', $filters['direction'] ?? 'asc')
            ->orderBy('id')
            ->paginate($filters['per_page'] ?? 15)
            ->withQueryString();

        return CourierResource::collection($couriers);
    }

    /**
     * Store a newly created courier.
     */
    #[OA\Post(
        path: '/couriers',
        operationId: 'couriers.store',
        summary: 'Create a courier',
        tags: ['Couriers'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CourierPayload'),
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Courier created.',
                content: new OA\JsonContent(properties: [new OA\Property(property: 'data', ref: '#/components/schemas/Courier')]),
            ),
            new OA\Response(response: 422, description: 'Validation failed.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ],
    )]
    public function store(StoreCourierRequest $request): JsonResponse
    {
        $courier = Courier::create($request->validated());

        return (new CourierResource($courier))
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }

    /**
     * Display the specified courier.
     */
    #[OA\Get(
        path: '/couriers/{courier}',
        operationId: 'couriers.show',
        summary: 'Get a courier',
        tags: ['Couriers'],
        parameters: [
            new OA\PathParameter(name: 'courier', description: 'Courier id.', required: true, schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'The courier.',
                content: new OA\JsonContent(properties: [new OA\Property(property: 'data', ref: '#/components/schemas/Courier')]),
            ),
            new OA\Response(response: 404, description: 'Courier not found.', content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse')),
        ],
    )]
    public function show(Courier $courier): CourierResource
    {
        return new CourierResource($courier);
    }

    /**
     * Update the specified courier.
     */
    #[OA\Patch(
        path: '/couriers/{courier}',
        operationId: 'couriers.update',
        summary: 'Update a courier',
        description: 'Updates a courier. Every field is optional; only the provided attributes are changed.',
        tags: ['Couriers'],
        parameters: [
            new OA\PathParameter(name: 'courier', description: 'Courier id.', required: true, schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CourierPayload'),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Courier updated.',
                content: new OA\JsonContent(properties: [new OA\Property(property: 'data', ref: '#/components/schemas/Courier')]),
            ),
            new OA\Response(response: 404, description: 'Courier not found.', content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse')),
            new OA\Response(response: 422, description: 'Validation failed.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ],
    )]
    #[OA\Put(
        path: '/couriers/{courier}',
        operationId: 'couriers.replace',
        summary: 'Replace a courier',
        tags: ['Couriers'],
        parameters: [
            new OA\PathParameter(name: 'courier', description: 'Courier id.', required: true, schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CourierPayload'),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Courier updated.',
                content: new OA\JsonContent(properties: [new OA\Property(property: 'data', ref: '#/components/schemas/Courier')]),
            ),
            new OA\Response(response: 404, description: 'Courier not found.', content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse')),
            new OA\Response(response: 422, description: 'Validation failed.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ],
    )]
    public function update(UpdateCourierRequest $request, Courier $courier): CourierResource
    {
        $courier->update($request->validated());

        return new CourierResource($courier);
    }

    /**
     * Remove the specified courier.
     */
    #[OA\Delete(
        path: '/couriers/{courier}',
        operationId: 'couriers.destroy',
        summary: 'Delete a courier',
        tags: ['Couriers'],
        parameters: [
            new OA\PathParameter(name: 'courier', description: 'Courier id.', required: true, schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Courier deleted.', content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse')),
            new OA\Response(response: 404, description: 'Courier not found.', content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse')),
        ],
    )]
    public function destroy(Courier $courier): JsonResponse
    {
        $courier->delete();

        return response()->json(['message' => 'Courier deleted successfully.']);
    }
}
