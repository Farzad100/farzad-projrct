<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use App\Models\Useful;
use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{ 
  private const ashti = 26;
  private const ehsan = 490;
  private const akhtari = 1569;
  private const mehrabi = 6885;
  private const haji = 24303;
  private const ramezan = 12212;
  private const moghadam = 30479;
  private const borhan = 2595;
  private const ezzati = 18850;
  private const maleky = 14717;
  private const asgharzadeh = 2906;
  private const rezapour = 21353;
  private const ebi = 37742;

  public static function chart_whitelist($user_id = null)
  {
    $arr = [
      self::ashti,
      self::ehsan,
      self::akhtari,
      self::mehrabi,
      self::haji,
      self::asgharzadeh
    ];

    return $user_id ? in_array($user_id, $arr) : $arr;
  }

  public static function just_viewer($user_id = null)
  {
    $arr = [
      self::maleky,
      self::ebi
    ];

    return $user_id ? in_array($user_id, $arr) : $arr;
  }

  public function handle($request, Closure $next)
  {
    $user = Auth::user();
    if ($user->is_admin < 11) {
      abort(403, 'Forbidden!');
      return response()->json('o_O', 403);
    }

    $route = $request->route();
    $uri = explode('/', $route->uri);

    if (in_array($uri[2], ['charts']) && !self::chart_whitelist($user->id))
      return Useful::no();

    if (in_array($user->id, [self::ramezan, self::moghadam]) && !in_array($uri[2], ['orders', 'users', 'docs', 'ghests', 'accounting', 'cards', 'shops', 'organs']))
      return Useful::no();

    if (in_array($user->id, [self::ebi]) && !in_array($uri[2], ['ghests', 'cheques'])) {
      if ($uri[2] === 'orders' && $uri[4] === 'cheques-download')
        $x = 1;
      else
        return Useful::no();
    }

    if (self::just_viewer($user->id) &&  $route->methods[0] == 'POST')
      return Useful::no();

    $update = Setting::handle_update($request);
    if ($update) return $update;
    return $next($request);
  }
}
