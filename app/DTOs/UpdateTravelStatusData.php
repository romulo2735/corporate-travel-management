<?php

namespace App\DTOs;

class UpdateTravelStatusData
{
    public function __construct(
        public int    $id,
        public string $newStatus,
        public int $user_id
    )
    {
    }
}
