<?php

namespace App\Http\Controllers;

use App\Models\Edate;
use App\Models\Ghest;
use App\Models\ICBS;
use App\Models\Order;
use App\Models\Pattern;
use App\Models\Shop;
use App\Models\Sms;
use App\Models\Useful;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QueueController extends Controller
{
    public static function orders_cancel_upload_secondary_after($days)
    {
        $now = Carbon::now();
        Order::where('status', 'upload_secondary')->whereDate('docs_accepted_at', '<', Carbon::now()->subDays($days))->update([
            'status' => 'cancelled',
            'closed_at' => $now,
            'reason' => "عدم بارگذاری مدارک تکمیلی (تصویر چک ها) در مهلت $days روزه"
        ]);
        return 1;
    }

    public static function orders_cancel_submitted_after($days)
    {
        $now = Carbon::now();
        Order::where('status', 'submitted')->whereDate('created_at', '<', Carbon::now()->subDays($days + 2))->update([
            'status' => 'cancelled',
            'closed_at' => $now,
            'reason' => "عدم بارگذاری مدارک موردنیاز در $days روز گذشته"
        ]);
        return 1;
    }

    public static function orders_sync_hastinja()
    {
        $orders_hi = DB::connection('hastinja')->table('orders')
            ->whereIn('status', ['wait_for_cheques', 'prepayment', 'prepaid', 'cycle'])
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->select('users.nid', 'users.username', 'orders.id', 'orders.user_id', 'orders.oid')
            ->get();

        foreach ($orders_hi as $order_hi) {
            $order_id = $order_hi->id;
            $nid = $order_hi->nid;
            $order = Order::select('id', 'user_id', 'oid')->searchExact('nid', $nid, 'user')->first();
            if ($order) {
                $order->update(['tag' => 'hi-' . $order_id]);
                DB::connection('hastinja')->table('orders')->where('id', $order_id)->update(['tag' => $order->oid]);
            }
        }

        return 1;
    }

    public static function orders_inquiry()
    {
        $start = time();

        $orders = Order::where('status', 'draft')
            ->whereNotNull('inquiry_paid_at')
            ->with('user')
            ->orderBy('id', 'asc')->limit(100)->get();

        foreach ($orders as $order) {
            //REG
            if (!$order->user->birth || !$order->user->nid) {
                User::whereId($order->user_id)->update(['birth' => null, 'rejected_at' => Carbon::now()]);
                Sms::send($order->user->username, "کاربر گرامی، طبق نتیجه استعلام، تاریخ تولد اظهارشده در حساب کاربری با مشخصات حقیقی شما مغایرت دارد. جهت انجام مجدد استعلام، این مورد را با مراجعه به صفحه سفارش خود اصلاح نمایید.");
                continue;
            }

            if (!$order->user->verified_at && !$order->user->rejected_at) {
                $inq_reg = ICBS::RegInfo($order->user->nid, $order->user->birth, 1);

                if (!isset($inq_reg->result[ICBS::$method_RegInfo . 'Result']) || $inq_reg->status == 0) continue;

                $inq_reg = $inq_reg->result[ICBS::$method_RegInfo . 'Result'];

                if (empty($inq_reg['BirthDate']) || empty($inq_reg['Gender'])) {
                    User::whereId($order->user_id)->update(['rejected_at' => Carbon::now()]);
                    ICBS::where('method', ICBS::$method_RegInfo)->where('nid', $order->user->nid)->delete();
                    Useful::report("👤" . " " . "مغایرت استعلام هویتی", [
                        'نام' => Useful::std($inq_reg['Name']) . ' ' . Useful::std($inq_reg['LastName']),
                        'موبایل' => $order->user->mobile,
                        'کدملی' => $order->user->nid,
                        'تاریخ تولد اظهارشده' => $order->user->birth,
                    ]);
                    Sms::send($order->user->username, "کاربر گرامی، طبق استعلام صورت گرفته، تاریخ تولد اظهارشده در حساب کاربری با مشخصات حقیقی شما مغایرت دارد. جهت انجام مجدد استعلام، این مورد را با مراجعه به پنل اصلاح نمایید.");
                    continue;
                }

                if (Useful::std($inq_reg['DeathStatus']) != 'زنده') {
                    Useful::report("⚰️" . " " . "فوت شده", [
                        'نام' => Useful::std($inq_reg['Name']) . ' ' . Useful::std($inq_reg['LastName']),
                        'موبایل' => $order->user->mobile,
                        'کدملی' => $order->user->nid,
                    ]);
                }

                DB::table('users')->where('id', $order->user_id)->update([
                    'fname' => Useful::std($inq_reg['Name']),
                    'lname' => Useful::std($inq_reg['LastName']),
                    'verified_at' => Carbon::now(),
                    'gender' => Useful::std($inq_reg['Gender']) == 'مرد' ? 'M' : 'F'
                ]);
            }
            if (time() - $start > 45) break;

            //BANK
            $inq_bank = ICBS::IntegratedCs($order->user->nid); 
            
            if (!isset($inq_bank->result[ICBS::$method_IntegratedCs . 'Result']) || $inq_bank->status == 0)
                $inq_bank = ICBS::IntegratedCs($order->user->nid);

            $inq_bank = $inq_bank->result[ICBS::$method_IntegratedCs . 'Result'];

            if (!isset($inq_bank['BankCsCheckInfo'])) {
                ICBS::where('nid', $order->user->nid)->where('method', ICBS::$method_IntegratedCs)->orderBy('id', 'desc')->limit(1)->delete();
                Useful::report('⭕️ ' . 'خطای استعلام بانکی', [
                    'کدملی' => $order->user->nid,
                    'علت' => 'BankCsCheckInfo'
                ]);
                continue;
            }

            if (!isset($inq_bank['BankCsCheckInfo']['CheckCount'])) {
                ICBS::where('nid', $order->user->nid)->where('method', ICBS::$method_IntegratedCs)->orderBy('id', 'desc')->limit(1)->delete();
                Useful::report('⭕️ ' . 'خطای استعلام بانکی', [
                    'کدملی' => $order->user->nid,
                    'علت' => 'CheckCount'
                ]);
                continue;
            }

            if ($inq_bank['BankCsCheckInfo']['CheckCount'] > 0) {
                Sms::send($order->user->username, Pattern::order_rejected_bch, ['oid' => $order->oid]);
                $order->update([
                    'status' => 'rejected',
                    'closed_at' => Carbon::now(),
                    'reason' => 'طبق استعلام سیستمی، کاربر دارای چک برگشتی در شبکه بانکی است.'
                ]);
            } else {
                $user = User::whereId($order->user_id)->first();
                if ($order->shop_id) {
                    Sms::send(
                        $user->username,
                        Pattern::user_order_by_shop_submitted,
                        [
                            'name' => $user->fname,
                            'oid' => $order->oid,
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
                            'oid' => $order->oid,
                        ]
                    );
                }
                $order->update(['status' => 'submitted']);
            }

            if (time() - $start > 45) break;
        }

        return 1;
    }

    public static function ghests_auto_pass_cheques()
    {
        $t = Edate::edateFromCarbon('Ymd', Carbon::now()->subDays(2));
        $f = Edate::edateFromCarbon('Ymd', Carbon::now()->subDays(5));
        $ghests = Ghest::where('type', 'cheque')->whereBetween('shamsi', [$f, $t])->whereNull('passed_at')->whereNull('backed_at')->get();

        foreach ($ghests as $ghest) {
            $ghest_date = $ghest->ghest_date;
            if ($ghest->update(['passed_at' => $ghest_date])) {
                $order_id = $ghest->order_id;
                $passed_count = Ghest::where('order_id', $order_id)->passed()->count();
                Order::whereId($order_id)->update(['passed' => $passed_count]);
                $order = Order::whereId($order_id)->whereColumn('cheques', 'passed')->first();
                if ($order) {
                    $user = User::info($order->user_id);
                    $order->update(['status' => 'completed', 'closed_at' => Carbon::now()]);
                    Sms::send(
                        $user->username,
                        Pattern::user_order_completed,
                        [
                            'name' => $user->fname,
                            'oid' => $order->oid,
                        ]
                    );
                }
            }
        }

        return 1;
    }

    public static function reminder_near()
    {
        $startTS = time();

        $ghests = Ghest::whereNull('first_alarm_at')->notPassed()->soClose()->with([
            'order' => function ($q) {
                $q->select('id', 'user_id', 'oid', 'payback_type');
            },
            'order.user' => function ($q) {
                $q->select('id', 'fname', 'username');
            }
        ])->get();

        foreach ($ghests as $ghest) {
            if ($ghest->order->payback_type == 'cheque') {
                Sms::send(
                    $ghest->order->user->username,
                    Pattern::user_order_cheque_reminder_1,
                    [
                        'name' => $ghest->order->user->fname,
                        'oid' => $ghest->order->oid,
                        'amount' => number_format($ghest->amount),
                        'date' => Edate::edateFromCarbon('j F Y', $ghest->ghest_date, 'fa'),
                    ]
                );
            }
            DB::table('ghests')->where('id', $ghest->id)->update(['first_alarm_at' => Carbon::now()]);

            if (time() - $startTS > 40) break;
            usleep(mt_rand(500000, 1500000));
        }
        return 1;
    }

    public static function reminder_tomorrow()
    {
        $startTS = time();

        $ghests = Ghest::whereNull('last_alarm_at')->notPassed()->tomorrow()->with([
            'order' => function ($q) {
                $q->select('id', 'user_id', 'oid', 'payback_type');
            },
            'order.user' => function ($q) {
                $q->select('id', 'fname', 'username');
            }
        ])->get();

        foreach ($ghests as $ghest) {
            if ($ghest->order->payback_type == 'cheque') {
                Sms::send(
                    $ghest->order->user->username,
                    Pattern::user_order_cheque_reminder_2_alt,
                    [
                        'name' => $ghest->order->user->fname,
                        'oid' => $ghest->order->oid,
                    ]
                );
            } else {
                $oum =  Ghest::where('order_id', $ghest->order->id)->passed()->count() + 1;
                Sms::send(
                    $ghest->order->user->username,
                    Pattern::user_order_ghest_reminder,
                    [
                        'name' => $ghest->order->user->fname,
                        'oid' => $ghest->order->oid,
                        'oum' => Useful::oum($oum),
                    ]
                );
            }
            DB::table('ghests')->where('id', $ghest->id)->update(['last_alarm_at' => Carbon::now()]);

            if (time() - $startTS > 40) break;
            usleep(mt_rand(500000, 1500000));
        }
        return 1;
    }

    public static function notif_bch1()
    {
        $startTS = time();

        $ghests = Ghest::select('id', 'order_id', 'ghest_date', 'type')->whereNull('bch1_notif_at')->backed()->whereNull('passed_at')
            ->with([
                'order' => function ($q) {
                    $q->select('id', 'user_id', 'oid', 'payback_type');
                },
                'order.user' => function ($q) {
                    $q->select('id', 'fname', 'lname', 'username');
                }
            ])->get();

        foreach ($ghests as $ghest) {
            if ($ghest->order->payback_type != 'cheque') continue;

            Sms::send(
                $ghest->order->user->username,
                'مشتری گرامی، متاسفانه چک شما به تاریخ ' .
                    Edate::edateFromCarbon('Y-m-d', $ghest->ghest_date) .
                    'برگشت خورده است.لطفا پس از تأمین موجودی حساب، برای پاس شدن چک خود با قسطا تماس بگیرید.' . "\n" .
                    'تلفن: 02191070092',
            );

            DB::table('ghests')->where('id', $ghest->id)->update(['bch1_notif_at' => Carbon::now()]);

            if (time() - $startTS > 40) break;
            usleep(mt_rand(500000, 1500000));
        }
        return 1;
    }

    public static function notif_bch2()
    {
        $startTS = time();

        $ghests = Ghest::select('id', 'order_id', 'ghest_date', 'type')->whereNull('bch2_notif_at')->backed(7)->whereNull('passed_at')
            ->with([
                'order' => function ($q) {
                    $q->select('id', 'user_id', 'oid', 'payback_type');
                },
                'order.user' => function ($q) {
                    $q->select('id', 'fname', 'lname', 'username');
                }
            ])->get();

        foreach ($ghests as $ghest) {
            if ($ghest->order->payback_type != 'cheque') continue;

            Sms::send(
                $ghest->order->user->username,
                'مشتری گرامی، متاسفانه چک شما به تاریخ ' .
                    Edate::edateFromCarbon('Y-m-d', $ghest->ghest_date) .
                    'همچنان در وضعیت برگشتی باقی مانده است. برای جلوگیری از سوء اثر در سوابق بانکی خود هرچه سریعتر پس از تأمین موجودی حساب، با قسطا تماس بگیرید. ' . "\n" .
                    'تلفن: 02191070092',
            );

            DB::table('ghests')->where('id', $ghest->id)->update(['bch2_notif_at' => Carbon::now()]);

            if (time() - $startTS > 40) break;
            usleep(mt_rand(500000, 1500000));
        }
        return 1;
    }

    public static function notif_bch3()
    {
        $startTS = time();

        $ghests = Ghest::select('id', 'order_id', 'ghest_date', 'type')->whereNull('bch3_notif_at')->backed(23)->whereNull('passed_at')
            ->with([
                'order' => function ($q) {
                    $q->select('id', 'user_id', 'oid', 'payback_type');
                },
                'order.user' => function ($q) {
                    $q->select('id', 'fname', 'lname', 'username');
                }
            ])->get();

        foreach ($ghests as $ghest) {
            if ($ghest->order->payback_type != 'cheque') continue;

            Sms::send(
                $ghest->order->user->username,
                'جناب آقا/خانم ' . $ghest->order->user->full_name .
                    ' پیگیری قضایی چک برگشتی شما در 10 روز آینده آغاز خواهد شد. برای جلوگیری از اقدام قضایی و پاس کردن چک خود هرچه سریعتر با قسطا تماس بگیرید.' . "\n" .
                    'تلفن: 02191070092',
            );

            DB::table('ghests')->where('id', $ghest->id)->update(['bch3_notif_at' => Carbon::now()]);

            if (time() - $startTS > 40) break;
            usleep(mt_rand(500000, 1500000));
        }
        return 1;
    }

    public static function users_reginfo()
    {
        $start = time();

        $users = User::whereNotNull('verified_at')->whereNull('fname')->whereNotNull('nid')->whereNotNull('birth')->get();

        foreach ($users as $user) {
            $inq_reg = ICBS::RegInfo($user->nid, $user->birth, 1);

            if (!isset($inq_reg->result[ICBS::$method_RegInfo . 'Result']) || $inq_reg->status == 0) continue;

            $inq_reg = $inq_reg->result[ICBS::$method_RegInfo . 'Result'];

            if (empty($inq_reg['BirthDate']) || empty($inq_reg['Gender'])) {
                $user->update(['rejected_at' => Carbon::now()]);
                ICBS::where('method', ICBS::$method_RegInfo)->where('nid', $user->nid)->delete();
                Useful::report("👤" . " " . "مغایرت استعلام هویتی", [
                    'نام' => Useful::std($inq_reg['Name']) . ' ' . Useful::std($inq_reg['LastName']),
                    'موبایل' => $user->mobile,
                    'کدملی' => $user->nid,
                    'تاریخ تولد اظهارشده' => $user->birth,
                ]);
                continue;
            }

            if (Useful::std($inq_reg['DeathStatus']) != 'زنده') {
                Useful::report("⚰️" . " " . "فوت شده", [
                    'نام' => Useful::std($inq_reg['Name']) . ' ' . Useful::std($inq_reg['LastName']),
                    'موبایل' => $user->mobile,
                    'کدملی' => $user->nid,
                ]);
            }

            $user->where('id', $user->id)->update([
                'fname' => Useful::std($inq_reg['Name']),
                'lname' => Useful::std($inq_reg['LastName']),
                'verified_at' => Carbon::now(),
                'gender' => Useful::std($inq_reg['Gender']) == 'مرد' ? 'M' : 'F'
            ]);

            if (time() - $start > 45) break;
        }
        return 1;
    }

    public static function shops_reginfo()
    {
        $start = time();

        $shops = Shop::whereIn('status', ['docs_uploaded', 'final', 'active'])->orderBy('id', 'desc')->limit(10)
            ->with([
                'user' => function ($q) {
                    $q->whereNull('fname')->whereNotNull('nid')->whereNotNull('birth');
                }
            ])->get();

        foreach ($shops as $shop) {
            $user = $shop->user;
            if (!$user) continue;

            $inq_reg = ICBS::RegInfo($user->nid, $user->birth, 1);

            if (!isset($inq_reg->result[ICBS::$method_RegInfo . 'Result']) || $inq_reg->status == 0) continue;

            $inq_reg = $inq_reg->result[ICBS::$method_RegInfo . 'Result'];

            if (empty($inq_reg['BirthDate']) || empty($inq_reg['Gender'])) {
                $user->update(['rejected_at' => Carbon::now()]);
                ICBS::where('method', ICBS::$method_RegInfo)->where('nid', $user->nid)->delete();
                Useful::report("👤" . " " . "مغایرت استعلام هویتی", [
                    'نام' => Useful::std($inq_reg['Name']) . ' ' . Useful::std($inq_reg['LastName']),
                    'موبایل' => $user->mobile,
                    'کدملی' => $user->nid,
                    'تاریخ تولد اظهارشده' => $user->birth,
                ]);
                continue;
            }

            if (Useful::std($inq_reg['DeathStatus']) != 'زنده') {
                Useful::report("⚰️" . " " . "فوت شده", [
                    'نام' => Useful::std($inq_reg['Name']) . ' ' . Useful::std($inq_reg['LastName']),
                    'موبایل' => $user->mobile,
                    'کدملی' => $user->nid,
                ]);
            }

            $user->where('id', $user->id)->update([
                'fname' => Useful::std($inq_reg['Name']),
                'lname' => Useful::std($inq_reg['LastName']),
                'verified_at' => Carbon::now(),
                'gender' => Useful::std($inq_reg['Gender']) == 'مرد' ? 'M' : 'F'
            ]);

            if (time() - $start > 45) break;
        }
        return 1;
    }
}
