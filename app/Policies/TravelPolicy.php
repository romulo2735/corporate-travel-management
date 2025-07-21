<?php

namespace App\Policies;

use App\Models\Travel;
use App\Models\User;

class TravelPolicy
{
    public function view(User $user, Travel $travel): bool
    {
        return $travel->user_id === $user->id;
    }

    public function update(User $user, Travel $travel): bool
    {
        return $travel->user_id === $user->id;
    }

    public function delete(User $user, Travel $travel): bool
    {
        return $travel->user_id === $user->id;
    }

    public function cancel(User $user, Travel $travel): bool
    {
        return $travel->user_id === $user->id;
    }
}
