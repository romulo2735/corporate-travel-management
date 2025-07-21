<?php

namespace App\Http\Controllers;

use App\Http\Requests\CancelTravelRequest;
use App\Http\Requests\FilterRequest;
use App\Http\Requests\StoreTravelRequest;
use App\Http\Requests\UpdateTravelStatusRequest;
use App\Http\Resources\TravelResource;
use App\Models\Travel;
use App\Services\TravelService;
use App\TravelStatusEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class TravelController extends Controller
{
    public function __construct(protected TravelService $service)
    {
    }

    /**
     * @param FilterRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function index(FilterRequest $request): JsonResponse|AnonymousResourceCollection
    {
        $filters = $request->only(['status', 'destination', 'from_date', 'to_date']);

        $travels = $this->service->handleList(
            $filters['status'] ?? null,
            $filters['destination'] ?? null,
            $filters['from_date'] ?? null,
            $filters['to_date'] ?? null,
        );
        return TravelResource::collection($travels);
    }


    public function show(Travel $travel): TravelResource
    {
        Gate::authorize('view', $travel);

        return new TravelResource($travel);
    }

    /**
     * @param StoreTravelRequest $request
     * @return TravelResource
     */
    public function store(StoreTravelRequest $request): TravelResource
    {
        $travel = $this->service->handleCreate($request->toDto());

        return new TravelResource($travel);
    }


    /**
     * @param UpdateTravelStatusRequest $request
     * @return TravelResource
     * @throws ValidationException
     */
    public function updateStatus(UpdateTravelStatusRequest $request): TravelResource
    {
        $travel = $this->service->handleUpdateStatus($request->toDto());

        return new TravelResource($travel);
    }

    /**
     * @param CancelTravelRequest $request
     * @return JsonResponse
     */
    public function cancel(CancelTravelRequest $request): JsonResponse
    {
        try {
            $travel = $this->service->handleCancel($request->toDto());

            Gate::authorize('cancel', $travel);

            return response()->json([
                'message' => 'Travel request successfully canceled.',
                'data' => new TravelResource($travel)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
