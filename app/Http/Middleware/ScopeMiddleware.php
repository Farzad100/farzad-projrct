<?php

namespace App\Http\Middleware;

use Closure;

class ScopeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $level = Auth::user()->is_admin;
        if ($level < 16) abort(403, 'Forbidden!');
        return $next($request);
    }
}
