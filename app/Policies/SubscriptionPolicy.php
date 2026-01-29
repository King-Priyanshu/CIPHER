<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubscriptionPolicy
{
    use HandlesAuthorization;

    public function view(User $user, UserSubscription $subscription)
    {
        // User can view their own subscription, Admin can view all
        return $user->id === $subscription->user_id || $user->hasRole('admin');
    }

    public function update(User $user, UserSubscription $subscription)
    {
        // Only Admin can manually update status, or user via cancellation flow
        return $user->hasRole('admin') || $user->id === $subscription->user_id;
    }
}
