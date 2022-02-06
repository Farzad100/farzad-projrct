<?php

namespace App\Http\Controllers\Auth;

use App\Models\Useful;
use App\Http\Controllers\Controller;
use App\Models\ICBS;
use App\Models\Verification;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
  use RegistersUsers;

  /**
   * Where to redirect users after registration.
   *
   * @var string
   */
  protected $redirectTo = RouteServiceProvider::HOME;

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('guest');
  }

  /**
   * Get a validator for an incoming registration request.
   *
   * @param array $data
   * @return \Illuminate\Contracts\Validation\Validator
   */
  protected function validator(array $data)
  {
    return Validator::make($data, [
      'username' => ['required', 'string', 'min:10', 'max:15'],
      'tracker' => ['required', 'string', 'min:6'],
    ]);
  }

  protected function create(Request $request)
  {
    $request->validate([
      'username' => ['required', 'string', 'min:10', 'max:15'],
      'tracker' => ['required', 'string', 'min:6'],
    ]);

    $mode = $request->mode;
    if ($mode != 'shop')
      $request->validate(['password' => ['required', 'string', 'min:4']]);

    $username = Useful::enum($request->username, 'username');
    $verification = \App\Http\Controllers\VerificationController::is_valid($username, $request['tracker']);
    if (!$verification)
      return Useful::nok(['invalidNumber', 'شماره موبایل تأیید نشد']);

    $birth = $request->birth;
    if ($birth && strlen($birth) >= 8)
      $birth =  Useful::enum($birth, 'birth');
    else
      return Useful::nok(['birth_invalid', 'تاریخ تولد نامعتبر است']);

    $nid = $request->nid;
    if (!$nid)
      return Useful::nok(['nid_invalid', 'کدملی نامعتبر است']);

    $nid = User::check_nid($nid);
    if (User::where('nid', $nid)->whereNotNull('verified_at')->exists())
      return Useful::nok(['nid_exists', 'این کدملی توسط کاربر دیگری قبلاً ثبت شده است.']);

    $utm = $request->utm;
    if (!$utm || $mode === 'shop') $utm = [];

    $user = User::create([
      'username' => $username,
      'password' => $mode == 'shop' ? 'shouldbechanged' : Hash::make($request->password),
      'simcard_owner' => null,
      'nid' => $nid,
      'birth' => $birth,
      'rfrr' => Useful::enum($request->rfrr),
      'utm_source' => $utm['utm_source'] ?? null,
      'utm_medium' => $utm['utm_medium'] ?? null,
      'utm_campaign' => $utm['utm_content'] ?? null,
      'utm_content' => $utm['utm_campaign'] ?? null,
    ]);

    Verification::where('track_id', $request->tracker)->delete();
    User::generateUserInviteCode($user->id);

    //Shahkar
    if (config('app.env') == 'production') {
      if (!$user->simcard_owner) {
        $shahkar = ICBS::Shahkar($user->nid, Useful::enum($user->username, 'mobile'), $mode == 'shop' ? 'restrict-shop' : 'restrict', $user->id);

        if (is_array($shahkar) && isset($shahkar['name']) && isset($shahkar['message']))
          return Useful::nok($shahkar);

        $inq_sim = $shahkar->result['MobileAndNationalCodeMatchingResult'];
        if (isset($inq_sim['Result']) && $inq_sim['Result'] === true)
          $user->update(['simcard_owner' => $shahkar->id]);
        else if (isset($inq_sim['Result']) && $inq_sim['Result'] === false) {
          $user->delete();
          if ($mode == 'shop') $msg = 'طبق نتیجه استعلام، شماره موبایل و کد ملی وارد شده با هم تطابق ندارند. لطفاً با شماره سیم‌کارت و کدملی متعلق به خود کاربر در سامانه ثبت سفارش نمایید.';
          else $msg = 'طبق نتیجه استعلام، شماره موبایل و کد ملی وارد شده با هم تطابق ندارند. لطفاً با شماره سیم‌کارت و کدملی متعلق به خود در سامانه ثبت نام نمایید.';
          return Useful::nok(null, [
            'custom_message' => [
              'msg_title' => '',
              'msg' => '<p>' . $msg . '</p>',
              'msg_id' => 0,
              'type' => 'danger',
              'icon' => 'exclamation-circle',
              'style' => 'modal',
              'buttons' => [
                [
                  'text' => 'بستن',
                  'type' => 'light',
                  'mode' => 'close',
                ],
                [
                  'text' => 'ثبت نام مجدد',
                  'type' => 'primary',
                  'mode' => 'url',
                  'url' => config('app.url') . '/register'
                ],
              ]
            ]
          ]);
        }
      }
    }

    $user = User::where('id', $user->id)->first();

    if ($mode == 'shop') {
      return Useful::ok([
        'user_exists' => true,
        'user' => [
          'fname' => 'کاربر',
          'lname' => $user->lname,
          'nid' => $user->nid,
          'username' => $user->username,
          'mobile' => '0' . $user->username,
        ]
      ], ['clear_reg_cookies']);
    } else {
      Auth::login($user);
      $agent_string = User::agent()->overview;
      $t = Auth::user()->createToken($agent_string);
      $a = $t->accessToken;
      return Useful::ok([
        'user_exists' => true,
        'user' => [
          'fname' => 'کاربر',
          'lname' => $user->lname,
          'nid' => $user->nid,
          'username' => $user->username,
          'mobile' => '0' . $user->username,
          'roles' => User::roles($user),
          'token' => $a
        ]
      ], ['clear_reg_cookies']);
    }
    return Useful::ok(['user_id' => $user->id]);
  }

  protected function password_recovery(Request $request)
  {
    $request->validate([
      'username' => ['required', 'string', 'min:10', 'max:15'],
      'password' => ['required', 'string', 'min:4'],
      'tracker' => ['required', 'string', 'min:6'],
    ]);
    $username = Useful::enum($request->username, 'username');
    $verification = \App\Http\Controllers\VerificationController::is_valid($username, $request->tracker);
    if ($verification) {
      if (User::where(['username' => $username])->update(['password' => Hash::make($request->password)])) {
        Verification::where('track_id', $request->tracker)->delete();
        $user = User::where(['username' => $username])->first();
        Auth::login($user);
        if (true) {
          $agent_string = User::agent()->overview;
          $t = Auth::user()->createToken($agent_string);
          $a = $t->accessToken;
          return Useful::ok([
            'is_verified' => true,
            'user_exists' => true,
            'user' => [
              'fname' => $user->fname,
              'lname' => $user->lname,
              'nid' => $user->nid,
              'username' => $user->username,
              'mobile' => '0' . $user->username,
              'roles' => User::roles($user),
              'token' => $a
            ]
          ]);
        }
        return Useful::ok();
      }
      return Useful::nok(['dbu', 'متأسفانه مشکلی در ارتباط با سرور رخ داده است.']);
    }
    return Useful::nok(['invalidNumber', 'شماره موبایل تأیید نشد']);
  }
}
