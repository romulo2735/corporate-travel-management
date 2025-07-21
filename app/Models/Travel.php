<?php

namespace App\Models;

use App\TravelStatusEnum;
use Database\Factories\TravelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Travel extends Model
{
    /** @use HasFactory<TravelFactory> */
    use HasFactory;

    protected $fillable = [
        'requester_name',
        'destination',
        'departure_date',
        'return_date',
        'status',
        'user_id',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
        'status' => TravelStatusEnum::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
