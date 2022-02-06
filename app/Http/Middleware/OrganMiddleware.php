<?php

namespace App\Http\Middleware;

use App\Models\Setting; 
use App\Models\User;
use Closure;

class OrganMiddleware
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
    if (!User::is_organ() && !User::is_admin()) abort(403, 'Forbidden!');

    $update = Setting::handle_update($request);
    if ($update) return $update;
    return $next($request);
  }
}
