<?php

namespace App\Http\Controllers;

use App\Models\Edate;
use App\Models\Useful;
use App\Models\Account;
use App\Models\Address;
use App\Models\Card;
use App\Models\Charge;
use App\Models\Cheque;
use App\Models\Doc;
use App\Models\Finnotech;
use App\Models\Ghest;
use App\Models\ICBS;
use App\Models\Order;
use App\Models\Organ;
use App\Models\Pattern;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Shop;
use App\Models\Sms;
use App\Models\User;
use App\Models\Usermeta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class OrderController extends Controller
{
  public function __construct()
  {
  }

  public function index(Request $request)
  {
    $uri = explode('/', Route::current()->uri);
    $role = $uri[1];
    $export = end($uri) == 'export' ? 1 : null;
    $status = $request->status;
    $dateType = $request->date_type;
    if (empty($dateType)) {
      switch ($status) {
        case 'docs_uploaded':
          $dateType = 'docs_uploaded_at';
          break;
        case 'upload_secondary':
          $dateType = 'docs_accepted_at';
          break;
        case 'check_secondary':
          $dateType = 'secondary_uploaded_at';
          break;
        case 'wait_for_cheques':
          $dateType = 'secondary_accepted_at';
          break;
        case 'prepayment':
          $dateType = 'docs_received_at';
          break;
        case 'prepaid':
          $dateType = 'prepaid_at';
          break;
        case 'cycle_cheque':
        case 'cycle_epay':
        case 'completed':
          $dateType = 'charged_at';
          break;
        default:
          $dateType = 'created_at';
          break;
      }
    }
    $request->date_type = $dateType;
    $sort = $request->sort;
    if (empty($sort)) $sort = 'desc';
    $query = Order::query();

    switch ($role) {
      case 'shop':
        $shop_id = Shop::select('id')->where('user_id', Auth::user()->id)->first()->id;
        $query->where('shop_id', $shop_id);
        break;
      case 'organ':
        $organ_id = Organ::select('id')->where('user_id', Auth::user()->id)->first()->id;
        $query->where('organ_id', $organ_id);
        break;
      case 'admin':
      default:
        break;
    }

    $query->search($request, $role)->with([
      'user' => function ($query) {
        $query->select('id', 'fname', 'lname', 'username', 'nid', 'birth', 'badge', 'utm_source', 'is_admin')->withTrashed();
      },
      'organ' => function ($query) {
        $query->select('id', 'type', 'name', 'fame')->withTrashed();
      },
      'shop' => function ($query) {
        $query->select('id', 'type', 'name')->withTrashed();
      }
    ]);

    $orders = $export ? $query->get() : $query->paginate(21);

    foreach ($orders as $order) {
      /* if (!$order->user)
        return Useful::nok(['user_not_found', $order->id]);
      $order->user->roles = User::roles($order->user); */
      $order->status = Order::status_fixer($order);
      $order->status_farsi = Order::$status[$order->status];
      $order = Order::ghestify($order);
      if ($role == 'admin') $order->completed_orders_count = Order::where(['user_id' => $order->user_id])->completed()->count();

      switch ($order->status) {
        case 'docs_uploaded':
          $order->date_index_label = "تاریخ بارگذاری مدارک";
          $order->date_index_value = $order->docs_uploaded_at;
          break;
        case 'upload_secondary':
          $order->date_index_label = "تاریخ تأیید مدارک";
          $order->date_index_value = $order->docs_accepted_at;
          break;
        case 'check_secondary':
          $order->date_index_label = "تاریخ بارگذاری مدارک تکمیلی";
          $order->date_index_value = $order->secondary_uploaded_at;
          break;
        case 'wait_for_cheques':
          $order->date_index_label = "تاریخ تأیید مدارک تکمیلی";
          $order->date_index_value = $order->secondary_accepted_at;
          break;
        case 'prepayment':
          $order->date_index_label = "تاریخ تحویل مدارک";
          $order->date_index_value = $order->docs_received_at;
          break;
        case 'prepaid':
          $order->date_index_label = "تاریخ پیش پرداخت";
          $order->date_index_value = $order->prepaid_at;
          break;
        case 'cycle_cheque':
        case 'cycle_epay':
        case 'completed':
          $order->date_index_label = "تاریخ شارژ";
          $order->date_index_value = $order->charged_at;
          if ($export) {
            $ghests = Ghest::select('ghest_date')->where('order_id', $order->id)->orderBy('ghest_date', 'asc')->get();
            $ghests = $ghests->toArray();
            if (count($ghests) < 1) return $order->id;
            $order->first_ghest_at = $ghests[0]['ghest_date'];
            $last_index = count($ghests) - 1;
            $order->last_ghest_at = $ghests[$last_index]['ghest_date'];
          }
          break;
        case 'cancelled':
        case 'rejected':
          $order->date_index_label = "تاریخ رد/لغو";
          $order->date_index_value = $order->closed_at;
          break;
        case 'submitted':
        default:
          $order->date_index_label = "تاریخ ثبت سفارش";
          $order->date_index_value = $order->created_at;
          break;
      }
    }

    if ($export) return $this->export($orders);
    return $orders;
  }

  public function export($orders)
  {
    $data[0] = [
      'ردیف',
      'موبایل',
      'نام کاربر',
      'شماره سفارش',
      'وضعیت',
      'سبد خرید',
      'پیش پرداخت',
      'تعداد ماه',
      'تعداد قسط',
      'پاس شده',
      'هر قسط',
      'کل بازپرداخت',
      'بهره',
      'سازمان',
      'فروشگاه',
      'سفارشهای پایان یافته',
      'معرف',
      'سری کارت',
      'سری شارژ',
      'تاریخ ثبت',
      'تاریخ بارگذاری مدارک',
      'تاریخ تأیید مدارک',
      'تاریخ تحویل مدارک',
      'تاریخ پیش پرداخت',
      'تاریخ شارژ',
      'تاریخ اولین قسط',
      'تاریخ آخرین قسط',
    ];

    $n = 1;
    foreach ($orders as $order) {

      $data[$n] = [
        $n,
        $order->user->username,
        $order->user->fname . " " . $order->user->lname,
        $order->oid,
        $order->status_farsi,
        $order->amount,
        $order->prepayment,
        $order->months,
        $order->cheques,
        $order->passed,
        $order->ghest,
        $order->total,
        $order->gain,
        $order->organ_id ? ($order->organ->fame ? $order->organ->fame : $order->organ->name) : '',
        $order->shop_id ? $order->shop->name : '',
        $order->completed_orders_count,
        $order->user->utm_source,
        $order->series_card,
        $order->series,
        Edate::edateFromCarbon('Y-m-d - H:i', $order->created_at),
        Edate::edateFromCarbon('Y-m-d - H:i', $order->docs_uploaded_at),
        Edate::edateFromCarbon('Y-m-d - H:i', $order->docs_accepted_at),
        Edate::edateFromCarbon('Y-m-d - H:i', $order->docs_received_at),
        Edate::edateFromCarbon('Y-m-d - H:i', $order->prepaid_at),
        Edate::edateFromCarbon('Y-m-d - H:i', $order->charged_at),
        in_array($order->status, ['cycle', 'completed']) ? Edate::edateFromCarbon('Y-m-d', $order->first_ghest_at) : '',
        in_array($order->status, ['cycle', 'completed']) ? Edate::edateFromCarbon('Y-m-d', $order->last_ghest_at) : '',
      ];
      ++$n;
    }

    $url = ExportController::excel($data, 'orders');
    return Useful::ok(['url' => $url], ['action_type' => 'open_url']);
  }

  public function my_orders()
  {
    $user = Auth::user();
    $Orders = Order::where('user_id', $user->id)->with([
      'organ' => function ($query) {
        $query->select('id', 'type', 'name', 'fame');
      },
      'shop' => function ($query) {
        $query->select('id', 'type', 'name');
      }
    ])->orderBy('id', 'desc')->get();
    foreach ($Orders as $order) {
      $order->status = Order::status_fixer($order);
      $order->status_farsi = Order::$status[$order->status];
      $order = Order::ghestify($order);
      $order->completed_orders_count = Order::where(['user_id' => $order->user_id])->completed()->count();
    }
    return Useful::ok($Orders);
  }

  public function single(Request $request)
  {
    $uri = Route::current()->uri;
    $role = explode('/', $uri)[1];
    $oid = $request->oid;

    if ($oid == 'last' || $oid == 'recent') {
      $user = Auth::user();
      $query = Order::where('user_id', $user->id)->worthy()->orderBy('id', 'desc');
    } else {
      $order = Order::oid($oid);
      $user = User::whereId($order->user_id)->withTrashed()->first();
      $query = Order::where(['oid' => $oid]);
    }
    $order = $query->with([
      'user' => function ($query) {
        $query->withTrashed();
      },
      'payments' => function ($query) {
        $query->select('id', 'order_id', 'amount', 'type', 'status', 'ref_id', 'discount_id', 'paid_at')->orderBy('id', 'desc');
      },
      'payments.discount' => function ($query) {
        $query->select('id', 'code', 'amount');
      },
      'organ' => function ($query) {
        $query->select('id', 'type', 'name', 'fame', 'level', 'payback_type');
      },
      'shop' => function ($query) {
        $query->select('id', 'type', 'name');
      }
    ])->first();

    if (!$order) return Useful::ok();

    $this->guard($order);

    $order->user->roles = User::roles($order->user);
    $order->user->addresses = Address::index($order->user_id);
    $order->status = self::review_docs($order)[0]['new_status'];
    $order->status = Order::status_fixer($order);

    if ($order->status == 'draft' && $order->user->nid && $order->user->birth) {
      $orders_in_month = Order::select('id')->where('user_id', $order->user_id)
        ->where('created_at', '>', Carbon::now()->subDays(ICBS::expiration_days))->get();
      foreach ($orders_in_month as $oim) $oarr[] = $oim->id;

      $pay = Payment::whereIn('order_id', $oarr)->where(['type' => 'inquiry', 'status' => 100])->first();
      if ($pay) $order->update(['inquiry_paid_at' => $pay->paid_at]);

      if (config('app.env') == 'production') {
        $invoice = self::invoice_inquiry_costs($order->user);
        if ($invoice['total'] === 'SIM') {
          if ($order->shop_id)
            Sms::send(
              Shop::mobile($order->shop_id),
              Pattern::order_rejected_inq_sim_seller,
              ['oid' => $order->oid, 'mobile' => $order->user->username]
            );

          $order->update([
            'status' => 'rejected',
            'closed_at' => Carbon::now(),
            'reason' => 'مالکیت این شماره موبایل و کدملی کاربر با هم تطابق ندارد.'
          ]);

          if (Order::where('user_id', $order->user_id)->worthy()->count() == 0 && count($order->user->roles) == 1) {
            $user->delete();
            Sms::send($order->user->username, Pattern::order_rejected_inq_sim);
          } else
            Sms::send($order->user->username, Pattern::order_rejected_inq_sim_xp);

          $order->status = 'rejected';
        } else if ($invoice['total'] === 'REG') {
          $order->invoice = $invoice;
        } else if ($invoice['total'] === 'BCH') {
          Sms::send($order->user->username, Pattern::order_rejected_bch, ['oid' => $order->oid]);
          $order->update([
            'status' => 'rejected',
            'closed_at' => Carbon::now(),
            'reason' => 'طبق استعلام سیستمی، کاربر دارای چک برگشتی در شبکه بانکی است.'
          ]);
          $order->status = 'rejected';
        } elseif ($invoice['total'] > 0) {
          $order->invoice = $invoice;
        } else {
          $order->update(['status' => 'submitted']);
          $order->status = 'submitted';
        }
      } else {
        $order->update(['status' => 'submitted']);
        if ($order->shop_id) {
          Sms::send(
            $user->username,
            Pattern::user_order_by_shop_submitted,
            [
              'name' => $user->fname,
              'oid' => $oid,
              'product' => $order->product,
              'shop' => Shop::title($order->shop_id),
              'amount' => number_format($order->amount),
              'prepayment' => number_format($order->prepayment),
              'months' => $order->months,
              'cheques' => $order->cheques,
              'total' => number_format($order->total),
              'ghest' => number_format($order->ghest),
              'phone' => config('globals.phone'),
            ]
          );
          Sms::send(
            $user->username,
            Pattern::user_order_by_shop_cta_docs,
            [
              'name' => $user->fname,
            ]
          );
        } else {
          Sms::send(
            $user->username,
            Pattern::user_order_submitted,
            [
              'name' => $user->fname,
              'oid' => $oid,
            ]
          );
        }
        $order->status = 'submitted';
      }
    }

    if ($order->status == 'submitted') {
      if (empty($order->user->nid) || empty($order->user->birth)) {
        $order->update(['status' => 'draft']);
        return Useful::ok(null, ['force_update']);
      }

      if (Address::where('user_id', $order->user_id)->where('type', 'home')->doesntExist())
        $order->show_address_form = true;

      if (!$order->show_address_form)
        $order->rt = [
          'rabbit' => [
            'time' => '1 روزه',
            'price' => '10000'
          ],
          'turtle' => [
            'time' => '3 روزه',
            'price' => '30000'
          ],
        ];
    }

    if ($role == 'organ' && $order->status == 'pended_by_organ') {
      $summary = OrganController::summary($order->organ_id);
      $data = [
        'total' => $summary->total,
        'locked' => $summary->locked,
        'available' => $summary->available,
        'expired' => $summary->expired,
      ];
      if ($summary->available_extra < ($order->total - $order->prepayment)) {
        $order->cta = [
          'ok' => false,
          'description' => 'متأسفانه به دلیل کافی نبودن اعتبار سازمان، این سفارش قابل تأیید نمی باشد',
          'data' => $data
        ];
      } else if ($summary->expired < 0) {
        $order->cta = [
          'ok' => false,
          'description' => 'متأسفانه به دلیل پرداخت نشدن اقساط سایر سفارش ها، این سفارش قابل تأیید نمی باشد',
        ];
      } else {
        $order->cta = [
          'ok' => true,
          'description' => 'چنانچه این سفارش مورد پذیرش سازمان است، آن را تأیید کنید تا در فرآیند بررسی قرار بگیرد',
          'data' => $data,
          'method' => 'post',
          'url_accept' => 'organ/orders/' . $order->oid . '/accept',
          'url_reject' => 'organ/orders/' . $order->oid . '/reject'
        ];
      }
    }

    $order = Order::ghestify($order);

    if ($order->status === 'upload_secondary' && $order->payback_type === 'cheque') {
      $cheques = Cheque::where(['order_id' => $order->id, 'type' => 'chf', 'is_submitted' => 1])
        ->whereNotNull('series')->whereNull('decided_at')->orderBy('badge', 'asc')->get();

      $chbs = Cheque::where(['order_id' => $order->id, 'type' => 'chb'])->orderBy('badge', 'asc')->get();

      $do = 1;
      foreach ($cheques as $chx) {
        if ($chx->need_chb === 1 && !$chbs->where('series', $chx->id)->first()) {
          $do = 0;
          break;
        }
      }

      if ($cheques->count() === (int) $order->cheques && $do === 1) {
        $order->update([
          'status' => 'check_secondary',
          'secondary_uploaded_at' => Carbon::now(),
          'secondary_accepted_at' => null
        ]);

        $order->secondary_uploaded_at = Carbon::now();
        $order->status = 'check_secondary';
      }
    }

    $order->first_ghest = $order->first_ghest_at ? Edate::edateFromCarbon('Y-m-d', $order->first_ghest_at) : null;

    $order->ghestacard = Card::ghestacard($order->user_id);

    if ($order->status === 'wait_for_cheques') {
      $order->ghesta_address = 'نشانی: ' . config('globals.address') . ' - کد پستی: ' . config('globals.postal_code');
      if ($order->shop_id) {
        $scope = 'shop_' . $order->shop->type;
      } else if ($order->organ_id) {
        $scope = 'organ_' . $order->organ->level . '_' . $order->organ->payback_type;
      } else {
        $scope = 'user';
      }
      $docs_for_send = '';
      foreach (Doc::$order['delivery'][$scope] as $type) {
        $docs_for_send .= Doc::$types[$type]['item'] . ' ، ';
      }
      $message = "قرارداد امضاشده را به همراه <strong>$docs_for_send</strong> به آدرس ما ارسال نمایید.";
      if (Address::select('state')->where('user_id', $order->user_id)->where('type', 'home')->orderBy('id', 'desc')->first()->state == 7) {
        if (in_array($order->ghestacard['status'], ['printing']))
          $message .= "<br/> <small>با توجه به اینکه قسطاکارت شما از سمت بانک صادرکننده هنوز به دست ما نرسیده لطفاً از مراجعه حضوری خودداری فرمایید. بعد از صدور قسطاکارت می توانید با هماهنگی، موارد ذکرشده را تحویل داده و قسطاکارت خود را دریافت نمایید.</small>";
        else
          $message .= "<br/> <small>چنانچه قصد مراجعه حضوری دارید، حتماً با پشتیبانی " . config('globals.phone') . " داخلی 1 هماهنگی بفرمایید.</small>";
      }
      $order->send_docs_cta = $message;
    }

    if (in_array($order->status, ['prepayment', 'prepaid', 'cycle_cheque', 'cycle_epay', 'extra_charge', 'completed'])) {
      $pay = Payment::select('id', 'ref_id')->where('order_id', $order->id)->prepayment()->successful()->first();
      if (!$pay && $order->prepayment == 0) {
        $pay = Payment::create([
          'order_id' => $order->id,
          'type' => 'prepayment',
          'paid_at' => $order->charged_at ? $order->charged_at : Carbon::now(),
          'amount' => 0,
          'ref_id' => 1,
          'status' => 100,
        ]);
        Order::whereId($order->id)->update([
          'prepaid_at' => $order->charged_at ? $order->charged_at : Carbon::now()
        ]);
      }
      if ($order->status != 'prepayment') {
        $ref_id = $pay->ref_id;
        if ($ref_id < 2) $order->prepaid_type = 'offline';
      }
    }

    if ($order->status == 'prepayment') {
      if ($order->ghestacard['status'] == 'delivered') {
        if ($order->prepayment == 0) {
          Order::whereId($order->id)->update([
            'status' => 'prepaid',
            'prepaid_at' => Carbon::now()
          ]);
          $order->status = 'prepaid';
        }
      } else if (!$order->shop_id) {
        $order->status = 'wait_for_card';
        $order->status_farsi = Order::$status['wait_for_card'];
        $order->what_to_do = $order->ghestacard['what_to_do'];
      }

      $order->final_invoice = PaymentController::final_invoice($order);
    }

    if ($order->shop_id) $order->ghestacard = null;

    if ($role == 'admin') {
      switch ($order->status) {
        case 'docs_uploaded':
          $order->cta = [
            'status' => 'info',
            'text' => 'ارتقای وضعیت سفارش',
            'description' => 'در صورتی که تمامی مدارک را اعتبارسنجی کرده اید، روی دکمه زیر کلیک کنید تا سفارش وارد مرحله بعدی (بارگذاری تصویر تضامین و...) شود',
            'method' => 'post',
            'url' => 'admin/orders/' . $order->oid . '/change/upload_secondary'
          ];
          break;
        case 'upload_secondary':
          $order->cta = [
            'status' => 'info',
            'text' => 'بازگشت به صف اعتبارسنجی',
            'description' => 'اگر اشتباهاً وارد این مرحله شده، می توانید با زدن دکمه زیر، سفارش را به مرحله قبل بازگردانید',
            'method' => 'post',
            'url' => 'admin/orders/' . $order->oid . '/change/rollback_docs_uploaded'
          ];
          break;
        case 'check_secondary':
          $order->cta = [
            'status' => 'info',
            'text' => 'ارتقای وضعیت سفارش',
            'description' => 'در صورتی که مدارک و تضامین لازم بارگذاری شده، روی دکمه زیر کلیک کنید تا به مشتری اطلاع دهیم این مدارک را ارسال کند.',
            'method' => 'post',
            'url' => 'admin/orders/' . $order->oid . '/change/wait_for_cheques',
          ];
          break;
        case 'wait_for_cheques':
          $order->cta = [
            'status' => 'info',
            'text' => 'مدارک را تحویل گرفتم',
            'description' => 'در صورتی که تمامی مدارک (تضامین و...) را تحویل گرفته اید، روی دکمه زیر کلیک کنید تا سفارش وارد مرحله بعدی (پیش پرداخت) شود',
            'method' => 'post',
            'url' => 'admin/orders/' . $order->oid . '/change/prepayment',
            'text2' => 'بازگشت به مرحله بررسی تضامین',
            'url2' => 'admin/orders/' . $order->oid . '/change/rollback_check_secondary'
          ];
          break;
        case 'prepayment':
          $order->cta = [
            'status' => 'info',
            'text' => 'غیر اینترنتی پرداخت شد',
            'description' => 'در صورتی که کاربر پیش پرداخت را بصورت غیر اینترنتی پرداخت کرده، روی دکمه زیر کلیک کنید.',
            'method' => 'post',
            'url' => 'admin/orders/' . $order->oid . '/payment/prepayment',
            'text2' => 'بازگشت به مرحله درانتظارچک',
            'url2' => 'admin/orders/' . $order->oid . '/change/rollback_wait_for_cheques'
          ];
          break;
        case 'prepaid':
          /* $order->cta = [
            'status' => 'info',
            'text' => 'شارژ انجام شد',
            'method' => 'post',
            'url' => 'admin/orders/' . $order->oid . '/change/cycle'
          ]; */
          $order->cta = [
            'text' => 'بررسی و شارژ قسطاکارت',
            'description' => 'برای شارژ قسطاکارت روی دکمه زیر کلیک کنید و اطلاعات لازم را بررسی کنید',
            'type' => 'modal',
            'modalName' => 'orderChargeModal',
            'data' => $this->charging_info($order),
            'url' => 'admin/orders/' . $order->oid . '/charge',
            'method' => 'post',
          ];
          break;
        case 'cycle_cheque':

          break;
        case 'cycle_epay':

          break;
        case 'extra_charge':
          $order->cta = [
            'status' => 'info',
            'text' => 'شارژ اضافی انجام شد',
            'description' => 'در صورتی که قسطاکارت مشتری را شارژ کرده اید، روی دکمه زیر کلیک کنید:',
            'method' => 'post',
            'url' => 'admin/orders/' . $order->oid . '/change/extra_charged'
          ];
          break;
        case 'completed':

          break;
        case 'rejected':
        case 'cancelled':
          $order->cta = [
            'status' => 'info',
            'text' => 'بازیابی سفارش',
            'description' => 'در صورتی که سفارش باید مجدداً وارد فرآیند شود، برای بازیابی روی دکمه زیر کلیک کنید:',
            'method' => 'post',
            'url' => 'admin/orders/' . $order->oid . '/change/recovery'
          ];
          break;
      }
    }

    $order->all_dates = [
      [
        'title' => "ثبت سفارش",
        'date' => $order->created_at,
      ],
      [
        'title' => "بارگذاری مدارک",
        'date' => $order->docs_uploaded_at,
      ],
      [
        'title' => "مهلت بررسی مدارک",
        'date' => $order->docs_warning_at,
      ],
      [
        'title' => "تأیید مدارک",
        'date' => $order->docs_accepted_at,
      ],
      [
        'title' => "بارگذاری مدارک تکمیلی",
        'date' => $order->secondary_uploaded_at,
      ],
      [
        'title' => "تأیید مدارک تکمیلی",
        'date' => $order->secondary_accepted_at,
      ],
      [
        'title' => "تحویل مدارک",
        'date' => $order->docs_received_at,
      ],
      [
        'title' => "پیش پرداخت",
        'date' => $order->prepaid_at,
      ],
      [
        'title' => "شارژ",
        'date' => $order->charged_at,
      ],
      [
        'title' => "موعد اولین قسط",
        'date' => $order->first_ghest_at,
      ],
      [
        'title' => "لغو",
        'date' => $order->closed_at,
      ]
    ];

    $order->status_farsi = Order::$status[$order->status];

    //What To Do
    switch ($order->status) {
      case 'draft':
        $order->what_to_do = '';
        $order->date_index_label = "تاریخ ثبت سفارش";
        $order->date_index_value = $order->created_at;
        break;
      case 'submitted':
        if (($order->show_nid_form || $order->show_birth_form || $order->show_address_form) && $order->shop_id)
          $order->what_to_do = 'قبل از بارگذاری مدارک نیاز به تکمیل اطلاعات مشتری است. اگر با کامپیوتر یا دسکتاپ هستید، کادر قرمز سمت چپ (تکمیل اطلاعات مشتری) را ببینید، اگر با موبایل هستید، این کادر را بالای صفحه می یابید.';
        else
          $order->what_to_do = 'سفارش شما ثبت شده، اما برای ادامه فرآیند می بایست مدارک مورد نیاز را بارگذاری کنید.';
        $order->date_index_label = "تاریخ ثبت سفارش";
        $order->date_index_value = $order->created_at;
        break;
      case 'docs_uploaded':
        $order->what_to_do = 'لطفاً منتظر نتیجه اعتبارسنجی سفارش خود باشید.';
        $order->date_index_label = "تاریخ بارگذاری مدارک";
        $order->date_index_value = $order->docs_uploaded_at;
        break;
      case 'upload_secondary':
        $order->what_to_do = 'لطفاً تصویر تضامین لازم (چک/سفته/...) را در صفحه سفارش بارگذاری کنید.';
        $order->date_index_label = "تاریخ تأیید مدارک";
        $order->date_index_value = $order->docs_accepted_at;
        break;
      case 'check_secondary':
        $order->what_to_do = 'لطفاً منتظر بمانید تا کارشناسان قسطا، تصاویر بارگذاری شده را بررسی کنند.';
        $order->date_index_label = "تاریخ بارگذاری مدارک تکمیلی";
        $order->date_index_value = $order->secondary_uploaded_at;
        break;
      case 'wait_for_cheques':
        $order->what_to_do = 'تصویر تضامین تأیید شد.';
        $order->date_index_label = "تاریخ تأیید مدارک تکمیلی";
        $order->date_index_value = $order->secondary_accepted_at;
        break;
      case 'prepayment':
        $order->what_to_do = 'جهت نهایی شدن سفارش و شارژ قسطاکارت، لطفاً پیش پرداخت را پرداخت کنید. در نظر داشته باشید چنانچه پیش پرداخت را تا ساعت 11 روز کاری پرداخت نمایید، قسطاکارت شما حداکثر تا ساعت 18 همان روز شارژ خواهدشد؛ در غیر این صورت، شارژ در اولین روز کاری بعد انجام خواهدشد.';
        $order->date_index_label = "تاریخ تحویل مدارک";
        $order->date_index_value = $order->docs_received_at;
        break;
      case 'prepaid':
        $order->what_to_do = 'پیش پرداخت سفارش شما تأیید شد و در صف شارژ قسطاکارت قرار گرفت.';
        $order->date_index_label = "تاریخ پیش پرداخت";
        $order->date_index_value = $order->prepaid_at;
        break;
      case 'cycle_cheque':
        $order->what_to_do = 'سفارش شما در دوره اقساط قرار دارد و چک های شما در موعد مقرر وصول خواهدشد.';
        $order->date_index_label = "تاریخ شارژ";
        $order->date_index_value = $order->charged_at;
        break;
      case 'cycle_epay':
        $order->what_to_do = 'سفارش شما در دوره اقساط قرار دارد و می توانید از اینجا اقساط خود را پرداخت نمایید.';
        $order->date_index_label = "تاریخ شارژ";
        $order->date_index_value = $order->charged_at;
        break;
      case 'extra_charge':
        $order->what_to_do = 'در صف شارژ اضافی قسطاکارت';
        $order->date_index_label = "تاریخ شارژ";
        $order->date_index_value = $order->charged_at;
        break;
      case 'completed':
        $order->what_to_do = 'اقساط سفارش شما پایان یافته :)';
        $order->date_index_label = "تاریخ شارژ";
        $order->date_index_value = $order->charged_at;
        break;
      case 'cancelled':
        $order->what_to_do = 'این سفارش توسط شما لغو شده است.';
        $order->date_index_label = "تاریخ لغو";
        $order->date_index_value = $order->closed_at;
        break;
      case 'rejected':
        $order->what_to_do = 'این سفارش تأیید نشده است.';
        $order->date_index_label = "تاریخ رد";
        $order->date_index_value = $order->closed_at;
        break;
    }

    /* $order->step = [
      [
        'title' => '',
        'status' => ''
      ]
    ]; */

    if ($order->tag) {
      $exp = explode('-', $order->tag);
      $ord_id = $exp[1];
      $order->hastinja = DB::connection('hastinja')->table('orders')->where('id', $ord_id)->first();
      $order->hastinja->status = Order::status_fixer($order->hastinja);
      $order->hastinja->status_farsi = Order::$status[$order->hastinja->status];
    }

    /* if (in_array($order->status, ['submitted', 'docs_uploaded'])) {
      $order->important_msg = [
        'color' => 'warning',
        'text' => 'همراهان گرامی قسطا در نظر داشته باشید به علت ترافیک کاری آخر سال سفارش‌هایی که  ثبت و یا بارگذاری مدارک شان بعد از تاریخ 22 اسفند انجام شود، فرایند اعتبار سنجی آن‌ها بعد از 14 فروردین انجام خواهد شد.',
      ];
    } */

    Auth::user()->username == 9109270876 ? $order->debugger = 1 : $order->debugger = null;

    return Useful::ok($order);
  }

  public function charging_info($order)
  {
    $amount = $order->amount;
    $res['amount'] = $amount;
    $com = 0;
    if ($order->shop_id) {
      $res['mode'] = 'shop';
      $shop = Shop::query()
        ->select('id', 'user_id', 'name', 'type', 'domain', 'commission_default', 'commission_percent', 'commission_amount', 'start_at')
        ->where('id', $order->shop_id)->first();
      $user = User::info($shop->user_id);
      $res['description'] = "این سفارش توسط فروشگاه <strong>«" . $shop->name . "»</strong> ثبت شده، پس قسطاکارت فروشنده شارژ خواهد شد.";
      if (Carbon::parse($shop->start_at)->timestamp < Carbon::now()->subMonth()->timestamp) {
        if ($shop->commission_default == 1) {
          $gv = Setting::select('prop', 'val')->whereIn('prop', ['commission_shop', 'commission_shop_ceil'])->get();
          $commission_percent = $gv[0]->val;
          $commission_ceil = $gv[1]->val;
          $res['x'] = $commission_percent . ' x ' . $commission_ceil;
        } else {
          $commission_percent = $shop->commission_percent;
          $commission_ceil = $shop->commission_amount;
        }
        $com = $amount * ($commission_percent / 100);
        if ($com > $commission_ceil) $com = $commission_ceil;
        $res['commission'] = $com;
        $res['amount_to_charge'] = $amount - $com;
      }
    } else {
      $res['mode'] = 'user';
      $user = User::info($order->user_id);
    }
    $res['commission'] = $com;
    $res['amount_to_charge'] = $amount - $com;
    $card = Card::where('user_id', $user->id)->orderBy('id', 'desc')->whereNotNull('delivered_at')->first();

    if (!$card)
      $res['description'] = "فروشنده هنوز قسطاکارت خود را دریافت نکرده است.";
    else if (substr($card->card_number, 0, 4) != '6362')
      $res['description'] = "این کارت متعلق به بانک آینده نیست و باید بصورت آفلاین شارژ شود.";

    $res['card_number'] = $card ? $card->card_number : null;
    $res['card_id'] = $card ? $card->id : null;
    $res['name'] = $user->full_name;
    $res['mobile'] = $user->mobile;

    return $res;
  }

  //SALAR
  public function charge(Request $request)
  {
    $operator = Auth::user();
    if ($operator->is_admin < 15)
      return Useful::nok(['dont dont', 'شما اجازه این کار را ندارید!']);

    $oid = $request->oid;
    $mode = $request->mode;
    $order = Order::where('oid', $oid)->first();
    $charge = (object) $this->charging_info($order);

    if ($chargeRecord = Charge::create([
      'type' => 'charge',
      'card_id' => $charge->card_id,
      'order_id' => $order->id,
      'created_by' => Auth::user()->id,
      'amount' => $charge->amount_to_charge,
      'commission' => $charge->commission,
      'deposit' => Finnotech::$DEPOSIT_ID
    ])) {
      if (in_array($mode, ['offline', 'manual'])) {
        $order->update(['charge_id' => $chargeRecord->id]);
        $chargeRecord->update([
          'tracker' => $mode,
          'track_id' => null,
          'charged_at' => Carbon::now(),
          'status' => 1,
        ]);

        //Change Order Status 
        $user = User::info($order->user_id);
        if ($order->payback_type == 'epay') {
          $gen = Order::generate_ghests($order);
          if (!$gen)
            return Useful::nok(['dbgh-ins', 'خطایی رخ داده. با مدیر فنی تماس بگیرید.']);
        }

        $order->update([
          'series' => $request->series,
          'status' => 'cycle',
          'charged_at' => Carbon::now(),
          'charge_type' => 'شارژ'
        ]);

        return Useful::ok();
      }

      $res = Finnotech::bonCharge($charge->card_number, $charge->amount_to_charge * 10, $chargeRecord->id);
      $result = json_decode($res, true);
      if (isset($result['status']) && $result['status'] == 'DONE') {
        $chargeRecord->update([
          'tracker' => $result['result']['tracker'],
          'track_id' => $result['trackId'],
          'charged_at' => Carbon::now(),
          'status' => 1,
        ]);
        $charge = Charge::where(['order_id' => $order->id, 'type' => 'charge', 'status' => 1])->first();
        if (!$charge) {
          $order->update(['charge_id' => $chargeRecord->id]);
          $chargeRecord->update([
            'tracker' => $result['result']['tracker'],
            'track_id' => $result['trackId'],
            'charged_at' => Carbon::now(),
            'status' => 1,
          ]);
        }

        //Change Order Status 
        $user = User::info($order->user_id);
        if ($order->payback_type == 'epay') {
          $gen = Order::generate_ghests($order);
          if (!$gen)
            return Useful::nok(['dbgh-ins', 'خطایی رخ داده. با مدیر فنی تماس بگیرید.']);
        }

        $order->update([
          'series' => $request->series,
          'status' => 'cycle',
          'charged_at' => Carbon::now(),
          'charge_type' => 'شارژ'
        ]);

        if ($order->shop_id) {
          Sms::send(
            Shop::mobile($order->shop_id),
            Pattern::seller_order_by_shop_charged,
            [
              'oid' => $order->oid,
              'amount' => number_format($order->amount),
              'commission' => $charge->commission,
              'charged' => number_format($order->amount_to_charge),
            ]
          );
        } else {
          Sms::send(
            $user->mobile,
            Pattern::user_order_charged,
            [
              'name' => $user->fname,
              'oid' => $order->oid,
              'amount' => number_format($order->amount),
            ]
          );
        }
        return Useful::ok();
      } else {
        $chargeRecord->update(['comment' => $res]);
        return Useful::nok($res);
      }
    }
    return Useful::nok(['dbcharge', 'خطا در ارتباط با']);
  }

  public function docs(Request $request)
  {
    $uri = Route::current()->uri;
    $role = explode('/', $uri)[1];
    $oid = $request->oid;
    $order = Order::oid($oid);
    $this->guard($order);
    if ($role == 'admin') {
      if (in_array($order->status, ['submitted', 'docs_uploaded'])) $docs = self::review_docs($order);
      else $docs = self::review_docs($order, true);
    } else {
      if (!in_array($order->status, ['submitted', 'docs_uploaded', 'upload_secondary', 'check_secondary']))
        return Useful::ok(['cta' => null, 'docs' => null]);

      $docs = self::review_docs($order);
    }
    $user = User::info($order->user_id);
    $cta = null;
    if ($role != 'admin') {
      switch ($order->status) {
        case 'submitted':
        case 'docs_uploaded':
          if (Address::where('user_id', $user->id)->where('type', 'home')->doesntExist() || empty($user->nid)) {
            if ($order->shop_id) {
              $cta = null;
            } else {
              $cta = [
                'status' => 'profile_first',
                'text' => 'تکمیل حساب کاربری',
                'description' => 'کاربر گرامی، اطلاعات حساب کاربری شما ناقص است. لطفاً روی دکمه مقابل کلیک کرده و اطلاعات لازم را ارسال نمایید:',
                'method' => 'redirect',
                'url' => 'dash-user-profile'
              ];
            }
            $docs = null;
          }
          break;
        default:
          $cta = null;
          break;
      }
    }

    return Useful::ok(['cta' => $cta, 'docs' => $docs]);
  }

  public static function review_docs($order, $all = false)
  {
    if (in_array($order->status, ['draft'])) return [['new_status' => $order->status]];
    $mode = in_array($order->status, ['submitted', 'docs_uploaded']) ? 'primary' : 'secondary';
    if ($order->shop_id) $scope = 'shop';
    else if ($order->organ_id) $scope = 'organ';
    else $scope = 'user';
    $order_id = $order->id;
    $user_id = $order->user_id;
    $shop_id = $order->shop_id;
    $organ_id = $order->organ_id;
    switch ($scope) {
      case 'shop':
        $shop = Shop::whereId($shop_id)->first();
        $shop_type = $shop->type;
        $scope = 'shop_' . $shop_type;
        break;
      case 'organ':
        $organ = Organ::select('level', 'payback_type')->whereId($organ_id)->first();
        $scope = 'organ_' . $organ->level . '_' . $organ->payback_type;
        break;
      default:
        break;
    }
    $docs = [];
    $n = 0;
    $point_primary = 0;
    $point_required = 0;
    if ($all) $docs_arr = array_merge(Doc::$order['primary'][$scope], Doc::$order['secondary'][$scope]);
    else $docs_arr = Doc::$order[$mode][$scope];
    $acc = null;
    foreach ($docs_arr as $type) {
      $docTypes = Doc::$types;
      if (in_array($type, ['report', 'backup']) && !$acc) continue;
      if ($docTypes[$type]['scope'] == 'user') {
        $doc = Doc::where('user_id', $user_id)->where('type', $type)->orderBy('id', 'desc')->first();
        $docs[$n]['url'] = '/api/orders/' . $order->oid . '/upload/' . $type;
        $docs[$n]['concatUrl'] = '/orders/' . $order->oid . '/concat/' . $type;
      } else {
        $doc = Doc::where('order_id', $order_id)->where('type', $type)->orderBy('id', 'desc')->first();
        $docs[$n]['url'] = '/api/orders/' . $order->oid . '/upload/' . $type;
        $docs[$n]['concatUrl'] = '/orders/' . $order->oid . '/concat/' . $type;
        if ($type == 'backup') $docs[$n]['skip_url'] = '/orders/' . $order->oid . '/skip/' . $type . '/' . $user_id;
      }
      $docs[$n]['url'] .= '/' . $user_id;
      $docs[$n]['concatUrl'] .= '/' . $user_id;

      if ($doc) {
        switch ($doc->is_verified) {
          case 1:
            $docs[$n]['status'] = 'verified';
            $docs[$n]['point'] = 1;
            $docs[$n]['message'] = 'این مدرک تأیید شده';
            break;
          case 0:
            $docs[$n]['status'] = 'pending';
            $docs[$n]['point'] = 1;
            $docs[$n]['message'] = 'در انتظار بررسی توسط کارشناسان قسطا';
            break;
          case -1:
            $docs[$n]['status'] = 'rejected';
            $docs[$n]['point'] = 0;
            $docs[$n]['message'] = 'این مدرک به دلیل ' . $doc->reason . ' تأیید نشده. لطفاً مجدداً آن را بارگذاری کنید.';
            if (empty($doc->reason)) $docs[$n]['message'] = 'این مدرک توسط کارشناس قسطا تأیید نشده. لطفاً مجدداً آن را بارگذاری کنید.';
            break;
        }

        if (User::is_admin()) {
          $docs[$n]['link'] = '/admin/docs/' . $doc->id;
          $docs[$n]['format'] = $doc->format;
          $docs[$n]['bank_id'] = $doc->bank_id;
          /* if (in_array($type, ['report', 'backup'])) {
            if (!is_null($doc->is_readable)) $docs[$n]['is_readable'] = $doc->is_readable;
            $docs[$n]['is_readable_url'] = '/admin/docs/' . $doc->id . '/readability/';
          } */
        }
      } else {
        $docs[$n]['status'] = 'void';
        $docs[$n]['point'] = 0;
        $docs[$n]['message'] = 'این مدرک هنوز بارگذاری نشده';
      }

      if ($type == 'cheque') {
        $acc = $doc ? Account::select('iban', 'branch_code', 'branch_name')->whereId($doc->account_id)->first() : null;
        if ($acc)
          $docs[$n]['account'] = $acc;
        else
          $docs[$n]['account'] = [
            'iban' => null,
            'branch_code' => null,
            'branch_name' => null,
          ];
      }

      $docs[$n]['required'] = !in_array($type, Doc::$order_optional);

      if (in_array($type, ['backup']))
        $docs[$n]['is_bank_needed'] = true;

      $docs[$n]['bank_id'] = $doc ? $doc->bank_id : null;
      $docs[$n]['name'] = $type;
      $docs[$n]['title'] = $docTypes[$type]['title'];
      $docs[$n]['description'] = $docTypes[$type]['description'];
      if (isset($docTypes[$type]['acceptedFiles'])) $docs[$n]['acceptedFiles'] = $docTypes[$type]['acceptedFiles'];
      switch ($type) {
        case 'report':
          $start = Edate::edateFromCarbon('F Y', Carbon::now()->subMonths(3));
          $docs[$n]['description'] = "پرینت حساب متصل به دسته چکتان (سه ماهه اخیر) را اینجا بفرستید، تقریبا از اوایل $start تا همین روزهای اخیر. این گزارش را می تونید از اینترنت بانک در قالب فایل اکسل (xls,xlsx) یا با مراجعه به شعبه تهیه کنید." . Doc::validation_table();
          break;
        case 'sefte':
          $amount = number_format(round($order->total * config('globals.order.guarantee_ratio') / 100000) * 100000 * 10);
          $docs[$n]['description'] = "یک الی سه فقره سفته مجموعاً به مبلغ $amount ریال تهیه کرده و پس از تکمیل آن، تصویر آن(ها) را بارگذاری نمایید. در قسمت تاریخ چیزی ننویسید.";
          break;
        case 'gcheck':
          $amount = number_format(round($order->total * config('globals.order.guarantee_ratio') / 100000) * 100000 * 10);
          $docs[$n]['description'] = "یک فقره چک به مبلغ <strong style='font-size:large;'>$amount</strong> ریال، در وجه " .
            "<strong style='font-size:large;'>" . config('globals.cheque_to') . "</strong>" .
            " و شناسه ملی: " . "<strong style='font-size:large;'>" . config('globals.cheque_to_nic') . "</strong>" .
            " نوشته و تصویر آن را بارگذاری نمایید. در قسمت تاریخ چیزی ننویسید.";
          break;
        default:
          break;
      }
      $docs[$n]['maxFiles'] = $docTypes[$type]['maxFiles'];
      $point_primary = $point_primary + $docs[$n]['point'];
      if (!in_array($type, Doc::$order_optional) && in_array($type, Doc::$order[$mode][$scope])) {
        $point_required = $point_required + $docs[$n]['point'];
      }
      ++$n;
    }
    $new_status = $order->status;

    if (in_array($order->status, ['submitted', 'docs_uploaded'])) {

      if (Address::where('user_id', $order->user_id)->where('type', 'home')->doesntExist()) {
        $new_status = 'submitted';
        if ($order->docs_warning_at) {
          Order::whereId($order_id)->update([
            'status' => 'submitted',
            'docs_uploaded_at' => null,
            'docs_warning_at' => null,
            'docs_accepted_at' => null,
          ]);
        }
      }

      if ($order->status == 'submitted' && $point_required >= (count(Doc::$order[$mode][$scope]) - count(Doc::$order_optional))) {
        $new_status = 'docs_uploaded';
        $order_update = Order::whereId($order_id)->update([
          'status' => 'docs_uploaded',
          'docs_uploaded_at' => Carbon::now(),
          'docs_accepted_at' => null,
        ]);
        if (!$order->docs_warning_at && (($order->organ_id && $order->organ_accepted_at) || (!$order->organ_id))) {
          if ($order_update) {
            $user = User::info($order->user_id);
            $rep = Doc::select('format')->where('order_id', $order_id)->where('type', 'report')->orderBy('id', 'desc')->first();
            $bkp = Doc::select('format')->where('order_id', $order_id)->where('type', 'backup')->orderBy('id', 'desc')->first();
            if (in_array($rep->format, ['xls', 'xlsx']) && (in_array($bkp->format, ['xls', 'xlsx']) || is_null($bkp->format)))
              $rt = Order::inquiry_rabbit_time;
            else
              $rt = Order::inquiry_turtle_time;

            $uts = Order::ultimatum(time() + ($rt - 2 * 86400));

            Order::whereId($order_id)->update(['docs_warning_at' => Carbon::createFromTimestamp($uts)]);
            Sms::send(
              $user->mobile,
              Pattern::user_order_docs_uploaded,
              [
                'name' => $user->fname,
                'oid' => $order->oid,
                'date' => Edate::edate('j F، ساعت H', $uts, 'fa')
              ]
            );
            Sms::send(
              config('globals.support.mobile'),
              Pattern::admin_order_docs_uploaded,
              [
                'oid' => $order->oid,
                'date' => Edate::edate('j F، ساعت H', $uts, 'fa')
              ]
            );
            if ($order->shop_id) {
              Sms::send(
                Shop::mobile($order->shop_id),
                Pattern::user_order_by_shop_docs_uploaded,
                [
                  'name' => $user->full_name,
                  'oid' => $order->oid,
                  'date' => Edate::edate('j F، ساعت H', $uts, 'fa')
                ]
              );
            }
          }
        }
      } else if ($order->status == 'docs_uploaded' && $point_required < (count(Doc::$order[$mode][$scope]) - count(Doc::$order_optional))) {
        $new_status = 'submitted';
        if ($order->docs_warning_at) {
          Order::whereId($order_id)->update([
            'status' => 'submitted',
            'docs_uploaded_at' => null,
            'docs_warning_at' => null,
            'docs_accepted_at' => null,
          ]);
        }
      }
    } else if (in_array($order->status, ['upload_secondary', 'check_secondary', 'wait_for_cheques'])) {
      if ($order->payback_type === 'epay') {
        if ($point_required >= count(Doc::$order[$mode][$scope]) && $order->status === 'upload_secondary') {
          Order::whereId($order->id)->update(['status' => 'check_secondary', 'secondary_uploaded_at' => Carbon::now()]);
          $order->secondary_uploaded_at = Carbon::now();
          $order->status = 'check_secondary';
        } else if ($order->status === 'check_secondary') {
          Order::whereId($order->id)->update(['status' => 'upload_secondary', 'secondary_uploaded_at' => null]);
          $order->secondary_uploaded_at = null;
          $order->status = 'upload_secondary';
        }
      }
      $new_status = $order->status;
    }
    $docs[0]['new_status'] = $new_status;
    return $docs;
  }

  public function check_please(Request $request)
  {
    $oid = $request->oid;

    $order = Order::where('oid', $oid)->first();
    $this->guard($order);
    $review_docs = self::review_docs($order);
    $order->status = $review_docs[0]['new_status'];
    $user = User::info($order->user_id);

    switch ($order->status) {
      case 'submitted':
      case 'docs_uploaded':
        if (Address::where(['user_id' => $user->id, 'type' => 'home'])->doesntExist()) {
          return Useful::nok(['address', 'شما هنوز آدرس محل سکونت خود را ثبت نکرده اید']);
        }
        if (!$user->nid) {
          return Useful::nok(['profile', 'ابتدا اطلاعات حساب کاربری خود را کامل کنید']);
        }
        if ($review_docs[0]['new_status'] == 'docs_uploaded') {
          return Useful::ok(['message' => 'مدارک شما در صف اعتبارسنجی توسط کارشناسان قسطا قرار گرفت. نتیجه اعتبارسنجی از طریق پیامک به شما اطلاع رسانی خواهد شد.']);
        }
      default:
        break;
    }

    return Useful::nok(['docs', 'نقص در مدارک ارسالی']);
  }

  public static function maxAmounts($userId, $organ_level = null)
  {
    $orderCompletedCount = Order::where(['user_id' => $userId])->completed()->count();

    if ($orderCompletedCount == 0) $x = 'first';
    else if ($orderCompletedCount == 1) $x = 'second';
    else $x = 'third';

    $gv = Setting::gv();
    if ($organ_level) {
      if ($x == 'third') $x = 'second';
      $amount = $gv->{"max_amount_" . $x . "_organ_level_" . $organ_level};
      $ghest = 5000000;
      $rpa = $gv->{"rpa_organ_level_" . $organ_level};
    } else {
      $amount = $gv->{"max_amount_" . $x};
      $ghest = $gv->{"max_ghest_" . $x};
      $rpa = $gv->rpa;
    }

    return [
      'max_allowed_amount' => $amount,
      'max_allowed_ghest' => $ghest,
      'completed_orders_count' => $orderCompletedCount,
      'rpa' => $rpa,
    ];
  }

  public static function invoice_inquiry_costs($user)
  {
    $cost_inquiry = 0;
    $extra = [];
    //Check Sim
    if (!$user->simcard_owner) {
      $icbs = ICBS::Shahkar($user->nid, Useful::enum($user->username, 'mobile'), 0, $user->id);
      if ($icbs) {
        $inq_sim = $icbs->result['MobileAndNationalCodeMatchingResult'];
        if ($inq_sim['Result'] === true)
          DB::table('users')->where('id', $user->id)->update(['simcard_owner' => $icbs->id]);
        else return ['total' => 'SIM'];
      } else {
        /* $cost_inquiry += ICBS::price_sim;
        $extra[] = 'sim'; */
      }
    }

    /* $inq_sim_count = ICBS::where('user_id', $user->id)
      ->where('method', ICBS::$method_MobileAndNationalCodeMatching)
      ->whereNull('paid_at')->count();
    $cost_inquiry += ICBS::price_sim * $inq_sim_count; */

    //Check RegInfo
    if (!$user->verified_at) {
      $icbs = ICBS::last_record([
        'method' => ICBS::$method_RegInfo,
        'nid' => $user->nid
      ]);
      if (!$icbs) {
        $cost_inquiry += ICBS::price_reg;
        $extra[] = 'reg';
      } else {
        $inq_reg = $icbs->result['RegInfoResult'];

        if (empty($inq_reg['BirthDate']) || empty($inq_reg['Gender'])) {
          $cost_inquiry += ICBS::price_reg;
          $extra[] = 'reg';
          User::whereId($user->id)->update(['rejected_at' => Carbon::now()]);
          ICBS::where('method', ICBS::$method_RegInfo)->where('nid', $user->nid)->update(['status' => 0]);
          Sms::send($user->username, "کاربر گرامی، طبق استعلام صورت گرفته، تاریخ تولد اظهارشده در حساب کاربری با مشخصات حقیقی شما مغایرت دارد. جهت انجام مجدد استعلام، این مورد را با مراجعه به پنل اصلاح نمایید.");
          return ['total' => 'REG'];
        } else {
          DB::table('users')->where('id', $user->id)->update([
            'fname' => Useful::std($inq_reg['Name']),
            'lname' => Useful::std($inq_reg['LastName']),
            'verified_at' => Carbon::now(),
            'gender' => Useful::std($inq_reg['Gender']) == 'مرد' ? 'M' : 'F'
          ]);
        }
      }
    }

    //Check Bank
    $icbs = ICBS::last_record([
      'method' => ICBS::$method_IntegratedCs,
      'nid' => $user->nid
    ]);

    $cost_bank = 0;
    if (!$icbs || ($icbs && $icbs->is_expired)) {
      $cost_bank += ICBS::price_bank;
      $extra[] = 'bank';
    } else {
      $inq_bank = $icbs->result['IntegratedCsResult'];
      if ($inq_bank['BankCsCheckInfo']['CheckCount'] > 0)
        return ['total' => 'BCH'];
    }

    return [
      'description' => '',
      'total' => $cost_bank + $cost_inquiry,
      'details' => [
        [
          'type' => 'bank',
          'title' => 'استعلام‌های بانکی',
          'price' => $cost_bank,
        ],
        [
          'type' => 'reg',
          'title' => 'استعلام‌های هویتی',
          'price' => $cost_inquiry,
        ]
      ],
      'extra' => $extra
    ];
  }

  public function limits(Request $request)
  {
    $uri = Route::current()->uri;
    $role = explode('/', $uri)[1];
    $prefs = Setting::gv();
    $organ_level = null;

    if ($role == 'shop') {
      $username = Useful::enum($request->username, 'username');
      $user = User::where('username', $username)->first();
    } else if ($role == 'organ') {
      $user = Auth::user();
      $code = Useful::enum($request->code);
      $organ_level = Organ::select('level')->where('code', $code)->first()->level;
    } else $user = Auth::user();

    if ($this->has_unfinished_order($user->id)) {
      if ($role == 'shop') {
        return Useful::nok([
          'name' => 'unfinished_order_exists',
          'message' => 'این کاربر هنوز یک سفارش در حال انجام دارد.'
        ]);
      }
      return Useful::nok([
        'name' => 'unfinished_order_exists',
        'message' => 'شما هنوز یک سفارش در حال انجام دارید. لطفاً ابتدا آن را تکمیل یا لغو کنید و سپس اقدام به ثبت سفارش بعدی کنید.'
      ]);
    }

    if ($user->badge === 'red')
      return Useful::nok([
        'name' => 'red',
        'message' => 'امکان ارائه خدمات به این کاربر وجود ندارد'
      ]);

    $max = self::maxAmounts($user->id, $organ_level);
    $prefs->max_allowed_amount = $max['max_allowed_amount'];
    $prefs->max_allowed_ghest = $max['max_allowed_ghest'];
    $prefs->rpa = $max['rpa'];
    if (isset($code) && $code == '52617') $prefs->rpa = 0;
    $prefs->completed_orders_count = $max['completed_orders_count'];
    $prefs->custom_card_msg = null;

    //TEMP LIMIT
    if ($prefs->completed_orders_count > 0 && $role != 'shop' && $user->id != 7378) {
      return Useful::ok(null, [
        'custom_message' => [
          'msg_title' => '',
          'msg' => '<p>' . 'مشتری عزیز، به دلیل فراهم نبودن امکان ثبت سفارش در دو ماه گذشته (به دلیل بهبود زیرساخت های قسطا) و تجمیع سفارش‌های مشتریان در این دوره، حجم ثبت سفارش در سایت قسطا بسیار افزایش پیدا کرده است. لذا جهت ارائه خدمات به تعداد بیشتری از مشتریان تا اطلاع ثانوی ثبت سفارش فقط برای فروشگاه ها و سفارش اولی‌ها امکان پذیر خواهد بود. ' . '</p>' .
            '<p>' . 'به محض برگشتن شرایط به روال عادی، امکان ثبت سفارش دوم به بعد نیز مجددا فراهم خواهد شد. ' . '</p>' .
            '<p>' . 'از همراهی شما متشکریم.' . '</p>',
          'msg_id' => 135,
          'type' => 'warning',
          'icon' => 'exclamation-circle',
          'style' => 'modal',
          'buttons' => [
            [
              'text' => 'بازگشت',
              'type' => 'light',
              'mode' => 'url',
              'url' => 'https://ghesta.ir/dashboard/user/orders',
            ],
          ]
        ]
      ]);
    }

    //Prepayment Zero for FormFillers
    $userController = new UserController();
    $prefs->form_extra_score =  $userController->forms_fill_status($request)['result']['form_extra'];
    if (
      $prefs->form_extra_score == 100
      && Usermeta::where(['user_id' => $user->id])
      ->whereBetween('created_at', [Edate::j2carbon('1399-12-07'), Edate::j2carbon('1399-12-11')])->exists()
      && Order::where(['user_id' => $user->id])->successful()->exists()
      && Order::where(['user_id' => $user->id, 'prepayment' => 0])->worthy()->whereNull('organ_id')->doesntExist()
    ) {
      $prefs->rpa = 0;
      $prefs->custom_card_msg = 'پیش پرداخت شما در این سفارش به دلیل تکمیل حساب کاربری 0 تومان شده است.';
    }

    // $prefs->important_msg = [
    //   'color' => 'warning',
    //   'text' => 'همراهان گرامی قسطا در نظر داشته باشید به علت ترافیک کاری آخر سال سفارش‌هایی که  ثبت و یا بارگذاری مدارک شان بعد از تاریخ 22 اسفند انجام شود، فرایند اعتبار سنجی آن‌ها بعد از 14 فروردین انجام خواهد شد.',
    // ];

    //TEMP CLOSE
    /* if (time() > Edate::jmktime(0, 0, 0, 1, 16, 1400) && !in_array($user->username, ['9336762500', '9182705069', '9109270876', '9127967590', '9123166200'])) {
      $prefs->create_control = [
        'is_lock' => true,
        'text' => 'کاربر گرامی، به دلیل انجام تغییرات در زیرساخت اعتبارسنجی سایت، امکان ثبت سفارش موقتاً وجود ندارد. از همراهی شما متشکریم.'
      ];
    } */

    return Useful::ok($prefs);
  }

  public function has_unfinished_order($user_id)
  {
    $orders_inprogress_count = Order::where('user_id', $user_id)->inprogress()->count();
    if ($orders_inprogress_count > 0) {
      if (in_array(User::select('username')->whereId($user_id)->first()->username, [
        '9109270876', '9393949843', '9128235099', '9127967590', '9336762500', '9127967590', '9123166200'
      ])) return false;
      return true;
    }
    return false;
  }

  public function create(Request $request)
  {
    $uri = Route::current()->uri;
    $role = explode('/', $uri)[1];
    $organ_id = null;
    $organ_level = null;
    $shop_id = null;
    $payback_type = 'cheque';

    if ($role == 'shop') {
      $username = Useful::enum($request->username, 'username');
      $user = User::where('username', $username)->first();
      $shop_id = Shop::select('id')->where('user_id', Auth::user()->id)->first()->id;
    } else $user = Auth::user();

    if ($this->has_unfinished_order($user->id)) {
      if ($role == 'shop') {
        return Useful::nok([
          'name' => 'unfinished_order_exists',
          'message' => 'این مشتری یک سفارش در حال انجام دارد که باید آن را تکمیل یا لغو کند.'
        ]);
      } else {
        return Useful::nok([
          'name' => 'unfinished_order_exists',
          'message' => 'شما هنوز یک سفارش در حال انجام دارید. لطفاً ابتدا آن را تکمیل یا لغو کنید و سپس اقدام به ثبت سفارش بعدی کنید.'
        ]);
      }
    }

    $amount = $request->amount;
    $prepayment = $request->prepayment;
    $organ_code = $request->organ_code;
    $months = $request->months;
    $cheques = $request->cheques;
    $product = $request->product;

    //TEMP LIMIT
    if (self::maxAmounts($user->id, $organ_level)['completed_orders_count'] > 0 && $role != 'shop' && $user->id != 7378) {
      return Useful::ok(null, [
        'custom_message' => [
          'msg_title' => '',
          'msg' => '<p>' . 'مشتری عزیز، به دلیل فراهم نبودن امکان ثبت سفارش در دو ماه گذشته (به دلیل بهبود زیرساخت های قسطا) و تجمیع سفارش‌های مشتریان در این دوره، حجم ثبت سفارش در سایت قسطا بسیار افزایش پیدا کرده است. لذا جهت ارائه خدمات به تعداد بیشتری از مشتریان تا اطلاع ثانوی ثبت سفارش فقط برای فروشگاه ها و سفارش اولی‌ها امکان پذیر خواهد بود. ' . '</p>' .
            '<p>' . 'به محض برگشتن شرایط به روال عادی، امکان ثبت سفارش دوم به بعد نیز مجددا فراهم خواهد شد. ' . '</p>' .
            '<p>' . 'از همراهی شما متشکریم.' . '</p>',
          'msg_id' => 135,
          'type' => 'warning',
          'icon' => 'exclamation-circle',
          'style' => 'modal',
          'buttons' => [
            [
              'text' => 'بازگشت',
              'type' => 'light',
              'mode' => 'url',
              'url' => 'https://ghesta.ir/dashboard/user/orders',
            ],
          ]
        ]
      ]);
    }

    //TEMP CLOSE
    /* if (!in_array($user->username, ['9336762500', '9182705069', '9109270876', '9127967590', '9123166200']))
      return Useful::nok(['temp_close', 'امکان ثبت سفارش موقتاً وجود ندارد.']); */

    if ($organ_code) {
      if (Organ::where(['code' => $organ_code, 'status' => 'active'])->doesntExist()) {
        return Useful::nok([
          'name' => 'organ_not_found',
          'message' => 'کد سازمانی معتبر نیست'
        ]);
      }
      $organ = Organ::select('id', 'level', 'payback_type')->where('code', $organ_code)->first();
      $organ_id = $organ->id;
      $organ_level = $organ->level;
      $payback_type = $organ->payback_type;
    } else {
    }

    $limits = self::maxAmounts($user->id, $organ_level);
    $max = $limits['max_allowed_amount'];
    $max_allowed_ghest = $limits['max_allowed_ghest'];
    $rpa = $limits['rpa'];

    $request->validate([
      'amount' => ['required', 'numeric', 'min:1200000'],
      'months' => ['required', 'numeric', 'min:3', 'max:12'],
      'cheques' => ['required', 'numeric', 'min:3', 'max:12'],
    ]);

    if ($amount > $max) return Useful::nok([
      'name' => 'max_amount_exceeded',
      'message' => 'اعتبار قسطاکارت درخواستی شما نمی تواند بیشتر از ' . number_format($max) . ' تومان باشد.'
    ]);

    $order = (object)[
      'amount' => $amount,
      'prepayment' => $prepayment,
      'rpa' => $rpa,
      'organ_id' => $organ_id,
      'organ_level' => $organ_level,
      'months' => $months,
      'cheques' => $cheques,
    ];

    $order = Order::ghestify($order, 1);

    //Maximum Ghest Validation
    if ($order->ghest > $max_allowed_ghest) {
      return Useful::nok([
        'name' => 'max_ghest_exceeded',
        'message' => 'سقف هر قسط شما نمی تواند بیشتر از ' . number_format($max_allowed_ghest) . ' تومان باشد. برای رفع این خطا می توانید اعتبار قسطاکارت را کاهش دهید یا تعداد اقساط را افزایش دهید'
      ]);
    }


    if ($ORDER = Order::create([
      'oid' => Useful::randomString(5),
      'user_id' => $user->id,
      'status' => 'draft',
      'amount' => $order->amount,
      'prepayment' => $order->prepayment,
      'product' => $product,
      'months' => $order->months,
      'cheques' => $order->cheques,
      'gain' => $order->gain,
      'ghest' => $order->ghest,
      'total' => $order->total,
      'organ_id' => $organ_id,
      'shop_id' => $shop_id ? $shop_id : null,
      'payback_type' => $payback_type == 'cheque' ? 'cheque' : 'epay'
    ])) {
      $oid = 'GHC-' . mt_rand(1, 9) . strrev($ORDER->id);
      if ($ORDER->update(['oid' => $oid])) {
        $card = Card::select('series')->where('user_id', $user->id)->orderBy('id', 'desc')->first();
        if ($card) $ORDER->update(['series_card' => $card->series]);
        return Useful::ok(['oid' => $oid]);
      }
      return Useful::nok(['dbc', 'خطا در ارتباط با سرور. دوباره تلاش کنید.']);
    }
    return Useful::nok(['db', 'خطا در ارتباط با سرور. لطفاً سفارش خود را بازبینی کرده و در صورت صحیح بودن اطلاعات مجدداً ثبت کنید.']);
  }

  public function cancel(Request $request)
  {
    $oid = $request->oid;
    $reason = $request->reason;
    $order = Order::oid($oid);
    $this->guard($order);
    if (in_array($order->status, ['prepaid', 'cycle', 'completed']))
      return Useful::nok(['not_allowed']);

    if (Order::where('oid', $oid)->update([
      'status' => 'cancelled',
      'closed_at' => Carbon::now(),
      'reason' => $reason,
      'rejected_by' => Auth::user()->id
    ])) {
      Ghest::where('order_id', $order->id)->delete();
      return Useful::ok();
    }
    return Useful::nok(['dbo-c']);
  }

  public function cheques_preview(Request $request)
  {
    $uri = Route::current()->uri;
    $scope = explode('/', $uri)[1];

    $oid = $request->oid;
    $order = Order::oid($oid);

    if ($scope != 'admin') $this->guard($order);

    return Useful::ok(Order::cheques_info($order));
  }

  public function accounts(Request $request)
  {
    $oid = $request->oid;
    $order = Order::oid($oid);
    $this->guard($order);
    $accounts = Account::select('id', 'bank_id', 'branch_name', 'branch_code', 'iban')->where('user_id', $order->user_id)->get();
    $res = [];
    foreach ($accounts as $account) {
      $res[] = [
        'logo' => config('banks.' . $account->bank_id . '.name'),
        'name' => config('banks.' . $account->bank_id . '.fa'),
        'iban' => $account->iban,
        'branch_name' => $account->branch_name,
        'branch_code' => $account->branch_code,
      ];
    }
    return Useful::ok($res);
  }

  public function define_account(Request $request)
  {
    $request->validate([
      'iban' => ['required', 'string', 'min:24', 'max:26'],
      'branch_name' => ['required', 'string', 'min:1'],
      'branch_code' => ['required', 'string', 'min:1', 'max:30']
    ]);
    $oid = $request->oid;
    $order = Order::oid($oid);
    $this->guard($order);

    $iban = substr($request->iban, -24);
    $branch_name = $request->branch_name;
    $branch_code = $request->branch_code;

    if (Account::updateOrCreate([
      'user_id' => $order->user_id,
      'iban' => substr($iban, -24)
    ], [
      'branch_name' => $branch_name,
      'branch_code' => $branch_code,
      'bank_id' => substr($iban, 2, 3)
    ]))
      return Useful::ok();

    return Useful::nok(['dbacc', 'خطای 4320 رخ داده است']);
  }

  public function cheques(Request $request)
  {
    $item = $request->item;
    $order = Order::oid($request->oid);

    $this->guard($order);

    if (User::is_admin())
      $ch = Cheque::where(['order_id' => $order->id, 'badge' => $item])->first();
    else
      $ch = Cheque::where(['order_id' => $order->id, 'badge' => $item])->where('is_verified', '!=', 1)->first();

    if (isset($item) && $ch) {
      $arr = [
        'prepend' => Useful::enum($request->prepend),
        'append' => Useful::enum($request->append),
        'series' => self::cheque_serial_helper(Useful::enum($request->series)),
        'is_submitted' => 1,
        'decided_at' => null,
      ];
      if (!$ch->isbn || User::is_admin()) {
        $arr['isbn'] = Useful::enum($request->isbn);
        if (User::is_admin()) $arr['isbn_verified_at'] = Carbon::now();
      }

      Cheque::whereId($ch->id)->update($arr);

      return Useful::ok();
    }

    return Useful::nok();
  }

  //ADMIN
  public function cheques_download(Request $request)
  {
    $order = Order::oid($request->oid);
    if (Order::has_old_cheques($order->id)) {
      $doc_id = Doc::select('id')->where(['order_id' => $order->id, 'type' => 'cheques', 'is_verified' => 1])->first()->id;
      $link = DocController::downloadFile($doc_id);
      return Useful::ok(['url' => $link], ['action_type' => 'open_url']);
    }

    return Useful::nok();
  }

  public static function cheque_serial_helper($serial)
  {
    $x = explode('/', $serial);
    if (count($x) == 1) return Useful::enum($x[0]);
    else if (count($x) > 1) return Useful::enum($x[1]);
  }

  public function essentials(Request $request)
  {
    $order = Order::oid($request->oid);
    $this->guard($order);
    $user = User::whereId($order->user_id)->first();

    if ($user->verified_at)
      return Useful::nok('کاربر قبلاً احراز هویت شده است.');

    if ($user->simcard_owner)
      return Useful::nok('مالکیت این سیمکارت و کدملی قبلاً تأیید شده است.');

    $nid_valid = User::check_nid($request->nid);

    if (!$nid_valid || strlen($request->birth) < 8)
      return Useful::nok('لطفاً اطلاعات را بطور کامل و صحیح وارد کنید');

    if (User::where('nid', $nid_valid)->whereNotNull('verified_at')->whereNotNull('simcard_owner')->exists())
      return Useful::nok('کد ملی تکراری/نامعتبر است.');

    if ($nid_valid && !$user->simcard_owner) {
      $icbs = ICBS::Shahkar($nid_valid, Useful::enum($user->username, 'mobile'), 'restrict', $user->id);
      $inq_sim = $icbs->result['MobileAndNationalCodeMatchingResult'];
      if ($inq_sim['Result'] === true) {
        $user->update([
          'nid' => $nid_valid,
          'simcard_owner' => $icbs->id,
          'birth' => Useful::enum($request->birth, 'birth'),
          'rejected_at' => null
        ]);
        return Useful::ok();
      } else {
        return Useful::nok('کد ملی تکراری/نامعتبر است.');
      }
    }

    $user->update(['birth' => Useful::enum($request->birth, 'birth')]);

    return Useful::ok();
  }

  public function edit_user_by_shop(Request $request)
  {
    $order = Order::oid($request->oid);
    $this->guard($order);

    $that_user = User::where('id', $order->user_id)->first();
    if ($that_user->verified_at)
      return Useful::nok(['verified_before', 'این کاربر قبلاً احراز هویت شده']);

    $nid = $request->nid;
    if ($nid) {
      $nid_valid = User::check_nid($nid);
      if (!$nid_valid) return Useful::nok(['nid_invalid', 'کدملی نامعتبر است']);
      if (User::whereId($order->user_id)->whereNotNull('nid')->exists())
        return Useful::nok([
          'name' => 'nid_exists',
          'message' => 'کدملی این کاربر قبلاً ثبت شده است.'
        ]);

      if (User::where('nid', $nid_valid)->exists())
        return Useful::nok([
          'name' => 'nid_exists',
          'message' => 'این کدملی توسط کاربر دیگری قبلاً ثبت شده است.'
        ]);

      if (User::whereId($order->user_id)->whereNull('verified_at')->update(['nid' => $nid_valid]))
        return Useful::ok();
    }

    $birth = $request->birth;
    if ($birth) {
      $b = explode('-', $birth);

      if (strlen($birth) < 8 || $b[0] > Edate::edateFromCarbon('Y', Carbon::now()->subYears(17)))
        return Useful::nok(['birth_invalid', 'تاریخ تولد نامعتبر است']);

      $birth = $b[0] . '-' . Useful::zerofill($b[1], 2) . '-' . Useful::zerofill($b[2], 2);
      if (User::whereId($order->user_id)->update(['birth' => $birth])) return Useful::ok();
    }

    return Useful::nok();
  }

  public function edit_address_by_shop(Request $request)
  {
    $order = Order::oid($request->oid);
    $this->guard($order);

    $type = $request->type;
    $state = Address::state_code($request->state);
    $city = $request->city;
    $address = Useful::enum($request->address);
    $postal_code = Useful::enum($request->postal_code);
    $phone = Useful::enum($request->phone, 'phone');

    if (Address::where(['user_id' => $order->user_id, 'type' => $type])->whereNotNull('address_verified_at')->exists())
      return Useful::nok(['address_exists', 'آدرس قبلاً ثبت شده است']);

    Address::create([
      'user_id' => $order->user_id,
      'type' => $type,
      'state' => $state,
      'city' => $city,
      'address' => $address,
      'postal_code' => $postal_code,
      'phone' => $phone,
    ]);

    return Useful::ok();
  }

  public function guard($order)
  {
    $user = Auth::user();
    if (User::is_admin() || $user->id == $order->user_id) return true;
    if ($order->shop_id) {
      $shop_owner_id = Shop::select('user_id')->where('id', $order->shop_id)->first()->user_id;
      if ($user->id == $shop_owner_id) return true;
    }
    if ($order->organ_id) {
      $organ_owner_id = Organ::select('user_id')->where('id', $order->organ_id)->first()->user_id;
      if ($user->id == $organ_owner_id) return true;
    }
    return abort(403, 'This is not your business!');
  }

  public function order_profit_calc($order)
  {
    $credit = $order->amount - $order->prepayment;
    $ghest = $order->ghest;
    $gain = (1 + $order->gain) ** ($order->months / $order->cheques) - 1;
    $o_total = 0;
    $i_total = 0;
    $remaining = $credit;
    $result = [];
    for ($item = 0; $item < ($order->cheques - 1); $item++) {
      $i = round($remaining * $gain);
      $o = round($ghest - $i);
      $o_total += $o;
      $i_total += $i;
      $remaining = $remaining - $o;
      $result[$item] = [
        'origin' => $o,
        'income' => $i,
        'remaining' => round($remaining)
      ];
    }

    $result[($order->cheques - 1)] = [
      'origin' => $credit - $o_total,
      'income' => ($order->total - $order->amount) - $i_total,
      'remaining' => 0
    ];

    return $result;
  }

  //ADMIN
  public function change_status(Request $request)
  {
    $auth_user = Auth::user();
    if (!in_array($auth_user->username, ['9393949843', '9128222430', '9109270876', '9336248455', '9385586943', '9127967590', '9194104735'])) abort(403);
    $status = $request->status;
    $oid = $request->oid;
    $mode = $request->mode;
    $mode == 'silent' ? $silent = 1 : $silent = 0;
    $reason = $request->reason;
    $order = Order::where('oid', $oid)->with(['shop', 'organ'])->first();
    $this->guard($order);
    $user = User::info($order->user_id);

    switch ($status) {
      case 'rejected':
        $order->update(['status' => 'rejected', 'closed_at' => Carbon::now(), 'reason' => $reason]);
        Ghest::where('order_id', $order->id)->delete();
        if (!empty($reason)) {
          NoteController::make('order', $order->id, $reason, 'سفارش رد شد.', 'reject_order');
          Sms::send(
            $user->mobile,
            Pattern::user_order_rejected,
            [
              'name' => $user->fname,
              'oid' => $order->oid,
              'reason' => $reason
            ]
          );
          if ($order->shop_id) {
            Sms::send(
              Shop::mobile($order->shop_id),
              Pattern::seller_order_by_shop_rejected,
              [
                'oid' => $order->oid,
                'reason' => $reason
              ]
            );
          }
        }
        break;
      case 'cancelled':
        if (!empty($comment))
          NoteController::make('order', $order->id, $reason, 'سفارش لغو شد.', 'cancel_order');

        $order->update(['status' => 'cancelled', 'closed_at' => Carbon::now(), 'reason' => $reason]);
        Ghest::where('order_id', $order->id)->delete();
        break;
      case 'upload_secondary':
        $default_ts = Carbon::now()->addMonth()->addDays(5)->getTimestamp();
        $day = Edate::edate('d', $default_ts);
        $day == 31 ?  $d = 6 : $d = 5;
        $order->months == $order->cheques ? $m = 1 : $m = 2;
        $first_ghest = Carbon::now()->addMonths($m)->addDays($d);
        $order->update([
          'status' => 'upload_secondary',
          'docs_accepted_at' => Carbon::now(),
          'first_ghest_at' => $first_ghest
        ]);
        if ($order->shop_id) {

          Sms::send(
            Shop::mobile($order->shop_id),
            Pattern::seller_order_by_shop_docs_accepted,
            [
              'name' => $user->full_name,
              'oid' => $order->oid,
              'amount' => number_format($order->amount),
              'prepayment' => number_format($order->prepayment),
              'ghest' => number_format($order->ghest),
              'months' => $order->months,
              'cheques' => $order->cheques,
            ]
          );

          Sms::send(
            $user->mobile,
            Pattern::user_order_by_shop_docs_accepted,
            [
              'name' => $user->full_name,
              'oid' => $order->oid,
              'shop' => Shop::title($order->shop_id),
              'ghest' => number_format($order->ghest),
              'cheques_to' => config('globals.cheque_to'),
              'amount' => number_format($order->amount),
              'prepayment' => number_format($order->prepayment),
              'ghest' => number_format($order->ghest),
              'months' => $order->months,
              'cheques' => $order->cheques,
            ]
          );
        } else {
          Sms::send(
            $user->mobile,
            Pattern::user_order_docs_accepted,
            [
              'name' => $user->fname,
              'oid' => $order->oid,
              'amount' => number_format($order->amount),
              'prepayment' => number_format($order->prepayment),
              'ghest' => number_format($order->ghest),
              'months' => $order->months,
              'cheques' => $order->cheques,
            ]
          );
        }
        break;
      case 'wait_for_cheques':
        $ch_count = Cheque::where(['order_id' => $order->id, 'type' => 'chf', 'is_verified' => 1])->count();
        if ($order->payback_type === 'cheque' && $ch_count != $order->cheques)
          return Useful::nok('ابتدا باید همه چک‌ها تأیید شوند!');

        $order->update([
          'status' => 'wait_for_cheques',
          'secondary_accepted_at' => Carbon::now()
        ]);
        if ($order->shop_id) {
          Sms::send(
            Shop::mobile($order->shop_id),
            Pattern::seller_order_by_shop_secondary_accepted,
            [
              'name' => $user->full_name,
              'oid' => $order->oid,
            ]
          );
          Sms::send(
            $user->mobile,
            Pattern::user_order_by_shop_secondary_accepted_prepayment,
            [
              'name' => $user->fname,
              'shop' => Shop::title($order->shop_id),
            ]
          );
        } else {
          Sms::send(
            $user->mobile,
            Pattern::user_order_secondary_accepted,
            [
              'name' => $user->fname,
              'oid' => $order->oid,
            ]
          );
        }
        break;
      case 'prepayment':
        $now = Carbon::now();
        $order->update(['status' => 'prepayment', 'docs_received_at' => $now]);
        if ($order->payback_type == 'epay') {
          $gen = Order::generate_ghests($order);
          if (!$gen) return Useful::nok(['dbgh-ins', 'خطایی رخ داده. با مدیر فنی تماس بگیرید.']);
        }
        // Ghest::where('order_id', $order->id)->update(['received_at' => $order->docs_received_at]);

        if ($order->payback_type === 'cheque') {
          Ghest::where('order_id', $order->id)->delete();

          $profit_calc = $this->order_profit_calc($order);
          $cheque_dates = Order::cheque_dates($order);

          $cheques = Cheque::where(['order_id' => $order->id, 'type' => 'chf', 'is_verified' => 1])->orderBy('badge', 'asc')->get();

          foreach ($cheques as $ch) {
            $ghs[] = [
              'type' => 'cheque',
              'order_id' => $order->id,
              'account_id' => $ch->account_id,
              'cheque_id' => $ch->id,
              'isbn' => $ch->isbn,
              'prepend' => $ch->prepend,
              'append' => $ch->append,
              'series' => $ch->series,
              'amount' => $order->ghest,
              'origin' => $profit_calc[$ch->badge]['origin'],
              'income' => $profit_calc[$ch->badge]['income'],
              'shamsi' => str_replace('-', '', $cheque_dates[$ch->badge]['date']),
              'ghest_date' => Edate::j2carbon($cheque_dates[$ch->badge]['date']),
              'received_at' => $now,
              'created_at' => $now,
              'updated_at' => $now,
            ];
          }

          DB::table('ghests')->insert($ghs);
        }

        if ($order->shop_id) {
          Sms::send(
            Shop::mobile($order->shop_id),
            Pattern::seller_order_by_shop_prepayment,
            [
              'name' => $user->full_name,
              'oid' => $order->oid,
            ]
          );
        } else {
          if ($order->prepayment == 0) {
            Sms::send(
              $user->mobile,
              Pattern::user_order_docs_received_zero_prepayment,
              [
                'name' => $user->fname,
                'oid' => $order->oid,
              ]
            );
          } else {
            Sms::send(
              $user->mobile,
              Pattern::user_order_docs_received_prepayment,
              [
                'name' => $user->fname,
                'oid' => $order->oid,
              ]
            );
          }
        }
        break;
      case 'extra_charged':
        $extra = Payment::where(['order_id' => $order->id, 'type' => 'extra', 'status' => 100])->orderBy('id', 'desc')->first();
        $extra->update(['charged_at' => Carbon::now()]);

        $order->update(['status' => 'cycle']);

        Sms::send(
          $user->mobile,
          Pattern::user_order_extra_charged,
          [
            'name' => $user->fname,
            'oid' => $order->oid,
            'amount' => number_format($extra->amount),
          ]
        );

        NoteController::make('order', $order->id, 'شارژ اضافی به مبلغ ' . number_format($extra->amount) . ' تومان انجام شد.', 'شارژ اضافی', 'extra_charge');
        break;
      case 'cycle':
      case 'cycle_epay':
      case 'cycle_cheque':
        !empty($request->charge_type) ? $charge_type = $request->charge_type : $charge_type = 'شارژ';
        $order->update([
          'status' => 'cycle',
          'charged_at' => Carbon::now(),
          'charge_type' => $charge_type
        ]);

        if ($order->shop_id) {
          $charge = (object) $this->charging_info($order);
          Sms::send(
            Shop::mobile($order->shop_id),
            Pattern::seller_order_by_shop_charged,
            [
              'oid' => $order->oid,
              'amount' => number_format($order->amount),
              'commission' => $charge->commission,
              'charged' => number_format($order->amount_to_charge),
            ]
          );
        } else {
          Sms::send(
            $user->mobile,
            Pattern::user_order_charged,
            [
              'name' => $user->fname,
              'oid' => $order->oid,
              'amount' => number_format($order->amount),
            ]
          );
        }
        break;
      case 'completed':
        $order->update(['status' => 'completed', 'closed_at' => Carbon::now()]);
        Sms::send(
          $user->mobile,
          Pattern::user_order_completed,
          [
            'name' => $user->fname,
            'oid' => $order->oid,
          ]
        );
        break;
      case 'rollback_docs_uploaded':
        $order->update(['status' => 'docs_uploaded', 'docs_accepted_at' => null]);
        break;
      case 'rollback_upload_secondary':
        $order->update(['status' => 'upload_secondary', 'secondary_uploaded_at' => null, 'secondary_accepted_at' => null]);
        if ($order->shop_id) {
          Sms::send(
            Shop::mobile($order->shop_id),
            'فروشنده محترم، ' .
              'چکهای بارگذاری شده برای سفارش ' . $order->oid . ' توسط کارشناس بررسی شد. نیاز به چند اصلاح است. لطفاً به صفحه سفارش خود مراجعه نمایید.'
          );
          Sms::send($order->user->username, $order->user->fname . ' عزیز، ' . 'تصویر چکهای بارگذاری شده برای سفارش ' . $order->oid . ' توسط کارشناس بررسی شد. نیاز به چند اصلاح است. جهت کسب اطلاعات بیشتر، موارد را از فروشنده پیگیری یا به صفحه سفارش خود مراجعه کنید.');
        } else {
          Sms::send($order->user->username, $order->user->fname . ' عزیز، ' . 'تصویر چکهای بارگذاری شده برای سفارش ' . $order->oid . ' توسط کارشناس بررسی شد. نیاز به چند اصلاح است. لطفاً به صفحه سفارش خود مراجعه نمایید.');
        }
        break;
      case 'rollback_check_secondary':
        $order->update(['status' => 'check_secondary', 'secondary_accepted_at' => null]);
        break;
      case 'rollback_wait_for_cheques':
        $order->update(['status' => 'wait_for_cheques', 'docs_received_at' => null]);
        break;
      case 'recovery':
        $order->update([
          'status' => 'draft',
          'closed_at' => null,
          'docs_received_at' => null,
          'docs_accepted_at' => null,
          'secondary_uploded_at' => null,
          'secondary_accepted_at' => null,
        ]);
        break;
      default:
        break;
    }
    $meta = $order->meta;
    $meta[$status . '_by'] = $auth_user->id;
    $order->update(['meta' => $meta]);
    return Useful::ok();
  }

  //ADMIN
  public function edit(Request $request)
  {
    $oid = $request->oid;
    $order = Order::oid($oid);
    $status = $order->status;
    $status = Order::status_fixer($order);
    $product = $request->product;
    $amount = $request->amount;
    $order->amount = $amount;
    $prepayment = $request->prepayment;
    $order->prepayment = $prepayment;
    $months = $request->months;
    $order->months = $months;
    $cheques = $request->cheques;
    $order->cheques = $cheques;
    $gain = $request->gain;
    $order->gain = $gain;
    $payback_type = $request->payback_type;
    $series = $request->series;
    $series_card = $request->series_card;
    $charge_type = $request->charge_type;
    $ghestify = Order::ghestify($order, 1);
    $arr = [
      'amount' => $amount,
      'prepayment' => $prepayment,
      'months' => $months,
      'cheques' => $cheques,
      'gain' => $gain,
      'ghest' => $ghestify->ghest,
      'total' => $ghestify->total,
      'product' => $product,
      'payback_type' => $payback_type,
      'status' => $status,
      'series' => empty($series) ? null : $series,
      'series_card' => empty($series_card) ? null : $series_card,
      'charge_type' => $charge_type,
    ];
    if (
      $request->first_ghest_at &&
      in_array($status, ['submitted', 'docs_uploaded', 'upload_secondary', 'upload_secondary', 'check_secondary'])
    ) {
      $first_ghest = explode('-', $request->first_ghest_at);
      if ($first_ghest[2] == 31) {
        $first_ghest[2] = 1;
        ++$first_ghest[1];
      }
      $TS = Edate::jmktime('6', '0', '0', $first_ghest[1], $first_ghest[2], $first_ghest[0]);
      $first_ghest_at = Carbon::createFromTimestamp($TS, 'Asia/Tehran')->toDateTimeString();
      $arr['first_ghest_at'] = $first_ghest_at;
    }
    if ($order->update($arr)) return Useful::ok();
    return Useful::nok(['dbo-e', 'خطا در ویرایش سفارش']);
  }

  //ADMIN
  public function counts(Request $request)
  {
    $Status = Order::$states;
    foreach ($Status as $status) {
      $res[$status] = Order::searchStatus($status)->count();
    }
    return Useful::ok($res);
  }

  //ORGAN
  public function accept_order(Request $request)
  {
    if (Organ::select('status')->where('user_id', Auth::user()->id)->first()->status != 'active')
      return Useful::nok(['forbidden']);

    $oid = $request->oid;
    $order = Order::oid($oid);
    $this->guard($order);
    $summary = OrganController::summary($order->organ_id);
    if ($summary->available_extra <= Order::required_credit($order)) {
      return Useful::nok(['credit_not_enough', 'اعتبار کافی نیست']);
    }
    $order->update(['organ_accepted_at' => Carbon::now()]);
    return Useful::ok();
  }

  //ORGAN
  public function reject_order(Request $request)
  {
    if (Organ::select('status')->where('user_id', Auth::user()->id)->first()->status != 'active')
      return Useful::nok(['forbidden']);

    $oid = $request->oid;
    $reason = $request->reason;
    $order = Order::oid($oid);
    $this->guard($order);
    if (!$reason) $reason = 'این سفارش توسط سازمان رد شده است. جهت اطلاعات بیشتر از مسئول مربوطه در سازمان خود پیگیری کنید.';
    $order->update(['status' => 'rejected', 'closed_at' => Carbon::now(), 'reason' => $reason]);
    $user = User::info($order->user_id);
    Sms::send(
      $user->mobile,
      Pattern::user_order_rejected_by_organ,
      [
        'name' => $user->fname,
        'oid' => $order->oid,
      ]
    );
    return Useful::ok();
  }

  public function contract(Request $request)
  {
    $oid = $request->oid;
    $order = Order::where('oid', $oid)->with([
      'user.addresses',
      'shop',
      'organ' => function ($query) {
        $query->select('id', 'type', 'name', 'fame', 'payback_type');
      }
    ])->first();
    $this->guard($order);
    $user = User::info($order->user_id);
    if ($order->shop_id) {
      $owner = User::info($order->shop->user_id);
      $shop = [
        'name' => $order->shop->name,
        'type' => $order->shop->type,
        'state' => Address::state_name($order->shop->state),
        'city' => $order->shop->city,
        'address' => $order->shop->address,
        'phone' => $order->shop->phone,
        'owner_name' => $owner->full_name,
        'owner_mobile' => $owner->mobile,
      ];
    } else $shop = null;
    if ($order->organ_id) {
      $organ = [
        'name' => $order->organ->fame ? $order->organ->fame : $order->organ->name,
        'type' => $order->organ->type,
        'payback_type' => $order->organ->payback_type,
        'payback_type_farsi' => $order->organ->payback_type == 'gcheck' ? 'چک ضمانت' : 'سفته',
      ];
    } else $organ = null;

    $a['work'] = null;
    foreach ($order->user->addresses as $address) {
      $a[$address->type] = [
        'address' => Address::state_name($address->state) . ' - ' . $address->city . ' - ' . $address->address,
        'phone' => $address->phone
      ];
    }

    $cheques_info = Order::cheques_info($order);

    if (is_null($cheques_info['iban']) && $order->payback_type == 'cheque')
      return Useful::nok(['cheques_not_found', 'ابتدا اطلاعات چک ها را در فرم مربوطه وارد کنید.']);

    $data = [
      'oid' => $order->oid,
      'product' => $order->product,
      'amount' => $order->amount,
      'prepayment' => $order->prepayment,
      'total' => $order->total,
      'months' => $order->months,
      'cheques' => $order->cheques,
      'payback_type' => $order->payback_type,
      'organ_id' => $order->organ_id,
      'organ' => $organ,
      'shop_id' => $order->shop_id,
      'shop' => $shop,
      'user_name' => $user->full_name,
      'user_nid' => $user->nid,
      'user_mobile' => $user->mobile,
      'cheques_info' => $cheques_info,
      'home_address' => $a['home']['address'],
      'home_phone' => $a['home']['phone'],
      'work_address' => count($a) > 1 && $a['work'] ? (strlen($a['work']['address']) > 5 ? $a['work']['address'] : '') : '',
      'work_phone' => count($a) > 1 && $a['work'] ? $a['work']['phone'] : '',
    ];
    if ($order->payback_type == 'cheque') {
      $cheque_numbers = Cheque::select('id', 'account_id', 'series')
        ->where('order_id', $order->id)->where('type', 'chf')->orderBy('badge', 'asc')->get();

      if (!$cheque_numbers || $cheque_numbers->count() === 0)
        $cheque_numbers = Ghest::select('id', 'account_id', 'series')
          ->where('order_id', $order->id)->orderBy('shamsi', 'asc')->get();

      $account = Account::select('bank_id', 'branch_name', 'branch_code', 'iban')
        ->whereId($cheque_numbers[0]['account_id'])->first();

      $data['bank_name'] = config('banks.' . $account->bank_id)['fa'];
      $data['bank_branch_name'] = $account->branch_name;
      $data['bank_branch_code'] = $account->branch_code;
      $data['bank_iban'] = $account->iban;
      $data['cheque_numbers'] = $cheque_numbers;
    }

    return Useful::ok($data);
  }

  public function card_stick(Request $request)
  {
    $oid = $request->oid;
    $order = Order::where('oid', $oid)->with([
      'user.addresses',
      'shop',
      'organ' => function ($query) {
        $query->select('id', 'type', 'name', 'fame', 'payback_type');
      }
    ])->first();
    $this->guard($order);
    $user = User::info($order->user_id);
    if ($order->shop_id) {
      $owner = User::info($order->shop->user_id);
      $shop = [
        'name' => $order->shop->name,
        'type' => $order->shop->type,
        'state' => Address::state_name($order->shop->state),
        'city' => $order->shop->city,
        'address' => $order->shop->address,
        'phone' => $order->shop->phone,
        'owner_name' => $owner->full_name,
        'owner_mobile' => $owner->mobile,
      ];
    } else $shop = null;
    if ($order->organ_id) {
      $organ = [
        'name' => $order->organ->fame ? $order->organ->fame : $order->organ->name,
        'type' => $order->organ->type,
        'payback_type' => $order->organ->payback_type,
        'payback_type_farsi' => $order->organ->payback_type == 'gcheck' ? 'چک ضمانت' : 'سفته',
      ];
    } else $organ = null;

    $a['work'] = null;
    foreach ($order->user->addresses as $address) {
      $a[$address->type] = [
        'address' => Address::state_name($address->state) . ' - ' . $address->city . ' - ' . $address->address,
        'post' => $address->postal_code
      ];
    }

    $data = [
      'name' => $user->full_name,
      'nid' => $user->nid,
      'mobile' => $user->mobile,
      'address' => $a[$request->address_type]['address'],
      'post' => $a[$request->address_type]['post']
    ];

    return Useful::ok($data);
  }

  //ADMIN
  public function chart_overview(Request $request)
  {
    $states = [
      [
        'name' => 'today',
        'title' => 'امروز',
        'from_date' => Edate::edate('Y-m-d'),
        'to_date' => Edate::edate('Y-m-d'),
      ],
      [
        'name' => 'yesterday',
        'title' => 'دیروز',
        'from_date' => Edate::edate('Y-m-d', time() - 82500),
        'to_date' => Edate::edate('Y-m-d', time() - 82500),
      ],
      [
        'name' => 'this_month',
        'title' => 'ماه جاری' . ' (' . Edate::edate('F') . ')',
        'from_date' => Edate::this_month_span()[0],
        'to_date' => Edate::this_month_span()[1],
      ],
      [
        'name' => 'last_month',
        'title' => 'ماه گذشته' . ' (' . Edate::last_month_span()[2] . ')',
        'from_date' => Edate::last_month_span()[0],
        'to_date' => Edate::last_month_span()[1],
      ],
      [
        'name' => 'this_year',
        'title' => 'سال جاری',
        'from_date' => Edate::edate('Y-') . '01-01',
        'to_date' => Edate::edate('Y-m-d'),
      ],
      [
        'name' => 'last_year',
        'title' => 'سال گذشته',
        'from_date' => Edate::last_year_span()[0],
        'to_date' => Edate::last_year_span()[1],
      ],
    ];

    $new = [];
    $n = 0;
    foreach ($states as $state) {
      $orders_count = Order::select('id')->successful()
        ->searchDate('charged_at', $state['from_date'], $state['to_date'])->count();

      $orders_count_shop = Order::select('id')->successful()->whereNotNull('shop_id')
        ->searchDate('charged_at', $state['from_date'], $state['to_date'])->count();

      $orders_count_organ = Order::select('id')->successful()->whereNotNull('organ_id')
        ->searchDate('charged_at', $state['from_date'], $state['to_date'])->count();

      $orders_count_user = $orders_count - $orders_count_shop - $orders_count_organ;

      $col['date'] = [
        'title' => '',
        'value' => $state['title'],
      ];
      $col['user'] = [
        'title' => 'شخصی',
        'value' => $orders_count_user,
        'type' => 'countable'
      ];
      $col['shop'] = [
        'title' => 'فروشگاهی',
        'value' => $orders_count_shop,
        'type' => 'countable'
      ];
      $col['organ'] = [
        'title' => 'سازمانی',
        'value' => $orders_count_organ,
        'type' => 'countable'
      ];
      $col['total'] = [
        'title' => 'مجموع',
        'value' => $orders_count,
        'type' => 'countable'
      ];
      $new[$n]['td'] = $col;
      $col = null;
      ++$n;
    }

    return Useful::ok($new);
  }

  //ADMIN
  public function chart_pie(Request $request)
  {
    $months = $request->months;
    if (!$months) $months = 1;
    $months = 'all';

    if ($months == 'all') {
      $orders_count = Order::select('id')->successful()->count();

      $orders_count_shop = Order::select('id')->successful()->whereNotNull('shop_id')->count();

      $orders_count_organ = Order::select('id')->successful()->whereNotNull('organ_id')->count();

      $orders_count_user = $orders_count - $orders_count_shop - $orders_count_organ;
    } else {
      $orders_count = Order::select('id')->successful()
        ->whereDate('charged_at', '>=', Carbon::now()->subMonths($months))->count();

      $orders_count_shop = Order::select('id')->successful()->whereNotNull('shop_id')
        ->whereDate('charged_at', '>=', Carbon::now()->subMonths($months))->count();

      $orders_count_organ = Order::select('id')->successful()->whereNotNull('organ_id')
        ->whereDate('charged_at', '>=', Carbon::now()->subMonths($months))->count();

      $orders_count_user = $orders_count - $orders_count_shop - $orders_count_organ;
    }

    $data = [
      [
        'name' => 'شخصی',
        'data' => (int) $orders_count_user,
        'type' => 'countable'
      ],
      [
        'name' => 'فروشگاهی',
        'data' => (int) $orders_count_shop,
        'type' => 'countable'
      ],
      [
        'name' => 'سازمانی',
        'data' => (int) $orders_count_organ,
        'type' => 'countable'
      ],
    ];

    $colors = ['#00BFA5', '#f44336', '#03A9F4'];

    return Useful::ok(['series' => $data, 'colors' => $colors]);
  }

  //ADMIN
  public function chart_line(Request $request)
  {
    $months = $request->months;
    if (!$months) $months = 1;
    $months = explode('?', $months)[0];
    $from_date = $request->from_date;
    $to_date = $request->to_date;

    if ($from_date && $to_date) {
      $orders = Order::select('id', 'charged_at', 'shop_id', 'organ_id')->successful()
        ->searchDate('charged_at', $from_date, $to_date)->orderBy('charged_at', 'asc')->get();
    } else {
      $orders = Order::select('id', 'charged_at', 'shop_id', 'organ_id')->successful()
        ->whereDate('charged_at', '>=', Carbon::now()->subMonths($months))->orderBy('charged_at', 'asc')->get();
    }

    $filtered = $orders->groupBy(function ($item, $key) {
      return Carbon::parse($item->charged_at)->format('Y-m-d');
    });
    $datetimes = $filtered->keys()->toArray();

    $user = [];
    $shop = [];
    $organ = [];

    $n = 0;
    foreach ($filtered as $group) {
      $all_count = count($group);
      $shop_count = count($group->whereNotNull('shop_id'));
      $organ_count = count($group->whereNotNull('organ_id'));
      $user_count = $all_count - $shop_count - $organ_count;

      $shop[$n] = [
        $datetimes[$n] . 'T06:00:00.000Z',
        $shop_count
      ];
      $organ[$n] = [
        $datetimes[$n] . 'T06:00:00.000Z',
        $organ_count
      ];
      $user[$n] = [
        $datetimes[$n] . 'T06:00:00.000Z',
        $user_count
      ];
      ++$n;
    }

    $data = [
      [
        'name' => 'شخصی',
        'data' => $user,
      ],
      [
        'name' => 'فروشگاهی',
        'data' => $shop,
      ],
      [
        'name' => 'سازمانی',
        'data' => $organ,
      ],
    ];

    $colors = ['#00BFA5', '#f44336', '#03A9F4', '#0D47A1'];

    return Useful::ok(['series' => $data, 'colors' => $colors]);
  }

  //ADMIN
  public function chart_overview_sales(Request $request)
  {
    $states = [
      [
        'name' => 'today',
        'title' => 'امروز',
        'from_date' => Edate::edate('Y-m-d'),
        'to_date' => Edate::edate('Y-m-d'),
      ],
      [
        'name' => 'yesterday',
        'title' => 'دیروز',
        'from_date' => Edate::edate('Y-m-d', time() - 82500),
        'to_date' => Edate::edate('Y-m-d', time() - 82500),
      ],
      [
        'name' => 'this_month',
        'title' => 'ماه جاری' . ' (' . Edate::edate('F') . ')',
        'from_date' => Edate::this_month_span()[0],
        'to_date' => Edate::this_month_span()[1],
      ],
      [
        'name' => 'last_month',
        'title' => 'ماه گذشته' . ' (' . Edate::last_month_span()[2] . ')',
        'from_date' => Edate::last_month_span()[0],
        'to_date' => Edate::last_month_span()[1],
      ],
      [
        'name' => 'this_year',
        'title' => 'سال جاری',
        'from_date' => Edate::edate('Y-') . '01-01',
        'to_date' => Edate::edate('Y-m-d'),
      ],
      [
        'name' => 'last_year',
        'title' => 'سال گذشته',
        'from_date' => Edate::last_year_span()[0],
        'to_date' => Edate::last_year_span()[1],
      ],
    ];

    $new = [];
    $n = 0;
    foreach ($states as $state) {
      $orders_sale = Order::successful()
        ->searchDate('charged_at', $state['from_date'], $state['to_date'])->sum('amount');

      $orders_sale_shop = Order::successful()->whereNotNull('shop_id')
        ->searchDate('charged_at', $state['from_date'], $state['to_date'])->sum('amount');

      $orders_sale_organ = Order::successful()->whereNotNull('organ_id')
        ->searchDate('charged_at', $state['from_date'], $state['to_date'])->sum('amount');

      $orders_sale_user = $orders_sale - $orders_sale_shop - $orders_sale_organ;

      $col['date'] = [
        'title' => '',
        'value' => $state['title'],
      ];
      $col['user'] = [
        'title' => 'شخصی',
        'value' => Useful::round($orders_sale_user, 100000),
        'type' => 'countable'
      ];
      $col['shop'] = [
        'title' => 'فروشگاهی',
        'value' => Useful::round($orders_sale_shop, 100000),
        'type' => 'countable'
      ];
      $col['organ'] = [
        'title' => 'سازمانی',
        'value' => Useful::round($orders_sale_organ, 100000),
        'type' => 'countable'
      ];
      $col['total'] = [
        'title' => 'مجموع',
        'value' => Useful::round($orders_sale, 100000),
        'type' => 'countable'
      ];
      $new[$n]['td'] = $col;
      $col = null;
      ++$n;
    }

    return Useful::ok($new);
  }

  //ADMIN
  public function chart_pie_sales(Request $request)
  {
    $months = $request->months;
    if (!$months) $months = 1;
    $months = 'all';

    if ($months == 'all') {
      $orders_sale = Order::successful()->sum('amount');

      $orders_sale_shop = Order::successful()->whereNotNull('shop_id')->sum('amount');

      $orders_sale_organ = Order::successful()->whereNotNull('organ_id')->sum('amount');

      $orders_sale_user = $orders_sale - $orders_sale_shop - $orders_sale_organ;
    } else {
      $orders_sale = Order::successful()
        ->whereDate('charged_at', '>=', Carbon::now()->subMonths($months))->sum('amount');

      $orders_sale_shop = Order::successful()->whereNotNull('shop_id')
        ->whereDate('charged_at', '>=', Carbon::now()->subMonths($months))->sum('amount');

      $orders_sale_organ = Order::successful()->whereNotNull('organ_id')
        ->whereDate('charged_at', '>=', Carbon::now()->subMonths($months))->sum('amount');

      $orders_sale_user = $orders_sale - $orders_sale_shop - $orders_sale_organ;
    }

    $data = [
      [
        'name' => 'شخصی',
        'data' => Useful::round($orders_sale_user, 100000),
      ],
      [
        'name' => 'فروشگاهی',
        'data' => Useful::round($orders_sale_shop, 100000),
      ],
      [
        'name' => 'سازمانی',
        'data' => Useful::round($orders_sale_organ, 100000),
      ],
    ];

    $colors = ['#00BFA5', '#f44336', '#03A9F4'];

    return Useful::ok(['series' => $data, 'colors' => $colors]);
  }

  //ADMIN
  public function chart_line_sales(Request $request)
  {
    $months = $request->months;
    if (!$months) $months = 1;
    $months = explode('?', $months)[0];
    $from_date = $request->from_date;
    $to_date = $request->to_date;

    if ($from_date && $to_date) {
      $orders = Order::select('id', 'charged_at', 'shop_id', 'organ_id', 'amount')->successful()
        ->searchDate('charged_at', $from_date, $to_date)->orderBy('charged_at', 'asc')->get();
    } else {
      $orders = Order::select('id', 'charged_at', 'shop_id', 'organ_id', 'amount')->successful()
        ->whereDate('charged_at', '>=', Carbon::now()->subMonths($months))->orderBy('charged_at', 'asc')->get();
    }

    $filtered = $orders->groupBy(function ($item, $key) {
      return Carbon::parse($item->charged_at)->format('Y-m-d');
    });
    $datetimes = $filtered->keys()->toArray();

    $user = [];
    $shop = [];
    $organ = [];

    $n = 0;
    foreach ($filtered as $group) {
      $all_sale = Useful::round($group->sum('amount'), 100000);
      if ($all_sale == 0) {
        ++$n;
        continue;
      }
      $shop_sale = Useful::round($group->whereNotNull('shop_id')->sum('amount'), 100000);
      $organ_sale = Useful::round($group->whereNotNull('organ_id')->sum('amount'), 100000);
      $user_sale = $all_sale - $shop_sale - $organ_sale;

      $shop[$n] = [
        $datetimes[$n] . 'T06:00:00.000Z',
        (int) $shop_sale
      ];
      $organ[$n] = [
        $datetimes[$n] . 'T06:00:00.000Z',
        (int) $organ_sale
      ];
      $user[$n] = [
        $datetimes[$n] . 'T06:00:00.000Z',
        (int) $user_sale
      ];
      ++$n;
    }

    $data = [
      [
        'name' => 'شخصی',
        'data' => $user,
      ],
      [
        'name' => 'فروشگاهی',
        'data' => $shop,
      ],
      [
        'name' => 'سازمانی',
        'data' => $organ,
      ],
    ];

    $colors = ['#00BFA5', '#f44336', '#03A9F4', '#0D47A1'];

    return Useful::ok(['series' => $data, 'colors' => $colors]);
  }
}
