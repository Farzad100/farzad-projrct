<?php

namespace App\Models;

use App\Http\Controllers\DocController;
use App\Models\Useful;
use App\Models\Edate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
  use SoftDeletes;

  protected $guarded = ['id'];

  protected $casts = ['meta' => 'array'];

  public const inquiry_rabbit_price = 10000;
  public const inquiry_rabbit_time = 1 * 86400;
  public const inquiry_turtle_price = 30000;
  public const inquiry_turtle_time = 3 * 86400;

  public function user()
  {
    return $this->belongsTo(User::class, 'user_id');
  }

  public function docs()
  {
    return $this->hasMany(Doc::class, 'order_id');
  }

  public function credits()
  {
    return $this->hasMany(Credit::class, 'order_id');
  }

  public function credits_done()
  {
    return $this->hasMany(Credit::class, 'order_id')->whereNotNull('done_at');
  }

  public function cheques()
  {
    return $this->hasMany(Cheque::class, 'order_id');
  }

  public function charges()
  {
    return $this->hasMany(Charge::class, 'order_id');
  }

  public function payments()
  {
    return $this->hasMany(Payment::class, 'order_id');
  }

  public function organ()
  {
    return $this->belongsTo(Organ::class, 'organ_id');
  }

  public function shop()
  {
    return $this->belongsTo(Shop::class, 'shop_id');
  }

  public function provider()
  {
    return $this->belongsTo(Provider::class, 'provider_id');
  }

  public function ghests()
  {
    return $this->hasMany(Ghest::class, 'order_id');
  }

  public static $states = ['all', 'draft', 'submitted', 'docs_uploaded', 'scoring', 'scored', 'pended_by_organ', 'upload_secondary', 'check_secondary', 'wait_for_cheques', 'prepayment', 'prepaid', 'cancelled', 'rejected', 'cycle_cheque',  'cycle_epay', 'extra_charge', 'completed'];

  public static $status = [
    'draft' => 'استعلام',
    'submitted' => 'نیاز به مدارک',
    'docs_uploaded' => 'بررسی مدارک اولیه',
    'scoring' => 'در صف اعتبارسنجی',
    'scored' => 'تأیید اعتبار',
    'pended_by_organ' => 'در انتظار تأیید سازمان',
    'upload_secondary' => 'بارگذاری مدارک تکمیلی',
    'check_secondary' => 'بررسی مدارک تکمیلی',
    'wait_for_cheques' => 'ارسال چک/مدارک تکمیلی',
    'wait_for_card' => 'دریافت قسطاکارت',
    'prepayment' => 'پیش پرداخت',
    'prepaid' => 'در صف شارژ',
    'cancelled' => 'لغو شده',
    'rejected' => 'رد شده',
    'worthless' => 'لغو/رد شده',
    'cycle_cheque' => 'دوره چک',
    'extra_charge' => 'شارژ اضافی',
    'cycle_epay' => 'دوره اقساط',
    'cycle' => 'دوره چک/اقساط',
    'completed' => 'پایان یافته'
  ];

  public static $traits = [
    'discounty' => 'تخفیفی',
    'organy' => 'سازمانی',
    'shopy' => 'فروشگاهی',
    'extra_charged' => 'شارژ اضافی شده',
    'cheque' => 'چکی',
    'epay' => 'قسطی',
  ];

  public static $ULTIMATUM_DOCS = 8 * 3600;

  public static function is_holiday($ts = null)
  {
    if (is_null($ts)) $ts = time();
    $dt = explode('-', Edate::edate('Y-m-d-H-w', $ts));
    $y = (int)$dt[0];
    $m = (int)$dt[1];
    $d = (int)$dt[2];
    $H = (int)$dt[3];
    $rooz = (int)$dt[4];
    if ($rooz === 6) return true;

    $holidays = json_decode(file_get_contents(public_path() . '/holidays.json'), true);
    if (in_array($d, $holidays[$y][$m])) return true;

    if ($rooz === 5 && $H > 12) return true;
    if ($H > 18) return true;

    return false;
  }

  public static function ultimatumy($ts = null, $rt)
  {
    if (is_null($ts)) $ts = time();

    $dt = explode('-', Edate::edate('Y-m-d-H-w', $ts));
    $y = (int)$dt[0];
    $m = (int)$dt[1];
    $d = (int)$dt[2];
    $h = (int)$dt[3];
    $rooz = (int)$dt[4];

    if ($h < 9) $h = 9;
    if ($rooz == 5) {
      $d = $d + 2;
    } else if ($h > 17) {
      ++$d;
      $h = 9;
    }

    $ts = Edate::jmktime($h, 0, 0, $m, $d, $y);

    if (self::is_holiday($ts + ($rt - 86400)))

      for ($i = 0; $i < 30; $i++) {
        if (self::is_holiday($ts)) {

          $dt = explode('-', Edate::edate('Y-m-d-H', $ts));
          $y = (int)$dt[0];
          $m = (int)$dt[1];
          $d = (int)$dt[2];
          $h = (int)$dt[3];

          if ($h < 9) $h = 9;
          else if ($h > 17) {
            ++$d;
            $h = 9;
          } else

            $ts = Edate::jmktime($h, 0, 0, $m, $d, $y);
          continue;
        }
      }
  }

  public static function ultimatum($dateTS = null)
  {
    if (is_null($dateTS)) $dateTS = time();
    $holidays = json_decode(file_get_contents(public_path() . '/holidays.json'), true);
    $dt = explode('-', Edate::edate('Y-m-d-H-i-t-N', $dateTS));
    $y = (int)$dt[0];
    $m = (int)$dt[1];
    $d = (int)$dt[2];
    $H = (int)$dt[3];
    $i = (int)$dt[4];
    $maxDays = (int)$dt[5];
    $boundry_hour = 18 - (self::$ULTIMATUM_DOCS / 3600);
    if ($H >= $boundry_hour) {
      $extra_sec = 8.5 * 3600;
      $dt = explode('-', Edate::edate('Y-m-d-H-i-t-N', $dateTS + $extra_sec));
      $y = (int)$dt[0];
      $m = (int)$dt[1];
      $d = (int)$dt[2];
      $H = (int)$dt[3];
      $i = (int)$dt[4];
      $maxDays = (int)$dt[5];
    }
    if ($H < 9) $H = 9;
    $N = (int)$dt[6];
    if ($H > 9 || ($H == 9 && $i > 0)) {
      $dt = explode('-', Edate::edate('Y-m-d-H-i-t', $dateTS + 86000));
      $y = (int)$dt[0];
      $m = (int)$dt[1];
      $d = (int)$dt[2];
      $H = (int)$dt[3];
      $i = (int)$dt[4];
      $maxDays = (int)$dt[5];
    }
    $end = 0;
    $resetDay = 0;
    $resetMonth = 0;
    for ($yx = $y; $yx <= $y + 1; $yx++) {
      if ($resetMonth == 1) {
        $d = 1;
        $m = 1;
        $resetDay = 0;
        $resetMonth = 0;
      }
      for ($mx = $m; $mx <= 12; $mx++) {
        if ($resetDay == 1) {
          $d = 1;
          $resetDay = 0;
        }
        $mxx = sprintf('%02d', $mx);
        for ($dx = $d; $dx <= $maxDays; $dx++) {
          $dxx = sprintf('%02d', $dx);
          $eee_ts = Edate::jmktime($H, 0, 0, $mxx, $dxx, $yx);
          $rooz = (int) Edate::edate('w', $eee_ts);
          if ($rooz == 6 || !is_numeric(array_search($dxx, $holidays["$yx"]["$mxx"])) && $H < $boundry_hour) {
            $ts = Edate::jmktime($H, $i, 1, $mx, $dx, $yx);
            $result = $ts + self::$ULTIMATUM_DOCS;
            $end = 1;
            break;
          }
          $H = 9;
          $i = 0;
          if ($dx >= $maxDays) $resetDay = 1;
        }
        if ($end == 1) break;
        if ($mx >= 12) $resetMonth = 1;
      }
      if ($end == 1) break;
    }

    //return timestamp
    return $result;
  }

  public static function status_fixer($order)
  {
    if (!$order) return false;
    $status = $order->status;
    switch ($status) {
      case 'docs_uploaded':
        if ($order->organ_id && empty($order->organ_accepted_at)) $status = 'pended_by_organ';
        else if (empty($order->docs_warning_at)) {
        }
        break;
      case 'cycle':
        if ($order->payback_type == 'cheque') $status = 'cycle_cheque';
        else $status = 'cycle_epay';
        break;
      default:
        break;
    }
    return $status;
  }

  public static function has_old_cheques($order_id)
  {
    return !Cheque::where('order_id',$order_id)->exists();
  }

  public static function doc_path()
  {
    return $dest = public_path() . '/TemPerorY';
  }

  public function scopeOid($query, $oid)
  {
    return $query->where('oid', $oid)->first();
  }

  public function scopeChargedIn($query, $fromDate, $toDate)
  {
    return $query->whereIn('status', ['cycle_cheque', 'cycle_epay', 'completed'])->whereBetween('finished_at', [$fromDate, $toDate]);
  }

  public function scopeExtraCharge($query)
  {
    return $query->where('status', 'cycle')->whereHas('payments', function ($query) {
      $query->where(['status' => 100, 'type' => 'extra'])->whereNull('charged_at');
    });
  }

  public function scopePendedByOrgan($query, $organ_id = null)
  {
    if ($organ_id) {
      return $query->where('organ_id', $organ_id)->where('status', 'docs_uploaded')->whereNull('organ_accepted_at');
    }
    return $query->whereNotNull('organ_id')->where('status', 'docs_uploaded')->whereNull('organ_accepted_at');
  }

  public function scopeWorthless($query)
  {
    return $query->whereIn('status', ['cancelled', 'rejected']);
  }

  public function scopeWorthy($query)
  {
    return $query->whereNotIn('status', ['cancelled', 'rejected']);
  }

  public function scopeSuccessful($query)
  {
    return $query->whereIn('status', ['cycle', 'completed']);
  }

  public function scopeCompleted($query)
  {
    return $query->whereIn('status', ['cycle', 'completed'])->whereRaw('`passed`>=`cheques`-1');
  }

  public function scopeInprogress($query)
  {
    return $query->whereNotIn('status', ['cancelled', 'rejected'])->whereRaw('`passed`<`cheques`-1');
  }

  public function scopeCycleCheque($query)
  {
    return $query->where('status', 'cycle')->where('payback_type', 'cheque');
  }

  public function scopeCycleEpay($query)
  {
    return $query->where('status', 'cycle')->where('payback_type', 'epay');
  }

  public function scopeCredit($query)
  {
    return $query->where('is_credit', 1);
  }

  public function scopeNormal($query)
  {
    return $query->where('is_credit', 0);
  }

  public function scopeThisUser($query)
  {
    return $query->where('user_id', Auth::user()->id);
  }

  public static function ghestify($order, $force = 0, $old = 0)
  {
    if ($force == 0) {
      if (isset($order->ghest) && isset($order->total)) return $order;
    }

    if (!isset($order->gain)) $order->gain = Setting::gv()->gain;

    if (!is_numeric($order->prepayment)) {
      if (isset($order->rpa)) $rpa = $order->rpa;
      else {
        $gv = Setting::gv();
        if (empty($order->organ_id)) $rpa = $gv->rpa;
        else {
          if ($order->organ_level == 2) $rpa = $gv->rpa_organ_level_2;
          else $rpa = $gv->rpa_organ_level_1;
        }
      }
      $order->prepayment = $order->amount * $rpa;
    }
    $k = doubleval($order->gain) + 1;
    $y = intval($order->amount) - intval($order->prepayment);
    $n = intval($order->months);
    if ($n == 2 * intval($order->cheques)) $up = $y * ($k ** $n) * (($k ** 2) - 1);
    else $up = $y * ($k ** $n) * ($k - 1);
    $down = ($k ** $n) - 1;
    if ($down == 0) return 0;
    $divide = $up / $down;
    $res = ceil($divide / 5000) * 5000;
    if ($res > 1000) $order->ghest = $res;
    else $order->ghest = 0;
    $order->total = ($order->ghest * $order->cheques) + $order->prepayment;
    return $order;
  }

  public static function cheque_dates($order)
  {
    if (!$order->first_ghest_at) return [];
    $ymd = Edate::edateFromCarbon('Y-m-d', $order->first_ghest_at);
    $baseDate = explode('-', $ymd);
    $baseYear = (int) $baseDate[0];
    $baseMonth = (int) $baseDate[1];
    $baseDay = (int) $baseDate[2];

    $m = $baseMonth;
    for ($i = 0; $i < $order->cheques; $i++) {
      if ($m == 13 || $m == 14) ++$baseYear;
      if ($m > 12) $m = $m % 12;
      $words = Edate::jdate_words(['ss' => $baseYear, 'mm' => $m, 'rr' => $baseDay]);
      $details[$i] = [
        'badge' => $i,
        'date' => $baseYear . '-' . sprintf('%02d', $m) . '-' . sprintf('%02d', $baseDay),
        'date_farsi' => $words['rr'] . ' ' . $words['mm'] . ' ' . $words['ss']
      ];
      $m = $m + ($order->months == $order->cheques ? 1 : 2);
    }

    return $details;
  }

  public static function cheques_info($order)
  {
    $payback_type = $order->payback_type;
    $details = [];
    $dates = self::cheque_dates($order);
    $chb = null;

    /* $cheque_numbers = Ghest::select('id', 'account_id', 'series', 'prepend', 'append', 'isbn')
      ->where('order_id', $order->id)->orderBy('shamsi', 'asc')->get(); */

    $cheque_empty = Doc::select('account_id')->where([
      'type' => 'cheque',
      'order_id' => $order->id,
      'is_verified' => 1
    ])->orderBy('id', 'desc')->first();

    $account_id = $cheque_empty ? $cheque_empty->account_id : null;

    $chfps = Cheque::whereIn('type', ['chf', 'chb'])->where('order_id', $order->id)->get();
    $first_chf = $chfps->where('type', 'chf')->where('badge', 0)->first();
    for ($i = 0; $i < $order->cheques; $i++) {
      $ch = null;
      $details[$i] = [
        'badge' => $i,
        'amount' => $order->ghest,
        'date' => $dates[$i]['date'],
        'date_farsi' => $dates[$i]['date_farsi']
      ];

      if ($payback_type == 'cheque') {
        $account = Account::select('iban', 'bank_id', 'branch_name', 'branch_code')->whereId($account_id)->first();
        $chf = $chfps->where('type', 'chf')->where('badge', $i)->first();
        if ($chf) {
          $ch['comment'] = $chf->comment;
          switch ($chf->is_verified) {
            case 1:
              $ch['isbn_locked'] = ($chf->is_readable == 1 && strlen($chf->isbn) == 16) ? $chf->isbn : null;
              $ch['status'] = 'verified';
              $ch['message'] = 'این چک کاملاً تأیید شده';
              break;
            case 0:
              $ch['isbn_locked'] = ($chf->is_readable == 1 && strlen($chf->isbn) == 16) ? $chf->isbn : null;
              $ch['status'] = 'pending';
              if (!$chf->prepend || !$chf->series) {
                $ch['status'] = 'attention';
                $ch['message'] = 'اطلاعات چک وارد نشده';
              } else if (!$chf->append && in_array($account->bank_id, [12, 17])) {
                $ch['status'] = 'attention';
                $ch['message'] = 'اطلاعات چک بطور کامل وارد نشده.';
              } else if ($chf->is_submitted === 0) {
                $ch['status'] = 'attention';
                $ch['message'] = 'چک در سامانه صیاد ثبت نشده';
              }

              if ($chf->decided_at) $ch['status'] = 'attention';

              if ($chf->need_chb) {
                $chb = $chfps->where('type', 'chb')->where('series', $chf->id)->first();
                if ($chb) {
                  $ch['chb_uploaded'] = 1;
                } else {
                  $ch['status'] = 'attention';
                  $ch['comment'] = $chf->comment;
                  $ch['url_back'] = "/api/orders/" . $order->oid . "/upload-cheque/chb/" . $chf->id;
                }
                $ch['need_chb'] = 1;
              } else $chb = null;
              break;
            case -1:
              $ch['status'] = 'rejected';
              $ch['description'] = 'این چک را اشتباه بارگذاری کرده اید و باید مجدداً ارسال شود.';

              if ($chf->need_chb) {
                $chb = $chfps->where('type', 'chb')->where('series', $chf->id)->first();
                if ($chb) {
                  $ch['chb_uploaded'] = 1;
                } else {
                  $ch['status'] = 'rejected';
                  $ch['comment'] = $chf->comment;
                  $ch['url_back'] = "/api/orders/" . $order->oid . "/upload-cheque/chb/" . $chf->id;
                }
                $ch['need_chb'] = 1;
              } else $chb = null;
              break;
          }

          $ch['url_back'] = "/api/orders/" . $order->oid . "/upload-cheque/chb/" . $chf->id;
          $ch['is_readable'] = $chf->is_readable;

          if (User::is_admin()) {
            $ch['link_back'] = $chb ? '/admin/docs/' . $chb->id . '/cheques' : null;
            $ch['link'] = '/admin/docs/' . $chf->id . '/cheques';
            $ch['photo'] = DocController::downloadFile($chf->id, 'cheques');
            $ch['decision_link'] = '/admin/cheques/' . $chf->id;
            $ch['format'] = $chf->format;
          }
        } else {
          $ch['status'] = 'void';
          $ch['message'] = 'این تصویر هنوز بارگذاری نشده';
          $ch['description'] = "از چک نوشته شده خود از زاویه مستقیم عکس بگیرید و تصویر آن را اینجا بارگذاری کنید. بطوری که اطلاعات آن مخصوصاً شناسه صیادی واضح باشد.";
        }
        $ch['url'] = "/api/orders/" . $order->oid . "/upload-cheque/chf/" . $details[$i]['badge'];

        $details[$i] = array_merge($details[$i], [
          'oum' => 'چک ' . Useful::oum($i + 1),
          'is_submitted' => $chf->is_submitted ?? null,
          'photo_verified_at' => $chf->photo_verified_at ?? null,
          'isbn_verified_at' => $chf->isbn_verified_at ?? null,
          'data_verified_at' => $chf->data_verified_at ?? null,
          'submit_verified_at' => $chf->submit_verified_at ?? null,
          'prepend' => $chf->prepend ?? null,
          'append' => $chf->append ?? null,
          'series' => $chf->series ?? null,
          'isbn' => $chf->isbn ?? null,
          'isbn_locked' => $ch['isbn_locked'] ?? null,
          'name' => 'chf',
          'message' => $ch['message'] ?? null,
          'description' => $ch['description'] ?? null,
          'status' => $ch['status'] ?? null,
          'url' => $ch['url'] ?? null,
          'url_back' => $ch['url_back'] ?? null,
          'photo' => $ch['photo'] ?? null,
          'link' => $ch['link'] ?? null,
          'decision_link' => $ch['decision_link'] ?? null,
          'format' => $ch['format'] ?? null,
          'comment' => $ch['comment'] ?? null,
          'need_chb' => $ch['need_chb'] ?? null,
          'chb_uploaded' => $ch['chb_uploaded'] ?? null,
          'link_back' => $ch['link_back'] ?? null,
          'is_readable' => $ch['is_readable'] ?? null,
        ]);
      }
    }

    $chequesInfo = [
      'cheques' => $details,
      'count' => $order->cheques,
      'amount' => $order->ghest,
      'total' => $order->ghest * $order->cheques,
      'prepend' => $first_chf->prepend ?? null,
      'append' => $first_chf->append ?? null,
      'guarantee' => round($order->total * config('globals.order.guarantee_ratio') / 100000) * 100000,
      'name' => config('globals.cheque_to'),
      'nic' => config('globals.cheque_to_nic'),
      'iban' => null,
      'branch_name' => null,
      'branch_code' => null,
    ];

    if ($account_id && $order->payback_type == 'cheque') {
      $chequesInfo['bank_id'] = $account->bank_id;
      $chequesInfo['iban'] = $account->iban;
      $chequesInfo['branch_name'] = $account->branch_name;
      $chequesInfo['branch_code'] = $account->branch_code;
    }

    if (User::is_admin()) $chequesInfo['reject_options'] = Doc::reject_options;

    return $chequesInfo;
  }

  public static function generate_ghests($order)
  {
    $order_id = $order->id;
    Ghest::where(['order_id' => $order_id])->delete();

    $info = self::cheques_info($order);
    foreach ($info['cheques'] as $ch) {
      $arr[] = [
        'order_id' => $order_id,
        'amount' => $ch['amount'],
        'ghest_date' => Edate::j2carbon($ch['date']),
        'shamsi' => str_replace('-', '', $ch['date']),
        'type' => $order->payback_type,
      ];
    }

    if (Ghest::insert($arr)) return true;
    return false;
  }

  public static function match_this_organ($order)
  {
    $user_id = Auth::user()->id;
    $organ_id = Organ::where('user_id', $user_id)->first()->id;
    if ($order->organ_id == $organ_id) return true;
    return false;
  }

  public static function match_this_shop($order)
  {
    $user_id = Auth::user()->id;
    $shop_id = Shop::where('user_id', $user_id)->first()->id;
    if ($order->shop_id == $shop_id) return true;
    return false;
  }

  public static function is_pended_by_organ($order)
  {
    return self::status_fixer($order) == 'pended_by_organ';
  }

  public static function required_credit($order)
  {
    return $order->cheques * $order->ghest;
  }

  public static function cancel_left_upload_secondary()
  {
    Order::uploadSecondary()->where('docs_accepted_at', '<', Carbon::now()->subMonth())
      ->update(['status' => 'cancelled', 'closed_at' => Carbon::now()]);
    return true;
  }

  public function scopeSearch($query, $request, $role = null)
  {
    if (empty($request->sort)) {
      $sort_by = 'id';
      $sort_order = 'desc';
    } else {
      $sort = explode('-', $request->sort);
      $sort_by = $sort[0];
      $sort_order = $sort[1];
    }
    if ($request->fast_status) {
      $x = $query->searchStatus($request->fast_status);
    } else {
      $x = $query
        ->searchDate($request->date_type, $request->from_date, $request->to_date)
        ->searchStatus($request->status)
        ->searchTrait($request->trait)
        ->searchAmount($request->min, $request->max)
        ->searchLike('oid', $request->oid)
        ->searchExact('series', $request->series)
        ->searchExact('series_card', $request->series_card)
        ->searchExact('amount', $request->amount)
        ->searchLike('product', $request->name)
        ->searchLike('username', substr($request->mobile, -10), 'user')
        ->searchLike('fname', $request->fname, 'user')
        ->searchLike('lname', $request->lname, 'user')
        ->searchLike('nid', $request->nid, 'user');
      if ($role == 'admin') {
        $x
          ->searchExact('series', $request->series)
          ->searchOrgan($request->organ)
          ->searchLike('name', $request->shop, 'shop');
      }
    }
    return $x->orderBy($sort_by, $sort_order);
  }

  public function scopeSearchStatus($query, $status = null)
  {
    if (empty($status) || $status == 'all') return $query;
    switch ($status) {
      case 'pended_by_organ':
        return $query->pendedByOrgan();
        break;
      case 'worthless':
        return $query->worthless();
        break;
      case 'cycle_cheque':
        return $query->cycleCheque();
        break;
      case 'cycle_epay':
        return $query->cycleEpay();
        break;
      default:
        return $query->where('status', $status);
        break;
    }
  }

  public function scopeSearchTrait($query, $trait = null)
  {
    if (empty($trait) || $trait == 'all') return $query;
    switch ($trait) {
      case 'discounty':
        return $query->whereHas('payments', function ($query) {
          $query->whereNotNull('discount_id');
        });

      case 'extra_charged':
        return $query->whereIn('status', ['cycle', 'completed'])->whereHas('payments', function ($query) {
          $query->where(['status' => 100, 'type' => 'extra'])->whereNotNull('charged_at');
        });

      case 'organy':
        return $query->whereNotNull('organ_id');

      case 'shopy':
        return $query->whereNotNull('shop_id');

      case 'epay':
        return $query->where('payback_type', 'epay');

      case 'cheque':
        return $query->where('payback_type', 'cheque');

      case 'hastinja':
        return $query->where('tag', 'like', 'hi-%');

      default:
        return $query;
    }
  }

  public function scopeSearchAmount($query, $min = null, $max = null)
  {
    if (empty($min) && empty($max)) {
      return $query;
    } else if (empty($min)) {
      return $query->where('amount', '<=', (int)$max);
    } else if (empty($max)) {
      return $query->where('amount', '>=', (int)$min);
    } else {
      return $query->whereBetween('amount', [(int)$min, (int)$max]);
    }
  }

  public function scopeSearchOrgan($query, $organ = null)
  {
    if (empty($organ)) return $query;
    if (is_numeric($organ)) {
      return $query->whereHas('organ', function ($query) use ($organ) {
        $query->where('code', $organ);
      });
    }
    return $query->whereHas('organ', function ($query) use ($organ) {
      $query->where('name', 'like', '%' . $organ . '%')->orWhere('fame', 'like', '%' . $organ . '%');
    });
    return false;
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
