<?php

namespace App\Http\Controllers;

use App\Models\Blacklist;
use App\Models\Useful;
use App\Models\Pattern;
use App\Models\Shop;
use App\Models\Sms;
use App\Models\User;
use App\Models\Verification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;

class VerificationController extends Controller
{
  public function generate_otp(Request $request)
  { 
    // return Useful::nok(['sms_failed', 'با عرض پوزش، در حال حاضر به دلیل اختلال در سامانه پیامکی، قادر به ارسال پیامک نیستیم. لطفاً ساعاتی دیگر مجدداً تلاش کنید.']);
    
    $request->validate([
      'username' => 'bail|required|min:10|max:15',
      'type' => 'bail|required|string',
    ]);
    $type = $request->type;
    if ($type == 'shop_order') {
      $user = Auth::user(); 
      $shop = Shop::select('status')->where('user_id', $user->id)->first();
      if ($shop->status != 'active') {
        return Useful::nok([
          'name' => 'shop_inactive',
          'message' => 'فروشگاه شما غیرفعال است'
        ]);
      }
    }
    $username = Useful::enum($request->username, 'username');

    switch ($type) {
      case 'register':
        $pattern = Pattern::user_auth_otp_registration;
        break;
      case 'password':
        $pattern = Pattern::otp;
        break;
      case 'shop_order':
        $pattern = Pattern::user_order_by_shop_otp;
        $user = Auth::user();
        $shop_name = Shop::select('name')->where('user_id', $user->id)->first()->name;
        break;
    }
    if (in_array($request->route, ['seller-register', 'organ-register'])) $pattern = Pattern::otp;

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
    ];

    $ip = Useful::ip();

    if (Blacklist::where(['val' => $ip])->exists())
      return Useful::nok(['blocked', 'متأسفانه شماره شما در سامانه قسطا مسدود شده است']);

    if (
      $type != 'shop_order' &&
      (Verification::where('ip', $ip)->where('otp_sent_at', '>', Carbon::now()->subHour())->count() > 4 ||
        Verification::where('ip', $ip)->where('otp_sent_at', '>', Carbon::now()->subWeek())->count() > 20)
    ) {
      Blacklist::create([
        'type' => 'ip',
        'val' => $ip,
        'meta' => $meta
      ]);
      Sms::send(config('globals.cto.mobile'), 'mob: ' . $username . ' ip: ' . $ip);
      Useful::report("⛔️" . " بلک لیست otp", [
        'موبایل' => Useful::enum($username, 'mobile'),
        'نوع درخواست' => $type,
        'آیپی' => $ip . "\n" . Useful::ip(),
      ]);
      return Useful::nok(['blocked', 'متأسفانه شماره شما در سامانه قسطا مسدود شده است']);
    }

    if ($err = Sms::is_flooding($username, $pattern))
      return Useful::nok(['flooding', $err]);

    $last = Verification::where([
      'prop' => 'mobile',
      'val' => $username,
      'type' => $type,
      'is_verified' => 0
    ])->where('otp_sent_at', '>', Carbon::now()->subHour())->orderBy('id', 'desc')->first();

    if ($last) {
      $verification = $last;
      $otp = $verification->otp;
      $trackId = $verification->track_id;
    } else {
      $otp = mt_rand(10000, 99999);
      $trackId = Useful::randomString(15);
      $verification = Verification::create([
        'prop' => 'mobile',
        'val' => $username,
        'type' => $type,
        'is_verified' => 0,
        'verified_at' => null,
        'track_id' => $trackId,
        'otp' => $otp,
        'otp_sent_at' => Carbon::now(),
        'ip' => $ip,
        'meta' => $meta
      ]);
    }

    if ($verification) {
      $shop_name = '';
      if (Sms::send($username, $pattern, ['otp' => $otp, 'shop' => $shop_name]))
        return Useful::ok(['track_id' => $trackId, 'otp_sent_at' => Carbon::now()]);
      return Useful::nok(['sms', 'متأسفانه در این لحظه قادر به ارسال پیامک نیستیم. لطفاً چند دقیقه دیگر مجدداً امتحان کنید.']);
    }
    return Useful::nok(['db', 'متأسفانه مشکلی در ارتباط با سایت رخ داده است.']);
  }

  public function validate_otp(Request $request)
  {
    $username = Useful::enum($request->username, 'username');
    $otp = Useful::enum($request->otp);
    $track_id = $request->track_id;

    $case = Verification::where(
      [
        'val' => $username,
        'track_id' => $track_id
      ]
    )->whereNull('verified_at')
      ->orderBy('id', 'desc')->first();

    if ($case) {
      if ($case->otp == $otp) {
        $tracker = Useful::randomString(15);
        $case->update([
          'is_verified' => 1,
          'verified_at' => Carbon::now(),
          'track_id' => $tracker
        ]);
        if (User::where('username', $username)->exists()) {
          $user = User::where('username', $username)->first();
          $params =  [
            'fname' => $user->fname,
            'lname' => $user->lname,
            'nid' => $user->nid,
            'username' => $user->username,
            'mobile' => '0' . $user->username,
          ];
          if ($case->type != 'shop_order') {
            Auth::login($user);
            $agent_string = User::agent()->overview;
            $t = Auth::user()->createToken($agent_string);
            $a = $t->accessToken;
            $params['roles'] = User::roles($user);
            $params['token'] = $a;
          }
          return Useful::ok(['tracker' => $tracker, 'user_exists' => true, 'user' => $params]);
        }
        return Useful::ok(['tracker' => $tracker, 'user_exists' => false]);
      }
      $case->update(['try_times' => $case->try_times + 1]);
      if ($case->try_times > 3) {
        $case->update(['track_id' => Useful::randomString(16)]);
        return Useful::nok(['otp_wrong', 'کد منقضی شده است.'], true);
      }
      return Useful::nok(['otp', 'کد وارد شده اشتباه است']);
    }
    return Useful::nok(['cheat', 'اطلاعات نامعتبر است']);
  }

  public static function is_valid($username, $tracker)
  {
    return Verification::where(['val' => $username, 'track_id' => $tracker, 'is_verified' => 1])->exists();
  }
}
