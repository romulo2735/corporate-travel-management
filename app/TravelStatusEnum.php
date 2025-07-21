<?php

namespace App;

enum TravelStatusEnum: string
{
    case REQUESTED = 'requested';
    case APPROVED = 'approved';
    case CANCELED = 'canceled';
}
