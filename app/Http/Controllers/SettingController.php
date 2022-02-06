<?php

namespace App\Http\Controllers;

use App\Models\Useful;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    //ADMIN
    public function index(Request $request)
    {
        $sets = Setting::where(['display' => 1])->get();

        $fields['order'] = [];
        $fields['order_organ'] = [];
        $fields['shop'] = [];

        foreach ($sets as $set) {
            $fields[$set->group][] = [
                'label' => $set->name,
                'v_model' => $set->prop,
                'value' => $set->val,
                'type' => 'text',
                'mode' => $set->type,
                'width' => '4',
            ];
        }

        $res = [
            [
                'section' => 'سفارش های معمولی',
                'size' => 'lg',
                'fields' => $fields['order']
            ],
            [
                'section' => 'سفارش های سازمانی',
                'size' => 'lg',
                'fields' => $fields['order_organ']
            ],
            [
                'section' => 'تنظیمات فروشگاه ها',
                'size' => 'lg',
                'fields' => $fields['shop']
            ]
        ];

        return Useful::ok($res);
    }

    //ADMIN
    public function edit(Request $request)
    {
        $input = $request->all();
        foreach ($input as $key => $value) {
            Setting::where(['prop' => $key, 'display' => 1])->update(['val' => $value]);
        }
        return Useful::ok();
    }

    public function globals()
    {
        $settings = (array) Setting::gv();
        return Useful::ok($settings);
    }
}
