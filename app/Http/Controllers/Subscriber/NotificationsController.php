<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Assuming database notifications are used, or we can mock for now as per previous patterns
        // Laravel's default is $user->notifications
        // If not set up, we'll send an empty collection to avoid crash
        $notifications = $user->notifications ?? collect([]);

        return view('subscriber.notifications.index', compact('notifications'));
    }
}
