<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscriber
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if (! $request->user()->isSubscriber()) {
            abort(403, 'Access denied. Subscriber role required.');
        }

        // Check for active subscription (using hasAccess which includes grace period)
        $subscription = $request->user()->subscription;
        
        if (! $subscription || ! $subscription->hasAccess()) {
            return redirect()->route('subscriber.subscription.index')
                ->with('warning', 'Your subscription is inactive. Please renew to continue.');
        }

        // If in grace period, flash a warning
        if ($subscription->isPastDue() && $subscription->isInGracePeriod()) {
            $daysLeft = $subscription->graceDaysRemaining();
            session()->flash('grace_warning', "Your payment failed. You have {$daysLeft} days to update your payment method before access is suspended.");
        }

        return $next($request);
    }
}

