<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature = null): Response
    {
        $user = Auth::user();

        // If no feature is specified, just check if the user is subscribed
        if (!$feature) {
            if (!$user->hasSubscription()) {
                return redirect()->route('pricing.index')
                    ->with('error', 'You need to subscribe to access this feature.');
            }
        } else {
            // Check if the user has access to the specified feature
            if (!$user->canAccess($feature)) {
                return redirect()->route('pricing.index')
                    ->with('error', 'Your current plan does not include this feature. Please upgrade to access it.');
            }
        }

        return $next($request);
    }
}
