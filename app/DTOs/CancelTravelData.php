<?php

namespace App\DTOs;

class CancelTravelData
{
    public function __construct(
        public int $id,
        public int $userId,
    )
    {
    }
}
