<?php

namespace App\Http\Middleware;

use App\Models\Order;
use Closure;
use Illuminate\Support\Facades\Auth;

class OrderMiddleware
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
    $oid = $request->oid;
    if(!empty($oid) && !in_array($oid,['first','last','all','recent'])){
      $user_id = Auth::user()->id;
      $order_user_id = Order::select('user_id')->where('oid',$oid)->first()->user_id;
      if($user_id != $order_user_id) abort(403,'This is not your business!');
    }
    return $next($request);
  }
}
