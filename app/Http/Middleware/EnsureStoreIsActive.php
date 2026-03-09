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
            if ($user->store->isTrialExpired() || ($user->store->isActive() && $user->store->subscription_ends_at?->isPast())) {
                $user->store->update(['subscription_status' => 'expired']);
            }

            $message = match ($user->store->subscription_status) {
                'suspended' => 'Toko Anda telah ditangguhkan. Silakan hubungi support.',
                'cancelled' => 'Langganan toko Anda telah dibatalkan.',
                'expired' => 'Langganan Anda telah berakhir. Silakan perpanjang untuk lanjut menggunakan sistem.',
                default => 'Akses toko Anda telah berakhir.',
            };

            auth()->logout();

            return redirect()->route('subscription.expired')->with('error', $message);
        }

        return $next($request);
    }
}
