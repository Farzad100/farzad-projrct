<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
  use HasApiTokens, Notifiable, SoftDeletes;

  protected $guarded = ['id'];

  protected $hidden = ['password'];

  protected $casts = ['email_verified_at' => 'datetime'];

  protected $appends = ['full_name', 'mobile'];

  public function getFullNameAttribute()
  {
    return "{$this->fname} {$this->lname}";
  }

  public function getMobileAttribute()
  {
    return "0{$this->username}";
  }

  public function tokens()
  {
    return $this->hasMany(OauthAccessToken::class, 'user_id');
  }

  public function usermeta()
  {
    return $this->hasOne(Usermeta::class, 'user_id');
  }

  public function addresses()
  {
    return $this->hasMany(Address::class);
  }

  public function orders()
  {
    return $this->hasMany(Order::class);
  }

  /* public function ghests()
  {
    return $this->hasMany(Ghest::class);
  } */

  public function docs()
  {
    return $this->hasMany(Doc::class);
  }

  public function permissions()
  {
    return $this->hasMany(Permission::class);
  }

  public function accounts()
  {
    return $this->hasMany(Account::class);
  }

  public function cards()
  {
    return $this->hasMany(Card::class);
  }

  public function shops()
  {
    return $this->hasMany(Shop::class);
  }

  public function organs()
  {
    return $this->hasMany(Organ::class);
  }

  public static function info($user_id = null, $mode = null)
  {
    if (is_null($user_id)) $user_id = Auth::user()->id;

    if ($mode == 'all') {
      $user = User::withTrashed()->whereId($user_id)->with('meta')->first();
    } else {
      $user = User::withTrashed()->whereId($user_id)->first();
    }

    $arr = [
      'id' => $user->id,
      'username' => $user->username,
      'mobile' => $user->mobile,
      'email' => $user->email,
      'fname' => $user->fname,
      'lname' => $user->lname,
      'full_name' => $user->full_name,
      'nid' => $user->nid,
      'birth' => $user->birth,
      'badge' => $user->badge,
      'verified_at' => $user->verified_at,
      'is_admin' => $user->is_admin,
    ];

    if ($mode == 'all') {
      $usermeta = $user->meta;
      $arr2 = [
        'meta_id' => $usermeta->id,
        'person_verified_at' => $usermeta->verified_at,
        'has_cheque' => $usermeta->has_cheque,
        'gender' => $usermeta->gender,
        'is_married' => $usermeta->is_married,
        'education' => $usermeta->education,
        'job' => $usermeta->job,
        'salary' => $usermeta->salary,
      ];
      $arr = array_merge($arr, $arr2);
    }

    return (object)$arr;
  }

  public static function agent()
  {
    $agent = new Agent();
    if ($agent->isDesktop()) $device = 'desktop';
    else if ($agent->isTablet()) $device = 'tablet';
    else if ($agent->isPhone()) $device = 'phone';
    else $device = 'robot';
    $meta = [
      'type' => $device,
      'model' => $agent->device(),
      'platform' => $agent->platform(),
      'browser' => $agent->browser(),
      'overview' => $device . '_' . $agent->device() . '_' . $agent->platform() . '_' . $agent->browser()
    ];
    return (object)$meta;
  }

  public function orders_inprogress()
  {
    return $this->hasMany(Order::class)->inprogress();
  }

  public function orders_worthless()
  {
    return $this->hasMany(Order::class)->worthless();
  }

  public function orders_successful()
  {
    return $this->hasMany(Order::class)->successful();
  }

  public static function generateUserInviteCode($userId)
  {
    $code = 'u' . ($userId + 59) . mt_rand(0, 9);
    $user = User::find($userId);
    if (empty($user->invite_code)) {
      DB::table('users')->where('id', $userId)->update(['invite_code' => $code]);
      return true;
    }
    return false;
  }

  public static function check_nid($code)
  {
    $code = str_pad(Edate::tr_num($code), 10, '0', STR_PAD_LEFT);
    if (strlen((int)$code) < 8) return false;
    if (!preg_match('/^[0-9]{10}$/', $code)) return false;
    for ($i = 0; $i < 10; $i++)
      if (preg_match('/^' . $i . '{10}$/', $code)) return false;
    for ($i = 0, $sum = 0; $i < 9; $i++) $sum += ((10 - $i) * intval(substr($code, $i, 1)));
    $ret = $sum % 11;
    $parity = intval(substr($code, 9, 1));
    if (($ret < 2 && $ret == $parity) || ($ret >= 2 && $ret == 11 - $parity)) return $code;
    return false;
  }

  public static function is_admin($user = null)
  {
    if (is_null($user)) $user = Auth::user();
    if (!$user) return false;
    else if ($user->is_admin > 10) return true;
    else return false;
  }

  public static function is_editor()
  {
    $user = Auth::user();
    if (!$user) return false;
    else if ($user->is_admin > 11) return true;
    else return false;
  }

  public static function is_watcher()
  {
    $user = Auth::user();
    if (!$user) return false;
    else if ($user->is_admin > 10) return true;
    else return false;
  }

  public static function is_shop()
  {
    $user = Auth::user();
    if (!$user) return false;
    else if (Shop::where('user_id', $user->id)->exists()) return true;
    else return false;
  }

  public static function is_organ()
  {
    $user = Auth::user();
    if (!$user) return false;
    else if (Organ::where('user_id', $user->id)->exists()) return true;
    else return false;
  }

  public static function roles($user)
  {
    $roles = [];
    if ($user->is_admin > 10) $roles[] = 'admin';
    if (Shop::where(['user_id' => $user->id, 'type' => 'online'])->exists()) $roles[] = 'eshop';
    if (Shop::where(['user_id' => $user->id, 'type' => 'offline'])->exists()) $roles[] = 'shop';
    if (Organ::where('user_id', $user->id)->exists()) $roles[] = 'organ';
    $roles[] = 'user';
    return $roles;
  }

  public static function mobile($user_id)
  {
    return '0' . User::select('username')->whereId($user_id)->first()->username;
  }

  public function scopeSearch($query, $request, $role = null)
  {
    if (empty($request->sort)) {
      $sort_by = 'id';
      $sort_order = 'desc';
    } else {
      $sort = explode('-', $request->sort);
      $sort_by = $sort[0];
      $sort_by_map = [
        'name' => 'lname',
        'utm' => 'utm_source',
        'date' => 'created_at',
      ];
      $sort_by = $sort_by_map[$sort_by] ?? $sort_by;
      $sort_order = $sort[1];
    }
    $x = $query
      ->searchDate('created_at', $request->from_date, $request->to_date)
      ->searchLike('username', substr($request->mobile, -10))
      ->searchLike('fname', $request->fname)
      ->searchLike('lname', $request->lname)
      ->searchLike('nid', $request->nid)
      ->searchExact('badge', $request->badge)
      ->searchLike('utm_source', $request->utm_source)
      ->searchLike('utm_medium', $request->utm_medium)
      ->searchLike('utm_campaign', $request->utm_campaign)
      ->searchLike('utm_content', $request->utm_content)
      ->orderBy($sort_by, $sort_order);
    return $x;
  }

  public function scopeSearchDate($query, $type, $fromDate = null, $toDate = null, $format = 'j')
  {
    $fromDate && strlen($fromDate) > 6 ? $f = true : $f = false;
    $toDate && strlen($toDate) > 6 ? $t = true : $t = false;

    if ($f) {
      $fromPart = explode('-', $fromDate);
      $fromTS = Edate::jmktime(0, 0, 1, $fromPart[1], $fromPart[2], $fromPart[0]);
      $from = Carbon::createFromTimestamp($fromTS);
    }
    if ($t) {
      $toPart = explode('-', $toDate);
      $toTS = Edate::jmktime(23, 59, 59, $toPart[1], $toPart[2], $toPart[0]);
      $to = Carbon::createFromTimestamp($toTS);
    }

    if (!$f && !$t) {
      return $query;
    } else if (!$f) {
      return $query->where($type, '<=', $to);
    } else if (!$t) {
      return $query->where($type, '>=', $from);
    } else {
      return $query->whereBetween($type, [$from, $to]);
    }
  }

  public function scopeSearchExact($query, $type, $str, $rel = null)
  {
    if (empty($str)) return $query;
    if ($rel) {
      return $query->whereHas($rel, function ($query) use ($str, $type) {
        $query->where($type,  $str);
      });
    }
    return $query->where($type, $str);
  }

  public function scopeSearchLike($query, $type, $str, $rel = null)
  {
    if (empty($str)) return $query;
    if ($rel) {
      return $query->whereHas($rel, function ($query) use ($str, $type) {
        $query->where($type, 'like', '%' . $str . '%');
      });
    }
    return $query->where($type, 'like', '%' . $str . '%');
  }
}
