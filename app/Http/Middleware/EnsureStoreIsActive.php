<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStoreIsActive
{
    /**
     * Ensure the authenticated user's store has an active subscription.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->store) {
            auth()->logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Your account is not associated with any store.',
            ]);
        }

        if (!$user->store->canAccess()) {
            auth()->logout();

            $message = match ($user->store->subscription_status) {
                'suspended' => 'Your store has been suspended. Please contact support.',
                'cancelled' => 'Your store subscription has been cancelled.',
                default => 'Your store subscription has expired.',
            };

            return redirect()->route('login')->withErrors([
                'email' => $message,
            ]);
        }

        return $next($request);
    }
}
