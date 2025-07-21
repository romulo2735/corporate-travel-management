<?php

namespace App\Repositories;

use App\DTOs\TravelData;
use App\Models\Travel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TravelRepository
{
    public function getFiltered(?string $status, ?string $destination, ?string $fromDate, ?string $toDate): LengthAwarePaginator
    {
        return Travel::query()
            ->where('user_id', auth()->id())
            ->when($status, fn($query) => $query->where('status', $status)
            )
            ->when($destination, fn($query) => $query->where('destination', 'like', "%{$destination}%")
            )
            ->when($fromDate, fn($query) => $query->whereDate('departure_date', '>=', $fromDate)
            )
            ->when($toDate, fn($query) => $query->whereDate('return_date', '<=', $toDate)
            )
            ->orderByDesc('created_at')
            ->paginate(10);
    }

    public function create(TravelData $data): Travel
    {
        return Travel::query()->create([
            'requester_name' => $data->requesterName,
            'destination' => $data->destination,
            'departure_date' => $data->departureDate,
            'return_date' => $data->returnDate,
            'user_id' => auth()->user()->id
        ]);
    }

    public function findById(int $id): ?Travel
    {
        return Travel::query()->find($id);
    }
}
