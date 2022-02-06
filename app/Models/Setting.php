<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
  protected $guarded = ['id'];

  private static $custom = 310;

  private static $user = 216;

  private static $shop = 216;

  private static $organ = 216;

  private static $admin = 218;


  public static function gv()
  {
    static $Setting;
    if ($Setting === null) {
      $sets = Setting::select('prop', 'val', 'type')->where('is_public', 1)->get();
      $Setting = [];
      foreach ($sets as $set) {
        if ($set->type == 'percent') {
          $Setting[$set->prop] = $set->val / 100;
        } else if ($set->type == 'array') {
          $Setting[$set->prop] = json_decode($set->val, true);
        } else {
          $Setting[$set->prop] = $set->val;
        }
      }
    }
    return (object)$Setting;
  }

  public static function version()
  {
    return (object)[
      'user' => self::$user,
      'shop' => self::$shop,
      'organ' => self::$organ,
      'admin' => self::$admin,
      'all' => self::$user . '_' . self::$shop . '_' . self::$organ . '_' . self::$admin . '_' . self::$custom,
    ];
  }

  public static function is_update_required($str)
  {
    $x = explode('_', $str);
    if (count($x) < 3) {
      return (object)[
        'user' => true,
        'shop' => true,
        'organ' => true,
        'admin' => true,
      ];
    }

    $latest = self::version();

    return (object)[
      'user' => $latest->user > $x[0] ? true : false,
      'shop' => $latest->shop > $x[1] ? true : false,
      'organ' => $latest->organ > $x[2] ? true : false,
      'admin' => $latest->admin > $x[3] ? true : false,
    ];
  }

  public static function force_update()
  {
    return response()->json(['status' => false, 'force_update' => true, 'error' => ['message' => 'درحال دریافت آپدیت ها...']], 200);
  }

  public static function handle_update($request)
  {
    $route = $request->route();
    $update = !(isset($route->action['as']) && substr($route->action['as'], 0, 3) == 'nou');
    if (
      $request->header('app-version') && $route->methods[0] == 'GET' && $update
      && Setting::is_update_required($request->header('app-version'))->{end($route->action['middleware'])}
    )
      return Setting::force_update();
    return false;
  }
}
