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

class CourierController extends Controller
{
    /**
     * Display a paginated list of couriers.
     */
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
    public function show(Courier $courier): CourierResource
    {
        return new CourierResource($courier);
    }

    /**
     * Update the specified courier.
     */
    public function update(UpdateCourierRequest $request, Courier $courier): CourierResource
    {
        $courier->update($request->validated());

        return new CourierResource($courier);
    }

    /**
     * Remove the specified courier.
     */
    public function destroy(Courier $courier): JsonResponse
    {
        $courier->delete();

        return response()->json(['message' => 'Courier deleted successfully.']);
    }
}
