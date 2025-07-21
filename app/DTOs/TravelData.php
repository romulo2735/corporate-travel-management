<?php

namespace App\DTOs;

class TravelData
{
    public function __construct(
        public string $requesterName,
        public string $destination,
        public string $departureDate,
        public string $returnDate
    )
    {
    }
}
