<?php

namespace App\Http\Controllers;

use App\Models\Edate;
use App\Models\Useful;
use App\Models\Discount;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    //ADMIN
    public function index(Request $request)
    { 
        $discounts = Discount::search($request)->orderBy('created_at', 'desc')->paginate(100);
        $new = [];
        $n = 0;
        foreach ($discounts as $discount) {
            $used = Payment::where('discount_id', $discount->id)->where('status', 100)->count();
            $discount->status = Discount::status_fixer($discount, $used);

            $new[$n]['id'] = $discount->id;

            $col['name'] = [
                'title' => 'کد تخفیف',
                'value' => $discount->code,
                'addition' => $discount->mobile ? 'اختصاصی: ' . $discount->mobile : '',
            ];

            $col['status'] = [
                'title' => 'وضعیت',
                'value' => Discount::$status[$discount->status]['farsi'],
                'css_classes' => 'badge-' . Discount::$status[$discount->status]['class'],
                'type' => 'status'
            ];

            $col['amount'] = [
                'title' => 'مقدار',
                'value' =>  $discount->amount,
                'type' => 'toman',
            ];

            $col['limit'] = [
                'title' => 'ظرفیت',
                'value' => number_format($discount->limit),
                'addition' => $discount->limit_per_user > 1 ? 'هر کاربر: ' . $discount->limit_per_user : ''
            ];

            $col['used'] = [
                'title' => 'مصرفی',
                'value' =>  $used,
                'addition' => $used > 0 ? 'مانده: ' . ($discount->limit - $used) : '',
            ];

            $col['expired_at'] = [
                'title' => 'تاریخ انقضا',
                'value' =>  Edate::edateFromCarbon('j F Y', $discount->expired_at),
                'addition' => $discount->status == 'active' ? Edate::date_diff_carbon(Carbon::now(), $discount->expired_at) . ' دیگر' : '',
            ];

            /* $col['utm'] = [
                'title' => 'UTM',
                'value' => $user->utm_source,
                'addition' => $user->utm_campaign ? 'camp: ' . $user->utm_campaign : '',
            ]; */

            $col['action'] = [
                'type' => 'button',
                'buttons' => [
                    [
                        'value' => 'ویرایش',
                        'icon' => 'calendar-edit',
                        'btn_color' => 'info',
                        'type' => 'modal',
                        'modal_name' => 'editModal',
                        'method' => 'post',
                        'endpoint' => '/admin/discounts/' . $discount->id . '/edit',
                    ],
                    [
                        'value' => '',
                        'icon' => 'trash',
                        'css_classes' => 'text-danger',
                        'type' => 'confirm',
                        'method' => 'post',
                        'endpoint' => '/admin/discounts/' . $discount->id . '/delete',
                    ],
                ],
            ];

            $new[$n]['td'] = $col;
            $col = null;
            ++$n;
        }
        return Useful::recollect($discounts, $new);
    }

    //ADMIN
    public function create(Request $request)
    {
        if (Discount::where('code', $request->code)->exists())
            return Useful::nok(['code_exists', 'یک کد تخفیف فعال با این نام وجود دارد.']);

        $date = explode('-', $request->expired_at);
        $y = (int)$date[0];
        $m = (int)$date[1];
        $d = (int)$date[2];
        $ts = Edate::jmktime(23, 59, 59, $m, $d, $y);
        if (Discount::create([
            'code' => $request->code,
            'percent' => 100,
            'amount' => $request->amount,
            'limit' => $request->limit,
            'limit_per_user' => $request->limit_per_user ? $request->limit_per_user : 1,
            'mobile' => $request->mobile,
            'expired_at' => Carbon::createFromTimestamp($ts),
        ])) {
            return Useful::ok();
        }
        return Useful::nok(['dbd-c']);
    }

    //ADMIN
    public function edit(Request $request)
    {
        if (Discount::find($request->id)->update([
            'is_active' => $request->is_active,
            'limit' => $request->limit,
            'limit_per_user' => $request->limit_per_user ? $request->limit_per_user : 1,
            'expired_at' => Edate::j2carbon($request->expired_at),
        ])) {
            return Useful::ok();
        }
        return Useful::nok(['dbo-e', 'خطا در اتصال به دیتابیس']);
    }

    //ADMIN
    public function edit_fields(Request $request)
    {
        $discount = Discount::where('id', $request->id)->first();
        if ($discount->expired_at) {
            $expired_at = Edate::edateFromCarbon('Y-m-d', $discount->expired_at);
            $exp = explode('-', $expired_at);
        } else {
            $expired_at = null;
            $exp = null;
        }
        $res = [
            [
                'section' => '',
                'size' => 'lg',
                'fields' => [
                    [
                        'label' => 'عبارت',
                        'v_model' => 'code',
                        'value' => $discount->code,
                        'type' => 'text',
                        'width' => '6',
                        'disabled' => true,
                    ],
                    [
                        'label' => 'فعال',
                        'v_model' => 'is_active',
                        'value' => $discount->is_active,
                        'type' => 'switch',
                        'width' => '6',
                    ],
                    [
                        'label' => 'ظرفیت کلی',
                        'v_model' => 'limit',
                        'value' => $discount->limit,
                        'type' => 'text',
                        'width' => '6',
                    ],
                    [
                        'label' => 'به ازای هر کاربر',
                        'v_model' => 'limit_per_user',
                        'value' => $discount->limit_per_user,
                        'type' => 'text',
                        'width' => '6',
                    ],
                    [
                        'label' => 'تاریخ انقضا',
                        'v_model' => 'expired_at',
                        'value' => $expired_at,
                        'type' => 'date',
                        'values' => [
                            'y' => $exp ? $exp[0] : null,
                            'm' => $exp ? $exp[1] : null,
                            'd' => $exp ? $exp[2] : null,
                        ],
                    ],
                ],
            ]
        ];

        return Useful::ok($res);
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        if (Discount::whereId($id)->delete()) return Useful::ok();
        return Useful::nok();
    }
}
