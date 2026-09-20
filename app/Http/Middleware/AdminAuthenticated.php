<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Exclude login route from authentication check
        if ($request->is('login') || $request->is('/')) {
            return $next($request);
        }
        
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        return $next($request);
    }
}