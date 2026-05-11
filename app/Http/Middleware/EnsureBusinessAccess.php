<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBusinessAccess
{
    /**
     * Handle an incoming request.
     * Ensures users can only access data from their own business
     * Super Admin can access all businesses
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Super Admin can access everything
        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }

        // Admin and Salesperson must have a business_id
        if ($user && !$user->business_id) {
            abort(403, 'You are not assigned to any business. Please contact the Super Admin.');
        }

        // Check if user's business is active
        if ($user && $user->business && !$user->business->isActive()) {
            abort(403, 'Your business account is currently inactive. Please contact the Super Admin.');
        }

        return $next($request);
    }
}
