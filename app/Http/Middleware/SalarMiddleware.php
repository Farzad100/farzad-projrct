<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SalarMiddleware
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
    if (Auth::user()->is_admin < 15) {
      abort(403, 'Forbidden!');
      return response()->json('o_O', 403);
    }
    return $next($request);
  }
}
