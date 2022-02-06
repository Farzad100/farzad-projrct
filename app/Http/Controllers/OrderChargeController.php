<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Models\Commission;
use App\Models\Edate;
use App\Models\Shop;
use App\Models\Useful;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class OrderChargeController extends Controller
{
    public function index(Request $request)
    {
        $uri = Route::current()->uri;
        $role = explode('/', $uri)[1];

        Auth::user()->username == 9109270876 ? $debugger = 1 : $debugger = null;

        switch ($role) {
            case 'admin':
                $query = Charge::query();
                break;
            case 'shop':
                $shop_id = Shop::select('id')->where('user_id', Auth::user()->id)->first()->id;
                $query = Charge::shopId($shop_id);
                break;
            default:
                return Useful::nok(['scope_not_found', 'دسترسی غیرمجاز']);
        }

        $coms = $query->select('id', 'card_id', 'amount', 'commission', 'charged_at', 'created_by', 'order_id')->where('type', 'charge')
            ->whereNotNull('charged_at')->whereHas('order.shop')->with([
                'order' => function ($q) {
                    $q->select('id', 'oid', 'user_id', 'shop_id', 'amount');
                }, 'order.shop' => function ($q) {
                    $q->select('id', 'user_id', 'name', 'type');
                }, 'order.shop.user' => function ($q) {
                    $q->select('id', 'fname', 'lname', 'username', 'nid');
                }, 'card' => function ($q) {
                    $q->select('id', 'user_id', 'card_number');
                }, 'card.user' => function ($q) {
                    $q->select('id', 'fname', 'lname', 'username', 'nid');
                }
            ])->paginate(20);

        $new = [];
        $n = 0;
        foreach ($coms as $com) {
            $new[$n]['id'] = $com->id;
            if ($debugger) {
                $col['did'] = [
                    'title' => 'شناسه کارمزد',
                    'value' => $com->id,
                ];
            }
            $col['order'] = [
                'title' => 'سفارش',
                'value' => $com->order->oid,
                'addition' => number_format($com->order->amount) . ' تومان'
            ];
            $col['com'] = [
                'title' => 'کارمزد قسطا',
                'value' => $com->commission,
                'type' => 'toman'
            ];
            $col['charged'] = [
                'title' => 'شارژ شده',
                'value' => $com->amount,
                'type' => 'toman'
            ];
            $col['date'] = [
                'title' => 'تاریخ',
                'value' => Edate::edateFromCarbon('j F Y', $com->charged_at),
                'addition' => 'ساعت ' . Edate::edateFromCarbon('H:i', $com->charged_at),
            ];
            $col['card'] = [
                'title' => 'قسطاکارت',
                'value' => $com->card->card_number,
            ];

            if ($role == 'admin') {
                $col['shop'] = [
                    'title' => 'فروشگاه',
                    'value' => $com->order->shop->name,
                    'addition' => $com->order->shop->type == 'offline' ? 'آفلاین' : 'آنلاین'
                ];
                $col['seller'] = [
                    'title' => 'فروشنده',
                    'value' => $com->order->shop->user->full_name,
                    'addition' => $com->order->shop->user->mobile,
                ];
            }

            $col['card_owner'] = [
                'title' => 'مالک کارت',
                'value' => $com->card->user->full_name,
                'addition' => $com->card->user->mobile,
                'css_classes' => 'text-right'
            ];
            $new[$n]['td'] = $col;
            $col = null;
            ++$n;
        }

        return Useful::recollect($coms, $new);
    }
}
