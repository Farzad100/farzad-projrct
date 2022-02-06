<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Cdns\CdnsController;
use App\Http\Controllers\Clients\MalexController;
use App\Http\Controllers\DocController;
use App\Mail\EshopVerification;
use App\Models\Account;
use App\Models\Charge;
use App\Models\Company;
use App\Models\Doc;
use App\Models\Edate;
use App\Models\Finnotech;
use App\Models\Ghest;
use App\Models\ICBS;
use App\Models\Order;
use App\Models\Organ;
use App\Models\Payment;
use App\Models\Shop;
use App\Models\Useful;
use App\Models\User;
use App\Packages\Payping;
use App\Packages\Venesh;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Psy\Command\DocCommand;

class TestController extends Controller
{
    public static function order_profit_calc($order)
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

    public static function order_ghests_profit_check($order)
    {
        $origin_sum = Ghest::where('order_id', $order->id)->sum('origin');
        $income_sum = Ghest::where('order_id', $order->id)->sum('income');
        return $order->total == $origin_sum + $income_sum ? 'ok' . nl2br("\n") : ($order->id) . nl2br("\n");
    }

    public static function xx()
    {
        $start = time();

        $ghests = Ghest::whereNull('income')->orderBy('id', 'desc')->distinct()->limit(1000)->get(['order_id']);
        foreach ($ghests as $ghest) $order_ids[] = $ghest->order_id;

        $orders = Order::whereIn('id', $order_ids)->get();

        foreach ($orders as $order) {
            $report[] = $order->oid;
            $res = self::order_profit_calc($order);

            $ghests = Ghest::where('order_id', $order->id)->orderBy('ghest_date', 'asc')->get();

            foreach ($res as $key => $item) {
                $ghests[$key]->update([
                    'origin' => $item['origin'],
                    'income' => $item['income']
                ]);
            }

            if (time() - $start > 45) return 1;
        }

        return $report;
    }

    public static function yy()
    {
        $reps = Doc::select('id', 'order_id', 'bank_id')->where('type', 'report')->where('is_verified', 1)
            ->whereNotNull('bank_id')->orderBy('id', 'desc')->get();

        foreach ($reps as $rep) {
            Doc::where('order_id', $rep->order_id)->where('type', 'cheque')->update(['bank_id' => $rep->bank_id]);
        }

        return count($reps);
    }

    public static function zz()
    {
        $chs = Doc::select('id', 'order_id', 'bank_id')->where('type', 'cheque')->where('is_verified', 1)
            ->whereNotNull('bank_id')->orderBy('id', 'desc')->limit(100)->get();

        foreach ($chs as $ch) {
        }

        return $chs;
    }

    public static function qq()
    {
        $start = time();

        $chs = Doc::select('id', 'order_id', 'bank_id')->where('type', 'cheque')->where('is_verified', 1)
            ->whereNotNull('bank_id')->orderBy('id', 'desc')->offset(3)->limit(7)->get();

        foreach ($chs as $ch) {
            $ch->bank = Account::bank_name($ch->bank_id)['fa'];
            $ch->file = DocController::downloadFile($ch->id);
            $ch->result = Venesh::chequeReader(file_get_contents($ch->file), false);
            if (time() - $start > 30) break;
        }

        return $chs;
    }

    public function get(Request $request)
    {
        return QueueController::orders_inquiry();
        // return QueueController::shops_reginfo();
        return Payping::user_exists('info@botika.ir');
        // return ICBS::RegInfo('2991029242', '1334-07-02');
        // return ICBS::PostInfo("1818977855"); 
        return ICBS::IntegratedCs(2739668902);
        /* return Ghest::select('shamsi','amount')->whereBetween('ghest_date', [Carbon::parse('2020-03-20 00:00:00'), Carbon::parse('2021-03-20 00:00:00')])
        ->orderBy('ghest_date','desc')->get();
        return number_format(Ghest::whereBetween('ghest_date', [Carbon::parse('2020-03-20 00:00:00'), Carbon::parse('2021-03-20 00:00:00')])
            ->sum('amount')); */
        // return ICBS::Shahkar('2755609192', '09149417287');

        // return Payping::user_exists("info@ghesta.ir");
        /* $orders = Order::whereIn('status', ['cycle', 'completed'])
            ->where('cheques', 3)->orderBy('id', 'desc')->limit(100)->get();

        $n = 0;
        foreach ($orders as $order) {
            $x[$n] = self::order_profit_calc($order);
            $x[$n][] = [ 
                'amount' => number_format($order->amount),
                'prepayment' => number_format($order->prepayment),
                'cre' => number_format($order->amount - $order->prepayment),
                'ghest' => number_format($order->ghest),
                'total_receive' => number_format($order->total),
                'total_income' => number_format($order->total - ($order->amount - $order->prepayment)),
                'm' => number_format($order->months),
            ];
            ++$n;
        }

        return $x; */
        /* Mail::to("info@botika.net")->send(new EshopVerification(52335));
        Mail::to("ehsan1859@gmail.com")->send(new EshopVerification(217654)); */

        return Useful::ip();
        return Finnotech::bonStatement("6362144260270926", "13990422", "13990425", "bon-state-m" . mt_rand(10000, 99999));
        $x = null;
        if ($x === true) return 'yes';
        elseif (is_null($x)) return 'nul';
        else return 'no';
        $items = Order::select('id', 'shop_id', 'organ_id', 'created_at')->where('shop_id', '>', 1)->orWhere('organ_id', '>', 1)->get();
        return [
            'all' => $items->count(),
            'shop' => $items->where('shop_id', '>', 1)->count(),
            'shopx' => $items->where('shop_id', '>', 1)->where('created_at', '>', Carbon::now()->subMonths(4))->count(),
            'shopy' => $items->where('shop_id', '>', 1)->where('created_at', '<=', Carbon::now()->subMonths(4))->count(),
            'organ' => $items->where('organ_id', '>', 1)->count(),
        ];
        return 1;
        return ICBS::Shahkar('0016974654', '09109270876') ? 'yes' : 'no';
        return ICBS::IntegratedCs('1699890511');
        return ICBS::RegInfo('0016974654', '1372-10-23');
        return DocController::downloadFile($request->slug);
        return Finnotech::backCheques('0016974654');
        return Finnotech::bonChargeInquiry("bon-charge-2584", 'm' . mt_rand(10000, 99999));
        $x = new MalexController();
        return $x->cs();
    }

    public function post(Request $request)
    {
        return $request->name;
    }

    public function hasher($str)
    {
        return Hash::make($str);
    }

    public static function db2std()
    {
        //accounts
        $arr = ['id', 'branch_code'];
        $items = Account::select($arr)->get();
        foreach ($items as $item) {
            $item->update(['branch_code' => str_replace(['کد ', 'کد'], ['', ''], $item->branch_code)]);
        }

        return 1;

        //shops
        $arr = ['id', 'name', 'website', 'type', 'domain', 'email', 'phone', 'phone_direct', 'city', 'address', 'postal_code', 'about'];
        $items = Shop::select($arr)->get();
        foreach ($items as $item) {
            foreach ($arr as $el) $x[$el] = Useful::std($item->$el);
            if ($item->type == 'online') {
                $x['domain'] = Useful::domain_extractor($item->website);
            }
            $item->update($x);
            unset($x);
        }

        //organs
        $arr = ['id', 'name', 'fame', 'website', 'email', 'phone', 'phone_direct', 'about'];
        $items = Organ::select($arr)->get();
        foreach ($items as $item) {
            foreach ($arr as $el) $x[$el] = Useful::std($item->$el);
            $item->update($x);
            unset($x);
        }

        //ghests
        $arr = ['id', 'series', 'shamsi'];
        $items = Ghest::select($arr)->get();
        foreach ($items as $item) {
            foreach ($arr as $el) $x[$el] = Useful::std($item->$el);
            $x['shamsi'] = str_replace('-', '', $item->shamsi);
            $item->update($x);
            unset($x);
        }

        //companies
        $arr = ['id', 'name', 'fame', 'nic', 'ec', 'rn', 'ceo_fname', 'ceo_lname', 'ceo_mobile', 'city', 'address', 'postal_code', 'phone'];
        $items = Company::select($arr)->get();
        foreach ($items as $item) {
            foreach ($arr as $el) $x[$el] = Useful::std($item->$el);
            $item->update($x);
            unset($x);
        }

        //addresses
        $arr = ['id', 'city', 'address', 'postal_code', 'phone'];
        $items = Address::select($arr)->get();
        foreach ($items as $item) {
            foreach ($arr as $el) $x[$el] = Useful::std($item->$el);
            $item->update($x);
            unset($x);
        }

        //accounts
        $arr = ['id', 'branch_code'];
        $items = Account::select($arr)->get();
        foreach ($items as $item) {
            foreach ($arr as $el) $x[$el] = Useful::std($item->$el);
            $item->update($x);
            unset($x);
        }

        return 1;
    }

    /* public function order_profit_calc($order)
    {
        $credit = $order->amount - $order->prepayment;
        $ghest = $order->ghest;
        $gain = (1 + $order->gain) ** ($order->months / $order->cheques) - 1;
        $result['oid'] = $order->oid;
        $result['amount'] = $order->amount;
        $result['prepayment'] = $order->prepayment;
        $result['credit'] = $order->amount - $order->prepayment;
        $result['ghest'] = $ghest;
        $result['total'] = $order->total;
        $result['months'] = $order->months;
        $result['cheques'] = $order->cheques;
        $result['gain'] = $gain;
        $result['details'][0] = [
            'origin' => 0,
            'income' => 0,
            'remaining' => $credit
        ];

        $result['o'] = 0;
        $result['i'] = 0;
        for ($item = 1; $item < $order->cheques; $item++) {
            $i = round($credit * $gain);
            $o = round($ghest - $i);
            $result['i'] += $i;
            $result['o'] += $o;
            $credit = $credit - $o;
            $result['details'][$item] = [
                'origin' => $o,
                'income' => $i,
                'remaining' => round($credit)
            ];
        }

        $result['x'] = [
            'origin' => $result['credit'] - $result['o'],
            'income' => $result['total'] - $result['credit'] - $result['i'],
            'remaining' => 0
        ];
        return $result;
    } */
}
