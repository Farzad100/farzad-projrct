<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;

class Shared
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    { 
        view()->share('version', Setting::version()->all);
        return $next($request);
    }
}
