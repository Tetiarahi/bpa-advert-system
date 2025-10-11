<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PresenterActivityLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Set the activity log causer for presenter guard
        if (Auth::guard('presenter')->check()) {
            $presenter = Auth::guard('presenter')->user();
            activity()->causedBy($presenter);
        }

        return $next($request);
    }
}
