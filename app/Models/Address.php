<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes; 
    
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static $states_iran = [
        0 => 'آذربایجان شرقی',
        1 => 'آذربایجان غربی',
        2 => 'اردبیل',
        3 => 'اصفهان',
        4 => 'البرز',
        5 => 'ایلام',
        6 => 'بوشهر',
        7 => 'تهران',
        8 => 'چهارمحال و بختیاری',
        9 => 'خراسان جنوبی',
        10 => 'خراسان رضوی',
        11 => 'خراسان شمالی',
        12 => 'خوزستان',
        13 => 'زنجان',
        14 => 'سمنان',
        15 => 'سیستان و بلوچستان',
        16 => 'فارس',
        17 => 'قزوین',
        18 => 'قم',
        19 => 'کردستان',
        20 => 'کرمان',
        21 => 'کرمانشاه',
        22 => 'کهگیلویه و بویراحمد',
        23 => 'گلستان',
        24 => 'گیلان',
        25 => 'لرستان',
        26 => 'مازندران',
        27 => 'مرکزی',
        28 => 'هرمزگان',
        29 => 'همدان',
        30 => 'یزد'
    ];

    public static function list_states()
    {
        $arr = [];
        $items = self::$states_iran;
        foreach ($items as $index => $label) {
            array_push($arr, ['label' => $label, 'value' => $index]);
        }
        return $arr;
    }

    public static function state_code($state)
    {
        if (is_numeric($state)) return $state;
        return array_search($state, Address::$states_iran);
    }

    public static function state_name($code)
    {
        if (is_numeric($code)) return self::$states_iran[$code];
        return $code;
    }

    public static function index($user_id, $mode = '')
    {
        $home_address = Address::where('user_id', $user_id)->where('type', 'home')->orderBy('id', 'desc')->limit(1)->get();
        $work_address = Address::where('user_id', $user_id)->where('type', 'work')->orderBy('id', 'desc')->limit(1)->get();

        if (count($work_address) > 0) $addresses = $home_address->merge($work_address);
        else if (count($home_address) == 0) $addresses = [];
        else $addresses = $home_address;

        foreach ($addresses as $address) {
            $address->state = Address::state_name($address->state);
            $address->editable = $address->address_verified_at ? false : true;
        }

        return $addresses;
    }
}
