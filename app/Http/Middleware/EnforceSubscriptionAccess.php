<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;

class EnforceSubscriptionAccess
{
    /**
     * Handle an incoming request mapping exactly targeting strictly Suspended
     * or natively expired Cancelled accounts cleanly out of the core application.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user || !$user->organisation || Auth::guard('super_admin')->check()) {
            return $next($request);
        }

        $subscription = $user->organisation->currentSubscription;
        $isBlocked = false;

        if ($subscription) {
            // Check natively explicit suspensions locking execution natively
            if ($subscription->status === 'Suspended') {
                $isBlocked = true;
            } 
            // Lock out natively executed Cancellations strictly passing mathematical boundaries natively
            elseif ($subscription->status === 'Cancelled' && $subscription->end_date && now()->isAfter($subscription->end_date)) {
                $isBlocked = true;
            }
        }

        if ($isBlocked) {
            $allowedRoutes = [
                'logout',
                'settings.index',
                'settings.billing',
                'settings.subscription.start',
                'settings.subscription.cancel'
            ];

            if (!in_array($request->route() ? $request->route()->getName() : '', $allowedRoutes)) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Subscription Suspended.'
                    ], 403);
                }

                // Explicit route fragment automatically locking the GUI tightly natively onto Billing dashboards
                return redirect()->route('settings.index')->withFragment('billing');
            }
        }

        return $next($request);
    }
}
