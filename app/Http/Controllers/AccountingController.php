<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Models\Edate;
use App\Models\Finnotech;
use App\Models\Ghest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Useful;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AccountingController extends Controller
{
    //users (have successful orders)
    public static function users(Request $request)
    {
        $type = $request->type;
        $nid = $request->nid;
        $from = $request->from_date;
        $from_ts = null;

        if ($from) {
            $x = explode('-', $from);
            $from_ts = Edate::jmktime(0, 0, 0, $x[1], $x[2], $x[0]);
        }

        if (strlen($nid) > 8) {
            $from = User::select('verified_at')->where('nid', $nid)->whereNotNull('verified_at')->first()->verified_at;
            $from_ts = Carbon::parse($from)->timestamp;
        }

        $users = User::select('id', 'fname', 'lname', 'nid', 'verified_at')
            ->where(function ($q) use ($from_ts) {
                if ($from_ts)
                    $q->whereDate('verified_at', '>=', Carbon::createFromTimestamp($from_ts));
                else
                    $q->whereNotNull('verified_at');
            })
            ->whereHas('orders', function ($q) {
                $q->whereIn('status', ['cycle', 'completed']);
            })->orderBy('verified_at', 'asc')->get();

        $data[0] = [
            'نوع قلم',
            'طرف حساب نوع',
            'طرف حساب نام',
            'طرف حساب نام خانوادگی',
            'طرف حساب عنوان',
            'طرف حساب کد'
        ];

        $lastNid = '';
        $lastDate = null;
        foreach ($users as $user) {
            $data[] = [
                '',
                1,
                $user->fname,
                $user->lname,
                $user->lname . ' ' . $user->fname . ' - ' . $user->nid,
                $user->nid
            ];
            $lastDate = $user->verified_at;
            $lastNid = $user->nid;
        }

        if ($type == 'xls')
            $url = ExportController::excel($data, 'users__' . $lastNid . '__' . Edate::edateFromCarbon('Y-m-d_H-i-s', $lastDate), 'strict');
        else
            $url = ExportController::csv($data, 'users__' . $lastNid . '__' . Edate::edateFromCarbon('Y-m-d_H-i-s', $lastDate), 'strict', $type);

        return Useful::ok(['url' => $url], ['action_type' => 'open_url']);
    }

    //cheques (successful orders)
    public static function cheques(Request $request)
    {
        $type = $request->type;
        $order_number = $request->order_number;
        $series = $request->series;

        $from = $request->from_date;
        $from_ts = null;

        if ($from) {
            $x = explode('-', $from);
            $from_ts = Edate::jmktime(0, 0, 0, $x[1], $x[2], $x[0]);
        }
        if (strlen($order_number) > 8) {
            $oid = 'GHC-' . (int)substr($order_number, 2);
            $from = Order::select('docs_received_at')->where('oid', $oid)->first()->docs_received_at;
            $from_ts = Carbon::parse($from)->timestamp;
        }

        $ghests = Ghest::searchExact('series', $series, 'order')
            ->whereHas('order', function ($q) use ($from_ts) {
                if ($from_ts)
                    $q->where('docs_received_at', '>=', Carbon::createFromTimestamp($from_ts));
                else
                    $q->whereNotNull('docs_received_at');
            })->with([
                'account' => function ($q) {
                    $q->select('id', 'bank_id', 'branch_name', 'branch_code', 'iban');
                },
                'order' => function ($q) {
                    $q->select('id', 'user_id', 'oid', 'docs_received_at', 'cheques', 'organ_id', 'series');
                },
                'order.organ' => function ($q) {
                    $q->select('id', 'fame', 'name');
                },
                'order.user' => function ($q) {
                    $q->select('id', 'fname', 'lname', 'nid');
                }
            ])->orderBy('received_at', 'asc')->get();

        $data[0] = [
            'نوع قلم',
            'رسید دریافت طرف مقابل',
            'رسید دریافت شماره',
            'رسید دریافت تاریخ',
            'چک شماره',
            'چک پشت نمره',
            'چک مبلغ',
            'چک تاریخ سررسید',
            'رسید دریافت شرح',
            'چک بانک',
            'چک کد شعبه',
            'چک شعبه1',
            'چک شماره حساب',
            'چک شرح',
            'رسید دریافت جمع دریافت',
            'رسید دریافت کد معین',
            'رسید دریافت صندوق',
        ];

        $lastDate = null;
        $lastOid = '';
        foreach ($ghests as $ghest) {
            $account = $ghest->account;
            $order = $ghest->order;
            $user = $ghest->order->user;
            if (!Useful::str_has($order->oid, '-')) continue;
            if ($ghest->type == 'epay') {
                $branch_name = $order->organ_id ? ($order->organ->fame ? $order->organ->fame : $order->organ->name) : 'غیر';
            } else if ($account) {

                $cheque_number_full = Useful::cheque_number_helper(substr($account->iban, 2, 3), $ghest->prepend, $ghest->series, $ghest->append);
            } else $cheque_number_full = null;

            $order_number = (int) ('1' . Useful::zerofill(explode('-', $order->oid)[1], 8));

            if (isset($temp[$ghest->order_id])) {
                $temp[$ghest->order_id][] = $ghest->id;
            } else {
                $temp = null;
                $temp[$ghest->order_id][] = $ghest->id;
            }

            $data[] = [
                'ReceiptCheque',
                $user->lname . ' ' . $user->fname . ' - ' . $user->nid,
                $order_number,
                $order->docs_received_at ? Edate::edateFromCarbon('Y/m/d', $order->docs_received_at) : '',
                $ghest->type == 'epay' ? explode('-', $order->oid)[1] . '-' . count($temp[$ghest->order_id]) : $cheque_number_full,
                ($type == 'xls' && $ghest->isbn) ? 'IR' . $ghest->isbn : $ghest->isbn,
                $ghest->amount * 10,
                Edate::edateFromCarbon('Y/m/d', $ghest->ghest_date),
                'قرارداد شماره ' . $order->oid . ' سری ' . $order->series,
                $ghest->type == 'epay' ? 10 : ($account ? (int)$account->bank_id : ''),
                $ghest->type == 'epay' ? 1 : ($account ? $account->branch_code : ''),
                $ghest->type == 'epay' ? $branch_name : ($account ? $account->branch_name : ''),
                $account ?  ($type == 'xls' ? 'IR' . $account->iban : $account->iban) : '',
                'قرارداد شماره ' . $order->oid . ' سری ' . $order->series,
                $order->cheques * $ghest->amount * 10,
                81001,
                $ghest->type == 'epay' ? 'صندوق اقساط' : 'صندوق اسناد'
            ];
            $lastDate = $order->docs_received_at;
            $lastOid = $order_number;
        }

        if ($type == 'xls')
            $url = ExportController::excel($data, 'cheques__' . $lastOid . '__' . Edate::edateFromCarbon('Y-m-d_H-i-s', $lastDate), 'strict');
        else
            $url = ExportController::csv($data, 'cheques__' . $lastOid . '__' . Edate::edateFromCarbon('Y-m-d_H-i-s', $lastDate), 'strict', $type);
        return Useful::ok(['url' => $url], ['action_type' => 'open_url']);
    }

    //pish
    public static function prepayments(Request $request)
    {
        $type = $request->type;
        $order_number = $request->order_number;

        $from = $request->from_date;
        $from_ts = null;

        if ($from) {
            $x = explode('-', $from);
            $from_ts = Edate::jmktime(0, 0, 0, $x[1], $x[2], $x[0]);
        }

        $pays = Payment::select('id', 'order_id', 'paid_at', 'amount', 'gateway')
            ->where(['type' => 'prepayment', 'status' => 100])
            ->where('ref_id', '!=', 1)
            ->where(function ($q) use ($from_ts) {
                if ($from_ts)
                    $q->whereDate('paid_at', '>=', Carbon::createFromTimestamp($from_ts));
                else
                    $q->whereNotNull('paid_at');
            })
            ->with([
                'order' => function ($q) {
                    $q->select('id', 'user_id', 'oid', 'prepayment', 'organ_id', 'series')->withTrashed();
                },
                'order.user' => function ($q) {
                    $q->select('id', 'fname', 'lname', 'nid')->withTrashed();
                }
            ])
            ->orderBy('paid_at', 'asc')->get();

        $data[0] = [
            'نوع قلم',
            'شماره',
            'رسید دریافت مبلغ نقد',
            'رسید دریافت جمع دریافت',
            'رسید دریافت صندوق',
            'رسید دریافت طرف مقابل',
            'رسید دریافت شرح',
            'رسید دریافت تاریخ',
            'رسید دریافت کد معین',
        ];

        $lastDate = null;
        $lastOid = '';
        foreach ($pays as $pay) {
            $order = $pay->order;
            $user = $pay->order->user;
            if (!Useful::str_has($order->oid, '-')) continue;
            $order_number = (int) ('2' . Useful::zerofill(explode('-', $order->oid)[1], 8));

            $data[] = [
                '',
                $order_number,
                $pay->amount * 10,
                $pay->amount * 10,
                $pay->gateway == 'zarinpal' ? 'حساب زرین پال' : ($pay->gateway == 'payping' ? 'حساب پی پینگ' : ''),
                $user->lname . ' ' . $user->fname . ' - ' . $user->nid,
                'واریز پیش پرداخت قسطاکارت سفارش ' . $order->oid,
                Edate::edateFromCarbon('Y/m/d', $pay->paid_at),
                81001,
            ];
            $lastDate = $pay->paid_at;
            $lastOid = $order_number;
        }

        if ($type == 'xls')
            $url = ExportController::excel($data, 'prepayments__' . $lastOid . '__' . Edate::edateFromCarbon('Y-m-d_H-i-s', $lastDate), 'strict');
        else
            $url = ExportController::csv($data, 'prepayments__' . $lastOid . '__' . Edate::edateFromCarbon('Y-m-d_H-i-s', $lastDate), 'strict', $type);
        return Useful::ok(['url' => $url], ['action_type' => 'open_url']);
    }

    //charges
    public static function charges(Request $request)
    {
        $type = $request->type;
        $order_number = $request->order_number;

        $from = $request->from_date;
        $from_ts = null;

        if ($from) {
            $x = explode('-', $from);
            $from_ts = Edate::jmktime(0, 0, 0, $x[1], $x[2], $x[0]);
        }

        $charges = Charge::select('id', 'card_id', 'order_id', 'amount', 'charged_at', 'type')
            ->where('amount', '>', 10000)
            ->where(function ($q) use ($from_ts) {
                if ($from_ts)
                    $q->where('charged_at', '>=', Carbon::createFromTimestamp($from_ts));
                else
                    $q->whereNotNull('charged_at');
            })
            ->with([
                'order' => function ($q) {
                    $q->select('id', 'user_id', 'oid', 'shop_id');
                },
                'order.user' => function ($q) {
                    $q->select('id', 'fname', 'lname', 'username', 'nid')->withTrashed();
                },
                'card' => function ($q) {
                    $q->select('id', 'user_id', 'card_number')->withTrashed();
                },

            ])
            ->orderBy('charged_at', 'asc')->get();

        $data[0] = [
            'نوع قلم',
            'اعلامیه پرداخت کد معین',
            'اعلامیه پرداخت کد تفصیل',
            'اعلامیه پرداخت شرح',
            'اعلامیه پرداخت مبلغ پرداخت',
            'اعلامیه پرداخت تاریخ',
            'اعلامیه پرداخت شماره',
            'اعلامیه پرداخت طرف مقابل',
            'اعلامیه برداشت تفصیل حساب بانکی',
            'اعلامیه برداشت حساب بانکی',
            'اعلامیه برداشت مبلغ',
            'اعلامیه برداشت تاریخ',
            'اعلامیه برداشت شماره',
            'اعلامیه برداشت شرح',
        ];

        $lastDate = null;
        $lastOid = '';
        $deposit_id = Finnotech::$DEPOSIT_ID;
        foreach ($charges as $charge) {
            $order = $charge->order;
            if (!$order) $order = (object) ['oid' => '-'];
            $user = $charge->order->user ?? (object) ['fname' => '', 'lname' => '', 'nid' => ''];
            $card = $charge->card;
            $order_number = (int) ('1' . Useful::zerofill(explode('-', $order->oid)[1], 8));

            $data[] = [
                'PaymentDraft',
                '81001',
                $user->nid,
                ($charge->type == 'charge' ? 'شارژ' : ($charge->type == 'extra' ? 'اضافی' : 'واریز')) . ' قسطاکارت شماره ' . $card->card_number . ' بابت سفارش ' . $order->oid,
                $charge->amount * 10, 
                Edate::edateFromCarbon('Y/m/d', $charge->charged_at),
                $order_number,
                $user->lname . ' ' . $user->fname . ' - ' . $user->nid,
                1005,
                'آینده آزادی ' . $deposit_id,
                $charge->amount * 10,
                Edate::edateFromCarbon('Y/m/d', $charge->charged_at),
                $order_number,
                ($charge->type == 'charge' ? 'شارژ' : ($charge->type == 'extra' ? 'اضافی' : 'واریز')) . ' قسطاکارت شماره ' . $card->card_number . ' بابت سفارش ' . $order->oid,
            ];

            $lastDate = $charge->charged_at;
            $lastOid = $order_number;
        }

        if ($type == 'xls')
            $url = ExportController::excel($data, 'charges__' . $lastOid . '__' . Edate::edateFromCarbon('Y-m-d_H-i-s', $lastDate), 'strict');
        else
            $url = ExportController::csv($data, 'charges__' . $lastOid . '__' . Edate::edateFromCarbon('Y-m-d_H-i-s', $lastDate), 'strict', $type);

        return Useful::ok(['url' => $url], ['action_type' => 'open_url']);
    }
}
