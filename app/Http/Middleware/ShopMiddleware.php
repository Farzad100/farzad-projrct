<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use App\Models\Useful;
use App\Models\User;
use Closure;

class ShopMiddleware
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
    if (!User::is_shop() && !User::is_admin()) abort(403, 'Forbidden!'); 
    
    $update = Setting::handle_update($request);
    if ($update) return $update;
    return $next($request);
  }
}
