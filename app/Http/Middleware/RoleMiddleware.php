<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Allowed roles (e.g., 'admin', 'cashier')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        if (!$user->is_active) {
            auth()->logout();
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your account has been deactivated. Please contact the administrator.'], 403);
            }
            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been deactivated. Please contact the administrator.',
            ]);
        }

        if (!empty($roles) && !in_array($user->role, $roles)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You do not have permission to access this page.'], 403);
            }
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
