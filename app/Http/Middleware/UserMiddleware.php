<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;

class UserMiddleware
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
    $update = Setting::handle_update($request);
    if ($update) return $update;
    return $next($request);
  }
}
