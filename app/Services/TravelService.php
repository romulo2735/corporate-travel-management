<?php

namespace App\Services;

use App\DTOs\CancelTravelData;
use App\DTOs\TravelData;
use App\DTOs\UpdateTravelStatusData;
use App\Events\TravelStatusUpdated;
use App\Http\Resources\TravelResource;
use App\Models\Travel;
use App\Repositories\TravelRepository;
use App\TravelStatusEnum;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class TravelService
{
    public function __construct(protected TravelRepository $repository)
    {
    }

    public function handleList(?string $status, ?string $destination = null, ?string $fromDate = null, ?string $toDate = null): LengthAwarePaginator
    {
        return $this->repository->getFiltered($status, $destination, $fromDate, $toDate);
    }

    public function handleCreate(TravelData $data): Travel
    {
        return $this->repository->create($data);
    }

    public function handleUpdateStatus(UpdateTravelStatusData $data): Travel
    {
        $travel = $this->repository->findById($data->id);

        Gate::authorize('update', $travel);

        if (!$travel) {
            throw ValidationException::withMessages(['travel' => 'Travel not found']);
        }

        if ($travel->user_id === $data->user_id) {
            throw ValidationException::withMessages(['status' => 'You cannot change your own trip']);
        }

        $travel->update([
            'status' => TravelStatusEnum::from($data->newStatus)
        ]);

        event(new TravelStatusUpdated($travel));


        return $travel;
    }

    public function handleCancel(CancelTravelData $dto): ?Travel
    {
        $travel = $this->repository->findById($dto->id);

        if ($travel->user_id === Auth::id()) {
            throw new \Exception('You cannot cancel your own travel request.');
        }

        if ($travel->status !== TravelStatusEnum::APPROVED) {
            throw new \Exception('Only approved orders can be canceled.');
        }

        $travel->status = TravelStatusEnum::CANCELED;
        $travel->save();

        event(new TravelStatusUpdated($travel));

        return $travel;
    }
}
